<?php

class DIFL_PricingTable extends ET_Builder_Module {
	/**
	 * @var string
	 */
	public $icon_path;
	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	public function init() {
		$this->name       = esc_html__( 'Advanced Pricing Table', 'divi_flash' );
		$this->slug       = 'difl_pricingtable';
		$this->child_slug = 'difl_pricingtableitem';
		$this->vb_support = 'on';

		$this->icon_path = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/pricing-table.svg';

		$this->child_item_text = esc_html__( 'Pricing Item', 'divi_flash' );
	}

	public function get_settings_modal_toggles() {
		$toggles = [];

		$toggles['general'] = [
			'toggles' => [
				'main_content' => esc_html__( 'Elements Animation', 'divi_flash' ),
				'tooltip'      => esc_html__( 'Feature Tooltip', 'divi_flash' ),
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

		$animation = [
			'enable_animation'        => [
				'label'            => esc_html__( 'Enable Stagger Animation', 'divi_flash' ),
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
			],
			'difl_animation_types'    => [
				'label'       => esc_html__( 'Types', 'divi_flash' ),
				'type'        => 'select',
				'default'     => 'fadeInUpShort',
				'options'     => [
					'fadeIn'                 => esc_html__( 'Fade In', 'divi_flash' ),
					'fadeInLeftShort'        => esc_html__( 'Fade In Left', 'divi_flash' ),
					'fadeInRightShort'       => esc_html__( 'Fade In Right', 'divi_flash' ),
					'fadeInUpShort'          => esc_html__( 'Fade In Up', 'divi_flash' ),
					'fadeInDownShort'        => esc_html__( 'Fade In Down', 'divi_flash' ),
					'zoomInShort'            => esc_html__( 'Grow', 'divi_flash' ),
					'bounceInShort'          => esc_html__( 'Bounce In', 'divi_flash' ),
					'bounceInLeftShort'      => esc_html__( 'Bounce In Left', 'divi_flash' ),
					'bounceInRightShort'     => esc_html__( 'Bounce In Right', 'divi_flash' ),
					'bounceInUpShort'        => esc_html__( 'Bounce In Up', 'divi_flash' ),
					'bounceInDownShort'      => esc_html__( 'Bounce In Down', 'divi_flash' ),
					'flipInXShort'           => esc_html__( 'Flip InX', 'divi_flash' ),
					'flipInYShort'           => esc_html__( 'Flip InY', 'divi_flash' ),
					'jackInTheBoxShort'      => esc_html__( 'Jack In The Box', 'divi_flash' ),
					'rotateInShort'          => esc_html__( 'Rotate In', 'divi_flash' ),
					'rotateInDownLeftShort'  => esc_html__( 'Rotate In Down Left', 'divi_flash' ),
					'rotateInUpLeftShort'    => esc_html__( 'Rotate In Up Left', 'divi_flash' ),
					'rotateInDownRightShort' => esc_html__( 'Rotate In Down Right', 'divi_flash' ),
					'rotateInUpRightShort'   => esc_html__( 'Rotate In Up Right', 'divi_flash' ),
				],
				'toggle_slug' => 'main_content',
				'tab_slug'    => 'general',
				'show_if'     => [ 'enable_animation' => 'on' ],
			],
			'difl_animation_delay'    => [
				'label'            => esc_html__( 'Delay', 'divi_flash' ),
				'type'             => 'range',
				'default'          => '200ms',
				'default_on_front' => '200ms',
				'default_unit'     => 'ms',
				'range_settings'   => [
					'min'  => '0',
					'max'  => '2000',
					'step' => '100',
				],
				'mobile_options'   => true,
				'responsive'       => true,
				'toggle_slug'      => 'main_content',
				'tab_slug'         => 'general',
				'show_if'          => [ 'enable_animation' => 'on' ],
			],
			'difl_animation_duration' => [
				'label'            => esc_html__( 'Duration', 'divi_flash' ),
				'type'             => 'range',
				'default'          => '500ms',
				'default_on_front' => '500ms',
				'default_unit'     => 'ms',
				'range_settings'   => [
					'min'  => '200',
					'max'  => '3000',
					'step' => '100',
				],
				'mobile_options'   => true,
				'responsive'       => true,
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
				'show_if'          => [ 'enable_animation' => 'on' ],
			],
		];

		$feature_tooltip = [
			'enable_feature_tooltip'         => [
				'label'           => esc_html__( 'Enable Tooltip', 'divi_flash' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'tooltip',

			],
			'feature_text_tooltip_placement' => [
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
					'left-end'     => esc_html__( 'Left End', 'divi_flash' ),
				],
				'default'     => 'right',
				'tab_slug'    => 'general',
				'toggle_slug' => 'tooltip',
				'show_if'     => [ 'enable_feature_tooltip' => 'on' ],
			],

			'feature_text_tooltip_animation' => [
				'label'            => esc_html__( 'Animation', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'fade'         => esc_html__( 'Fade', 'divi_flash' ),
					'scale'        => esc_html__( 'Scale', 'divi_flash' ),
					'rotate'       => esc_html__( 'Rotate', 'divi_flash' ),
					'shift-away'   => esc_html__( 'Shift Away', 'divi_flash' ),
					'shift-toward' => esc_html__( 'Shift Toward', 'divi_flash' ),
					'perspective'  => esc_html__( 'Perspective', 'divi_flash' ),
				],
				'default'          => 'scale',
				'default_on_front' => 'scale',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'tooltip',
				'show_if'          => [ 'enable_feature_tooltip' => 'on' ],
			],

			'feature_text_tooltip_trigger' => [
				'label'       => esc_html__( 'Trigger', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'mouseenter focus' => esc_html__( 'Hover', 'divi_flash' ),
					'click'            => esc_html__( 'Click', 'divi_flash' ),
					'mouseenter click' => esc_html__( 'Hover And Click', 'divi_flash' ),
				],
				'default'     => 'mouseenter focus',
				'tab_slug'    => 'general',
				'toggle_slug' => 'tooltip',
				'show_if'     => [ 'enable_feature_tooltip' => 'on' ],
			],

			'feature_text_tooltip_mouse_style' => [
				'label'            => esc_html__( 'Cursor Style', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'default'      => esc_html__( 'Default', 'divi_flash' ),
					'help'         => esc_html__( 'Help', 'divi_flash' ),
					'context-menu' => esc_html__( 'Context Menu', 'divi_flash' ),
					'pointer'      => esc_html__( 'Pointer', 'divi_flash' ),
				],
				'default'          => 'help',
				'default_on_front' => 'help',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'tooltip',
				'show_if'          => [ 'enable_feature_tooltip' => 'on' ],
			],


			'feature_text_tooltip_duration'             => [
				'label'          => esc_html__( 'Transition Duration', 'divi_flash' ),
				'description'    => esc_html__( 'Specifies the duration of the transition.', 'divi_flash' ),
				'type'           => 'range',
				'tab_slug'       => 'general',
				'toggle_slug'    => 'tooltip',
				'default'        => 300,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '1000',
					'step' => '1',
				],
				'show_if'        => [ 'enable_feature_tooltip' => 'on' ],
			],
			'feature_text_tooltip_interactive'          => [
				'label'       => esc_html__( 'Hover Over Tooltip', 'divi_flash' ),
				'description' => esc_html__( 'Tooltip allowing you to hover over and click inside it.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' ),
				],
				'default'     => 'on',
				'tab_slug'    => 'general',
				'toggle_slug' => 'tooltip',
				'show_if'     => [ 'enable_feature_tooltip' => 'on' ],
			],
			'feature_text_tooltip_interactive_border'   => [
				'label'          => esc_html__( 'Tooltip Hover Area', 'divi_flash' ),
				'description'    => esc_html__( 'Determines the size of the invisible border around the tooltip that will prevent it from hiding if the cursor left it.', 'divi_flash' ),
				'type'           => 'range',
				'tab_slug'       => 'general',
				'toggle_slug'    => 'tooltip',
				'show_if'        => [ 'enable_feature_tooltip' => 'on', 'feature_text_tooltip_interactive' => 'on' ],
				'default'        => 2,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
			],
			'feature_text_tooltip_interactive_debounce' => [
				'label'          => esc_html__( 'Tooltip Content Hide Delay', 'divi_flash' ),
				'description'    => esc_html__( 'Determines the time in ms to debounce the Tooltip content hide handler when the cursor leaves.', 'divi_flash' ),
				'type'           => 'range',
				'tab_slug'       => 'general',
				'toggle_slug'    => 'tooltip',
				'show_if'        => [ 'enable_feature_tooltip' => 'on', 'feature_text_tooltip_interactive' => 'on' ],
				'default'        => 0,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '1000',
					'step' => '10',
				],
			],
			'feature_text_tooltip_max_width'            => [
				'label'          => esc_html__( 'Max Width', 'divi_flash' ),
				'description'    => esc_html__( 'Specifies the maximum width of the tooltip. Useful to prevent it from being too horizontally wide to read.', 'divi_flash' ),
				'type'           => 'range',
				'tab_slug'       => 'general',
				'toggle_slug'    => 'tooltip',
				'default'        => 350,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '1000',
					'step' => '1',
				],
				'show_if'        => [ 'enable_feature_tooltip' => 'on' ],
			],

			'feature_text_tooltip_offset_distance_vertical' => [
				'label'          => esc_html__( 'Vertical Distance ', 'divi_flash' ),
				'description'    => esc_html__( 'Tooltip Distance length from spot to tooltip', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'tooltip',
				'default'        => 0,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'show_if'        => [ 'enable_feature_tooltip' => 'on' ],
			],

			'feature_text_tooltip_offset_distance' => [
				'label'          => esc_html__( 'Horizontal Distance', 'divi_flash' ),
				'description'    => esc_html__( 'Tooltip Distance length from spot to tooltip', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'tooltip',
				'default'        => 10,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'show_if'        => [ 'enable_feature_tooltip' => 'on' ],
			],
		];

		return array_merge( $general, $animation, $feature_tooltip );
	}

	public function get_advanced_fields_config() {
		$advanced_fields                                     = [];
		$advanced_fields['text']                             = false;
		$advanced_fields['fonts']                            = false;
		$advanced_fields['borders']['default']               = [
			'css' => [
				'main' => [
					'border_radii'        => '%%order_class%%',
					'border_radii_hover'  => '%%order_class%%:hover',
					'border_styles'       => '%%order_class%%',
					'border_styles_hover' => '%%order_class%%:hover',
				],
			],
		];
		$advanced_fields['box_shadow']['default']            = [
			'css' => [
				'main'      => '%%order_class%%',
				'hover'     => '%%order_class%%:hover',
				'important' => 'all',
			],
		];
		$advanced_fields['box_shadow']['tooltip_box_shadow'] = [
			'css'         => [
				'main'  => '.tippy-box[data-theme*=".difl_pricingtableitem_"]',
				'hover' => '.tippy-box[data-theme*=".difl_pricingtableitem_"]:hover',
			],
			'toggle_slug' => 'tooltip',
			'tab_slug'    => 'general',
		];

		return $advanced_fields;
	}

	public function &__get( $name ) {
		if ( array_key_exists( $name, $this->props ) ) {
			return $this->props[ $name ];
		}

		throw new Exception( sprintf( 'Property %s does not exist', $name ) );
	}

	public function render( $attrs, $content, $render_slug, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
		static $script_loaded = false;
		if ( 'on' === $this->enable_animation ) {
			wp_enqueue_style( 'df-animate-styles' );
		}

		if ( ( 'on' === $this->enable_feature_tooltip || 'on' === $this->enable_animation ) && !$script_loaded ) {
			wp_enqueue_script( 'image-hotspot-popper-script' );
			wp_enqueue_script( 'image-hotspot-tippy-bundle-script' );
			wp_register_script( 'difl-pricing-table-inline-script', '', [], false, true );
			wp_enqueue_script( 'difl-pricing-table-inline-script' );

			// Attach the inline JS to the dummy handle
			wp_add_inline_script( 'difl-pricing-table-inline-script', $this->enque_scripts() );
			$script_loaded = true;
		}

		return sprintf( '<div class="difl-pricing-table-wrapper" data-tooltip=\'%2$s\'  data-animation=\'%3$s\'>%1$s</div>', $this->content, $this->get_settings( 'enable_feature_tooltip', 'feature_text_tooltip_' ), $this->get_settings( 'enable_animation', 'difl_animation_' ) );
	}

	protected function get_settings( $enable_key, $prefix ) {
		if ( 'off' === $this->$enable_key ) {
			return wp_json_encode( '' );
		}

		$fields = array_filter( $this->get_fields(), function ( $item, $key ) use ( $prefix ) {
			return str_starts_with( $key, $prefix );
		}, 1 );

		$settings = [];

		foreach ( $fields as $key => $tooltip_option ) {
			$settings[ str_replace( $prefix, '', $key ) ] = $this->{$key};
		}

		return wp_json_encode( $settings );
	}

	public function enque_scripts()
	{
		ob_start();
		?>
        (() => {
        window.addEventListener('load', () => {
        const selectAll = selector => document.querySelectorAll(selector);
        const settingsAll = selectAll('.difl_pricingtable .difl-pricing-table-wrapper');

        [...settingsAll].forEach(table => {
        const handleTooltip = () => {
        const tooltips = table.querySelectorAll('.difl_pricingtableitem .item-feature');
        [...tooltips].forEach((tooltip, index) => {
        const item_selctor = tooltip.closest('.difl_pricingtableitem').classList.value.split(' ').filter(class_name =>
        class_name.indexOf('difl_pricingtableitem_') !== -1
        )[0];

        const tipper = tooltips[index];
        const tooltipSettings = JSON.parse(table?.dataset?.tooltip);
        const content = JSON.parse(tooltip?.dataset?.tooltip).main_content;
        if (!tooltipSettings || !content) return;

        tooltip.closest('.difl_pricingtableitem').style.zIndex = 99999;
        tipper.style.cursor = tooltipSettings.mouse_style;

        const {
        animation,
        placement,
        trigger,
        duration,
        interactive,
        interactive_border,
        interactive_debounce
        } = tooltipSettings;

        const options = {
        content,
        duration,
        animation,
        placement,
        trigger,
        interactive: interactive === 'on',
        interactiveBorder: parseInt(interactive_border) || 2,
        interactiveDebounce: parseInt(interactive_debounce) || 0,
        allowHTML: true,
        maxWidth: parseInt(tooltipSettings.max_width),
        offset: [parseInt(tooltipSettings.offset_distance_vertical), parseInt(tooltipSettings.offset_distance)],
        theme: `.${item_selctor}`,
        };
        tippy(tipper, options);
        });
        };

        const handleAnimation = () => {
        const animation = JSON.parse(table?.dataset?.animation);
        if (!animation) return;

        const { types, delay, duration } = animation;
        const childs = [...table.querySelectorAll('.difl_pricingtableitem')];
        childs.forEach((child, index = index + 1) => {
        child.style['animation-delay'] = (delay.replace('ms', '') * index) + 'ms';
        child.classList.add('animated', types);
        child.style['animation-duration'] = duration;
        if (child.querySelector('.item-ribbon')) {
        setTimeout(() => {
        child.classList.remove('animated', types);
        child.style.animationDuration = '';
        child.style.animationDelay = '';
        }, duration);
        }
        });
        };

        handleAnimation();
        handleTooltip();
        });
        });
        })();
		<?php
		return ob_get_clean();
	}

}

new DIFL_PricingTable();