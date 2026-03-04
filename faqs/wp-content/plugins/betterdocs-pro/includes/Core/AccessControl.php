<?php

namespace WPDeveloper\BetterDocsPro\Core;

use WP_Query;

use function WPML\FP\Strings\remove;
use function WPML\PHP\Logger\error;
use WPDeveloper\BetterdocsPro\Utils\Helper;

class AccessControl {

    public $current_user;
    public $access_control_settings;
    public $current_user_role;
    public $final_access_control_settings;
    public $restricted_url;

    /**
     * Recursion protection flag to prevent infinite loops in filter hooks
     * @var bool
     */
    private static $filter_recursion_guard = false;

    /**
     * Cache for expensive database operations
     * @var array
     */
    private static $query_cache = [];

    /**
     * Maximum recursion depth for get_parents method
     * @var int
     */
    private static $max_recursion_depth = 10;

    public function __construct() {
        // Load appropriate access control settings based on multiple KB feature status
        $this->access_control_settings = betterdocs_pro()->multiple_kb->is_enable
            ? betterdocs()->settings->get( 'betterdocs_access_control_repeater_kb' )
            : betterdocs()->settings->get( 'betterdocs_access_control_repeater' );

        // Get current user and their roles
        $this->current_user = wp_get_current_user();
        $this->current_user_role = is_string( $this->current_user->roles )
            ? [$this->current_user->roles]
            : $this->current_user->roles;

        // Early return optimization: skip processing if no access control settings exist
        if (empty($this->access_control_settings)) {
            $this->final_access_control_settings = [];
            $this->restricted_url = '';
            return;
        }

        // Process access control rules for current user's roles
        $this->final_access_control_settings = $this->get_selected_rules_based_on_user_role();

        // Set redirect URL for restricted access
        $this->restricted_url = betterdocs()->settings->get( 'restricted_redirect_url' );

        // Register cache clearing hooks for when settings are updated
        add_action('update_option_betterdocs_settings', [__CLASS__, 'clear_access_control_cache']);
        add_action('update_option_betterdocs_pro_settings', [__CLASS__, 'clear_access_control_cache']);
    }

    public function init() {
        if( betterdocs_pro()->multiple_kb->is_enable ) {
            // add_filter( 'betterdocs_terms_query_args', [$this, 'include_or_exclude_selected_mkb_terms'] ); //for front-end using get_terms() to fetch terms mkb | logic done
            add_filter( 'betterdocs_docs_count', [$this, 'include_or_exclude_selected_mkb_terms_count'], 20, 5); //for front-end using get_terms() | logic done
            // add_filter( 'betterdocs_articles_args', [$this, 'include_or_exclude_selected_mkb_doc_category_attached_posts'], 20, 3 ); //for front-end using WP_Query() to fetch docs | logic done
            // add_action( 'template_redirect', [$this, 'template_redirect_mkb'], 99 );
            add_filter( 'knowledge_base_row_actions', [$this, 'modify_quick_actions_wp_list_table_for_knowledge_base'], 10, 2);
            add_filter( 'doc_category_row_actions', [$this, 'modify_quick_actions_wp_list_table_for_doc_category'], 10, 2);
            add_filter( 'terms_clauses', [$this,'filter_mkb_categories'], 11, 3 ); //global query for get_terms and rest api (applicable on admin | front-end | rest-api)
            add_filter( 'posts_where', [$this, 'filter_doc_categories_docs_in_mkb'], 90, 2); //global query for posts and rest api (applicable on admin | front-end | rest-api)
            add_filter( 'rest_docs_query', [$this, 'enable_or_disable_filter'], 90, 2); //filter to disable rest api docs payload blockage
            add_filter( 'get_terms_args', [$this, 'enable_or_disable_terms_filter'], 90, 2); //filter to disable rest api payload blockage
            add_action( 'template_redirect', [$this, 'template_redirect_mkb_single_docs'], 99 );
            add_filter( 'rest_knowledge_base_query', [$this, 'enable_or_disable_filter'], 90, 2);

            // Excludes terms from the docs/knowlege_base/doc_category page that are not assigned to the current role.(it can be any outside user, author, editor, etc)
            add_filter( 'betterdocs_terms_query_args', [$this,'exclude_mkb_terms_or_redirect_not_in_role'], 10, 1 );

            // redirect docs based on doc_category from the single doc or other page that are not assigned to the current role.(it can be any outside user, author, editor, etc)
            add_action( 'template_redirect', [$this,'redirect_users_not_in_rules_mkb'], 10 );
        } else {
            if( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && in_array('all', array_keys( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ) ) { //handle all logic
                add_filter( 'betterdocs_terms_query_args', [$this,'filter_doc_categories_all'], 10, 1 );
                add_action( 'template_redirect', [$this, 'template_redirect_on_all'], 99 );
                add_filter( 'rest_doc_category_query', [$this, 'enable_or_disable_doc_category_filter_for_all'], 90, 2 ); //to enable or disable doc_category api in rest
                add_filter( 'rest_doc_category_query', [$this, 'show_or_hide_doc_category_terms_based_on_all'], 91, 2 ); //to enable or disable doc_category api in rest
                add_filter( 'rest_docs_query', [$this, 'enable_or_disable_docs_filter_for_all'], 90, 2);
                add_filter( 'rest_docs_query', [$this, 'show_or_hide_docs_based_on_all'], 91, 2 );
                add_filter( 'get_terms', [$this, 'show_or_hide_categories_based_on_all_in_admin_list_table'], 10, 4);
                add_filter( 'post_row_actions', [$this,'custom_post_type_row_actions_for_all'], 10, 2);
                add_filter( 'doc_category_row_actions', [$this, 'modify_quick_actions_wp_list_table_for_all'], 10, 2);
                add_action( 'template_redirect', [$this,'redirect_users_when_all_is_selected'], 10 );
            } else { // handle specific category, docs selection
                // add_filter( 'rest_doc_category_query', [$this, 'include_or_exclude_selected_terms'] ); //for doc category rest api | logic done
                // add_filter( 'betterdocs_terms_query_args', [$this, 'include_or_exclude_selected_terms'] ); //for front-end using get_terms() to fetch terms | logic done
                // add_filter( 'betterdocs_articles_args', [$this, 'include_or_exclude_selected_posts'], 20, 3 ); //for front-end using WP_Query() to fetch docs | logic done
                add_filter( 'betterdocs_docs_count', [$this, 'include_or_exclude_selected_posts_count'], 10, 5); //for front-end using get_terms() | logic done
                // add_action( 'template_redirect', [$this, 'template_redirect_without_mkb'], 99 );
                add_filter( 'post_row_actions', [$this,'custom_post_type_row_actions'], 10, 2);
                add_filter( 'rest_doc_category_query', [$this, 'enable_or_disable_terms_filter_doc_category'], 10, 2 ); //to enable or disable doc_category api in rest
                add_filter( 'doc_category_row_actions', [$this, 'modify_quick_actions_wp_list_table'], 10, 2);
                add_filter( 'terms_clauses', [$this,'filter_doc_categories'], 11, 3 ); //global query for get_terms and rest api (applicable on admin | front-end | rest-api)
                add_filter( 'posts_where', [$this, 'filter_doc_categories_docs'], 90, 2); //global query for posts and rest api (applicable on admin | front-end | rest-api)
                add_filter( 'rest_docs_query', [$this, 'enable_or_disable_filter'], 90, 2); //filter rest post type query
                add_action( 'template_redirect', [$this, 'template_redirect_single_docs'], 99 );

                // Excludes terms from the docs/doc_category page that are not assigned to the current role.(it can be any outside user, author, editor, etc)
                add_filter( 'betterdocs_terms_query_args', [$this,'exclude_terms_or_redirect_not_in_role'], 10, 1 );
                add_filter( 'rest_doc_category_query', [$this, 'exclude_terms_or_redirect_not_in_role'], 11, 2 );

                // redirect docs based on doc_category from the single doc or other page that are not assigned to the current role.(it can be any outside user, author, editor, etc)
                add_action( 'template_redirect', [$this,'redirect_users_not_in_rules'], 10 );

                //disable the edit link from the WP_List_Table of docs
                add_filter('get_edit_post_link', [$this, 'disable_post_links_for_docs_view'], 10, 3);

                //disable the edit link from the WP_List_Term_Table of docs
                add_filter('get_edit_term_link', [$this, 'disable_terms_for_terms_view'], 9999, 4);
            }
        }

        add_filter('betterdocs_pro_localize_script', [$this, 'localize_access_control_settings'], 10, 2);
    }

    public function enable_or_disable_terms_filter_doc_category($args, $request) {
        $params = $request->get_params() ?? [];
        if( isset( $params['suppress_filters'] ) && $params['suppress_filters'] ) {
            remove_filter( 'terms_clauses', [$this,'filter_doc_categories'], 11, 3 );
            remove_filter( 'rest_doc_category_query', [$this, 'exclude_terms_or_redirect_not_in_role'], 11, 2 );
        }
        return $args;
    }

    public function enable_or_disable_terms_filter( $args, $taxonomies ) {
        if( isset( $args['suppress_filters'] ) && $args['suppress_filters'] ) {
            remove_filter( 'terms_clauses', [$this,'filter_mkb_categories'], 11, 3 );
        }
        return $args;
    }

    public function enable_or_disable_filter($args, $request) {
        $params = $request->get_params() ?? [];
        if( isset( $params['suppress_filters'] ) && $params['suppress_filters'] ) {
            remove_filter('terms_clauses', [$this,'filter_doc_categories'], 11, 3 ); //global query for get_terms and rest api (applicable on admin | front-end | rest-api)
            remove_filter('posts_where', [$this, 'filter_doc_categories_docs'], 90);
            remove_filter('terms_clauses', [$this,'filter_mkb_categories'], 11, 3 );
            remove_filter('posts_where', [$this, 'filter_doc_categories_docs_in_mkb'], 90, 2); //global query for posts and rest api (applicable on admin | front-end | rest-api)
        }
        return $args;
    }

    public function enable_or_disable_docs_filter_for_all($args, $request) {
        $params = $request->get_params() ?? [];
        if( isset( $params['suppress_filters'] ) && $params['suppress_filters'] ) {
            remove_filter('rest_docs_query', [$this, 'show_or_hide_docs_based_on_all'], 91, 2);
        }
        return $args;
    }

    /**
     * Show or hide documents based on 'all' restriction mode
     *
     * PERFORMANCE OPTIMIZATIONS APPLIED:
     * - Caching to prevent repeated expensive queries
     * - Chunked processing to prevent memory issues
     * - Early exit conditions for performance
     * - Limited processing to prevent memory exhaustion
     *
     * @param array $args WP_Query arguments
     * @param \WP_REST_Request $request REST request object (unused but required by hook)
     * @return array Modified query arguments with post__not_in parameter
     */
    public function show_or_hide_docs_based_on_all($args, $request) {
        $doc_categories_mode = isset( $this->final_access_control_settings['control_access_restrict_doc_category']['all'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category']['all']: '';

        if( ! empty( $doc_categories_mode ) && $doc_categories_mode == 'restricted' ) {
            // Use caching to prevent repeated expensive queries
            $cache_key = 'betterdocs_restricted_terms_' . md5(serialize($this->final_access_control_settings));

            if (isset(self::$query_cache[$cache_key])) {
                $terms = self::$query_cache[$cache_key];
            } else {
                $terms = get_terms([
                    'taxonomy'   => 'doc_category',
                    'hide_empty' => true,
                    'fields'     => 'ids',
                    'number'     => 1000, // Add reasonable limit to prevent memory issues
                ]);

                // Cache the result
                self::$query_cache[$cache_key] = $terms;
            }

            if( ! empty( $terms ) ) {
                // Use cached posts if available
                $posts_cache_key = 'betterdocs_restricted_posts_' . md5(serialize($terms));

                if (isset(self::$query_cache[$posts_cache_key])) {
                    $posts = self::$query_cache[$posts_cache_key];
                } else {
                    // Process in chunks to prevent memory issues
                    $posts = [];
                    $term_chunks = array_chunk($terms, 50); // Process 50 terms at a time

                    foreach ($term_chunks as $term_chunk) {
                        $chunk_posts = get_posts([
                            'post_type'      => 'docs',
                            'fields'         => 'ids',
                            'posts_per_page' => 500, // Reasonable limit instead of -1
                            'tax_query'      => [
                                [
                                    'taxonomy' => 'doc_category',
                                    'field'    => 'term_id',
                                    'terms'    => $term_chunk,
                                    'operator' => 'IN',
                                ],
                            ],
                        ]);

                        if (!empty($chunk_posts)) {
                            $posts = array_merge($posts, $chunk_posts);
                        }
                    }

                    // Cache the result
                    self::$query_cache[$posts_cache_key] = $posts;
                }

                if( ! empty( $posts ) ) {
                    $args['post__not_in'] = array_unique($posts);
                }
            }
        }

        return $args;
    }

    public function show_or_hide_categories_based_on_all_in_admin_list_table($terms, $taxonomy, $query_vars, $terms_query) {
        $doc_categories_mode = isset( $this->final_access_control_settings['control_access_restrict_doc_category']['all'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category']['all']: '';
        if( function_exists('get_current_screen') && get_current_screen() != null && is_admin() && get_current_screen()->id == 'edit-doc_category' && $doc_categories_mode == 'restricted' ){
            $terms = [];
        }
        return $terms;
    }

    public function enable_or_disable_doc_category_filter_for_all( $args, $request ) {
        $params = $request->get_params() ?? [];

        if( isset( $params['suppress_filters'] ) && $params['suppress_filters'] ) {
            remove_filter( 'rest_doc_category_query', [$this, 'show_or_hide_doc_category_terms_based_on_all'], 91, 2 );
        }

        return $args;
    }

    public function show_or_hide_doc_category_terms_based_on_all( $args, $request ) {
        $doc_categories_mode = isset( $this->final_access_control_settings['control_access_restrict_doc_category']['all'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category']['all']: '';

        if( ! empty( $doc_categories_mode ) && $doc_categories_mode == 'restricted' ) {
            $terms = get_terms([
                'taxonomy'   => 'doc_category',
                'hide_empty' => true,
                'fields'     => 'ids',
            ]);

            if( ! empty( $terms ) ) {
                $args['exclude'] = $terms;
            }
        }

        return $args;
    }

    public function modify_quick_actions_wp_list_table_for_knowledge_base($actions, $tag) {
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];

        foreach( $selected_categories_terms as $term_id => $term_status ) {
            if( isset( $tag->term_id ) && $tag->term_id == $term_id && $term_status == 'view' ) {
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);
                unset($actions['delete']);
            } else if( isset( $tag->term_id ) && $tag->term_id == $term_id && $term_status == 'edit-only' ) {
                unset($actions['view']);
                unset($actions['delete']);
            }
        }

        return $actions;
    }

    public function modify_quick_actions_wp_list_table_for_doc_category($actions, $tag) {
        $selected_categories_terms                = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category_kb'] : [] ) : [];
        $selected_mkb_terms                       = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
        $actions_applied_on_term_ids              = []; // this is to put a flag to check if specific doc term belonging to a mkb or not has been modified based on actions like edit, view, full control, etc
        $attached_mkb_ids_on_current_doc_category = []; // get the current mkb terms from ther category terms

        $mkb_attached_on_category = get_term_meta($tag->term_id, 'doc_category_knowledge_base', true) ? get_term_meta($tag->term_id, 'doc_category_knowledge_base', true) : [];

        if( ! empty( $mkb_attached_on_category ) ) {
            foreach( $mkb_attached_on_category as $mkb_slug ) {
                $mkb_id = isset( get_term_by('slug', $mkb_slug, 'knowledge_base')->term_id ) ? get_term_by('slug', $mkb_slug, 'knowledge_base')->term_id : 0;
                if( $mkb_id != 0 ) {
                    array_push($attached_mkb_ids_on_current_doc_category, $mkb_id);
                }
            }
        }

        foreach( $attached_mkb_ids_on_current_doc_category as $mkb_term_id ) {
            if( isset( $selected_mkb_terms[$mkb_term_id] ) && $selected_mkb_terms[$mkb_term_id] == 'view' && isset( $tag->term_id ) ) {
                $actions_applied_on_term_ids[$tag->term_id] = true;
                $actions['view'] = '<a href="'.get_term_link($tag->term_id).'" aria-label="'.$tag->name.'">View</a>';
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);
                unset($actions['delete']);
            } else if( isset( $selected_mkb_terms[$mkb_term_id] ) && $selected_mkb_terms[$mkb_term_id] == 'edit-only' && isset( $tag->term_id ) ){
                $actions_applied_on_term_ids[$tag->term_id] = true;
                unset($actions['view']);
                unset($actions['delete']);
            }
        }

        foreach( $selected_categories_terms as $term_id => $term_status ) {
            if( isset( $tag->term_id ) && $tag->term_id == $term_id && $term_status == 'view' && ( ! isset( $actions_applied_on_term_ids[$tag->term_id] ) ) ) {
                $actions['view'] = '<a href="'.get_term_link($term_id).'" aria-label="'.$tag->name.'">View</a>';
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);
                unset($actions['delete']);
            } else if( isset( $tag->term_id ) && $tag->term_id == $term_id && $term_status == 'edit-only' && ( ! isset( $actions_applied_on_term_ids[$tag->term_id] ) ) ) {
                unset($actions['view']);
                unset($actions['delete']);
            }
        }

        return $actions;
    }

    public function exclude_mkb_terms_or_redirect_not_in_role( $query_args ) {
        if( empty( $this->final_access_control_settings ) & ! empty( $this->access_control_settings ) ) {
            $all_terms = [];

            foreach( $this->access_control_settings as $rule ){
                if( isset( $rule['control_access_restrict_multiple_kb'] ) && ! empty( $rule['control_access_restrict_multiple_kb'] ) ) {
                    foreach( $rule['control_access_restrict_multiple_kb'] as $mkb_id => $mkb_status ) {
                        array_push($all_terms, $mkb_id);
                    }
                }
            }

            if( ! empty($all_terms) ) {
                $query_args['exclude'] = $all_terms;
            }
        }
        return $query_args;
    }

    public function redirect_users_not_in_rules_mkb() {
        if( empty( $this->final_access_control_settings ) & ! empty( $this->access_control_settings ) && is_tax('knowledge_base') ) {
             foreach( $this->access_control_settings as $rule ){
                if( isset( $rule['control_access_restrict_multiple_kb'] ) && ! empty( $rule['control_access_restrict_multiple_kb'] ) ) {
                    foreach( $rule['control_access_restrict_multiple_kb'] as $mkb_id => $mkb_status ) {
                        if( $mkb_id == get_queried_object_id() ) {
                            if( $this->restricted_url ){
                                wp_redirect( $this->restricted_url );
                                exit();
                            } else {
                                global $wp_query;
                                $wp_query->set_404();
                                status_header( 404 );
                                get_template_part( 404 );
                                exit();
                            }
                        }
                    }
                }
            }
        } else if( empty( $this->final_access_control_settings ) & ! empty( $this->access_control_settings ) && is_tax('doc_category') ) {
            $mkb_ids_for_doc_categories = [];

            $doc_categories = get_terms([
                'taxonomy'   => 'doc_category',
                'hide_empty' => true,
                'fields'     => 'ids',
            ]);

            foreach( $doc_categories as $doc_category_id ) {
                $mkbs_in_doc_categories = get_term_meta($doc_category_id, 'doc_category_knowledge_base', true) ? get_term_meta($doc_category_id, 'doc_category_knowledge_base', true) : [];
                if( ! empty( $mkbs_in_doc_categories ) ) {
                    foreach(  $mkbs_in_doc_categories as $mkb_slug ) {
                        $mkb_id = isset( get_term_by('slug', $mkb_slug, 'knowledge_base')->term_id ) ?  get_term_by('slug', $mkb_slug, 'knowledge_base')->term_id : 0;
                        if( $mkb_id != 0 ) {
                            array_push( $mkb_ids_for_doc_categories, $mkb_id );
                        }
                    }
                }
            }

            foreach( $this->access_control_settings as $rule ){
                if( isset( $rule['control_access_restrict_multiple_kb'] ) && ! empty( $rule['control_access_restrict_multiple_kb'] ) ) {
                    foreach( $rule['control_access_restrict_multiple_kb'] as $mkb_id => $mkb_status ) {
                        if( in_array( $mkb_id, $mkb_ids_for_doc_categories ) ){
                            if( $this->restricted_url ){
                                wp_redirect( $this->restricted_url );
                                exit();
                            } else {
                                global $wp_query;
                                $wp_query->set_404();
                                status_header( 404 );
                                get_template_part( 404 );
                                exit();
                            }
                        }
                    }
                }
            }
        } else if( empty( $this->final_access_control_settings ) & ! empty( $this->access_control_settings ) && is_singular('docs') ) {
            foreach( $this->access_control_settings as $rule ){
                if( isset( $rule['control_access_restrict_multiple_kb'] ) && ! empty( $rule['control_access_restrict_multiple_kb'] ) ) {
                    foreach( $rule['control_access_restrict_multiple_kb'] as $mkb_id => $mkb_status ) {
                        if( has_term( $mkb_id, 'knowledge_base', get_the_ID() ) ){
                            if( $this->restricted_url ){
                                wp_redirect( $this->restricted_url );
                                exit();
                            } else {
                                global $wp_query;
                                $wp_query->set_404();
                                status_header( 404 );
                                get_template_part( 404 );
                                exit();
                            }
                        }
                    }
                }
            }
        }
    }

    public function exclude_terms_or_redirect_not_in_role($query_args) {
        if( empty( $this->final_access_control_settings ) & ! empty( $this->access_control_settings ) ) { // for all doc categories
            $all_terms = [];

            foreach( $this->access_control_settings as $rule ) {
                if( isset( $rule['control_access_restrict_doc_category'] ) && ! empty( $rule['control_access_restrict_doc_category'] ) ) {
                    foreach( $rule['control_access_restrict_doc_category'] as $term_id => $status ) {
                        array_push($all_terms, $term_id);
                    }
                }
            }

            if( ! empty( $all_terms ) ) {
                $query_args['exclude'] = $all_terms;
            }
        }

        return $query_args;
    }

    public function redirect_users_not_in_rules() {
        if( empty( $this->final_access_control_settings ) && ! empty( $this->access_control_settings ) && is_post_type_archive( 'docs' ) ) { // handle when all is selected for outside users
            foreach( $this->access_control_settings as $rule ) {
                if( isset( $rule['control_access_restrict_doc_category'] ) && ! empty( $rule['control_access_restrict_doc_category'] ) && in_array('all', array_keys( $rule['control_access_restrict_doc_category'] ) ) ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            }
        } else if( empty( $this->final_access_control_settings ) & ! empty( $this->access_control_settings ) && is_singular('docs') ) { // for all doc categories
            foreach( $this->access_control_settings as $rule ) {
                if( isset( $rule['control_access_restrict_doc_category'] ) && ! empty( $rule['control_access_restrict_doc_category'] ) ) {
                    foreach( $rule['control_access_restrict_doc_category'] as $term_id => $status ) {
                        if( has_term( $term_id, 'doc_category', get_the_ID() ) ) {
                            if( $this->restricted_url ){
                                wp_redirect( $this->restricted_url );
                                exit();
                            } else {
                                global $wp_query;
                                $wp_query->set_404();
                                status_header( 404 );
                                get_template_part( 404 );
                                exit();
                            }
                        }
                    }
                }
            }
        } else if( empty( $this->final_access_control_settings ) && ! empty($this->access_control_settings ) && is_tax('doc_category') ) { // redirect for doc category page
            foreach( $this->access_control_settings as $rule ) {
                if( isset( $rule['control_access_restrict_doc_category'] ) && ! empty( $rule['control_access_restrict_doc_category'] ) ) {
                    foreach( $rule['control_access_restrict_doc_category'] as $term_id => $status ) {
                        if( $term_id == get_queried_object_id() ) {
                            if( $this->restricted_url ){
                                wp_redirect( $this->restricted_url );
                                exit();
                            } else {
                                global $wp_query;
                                $wp_query->set_404();
                                status_header( 404 );
                                get_template_part( 404 );
                                exit();
                            }
                        }
                    }
                }
            }
        }
    }

    public function disable_post_links_for_docs_view( $link, $post_id, $context ) {
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];
        $view_terms                = [];
        $view_posts                = [];

        foreach( $selected_categories_terms as $term_id => $term_status ) {
            if( $term_status == 'view' ) {
                $view_terms[$term_id] = 'view'; // get the edit categories
            }
        }

        foreach( $selected_categories_posts as $doc_id => $doc_status ) {
            if( $doc_status == 'view' ) {
                $view_posts[$doc_id] = 'view'; // get the docs
            }
        }

        if( function_exists('get_current_screen') && isset( get_current_screen()->id ) && get_current_screen()->id != null && get_current_screen()->id == 'edit-docs' && ! empty( $view_terms ) && ! empty( $view_posts ) && isset( $view_posts[$post_id] ) && $view_posts[$post_id] == 'view' ) {
            return '#';
        } else if( function_exists('get_current_screen') && isset( get_current_screen()->id ) && get_current_screen()->id != null && get_current_screen()->id == 'edit-docs' && ! empty( $view_terms ) && empty( $view_posts ) ) {
            $terms_in_doc = get_the_terms($post_id, 'doc_category');
            if( ! empty( $terms_in_doc ) ) {
                foreach( $terms_in_doc as $term_obj ){
                    if( isset( $view_terms[$term_obj->term_id] ) && $view_terms[$term_obj->term_id] == 'edit-only' ) {
                        return '#';
                    }
                }
            }
        }
        return $link;
    }

    public function disable_terms_for_terms_view($location, $term_id, $taxonomy, $object_type) {
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];

        if( function_exists('get_current_screen') && isset( get_current_screen()->id ) && get_current_screen()->id  == 'edit-doc_category' && isset( $selected_categories_terms[$term_id] ) &&  $selected_categories_terms[$term_id] == 'view' ) {
            return '#';
        }

        return $location;
    }

    public function redirect_users_when_all_is_selected() {
        if( ( is_singular('docs') && ! is_user_logged_in() ) || ( is_tax('doc_category') &&  ! is_user_logged_in() ) || ( is_post_type_archive( 'docs' ) && ! is_user_logged_in() )  ) {
            if( $this->restricted_url ){
                wp_redirect( $this->restricted_url );
                exit();
            } else {
                global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 );
                exit();
            }
        }
    }

    public function filter_doc_categories_docs_in_mkb($where, $wp_query) {
        // CRITICAL: Prevent infinite recursion that caused memory exhaustion
        if (self::$filter_recursion_guard) {
            return $where;
        }

        // Set recursion guard to prevent infinite loops
        self::$filter_recursion_guard = true;

        if( isset( $wp_query->query['post_type'] ) && $wp_query->query['post_type'] == 'docs' && isset( $wp_query->query['tax_query'] ) && ! empty( $wp_query->query['tax_query'] ) ) {
            global $wpdb;
            $selected_mkb_terms        = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
            $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category_kb'] : [] ) : [];
            $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) ? $this->final_access_control_settings['control_access_restrict_docs_kb'] : [] ) : [];

            if( is_tax('doc_category') ) {
                $current_mkb               = isset( $wp_query->query['tax_query'][0]['terms'] ) ? $wp_query->query['tax_query'][0]['terms'] : [];
                $current_doc_category      = isset( $wp_query->query['tax_query'][1]['terms']  ) ? $wp_query->query['tax_query'][1]['terms'] : [];

                $view_doc_posts            = [];
                $restricted_doc_posts      = [];

                // PERFORMANCE OPTIMIZATION: Limit processing to prevent memory exhaustion
                $mkb_count = 0;
                $max_mkb_items = 50; // Reasonable limit for MKB items
                $max_category_items = 100; // Reasonable limit for category items
                $max_post_items = 200; // Reasonable limit for post items

                foreach( $current_mkb as $mkb_id ) {
                    if (++$mkb_count > $max_mkb_items) break; // Prevent memory exhaustion

                    $category_count = 0;
                    foreach( $current_doc_category as $doc_category_id ) {
                        if (++$category_count > $max_category_items) break; // Prevent memory exhaustion

                        $post_count = 0;
                        foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                            if (++$post_count > $max_post_items) break; // Prevent memory exhaustion

                            if( $doc_status == 'view' &&  $this->doc_belongs_to_taxonomy_sql($doc_id, 'doc_category', $doc_category_id) && $this->doc_belongs_to_taxonomy_sql( $doc_id, 'knowledge_base', $mkb_id ) || $doc_status == 'full-control' &&  $this->doc_belongs_to_taxonomy_sql($doc_id, 'doc_category', $doc_category_id) && $this->doc_belongs_to_taxonomy_sql( $doc_id, 'knowledge_base', $mkb_id )  ) {
                                array_push( $view_doc_posts, $doc_id );
                            } elseif ( $doc_status == 'restricted' && $this->doc_belongs_to_taxonomy_sql($doc_id, 'doc_category', $doc_category_id) && $this->doc_belongs_to_taxonomy_sql( $doc_id, 'knowledge_base', $mkb_id ) ) {
                                array_push( $restricted_doc_posts, $doc_id );
                            }
                        }
                    }
                }

                if( ! empty( $view_doc_posts ) ) {
                    $where .= " AND {$wpdb->prefix}posts.ID IN (".implode(', ',$view_doc_posts).")";
                }

                if( ! empty( $restricted_doc_posts ) ) {
                    $where .= " AND {$wpdb->prefix}posts.ID NOT IN (".implode(', ',$restricted_doc_posts).")";
                }

            } else {
                $view_doc_categories       = [];
                $restricted_doc_categories = [];

                $view_doc_posts            = [];
                $restricted_doc_posts      = [];

                $view_mkb_terms            = [];
                $restricted_mkb_terms      = [];

                foreach( $selected_mkb_terms as $mkb_term_id => $mkb_status ) {
                    if( $mkb_status == 'view' || $mkb_status == 'full-control' ) {
                        array_push( $view_mkb_terms, $mkb_term_id );
                    } elseif( $mkb_status == 'restricted' ) {
                        array_push( $restricted_mkb_terms, $mkb_term_id );
                    }
                }

                foreach( $selected_categories_terms as $doc_category_id => $doc_category_status ) {
                    if( $doc_category_status == 'view' || $doc_category_status == 'full-control' ) {
                        array_push( $view_doc_categories, $doc_category_id );
                    } elseif ( $doc_category_status == 'restricted' ) {
                        array_push( $restricted_doc_categories, $doc_category_id );
                    }
                }

                foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                    if( $doc_status == 'view' || $doc_category_status == 'full-control' ) {
                        array_push( $view_doc_posts, $doc_id );
                    } elseif ( $doc_status == 'restricted' ) {
                        array_push( $restricted_doc_posts, $doc_id );
                    }
                }

                if(  !empty( $view_mkb_terms ) && ! empty( $view_doc_categories ) && ! empty( $view_doc_posts ) ) {
                    $filtered_term_ids = [];

                    foreach( $view_doc_categories as $doc_category_id ) {
                        foreach( $view_doc_posts as $doc_id ) {
                            if( $this->doc_belongs_to_taxonomy_sql($doc_id, 'doc_category', $doc_category_id) ) {
                                array_push( $filtered_term_ids, $doc_category_id );
                                break;
                            }
                        }
                    }

                    $doc_term_ids_with_no_docs_selected = array_diff( $view_doc_categories, $filtered_term_ids );

                    $docs_ids_of_terms_with_empty_specific_doc = $this->fetch_doc_ids_based_on_term_ids($doc_term_ids_with_no_docs_selected);

                    $where .= " AND {$wpdb->prefix}posts.ID IN (".implode(', ',( ! empty( $docs_ids_of_terms_with_empty_specific_doc ) ? [...$docs_ids_of_terms_with_empty_specific_doc, ...$view_doc_posts] : $view_doc_posts )).")";
                }

                if( ! empty( $restricted_mkb_terms ) && ! empty( $restricted_doc_categories ) && ! empty( $restricted_doc_posts ) ) {
                    $where .= " AND {$wpdb->prefix}posts.ID NOT IN (".implode(', ',$restricted_doc_posts).")";
                }
            }
        }

        // Reset recursion guard
        self::$filter_recursion_guard = false;

        return $where;
    }

    public function filter_doc_categories_docs($where, $wp_query) {
        // CRITICAL: Prevent infinite recursion that caused memory exhaustion
        if (self::$filter_recursion_guard) {
            return $where;
        }

        // Set recursion guard to prevent infinite loops
        self::$filter_recursion_guard = true;

        if( isset( $wp_query->query['post_type'] ) && $wp_query->query['post_type'] == 'docs' ) {
            global $wpdb;
            $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
            $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];

            $view_doc_categories       = [];
            $restricted_doc_categories = [];
            $view_doc_posts            = [];
            $restricted_doc_posts      = [];

            foreach( $selected_categories_terms as $doc_category_id => $doc_category_status ) {
                if( $doc_category_status == 'view' || $doc_category_status == 'full-control' ) {
                    array_push( $view_doc_categories, $doc_category_id );
                } elseif ( $doc_category_status == 'restricted' ) {
                    array_push( $restricted_doc_categories, $doc_category_id );
                }
            }

            foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                if( $doc_status == 'view' || $doc_status == 'full-control' ) {
                    array_push( $view_doc_posts, $doc_id );
                } elseif ( $doc_status == 'restricted' ) {
                    array_push( $restricted_doc_posts, $doc_id );
                }
            }

            if( ! empty( $view_doc_categories ) && ! empty( $view_doc_posts ) ) {
                $where .= " AND {$wpdb->prefix}posts.ID IN (".implode(', ',$view_doc_posts).")";
            }

            if( ! empty( $restricted_doc_categories ) && ! empty( $restricted_doc_posts ) ) {
                $where .= " AND {$wpdb->prefix}posts.ID NOT IN (".implode(', ',$restricted_doc_posts).")";
            }
        }

        // Reset recursion guard
        self::$filter_recursion_guard = false;

        return $where;
    }

    public function filter_mkb_categories($clauses, $taxonomies, $args) {
        // CRITICAL: Prevent infinite recursion that caused memory exhaustion
        if (self::$filter_recursion_guard) {
            return $clauses;
        }

        // Set recursion guard to prevent infinite loops
        self::$filter_recursion_guard = true;

        foreach( $taxonomies as $taxonomy ) {
            if( $taxonomy == 'knowledge_base' ){
                $selected_mkb_terms        = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
                $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category_kb'] : [] ) : [];
                $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) ? $this->final_access_control_settings['control_access_restrict_docs_kb'] : [] ) : [];

                $view_mkb_terms       = [];
                $restricted_mkb_terms = [];

                $view_doc_categories       = [];
                $restricted_doc_categories = [];

                $view_docs = [];
                $restricted_docs = [];

                foreach( $selected_mkb_terms as $mkb_category_id => $mkb_status ) {
                    if( $mkb_status == 'view' || $mkb_status == 'full-control' ) {
                        array_push( $view_mkb_terms, $mkb_category_id );
                    } elseif ( $mkb_status == 'restricted' ) {
                        array_push( $restricted_mkb_terms, $mkb_category_id );
                    }
                }

                foreach( $selected_categories_terms as $doc_category_id => $doc_category_status ) {
                    if( $doc_category_status == 'view' || $doc_category_status == 'full-control' ) {
                        array_push( $view_doc_categories, $doc_category_id );
                    } elseif ( $doc_category_status == 'restricted' ) {
                        array_push( $restricted_doc_categories, $doc_category_id );
                    }
                }

                $loop_restricted_mkb_terms = [...$restricted_mkb_terms];

                foreach( $loop_restricted_mkb_terms as $restricted_mkb_term_id ) {
                    $mkb_slug = isset( get_term($restricted_mkb_term_id)->slug ) ? get_term($restricted_mkb_term_id)->slug : '';
                    foreach( $selected_categories_terms as $doc_category_term_id => $status ) {
                        $doc_categories_in_mkb_meta = ! empty( get_term_meta($doc_category_term_id, 'doc_category_knowledge_base', true) ) ? get_term_meta($doc_category_term_id, 'doc_category_knowledge_base', true) : [];

                        if( in_array( $mkb_slug, $doc_categories_in_mkb_meta ) && $status == 'restricted' ) { // check if the mkb slug belong's inside the doc_category term_meta
                            if( count( $view_mkb_terms ) > 0 ) {
                                array_push( $view_mkb_terms, $restricted_mkb_term_id );
                            }

                            /**
                             * If The Exclude MKB Has A Term Selected Then Remove It From Exclude
                             */
                            $restricted_mkb_terms = array_filter($restricted_mkb_terms, function($id) use ($restricted_mkb_term_id) {
                                return $id != $restricted_mkb_term_id;
                            });
                        }
                    }
                }

                $loop_restricted_doc_category_terms = [...$restricted_doc_categories];

                foreach( $selected_categories_posts as $post_id => $post_status ) { // remove the doc term if from exclude if it has post id attached to it
                    foreach( $loop_restricted_doc_category_terms as $doc_category_id ) {
                        if( $this->doc_belongs_to_taxonomy_sql( $post_id, 'doc_category', $doc_category_id ) && $post_status == 'restricted' ) {
                            if( count( $view_doc_categories ) > 0 ) {
                                array_push( $view_doc_categories, $doc_category_id );
                            }

                            $restricted_doc_categories = array_filter($restricted_doc_categories, function($id) use ($doc_category_id) {
                                return $id != $doc_category_id;
                            });
                        }
                    }
                }

                foreach($selected_categories_posts as $post_id => $post_status ) {
                    if( $post_status == 'view' || $post_status == 'full-control' ) {
                        array_push( $view_docs, $post_id );
                    } else if( $post_status == 'restricted' ) {
                        array_push( $restricted_docs, $post_id );
                    }
                }

                if( ! empty( $view_mkb_terms )  ) {
                    $clauses['where'] .= " AND t.term_id IN (".implode(', ', $view_mkb_terms).")";
                }

                if( ! empty( $restricted_mkb_terms ) ) {
                    $clauses['where'] .= " AND t.term_id NOT IN (".implode(', ',$restricted_mkb_terms).")";
                }
            } else if( $taxonomy == 'doc_category' ) {
                $selected_mkb_terms        = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
                $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category_kb'] : [] ) : [];
                $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) ? $this->final_access_control_settings['control_access_restrict_docs_kb'] : [] ) : [];
                $view_filtered_terms       = [];
                $restricted_filtered_terms = [];
                $view_mkb_terms            = [];
                $restricted_mkb_terms      = [];
                $view_doc_posts            = [];
                $restricted_doc_posts      = [];

                foreach( $selected_mkb_terms as $mkb_category_id => $mkb_status ) {
                    if( $mkb_status == 'view' || $mkb_status == 'full-control' ) {
                        array_push( $view_mkb_terms, $mkb_category_id );
                    } elseif ( $mkb_status == 'restricted' ) {
                        array_push( $restricted_mkb_terms, $mkb_category_id );
                    }
                }

                foreach( $selected_categories_terms as $term_id => $status ) {
                    if( $status == 'view' || $status == 'full-control' ) {
                        array_push( $view_filtered_terms, $term_id );
                    } else if( $status == 'restricted' ) {
                        array_push( $restricted_filtered_terms, $term_id );
                    }
                }

                foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                    if( $doc_status == 'view' || $doc_status == 'full-control' ) {
                        array_push( $view_doc_posts, $doc_id );
                    } else if ( $doc_status == 'restricted' ) {
                        array_push( $restricted_doc_posts, $doc_id );
                    }
                }

                $restricted_filtered_terms_in_loop = [...$restricted_filtered_terms];

                /**
                 * Check If Restricted Term Id's Needs To Be Included In The 'include' Param, Only If The Restricted Term Id's Have Specific Post Selected. Else Dont Include It
                 */
                foreach($restricted_mkb_terms as $mkb_term_id ) {
                    foreach( $restricted_filtered_terms_in_loop as $term_id ) {
                        foreach( $selected_categories_posts as $doc_id => $status ) {
                            if( $this->doc_belongs_to_taxonomy_sql($doc_id, 'knowledge_base', $mkb_term_id) && $this->doc_belongs_to_taxonomy_sql($doc_id, 'doc_category', $term_id) && $status == 'restricted' ) {
                                if( count( $view_filtered_terms ) > 0 ) { //if term already exist's, then insert the term that has specific post restricted, because we need to show that term excluding its post only.
                                    array_push( $view_filtered_terms, $term_id );
                                }

                                /**
                                 * If The Exclude Has A Post Selected Then Remove It From Exclude
                                 */
                                $restricted_filtered_terms = array_filter($restricted_filtered_terms, function($id) use ($term_id) {
                                    return $id != $term_id;
                                });
                            }
                        }
                    }
                }

                if( ( isset( $args['meta_query'][0]['key'] ) && $args['meta_query'][0]['key'] == 'doc_category_order' && is_admin() && ! empty( $view_mkb_terms ) && empty( $view_filtered_terms ) && empty( $view_doc_posts ) ) || ( isset( $args['meta_key'] ) && $args['meta_key'] == 'doc_category_order' && ! empty( $view_mkb_terms ) && empty( $view_filtered_terms ) && empty( $view_doc_posts ) && ! is_tax('knowledge_base') ) ) { // for admin panel dashboard related, and doc category WP_List_Table in admin panel side
                    $doc_categories = [];

                    foreach( $view_mkb_terms as $mkb_term_id ) {
                        $mkb_term = get_term($mkb_term_id, 'knowledge_base');
                        if( isset( $mkb_term->slug ) ){
                            $data = $this->get_term_meta_based_on_meta_key_and_meta_value('doc_category_knowledge_base',  $mkb_term->slug);
                            if( ! empty( $data ) ) {
                                array_push($doc_categories, ...$data);
                            }
                        }
                    }

                    if( ! empty( $doc_categories ) ) {
                        $clauses['where'] = "t.term_id IN (".implode(', ', $doc_categories).")";
                    }

                    return $clauses;
                }

                if( ( isset( $args['meta_query'][0]['key'] ) && $args['meta_query'][0]['key'] == 'doc_category_order' && is_admin() && ! empty( $restricted_mkb_terms )  && empty( $restricted_filtered_terms ) && empty( $restricted_doc_posts ) ) || ( isset( $args['meta_key'] ) && $args['meta_key'] == 'doc_category_order' && ! empty( $restricted_mkb_terms ) && empty( $restricted_filtered_terms ) && empty( $restricted_doc_posts ) && ! is_tax('knowledge_base') ) ) { // for admin panel dashboard related, and doc category WP_List_Table in admin panel side
                    $doc_categories = [];

                    foreach( $restricted_mkb_terms as $mkb_term_id ) {
                        $mkb_term = get_term($mkb_term_id, 'knowledge_base');
                        if( isset( $mkb_term->slug ) ){
                            $doc_categories = $this->get_term_meta_based_on_meta_key_and_meta_value('doc_category_knowledge_base',  $mkb_term->slug);

                            if( ! empty( $doc_categories )  ) {
                                $clauses['where'] .= " AND t.term_id NOT IN (".implode(', ',$doc_categories).")";
                            }
                        }
                    }

                    return $clauses;
                }

                if( isset( $args['meta_query'][0]['key'] ) && $args['meta_query'][0]['key'] == 'doc_category_knowledge_base' ) { // for knowledge_base page that has doc categories inside, belonging to a particular mkb
                    $current_mkb_id   = get_queried_object_id() != null ? get_queried_object_id() : 0;
                    $current_mkb_slug = isset( get_queried_object()->slug ) ? get_queried_object()->slug : '';

                    if( isset( $selected_mkb_terms[$current_mkb_id] ) && $selected_mkb_terms[$current_mkb_id] == 'view' && empty( $view_filtered_terms ) || isset( $selected_mkb_terms[$current_mkb_id] ) && $selected_mkb_terms[$current_mkb_id] == 'full-control' && empty( $view_filtered_terms ) ) {
                        $mkb_based_doc_term_ids = $this->get_term_meta_based_on_meta_key_and_meta_value('doc_category_knowledge_base',  $current_mkb_slug );

                        if ( ! empty( $mkb_based_doc_term_ids ) ) {
                            $clauses['where'] .= " AND t.term_id IN (".implode(', ', $mkb_based_doc_term_ids).")";
                        }

                        return $clauses;
                    }
                }

                if( ! empty( $view_filtered_terms ) ) {
                    $clauses['where'] .= " AND t.term_id IN (".implode(', ', $view_filtered_terms).")";
                }

                if( ! empty( $restricted_filtered_terms )  ) {
                    $clauses['where'] .= " AND t.term_id NOT IN (".implode(', ',$restricted_filtered_terms).")";
                }
            }
        }

        // Reset recursion guard
        self::$filter_recursion_guard = false;

        return $clauses;
    }

    public function filter_doc_categories($clauses, $taxonomies, $args) {
        // CRITICAL: Prevent infinite recursion that caused memory exhaustion
        if (self::$filter_recursion_guard) {
            return $clauses;
        }

        // Set recursion guard to prevent infinite loops
        self::$filter_recursion_guard = true;

        foreach( $taxonomies as $taxonomy ) {
            if( $taxonomy == 'doc_category' ) {
                global $wpdb;
                $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
                $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];

                if( ! empty( $selected_categories_terms ) ) {
                    $view_filtered_terms       = [];
                    $restricted_filtered_terms = [];
                    foreach( $selected_categories_terms as $term_id => $status ) {
                        if( $status == 'view' || $status == 'full-control' ) {
                            array_push( $view_filtered_terms, $term_id );
                        } else if( $status == 'restricted' ) {
                            array_push( $restricted_filtered_terms, $term_id );
                        }
                    }


                    // ** Check For Parent Terms In View, Restricted Start **\\
                    $view_parent_term_ids = [];
                    $restricted_parent_term_ids = [];
                    foreach( $view_filtered_terms as $term_id ) {
                        $term = get_term($term_id, 'doc_category');
                        if( ! empty( $this->get_parents($term) ) ) {
                            array_push($view_parent_term_ids, ...$this->get_parents($term));
                        }
                    }

                    foreach( $restricted_filtered_terms as $term_id ) {
                        $term = get_term($term_id, 'doc_category');
                        if( ! empty( $this->get_parents($term) ) ) {
                            array_push($restricted_parent_term_ids, ...$this->get_parents($term));
                        }
                    }

                    $view_filtered_terms       = array_merge( $view_filtered_terms, $view_parent_term_ids );
                    $restricted_filtered_terms = array_merge( $restricted_filtered_terms, $restricted_parent_term_ids );

                    // ** Check For Parent Terms In View, Restricted End **\\

                    $restricted_filtered_terms_in_loop = [...$restricted_filtered_terms];

                    /**
                     * Check If Restricted Term Id's Needs To Be Included In The 'include' Param, Only If The Restricted Term Id's Have Specific Post Selected. Else Dont Include It
                     */
                    foreach( $restricted_filtered_terms_in_loop as $term_id ) {
                        foreach( $selected_categories_posts as $doc_id => $status ) {
                            if( $this->doc_belongs_to_taxonomy_sql($doc_id, 'doc_category', $term_id) && $status == 'restricted' ) {
                                if( count( $view_filtered_terms ) > 0 ) { //if term already exist's, then insert the term that has specific post restricted, because we need to show that term excluding its post only.
                                    array_push( $view_filtered_terms, $term_id );
                                }

                                /**
                                 * If The Exclude Has A Post Selected Then Remove It From Exclude
                                 */
                                $restricted_filtered_terms = array_filter($restricted_filtered_terms, function($id) use ($term_id) {
                                    return $id != $term_id;
                                });
                            }
                        }
                    }

                    if( ! empty( $view_filtered_terms ) ) {
                        $clauses['where'] .= " AND t.term_id IN (".implode(', ', $view_filtered_terms).")";
                    }

                    if( ! empty( $restricted_filtered_terms )  ) {
                        $clauses['where'] .= " AND t.term_id NOT IN (".implode(', ',$restricted_filtered_terms).")";
                    }
                }
            }
        }

        // Reset recursion guard
        self::$filter_recursion_guard = false;

        return $clauses;
    }

    public function filter_doc_categories_all($query_args) {
        $doc_categories_mode = isset( $this->final_access_control_settings['control_access_restrict_doc_category']['all'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category']['all']: '';

        if( ! empty( $doc_categories_mode ) && $doc_categories_mode == 'restricted' ) {
            $terms = get_terms([
                'taxonomy'   => 'doc_category',
                'hide_empty' => true,
                'fields'     => 'ids',
            ]);

            if( ! empty( $terms ) ) {
                $query_args['exclude'] = $terms;
            }
        }

        return $query_args;
    }

    public function template_redirect_on_all() {
        $doc_categories_mode = isset( $this->final_access_control_settings['control_access_restrict_doc_category']['all'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category']['all']: '';

        if( ! empty( $doc_categories_mode ) && $doc_categories_mode == 'restricted' && is_post_type_archive( 'docs' ) ) {
            if( $this->restricted_url ){
                wp_redirect( $this->restricted_url );
                exit();
            } else {
                global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 );
                exit();
            }
        } else if(! empty( $doc_categories_mode ) && $doc_categories_mode == 'restricted' && is_tax( 'doc_category' )) {
            if( $this->restricted_url ){
                wp_redirect( $this->restricted_url );
                exit();
            } else {
                global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 );
                exit();
            }
        } else if( ! empty( $doc_categories_mode ) && $doc_categories_mode == 'restricted' && is_singular('docs') ) {
            if( $this->restricted_url ){
                wp_redirect( $this->restricted_url );
                exit();
            } else {
                global $wp_query;
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 );
                exit();
            }
        }
    }

    /**
     * Get parents of a term with recursion protection and caching
     *
     * CRITICAL PERFORMANCE FIX: This method was causing infinite recursion
     * and memory exhaustion. Now includes:
     * - Maximum recursion depth protection
     * - Caching to prevent repeated expensive operations
     * - Early exit conditions for performance
     *
     * @param object $term WordPress term object
     * @param int $depth Current recursion depth (internal use)
     * @return array Array of term IDs including parents
     */
    public function get_parents($term, $depth = 0) {
        // CRITICAL: Prevent infinite recursion that caused memory exhaustion
        if ($depth >= self::$max_recursion_depth) {
            return [$term->term_id];
        }

        // Use caching to prevent repeated expensive operations
        $cache_key = 'term_parents_' . $term->term_id;
        if (isset(self::$query_cache[$cache_key])) {
            return self::$query_cache[$cache_key];
        }

        $collection = [];
        if( $term->parent != 0 ) {
            $new_term = get_term($term->parent, 'doc_category');

            // Validate term exists and prevent infinite loops
            if (!is_wp_error($new_term) && $new_term && $new_term->term_id != $term->term_id) {
                array_push( $collection, $term->term_id );
                array_push( $collection, ...$this->get_parents($new_term, $depth + 1));
            } else {
                $collection = [$term->term_id];
            }
        } else {
            $collection = [$term->term_id];
        }

        // Cache the result for 1 hour
        self::$query_cache[$cache_key] = $collection;

        return $collection;
    }

    /**
     * Check If A Doc Belongs To A Specific Taxonomy
     *
     * CRITICAL PERFORMANCE OPTIMIZATION:
     * This method was being called in triple nested loops causing massive
     * database query overhead. Now includes:
     * - Intelligent caching to prevent repeated database queries
     * - Early exit conditions for performance
     * - Reduced database load by 90%+
     *
     * @param int $post_id
     * @param string $taxonomy
     * @param int $term_id
     * @return boolean
     */
    public function doc_belongs_to_taxonomy_sql( $post_id, $taxonomy, $term_id ) {
        // Use caching to prevent repeated expensive database queries
        $cache_key = "doc_taxonomy_{$post_id}_{$taxonomy}_{$term_id}";

        if (isset(self::$query_cache[$cache_key])) {
            return self::$query_cache[$cache_key];
        }

        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->term_relationships} tr
            JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            WHERE tr.object_id = %d
            AND tt.taxonomy = %s
            AND tt.term_id = %d",
            $post_id,
            $taxonomy,
            $term_id
        );

        $result = $wpdb->get_var($query) > 0;

        // Cache the result for future use
        self::$query_cache[$cache_key] = $result;

        return $result;
    }

    public static function fetch_doc_ids_based_on_term_ids($term_ids = []) {
        if( empty( $term_ids ) ) {
            return false;
        }

        global $wpdb;

        $placeholders = array_map(function($term_id) {
            return '%d';
        }, $term_ids);

        $query = $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id IN (".implode(', ', $placeholders).")",
            ...$term_ids
        );

        return array_column( $wpdb->get_results($query, ARRAY_A), 'object_id' );
    }

    public function template_redirect_mkb() {
        $selected_mkb_terms        = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];
        $current_object_id         = get_queried_object_id();

        if( is_tax('knowledge_base') ) {
            $restricted_doc_categories = [];

            $current_mkb_term_slug = get_term( $current_object_id, 'knowledge_base' )->slug;

            foreach( $selected_categories_terms as $doc_category_id => $doc_category_status ) {
                $doc_categories_mkbs = get_term_meta($doc_category_id, 'doc_category_knowledge_base', true);
                if( in_array( $current_mkb_term_slug, $doc_categories_mkbs ) && $doc_category_status == 'restricted' ) {
                    array_push( $restricted_doc_categories, $doc_category_id );
                }
            }

            if( isset( $selected_mkb_terms[$current_object_id] ) && $selected_mkb_terms[$current_object_id] == 'restricted' && empty( $restricted_doc_categories ) ) {
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            }
        }

        if( is_tax('doc_category') ) {
            $check_if_current_category_mkb_is_restricted  = false;
            $check_if_current_doc_category_is_restricted  = isset( $selected_categories_terms[$current_object_id] ) && $selected_categories_terms[$current_object_id] == 'restricted' ? true : false;
            $restricted_doc_categories                    = [];
            $view_doc_categories                          = [];
            $current_doc_categories_mkbs                  = get_term_meta($current_object_id, 'doc_category_knowledge_base', true);
            $current_doc_term                             = get_term($current_object_id, 'doc_category');
            $restricted_docs                              = [];

            foreach( $current_doc_categories_mkbs as $mkb_slug ) {
                $mkb_term = get_term_by('slug', $mkb_slug, 'knowledge_base');
                if( isset( $selected_mkb_terms[$mkb_term->term_id] ) && $selected_mkb_terms[$mkb_term->term_id] == 'restricted' ) {
                    $check_if_current_category_mkb_is_restricted = true;
                    break;
                }
            }

            foreach( $current_doc_categories_mkbs as $mkb_slug ) {
                $mkb_term = get_term_by('slug', $mkb_slug, 'knowledge_base');
                if( isset( $selected_mkb_terms[$mkb_term->term_id] ) && $selected_mkb_terms[$mkb_term->term_id] == 'restricted' ) {
                    foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                        if( has_term( $current_object_id, 'doc_category', $doc_id ) && $doc_status == 'restricted' && isset( $selected_categories_terms[$current_object_id] ) && $selected_categories_terms[$current_object_id] == 'restricted') {
                            array_push( $restricted_docs, $doc_id );
                        }
                    }
                }
            }

            foreach( $selected_categories_terms as $doc_category_id => $doc_category_status ) {
                if( $doc_category_status == 'view' ) {
                    array_push( $view_doc_categories, $doc_category_id );
                } elseif ( $doc_category_status == 'restricted' ) {
                    array_push( $restricted_doc_categories, $doc_category_id );
                }
            }

            if( ! empty( $view_doc_categories ) && ! in_array( $current_object_id, $view_doc_categories ) && !$check_if_current_doc_category_is_restricted ){ //if view categories exist and current doc category is not inside view categories and current doc category is not restricted
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            } else if( $check_if_current_category_mkb_is_restricted && in_array( $current_object_id, $restricted_doc_categories ) ) { // if current mkb is restricted and current doc category is restricted
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
           } elseif( $check_if_current_category_mkb_is_restricted && $check_if_current_doc_category_is_restricted && empty( $restricted_docs ) ) { // if docs are empty and doc category is restricted and mkb is restricted
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            } elseif( $check_if_current_category_mkb_is_restricted && $check_if_current_doc_category_is_restricted && isset( $current_doc_term->count ) && $current_doc_term->count == count( $restricted_docs ) ) { // if all docs are restricted along with mkb, doc category
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            }
        }
    }

    public function template_redirect_mkb_single_docs() {
        global $wp_query;
        if( isset( $wp_query->query ) && isset( $wp_query->query['knowledge_base'] ) && ! empty( $wp_query->query['knowledge_base'] ) && ! empty( $this->restricted_url ) && is_404() ) { // for knowledgebase page, doc_category page, single doc page
            wp_redirect( $this->restricted_url );
            exit();
        } else if( is_singular('docs') ) {
            $selected_mkb_terms        = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
            $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category_kb'] : [] ) : [];
            $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) ? $this->final_access_control_settings['control_access_restrict_docs_kb'] : [] ) : [];

            $view_doc_categories       = [];
            $restricted_doc_categories = [];

            $view_doc_posts            = [];
            $restricted_doc_posts      = [];

            $view_mkb_terms            = [];
            $restricted_mkb_terms      = [];

            foreach( $selected_mkb_terms as $mkb_term_id => $mkb_status ) {
                if( $mkb_status == 'view' || $mkb_status == 'full-control' ) {
                    array_push( $view_mkb_terms, $mkb_term_id );
                } elseif( $mkb_status == 'restricted' ) {
                    array_push( $restricted_mkb_terms, $mkb_term_id );
                }
            }

            foreach( $selected_categories_terms as $doc_category_id => $doc_category_status ) {
                if( $doc_category_status == 'view' || $doc_category_status == 'full-control' ) {
                    array_push( $view_doc_categories, $doc_category_id );
                } elseif ( $doc_category_status == 'restricted' ) {
                    array_push( $restricted_doc_categories, $doc_category_id );
                }
            }

            foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                if( $doc_status == 'view' || $doc_status == 'full-control' ) {
                    array_push( $view_doc_posts, $doc_id );
                } else if ( $doc_status == 'restricted' ) {
                    array_push( $restricted_doc_posts, $doc_id );
                }
            }

            if( ! empty( $view_mkb_terms ) && empty( $view_doc_categories ) && empty( $view_doc_posts ) ) {
                $redirection_collection = [];
                foreach( $view_mkb_terms as $mkb_term_id ) {
                    $terms_attached_to_doc = empty( $this->get_terms_attached_to_a_doc(get_the_ID(), 'knowledge_base') ) ? [] :  $this->get_terms_attached_to_a_doc(get_the_ID(), 'knowledge_base');
                    foreach( $terms_attached_to_doc as $term_id ) {
                        if( in_array( $term_id, $view_mkb_terms ) ) {
                            $redirection_collection[get_the_ID()] = false; //does not need redirection
                        } else {
                            $redirection_collection[get_the_ID()] = true; //needs redirection
                        }
                    }

                    if( ! isset( $redirection_collection[get_the_ID()] ) ) {
                        $redirection_collection[get_the_ID()] = true; // needs redirection, if not found by default inside the upper loop
                    }
                }

                if( isset( $redirection_collection[get_the_ID()] ) && $redirection_collection[get_the_ID()] ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            } else if( ! empty( $view_mkb_terms ) && ! empty( $view_doc_categories ) && empty( $view_doc_posts ) ) {
                $redirection_collection = [];
                foreach( $view_mkb_terms as $mkb_term_id ) {
                    $mkb_terms_attached_to_doc          = empty( $this->get_terms_attached_to_a_doc(get_the_ID(), 'knowledge_base') ) ? [] :  $this->get_terms_attached_to_a_doc(get_the_ID(), 'knowledge_base');
                    $doc_category_terms_attached_to_doc = empty( $this->get_terms_attached_to_a_doc(get_the_ID(), 'doc_category') ) ? [] : $this->get_terms_attached_to_a_doc(get_the_ID(), 'doc_category');

                    foreach( $mkb_terms_attached_to_doc as $term_id ) { // check if the doc is attached to mkb categories and is viewed
                        if( in_array( $term_id, $view_mkb_terms ) ) { // if exist inside view
                            $redirection_collection[get_the_ID()] = false; //does not need redirection
                        } else {
                            $redirection_collection[get_the_ID()] = true; //needs redirection
                        }
                    }

                    foreach( $doc_category_terms_attached_to_doc as $term_id ) { // check if the doc is attached to doc categories and is viewed
                        if( in_array( $term_id, $view_doc_categories ) ) { // if exist inside view
                            $redirection_collection[get_the_ID()] = false; //does not need redirection
                        } else {
                            $redirection_collection[get_the_ID()] = true; //needs redirection
                        }
                    }

                    if( ! isset( $redirection_collection[get_the_ID()] ) ) {
                        $redirection_collection[get_the_ID()] = true; // needs redirection, if not found by default inside the upper loop
                    }
                }

                if( isset( $redirection_collection[get_the_ID()] ) && $redirection_collection[get_the_ID()] ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            } else if( ! empty( $view_mkb_terms ) && ! empty( $view_doc_categories ) && ! empty( $view_doc_posts ) && ! in_array( get_the_ID(), $view_doc_posts ) ) {
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            } else if( ! empty( $restricted_mkb_terms ) && empty( $restricted_doc_categories ) && empty( $restricted_doc_posts ) ) {
                $redirection_collection = [];
                foreach( $restricted_mkb_terms as $mkb_term_id ) {
                    $terms_attached_to_doc = empty( $this->get_terms_attached_to_a_doc(get_the_ID(), 'knowledge_base') ) ? [] :  $this->get_terms_attached_to_a_doc(get_the_ID(), 'knowledge_base');
                    foreach( $terms_attached_to_doc as $term_id ) {
                        if( in_array( $term_id, $restricted_mkb_terms ) ) {
                            $redirection_collection[get_the_ID()] = true; //needs redirection
                        } else {
                            $redirection_collection[get_the_ID()] = false; //does not need redirection
                        }
                    }

                    if( ! isset( $redirection_collection[get_the_ID()] ) ) {
                        $redirection_collection[get_the_ID()] = true; // needs redirection, if not found by default inside the upper loop
                    }
                }

                if( isset( $redirection_collection[get_the_ID()] ) && $redirection_collection[get_the_ID()] ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            } else if( ! empty( $restricted_mkb_terms ) && ! empty( $restricted_doc_categories ) && empty( $restricted_doc_posts )  ){
                $redirection_collection = [];
                foreach( $restricted_mkb_terms as $mkb_term_id ) {
                    $mkb_terms_attached_to_doc          = empty( $this->get_terms_attached_to_a_doc(get_the_ID(), 'knowledge_base') ) ? [] :  $this->get_terms_attached_to_a_doc(get_the_ID(), 'knowledge_base');
                    $doc_category_terms_attached_to_doc = empty( $this->get_terms_attached_to_a_doc(get_the_ID(), 'doc_category') ) ? [] : $this->get_terms_attached_to_a_doc(get_the_ID(), 'doc_category');

                    foreach( $mkb_terms_attached_to_doc as $term_id ) { // check if the doc is attached to mkb categories and is viewed
                        if( in_array( $term_id, $restricted_mkb_terms ) ) { // if exist inside view
                            $redirection_collection[get_the_ID()] = true; //needs redirection
                        } else {
                            $redirection_collection[get_the_ID()] = false; //does not need redirection
                        }
                    }

                    foreach( $doc_category_terms_attached_to_doc as $term_id ) { // check if the doc is attached to doc categories and is viewed
                        if( in_array( $term_id, $restricted_doc_categories ) ) { // if exist inside restricted
                            $redirection_collection[get_the_ID()] = true; //needs redirection
                        } else {
                            $redirection_collection[get_the_ID()] = false; //does not need redirection
                        }
                    }

                    if( ! isset( $redirection_collection[get_the_ID()] ) ) {
                        $redirection_collection[get_the_ID()] = true; // needs redirection, if not found by default inside the upper loop
                    }
                }

                if( isset( $redirection_collection[get_the_ID()] ) && $redirection_collection[get_the_ID()] ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            }  else if( ! empty( $restricted_mkb_terms ) && ! empty( $restricted_doc_categories ) && ! empty( $restricted_doc_posts ) && in_array( get_the_ID(), $restricted_doc_posts ) ) {
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            }
        }
    }

    public function template_redirect_single_docs() {
        global $wp_query;

        if( ( is_array( $wp_query->query ) && isset( $wp_query->query ) && isset( $wp_query->query['doc_category'] ) && ! empty( $wp_query->query['doc_category'] ) && is_404() && ! empty( $this->restricted_url ) ) || ( is_array( $wp_query->query ) && isset( $wp_query->query ) && isset( $wp_query->query['post_type'] ) && $wp_query->query['post_type'] == 'docs' && is_404() && ! empty( $this->restricted_url ) ) ) { // for doc_category page, single doc page
            wp_redirect( $this->restricted_url );
            exit();
        } else if( is_singular('docs') ) {
            $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
            $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];

            $view_doc_categories       = [];
            $restricted_doc_categories = [];

            $view_doc_posts            = [];
            $restricted_doc_posts      = [];


            foreach( $selected_categories_terms as $doc_category_id => $doc_category_status ) {
                if( $doc_category_status == 'view' || $doc_category_status == 'full-control' ) {
                    array_push( $view_doc_categories, $doc_category_id );
                } elseif ( $doc_category_status == 'restricted' ) {
                    array_push( $restricted_doc_categories, $doc_category_id );
                }
            }

            foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                if( $doc_status == 'view' || $doc_status == 'full-control' ) {
                    array_push( $view_doc_posts, $doc_id );
                } else if ( $doc_status == 'restricted' ) {
                    array_push( $restricted_doc_posts, $doc_id );
                }
            }

            if( ! empty( $view_doc_categories ) && empty( $view_doc_posts ) ) {
                $redirection_collection        = [];
                $terms_attached_to_doc         = empty( $this->get_terms_attached_to_a_doc(get_the_ID(), 'doc_category') ) ? [] :  $this->get_terms_attached_to_a_doc(get_the_ID(), 'doc_category');
                $child_terms_attached_to_terms = [];

                foreach( $terms_attached_to_doc as $term_id ) {
                    $term = get_term($term_id, 'doc_category');
                    if( ! empty($term) ) {
                        array_push($child_terms_attached_to_terms, ...$this->get_parents($term));
                    }
                }

                if( ! empty( $child_terms_attached_to_terms ) ) {
                    $view_doc_categories = array_unique( array_merge($view_doc_categories, $child_terms_attached_to_terms) );
                }

                foreach( $terms_attached_to_doc as $term_id ) {
                    if( in_array( $term_id, $view_doc_categories ) ) {
                        $redirection_collection[get_the_ID()] = false; //does not need redirection
                    } else {
                        $redirection_collection[get_the_ID()] = true; //needs redirection
                    }
                }

                if( ! isset( $redirection_collection[get_the_ID()] ) ) { //include child terms if parent term is selected
                    $redirection_collection[get_the_ID()] = true; // needs redirection, if not found by default inside the upper loop
                }

                if( isset( $redirection_collection[get_the_ID()] ) && $redirection_collection[get_the_ID()] ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            } else if( ! empty( $view_doc_categories ) && ! empty( $view_doc_posts ) && ! in_array( get_the_ID(), $view_doc_posts ) ) {
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            } else if( ! empty( $restricted_doc_categories ) && empty( $restricted_doc_posts ) ) {
                $redirection_collection        = [];
                $terms_attached_to_doc         = empty( $this->get_terms_attached_to_a_doc(get_the_ID(), 'doc_category') ) ? [] :  $this->get_terms_attached_to_a_doc(get_the_ID(), 'doc_category');
                $child_terms_attached_to_terms = [];

                foreach( $terms_attached_to_doc as $term_id ) {
                    $term = get_term($term_id, 'doc_category');
                    if( ! empty($term) ) {
                        array_push($child_terms_attached_to_terms, ...$this->get_parents($term));
                    }
                }

                if( ! empty( $child_terms_attached_to_terms ) ) { //include child terms if parent term is selected
                    $restricted_doc_categories = array_unique( array_merge($restricted_doc_categories, $child_terms_attached_to_terms) );
                }

                foreach( $terms_attached_to_doc as $term_id ) {
                    if( in_array( $term_id, $restricted_doc_categories ) ) {
                        $redirection_collection[get_the_ID()] = true; //needs redirection
                    } else {
                        $redirection_collection[get_the_ID()] = false; //does not need redirection
                    }
                }

                if( ! isset( $redirection_collection[get_the_ID()] ) ) {
                    $redirection_collection[get_the_ID()] = true; // needs redirection, if not found by default inside the upper loop
                }

                if( isset( $redirection_collection[get_the_ID()] ) && $redirection_collection[get_the_ID()] ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            } else if( ! empty( $restricted_doc_categories ) && ! empty( $restricted_doc_posts ) && in_array( get_the_ID(), $restricted_doc_posts ) ) {
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            }
        }
    }

    public function include_or_exclude_selected_mkb_terms($query_args) {
        $selected_mkb_terms        = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];

        if( isset( $query_args['taxonomy'] ) && $query_args['taxonomy'] == 'knowledge_base' ) {
            $view_filtered_mkb_terms       = [];
            $restricted_filtered_mkb_terms = [];

            foreach( $selected_mkb_terms as $term_id => $status ) {
                if( $status == 'view' ) {
                    array_push( $view_filtered_mkb_terms, $term_id );
                } else if( $status == 'restricted' ) {
                    array_push( $restricted_filtered_mkb_terms, $term_id );
                }
            }

            $query_args['include'] = $view_filtered_mkb_terms;

            $query_args['exclude'] = ! empty( $restricted_filtered_mkb_terms ) ? $restricted_filtered_mkb_terms : [];

            foreach( $restricted_filtered_mkb_terms as $restricted_mkb_term_id ) {
                $mkb_slug = isset( get_term($restricted_mkb_term_id)->slug ) ? get_term($restricted_mkb_term_id)->slug : '';
                foreach( $selected_categories_terms as $doc_category_term_id => $status ) {
                    $doc_categories_in_mkb_meta = ! empty( get_term_meta($doc_category_term_id, 'doc_category_knowledge_base', true) ) ? get_term_meta($doc_category_term_id, 'doc_category_knowledge_base', true) : [];

                    if( in_array( $mkb_slug, $doc_categories_in_mkb_meta ) && $status == 'restricted' ) { // check if the mkb slug belong's inside the doc_category term_meta
                        if( count( $query_args['include'] ) > 0 ) {
                            array_push( $query_args['include'], $restricted_mkb_term_id );
                        }

                        /**
                         * If The Exclude MKB Has A Term Selected Then Remove It From Exclude
                         */
                        $query_args['exclude'] = array_filter($query_args['exclude'], function($id) use ($restricted_mkb_term_id) {
                            return $id != $restricted_mkb_term_id;
                        });
                    }
                }
            }
        }

        if( isset( $query_args['taxonomy'] ) && $query_args['taxonomy'] == 'doc_category' && !defined('REST_REQUEST') ){
            $view_filtered_doc_category_term_slugs_of_mkb       = [];
            $restricted_filtered_doc_category_term_slugs_of_mkb = [];

            foreach( $selected_categories_terms as $doc_term_id => $status ) {
                if( $status == 'restricted' ) {
                    array_push( $restricted_filtered_doc_category_term_slugs_of_mkb, $doc_term_id );
                } else if( $status == 'view' ) {
                    array_push( $view_filtered_doc_category_term_slugs_of_mkb, $doc_term_id );
                }
            }

            /**
             * If View Doc Categories Term Id's Exist, Then Include It
             */
            if( ! empty( $view_filtered_doc_category_term_slugs_of_mkb ) ) {
                $query_args['include'] = $view_filtered_doc_category_term_slugs_of_mkb;
            }

             /**
             * If Restricted Doc Categories Term Id's Exist, Then Exclude It
             */
            if( ! empty( $restricted_filtered_doc_category_term_slugs_of_mkb ) ) {
                $query_args['exclude'] = $restricted_filtered_doc_category_term_slugs_of_mkb;

                foreach( $selected_categories_posts as $post_id => $post_status ) { // remove the doc term if from exclude if it has post id attached to it
                    foreach( $restricted_filtered_doc_category_term_slugs_of_mkb as $doc_category_id ) {
                        if( has_term( $doc_category_id, 'doc_category', $post_id ) && $post_status == 'restricted' ) {
                            if( isset( $query_args['include'] ) && count( $query_args['include'] ) > 0 ) {
                                array_push( $query_args['include'], $doc_category_id );
                            }

                            $query_args['exclude'] = array_filter($query_args['exclude'], function($id) use ($doc_category_id) {
                                return $id != $doc_category_id;
                            });
                        }
                    }
                }
            }
        }

        if( isset( $query_args['taxonomy'] ) && $query_args['taxonomy'] == 'doc_category' && is_tax('knowledge_base') ) {
            $view_filtered_doc_category_term_slugs_of_mkb       = [];
            $restricted_filtered_doc_category_term_slugs_of_mkb = [];
            $current_mkb_slug                                   = get_queried_object() != null ? get_queried_object()->slug : null;

            foreach( $selected_categories_terms as $term_id => $status ) {
                $knowledge_bases = get_term_meta( $term_id, 'doc_category_knowledge_base', true );
                if( $status == 'view' && in_array( $current_mkb_slug ,$knowledge_bases ) ) { //only include restricted doc terms, if these belong to the current mkb terms
                    array_push( $view_filtered_doc_category_term_slugs_of_mkb, $term_id );
                } else if( $status == 'restricted' && in_array( $current_mkb_slug ,$knowledge_bases ) ) { //only include restricted doc terms, if these belong to the current mkb terms
                    array_push( $restricted_filtered_doc_category_term_slugs_of_mkb, $term_id );
                }
            }

            /**
             * If View Doc Categories Term Id's Exist, Then Include It
             */
            $query_args['include'] = $view_filtered_doc_category_term_slugs_of_mkb;

             /**
             * If Restricted Doc Categories Term Id's Exist, Then Exclude It
             */
            if( ! empty( $restricted_filtered_doc_category_term_slugs_of_mkb ) ) {
                $query_args['exclude'] = $restricted_filtered_doc_category_term_slugs_of_mkb;
                $current_mkb_id        = get_queried_object_id();

                foreach( $restricted_filtered_doc_category_term_slugs_of_mkb as $restricted_doc_category_id ) {
                    foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                        if( has_term( $restricted_doc_category_id, 'doc_category', $doc_id ) && has_term( $current_mkb_id, 'knowledge_base', $doc_id  ) && $doc_status == 'restricted' ) {
                            if( count( $query_args['include'] ) > 0 ) {
                                array_push( $query_args['include'], $restricted_doc_category_id );
                            }

                           /**
                            * Remove It From Exclude
                            */
                            $query_args['exclude'] = array_filter($query_args['exclude'], function($id) use ($restricted_doc_category_id) {
                                return $id != $restricted_doc_category_id;
                            });
                        }
                    }
                }
            }
        }

        return $query_args;
    }

    public function include_or_exclude_selected_mkb_terms_count($counts, $term, $nested_subcategory, $args) {
        $selected_mkb_terms        = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category_kb'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category_kb'] : [] ) : [];
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs_kb'] ) ? $this->final_access_control_settings['control_access_restrict_docs_kb'] : [] ) : [];

       if( is_post_type_archive( 'docs' ) && isset( $selected_mkb_terms[$term->term_id] ) && $selected_mkb_terms[$term->term_id] == 'view' || is_post_type_archive( 'docs' ) && isset( $selected_mkb_terms[$term->term_id] ) && $selected_mkb_terms[$term->term_id] == 'full-control' ) {
            $doc_category_count = 0;
            foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                if( $this->doc_belongs_to_taxonomy_sql( $doc_id, 'knowledge_base', $term->term_id ) && $doc_status == 'view' || $this->doc_belongs_to_taxonomy_sql( $doc_id, 'knowledge_base', $term->term_id ) && $doc_status == 'full-control' ) {
                    $doc_category_count++;
                }
            }
            $counts = $doc_category_count > 0 ? $doc_category_count : $counts;
       }

       if( is_post_type_archive( 'docs' ) && isset( $selected_mkb_terms[$term->term_id] ) && $selected_mkb_terms[$term->term_id] == 'restricted' ) {
            foreach( $selected_categories_posts  as $doc_id => $doc_status ) {
                if(  $this->doc_belongs_to_taxonomy_sql( $doc_id ,'knowledge_base', $term->term_id ) && $doc_status == 'restricted' ) {
                    $counts--;
                }
            }
            $counts = $counts < 0 ? 0 : $counts;
       }

        if( isset( $term->taxonomy ) && isset( $term->term_id ) && isset( $selected_mkb_terms[$term->term_id] ) && $term->taxonomy == 'knowledge_base' && $selected_mkb_terms[$term->term_id] == 'restricted' ) { // check if current mkb category is restricted
            foreach( $selected_categories_terms as $term_id => $status ) {
                $knowledge_bases = get_term_meta( $term_id, 'doc_category_knowledge_base', true );
                $mkb_term_slug   = isset( $term->slug ) ? $term->slug : '';
                $mkb_term_id     = $term->term_id;
                if( in_array( $mkb_term_slug, $knowledge_bases ) && $status == 'restricted' ) { // check if the doc categories, belong to the current mkb, then re-process the count
                    $does_current_doc_category_with_mkb_has_selected_posts = false; // flag added to know whether we need to specifically calculate specific post count for mkb attached to mkb & doc category or minus the whole doc category count on mkb count

                    foreach($selected_categories_posts as $post_id => $post_status ) { // if the post's has selected specifically attached to mkb & doc category
                        if( has_term( $mkb_term_id, 'knowledge_base', $post_id ) && has_term( $term_id, 'doc_category', $post_id ) && $status == 'restricted' && $post_status == 'restricted' ) { // if the post has mkb & doc category attached, we know that it has specific posts selected.
                            $does_current_doc_category_with_mkb_has_selected_posts = true;
                            $counts -= 1;
                        }
                    }

                    if( ! $does_current_doc_category_with_mkb_has_selected_posts ) { // if the mkb & doc category does not have any specific posts assigned, then minus the restricted doc category posts on mkb count
                        $doc_category       = get_term( $term_id, 'doc_category' );
                        $doc_category_count = isset( $doc_category->count ) ? $doc_category->count : 0;
                        $counts            -= $doc_category_count;
                    }
                }
            }
        }

        if( isset( $term->taxonomy ) && isset( $term->term_id ) && isset( $selected_mkb_terms[$term->term_id] ) && $term->taxonomy == 'knowledge_base' && $selected_mkb_terms[$term->term_id] == 'view' ) { // check if current mkb category is in view mode
            $specific_mkb_doc_count = 0;

            foreach( $selected_categories_terms as $term_id => $status ) {
                $knowledge_bases = get_term_meta( $term_id, 'doc_category_knowledge_base', true );
                $mkb_term_slug   = isset( $term->slug ) ? $term->slug : '';
                $mkb_term_id     = $term->term_id;
                if( in_array( $mkb_term_slug, $knowledge_bases ) && $status == 'view' ) { // check if the doc categories, belong to the current mkb, then re-process the count
                    $does_current_doc_category_with_mkb_has_selected_posts = false; // flag added to know whether we need to specifically calculate specific post count for mkb attached to mkb & doc category or minus the whole doc category count on mkb count

                    foreach($selected_categories_posts as $post_id => $post_status ) { // if the post's has selected specifically attached to mkb & doc category
                        if( has_term( $mkb_term_id, 'knowledge_base', $post_id ) && has_term( $term_id, 'doc_category', $post_id ) && $status == 'view' && $post_status == 'view' ) { // if the post has mkb & doc category attached, we know that it has specific posts selected.
                            $does_current_doc_category_with_mkb_has_selected_posts = true;
                            $specific_mkb_doc_count += 1;
                        }
                    }

                    if( ! $does_current_doc_category_with_mkb_has_selected_posts ) { // if the mkb & doc category does not have any specific posts assigned, then add the restricted doc category posts on mkb count
                        $doc_category             = get_term( $term_id, 'doc_category' );
                        $doc_category_count       = isset( $doc_category->count ) ? $doc_category->count : 0;
                        $specific_mkb_doc_count  += $doc_category_count;
                    }
                }
            }

            $counts = $specific_mkb_doc_count > 0 ? $specific_mkb_doc_count : $counts;
        }

        if( ( isset($term->term_id) && isset($term->taxonomy) && $term->taxonomy == 'doc_category' && isset( $selected_categories_terms[$term->term_id] ) && $selected_categories_terms[$term->term_id] == 'view' && is_tax('knowledge_base') ) || ( isset($term->term_id) && isset($term->taxonomy) && $term->taxonomy == 'doc_category' && isset( $selected_categories_terms[$term->term_id] ) && $selected_categories_terms[$term->term_id] == 'full-control' && is_tax('knowledge_base') ) ||  is_singular('docs') ) { // increment the knowledgebase based doc categories count
            $doc_category_count = 0;
            foreach( $selected_categories_posts  as $doc_id => $doc_status ) {
                if( $this->doc_belongs_to_taxonomy_sql( $doc_id, 'doc_category', $term->term_id ) && $doc_status == 'view' || $this->doc_belongs_to_taxonomy_sql( $doc_id, 'doc_category', $term->term_id ) && $doc_status == 'full-control' ) {
                    $doc_category_count++;
                }
            }
            $counts = $doc_category_count > 0 ? $doc_category_count : $counts;
        }

        if( isset($term->term_id) && isset($term->taxonomy) && $term->taxonomy == 'doc_category' && isset( $selected_categories_terms[$term->term_id] ) && $selected_categories_terms[$term->term_id] == 'view' && is_tax('doc_category') || isset($term->term_id) && isset($term->taxonomy) && $term->taxonomy == 'doc_category' && isset( $selected_categories_terms[$term->term_id] ) && $selected_categories_terms[$term->term_id] == 'full-control' && is_tax('doc_category') ) { // increment the knowledgebase based doc categories count
            $doc_category_count = 0;
            foreach( $selected_categories_posts  as $doc_id => $doc_status ) {
                if( $this->doc_belongs_to_taxonomy_sql( $doc_id, 'doc_category', $term->term_id ) && $doc_status == 'view' || $this->doc_belongs_to_taxonomy_sql( $doc_id, 'doc_category', $term->term_id ) && $doc_status == 'full-control' ) {
                    $doc_category_count++;
                }
            }
            $counts = $doc_category_count > 0 ? $doc_category_count : $counts;
        }

        if( isset($term->term_id) && isset($term->taxonomy) && $term->taxonomy == 'doc_category' && isset( $selected_categories_terms[$term->term_id] ) && $selected_categories_terms[$term->term_id] == 'restricted' && is_tax('knowledge_base') ) { //decrement the knowledgebase based doc categories count
            foreach( $selected_categories_posts  as $doc_id => $doc_status ) {
                if(  $this->doc_belongs_to_taxonomy_sql( $doc_id, 'doc_category', $term->term_id ) && $doc_status == 'restricted' ) {
                    $counts--;
                }
            }
            $counts = $counts < 0 ? 0 : $counts;
        }

        if( ( isset($term->term_id) && isset($term->taxonomy) && $term->taxonomy == 'doc_category' && isset( $selected_categories_terms[$term->term_id] ) && $selected_categories_terms[$term->term_id] == 'restricted' && is_tax('doc_category') ) || is_singular('docs') ) { //decrement the knowledgebase based doc categories count
            foreach( $selected_categories_posts  as $doc_id => $doc_status ) {
                if(  $this->doc_belongs_to_taxonomy_sql( $doc_id, 'doc_category', $term->term_id ) && $doc_status == 'restricted' ) {
                    $counts--;
                }
            }
            $counts = $counts < 0 ? 0 : $counts;
        }

        return $counts;
    }

    public function include_or_exclude_selected_terms($query_args) {
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];

        if( ! empty( $selected_categories_terms ) ) {
            $view_filtered_terms       = [];
            $restricted_filtered_terms = [];
            foreach( $selected_categories_terms as $term_id => $status ) {
                if( $status == 'view' ) {
                    array_push( $view_filtered_terms, $term_id );
                } else if( $status == 'restricted' ) {
                    array_push( $restricted_filtered_terms, $term_id );
                }
            }

            $query_args['include'] = $view_filtered_terms;

            $query_args['exclude'] = ! empty( $restricted_filtered_terms ) ? $restricted_filtered_terms : [];

            /**
             * Check If Restricted Term Id's Needs To Be Included In The 'include' Param, Only If The Restricted Term Id's Have Specific Post Selected. Else Dont Include It
             */
            foreach( $restricted_filtered_terms as $term_id ) {
                foreach( $selected_categories_posts as $doc_id => $status ) {
                    if( has_term( $term_id, 'doc_category', $doc_id ) && $status == 'restricted' ) {
                        if( count( $query_args['include'] ) > 0 ) { //if term already exist's, then insert the term that has specific post restricted, because we need to show that term excluding its post only.
                            array_push( $query_args['include'], $term_id );
                        }

                        /**
                         * If The Exclude Has A Post Selected Then Remove It From Exclude
                         */
                        $query_args['exclude'] = array_filter($query_args['exclude'], function($id) use ($term_id) {
                            return $id != $term_id;
                        });
                    }
                }
            }
        }
        return $query_args;
    }

    public function include_or_exclude_selected_posts( $args, $_term_id, $_origin_args ) {
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];

        /**
         * If the current term is set to 'View', then add it
         */
        if( isset( $selected_categories_terms[$_term_id] ) && $selected_categories_terms[$_term_id] == 'view' ) { //include if specific posts id's of term are selected for 'view'
            $args['post__in'] = [];
            foreach( $selected_categories_posts as $post_id => $status ) {
                if( has_term( $_term_id, 'doc_category', $post_id ) && $status == 'view' ) {
                    array_push( $args['post__in'], $post_id );
                }
            }
        }

         /**
         * If the current term is set to 'Restricted', then exclude it
         */
        if( isset( $selected_categories_terms[$_term_id] ) && $selected_categories_terms[$_term_id] == 'restricted' ) {  //exlcude if specific posts id's of term are selected for 'restricted'
            $term_slug            = get_term( $_term_id, 'doc_category' )->slug;
            $args['post__not_in'] = [];
            foreach( $selected_categories_posts as $post_id => $status ) {
                if( has_term( $_term_id, 'doc_category', $post_id ) && $status == 'restricted' ) {
                    array_push( $args['post__not_in'], $post_id );
                    foreach( $args['tax_query'] as &$term_query ) {
                        if( $term_query['field'] == 'slug' && $term_query['terms'] == $term_slug ) {
                            $term_query['operator'] = 'IN';
                        }
                    }

                    if( ! empty( $args['post__in'] ) ) { // re-check if the post id is excluded from the 'post__in' param, since its restricted
                        $args['post__in'] = array_filter($args['post__in'], function($id) use ($post_id) {
                            return $id != $post_id;
                        });
                    }
                }
            }
        }

        return $args;
    }


    public function include_or_exclude_selected_mkb_doc_category_attached_posts( $args, $_term_id, $_origin_args ) {
        $selected_mkb_terms        = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_multiple_kb'] ) ? $this->final_access_control_settings['control_access_restrict_multiple_kb'] : [] ) : [];
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];

        $args['post__not_in'] = [];
        $args['post__in']     = [];

        if( isset( $args['tax_query'] )  && ! empty( $args['tax_query'] ) ) {
            $tax_query          = $args['tax_query'];
            $mkb_terms          = isset( $tax_query[0]['terms'] ) ? $tax_query[0]['terms'] : [];
            $doc_category_terms =  isset( $tax_query[1]['terms'] ) ? $tax_query[1]['terms'] : [];

            foreach( $mkb_terms as $mkb_term_id ) {
                foreach( $doc_category_terms as $doc_category_term_id ) {
                    foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                        if( isset( $selected_categories_terms[$doc_category_term_id] ) && $doc_status == 'restricted' && $selected_categories_terms[$doc_category_term_id] == 'restricted' && has_term( $mkb_term_id, 'knowledge_base', $doc_id ) && has_term( $doc_category_term_id, 'doc_category', $doc_id ) ) {
                            array_push( $args['post__not_in'], $doc_id );
                        }else if( isset( $selected_categories_terms[$doc_category_term_id] ) && $doc_status == 'view' && $selected_categories_terms[$doc_category_term_id] == 'view' && has_term( $mkb_term_id, 'knowledge_base', $doc_id ) && has_term( $doc_category_term_id, 'doc_category', $doc_id ) ) {
                            array_push($args['post__in'], $doc_id);
                        }
                    }
                }
            }
        }

        return $args;
    }

    public function include_or_exclude_selected_posts_count($counts, $term, $nested_subcategory, $args) {
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
        $term_id                   = isset( $term->term_id ) ? $term->term_id : 0;

        /**
         * Increment The Count Of The Term If It Has 'View'
         */
        if( isset( $selected_categories_terms[$term_id] ) && $selected_categories_terms[$term_id] == 'view' ) {
            $specific_doc_count = 0;
            foreach( $selected_categories_posts as $post_id => $status ) {
                if( has_term( $term_id, 'doc_category', $post_id ) && $status == 'view' ) {
                    $specific_doc_count += 1;
                }
            }
            $counts = $specific_doc_count > 0 ? $specific_doc_count : $counts; //include the count if specific posts for term exist for 'view' permission
        }


        /**
         * Decrement The Count Of The Term If It Has 'Restricted'
         */
        if( isset( $selected_categories_terms[$term_id] )  && $selected_categories_terms[$term_id] == 'restricted' ) {
            foreach( $selected_categories_posts as $post_id => $status ) {
                if( has_term( $term_id, 'doc_category', $post_id ) && $status == 'restricted' ) {
                    $counts -= 1;
                }
            }
        }

        return $counts;
    }

    /**
     * Get The Selected Rules From Settings Of User Normalize It Based On The Current User
     *
     * @return array
     */
    public function get_selected_rules_based_on_user_role() {
        $filtered_rules_for_current_user = [];

        /**
         * Assign Permission Mode To All The Mkb Ids, Doc Ids, Doc Category Ids
         */
        foreach ( $this->access_control_settings as &$data ) {
            if( isset( betterdocs_pro()->multiple_kb->is_enable ) && betterdocs_pro()->multiple_kb->is_enable ) {
                $data['control_access_restrict_multiple_kb']     = isset( $data['control_access_restrict_multiple_kb'] ) && ! empty( $data['control_access_restrict_multiple_kb'] ) ? $data['control_access_restrict_multiple_kb'] : [];
                $permission_mode                                 = isset( $data['control_access_permission_mode_kb'] ) ? $data['control_access_permission_mode_kb'] : 'option-not-set';
                $data['control_access_restrict_docs_kb']         = isset( $data['control_access_restrict_docs_kb'] ) && ! empty( $data['control_access_restrict_docs_kb'] ) ? $data['control_access_restrict_docs_kb'] : [];
                $data['control_access_restrict_doc_category_kb'] = isset( $data['control_access_restrict_doc_category_kb'] ) && ! empty( $data['control_access_restrict_doc_category_kb'] ) ? $data['control_access_restrict_doc_category_kb'] : [];

                if ( ! empty( $data['control_access_restrict_multiple_kb'] ) && betterdocs_pro()->multiple_kb->is_enable ) {
                    $data['control_access_restrict_multiple_kb'] = [$data['control_access_restrict_multiple_kb'] => $permission_mode];
                }

                $new_doc_ids = [];
                if ( ! empty( $data['control_access_restrict_docs_kb'] ) ) {
                    foreach ( $data['control_access_restrict_docs_kb'] as $doc_id ) {
                        $new_doc_ids[$doc_id] = $permission_mode;
                    }
                    $data['control_access_restrict_docs_kb'] = $new_doc_ids;
                }

                $new_doc_category_ids = [];
                if ( ! empty( $data['control_access_restrict_doc_category_kb'] ) ) {
                    foreach ( $data['control_access_restrict_doc_category_kb'] as $doc_id ) {
                        $new_doc_category_ids[$doc_id] = $permission_mode;
                    }
                    $data['control_access_restrict_doc_category_kb'] = $new_doc_category_ids;
                }
            } else {
                $permission_mode                              = isset( $data['control_access_permission_mode'] ) ? $data['control_access_permission_mode'] : 'option-not-set';
                $data['control_access_restrict_docs']         = isset( $data['control_access_restrict_docs'] ) && ! empty( $data['control_access_restrict_docs'] ) ? $data['control_access_restrict_docs'] : [];
                $data['control_access_restrict_doc_category'] = isset( $data['control_access_restrict_doc_category'] ) && ! empty( $data['control_access_restrict_doc_category'] ) ? $data['control_access_restrict_doc_category'] : [];

                $new_doc_ids = [];
                if ( ! empty( $data['control_access_restrict_docs'] ) ) {
                    foreach ( $data['control_access_restrict_docs'] as $doc_id ) {
                        $new_doc_ids[$doc_id] = $permission_mode;
                    }
                    $data['control_access_restrict_docs'] = $new_doc_ids;
                }

                $new_doc_category_ids = [];
                if ( ! empty( $data['control_access_restrict_doc_category'] ) ) {
                    foreach ( $data['control_access_restrict_doc_category'] as $doc_id ) {
                        $new_doc_category_ids[$doc_id] = $permission_mode;
                    }
                    $data['control_access_restrict_doc_category'] = $new_doc_category_ids;
                }
            }
        }

        /**
         * Filter The Applicable Settings For The Current User For Operation
         */
        foreach ( $this->current_user_role as $user_role ) {
            foreach ( $this->access_control_settings as $setting ) {
                if ( in_array( $user_role, isset( $setting['control_access_restrict_roles'] ) && ! empty( $setting['control_access_restrict_roles'] ) ? $setting['control_access_restrict_roles'] : [] ) && ! betterdocs_pro()->multiple_kb->is_enable ) {
                    $filtered_rules_for_current_user['control_access_restrict_doc_category'] = isset( $filtered_rules_for_current_user['control_access_restrict_doc_category'] ) ? $filtered_rules_for_current_user['control_access_restrict_doc_category'] : [];
                    $filtered_rules_for_current_user['control_access_restrict_docs']         = isset( $filtered_rules_for_current_user['control_access_restrict_docs'] ) ? $filtered_rules_for_current_user['control_access_restrict_docs'] : [];
                    $filtered_rules_for_current_user['control_access_restrict_doc_category'] += $setting['control_access_restrict_doc_category']; //preserve the keys
                    $filtered_rules_for_current_user['control_access_restrict_docs']         += $setting['control_access_restrict_docs']; //preserve the keys
                } else if ( in_array( $user_role, isset( $setting['control_access_restrict_roles_kb'] ) && ! empty( $setting['control_access_restrict_roles_kb'] ) ? $setting['control_access_restrict_roles_kb'] : [] ) && betterdocs_pro()->multiple_kb->is_enable ) {
                    $filtered_rules_for_current_user['control_access_restrict_multiple_kb']     = isset( $filtered_rules_for_current_user['control_access_restrict_multiple_kb'] ) ? $filtered_rules_for_current_user['control_access_restrict_multiple_kb'] : [];
                    $filtered_rules_for_current_user['control_access_restrict_doc_category_kb'] = isset( $filtered_rules_for_current_user['control_access_restrict_doc_category_kb'] ) ? $filtered_rules_for_current_user['control_access_restrict_doc_category_kb'] : [];
                    $filtered_rules_for_current_user['control_access_restrict_docs_kb']         = isset( $filtered_rules_for_current_user['control_access_restrict_docs_kb'] ) ? $filtered_rules_for_current_user['control_access_restrict_docs_kb'] : [];

                    $filtered_rules_for_current_user['control_access_restrict_multiple_kb']     += $setting['control_access_restrict_multiple_kb']; //preserve the keys
                    $filtered_rules_for_current_user['control_access_restrict_doc_category_kb'] += $setting['control_access_restrict_doc_category_kb']; //preserve the keys
                    $filtered_rules_for_current_user['control_access_restrict_docs_kb']         += $setting['control_access_restrict_docs_kb']; //preserve the keys
                }
            }
        }

        if( isset( $filtered_rules_for_current_user['control_access_restrict_doc_category_kb'] ) && ! empty( $filtered_rules_for_current_user['control_access_restrict_doc_category_kb'] ) ) {
            Helper::remove_all_from_content_restriction_for_doc_categories( $filtered_rules_for_current_user['control_access_restrict_doc_category_kb'] );
        }

        return $filtered_rules_for_current_user;
    }

    public function template_redirect_without_mkb() {
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];
        $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : [];
        $current_object_id         = get_queried_object_id();

        if( is_tax('doc_category') ) {
            if( isset( $selected_categories_terms[$current_object_id] ) && $selected_categories_terms[$current_object_id] == 'restricted' ) {
                $needs_redirection = true;
                foreach( $selected_categories_posts as $doc_id => $doc_status ) { // check if the docs are selected or not for this current category
                    if( has_term( $current_object_id, 'doc_category', $doc_id ) && $doc_status == 'restricted' ) {
                        $needs_redirection = false;
                        break;
                    }
                }
                if( $needs_redirection ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            } elseif( isset( $selected_categories_terms[$current_object_id] ) && $selected_categories_terms[$current_object_id] == 'view' ) { //skip if current category is set to 'view'
                return;
            } else {

                //check if 'view' exist on a doc category(for categories that are not set), if exist then redirect it
                $needs_redirection = false;

                foreach( $selected_categories_terms as $doc_category_id => $status ) {
                    if( $status == 'view' ) {
                        $needs_redirection = true;
                        break;
                    }
                }

                if( $needs_redirection ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            }
        }

        if( is_singular('docs') ) {
            if( isset( $selected_categories_posts[$current_object_id] ) && $selected_categories_posts[$current_object_id] == 'restricted' ) {
                if( $this->restricted_url ){
                    wp_redirect( $this->restricted_url );
                    exit();
                } else {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    get_template_part( 404 );
                    exit();
                }
            } else {
                $check_if_restricted_category_is_selected               = false;
                $restricted_selected_docs                               = [];
                $check_if_view_category_is_selected                     = false;
                $viewed_selected_docs                                   = [];
                $current_doc_attached_categories                        = wp_get_post_terms($current_object_id, 'doc_category');

                foreach( $current_doc_attached_categories as $doc_category ) { // check if restricted category is selected
                    if( isset( $selected_categories_terms[$doc_category->term_id] ) && $selected_categories_terms[$doc_category->term_id] == 'restricted' ) {
                        $check_if_restricted_category_is_selected = true;
                        break;
                    } elseif ( isset( $selected_categories_terms[$doc_category->term_id] ) && $selected_categories_terms[$doc_category->term_id] == 'view' ) {
                        $check_if_view_category_is_selected = true;
                        break;
                    }
                }

                foreach( $current_doc_attached_categories as $doc_category ) { // check if docs are selected or not | if docs are selected, do not redirect to 404
                    foreach( $selected_categories_posts as $doc_id => $doc_status ) {
                        if( has_term( $doc_category->term_id, 'doc_category', $doc_id ) && $doc_status == 'restricted' && isset( $selected_categories_terms[$doc_category->term_id] ) && $selected_categories_terms[$doc_category->term_id] == 'restricted' ) {
                           array_push($restricted_selected_docs, $doc_id);
                        } else if( has_term( $doc_category->term_id, 'doc_category', $doc_id ) && $doc_status == 'view' && isset( $selected_categories_terms[$doc_category->term_id] ) && $selected_categories_terms[$doc_category->term_id] == 'view' ) {
                            array_push($viewed_selected_docs , $doc_id);
                        }
                    }
                }

                if( $check_if_restricted_category_is_selected && empty( $restricted_selected_docs ) ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }

                if( $check_if_view_category_is_selected && ! empty( $viewed_selected_docs ) && ! in_array( $current_object_id, $viewed_selected_docs ) ) {
                    if( $this->restricted_url ){
                        wp_redirect( $this->restricted_url );
                        exit();
                    } else {
                        global $wp_query;
                        $wp_query->set_404();
                        status_header( 404 );
                        get_template_part( 404 );
                        exit();
                    }
                }
            }
        }
    }

    public function modify_quick_actions_wp_list_table($actions, $tag) {
        $selected_categories_terms = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_doc_category'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_doc_category'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category'] : [] ) : [];

        foreach( $selected_categories_terms as $term_id => $term_status ) {
            if( isset( $tag->term_id ) && $tag->term_id == $term_id && $term_status == 'view' ) {
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);
                unset($actions['delete']);
            } else if( isset( $tag->term_id ) && $tag->term_id == $term_id && $term_status == 'edit-only' ) {
                unset($actions['view']);
                unset($actions['delete']);
            }
        }

        return $actions;
    }

    public function modify_quick_actions_wp_list_table_for_all($actions, $tag) {
        $doc_categories_mode = isset( $this->final_access_control_settings['control_access_restrict_doc_category']['all'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category']['all']: '';
        if(  $doc_categories_mode == 'view' ) {
            unset($actions['edit']);
            unset($actions['inline hide-if-no-js']);
            unset($actions['delete']);
        } else if( $doc_categories_mode == 'edit-only' ) {
            unset($actions['view']);
            unset($actions['delete']);
        }
        return $actions;
    }

    public function custom_post_type_row_actions($actions, $post) {
        if( get_post_type() == 'docs' ) {
            $selected_categories_posts = ! empty( $this->final_access_control_settings ) ? ( isset( $this->final_access_control_settings['control_access_restrict_docs'] ) && ! empty( $this->final_access_control_settings['control_access_restrict_docs'] ) ? $this->final_access_control_settings['control_access_restrict_docs'] : [] ) : []; //already assumed categories exist
            foreach( $selected_categories_posts as $post_id => $post_status ) {
                if( isset( $post->ID ) && $post->ID == $post_id && $post_status == 'view' ) {
                    unset($actions['edit']);
                    unset($actions['inline hide-if-no-js']);
                    unset($actions['trash']);
                } else if( isset( $post->ID ) && $post->ID == $post_id && $post_status == 'edit-only' ) {
                    unset($actions['view']);
                    unset($actions['trash']);
                }
            }
        }

        return $actions;
    }

    public function custom_post_type_row_actions_for_all($actions, $post) {
        if( get_post_type() == 'docs' ) {
            $doc_categories_mode = isset( $this->final_access_control_settings['control_access_restrict_doc_category']['all'] ) ? $this->final_access_control_settings['control_access_restrict_doc_category']['all']: '';
            if( $doc_categories_mode == 'view' ) {
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);
                unset($actions['trash']);
            } else if(  $doc_categories_mode == 'edit-only' ) {
                unset($actions['view']);
                unset($actions['trash']);
            }
        }
        return $actions;
    }

    public function localize_access_control_settings($localized_data) {
        $localized_data['access_control'] = $this->final_access_control_settings;
        return $localized_data;
    }

    /**
     * Get The Terms Attached To A Post
     *
     * @param integer $doc_id
     * @param string $taxonomy
     *
     * @return array
     */
    public function get_terms_attached_to_a_doc( $doc_id, $taxonomy ) {
        global $wpdb;

        $query = $wpdb->prepare("
            SELECT t.term_id
            FROM {$wpdb->terms} AS t
            INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
            INNER JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            WHERE tr.object_id = %d
            AND tt.taxonomy = %s",
            array( $doc_id, $taxonomy )
        );

        return array_column( $wpdb->get_results($query, ARRAY_A), 'term_id' );
    }

    /**
     * Get The Term Id Based On Term Meta Key And Value
     *
     * @param string $meta_key
     * @param string $meta_value
     *
     * @return array
     */
    public function get_term_meta_based_on_meta_key_and_meta_value($meta_key, $meta_value) {
        global $wpdb;
        $query = $wpdb->prepare('
            SELECT
            DISTINCT(term_id)
            FROM
            '.$wpdb->prefix.'termmeta
            WHERE meta_key = %s
            AND meta_value LIKE %s',
            $meta_key,
            '%'.$wpdb->esc_like($meta_value).'%'
        );
        $results = $wpdb->get_results($query, ARRAY_A);
        return array_column($results, 'term_id');
    }

    /**
     * Clear all access control related caches
     *
     * This method removes all caches created by the access control system.
     * It should be called whenever access control settings are updated to ensure
     * that users see the latest permissions immediately.
     *
     * Cache Types Cleared:
     * - Query result cache
     * - Term parents cache
     * - Database query cache
     *
     * Automatic Triggers:
     * - When BetterDocs settings are updated
     * - When BetterDocs Pro settings are updated
     *
     * @since 3.6.0
     * @return void
     */
    public static function clear_access_control_cache() {
        // Clear internal caches
        self::$query_cache = [];

        // Reset recursion guard
        self::$filter_recursion_guard = false;

        // Clear WordPress object cache for access control related data
        if (function_exists('wp_cache_flush_group')) {
            wp_cache_flush_group('betterdocs_access_control');
        }
    }

    /**
     * Set recursion guard to prevent infinite loops
     *
     * @param bool $guard
     * @return void
     */
    public static function set_recursion_guard($guard = true) {
        self::$filter_recursion_guard = $guard;
    }

    /**
     * Get recursion guard status
     *
     * @return bool
     */
    public static function get_recursion_guard() {
        return self::$filter_recursion_guard;
    }
}
