<?php

class DF_VerticalMenu extends ET_Builder_Module
{
	use DF_UTLS;
	use DIFL\Handler\Fa_Icon_Handler;

	public $slug = 'difl_vertical_menu';
	public $vb_support = 'on';
	public $icon_path;
	public $main_css_core_element;
	public $active_menu_item_icon_selector;
	public $active_menu_item_icon_hover_selector;
	public $active_sub_menu_item_icon_selector;
	public $active_sub_menu_item_icon_hover_selector;
	public $active_menu_item_selector;
	public $active_menu_item_hover_selector;
	public $active_sub_menu_item_selector;
	public $active_mega_menu_item_selector;
	public $active_sub_menu_item_hover_selector;
	public $active_mega_menu_item_hover_selector;
	public $active_mega_menu_item_icon_selector;
	public $active_mega_menu_item_icon_hover_selector;

	protected $module_credits = [
		'module_uri' => '',
		'author' => 'DiviFlash',
		'author_uri' => '',
	];

	public function init()
	{
		$this->name = esc_html__('Vertical Menu', 'divi_flash');
		$this->main_css_core_element = "%%order_class%%  .df_vertical_menu_main_container";
		$this->main_css_element = "%%order_class%%  .df_vertical_menu_main_container .df-vertical-menu-nav-wrap  ul.df-vertical-menu-nav";
		$this->icon_path = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/vertical-menu.svg';

		// Icon Selector 
		$this->active_menu_item_icon_selector = "{$this->main_css_element} li[class*='current'] .df_vertical_menu_item_elements_wrapper>.df-vertical-menu-icon";
		$this->active_menu_item_icon_hover_selector = "{$this->main_css_element} li[class*='current']>.df_vertical_menu_item_elements_wrapper:hover>.df-vertical-menu-icon";
		$this->active_sub_menu_item_icon_selector = "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-col-added>ul):not(.df-vertical-inside-mega-menu)>li[class*='current']>.df_vertical_menu_item_elements_wrapper>.df-vertical-menu-icon";
		$this->active_sub_menu_item_icon_hover_selector = "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-col-added>ul):not(.df-vertical-inside-mega-menu)>li[class*='current']>a:hover>.df-vertical-menu-icon";
		$this->active_mega_menu_item_icon_selector = "{$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added  li[class*='current'] > .df_vertical_menu_item_elements_wrapper .df-vertical-menu-icon";
		$this->active_mega_menu_item_icon_hover_selector = "{$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added  li[class*='current'] > .df_vertical_menu_item_elements_wrapper:hover .df-vertical-menu-icon";

		//Dom Selector
		$this->active_menu_item_selector = "{$this->main_css_element} li[class*='current'] .df_vertical_menu_item_elements_wrapper";
		$this->active_menu_item_hover_selector = "{$this->main_css_element} li[class*='current'] a:hover";
		$this->active_sub_menu_item_selector = "{$this->main_css_element} ul.df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li[class*='current'] > .df_vertical_menu_item_elements_wrapper";
		$this->active_sub_menu_item_hover_selector = "{$this->main_css_element}  ul.df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li[class*='current'] > .df_vertical_menu_item_elements_wrapper:hover";
		$this->active_mega_menu_item_selector = "{$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added  li[class*='current'] > .df_vertical_menu_item_elements_wrapper";
		$this->active_mega_menu_item_hover_selector = "{$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added  li[class*='current'] > .df_vertical_menu_item_elements_wrapper:hover";
	}

	private function dynamic_main_css_element($dynamic_val)
	{
		return "%%order_class%%  .df_vertical_menu_main_container$dynamic_val .df-vertical-menu-nav-wrap  ul.df-vertical-menu-nav";
	}
	public function get_settings_modal_toggles()
	{

		$menu_additional_contents_sub_toggle_items = [
			'general' => [
				'name' => 'General',
			],
			'sub-menu' => [
				'name' => 'SubMenu',
			],
		];


		$mega_menu_additional_contents_sub_toggle_items = [
			'container' => [
				'name' => 'Container',
			],
			'item' => [
				'name' => 'Item',
			],
		];
		$menu_active_state_sub_toggles_item = [
			'main_menu' => [
				'name' => 'Main Menu',
			],
			'sub_menu' => [
				'name' => 'Submenu',
			],
			'mega_menu' => [
				'name' => 'Mega Menu',
			],
		];
		$toggle_key__hamburger_style_subtoggle = [
			'container' => [
				'name' => 'Container',
			],
			'text' => [
				'name' => 'Text',
			],
			'icon' => [
				'name' => 'Icon',
			],
		];


		return [
			'general' => [
				'toggles' => [
					'toggle_key__content' => esc_html__('Content', 'divi_flash'),
					'toggle_key__builder_view' => esc_html__('Builder View', 'divi_flash'),
					'toggle_key__settings' => esc_html__('Settings', 'divi_flash'),
					'toggle_key__hamburger' => esc_html__('Hamburger', 'divi_flash'),
					'toggle_key__menu_item_hover_animation' => esc_html__('Menu Item Hover Animation', 'divi_flash'),
				],
			],

			'advanced' => [
				'toggles' => [
					'toggle_key__alignment' => esc_html__('Alignment', 'divi_flash'),
					'toggle_key__text_style' => esc_html__('Text Style', 'divi_flash'),
					'toggle_key__menu_items' => esc_html__('Menu Items', 'divi_flash'),

					'toggle_key__sub_menu_items' => [
						'title' => esc_html__('Submenu', 'divi_flash'),
						'tabbed_subtoggles' => true,
						'sub_toggles' => $mega_menu_additional_contents_sub_toggle_items,
					],

					'toggle_key__mega_menu' => [
						'title' => esc_html__('Mega Menu', 'divi_flash'),
						'tabbed_subtoggles' => true,
						'sub_toggles' => $mega_menu_additional_contents_sub_toggle_items,
					],
					'toggle_key__mega_menu_parent' => esc_html__('Mega Menu Parent', 'divi_flash'),

					'toggle_key__menu__active_state' => [
						'title' => esc_html__('Active State', 'divi_flash'),
						'tabbed_subtoggles' => true,
						'sub_toggles' => $menu_active_state_sub_toggles_item,
					],


					'toggle_key__badge' => [
						'title' => esc_html__('Badge', 'divi_flash'),
						'tabbed_subtoggles' => true,
						'sub_toggles' => $menu_additional_contents_sub_toggle_items,
					],

					'toggle_key__tooltip' => [
						'title' => esc_html__('Tooltip', 'divi_flash'),
						'tabbed_subtoggles' => true,
						'sub_toggles' => $menu_additional_contents_sub_toggle_items,
					],
					'toggle_key__hamburger_style' => [
						'title' => esc_html__('Hamburger', 'divi_flash'),
						'tabbed_subtoggles' => true,
						'sub_toggles' => $toggle_key__hamburger_style_subtoggle,
					],
				]
			],
		];
	}

	public function get_advanced_fields_config()
	{
		$advanced_fields = [];

		$advanced_fields['background'] = [
			'use_background_image' => true,
			'use_background_color_gradient' => true,
			'use_background_video' => false,
			'use_background_pattern' => false,
			'use_background_mask' => false,
			'css' => [
				'main' => "{$this->main_css_core_element}",
				'important' => 'all',
			],

		];

		// main menu border style 
		$advanced_fields['borders'] = [

			'default' => [
				'css' => [
					'main' => [
						'border_radii' => $this->main_css_core_element,
						'border_styles' => $this->main_css_core_element,
						'border_styles_hover' => "{$this->main_css_core_element}:hover"
					]
				]
			],

			'style_settings__menu__items_border' => [
				'css' => [
					'main' => [
						'border_radii' => "{$this->main_css_element}  li  .df_vertical_menu_item_elements_wrapper",
						'border_styles' => "{$this->main_css_element}  li  .df_vertical_menu_item_elements_wrapper",
						'border_styles_hover' => "{$this->main_css_element}  li  .df_vertical_menu_item_elements_wrapper:hover",
					]
				],
				'defaults' => [
					'border_radii' => ''
				],
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__menu_items',
			],

			'style_settings__sub_menu__wrapper_border' => [
				'css' => [
					'main' => [
						'border_radii' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-custom-submenu) ",
						'border_styles' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-custom-submenu) ",
						'border_styles_hover' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-custom-submenu):hover",
					]
				],
				'defaults' => [
					'border_radii' => 'on| | | | '
				],
				'tab_slug' => 'advanced',
				'sub_toggle' => 'container',
				'toggle_slug' => 'toggle_key__sub_menu_items',
			],

			'style_settings__sub_menu__item_border' => [
				'css' => [
					'main' => [
						'border_radii' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-custom-submenu) li .df_vertical_menu_item_elements_wrapper",
						'border_styles' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-custom-submenu) li .df_vertical_menu_item_elements_wrapper",
						'border_styles_hover' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-custom-submenu) li .df_vertical_menu_item_elements_wrapper:hover",
					]
				],
				'defaults' => [
					'border_radii' => 'on| | | | '
				],
				'tab_slug' => 'advanced',
				'sub_toggle' => 'item',
				'toggle_slug' => 'toggle_key__sub_menu_items',
			],

			'toggle_key__mega_menu_border' => [
				'css' => [
					'main' => [
						'border_radii' => "{$this->main_css_element}  li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added",
						'border_styles' => "{$this->main_css_element}  li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added",
						'border_styles_hover' => "{$this->main_css_element}  li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added:hover",
					]
				],
				'defaults' => [
					'border_radii' => 'on| | | | '
				],
				'tab_slug' => 'advanced',
				'sub_toggle' => 'container',
				'toggle_slug' => 'toggle_key__mega_menu',
			],
			'toggle_key__mega_menu__item_border' => [
				'css' => [
					'main' => [
						'border_radii' => "{$this->main_css_element}  li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added li a.df_vertical_menu_item_elements_wrapper",
						'border_styles' => "{$this->main_css_element}  li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added  li a.df_vertical_menu_item_elements_wrapper",
						'border_styles_hover' => "{$this->main_css_element}  li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added:hover li a.df_vertical_menu_item_elements_wrapper",
					]
				],
				'defaults' => [
					'border_radii' => 'on| | | | '
				],
				'tab_slug' => 'advanced',
				'sub_toggle' => 'item',
				'toggle_slug' => 'toggle_key__mega_menu',
			],
		];

		// fonts settings 
		$advanced_fields['fonts'] = [

			// MENU TYPOGRAPHY
			'style_settings__menu__typography' => [
				'label' => '',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__text_style',
				'line_height' => [
					'default' => '1.4em',
				],
				'hide_text_align' => true,
				'font_size' => [
					'default' => '16px',
				],
				'font_color' => [
					'default' => '#fff',
				],
				'css' => [
					'main' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper",
					'important' => 'all',
				],
			],

			// BADGE TYPOGRAPHY
			'style_settings__menu__badge__typography' => [
				'label' => '',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__badge',
				'sub_toggle' => 'general',
				'hide_text_align' => true,
				'line_height' => [
					'default' => '1.4em',
				],
				'font_size' => [
					'default' => '0.8em',
				],
				'font_color' => [
					'default' => '#fff',
				],
				'css' => [
					'main' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-badge",
					'hover' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper:hover .df-vertical-nav-item-badge",
					'important' => 'all',
				],
			],

			// SUB MENU TYPOGRAPHY
			'style_settings__sub_menu__typography' => [
				'label' => '',
				'tab_slug' => 'advanced',
				'sub_toggle' => 'item',
				'toggle_slug' => 'toggle_key__sub_menu_items',
				'line_height' => [
					'default' => '',
				],
				'hide_text_align' => true,
				'font_size' => [
					'default' => '',
				],
				'font_color' => [
					'default' => '',
				],
				'css' => [
					'main' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-inside-mega-menu)>li>.df_vertical_menu_item_elements_wrapper",
					'important' => 'all',
				],
				'priority' => 4,
			],

			// SUB MENU BADGE TYPOGRAPHY
			'style_settings__sub_menu__badge__typography' => [
				'label' => '',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__badge',
				'sub_toggle' => 'sub-menu',
				'hide_text_align' => true,
				'line_height' => [
					'default' => '1.4em',
				],
				'font_size' => [
					'default' => '0.8em',
				],
				'font_color' => [
					'default' => '#fff',
				],
				'css' => [
					'main' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-badge",
					'hover' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) li>.df_vertical_menu_item_elements_wrapper:hover .df-vertical-nav-item-badge",
					'important' => 'all',
				],
			],

			// TOOLTIP TYPOGRAPHY 
			'style_settings__menu__tooltip__typography' => [
				'label' => '',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__tooltip',
				'sub_toggle' => 'general',
				'hide_text_align' => true,
				'line_height' => [
					'default' => '1.4em',
				],
				'font_size' => [
					'default' => '16px',
				],
				'font_color' => [
					'default' => '#fff',
				],
				'css' => [
					'main' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-tooltip",
					'hover' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper:hover .df-vertical-nav-item-tooltip",
					'important' => 'all',
				],
			],

			// SUB MENU TOOLTIP TYPOGRAPHY
			'style_settings__sub_menu__tooltip__typography' => [
				'label' => '',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__tooltip',
				'sub_toggle' => 'sub-menu',
				'hide_text_align' => true,
				'line_height' => [
					'default' => '1.4em',
				],
				'font_size' => [
					'default' => '16px',
				],
				'font_color' => [
					'default' => '#fff',
				],
				'css' => [
					'main' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-tooltip",
					'hover' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) li>.df_vertical_menu_item_elements_wrapper:hover .df-vertical-nav-item-tooltip",
					'important' => 'all',
				],
			],

			// Hamburger TYPOGRAPHY
			'style_settings__hamburger_typography' => [
				'label' => '',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__hamburger_style',
				'sub_toggle' => 'text',
				'hide_text_align' => true,
				'line_height' => [
					'default' => '1.4em',
				],
				'font_size' => [
					'default' => '14px',
				],
				'font_color' => [
					'default' => '#000',
				],
				'css' => [
					'main' => "{$this->main_css_core_element}  .df-vertical-humberger-container .df-vertical-menu-hamburger-text",
					'hover' => "{$this->main_css_core_element}  .df-vertical-humberger-container:hover .df-vertical-menu-hamburger-text",
					'important' => 'all',
				],
			],

			'style_settings__mega_menu__parent__typography' => [
				'label' => '',
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__mega_menu_parent',
				'sub_toggle' => 'parent',
				'hide_text_align' => true,
				'css' => [
					'main' => "{$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added > div > li > .df_vertical_menu_item_elements_wrapper",
					'important' => 'all',
				],
			],
		];

		// Box Shadow 
		$advanced_fields['box_shadow'] = [
			'default' => [
				'css' => [
					'main' => "{$this->main_css_core_element}",
					'important' => 'all',
				],
			],

			// MAIN MENU-->GENERAL-->BOX SHADOW
			'style_settings__menu__items_box_shadow' => [
				'css' => [
					'main' => "{$this->main_css_element} > li  .df_vertical_menu_item_elements_wrapper",
				],
				'tab_slug' => 'advanced',
				'toggle_slug' => 'toggle_key__menu_items',
			],

			// SUB MENU-->GENERAL-->BOX SHADOW
			'style_settings__sub_menu__items_box_shadow' => [
				'css' => [
					'main' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper",
				],
				'tab_slug' => 'advanced',
				'sub_toggle' => 'item',
				'toggle_slug' => 'toggle_key__sub_menu_items',
			],
			// MEGA MENU-->GENERAL-->BOX SHADOW
			'style_settings__mega_menu__items_box_shadow' => [
				'css' => [
					'main' => "{$this->main_css_element}  .df-vertical-sub-menu.df-vertical-col-added  li > .df_vertical_menu_item_elements_wrapper",
				],
				'tab_slug' => 'advanced',
				'sub_toggle' => 'item',
				'toggle_slug' => 'toggle_key__mega_menu',

			],
		];
		$advanced_fields['margin_padding'] = [
			'css' => [
				'main' => $this->main_css_core_element,
				'important' => 'all',
			]
		];
		// disable pre-settings 
		$advanced_fields['text'] = false;
		$advanced_fields['module_text'] = false;
		$advanced_fields['filters'] = false;
		$advanced_fields['transform'] = false;
		$advanced_fields['link_options'] = false;

		return $advanced_fields;
	}

	public function get_fields()
	{
		$fields = [];

		//ANCHOR - GENERAL -- START-->
		$fields['settings__select_menu_slug'] = [
			'label' => esc_html__('Select Menu', 'divi_flash'),
			'type' => 'select',
			'option_category' => 'basic_option',
			'description' => sprintf(
				'<p class="description">%2$s. <a href="%1$s" target="_blank">%3$s</a>.</p>',
				esc_url(admin_url('nav-menus.php')),
				esc_html__('Select a menu that should be used in the module', 'et_builder'),
				esc_html__('Click here to create new menu', 'et_builder')
			),
			'toggle_slug' => 'toggle_key__content',
			'options' => et_builder_get_nav_menus_options(),
			'default' => 'none'
		];

		//BUILDER NESTED VIEW
		$fields['settings__builder_visiblity'] = [
			'label' => esc_html__('Show Submenu on Builder', 'divi_flash'),
			'type' => 'yes_no_button',
			'description' => 'When this setting is enabled, you will be able to see a preview of the nested submenu in the builder.',
			'options' => [
				'off' => esc_html__('NO', 'divi_flash'),
				'on' => esc_html__('YES', 'divi_flash'),
			],
			'default' => 'off',
			'toggle_slug' => 'toggle_key__builder_view',
		];

		//ANCHOR - Settings -- START-->
		//SUB Menu Reveal Type
		$fields['settings__submenu_reveal_type'] = [
			'label' => esc_html__('Submenu Type', 'divi_flash'),
			'type' => 'select',
			'options' =>
				[
					'df-vertical-sub-menu-reveal-stack' => esc_html__('Stack', 'divi_flash'),
					'df-vertical-sub-menu-reveal-flyout' => esc_html__('Flyout', 'divi_flash'),
				]
			,
			'default' => 'df-vertical-sub-menu-reveal-flyout',
			'toggle_slug' => 'toggle_key__settings',
		];
		//SUB Menu Reveal Type
		$fields['settings__submenu_reveal_dir'] = [
			'label' => esc_html__('Submenu Reveal Direction', 'divi_flash'),
			'type' => 'select',
			'options' =>
				[
					'df-vertical-sub-menu-reveal-left' => esc_html__('Left', 'divi_flash'),
					'df-vertical-sub-menu-reveal-right' => esc_html__('Right', 'divi_flash'),
				]
			,
			'default' => 'df-vertical-sub-menu-reveal-right',
			'toggle_slug' => 'toggle_key__settings',
			'show_if' => [
				'settings__submenu_reveal_type' => 'df-vertical-sub-menu-reveal-flyout'
			]
		];

		$fields['settings__clicked_menu_element_style'] = [
			'label' => esc_html__('Active State Style on Clicked', 'divi_flash'),
			'description' => esc_html__('If disable active state style work only for current page item', 'divi_flash'),
			'type' => 'yes_no_button',
			'options' => [
				'off' => esc_html__('NO', 'divi_flash'),
				'on' => esc_html__('YES', 'divi_flash'),
			],
			'default' => 'off',
			'toggle_slug' => 'toggle_key__settings',
			'show_if' => [
				'settings__submenu_reveal_type' => 'df-vertical-sub-menu-reveal-stack'
			]
		];

		//badge visiblity 
		$fields['settings__badge_visiblity'] = [
			'label' => esc_html__('Badge ', 'divi_flash'),
			'type' => 'yes_no_button',
			'options' =>
				[
					'on' => esc_html__('ON', 'divi_flash'),
					'off' => esc_html__('OFF', 'divi_flash'),
				]
			,
			'default' => 'on',
			'toggle_slug' => 'toggle_key__settings',
		];

		//toltip visiblity
		$fields['settings__tooltip_visiblity'] = [
			'label' => esc_html__('Tooltip ', 'divi_flash'),
			'type' => 'yes_no_button',
			'options' =>
				[
					'on' => esc_html__('ON', 'divi_flash'),
					'off' => esc_html__('OFF', 'divi_flash'),
				]
			,
			'default' => 'on',
			'toggle_slug' => 'toggle_key__settings',
		];

		//ANCHOR - Menu Item Hover Animation -- START -->
		$fields['settings__menu_item_hover_animation'] = [
			'label' => esc_html__('Enable Item Animation ', 'divi_flash'),
			'type' => 'yes_no_button',
			'options' =>
				[
					'on' => esc_html__('ON', 'divi_flash'),
					'off' => esc_html__('OFF', 'divi_flash'),
				]
			,
			'default' => 'off',
			'toggle_slug' => 'toggle_key__menu_item_hover_animation',
		];
		$fields['settings__select_animation_type'] = [
			'label' => esc_html__('Select Animation', 'divi_flash'),
			'type' => 'select',
			'option_category' => 'basic_option',
			'description' => esc_html__('Selected Animation Applied on menu hover', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu_item_hover_animation',
			'options' => [
				'item-hover-1' => esc_html__('Animation 1', 'divi_flash'),
				'item-hover-2' => esc_html__('Animation 2', 'divi_flash'),
				'item-hover-3' => esc_html__('Animation 3', 'divi_flash'),
			],
			'default' => 'item-hover-1',
			'show_if' => [
				'settings__menu_item_hover_animation' => 'on'
			]
		];


		$fields['settings__use_hamburger_for_mobile'] = [
			'label' => esc_html__('Hamburger ', 'divi_flash'),
			'type' => 'yes_no_button',
			'options' =>
				[
					'on' => esc_html__('ON', 'divi_flash'),
					'off' => esc_html__('OFF', 'divi_flash'),
				]
			,
			'default' => 'off',
			'toggle_slug' => 'toggle_key__settings',
		];

		$fields['settings__hamburger_menu_reveal_type'] = [
			'label' => esc_html__('Menu Reveal On', 'divi_flash'),
			'type' => 'select',
			'options' =>
				[
					'df_hamburger_reveal_on_click' => esc_html__('Click', 'divi_flash'),
					'df_hamburger_reveal_on_hover' => esc_html__('Hover', 'divi_flash'),
				]
			,
			'default' => 'df_hamburger_reveal_on_click',
			'toggle_slug' => 'toggle_key__hamburger',
			'priority' => 0,
			'show_if' => [
				'settings__use_hamburger_for_mobile' => 'on'
			]
		];

		$fields['settings__hamburger_text'] = [
			'label' => esc_html__('Text', 'divi_flash'),
			'type' => 'text',
			'option_category' => 'basic_option',
			'description' => esc_html__('Text for Hamburger.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__hamburger',
			'dynamic_content' => false,
			'mobile_options' => false,
			'priority' => 1,
			'hover' => 'tabs',
			'show_if' => [
				'settings__use_hamburger_for_mobile' => 'on'
			],
		];

		$fields['settings__hamburger_icon_preset'] = [
			'label' => esc_html__('Hamburger Icon', 'divi_flash'),
			'type' => 'select',
			'option_category' => 'basic_option',
			'toggle_slug' => 'toggle_key__hamburger',
			'options' => [
				'icon1' => esc_html__('Hamburger 1', 'divi_flash'),
				'icon2' => esc_html__('Hamburger 2', 'divi_flash'),
				'icon3' => esc_html__('Hamburger 3', 'divi_flash'),
				'icon4' => esc_html__('Hamburger 4', 'divi_flash'),
				'icon5' => esc_html__('Hamburger 5', 'divi_flash'),
			],
			'default' => 'icon1',
			'show_if' => [
				'settings__use_hamburger_for_mobile' => 'on'
			]
		];


		$fields['settings__hamburger_icon_color'] = [
			'default_on_front' => true,
			'label' => esc_html__('Icon Color', 'divi_flash'),
			'type' => 'color-alpha',
			'default' => '#000',
			'toggle_slug' => 'toggle_key__hamburger_style',
			'tab_slug' => 'advanced',
			'sub_toggle' => 'icon',
			'hover' => 'tabs',
		];
		$fields['style_settings__hamburger_icon_font_size'] = [
			'label' => esc_html__('Icon Size', 'divi_flash'),
			'type' => 'range',
			'option_category' => 'font_option',
			'toggle_slug' => 'toggle_key__hamburger_style',
			'sub_toggle' => 'icon',
			'tab_slug' => 'advanced',
			'default' => '14px',
			'default_unit' => 'px',
			'default_on_front' => '',
			'range_settings' =>
				[
					'min' => '1',
					'max' => '120',
					'step' => '1',
				],
			'mobile_options' => true,
			'responsive' => true,
		];
		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => 'Wrapper',
					'key' => 'style_settings__hamburger__wrapper_spacing',
					// 'option' => 'padding',
					'toggle_slug' => 'toggle_key__hamburger_style',
					'sub_toggle' => 'container',
					'priority' => 1,
				]
			)
		);
		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => '',
					'key' => 'style_settings__hamburger__wrapper__bg',
					'toggle_slug' => 'toggle_key__hamburger_style',
					'sub_toggle' => 'container',
					'tab_slug' => 'advanced',
					'priority' => 0,
				]
			)
		);

		$fields['settings__select_animation_color'] = [
			'default_on_front' => true,
			'label' => esc_html__('Line Color', 'divi_flash'),
			'type' => 'color-alpha',
			'default' => '#0038f0',
			'description' => esc_html__('Here you can define a custom line color.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu_item_hover_animation',
			'hover' => 'tabs',
			'show_if' => [
				'settings__menu_item_hover_animation' => 'on'
			]
		];
		$fields['settings__animation__line_weight'] = [
			'label' => esc_html__('Line Weight', 'divi_flash'),
			'type' => 'range',
			'toggle_slug' => 'toggle_key__menu_item_hover_animation',
			'default' => '2px',
			'default_unit' => 'px',
			'range_settings' =>
				[
					'min' => '1',
					'max' => '10',
					'step' => '1',
				]
			,
			'mobile_options' => true,
			'responsive' => true,
			'show_if' => [
				'settings__menu_item_hover_animation' => 'on'
			]
		];

		//ANCHOR - Alignment -- START-->
		$fields['style_settings__alignment'] = [
			'label' => esc_html__('Alignment', 'divi_flash'),
			'type' => 'multiple_buttons',
			'toggle_slug' => 'toggle_key__alignment',
			'tab_slug' => 'advanced',
			'options' => array(
				'df-vertical-menu-alignment-left' => array(
					'title' => esc_html__('Left', 'divi_flash'),
					'icon' => 'align-left',
				),
				'df-vertical-menu-alignment-center' => array(
					'title' => esc_html__('Center', 'divi_flash'),
					'icon' => 'align-center',
				),
				'df-vertical-menu-alignment-right' => array(
					'title' => esc_html__('Right', 'divi_flash'),
					'icon' => 'align-right',
				),
				'df-vertical-menu-alignment-justified' => array(
					'title' => esc_html__('Justified', 'divi_flash'),
					'icon' => 'text-justify',
				),
			),
			'default' => 'df-vertical-menu-alignment-justified',
			'toggleable' => true,
			'mobile_options' => true,
			'responsive' => true,

		];
		//ANCHOR - Menu Items -- START-->
		$fields['style_settings__menu__item_gap'] = [
			'label' => esc_html__('Item Gap', 'divi_flash'),
			'type' => 'range',
			'toggle_slug' => 'toggle_key__menu_items',
			'tab_slug' => 'advanced',
			'default' => '0px',
			'default_unit' => 'px',
			'range_settings' =>
				[
					'min' => '1',
					'max' => '120',
					'step' => '1',
				]
			,
			'mobile_options' => true,
			'responsive' => true,
		];

		$fields['style_settings__menu__icon_color'] = [
			'label' => esc_html__(' Icon Color', 'divi_flash'),
			'type' => 'color-alpha',
			'description' => esc_html__('Here you can define a custom color for menu icon.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu_items',
			'tab_slug' => 'advanced',
			'hover' => 'tabs',
			'default' => '',
		];

		$fields['style_settings__menu__icon_font_size'] = [
			'label' => esc_html__('Icon Size', 'divi_flash'),
			'type' => 'range',
			'option_category' => 'font_option',
			'toggle_slug' => 'toggle_key__menu_items',
			'tab_slug' => 'advanced',
			'default' => '14px',
			'default_unit' => 'px',
			'default_on_front' => '',
			'range_settings' =>
				[
					'min' => '1',
					'max' => '120',
					'step' => '1',
				],
			'mobile_options' => true,
			'responsive' => true,
		];

		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => '',
					'key' => 'style_settings__menu__item_bg',
					'toggle_slug' => 'toggle_key__menu_items',
					'tab_slug' => 'advanced'
				]
			)
		);

		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => 'Menu items',
					'key' => 'style_settings__menu__item_spacing',
					'option' => 'padding',
					'toggle_slug' => 'toggle_key__menu_items',
					'sub_toggle' => 'general',
					'default_padding' => '10px|10px|10px|10px'
				]
			)
		);

		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => 'Icon/Image',
					'key' => 'style_settings__menu__item_icon_spacing',
					'option' => 'margin',
					'toggle_slug' => 'toggle_key__menu_items',
					'sub_toggle' => 'general',
					'default_padding' => '0px|0px|0px|0px'
				]
			)
		);


		//ANCHOR - SubMenu Items -- START-->
		$fields['style_settings__sub_menu__item_gap'] = [
			'label' => esc_html__('Item Gap', 'divi_flash'),
			'type' => 'range',
			'sub_toggle' => 'item',
			'toggle_slug' => 'toggle_key__sub_menu_items',
			'tab_slug' => 'advanced',
			'default_unit' => 'px',
			'default' => 'inherit',
			'range_settings' =>
				[
					'min' => '1',
					'max' => '120',
					'step' => '1',
				],
			'mobile_options' => true,
			'responsive' => true,
			'priority' => 0,
		];
		$fields['style_settings__sub_menu__icon_color'] = [
			'default_on_front' => true,
			'label' => esc_html__('Icon Color', 'divi_flash'),
			'type' => 'color-alpha',
			'default' => '',
			'description' => esc_html__('Here you can define a custom color for icon.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__sub_menu_items',
			'sub_toggle' => 'item',
			'tab_slug' => 'advanced',
			'hover' => 'tabs',
			'priority' => 1,
		];
		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => '',
					'key' => 'style_settings__sub_menu__item__bg',
					'toggle_slug' => 'toggle_key__sub_menu_items',
					'sub_toggle' => 'item',
					'tab_slug' => 'advanced',
					'priority' => 2,
				]
			)
		);
		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => 'Item',
					'key' => 'style_settings__sub_menu__item_spacing',
					'option' => 'padding',
					'toggle_slug' => 'toggle_key__sub_menu_items',
					'sub_toggle' => 'item',
					'priority' => 3,
				]
			)
		);
		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => 'Icon/Image',
					'key' => 'style_settings__sub_menu__item_icon_spacing',
					'option' => 'margin',
					'sub_toggle' => 'item',
					'toggle_slug' => 'toggle_key__sub_menu_items',
					'priority' => 4,
				]
			)
		);

		// ----------------
		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => '',
					'key' => 'style_settings__sub_menu__wrapper__bg',
					'toggle_slug' => 'toggle_key__sub_menu_items',
					'sub_toggle' => 'container',
					'tab_slug' => 'advanced',
					'priority' => 0
				]
			)
		);

		$fields['style_settings__sub_menu__tree_view'] = [
			'label' => esc_html__('Enable Submenu Tree View', 'divi_flash'),
			'type' => 'yes_no_button',
			'description' => 'When enable that time wrapper padding left or right will apply in each submenu wrapper ',
			'options' => [
				'off' => esc_html__('NO', 'divi_flash'),
				'on' => esc_html__('YES', 'divi_flash'),
			],
			'default' => 'on',
			'toggle_slug' => 'toggle_key__sub_menu_items',
			'sub_toggle' => 'container',
			'tab_slug' => 'advanced',
			'priority' => 2,
			'show_if' => [
				'settings__submenu_reveal_type' => 'df-vertical-sub-menu-reveal-stack',
			]
		];
		$fields['style_settings__sub_menu__tree_view_spacing'] = [
			'label' => esc_html__('Tree Spacing', 'divi_flash'),
			'type' => 'range',
			'toggle_slug' => 'toggle_key__sub_menu_items',
			'sub_toggle' => 'container',
			'tab_slug' => 'advanced',
			'default' => '10px',
			'default_unit' => 'px',
			'priority' => 3,
			'range_settings' =>
				[
					'min' => '1',
					'max' => '120',
					'step' => '1',
				],
			'show_if' => [
				'settings__submenu_reveal_type' => 'df-vertical-sub-menu-reveal-stack',
				'style_settings__sub_menu__tree_view' => 'on'
			],
			'mobile_options' => true,
			'responsive' => true,
		];

		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => 'Wrapper',
					'key' => 'style_settings__sub_menu__wrapper_spacing',
					'option' => 'padding',
					'toggle_slug' => 'toggle_key__sub_menu_items',
					'sub_toggle' => 'container',
					'priority' => 1
				]
			)
		);

		//ANCHOR - Mega Menu -- START-->
		$fields['style_settings__mega_menu__columns'] = [
			'label' => esc_html__('Column Gap', 'divi_flash'),
			'type' => 'range',
			'toggle_slug' => 'toggle_key__mega_menu',
			'sub_toggle' => 'container',
			'tab_slug' => 'advanced',
			'default' => '0px',
			'default_unit' => 'px',
			'priority' => 1,
			'range_settings' =>
				[
					'min' => '1',
					'max' => '120',
					'step' => '1',
				],
			'mobile_options' => true,
			'responsive' => true,
		];
		// $fields['style_settings__mega_menu__item_width'] = [
		// 	'label' => esc_html__('Column Width', 'divi_flash'),
		// 	'type' => 'range',
		// 	'toggle_slug' => 'toggle_key__mega_menu',
		// 	'sub_toggle' => 'container',
		// 	'tab_slug' => 'advanced',
		// 	'priority' => 2,
		// 	'default' => '150',
		// 	'default_unit' => 'px',
		// 	'range_settings' =>
		// 		[
		// 			'min' => '1',
		// 			'max' => '500',
		// 			'step' => '1'
		// 		],
		// 	'mobile_options' => true,
		// 	'responsive' => true,
		// ];

		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => '',
					'key' => 'style_settings__mega_menu__wrapper_bg',
					'toggle_slug' => 'toggle_key__mega_menu',
					'sub_toggle' => 'container',
					'priority' => 3,
					'tab_slug' => 'advanced'
				]
			)
		);

		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => '',
					'key' => 'style_settings__mega_menu__wrapper_spacing',
					'toggle_slug' => 'toggle_key__mega_menu',
					'sub_toggle' => 'container',
					'option' => 'padding',
					'tab_slug' => 'advanced',
					'priority' => 4,
					'default_padding' => '15px|15px|15px|15px'
				]
			)
		);


		$fields['style_settings__mega_menu__item_gap'] = [
			'label' => esc_html__('item Gap', 'divi_flash'),
			'type' => 'range',
			'toggle_slug' => 'toggle_key__mega_menu',
			'sub_toggle' => 'item',
			'tab_slug' => 'advanced',
			'priority' => 1,
			'default' => 'inherit',
			'default_unit' => 'px',
			'range_settings' =>
				[
					'min' => '1',
					'max' => '120',
					'step' => '1'
				],
			'mobile_options' => true,
			'responsive' => true,
		];
		$fields['style_settings__mega_menu__icon_color'] = [
			'default_on_front' => true,
			'label' => esc_html__('Icon Color', 'divi_flash'),
			'type' => 'color-alpha',
			'default' => '',
			'description' => esc_html__('Here you can define a custom color for icon.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__mega_menu',
			'priority' => 1,
			'sub_toggle' => 'item',
			'tab_slug' => 'advanced',
			'hover' => 'tabs'
		];
		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => '',
					'key' => 'style_settings__mega_menu__items_bg',
					'toggle_slug' => 'toggle_key__mega_menu',
					'priority' => 2,
					'sub_toggle' => 'item',
					'tab_slug' => 'advanced',
				]
			)
		);
		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => 'Item',
					'key' => 'style_settings__mega_menu__items_spacing',
					'toggle_slug' => 'toggle_key__mega_menu',
					'sub_toggle' => 'item',
					'option' => 'padding',
					'priority' => 3,
				]
			)
		);

		$fields = array_merge(
			$fields,
			$this->add_margin_padding(
				[
					'title' => 'Icon/Image',
					'key' => 'style_settings__mega_menu__items_icon_spacing',
					'toggle_slug' => 'toggle_key__mega_menu',
					'sub_toggle' => 'item',
					'option' => 'margin',
					'priority' => 3,
				]
			)
		);


		//ANCHOR - Active State -- START-->
		$fields['style_settings__menu__active__font_color'] = [
			'label' => esc_html__('Font Color', 'divi_flash'),
			'type' => 'color-alpha',
			'description' => esc_html__('Here you can define a custom color for active menu font.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu__active_state',
			'sub_toggle' => 'main_menu',
			'tab_slug' => 'advanced',
			'hover' => 'tabs',
			'default' => '',
		];

		$fields['style_settings__menu__active__icon_color'] = [
			'label' => esc_html__('Icon Color', 'divi_flash'),
			'type' => 'color-alpha',
			'description' => esc_html__('Here you can define a custom color for active menu icon.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu__active_state',
			'sub_toggle' => 'main_menu',
			'tab_slug' => 'advanced',
			'hover' => 'tabs',
			'default' => '',
		];
		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => 'Item',
					'key' => 'style_settings__menu__active__item_bg',
					'toggle_slug' => 'toggle_key__menu__active_state',
					'sub_toggle' => 'main_menu',
					'tab_slug' => 'advanced'
				]
			)
		);


		// --sub menu-->
		$fields['style_settings__sub_menu__active__font_color'] = [
			'label' => esc_html__('Font Color', 'divi_flash'),
			'type' => 'color-alpha',
			'description' => esc_html__('Here you can define a seperate color for submenu.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu__active_state',
			'sub_toggle' => 'sub_menu',
			'tab_slug' => 'advanced',
			'hover' => 'tabs',
			'default' => '',
		];
		$fields['style_settings__sub_menu__active__icon_color'] = [
			'label' => esc_html__('Icon Color', 'divi_flash'),
			'type' => 'color-alpha',
			'default' => '',
			'description' => esc_html__('Here you can define a custom color for active menu icon.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu__active_state',
			'sub_toggle' => 'sub_menu',
			'tab_slug' => 'advanced',
			'hover' => 'tabs',
			'default' => '',
		];
		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => 'Item',
					'key' => 'style_settings__sub_menu__active__bg',
					'toggle_slug' => 'toggle_key__menu__active_state',
					'sub_toggle' => 'sub_menu',
					'tab_slug' => 'advanced'
				]
			)
		);

		// --mega--menu--> 
		$fields['style_settings__mega_menu__active__font_color'] = [
			'label' => esc_html__('Font Color', 'divi_flash'),
			'type' => 'color-alpha',
			'description' => esc_html__('Here you can define a seperate color for submenu.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu__active_state',
			'sub_toggle' => 'mega_menu',
			'tab_slug' => 'advanced',
			'hover' => 'tabs',
			'default' => '',
		];
		$fields['style_settings__mega_menu__active__icon_color'] = [
			'label' => esc_html__('Icon Color', 'divi_flash'),
			'type' => 'color-alpha',
			'description' => esc_html__('Here you can define a custom color for active menu icon.', 'divi_flash'),
			'toggle_slug' => 'toggle_key__menu__active_state',
			'sub_toggle' => 'mega_menu',
			'tab_slug' => 'advanced',
			'hover' => 'tabs',
			'default' => '',
		];

		$fields = array_merge(
			$fields,
			$this->df_add_bg_field(
				[
					'label' => 'Item',
					'key' => 'style_settings__mega_menu__active__items_bg',
					'toggle_slug' => 'toggle_key__menu__active_state',
					'sub_toggle' => 'mega_menu',
					'tab_slug' => 'advanced'
				]
			)
		);


		//ANCHOR - BADGE -- START-->
		$fields = array_merge($this->df_add_bg_field(
			[
				'label' => 'Background',
				'key' => 'style_settings__menu__badge_bg',
				'toggle_slug' => 'toggle_key__badge',
				'tab_slug' => 'advanced',
				'sub_toggle' => 'general',
			]
		), $fields);

		$fields = array_merge($this->df_add_bg_field(
			[
				'label' => 'Background',
				'key' => 'style_settings__sub_menu__badge_bg',
				'toggle_slug' => 'toggle_key__badge',
				'tab_slug' => 'advanced',
				'sub_toggle' => 'sub-menu',
			]
		), $fields);

		$fields['style_settings__badge__alignment'] = [
			'label' => esc_html__('Badge Alignment', 'divi_flash'),
			'type' => 'text_align',
			'options' =>
				[
					'left' => 'Left',
					'right' => 'Right'
				]
			,
			'default' => '',
			'tab_slug' => 'advanced',
			'toggle_slug' => 'toggle_key__badge',
			'sub_toggle' => 'general',
		];

		//ANCHOR - TOOLTIP -- START-->
		$fields = array_merge($this->df_add_bg_field(
			[
				'label' => 'Tooltip Background',
				'key' => 'style_settings__menu__tooltip_bg',
				'toggle_slug' => 'toggle_key__tooltip',
				'sub_toggle' => 'general',
				'tab_slug' => 'advanced',
			]
		), $fields);

		$fields = array_merge($this->df_add_bg_field(

			[
				'label' => 'Tooltip Background',
				'key' => 'style_settings__sub_menu__tooltip_bg',
				'toggle_slug' => 'toggle_key__tooltip',
				'sub_toggle' => 'sub-menu',
				'tab_slug' => 'advanced',
			]
		), $fields);

		return $fields;
	}

	//ANCHOR - Render CSS
	public function additional_css_styles($render_slug)
	{
		if ($this->props['settings__clicked_menu_element_style'] == 'on' && $this->props['settings__submenu_reveal_type'] == 'df-vertical-sub-menu-reveal-stack') {

			//Icon Selector
			$this->active_menu_item_icon_selector .= ", {$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper.active>.df-vertical-menu-icon";
			$this->active_menu_item_icon_hover_selector .= ",{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper.active:hover>.df-vertical-menu-icon";
			$this->active_sub_menu_item_icon_selector .= ", {$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-col-added>ul):not(.df-vertical-inside-mega-menu)>li>.df_vertical_menu_item_elements_wrapper.active>.df-vertical-menu-icon";
			$this->active_sub_menu_item_icon_hover_selector .= " , {$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-col-added>ul):not(.df-vertical-inside-mega-menu)>li>.df_vertical_menu_item_elements_wrapper.active:hover>.df-vertical-menu-icon";
			$this->active_mega_menu_item_icon_selector .= " ,{$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added  li > .df_vertical_menu_item_elements_wrapper.active .df-vertical-menu-icon";
			$this->active_mega_menu_item_icon_hover_selector .= " ,{$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added  li > .df_vertical_menu_item_elements_wrapper.active:hover .df-vertical-menu-icon";

			//Dom Selector
			$this->active_menu_item_selector .= " ,{$this->main_css_element} li > .df_vertical_menu_item_elements_wrapper.active";
			$this->active_menu_item_hover_selector .= " ,{$this->main_css_element} li > .df_vertical_menu_item_elements_wrapper.active:hover";
			$this->active_sub_menu_item_selector .= " , {$this->main_css_element} ul.df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper.active";
			$this->active_sub_menu_item_hover_selector .= " ,{$this->main_css_element} ul.df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper.active:hover";
			$this->active_mega_menu_item_selector .= " , {$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added  li > .df_vertical_menu_item_elements_wrapper.active";
			$this->active_mega_menu_item_hover_selector .= " ,{$this->main_css_element}  ul.df-vertical-sub-menu.df-vertical-col-added  li > .df_vertical_menu_item_elements_wrapper.active";

		}
		//ANCHOR main menu css
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__item_gap',
				'selector' => "{$this->main_css_element}  li:not(:first-child),{$this->main_css_element} .df-vertical-inside-mega-menu li:first-child",
				'unit' => 'px',
				'type' => 'margin-top',
				'important' => true,
			]
		);

		// //alignment style 
		// if ($this->props['style_settings__alignment'] == 'center') {
		// 	$this->apply_single_value(
		// 		[
		// 			'render_slug' => $render_slug,
		// 			'slug' => 'style_settings__alignment',
		// 			'selector' => "{$this->main_css_element} ul[class*='df-vertical-menu-nav-level-'] li.df-vertical-menu-item .df_vertical_menu_item_elements_wrapper",
		// 			'unit' => '',
		// 			'type' => 'justify-content',
		// 			'important' => true,
		// 		]
		// 	);
		// }

		if (method_exists('ET_Builder_Module_Helper_Style_Processor', 'process_extended_icon')) {
			$this->generate_styles(
				array(
					'utility_arg' => 'icon_font_family',
					'render_slug' => $render_slug,
					'base_attr_name' => 'settings__hamburger_icon',
					'important' => true,
					'selector' => '%%order_class%% .et-pb-icon.df-vertical-menu-hamburger-icon',
					'processor' => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);
		}

		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug' => 'settings__animation__line_weight',
				'selector' => "{$this->main_css_element} li.df-vertical-menu-item > .df_vertical_menu_item_elements_wrapper .df_vertical_border_hover_effect:after , {$this->main_css_element} li.df-vertical-menu-item > .df_vertical_menu_item_elements_wrapper .df_vertical_border_hover_effect:before",
				'unit' => 'px',
				'type' => 'height',
				'important' => true,
			]
		);
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__icon_font_size',
				'selector' => "{$this->main_css_element} > li .df_vertical_menu_item_elements_wrapper span.df-vertical-menu-icon",
				'unit' => 'px',
				'type' => 'font-size',
				'default' => '14',
				'important' => true,
			]
		);
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__hamburger_icon_font_size',
				'selector' => "{$this->main_css_core_element} .df-vertical-humberger-container span.df-vertical-menu-hamburger-icon .hamburger",
				'unit' => 'px',
				'type' => 'width',
				'default' => '14',
				'important' => true,
			]
		);
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__hamburger_icon_font_size',
				'selector' => "{$this->main_css_core_element} .df-vertical-humberger-container span.df-vertical-menu-hamburger-icon .hamburger",
				'unit' => 'px',
				'type' => 'height',
				'default' => '14',
				'important' => true,
			]
		);

		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__item_gap',
				'selector' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li:not(:first-child)",
				'unit' => 'px',
				'important' => true,
				'type' => 'margin-top',
			]
		);
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__item_gap',
				'selector' => "{$this->main_css_element} li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added .df-vertical-sub-menu li",
				'unit' => 'px',
				'important' => true,
				'type' => 'margin-top',
			]
		);
		$this->apply_single_value(

			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__columns',
				'selector' => "{$this->main_css_element} li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added",
				'unit' => 'px',
				'important' => true,
				'type' => 'gap',
			]

		);
		$this->df_process_range(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__tree_view_spacing',
				'selector' => "{$this->dynamic_main_css_element('.df_enable_sub_menu__tree_view')}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-mega-menu-item):not(.df-vertical-menu-nav-level-1)",
				'unit' => 'px',
				'type' => 'padding-left',
				'important' => true,
			]
		);

		$this->df_process_color(

			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__icon_color',
				'selector' => "{$this->main_css_element} li>a>.df-vertical-menu-icon ",
				'hover' => "{$this->main_css_element} li>a:hover>.df-vertical-menu-icon ",
				'type' => 'color'
			]
		);
		$this->df_process_color(

			[
				'render_slug' => $render_slug,
				'slug' => 'settings__hamburger_icon_color',
				'selector' => "{$this->main_css_core_element} .df-vertical-humberger-container span.df-vertical-menu-hamburger-icon svg ",
				'hover' => "{$this->main_css_core_element} .df-vertical-humberger-container:hover span.df-vertical-menu-hamburger-icon svg ",
				'type' => 'fill'
			]
		);
		$this->df_process_color(

			[
				'render_slug' => $render_slug,
				'slug' => 'settings__select_animation_color',
				'selector' => "{$this->main_css_element} li.df-vertical-menu-item > .df_vertical_menu_item_elements_wrapper .df_vertical_border_hover_effect:after,{$this->main_css_element} li.df-vertical-menu-item > .df_vertical_menu_item_elements_wrapper .df_vertical_border_hover_effect:before ",
				'hover' => "{$this->main_css_element}  > li.df-vertical-menu-item > .df_vertical_menu_item_elements_wrapper .df_vertical_border_hover_effect:after,{$this->main_css_element} li.df-vertical-menu-item > .df_vertical_menu_item_elements_wrapper .df_vertical_border_hover_effect:before ",
				'type' => 'background-color'
			]
		);

		$this->df_process_color(

			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__active__icon_color',
				'selector' => $this->active_menu_item_icon_selector,
				'hover' => $this->active_menu_item_icon_hover_selector,
				'type' => 'color'
			]
		);
		$this->df_process_color(

			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__active__icon_color',
				'selector' => $this->active_sub_menu_item_icon_selector,
				'hover' => $this->active_sub_menu_item_icon_hover_selector,
				'type' => 'color'
			]

		);

		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__active__font_color',
				'selector' => $this->active_menu_item_selector,
				'hover' => $this->active_menu_item_hover_selector,
				'type' => 'color'
			]
		);

		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__icon_color',
				'selector' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-col-added> ul):not(.df-vertical-inside-mega-menu) > li > a>.df-vertical-menu-icon   ",
				'hover' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-col-added > ul):not(.df-vertical-inside-mega-menu) > li > a:hover>.df-vertical-menu-icon   ",
				'type' => 'color'
			]
		);
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__icon_color',
				'selector' => "{$this->main_css_element} .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper > .df-vertical-menu-icon  ",
				'hover' => "{$this->main_css_element} .df-vertical-sub-menu.df-vertical-col-added li a:hover > .df-vertical-menu-icon   ",
				'type' => 'color'
			]
		);

		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__active__font_color',
				'selector' => $this->active_sub_menu_item_selector,
				'hover' => $this->active_sub_menu_item_hover_selector,
				'type' => 'color'
			]
		);
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__active__font_color',
				'selector' => $this->active_mega_menu_item_selector,
				'hover' => $this->active_mega_menu_item_hover_selector,
				'type' => 'color'
			]
		);
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__active__icon_color',
				'selector' => $this->active_mega_menu_item_icon_selector,
				'hover' => $this->active_mega_menu_item_icon_hover_selector,
				'type' => 'color'
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__item_bg',
				'selector' => "{$this->main_css_element} li > a",
				'hover' => "{$this->main_css_element}  li > a:hover"
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__active__item_bg',
				'selector' => $this->active_menu_item_selector,
				'hover' => $this->active_menu_item_hover_selector
			]
		);

		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__item_icon_spacing_margin',
				'type' => 'margin',
				'selector' => "{$this->main_css_element}  li > .df_vertical_menu_item_elements_wrapper .df-vertical-menu-icon,{$this->main_css_element}  li > .df_vertical_menu_item_elements_wrapper > img",
				'hover' => "{$this->main_css_element}  li > a:hover .df-vertical-menu-icon , {$this->main_css_element}  li > a:hover > img",
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__item_spacing_padding',
				'type' => 'padding',
				'selector' => "{$this->main_css_element}  li > .df_vertical_menu_item_elements_wrapper",
				'hover' => "{$this->main_css_element}  li > .df_vertical_menu_item_elements_wrapper:hover",
			]
		);


		// submenu items
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__item__bg',
				'selector' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-custom-submenu) > li  .df_vertical_menu_item_elements_wrapper",
				'hover' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-custom-submenu) > li  .df_vertical_menu_item_elements_wrapper:hover"
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__wrapper__bg',
				'selector' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-mega-menu-item):not(.df-vertical-custom-submenu)",
				'hover' => "{$this->main_css_element} .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-mega-menu-item):not(.df-vertical-custom-submenu):hover"
			]
		);

		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__hamburger__wrapper__bg',
				'selector' => "{$this->main_css_core_element}  .df-vertical-humberger-container",
				'hover' => "{$this->main_css_core_element}  .df-vertical-humberger-container:hover"
			]
		);
		// submenu items
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__wrapper_bg',
				'selector' => "{$this->main_css_element} .df-vertical-sub-menu.df-vertical-col-added",
				'hover' => "{$this->main_css_element} .df-vertical-sub-menu.df-vertical-col-added:hover"
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__items_bg',
				'selector' => "{$this->main_css_element} .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper",
				'hover' => "{$this->main_css_element} .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper:hover"
			]
		);


		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__active__items_bg',
				'selector' => $this->active_mega_menu_item_selector,
				'hover' => $this->active_mega_menu_item_hover_selector
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__active__bg',
				'selector' => $this->active_sub_menu_item_selector,
				'hover' => $this->active_sub_menu_item_hover_selector
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__item_icon_spacing_margin',
				'type' => 'margin',
				'selector' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper .df-vertical-menu-icon,{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper> img",
				'hover' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper:hover .df-vertical-menu-icon,{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper:hover > img",
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__item_spacing_padding',
				'type' => 'padding',
				'selector' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper",
				'hover' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu) > li > .df_vertical_menu_item_elements_wrapper:hover",
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__wrapper_spacing_padding',
				'type' => 'padding',
				'selector' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-mega-menu-item)",
				'hover' => "{$this->main_css_element}  .df-vertical-sub-menu:not(.df-vertical-col-added):not(.df-vertical-inside-mega-menu):not(.df-vertical-mega-menu-item):hover",
				'important' => false
			]
		);

		//FIXME - Spacing		//hamburger
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__hamburger__wrapper_spacing_padding',
				'type' => 'padding',
				'selector' => "{$this->main_css_core_element}  .df-vertical-humberger-container",
				'hover' => "{$this->main_css_core_element}  .df-vertical-humberger-container:hover",
				'important' => false
			]
		);

		//hamburger
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__hamburger__wrapper_spacing_margin',
				'type' => 'margin',
				'selector' => "{$this->main_css_core_element}  .df-vertical-humberger-container",
				'hover' => "{$this->main_css_core_element}  .df-vertical-humberger-container:hover",
				'important' => false
			]
		);

		// mega menu 
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__wrapper_spacing_padding',
				'type' => 'padding',
				'selector' => "{$this->main_css_element}  li.df-vertical-menu-item .df-vertical-sub-menu.df-vertical-col-added ",
				'hover' => "{$this->main_css_element} li.df-vertical-menu-item  .df-vertical-sub-menu.df-vertical-col-added:hover",
				'important' => false //it should be false otherwise submenu reveal on click lose smoothness
			]
		);

		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__items_icon_spacing_margin',
				'type' => 'margin',
				'selector' => "{$this->main_css_element}  .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper .df-vertical-menu-icon,{$this->main_css_element}  .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper > img",
				'hover' => "{$this->main_css_element}  .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper:hover .df-vertical-menu-icon,{$this->main_css_element}  .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper:hover > img",
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__mega_menu__items_spacing_padding',
				'type' => 'padding',
				'selector' => "{$this->main_css_element}  .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper ",
				'hover' => "{$this->main_css_element}  .df-vertical-sub-menu.df-vertical-col-added li .df_vertical_menu_item_elements_wrapper:hover",
			]
		);

		// ANCHOR - PROCESS-badge-STYLE
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__badge_bg',
				'selector' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-badge",
				'hover' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper:hover .df-vertical-nav-item-badge",
				'important' => true
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__badge_bg',
				'selector' => "{$this->main_css_element} .df-vertical-sub-menu li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-badge",
				'hover' => "{$this->main_css_element} .df-vertical-sub-menu li>a:hover .df-vertical-nav-item-badge",
				'important' => true
			]
		);


		if (isset($this->props['settings__badge_visiblity']) && $this->props['settings__badge_visiblity'] == 'off') {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-badge",
					'declaration' => sprintf(
						'%2$s:%1$s !important;',
						'none',
						'display'
					),
					// 'media_query' => ET_Builder_Element::get_media_query('max_width_980')
				]
			);
		}

		// ANCHOR - PROCESS-tooltip-STYLE
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__menu__tooltip_bg',
				'selector' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-tooltip",
				'hover' => "{$this->main_css_element} li>a:hover .df-vertical-nav-item-tooltip",
				'important' => true
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug' => 'style_settings__sub_menu__tooltip_bg',
				'selector' => "{$this->main_css_element}  .df-vertical-sub-menu li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-tooltip",
				'hover' => "{$this->main_css_element} .df-vertical-sub-menu li>a:hover .df-vertical-nav-item-tooltip",
				'important' => true
			]
		);
		if (isset($this->props['settings__tooltip_visiblity']) && $this->props['settings__tooltip_visiblity'] == 'off') {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' => "{$this->main_css_element} li>.df_vertical_menu_item_elements_wrapper .df-vertical-nav-item-tooltip",
					'declaration' => sprintf(
						'%2$s:%1$s !important;',
						'none',
						'display'
					),
				]
			);
		}
	}
	public function hamburgerMarkup($status, $content)
	{
		if ($status == "df_enabled_hamburger") {
			return sprintf('<span class="df-vertical-humberger-container">
                            		<span class="df-vertical-menu-hamburger-text">%1$s</span>
                            		<span class="df-vertical-menu-hamburger-icon">
                            			<span class="hamburger">
																		<div class="close-hamburger"> %2$s</div> 
																		<div class="open-hamburger"> %3$s </div>
																	</span>
                        				</span>
																</span>', $content, $this->hamburger_svg(), $this->hamburger_svg(true));
		} else {
			return '';
		}
	}
	public function hamburger_svg($close = false)
	{
		$jsonString = file_get_contents(DIFL_ADMIN_DIR_PATH . "assets/svg/verticalMenu.json");
		$svgArray = json_decode($jsonString, true);

		if ($close) {
			return $svgArray['VerticalMenu']['close'];
		} else {
			if (empty($this->props['settings__hamburger_icon_preset'])) {
				return $svgArray['VerticalMenu']['icon1'];
			}
		}


		return $svgArray['VerticalMenu'][$this->props['settings__hamburger_icon_preset']];
	}
	public function render($attrs, $content, $render_slug)
	{
		$this->additional_css_styles($render_slug);
		wp_enqueue_script('df_vertical_menu');

		$menu_slug = $this->props['settings__select_menu_slug'];
		$animation_type = '';
		$animation_type = $this->props['settings__menu_item_hover_animation'] == 'on' ? $this->props['settings__select_animation_type'] : '';
		$settings__submenu_reveal_type = $this->props['settings__submenu_reveal_type'];
		$content_alignment = $this->props['style_settings__alignment'];
		$content_alignment_tablet = $this->props['style_settings__alignment_tablet'];
		$content_alignment_phone = $this->props['style_settings__alignment_phone'];
		$settings__hamburger_icon_preset = $this->props['settings__hamburger_icon_preset'];
		$settings__hamburger_text = $this->props['settings__hamburger_text'];
		$settings__use_hamburger_for_mobile = '';
		$settings__submenu_reveal_dir = $this->props['settings__submenu_reveal_dir'];
		$settings__hamburger_menu_reveal_type = $this->props['settings__hamburger_menu_reveal_type'];

		if ($this->props['settings__use_hamburger_for_mobile'] == 'on') {
			$settings__use_hamburger_for_mobile = 'df_enabled_hamburger';
		}
		;

		if ($this->props['settings__submenu_reveal_type'] === 'df-vertical-sub-menu-reveal-stack') {
			$style_settings__sub_menu__tree_view = ($this->props['style_settings__sub_menu__tree_view'] === 'off' && $this->props['settings__submenu_reveal_type'] == 'df-vertical-sub-menu-reveal-stack') ? 'df_disable_sub_menu__tree_view' : 'df_enable_sub_menu__tree_view';
		} else {
			$style_settings__sub_menu__tree_view = '';
		}


		$style_settings__badge__alignment = $this->props['style_settings__badge__alignment'];
		if ($animation_type != "") {
			$animation_type = "df-vertical-has-item-animation $animation_type";
		}
		$menu_items = df_vertical_get_am_menu(
			[
				'menu' => '',
				'menu_id' => $menu_slug,
			]
		);
		add_filter('et_global_assets_list', [$this, 'difl_load_required_divi_assets'], 10, 3);
		add_filter('et_late_global_assets_list', [$this, 'difl_load_required_divi_assets'], 10, 3);

		return sprintf(
			'<div class="%12$s %10$s %13$s df_vertical_menu_main_container  %7$s %4$s %5$s-tablet %6$s-phone %1$s badge-position-%2$s %3$s" > 
%11$s %8$s</div>',
			esc_html($settings__submenu_reveal_type),
			esc_html($style_settings__badge__alignment),
			esc_html($animation_type),
			esc_html($content_alignment),
			esc_html($content_alignment_tablet),
			esc_html($content_alignment_phone),
			esc_html($style_settings__sub_menu__tree_view),
			$menu_items,
			esc_html($settings__hamburger_icon_preset),
			esc_html($settings__use_hamburger_for_mobile),
			$this->hamburgerMarkup($settings__use_hamburger_for_mobile, $settings__hamburger_text),
			esc_html( $settings__submenu_reveal_dir ),
			esc_html( $settings__hamburger_menu_reveal_type )

		);
	}
	public function difl_load_required_divi_assets($assets_list, $assets_args, $instance)
	{
		$assets_prefix = et_get_dynamic_assets_path();
		$all_shortcodes = $instance->get_saved_page_shortcodes();

		if (!isset($assets_list['et_icons_all'])) {
			$assets_list['et_icons_all'] = [
				'css' => "{$assets_prefix}/css/icons_all.css",
			];
		}

		if (!isset($assets_list['et_icons_fa'])) {
			$assets_list['et_icons_fa'] = [
				'css' => "{$assets_prefix}/css/icons_fa_all.css",
			];
		}

		return $assets_list;
	}
}

new DF_VerticalMenu;

