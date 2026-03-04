<?php

namespace DIFL\Customizer\Extensions;

use DIFL\Customizer\Base_Customizer;
use DIFL\Customizer\Types\Control;
use DIFL\Customizer\Types\Section;

class Login_Form extends Base_Customizer {

	const SECTION = 'difl_login_form_';

	const UNIT = [ 'px', 'em', 'rem' ];

	private static $decorations;

	public function init() {
		self::$decorations = [
			'none'         => esc_html__( 'None', 'divi_flash' ),
			'line-through' => esc_html__( 'Line Through', 'divi_flash' ),
			'underline'    => esc_html__( 'Underline', 'divi_flash' ),
			'overline'     => esc_html__( 'Overline', 'divi_flash' ),
		];
		parent::init();
		add_action( 'customize_controls_print_footer_scripts', [ $this, 'live_refresh_scripts' ] );
		add_action( 'login_enqueue_scripts', [ $this, 'customize_script' ], PHP_INT_MAX );
	}

	public function show_login_form() {
		if ( is_customize_preview() ) {
			$login_url   = get_option( 'siteurl' ) . '/wp-login.php';
			$current_url = get_permalink();
			if ( $login_url !== $current_url ) {
				wp_redirect( $login_url );
				exit;
			}
			exit();
		}
	}

	public function add_controls() {
		$this->add_sections();
		$this->add_logo_controls();
		$this->add_bg_controls();
		$this->add_font_controls();
		$this->add_login_form_controls();
		$this->add_button_controls();
		$this->add_form_footer_controls();

	}

	private function add_sections() {
		$this->add_section(
			new Section(
				self::SECTION,
				[
					'priority' => 81,
					'title'    => esc_html__( 'Login Form', 'divi_flash' ),
					'panel'    => 'difl_advanced_genaral',
				]
			)
		);

	}

	private function add_logo_controls() {
		$this->add_control(
			new Control(
				self::SECTION . 'logo',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Logo', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 8,
					'class'            => 'difl-login-form-logo',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 4,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'disable_logo',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Disable Logo', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 10,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);
		$this->add_control(
			new Control(
				self::SECTION . 'logo_image',
				[
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Image', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 11,
					'class'           => 'scroll-to-top-general',
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\WP_Customize_Upload_Control'
			)
		);

		$this->add_responsive_range_control(
			[
				'key'             => 'logo_width',
				'default'         => 16,
				'label'           => esc_html__( 'Width', 'divi_flash' ),
				'description'     => esc_html__( 'Width', 'divi_flash' ),
				'options'         => [
					'min'  => 10,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 12,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_control(
			new Control(
				self::SECTION . 'logo_width',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => 30,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Image Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 100,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 100,
							'tablet'  => 100,
							'desktop' => 100,
						],
						'units'      => self::UNIT,
					],
					'priority'        => 12,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'logo_bottom_spacing',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => 16,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Bottom Spacing', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 100,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 100,
							'tablet'  => 100,
							'desktop' => 100,
						],
						'units'      => self::UNIT,
					],
					'priority'        => 13,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);
	}

	private function add_bg_controls() {
		$this->add_control(
			new Control(
				self::SECTION . 'background',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Background', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 14,
					'class'            => 'difl-login-form-background',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 5,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);


		$this->add_control(
			new Control(
				self::SECTION . 'background_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#ffffff',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Background Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 15,
					'input_attrs'     => [
						'allow_gradient' => true,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'background_image',
				[
					'transport' => $this->selective_refresh,
				],
				[
					'label'            => esc_html__( 'Background Image', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 16,
					'class'            => 'scroll-to-top-general',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 5,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'\WP_Customize_Upload_Control'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'background_repeat',
				[
					'sanitize_callback' => '',
					'default'           => 'no-repeat',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Background Repeat', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 17,
					'type'            => 'select',
					'choices'         => [
						'no-repeat' => esc_html__( 'No Repeat', 'divi_flash' ),
						'repeat'    => esc_html__( 'Repeat', 'divi_flash' ),
						'repeat-x'  => esc_html__( 'Repeat Horizontally', 'divi_flash' ),
						'repeat-y'  => esc_html__( 'Repeat Vertically', 'divi_flash' ),
						'space'     => esc_html__( 'Space', 'divi_flash' ),
						'round'     => esc_html__( 'Round', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'background_position',
				[
					'sanitize_callback' => '',
					'default'           => 'center center',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Background Position', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 18,
					'type'            => 'select',
					'choices'         => [
						'top left'      => esc_html__( 'Top Left', 'divi_flash' ),
						'top center'    => esc_html__( 'Top Center', 'divi_flash' ),
						'top right'     => esc_html__( 'Top Right', 'divi_flash' ),
						'center left'   => esc_html__( 'Center Left', 'divi_flash' ),
						'center center' => esc_html__( 'Center', 'divi_flash' ),
						'center right'  => esc_html__( 'Center Right', 'divi_flash' ),
						'bottom left'   => esc_html__( 'Bottom Left', 'divi_flash' ),
						'bottom center' => esc_html__( 'Bottom Center', 'divi_flash' ),
						'bottom right'  => esc_html__( 'Bottom Right', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'background_size',
				[
					'sanitize_callback' => '',
					'default'           => 'cover',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Background Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 19,
					'type'            => 'select',
					'choices'         => [
						'contain' => esc_html__( 'Contain', 'divi_flash' ),
						'cover'   => esc_html__( 'Cover', 'divi_flash' ),
						'auto'    => esc_html__( 'Auto', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);
	}

	private function add_font_controls() {
		$this->add_control(
			new Control(
				self::SECTION . 'font',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Font Family', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 20,
					'class'            => 'difl-login-form-font',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 1,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);
		$this->add_control(
			new Control(
				self::SECTION . 'font_family',
				[
					'sanitize_callback' => '',
					'default'           => 'none',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Font Family', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 20,
					'type'            => 'select',
					'choices'         => $this->get_font(),
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Divi_Select'
			)
		);
	}

	private function add_login_form_controls() {
		$this->add_control(
			new Control(
				self::SECTION . 'form',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Form', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 21,
					'class'            => 'difl-login-form-form-container',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 28,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);

//		$this->add_control(
//			new Control(
//				self::SECTION . 'form_alignment',
//				[
//					'sanitize_callback' => '',
//					'default'           => 'center',
//					'transport'         => $this->selective_refresh,
//				],
//				[
//					'label'           => esc_html__( 'Alignment', 'divi_flash' ),
//					'section'         => self::SECTION,
//					'priority'        => 22,
//					'type'            => 'select',
//					'choices'         => [
//						'left'   => esc_html__( 'Left', 'divi_flash' ),
//						'center' => esc_html__( 'Center', 'divi_flash' ),
//						'right'  => esc_html__( 'Right', 'divi_flash' ),
//					],
//					'active_callback' => [ $this, 'is_extension_enabled' ],
//				]
//			)
//		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_remove_background',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Remove Background', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 22,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_background_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#ffffff',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Background Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 24,
					'input_attrs'     => [
						'allow_gradient' => true,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_background_image',
				[
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Background Image', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 25,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\WP_Customize_Upload_Control'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_width',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '{ "mobile": "400", "tablet": "400", "desktop": "400" }',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Width', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'mobile'  => [
							'min'     => 1,
							'max'     => 1000,
							'default' => 400,
						],
						'tablet'  => [
							'min'     => 1,
							'max'     => 1000,
							'default' => 400,
						],
						'desktop' => [
							'min'     => 1,
							'max'     => 1000,
							'default' => 400,
						],
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 1000,
						'defaultVal' => [
							'mobile'  => 400,
							'tablet'  => 400,
							'desktop' => 400,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => self::UNIT,
					],
					'priority'        => 26,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_height',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '{ "mobile": "400", "tablet": "400", "desktop": "400" }',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Height', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'mobile'  => [
							'min'     => 1,
							'max'     => 1000,
							'default' => 400,
						],
						'tablet'  => [
							'min'     => 1,
							'max'     => 1000,
							'default' => 400,
						],
						'desktop' => [
							'min'     => 1,
							'max'     => 1000,
							'default' => 400,
						],
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 1000,
						'defaultVal' => [
							'mobile'  => 400,
							'tablet'  => 400,
							'desktop' => 400,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => self::UNIT,
					],
					'priority'        => 27,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_border_width',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '2',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Border Width', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 30,
						'default' => 2,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 30,
						'defaultVal' => [
							'mobile'  => 2,
							'tablet'  => 2,
							'desktop' => 2,
						],
						'units'      => [ 'px', 'em', 'rem' ],
					],
					'priority'        => 28,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_border_radius',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '5',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Border Radius', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 5,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 5,
							'tablet'  => 5,
							'desktop' => 5,
						],
						'units'      => [ 'px', 'em', 'rem' ],
					],
					'priority'        => 29,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_color_controls(
			[
				'key'             => 'form_border_color',
				'default'         => '#000000',
				'label'           => esc_html__( 'Border Color', 'divi_flash' ),
				'section'         => self::SECTION,
				'priority'        => 30,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_box_shadow',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Box Shadow', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 31,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_box_shadow_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#FFFFFF',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Box Shadow Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 32,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_range_controls(
			[
				'key'             => 'form_box_shadow_blur',
				'default'         => 2,
				'label'           => esc_html__( 'Box Shadow Blur (PX)', 'divi_flash' ),
				'description'     => esc_html__( 'Blur Offset for shadow', 'divi_flash' ),
				'options'         => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 33,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_range_controls(
			[
				'key'             => 'form_box_shadow_horizontal',
				'default'         => 2,
				'label'           => esc_html__( 'Box Shadow Horizontal (PX)', 'divi_flash' ),
				'description'     => esc_html__( 'Horizontal Offset for shadow', 'divi_flash' ),
				'options'         => [
					'min'  => - 100,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 34,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_range_controls(
			[
				'key'             => 'form_box_shadow_vertical',
				'default'         => 2,
				'label'           => esc_html__( 'Box Shadow Vertical (PX)', 'divi_flash' ),
				'description'     => esc_html__( 'Vertical Offset for shadow', 'divi_flash' ),
				'options'         => [
					'min'  => - 100,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 35,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_range_controls(
			[
				'key'             => 'form_box_shadow_spread',
				'default'         => 2,
				'label'           => esc_html__( 'Box Shadow Spread (PX)', 'divi_flash' ),
				'description'     => esc_html__( 'Spread Offset for shadow', 'divi_flash' ),
				'options'         => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 36,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_padding',
				[
					'default'   => self::$default_space_values,
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => __( 'Form Padding', 'divi_flash' ),
					'section'         => self::SECTION,
					'input_attrs'     => [
						'units' => self::UNIT,
					],
					'default'         => self::$default_space_values,
					'priority'        => 37,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_text_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '16',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Input Field Text Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 16,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
						],
					],
					'priority'        => 38,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_text_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Input Field Text Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 39,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_margin',
				[
					'default'   => self::$default_space_values,
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => __( 'Input Field Margin', 'divi_flash' ),
					'section'         => self::SECTION,
					'input_attrs'     => [
						'units' => self::UNIT,
					],
					'default'         => self::$default_space_values,
					'priority'        => 40,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_padding',
				[
					'default'   => self::$default_space_values,
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => __( 'Input Field Padding', 'divi_flash' ),
					'section'         => self::SECTION,
					'input_attrs'     => [
						'units' => self::UNIT,
					],
					'default'         => self::$default_space_values,
					'priority'        => 41,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_border_width',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '1',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Input Field Border Width', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 30,
						'default' => 1,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 30,
						'defaultVal' => [
							'mobile'  => 1,
							'tablet'  => 1,
							'desktop' => 1,
						],
					],
					'priority'        => 42,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_border_radius',
				[
					'default'   => self::$default_space_values,
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => __( 'Input Field Border Radius', 'divi_flash' ),
					'section'         => self::SECTION,
					'input_attrs'     => [
						'units' => self::UNIT,
					],
					'default'         => self::$default_space_values,
					'priority'        => 43,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_border_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Input Field Border Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 44,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_background_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#ffffff',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Input Field Background', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 45,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_background_color_active',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#ffffff',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Active Input Field Background', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 46,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_label_font_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '16',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Label Font Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 16,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
						],
					],
					'priority'        => 47,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_label_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Label Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 48,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_remember_me_label_font_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '16',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Remember Me Label Font Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 16,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
						],
					],
					'priority'        => 49,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_input_field_remember_me_label_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Remember Me Label Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 50,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);
	}

	private function add_button_controls() {
		$this->add_control(
			new Control(
				self::SECTION . 'button',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Button', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 51,
					'class'            => 'difl-login-form-button',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 19,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_text_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '16',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Text Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 16,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
						],
					],
					'priority'        => 52,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_text_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Text Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 53,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_text_hover_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Text Hover Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 54,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_background_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#ffffff',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Background Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 55,
					'input_attrs'     => [
						'allow_gradient' => true,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_background_color_hover',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#ffffff',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Background Hover Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 56,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_border_width',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '1',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Border Width', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 30,
						'default' => 1,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 30,
						'defaultVal' => [
							'mobile'  => 1,
							'tablet'  => 1,
							'desktop' => 1,
						],
					],
					'priority'        => 57,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_border_radius',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '5',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Border Radius', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 5,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 5,
							'tablet'  => 5,
							'desktop' => 5,
						],
					],
					'priority'        => 58,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_border_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Border Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 59,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_border_color_hover',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Border Hover Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 60,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_enable_shadow',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Enable Shadow', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 61,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_shadow_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Shadow Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 62,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_range_controls(
			[
				'key'             => 'button_shadow_blur',
				'default'         => 2,
				'label'           => esc_html__( 'Shadow Blur(PX)', 'divi_flash' ),
				'description'     => esc_html__( 'Blur Offset for shadow', 'divi_flash' ),
				'options'         => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 63,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_range_controls(
			[
				'key'             => 'button_shadow_horizontal',
				'default'         => 2,
				'label'           => esc_html__( 'Shadow Horizontal(PX)', 'divi_flash' ),
				'description'     => esc_html__( 'Horizontal Offset for shadow', 'divi_flash' ),
				'options'         => [
					'min'  => - 100,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 64,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_range_controls(
			[
				'key'             => 'button_shadow_vertical',
				'default'         => 2,
				'label'           => esc_html__( 'Shadow Vertical(PX)', 'divi_flash' ),
				'description'     => esc_html__( 'Vertical Offset for shadow', 'divi_flash' ),
				'options'         => [
					'min'  => - 100,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 65,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_range_controls(
			[
				'key'             => 'button_shadow_spread',
				'default'         => 2,
				'label'           => esc_html__( 'Shadow Spread(PX)', 'divi_flash' ),
				'description'     => esc_html__( 'Spread Offset for shadow', 'divi_flash' ),
				'options'         => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 66,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_shadow_hover_enable',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Shadow Hover Enable', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 67,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_shadow_hover_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#000000',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Shadow Hover Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 68,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_shadow_hover_blur',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => 2,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Shadow Hover Blur(PX)', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 2,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => 2,
					],
					'priority'        => 69,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range', false ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'button_padding',
				[
					'default'   => self::$default_space_values,
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => __( 'Padding', 'divi_flash' ),
					'section'         => self::SECTION,
					'input_attrs'     => [
						'units' => self::UNIT,
					],
					'default'         => self::$default_space_values,
					'priority'        => 70,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);

	}

	private function add_form_footer_controls() {
		$this->add_control(
			new Control(
				self::SECTION . 'form_footer',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Form Footer', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 71,
					'class'            => 'difl-login-form-form-footer',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 16,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_footer_disable_lost_password',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Disable Lost Password', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 72,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_footer_lost_password_text_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '16',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Lost Password Text Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'priority'        => 73,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 16,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
						],
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_color_controls(
			[
				'key'             => 'form_footer_lost_password_text_color',
				'default'         => '#000000',
				'label'           => esc_html__( 'Lost Password Text Color', 'divi_flash' ),
				'section'         => self::SECTION,
				'priority'        => 74,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_color_controls(
			[
				'key'             => 'form_footer_lost_password_text_hover_color',
				'default'         => '#000000',
				'label'           => esc_html__( 'Lost Password Text Hover Color', 'divi_flash' ),
				'section'         => self::SECTION,
				'priority'        => 75,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_select_controls(
			[
				'key'      => 'form_footer_lost_password_text_decoration',
				'default'  => 'none',
				'label'    => 'Lost Password Text Decoration',
				'options'  => self::$decorations,
				'priority' => 76,
			]
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_footer_disable_back_to_site',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Disable Back to Site', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 77,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_footer_back_to_site_text_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '16',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Back to Site Text Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 100,
						'default' => 16,
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
						],
					],
					'priority'        => 78,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_color_controls(
			[
				'key'             => 'form_footer_back_to_site_text_color',
				'default'         => '#000000',
				'label'           => esc_html__( 'Back to Site Text Color', 'divi_flash' ),
				'section'         => self::SECTION,
				'priority'        => 79,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);
		$this->add_color_controls(
			[
				'key'             => 'form_footer_back_to_site_text_hover_color',
				'default'         => '#000000',
				'label'           => esc_html__( 'Back to Site Text Hover Color', 'divi_flash' ),
				'section'         => self::SECTION,
				'priority'        => 80,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_select_controls(
			[
				'key'      => 'form_footer_back_to_site_text_decoration',
				'default'  => 'none',
				'label'    => 'Back to Site Text Decoration',
				'options'  => self::$decorations,
				'priority' => 81,
			]
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_footer_enable_copyright',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Enable Copyright', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 82,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_footer_copyright_text',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'Copyright',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Copyright Note', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'text',
					'priority'        => 83,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		$this->add_color_controls(
			[
				'key'             => 'form_footer_copyright_text_color',
				'default'         => '#000000',
				'label'           => esc_html__( 'Copyright Text Color', 'divi_flash' ),
				'section'         => self::SECTION,
				'priority'        => 84,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_control(
			new Control(
				self::SECTION . 'form_footer_copyright_text_background_color',
				[
					'sanitize_callback' => 'difl_sanitize_colors',
					'default'           => '#ffffff',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Copyright Text Background Color', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 85,
					'input_attrs'     => [
						'allow_gradient' => false,
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Color'
			)
		);

		$this->add_responsive_range_control(
			[
				'key'             => 'form_footer_copyright_text_font_size',
				'default'         => 16,
				'label'           => esc_html__( 'Copyright Text Size', 'divi_flash' ),
				'description'     => esc_html__( 'Size', 'divi_flash' ),
				'options'         => [
					'min'  => 10,
					'max'  => 32,
					'step' => 1,
				],
				'priority'        => 86,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);

		$this->add_responsive_range_control(
			[
				'key'             => 'form_footer_copyright_text_padding',
				'default'         => 10,
				'label'           => esc_html__( 'Copyright Text Padding', 'divi_flash' ),
				'description'     => esc_html__( 'Padding', 'divi_flash' ),
				'options'         => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'priority'        => 87,
				'active_callback' => [ $this, 'is_extension_enabled' ],
			]
		);
	}

	public static function is_extension_enabled() {
		return true;
	}

	public function customize_script() {
		if ( ! is_customize_preview() && ! get_option( 'df_general_customize_login' ) ) {
			return;
		}
		?>
        <style>
            body #login {
                width: fit-content;
            }

            body #loginform {
                display: flex;
                flex-direction: column;
                justify-content: center;
                min-width: fit-content;
                min-height: fit-content;
            }

            body #wp-submit {
                transition: all .3s ease;
            }

            body #login #nav a, body #login #nav a:hover {
                text-decoration: inherit;
                color: inherit;
            }

            body .copyright {
                position: sticky;
                top: 100%;
                width: 100%;
                z-index: 10;
            }

            body #loginform #rememberme::before {
                width: inherit;
                height: inherit;
                float: none;
            }

            body #login .button.wp-hide-pw {
                height: 100% !important;
            }

            body #login .wp-login-logo a {
                background-position: center !important;
                background-size: contain !important;
            }
        </style>
		<?php
	}

	public function live_refresh_scripts() {
		if ( ! is_customize_preview() ) {
			return;
		}
		?>
        <script>
			(() => {
				wp.customize.bind( 'ready', function () {
					const protocol = window.location.protocol;
					const host = window.location.host;
					const login_form_url = `${ protocol }//${ host }/wp-admin/customize.php?url=${ protocol }//${ host }/wp-login.php&autofocus=difl_advanced_genaral`;

					wp.customize.section.each( function ( section ) {
						if ( section.id === 'difl_login_form_' ) {
							section.expanded.bind( function ( is_expanded ) {
								if ( is_expanded && section.id === 'difl_login_form_' && new URLSearchParams( window.location.search ).get( 'autofocus' ) !== 'difl_advanced_genaral' ) {
									window.location.replace( login_form_url );
								}
								if ( ! is_expanded && window.location.href === login_form_url ) {
									window.location.replace( `${ protocol }//${ host }/wp-admin/customize.php?autofocus[panel]=difl_advanced_genaral` );
								}
							} );
						}
					} );
				} );
			})();
			(() => {
					const handlePreview = () => {
						const PREFIX = 'difl_login_form_';
						const CPREFIX = `customize-control-${ PREFIX }`;
						const preview = window.frames[window.frames.length - 1].document;
						const login_form = preview.querySelector( 'body #login form' );
						if ( ! login_form ) {
							return;
						}

						if ( new URLSearchParams( window.location.search ).get( 'autofocus' ) === 'difl_advanced_genaral' ) {
							wp.customize.section( 'difl_login_form_' ).focus();
						}

						const body = preview.querySelector( 'body.login' );
						const login = preview.getElementById( 'login' );
						const logo_heading = login.querySelector( '.wp-login-logo' );
						const logo_link = login.querySelector( '.wp-login-logo a' );
						const form_input_field = login_form.querySelectorAll( 'input[type="text"], input[type="password"]' );
						const user_name = login_form.querySelector( '#user_login' );
						const user_name_label = login_form.querySelector( 'label[for="user_login"]' );
						const password = login_form.querySelector( '#user_pass' );
						const password_label = login_form.querySelector( 'label[for="user_pass"]' );
						const remember_me = login_form.querySelector( '#rememberme' );
						const form_inputs = login_form.querySelectorAll( 'input[id="user_login"], input[id="user_pass"]' );
						const form_labels = login_form.querySelectorAll( 'label[for="user_login"], label[for="user_pass"], #login-message p' );
						const remember_me_checkbox = login_form.querySelector( '#rememberme' );
						const remember_me_label = login_form.querySelector( 'label[for="rememberme"]' );
						const button = login_form.querySelector( '#wp-submit' );
						const back_to_site = body.querySelector( '#backtoblog' );
						const back_to_site_text = back_to_site.querySelector( 'a' );
						const lost_password = body.querySelector( '#nav' );
						const logo = login.querySelector( '.wp-login-logo a' );
						const get_current_device = () => {
							let device = null;
							device = window.parent.document.querySelector( '.devices-wrapper button.active' );
							if ( ! device ) {
								return 'desktop';
							}
							return device.dataset.device;
						}

						const get_responsive_value = ( string, type = 'single' ) => {
							if ( ! string ) {
								return '';
							}

							const device = get_current_device();
							let sizes = string;

							if ( type === 'single' ) {
								const data = JSON.parse( sizes );
								const unit = data.suffix && device in data.suffix ? data.suffix[device] : 'px';

								return `${ data[device] }${ unit }`;
							}

							if ( type === 'quad' ) {
								const data = typeof sizes === 'object' ? sizes : JSON.parse( sizes );
								const unit = data[`${ device }-unit`] || 'px';
								const deviceData = Object.values( data[device] ).map( side => `${ side }${ unit }` );

								return deviceData.join( ' ' );
							}

							return '';
						}
						const getElementById = id => window.parent.document.getElementById( id );
						const hideElement = id => getElementById( id ).style.display = 'none';
						const showElement = id => getElementById( id ).style.display = 'block';
						const logo_control = [ 'logo_image', 'logo_width', 'logo_bottom_spacing' ];
						const form_background_controls = [ 'form_background_image', 'form_background_color', ];
						const box_shadow_control = [ 'form_box_shadow_color', 'form_box_shadow_blur', 'form_box_shadow_horizontal', 'form_box_shadow_vertical', 'form_box_shadow_spread', ];
						const button_shadow_control = [ 'button_shadow_color', 'button_shadow_blur', 'button_shadow_spread', 'button_shadow_horizontal', 'button_shadow_vertical' ];
						const button_shadow_hover_control = [ 'button_shadow_hover_color', 'button_shadow_hover_blur' ];
						const copyright_control = [ 'form_footer_copyright_text', 'form_footer_copyright_text_color', 'form_footer_copyright_text_background_color', 'form_footer_copyright_text_font_size', 'form_footer_copyright_text_padding' ];
						const back_to_site_control = [ 'form_footer_back_to_site_text_size', 'form_footer_back_to_site_text_color', 'form_footer_back_to_site_text_hover_color', 'form_footer_back_to_site_text_decoration' ];
						const lost_password_control = [ 'form_footer_lost_password_text_size', 'form_footer_lost_password_text_color', 'form_footer_lost_password_text_hover_color', 'form_footer_lost_password_text_decoration' ];
						const showHideControl = ( control, show ) => {
							control.forEach( element => {
								if ( show ) {
									showElement( `${ CPREFIX }${ element }` );
								} else {
									hideElement( `${ CPREFIX }${ element }` );
								}
							} )
						}
						// Logo control
						if ( wp.customize( `${ PREFIX }logo_image` ).get() ) {
							logo.style.backgroundImage = `url(${ wp.customize( `${ PREFIX }logo_image` ).get() })`;
							logo.href = _wpCustomizeSettings.url.home
							logo.setAttribute( 'target', '_blank' )
						}
						logo.style.backgroundSize = get_responsive_value( wp.customize( `${ PREFIX }logo_width` ).get() );
						logo.style.width = get_responsive_value( wp.customize( `${ PREFIX }logo_width` ).get() );
						logo.style.height = get_responsive_value( wp.customize( `${ PREFIX }logo_width` ).get() );
						logo_heading.style.marginBottom = get_responsive_value( wp.customize( `${ PREFIX }logo_bottom_spacing` ).get() );

						wp.customize( `${ PREFIX }disable_logo`, value => {
							value.bind( function ( newval ) {
								showHideControl( logo_control, ! newval );
								if ( newval ) {
									logo.style.display = 'none';
								} else {
									logo.style.display = 'block';
								}
							} )
						} );

						if ( ! wp.customize( `${ PREFIX }disable_logo` ).get() ) {
							wp.customize( `${ PREFIX }logo_image`, value => {
								value.bind( function ( newval ) {
									if ( newval ) {
										logo.style.backgroundImage = `url(${ newval })`;
									} else {
										logo.style.backgroundImage = '';
									}
								} )
							} );
							wp.customize( `${ PREFIX }logo_width`, value => {
								value.bind( function ( newval ) {
									const logo_width = get_responsive_value( newval );
									logo.style.width = logo_width;
									logo.style.height = logo_width;
									logo.style.backgroundSize = logo_width;
								} )
							} );
							wp.customize( `${ PREFIX }logo_height`, value => {
								value.bind( function ( newval ) {
									// logo.style.height = get_responsive_value( newval );
								} )
							} );
							wp.customize( `${ PREFIX }logo_bottom_spacing`, value => {
								value.bind( function ( newval ) {
									logo_heading.style.marginBottom = get_responsive_value( newval );
								} )
							} );
							logo.href = _wpCustomizeSettings.url.home
							logo.setAttribute( 'target', '_blank' )
						}

						// Handle Background

						if ( wp.customize( `${ PREFIX }background_color` ).get() && ! wp.customize( `${ PREFIX }background_color` ).get().startsWith( 'linear-gradient' ) ) {
							body.style.backgroundColor = wp.customize( `${ PREFIX }background_color` ).get();
						} else {
							body.style.backgroundImage = wp.customize( `${ PREFIX }background_color` ).get();
						}

						if ( wp.customize( `${ PREFIX }background_image` ).get() ) {
							body.style.backgroundImage = `url(${ wp.customize( `${ PREFIX }background_image` ).get() })`;
						}

						if ( wp.customize( `${ PREFIX }background_repeat` ).get() ) {
							body.style.backgroundRepeat = wp.customize( `${ PREFIX }background_repeat` ).get();
						}

						if ( wp.customize( `${ PREFIX }background_position` ).get() ) {
							body.style.backgroundPosition = wp.customize( `${ PREFIX }background_position` ).get();
						}

						if ( wp.customize( `${ PREFIX }background_size` ).get() ) {
							body.style.backgroundSize = wp.customize( `${ PREFIX }background_size` ).get();
						}

						wp.customize( `${ PREFIX }background_image`, value => {
							value.bind( function ( newval ) {
								const bg_color = wp.customize( `${ PREFIX }background_color` ).get();
								if ( newval ) {
									body.style.backgroundImage = `url(${ newval })`;
									body.style.backgroundSize = 'cover';
									body.style.backgroundPosition = 'center';
									body.style.backgroundRepeat = 'no-repeat';
								} else {
									body.style.backgroundImage = '';
									if ( bg_color ) {
										if ( bg_color.startsWith( 'linear-gradient' ) ) {
											body.style.backgroundImage = bg_color;
										} else {
											body.style.backgroundColor = bg_color;
										}
									}
								}
							} )
						} );

						wp.customize( `${ PREFIX }background_color`, value => {
							value.bind( function ( newval ) {
								const bg_image = wp.customize( `${ PREFIX }background_image` ).get();
								if ( newval ) {
									body.style.backgroundColor = newval;
									if ( newval.startsWith( 'linear-gradient' ) ) {
										body.style.backgroundImage = newval;
									}
								} else {
									body.style.backgroundColor = '';
									if ( bg_image ) {
										body.style.backgroundImage = `url(${ bg_image })`;
									} else {
										body.style.backgroundImage = '';
									}
								}
							} )
						} );

						wp.customize( `${ PREFIX }background_repeat`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									body.style.backgroundRepeat = newval;
								} else {
									body.style.backgroundRepeat = 'none';
								}
							} )
						} );

						wp.customize( `${ PREFIX }background_position`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									body.style.backgroundPosition = newval;
								} else {
									body.style.backgroundPosition = 'center center';
								}
							} )
						} );

						wp.customize( `${ PREFIX }background_size`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									body.style.backgroundSize = newval;
								} else {
									body.style.backgroundSize = 'cover';
								}
							} )
						} );

						if ( wp.customize( `${ PREFIX }font_family` ).get() ) {
							login.style.fontFamily = `'${ wp.customize( `${ PREFIX }font_family` ).get() }'`;
						}

						const addFont = ( font ) => {
							const link = document.createElement( 'link' );
							link.id = 'et_gf_' + font.replace( / /g, '_' ).toLowerCase();
							link.href = '//fonts.googleapis.com/css?family=' + font.replace( / /g, '+' );
							link.rel = 'stylesheet';
							link.type = 'text/css';

							preview.head.appendChild( link );
						}
						// Handle Font
						wp.customize( `${ PREFIX }font_family`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login.style.fontFamily = `'${ newval }'`;
									addFont( newval );
								}
							} )
						} );

						// Handle Form

						if ( wp.customize( `${ PREFIX }form_background_color` ).get() && ! wp.customize( `${ PREFIX }form_remove_background` ).get() ) {
							const bg_color = wp.customize( `${ PREFIX }form_background_color` ).get();
							login_form.style.backgroundColor = bg_color;
							if ( bg_color.startsWith( 'linear-gradient' ) ) {
								login_form.style.backgroundImage = bg_color;
							}
						}

						if ( wp.customize( `${ PREFIX }form_background_image` ).get() && ! wp.customize( `${ PREFIX }form_remove_background` ).get() ) {
							login_form.style.backgroundImage = `url(${ wp.customize( `${ PREFIX }form_background_image` ).get() })`;
							login_form.style.backgroundSize = 'cover';
							login_form.style.backgroundPosition = 'center';
							login_form.style.backgroundRepeat = 'no-repeat';
						}
						if ( wp.customize( `${ PREFIX }form_width` ).get() ) {
							login_form.style.width = get_responsive_value( wp.customize( `${ PREFIX }form_width` ).get() );
						}
						if ( wp.customize( `${ PREFIX }form_height` ).get() ) {
							login_form.style.height = get_responsive_value( wp.customize( `${ PREFIX }form_height` ).get() );
						}
						if ( wp.customize( `${ PREFIX }form_border_width` ).get() ) {
							login_form.style.borderWidth = get_responsive_value( wp.customize( `${ PREFIX }form_border_width` ).get() );
						}
						if ( wp.customize( `${ PREFIX }form_border_radius` ).get() ) {
							login_form.style.borderRadius = get_responsive_value( wp.customize( `${ PREFIX }form_border_radius` ).get() );
						}
						if ( wp.customize( `${ PREFIX }form_border_color` ).get() ) {
							login_form.style.borderColor = wp.customize( `${ PREFIX }form_border_color` ).get();
						}
						if ( wp.customize( `${ PREFIX }form_box_shadow` ).get() ) {
							login_form.style.boxShadow = `${ wp.customize( `${ PREFIX }form_box_shadow_horizontal` ).get() }px ${ wp.customize( `${ PREFIX }form_box_shadow_vertical` ).get() }px ${ wp.customize( `${ PREFIX }form_box_shadow_blur` ).get() }px ${ wp.customize( `${ PREFIX }form_box_shadow_spread` ).get() }px ${ wp.customize( `${ PREFIX }form_box_shadow_color` ).get() }`;
						}

						if ( wp.customize( `${ PREFIX }form_remove_background` ).get() ) {
							showHideControl( form_background_controls, false );
							login_form.style.backgroundColor = 'transparent';
							login_form.style.backgroundImage = 'none';
						}

						wp.customize( `${ PREFIX }form_alignment`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login_form.style.alignItems = newval;
								}
							} )
						} );

						wp.customize( `${ PREFIX }form_remove_background`, value => {
							value.bind( function ( newval ) {
								showHideControl( form_background_controls, ! newval );
								if ( newval ) {
									login_form.style.backgroundColor = 'transparent';
									login_form.style.backgroundImage = 'none';
								} else {
									login_form.style.backgroundColor = `${ wp.customize( `${ PREFIX }form_background_color` ).get() }`;
									login_form.style.backgroundImage = `url(${ wp.customize( `${ PREFIX }form_background_image` ).get() })`;
								}
							} )
						} );

						wp.customize( `${ PREFIX }form_background_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login_form.style.backgroundColor = newval;
									if ( newval.startsWith( 'linear-gradient' ) ) {
										login_form.style.backgroundImage = newval;
									}
								} else {
									login_form.style.backgroundColor = '';
									if ( wp.customize.control( `${ PREFIX }form_background_image` ).get() ) {
										login_form.style.backgroundImage = wp.customize.control( `${ PREFIX }form_background_image` ).get();
									} else {
										login_form.style.backgroundImage = '';
									}
								}
							} )
						} );

						wp.customize( `${ PREFIX }form_background_image`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login_form.style.backgroundImage = `url(${ newval })`;
									login_form.style.backgroundSize = 'cover';
									login_form.style.backgroundPosition = 'center';
									login_form.style.backgroundRepeat = 'no-repeat';
								} else {
									const bg_color = wp.customize( `${ PREFIX }form_background_color` ).get();
									if ( bg_color ) {
										if ( ! bg_color.startsWith( 'linear-gradient' ) ) {
											login_form.style.backgroundColor = bg_color;
										} else {
											login_form.style.backgroundImage = bg_color;
										}
									} else {
										login_form.style.backgroundImage = '';
									}
								}
							} )
						} );

						wp.customize( `${ PREFIX }form_width`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login_form.style.width = get_responsive_value( newval );
								}
							} )
						} );

						wp.customize( `${ PREFIX }form_height`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login_form.style.height = get_responsive_value( newval );
								}
							} )
						} );

						wp.customize( `${ PREFIX }form_border_width`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login_form.style.borderWidth = get_responsive_value( newval );
								}
							} )
						} );

						wp.customize( `${ PREFIX }form_border_radius`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login_form.style.borderRadius = get_responsive_value( newval );
								}
							} )
						} );

						wp.customize( `${ PREFIX }form_border_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									login_form.style.borderColor = newval;
								}
							} )
						} );

						const applyFormBoxShadow = () => {
							login_form.style.boxShadow = `${ wp.customize( `${ PREFIX }form_box_shadow_horizontal` ).get() }px ${ wp.customize( `${ PREFIX }form_box_shadow_vertical` ).get() }px ${ wp.customize( `${ PREFIX }form_box_shadow_blur` ).get() }px ${ wp.customize( `${ PREFIX }form_box_shadow_spread` ).get() }px ${ wp.customize( `${ PREFIX }form_box_shadow_color` ).get() }`;
						}

						wp.customize( `${ PREFIX }form_box_shadow`, value => {
							value.bind( function ( newval ) {
								showHideControl( box_shadow_control, newval );
								if ( ! newval ) {
									login_form.style.boxShadow = '';
								} else {
									applyFormBoxShadow();
								}
							} )
						} );

						box_shadow_control.forEach( element => {
							wp.customize( `${ PREFIX }${ element }`, value => {
								value.bind( function ( newval ) {
									if ( newval ) {
										applyFormBoxShadow();
									}
								} )
							} );
						} );

						form_inputs.forEach( element => {
							element.style.backgroundColor = wp.customize( `${ PREFIX }form_input_field_background_color` ).get();
							element.style.color = wp.customize( `${ PREFIX }form_input_field_text_color` ).get();
							element.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_text_size` ).get() );
							element.style.borderColor = wp.customize( `${ PREFIX }form_input_field_border_color` ).get();
							element.style.borderRadius = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_border_radius` ).get(), 'quad' );
							element.style.borderWidth = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_border_width` ).get() );
							element.style.padding = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_padding` ).get(), 'quad' );
							element.style.margin = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_margin` ).get(), 'quad' );
							element.addEventListener( 'focus', () => {
								element.style.backgroundColor = wp.customize( `${ PREFIX }form_input_field_background_color_active` ).get();
							} );
							element.addEventListener( 'focusout', () => {
								element.style.backgroundColor = wp.customize( `${ PREFIX }form_input_field_background_color` ).get();
							} );
						} );
						const default_padding = get_responsive_value( wp.customize( `${ PREFIX }form_padding` ).get(), 'quad' );

						if ( '8px 10px 8px 10px' !== default_padding ) {
							login_form.style.padding = get_responsive_value( wp.customize( `${ PREFIX }form_padding` ).get(), 'quad' );
						}

						wp.customize( `${ PREFIX }form_padding`, value => {
							value.bind( function ( newval ) {
								login_form.style.padding = get_responsive_value( newval, 'quad' );
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_text_size`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.fontSize = get_responsive_value( newval );
								} );
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_padding`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.padding = get_responsive_value( newval, 'quad' );
								} );
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_margin`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.margin = get_responsive_value( newval, 'quad' );
								} )
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_border_width`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.borderWidth = get_responsive_value( newval );
								} );
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_border_radius`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.borderRadius = get_responsive_value( newval, 'quad' );
								} );
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_border_color`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.borderColor = newval;
								} );
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_background_color`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.backgroundColor = newval;
								} );
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_background_color_active`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.backgroundColor = newval;
								} );
							} );
						} );

						wp.customize( `${ PREFIX }form_input_field_text_color`, value => {
							value.bind( function ( newval ) {
								form_inputs.forEach( element => {
									element.style.color = newval;
								} );
							} );
						} );


						form_labels.forEach( element => {
							element.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_label_font_size` ).get() );
							element.style.color = wp.customize( `${ PREFIX }form_input_field_label_color` ).get();
						} )

						wp.customize( `${ PREFIX }form_input_field_label_font_size`, value => {
							value.bind( function ( newval ) {
								form_labels.forEach( element => {
									element.style.fontSize = get_responsive_value( newval );
								} )
							} )
						} );
						wp.customize( `${ PREFIX }form_input_field_label_color`, value => {
							value.bind( function ( newval ) {
								form_labels.forEach( element => {
									element.style.color = newval;
								} )
							} )
						} );


						remember_me_checkbox.style.width = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_remember_me_label_font_size` ).get() );
						remember_me_checkbox.style.height = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_remember_me_label_font_size` ).get() );
						remember_me_label.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }form_input_field_remember_me_label_font_size` ).get() );
						remember_me_label.style.verticalAlign = 'middle';
						remember_me_label.style.color = wp.customize( `${ PREFIX }form_input_field_remember_me_label_color` ).get();

						wp.customize( `${ PREFIX }form_input_field_remember_me_label_font_size`, value => {
							value.bind( function ( newval ) {
								remember_me_label.style.fontSize = get_responsive_value( newval );
								remember_me_checkbox.style.width = get_responsive_value( newval );
								remember_me_checkbox.style.height = get_responsive_value( newval );
								remember_me_label.style.verticalAlign = 'middle';
							} )
						} );
						wp.customize( `${ PREFIX }form_input_field_remember_me_label_color`, value => {
							value.bind( function ( newval ) {
								remember_me_label.style.color = newval;
							} )
						} );

						// Form Button

						if ( wp.customize( `${ PREFIX }button_text_size` ).get() ) {
							button.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }button_text_size` ).get() );
						}

						if ( wp.customize( `${ PREFIX }button_text_color` ).get() ) {
							button.style.color = wp.customize( `${ PREFIX }button_text_color` ).get();
						}

						if ( wp.customize( `${ PREFIX }button_background_color` ).get() ) {
							const bg_color = wp.customize( `${ PREFIX }button_background_color` ).get();
							button.style.backgroundColor = bg_color;
							if ( bg_color.startsWith( 'linear-gradient' ) ) {
								button.style.backgroundImage = bg_color;
							}
						}

						if ( wp.customize( `${ PREFIX }button_border_width` ).get() ) {
							button.style.borderWidth = get_responsive_value( wp.customize( `${ PREFIX }button_border_width` ).get() );
						}
						if ( wp.customize( `${ PREFIX }button_border_radius` ).get() ) {
							button.style.borderRadius = get_responsive_value( wp.customize( `${ PREFIX }button_border_radius` ).get() );
						}
						if ( wp.customize( `${ PREFIX }button_border_color` ).get() ) {
							button.style.borderColor = wp.customize( `${ PREFIX }button_border_color` ).get();
						}

						wp.customize( `${ PREFIX }button_text_size`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.fontSize = get_responsive_value( newval );
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_text_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.color = newval;
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_background_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.backgroundColor = newval;
									if ( newval.startsWith( 'linear-gradient' ) ) {
										button.style.backgroundImage = newval;
									}
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_background_color_hover`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.backgroundHoverColor = newval;
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_border_width`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.borderWidth = get_responsive_value( newval );
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_border_radius`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.borderRadius = get_responsive_value( newval );
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_border_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.borderColor = newval;
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_border_color_hover`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.borderHoverColor = newval;
								}
							} )
						} );
						if ( ! wp.customize( `${ PREFIX }button_enable_shadow` ).get() ) {
							showHideControl( button_shadow_control, false );
						}
						wp.customize( `${ PREFIX }button_enable_shadow`, value => {
							value.bind( function ( newval ) {
								showHideControl( button_shadow_control, newval );
								if ( ! newval ) {
									button.style.boxShadow = '';
								} else {
									applyButtonBoxShadow();
								}
							} )
						} );

						const applyButtonBoxShadow = () => {
							button.style.boxShadow = `${ wp.customize( `${ PREFIX }button_shadow_horizontal` ).get() }px ${ wp.customize( `${ PREFIX }button_shadow_vertical` ).get() }px ${ wp.customize( `${ PREFIX }button_shadow_blur` ).get() }px ${ wp.customize( `${ PREFIX }button_shadow_spread` ).get() }px ${ wp.customize( `${ PREFIX }button_shadow_color` ).get() }`;
						}

						const box_shadow_button = [ 'button_shadow_color', 'button_shadow_blur', 'button_shadow_horizontal', 'button_shadow_vertical', 'button_shadow_spread', ];
						box_shadow_button.forEach( element => {
							wp.customize( `${ PREFIX }${ element }`, value => {
								value.bind( function ( newval ) {
									if ( newval ) {
										applyButtonBoxShadow();
									}
								} )
							} );
						} );

						const applyButtonHoverBoxShadow = () => {
							button.style.boxShadow = `${ wp.customize( `${ PREFIX }button_shadow_horizontal` ).get() }px ${ wp.customize( `${ PREFIX }button_shadow_vertical` ).get() }px ${ wp.customize( `${ PREFIX }button_shadow_hover_blur` ).get() }px ${ wp.customize( `${ PREFIX }button_shadow_spread` ).get() }px ${ wp.customize( `${ PREFIX }button_shadow_hover_color` ).get() }`;
						}

						if ( wp.customize( `${ PREFIX }button_enable_shadow` ).get() ) {
							showHideControl( button_shadow_hover_control, true );
							applyButtonBoxShadow();
						}

						if ( ! wp.customize( `${ PREFIX }button_enable_shadow` ).get() ) {
							showHideControl( button_shadow_hover_control, false );
						}

						button.addEventListener( 'mouseenter', applyButtonHoverBoxShadow );
						button.addEventListener( 'mouseleave', applyButtonBoxShadow );

						if ( wp.customize( `${ PREFIX }button_shadow_hover_enable` ).get() ) {
							showHideControl( button_shadow_hover_control, true );
							button.addEventListener( 'mouseenter', applyButtonHoverBoxShadow );
							button.addEventListener( 'mouseleave', applyButtonBoxShadow );
						} else {
							showHideControl( button_shadow_hover_control, false );
						}

						wp.customize( `${ PREFIX }button_shadow_hover_enable`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									showHideControl( button_shadow_hover_control, newval );
									button.addEventListener( 'mouseenter', applyButtonHoverBoxShadow );
								} else {
									showHideControl( button_shadow_hover_control, false );
									button.removeEventListener( 'mouseenter', applyButtonHoverBoxShadow );
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_shadow_hover_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.shadowHoverColor = newval;
								}
							} )
						} );

						wp.customize( `${ PREFIX }button_shadow_hover_blur`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.shadowHoverBlur = get_responsive_value( newval );
								}
							} )
						} );

						if ( button ) {
							button.style.padding = get_responsive_value( wp.customize( `${ PREFIX }button_padding` ).get(), 'quad' );
						}

						wp.customize( `${ PREFIX }button_padding`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									button.style.padding = get_responsive_value( newval, 'quad' );
								}
							} )
						} );

						// Form Footer Lost Password
						if ( wp.customize( `${ PREFIX }form_footer_disable_lost_password` ).get() ) {
							showHideControl( lost_password_control, false );
							lost_password.style.display = 'none';
						} else {
							showHideControl( lost_password_control, true );
							lost_password.style.display = 'block';
							lost_password.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }form_footer_lost_password_text_size` ).get() );
							lost_password.style.color = wp.customize( `${ PREFIX }form_footer_lost_password_text_color` ).get();
							lost_password.style.textDecoration = wp.customize( `${ PREFIX }form_footer_lost_password_text_decoration` ).get();
						}

						wp.customize( `${ PREFIX }form_footer_disable_lost_password`, value => {
							value.bind( function ( newval ) {
								if ( ! newval ) {
									showHideControl( lost_password_control, true );
									lost_password.style.display = 'block';
									lost_password.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }form_footer_lost_password_text_size` ).get() );
									lost_password.style.color = wp.customize( `${ PREFIX }form_footer_lost_password_text_color` ).get();
									lost_password.style.textDecoration = wp.customize( `${ PREFIX }form_footer_lost_password_text_decoration` ).get();
								} else {
									showHideControl( lost_password_control, false );
									lost_password.style.display = 'none';
								}
							} )
						} )
						wp.customize( `${ PREFIX }form_footer_lost_password_text_size`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									lost_password.style.fontSize = get_responsive_value( newval );
								}
							} )
						} );
						wp.customize( `${ PREFIX }form_footer_lost_password_text_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									lost_password.style.color = newval;
								}
							} )
						} );
						lost_password.addEventListener( 'mouseenter', () => {
							if ( wp.customize( `${ PREFIX }form_footer_lost_password_text_hover_color` ).get() ) {
								lost_password.style.color = wp.customize( `${ PREFIX }form_footer_lost_password_text_hover_color` ).get();
							}
						} );
						lost_password.addEventListener( 'mouseleave', () => {
							if ( wp.customize( `${ PREFIX }form_footer_lost_password_text_hover_color` ).get() ) {
								lost_password.style.color = wp.customize( `${ PREFIX }form_footer_lost_password_text_color` ).get();
							}
						} );
						wp.customize( `${ PREFIX }form_footer_lost_password_text_decoration`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									lost_password.style.textDecoration = newval;
								}
							} )
						} );

						// Form Footer Back to Site
						if ( wp.customize( `${ PREFIX }form_footer_disable_back_to_site` ).get() ) {
							back_to_site.style.display = 'none';
						}
						if ( wp.customize( `${ PREFIX }form_footer_disable_back_to_site` ).get() ) {
							showHideControl( back_to_site_control, false );
							back_to_site.style.display = 'none';
						} else {
							showHideControl( back_to_site_control, true );
							back_to_site.style.display = 'block';
							back_to_site.style.color = wp.customize( `${ PREFIX }form_footer_back_to_site_text_color` ).get();
							back_to_site.style.textDecoration = wp.customize( `${ PREFIX }form_footer_back_to_site_text_decoration` ).get();
							back_to_site.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }form_footer_back_to_site_text_size` ).get() );
						}

						wp.customize( `${ PREFIX }form_footer_disable_back_to_site`, value => {
							value.bind( function ( newval ) {
								if ( ! newval ) {
									showHideControl( back_to_site_control, true );
									back_to_site.style.display = 'block';
									back_to_site.style.color = wp.customize( `${ PREFIX }form_footer_back_to_site_text_color` ).get();
									back_to_site.style.textDecoration = wp.customize( `${ PREFIX }form_footer_back_to_site_text_decoration` ).get();
									back_to_site.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }form_footer_back_to_site_text_size` ).get() );
								} else {
									showHideControl( back_to_site_control, false );
									back_to_site.style.display = 'none';
								}
							} )
						} )
						wp.customize( `${ PREFIX }form_footer_back_to_site_text_size`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									back_to_site_text.style.fontSize = get_responsive_value( newval );
								}
							} )
						} );
						wp.customize( `${ PREFIX }form_footer_back_to_site_text_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									back_to_site_text.style.color = newval;
								}
							} )
						} );

						back_to_site.addEventListener( 'mouseenter', () => {
							if ( wp.customize( `${ PREFIX }form_footer_back_to_site_text_hover_color` ).get() ) {
								back_to_site_text.style.color = wp.customize( `${ PREFIX }form_footer_back_to_site_text_hover_color` ).get();
							}
						} );

						back_to_site.addEventListener( 'mouseleave', () => {
							if ( wp.customize( `${ PREFIX }form_footer_back_to_site_text_hover_color` ).get() ) {
								back_to_site_text.style.color = wp.customize( `${ PREFIX }form_footer_back_to_site_text_color` ).get();
							}
						} );

						wp.customize( `${ PREFIX }form_footer_back_to_site_text_hover_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									back_to_site.addEventListener( 'mouseenter', () => {
										if ( newval ) {
											back_to_site_text.style.color = newval;
										}
									} );
									back_to_site.addEventListener( 'mouseleave', () => {
										if ( newval ) {
											back_to_site_text.style.color = wp.customize( `${ PREFIX }form_footer_back_to_site_text_color` ).get();
										}
									} );
								}
							} );
						} );
						wp.customize( `${ PREFIX }form_footer_back_to_site_text_decoration`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									back_to_site_text.style.textDecoration = newval;
								}
							} )
						} );

						// Form Footer Copyright
						let copyright = body.querySelector( '.copyright' );
						const addCopyright = () => {
							if ( ! copyright ) {
								copyright = document.createElement( 'p' );
							}
							copyright.classList.add( 'copyright' );
							copyright.innerHTML = wp.customize( `${ PREFIX }form_footer_copyright_text` ).get();
							copyright.style.color = wp.customize( `${ PREFIX }form_footer_copyright_text_color` ).get();
							copyright.style.backgroundColor = wp.customize( `${ PREFIX }form_footer_copyright_text_background_color` ).get();
							if ( wp.customize( `${ PREFIX }form_footer_copyright_text_font_size` ).get() !== 16 ) {
								copyright.style.fontSize = get_responsive_value( wp.customize( `${ PREFIX }form_footer_copyright_text_font_size` ).get() );
							} else {
								copyright.style.fontSize = '16px';
							}
							if ( wp.customize( `${ PREFIX }form_footer_copyright_text_padding` ).get() !== 10 ) {
								copyright.style.padding = get_responsive_value( wp.customize( `${ PREFIX }form_footer_copyright_text_padding` ).get() );
							} else {
								copyright.style.padding = '10px';
							}
							copyright.style.textAlign = 'center';
							body.appendChild( copyright );
						}

						if ( body.querySelector( '.copyright' ) ) {
							const copyright = body.querySelector( '.copyright' );
						}

						if ( wp.customize( `${ PREFIX }form_footer_enable_copyright` ).get() ) {
							showHideControl( copyright_control, true );
							addCopyright();
						} else {
							showHideControl( copyright_control, false );
							if ( copyright ) {
								copyright.remove();
							}
						}
						wp.customize( `${ PREFIX }form_footer_enable_copyright`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									showHideControl( copyright_control, true );
									if ( copyright ) {
										copyright.remove();
									}
									addCopyright();
								} else {
									showHideControl( copyright_control, false );
									copyright.remove();
								}
							} );
						} );
						wp.customize( `${ PREFIX }form_footer_copyright_text`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									if ( ! copyright ) {
										addCopyright();
									}
									copyright.innerHTML = newval;
								}
							} )
						} );
						wp.customize( `${ PREFIX }form_footer_copyright_text_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									if ( ! copyright ) {
										addCopyright();
									}
									copyright.style.color = newval;
								}
							} )
						} );
						wp.customize( `${ PREFIX }form_footer_copyright_text_background_color`, value => {
							value.bind( function ( newval ) {
								if ( newval ) {
									if ( ! copyright ) {
										addCopyright();
									}
									copyright.style.backgroundColor = newval;
								}
							} )
						} );
						wp.customize( `${ PREFIX }form_footer_copyright_text_font_size`, value => {
							value.bind( function ( newval ) {
								if ( ! copyright ) {
									addCopyright();
								}
								copyright.style.fontSize = get_responsive_value( newval );
							} )
						} );
						wp.customize( `${ PREFIX }form_footer_copyright_text_padding`, value => {
							value.bind( function ( newval ) {
								if ( ! copyright ) {
									addCopyright();
								}
								copyright.style.padding = get_responsive_value( newval );
							} )
						} );
					}

					window.addEventListener( 'load', handlePreview );
				}
			)
			();
        </script>
		<?php
	}
}