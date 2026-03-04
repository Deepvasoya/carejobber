<?php

class SocialShare extends ET_Builder_Module {
	use \DIFL\Handler\Fa_Icon_Handler;
	use DF_UTLS;

	public $icon_path;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	function init() {
		$this->name            = esc_html__( 'Social Share', 'divi_flash' );
		$this->plural          = esc_html__( 'Social Share', 'divi_flash' );
		$this->slug            = 'difl_social_share';
		$this->vb_support      = 'on';
		$this->icon_path       = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/social-share.svg';
		$this->child_slug      = 'difl_social_share_item';
		$this->child_item_text = esc_html__( 'Social Share Item', 'divi_flash' );

		$this->main_css_element = "%%order_class%%";
	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'settings'        => esc_html__( 'General', 'divi_flash' ),
					'header'          => esc_html__( 'Header', 'divi_flash' ),
					'content_tooltip' => esc_html__( 'Tooltip Settings', 'divi_flash' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'settings'              => esc_html__( 'Settings', 'divi_flash' ),
					'alignment'             => esc_html__( 'Alignment', 'divi_flash' ),
					'icon'                  => esc_html__( 'Icon/Image', 'divi_flash' ),
					'label'                 => esc_html__( 'Label', 'divi_flash' ),
					'label_container'       => esc_html__( 'Label Container', 'divi_flash' ),
					'header'                => [
						'title'             => esc_html__( 'Header', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => [
							'icon'      => [
								'name' => 'Icon',
							],
							'title'     => [
								'name' => 'Title',
							],
							'sub_title' => [
								'name' => 'Sub Title',
							],
						],
					],
					'header_container'      => esc_html__( 'Header Container', 'divi_flash' ),
					'share_button'          => esc_html__( 'Share Button', 'divi_flash' ),
					'design_tooltip'        => esc_html__( 'Tooltip', 'divi_flash' ),
					'design_tooltip_text'   => [
						'title'             => esc_html__( ' Tooltip Text', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'sub_toggles'       => [
							'body'  => [
								'name' => 'p',
								'icon' => 'text-left',
							],
							'a'     => [
								'name' => 'A',
								'icon' => 'text-link',
							],
							'ul'    => [
								'name' => 'UL',
								'icon' => 'list',
							],
							'ol'    => [
								'name' => 'OL',
								'icon' => 'numbered-list',
							],
							'quote' => [
								'name' => 'QUOTE',
								'icon' => 'text-quote',
							]
						],
					],
					'design_tooltip_header' => [
						'title'             => esc_html__( 'Tooltip Heading Text', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => [
							'h1' => [
								'name' => 'H1',
								'icon' => 'text-h1',
							],
							'h2' => [
								'name' => 'H2',
								'icon' => 'text-h2',
							],
							'h3' => [
								'name' => 'H3',
								'icon' => 'text-h3',
							],
							'h4' => [
								'name' => 'H4',
								'icon' => 'text-h4',
							],
							'h5' => [
								'name' => 'H5',
								'icon' => 'text-h5',
							],
							'h6' => [
								'name' => 'H6',
								'icon' => 'text-h6',
							],
						],
					],
				],
			],
		];
	}

	public function get_advanced_fields_config() {
		$advanced_fields = [];

		$advanced_fields['borders']['default']             = [
			'css'      => [
				'main' => [
					'border_radii'  => "%%order_class%%",
					'border_styles' => "%%order_class%%",
				],
			],
			'defaults' => [
				'border_radii'  => 'on|0px|0px|0px|0px',
				'border_styles' => [
					'width' => '0px',
					'color' => '#333333',
					'style' => 'solid',
				],
			],
		];
		$advanced_fields['borders']['icon']                = [
			'css'         => [
				'main' => [
					'border_radii'  => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon',
					'border_styles' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon',
				],
			],
			'defaults'    => [
				'border_radii'  => 'on|0px|0px|0px|0px',
				'border_styles' => [
					'width' => '0px',
					'color' => '#333333',
					'style' => 'solid',
				],
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'icon',
		];
		$advanced_fields['borders']['label']               = [
			'css'         => [
				'main' => [
					'border_radii'  => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content',
					'border_styles' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content',
				],
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'label_container',
		];
		$advanced_fields['borders']['header_container']    = [
			'css'         => [
				'main' => [
					'border_radii'  => '%%order_class%% #difl-social-share-header-container.difl_social_share_header_container',
					'border_styles' => '%%order_class%% #difl-social-share-header-container.difl_social_share_header_container',
				],
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'header_container',
		];
		$advanced_fields['borders']['share_button']        = [
			'css'         => [
				'main' => [
					'border_radii'  => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper',
					'border_styles' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper',
				],
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'share_button',
		];
		$advanced_fields['box_shadow']                     = [
			'default' => [
				'css' => [
					'main' => '%%order_class%%',
				],
			],
		];
		$advanced_fields['box_shadow']['icon']             = [
			'css'         => [
				'main'      => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon',
				'important' => true,
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'icon',
		];
		$advanced_fields['box_shadow']['label']            = [
			'css'         => [
				'main'      => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content',
				'important' => true,
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'label_container',
		];
		$advanced_fields['box_shadow']['header_container'] = [
			'css'         => [
				'main'      => '%%order_class%% #difl-social-share-header-container.difl_social_share_header_container',
				'important' => true,
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'header_container',
		];
		$advanced_fields['box_shadow']['share_button']     = [
			'css'         => [
				'main'      => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper',
				'important' => true,
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'share_button',
		];
		$advanced_fields['fonts']['label']                 = [
			'label'            => esc_html__( 'Title', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'label',
			'hide_text_shadow' => false,
			'line_height'      => [
				'default' => '1.7em',
			],
			'font_size'        => [
				'default' => '12px',
			],
			'css'              => [
				'main' => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text",
				'hover' => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover .difl_social_share_content .difl_social_share_text",
			],
		];
		$advanced_fields['fonts']['icon']                  = [
			'label'               => esc_html__( 'Icon', 'divi_flash' ),
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'icon',
			'hide_text_shadow'    => false,
			'hide_text_align'     => true,
			'hide_text_color'     => true,
			'hide_font'           => true,
			'hide_font_size'      => true,
			'hide_line_height'    => true,
			'hide_letter_spacing' => true,
			'css'                 => [
				'main' => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon i:before",
				'hover' => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover .difl_social_share_icon i:before",
			],
		];
		$advanced_fields['fonts']['header_title']          = [
			'label'            => esc_html__( 'Title', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'header',
			'sub_toggle'       => 'title',
			'hide_text_shadow' => false,
			'line_height'      => [
				'default' => '1em',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main' => "%%order_class%% #difl-social-share-header-container .difl_social_share_header_content .difl_social_share_header_title",
				'hover' => "%%order_class%% #difl-social-share-header-container:hover .difl_social_share_header_content .difl_social_share_header_title",
			],
		];
		$advanced_fields['fonts']['header_sub_title']      = [
			'label'            => esc_html__( 'Sub-Title', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'header',
			'sub_toggle'       => 'sub_title',
			'hide_text_shadow' => false,
			'line_height'      => [
				'default' => '1em',
			],
			'font_size'        => [
				'default' => '13px',
			],
			'css'              => [
				'main' => "%%order_class%% #difl-social-share-header-container .difl_social_share_header_content .difl_social_share_header_sub_title",
				'hover' => "%%order_class%% #difl-social-share-header-container:hover .difl_social_share_header_content .difl_social_share_header_sub_title",
			],
		];
		$advanced_fields['margin_padding']                 = [
			'css' => [
				'main' => '%%order_class%%',
			],
		];
		$advanced_fields['text']                           = false;
		$advanced_fields['filters']                        = false;
		$advanced_fields['transform']                      = false;
		$advanced_fields['button']                         = false;
		$advanced_fields['link_options']                   = false;

		/*------ Tooltip -------*/
		$advanced_fields['fonts']['tooltip_text_a']           = [
			'label'            => esc_html__( 'Link', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'a',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] a",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover a",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_ul']          = [
			'label'            => esc_html__( 'Ul', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'ul',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] ul li",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover ul li",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_ol']          = [
			'label'            => esc_html__( 'Ol', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'ol',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] ol li",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover ol li",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h1']          = [
			'label'            => esc_html__( 'H1', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h1',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '32px',
			],
			'font_size'        => [
				'default' => '32px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h1",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h1",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h2']          = [
			'label'            => esc_html__( 'H2', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h2',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '28px',
			],
			'font_size'        => [
				'default' => '28px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h2",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h2",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h3']          = [
			'label'            => esc_html__( 'H3', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h3',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '24px',
			],
			'font_size'        => [
				'default' => '24px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h3",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h3",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h4']          = [
			'label'            => esc_html__( 'H4', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h4',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '20px',
			],
			'font_size'        => [
				'default' => '20px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h4",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h4",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h5']          = [
			'label'            => esc_html__( 'H5', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h5',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '16px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h5",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h5",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h6']          = [
			'label'            => esc_html__( 'H6', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h6',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '14px',
			],
			'font_size'        => [
				'default' => '14px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h6",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h6",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_body']        = [
			'label'            => esc_html__( 'Body', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'body',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}']",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover",
				'important' => 'all'
			]
		];
		$advanced_fields['borders']['tooltips_border']        = [
			'css'         => [
				'main' => [
					'border_radii'        => ".tippy-box[data-theme~='{$this->main_css_element}']",
					'border_styles'       => ".tippy-box[data-theme~='{$this->main_css_element}']",
					'border_styles_hover' => ".tippy-box[data-theme~='{$this->main_css_element}']:hover"
				]
			],
			'toggle_slug' => 'design_tooltip',
			'tab_slug'    => 'advanced'
		];
		$advanced_fields['fonts']['tooltip_text_quote']       = [
			'label'            => esc_html__( 'Quote', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'quote',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] blockquote",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover blockquote",
				'important' => 'all'
			]
		];
		$advanced_fields['box_shadow']['tooltips_box_shadow'] = [
			'css'         => [
				'main'  => ".tippy-box[data-theme~='{$this->main_css_element}']",
				'hover' => ".tippy-box[data-theme~='{$this->main_css_element}']:hover"
			],
			'toggle_slug' => 'design_tooltip',
			'tab_slug'    => 'advanced'
		];

		return $advanced_fields;
	}

	public function get_custom_css_fields_config() {
		return [
			'button_container'           => [
				'label'    => esc_html__( 'Share Button', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper'
			],
			'button_container_hover'     => [
				'label'    => esc_html__( 'Share Button Hover', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover'
			],
			'icon_image_container'       => [
				'label'    => esc_html__( 'Icon/Image Container', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon'
			],
			'icon_image_container_hover' => [
				'label'    => esc_html__( 'Icon/Image Container Hover', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon:hover'
			],
			'label_container'            => [
				'label'    => esc_html__( 'Label Container', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content'
			],
			'label_container_hover'      => [
				'label'    => esc_html__( 'Label Container Hover', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content:hover'
			],
			'icon'                       => [
				'label'    => esc_html__( 'Icon', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon i:before'
			],
			'icon_hover'                 => [
				'label'    => esc_html__( 'Icon Hover', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon i:hover:before'
			],
			'image'                      => [
				'label'    => esc_html__( 'Image', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon img'
			],
			'image_hover'                => [
				'label'    => esc_html__( 'Image Hover', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon img:hover'
			],
			'label'                      => [
				'label'    => esc_html__( 'Label', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text'
			],
			'label_hover'                => [
				'label'    => esc_html__( 'Label Hover', 'divi_flash' ),
				'selector' => '%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text:hover'
			],
		];
	}

	public function get_fields() {
		$fields = [];

		$fields['item_view'] = [
			'label'            => esc_html__( 'View', 'divi_flash' ),
			'description'      => esc_html__( 'Here you can choose the view of your Social Share Item', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'configuration',
			'options'          => [
				'iconAndText' => esc_html__( 'Icon & Text', 'divi_flash' ),
				'icon'        => esc_html__( 'Icon', 'divi_flash' ),
				'text'        => esc_html__( 'Text', 'divi_flash' ),
			],
			'default'          => 'iconAndText',
			'default_on_front' => 'iconAndText',
			'toggle_slug'      => 'settings',
		];
//		$fields['show_label']     = [
//			'label'            => esc_html__( 'Show Label', 'divi_flash' ),
//			'type'             => 'yes_no_button',
//			'option_category'  => 'configuration',
//			'options'          => [
//				'off' => esc_html__( 'No', 'divi_flash' ),
//				'on'  => esc_html__( 'Yes', 'divi_flash' ),
//			],
//			'toggle_slug'      => 'settings',
//			'description'      => esc_html__( '', 'divi_flash' ),
//			'default_on_front' => 'on',
//			'default'          => 'on',
//			'show_if'          => [
//				'item_view' => 'iconAndText'
//			]
//		];
		$fields['column_view']    = [
			'label'           => esc_html__( 'Columns', 'divi_flash' ),
			'description'     => esc_html__( '', 'divi_flash' ),
			'type'            => 'select',
			'default'         => 'auto',
			'option_category' => 'configuration',
			'mobile_options'  => true,
			'responsive'      => true,
			'options'         => [
				'auto'  => esc_html__( 'Auto', 'divi_flash' ),
				'one'   => esc_html__( '1', 'divi_flash' ),
				'two'   => esc_html__( '2', 'divi_flash' ),
				'three' => esc_html__( '3', 'divi_flash' ),
				'four'  => esc_html__( '4', 'divi_flash' ),
				'five'  => esc_html__( '5', 'divi_flash' ),
				'six'   => esc_html__( '6', 'divi_flash' ),
			],
			'tab_slug'        => 'general',
			'toggle_slug'     => 'settings',
		];
		$fields['columns_gap']    = [
			'label'            => esc_html__( 'Columns Gap', 'divi_flash' ),
			'description'      => esc_html__( 'Control the gap of the Social Share column by increasing or decreasing the gap size.', 'divi_flash' ),
			'type'             => 'range',
			'option_category'  => 'font_option',
			'allowed_units'    => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'default'          => '10px',
			'default_unit'     => 'px',
			'default_on_front' => '10px',
			'range_settings'   => [
				'min'  => '1',
				'max'  => '120',
				'step' => '1',
			],
			'mobile_options'   => false,
			'responsive'       => false,
			'sticky'           => false,
			'hover'            => false,
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'settings',
		];
		$fields['rows_gap']       = [
			'label'            => esc_html__( 'Rows Gap', 'divi_flash' ),
			'description'      => esc_html__( 'Control the gap of the Social Share row by increasing or decreasing the gap size.', 'divi_flash' ),
			'type'             => 'range',
			'option_category'  => 'font_option',
			'allowed_units'    => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'default'          => '10px',
			'default_unit'     => 'px',
			'default_on_front' => '10px',
			'range_settings'   => [
				'min'  => '1',
				'max'  => '120',
				'step' => '1',
			],
			'mobile_options'   => false,
			'responsive'       => false,
			'sticky'           => false,
			'hover'            => false,
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'settings',
		];
		$fields['button_height']  = [
			'label'            => esc_html__( 'Button Height', 'divi_flash' ),
			'description'      => esc_html__( 'Control the Button Height of the Social Share Button', 'divi_flash' ),
			'type'             => 'range',
			'option_category'  => 'font_option',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'settings',
			'allowed_units'    => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'default'          => 'auto',
			'default_unit'     => '',
			'default_on_front' => '',
			'range_settings'   => [
				'min'  => '1',
				'max'  => '120',
				'step' => '1',
			],
			'mobile_options'   => true,
			'depends_show_if'  => 'on',
			'responsive'       => true,
			'sticky'           => true,
		];
		$fields['url_new_window'] = [
			'label'            => esc_html__( 'Link Target', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'configuration',
			'options'          => [
				'off' => esc_html__( 'In The Same Window', 'divi_flash' ),
				'on'  => esc_html__( 'In The New Tab', 'divi_flash' ),
			],
			'toggle_slug'      => 'settings',
			'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'divi_flash' ),
			'default_on_front' => 'on',
			'default'          => 'on',
		];
		// Icon
		$fields['icon_color']         = [
			'label'          => esc_html__( 'Icon Color', 'divi_flash' ),
			'description'    => esc_html__( 'Here you can define a custom color for the social network icon.', 'divi_flash' ),
			'type'           => 'color-alpha',
			'custom_color'   => true,
			'tab_slug'       => 'advanced',
			'toggle_slug'    => 'icon',
			'hover'          => 'tabs',
			'mobile_options' => true,
			'sticky'         => true,
		];
		$fields['use_icon_font_size'] = [
			'label'            => esc_html__( 'Use Custom Icon Size', 'divi_flash' ),
			'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'off' => et_builder_i18n( 'No' ),
				'on'  => et_builder_i18n( 'Yes' ),
			],
			'default_on_front' => 'off',
			'affects'          => [
				'icon_font_size',
			],
			'depends_show_if'  => 'on',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'icon',
			'option_category'  => 'font_option',
		];
		$fields['icon_font_size']     = [
			'label'            => esc_html__( 'Icon Font Size', 'divi_flash' ),
			'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'divi_flash' ),
			'type'             => 'range',
			'option_category'  => 'font_option',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'icon',
			'allowed_units'    => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'default'          => '16px',
			'default_unit'     => 'px',
			'default_on_front' => '',
			'range_settings'   => [
				'min'  => '1',
				'max'  => '120',
				'step' => '1',
			],
			'mobile_options'   => true,
			'depends_show_if'  => 'on',
			'responsive'       => true,
			'sticky'           => true,
			'hover'            => 'tabs',
		];
		$fields['icon_position']      = [
			'label'           => esc_html__( 'Icon Position', 'divi_flash' ),
			'type'            => 'select',
			'option_category' => 'configuration',
			'options'         => [
				'row'            => esc_html__( 'Left', 'divi_flash' ),
				'row-reverse'    => esc_html__( 'Right', 'divi_flash' ),
				'column'         => esc_html__( 'Top', 'divi_flash' ),
				'column-reverse' => esc_html__( 'Bottom', 'divi_flash' ),
			],
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'icon',
			'description'     => esc_html__( 'Here you can choose Social Share Item content direction.', 'divi_flash' ),
		];
		$fields['icon_alignment']     = [
			'label'           => esc_html__( 'Icon Alignment', 'divi_flash' ),
			'type'            => 'select',
			'option_category' => 'configuration',
			'options'         => [
				'flex-start' => esc_html__( 'Top / Left', 'divi_flash' ),
				'center'     => esc_html__( 'Center', 'divi_flash' ),
				'flex-end'   => esc_html__( 'Bottom / Right', 'divi_flash' ),
			],
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'icon',
			'description'     => esc_html__( 'Here you can choose Social Share Item content direction.', 'divi_flash' ),
		];

		$fields['content_alignment']                = [
			'label'            => esc_html__( 'Item Alignment (Single Row)', 'divi_flash' ),
			'description'      => esc_html__( 'In Auto Column view, when you have single rows, you can align your items to the left, right, or center inside the container.', 'divi_flash' ),
			'type'             => 'text_align',
			'option_category'  => 'configuration',
			'options'          => et_builder_get_text_orientation_options( [ 'justified' ] ),
			'default'          => 'left',
			'default_on_front' => 'left',
			'show_if'          => [
				'column_view' => 'auto'
			],
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'alignment',
			'mobile_options'   => true,
		];
		$fields['column_auto_child_item_alignment'] = [
			'label'           => esc_html__( 'Item Alignment (Multiple Row)', 'divi_flash' ),
			'description'     => esc_html__( 'In Auto Column view, when you have multiple rows, you can align your items to the left, right, or center.', 'divi_flash' ),
			'type'            => 'multiple_buttons',
			'options'         => [
				'start'  => [
					'title' => esc_html__( 'Left', 'divi_flash' ),
					'icon'  => 'text-left', // Any svg icon that is defined on ETBuilderIcon component
				],
				'center' => [
					'title' => esc_html__( 'Center', 'divi_flash' ),
					'icon'  => 'text-center', // Any svg icon that is defined on ETBuilderIcon component
				],
				'end'    => [
					'title' => esc_html__( 'Right', 'divi_flash' ),
					'icon'  => 'text-right', // Any svg icon that is defined on ETBuilderIcon component
				],
			],
			'toggleable'      => true,
			'multi_selection' => false,
			'show_if'         => [
				'column_view' => 'auto'
			],
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'alignment',
			'mobile_options'  => true,
		];
		$fields['child_content_alignment']          = [
			'label'           => esc_html__( 'Item Content Alignment', 'divi_flash' ),
			'description'     => esc_html__( 'Align the contents of each item to the left, right, or center.', 'divi_flash' ),
			'type'            => 'multiple_buttons',
			'options'         => [
				'start'  => [
					'title' => esc_html__( 'Left', 'divi_flash' ),
					'icon'  => 'text-left', // Any svg icon that is defined on ETBuilderIcon component
				],
				'center' => [
					'title' => esc_html__( 'Center', 'divi_flash' ),
					'icon'  => 'text-center', // Any svg icon that is defined on ETBuilderIcon component
				],
				'end'    => [
					'title' => esc_html__( 'Right', 'divi_flash' ),
					'icon'  => 'text-right', // Any svg icon that is defined on ETBuilderIcon component
				],
			],
			'toggleable'      => true,
			'multi_selection' => false,
			'show_if_not'     => [
				'column_view' => 'auto'
			],
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'alignment',
			'mobile_options'  => true,
		];
		// Header
		$fields['enable_header']             = [
			'label'            => esc_html__( 'Add Header', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'option_category'  => 'configuration',
			'options'          => [
				'off' => esc_html__( 'No', 'divi_flash' ),
				'on'  => esc_html__( 'Yes', 'divi_flash' ),
			],
			'toggle_slug'      => 'settings',
			'description'      => esc_html__( '', 'divi_flash' ),
			'default_on_front' => 'off',
			'default'          => 'off',
			'affects'          => [
				'header_title',
				'header_sub_title',
				'header_icon',
				'use_header_icon_font_size',
				'header_icon_position',
				'header_icon_alignment',
				'header_icon_color'
			],
		];
		$fields['header_title']              = [
			'label'           => esc_html__( 'Title Text', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'depends_show_if' => 'on',
			'description'     => esc_html__( 'This defines the Header Title text.', 'divi_flash' ),
			'tab_slug'        => 'general',
			'toggle_slug'     => 'header',
			'dynamic_content' => 'text',
		];
		$fields['header_sub_title']          = [
			'label'           => esc_html__( 'Sub Title Text', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'depends_show_if' => 'on',
			'description'     => esc_html__( 'This defines the Header Sub Title text.', 'divi_flash' ),
			'tab_slug'        => 'general',
			'toggle_slug'     => 'header',
			'dynamic_content' => 'text',
		];
		$fields['header_icon']               = [
			'label'           => esc_html__( 'Icon', 'divi_flash' ),
			'description'     => esc_html__( 'Choose an icon to display with your blurb.', 'divi_flash' ),
			'type'            => 'select_icon',
			'option_category' => 'basic_option',
			'depends_show_if' => 'on',
			'class'           => [ 'et-pb-font-icon' ],
			'toggle_slug'     => 'header',
		];
		$fields['header_icon_color']         = [
			'label'           => esc_html__( 'Icon Color', 'divi_flash' ),
			'description'     => esc_html__( 'Here you can define a custom color for the social network icon.', 'divi_flash' ),
			'type'            => 'color-alpha',
			'custom_color'    => true,
			'depends_show_if' => 'on',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'header',
			'sub_toggle'      => 'icon',
		];
		$fields['use_header_icon_font_size'] = [
			'label'            => esc_html__( 'Use Custom Icon Size', 'divi_flash' ),
			'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'off' => et_builder_i18n( 'No' ),
				'on'  => et_builder_i18n( 'Yes' ),
			],
			'default_on_front' => 'off',
			'affects'          => [
				'header_icon_font_size',
			],
			'depends_show_if'  => 'on',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'header',
			'sub_toggle'       => 'icon',
			'option_category'  => 'font_option',
		];
		$fields['header_icon_font_size']     = [
			'label'            => esc_html__( 'Icon Font Size', 'divi_flash' ),
			'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'divi_flash' ),
			'type'             => 'range',
			'option_category'  => 'font_option',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'header',
			'sub_toggle'       => 'icon',
			'allowed_units'    => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'default'          => '16px',
			'default_unit'     => 'px',
			'default_on_front' => '',
			'range_settings'   => [
				'min'  => '1',
				'max'  => '120',
				'step' => '1',
			],
			'mobile_options'   => true,
			'depends_show_if'  => 'on',
			'responsive'       => true,
		];
		$fields['header_icon_position']      = [
			'label'            => esc_html__( 'Icon Position', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'configuration',
			'depends_show_if'  => 'on',
			'options'          => [
				'row'            => esc_html__( 'Left', 'divi_flash' ),
				'row-reverse'    => esc_html__( 'Right', 'divi_flash' ),
				'column'         => esc_html__( 'Top', 'divi_flash' ),
				'column-reverse' => esc_html__( 'Bottom', 'divi_flash' ),
			],
			'default'          => 'row',
			'default_on_front' => 'row',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'header',
			'sub_toggle'       => 'icon',
			'description'      => esc_html__( 'Here you can choose Social Share Item content direction.', 'divi_flash' ),
		];
		$fields['header_icon_alignment']     = [
			'label'            => esc_html__( 'Icon Alignment', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'configuration',
			'depends_show_if'  => 'on',
			'options'          => [
				'flex-start' => esc_html__( 'Top / Left', 'divi_flash' ),
				'center'     => esc_html__( 'Center', 'divi_flash' ),
				'flex-end'   => esc_html__( 'Bottom / Right', 'divi_flash' ),
			],
			'default'          => 'center',
			'default_on_front' => 'center',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'header',
			'sub_toggle'       => 'icon',
			'description'      => esc_html__( 'Here you can choose Social Share Item content direction.', 'divi_flash' ),
		];
		$fields['header_content_gap']        = [
			'label'            => esc_html__( 'Item Gap', 'divi_flash' ),
			'description'      => esc_html__( 'Control the gap of the Social Share header item by increasing or decreasing the gap size.', 'divi_flash' ),
			'type'             => 'range',
			'option_category'  => 'font_option',
			'allowed_units'    => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'default'          => '5px',
			'default_unit'     => 'px',
			'default_on_front' => '5px',
			'range_settings'   => [
				'min'  => '1',
				'max'  => '120',
				'step' => '1',
			],
			'mobile_options'   => false,
			'responsive'       => false,
			'sticky'           => false,
			'hover'            => false,
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'header_container',
		];
		$fields['header_alignment']          = [
			'label'           => esc_html__( 'Alignment', 'divi_flash' ),
			'description'     => esc_html__( 'Align your Header to the left, right or center of the module.', 'divi_flash' ),
			'type'            => 'multiple_buttons',
			'options'         => [
				'flex-start' => [
					'title' => esc_html__( 'Left', 'divi_flash' ),
					'icon'  => 'text-left', // Any svg icon that is defined on ETBuilderIcon component
				],
				'center'     => [
					'title' => esc_html__( 'Center', 'divi_flash' ),
					'icon'  => 'text-center', // Any svg icon that is defined on ETBuilderIcon component
				],
				'flex-end'   => [
					'title' => esc_html__( 'Right', 'divi_flash' ),
					'icon'  => 'text-right', // Any svg icon that is defined on ETBuilderIcon component
				],
			],
			'toggleable'      => true,
			'multi_selection' => false,
			'mobile_options'  => false,
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'header_container',
		];

		// Hover Animation
//		$fields['hover_animation'] = [
//			'label'           => esc_html__( 'Hover Animation', 'divi_flash' ),
//			'type'            => 'select',
//			'default'         => 'dfss-none',
//			'option_category' => 'configuration',
//			'options'         => [
//				'dfss-none'                   => esc_html__( 'None', 'divi_flash' ),
//				'dfss-grow'                   => esc_html__( 'Grow', 'divi_flash' ),
//				'dfss-grow-rotate'            => esc_html__( 'Grow Rotate', 'divi_flash' ),
//				'dfss-shrink'                 => esc_html__( 'Shrink', 'divi_flash' ),
//				'dfss-pulse'                  => esc_html__( 'Pulse', 'divi_flash' ),
//				'dfss-pulse-grow'             => esc_html__( 'Pulse Grow', 'divi_flash' ),
//				'dfss-pulse-shrink'           => esc_html__( 'Pulse Shrink', 'divi_flash' ),
//				'dfss-push'                   => esc_html__( 'Push', 'divi_flash' ),
//				'dfss-pop'                    => esc_html__( 'Pop', 'divi_flash' ),
//				'dfss-bounce-in'              => esc_html__( 'Bounce In', 'divi_flash' ),
//				'dfss-bounce-out'             => esc_html__( 'Bounce Out', 'divi_flash' ),
//				'dfss-rotate'                 => esc_html__( 'Rotate', 'divi_flash' ),
//				'dfss-float'                  => esc_html__( 'Float', 'divi_flash' ),
//				'dfss-sink'                   => esc_html__( 'Sink', 'divi_flash' ),
//				'dfss-bob'                    => esc_html__( 'Bob', 'divi_flash' ),
//				'dfss-hang'                   => esc_html__( 'Hang', 'divi_flash' ),
//				'dfss-skew'                   => esc_html__( 'Skew', 'divi_flash' ),
//				'dfss-skew-forward'           => esc_html__( 'Skew Forward', 'divi_flash' ),
//				'dfss-skew-backward'          => esc_html__( 'Skew Backward', 'divi_flash' ),
//				'dfss-wobble-vertical'        => esc_html__( 'Wobble Vertical', 'divi_flash' ),
//				'dfss-wobble-horizontal'      => esc_html__( 'Wobble Horizontal', 'divi_flash' ),
//				'dfss-wobble-to-bottom-right' => esc_html__( 'Wobble to Bottom Right', 'divi_flash' ),
//				'dfss-wobble-to-top-right'    => esc_html__( 'Wobble to Top Right', 'divi_flash' ),
//				'dfss-wobble-top'             => esc_html__( 'Wobble Top', 'divi_flash' ),
//				'dfss-wobble-bottom'          => esc_html__( 'Wobble Bottom', 'divi_flash' ),
//				'dfss-wobble-skew'            => esc_html__( 'Wobble Skew', 'divi_flash' ),
//				'dfss-buzz'                   => esc_html__( 'Buzz', 'divi_flash' ),
//				'dfss-buzz-out'               => esc_html__( 'Buzz Out', 'divi_flash' ),
//				'dfss-forward'                => esc_html__( 'Forward', 'divi_flash' ),
//				'dfss-backward'               => esc_html__( 'Backward', 'divi_flash' ),
//			],
//			'tab_slug'        => 'general',
//			'toggle_slug'     => 'settings',
//		];

		// Tooltip
		$fields['field_tooltip_enable']               = [
			'label'       => esc_html__( 'Tooltip', 'divi_flash' ),
			'description' => esc_html__( 'Tooltip On/ Off.', 'divi_flash' ),
			'type'        => 'yes_no_button',
			'options'     => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'     => 'off',
			'toggle_slug' => 'content_tooltip'
		];
		$fields['field_tooltip_arrow']                = [
			'label'       => esc_html__( 'Arrow', 'divi_flash' ),
			'type'        => 'yes_no_button',
			'options'     => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'     => 'on',
			'toggle_slug' => 'content_tooltip',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_disable_on_mobile']    = [
			'label'            => esc_html__( 'Disable on Mobile', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'          => 'on',
			'default_in_front' => 'on',
			'toggle_slug'      => 'content_tooltip',
			'show_if'          => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_placement']            = [
			'label'       => esc_html__( 'Placement', 'divi_flash' ),
			'type'        => 'select',
			'options'     => [
				'top'          => esc_html__( 'Top', 'divi_flash' ),
				'top-start'    => esc_html__( 'Top Start', 'divi_flash' ),
				'top-end'      => esc_html__( 'Top End', 'divi_flash' ),
				'right'        => esc_html__( 'Right', 'divi_flash' ),
				'right-start'  => esc_html__( 'Right Start', 'divi_flash' ),
				'right-end'    => esc_html__( 'Right End', 'divi_flash' ),
				'bottom'       => esc_html__( 'Bottom', 'divi_flash' ),
				'bottom-start' => esc_html__( 'Bottom Start', 'divi_flash' ),
				'bottom-end'   => esc_html__( 'Bottom End', 'divi_flash' ),
				'left'         => esc_html__( 'Left', 'divi_flash' ),
				'left-start'   => esc_html__( 'Left Start', 'divi_flash' ),
				'left-end'     => esc_html__( 'Left End', 'divi_flash' )
			],
			'default'     => 'top',
			'toggle_slug' => 'content_tooltip',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_animation']            = [
			'label'       => esc_html__( 'Animation', 'divi_flash' ),
			'type'        => 'select',
			'options'     => [
				'fade'         => esc_html__( 'Fade', 'divi_flash' ),
				'scale'        => esc_html__( 'Scale', 'divi_flash' ),
				'rotate'       => esc_html__( 'Rotate', 'divi_flash' ),
				'shift-away'   => esc_html__( 'Shift-away', 'divi_flash' ),
				'shift-toward' => esc_html__( 'Shift-toward', 'divi_flash' ),
				'perspective'  => esc_html__( 'Perspective', 'divi_flash' )
			],
			'default'     => 'fade',
			'toggle_slug' => 'content_tooltip',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_trigger']              = [
			'label'       => esc_html__( 'Trigger', 'divi_flash' ),
			'type'        => 'select',
			'options'     => [
				'mouseenter focus' => esc_html__( 'Hover', 'divi_flash' ),
				'click'            => esc_html__( 'Click', 'divi_flash' ),
				'mouseenter click' => esc_html__( 'Hover And Click', 'divi_flash' )
			],
			'default'     => 'mouseenter focus',
			'toggle_slug' => 'content_tooltip',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_interactive']          = [
			'label'       => esc_html__( 'Hover Over Tooltip', 'divi_flash' ),
			'description' => esc_html__( 'Tooltip allowing you to hover over and click inside it.', 'divi_flash' ),
			'type'        => 'yes_no_button',
			'options'     => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'     => 'on',
			'toggle_slug' => 'content_tooltip',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			]
		];
		$fields['field_tooltip_interactive_border']   = [
			'label'          => esc_html__( 'Tooltip Hover Area', 'divi_flash' ),
			'description'    => esc_html__( 'Determines the size of the invisible border around the tooltip that will prevent it from hiding if the cursor left it.', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'content_tooltip',
			'default'        => 2,
			'allowed_units'  => [],
			'validate_unit'  => false,
			'range_settings' => [
				'min'  => '0',
				'max'  => '100',
				'step' => '1'
			],
			'show_if'        => [
				'field_tooltip_interactive' => 'on',
				'field_tooltip_enable'      => 'on'
			],
		];
		$fields['field_tooltip_content_delay']        = [
			'label'          => esc_html__( 'Tooltip Content Delay [ms]', 'divi_flash' ),
			'description'    => esc_html__( 'Determines the time in ms to show the Tooltip content when triggger.', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'content_tooltip',
			'default'        => 300,
			'allowed_units'  => [],
			'validate_unit'  => false,
			'range_settings' => [
				'min'       => 0,
				'step'      => 100,
				'min_limit' => 0,
			],
			'show_if'        => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_interactive_debounce'] = [
			'label'          => esc_html__( 'Tooltip Content Hide Delay', 'divi_flash' ),
			'description'    => esc_html__( 'Determines the time in ms to debounce the Tooltip content hide handler when the cursor leaves.', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'content_tooltip',
			'default'        => 0,
			'allowed_units'  => [],
			'validate_unit'  => false,
			'range_settings' => [
				'min'  => '0',
				'max'  => '1000',
				'step' => '10'
			],
			'show_if'        => [
				'field_tooltip_interactive' => 'on',
				'field_tooltip_enable'      => 'on'
			],
		];
		$fields['field_tooltip_follow_cursor']        = [
			'label'       => esc_html__( 'Follow Cursor', 'divi_flash' ),
			'description' => esc_html__( 'Tooltip move with mouse courser.', 'divi_flash' ),
			'type'        => 'yes_no_button',
			'options'     => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'     => 'off',
			'toggle_slug' => 'content_tooltip',
			'show_if'     => [
				'field_tooltip_enable'  => 'on',
				'field_tooltip_trigger' => 'mouseenter focus'
			],
		];
		$fields['field_tooltip_custom_maxwidth']      = [
			'label'          => esc_html__( 'Max Width', 'divi_flash' ),
			'description'    => esc_html__( 'Specifies the maximum width of the tooltip. Useful to prevent it from being too horizontally wide to read.', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'content_tooltip',
			'default'        => 350,
			'allowed_units'  => [],
			'validate_unit'  => false,
			'range_settings' => [
				'min'  => '0',
				'max'  => '1000',
				'step' => '1'
			],
			'show_if'        => [
				'field_tooltip_enable' => 'on'
			]
		];
		$fields['field_tooltip_offset_enable']        = [
			'label'       => esc_html__( 'Tooltip Distance', 'divi_flash' ),
			'description' => esc_html__( 'Displaces the tippy from its reference element in pixels (skidding and distance).', 'divi_flash' ),
			'type'        => 'yes_no_button',
			'options'     => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'     => 'off',
			'toggle_slug' => 'content_tooltip',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_offset_skidding']      = [
			'label'          => esc_html__( 'Tooltip Horizontal Position', 'divi_flash' ),
			'description'    => esc_html__( 'Tooltip horizontal distance length from element to tooltip.', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'content_tooltip',
			'default'        => 0,
			'allowed_units'  => [],
			'validate_unit'  => false,
			'range_settings' => [
				'min'  => '0',
				'max'  => '1000',
				'step' => '1'
			],
			'show_if'        => [
				'field_tooltip_offset_enable' => 'on',
				'field_tooltip_enable'        => 'on'
			],
		];
		$fields['field_tooltip_offset_distance']      = [
			'label'          => esc_html__( 'Tooltip Vertical Position', 'divi_flash' ),
			'description'    => esc_html__( 'Tooltip vertical distance length from spot to tooltip', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'content_tooltip',
			'default'        => 10,
			'allowed_units'  => [],
			'validate_unit'  => false,
			'range_settings' => [
				'min'  => '0',
				'max'  => '100',
				'step' => '1',
			],
			'show_if'        => [
				'field_tooltip_offset_enable' => 'on',
				'field_tooltip_enable'        => 'on'
			],
		];
		$fields['field_tooltip_arrow_color']          = [
			'label'       => esc_html__( 'Tooltip Arrow Color', 'divi_flash' ),
			'type'        => 'color-alpha',
			'toggle_slug' => 'design_tooltip',
			'tab_slug'    => 'advanced',
			'hover'       => 'tabs',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields                                       = array_merge(
			$fields,
			$this->df_add_bg_field( [
				'label'       => 'Background',
				'key'         => 'field_tooltip_background',
				'toggle_slug' => 'design_tooltip',
				'tab_slug'    => 'advanced',
				'image'       => false,
				'hover'       => 'tabs',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			] ),
			$this->add_margin_padding( [
				'title'       => 'Tooltips',
				'key'         => 'tooltips',
				'toggle_slug' => 'margin_padding',
				'tab_slug'    => 'advanced',
				'option'      => 'padding',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			] )
		);

		$fields = array_merge(
			$fields,
			$this->add_margin_padding( [
				'title'       => 'Icon/Image Container',
				'key'         => 'icon_container',
				'toggle_slug' => 'margin_padding',
			] ),
			$this->add_margin_padding( [
				'title'           => 'Label Container',
				'key'             => 'label_container',
				'default_padding' => '0px|10px|0px|5px|false|false',
				'toggle_slug'     => 'margin_padding',
			] ),
			$this->add_margin_padding( [
				'title'           => 'Header Container',
				'key'             => 'header_container',
				'default_margin'  => '0px|0px|10px|0px|false|false',
				'default_padding' => '0px|10px|0px|0px|false|false',
				'toggle_slug'     => 'margin_padding',
			] ),
			$this->add_margin_padding( [
				'title'           => 'Header Text Container',
				'key'             => 'header_text_container',
				'default_padding' => '0px|0px|0px|0px|false|false',
				'toggle_slug'     => 'margin_padding',
			] ),
			$this->add_margin_padding( [
				'title'           => 'Share Button',
				'key'             => 'share_button',
				'default_padding' => '5px|5px|5px|5px|false|false',
				'toggle_slug'     => 'margin_padding',
			] ),
			$this->df_add_bg_field(
				[
					'label'       => 'Icon Background',
					'key'         => 'icon_bg_color',
					'toggle_slug' => 'icon',
					'tab_slug'    => 'advanced'
				]
			),
			$this->df_add_bg_field(
				[
					'label'       => 'Background',
					'key'         => 'text_container_bg_color',
					'toggle_slug' => 'label_container',
					'tab_slug'    => 'advanced'
				]
			),
			$this->df_add_bg_field(
				[
					'label'       => 'Background',
					'key'         => 'header_container_bg_color',
					'toggle_slug' => 'header_container',
					'tab_slug'    => 'advanced'
				]
			)
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['columns_gap']['column-gap'] = '%%order_class%% #difl-social-share-container.difl_social_share_container';
		$fields['rows_gap']['row-gap']       = '%%order_class%% #difl-social-share-container.difl_social_share_container';

		$fields['icon_color']['color'] = '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper:before';
		$fields['icon_font_size']      = [
			'font-size'   => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper:before',
			'line-height' => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper:before',
			'height'      => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper:before',
			'width'       => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper:before',
			'height'      => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper',
			'width'       => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper',
		];

		// Icon Background
		$fields = $this->df_background_transition(
			[
				'fields'   => $fields,
				'key'      => 'icon_bg_color',
				'selector' => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon"
			]
		);

		// Text Container Background
		$fields = $this->df_background_transition(
			[
				'fields'   => $fields,
				'key'      => 'text_container_bg_color',
				'selector' => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content"
			]
		);

		/*------ Spacing ------*/
		// Icon
		$fields['icon_container_margin']['margin']   = "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon";
		$fields['icon_container_padding']['padding'] = "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon";
		// Media
		$fields['label_container_margin']['margin']   = "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content_container";
		$fields['label_container_padding']['padding'] = "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content";
		// Header Container
		$fields['header_container_margin']['margin']   = "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container";
		$fields['header_container_padding']['padding'] = "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container";
		// Header Text Container
		$fields['header_text_container_margin']['margin']   = "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container .difl_social_share_header_content";
		$fields['header_text_container_padding']['padding'] = "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container .difl_social_share_header_content";
		// Share Button
		$fields['share_button_margin']['margin']   = "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper";
		$fields['share_button_padding']['padding'] = "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper";

		/*------ Tooltip -------*/
		$tooltips                              = "{$this->main_css_element} .tippy-box";
		$tooltips_arrow                        = "{$this->main_css_element} .tippy-arrow:before";
		$fields['tooltips_padding']['padding'] = $tooltips;
		$fields                                = $this->df_background_transition( [
			'fields'   => $fields,
			'key'      => 'field_tooltip_background',
			'selector' => $tooltips
		] );
		// Color
		$fields['field_tooltip_arrow_color']['color']          = $tooltips_arrow;
		// border fix
		$fields = $this->df_fix_border_transition(
			$fields,
			'tooltips_border',
			$tooltips
		);
		//box-shadow Fix
		$fields = $this->df_fix_box_shadow_transition(
			$fields,
			'tooltips_box_shadow',
			$tooltips
		);

		return $fields;
	}

	public function additional_css_styles( $render_slug ) {
		// Column View
		$column_view                   = $this->props['column_view'];
		$column_view_tablet            = ! empty( $this->props['column_view_tablet'] ) ? $this->props['column_view_tablet'] : $column_view;
		$column_view_phone             = ! empty( $this->props['column_view_phone'] ) ? $this->props['column_view_phone'] : $column_view_tablet;
		$column_view_last_edited       = $this->props['column_view_last_edited'];
		$column_view_responsive_active = et_pb_get_responsive_status( $column_view_last_edited );
		$column_template_ref           = [
			'auto'  => 'gap: 10px; display: inline-flex; flex-wrap: wrap;',
			'one'   => 'display: grid; grid-template-columns: repeat(1, 1fr);',
			'two'   => 'display: grid; grid-template-columns: repeat(2, 1fr);',
			'three' => 'display: grid; grid-template-columns: repeat(3, 1fr);',
			'four'  => 'display: grid; grid-template-columns: repeat(4, 1fr);',
			'five'  => 'display: grid; grid-template-columns: repeat(5, 1fr);',
			'six'   => 'display: grid; grid-template-columns: repeat(6, 1fr);',
		];
		ET_Builder_Element::set_style(
			$render_slug,
			[
				'selector'    => '%%order_class%% #difl-social-share-container.difl_social_share_container',
				'declaration' => $column_template_ref[ $column_view ],
			]
		);

		// responsive column layout work
		if ( $column_view_responsive_active ) {
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => '%%order_class%% #difl-social-share-container.difl_social_share_container',
				'declaration' => $column_template_ref[ $column_view_tablet ],
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => '%%order_class%% #difl-social-share-container.difl_social_share_container',
				'declaration' => $column_template_ref[ $column_view_phone ],
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			] );
		}

		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'columns_gap',
			'type'        => 'column-gap',
			'selector'    => '%%order_class%% #difl-social-share-container.difl_social_share_container',
			'default'     => '10'
		] );
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'rows_gap',
			'type'        => 'row-gap',
			'selector'    => '%%order_class%% #difl-social-share-container.difl_social_share_container',
			'default'     => '10'
		] );

		$icon_position = ! empty( $this->props['icon_position'] ) ? $this->props['icon_position'] : '';
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper",
			'declaration' => sprintf( 'flex-direction: %1$s;', esc_attr($icon_position) )
		] );
		$icon_alignment = ! empty( $this->props['icon_alignment'] ) ? $this->props['icon_alignment'] : '';
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon",
			'declaration' => sprintf( 'align-self: %1$s;', esc_attr($icon_alignment) )
		] );

		// Icon Background Color
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_bg_color',
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon",
				'hover'       => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover .difl_social_share_icon",
			]
		);

		// Text Container Background Color
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_container_bg_color',
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content",
				'hover'       => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover .difl_social_share_content",
			]
		);

		// Content Alignment
		$content_alignment        = isset( $this->props['content_alignment'] ) ? $this->props['content_alignment'] : 'left';
		$content_alignment_tablet = isset( $this->props['content_alignment_tablet'] ) ? $this->props['content_alignment_tablet'] : $content_alignment;
		$content_alignment_phone  = isset( $this->props['content_alignment_phone'] ) ? $this->props['content_alignment_phone'] : $content_alignment_tablet;
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% > div:first-of-type",
			'declaration' => sprintf( 'text-align: %1$s;', esc_attr($content_alignment) ),
		] );
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% > div:first-of-type",
			'declaration' => sprintf( 'text-align: %1$s;', esc_attr($content_alignment_tablet) ),
			'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
		] );
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% > div:first-of-type",
			'declaration' => sprintf( 'text-align: %1$s;', esc_attr($content_alignment_phone) ),
			'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
		] );

		// Column Auto Content Alignment
		if ( 'auto' === $this->props['column_view'] ) {
			$column_auto_child_item_alignment        = isset( $this->props['column_auto_child_item_alignment'] ) ? $this->props['column_auto_child_item_alignment'] : 'center';
			$column_auto_child_item_alignment_tablet = isset( $this->props['column_auto_child_item_alignment_tablet'] ) ? $this->props['column_auto_child_item_alignment_tablet'] : $column_auto_child_item_alignment;
			$column_auto_child_item_alignment_phone  = isset( $this->props['column_auto_child_item_alignment_phone'] ) ? $this->props['column_auto_child_item_alignment_phone'] : $column_auto_child_item_alignment_tablet;
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "%%order_class%% .difl_social_share_container",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($column_auto_child_item_alignment) ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "%%order_class%% .difl_social_share_container",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($column_auto_child_item_alignment_tablet) ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "%%order_class%% .difl_social_share_container",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($column_auto_child_item_alignment_phone) ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			] );
		}

		// Child Content Alignment
		$child_content_alignment        = isset( $this->props['child_content_alignment'] ) ? $this->props['child_content_alignment'] : 'center';
		$child_content_alignment_tablet = isset( $this->props['child_content_alignment_tablet'] ) ? $this->props['child_content_alignment_tablet'] : $child_content_alignment;
		$child_content_alignment_phone  = isset( $this->props['child_content_alignment_phone'] ) ? $this->props['child_content_alignment_phone'] : $child_content_alignment_tablet;
		$css_key                        = 'justify-content';
		if ( in_array( $this->props['icon_position'], [ 'column', 'column-reverse' ] ) ) {
			$css_key = 'align-items';
		}
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper",
			'declaration' => sprintf( '%1$s: %2$s;', esc_attr($css_key), esc_attr($child_content_alignment) ),
		] );
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper",
			'declaration' => sprintf( '%1$s: %2$s;', esc_attr($css_key), esc_attr($child_content_alignment_tablet) ),
			'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
		] );
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper",
			'declaration' => sprintf( '%1$s: %2$s;', esc_attr($css_key), esc_attr($child_content_alignment_phone) ),
			'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
		] );

		// Button Height
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'button_height',
			'type'        => 'height',
			'selector'    => '%%order_class%% #difl-social-share-container.difl_social_share_container .difl_social_share_item_wrapper',
		] );
		if ( in_array( $this->props['icon_position'], [
				'column',
				'column-reverse'
			] ) && ! empty( $this->prop['button_height'] ) ) {
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper",
				'declaration' => 'justify-content: center;',
			] );
		}


		// Header
		$header_icon_position = ! empty( $this->props['header_icon_position'] ) ? $this->props['header_icon_position'] : 'row';
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container",
			'declaration' => sprintf( 'flex-direction: %1$s;', esc_attr($header_icon_position) )
		] );
		$header_icon_alignment = ! empty( $this->props['header_icon_alignment'] ) ? $this->props['header_icon_alignment'] : 'center';
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container .difl_social_share_header_icon",
			'declaration' => sprintf( 'align-self: %1$s;', esc_attr($header_icon_alignment) )
		] );
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'header_container_bg_color',
				'selector'    => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container",
				'hover'       => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container:hover",
			]
		);
		// Header Item Gap
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'header_content_gap',
			'type'        => 'gap',
			'selector'    => '%%order_class%% #difl-social-share-header-container.difl_social_share_header_container',
			'default'     => '5px'
		] );
		// Header Alignment
		$header_alignment = isset( $this->props['header_alignment'] ) ? $this->props['header_alignment'] : 'flex-start';
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "%%order_class%% .difl_social_share_header",
			'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($header_alignment) ),
		] );

		/*------ Spacing ------*/
		// Icon
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_container_margin',
				'type'        => 'margin',
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon",
				'hover'       => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover .difl_social_share_icon",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_container_padding',
				'type'        => 'padding',
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_icon",
				'hover'       => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover .difl_social_share_icon",
				'important'   => false
			]
		);
		// Label
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'label_container_margin',
				'type'        => 'margin',
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content_container",
				'hover'       => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover .difl_social_share_content_container",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'label_container_padding',
				'type'        => 'padding',
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper .difl_social_share_content",
				'hover'       => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover .difl_social_share_content",
				'important'   => false
			]
		);
		// Header Container
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'header_container_margin',
				'type'        => 'margin',
				'selector'    => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container",
				'hover'       => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container:hover",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'header_container_padding',
				'type'        => 'padding',
				'selector'    => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container",
				'hover'       => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container:hover",
				'important'   => false
			]
		);
		// Header Text Container
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'header_text_container_margin',
				'type'        => 'margin',
				'selector'    => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container .difl_social_share_header_content",
				'hover'       => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container:hover .difl_social_share_header_content",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'header_text_container_padding',
				'type'        => 'padding',
				'selector'    => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container .difl_social_share_header_content",
				'hover'       => "%%order_class%% #difl-social-share-header-container.difl_social_share_header_container:hover .difl_social_share_header_content",
				'important'   => false
			]
		);
		// Share Button
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'share_button_margin',
				'type'        => 'margin',
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper",
				'hover'       => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'share_button_padding',
				'type'        => 'padding',
				'selector'    => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper",
				'hover'       => "%%order_class%% #difl-social-share-container .difl_social_share_item_wrapper:hover",
				'important'   => false
			]
		);

		/*------ Tooltip -------*/
		$this->set_margin_padding_styles( [
			'render_slug' => $render_slug,
			'slug'        => 'tooltips_padding',
			'type'        => 'padding',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element']",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element']:hover"
		] );
		$this->df_process_bg( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_background',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element']",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element']:hover"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-top-color',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='top'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='top']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-bottom-color',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='bottom'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='bottom']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-left-color',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='left'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='left']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-right-color',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='right'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='right']:hover > .tippy-arrow::before"
		] );

		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'] .tippy-content p",
			'declaration' => 'padding-bottom: 0px;'
		] );
	}

	function before_render() {
		global $difl_ss_helper_data;

		$url_new_window = $this->props['url_new_window'];
//		$show_label      = $this->props['show_label'];
		$item_view       = $this->props['item_view'];
//		$hover_animation = $this->props['hover_animation'];

		$difl_ss_helper_data = [
			'url_new_window'  => $url_new_window,
//			'show_label'      => $show_label,
			'item_view'       => $item_view,
			'hover_animation' => '',
		];

	}

	public function render( $attrs, $content, $render_slug ) {
		global $difl_ss_helper_data;
		wp_enqueue_script('df_social_share');
		if ( 'on' === $this->props['field_tooltip_enable'] ) {
			wp_enqueue_script( 'image-hotspot-popper-script' );
			wp_enqueue_script( 'image-hotspot-tippy-bundle-script' );
		}
		$this->handle_fa_icon();
		$this->additional_css_styles( $render_slug );

		$icon_container_padding        = $this->props['icon_container_padding'];
		$icon_container_padding_tablet = $this->props['icon_container_padding_tablet'];
		$icon_container_padding_phone  = $this->props['icon_container_padding_phone'];

		if ( '' !== $icon_container_padding || '' !== $icon_container_padding_tablet || '' !== $icon_container_padding_phone ) {
			$el_style = array(
				'selector'    => '%%order_class%% #difl-social-share-container.difl_social_share_container a.difl_social_share_item_wrapper .difl_social_share_icon',
				'declaration' => 'width: auto; height: auto;',
			);
			ET_Builder_Element::set_style( $render_slug, $el_style );
		}

		$use_icon_font_size = $this->props['use_icon_font_size'];

		// Icon Color.
		$this->generate_styles(
			[
				'base_attr_name'                  => 'icon_color',
				'selector'                        => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper .difl_social_share_icon i:before',
				'hover_selector'                  => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper:hover .difl_social_share_icon i:before',
				'selector_wrapper'                => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper .difl_social_share_icon',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			]
		);

		// Icon Size.
		if ( 'off' !== $use_icon_font_size ) {
			// Calculate icon size + its wrapper dimension.
			$this->generate_styles(
				[
					'base_attr_name'                  => 'icon_font_size',
					'selector'                        => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper .difl_social_share_icon img',
					'selector_wrapper'                => '%%order_class%% #difl-social-share-container a.difl_social_share_item_wrapper .difl_social_share_icon ',
					'hover_pseudo_selector_location'  => 'suffix',
					'sticky_pseudo_selector_location' => 'prefix',
					'render_slug'                     => $render_slug,
					'type'                            => 'range',
					'css_property'                    => 'right',

					// processed attr value can't be directly assigned to single css property so
					// custom processor is needed to render this attr.
					'processor'                       => [
						'ET_Builder_Module_Helper_Style_Processor',
						'process_social_media_icon_font_size',
					],
				]
			);
		}

		$header_output = '';
		if ( 'on' === $this->props['enable_header'] ) {
			$header_title     = ! empty( $this->props['header_title'] ) ? $this->props['header_title'] : '';
			$header_sub_title = ! empty( $this->props['header_sub_title'] ) ? $this->props['header_sub_title'] : '';
			$header_content   = "";
			if ( ! empty( $header_title ) || ! empty( $header_title ) ) {
				$header_content = sprintf(
					'<div class="difl_social_share_header_content">%1$s%2$s</div>',
					! empty( $header_title ) ? sprintf( '<span class="difl_social_share_header_title">%1$s</span>', esc_html($header_title) ) : '',
					! empty( $header_sub_title ) ? sprintf( '<span class="difl_social_share_header_sub_title">%1$s</span>', esc_html($header_sub_title) ) : ''
				);
			}
			$header_icon = '';
			if ( ! empty( $this->props['header_icon'] ) ) {
				// Font Icon Style.
				$this->generate_styles(
					[
						'utility_arg'    => 'icon_font_family',
						'render_slug'    => $render_slug,
						'base_attr_name' => 'header_icon',
						'important'      => true,
						'selector'       => "%%order_class%% #difl-social-share-header #difl-social-share-header-container .difl_social_share_header_icon",
						'hover_selector' => "%%order_class%% #difl-social-share-header-container:hover .difl_social_share_header_icon",
						'processor'      => [
							'ET_Builder_Module_Helper_Style_Processor',
							'process_extended_icon',
						],
					]
				);

				// Icon Color.
				$this->generate_styles(
					[
						'base_attr_name'                  => 'header_icon_color',
						'selector'                        => '%%order_class%% #difl-social-share-header #difl-social-share-header-container .difl_social_share_header_icon',
						'selector_wrapper'                => '%%order_class%% #difl-social-share-header-container .difl_social_share_header_icon',
						'hover_pseudo_selector_location'  => 'suffix',
						'sticky_pseudo_selector_location' => 'prefix',
						'css_property'                    => 'color',
						'render_slug'                     => $render_slug,
						'type'                            => 'color',
					]
				);

				// Icon Size.
				if ( 'off' !== $this->props['use_header_icon_font_size'] ) {
					// Calculate icon size + its wrapper dimension.
					$this->generate_styles(
						[
							'base_attr_name'                  => 'header_icon_font_size',
							'selector'                        => '%%order_class%% #difl-social-share-header #difl-social-share-header-container .difl_social_share_header_icon',
							'selector_wrapper'                => '%%order_class%% #difl-social-share-header-container .difl_social_share_header_icon',
							'hover_pseudo_selector_location'  => 'suffix',
							'sticky_pseudo_selector_location' => 'prefix',
							'render_slug'                     => $render_slug,
							'type'                            => 'range',
							'css_property'                    => 'right',

							// processed attr value can't be directly assigned to single css property so
							// custom processor is needed to render this attr.
							'processor'                       => [
								'ET_Builder_Module_Helper_Style_Processor',
								'process_social_media_icon_font_size',
							],
						]
					);
				}

				$header_icon = sprintf( '<span class="et-pb-icon difl_social_share_header_icon">%1$s</span>', esc_attr( et_pb_process_font_icon( $this->props['header_icon'] ) ) );
			}

			$header_output = sprintf( '<div id="difl-social-share-header" class="difl_social_share_header"><div id="difl-social-share-header-container" class="difl_social_share_header_container">%1$s%2$s</div></div>', $header_icon, $header_content );
		}

		// Tooltip
		$data_settings = $this->process_tooltip_data( $this->props );

		add_filter( 'et_global_assets_list', [ $this, 'difl_load_required_divi_assets' ], 10, 3 );
		add_filter( 'et_late_global_assets_list', [ $this, 'difl_load_required_divi_assets' ], 10, 3 );

		return sprintf( '%2$s<div id="difl-social-share-container" class="difl_social_share_container" data-settings=\'%3$s\'>%1$s</div>', $this->content, $header_output, wp_json_encode( $data_settings ) );
	}

	public function process_tooltip_data( $props ) {
		$data_settings = [
			'tooltip_enable'      => 'on' === $props['field_tooltip_enable'],
			'arrow'               => 'on' === $props['field_tooltip_arrow'],
			'disable_on_mobile'   => 'on' === $props['field_tooltip_disable_on_mobile'],
			'interactive'         => 'on' === $props['field_tooltip_interactive'],
			'interactiveBorder'   => 'on' === $props['field_tooltip_interactive'] && isset( $props['field_tooltip_interactive_border'] ) ? $props['field_tooltip_interactive_border'] : 2,
			'interactiveDebounce' => 'on' === $props['field_tooltip_interactive'] && isset( $props['field_tooltip_interactive_debounce'] ) ? $props['field_tooltip_interactive_debounce'] : 0,
			'animation'           => isset( $props['field_tooltip_animation'] ) ? $props['field_tooltip_animation'] : 'fade',
			'placement'           => isset( $props['field_tooltip_placement'] ) ? $props['field_tooltip_placement'] : 'top',
			'trigger'             => isset( $props['field_tooltip_trigger'] ) ? $props['field_tooltip_trigger'] : 'focus',
			'followCursor'        => 'on' === $props['field_tooltip_follow_cursor'],
			'maxWidth'            => isset( $props['field_tooltip_custom_maxwidth'] ) ? $props['field_tooltip_custom_maxwidth'] : 350,
			'offsetEnable'        => 'on' === $props['field_tooltip_offset_enable'],
			'offsetSkidding'      => 'on' === $props['field_tooltip_offset_enable'] && isset( $props['field_tooltip_offset_skidding'] ) ? $props['field_tooltip_offset_skidding'] : 0,
			'offsetDistance'      => 'on' === $props['field_tooltip_offset_enable'] && isset( $props['field_tooltip_offset_distance'] ) ? $props['field_tooltip_offset_distance'] : 10,
			'delay' => 'on' === $props['field_tooltip_enable'] && isset($props['field_tooltip_content_delay']) ? $props['field_tooltip_content_delay'] : 300
		];

		return $data_settings;
	}

	public function difl_load_required_divi_assets( $assets_list, $assets_args, $instance ) {
		$assets_prefix  = et_get_dynamic_assets_path();
		$all_shortcodes = $instance->get_saved_page_shortcodes();

		if ( ! isset( $assets_list['et_icons_all'] ) ) {
			$assets_list['et_icons_all'] = [
				'css' => "{$assets_prefix}/css/icons_all.css",
			];
		}

		if ( ! isset( $assets_list['et_icons_fa'] ) ) {
			$assets_list['et_icons_fa'] = [
				'css' => "{$assets_prefix}/css/icons_fa_all.css",
			];
		}

		return $assets_list;
	}
}

new SocialShare;