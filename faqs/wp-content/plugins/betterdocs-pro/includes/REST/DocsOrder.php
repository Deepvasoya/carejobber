<?php

namespace WPDeveloper\BetterDocsPro\REST;

use WP_REST_Request;
use WPDeveloper\BetterDocs\Core\BaseAPI;
use WPDeveloper\BetterDocs\Utils\Helper;

class DocsOrder extends BaseAPI {

    /**
     * @return mixed
     */
    public function register() {
        $this->get( '/docs_order', [$this, 'docs_order'], ['orderby' => [], "order" => [], "per_page" => [], "doc_category" => []] );
        $this->get( '/knowledge_base', [$this, 'fetch_kb_doc_category'] );
        $this->get( '/doc_category', [$this, 'docs_order'], ['orderby' => [], "order" => [], "per_page" => [], "doc_category" => []] );
    }

    /**
     * BetterDocs Order API Callback
     */
    public function docs_order( $attr ) {
        $query_args = [
            'post_status'    => 'publish',
            'post_type'      => 'docs',
            'posts_per_page' => isset( $attr['per_page'] ) ? $attr['per_page'] : 10,
            'orderby'        => isset( $attr['orderby'] ) && $attr['orderby'] === 'betterdocs_order' ? 'post__in' : 'menu_order'
        ];

        $term_id    = isset( $attr['doc_category'] ) ? $attr['doc_category'] : 0;
        // Get language-specific meta key with fallback
        $meta_key   = Helper::get_meta_key_with_fallback( '_docs_order', $term_id );
        $docs_order = get_term_meta( $term_id, $meta_key, true );

        $term_object = get_term( $term_id );

        $query_args['tax_query'][] =
            [
            'taxonomy'         => 'doc_category',
            'field'            => 'slug',
            'terms'            => $term_object->slug,
            'operator'         => 'AND',
            'include_children' => false
        ];

        $query_args['orderby'] = ! empty( $docs_order ) ? $query_args['orderby'] : 'menu_order';

        $new_ids = [];
        global $wpdb;

        if ( ! empty( $docs_order ) ) {
            $docs_order = explode( ',', $docs_order );

            $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = $term_id" );

            if ( ! is_null( $results ) && ! empty( $results ) && is_array( $results ) ) {
                $object_ids = array_filter( $results, function ( $value ) use ( $docs_order ) {
                    return ! in_array( $value->object_id, $docs_order );
                } );

                if ( ! empty( $object_ids ) ) {
                    array_walk( $object_ids, function ( $value ) use ( &$new_ids ) {
                        $new_ids[] = $value->object_id;
                    } );
                }
            }
        } else {
            $docs_order = [];
        }

        $query_args['post__in'] = array_merge( $new_ids, $docs_order );

        $data = [];
        $loop = new \WP_Query( $query_args );

        if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) {
                $loop->the_post();
                $docs                      = [];
                $docs['id']                = get_the_ID();
                $docs['title']['rendered'] = wp_kses( get_the_title( get_the_ID() ), betterdocs()->template_helper::ALLOWED_HTML_TAGS );
                $docs['permalink']         = get_permalink();
                $data[]                    = $docs;
            }
            wp_reset_postdata();
        }
        return $data;
    }

    public function fetch_kb_doc_category( WP_REST_Request $request ) {
        $hide_empty         = $request->get_param( 'hide_empty' );
        $kb_order           = $request->get_param( 'order' );
        $kb_orderby         = $request->get_param( 'orderby' );
        $kb_per_page        = $request->get_param( 'per_page' );
        $include            = $request->get_param( 'include' );
        $exclude            = $request->get_param( 'exclude' );
        $nested_subcategory = $request->get_param( 'nested_subcategory' );

        $new_kb_terms = [];

        $kb_terms_args = [
            'taxonomy'   => 'knowledge_base',
            'hide_empty' => $hide_empty,
            'parent'     => 0,
            'offset'     => 0,
            'number'     => $kb_per_page,
            'include'    => $include,
            'exclude'    => $exclude,
            'order'      => $kb_order,
            'orderby'    => $kb_orderby,
            'meta_key'   => 'kb_order'
        ];
        $kb_terms_query_args = betterdocs()->query->terms_query( $kb_terms_args );
        $kb_terms = betterdocs()->query->get_terms( $kb_terms_query_args );

        $doc_terms_args = [
            'hide_empty' => true,
            'taxonomy'   => 'doc_category',
            'parent'     => 0,
            'order'      => $kb_order,
            'orderby'    => $kb_orderby,
            'number'     => 'all'
        ];

        if ( $nested_subcategory == 'false' ) {
            unset( $doc_terms_args['parent'] );
        }
        if ( ! is_wp_error( $kb_terms ) ) {
            foreach ( $kb_terms as $kb ) {
                $doc_terms_args['meta_query'] = [
                    'relation' => 'OR',
                    [
                        'key'     => 'doc_category_knowledge_base',
                        'value'   => isset( $kb->slug ) ? $kb->slug : '',
                        'compare' => 'LIKE'
                    ]
                ];

                if( gettype($kb) == 'array' ) {
                    $kb = (object)$kb;
                }

                $kb->doc_categories = [];
                $doc_categories     = get_terms( $doc_terms_args );
                foreach ( $doc_categories as $doc_category ) {
                    $_counts = betterdocs()->query->get_docs_count( $doc_category, $nested_subcategory == 'false' ? false : true, [
                        'multiple_knowledge_base' => true,
                        'kb_slug'                 => $kb->slug
                    ] );

                    if ( $_counts <= 0 ) {
                        continue;
                    }

                    $attachment_id = get_term_meta( $doc_category->term_id, 'doc_category_image-id', true );

                    if ( ! $attachment_id ) {
                        $doc_category->thumbnail = null;
                    } else {
                        $doc_category->thumbnail = wp_get_attachment_url( $attachment_id );
                    }

                    array_push( $kb->doc_categories, $doc_category );
                }

                array_push( $new_kb_terms, $kb );
            }
        }
        return $new_kb_terms;
    }

    public function get_ordered_doc_categories( $data ) {
        // Use betterdocs_order which will handle fallback logic automatically
        $args = array(
            'taxonomy' => 'doc_category',
            'orderby' => 'betterdocs_order',
            'order' => 'ASC', // Order from lowest to highest
        );

        // Use BetterDocs query method which handles the fallback logic
        $doc_categories = betterdocs()->query->terms_query( $args );

        return rest_ensure_response( $doc_categories );
    }
}
