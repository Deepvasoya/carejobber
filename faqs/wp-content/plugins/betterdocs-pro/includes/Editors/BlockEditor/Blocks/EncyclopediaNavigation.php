<?php

namespace WPDeveloper\BetterDocsPro\Editors\BlockEditor\Blocks;

use WPDeveloper\BetterDocs\Editors\BlockEditor\Block;
use WPDeveloper\BetterDocs\Utils\Helper;

class EncyclopediaNavigation extends Block {

    public $is_pro = true;

    /**
     * unique name of block
     * @return string
     */
    public function get_name() {
        return 'encyclopedia-navigation';
    }

    protected $editor_styles = [
        'betterdocs-encyclopedia'
    ];

    protected $frontend_styles = [
        'betterdocs-encyclopedia'
    ];

    public function get_default_attributes() {
        return [
            'blockId'             => '',
            'alphabet_list_style' => 'box'
        ];
    }

    public function render( $attributes, $content ) {
        $this->views( 'layouts/encyclopedia/navigation' );
    }

    public function view_params() {
        return [
            'blockId'             => $this->attributes['blockId'] . ' ',
            'alphabet_list_style' => $this->attributes['alphabet_list_style'],
            'docs_by_letter'      => Helper::docs_sort_by_letter(),
            'current_letter'      => ! empty( strtoupper( substr( get_the_title(), 0, 1 ) ) ) ? strtoupper( substr( get_the_title(), 0, 1 ) ) : ''
        ];
    }
}
