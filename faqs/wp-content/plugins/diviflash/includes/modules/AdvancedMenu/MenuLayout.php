<?php

trait MenuLayout {

    public $mobile_slide = '';

    private $device = 'desktop';

    private $menu_order;

    public function item_class($item) {
        $class = $item['class'];
        $class = !empty($item['link_option_url']) ? $class . ' et_clickable' : $class;

        return $class;
    }
    /**
     * Render logo
     *
     * @param Array | $item
     * @return String | html
     */
    public function logo($item) {
        $logo_alt            = $item['logo_alt'];
		$logo_src            = $item['logo_upload'];
        $logo_sizes = et_get_attachment_size_by_url($logo_src);
        $logo_url = $item['logo_url'];

        $logo = sprintf('<img src="%1$s" class="df-site-logo" alt="%2$s" width="%3$s" height="%4$s" />',
            $logo_src,
            $logo_alt,
            $logo_sizes[0],
            $logo_sizes[1]
        );
        $sticky_logo = isset($item['sticky_logo']) && !empty($item['sticky_logo']) ? sprintf('<img src="%1$s" class="df-site-logo sticky-logo" alt="%2$s" width="%3$s" height="%4$s" />',
            $item['sticky_logo'],
            $logo_alt,
            $logo_sizes[0],
            $logo_sizes[1]
        ) : '';

        if(!empty($logo_url)) {
            $logo_target = $item['logo_url_new_window'] === 'on' ? 'target="_blank"' : '';
            $logo = sprintf('<a href="%1$s" %3$s aria-label="%4$s">%2$s</a>', $logo_url, $logo, $logo_target,$logo_alt);
            $sticky_logo = sprintf('<a href="%1$s" %3$s aria-label="%4$s">%2$s</a>', $logo_url, $sticky_logo, $logo_target,$logo_alt);
        }
        $sticky_class = !empty($item['sticky_logo']) ? ' df-has-sticky' : '';

        $rendered_content = sprintf('<div class="%1$s df-am-item%4$s">
            %2$s%3$s</div>',
            $this->item_class($item),
            $logo,
            $sticky_logo,
            $sticky_class
        );

        return $rendered_content;
    }

    private $processed_menu_item_class = array();
    /**
     * Render Mobile menu
     *
     * @param Array | $item
     * @return String | html
     */
    public function render_mobile_menu($item) {
        if (in_array($item['class'], $this->processed_menu_item_class)) {
            return;
        }
        $this->processed_menu_item_class[] = $item['class'];

        $df_menu_nav = df_get_am_menu(array(
            'menu' => '',
            'menu_id' => $item['menu_id']
        ));
        $mobile = sprintf('
            <div class="df-mobile-menu %2$s_mobile_menu">
                <div class="mobile-slide-inner-wrap">
                    <div class="%2$s df-am-item">
                        %1$s
                    </div> %3$s
                </div>
            </div>',
            $df_menu_nav,
            $item['class'],
            $this->mslide_button($item)
        );
        $this->mobile_slide .= $mobile;
    }
    /**
     * Render Mobile slide Button
     *
     * @param Array | $item
     * @return String | html
     */
    public function mslide_button($item) {
        if(!isset($item['use_mslide_btn']) || $item['use_mslide_btn'] !== 'on') return;

        $button_icon = $item['mslide_use_button_icon'] === 'on' ?
            sprintf('<span class="df-mslide-button-icon">%1$s</span>', $item['mslide_button_font_icon']) : '' ;

        $rendered_content = sprintf('
            <a class="%1$s df-mobile-button" href="%3$s" %4$s>
                %6$s%2$s%5$s
            </a>',
            $item['class'] . '_mslide_btn',
            $item['mslide_button_text'] == '' ? 'Button Text' : $item['mslide_button_text'],
            $item['mslide_button_url'],
            $item['mslide_button_url_new_window'] === 'on' ? 'target="_blank"' : '',
            $item['mslide_button_icon_on_left'] != 'on' ? $button_icon : '',
            $item['mslide_button_icon_on_left'] === 'on' ? $button_icon : ''
        );

        return $rendered_content;
    }

    /**
     * Render Menu
     *
     * @param Array | $item
     * @return String | html
     */
    public function menu($item) {
        $df_menu_nav = df_get_am_menu(array(
            'menu' => '',
            'menu_id' => $item['menu_id']
        ));
        $dekstop_menu = $item['desktop_menu'] === 'normal' ? sprintf('<div class="df-normal-menu-wrap">
            %1$s
        </div>', $df_menu_nav) : '';

        $mobile_menu_bar = $item['mobile_menu'] == 'mobile_menu' ? sprintf('<div class="df-mobile-menu-wrap">
                <button class="df-mobile-menu-button notranslate" data-menu="%3$s_mobile_menu" data-open="off">%4$s</button>
            </div>',
            $df_menu_nav,
            $item['class'],
            $this->item_class($item), // $this->menu_order,
            $item['mm_trigger_icon']
        ) : '';

        $this->render_mobile_menu($item);

        $hover_animation = $item['use_item_hover'] === 'on' ?
            'has-item-animation ' . $item['item_hover'] : '';

        return sprintf('<div class="%1$s df-am-item %4$s%5$s %6$s">
                %2$s
                %3$s
            </div>',
            $this->item_class($item),
            $dekstop_menu,
            $mobile_menu_bar,
            $item['submenu_reveal_anime'],
            $item['use_submenu_arrow'] === 'on' ? ' with-smenu-arrow' : '',
            $hover_animation
        );
    }

    /**
     * Render mobile menu trigger button
     *
     * @param Array | $item
     * @return String | html
     */
    public function mm_trigger($item) {
        return sprintf('
                <div class="%1$s df-am-item">
                    <button class="df-mobile-menu-button df-am-item" data-menu="%2$s_mobile_menu">
                        %3$s
                    </button>
                </div>
            ',
            $this->item_class($item),
            $this->item_class($item), // $this->menu_order,
            $item['mm_trigger_icon']
        );
    }

    /**
     * Render Button
     *
     * @param Array | $item
     * @return String | html
     */
    public function button($item) {
        $button_icon = $item['use_button_icon'] === 'on' ?
            sprintf('<span class="df-am-button-icon">%1$s</span>', $item['button_font_icon']) : '' ;

        $rendered_content = sprintf('
            <a class="%1$s df-am-item df-menu-button %7$s" href="%3$s" %4$s>
                %6$s%2$s%5$s
            </a>',
            $this->item_class($item),
            $item['button_text'] == '' ? 'Button Text' : $item['button_text'],
            $item['button_url'],
            $item['button_url_new_window'] === 'on' ? 'target="_blank"' : '',
            $item['button_icon_on_left'] != 'on' ? $button_icon : '',
            $item['button_icon_on_left'] === 'on' ? $button_icon : '',
            $item['button_show_icon_on_hover'] === 'on' ? 'show_icon_on_hover' : ''

        );

        return $rendered_content;
    }

    /**
     * Render Search box
     *
     * @param Array | $item
     * @return String | html
     */
    public function search($item) {
        $class_name = $item["class"];
        $style = $item['search_style'];
        $placeholder = $item['placeholder'];
        $search_icon = $item['search_icon'];
        $indexClass = $item['indexClass'];

        if($style == 'df-searchbox-style-5') {
            // popup searchbox
            if($this->device == 'desktop') { // generate only once
                add_action('wp_footer', function() use($indexClass, $style, $placeholder, $search_icon){
                    echo sprintf('
                        <div class="%1$s_modal df-am-item df-am-search %2$s" style="opacity:0;">
                            <button class="serach-box-close">M</button>
                            <form class="" action="%3$s" role="search" method="get">
                                <input name="s" class="df_am_s" type="text" placeholder="%4$s" />
                                <button type="submit" class="df_am_searchsubmit with-icon">%5$s</button>
                            </form>
                        </div>',
                        esc_attr($indexClass),
                        esc_attr($style),
                        esc_url( home_url( '/' ) ),
                        esc_attr($placeholder),
                        esc_attr($search_icon)
                    );
                });
            }
            return sprintf('
                    <button class="%1$s df-am-item df-am-search-button %3$s" data-search="%5$s">
                        %4$s
                    </button>
                ',
                $item['class'],
                esc_url( home_url( '/' ) ),
                $style !== '' ? $style : 'df-searchbox-style-1',
                $item['search_tr_icon'],
                $indexClass
            );
        }
        return sprintf('
                <div class="%1$s df-am-item df-am-search %3$s">
                    <form class="" action="%2$s" role="search" method="get">
                        <input name="s" class="df_am_s" type="text" placeholder="%4$s" />
                        <button type="submit" class="df_am_searchsubmit with-icon">%5$s</button>
                    </form>
                </div>
            ',
            $item['class'],
            esc_url( home_url( '/' ) ),
            $style !== '' ? $style : 'df-searchbox-style-1',
            $placeholder !== '' ? $placeholder : '',
            $search_icon
        );
    }

    /**
     * Render Custom Text
     *
     * @param Array | $item
     * @return String | html
     */
    public function text($item) {
        return sprintf('
                <div class="%1$s df-am-item">
                    %2$s
                </div>
            ',
            $this->item_class($item),
            $item['content']
        );
    }

    /**
     * Render woocommerce cart
     *
     * @param Array | $item
     * @return String | html
     */
    public function cart($item) {
        if ( !class_exists( 'WooCommerce' ) ) {
            return 'Woocommerce not found!';
        }
        // get the woocommerce cart url
        $cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : wc_get_page_permalink('cart');

        $item_count = $item['use_cart_count'] === 'on' ?
        sprintf('<span class="cart-item-count">%1$s</span>', WC()->cart->get_cart_contents_count()) : '';

        $cart_total = $item['use_cart_total'] === 'on' ? sprintf('<span class="cart-total">%1$s</span>',
            WC()->cart->get_total()
        ) : '';

        return sprintf('
                <div class="%1$s df-am-item">
                    <a href="%3$s" class="df-cart-info" target="_top">
                        <span class="cart-icon-wrap"><span class="cart-icon">%2$s</span>%4$s</span>
                        %5$s
                    </a>
                </div>
            ',
            $item['class'],
            $item['cart_icon'],
            $cart_url,
            $item_count,
            $cart_total
        );
    }

    /**
     * Render icon button
     *
     * @param Array | $item
     * @return String | html
     */
    public function icon_box($item) {
        $target = $item['icon_box_url_new_window'] === 'on' ?
            sprintf('target="%1$s"', $item['icon_box_url_new_window']) : '';
        return sprintf('
                <a href="%3$s" class="%1$s df-icon-button" %4$s title="%5$s">
                    <span>%2$s</span>
                </a>
            ',
            $this->item_class($item),
            $item['icon_btn_font_icon'],
            $item['icon_box_url'],
            $target,
            $item['icon_link_title']
        );
    }
    /**
     * Render vr divider
     *
     * @param Array | $item
     * @return String | html
     */
    public function divider($item) {
        return sprintf('<span class="%1$s df-am-item df-vr-divider"></span>', $this->item_class($item));
    }

    /**
     * Iterate over menu items
     * and call the associate function
     *
     * @param Array
     * @param String | $menu_order
     *
     * @return String | $layout
     */
    public function df_menu_items($props) {
        $layout = '';
        foreach($props as $item) {
            $layout .= call_user_func(array($this, $item['type']), $item);
        }
        return $layout;
    }

    public function renderTopRow($props) {
        $left = isset($props['top_left']) && !empty($props['top_left']) && $this->dfls_item_display($props['top_left'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col left">%s</div>', $this->df_menu_items($props['top_left']))
          : '<div class="df-am-col left"></div>';

        $center = isset($props['top_center']) && !empty($props['top_center']) && $this->dfls_item_display($props['top_center'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col center">%s</div>', $this->df_menu_items($props['top_center']))
          : '';

        $button_show_icon_on_hover = isset($props['top_right'][0]['button_show_icon_on_hover']) && $props['top_right'][0]['button_show_icon_on_hover'] == 'on' && $this->dfls_item_display($props['top_right'][0]['display_conditions']) ? 'show_icon_on_hover' : '';

        $right = isset($props['top_right']) && !empty($props['top_right']) && $this->dfls_item_display($props['top_right'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col right %s">%s</div>', $button_show_icon_on_hover , $this->df_menu_items($props['top_right']))
          : '<div class="df-am-col right"></div>';

        if (empty($props['top_left']) && empty($props['top_center']) && empty($props['top_right'])) {
          return '';
        }
        $sticky_class = $this->props['trow_hide_on_sticky'] == 'on' ? ' hide_on_sticky' : '';
        return sprintf('<div class="df-am-row top-row%4$s"><div class="row-inner">%s%s%s</div></div>', $left, $center, $right, $sticky_class);
    }

    public function renderCenterRow($props) {
        // $props['center_left'][0]['display_conditions']
        $left = isset($props['center_left']) && !empty($props['center_left']) && $this->dfls_item_display($props['center_left'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col left">%s</div>', $this->df_menu_items($props['center_left']))
          : '<div class="df-am-col left"></div>';

        $center = isset($props['center_center']) && !empty($props['center_center']) && $this->dfls_item_display($props['center_center'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col center">%s</div>', $this->df_menu_items($props['center_center']))
          : '';
        $button_show_icon_on_hover = isset($props['center_right'][0]['button_show_icon_on_hover']) && $props['center_right'][0]['button_show_icon_on_hover'] == 'on' && $this->dfls_item_display($props['center_right'][0]['display_conditions']) ? 'show_icon_on_hover' : '';
        $right = isset($props['center_right']) && !empty($props['center_right']) && $this->dfls_item_display($props['center_right'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col right %s">%s</div>', $button_show_icon_on_hover, $this->df_menu_items($props['center_right']))
          : '<div class="df-am-col right"></div>';

        if (empty($props['center_left']) && empty($props['center_center']) && empty($props['center_right'])) {
          return '';
        }
        $sticky_class = $this->props['crow_hide_on_sticky'] == 'on' ? ' hide_on_sticky' : '';
        return sprintf('<div class="df-am-row center-row%4$s"><div class="row-inner">%s%s%s</div></div>', $left, $center, $right, $sticky_class);
    }

    public function renderBottomRow($props) {
        $left = isset($props['bottom_left']) && !empty($props['bottom_left']) && $this->dfls_item_display($props['bottom_left'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col left">%s</div>', $this->df_menu_items($props['bottom_left']))
          : '<div class="df-am-col left"></div>';

        $center = isset($props['bottom_center']) && !empty($props['bottom_center']) && $this->dfls_item_display($props['bottom_center'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col center">%s</div>', $this->df_menu_items($props['bottom_center']))
          : '';

        $button_show_icon_on_hover = isset($props['bottom_right'][0]['button_show_icon_on_hover']) && $props['bottom_right'][0]['button_show_icon_on_hover'] == 'on' && $this->dfls_item_display($props['bottom_right'][0]['display_conditions']) ? 'show_icon_on_hover' : '';

        $right = isset($props['bottom_right']) && !empty($props['bottom_right']) && $this->dfls_item_display($props['bottom_right'][0]['display_conditions'])
          ? sprintf('<div class="df-am-col right %s">%s</div>', $button_show_icon_on_hover, $this->df_menu_items($props['bottom_right']))
          : '<div class="df-am-col right"></div>';

        if (empty($props['bottom_left']) && empty($props['bottom_center']) && empty($props['bottom_right'])) {
          return '';
        }
        $sticky_class = $this->props['brow_hide_on_sticky'] == 'on' ? ' hide_on_sticky' : '';
        return sprintf('<div class="df-am-row bottom-row%4$s"><div class="row-inner">%s%s%s</div></div>', $left, $center, $right, $sticky_class);
    }


    public function render_menu_layout($props, $menu_order, $device='desktop') {

        $this->menu_order = $menu_order;
        $this->device = $device;

        return sprintf(
            '%1$s%2$s%3$s',
            $this->renderTopRow($props),
            $this->renderCenterRow($props),
            $this->renderBottomRow($props)
        );
    }

    /**
     * Render mobile slide Layout
     *
     * @param Array $props
     * @param String $menu_order
     *
     * @return String HTML
     */
    public function render_mobile_slide_items($props, $menu_order) {
        $this->menu_order = $menu_order;
        if(!isset($props['mobile_slide'])) return;
        return $this->df_menu_items($props['mobile_slide']);
    }

    public function dfls_item_display($display_conditions){

        $is_displayable                        = true;
        $is_display_conditions_set             = isset( $display_conditions ) && ! empty( $display_conditions );
        $is_display_conditions_as_base64_empty = 'W10=' === $display_conditions;
        $has_display_conditions                = $is_display_conditions_set && ! $is_display_conditions_as_base64_empty;

        if ( $has_display_conditions ) {
            $display_conditions_json = base64_decode( $display_conditions );
        }
        if ( $has_display_conditions && false !== $display_conditions_json ) {
            $display_conditions = json_decode( $display_conditions_json, true );
            $is_displayable     = \ET_Builder_Module_Fields_Factory::get( 'DisplayConditions' )->is_displayable( $display_conditions );
        }
        return $is_displayable;
    }
}
