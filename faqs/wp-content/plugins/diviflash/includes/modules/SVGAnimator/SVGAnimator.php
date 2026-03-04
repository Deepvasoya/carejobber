<?php

class DIFL_SVGAnimator extends ET_Builder_Module {
	public $icon_path;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => 'https://diviflash.com',
	];

	public function init() {
		$this->slug             = 'difl_svganimator';
		$this->vb_support       = 'on';
		$this->name             = esc_html__( 'SVG Animator', 'divi_flash' );
		$this->main_css_element = '%%order_class%%';

		$this->icon_path = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/svg-animator.svg';

	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'content'   => esc_html__( 'Element ', 'divi_flash' ),
					'animation' => esc_html__( 'Animation', 'divi_flash' ),
				],
			],
			'advanced' => [
				'toggles' => [
				],
			],
		];
	}

	public function get_fields() {
		$fields = [];

		$fields['svg_src'] = [
			'label'              => esc_html__( 'Upload SVG', 'divi_flash' ),
			'type'               => 'upload',
			'option_category'    => 'basic_option',
			'upload_button_text' => esc_html__( 'Upload SVG', 'divi_flash' ),
			'choose_text'        => esc_html__( 'Choose SVG', 'divi_flash' ),
			'update_text'        => esc_html__( 'Set SVG', 'divi_flash' ),
			'description'        => esc_html__( 'Upload SVG File', 'divi_flash' ),
			'toggle_slug'        => 'content',
			'computed_affects'   => [
				'__svg_animator',
			],
		];

		$fields['notice'] = [
			'type'        => 'df_json_ex_notice',
			'tab_slug'    => 'general',
			'toggle_slug' => 'content',
			'message'     => __( 'Enable SVG file upload option from DiviFlash ', 'divi_flash' ),
			'url'         => esc_url_raw( admin_url() . '?page=diviflash&tab=settings' ),
		];

		$fields['alignment'] = [
			'label'           => esc_html__( 'Alignment', 'divi_flash' ),
			'type'            => 'select',
			'option_category' => 'basic_option',
			'options'         => [
				'flex-start' => esc_html__( 'Left', 'divi_flash' ),
				'center'     => esc_html__( 'Center', 'divi_flash' ),
				'flex-end'   => esc_html__( 'Right', 'divi_flash' ),
			],
			'mobile_options'  => true,
			'description'     => esc_html__( 'Here you can choose the image alignment.', 'divi_flash' ),
			'default'         => 'center',
			'toggle_slug'     => 'content',
		];

		$fields['svg_color'] = [
			'label'       => esc_html__( 'Color', 'divi_flash' ),
			'type'        => 'color-alpha',
			'tab_slug'    => 'general',
			'toggle_slug' => 'content',
			'description' => esc_html__( 'Here you can define a custom color for a SVG.', 'divi_flash' ),
		];

		$fields['svg_width'] = [
			'label'           => esc_html__( 'Width', 'divi_flash' ),
			'type'            => 'range',
			'tab_slug'        => 'general',
			'toggle_slug'     => 'content',
			'default'         => '100%',
			'default_unit'    => '%',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'This defines SVG width. If not set 100%.', 'divi_flash' ),
			'allowed_units'   => [ 'px', 'em', 'rem', '%' ],
			'mobile_options'  => true,
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
		];

		$fields['svg_height'] = [
			'label'            => esc_html__( 'Height', 'divi_flash' ),
			'type'             => 'range',
			'tab_slug'         => 'general',
			'toggle_slug'      => 'content',
			'default'          => '100%',
			'default_on_front' => '100%',
			'default_unit'     => '%',
			'option_category'  => 'basic_option',
			'description'      => esc_html__( 'This defines SVG height. If not set 100%.', 'divi_flash' ),
			'allowed_units'    => [ 'px', 'em', 'rem', '%' ],
			'mobile_options'   => true,
			'range_settings'   => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
		];

		$fields['svg_weight'] = [
			'label'           => esc_html__( 'Line Width', 'divi_flash' ),
			'type'            => 'range',
			'tab_slug'        => 'general',
			'toggle_slug'     => 'content',
			'default_unit'    => 'px',
			'default'         => '2px',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'This defines SVG line width.', 'divi_flash' ),
			'range_settings'  => [
				'min'  => '1',
				'max'  => '20',
				'step' => '1',
			],
		];

		$fields['animation_type'] = [
			'label'            => esc_html__( 'Type', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'basic_option',
			'options'          => [
				'delayed'  => esc_html__( 'Delayed', 'divi_flash' ),
				'sync'     => esc_html__( 'Sync', 'divi_flash' ),
				'oneByOne' => esc_html__( 'One By One', 'divi_flash' ),
			],
			'default_on_front' => 'delayed',
			'description'      => esc_html__( 'Animation type.', 'divi_flash' ),
			'toggle_slug'      => 'animation',
		];

		$fields['delay_animation'] = [
			'label'          => esc_html__( 'Delay', 'divi_flash' ),
			'description'    => esc_html__( 'Delay depends on delayed type and must less than Speed', 'divi_flash' ),
			'type'           => 'range',
			'range_settings' => [
				'min'       => '0',
				'min_limit' => '0',
				'max'       => '2000',
				'step'      => '1',
			],
			'default_unit'   => 'ms',
			'default'        => '0ms',
			'toggle_slug'    => 'animation',
			'show_if'        => [
				'animation_type' => 'delayed',
			],
		];

		$fields['duration_animation'] = [
			'label'           => esc_html__( 'Speed', 'divi_flash' ),
			'description'     => esc_html__( 'Animation speed in frames.', 'divi_flash' ),
			'type'            => 'range',
			'default'         => '100',
			'range_settings'  => [
				'min'  => '1',
				'max'  => '2000',
				'step' => '1',
			],
			'unitless'        => true,
			'option_category' => 'basic_option',
			'toggle_slug'     => 'animation',
		];

		$fields['animation_timing_func'] = [
			'label'           => esc_html__( 'Timing Function', 'divi_flash' ),
			'type'            => 'select',
			'option_category' => 'basic_option',
			'default'         => 'LINEAR',
			'options'         => [
				'LINEAR'          => esc_html__( 'Linear', 'divi_flash' ),
				'EASE'            => esc_html__( 'Ease', 'divi_flash' ),
				'EASE_IN'         => esc_html__( 'Ease-in', 'divi_flash' ),
				'EASE_OUT'        => esc_html__( 'Ease-out', 'divi_flash' ),
				'EASE_OUT_BOUNCE' => esc_html__( 'Ease-out Bounce', 'divi_flash' ),
			],
			'description'     => esc_html__( 'Here you can choose animation timing function.', 'divi_flash' ),
			'toggle_slug'     => 'animation',
		];

		$fields['enable_loop'] = [
			'label'            => esc_html__( 'Loop', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'off' => esc_html__( 'No', 'divi_flash' ),
				'on'  => esc_html__( 'Yes', 'divi_flash' ),
			],
			'description'      => esc_html__( 'Enable Loop', 'divi_flash' ),
			'default'          => 'off',
			'default_on_front' => 'off',
			'toggle_slug'      => 'animation',
			'tab_slug'         => 'general',
		];

		$fields['loop_infinite'] = [
			'label'            => esc_html__( 'Unlimited', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'off' => esc_html__( 'No', 'divi_flash' ),
				'on'  => esc_html__( 'Yes', 'divi_flash' ),
			],
			'description'      => esc_html__( 'Enable This for unlimited animation', 'divi_flash' ),
			'default_on_front' => 'off',
			'toggle_slug'      => 'animation',
			'tab_slug'         => 'general',
			'show_if'          => [
				'enable_loop' => 'on',
			],
		];

		$fields['loop_time'] = [
			'label'           => esc_html__( 'Loop Count', 'divi_flash' ),
			'type'            => 'range',
			'tab_slug'        => 'general',
			'toggle_slug'     => 'animation',
			'default'         => '1',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Animation cycle in number', 'divi_flash' ),
			'range_settings'  => [
				'min'  => '1',
				'max'  => '20',
				'step' => '1',
			],
			'show_if'         => [ 'enable_loop' => 'on', ],
			'show_if_not'     => [
				'loop_infinite' => 'on',
			],
		];

		$fields['path_timing_func'] = [
			'label'           => esc_html__( 'Path Timing Function', 'divi_flash' ),
			'type'            => 'select',
			'option_category' => 'basic_option',
			'default'         => 'LINEAR',
			'options'         => [
				'LINEAR'          => esc_html__( 'Linear', 'divi_flash' ),
				'EASE'            => esc_html__( 'Ease', 'divi_flash' ),
				'EASE_IN'         => esc_html__( 'Ease-in', 'divi_flash' ),
				'EASE_OUT'        => esc_html__( 'Ease-out', 'divi_flash' ),
				'EASE_OUT_BOUNCE' => esc_html__( 'Ease-out Bounce', 'divi_flash' ),
			],
			'description'     => esc_html__( 'Here you can choose animation path timing function.', 'divi_flash' ),
			'toggle_slug'     => 'animation',
		];


		$fields['animation_start'] = [
			'label'            => esc_html__( 'Trigger Type', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'basic_option',
			'default'          => 'inViewport',
			'default_in_front' => 'inViewport',
			'options'          => [
				'inViewport' => esc_html__( 'In Viewport', 'divi_flash' ),
				'autostart'  => esc_html__( 'Auto Start', 'divi_flash' ),
			],
			'description'      => esc_html__( 'Here you can choose animation start function.', 'divi_flash' ),
			'toggle_slug'      => 'animation',
		];

		$fields['enable_replay'] = [
			'label'            => esc_html__( 'Replay', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'off' => esc_html__( 'No', 'divi_flash' ),
				'on'  => esc_html__( 'Yes', 'divi_flash' ),
			],
			'description'      => esc_html__( 'Enable Replay', 'divi_flash' ),
			'default'          => 'off',
			'default_on_front' => 'off',
			'toggle_slug'      => 'animation',
			'tab_slug'         => 'general',
			'show_if'          => [ 'loop_infinite' => 'off', ],
		];

		$fields['replay_type'] = [
			'label'            => esc_html__( 'Replay Type', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'basic_option',
			'default'          => 'click',
			'default_in_front' => 'click',
			'options'          => [
				'click'      => esc_html__( 'Replay on Click', 'divi_flash' ),
				'mouseenter' => esc_html__( 'Replay on Hover', 'divi_flash' ),
			],
			'description'      => esc_html__( 'Select Replay Type', 'divi_flash' ),
			'toggle_slug'      => 'animation',
			'show_if'          => [ 'enable_replay' => 'on', 'loop_infinite' => 'off' ],
		];

		$fields['__svg_animator'] = [
			'type'                => 'computed',
			'computed_callback'   => [ 'DIFL_SVGAnimator', 'render_svg_animator' ],
			'computed_depends_on' => [
				'svg_src',
			],
		];

		return $fields;
	}

	public function get_advanced_fields_config() {
		$advanced_fields                = [];
		$advanced_fields['text']        = false;
		$advanced_fields['text_shadow'] = false;
		$advanced_fields['fonts']       = false;

		return $advanced_fields;
	}

	public static function add_prefix_svg_selector( $svg_html, $render_slug ) {
		$order_class = self::get_module_order_class( $render_slug ) ?: 'difl_svg_animator_unknown';
		$prefix      = '.' . $order_class . ' ';

		if ( preg_match_all( '/<style (.*?)>(.*?)<\/style>/s', $svg_html, $style_codes ) ) {
			foreach ( $style_codes[2] as $style_code ) {
				$prefixed_styles = '';

				preg_match_all( '/(?ims)([a-z0-9\s\.\:#_\-@,]+)\{([^\}]*)\}/', $style_code, $rules );
				foreach ( $rules[1] as $i => $selector ) {
					$selectors       = array_map(
						fn( $sel ) => $prefix . trim( $sel ),
						explode( ',', trim( $selector ) )
					);
					$prefixed_styles .= implode( ',', $selectors ) . '{' . trim( $rules[2][ $i ] ) . "}\r\n";
				}

				$svg_html = str_replace( $style_code, $prefixed_styles, $svg_html );
			}
		}

		return $svg_html;
	}

	public static function render_svg_animator( $args, $render_slug ) {
		$svg_src              = array_key_exists( 'svg_src', $args ) && ! empty( $args['svg_src'] ) ? $args['svg_src'] : '';
		$svg_color            = array_key_exists( 'svg_color', $args ) && ! empty( $args['svg_color'] ) ? $args['svg_color'] : '';
		$svg_weight           = array_key_exists( 'svg_weight', $args ) && ! empty( $args['svg_weight'] ) ? $args['svg_weight'] : '';
		$animation_type       = array_key_exists( 'animation_type', $args ) && ! empty( $args['animation_type'] ) ? $args['animation_type'] : '';
		$animation_delay      = array_key_exists( 'delay_animation', $args ) && ! empty( $args['delay_animation'] ) ? $args['delay_animation'] : '';
		$animation_duration   = array_key_exists( 'duration_animation', $args ) && ! empty( $args['duration_animation'] ) ? $args['duration_animation'] : '';
		$path_timing_function = array_key_exists( 'path_timing_func', $args ) && ! empty( $args['path_timing_func'] ) ? $args['path_timing_func'] : '';
		$anim_timing_function = array_key_exists( 'animation_timing_func', $args ) && ! empty( $args['animation_timing_func'] ) ? $args['animation_timing_func'] : '';
		$animation_start      = array_key_exists( 'animation_start', $args ) && ! empty( $args['animation_start'] ) ? $args['animation_start'] : '';
		$replay_enable        = array_key_exists( 'enable_replay', $args ) && ! empty( $args['enable_replay'] ) ? $args['enable_replay'] : '';
		$repeat_type          = array_key_exists( 'replay_type', $args ) && ! empty( $args['replay_type'] ) ? $args['replay_type'] : '';
		$is_loop              = array_key_exists( 'enable_loop', $args ) && ! empty( $args['enable_loop'] ) ? $args['enable_loop'] : '';
		$loop_infinite        = array_key_exists( 'loop_infinite', $args ) && ! empty( $args['loop_infinite'] ) ? $args['loop_infinite'] : '';
		$iteration_number        = array_key_exists( 'loop_time', $args ) && ! empty( $args['loop_time'] ) ? $args['loop_time'] : '';

		$sa_svg_id = self::get_module_order_class( $render_slug );
		$config    = [];
		if ( $animation_type != 'none' ) {
			$config = [
				'svg_id'             => $sa_svg_id,
				'type'               => ( $animation_type != '' ? $animation_type : 'delayed' ),
				'delay'              => $animation_delay,
				'duration'           => ( $animation_duration != '' ? $animation_duration : '200' ),
				'start'              => ( $animation_start != '' ? $animation_start : 'autostart' ),
				'pathTimingFunction' => ( $path_timing_function != '' ? $path_timing_function : 'linear' ),
				'animTimingFunction' => ( $anim_timing_function != '' ? $anim_timing_function : 'linear' ),
				'replay_enable'      => $replay_enable,
				'repeat_type'        => ( $repeat_type != '' ? $repeat_type : 'click' ),
				'loop_enabled'       => ( $is_loop != '' ? $is_loop : 'off' ),
				'loop_infinite'      => ( $loop_infinite != '' ? $loop_infinite : 'off' ),
				'iteration_number'   => ( $iteration_number != '' ? $iteration_number : '1' ),
			];
		}

		$svg_color   = ( $svg_color != '' && $svg_color != '#' ? $svg_color : '#000000' );
		$svg_content = '';

		if ( $svg_src ) {
			$svg_content    = file_get_contents( $svg_src );
			if ( ! $svg_content ) {
				$response    = wp_remote_get( $svg_src );
				$svg_content = wp_remote_retrieve_body( $response );
			}
			$svg_attributes = 'fill="none" stroke-width="' . $svg_weight . '" stroke="' . $svg_color . '"';
			$validators     = [
				'<svg'            => '<svg id="difl-svg-animator-' . $sa_svg_id . '"',
				'<ellipse'        => '<ellipse ' . $svg_attributes,
				'<rect'           => '<rect ' . $svg_attributes,
				'<circle'         => '<circle ' . $svg_attributes,
				'<polygon'        => '<polygon ' . $svg_attributes,
				'<polyline'       => '<polyline ' . $svg_attributes,
				'<defs'           => '<defs ' . $svg_attributes,
				'<linearGradient' => '<linearGradient ' . $svg_attributes,
				'<path'           => '<path ' . $svg_attributes,
				'style="'         => 'style="fill:none!important;',
			];

			foreach ( $validators as $key => $value ) {
				$svg_content = str_replace( $key, $value, $svg_content );
			}
		}

		$svg_content = DIFL_SVGAnimator::add_prefix_svg_selector( $svg_content, $render_slug );

		$svg_animator = sprintf(
			'<div class="difl-svg-animator-inner-wrapper" data-config=\'%1$s\'>
                %2$s
            </div>',
			wp_json_encode( $config ),
			$svg_content
		);

		return $svg_animator;
	}

	public function render( $attrs, $content, $render_slug ) {
		wp_enqueue_script( 'df_svg_animator' );
		$this->render_style( $render_slug );

		$svg_animator = $this->render_svg_animator( $this->props, $render_slug );

		$output = sprintf(
			'<div class="difl-svg-animator-container" style="opacity: 0;">
                %1$s
            </div>',
			$svg_animator
		);

		return $output;
	}

	private function render_style( $render_slug ) {
		$svg_width = $this->get_responsive_value( 'svg_width' );

		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => '%%order_class%% .difl-svg-animator-container svg',
			'declaration' => sprintf( 'width: %1$s !important;', $svg_width['desktop'] ),
		] );

		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => '%%order_class%% .difl-svg-animator-container svg',
			'declaration' => sprintf( 'width: %1$s !important;', $svg_width['tablet'] ),
			'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
		] );

		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => '%%order_class%% .difl-svg-animator-container svg',
			'declaration' => sprintf( 'width: %1$s !important;', $svg_width['phone'] ),
			'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
		] );

		$svg_height = $this->get_responsive_value( 'svg_height' );

		if ( ! empty( $svg_height ) ) {
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => '%%order_class%% .difl-svg-animator-container svg',
				'declaration' => sprintf( 'height: %1$s !important;', $svg_height['desktop'] ),
			] );

			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => '%%order_class%% .difl-svg-animator-container svg',
				'declaration' => sprintf( 'height: %1$s !important;', $svg_height['tablet'] ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			] );

			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => '%%order_class%% .difl-svg-animator-container svg',
				'declaration' => sprintf( 'height: %1$s !important;', $svg_height['phone'] ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			] );
		}

		$svg_color  = $this->props['svg_color'];
		$svg_weight = $this->props['svg_weight'];

		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => '%%order_class%% .difl-svg-animator-container svg *',
			'declaration' => sprintf( 'stroke: %1$s !important;', $svg_color ),
		] );

		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => '%%order_class%% .difl-svg-animator-container svg *',
			'declaration' => sprintf( 'stroke-width: %1$s !important;', $svg_weight ),
		] );

		$alignment = $this->get_responsive_value( 'alignment' );

		if ( ! empty( $alignment ) ) {
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => '%%order_class%% .difl-svg-animator-inner-wrapper',
				'declaration' => sprintf( 'justify-content: %1$s !important;', $alignment['desktop'] ),
			] );

			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => '%%order_class%% .difl-svg-animator-inner-wrapper',
				'declaration' => sprintf( 'justify-content: %1$s !important;', $alignment['tablet'] ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			] );

			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => '%%order_class%% .difl-svg-animator-inner-wrapper',
				'declaration' => sprintf( 'justify-content: %1$s !important;', $alignment['phone'] ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			] );
		}

	}

	protected function get_responsive_value( $property, $default = '', $default_if_empty = true, $base_name = '' ) {
		$responsive_enabled = isset( $this->props["{$base_name}_last_edited"] )
			? et_pb_get_responsive_status( $this->props["{$base_name}_last_edited"] )
			: ( isset( $this->props["{$property}_last_edited"] )
				? et_pb_get_responsive_status( $this->props["{$property}_last_edited"] )
				: false );

		$desktop = isset( $this->props[ $property ] ) &&
		           ( ! $default_if_empty || $this->props[ $property ] !== '' )
			? $this->props[ $property ]
			: $default;

		$tablet = $responsive_enabled &&
		          isset( $this->props["{$property}_tablet"] ) &&
		          $this->props["{$property}_tablet"] !== ''
			? $this->props["{$property}_tablet"]
			: $desktop;

		$phone = $responsive_enabled &&
		         isset( $this->props["{$property}_phone"] ) &&
		         $this->props["{$property}_phone"] !== ''
			? $this->props["{$property}_phone"]
			: $tablet;

		return compact( 'desktop', 'tablet', 'phone' );
	}


}

new DIFL_SVGAnimator;
