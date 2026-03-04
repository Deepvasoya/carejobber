<?php

class AvatarStackItem extends ET_Builder_Module {
	use \DIFL\Handler\Fa_Icon_Handler;
	use DF_UTLS;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	function init() {
		$this->name                        = esc_html__( 'Stack Item', 'divi_flash' );
		$this->plural                      = esc_html__( 'Stack Items', 'divi_flash' );
		$this->slug                        = 'difl_avatar_stack_item';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'admin_label';
		$this->child_title_fallback_var    = 'field_content_type';
		$this->settings_text               = esc_html__( 'Stack Item Settings', 'divi_flash' );
		$this->advanced_setting_title_text = esc_html__( 'New Stack Item', 'divi_flash' );

		$this->main_css_element            = ".difl_avatar_stack #difl-avatar-stack-container %%order_class%%.difl_avatar_stack_item";
	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'content_main'    => esc_html__( 'Content', 'divi_flash' ),
					'content_tooltip' => esc_html__( 'Tooltip Content', 'divi_flash' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'design_icon'   => esc_html__( 'Icon', 'divi_flash' ),
					'design_rating' => esc_html__( 'Rating', 'divi_flash' ),
					'design_text'   => [
						'title'             => esc_html__( 'Text', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => [
							'title'    => [
								'name' => 'Title',
							],
							'subtitle' => [
								'name' => 'Sub-Title',
							]
						],
					]
				],
			],
		];
	}

	public function get_fields() {
		$main_content = [
			'field_content_type' => [
				'label'       => esc_html__( 'Content Type', 'divi_flash' ),
				'type'        => 'select',
				'default'     => 'icon',
				'options'     => [
					'icon'   => esc_html__( 'Icon', 'divi_flash' ),
					'image'  => esc_html__( 'Image', 'divi_flash' ),
					'rating' => esc_html__( 'Rating', 'divi_flash' ),
					'text'   => esc_html__( 'Text', 'divi_flash' ),
				],
				'tab_slug'    => 'general',
				'toggle_slug' => 'content_main',
			],
			'admin_label'        => [
				'label'            => esc_html__( 'Admin Label', 'divi_flash' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'admin_label',
				'default_on_front' => ''
			]
		];
		$icon         = [
			'field_font_icon'  => [
				'label'           => esc_html__( 'Icon', 'divi_flash' ),
				'description'     => esc_html__( 'Choose an icon to display with your blurb.', 'divi_flash' ),
				'type'            => 'select_icon',
				'option_category' => 'basic_option',
				'default'         => '&#xe08a;||divi',
				'class'           => [ 'et-pb-font-icon' ],
				'toggle_slug'     => 'content_main',
				'mobile_options'  => true,
				'hover'           => 'tabs',
				'show_if'         => [
					'field_content_type' => 'icon',
				],
			],
			'field_icon_color' => [
				'label'          => esc_html__( 'Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your icon.', 'divi_flash' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'design_icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			],
			'field_icon_size'  => [
				'label'           => esc_html__( 'Size', 'divi_flash' ),
				'default'         => '30px',
				'range_settings'  => [
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				],
				'toggle_slug'     => 'design_icon',
				'description'     => esc_html__( 'Here you can choose Icon width.', 'divi_flash' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'validate_unit'   => true,
				'allowed_units'   => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
				'responsive'      => true,
				'mobile_options'  => true,
				'sticky'          => true,
				'hover'           => 'tabs',
			],
		];
		$image        = [
			'field_image_src' => [
				'label'              => et_builder_i18n( 'Image' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'divi_flash' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'divi_flash' ),
				'update_text'        => esc_attr__( 'Set As Image', 'divi_flash' ),
				'hide_metadata'      => true,
				'toggle_slug'        => 'content_main',
				'dynamic_content'    => 'image',
				'mobile_options'     => false,
				'hover'              => false,
				'show_if'            => [
					'field_content_type' => 'image',
				],
			]
		];
		$rating       = [
			'field_rating_number'    => [
				'label'       => esc_html__( 'Rating Number', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'1' => esc_html__( 'One', 'divi_flash' ),
					'2' => esc_html__( 'Two', 'divi_flash' ),
					'3' => esc_html__( 'Three', 'divi_flash' ),
					'4' => esc_html__( 'Four', 'divi_flash' ),
					'5' => esc_html__( 'Five', 'divi_flash' ),
				],
				'default'     => '5',
				'toggle_slug' => 'content_main',
				'show_if'     => [
					'field_content_type' => 'rating'
				]
			],
			'field_rating_label'     => [
				'label'           => esc_html__( 'Rating Label', 'divi_flash' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'This defines the Rating Label text.', 'divi_flash' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_main',
				'dynamic_content' => 'text',
				'show_if'         => [
					'field_content_type' => 'rating',
				],
			],
			'field_rating_position'  => [
				'label'            => esc_html__( 'Rating Position', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'center' => esc_html__( 'Center', 'divi_flash' ),
					'start'  => esc_html__( 'Top', 'divi_flash' ),
					'end'    => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'toggle_slug'      => 'content_main',
				'tab_slug'         => 'general',
				'show_if'          => [
					'field_content_type' => 'rating'
				],
			],
			'field_rating_alignment' => [
				'label'            => esc_html__( 'Rating Alignment', 'divi_flash' ),
				'type'             => 'text_align',
				'options'          => et_builder_get_text_orientation_options( [ 'justified' ] ),
				'toggle_slug'      => 'design_rating',
				'tab_slug'         => 'advanced',
				'show_if'          => [
					'field_content_type' => 'rating'
				],
			],
			'field_rating_icon_size' => [
				'label'           => esc_html__( 'Icon size', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => true,
				'responsive'      => true,
				'default_unit'    => 'px',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'design_rating',
				'show_if'         => [
					'field_content_type' => 'rating'
				],
			],
			'field_rating_color'     => [
				'label'       => esc_html__( 'Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'hover'       => 'tabs',
				'toggle_slug' => 'design_rating',
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'field_content_type' => 'rating'
				],
			],
			'field_blank_color'      => [
				'label'       => esc_html__( 'Blank Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'hover'       => 'tabs',
				'toggle_slug' => 'design_rating',
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'field_content_type' => 'rating'
				],
			],
		];
		$text         = [
			'field_title_text'    => [
				'label'           => esc_html__( 'Title Text', 'divi_flash' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'This defines the Title text.', 'divi_flash' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_main',
				'dynamic_content' => 'text',
				'show_if'         => [
					'field_content_type' => 'text',
				],
			],
			'field_subtitle_text' => [
				'label'           => esc_html__( 'Sub-Title Text', 'divi_flash' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'This defines the Sub-Title text.', 'divi_flash' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_main',
				'dynamic_content' => 'text',
				'show_if'         => [
					'field_content_type' => 'text',
				],
			],
			'field_text_position' => [
				'label'            => esc_html__( 'Text Position', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'center' => esc_html__( 'Center', 'divi_flash' ),
					'start'  => esc_html__( 'Top', 'divi_flash' ),
					'end'    => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'default'          => 'center',
				'default_on_front' => 'center',
				'toggle_slug'      => 'content_main',
				'tab_slug'         => 'general',
				'show_if'          => [
					'field_content_type' => 'text'
				],
			],
		];

		$tooltip = [
			'field_tooltip_content' => [
				'label'           => esc_html__( 'Tooltip Content', 'divi_flash' ),
				'type'            => 'tiny_mce',
				'formate'         => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Note: Html tags, shortcode are supported and shortcode will be view only frontend ', 'divi_flash' ),
				'toggle_slug'     => 'content_tooltip',
				'dynamic_content' => 'text'
			],
		];

		$height = [
			'field_item_height' => [
				'label'           => esc_html__( 'Height', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default_unit'    => 'px',
				'fixed_unit'      => 'px',
				'range_settings ' => [
					'min'  => '1',
					'max'  => '500',
					'step' => '1',
				],
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width'
			],
		];

		return array_merge(
			$main_content,
			$icon,
			$image,
			$rating,
			$text,
			$tooltip,
			$height
		);
	}

	public function get_advanced_fields_config() {
		$advanced_fields = [];

		$advanced_fields['fonts']['rating_label']  = [
			'label'            => esc_html__( 'Label', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_rating',
			'hide_text_shadow' => true,
			'css'              => [
				'main'      => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating_label",
			],
		];
		$advanced_fields['fonts']['text_title']    = [
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_text',
			'sub_toggle'       => 'title',
			'hide_text_shadow' => true,
			'css'              => [
				'main'      => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_text .difl_avatar_stack_text_container .difl_avatar_stack_text_title",
			],
		];
		$advanced_fields['fonts']['text_subtitle'] = [
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_text',
			'sub_toggle'       => 'subtitle',
			'hide_text_shadow' => true,
			'css'              => [
				'main'      => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_text .difl_avatar_stack_text_container .difl_avatar_stack_text_subtitle",
			],
		];

		$advanced_fields['max_width']             = [
			'options'              => [
				'width' => [
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
				],
			],
			'use_max_width'        => false,
			'use_module_alignment' => false,
			'css'                  => [
				'main'      => "{$this->main_css_element} .difl_avatar_stack_item_wrapper",
			],
		];
		$advanced_fields['height']                = false;
		$advanced_fields['margin_padding']        = [
			'css' => [
				'main'      => "
				.difl_avatar_stack > div > #difl-avatar-stack-container.difl_avatar_stack_container %%order_class%%.difl_avatar_stack_item .difl_avatar_stack_item_wrapper, 
				.et_pb_gutters3 .et_pb_column_4_4 .difl_avatar_stack .et_pb_module%%order_class%%.difl_avatar_stack_item .difl_avatar_stack_item_wrapper, 
				.et_pb_gutters3.et_pb_row .et_pb_column_4_4 .difl_avatar_stack .et_pb_module%%order_class%%.difl_avatar_stack_item .difl_avatar_stack_item_wrapper
				",
				'important' => 'all',
			],
		];
		$advanced_fields['borders']['default']    = [
			'css'      => [
				'main' => [
					'border_radii'  => "{$this->main_css_element} .difl_avatar_stack_item_wrapper",
					'border_styles' => "{$this->main_css_element} .difl_avatar_stack_item_wrapper",
				],
			],
			'defaults' => [
				'border_radii'  => 'on|0px|0px|0px|0px',
				'border_styles' => [
					'width' => '0px',
					'color' => '#333333',
					'style' => 'solid'
				]
			]
		];
		$advanced_fields['box_shadow']['default'] = [
			'css' => [
				'main'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper",
				'overlay' => 'inset',
			],
		];
		$advanced_fields['background']            = [
			'has_background_color_toggle'   => false,
			'use_background_color'          => true,
			'use_background_color_gradient' => true,
			'use_background_image'          => true,
			'use_background_video'          => false,
			'use_background_color_reset'    => true,
			'use_background_pattern'        => false,
			'use_background_mask'           => false,
			'use_background_image_parallax' => false,
			'css'                           => [
				'main' => "
				{$this->main_css_element} .difl_avatar_stack_item_wrapper",
			]
		];

//		$advanced_fields['link_options']       = true;
		$advanced_fields['custom_css']         = true;
		$advanced_fields['overflow']           = true;

		$advanced_fields['text']               = false;
		$advanced_fields['position_fields']    = false;
		$advanced_fields['z_index']            = false;
		$advanced_fields['sticky']             = false;
		$advanced_fields['scroll_effects']     = false;
		$advanced_fields['transform']          = false;
		$advanced_fields['display_conditions'] = false;
		$advanced_fields['transition']         = false;
		$advanced_fields['transition_fields']  = false;
		$advanced_fields['animation']          = false;
		$advanced_fields['form_field']         = false;
		$advanced_fields['image_icon']         = false;
		$advanced_fields['dividers']           = false;
		$advanced_fields['icon_settings']      = false;
		$advanced_fields['filters']            = false;
		$advanced_fields['child_filters']      = false;

		return $advanced_fields;
	}

	public function additional_css_styles( $render_slug ) {
		/*----- Icon -----*/
		$field_content_type = ! empty( $this->props['field_content_type'] ) ? $this->props['field_content_type'] : "icon";
		if ( 'icon' === $field_content_type ) {
			if ( ! empty( $this->props['field_icon_size'] && '30px' !== $this->props['field_icon_size'] ) ) {
				$this->df_process_range(
					[
						'render_slug' => $render_slug,
						'slug'        => 'field_icon_size',
						'type'        => 'font-size',
						'default'     => '30px',
						'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon",
						'hover'       => "{$this->main_css_element}:hover .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon",
					]
				);
			}
			$this->df_process_color(
				[
					'render_slug' => $render_slug,
					'slug'        => 'field_icon_color',
					'type'        => 'color',
					'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon",
					'hover'       => "{$this->main_css_element}:hover .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon"
				]
			);
		}

		/*----- Rating -----*/
		if ( 'rating' === $field_content_type ) {
			if(! empty( $this->props['field_rating_position'] )){
				ET_Builder_Element::set_style( $render_slug, [
					'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container",
					'declaration' => sprintf('justify-content:%1$s;', $this->props['field_rating_position'])
				] );
			}

			// alignment
			if(! empty( $this->props['field_rating_alignment'] )){
				ET_Builder_Element::set_style( $render_slug, [
					'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating",
					'declaration' => sprintf('text-align: %1$s;', $this->props['field_rating_alignment'])
				] );
			}

			// icon size
			$this->df_process_range(
				[
					'render_slug' => $render_slug,
					'slug'        => 'field_rating_icon_size',
					'type'        => 'font-size',
					'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating",
					'hover'       => "{$this->main_css_element}:hover .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating",
				]
			);
			// rating color
			$this->df_process_color( [
				'render_slug' => $render_slug,
				'slug'        => 'field_rating_color',
				'type'        => 'color',
				'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating span.rate:before",
				'hover'       => "{$this->main_css_element}:hover .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating span.rate:before"
			] );
			// blank color
			$this->df_process_color( [
				'render_slug' => $render_slug,
				'slug'        => 'field_blank_color',
				'type'        => 'color',
				'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating span.blank:before",
				'hover'       => "{$this->main_css_element}:hover .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating span.blank:before"
			] );
		}

		/*----- Text -----*/
		if ( 'text' === $field_content_type ) {
			$text_position = ! empty( $this->props['field_text_position'] ) ? $this->props['field_text_position'] : "center";
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_text .difl_avatar_stack_text_container",
				'declaration' => sprintf('justify-content: %1$s;', $text_position)
			] );
		}

		/*----- Height -----*/
		if( ! empty($this->props['field_item_height']) ){
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element} .difl_avatar_stack_item_wrapper",
				'declaration' => sprintf('height:%1$s;', $this->props['field_item_height'])
			] );
		}
	}

	public function render( $attrs, $content, $render_slug ) {
		$this->handle_fa_icon();
		$this->additional_css_styles( $render_slug );

		$output       = '';
		$output_class = 'has_icon';
		if ( 'image' === $this->props['field_content_type'] ) {
			$output       = $this->process_media( $this->props );
			$output_class = 'has_media';
		} else if ( 'rating' === $this->props['field_content_type'] ) {
			$output       = $this->process_rating( $this->props );
			$output_class = 'has_rating';
		} else if ( 'text' === $this->props['field_content_type'] ) {
			$output       = $this->process_text( $this->props );
			$output_class = 'has_text';
		} else {
			$output       = $this->process_icon( $this->props, $render_slug );
			$output_class = 'has_icon';
		}

		/*-------- Tooltip --------*/
		$tooltip_content = ! empty( $this->props['field_tooltip_content'] ) ? $this->props['field_tooltip_content'] : '';
		if ( null !== $tooltip_content ) {
			$tooltip_content = preg_replace( "/<p[^>]*>(?:\s|&nbsp;)*<\/p>/", '', $tooltip_content );
		}
		global $df_tooltip_item_data;
		$df_AStack_child_class                          = ET_Builder_Element::get_module_order_class( $render_slug );
		$df_tooltip_item_data[ $df_AStack_child_class ] = $tooltip_content;


		return sprintf( '
							<div class="difl_avatar_stack_item_wrapper %2$s">
							%1$s
							</div>
						',
			$output,
			$output_class
		);
	}

	public function process_icon( $props, $render_slug ) {
		if ( empty( $props['field_font_icon'] ) ) {
			return "";
		}
		$font_icon = ! empty( $props['field_font_icon'] ) ? $props['field_font_icon'] : '&#xe08a;||divi||400';
		// Font Icon Style.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'field_font_icon',
				'important'      => true,
				'selector'       => "{$this->main_css_element} .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon",
				'hover_selector' => "{$this->main_css_element}:hover .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon",
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);
		return sprintf( '<span class="et-pb-icon difl_avatar_stack_icon">%1$s</span>', esc_attr( et_pb_process_font_icon( $font_icon ) ) );
	}

	public function process_media( $props ) {
		if ( empty( $props['field_image_src'] ) ) {
			return "";
		}

		return sprintf( '<img src="%1$s" alt="" class="difl_avatar_stack_media"/>', esc_html( $props['field_image_src'] ) );
	}

	public function process_rating( $props ) {
		$rating = '';
		for ( $i = 1; $i <= 5; $i ++ ) {
			if ( $i <= $props['field_rating_number'] ) {
				$rating .= '<span class="rate"></span>';
			} else {
				$rating .= '<span class="blank"></span>';
			}
		}

		$rating_label = '';
		if ( ! empty( $props['field_rating_label'] ) ) {
			$rating_label = sprintf( '<span class="difl_avatar_stack_rating_label">%1$s</span>', esc_html( $props['field_rating_label'] ) );
		}

		return sprintf( '<div class="difl_avatar_stack_rating_container">
				<div class="difl_avatar_stack_rating">
					%1$s
				</div>
				%2$s
			</div>', $rating, $rating_label );
	}

	public function process_text( $props ) {
		$title = '';
		if ( ! empty( $props['field_title_text'] ) ) {
			$title = sprintf( '<h4 class="difl_avatar_stack_text_title">%1$s</h4>', esc_html( $props['field_title_text'] ) );
		}

		$subtitle = '';
		if ( ! empty( $props['field_subtitle_text'] ) ) {
			$subtitle = sprintf( '<h6 class="difl_avatar_stack_text_subtitle">%1$s</h6>', esc_html( $props['field_subtitle_text'] ) );
		}

		return sprintf( '<div class="difl_avatar_stack_text_container"> %1$s %2$s </div>', $title, $subtitle );
	}
}

new AvatarStackItem;