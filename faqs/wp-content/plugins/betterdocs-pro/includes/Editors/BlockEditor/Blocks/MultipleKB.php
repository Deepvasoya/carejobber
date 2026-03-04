<?php
namespace WPDeveloper\BetterDocsPro\Editors\BlockEditor\Blocks;

use WPDeveloper\BetterDocs\Editors\BlockEditor\Block;

class MultipleKB extends Block {

    public $is_pro = true;

    protected $editor_scripts = [
        'betterdocs-pro-blocks-editor'
    ];

    protected $editor_styles = [
        'betterdocs-fontawesome',
        'betterdocs-pro-blocks-editor',
        'betterdocs-blocks-category-box'
    ];

    protected $frontend_styles = [
        'betterdocs-fontawesome',
        'betterdocs-blocks-category-box',
        'betterdocs-docs'
    ];

    /**
     * unique name of block
     * @return string
     */
    public function get_name() {
        return 'multiple-kb';
    }

    public function get_default_attributes() {
        return [
            'blockId'           => '',
            'categories'        => [],
            'includeCategories' => '',
            'excludeCategories' => '',
            'boxPerPage'        => 9,
            'orderBy'           => 'name',
            'order'             => 'asc',
            'layout'            => 'default',
            'showIcon'          => true,
            'showTitle'         => true,
            'titleTag'          => 'h2',
            'showCount'         => true,
            'prefix'            => '',
            'suffix'            => __( 'Docs', 'betterdocs' ),
            'suffixSingular'    => __( 'Doc', 'betterdocs' ),
            'colRange'          => 3,
            'TABcolRange'       => 2,
            'MOBcolRange'       => 1,
            'layout4Col'        => 4,
            'showLastUpdatedTime' => true
        ];
    }

    public function view_params() {
        $attributes = &$this->attributes;

        $terms_object = [
            'taxonomy'   => 'knowledge_base',
            'order'      => $attributes['order'],
            'orderby'    => $attributes['orderBy'],
            'number'     => isset( $attributes['boxPerPage'] ) ? $attributes['boxPerPage'] : 5,
            'hide_empty' => true
        ];

        if ( 'kb_order' === $attributes['orderBy'] ) {
            $terms_object['meta_key'] = 'kb_order';
            $terms_object['orderby']  = 'meta_value_num';
        }

        $includes = $this->string_to_array( $attributes['includeCategories'] );
        $excludes = $this->string_to_array( $attributes['excludeCategories'] );
        $styles   = '';

        if ( ! empty( $includes ) ) {
            $terms_object['include'] = array_diff( $includes, (array) $excludes );
        }

        if ( ! empty( $excludes ) ) {
            $terms_object['exclude'] = $excludes;
        }

        $_wrapper_classes = [
            'betterdocs-category-box-wrapper',
            'betterdocs-category-list-view-wrapper',
            'betterdocs-multiple-kb-list-wrapper',
            'betterdocs-multiple-kb-wrapper',
            'betterdocs-pro'
        ];

        $_inner_wrapper_classes = [
            'betterdocs-category-box-inner-wrapper',
            'layout-flex',
            $attributes['layout'] === 'default' ? 'layout-1' : $attributes['layout'],
            "betterdocs-column-" . $attributes['colRange'],
            "betterdocs-column-tablet-" . $attributes['TABcolRange'],
            "betterdocs-column-mobile-" . $attributes['MOBcolRange']
        ];

        if ( $attributes['layout'] == 'layout-4' ) {
            $_inner_wrapper_classes = [
                'betterdocs-category-box-inner-wrapper',
                'layout-4',
                'docs-col-4',
                'single-kb',
                'betterdocs-categories-folder',
                'layout-4',
                'single-kb'
            ];
            $column     =  is_tax( 'doc_category' )  ?  3 : $attributes['layout4Col'];
            $terms_query_args = betterdocs()->query->terms_query( $terms_object );
            $terms_for_count = betterdocs()->query->get_terms( $terms_query_args );
            $term_count = ( ! is_wp_error( $terms_for_count ) ) ? count( $terms_for_count ) : 0;
            $reminder   = $term_count % $column;
            $styles     .= "--column: $column;";
            $styles     .= "--count: $term_count;";
            $styles     .= "--reminder: $reminder;";
        }


        $wrapper_attr = [
            'class' => $_wrapper_classes
        ];
        $inner_wrapper_attr = [
            'class'               => $_inner_wrapper_classes,
            'style'               => $styles,
            'data-column_desktop' => $attributes['colRange'],
            'data-column_tab'     => $attributes['TABcolRange'],
            'data-column_mobile'  => $attributes['MOBcolRange']
        ];

        $_params = [
            'wrapper_attr'            => $wrapper_attr,
            'inner_wrapper_attr'      => $inner_wrapper_attr,
            'terms_query_args'        => betterdocs()->query->terms_query( $terms_object ),
            'widget_type'             => 'category-box',
            'multiple_knowledge_base' => false,
            'kb_slug'                 => '',
            'nested_subcategory'      => false,
            'show_header'             => true,
            'show_description'        => false,
            'term_icon_meta_key'      => 'knowledge_base_image-id',
            'title_tag'               => $attributes['titleTag']
        ];

        if ( $attributes['layout'] == 'layout-4' ) {
            $column                   = is_tax( 'doc_category' ) ? 3 : $attributes['layout4Col'];
            $terms_query_args_2 = betterdocs()->query->terms_query( $terms_object );
            $terms_for_count_2 = betterdocs()->query->get_terms( $terms_query_args_2 );
            $term_count = ! is_wp_error( $terms_for_count_2 ) ? count( $terms_for_count_2 ) : 0;
            $reminder                 = $term_count % $column;
            $_params['show_icon']     = $this->attributes['showIcon'];
            $_params['show_count']    = $this->attributes['showCount'];
            $_params['total_terms']   = $term_count;
            $_params['reminder']      = $reminder;
            $_params['category_icon'] = 'folder';
            $_params['last_update']   = $this->attributes['showLastUpdatedTime'];
            $_params['taxonomy']      = 'knowledge_base';
            $_params['column']        = $column;
            unset($_params['inner_wrapper_attr']['data-column_desktop']); //not needed for layout 4
            unset($_params['inner_wrapper_attr']['data-column_tab']); // not needed for layout 4
            unset($_params['inner_wrapper_attr']['data-column_mobile']); // not needed for layout 4
        }

        return $_params;
    }

    public function filter_header_sequence( $_layout_sequence, $layout, $widget_type, $_defined_vars ) {
        $_new_layout_sequence = ['category_icon', [
            'class'    => 'betterdocs-category-title-counts',
            'sequence' => ['category_title', 'category_description', 'category_counts']
        ]];

        if( $layout == 'layout-4' ) {
            $_new_layout_sequence = ['category_icon', [
                'class'    => 'betterdocs-category-title-counts',
                'sequence' => ['new_post_tag', 'category_title', 'category_description', 'sub_category_counts', 'last_update']
            ]];
        }

        return $_new_layout_sequence;
    }

    public function render( $attributes, $content ) {
        if ( $attributes['layout'] == 'layout-4' ) {
            add_filter( 'betterdocs_layout_filename', [$this, 'change_to_layout_four'], 15, 3 );
        }

        add_filter( 'betterdocs_header_layout_sequence', [$this, 'filter_header_sequence'], 10, 4 );
        $this->views( 'layouts/base' );
        remove_filter( 'betterdocs_header_layout_sequence', [$this, 'filter_header_sequence'], 10 );
    }

    public function change_to_layout_four() {
        return 'layout-4';
    }
}
