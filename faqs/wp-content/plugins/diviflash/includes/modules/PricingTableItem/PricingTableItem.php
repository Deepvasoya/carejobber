<?php

class DIFL_PricingTableItem extends ET_Builder_Module {
	use DF_UTLS;
	use \DIFL\Handler\Fa_Icon_Handler;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	private static $processed_field = [];

	public function init() {
		$this->name                        = esc_html__( 'Advanced Pricing TableItem', 'divi_flash' );
		$this->plural                      = esc_html__( 'Advanced Pricing TableItems', 'divi_flash' );
		$this->slug                        = 'difl_pricingtableitem';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'admin_label';
		$this->child_title_fallback_var    = 'item_type';
		$this->settings_text               = esc_html__( 'Pricing TableItem Settings', 'divi_flash' );
		$this->advanced_setting_title_text = esc_html__( 'Pricing TableItem', 'divi_flash' );
	}

	public function get_settings_modal_toggles() {
		$sub_toggles_font    = [
			'p'     => [
				'name' => 'P',
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
			],
		];
		$sub_toggles_heading = [
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
		];

		return [
			'general'  => [
				'toggles' => [
					'main_content' => esc_html__( 'Element', 'divi_flash' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'text_style'              => [
						'title' => esc_html__( 'Text', 'divi_flash' ),
					],
					'dividers'                => [ 'title' => esc_html__( 'Divider', 'divi_flash' ), ],
					'heading'                 => [
						'title'             => esc_html__( 'Heading', 'divi_flash' ),
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
					'price_style'             => [ 'title' => esc_html__( 'Price', 'divi_flash' ), ],
					'feature_icon_style'      => [ 'title' => esc_html__( 'Feature Icon', 'divi_flash' ), ],
					'feature_tooltip_style'   => [ 'title' => esc_html__( 'Feature Tooltip Settings', 'divi_flash' ), ],
					'feature_tooltip_heading' => [
						'title'             => esc_html__( 'Feature Tooltip Heading', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => $sub_toggles_heading,
					],
					'feature_tooltip_font'    => [
						'title'             => esc_html__( 'Feature Tooltip Font', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => $sub_toggles_font,
					],
					'button_badge'            => [ 'title' => esc_html__( 'Button Badge', 'divi_flash' ), ],
					'button_style'            => [ 'title' => esc_html__( 'Button Style', 'divi_flash' ), ],
					'ribbon'                  => [ 'title' => esc_html__( 'Ribbon', 'divi_flash' ), ],
					'image_icon'              => [ 'title' => esc_html__( 'Image & Icon', 'divi_flash' ), ],
					'rating'                  => [
						'title' => esc_html__( 'Rating', 'divi_flash' ),
					],
					'image'                   => [
						'title' => esc_html__( 'Image Settings', 'divi_flash' ),
					],
				],
			],
		];
	}

	public function get_fields() {
		$general = [
			'item_type'   => [
				'label'       => esc_html__( 'Element Type', 'divi_flash' ),
				'type'        => 'select',
				'tab_slug'    => 'general',
				'toggle_slug' => 'main_content',
				'default'     => 'Text',
				'options'     => [
					'Text'    => __( 'Text', 'divi_flash' ),
					'Price'   => __( 'Price', 'divi_flash' ),
					'Feature' => __( 'Feature', 'divi_flash' ),
					'Icon'    => __( 'Icon', 'divi_flash' ),
					'Image'   => __( 'Image', 'divi_flash' ),
					'Ribbon'  => __( 'Ribbon', 'divi_flash' ),
					'Divider' => __( 'Divider', 'divi_flash' ),
					'Button'  => __( 'Button', 'divi_flash' ),
					'Rating'  => __( 'Rating', 'divi_flash' ),
				],
			],
			'admin_label' => [
				'label'           => esc_html__( 'Admin Label', 'divi_flash' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'admin_label',
			],

		];

		$text_fields = [
			'text_content' => [
				'label'           => esc_html__( 'Text Content', 'divi_flash' ),
				'type'            => 'tiny_mce',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'show_if'         => [ 'item_type' => 'Text' ],
			],
		];

		$price_fields            = [
			'price'                    => [
				'label'                => esc_html__( 'Regular Price', 'divi_flash' ),
				'type'                 => 'text',
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Price' ],
			],
			'price_prefix'             => [
				'label'                => esc_html__( 'Price Prefix', 'divi_flash' ),
				'type'                 => 'text',
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Price' ],
			],
			'price_prefix_placement'   => [
				'default'          => 'top',
				'default_on_front' => 'top',
				'label'            => esc_html__( 'Prefix Vertical Alignment', 'divi_flash' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'top'    => esc_html__( 'Top', 'divi_flash' ),
					'middle' => esc_html__( 'Middle', 'divi_flash' ),
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [ 'item_type' => 'Price' ],
			],
			'price_suffix'             => [
				'label'                => esc_html__( 'Price Suffix', 'divi_flash' ),
				'type'                 => 'text',
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Price' ],
			],
			'price_suffix_placement'   => [
				'default'          => 'bottom',
				'default_on_front' => 'bottom',
				'label'            => esc_html__( 'Suffix Vertical Alignment', 'divi_flash' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'top'    => esc_html__( 'Top', 'divi_flash' ),
					'middle' => esc_html__( 'Middle', 'divi_flash' ),
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [ 'item_type' => 'Price' ],
			],
			'enable_original_price'    => [
				'label'           => esc_html__( 'Enable Sale Price', 'divi_flash' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'     => 'main_content',
				'tab_slug'        => 'general',
				'show_if'         => [ 'item_type' => 'Price' ],
			],
			'price_gap'                => [
				'label'          => esc_html__( 'Gap Between Regular & Sale price', 'divi_flash' ),
				'type'           => 'range',
				'default'        => '8px',
				'default_unit'   => 'px',
				'range_settings' => [
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				],
				'mobile_options' => true,
				'responsive'     => true,
				'toggle_slug'    => 'main_content',
				'tab_slug'       => 'general',
				'show_if'        => [ 'item_type' => 'Price', 'enable_original_price' => 'on' ],
			],
			'original_price_placement' => [
				'label'            => esc_html__( 'Sale Price Placement', 'divi_flash' ),
				'type'             => 'select',
				'default'          => 'back',
				'default_on_front' => 'back',
				'option_category'  => 'configuration',
				'options'          => [
					'front' => esc_html__( 'Before Regular Price', 'divi_flash' ),
					'back'  => esc_html__( 'After Regular Price', 'divi_flash' ),
				],
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'show_if'          => [ 'item_type' => 'Price', 'enable_original_price' => 'on' ],
			],

			'original_price'                  => [
				'label'                => esc_html__( 'Sale Price', 'divi_flash' ),
				'type'                 => 'text',
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Price', 'enable_original_price' => 'on' ],
			],
			'original_price_prefix'           => [
				'label'                => esc_html__( 'Price Prefix', 'divi_flash' ),
				'type'                 => 'text',
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Price', 'enable_original_price' => 'on' ],
			],
			'original_price_prefix_placement' => [
				'label'            => esc_html__( 'Prefix Vertical Alignment', 'divi_flash' ),
				'default'          => 'top',
				'default_on_front' => 'top',
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'top'    => esc_html__( 'Top', 'divi_flash' ),
					'middle' => esc_html__( 'Middle', 'divi_flash' ),
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [ 'item_type' => 'Price', 'enable_original_price' => 'on' ],
			],
			'original_price_suffix'           => [
				'label'                => esc_html__( 'Price Suffix', 'divi_flash' ),
				'type'                 => 'text',
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Price', 'enable_original_price' => 'on' ],
			],
			'original_price_suffix_placement' => [
				'default'          => 'bottom',
				'default_on_front' => 'bottom',
				'label'            => esc_html__( 'Suffix Vertical Alignment', 'divi_flash' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'top'    => esc_html__( 'Top', 'divi_flash' ),
					'middle' => esc_html__( 'Middle', 'divi_flash' ),
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [ 'item_type' => 'Price', 'enable_original_price' => 'on' ],
			],
			'price_alignemnt'                 => [
				'label'            => esc_html__( 'Alignment', 'divi_flash' ),
				'type'             => 'align',
				'option_category'  => 'layout',
				'options'          => et_builder_get_text_orientation_options( [ 'justified' ] ),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [ 'item_type' => 'Price' ],
				'default'          => 'center',
				'default_on_front' => 'center',
			],
		];
		$feature_icon_spacing    = $this->add_margin_padding( [
			'key'         => 'feature_icon_spacing',
			'option'      => 'padding',
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_icon_style',
			'show_if'     => [ 'item_type' => 'Feature' ],
		] );
		$feature_tooltip_spacing = $this->add_margin_padding( [
			'key'         => 'feature_tooltip_spacing',
			'option'      => 'padding',
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_tooltip_style',
			'show_if'     => [ 'item_type' => 'Feature' ],
		] );
		$feature_fields          = array_merge( [
			'feature_text'                      => [
				'label'                => esc_html__( 'Feature Text', 'divi_flash' ),
				'type'                 => 'text',
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Feature' ],
			],
			'feature_text_tooltip'              => [
				'label'           => esc_html__( 'Enable Tooltip Content', 'divi_flash' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'     => 'main_content',
				'tab_slug'        => 'general',
				'show_if'         => [ 'item_type' => 'Feature' ],
			],
			'feature_text_tooltip_main_content' => [
				'label'                => esc_html__( 'Tooltip Content Area', 'divi_flash' ),
				'type'                 => 'tiny_mce',
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'text',
				'show_if'              => [
					'feature_text_tooltip' => 'on',
					'item_type'            => 'Feature',
				],
			],
			'feature_icon'                      => [
				'label'                => esc_html__( 'Feature Icon', 'divi_flash' ),
				'type'                 => 'select_icon',
				'class'                => [ 'et-pb-font-icon' ],
				'tab_slug'             => 'general',
				'toggle_slug'          => 'main_content',
				'dynamic_main_content' => 'image',
				'show_if'              => [ 'item_type' => 'Feature' ],
			],
			'price_icon_gap'                    => [
				'label'          => esc_html__( 'Gap Between Feature Text & Icon ', 'divi_flash' ),
				'type'           => 'range',
				'default'        => '8px',
				'default_unit'   => 'px',
				'range_settings' => [
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				],
				'mobile_options' => true,
				'responsive'     => true,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'main_content',
				'show_if'        => [ 'item_type' => 'Feature' ],
			],
			'feature_icon_placement'            => [
				'default'          => 'left',
				'default_on_front' => 'left',
				'label'            => esc_html__( 'Icon Placement', 'divi_flash' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'left'   => esc_html__( 'Left', 'divi_flash' ),
					'right'  => esc_html__( 'Right', 'divi_flash' ),
					'top'    => esc_html__( 'Top', 'divi_flash' ),
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [ 'item_type' => 'Feature' ],
			],
			'feature_icon_size'                 => [
				'label'          => esc_html__( 'Icon Size', 'divi_flash' ),
				'type'           => 'range',
				'default'        => '16px',
				'default_unit'   => 'px',
				'range_settings' => [
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				],
				'mobile_options' => true,
				'responsive'     => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'feature_icon_style',
				'show_if'        => [ 'item_type' => 'Feature' ],
			],
			'feature_icon_color'                => [
				'label'          => esc_html__( 'Icon Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => false,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'feature_icon_style',
				'show_if'        => [ 'item_type' => 'Feature' ],
			],
			'feature_icon_bg_color'             => [
				'label'          => esc_html__( 'Icon Background Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'feature_icon_style',
				'show_if'        => [ 'item_type' => 'Feature' ],
			],
			'feature_tooltip_bg_color'          => [
				'label'            => esc_html__( 'Tooltip Background Color', 'divi_flash' ),
				'type'             => 'color-alpha',
				'default'          => et_builder_accent_color(),
				'default_on_front' => et_builder_accent_color(),
				'mobile_options'   => true,
				'responsive'       => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'feature_tooltip_style',
				'show_if'          => [ 'item_type' => 'Feature' ],
			],
			'feature_tooltip_arrow_color'       => [
				'label'            => esc_html__( 'Tooltip Arrow Color', 'divi_flash' ),
				'default'          => et_builder_accent_color(),
				'default_on_front' => et_builder_accent_color(),
				'type'             => 'color-alpha',
				'mobile_options'   => true,
				'responsive'       => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'feature_tooltip_style',
				'show_if'          => [ 'item_type' => 'Feature' ],
			],
		], $feature_tooltip_spacing, $feature_icon_spacing );

		$icon_fields = [
			'item_icon' => [
				'label'                => esc_html__( 'Icon', 'divi_flash' ),
				'type'                 => 'select_icon',
				'class'                => [ 'et-pb-font-icon' ],
				'toggle_slug'          => 'main_content',
				'tab_slug'             => 'general',
				'dynamic_main_content' => 'image',
				'show_if'              => [ 'item_type' => 'Icon' ],
			],
		];

		$image_fields = [
			'item_image'     => [
				'label'                => esc_html__( 'Image', 'divi_flash' ),
				'type'                 => 'upload',
				'upload_button_text'   => esc_attr__( 'Upload an image', 'divi_flash' ),
				'choose_text'          => esc_attr__( 'Choose an Image', 'divi_flash' ),
				'update_text'          => esc_attr__( 'Set As Image', 'divi_flash' ),
				'toggle_slug'          => 'main_content',
				'tab_slug'             => 'general',
				'dynamic_main_content' => 'image',
				'show_if'              => [ 'item_type' => 'Image' ],
			],
			'item_image_alt' => [
				'label'                => esc_html__( 'Image alt', 'divi_flash' ),
				'type'                 => 'text',
				'toggle_slug'          => 'main_content',
				'tab_slug'             => 'general',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Image' ],
			],
		];

		$image_icon['icon_color']                  = [
			'default'         => et_builder_accent_color(),
			'label'           => esc_html__( 'Icon Color', 'divi_flash' ),
			'type'            => 'color-alpha',
			'depends_show_if' => 'on',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'image_icon',
			'mobile_options'  => false,
			'responsive'      => false,
			'show_if'         => [ 'item_type' => 'Icon' ],
			'hover'           => 'tabs',
			'sticky'          => true,
		];
		$image_icon['image_icon_background_color'] = [
			'label'          => esc_html__( 'Image/Icon Background Color', 'divi_flash' ),
			'type'           => 'color-alpha',
			'tab_slug'       => 'advanced',
			'toggle_slug'    => 'image_icon',
			'hover'          => 'tabs',
			'mobile_options' => false,
			'responsive'     => false,
			'sticky'         => true,
		];
		$image_icon['image_icon_width']            = [
			'label'                  => esc_html__( 'Image/Icon Width', 'divi_flash' ),
			'toggle_slug'            => 'image_icon',
			'type'                   => 'range',
			'default'                => '96px',
			'default_on_front'       => '96px',
			'range_settings'         => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'option_category'        => 'layout',
			'tab_slug'               => 'advanced',
			'mobile_options'         => true,
			'validate_unit'          => true,
			'allowed_units'          => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'responsive'             => true,
			'sticky'                 => true,
			'default_value_depends'  => [ 'use_icon' ],
			'default_values_mapping' => [
				'on'  => '96px',
				'off' => '100%',
			],
		];
		$image_icon['image_icon_alignment']        = [
			'label'            => esc_html__( 'Image/Icon Alignment', 'divi_flash' ),
			'type'             => 'align',
			'option_category'  => 'layout',
			'options'          => et_builder_get_text_orientation_options( [ 'justified' ] ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'image_icon',
			'default'          => 'center',
			'default_on_front' => 'center',
		];

		$ribbon_text_spacing = $this->add_margin_padding( [
			'title'       => esc_html__( 'Ribbon Text', 'divi_flash' ),
			'key'         => 'ribbon_spacing',
			'toggle_slug' => 'main_content',
			'tab_slug'    => 'general',
			'show_if'     => [ 'item_type' => 'Ribbon' ],
			'show_if_not' => [
				'ribbon_type' => [ 'image_only', 'icon_only', ],
			],
		] );
		$ribbon_icon_spacing = $this->add_margin_padding( [
			'title'       => esc_html__( 'Ribbon Icon', 'divi_flash' ),
			'key'         => 'ribbon_icon_spacing',
			'toggle_slug' => 'main_content',
			'tab_slug'    => 'general',
			'show_if'     => [ 'item_type' => 'Ribbon' ],
			'show_if_not' => [
				'ribbon_type' => [ 'image_only', 'text_only', 'text_image' ],
			],
		] );
		$ribbon_text_fields  = array_merge( [
			'ribbon_type'        => [
				'label'            => esc_html__( 'Choose Type', 'divi_flash' ),
				'default'          => 'text_only',
				'default_on_front' => 'text_only',
				'type'             => 'select',
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'option_category'  => 'configuration',
				'options'          => [
					'text_only'  => esc_html__( 'Text Only', 'divi_flash' ),
					'text_icon'  => esc_html__( 'Text With Icon', 'divi_flash' ),
					'text_image' => esc_html__( 'Text With Image', 'divi_flash' ),
					'image_only' => esc_html__( 'Image Only', 'divi_flash' ),
					'icon_only'  => esc_html__( 'Icon Only', 'divi_flash' ),
				],
				'show_if'          => [
					'item_type' => 'Ribbon',
				],
			],
			'ribbon_text'        => [
				'label'           => esc_html__( 'Ribbon Text', 'divi_flash' ),
				'type'            => 'text',
				'default'         => 'Ribbon Text',
				'toggle_slug'     => 'main_content',
				'tab_slug'        => 'general',
				'dynamic_content' => 'text',
				'show_if'         => [
					'item_type' => 'Ribbon',
				],
				'show_if_not'     => [
					'ribbon_type' => [ 'image_only', 'icon_only' ],
				],
			],
			'ribbon_orientation' => [
				'label'            => esc_html__( 'Text Orientation', 'divi_flash' ),
				'default'          => 'vertical',
				'default_on_front' => 'vertical',
				'type'             => 'select',
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'option_category'  => 'configuration',
				'options'          => [
					'horizontal' => esc_html__( 'Horizontal', 'divi_flash' ),
					'vertical'   => esc_html__( 'Vertical', 'divi_flash' ),
				],
				'show_if'          => [
					'item_type' => 'Ribbon',
				],
				'show_if_not'      => [
					'ribbon_type' => [ 'image_only', 'icon_only' ],
				],
			],
		],
			$ribbon_text_spacing
		);
		$ribbon_fields       = array_merge( $ribbon_text_fields, [
			'ribbon_icon'           => [
				'label'                => esc_html__( 'Ribbon Icon', 'divi_flash' ),
				'type'                 => 'select_icon',
				'class'                => [ 'et-pb-font-icon' ],
				'toggle_slug'          => 'main_content',
				'tab_slug'             => 'general',
				'dynamic_main_content' => 'image',
				'show_if'              => [ 'item_type' => 'Ribbon' ],
				'show_if_not'          => [
					'ribbon_type' => [ 'image_only', 'text_only', 'text_image' ],
				],
			],
			'ribbon_icon_placement' => [
				'default'          => 'left',
				'default_on_front' => 'left',
				'label'            => esc_html__( 'Icon Placement', 'divi_flash' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'left'   => esc_html__( 'Left', 'divi_flash' ),
					'right'  => esc_html__( 'Right', 'divi_flash' ),
					'top'    => esc_html__( 'Top', 'divi_flash' ),
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'show_if'          => [ 'item_type' => 'Ribbon' ],
				'show_if_not'      => [
					'ribbon_type' => [ 'image_only', 'text_only', 'text_image' ],
				],
			],
			'ribbon_icon_size'      => [
				'label'          => esc_html__( 'Icon Size', 'divi_flash' ),
				'type'           => 'range',
				'default'        => '16px',
				'default_unit'   => 'px',
				'range_settings' => [
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				],
				'mobile_options' => true,
				'responsive'     => true,
				'toggle_slug'    => 'main_content',
				'tab_slug'       => 'general',
				'show_if'        => [ 'item_type' => 'Ribbon' ],
				'show_if_not'    => [
					'ribbon_type' => [ 'image_only', 'text_only', 'text_image' ],
				],
			],
			'ribbon_icon_color'     => [
				'label'          => esc_html__( 'Icon Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => true,
				'responsive'     => true,
				'toggle_slug'    => 'main_content',
				'tab_slug'       => 'general',
				'show_if'        => [ 'item_type' => 'Ribbon' ],
				'show_if_not'    => [
					'ribbon_type' => [ 'image_only', 'text_only', 'text_image' ],
				],
			],
			'ribbon_icon_bg_color'  => [
				'label'          => esc_html__( 'Icon Background Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => true,
				'responsive'     => true,
				'toggle_slug'    => 'main_content',
				'tab_slug'       => 'general',
				'show_if'        => [ 'item_type' => 'Ribbon' ],
				'show_if_not'    => [
					'ribbon_type' => [ 'image_only', 'text_only', 'text_image' ],
				],
			],
		],
			$ribbon_icon_spacing,
			[
				'ribbon_image'       => [
					'label'              => esc_html__( 'Ribbon Image', 'divi_flash' ),
					'type'               => 'upload',
					'upload_button_text' => esc_attr__( 'Upload an image', 'divi_flash' ),
					'choose_text'        => esc_attr__( 'Choose an Image', 'divi_flash' ),
					'update_text'        => esc_attr__( 'Set As Image', 'divi_flash' ),
					'toggle_slug'        => 'main_content',
					'tab_slug'           => 'general',
					'dynamic_content'    => 'image',
					'show_if'            => [
						'item_type' => 'Ribbon',
					],
					'show_if_not'        => [
						'ribbon_type' => [ 'icon_only', 'text_only', 'text_icon' ],
					],
				],
				'ribbon_image_alt'   => [
					'label'                => esc_html__( 'Image alt', 'divi_flash' ),
					'type'                 => 'text',
					'toggle_slug'          => 'main_content',
					'tab_slug'             => 'general',
					'dynamic_main_content' => 'text',
					'show_if'              => [ 'item_type' => 'Ribbon' ],
					'show_if_not'          => [
						'ribbon_type' => [ 'icon_only', 'text_only', 'text_icon' ],
					],
				],
				'ribbon_image_width' => [
					'label'          => esc_html__( 'Image Width', 'divi_flash' ),
					'type'           => 'range',
					'default'        => '100px',
					'default_unit'   => 'px',
					'range_settings' => [
						'min'  => '0',
						'max'  => '500',
						'step' => '1',
					],
					'mobile_options' => true,
					'toggle_slug'    => 'main_content',
					'tab_slug'       => 'general',
					'show_if'        => [ 'item_type' => 'Ribbon' ],
					'show_if_not'    => [
						'ribbon_type' => [ 'icon_only', 'text_only', 'text_icon' ],
					],
				],
				'ribbon_transform_x' => [
					'label'          => esc_html__( 'Horizontal Position', 'divi_flash' ),
					'type'           => 'range',
					'default'        => '0%',
					'default_unit'   => '%',
					'range_settings' => [
						'min'  => '-100',
						'max'  => '100',
						'step' => '1',
					],
					'mobile_options' => true,
					'responsive'     => true,
					'toggle_slug'    => 'main_content',
					'tab_slug'       => 'general',
					'show_if'        => [ 'item_type' => 'Ribbon' ],
				],
				'ribbon_transform_y' => [
					'label'          => esc_html__( 'Vertically Position', 'divi_flash' ),
					'type'           => 'range',
					'default'        => '0%',
					'default_unit'   => '%',
					'range_settings' => [
						'min'  => '-100',
						'max'  => '100',
						'step' => '1',
					],
					'mobile_options' => true,
					'responsive'     => true,
					'toggle_slug'    => 'main_content',
					'tab_slug'       => 'general',
					'show_if'        => [ 'item_type' => 'Ribbon' ],
				],
				'ribbon_position'    => [
					'label'            => esc_html__( 'Ribbon Position', 'divi_flash' ),
					'type'             => 'select',
					'default'          => 'top_right',
					'default_on_front' => 'top_right',
					'options'          => [
						'top_left'     => esc_html__( 'Top Left', 'divi_flash' ),
						'top_right'    => esc_html__( 'Top Right', 'divi_flash' ),
						'bottom_left'  => esc_html__( 'Bottom Left', 'divi_flash' ),
						'bottom_right' => esc_html__( 'Bottom Right', 'divi_flash' ),
					],
					'toggle_slug'      => 'main_content',
					'tab_slug'         => 'general',
					'show_if'          => [ 'item_type' => 'Ribbon' ],
				],
				'ribbon_animation'   => [
					'label'            => esc_html__( 'Enable Animation', 'divi_flash' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'basic_option',
					'default'          => 'off',
					'default_on_front' => 'off',
					'options'          => [
						'off' => esc_html__( 'No', 'divi_flash' ),
						'on'  => esc_html__( 'Yes', 'divi_flash' ),
					],
					'toggle_slug'      => 'main_content',
					'tab_slug'         => 'general',
					'show_if'          => [ 'item_type' => 'Ribbon' ],
				],
			] );
		$divider_fields      = array_merge( [
			'divider_height' => [
				'label'          => esc_html__( 'Line Height', 'divi_flash' ),
				'type'           => 'range',
				'default'        => '3px',
				'default_unit'   => 'px',
				'allowed_units'  => [ 'px' ],
				'range_settings' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'mobile_options' => true,
				'toggle_slug'    => 'main_content',
				'tab_slug'       => 'general',
				'show_if'        => [ 'item_type' => 'Divider' ],
			],
		], $this->df_add_bg_field( [
			'label'         => esc_html__( 'Line Color', 'divi_flash' ),
			'key'           => 'divider_color',
			'show_if'       => [ 'item_type' => 'Divider' ],
			'default_color' => '#0a4b78',
			'toggle_slug'   => 'main_content',
			'tab_slug'      => 'general',
			'image'         => false,
			'prefix'        => '',
			'suffix'        => 'Color',
		] ) );
		$button_fields       = array_merge( [
			'button_text'                      => [
				'label'                => esc_html__( 'Button Text', 'divi_flash' ),
				'type'                 => 'text',
				'default'              => esc_html__( 'Click Here', 'divi_flash' ),
				'option_category'      => 'basic_option',
				'description'          => esc_html__( 'Input your desired button text, or leave blank for no button.', 'divi_flash' ),
				'toggle_slug'          => 'main_content',
				'tab_slug'             => 'general',
				'dynamic_main_content' => 'text',
				'show_if'              => [ 'item_type' => 'Button' ],
			],
			'button_url'                       => [
				'label'                => esc_html__( 'Button URL', 'divi_flash' ),
				'type'                 => 'text',
				'dynamic_main_content' => 'text',
				'option_category'      => 'basic_option',
				'description'          => esc_html__( 'Input URL for your button.', 'divi_flash' ),
				'toggle_slug'          => 'main_content',
				'tab_slug'             => 'general',
				'show_if'              => [ 'item_type' => 'Button' ],
			],
			'button_url_new_window'            => [
				'default'          => 'off',
				'default_on_front' => true,
				'label'            => esc_html__( 'Button Link Target', 'divi_flash' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'off' => esc_html__( 'In The Same Window', 'divi_flash' ),
					'on'  => esc_html__( 'In The New Tab', 'divi_flash' ),
				],
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'show_if'          => [ 'item_type' => 'Button' ],
			],
			'button_full_width'                => [
				'label'       => esc_html__( 'Enable Button Fullwidth', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'off' => esc_html__( 'Off', 'divi_flash' ),
					'on'  => esc_html__( 'On', 'divi_flash' ),
				],
				'default'     => 'off',
				'toggle_slug' => 'main_content',
				'tab_slug'    => 'general',
				'show_if'     => [ 'item_type' => 'Button' ],
			],
			'button_alignment'                 => [
				'label'          => esc_html__( 'Button Alignment', 'divi_flash' ),
				'type'           => 'text_align',
				'options'        => et_builder_get_text_orientation_options( [ 'justified' ] ),
				'toggle_slug'    => 'main_content',
				'tab_slug'       => 'general',
				'mobile_options' => true,
				'show_if'        => [
					'button_full_width' => 'off',
					'item_type'         => 'Button',
				],
			],
			'button_badge'                     => [
				'label'           => esc_html__( 'Enable Button Badge', 'divi_flash' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'     => 'main_content',
				'tab_slug'        => 'general',
				'show_if'         => [ 'item_type' => 'Button' ],
			],
			'button_badge_text'                => [
				'label'           => esc_html__( 'Badge Text', 'divi_flash' ),
				'type'            => 'text',
				'default'         => 'text',
				'toggle_slug'     => 'main_content',
				'tab_slug'        => 'general',
				'dynamic_content' => 'text',
				'show_if'         => [
					'item_type'    => 'Button',
					'button_badge' => 'on',
				],
			],
			'button_badge_position_vertically' => [
				'label'            => esc_html__( 'Badge Position', 'divi_flash' ),
				'type'             => 'select',
				'default'          => 'top',
				'default_on_front' => 'top',
				'options'          => [
					'top'    => esc_html__( 'Top', 'divi_flash' ),
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'show_if'          => [ 'item_type' => 'Button', 'button_badge' => 'on' ],
			],
			'button_badge_position'            => [
				'label'            => esc_html__( 'Badge Alignment', 'divi_flash' ),
				'type'             => 'select',
				'default'          => 'right',
				'default_on_front' => 'right',
				'options'          => [
					'right'  => esc_html__( 'Right', 'divi_flash' ),
					'center' => esc_html__( 'Center', 'divi_flash' ),
					'left'   => esc_html__( 'Left', 'divi_flash' ),
				],
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'show_if'          => [ 'item_type' => 'Button', 'button_badge' => 'on' ],
			],

			'button_badge_animation' => [
				'label'           => esc_html__( 'Enable Badge Animation', 'divi_flash' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'     => 'main_content',
				'tab_slug'        => 'general',
				'show_if'         => [ 'button_badge' => 'on' ],
				'affects'         => [ 'button_badge_animation_type' ],
			],
		], $this->df_add_bg_field( [
			'label'       => esc_html__( 'Background', 'divi_flash' ),
			'key'         => 'button_badge_bg',
			'toggle_slug' => 'button_badge',
			'tab_slug'    => 'advanced',
			'image'       => false,
		] ), $this->add_margin_padding( [
			'title'       => esc_html__( '', 'divi_flash' ),
			'key'         => 'button_badge_spacing',
			'toggle_slug' => 'button_badge',
			'tab_slug'    => 'advanced',
		] )
		);

		$rating_fields = [
			'rating_number'             => [
				'label'            => esc_html__( 'Rating Number', 'divi_flash' ),
				'type'             => 'range',
				'default'          => '5',
				'default_on_front' => '5',
				'range_settings'   => [
					'min'       => '0.1',
					'max'       => '5',
					'step'      => '0.1',
					'min_limit' => '0',
					'max_limit' => '5',
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [
					'item_type' => 'Rating',
				],
			],
			'rating_enable_custom_icon' => [
				'label'            => esc_html__( 'Use Custom Icon', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'off',
				'default_on_front' => 'off',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'show_if'          => [ 'item_type' => 'Rating' ],
			],
			'rating_icon'               => [
				'label'           => esc_html__( 'Rating Icon', 'divi_flash' ),
				'type'            => 'select_icon',
				'option_category' => 'basic_option',
				'default'         => '☆',
				'class'           => [ 'et-pb-font-icon' ],
				'toggle_slug'     => 'main_content',
				'tab_slug'        => 'general',
				'show_if'         => [
					'item_type'                 => 'Rating',
					'rating_enable_custom_icon' => 'on',
				],
			],
			'rating_label'              => [
				'label'           => esc_html__( 'Rating Label', 'divi_flash' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This defines the Rating Label text.', 'divi_flash' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'show_if'         => [ 'item_type' => 'Rating' ],
			],
			'rating_icon_label_gap'     => [
				'label'            => esc_html__( 'Icon & Label Gap', 'divi_flash' ),
				'type'             => 'range',
				'mobile_options'   => true,
				'responsive'       => true,
				'default_unit'     => 'px',
				'default'          => '8px',
				'default_on_front' => '8px',
				'range_settings '  => [
					'min'  => '8',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [
					'item_type' => 'Rating',
				],
			],
			'rating_icon_size'          => [
				'label'            => esc_html__( 'Icon size', 'divi_flash' ),
				'type'             => 'range',
				'mobile_options'   => true,
				'responsive'       => true,
				'default'          => '16px',
				'default_on_front' => '16px',
				'default_unit'     => 'px',
				'range_settings '  => [
					'min'  => '0',
					'max'  => '1000',
					'step' => '1',
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [
					'item_type' => 'Rating',
				],
			],
			'rating_icon_gap'           => [
				'label'           => esc_html__( 'Icon Gap', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => true,
				'responsive'      => true,
				'default_unit'    => 'px',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
				'show_if'         => [
					'item_type' => 'Rating',
				],
			],
			'rating_color'              => [
				'label'       => esc_html__( 'Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'general',
				'toggle_slug' => 'main_content',
				'show_if'     => [ 'item_type' => 'Rating' ],
			],
			'rating_color_inactive'     => [
				'label'       => esc_html__( 'Inactive Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'tab_slug'    => 'general',
				'toggle_slug' => 'main_content',
				'show_if'     => [ 'item_type' => 'Rating' ],
			],
			'rating_alignment'          => [
				'label'            => esc_html__( 'Alignment', 'divi_flash' ),
				'type'             => 'align',
				'option_category'  => 'layout',
				'options'          => et_builder_get_text_orientation_options( [ 'justified' ] ),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [ 'item_type' => 'Rating' ],
				'default'          => 'center',
				'default_on_front' => 'center',
			],
		];

		return array_merge( $general, $text_fields, $price_fields,
			$feature_fields,
			$icon_fields,
			$image_fields,
			$image_icon,
			$ribbon_fields,
			$divider_fields,
			$button_fields,
			$rating_fields );
	}

	public function get_advanced_fields_config() {
		$advanced_fields              = [];
		$advanced_fields['text']      = false;
		$module_font['default']       = [
			'label'          => esc_html__( 'Element', 'divi_flash' ),
			'css'            => [
				'main'       => '%%order_class%% .item-text, %%order_class%% .item-price .sale-price .price, %%order_class%% .item-feature .feature_text, %%order_class%% .item-button a, %%order_class%% .item-icon .item_icon, %%order_class%% .item-ribbon .ribbon_text, %%order_class%% .item-rating span.label',
				'hover'      => '%%order_class%% .item-text:hover,%%order_class%% .item-price .sale-price .price:hover, %%order_class%% .item-feature .feature_text:hover, %%order_class%% .item-button a:hover, %%order_class%% .item-icon .item_icon:hover, %%order_class%% .item-ribbon .ribbon_text:hover, %%order_class%% .item-rating span.label:hover',
				'text_align' => '%%order_class%%, %%order_class%% .item-rating span.label',
				'important'  => true,
			],
			'text_align'     => [
				'default'          => 'center',
				'default_on_front' => 'center',
			],
			'block_elements' => [
				'tabbed_subtoggles' => true,
				'bb_icons_support'  => true,
				'css'               => [
					'main'  => "%%order_class%% .item-text",
					'hover' => "%%order_class%% .item-text:hover",
				],
			],
		];
		$heading['heading_1']         = [
			'label'       => esc_html__( 'Heading', 'divi_flash' ),
			'font_size'   => [
				'default' => absint( et_get_option( 'body_header_size', '30' ) ) . 'px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => "%%order_class%% .item-text h1",
				'hover' => "%%order_class%% .item-text h1:hover",
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h1',
		];
		$heading['heading_2']         = [
			'label'       => esc_html__( 'Heading 2', 'divi_flash' ),
			'font_size'   => [
				'default' => '26px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => "%%order_class%% .item-text h2",
				'hover' => "%%order_class%% .item-text h2:hover",
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'heading',
			'sub_toggle'  => 'h2',
		];
		$heading['heading_3']         = [
			'label'       => esc_html__( 'Heading 3', 'divi_flash' ),
			'font_size'   => [
				'default' => '22px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => "%%order_class%% .item-text h3",
				'hover' => "%%order_class%% .item-text h3:hover",
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h3',
		];
		$heading['heading_4']         = [
			'label'       => esc_html__( 'Heading 4', 'divi_flash' ),
			'font_size'   => [
				'default' => '18px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => "%%order_class%% .item-text h4",
				'hover' => "%%order_class%% .item-text h4:hover",
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h4',
		];
		$heading['heading_5']         = [
			'label'       => esc_html__( 'Heading 5', 'divi_flash' ),
			'font_size'   => [
				'default' => '16px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => "%%order_class%% .item-text h5",
				'hover' => "%%order_class%% .item-text h5:hover",
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h5',
		];
		$heading['heading_6']         = [
			'label'       => esc_html__( 'Heading 6', 'divi_flash' ),
			'font_size'   => [
				'default' => '14px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => "%%order_class%% .item-text h6",
				'hover' => "%%order_class%% .item-text h6:hover",
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h6',
		];
		$heading['tooltip_heading_1'] = [
			'label'       => esc_html__( 'Heading 1', 'divi_flash' ),
			'font_size'   => [
				'default' => absint( et_get_option( 'body_header_size', '30' ) ) . 'px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '.tippy-box[data-theme~="%%order_class%%"] h1',
				'hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover h1',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_tooltip_heading',
			'sub_toggle'  => 'h1',
		];
		$heading['tooltip_heading_2'] = [
			'label'       => esc_html__( 'Heading 2', 'divi_flash' ),
			'font_size'   => [
				'default' => '26px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '.tippy-box[data-theme~="%%order_class%%"] h2',
				'hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover h2',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_tooltip_heading',
			'sub_toggle'  => 'h2',
		];
		$heading['tooltip_heading_3'] = [
			'label'       => esc_html__( 'Heading 3', 'divi_flash' ),
			'font_size'   => [
				'default' => '22px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '.tippy-box[data-theme~="%%order_class%%"] h3',
				'hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover h3',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_tooltip_heading',
			'sub_toggle'  => 'h3',
		];
		$heading['tooltip_heading_4'] = [
			'label'       => esc_html__( 'Heading 4', 'divi_flash' ),
			'font_size'   => [
				'default' => '18px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '.tippy-box[data-theme~="%%order_class%%"] h4',
				'hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover h4',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_tooltip_heading',
			'sub_toggle'  => 'h4',
		];
		$heading['tooltip_heading_5'] = [
			'label'       => esc_html__( 'Heading 5', 'divi_flash' ),
			'font_size'   => [
				'default' => '16px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '.tippy-box[data-theme~="%%order_class%%"] h5',
				'hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover h5',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_tooltip_heading',
			'sub_toggle'  => 'h5',
		];
		$heading['tooltip_heading_6'] = [
			'label'       => esc_html__( 'Heading 6', 'divi_flash' ),
			'font_size'   => [
				'default' => '14px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '.tippy-box[data-theme~="%%order_class%%"] h6',
				'hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover h6',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_tooltip_heading',
			'sub_toggle'  => 'h6',
		];

		$prefix_font['original_price']                     = [
			'label'   => esc_html__( 'Sale Price', 'divi_flash' ),
			'show_if' => [ 'item_type' => 'Price' ],
			'css'     => [
				'main'  => '%%order_class%% .item-price .original-price .price',
				'hover' => '%%order_class%% .item-price .original-price .price:hover',
			],
		];
		$regular_price_prefix_font['regular_price_prefix'] = [
			'label'   => esc_html__( 'Regular Price Prefix', 'divi_flash' ),
			'show_if' => [ 'item_type' => 'Price' ],
			'css'     => [
				'main'  => '%%order_class%% .item-price .sale-price .price_prefix',
				'hover' => '%%order_class%% .item-price .sale-price .price_prefix:hover',
			],
		];
		$regular_price_suffix_font['regular_price_suffix'] = [
			'label'   => esc_html__( 'Regular Price Suffix', 'divi_flash' ),
			'show_if' => [ 'item_type' => 'Price' ],
			'css'     => [
				'main'  => '%%order_class%%  .item-price .sale-price .price_suffix',
				'hover' => '%%order_class%%  .item-price .sale-price .price_suffix:hover',
			],
		];

		$sale_price_prefix_font['sale_price_prefix']  = [
			'label'   => esc_html__( 'Sale Price Prefix', 'divi_flash' ),
			'show_if' => [ 'item_type' => 'Price' ],
			'css'     => [
				'main'  => '%%order_class%% .item-price .original-price .price_prefix',
				'hover' => '%%order_class%% .item-price .original-price .price_prefix:hover',
			],
		];
		$sale_price_suffix_font['sale_price_suffix']  = [
			'label'   => esc_html__( 'Sale Price Suffix', 'divi_flash' ),
			'show_if' => [ 'item_type' => 'Price' ],
			'css'     => [
				'main'  => '%%order_class%%  .item-price .original-price .price_suffix',
				'hover' => '%%order_class%%  .item-price .original-price .price_suffix:hover',
			],
		];
		$button_badge_text['button_badge']            = [
			'show_if'     => [ 'button_badge_animation' => 'on' ],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'button_badge',
			'css'         => [
				'main'  => '%%order_class%%  .item-button .button-badge',
				'hover' => '%%order_class%%  .item-button .button-badge:hover',
			],
		];
		$feature_tooltip_font['feature_tooltip_font'] = [
			'label'          => esc_html__( 'Feature Tooltip', 'divi_flash' ),
			'show_if'        => [
				'item_type'            => 'Feature',
				'feature_text_tooltip' => 'on',
			],
			'tab_slug'       => 'advanced',
			'toggle_slug'    => 'feature_tooltip_font',
			'css'            => [
				'main'  => '.tippy-box[data-theme~="%%order_class%%"]',
				'hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover',
			],
			'block_elements' => [
				'tabbed_subtoggles' => true,
				'bb_icons_support'  => true,
				'css'               => [
					'main'  => '.tippy-box[data-theme~="%%order_class%%"]',
					'hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover',
				],
			],

		];
		$advanced_fields['fonts']                     = array_merge( $button_badge_text, $module_font, $heading, $prefix_font, $regular_price_prefix_font, $regular_price_suffix_font, $sale_price_prefix_font, $sale_price_suffix_font, $feature_tooltip_font );

		$border['default']                           = [
			'css' => [
				'main' => [
					'border_radii'        => "%%order_class%%, %%order_class%% .item-divider",
					'border_radii_hover'  => "%%order_class%%:hover, %%order_class%% .item-divider:hover",
					'border_styles'       => "%%order_class%%, %%order_class%% .item-divider",
					'border_styles_hover' => "%%order_class%%:hover,  %%order_class%% .item-divider:hover",
				],
			],
		];
		$feature_tooltip_border['tooltip']           = [
			'show_if'     => [ 'feature_text_tooltip' => 'on' ],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_tooltip_style',
			'css'         => [
				'main' => [
					'border_radii'        => '.tippy-box[data-theme~="%%order_class%%"]',
					'border_radii_hover'  => '.tippy-box[data-theme~="%%order_class%%"]:hover',
					'border_styles'       => '.tippy-box[data-theme~="%%order_class%%"]',
					'border_styles_hover' => '.tippy-box[data-theme~="%%order_class%%"]:hover',
				],
			],
			'important'   => 'all',
		];
		$feature_tooltip_icon_border['tooltip_icon'] = [
			'show_if'     => [ 'feature_text_tooltip' => 'on' ],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'feature_icon_style',
			'css'         => [
				'main' => [
					'border_radii'        => '%%order_class%%  .item-feature .feature_icon',
					'border_radii_hover'  => '%%order_class%%  .item-feature .feature_icon:hover',
					'border_styles'       => '%%order_class%%  .item-feature .feature_icon',
					'border_styles_hover' => '%%order_class%%  .item-feature .feature_icon:hover',
				],
			],
			'important'   => 'all',
		];
		$button_badge_border['button_badge']         = [
			'show_if'     => [ 'button_badge_animation' => 'on' ],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'button_badge',
			'css'         => [
				'main' => [
					'border_radii'        => '%%order_class%%  .item-button .button-badge',
					'border_radii_hover'  => '%%order_class%%  .item-button .button-badge:hover',
					'border_styles'       => '%%order_class%%  .item-button .button-badge',
					'border_styles_hover' => '%%order_class%%  .item-button .button-badge:hover',
				],
			],
			'important'   => 'all',
		];
		$image_icon_border['image_icon']             = [
			'css'          => [
				'main' => [
					'border_radii'        => '%%order_class%% .item-image img, %%order_class%% .item-icon .item_icon',
					'border_radii_hover'  => '%%order_class%%:hover .item-image img, %%order_class%%:hover .item-icon .item_icon ',
					'border_styles'       => '%%order_class%% .item-image img, %%order_class%% .item-icon .item_icon ',
					'border_styles_hover' => '%%order_class%%:hover .item-image img, %%order_class%%:hover .item-icon .item_icon',
				],
			],
			'label_prefix' => esc_html__( 'Image & Icon', 'divi_flash' ),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'image_icon',
		];

		$advanced_fields['borders'] = array_merge( $border, $feature_tooltip_border, $feature_tooltip_icon_border, $button_badge_border, $image_icon_border );

		$advanced_fields['button']['button'] = [
			'label'          => esc_html__( 'Button', 'divi_flash' ),
			'tab_slug'       => 'advanced',
			'toggle_slug'    => 'button_style',
			'css'            => [
				'main'         => '%%order_class%% .item-button .difl-pricingtableitem-button',
				'limited_main' => '%%order_class%% .item-button .difl-pricingtableitem-button',
				'important'    => true,
			],
			'box_shadow'     => [
				'css' => [
					'main'      => '%%order_class%% .item-button .difl-pricingtableitem-button',
					'important' => true,
				],
			],
			'use_alignment'  => false,
			'margin_padding' => [
				'css' => [
					'main'      => '%%order_class%% .item-button .difl-pricingtableitem-button',
					'important' => 'all',
				],
			],
			'show_if'        => [ 'item_type' => 'Button' ],
		];

		$advanced_fields['image_icon'] = [
			'image_icon' => [
				'margin_padding'  => [
					'css' => [
						'important' => 'all',
					],
				],
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image_icon',
				'label'           => esc_html__( 'Image & Icon', 'divi_flash' ),
				'css'             => [
					'padding' => '%%order_class%% .item-image img, %%order_class%% .item-icon .item_icon',
					'margin'  => '%%order_class%% .item-image img, %%order_class%% .item-icon .item_icon',
					'main'    => '%%order_class%% .item-image img, %%order_class%% .item-icon .item_icon',
				],
			],
		];

		$advanced_fields['margin_padding'] = [
			'css' => [
				'padding'   => '%%order_class%%',
				'margin'    => '%%order_class%%',
				'main'      => '%%order_class%%',
				'important' => 'all',
			],
		];

		return $advanced_fields;
	}

	public function &__get( $name ) {
		if ( array_key_exists( $name, $this->props ) ) {
			return $this->props[ $name ];
		}

		throw new Exception( sprintf( 'Property %s does not exist', $name ) );
	}

	private function get_item_fields( $item_type ) {
		$fields = $this->get_fields();
		if ( empty( $item_type ) || empty( $fields ) ) {
			return [];
		}

		return array_filter( $fields, function ( $field, $key ) use ( $item_type ) {
			return array_key_exists( 'show_if', $field ) && is_array( $field['show_if'] ) && in_array( $item_type, $field['show_if'] );
		}, 1 );
	}

	protected function item_content_by_keys( $fields, $parent_class = '' ) {
		if ( empty( $fields ) ) {
			return '';
		}

		$types = [ 'text', 'tiny_mce', 'select', 'select_icon', 'range', 'upload', 'color-alpha' ];

		$content = '';

		foreach ( $fields as $key => $field ) {
			$type  = $field['type'];
			$value = $this->{$key};

			if ( ! in_array( $type, $types ) || empty( $value ) ) {
				continue;
			}

			if ( ! empty( $field['show_if'] ) ) {
				foreach ( $field['show_if'] as $if_key => $slug ) {
					$dependent_value = $this->{$if_key};
					if ( $slug !== $dependent_value ) {
						continue 2;
					}
				}
			}

			switch ( $type ) {
				case 'text':
				case 'tiny_mce':
					$content .= sprintf( '<span class="%2$s">%1$s</span>', $value, $key );
					break;
				case 'select_icon':
					$content .= sprintf( '<span class="et-pb-icon %2$s">%1$s</span>', $key !== '' ? esc_attr( et_pb_process_font_icon( $value ) ) : '5', $key );
					break;
				default:
					break;
			}
		}

		return $content;
	} // Seperate this for every single item and that way we can add manipulate class order etc

	protected function generate_icon_style( $base_attr, $selector ) {
		$this->generate_styles(
			[
				'utility_arg'    => 'icon_font_family',
				'render_slug'    => $this->slug,
				'base_attr_name' => $base_attr,
				'important'      => true,
				'selector'       => $selector,
				'processor'      => [
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				],
			]
		);
	}

	protected function generate_generic_style( $key, $selector, $type = 'color' ) {
		$this->generate_styles(
			[
				'type'                            => $type,
				'render_slug'                     => $this->slug,
				'base_attr_name'                  => $key,
				'css_property'                    => $type,
				'selector'                        => $selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
			]
		);
	}

	protected function generate_font_size( $key, $selector, $type = 'range' ) {
		$this->generate_styles(
			[
				'type'                            => $type,
				'render_slug'                     => $this->slug,
				'base_attr_name'                  => $key,
				'selector'                        => $selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'font-size',
				'processor'                       => [
					'ET_Builder_Module_Helper_Style_Processor',
					'process_toggle_title_icon_font_size',
				],
			]
		);
	}


	// Types -- text, tiny_mce, select, select_icon, range, upload, color-alpha
	public function render_item_Text() {
		global $wp_embed;
		add_shortcode( 'embed', [ $wp_embed, 'shortcode' ] );

		return sprintf( '<div class="item-text">%1$s</div>', do_shortcode( html_entity_decode( preg_replace( [
			'/<\/p>/',
			'/<p>\s*<\/p>/',
			'/<p>\s*$/',
		], [ '', '', '' ], $this->text_content ) ) ) );
	}

	public function render_item_Price() {
		$original_price = '';
		if ( 'on' === $this->enable_original_price ) {
			$this->generate_generic_style( 'price_gap', '%%order_class%% .item-price', 'gap' );
			$prefix = sprintf( '<span class="price_prefix %2$s">%1$s</span>', esc_html( $this->original_price_prefix ), $this->original_price_prefix_placement );
			$price  = sprintf( '<span class="price">%1$s</span>', esc_html( $this->original_price ) );
			$suffix = sprintf( '<span class="price_suffix %2$s">%1$s</span>', esc_html( $this->original_price_suffix ), $this->original_price_suffix_placement );

			$original_price = sprintf( '<span class="original-price %4$s">%1$s %2$s %3$s</span>', $prefix, $price, $suffix, $this->original_price_placement );
		}
		$prefix     = sprintf( '<span class="price_prefix %2$s">%1$s</span>', esc_html( $this->price_prefix ), $this->price_prefix_placement );
		$price      = sprintf( '<span class="price">%1$s</span>', esc_html( $this->price ) );
		$suffix     = sprintf( '<span class="price_suffix %2$s">%1$s</span>', esc_html( $this->price_suffix ), $this->price_suffix_placement );
		$sale_price = sprintf( '<span class="sale-price">%1$s %2$s %3$s</span>', $prefix, $price, $suffix );

		return sprintf( '<div class="item-price %3$s">%1$s %2$s</div>', $original_price, $sale_price, $this->price_alignemnt );
	}

	public function render_item_Feature() {
		$keys = [
			'feature_text' => $this->get_fields()['feature_text'],
			'feature_icon' => $this->get_fields()['feature_icon'],
		];

		$icon_placement = $this->feature_icon_placement;
		$parent_class   = "item-feature icon-$icon_placement";
		$icon_selector  = '%%order_class%% .item-feature .et-pb-icon';
		$this->set_margin_padding_styles(
			[
				'render_slug' => $this->slug,
				'slug'        => "feature_icon_spacing_padding",
				'type'        => 'padding',
				'selector'    => "$icon_selector",
				'hover'       => "$icon_placement:hover",
			]
		);
		$tooltip_selector = '.tippy-box[data-theme~="%%order_class%%"]';
		$this->set_margin_padding_styles(
			[
				'render_slug' => $this->slug,
				'slug'        => 'feature_tooltip_spacing_padding',
				'type'        => 'padding',
				'selector'    => "$tooltip_selector",
				'hover'       => "$tooltip_selector:hover",
			]
		);
		$this->generate_generic_style( 'price_icon_gap', '%%order_class%% .item-feature', 'gap' );
		$this->generate_icon_style( 'feature_icon', $icon_selector );
		$this->generate_generic_style( 'feature_icon_color', $icon_selector );
		$this->generate_generic_style( 'feature_icon_bg_color', $icon_selector, 'background-color' );
		$this->generate_font_size( 'feature_icon_size', $icon_selector );
		$this->generate_generic_style( 'feature_tooltip_bg_color', '.tippy-box[data-theme~="%%order_class%%"]', 'background-color' );
		$this->generate_generic_style( 'feature_tooltip_arrow_color', '.tippy-box[data-theme~="%%order_class%%"] .tippy-arrow' );
		$tooltip_fileds  = array_filter( $this->get_fields(), function ( $item, $key ) {
			return str_starts_with( $key, 'feature_text_tooltip_' );
		}, 1 );
		$tooltip_options = [];
		foreach ( $tooltip_fileds as $key => $tooltip_option ) {
			$tooltip_options[ str_replace( 'feature_text_tooltip_', '', $key ) ] = 'feature_text_tooltip_main_content' === $key ? preg_replace( [
				'/<\/p>/',
				'/<p>\s*<\/p>/',
				'/<p>\s*$/',
			], [ '', '', '' ], $this->{$key} ) : $this->{$key};
		}

		return sprintf( '<div class="%2$s" data-tooltip=\'%3$s\'>%1$s</div>', $this->item_content_by_keys( $keys, $parent_class ), $parent_class, wp_json_encode( $tooltip_options ) );
	}

	public function render_item_Icon() {
		$keys          = $this->get_item_fields( 'Icon' );
		$parent_class  = 'item-icon';
		$icon_selector = '%%order_class%% .item-icon .et-pb-icon';
		$this->generate_icon_style( 'item_icon', $icon_selector );
		$this->generate_generic_style( 'icon_color', $icon_selector );
		$this->generate_generic_style( 'image_icon_background_color', $icon_selector, 'background-color' );
		$this->generate_font_size( 'image_icon_width', $icon_selector );

		return sprintf( '<div class="%2$s %3$s">%1$s</div>', $this->item_content_by_keys( $keys, $parent_class ), $parent_class, $this->image_icon_alignment );
	}

	public function render_item_Image() {
		$selector = '%%order_class%% .item-image img';
		$this->generate_generic_style( 'image_icon_background_color', $selector, 'background-color' );
		$this->generate_styles(
			[
				'type'                            => 'width',
				'render_slug'                     => $this->slug,
				'base_attr_name'                  => 'image_icon_width',
				'css_property'                    => 'width',
				'selector'                        => $selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
			]
		);

		$img = sprintf( '<img src="%1$s" alt="%2$s"/>', $this->item_image, $this->item_image_alt );

		return sprintf( '<span class="item-image %2$s">%1$s</span>', $img, $this->image_icon_alignment );
	}

	public function render_item_Ribbon() {
		$ribbon_transform_x        = $this->ribbon_transform_x;
		$ribbon_transform_x_tablet = $this->ribbon_transform_x_tablet;
		$ribbon_transform_x_phone  = $this->ribbon_transform_x_phone;
		$ribbon_transform_y        = $this->ribbon_transform_y;
		$ribbon_transform_y_tablet = $this->ribbon_transform_y_tablet;
		$ribbon_transform_y_phone  = $this->ribbon_transform_y_phone;
		if ( $ribbon_transform_x && $ribbon_transform_y ) {
			ET_Builder_Element::set_style( $this->slug, [
				'selector'    => '%%order_class%%',
				'declaration' => sprintf( 'transform: translate(%1$s, %2$s);', $ribbon_transform_x, $ribbon_transform_y ),
			] );
		}
		if ( $ribbon_transform_x_tablet && $ribbon_transform_y_tablet ) {
			ET_Builder_Element::set_style( $this->slug, [
				'selector'    => '%%order_class%%',
				'declaration' => sprintf( 'transform: translate(%1$s, %2$s);', $ribbon_transform_x_tablet, $ribbon_transform_y_tablet ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			] );
		}

		if ( $ribbon_transform_x_phone && $ribbon_transform_y_phone ) {
			ET_Builder_Element::set_style( $this->slug, [
				'selector'    => '%%order_class%%',
				'declaration' => sprintf( 'transform: translate(%1$s, %2$s);', $ribbon_transform_x_phone, $ribbon_transform_y_phone ),
				'device'      => ET_Builder_Element::get_media_query( 'max_width_767' ),
			] );
		}

		$selectors = [
			'ribbon_spacing_'      => '%%order_class%% .item-ribbon .ribbon_text',
			'ribbon_icon_spacing_' => '%%order_class%% .item-ribbon .ribbon_icon',
		];
		$spaces    = [ 'margin', 'padding' ];
		foreach ( $selectors as $key => $selector ) {
			foreach ( $spaces as $space ) {
				$this->set_margin_padding_styles(
					[
						'render_slug' => $this->slug,
						'type'        => $space,
						'slug'        => "$key$space",
						'selector'    => $selector,
						'hover'       => "$selector:hover",
					]
				);
			}
		}

		$animation    = 'on' === $this->ribbon_animation ? 'difl_bounce_in ' : '';
		$content      = '';
		$ribbon_type  = esc_attr( $this->ribbon_type );
		$icon_enable  = 'text_icon' === $ribbon_type || 'icon_only' === $ribbon_type;
		$image_enable = 'image_only' === $ribbon_type || 'text_image' === $ribbon_type;
		$text_enable  = 'text_icon' === $ribbon_type || 'text_image' === $ribbon_type || 'text_only' === $ribbon_type;

		$icon_placement = '';

		if ( $icon_enable ) {
			$key            = 'ribbon_icon';
			$keys           = [
				$key => $this->get_fields()[ $key ],
			];
			$icon_selector  = '%%order_class%% .item-ribbon .ribbon_icon.et-pb-icon';
			$icon_placement = 'icon-' . $this->ribbon_icon_placement;
			$this->generate_icon_style( $key, $icon_selector );
			$this->generate_generic_style( 'ribbon_icon_color', $icon_selector );
			$this->generate_generic_style( 'ribbon_icon_bg_color', $icon_selector, 'background-color' );
			$this->generate_font_size( 'ribbon_icon_size', $icon_selector );
			$content .= $this->item_content_by_keys( $keys );
		}

		if ( $image_enable ) {
			$this->generate_generic_style( 'ribbon_image_width', '%%order_class%% .item-ribbon .ribbon-image', 'width' );
			$image_src = esc_attr( $this->ribbon_image );
			if ( ! empty( $image_src ) ) {
				$content .= sprintf( '<img src="%1$s" class="ribbon-image" alt="%2$s"/>', $image_src, esc_attr( $this->ribbon_image_alt ) );
			}
		}

		if ( $text_enable ) {
			$content .= sprintf( '<span class="ribbon_text %2$s">%1$s</span>', esc_html( $this->ribbon_text ), esc_attr( $this->ribbon_orientation ) );
		}

		return sprintf( '<div class="item-ribbon %2$s %3$s %4$s">%1$s</div>', $content, esc_attr( $this->ribbon_position ), $animation, $icon_placement );
	}

	public function render_item_Divider() {
		$selector = '%%order_class%% .item-divider';
		$this->df_process_bg( [
			'render_slug' => $this->slug,
			'slug'        => 'divider_color',
			'selector'    => $selector,
			'hover'       => "$selector:hover",
		] );

		$this->generate_styles(
			[
				'type'                            => 'divider_height',
				'render_slug'                     => $this->slug,
				'base_attr_name'                  => 'divider_height',
				'css_property'                    => 'height',
				'selector'                        => $selector,
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
			]
		);

		return '<span class="item-divider"></span>';
	}

	public function render_item_Button() {
		$badge_content    = '';
		$alignment        = $this->button_alignment ? $this->button_alignment : 'center';
		$alignment_phone  = $this->button_alignment_phone ? 'phone-'.$this->button_alignment_phone : 'center';
		$alignment_tablet = $this->button_alignment_tablet ? 'tablet-'.$this->button_alignment_tablet : 'center';
		if ( 'on' === $this->button_badge ) {
			$badge_selector = '%%order_class%% .item-button .button-badge';
			$this->df_process_bg( [
				'render_slug' => $this->slug,
				'slug'        => 'button_badge_bg',
				'selector'    => $badge_selector,
				'hover'       => "$badge_selector:hover",
			] );

			$spaces = [ 'margin', 'padding' ];
			foreach ( $spaces as $space ) {
				$this->set_margin_padding_styles(
					[
						'render_slug' => $this->slug,
						'type'        => $space,
						'slug'        => "button_badge_spacing_$space",
						'selector'    => $badge_selector,
						'hover'       => "$badge_selector:hover",
					]
				);
			}
			if ( 'on' === $this->button_badge_animation ) {
				wp_enqueue_style( 'df-animate-styles' );
			}

			$badge_animation = 'on' === $this->button_badge_animation ? 'difl_bounce_in ' : '';
			$badge_content   = sprintf( '<span class="button-badge %4$s %2$s %3$s" style="animation-duration: 7s; animation-iteration-count: infinite">%1$s</span>', $this->button_badge_text, $this->button_badge_position, $this->button_badge_position_vertically, $badge_animation );
		}

		return sprintf(
			'<div class="item-button %3$s %4$s %5$s %6$s">
					%2$s
					%1$s
                </div>', $this->get_rendered_button(), $badge_content, 'on' === $this->button_full_width ? 'full-width' : '', $alignment, $alignment_phone, $alignment_tablet );
	}

	protected function get_rendered_button() {
		$button_text   = $this->props['button_text'];
		$button_target = 'on' === $this->props['button_url_new_window'] ? 'on' : 'off';
		$button_icon   = $this->props['button_icon'];
		$button_link   = $this->props['button_url'];
		$button_rel    = $this->props['button_rel'];

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$output = $this->render_button( [
			'button_classname'   => [ 'difl-pricingtableitem-button' ],
			'button_custom'      => $this->props['custom_button'],
			'button_rel'         => $button_rel,
			'button_text'        => $button_text,
			'button_url'         => $button_link,
			'custom_icon'        => $custom_icon,
			'custom_icon_tablet' => $custom_icon_tablet,
			'custom_icon_phone'  => $custom_icon_phone,
			'has_wrapper'        => false,
			'url_new_window'     => $button_target,
		] );

		return $output;
	}

	public function render_item_Rating() {
		$rating           = '';
		$label            = '';
		$is_custom        = $this->rating_enable_custom_icon === 'on';
		$rating_icon_star = $is_custom ? et_pb_process_font_icon( $this->rating_icon ) : '☆';

		if ( $is_custom ) {
			ET_Builder_Element::set_style( $this->slug, [
				'selector'    => '%%order_class%% .item-rating .star span.df_rating_icon_fill::before',
				'declaration' => 'content: attr(data-icon) !important;',
			] );

			ET_Builder_Element::set_style( $this->slug, [
				'selector'    => '%%order_class%% .item-rating .star span.df_rating_icon_fill::after',
				'declaration' => 'display: none !important;',
			] );
			$this->generate_generic_style( 'rating_color', '%%order_class%% .item-rating span.et-pb-icon:not(.df_rating_icon_empty), %%order_class%% .item-rating .df_rating_icon_fill::before' );
			$this->generate_generic_style( 'rating_color_inactive', '%%order_class%% .item-rating .df_rating_icon_empty' );
		} else {
			$this->generate_generic_style( 'rating_color', '%%order_class%% .item-rating span.et-pb-icon:not(.df_rating_icon_empty), %%order_class%% .item-rating .df_rating_icon_fill::before' );
			$this->generate_generic_style( 'rating_color_inactive', '%%order_class%% .item-rating .df_rating_icon_empty' );
		}

		$this->generate_icon_style( 'rating_icon', '%%order_class%% .item-rating .et-pb-icon' );
		$this->generate_generic_style( 'rating_icon_label_gap', '%%order_class%% .item-rating', 'gap' );
		$this->generate_generic_style( 'rating_icon_size', '%%order_class%% .item-rating span.et-pb-icon', 'font-size' );
		$this->generate_generic_style( 'rating_icon_gap', '%%order_class%% .item-rating span.et-pb-icon + span.et-pb-icon', 'margin-inline-start' );

		$rating_value = $this->rating_number;
		$get_float    = explode( '.', $rating_value );

		$rating_icon         = '';
		$rating_active_class = '';

		for ( $i = 1; $i <= 5; $i ++ ) {
			if ( ! isset( $rating_value ) ) {
				$rating_active_class = '';
			} else if ( $i <= $rating_value ) {
				$rating_active_class = 'df_rating_icon_fill';
			} else if ( $i == $get_float[0] + 1 && isset( $get_float[1] ) && $get_float[1] != '' && $get_float[1] != 0 ) {
				$rating_active_class = 'df_rating_icon_fill df_rating_icon_empty df_fill_' . $get_float[1];
			} else {
				$rating_active_class = 'df_rating_icon_empty';
			}
			$rating_icon .= '<span class="et-pb-icon ' . $rating_active_class . '" data-icon="' . $rating_icon_star . '">' . $rating_icon_star . '</span>';
		}
		$label_text = $this->rating_label;
		if ( $label_text ) {
			$label .= sprintf( '<span class="label">%1$s</span>', $label_text );
		}

		return sprintf( '<div class="item-rating %3$s"><span class="star">%1$s</span><span class="label">%2$s</span></div>', $rating_icon, $label, $this->rating_alignment );
	}

	public function render( $attrs, $content, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
		$this->handle_fa_icon();
		$type = $this->item_type;

		$method = "render_item_{$type}";

		return $this->$method();
	}
}

new DIFL_PricingTableItem();