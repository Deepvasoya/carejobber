<?php

class DIFL_TableOfContents extends ET_Builder_Module {
	public $icon_path;
	use DF_UTLS;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => 'diviflash.com',
	];

	public function init() {
		$this->name       = esc_html__( 'Table Of Contents', 'divi_flash' );
		$this->slug       = 'difl_table_of_contents';
		$this->icon_path  = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/toc.svg';
		$this->vb_support = 'on';
	}

	public function get_settings_modal_toggles() {
		$toggles = [];

		$toggles['general'] = [
			'toggles' => [
				'main_content'     => esc_html__( 'Content', 'divi_flash' ),
				'content_settings' => esc_html__( 'Content Settings', 'divi_flash' ),
				'title_settings'   => esc_html__( 'Title Settings', 'divi_flash' ),
				'marker_settings'  => [
					'title'             => __( 'Marker Settings', 'divi_flash' ),
					'tabbed_subtoggles' => true,
					'sub_toggles'       => [
						'h1' => [
							'name' => __( 'H1', 'divi_flash' ),
							'icon' => 'text-h1',
						],
						'h2' => [
							'name' => __( 'H2', 'divi_flash' ),
							'icon' => 'text-h2',
						],
						'h3' => [
							'name' => __( 'H3', 'divi_flash' ),
							'icon' => 'text-h3',
						],
						'h4' => [
							'name' => __( 'H4', 'divi_flash' ),
							'icon' => 'text-h4',
						],
						'h5' => [
							'name' => __( 'H5', 'divi_flash' ),
							'icon' => 'text-h5',
						],
						'h6' => [
							'name' => __( 'H6', 'divi_flash' ),
							'icon' => 'text-h6',
						],
					],
				],
			],
		];

		$toggles['advanced'] = [
			'toggles' => [
				'header_style'  => esc_html__( 'Header', 'divi_flash' ),
				'title_style'   => esc_html__( 'Header Title', 'divi_flash' ),
				'heading'       => [
					'title'             => esc_html__( 'Heading Style', 'divi_flash' ),
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
				'content_style' => esc_html__( 'Content Style', 'divi_flash' ),
				'active_style'  => esc_html__( 'Active Style', 'divi_flash' ),
			],
		];

		return $toggles;
	}

	public function get_fields() {
		$general = [
			'admin_label' => [
				'label'            => esc_html__( 'Admin Label', 'divi_flash' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'admin_label',
				'default_on_front' => '',
			],
		];

		$content = [
			'title'                      => [
				'label'           => esc_html__( 'Title', 'divi_flash' ),
				'type'            => 'text',
				'default'         => 'Table Of Content',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			],
			'title_tag'                  => [
				'default'          => 'div',
				'default_on_front' => 'div',
				'label'            => esc_html__( 'Title Tag', 'divi_flash' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'div' => esc_html__( 'div', 'divi_flash' ),
					'h1'  => esc_html__( 'h1', 'divi_flash' ),
					'h2'  => esc_html__( 'h2', 'divi_flash' ),
					'h3'  => esc_html__( 'h3', 'divi_flash' ),
					'h4'  => esc_html__( 'h4', 'divi_flash' ),
					'h5'  => esc_html__( 'h5', 'divi_flash' ),
					'h6'  => esc_html__( 'h6', 'divi_flash' ),
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
			],
			'heading_tags'               => [
				'label'            => __( 'Select Heading Tags', 'divi_flash' ),
				'type'             => 'multiple_checkboxes',
				'option_category'  => 'configuration',
				'description'      => __( 'Choose which heading levels should be included in the table of contents.', 'divi_flash' ),
				'options'          => [
					'h1' => 'H1 Headings',
					'h2' => 'H2 Headings',
					'h3' => 'H3 Headings',
					'h4' => 'H4 Headings',
					'h5' => 'H5 Headings',
					'h6' => 'H6 Headings',
				],
				'default'          => 'on|on|off|off|off|off',
				'default_on_front' => 'on|on|off|off|off|off',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
			],
			'headings_exclude_by_class'  => [
				'label'       => __( 'Exclude Heading Based on Class', 'divi_flash' ),
				'description' => __( 'Add CSS Class name with comma separated If you want to exclude heading from the table. start with dot now allowed', 'divi_flash' ),
				'type'        => 'text',
				'default'     => '',
				'tab_slug'    => 'general',
				'toggle_slug' => 'main_content',
			],
			'container_exclude_by_class' => [
				'label'       => __( 'Exclude Container Based on Class', 'divi_flash' ),
				'description' => __( 'Add CSS Class name with comma separated If you want to exclude section from the table. start with dot now allowed', 'divi_flash' ),
				'type'        => 'text',
				'default'     => '',
				'tab_slug'    => 'general',
				'toggle_slug' => 'main_content',
			],
			'minimum_number_of_headings' => [
				'label'            => __( 'Hide Module', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'2' => __( 'If Less than 2 Headings Found', 'divi_flash' ),
					'3' => __( 'If Less than 3 Headings Found', 'divi_flash' ),
					'4' => __( 'If Less than 4 Headings Found', 'divi_flash' ),
					'5' => __( 'If Less than 5 Headings Found', 'divi_flash' ),
					'6' => __( 'If Less than 6 Headings Found', 'divi_flash' ),
				],
				'default'          => '2',
				'default_on_front' => '2',
				'option_category'  => 'basic_option',
				'description'      => __( 'Minimum number of headings on the current content to show this module.', 'divi_flash' ),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
			],
		];

		$content_settings = [
			'hierarchical_view'     => [
				'label'            => esc_html__( 'Hierarchical View', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'on',
				'default_on_front' => 'on',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'      => 'content_settings',
				'tab_slug'         => 'general',
			],
			'offset'                => [
				'label'            => esc_html__( 'Heading Top Offset', 'divi_flash' ),
				'description'      => __( 'Offset from page top to current heading', 'divi_flash' ),
				'type'             => 'range',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'default'          => '80px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'responsive'       => true,
				'mobile_options'   => true,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '400',
					'step' => '1',
				],
				'show_if'          => [
					'hierarchical_view' => 'on',
				],
			],
			'highlight_active_link' => [
				'label'            => esc_html__( 'Highlight Active Link', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'on',
				'default_on_front' => 'on',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'      => 'content_settings',
				'tab_slug'         => 'general',
			],
			'marker_type'           => [
				'label'            => __( 'Marker', 'divi_flash' ),
				'type'             => 'select',
				'default'          => 'number',
				'default_on_front' => 'number',
				'options'          => [
					'number'          => __( 'Native Number', 'divi_flash' ),
					'number_with_dot' => __( 'Number with Dot', 'divi_flash' ),
					'icon'            => __( 'Icon', 'divi_flash' ),
					'none'            => __( 'None', 'divi_flash' ),
				],
				'option_category'  => 'basic_option',
				'description'      => __( 'List marker type', 'divi_flash' ),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'content_settings',
			],
		];

		$headings        = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
		$marker_settings = [];
		foreach ( $headings as $heading ) {
			$sub_toggle         = $heading;
			$heading_background = [
				"heading_bg_$heading" => [
					'label'          => esc_html__( 'Background Color', 'divi_flash' ),
					'type'           => 'color-alpha',
					'mobile_options' => false,
					'responsive'     => false,
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'heading',
					'sub_toggle'     => $sub_toggle,
				],
			];
			$heading_spacing    = $this->add_margin_padding( [
				'title'       => esc_html__( '', 'divi_flash' ),
				'key'         => "heading_spacing_$heading",
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'heading',
				'sub_toggle'  => $sub_toggle,
			] );
			$marker_settings    += [
				"marker_icon_$heading"               => [
					'label'           => esc_html__( 'Select Icon', 'divi_flash' ),
					'type'            => 'select_icon',
					'option_category' => 'basic_option',
					'class'           => [ 'et-pb-font-icon' ],
					'tab_slug'        => 'general',
					'toggle_slug'     => 'marker_settings',
					'sub_toggle'      => $sub_toggle,
					'show_if'         => [
						'marker_type' => 'icon',
					],
				],
				"marker_icon_size_$heading"          => [
					'label'            => esc_html__( 'Icon Size', 'divi_flash' ),
					'type'             => 'range',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'marker_settings',
					'sub_toggle'       => $sub_toggle,
					'default'          => '10px',
					'default_unit'     => 'px',
					'default_on_front' => '',
					'responsive'       => false,
					'mobile_options'   => false,
					'range_settings'   => [
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					],
					'show_if'          => [
						'marker_type' => 'icon',
					],
				],
				"marker_icon_color_$heading"         => [
					'label'          => esc_html__( 'Icon Color', 'divi_flash' ),
					'type'           => 'color-alpha',
					'mobile_options' => false,
					'responsive'     => false,
					'tab_slug'       => 'general',
					'toggle_slug'    => 'marker_settings',
					'sub_toggle'     => $sub_toggle,
					'show_if'        => [
						'marker_type' => 'icon',
					],
				],
				"marker_icon_space_heading_$heading" => [
					'label'            => esc_html__( 'Space Between Heading', 'divi_flash' ),
					'type'             => 'range',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'marker_settings',
					'sub_toggle'       => $sub_toggle,
					'default'          => '18px',
					'default_unit'     => 'px',
					'default_on_front' => '18px',
					'responsive'       => false,
					'mobile_options'   => false,
					'range_settings'   => [
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					],
					'show_if'          => [
						'marker_type' => 'icon',
					],
				],
			];

			$marker_settings += $heading_spacing;
			$marker_settings += $heading_background;

		}

		$title_settings = [
			'full_width_header'       => [
				'label'            => esc_html__( 'Enable Full Header', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'off',
				'default_on_front' => 'off',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'      => 'title_settings',
				'tab_slug'         => 'general',
			],
			'title_icon_gap'          => [
				'label'            => esc_html__( 'Gap Between Icon', 'divi_flash' ),
				'type'             => 'range',
				'default'          => '28px',
				'default_unit'     => 'px',
				'default_on_front' => '28px',
				'responsive'       => false,
				'mobile_options'   => false,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				],
				'toggle_slug'      => 'title_settings',
				'tab_slug'         => 'general',
				'show_if_not'      => [
					'full_width_header' => 'on',
				],
			],
			'collapsible_toc'         => [
				'label'            => esc_html__( 'Collapsible TOC', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'on',
				'default_on_front' => 'on',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'      => 'title_settings',
				'tab_slug'         => 'general',
			],
			'scrolling_speed'         => [
				'label'          => esc_html__( 'Expand / Collapse speed in (ms)', 'divi_flash' ),
				'type'           => 'range',
				'default'        => '200',
				'allowed_units'  => [ 'ms' ],
				'range_settings' => [
					'min'  => '0',
					'max'  => '2000',
					'step' => '50',
				],
				'validate_unit'  => false,
				'toggle_slug'    => 'title_settings',
				'tab_slug'       => 'general',
				'responsive'     => false,
				'mobile_options' => false,
				'show_if'        => [
					'collapsible_toc' => 'on',
				],
			],
			'default_collapse_state'  => [
				'label'            => __( 'Default Stage', 'divi_flash' ),
				'type'             => 'select',
				'default'          => 'expanded',
				'default_on_front' => 'expanded',
				'options'          => [
					'expanded'  => __( 'Expanded', 'divi_flash' ),
					'collapsed' => __( 'Collapsed', 'divi_flash' ),
				],
				'option_category'  => 'basic_option',
				'description'      => __( 'Default collapse stage', 'divi_flash' ),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'title_settings',
				'show_if'          => [
					'collapsible_toc' => 'on',
				],
			],
			'collapsible_with_sticky' => [
				'label'            => esc_html__( 'Collapse When Sticky', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'off',
				'default_on_front' => 'off',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'      => 'title_settings',
				'tab_slug'         => 'general',
				'show_if'          => [
					'collapsible_toc' => 'on',
				],
				'show_if_not'      => [
					'default_collapse_state' => 'collapsed',
				],
			],
			'title_icon'              => [
				'label'            => esc_html__( 'Title Icon', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'off',
				'default_on_front' => 'off',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'      => 'title_settings',
				'tab_slug'         => 'general',
				'show_if'          => [
					'collapsible_toc' => 'on',
				],
			],
			'expand_icon'             => [
				'label'           => esc_html__( 'Expand Icon', 'divi_flash' ),
				'type'            => 'select_icon',
				'option_category' => 'basic_option',
				'class'           => [ 'et-pb-font-icon' ],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'title_settings',
				'show_if'         => [
					'title_icon'      => 'on',
					'collapsible_toc' => 'on',
				],
			],
			'expand_icon_color'       => [
				'label'          => esc_html__( 'Expand Icon Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => false,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'title_settings',
				'show_if'        => [
					'title_icon'      => 'on',
					'collapsible_toc' => 'on',
				],
			],
			'expand_icon_size'        => [
				'label'            => esc_html__( 'Expand Icon Size', 'divi_flash' ),
				'type'             => 'range',
				'default'          => '28px',
				'default_unit'     => 'px',
				'default_on_front' => '28px',
				'responsive'       => false,
				'mobile_options'   => false,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'title_settings',
				'show_if'          => [
					'title_icon'      => 'on',
					'collapsible_toc' => 'on',
				],
			],
			'collapse_icon'           => [
				'label'           => esc_html__( 'Collapse Icon', 'divi_flash' ),
				'type'            => 'select_icon',
				'option_category' => 'basic_option',
				'class'           => [ 'et-pb-font-icon' ],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'title_settings',
				'show_if'         => [
					'title_icon'      => 'on',
					'collapsible_toc' => 'on',
				],
			],
			'collapse_icon_color'     => [
				'label'          => esc_html__( 'Collapse Icon Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => false,
				'tab_slug'       => 'general',
				'toggle_slug'    => 'title_settings',
				'show_if'        => [
					'title_icon'      => 'on',
					'collapsible_toc' => 'on',
				],
			],
			'collapse_icon_size'      => [
				'label'            => esc_html__( 'Collapse Icon Size', 'divi_flash' ),
				'type'             => 'range',
				'default'          => '28px',
				'default_unit'     => 'px',
				'default_on_front' => '28px',
				'responsive'       => false,
				'mobile_options'   => false,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'         => 'general',
				'toggle_slug'      => 'title_settings',
				'show_if'          => [
					'title_icon'      => 'on',
					'collapsible_toc' => 'on',
				],
			],
			'collapse_icon_only'      => [
				'label'            => esc_html__( 'Collapse Icon Only', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'off',
				'default_on_front' => 'off',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'toggle_slug'      => 'title_settings',
				'tab_slug'         => 'general',
				'show_if'          => [
					'collapsible_toc' => 'on',
					'title_icon'      => 'on',
				],
			],
		];

		$style = $this->get_style_fields();

		return array_merge( $general, $content, $content_settings, $marker_settings, $title_settings, $style );
	}

	protected function get_style_fields() {
		$header_spacing = $this->add_margin_padding( [
			'title'       => esc_html__( 'Header', 'divi_flash' ),
			'key'         => 'header_spacing',
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'header_style',
		] );

		$header_style = array_merge( [
			'header_bg_color' => [
				'label'          => esc_html__( 'Title Background', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => false,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'header_style',
			],
		], $header_spacing );

		$content_spacing = $this->add_margin_padding( [
			'title'       => esc_html__( '', 'divi_flash' ),
			'key'         => 'content_spacing',
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'content_style',
		] );

		$content_style = array_merge( [
			'use_content_height' => [
				'label'            => esc_html__( 'Enable Content Height', 'divi_flash' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'default'          => 'off',
				'default_on_front' => 'off',
				'options'          => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'content_style',
			],
			'content_height'     => [
				'label'            => esc_html__( 'Content Height', 'divi_flash' ),
				'type'             => 'range',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'content_style',
				'default'          => '300px',
				'default_on_front' => '300px',
				'default_unit'     => 'px',
				'responsive'       => true,
				'mobile_options'   => true,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '1000',
					'step' => '1',
				],
				'show_if'          => [ 'use_content_height' => 'on' ],
			],
			'body_bg_color'      => [
				'label'          => esc_html__( 'Content Background Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => false,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'content_style',
			],
		], $content_spacing );

		$active_spacing = $this->add_margin_padding( [
			'title'       => esc_html__( '', 'divi_flash' ),
			'key'         => 'active_spacing',
			'option'      => 'padding',
			'tab_slug'    => 'advanced',
			'show_if'     => [
				'highlight_active_link' => 'on',
			],
			'toggle_slug' => 'active_style',
			'hover'       => 'false',
		] );

		$active_style = array_merge( [
			'active_background_color'  => [
				'label'          => esc_html__( 'Active Background Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => false,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'active_style',
				'show_if'        => [
					'highlight_active_link' => 'on',
				],
			],
			'active_link_color'        => [
				'label'          => esc_html__( 'Active Link Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => false,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'active_style',
				'show_if'        => [
					'highlight_active_link' => 'on',
				],
			],
			'active_icon_marker_color' => [
				'label'          => esc_html__( 'Active Link Marker Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'mobile_options' => false,
				'responsive'     => false,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'active_style',
				'show_if'        => [
					'highlight_active_link' => 'on',
				],
			],
		], $active_spacing, [
				'active_link_border_on_parent' => [
					'label'            => esc_html__( 'Show Border With Parent Continer', 'divi_flash' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'basic_option',
					'default'          => 'on',
					'default_on_front' => 'on',
					'options'          => [
						'off' => esc_html__( 'No', 'divi_flash' ),
						'on'  => esc_html__( 'Yes', 'divi_flash' ),
					],
					'toggle_slug'      => 'active_style',
					'tab_slug'         => 'advanced',
					'show_if'          => [
						'highlight_active_link' => 'on',
					],
				],
			]
		);

		return array_merge( $header_style, $content_style, $active_style );
	}

	public function get_advanced_fields_config() {
		$advanced_fields                       = [];
		$advanced_fields['text']               = false;
		$advanced_fields['link_options']       = false;
		$advanced_fields['fonts']['title']     = [
			'label'       => esc_html__( 'Title', 'divi_flash' ),
			'css'         => [
				'main'       => '%%order_class%% .heading_container .title',
				'hover'      => '%%order_class%% .heading_container .title:hover',
				'text_align' => '%%order_class%% .heading_container .title',
				'important'  => true,
			],
			'text_align'  => [
				'default'          => 'left',
				'default_on_front' => 'left',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'title_style',
		];
		$advanced_fields['fonts']['heading_1'] = [
			'label'       => esc_html__( 'Heading', 'divi_flash' ),
			'font_size'   => [
				'default' => '24px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '%%order_class%% .body_container .difl_heading_level_1 > li > .toc-li-wrapper > a',
				'hover' => '%%order_class%% .body_container .difl_heading_level_1 > li > .toc-li-wrapper > a:hover',
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h1',
		];
		$advanced_fields['fonts']['heading_2'] = [
			'label'       => esc_html__( 'Heading 2', 'divi_flash' ),
			'font_size'   => [
				'default' => '20px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '%%order_class%% .body_container .difl_heading_level_2 > li > .toc-li-wrapper > a',
				'hover' => '%%order_class%% .body_container .difl_heading_level_2 > li > .toc-li-wrapper > a:hover',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'heading',
			'sub_toggle'  => 'h2',
		];
		$advanced_fields['fonts']['heading_3'] = [
			'label'       => esc_html__( 'Heading 3', 'divi_flash' ),
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
				'main'  => '%%order_class%% .body_container .difl_heading_level_3 > li > .toc-li-wrapper > a',
				'hover' => '%%order_class%% .body_container .difl_heading_level_3 > li > .toc-li-wrapper > a:hover',
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h3',
		];
		$advanced_fields['fonts']['heading_4'] = [
			'label'       => esc_html__( 'Heading 4', 'divi_flash' ),
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
				'main'  => '%%order_class%% .body_container .difl_heading_level_4 > li > .toc-li-wrapper > a',
				'hover' => '%%order_class%% .body_container .difl_heading_level_4 > li > .toc-li-wrapper > a:hover',
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h4',
		];
		$advanced_fields['fonts']['heading_5'] = [
			'label'       => esc_html__( 'Heading 5', 'divi_flash' ),
			'font_size'   => [
				'default' => '12px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '%%order_class%% .body_container .difl_heading_level_5 > li > .toc-li-wrapper > a',
				'hover' => '%%order_class%% .body_container .difl_heading_level_5 > li > .toc-li-wrapper > a:hover',
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h5',
		];
		$advanced_fields['fonts']['heading_6'] = [
			'label'       => esc_html__( 'Heading 6', 'divi_flash' ),
			'font_size'   => [
				'default' => '10px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '%%order_class%% .body_container .difl_heading_level_6 > li > .toc-li-wrapper > a',
				'hover' => '%%order_class%% .body_container .difl_heading_level_6 > li > .toc-li-wrapper > a:hover',
			],
			'toggle_slug' => 'heading',
			'tab_slug'    => 'advanced',
			'sub_toggle'  => 'h6',
		];

		$advanced_fields['fonts']['heading_font_1'] = [
			'label'       => esc_html__( 'Heading', 'divi_flash' ),
			'font_size'   => [
				'default' => '24px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '%%order_class%% .body_container .difl_heading_level_1 > li > .toc-li-wrapper .native-number, %%order_class%% .body_container .difl_heading_level_1 > li > .toc-li-wrapper .number-with-dot',
				'hover' => '%%order_class%% .body_container .difl_heading_level_1 > li > .toc-li-wrapper .native-number:hover, %%order_class%% .body_container .difl_heading_level_1 > li > .toc-li-wrapper .number-with-dot:hover',
			],
			'toggle_slug' => 'marker_settings',
			'tab_slug'    => 'general',
			'show_if'     => [
				'marker_type' => 'number',
			],
			'sub_toggle'  => 'h1',
		];
		$advanced_fields['fonts']['heading_font_2'] = [
			'label'       => esc_html__( 'Heading 2', 'divi_flash' ),
			'font_size'   => [
				'default' => '20px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '%%order_class%% .body_container .difl_heading_level_2 > li > .toc-li-wrapper .native-number, %%order_class%% .body_container .difl_heading_level_2 > li > .toc-li-wrapper .number-with-dot',
				'hover' => '%%order_class%% .body_container .difl_heading_level_2 > li > .toc-li-wrapper .native-number:hover, %%order_class%% .body_container .difl_heading_level_2 > li > .toc-li-wrapper .number-with-dot:hover',
			],
			'toggle_slug' => 'marker_settings',
			'tab_slug'    => 'general',
			'show_if'     => [
				'marker_type' => 'number',
			],
			'sub_toggle'  => 'h2',
		];
		$advanced_fields['fonts']['heading_font_3'] = [
			'label'       => esc_html__( 'Heading 3', 'divi_flash' ),
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
				'main'  => '%%order_class%% .body_container .difl_heading_level_3 > li > .toc-li-wrapper .native-number, %%order_class%% .body_container .difl_heading_level_3 > li > .toc-li-wrapper .number-with-dot',
				'hover' => '%%order_class%% .body_container .difl_heading_level_3 > li > .toc-li-wrapper .native-number:hover, %%order_class%% .body_container .difl_heading_level_3 > li > .toc-li-wrapper .number-with-dot:hover',
			],
			'toggle_slug' => 'marker_settings',
			'tab_slug'    => 'general',
			'show_if'     => [
				'marker_type' => 'number',
			],
			'sub_toggle'  => 'h3',
		];
		$advanced_fields['fonts']['heading_font_4'] = [
			'label'       => esc_html__( 'Heading 4', 'divi_flash' ),
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
				'main'  => '%%order_class%% .body_container .difl_heading_level_4 > li > .toc-li-wrapper .native-number, %%order_class%% .body_container .difl_heading_level_4 > li > .toc-li-wrapper .number-with-dot',
				'hover' => '%%order_class%% .body_container .difl_heading_level_4 > li > .toc-li-wrapper .native-number:hover, %%order_class%% .body_container .difl_heading_level_4 > li > .toc-li-wrapper .number-with-dot:hover',
			],
			'toggle_slug' => 'marker_settings',
			'tab_slug'    => 'general',
			'show_if'     => [
				'marker_type' => 'number',
			],
			'sub_toggle'  => 'h4',
		];
		$advanced_fields['fonts']['heading_font_5'] = [
			'label'       => esc_html__( 'Heading 5', 'divi_flash' ),
			'font_size'   => [
				'default' => '12px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '%%order_class%% .body_container .difl_heading_level_5 > li > .toc-li-wrapper .native-number, %%order_class%% .body_container .difl_heading_level_5 > li > .toc-li-wrapper .number-with-dot',
				'hover' => '%%order_class%% .body_container .difl_heading_level_5 > li > .toc-li-wrapper .native-number:hover, %%order_class%% .body_container .difl_heading_level_5 > li > .toc-li-wrapper .number-with-dot:hover',
			],
			'toggle_slug' => 'marker_settings',
			'tab_slug'    => 'general',
			'show_if'     => [
				'marker_type' => 'number',
			],
			'sub_toggle'  => 'h5',
		];
		$advanced_fields['fonts']['heading_font_6'] = [
			'label'       => esc_html__( 'Heading 6', 'divi_flash' ),
			'font_size'   => [
				'default' => '10px',
			],
			'font_weight' => [
				'default' => '500',
			],
			'line_height' => [
				'default' => '1.7',
			],
			'css'         => [
				'main'  => '%%order_class%% .body_container .difl_heading_level_6 > li > .toc-li-wrapper .native-number, %%order_class%% .body_container .difl_heading_level_6 > li > .toc-li-wrapper .number-with-dot',
				'hover' => '%%order_class%% .body_container .difl_heading_level_6 > li > .toc-li-wrapper .native-number:hover, %%order_class%% .body_container .difl_heading_level_6 > li > .toc-li-wrapper .number-with-dot:hover',
			],
			'toggle_slug' => 'marker_settings',
			'tab_slug'    => 'general',
			'show_if'     => [
				'marker_type' => 'number',
			],
			'sub_toggle'  => 'h6',
		];

		$advanced_fields['borders'] = [
			'default' => true,
		];

		$advanced_fields['borders'] ['title_border']   = [
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'header_style',
			'css'         => [
				'main' => [
					'border_radii'        => '%%order_class%% .heading_container',
					'border_radii_hover'  => '%%order_class%% .heading_container:hover',
					'border_styles'       => '%%order_class%% .heading_container',
					'border_styles_hover' => '%%order_class%% .heading_container:hover',
				],
			],
			'important'   => 'all',
		];
		$advanced_fields['borders'] ['content_border'] = [
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'content_style',
			'css'         => [
				'main' => [
					'border_radii'        => '%%order_class%% .body_container',
					'border_radii_hover'  => '%%order_class%% .body_container:hover',
					'border_styles'       => '%%order_class%% .body_container',
					'border_styles_hover' => '%%order_class%% .body_container:hover',
				],
			],
			'important'   => 'all',
		];
		$advanced_fields['borders'] ['active_style']   = [
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'active_style',
			'show_if'     => [
				'highlight_active_link' => 'on',
			],
			'css'         => [
				'main' => [
					'border_radii'        => '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li > .toc-li-wrapper:has( > a.active)',
					'border_radii_hover'  => '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li > .toc-li-wrapper:has( > a.active):hover',
					'border_styles'       => '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li > .toc-li-wrapper:has( > a.active)',
					'border_styles_hover' => '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li > .toc-li-wrapper:has( > a.active):hover',
				],
			],
			'important'   => 'all',
		];

		$advanced_fields['box_shadow'] ['active_style'] = [
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'active_style',
			'css'         => [
				'main'  => '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li .toc-li-wrapper:has( > a.active)',
				'hover' => '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li .toc-li-wrapper:has( > a.active):hover',
			],
			'important'   => 'all',
		];


		$headings = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
		foreach ( $headings as $heading ) {
			$sub_toggle                                            = $heading;
			$level                                                 = str_replace( 'h', '', $heading );
			$advanced_fields['borders']["heading_border_$heading"] = [
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'heading',
				'sub_toggle'  => $sub_toggle,
				'css'         => [
					'main' => [
						'border_radii'        => "%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul.difl_heading_level_$level > li > .toc-li-wrapper",
						'border_radii_hover'  => "%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul.difl_heading_level_$level > li:hover > .toc-li-wrapper",
						'border_styles'       => "%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul.difl_heading_level_$level > li > .toc-li-wrapper",
						'border_styles_hover' => "%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul.difl_heading_level_$level > li:hover > .toc-li-wrapper",
					],
				],
				'important'   => 'all',
			];

			$advanced_fields['box_shadow']["heading_shadow_$heading"] = [
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'heading',
				'sub_toggle'  => $sub_toggle,
				'css'         => [
					'main'  => "%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul.difl_heading_level_$level > li > .toc-li-wrapper",
					'hover' => "%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul.difl_heading_level_$level:hover > li > .toc-li-wrapper",
				],
				'important'   => 'all',
			];
		}

		return $advanced_fields;
	}

	public function &__get( $name ) {
		if ( array_key_exists( $name, $this->props ) ) {
			$value = $this->props[ $name ];
			if ( str_contains( $value, '|||' ) && str_starts_with( $name, 'active_spacing_' ) ) {
				$value = et_builder_get_element_style_css( $value, 'padding' );
			}

			return $value;
		}

		throw new Exception( sprintf( 'Property %s does not exist', $name ) );
	}

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
				'important'                       => true,
			]
		);
	}

	public function render_title_icon() {
		if ( empty( $this->props['title_icon'] ) || $this->props['title_icon'] !== 'on' ) {
			return null;
		}

		$expand_icon   = '%%order_class%% .heading_container .expand_icon';
		$collapse_icon = '%%order_class%% .heading_container .collapse_icon';
		$this->generate_icon_style( 'expand_icon', $expand_icon );
		$this->generate_icon_style( 'collapse_icon', $collapse_icon );
		$this->generate_font_size( 'expand_icon_size', $expand_icon );
		$this->generate_font_size( 'collapse_icon_size', $collapse_icon );
		$this->generate_generic_style( 'expand_icon_color', $expand_icon );
		$this->generate_generic_style( 'collapse_icon_color', $collapse_icon );

		$expand_icon = sprintf(
			'<span class="et-pb-icon expand_icon">%s</span>',
			et_pb_process_font_icon( $this->expand_icon )
		);

		$collapse_icon = sprintf(
			'<span class="et-pb-icon collapse_icon">%s</span>',
			et_pb_process_font_icon( $this->collapse_icon )
		);

		return sprintf(
			'<div class="icon">%s%s</div>',
			$expand_icon,
			$collapse_icon
		);
	}

	protected function apply_spacing() {
		$headings  = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
		$selectors = [
			'header_spacing_'  => '%%order_class%% .heading_container',
			'content_spacing_' => '%%order_class%% .body_container',
			'active_spacing_'  => '%%order_class%% .body_container ul.difl--toc--ul li > .toc-li-wrapper:has( > a.active)',
		];

		foreach ( $headings as $heading ) {
			$level                                    = str_replace( 'h', '', $heading );
			$selectors["heading_spacing_{$heading}_"] = ".difl_table_of_contents .body_container .difl_heading_level_$level > li";
			$this->generate_generic_style( "heading_bg_$heading", "%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul.difl_heading_level_$level > li > .toc-li-wrapper", 'background-color' );
		}

		$spaces = [ 'margin', 'padding' ];
		foreach ( $selectors as $key => $selector ) {
			foreach ( $spaces as $space ) {

				if ( ! array_key_exists( "$key$space", $this->props ) ) {
					continue;
				}

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
	}

	protected function get_headings() {
		$this->generate_generic_style( 'header_bg_color', '%%order_class%% .heading_container', 'background-color' );

		if ( 'on' === $this->props['use_content_height'] ) {
			$this->generate_generic_style( 'content_height', '%%order_class%% .body_container', 'height' );
		}

		$title_tag = $this->title_tag;
		$title     = $this->title;

		return sprintf(
			'<div class="heading_container">
        <%1$s class="title">%2$s</%1$s>
        %3$s
    </div>', esc_html( $title_tag ),
			esc_html( $title ),
			$this->render_title_icon()
		);
	}

	public function render( $attrs, $content, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
		static $is_script_enqueued = false;
		if ( ! $is_script_enqueued ) {
			add_action( 'wp_print_footer_scripts', [ $this, 'enqueue_script' ], PHP_INT_MAX );
			$is_script_enqueued = true;
		}
		wp_enqueue_script( 'jquery' );
		$this->apply_spacing();

		if ( 'on' === $this->props['highlight_active_link'] ) {
			$this->generate_generic_style( 'active_link_color', '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul a.active' );
			$this->generate_generic_style( 'active_icon_marker_color', '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul .toc-li-wrapper:has( > a.active) span' );
			$this->generate_generic_style( 'active_background_color', '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li .toc-li-wrapper:has( > a.active)', 'background-color' );
		}

		if ( 'off' === $this->props['full_width_header'] ) {
			$this->generate_generic_style( 'title_icon_gap', '.difl_table_of_contents .difl_toc_main_container .heading_container', 'gap' );
		}

		$this->generate_generic_style( 'body_bg_color', '%%order_class%% .difl_toc_main_container .body_container', 'background-color' );
		$settings = $this->get_settings();

		$output = '';
		$output .= $this->get_headings();

		$collapse_sticky    = 'on' === $this->props['collapsible_toc'] && 'on' === $this->props['collapsible_with_sticky'] ? 'collapse_sticky' : '';
		$full_width_header  = 'on' === $this->props['full_width_header'] && 'on' === $this->props['full_width_header'] ? 'full_width_header' : '';
		$collapse_icon_only = 'on' === $this->props['collapse_icon_only'] && 'on' === $this->props['collapse_icon_only'] ? 'collapse_icon_only' : '';

		$output .= sprintf(
			'<div class="body_container %1$s  %3$s %4$s %5$s" data-settings=\'%2$s\'></div>',
			! empty( $this->props['marker_type'] ) ? esc_attr( $this->props['marker_type'] ) : 'number',
			$settings,
			'on' === $this->props['use_content_height'] ? ' height_enable' : '',
			'off' === $this->props['hierarchical_view'] ? ' non_hierarchical' : '',
			$collapse_sticky
		);

		return sprintf( '<div class="difl_toc_main_container %2$s %3$s %4$s">%1$s</div>', $output, esc_attr( $this->default_collapse_state ), $full_width_header, $collapse_icon_only );
	}

	protected function get_settings() {
		$allowed_settings = [
			'heading_tags',
			'headings_exclude_by_class',
			'container_exclude_by_class',
			'minimum_number_of_headings',
			'scrolling_speed',
			'content_height',
			'default_collapse_state',
			'offset',
			'marker_type',
			'collapsible_with_sticky',
			'active_link_color',
			'active_link_border_on_parent',
			'active_spacing_padding',
			'active_spacing_padding_tablet',
			'active_spacing_padding_phone',
			'highlight_active_link',
		];

		$headings = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];

		foreach ( $headings as $heading ) {
			$icon     = "marker_icon_$heading";
			$size     = "marker_icon_size_$heading";
			$color    = "marker_icon_color_$heading";
			$level    = str_replace( 'h', '', $heading );
			$selector = ".difl_table_of_contents .body_container.icon .difl_heading_level_$level > li > .toc-li-wrapper > span.et-pb-icon";

			array_push( $allowed_settings, $icon );
			array_push( $allowed_settings, $size );
			array_push( $allowed_settings, $color );

			$this->generate_icon_style( $icon, $selector );
			$this->generate_generic_style( $color, $selector );
			$this->generate_generic_style( $size, $selector, 'font-size' );
			$this->generate_generic_style( "marker_icon_space_heading_$heading", ".difl_table_of_contents .body_container.icon .difl_heading_level_$level > li > .toc-li-wrapper > a", 'margin-left' );
		}


		$fields = array_filter( $this->get_fields(), function ( $item, $key ) use ( $allowed_settings ) {
			return in_array( $key, $allowed_settings );
		}, 1 );

		$settings = [];

		foreach ( $fields as $key => $tooltip_option ) {
			$settings[ $key ] = $this->{$key};
		}

		$settings['offset_tablet'] = $this->props['offset_tablet'];
		$settings['offset_phone']  = $this->props['offset_phone'];
		$settings['disabled_on']   = $this->props['disabled_on'];
		$settings['order_class']   = self::get_module_order_class( $this->slug );

		return wp_json_encode( $settings );
	}

	public function enqueue_script() { ?>
        <script>
			(() => {
					let current_order_class = 'difl_table_of_contents_0';

					let active_using_click = false;

					const create_toc = ( text, level, parent = undefined ) => {
						return {
							text,
							level,
							id: undefined,
							parent,
							children: []
						};
					}

					const parse_headings = elements => {
						const toc = [];
						let current_level = 0;
						let current_parent = undefined;

						elements.forEach( ( element, index ) => {
							const level = parseInt( element.tagName.substring( 1 ) );
							const text = element.textContent.trim();

							if ( current_level < level ) {
								const entry = create_toc( text, level, current_parent );
								current_parent ? current_parent.children.push( entry ) : toc.push( entry );
								current_parent = entry;
							} else {
								let new_parent = create_toc( text, level );
								while ( current_parent && current_parent.level >= level ) {
									current_parent = current_parent.parent;
								}
								if ( current_parent ) {
									new_parent.parent = current_parent;
									current_parent.children.push( new_parent );
								} else {
									toc.push( new_parent );
								}
								current_parent = new_parent;
							}

							current_level = level;

							const id = 'difl-toc-' + text.replace( /[^\w\s-]/g, '' ).replace( /\s+/g, '-' ).toLowerCase() + '-' + index;
							element?.classList?.add( 'difl-toc-item' );
							current_parent?.classList?.add( 'difl-toc-item' );
							if ( element.id ) {
								current_parent.id = element.id;
							} else {
								element.id = id;
								current_parent.id = id;
							}
						} );

						return toc;
					}

					const generate_markup = toc => {
						const is_icon_marker = 'icon' === get_settings( 'marker_type' );
						const is_number_with_dot = 'number_with_dot' === get_settings( 'marker_type' );
						const is_native_number = 'number' === get_settings( 'marker_type' );
						let icon_span = '';
						let level_stack = [ 1 ];
						let depth = 0;

						const build_list = ( entries, level = 1 ) => {
							let html = `<ul class="difl--toc--ul difl_heading_level_${ level }">`;
							const icon_settings = get_settings( `marker_icon_h${ level }` );

							entries.forEach( ( entry, index ) => {
								let numbering = level_stack.join( '.' );
								numbering = depth === 0 ? '0' + numbering + '.' : numbering;

								const classes = `difl--toc--li difl_heading_li_level_${ entry.level }`;
								const anchor = `<a class="difl--toc--anchor ${ classes }" href="#${ entry.id }">${ entry.text }</a>`;
								if ( is_icon_marker && undefined !== icon_settings ) {
									const icon = icon_settings.split( '||' )[0];
									icon_span = `<span class="et-pb-icon marker-icon">${ icon }</span>`;
								}

								if ( is_number_with_dot ) {
									icon_span = `<span class="number-with-dot">${ numbering }</span>`;
								}

								if ( is_native_number ) {
									let numbering = level_stack[depth];
									numbering = depth === 0 ? '0' + level_stack[0] : '0' + numbering;
									icon_span = `<span class="native-number">${ numbering }</span>`;
								}

								html += `<li id="${ entry.id }-toc-li"><div class="toc-li-wrapper">${ icon_span } ${ anchor }</div>`;
								if ( entry.children.length ) {
									level_stack.push( 1 );
									depth += 1;
									html += build_list( entry.children, level + 1 );
								}
								level_stack[depth] += 1;
								html += `</li>`;
							} );
							depth -= 1;
							level_stack.pop();
							return html + `</ul>`;
						}

						return build_list( toc );
					}

					const get_settings = ( key = 'all' ) => {
						const toc_main = document.querySelector( current_order_class );
						const body_container = toc_main.querySelector( '.body_container' );
						const settings = JSON.parse( body_container.dataset.settings );

						if ( 'all' === key ) {
							return settings;
						}

						return settings[key] || '';
					}

					const get_offset = () => {
						const is_mobile = window.matchMedia( '(max-width: 767px)' ).matches;
						const is_tablet = window.matchMedia( '(min-width: 768px) and (max-width: 1024px)' ).matches;
						let offset = get_settings( 'offset' ).replace( 'px', '' );

						if ( is_tablet ) {
							offset = get_settings( 'offset_tablet' );
						}

						if ( is_mobile ) {
							offset = get_settings( 'offset_mobile' );
						}

						return offset;
					}

					const get_parent_distance = ( wrapper, parent_selector, side = 'left' ) => {
						const parent = wrapper.closest( parent_selector );

						const parent_rect = parent.getBoundingClientRect();
						const child_rect = wrapper.getBoundingClientRect();

						return parent_rect[side] - child_rect[side];
					}

					const is_closest = ( element, selectors ) => {
						if ( ! selectors ) return false;
						return selectors.split( ',' ).some( ( selector ) => element.closest( `.${ selector }` ) ) !== null;
					}

					const is_matched = ( element, selectors ) => {
						if ( ! selectors ) return false;
						let matched = false;
						element.classList.forEach( class_name => {
							if ( matched ) return true;
							if ( selectors.split( ',' ).some( selector => class_name === selector ) ) {
								matched = true;
							}
						} )

						return matched;
					}

					const is_hidden = element => {
						const style = window.getComputedStyle( element );
						return style.display === 'none' || style.visibility === 'hidden';
					}

					const generate_toc = () => {
						const toc_main = document.querySelector( current_order_class );
						const body_container = toc_main.querySelector( '.body_container' );
						const settings = get_settings();
						const heading_tags = settings.heading_tags.split( '|' );
						const module_hide_number = settings.minimum_number_of_headings;
						const container_exclude = get_settings( 'container_exclude_by_class' );
						const heading_exclude = get_settings( 'headings_exclude_by_class' );
						let main_content = document.querySelector( '#main-content' );
						if ( ! main_content ) return;

						const allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ].filter( ( tag, index ) => heading_tags[index] === 'on' );

						if ( ! allowed_tags.length ) {
							toc_main.remove();
							return;
						}

						const headings = [ ...main_content.querySelectorAll( allowed_tags.join( ',' ) ) ].filter( heading => {
							return ! (heading.closest( '.entry-title' ) || heading.closest( '#sidebar' ) || heading.closest( '#comment-wrap' ) || heading.closest( '.heading_container' ) || is_closest( heading, container_exclude ) || is_matched( heading, heading_exclude ) || is_hidden( heading ))
						} )

						if ( headings?.length < module_hide_number ) {
							toc_main.remove();
							return;
						}

						const parsed_toc = parse_headings( Array.from( headings ) );
						body_container.innerHTML = generate_markup( parsed_toc );
					}

					const handle_multiple_modules = () => {
						const modules = document.querySelectorAll( '.difl_table_of_contents' );
						modules.forEach( ( module, index ) => {
							current_order_class = `.difl_table_of_contents_${ index }`;
							if ( ! module.classList.contains( current_order_class.replace( '.', '' ) ) ) {
								current_order_class = `.difl_table_of_contents_${ index }_tb_body`
							}
							generate_toc();
							toggle_header();
						} )
					}

					const get_spacing_value = ( value ) => {
						let top = 0, right = 0, bottom = 0, left = 0;
						value.split( ';' ).map( val => val.split( ':' ) ).forEach( item => {
							const side = item[0].replace( 'padding-', '' ).trim();
							const side_value = '' === item[1] ? 0 : parseInt( item[1] );

							if ( side === 'top' ) {
								top = side_value;
							} else if ( side === 'right' ) {
								right = side_value;
							} else if ( side === 'bottom' ) {
								bottom = side_value;
							} else if ( side === 'left' ) {
								left = side_value;
							}
						} );

						return { top, right, bottom, left };
					}

					const toggle_header = () => {
						const module = document.querySelector( current_order_class );
						const container = module.querySelector( '.difl_toc_main_container' );
						const header = container.querySelector( '.heading_container' );
						const body = container.querySelector( '.body_container' );
						const expand_icon = header.querySelector( '.icon .expand_icon' );
						const collapse_icon = header.querySelector( '.icon .collapse_icon' );
						const scrolling_speed = get_settings( 'scrolling_speed' );
						const transition = `all ${ scrolling_speed }ms ease-in-out`;
						const height = getComputedStyle( body ).height;
						const padding = getComputedStyle( body ).padding;
						const margin = getComputedStyle( body ).margin;
						const border = getComputedStyle( body ).border;
						const sections = document.querySelectorAll( '[class*="difl-toc-item"]' );
						const items = module.querySelectorAll( '.difl_toc_main_container .body_container a.difl--toc--li' );
						const offset = get_offset();
						const highlight_active_link = 'on' === get_settings( 'highlight_active_link' );
						const is_border_on_parent = 'on' === get_settings( 'active_link_border_on_parent' );
						const active_padding = get_settings( 'active_spacing_padding' );
						const active_padding_tablet = get_settings( 'active_spacing_padding_tablet' );
						const active_padding_phone = get_settings( 'active_spacing_padding_phone' );
						const active_padding_desktop_value = get_spacing_value( active_padding );
						const active_padding_tablet_value = get_spacing_value( active_padding_tablet );
						const active_padding_phone_value = get_spacing_value( active_padding_phone );

						const get_current_device = () => {
							if ( window.matchMedia( '(max-width: 767px)' ).matches ) {
								return 'mobile';
							} else if ( window.matchMedia( '(min-width: 768px) and (max-width: 1024px)' ).matches ) {
								return 'tablet';
							} else {
								return 'desktop';
							}
						}

						const get_padding_value = () => {
							const current_device = get_current_device();

							if ( 'desktop' === current_device ) {
								return active_padding_desktop_value;
							}
							if ( 'tablet' === current_device ) {
								return active_padding_tablet_value;
							}
							return active_padding_phone_value;
						}

						const highlight_active = () => {
							if ( active_using_click ) {
								return;
							}

							let active_id = null;

							sections.forEach( ( section ) => {
								const rect = section.getBoundingClientRect();
								if ( active_id !== null ) {
									return;
								}
								if ( rect.top >= 0 && rect.top < window.innerHeight - offset ) {
									active_id = section.getAttribute( 'id' );
								}
							} );

							items.forEach( ( item ) => {
								const href = item.getAttribute( 'href' ).substring( 1 );
								if ( href === active_id ) {
									if ( items[0] !== item ) {
										item.scrollIntoView( {
											behavior: 'smooth',
											block: 'center',
											inline: 'nearest'
										} );
									}

									if ( is_border_on_parent && highlight_active_link ) {
										const child_selector = `${ active_id }-toc-li`;
										const li = document.getElementById( `${ child_selector }` );
										const wrapper = li.querySelector( '.toc-li-wrapper' );
										const left = get_parent_distance( wrapper, '.body_container' );
										const right = get_parent_distance( wrapper, '.body_container', 'right' ) * -1;
										const margin_left = left + parseInt( getComputedStyle( wrapper ).marginLeft.replace( 'px', '' ) );
										const margin_right = right + parseInt( getComputedStyle( wrapper ).marginRight.replace( 'px', '' ) );
										const padding_left = Math.abs( margin_left ) + parseInt( get_padding_value().left );
										const padding_right = Math.abs( margin_right ) + parseInt( get_padding_value().right );

										wrapper.style.setProperty( 'margin-left', `${ margin_left }px`, 'important' );
										wrapper.style.setProperty( 'margin-right', `${ margin_right }px`, 'important' );
										wrapper.style.setProperty( 'padding-left', `${ padding_left }px`, 'important' );
										wrapper.style.setProperty( 'padding-right', `${ padding_right }px`, 'important' );
									}
									if ( highlight_active_link ) {
										item.classList.add( 'active' );
									}
									items.forEach( ( item_remove ) => {
										if ( item_remove !== item && item_remove.classList.contains( 'active' ) ) {
											item_remove.classList.remove( 'active' );
											if ( is_border_on_parent && highlight_active_link ) {
												getComputedStyle( item_remove.closest( '.toc-li-wrapper' ) ).marginLeft;
												item_remove.closest( '.toc-li-wrapper' ).style.setProperty( 'margin-left', 'initial' );
												item_remove.closest( '.toc-li-wrapper' ).style.setProperty( 'margin-right', 'initial' );
												item_remove.closest( '.toc-li-wrapper' ).style.setProperty( 'padding-left', 'initial' );
												item_remove.closest( '.toc-li-wrapper' ).style.setProperty( 'padding-right', 'initial' );
											}
										}
									} );
								}
							} );
						};
						window.addEventListener( 'scroll', () => {
							if ( module.classList.contains( 'et_pb_sticky_module' ) ) {
								highlight_active();
							}

							const is_sticky_enabled = get_settings( 'collapsible_with_sticky' ) === 'on';

							if ( ! is_sticky_enabled ) {
								return;
							}

							if ( ! module.classList.contains( 'et_pb_sticky_module' ) ) {
								return;
							}

							if ( body.classList.contains( 'collapse_sticky' ) && module.classList.contains( 'et_pb_sticky' ) ) {
								body.style.setProperty( 'height', '0px', 'important' );
								body.style.setProperty( 'padding', '0px', 'important' );
								container.classList.add( 'collapsed' );
								container.classList.remove( 'expanded' );
							}

							if ( ! module.classList.contains( 'et_pb_sticky' ) ) {
								body.style.setProperty( 'height', height, 'important' );
								body.style.setProperty( 'padding', padding, 'important' );
								container.classList.add( 'expanded' );
								container.classList.remove( 'collapsed' );
							}

							if ( ! container.classList.contains( 'expanded' ) ) {
								expand_icon.style.display = 'none';
								collapse_icon.style.display = 'block';
							} else {
								expand_icon.style.display = 'block';
								collapse_icon.style.display = 'none';
							}
						} );

						if ( container.classList.contains( 'collapsed' ) ) {
							body.style.setProperty( 'height', 0, 'important' );
							body.style.setProperty( 'padding', 0, 'important' );
							if ( expand_icon ) {
								expand_icon.style.display = 'none';
							}
							if ( collapse_icon ) {
								collapse_icon.style.display = 'block';
							}
						}

						body.style.transition = transition;
						header.addEventListener( 'click', ( e ) => {
							let toggle_class = container.classList.contains( 'collapsed' ) ? 'collapsed' : 'expanded';
							container.classList.toggle( toggle_class );
							if ( 'expanded' !== toggle_class ) {
								body.style.setProperty( 'height', height, 'important' );
								body.style.setProperty( 'padding', padding, 'important' );
								body.style.setProperty( 'margin', margin, 'important' );
								body.style.setProperty( 'border', border, 'important' );
								if ( expand_icon ) {
									expand_icon.style.display = 'block';
								}
								if ( collapse_icon ) {
									collapse_icon.style.display = 'none';
								}
							} else {
								container.classList.add( 'collapsed' )
								body.style.setProperty( 'height', 0, 'important' );
								body.style.setProperty( 'padding', 0, 'important' );
								body.style.setProperty( 'margin', 0, 'important' );
								body.style.setProperty( 'border', 'none', 'important' );
								if ( expand_icon ) {
									expand_icon.style.display = 'none';
								}
								if ( collapse_icon ) {
									collapse_icon.style.display = 'block';
								}
							}
						} )
					}

					const handle_active_color = () => {
						const module = document.querySelector( current_order_class );

						const body = module.querySelector( '.difl_toc_main_container .body_container' );

						if ( ! body.classList.contains( 'height_enable' ) ) {
							body.style.setProperty( 'height', getComputedStyle( body ).height, 'important' );
						}

						let offset = get_offset();

						const links = module.querySelectorAll( '.difl_toc_main_container .body_container a[class*="difl--toc--anchor"]' );

						links.forEach( link => {
							$( link ).off( 'click' );

							link.addEventListener( 'click', e => {
								active_using_click = true;
								links.forEach( link => link.classList.remove( 'active' ) );
								link.classList.add( 'active' );
								e.preventDefault();
								const id = e.target.getAttribute( 'href' ).substring( 1 );
								const element = document.getElementById( id );
								if ( element ) {
									const position = element.getBoundingClientRect().top + window.pageYOffset - offset;
									window.scrollTo( { top: position, behavior: 'smooth' } );
								}
								setTimeout( () => {
									active_using_click = false;
								}, 1000 );
							} );
						} );
					}

					window.addEventListener( 'load', handle_active_color )
					document.addEventListener( 'DOMContentLoaded', () => {
						handle_multiple_modules();
					} );
				}
			)();
        </script>
		<?php
	}
}

new DIFL_TableOfContents();