<?php

class DIFL_AdvancedButton extends ET_Builder_Module {
	use \DIFL\Handler\Fa_Icon_Handler;
	use DF_UTLS;

	public $icon_path;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	function init() {
		$this->name             = esc_html__( 'Advanced Button', 'divi_flash' );
		$this->plural           = esc_html__( 'Advanced Buttons', 'divi_flash' );
		$this->slug             = 'difl_advanced_button';
		$this->icon_path        = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/advanced-button.svg';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%% .difl_advanced_button_container';
	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'main_content'          => esc_html__( 'Contents', 'divi_flash' ),
					'main_settings'         => esc_html__( 'Settings', 'divi_flash' ),
					'main_animations'       => esc_html__( 'Animations', 'divi_flash' ),
					'main_hover'            => esc_html__( 'Hover', 'divi_flash' ),
					'main_tooltip'          => esc_html__( 'Tooltip', 'divi_flash' ),
					'main_tooltip_settings' => esc_html__( 'Tooltip Settings', 'divi_flash' ),
					'main_link'             => esc_html__( 'Link', 'divi_flash' ),
				]
			],
			'advanced' => [
				'toggles' => [
					'alignment'             => esc_html__( 'Alignment', 'divi_flash' ),
					'design_text'           => esc_html__( 'Text', 'divi_flash' ),
					'design_sub_text'       => esc_html__( 'Sub Text', 'divi_flash' ),
					'design_media'          => esc_html__( 'Media', 'divi_flash' ),
					'design_content'        => esc_html__( 'Content Container', 'divi_flash' ),
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

		$advanced_fields['height']                = [
			'css' => [
				'main'      => "{$this->main_css_element}",
				'important' => 'all',
			],
		];
		$advanced_fields['max_width']             = [
			'use_module_alignment' => false,
			'css'                  => [
				'main'             => "{$this->main_css_element}",
				'module_alignment' => "{$this->main_css_element} > div:first-of-type",
				'important'        => 'all',
			],
		];
		$advanced_fields['background']            = [
			'use_background_image'          => true,
			'use_background_color_gradient' => true,
			'use_background_video'          => false,
			'use_background_pattern'        => false,
			'use_background_mask'           => false,
			'css'                           => [
				'main'      => "{$this->main_css_element}",
				'important' => 'all',
			],
		];
		$advanced_fields['margin_padding']        = [
			'css'            => [
				'main'      => "{$this->main_css_element}",
				'important' => 'all',
			],
			'custom_padding' => [
				'default' => '5px|14px|5px|14px|false|false',
			],
		];
		$advanced_fields['borders']['default']    = [
			'css'      => [
				'main' => [
					'border_radii'  => "{$this->main_css_element}",
					'border_styles' => "{$this->main_css_element}",
				],
			],
			'defaults' => [
				'border_radii'  => 'on|3px|3px|3px|3px',
				'border_styles' => [
					'width' => '2px',
					'color' => '#2ea3f2',
					'style' => 'solid'
				]
			],
		];
		$advanced_fields['box_shadow']['default'] = [
			'css' => [
				'main'    => "{$this->main_css_element}",
				'overlay' => 'inset',
			],
		];

		$advanced_fields['fonts']['button_text']     = [
			'label'           => esc_html__( '', 'divi_flash' ),
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_text',
			'line_height'     => [
				'default' => '1.7em',
			],
			'font_size'       => [
				'default' => '20px',
			],
			'css'             => [
				'main'      => "
				{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text,
				{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text_hover
				",
				'hover'     => "
				{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text,
				{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text_hover
				",
				'important' => 'all',
			],
		];
		$advanced_fields['fonts']['button_sub_text'] = [
			'label'       => esc_html__( '', 'divi_flash' ),
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_sub_text',
			'line_height' => [
				'default' => '1em',
			],
			'font_size'   => [
				'default' => '16px',
			],
			'css'         => [
				'main'      => "{$this->main_css_element} .difl_adv_btn_sub_text, {$this->main_css_element} .difl_adv_btn_sub_text_hover",
				'hover'     => "{$this->main_css_element}:hover .difl_adv_btn_sub_text, {$this->main_css_element}:hover .difl_adv_btn_sub_text_hover",
				'important' => 'all',
			],
		];
		$advanced_fields['text']                     = false;
		$advanced_fields['link_options']             = false;

		$advanced_fields['borders']['media']         = [
			'css'         => [
				'main' => [
					'border_radii'  => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
					'border_styles' => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
				],
			],
			'defaults'    => [
				'border_radii'  => 'on|0px|0px|0px|0px',
				'border_styles' => [
					'width' => '0px',
					'color' => '#2ea3f2',
					'style' => 'solid'
				]
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_media',
			'show_if'     => [
				'use_button_icon' => 'on',
			],
		];
		$advanced_fields['box_shadow']['media']      = [
			'css'         => [
				'main'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
				'overlay' => 'inset',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_media',
		];
		$advanced_fields['image_icon']['image_icon'] = [
			'margin_padding'  => [
				'css'            => [
					'main' => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
					'hover' => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
					'important' => 'all',
				],
				'custom_padding' => [
					'default' => '0px|0px|0px|0px|false|false',
				],
			],
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_media',
			'label'           => esc_html__( '', 'divi_flash' ),
			'css'             => [
				'padding' => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
				'margin'  => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
				'main'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
				'hover'   => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
			],
		];

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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] a",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover a",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] ul li",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover ul li",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] ol li",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover ol li",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] h1",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover h1",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] h2",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover h2",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] h3",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover h3",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] h4",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover h4",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] h5",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover h5",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] h6",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover h6",
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%']",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover",
				'important' => 'all'
			]
		];
		$advanced_fields['borders']['tooltips_border']        = [
			'css'         => [
				'main' => [
					'border_radii'        => ".tippy-box[data-theme~='%%order_class%%']",
					'border_styles'       => ".tippy-box[data-theme~='%%order_class%%']",
					'border_styles_hover' => ".tippy-box[data-theme~='%%order_class%%']:hover"
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
				'main'      => ".tippy-box[data-theme~='%%order_class%%'] blockquote",
				'hover'     => ".tippy-box[data-theme~='%%order_class%%']:hover blockquote",
				'important' => 'all'
			]
		];
		$advanced_fields['box_shadow']['tooltips_box_shadow'] = [
			'css'         => [
				'main'  => ".tippy-box[data-theme~='%%order_class%%']",
				'hover' => ".tippy-box[data-theme~='%%order_class%%']:hover"
			],
			'toggle_slug' => 'design_tooltip',
			'tab_slug'    => 'advanced'
		];

		return $advanced_fields;
	}

	public function get_custom_css_fields_config() {
		return [
			'text' => [
				'label'    => esc_html__( 'Text', 'divi_flash' ),
				'selector' => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text"
			],
			'sub_text' => [
				'label'    => esc_html__( 'Sub Text', 'divi_flash' ),
				'selector' => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_sub_text"
			],
			'media' => [
				'label'    => esc_html__( 'Media Contaner', 'divi_flash' ),
				'selector' => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper"
			],
		];
	}

	public function get_fields() {
		$fields = [];

		$fields['button_text']     = [
			'label'           => esc_html__( 'Text', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Input your desired button text.', 'divi_flash' ),
			'toggle_slug'     => 'main_content',
			'dynamic_content' => 'text',
			'mobile_options'  => false,
			'hover'           => 'tabs',
		];
		$fields['button_sub_text'] = [
			'label'           => esc_html__( 'Sub Text', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Input your desired button sub text.', 'divi_flash' ),
			'toggle_slug'     => 'main_content',
			'dynamic_content' => 'text',
			'mobile_options'  => false,
			'hover'           => 'tabs',
		];
		$fields['use_button_icon'] = [
			'label'            => esc_html__( 'Use Button Icon', 'divi_flash' ),
			'description'      => esc_html__( '', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'          => 'on',
			'default_on_front' => 'on',
			'toggle_slug'      => 'main_content',
			'affects'          => [
				'button_icon',
				'icon_placement',
				'icon_color',
				'button_icon_size',
			],
		];
		$fields['button_icon']     = [
			'label'           => esc_html__( 'Icon', 'divi_flash' ),
			'description'     => esc_html__( 'Choose an icon to display with your Advanced Button.', 'divi_flash' ),
			'type'            => 'select_icon',
			'option_category' => 'basic_option',
			'default'         => '&#x35;||divi',
			'class'           => [ 'et-pb-font-icon' ],
			'toggle_slug'     => 'main_content',
			'mobile_options'  => false,
			'hover'           => 'tabs',
			'depends_show_if' => 'on',
		];
		$fields['button_image']    = [
			'label'              => et_builder_i18n( 'Image' ),
			'type'               => 'upload',
			'option_category'    => 'basic_option',
			'upload_button_text' => et_builder_i18n( 'Upload an image' ),
			'choose_text'        => esc_attr__( 'Choose an Image', 'divi_flash' ),
			'update_text'        => esc_attr__( 'Set As Image', 'divi_flash' ),
			'hide_metadata'      => true,
			'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'divi_flash' ),
			'default'            => '',
			'toggle_slug'        => 'main_content',
			'dynamic_content'    => 'image',
			'mobile_options'     => false,
			'hover'              => 'tabs',
			'show_if'            => [
				'use_button_icon' => 'off'
			],
		];

		$fields['button_link_type']      = [
			'label'       => esc_html__( 'Button Link Type', 'divi_flash' ),
			'type'        => 'select',
			'default'     => 'link_url',
			'options'     => [
				'link_url' => esc_html__( 'Link URL', 'divi_flash' ),
				'download' => esc_html__( 'Download', 'divi_flash' ),
				'email'    => esc_html__( 'Email', 'divi_flash' ),
				'phone'    => esc_html__( 'Phone', 'divi_flash' ),
			],
			'tab_slug'    => 'general',
			'toggle_slug' => 'main_link',
		];
		$fields['button_link_url']       = [
			'label'           => esc_html__( 'Button Link URL', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Input the destination URL for your button.', 'divi_flash' ),
			'toggle_slug'     => 'main_link',
			'dynamic_content' => 'url',
			'show_if'         => [
				'button_link_type' => 'link_url'
			],
		];
		$fields['button_link_download']  = [
			'label'           => esc_html__( 'Button Link Download URL', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Input the destination URL for your button to download.', 'divi_flash' ),
			'toggle_slug'     => 'main_link',
			'dynamic_content' => 'url',
			'show_if'         => [
				'button_link_type' => 'download'
			],
		];
		$fields['button_link_email']     = [
			'label'           => esc_html__( 'Button Link Email', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Input the destination URL for your button.', 'divi_flash' ),
			'toggle_slug'     => 'main_link',
			'dynamic_content' => 'url',
			'show_if'         => [
				'button_link_type' => 'email'
			],
		];
		$fields['button_link_phone']     = [
			'label'           => esc_html__( 'Button Link Contact No.', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Input the destination Contact No. for your button action.', 'divi_flash' ),
			'toggle_slug'     => 'main_link',
			'dynamic_content' => 'url',
			'show_if'         => [
				'button_link_type' => 'phone'
			],
		];
		$fields['button_url_new_window'] = [
			'label'            => esc_html__( 'Button Link Target', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'configuration',
			'options'          => array(
				'off' => esc_html__( 'In The Same Window', 'divi_flash' ),
				'on'  => esc_html__( 'In The New Tab', 'divi_flash' ),
			),
			'toggle_slug'      => 'main_link',
			'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'divi_flash' ),
			'default_on_front' => 'off',
			'show_if'          => [
				'button_link_type' => 'link_url'
			],
		];

		$fields['media_placement']          = [
			'label'            => esc_html__( 'Media Placement', 'divi_flash' ),
			'type'             => 'select',
			'options'          => [
				'media_left'  => esc_html__( 'Left', 'divi_flash' ),
				'media_right' => esc_html__( 'Right', 'divi_flash' ),
			],
			'default'          => 'media_right',
			'default_on_front' => 'media_right',
			'toggle_slug'      => 'main_settings',
			'mobile_options'   => false,
			'hover'            => 'tabs',
			'depends_show_if'  => 'on',
		];
		$fields['sub_text_placement']       = [
			'label'            => esc_html__( 'Sub Text Outside', 'divi_flash' ),
			'description'      => esc_html__( '', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'          => 'off',
			'default_on_front' => 'off',
			'toggle_slug'      => 'main_settings',
		];
		$fields['icon_color']               = [
			'default'         => '#2ea3f2',
			'label'           => esc_html__( 'Icon Color', 'divi_flash' ),
			'type'            => 'color-alpha',
			'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'divi_flash' ),
			'depends_show_if' => 'on',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_media',
			'hover'           => 'tabs',
			'mobile_options'  => true,
			'sticky'          => true,
		];
		$fields['button_icon_size']         = [
			'label'           => esc_html__( 'Icon Size', 'divi_flash' ),
			'default'         => '20px',
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Icon width.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_media',
			'validate_unit'   => true,
			'allowed_units'   => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'responsive'      => true,
			'mobile_options'  => true,
			'sticky'          => true,
			'hover'           => 'tabs',
			'depends_show_if' => 'on',
		];
		$fields['media_background_color']   = [
			'label'          => esc_html__( 'Background Color', 'divi_flash' ),
			'type'           => 'color-alpha',
			'description'    => esc_html__( 'Here you can define a custom background color for your icon.', 'divi_flash' ),
			'tab_slug'       => 'advanced',
			'toggle_slug'    => 'design_media',
			'hover'          => 'tabs',
			'mobile_options' => true,
			'sticky'         => true,
		];
		$fields['button_alignment']         = [
			'label'           => esc_html__( 'Button Alignment', 'divi_flash' ),
			'description'     => esc_html__( 'Align your button to the left, right or center of the module.', 'divi_flash' ),
			'type'            => 'text_align',
			'option_category' => 'configuration',
			'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'alignment',
			'mobile_options'  => true,
		];
		$fields['button_content_alignment'] = [
			'label'           => esc_html__( 'Button Content Alignment', 'divi_flash' ),
			'description'     => esc_html__( 'Align your content to the left, right or center of the Button.', 'divi_flash' ),
			'type'            => 'text_align',
			'option_category' => 'configuration',
			'options'         => et_builder_get_text_orientation_options(),
			'default'         => 'center',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'alignment',
			'mobile_options'  => true,
		];

		$fields['media_wrapper_width']  = [
			'label'           => esc_html__( 'Wrapper Width', 'divi_flash' ),
			'default'         => '30px',
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Media Wrapper width.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_media',
			'validate_unit'   => true,
			'allowed_units'   => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'responsive'      => true,
			'mobile_options'  => true,
			'sticky'          => true,
			'hover'           => 'tabs',
		];
		$fields['media_wrapper_height'] = [
			'label'           => esc_html__( 'Wrapper Height', 'divi_flash' ),
			'default'         => '30px',
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Media Wrapper height.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_media',
			'validate_unit'   => true,
			'allowed_units'   => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'responsive'      => true,
			'mobile_options'  => true,
			'sticky'          => true,
			'hover'           => 'tabs',
		];

		$content_margin         = $this->add_margin_padding( [
			'title'       => 'Content Container',
			'key'         => 'content_container',
			'toggle_slug' => 'margin_padding',
		] );
		$text_margin_padding    = $this->add_margin_padding( [
			'title'       => 'Text',
			'key'         => 'text',
			'toggle_slug' => 'margin_padding',
		] );
		$subtext_margin_padding = $this->add_margin_padding( [
			'title'       => 'Sub Text',
			'key'         => 'sub_text',
			'toggle_slug' => 'margin_padding',
		] );

		$twoD_effects                        = [];
		$twoD_effects['two_d_hover_effects'] = [
			'label'       => esc_html__( 'Select 2D Effect', 'divi_flash' ),
			'type'        => 'select',
			'default'     => 'dfab_none',
			'options'     => [
				'dfab_none'       => esc_html__( 'Select Effect', 'divi_flash' ),
				'dfab_bounce'     => esc_html__( 'Bounce', 'divi_flash' ),
				'dfab_flash'      => esc_html__( 'Flash', 'divi_flash' ),
				'dfab_pulse'      => esc_html__( 'Pulse', 'divi_flash' ),
				'dfab_rubberBand' => esc_html__( 'Rubber Band', 'divi_flash' ),
				'dfab_headShake'  => esc_html__( 'Head Shake', 'divi_flash' ),
				'dfab_swing'      => esc_html__( 'Swing', 'divi_flash' ),
				'dfab_tada'       => esc_html__( 'Tada', 'divi_flash' ),
				'dfab_wobble'     => esc_html__( 'Wobble', 'divi_flash' ),
				'dfab_jello'      => esc_html__( 'Jello', 'divi_flash' ),
				'dfab_heartBeat'  => esc_html__( 'Heart Beat', 'divi_flash' ),
			],
			'tab_slug'    => 'general',
			'toggle_slug' => 'main_hover',
		];

		// Background
		$backgroundEffects                                        = [];
		$backgroundEffects['bg_hover_effects']                    = [
			'label'       => esc_html__( 'Select Hover Background Effect', 'divi_flash' ),
			'type'        => 'select',
			'default'     => 'dfab_none',
			'options'     => [
				'dfab_none'                  => esc_html__( 'Select Effect', 'divi_flash' ),
				'dfab_reveal'                => esc_html__( 'Reveal', 'divi_flash' ),
				'dfab_ripple'                => esc_html__( 'Ripple', 'divi_flash' ),
				'dfab_ripple_position_aware' => esc_html__( 'Ripple Position Aware', 'divi_flash' ),
				'dfab_ripple_two_dot'        => esc_html__( 'Ripple Two Dot', 'divi_flash' ),
				'dfab_door_open'             => esc_html__( 'Door Open', 'divi_flash' ),
				'dfab_skew'                  => esc_html__( 'Skew', 'divi_flash' ),
				'dfab_two_shade'             => esc_html__( 'Two Shade', 'divi_flash' ),
			],
			'tab_slug'    => 'general',
			'toggle_slug' => 'main_hover',
		];
		$backgroundEffects['bg_hover_effect_directions']          = [
			'label'       => esc_html__( 'Select Hover Background Effect Direction', 'divi_flash' ),
			'type'        => 'select',
			'default'     => 'dfab_left',
			'options'     => [
				'dfab_left'   => esc_html__( 'From Left', 'divi_flash' ),
				'dfab_right'  => esc_html__( 'From Right', 'divi_flash' ),
				'dfab_top'    => esc_html__( 'From Top', 'divi_flash' ),
				'dfab_bottom' => esc_html__( 'From Bottom', 'divi_flash' ),
			],
			'tab_slug'    => 'general',
			'toggle_slug' => 'main_hover',
			'show_if'     => [
				'bg_hover_effects' => [ 'dfab_reveal', 'dfab_two_shade' ]
			],
			'show_if_not' => [
				'bg_hover_effects' => [ 'dfab_none', 'dfab_ripple' ]
			]

		];
		$backgroundEffects['bg_hover_skew_effect_directions']     = [
			'label'       => esc_html__( 'Select Hover Background Effect Direction', 'divi_flash' ),
			'type'        => 'select',
			'default'     => 'dfab_top_left',
			'options'     => [
				'dfab_top_left'     => esc_html__( 'From Top Left', 'divi_flash' ),
				'dfab_top_right'    => esc_html__( 'From Top Right', 'divi_flash' ),
				'dfab_bottom_left'  => esc_html__( 'From Bottom Left', 'divi_flash' ),
				'dfab_bottom_right' => esc_html__( 'From Bottom Right', 'divi_flash' ),
			],
			'tab_slug'    => 'general',
			'toggle_slug' => 'main_hover',
			'show_if'     => [
				'bg_hover_effects' => [ 'dfab_skew' ]
			],

		];
		$backgroundEffects['bg_hover_effect_hyper']               = [
			'label'            => esc_html__( 'Add Hypen', 'divi_flash' ),
			'description'      => esc_html__( '', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'          => 'off',
			'default_on_front' => 'off',
			'toggle_slug'      => 'main_content',
			'show_if'          => [
				'bg_hover_effects' => [ 'dfab_reveal', 'dfab_ripple' ]
			],
		];
		$backgroundEffects['bg_hover_hypen_color']                = [
			'label'          => esc_html__( 'Hover Hypen Color', 'divi_flash' ),
			'type'           => 'color-alpha',
			'default'        => '#333333',
			'description'    => esc_html__( 'Here you can define a custom hover hypen color.', 'divi_flash' ),
			'tab_slug'       => 'general',
			'toggle_slug'    => 'main_hover',
			'hover'          => false,
			'mobile_options' => false,
			'show_if'        => [
				'bg_hover_effect_hyper' => 'on',
				'bg_hover_effects'      => [ 'dfab_reveal', 'dfab_ripple' ]
			],
		];
		$backgroundEffects['bg_hover_effect_bounce']              = [
			'label'            => esc_html__( 'Add Bounce Effect', 'divi_flash' ),
			'description'      => esc_html__( '', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'          => 'off',
			'default_on_front' => 'off',
			'toggle_slug'      => 'main_content',
			'show_if'          => [
				'bg_hover_effects' => [ 'dfab_reveal', 'dfab_door_open', 'dfab_skew', 'dfab_two_shade' ]
			],
		];
		$backgroundEffects['bg_hover_background_color']           = [
			'label'          => esc_html__( 'Hover Background Color', 'divi_flash' ),
			'type'           => 'color-alpha',
			'description'    => esc_html__( 'Here you can define a custom hover background color.', 'divi_flash' ),
			'tab_slug'       => 'general',
			'toggle_slug'    => 'main_hover',
			'hover'          => false,
			'mobile_options' => false,
			'show_if'        => [
				'bg_hover_effects' => [
					'dfab_reveal',
					'dfab_ripple',
					'dfab_ripple_position_aware',
					'dfab_ripple_two_dot',
					'dfab_door_open',
					'dfab_skew',
					'dfab_two_shade'
				]
			],
			'show_if_not'    => [
				'bg_hover_effects' => 'dfab_none'
			]
		];
		$backgroundEffects['bg_hover_background_secondary_color'] = [
			'label'          => esc_html__( 'Hover Background Secondary Color', 'divi_flash' ),
			'type'           => 'color-alpha',
			'description'    => esc_html__( 'Here you can define a custom background color for your icon.', 'divi_flash' ),
			'tab_slug'       => 'general',
			'toggle_slug'    => 'main_hover',
			'hover'          => false,
			'mobile_options' => false,
			'show_if'        => [
				'bg_hover_effects' => 'dfab_two_shade'
			]
		];

		// Preview Button
		$fields['preview_btn'] = [
			'label'           => esc_html__( 'Hover Effects', 'divi_flash' ),
			'type'            => 'df_ab_preview_support',
			'hover'           => 'tabs',
			'tab_slug'        => 'general',
			'toggle_slug'     => 'main_hover',
		];

		// Border
		$borderEffects                         = [];
		$borderEffects['border_hover_effects'] = [
			'label'       => esc_html__( 'Select Hover Stroke Effect', 'divi_flash' ),
			'type'        => 'select',
			'default'     => 'dfab_none',
			'options'     => [
				'dfab_none'                        => esc_html__( 'Select Effect', 'divi_flash' ),
//				'dfab_border_ripple_in'            => esc_html__( 'Ripple In', 'divi_flash' ),
//				'dfab_border_ripple_out'           => esc_html__( 'Ripple Out', 'divi_flash' ),
				'dfab_border_slide_left'           => esc_html__( 'Slide Left', 'divi_flash' ),
				'dfab_border_slide_right'          => esc_html__( 'Slide Right', 'divi_flash' ),
				'dfab_border_outline_center'       => esc_html__( 'Outline Anim From Center', 'divi_flash' ),
				'dfab_border_outline_left'         => esc_html__( 'Outline Anim From Left', 'divi_flash' ),
				'dfab_border_outline_right'        => esc_html__( 'Outline Anim From Right', 'divi_flash' ),
				'dfab_border_outline_top'          => esc_html__( 'Outline Anim From Top', 'divi_flash' ),
				'dfab_border_outline_bottom'       => esc_html__( 'Outline Anim From Bottom', 'divi_flash' ),
				'dfab_border_outline_vertical'     => esc_html__( 'Outline Anim Horizontal', 'divi_flash' ),
				'dfab_border_outline_horizontal'   => esc_html__( 'Outline Anim Vertical', 'divi_flash' ),
				'dfab_border_outline_top_left'     => esc_html__( 'Outline Anim From Top Left', 'divi_flash' ),
				'dfab_border_outline_top_right'    => esc_html__( 'Outline Anim From top Right', 'divi_flash' ),
				'dfab_border_outline_bottom_left'  => esc_html__( 'Outline Anim From Bottom Left', 'divi_flash' ),
				'dfab_border_outline_bottom_right' => esc_html__( 'Outline Anim From Bottom Right', 'divi_flash' ),
				'dfab_border_outline_1'            => esc_html__( 'Outline Anim CC 1', 'divi_flash' ),
				'dfab_border_outline_12'           => esc_html__( 'Outline Anim CC 2', 'divi_flash' ),
				'dfab_border_outline_2'            => esc_html__( 'Outline Anim CC 3', 'divi_flash' ),
				'dfab_border_outline_22'           => esc_html__( 'Outline Anim CC 4', 'divi_flash' ),
				'dfab_border_outline_3'            => esc_html__( 'Outline Anim CC 5', 'divi_flash' ),
				'dfab_border_outline_32'           => esc_html__( 'Outline Anim CC 6', 'divi_flash' ),
				'dfab_border_outline_4'            => esc_html__( 'Outline Anim Corner 1', 'divi_flash' ),
				'dfab_border_outline_42'           => esc_html__( 'Outline Anim Corner 2', 'divi_flash' ),
			],
			'tab_slug'    => 'general',
			'toggle_slug' => 'main_hover',
		];
		$borderEffects['border_hover_color']   = [
			'label'          => esc_html__( 'Hover Border Color', 'divi_flash' ),
			'type'           => 'color-alpha',
			'description'    => esc_html__( 'Here you can define a custom hover border color.', 'divi_flash' ),
			'tab_slug'       => 'general',
			'toggle_slug'    => 'main_hover',
			'hover'          => false,
			'mobile_options' => false,
			'show_if'        => [
				'border_hover_effects' => [
					'dfab_border_ripple_in',
					'dfab_border_ripple_out',
					'dfab_border_slide_left',
					'dfab_border_slide_right',
					'dfab_border_outline_center',
					'dfab_border_outline_left',
					'dfab_border_outline_right',
					'dfab_border_outline_top',
					'dfab_border_outline_bottom',
					'dfab_border_outline_vertical',
					'dfab_border_outline_horizontal',
					'dfab_border_outline_top_left',
					'dfab_border_outline_top_right',
					'dfab_border_outline_bottom_left',
					'dfab_border_outline_bottom_right',
					'dfab_border_outline_1',
					'dfab_border_outline_12',
					'dfab_border_outline_2',
					'dfab_border_outline_22',
					'dfab_border_outline_3',
					'dfab_border_outline_32',
					'dfab_border_outline_4',
					'dfab_border_outline_42'
				]
			],
			'show_if_not'    => [
				'border_hover_effects' => 'dfab_none'
			]
		];

		// Icon
		$mediaEffects                                  = [];
		$mediaEffects['media_hover_effects']           = [
			'label'       => esc_html__( 'Select Hover Media Effect', 'divi_flash' ),
			'type'        => 'select',
			'default'     => 'dfab_none',
			'options'     => [
				'dfab_none'         => esc_html__( 'Select Effect', 'divi_flash' ),
				'dfab_hover_media'  => esc_html__( 'Show on Hover', 'divi_flash' ),
				'dfab_media_reveal' => esc_html__( 'Reveal', 'divi_flash' ),
				'dfab_media_slide'  => esc_html__( 'Slide', 'divi_flash' ),
			],
			'tab_slug'    => 'general',
			'toggle_slug' => 'main_hover',
		];
		$mediaEffects['media_hover_effect_directions'] = [
			'label'       => esc_html__( 'Select Hover Effect Direction', 'divi_flash' ),
			'type'        => 'select',
			'default'     => 'dfab_mr_left',
			'options'     => [
				'dfab_mr_left'   => esc_html__( 'From Left', 'divi_flash' ),
				'dfab_mr_right'  => esc_html__( 'From Right', 'divi_flash' ),
				'dfab_mr_top'    => esc_html__( 'From Top', 'divi_flash' ),
				'dfab_mr_bottom' => esc_html__( 'From Bottom', 'divi_flash' ),
			],
			'tab_slug'    => 'general',
			'toggle_slug' => 'main_hover',
			'show_if'     => [
				'media_hover_effects' => [ 'dfab_media_reveal', 'dfab_media_slide' ]
			],
			'show_if_not' => [
				'media_hover_effects' => [ 'dfab_none' ]
			]

		];

		$fields['main_hover'] = [
			'type'                => 'composite',
			'tab_slug'            => 'general',
			'toggle_slug'         => 'main_hover',
			'composite_type'      => 'default',
			'composite_structure' => [
				'2d_hover_elements'     => [
					'type'     => 'text',
					'label'    => esc_html__( '2D', 'divi_flash' ),
					'controls' => array_merge(
						$twoD_effects,
						$this->generate_transition_fields( 'two_d', "2D", [
							'tab_slug'    => 'general',
							'toggle_slug' => 'main_hover',
							'sub_toggle'  => "",
							'exclude'     => [ 'timing_function' ],
							'show_if'     => [
								'two_d_hover_effects' => [
									'dfab_bounce',
									'dfab_flash',
									'dfab_pulse',
									'dfab_rubberBand',
									'dfab_headShake',
									'dfab_swing',
									'dfab_tada',
									'dfab_wobble',
									'dfab_jello',
									'dfab_heartBeat'
								]
							],
							'show_if_not' => [
								'two_d_hover_effects' => 'dfab_none'
							]
						] )
					),
				],
				'bg_hover_elements'     => [
					'type'     => 'text',
					'label'    => esc_html__( 'BG', 'divi_flash' ),
					'controls' => array_merge(
						$backgroundEffects,
						$this->generate_transition_fields( 'bg', "BG", [
							'tab_slug'    => 'general',
							'toggle_slug' => 'main_hover',
							'sub_toggle'  => "",
							'exclude'     => [],
							'show_if'     => [
								'bg_hover_effects' => [
									'dfab_reveal',
									'dfab_ripple',
									'dfab_ripple_position_aware',
									'dfab_ripple_two_dot',
									'dfab_door_open',
									'dfab_skew',
									'dfab_two_shade'
								]
							],
							'show_if_not' => [
								'bg_hover_effects' => 'dfab_none'
							]
						] )
					),
				],
				'stroke_hover_elements' => [
					'type'     => 'text',
					'label'    => esc_html__( 'Stroke', 'divi_flash' ),
					'controls' => array_merge(
						$borderEffects,
						$this->generate_transition_fields( 'stroke', "Stroke", [
							'tab_slug'    => 'general',
							'toggle_slug' => 'main_hover',
							'sub_toggle'  => "",
							'exclude'     => [],
							'show_if'     => [
								'border_hover_effects' => [
									'dfab_border_ripple_in',
									'dfab_border_ripple_out',
									'dfab_border_slide_left',
									'dfab_border_slide_right',
									'dfab_border_outline_center',
									'dfab_border_outline_left',
									'dfab_border_outline_right',
									'dfab_border_outline_top',
									'dfab_border_outline_bottom',
									'dfab_border_outline_vertical',
									'dfab_border_outline_horizontal',
									'dfab_border_outline_top_left',
									'dfab_border_outline_top_right',
									'dfab_border_outline_bottom_left',
									'dfab_border_outline_bottom_right',
									'dfab_border_outline_1',
									'dfab_border_outline_12',
									'dfab_border_outline_2',
									'dfab_border_outline_22',
									'dfab_border_outline_3',
									'dfab_border_outline_32',
									'dfab_border_outline_4',
									'dfab_border_outline_42'
								]
							],
							'show_if_not' => [
								'border_hover_effects' => 'dfab_none'
							]
						] )
					),
				],
				'media_hover_elements'  => [
					'type'     => 'text',
					'label'    => esc_html__( 'Media', 'divi_flash' ),
					'controls' => array_merge(
						$mediaEffects,
						$this->generate_transition_fields( 'media', "Media", [
							'tab_slug'    => 'general',
							'toggle_slug' => 'main_hover',
							'sub_toggle'  => "",
							'exclude'     => [],
							'show_if'     => [
								'media_hover_effects' => [ 'dfab_hover_media', 'dfab_media_reveal', 'dfab_media_slide' ]
							],
							'show_if_not' => [
								'media_hover_effects' => 'dfab_none'
							]
						] )
					),
				]
			],
		];

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
			'toggle_slug' => 'main_tooltip'
		];
		$fields['field_tooltip_content']              = [
			'label'           => esc_html__( 'Tooltip Content', 'divi_flash' ),
			'type'            => 'tiny_mce',
			'formate'         => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Note: Html tags, shortcode are supported and shortcode will be view only frontend ', 'divi_flash' ),
			'toggle_slug'     => 'main_tooltip',
			'dynamic_content' => 'text',
			'show_if'         => [
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
			'toggle_slug'      => 'main_tooltip_settings',
			'show_if'          => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_arrow']                = [
			'label'       => esc_html__( 'Arrow', 'divi_flash' ),
			'type'        => 'yes_no_button',
			'options'     => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'     => 'on',
			'toggle_slug' => 'main_tooltip_settings',
			'show_if'     => [
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
			'toggle_slug' => 'main_tooltip_settings',
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
			'toggle_slug' => 'main_tooltip_settings',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			],
		];
//		$fields['field_tooltip_trigger']              = [
//			'label'       => esc_html__( 'Trigger', 'divi_flash' ),
//			'type'        => 'select',
//			'options'     => [
//				'mouseenter focus' => esc_html__( 'Hover', 'divi_flash' ),
//				'click'            => esc_html__( 'Click', 'divi_flash' ),
//				'mouseenter click' => esc_html__( 'Hover And Click', 'divi_flash' )
//			],
//			'default'     => 'mouseenter focus',
//			'toggle_slug' => 'main_tooltip_settings',
//			'show_if'     => [
//				'field_tooltip_enable' => 'on'
//			],
//		];
		$fields['field_tooltip_interactive']          = [
			'label'       => esc_html__( 'Hover Over Tooltip', 'divi_flash' ),
			'description' => esc_html__( 'Tooltip allowing you to hover over and click inside it.', 'divi_flash' ),
			'type'        => 'yes_no_button',
			'options'     => [
				'on'  => esc_html__( 'On', 'divi_flash' ),
				'off' => esc_html__( 'Off', 'divi_flash' )
			],
			'default'     => 'on',
			'toggle_slug' => 'main_tooltip_settings',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			]
		];
		$fields['field_tooltip_interactive_border']   = [
			'label'          => esc_html__( 'Tooltip Hover Area', 'divi_flash' ),
			'description'    => esc_html__( 'Determines the size of the invisible border around the tooltip that will prevent it from hiding if the cursor left it.', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'main_tooltip_settings',
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
			'toggle_slug'    => 'main_tooltip_settings',
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
			'toggle_slug'    => 'main_tooltip_settings',
			'default'        => 0,
			'allowed_units'  => [],
			'validate_unit'  => false,
			'range_settings' => [
				'min'  => '0',
				'max'  => '1000',
				'step' => '10'
			],
			'show_if'        => [
				'field_tooltip_enable'      => 'on'
			],
		];
//		$fields['field_tooltip_follow_cursor']        = [
//			'label'       => esc_html__( 'Follow Cursor', 'divi_flash' ),
//			'description' => esc_html__( 'Tooltip move with mouse courser.', 'divi_flash' ),
//			'type'        => 'yes_no_button',
//			'options'     => [
//				'on'  => esc_html__( 'On', 'divi_flash' ),
//				'off' => esc_html__( 'Off', 'divi_flash' )
//			],
//			'default'     => 'off',
//			'toggle_slug' => 'main_tooltip_settings',
//			'show_if'     => [
//				'field_tooltip_enable'  => 'on',
//				'field_tooltip_trigger' => 'mouseenter focus'
//			],
//		];
		$fields['field_tooltip_custom_maxwidth']      = [
			'label'          => esc_html__( 'Max Width', 'divi_flash' ),
			'description'    => esc_html__( 'Specifies the maximum width of the tooltip. Useful to prevent it from being too horizontally wide to read.', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'main_tooltip_settings',
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
			'toggle_slug' => 'main_tooltip_settings',
			'show_if'     => [
				'field_tooltip_enable' => 'on'
			],
		];
		$fields['field_tooltip_offset_skidding']      = [
			'label'          => esc_html__( 'Tooltip Horizontal Position', 'divi_flash' ),
			'description'    => esc_html__( 'Tooltip horizontal distance length from element to tooltip.', 'divi_flash' ),
			'type'           => 'range',
			'toggle_slug'    => 'main_tooltip_settings',
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
			'toggle_slug'    => 'main_tooltip_settings',
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
		$design_tooltip                               = array_merge(
			[
				'field_tooltip_arrow_color' => [
					'label'       => esc_html__( 'Tooltip Arrow Color', 'divi_flash' ),
					'type'        => 'color-alpha',
					'toggle_slug' => 'design_tooltip',
					'tab_slug'    => 'advanced',
					'hover'       => 'tabs',
					'show_if'     => [
						'field_tooltip_enable' => 'on'
					],
				]
			],
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


		return array_merge( $fields, $content_margin, $text_margin_padding, $subtext_margin_padding, $design_tooltip );
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['icon_color']['color']                        = "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_icon, {$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_icon_hover";
		$fields['media_background_color']['background-color'] = "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper";

		// Content Container
		$fields['content_container_margin']['margin']   = "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper";
		$fields['content_container_padding']['padding'] = "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper";

		// Text
		$fields['text_margin']['margin']   = "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text, {$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text_hover";
		$fields['text_padding']['padding'] = "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text, {$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text_hover";

		// Sub Text
		$fields['sub_text_margin']['margin']   = "{$this->main_css_element} .difl_adv_btn_sub_text, {$this->main_css_element} .difl_adv_btn_sub_text_hover";
		$fields['sub_text_padding']['padding'] = "{$this->main_css_element} .difl_adv_btn_sub_text, {$this->main_css_element} .difl_adv_btn_sub_text_hover";

		/*------ Tooltip -------*/
		$tooltips                              = "%%order_class%% .tippy-box";
		$tooltips_arrow                        = "%%order_class%% .tippy-arrow:before";
		$fields['tooltips_padding']['padding'] = $tooltips;
		$fields                                = $this->df_background_transition( [
			'fields'   => $fields,
			'key'      => 'field_tooltip_background',
			'selector' => $tooltips
		] );
		// Color
		$fields['field_tooltip_arrow_color']['color'] = $tooltips_arrow;
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
		// Button Alignment
		if ( ! empty( $this->props['button_alignment'] ) ) {
			$button_alignment        = isset( $this->props['button_alignment'] ) ? $this->props['button_alignment'] : 'left';
			$button_alignment_tablet = isset( $this->props['button_alignment_tablet'] ) ? $this->props['button_alignment_tablet'] : $button_alignment;
			$button_alignment_phone  = isset( $this->props['button_alignment_phone'] ) ? $this->props['button_alignment_phone'] : $button_alignment_tablet;
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "%%order_class%%",
				'declaration' => sprintf( 'text-align: %1$s;', esc_attr($button_alignment) ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "%%order_class%%",
				'declaration' => sprintf( 'text-align: %1$s;', esc_attr($button_alignment_tablet) ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "%%order_class%%",
				'declaration' => sprintf( 'text-align: %1$s;', esc_attr($button_alignment_phone) ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			] );
		}

		// Button Content Alignment
		if ( isset( $this->props['button_content_alignment'] ) ) {
			$button_content_alignment        = isset( $this->props['button_content_alignment'] ) ? $this->props['button_content_alignment'] : 'center';
			$button_content_alignment_tablet = isset( $this->props['button_content_alignment_tablet'] ) ? $this->props['button_content_alignment_tablet'] : $button_content_alignment;
			$button_content_alignment_phone  = isset( $this->props['button_content_alignment_phone'] ) ? $this->props['button_content_alignment_phone'] : $button_content_alignment_tablet;

			// Adjust alignment if it's 'justified'
			if ( 'justified' === $button_content_alignment ) {
				$button_content_alignment = 'space-between';
			}
			if ( 'justified' === $button_content_alignment_tablet ) {
				$button_content_alignment_tablet = 'space-between';
			}
			if ( 'justified' === $button_content_alignment_phone ) {
				$button_content_alignment_phone = 'space-between';
			}

			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($button_content_alignment) ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($button_content_alignment_tablet) ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($button_content_alignment_phone) ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			] );
		}

		if ( isset( $this->props['use_button_icon'] ) && "on" === $this->props['use_button_icon'] ) {
			$this->df_process_color(
				[
					'render_slug' => $render_slug,
					'slug'        => 'icon_color',
					'type'        => 'color',
					'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_icon, {$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_icon_hover",
					'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_icon, {$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_icon_hover"
				]
			);
			$this->df_process_range( [
				'render_slug' => $render_slug,
				'slug'        => 'button_icon_size',
				'type'        => 'font-size',
				'default'     => '20px',
				'selector'    => "{$this->main_css_element} .difl_adv_btn_media_wrapper .difl_adv_btn_icon, {$this->main_css_element} .difl_adv_btn_media_wrapper .difl_adv_btn_icon_hover",
				'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_media_wrapper .difl_adv_btn_icon, {$this->main_css_element}:hover .difl_adv_btn_media_wrapper .difl_adv_btn_icon_hover",
			] );
		}
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug'        => 'media_background_color',
				'type'        => 'background-color',
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
				'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper"
			]
		);
		// Media placement
		if ( isset( $this->props['media_placement'] ) && 'media_right' === $this->props['media_placement'] ) {
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
				'declaration' => "order: 2;"
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper",
				'declaration' => "order: 1;"
			] );
		}
		// Hover Media placement
		if ( isset( $this->props['media_placement__hover'] ) ) {
			if ( 'media_right' === $this->props['media_placement__hover'] ) {
				ET_Builder_Element::set_style( $render_slug, [
					'selector'    => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
					'declaration' => "order: 2;"
				] );
				ET_Builder_Element::set_style( $render_slug, [
					'selector'    => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper",
					'declaration' => "order: 1;"
				] );
			} else {
				ET_Builder_Element::set_style( $render_slug, [
					'selector'    => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_media_wrapper",
					'declaration' => "order: 1;"
				] );
				ET_Builder_Element::set_style( $render_slug, [
					'selector'    => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper",
					'declaration' => "order: 2;"
				] );
			}
		}

		/* Effects */
		// Background
		if ( ! empty( $this->props['bg_hover_background_color'] ) && 'dfab_none' !== $this->props['bg_hover_effects'] ) {
			$this->df_process_color(
				[
					'render_slug' => $render_slug,
					'slug'        => 'bg_hover_background_color',
					'type'        => '--dfab-bg-hover-background-color',
					'selector'    => "{$this->main_css_element}"
				]
			);
		}
		// Bounce
		if ( ! empty( $this->props['bg_hover_effects'] ) && in_array( $this->props['bg_hover_effects'], [
				'dfab_reveal',
				'dfab_door_open',
				'dfab_skew',
				'dfab_two_shade'
			] ) && 'on' === $this->props['bg_hover_effect_bounce'] ) {
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element}",
				'declaration' => "--dfab-bg-hover-background-transition-timimg-function: cubic-bezier(.52,1.64,.37,.66) !important;"
			] );
		}
		// Two Shade Secondary Color
		if ( ! empty( $this->props['bg_hover_effects'] ) && 'dfab_two_shade' === $this->props['bg_hover_effects'] ) {
			$this->df_process_color(
				[
					'render_slug' => $render_slug,
					'slug'        => 'bg_hover_background_secondary_color',
					'type'        => '--dfab-bg-hover-background-secondary-color',
					'selector'    => "{$this->main_css_element}"
				]
			);
		}
		// Hypen
		if ( 'on' === $this->props['bg_hover_effect_hyper'] && in_array( $this->props['bg_hover_effects'], [
				'dfab_reveal',
				'dfab_ripple'
			] ) ) {
			$this->df_process_color(
				[
					'render_slug' => $render_slug,
					'slug'        => 'bg_hover_hypen_color',
					'type'        => '--dfab-bg-hover-hypen-color',
					'selector'    => "{$this->main_css_element}"
				]
			);
		}

		// Border Effect
		if ( ! empty( $this->props['border_hover_color'] ) && 'dfab_none' !== $this->props['border_hover_effects'] ) {
			$this->df_process_color(
				[
					'render_slug' => $render_slug,
					'slug'        => 'border_hover_color',
					'type'        => '--dfab-border-hover-background-color',
					'selector'    => "{$this->main_css_element}"
				]
			);

		}

		// 2D Effect
		/* Effects */

		/* Transition Data */
		if ( ! empty( $this->props['bg_hover_effects'] ) && 'dfab_none' !== $this->props['bg_hover_effects'] ) {
			$bg_transition_duration        = ! empty( $this->props['bg_transition_duration'] ) ? $this->props['bg_transition_duration'] : 0.5;
			$bg_transition_delay           = ! empty( $this->props['bg_transition_delay'] ) ? $this->props['bg_transition_delay'] : 0.0;
			$bg_transition_timing_function = ! empty( $this->props['bg_transition_timing_function'] ) ? $this->props['bg_transition_timing_function'] : 'ease-out';
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element}",
				'declaration' => sprintf( '--dfab-bg-hover-background-transtion-time: %1$ss;--dfab-bg-hover-background-transition-timimg-function: %2$s;--dfab-bg-hover-background-transtion-delay: %3$ss;', esc_attr($bg_transition_duration), esc_attr($bg_transition_timing_function), esc_attr($bg_transition_delay) )
			] );
		}
		if ( ! empty( $this->props['border_hover_effects'] ) && 'dfab_none' !== $this->props['border_hover_effects'] ) {
			$stroke_transition_duration        = ! empty( $this->props['stroke_transition_duration'] ) ? $this->props['stroke_transition_duration'] : 0.5;
			$stroke_transition_delay           = ! empty( $this->props['stroke_transition_delay'] ) ? $this->props['stroke_transition_delay'] : 0.0;
			$stroke_transition_timing_function = ! empty( $this->props['stroke_transition_timing_function'] ) ? $this->props['stroke_transition_timing_function'] : 'ease-out';
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element}",
				'declaration' => sprintf( '--dfab-border-hover-background-transtion-time: %1$ss;--dfab-border-hover-background-transition-timimg-function: %2$s;--dfab-border-hover-background-transtion-delay: %3$ss;', esc_attr($stroke_transition_duration), esc_attr($stroke_transition_timing_function), esc_attr($stroke_transition_delay) )
			] );
		}
		if ( ! empty( $this->props['media_hover_effects'] ) && 'dfab_none' !== $this->props['media_hover_effects'] ) {
			$media_transition_duration        = ! empty( $this->props['media_transition_duration'] ) ? $this->props['media_transition_duration'] : 0.5;
			$media_transition_delay           = ! empty( $this->props['media_transition_delay'] ) ? $this->props['media_transition_delay'] : 0.0;
			$media_transition_timing_function = ! empty( $this->props['media_transition_timing_function'] ) ? $this->props['media_transition_timing_function'] : 'ease-out';
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element}",
				'declaration' => sprintf( '--dfab-media-hover-transition-duration: %1$ss;--dfab-media-hover-transition-function: %2$s;--dfab-media-hover-transition-delay: %3$ss;', esc_attr($media_transition_duration), esc_attr($media_transition_timing_function), esc_attr($media_transition_delay) )
			] );
		}
		if ( ! empty( $this->props['two_d_hover_effects'] ) && 'dfab_none' !== $this->props['two_d_hover_effects'] ) {
			$two_d_transition_duration = ! empty( $this->props['two_d_transition_duration'] ) ? $this->props['two_d_transition_duration'] : 0.5;
			$two_d_transition_delay    = ! empty( $this->props['two_d_transition_delay'] ) ? $this->props['two_d_transition_delay'] : 0.0;
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element}",
				'declaration' => sprintf( '--dfab-two-d-animation-duration: %1$ss;--dfab-two-d-animation-delay: %2$ss;', esc_attr($two_d_transition_duration), esc_attr($two_d_transition_delay) )
			] );
		}
		/* Transition Data */

		// Media Container Width & Height
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'media_wrapper_width',
			'type'        => '--dfab-media-wrapper-width',
			'default'     => '30px',
			'selector'    => "{$this->main_css_element}",
			'hover'       => "{$this->main_css_element}:hover",
		] );
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'media_wrapper_height',
			'type'        => '--dfab-media-wrapper-height',
			'default'     => '30px',
			'selector'    => "{$this->main_css_element}",
			'hover'       => "{$this->main_css_element}:hover",
		] );

		// Content Container Margin & Padding
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'content_container_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper",
				'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'content_container_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper",
				'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper",
				'important'   => false
			]
		);

		// Text Margin & Padding
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text, {$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text_hover",
				'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text, {$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text_hover",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text, {$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text_hover",
				'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text, {$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_text_wrapper .difl_adv_btn_text_hover",
				'important'   => false
			]
		);

		// Sub Text Margin & Padding
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'sub_text_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} .difl_adv_btn_sub_text, {$this->main_css_element} .difl_adv_btn_sub_text_hover",
				'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_sub_text, {$this->main_css_element}:hover .difl_adv_btn_sub_text_hover",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'sub_text_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element}  .difl_adv_btn_sub_text, {$this->main_css_element} .difl_adv_btn_sub_text_hover",
				'hover'       => "{$this->main_css_element}:hover .difl_adv_btn_sub_text, {$this->main_css_element}:hover .difl_adv_btn_sub_text_hover",
				'important'   => false
			]
		);

		/*------ Tooltip -------*/
		$this->set_margin_padding_styles( [
			'render_slug' => $render_slug,
			'slug'        => 'tooltips_padding',
			'type'        => 'padding',
			'selector'    => ".tippy-box[data-theme~='%%order_class%%']",
			'hover'       => ".tippy-box[data-theme~='%%order_class%%']:hover"
		] );
		$this->df_process_bg( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_background',
			'selector'    => ".tippy-box[data-theme~='%%order_class%%']",
			'hover'       => ".tippy-box[data-theme~='%%order_class%%']:hover"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-top-color',
			'selector'    => ".tippy-box[data-theme~='%%order_class%%'][data-placement^='top'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='%%order_class%%'][data-placement^='top']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-bottom-color',
			'selector'    => ".tippy-box[data-theme~='%%order_class%%'][data-placement^='bottom'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='%%order_class%%'][data-placement^='bottom']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-left-color',
			'selector'    => ".tippy-box[data-theme~='%%order_class%%'][data-placement^='left'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='%%order_class%%'][data-placement^='left']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-right-color',
			'selector'    => ".tippy-box[data-theme~='%%order_class%%'][data-placement^='right'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='%%order_class%%'][data-placement^='right']:hover > .tippy-arrow::before"
		] );

		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => ".tippy-box[data-theme~='%%order_class%%'] .tippy-content p",
			'declaration' => 'padding-bottom: 0px;'
		] );
	}

	public function render( $attrs, $content, $render_slug ) {
		$this->handle_fa_icon();
		$this->additional_css_styles( $render_slug );
		if ( 'on' === $this->props['field_tooltip_enable'] ) {
			wp_enqueue_script( 'image-hotspot-popper-script' );
			wp_enqueue_script( 'image-hotspot-tippy-bundle-script' );
		}
		wp_enqueue_script( 'df_advanced_button' );

		return $this->button_markup_process( $this->props, $render_slug );
	}

	public function button_markup_process( $props, $render_slug ) {
		/*===== Effects =====*/
		$classes = '';
		// Media Placement
		$media_placement = isset( $this->props['media_placement'] ) ? $this->props['media_placement'] : 'media_left';
		if ( "" !== $media_placement ) {
			$classes .= " " . $media_placement;
		}

		// Background
		$background_hover_effect        = ! empty( $this->props['bg_hover_effects'] ) && 'dfab_none' !== $this->props['bg_hover_effects'] ? $this->props['bg_hover_effects'] : "";
		$background_hover_effect_markup = "";
		if ( "" !== $background_hover_effect ) {
			$classes                        .= " " . $background_hover_effect;
			$background_hover_effect_markup = sprintf( '<span class="difl_adv_btn_bg_anim"></span>' );
		}
		if ( "" !== $background_hover_effect && in_array( $background_hover_effect, [
				'dfab_reveal',
				'dfab_reveal_with_hypen',
				'dfab_two_shade'
			] ) ) {
			$background_hover_effect_direction = ! empty( $this->props['bg_hover_effect_directions'] ) ? $this->props['bg_hover_effect_directions'] : "dfab_left";

			$classes .= " " . $background_hover_effect_direction;
		}
		$ripple_position_aware = "";
		if ( 'dfab_ripple_position_aware' === $this->props['bg_hover_effects'] ) {
			$ripple_position_aware = "<span class='dfab_position_aware_bg'></span>";
		}
		if ( 'dfab_skew' === $this->props['bg_hover_effects'] ) {
			$bg_hover_skew_effect_directions = ! empty( $this->props['bg_hover_skew_effect_directions'] ) ? $this->props['bg_hover_skew_effect_directions'] : "dfab_top_left";

			$classes .= " " . $bg_hover_skew_effect_directions;

		}

		// Hypen
		if ( 'on' === $this->props['bg_hover_effect_hyper'] && in_array( $background_hover_effect, [
				'dfab_reveal',
				'dfab_ripple'
			] ) ) {
			$classes .= " dfab_hypen";
		}

		// Border
		$border_hover_effect        = ! empty( $this->props['border_hover_effects'] ) && 'dfab_none' !== $this->props['border_hover_effects'] ? $this->props['border_hover_effects'] : "";
		$border_hover_effect_markup = "";
		if ( "" !== $border_hover_effect ) {
			$classes                    .= " " . $border_hover_effect;
			$border_hover_effect_markup = sprintf( '<span class="difl_adv_btn_border_anim"></span><span class="difl_adv_btn_border_anim_2"></span>' );
		}

		// 2D Effects
		$two_d_hover_effects = ! empty( $this->props['two_d_hover_effects'] ) && 'dfab_none' !== $this->props['two_d_hover_effects'] ? $this->props['two_d_hover_effects'] : "";
		if ( "" !== $two_d_hover_effects ) {
			$classes .= " " . $two_d_hover_effects;
			$classes .= " dfab__animate";
		}

		// Media Effects
		$media_hover_effects = ! empty( $this->props['media_hover_effects'] ) && 'dfab_none' !== $this->props['media_hover_effects'] ? $this->props['media_hover_effects'] : "";
		if ( "" !== $media_hover_effects ) {
			$classes .= " " . $media_hover_effects;
		}
		if ( in_array( $this->props['media_hover_effects'], [ 'dfab_media_reveal', 'dfab_media_slide' ] ) ) {
			$media_hover_effect_directions = ! empty( $this->props['media_hover_effect_directions'] ) ? $this->props['media_hover_effect_directions'] : "dfab_mr_left";
			$classes                       .= " " . $media_hover_effect_directions;
		}


		/*===== Effects =====*/
		$btn_sub_text_hover = '';
		if ( isset( $props['button_sub_text__hover'] ) && ! empty( $props['button_sub_text__hover'] ) ) {
			$btn_sub_text_hover = sprintf( '<span class="difl_adv_btn_sub_text_hover">%1$s</span>', esc_html($props['button_sub_text__hover']) );
		}

		$sub_text_markup         = "";
		$sub_text_markup_outside = "";
		if ( 'on' === $props['sub_text_placement'] && ! empty( $props['button_sub_text'] ) ) {
			$sub_text_markup_outside = sprintf( '<span class="difl_adv_btn_sub_text">%1$s</span>%2$s', esc_html($props['button_sub_text']), $btn_sub_text_hover );
		} else {
			$sub_text_markup = sprintf( '<span class="difl_adv_btn_sub_text">%1$s</span> %2$s', esc_html($props['button_sub_text']), $btn_sub_text_hover );
		}

		$btn_text       = ! empty( $props['button_text'] ) ? $props['button_text'] : "Advanced Button";
		$btn_text_hover = '';
		if ( isset( $props['button_text__hover'] ) && ! empty( $props['button_text__hover'] ) ) {
			$btn_text_hover = sprintf( '<span class="difl_adv_btn_text_hover">%1$s</span>', esc_html($props['button_text__hover']) );
		}

		$button_link_type              = ! empty( $props['button_link_type'] ) ? $props['button_link_type'] : 'link_url';
		$button_link_markup            = '';
		$button_link_additional_markup = '';
		if ( 'link_url' === $button_link_type ) {
			$button_link_markup = ! empty( $props['button_link_url'] ) ? $props['button_link_url'] : '#';
			if ( isset( $props['button_url_new_window'] ) && 'on' === $props['button_url_new_window'] ) {
				$button_link_additional_markup = 'target="_blank"';
			}
		}
		if ( 'download' === $button_link_type ) {
			$button_link_markup            = ! empty( $props['button_link_download'] ) ? $props['button_link_download'] : '';
			$button_link_additional_markup = 'download';
		}
		if ( 'email' === $button_link_type ) {
			$button_link_markup = ! empty( $props['button_link_email'] ) ? $props['button_link_email'] : '';
			$button_link_markup = 'mailto:' . $button_link_markup;
		}
		if ( 'phone' === $button_link_type ) {
			$button_link_markup = ! empty( $props['button_link_phone'] ) ? $props['button_link_phone'] : '';
			$button_link_markup = 'tel:' . $button_link_markup;
		}
		if ( 'link_url' === $button_link_type || 'download' === $button_link_type ) {
			$url_scheme = wp_parse_url( $button_link_markup, PHP_URL_SCHEME );

			if ( "https" !== $url_scheme && "http" !== $url_scheme && "#" !== $button_link_markup && "#" !== $button_link_markup[0] && "/" !== $button_link_markup[0] ) {
				$button_link_markup = "https://" . $button_link_markup;
			}
		}

		/*-------- Tooltip --------*/
		$data_settings   = $this->process_tooltip_data( $props );
		$tooltip_content = ! empty( $this->props['field_tooltip_content'] ) ? $this->props['field_tooltip_content'] : '';
		if ( null !== $tooltip_content ) {
			$tooltip_content = preg_replace( "/<p[^>]*>(?:\s|&nbsp;)*<\/p>/", '', $tooltip_content );
		}

		return sprintf(
			'<a class="difl_advanced_button_container%5$s" data-settings=\'%13$s\' data-text="%7$s" href="%11$s" %12$s>
							<span class="difl_adv_btn_wrapper">
								%1$s
								<span class="difl_adv_btn_text_wrapper">
									<span class="difl_adv_btn_text">%2$s</span>
									%10$s
									%3$s
								</span>
							</span>
							%4$s
							%6$s
							%8$s
							%9$s
							<noscript class="difl_advanced_button_tooltip_content">%14$s</noscript>
						</a>',
			( 'on' === $props['use_button_icon'] ) ? $this->process_icon( $props, $render_slug ) : $this->process_media( $props ),
			et_core_sanitized_previously($btn_text),
			et_core_sanitized_previously($sub_text_markup),
			et_core_sanitized_previously($sub_text_markup_outside),
			et_core_sanitized_previously($classes),
			$ripple_position_aware,
			( 'dfab_text_vertical_cut' === $this->props['bg_hover_effects'] ) ? et_core_sanitized_previously($btn_text) : '',
			$background_hover_effect_markup,
			$border_hover_effect_markup,
			et_core_sanitized_previously($btn_text_hover),
			et_core_sanitized_previously($button_link_markup),
			et_core_sanitized_previously($button_link_additional_markup),
			wp_json_encode( $data_settings ),
			et_core_sanitized_previously( $tooltip_content )
		);
	}

	public function process_icon( $props, $render_slug ) {
		if ( empty( $props['button_icon'] ) ) {
			return "";
		}
		$font_icon = ! empty( $props['button_icon'] ) ? $props['button_icon'] : '&#xe08a;||divi||400';
		// Font Icon Style.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'button_icon',
				'important'      => true,
				'selector'       => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_icon",
				'hover_selector' => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_icon",
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);
		$font_icon_hover = '';
		if ( ! empty( $props['button_icon__hover'] ) ) {
			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $render_slug,
					'base_attr_name' => 'button_icon__hover',
					'important'      => true,
					'selector'       => "{$this->main_css_element} .difl_adv_btn_wrapper .difl_adv_btn_icon_hover",
					'hover_selector' => "{$this->main_css_element}:hover .difl_adv_btn_wrapper .difl_adv_btn_icon_hover",
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);
			$font_icon_hover = sprintf( '<span class="et-pb-icon difl_adv_btn_media difl_adv_btn_icon_hover">%1$s</span>', esc_attr( et_pb_process_font_icon( $props['button_icon__hover'] ) ) );
		}

		return sprintf( '<span class="difl_adv_btn_media_wrapper"><span class="et-pb-icon difl_adv_btn_media difl_adv_btn_icon">%1$s</span>%2$s</span>', esc_attr( et_pb_process_font_icon( $font_icon ) ), $font_icon_hover );
	}

	public function process_media( $props ) {
		if ( empty( $props['button_image'] ) ) {
			return "";
		}

		$button_image_hover = '';
		if ( ! empty( $props['button_image__hover'] ) ) {
			$button_image_hover = sprintf( '<img class="difl_adv_btn_media difl_adv_btn_img_hover" src="%1$s" alt="Advanced Button" title="" />', $props['button_image__hover'] );
		}

		return sprintf( '<span class="difl_adv_btn_media_wrapper"><img class="difl_adv_btn_media difl_adv_btn_img" src="%1$s" alt="Advanced Button" title="" />%2$s</span>', $props['button_image'], $button_image_hover );
	}

	public function generate_transition_fields( $prefix, $label_prefix, $args = [] ) {
		$fields = [];
		if ( ! in_array( 'duration', $args['exclude'] ) ) {
			$fields["{$prefix}_transition_duration"] = [
				'label'           => esc_html__( "{$label_prefix} Transition Duration [Sec]", 'divi_flash' ),
				'default'         => 0.5,
				'range_settings'  => [
					'min'       => 1,
					'max'       => 10,
					'step'      => 0.1,
					'min_limit' => 0,
				],
				'description'     => esc_html__( '', 'divi_flash' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => $args['tab_slug'],
				'toggle_slug'     => $args['toggle_slug'],
				'sub_toggle'      => $args['sub_toggle'],
				'allowed_units'   => false,
				'show_if'         => $args['show_if'],
				'show_if_not'     => $args['show_if_not']
			];
		}
		if ( ! in_array( 'delay', $args['exclude'] ) ) {
			$fields["{$prefix}_transition_delay"] = [
				'label'           => esc_html__( "{$label_prefix} Transition Delay [Sec]", 'divi_flash' ),
				'default'         => 0.0,
				'range_settings'  => [
					'min'       => 1,
					'max'       => 10,
					'step'      => 0.1,
					'min_limit' => 0,
				],
				'description'     => esc_html__( '', 'divi_flash' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => $args['tab_slug'],
				'toggle_slug'     => $args['toggle_slug'],
				'sub_toggle'      => $args['sub_toggle'],
				'allowed_units'   => false,
				'show_if'         => $args['show_if'],
				'show_if_not'     => $args['show_if_not'],
			];
		}
		if ( ! in_array( 'timing_function', $args['exclude'] ) ) {
			$fields["{$prefix}_transition_timing_function"] = [
				'label'       => esc_html__( "{$label_prefix} Transition Timing Function ", 'divi_flash' ),
				'type'        => 'select',
				'default'     => 'ease-out',
				'options'     => [
					'ease'        => esc_html__( 'Ease', 'divi_flash' ),
					'linear'      => esc_html__( 'Linear', 'divi_flash' ),
					'ease-in'     => esc_html__( 'Ease In', 'divi_flash' ),
					'ease-out'    => esc_html__( 'Ease Out', 'divi_flash' ),
					'ease-in-out' => esc_html__( 'Ease In Out', 'divi_flash' ),
				],
				'tab_slug'    => $args['tab_slug'],
				'toggle_slug' => $args['toggle_slug'],
				'sub_toggle'  => $args['sub_toggle'],
				'show_if'     => $args['show_if'],
				'show_if_not' => $args['show_if_not'],
			];
		}

		return $fields;
	}

	public function process_tooltip_data( $props ) {
		$data_settings = [
			'tooltip_enable'      => 'on' === $props['field_tooltip_enable'],
			'disable_on_mobile'   => 'on' === $props['field_tooltip_disable_on_mobile'],
			'arrow'               => 'on' === $props['field_tooltip_arrow'],
			'interactive'         => 'on' === $props['field_tooltip_interactive'],
			'interactiveBorder'   => 'on' === $props['field_tooltip_interactive'] && isset( $props['field_tooltip_interactive_border'] ) ? $props['field_tooltip_interactive_border'] : 2,
			'interactiveDebounce' => 'on' === $props['field_tooltip_interactive'] && isset( $props['field_tooltip_interactive_debounce'] ) ? $props['field_tooltip_interactive_debounce'] : 0,
			'animation'           => isset( $props['field_tooltip_animation'] ) ? $props['field_tooltip_animation'] : 'fade',
			'placement'           => isset( $props['field_tooltip_placement'] ) ? $props['field_tooltip_placement'] : 'top',
			'trigger'             => 'mouseenter focus',
			'followCursor'        => false,
			'maxWidth'            => isset( $props['field_tooltip_custom_maxwidth'] ) ? $props['field_tooltip_custom_maxwidth'] : 350,
			'offsetEnable'        => 'on' === $props['field_tooltip_offset_enable'],
			'offsetSkidding'      => 'on' === $props['field_tooltip_offset_enable'] && isset( $props['field_tooltip_offset_skidding'] ) ? $props['field_tooltip_offset_skidding'] : 0,
			'offsetDistance'      => 'on' === $props['field_tooltip_offset_enable'] && isset( $props['field_tooltip_offset_distance'] ) ? $props['field_tooltip_offset_distance'] : 10,
			'delay'               => 'on' === $props['field_tooltip_enable'] && isset( $props['field_tooltip_content_delay'] ) ? $props['field_tooltip_content_delay'] : 300
		];

		return $data_settings;
	}

}

new DIFL_AdvancedButton;
