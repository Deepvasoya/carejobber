<?php

namespace DIFL\Customizer;

use DIFL\Customizer\Types\Control;
use DIFL\Customizer\Types\Panel;
use DIFL\Customizer\Types\Partial;
use DIFL\Customizer\Types\Section;
use WP_Customize_Manager;

/**
 * Customizer module base.
 *
 * @package DIFL\Customizer\Abstracts
 */
abstract class Base_Customizer {

	/**
	 * WP_Customize object
	 *
	 * @var WP_Customize_Manager $wp_customize object
	 */
	protected $wpc;

	/**
	 * Selective refresh.
	 *
	 * @var string transport or postMessage
	 */
	protected $selective_refresh;

	/**
	 * Controls to register.
	 *
	 * @var array  Controls that will be registered. (list of \DIFL\Customizer\Types\Control instances.)
	 */
	private $controls_to_register = [];

	/**
	 * Sections to register
	 *
	 * @var array  Controls that will be registered.
	 */
	private $sections_to_register = [];

	/**
	 * Panels to register
	 *
	 * @var array  Panels that will be registered.
	 */
	private $panels_to_register = [];

	/**
	 * Partials to register.
	 *
	 * @var array Partials that will be registered.
	 */
	private $partials_to_register = [];

	/**
	 * Control types to register.
	 *
	 * @var array  Control types that will be registered for use with the content_template Underscores template.
	 */
	private $types_to_register = [];

	protected static $default_space_values = [
		'desktop'      => [
			'top'    => 8,
			'right'  => 10,
			'bottom' => 8,
			'left'   => 10,
		],
		'tablet'       => [
			'top'    => 8,
			'right'  => 10,
			'bottom' => 8,
			'left'   => 10,
		],
		'mobile'       => [
			'top'    => 8,
			'right'  => 10,
			'bottom' => 8,
			'left'   => 10,
		],
		'desktop-unit' => 'px',
		'tablet-unit'  => 'px',
		'mobile-unit'  => 'px',
	];

	/**
	 * Base initialization.
	 */
	public function init() {
		add_action( 'customize_register', [ $this, 'register_controls_callback' ] );
	}

	/**
	 * The function tied to customize_register.
	 *
	 * @param object $wp_customize the customizer manager.
	 */
	public function register_controls_callback( $wp_customize ) {
		$this->wpc = $wp_customize;
		$this->set_selective_refresh();
		$this->add_controls();
		$this->after_add_controls();
		$this->register_controls();
		$this->register_panels();
		$this->register_sections();
		$this->register_types();
		$this->change_controls();
		$this->register_partials();
	}

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	abstract public function add_controls();

	/**
	 * Hook after controls are defined.
	 *
	 * @return void
	 */
	protected function after_add_controls() {
	}

	/**
	 * Change controls function.
	 *
	 * @return void
	 */
	protected function change_controls() {
	}

	/**
	 * Check selective refresh.
	 */
	private function set_selective_refresh() {
		$this->selective_refresh = isset( $this->wpc->selective_refresh ) ? 'postMessage' : 'refresh';
	}

	/**
	 * Register all the defined sections.
	 */
	private function register_panels() {
		$panels = $this->panels_to_register;
		foreach ( $panels as $panel ) {
			$this->wpc->add_panel( $panel->id, $panel->args );
		}
	}

	/**
	 * Register all the defined sections.
	 */
	private function register_sections() {
		$sections = $this->sections_to_register;
		foreach ( $sections as $section ) {
			if ( $section->custom_section !== null ) {
				$this->wpc->add_section( new $section->custom_section( $this->wpc, $section->id, $section->args ) );
			} else {
				$this->wpc->add_section( $section->id, $section->args );
			}
		}
	}

	/**
	 * Register all the defined controls.
	 */
	private function register_controls() {
		$controls = $this->controls_to_register;
		/** @var \DIFL\Customizer\Types\Control $control */
		foreach ( $controls as $control ) {
			$this->wpc->add_setting( $control->id, $control->setting_args );
			$control_type = null;
			if ( $control->custom_control !== null ) {
				$this->wpc->add_control( new $control->custom_control( $this->wpc, $control->id, $control->control_args ) );
				$control_type = $control->custom_control;
			} else {
				$new_control  = $this->wpc->add_control( $control->id, $control->control_args );
				$control_type = isset( $control->control_args['type'] ) ? $control->control_args['type'] : $new_control->type;
			}
			if ( isset( $control->control_args['live_refresh_selector'] ) && $control->control_args['live_refresh_selector'] !== false ) {
				$control_args = [
					'selector'   => $control->control_args['live_refresh_selector'],
					'id'         => $control->id,
					'type'       => $control_type,
					'additional' => isset( $control->control_args['live_refresh_css_prop'] ) ? $control->control_args['live_refresh_css_prop'] : false,
				];

				add_filter(
					'difl_customize_preview_localization',
					function ( $array ) use ( $control_args ) {
						if ( ! isset( $array[ $control_args['type'] ] ) ) {
							$array[ $control_args['type'] ] = [];
						}
						$array[ $control_args['type'] ][ $control_args['id'] ] = [
							'selector'   => $control_args['selector'],
							'additional' => $control_args['additional'],
						];

						return $array;
					}
				);
			}
			if ( isset( $control->control_args['conditional_header'] ) && $control->control_args['conditional_header'] ) {
				$id = $control->id;
				add_filter(
					'difl_pro_react_controls_localization',
					function ( $array ) use ( $id ) {
						$array['headerControls'][] = $id;

						return $array;
					}
				);
			}
			if ( isset( $control->partial ) ) {
				$this->add_partial( new Partial( $control->id, $control->partial ) );
			}
		}
	}

	/**
	 * Register control types defined to work with Underscores template.
	 */
	private function register_types() {
		$types = $this->types_to_register;
		foreach ( $types as $object => $type ) {
			call_user_func_array( [ $this->wpc, 'register_' . $type . '_type' ], [ $object ] );
		}
	}

	/**
	 * Register all the defined controls.
	 */
	private function register_partials() {
		$partials = $this->partials_to_register;
		foreach ( $partials as $partial ) {
			if ( empty( $partial ) ) {
				continue;
			}
			$this->wpc->selective_refresh->add_partial( $partial->id, $partial->args );
		}
	}

	/**
	 * Add the controls to load.
	 *
	 * @param Control $control control to add.
	 */
	public function add_control( Control $control ) {
		array_push( $this->controls_to_register, $control );
	}

	/**
	 * Add the sections to load.
	 *
	 * @param Section $section section to add.
	 */
	public function add_section( Section $section ) {
		array_push( $this->sections_to_register, $section );
	}

	/**
	 * Add the panels to load.
	 *
	 * @param Panel $panel panel to add.
	 */
	public function add_panel( Panel $panel ) {
		array_push( $this->panels_to_register, $panel );

	}

	/**
	 * Add types that will be registered for .
	 *
	 * @param string $object_name the object name that will be registered.
	 * @param string $type        the type of object to register [panel, section, control].
	 */
	public function register_type( $object_name, $type ) {
		$accepted_types = [ 'panel', 'section', 'control' ];
		if ( ! in_array( $type, $accepted_types, true ) ) {
			return;
		}
		$this->types_to_register[ $object_name ] = $type;
	}

	/**
	 * Add the partials to load.
	 *
	 * @param Partial $partial partial to add.
	 */
	public function add_partial( Partial $partial ) {
		if ( empty( $partial->args ) ) {
			return;
		}
		array_push( $this->partials_to_register, $partial );
	}

	protected function add_color_controls( $args = [] ) {
		$key      = $args['key'];
		$default  = $args['default'];
		$label    = $args['label'];
		$priority = $args['priority'];

		$this->add_control(
			new Control(
				static::SECTION . $key,
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => $default,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( $label, 'divi_flash' ),
					'section'         => static::SECTION,
					'priority'        => $priority,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);
	}

	protected function add_select_controls( $args = [] ) {
		$key      = $args['key'];
		$default  = $args['default'];
		$label    = $args['label'];
		$options  = $args['options'];
		$priority = $args['priority'];
		$this->add_control(
			new Control(
				static::SECTION . $key,
				[
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => $default,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( $label, 'divi_flash' ),
					'section'         => static::SECTION,
					'priority'        => $priority,
					'type'            => 'select',
					'choices'         => $options,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);
	}

	protected function add_range_controls( $args = [] ) {
		$key         = $args['key'];
		$default     = $args['default'];
		$label       = $args['label'];
		$description = $args['description'];
		$options     = $args['options'];
		$priority    = $args['priority'];

		$this->add_control(
			new Control(
				static::SECTION . $key,
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => 2,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( $label, 'divi_flash' ),
					'description'     => esc_html__( $description, 'divi_flash' ),
					'section'         => static::SECTION,
					'step'            => 1,
					'input_attrs'     => [
						'min'        => $options['min'],
						'max'        => $options['max'],
						'defaultVal' => $default,
					],
					'input_attr'      => [
						'min'     => $options['min'],
						'max'     => $options['max'],
						'step'    => $options['step'],
						'default' => $default,
					],
					'priority'        => $priority,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);
	}

	protected function add_responsive_range_control( $args = [] ) {
		$key      = $args['key'];
		$default  = $args['default'];
		$label    = $args['label'];
		$options  = $args['options'];
		$priority = $args['priority'];
		$this->add_control(
			new Control(
				static::SECTION . $key,
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => $default,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( $label, 'divi_flash' ),
					'section'         => static::SECTION,
					'step'            => 1,
					'input_attrs'     => [
						'min'        => $options['min'],
						'max'        => $options['max'],
						'defaultVal' => $default,
					],
					'input_attr'      => [
						'min'     => $options['min'],
						'max'     => $options['max'],
						'step'    => $options['step'],
						'default' => $default,
					],
					'priority'        => $priority,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range' ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Responsive_Range'
			)
		);
	}

	protected function get_font() {
		$site_domain = get_locale();

		$google_fonts = et_builder_get_fonts(
			[
				'prepend_standard_fonts' => false,
			]
		);

		$user_fonts = et_builder_get_custom_fonts();

		// combine google fonts with custom user fonts.
		$google_fonts = array_merge( $user_fonts, $google_fonts );

		$et_domain_fonts = [
			'ru_RU' => 'cyrillic',
			'uk'    => 'cyrillic',
			'bg_BG' => 'cyrillic',
			'vi'    => 'vietnamese',
			'el'    => 'greek',
			'ar'    => 'arabic',
			'he_IL' => 'hebrew',
			'th'    => 'thai',
			'si_lk' => 'sinhala',
			'bn_bd' => 'bengali',
			'ta_lk' => 'tamil',
			'te'    => 'telegu',
			'km'    => 'khmer',
			'kn'    => 'kannada',
			'ml_in' => 'malayalam',
			'ja'    => 'japanese',
			'ko_KR' => 'korean',
			'ml_IN' => 'malayalam',
			'zh_CN' => 'chinese-simplified',
		];

		$font_choices         = [];
		$font_choices['none'] = [
			'label' => 'Default Theme Font',
		];

		$removed_fonts_mapping = et_builder_old_fonts_mapping();

		foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
			$use_parent_font = false;

			if ( isset( $removed_fonts_mapping[ $google_font_name ] ) ) {
				$parent_font                             = $removed_fonts_mapping[ $google_font_name ]['parent_font'];
				$google_font_properties['character_set'] = $google_fonts[ $parent_font ]['character_set'];
				$use_parent_font                         = true;
			}

			if ( '' !== $site_domain && isset( $et_domain_fonts[ $site_domain ] ) && isset( $google_font_properties['character_set'] ) && false === strpos( $google_font_properties['character_set'], $et_domain_fonts[ $site_domain ] ) ) {
				continue;
			}

			$font_choices[ $google_font_name ] = [
				'label' => $google_font_name,
				'data'  => [
					'parent_font'    => $use_parent_font ? $google_font_properties['parent_font'] : '',
					'parent_styles'  => $use_parent_font ? $google_fonts[ $parent_font ]['styles'] : $google_font_properties['styles'],
					'current_styles' => $use_parent_font && isset( $google_fonts[ $parent_font ]['styles'] ) && isset( $google_font_properties['styles'] ) ? $google_font_properties['styles'] : '',
					'parent_subset'  => $use_parent_font && isset( $google_fonts[ $parent_font ]['character_set'] ) ? $google_fonts[ $parent_font ]['character_set'] : '',
					'standard'       => isset( $google_font_properties['standard'] ) && $google_font_properties['standard'] ? 'on' : 'off',
				],
			];
		}

		return $font_choices;
	}

	/**
	 * Get customizer object.
	 *
	 * @param string $type object type [ section, control, setting, panel ].
	 * @param string $id   the id of the customizer object.
	 *
	 * @return mixed|null
	 */
	public function get_customizer_object( $type, $id ) {
		$accepted_types = [ 'setting', 'control', 'section', 'panel' ];
		if ( ! in_array( $type, $accepted_types, true ) ) {
			return null;
		}
		$object = call_user_func_array( [ $this->wpc, 'get_' . $type ], [ $id ] );
		if ( empty( $object ) ) {
			return null;
		}

		return $object;
	}

	/**
	 * Change a customizer object.
	 *
	 * @param string               $type     object type [ section, control, setting, panel ].
	 * @param string               $id       id of object.
	 * @param string               $property property to change.
	 * @param string|integer|array $value    the value.
	 */
	public function change_customizer_object( $type, $id, $property, $value ) {
		$accepted_types = [ 'setting', 'control', 'section', 'panel' ];
		if ( ! in_array( $type, $accepted_types, true ) ) {
			return;
		}
		$object = call_user_func_array( [ $this->wpc, 'get_' . $type ], [ $id ] );

		if ( empty( $object ) ) {
			return;
		}

		$object->$property = $value;
	}

	/**
	 * Function to add controls that form the boxed layout.
	 *
	 * @param string $id       Controls short id.
	 * @param array  $settings Controls settings.
	 */
	public function add_boxed_layout_controls( $id, $settings ) {
		$this->add_control(
			new Control(
				'difl_' . $id . '_boxed_layout',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => array_key_exists( 'is_boxed_default', $settings ) ? $settings['is_boxed_default'] : false,
				],
				[
					'label'           => esc_html__( 'Boxed layout', 'divi_flash' ),
					'section'         => $settings['section'],
					'type'            => 'difl_toggle_control',
					'priority'        => $settings['priority'],
					'active_callback' => array_key_exists( 'toggle_active_callback', $settings ) ? $settings['toggle_active_callback'] : '__return_true',
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$padding_live_refresh_settings = [
			'responsive'  => true,
			'directional' => true,
			'template'    =>
				$settings['boxed_selector'] . '{
							padding-top: {{value.top}};
							padding-right: {{value.right}};
							padding-bottom: {{value.bottom}};
							padding-left: {{value.left}};
						}',
		];

		$background_live_refresh_settings = [
			'template' =>
				$settings['boxed_selector'] . '{
				   background-color: {{value}};
			    }',

		];

		$has_text_color = isset( $settings['has_text_color'] ) ? $settings['has_text_color'] : true;
		if ( $has_text_color ) {
			$template = $settings['text_color_css_selector'] . '{ color: {{value}}; }';
			if ( array_key_exists( 'border_color_css_selector', $settings ) ) {
				$template .= $settings['border_color_css_selector'] . '{ border-color: {{value}}; }';
			}
			$color_live_refresh_settings = [
				'template' => $template,
				'cssVar'   => [
					'vars'     => '--color',
					'selector' => $settings['boxed_selector'],
				],
			];
		}

		$padding_live_refresh_settings = [
			'cssVar' => [
				'vars'       => '--padding',
				'selector'   => $settings['boxed_selector'],
				'responsive' => true,
			],
		];

		$background_live_refresh_settings = [
			'cssVar' => [
				'vars'     => '--bgcolor',
				'selector' => $settings['boxed_selector'],
			],
		];

		$this->add_control(
			new Control(
				'difl_' . $id . '_boxed_padding',
				[
					'sanitize_callback' => [ $this, 'sanitize_spacing_array' ],
					'transport'         => $this->selective_refresh,
					'default'           => array_key_exists( 'padding_default', $settings ) ? $settings['padding_default'] : false,
				],
				[
					'label'                 => esc_html__( 'Section padding', 'divi_flash' ),
					'section'               => $settings['section'],
					'input_attrs'           => [
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
					],
					'default'               => array_key_exists( 'padding_default', $settings ) ? $settings['padding_default'] : false,
					'priority'              => $settings['priority'],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => $padding_live_refresh_settings,
					'active_callback'       => array_key_exists( 'active_callback', $settings ) ? $settings['active_callback'] : false,
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);

		$this->add_control(
			new Control(
				'difl_' . $id . '_boxed_background_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => array_key_exists( 'background_default', $settings ) ? $settings['background_default'] : false,
				],
				[
					'label'                 => esc_html__( 'Background color', 'divi_flash' ),
					'section'               => $settings['section'],
					'priority'              => $settings['priority'],
					'input_attrs'           => [
						'allow_gradient' => true,
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => $background_live_refresh_settings,
					'active_callback'       => array_key_exists( 'active_callback', $settings ) ? $settings['active_callback'] : false,
				],
				'DIFL\Customizer\Controls\React\Color'
			)
		);

		if ( $has_text_color ) {
			$this->add_control(
				new Control(
					'difl_' . $id . '_boxed_text_color',
					[
						'sanitize_callback' => 'difl_sanitize_colors',
						'transport'         => $this->selective_refresh,
						'default'           => array_key_exists( 'color_default', $settings ) ? $settings['color_default'] : false,
					],
					[
						'label'                 => esc_html__( 'Text color', 'divi_flash' ),
						'section'               => $settings['section'],
						'priority'              => $settings['priority'],
						'live_refresh_selector' => true,
						'live_refresh_css_prop' => $color_live_refresh_settings,
						'active_callback'       => array_key_exists( 'active_callback', $settings ) ? $settings['active_callback'] : false,
					],
					'DIFL\Customizer\Controls\React\Color'
				)
			);
		}
	}
}
