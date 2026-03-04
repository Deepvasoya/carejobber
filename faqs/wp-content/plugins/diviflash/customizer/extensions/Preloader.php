<?php

namespace DIFL\Customizer\Extensions;

use DIFL\Customizer\Base_Customizer;
use DIFL\Customizer\Types\Control;
use DIFL\Customizer\Types\Section;

class Preloader extends Base_Customizer {
	/**
	 * Array to hold div count from loader.css
	 */
	const PRESET = [
		'ball-pulse'                 => 3,
		'ball-triangle-path'         => 3,
		'ball-scale-multiple'        => 3,
		'ball-pulse-sync'            => 3,
		'ball-beat'                  => 3,
		'ball-scale-ripple-multiple' => 3,
		'ball-scale-random'          => 3,
		'ball-pulse-rise'            => 5,
		'line-scale'                 => 5,
		'line-scale-pulse-out'       => 5,
		'line-scale-pulse-out-rapid' => 5,
		'line-scale-party'           => 4,
		'pacman'                     => 5,
		'ball-clip-rotate-pulse'     => 2,
		'ball-clip-rotate-multiple'  => 2,
		'cube-transition'            => 2,
		'ball-zig-zag'               => 2,
		'ball-zig-zag-deflect'       => 2,
		'ball-grid-pulse'            => 9,
		'ball-grid-beat'             => 9,
		'ball-spin-fade-loader'      => 8,
		'line-spin-fade-loader'      => 8,
		'ball-clip-rotate'           => 1,
		'square-spin'                => 1,
		'ball-rotate'                => 1,
		'ball-scale'                 => 1,
		'ball-scale-ripple'          => 1,
		'triangle-skew-spin'         => 1,
		'semi-circle-spin'           => 1,
	];
	const TEXT_STYLE = [ 'one' => 'one', 'two' => 'two', 'three' => 'three', 'four' => 'four' ];

	const SECTION = 'difl_preloader_';

	const UNIT = [ 'px', 'em', 'rem' ];

	public function init() {
		parent::init();
		add_action( 'wp_head', [ $this, 'live_refresh_scripts' ], PHP_INT_MAX );
		add_action( 'customize_controls_print_footer_scripts', [ $this, 'customize_script' ], PHP_INT_MAX );
	}

	public function customize_script() {
		?>
        <script type="text/javascript">
			(() => {
				wp.customize.bind( 'ready', function () {
					wp.customize.section.each( function ( section ) {
						if ( section.id === 'difl_preloader_' ) {
							section.expanded.bind( function ( is_expanded ) {
								if ( ! is_expanded && section.id === 'difl_preloader_' ) {
									window.frames[window.frames.length - 1].document.querySelectorAll( '.difl_preloader_wrapper' ).forEach( element => element.remove() );
								}
							} );
						}
					} );
				} );
			})();
        </script>
		<?php
	}

	public function live_refresh_scripts() {
		if ( ! is_customize_preview() ) {
			return;
		}
		?>
        <script type="text/javascript">
			(() => {
				const PRESET = {
					'ball-pulse': 3,
					'ball-triangle-path': 3,
					'ball-scale-multiple': 3,
					'ball-pulse-sync': 3,
					'ball-beat': 3,
					'ball-scale-ripple-multiple': 3,
					'ball-scale-random': 3,
					'ball-pulse-rise': 5,
					'line-scale': 5,
					'line-scale-pulse-out': 5,
					'line-scale-pulse-out-rapid': 5,
					'line-scale-party': 4,
					'pacman': 5,
					'ball-clip-rotate-pulse': 2,
					'ball-clip-rotate-multiple': 2,
					'cube-transition': 2,
					'ball-zig-zag': 2,
					'ball-zig-zag-deflect': 2,
					'ball-grid-pulse': 9,
					'ball-grid-beat': 9,
					'ball-spin-fade-loader': 8,
					'line-spin-fade-loader': 8,
					'ball-clip-rotate': 1,
					'square-spin': 1,
					'ball-rotate': 1,
					'ball-scale': 1,
					'ball-scale-ripple': 1,
					'triangle-skew-spin': 1,
					'semi-circle-spin': 1,
				}

				const is_editing_section = () => {
					return window.parent.document.querySelector( '.customize-pane-child.accordion-section-content.accordion-section.control-section.control-section-default.open#sub-accordion-section-difl_preloader_' )
				}

				const get_current_device = () => {
					let device = null;
					device = window.parent.document.querySelector( '.devices-wrapper button.active' );
					if ( ! device ) {
						return 'desktop';
					}
					return device.dataset.device;
				}

				const get_resposnive_value = settings => {
					const device = get_current_device();
					const object = JSON.parse( settings );
					const value = object[device];
					const unit = object?.suffix ? object.suffix[device] : 'px';
					return `${ value }${ unit }`
				}

				const handlePreview = () => {
					let element = document.querySelector( 'body .difl_preloader_wrapper' );

					const handle_presets = () => {
						element = document.querySelector( 'body .difl_preloader_wrapper' );
						const scale = wp.customize( 'difl_preloader_element_scale' ).get();
						let icon_color = wp.customize( 'difl_preloader_icon_color' ).get();

						const setWH = element => {
							base_width = getComputedStyle( element ).width;
							base_height = getComputedStyle( element ).height;
							element.style.width = base_width * scale + 'px';
							element.style.height = base_height * scale + 'px';
						}

						const elements = [ ...document.querySelectorAll( '.difl_preloader_wrapper .difl_preloader_wrapper_inner div' ) ];
						const exceptional_elements = [ ...document.querySelectorAll( '.difl_preloader_wrapper .difl_preloader_wrapper_inner.ball-grid-pulse,.difl_preloader_wrapper .difl_preloader_wrapper_inner.ball-grid-beat' ) ];
						const spin_fade = [ ...document.querySelectorAll( '.difl_preloader_wrapper .difl_preloader_wrapper_inner.line-spin-fade-loader div,.difl_preloader_wrapper .difl_preloader_wrapper_inner.ball-spin-fade-loader div' ) ];

						elements.forEach( element => {
							setWH( element )
							if ( ! icon_color.startsWith( '#' ) ) {
								icon_color = element.style.getPropertyValue( '--difl--icon--color' );
							}
							element.style.borderColor = icon_color;
							element.style.setProperty( '--difl--icon--color', icon_color );
						} )

						exceptional_elements.forEach( element => {
							element.style.width = element.offsetWidth * scale + 'px';
						} )

						spin_fade.forEach( element => {
							element.style.borderRadius = window.getComputedStyle( element ).borderRadius.replace( 'px', '' ) * scale + 'px'
							element.style.top = window.getComputedStyle( element ).top.replace( 'px', '' ) * scale + 'px'
							element.style.left = window.getComputedStyle( element ).left.replace( 'px', '' ) * scale + 'px'
						} )
					}

					const renderPreloader = () => {
						const preloader_type = wp.customize( 'difl_preloader_type' ).get();
						const preset = wp.customize( 'difl_preloader_preset_type' ).get();
						const image_url = wp.customize( 'difl_preloader_image' ).get();

						const preloader_element = `<div class='difl_preloader_wrapper '>
            <div class="difl_preloader_wrapper_inner  ${ 'preset' === preloader_type ? preset : preloader_type }">
				${ 'preset' === preloader_type ? '<div></div>'.repeat( PRESET[preset] ) : '' }
				${ 'image' === preloader_type ? `<img src="${ image_url }"` : '' }
            </div>
        </div>`;
						document.querySelectorAll( '.difl_preloader_wrapper' ).forEach( element => element.remove() );
						document.querySelector( 'body' ).insertAdjacentHTML( 'afterbegin', preloader_element );
					}

					const showPreloader = () => {
						renderPreloader();
						handle_presets();

						const image = element.querySelector( '.difl_preloader_wrapper_inner.image img' );

						if ( image ) {
							const current_value = wp.customize( 'difl_preloader_image_size' ).get();
							image.style.width = get_resposnive_value( current_value );
						}


						if ( ! element ) {
							element = document.querySelector( 'body .difl_preloader_wrapper' );
						}

						element.style.display = 'flex';
						element.style.opacity = '1';
						if ( is_editing_section() ) {
							return;
						}
					}

					window.parent.document.addEventListener( 'click', function ( e ) {
						if ( (e.target.classList.contains( 'dashicons' ) && e.target.closest( '[id^="customize-control-difl_preloader"]' )) || e.target.classList.contains( 'preview-mobile' ) || e.target.classList.contains( 'preview-desktop' ) || e.target.classList.contains( 'preview-tablet' ) ) {
							if ( ! is_editing_section() ) {
								return;
							}
							showPreloader();
						}
					} );


					wp.customize( 'difl_preloader_enable_preloader', value => {
						value.bind( new_value => {
							if ( ! new_value ) {
								document.querySelectorAll( '.difl_preloader_wrapper' ).forEach( element => element.remove() );
							}
						} )
					} );

					const getElementById = id => window.parent.document.getElementById( id );
					const hideElement = id => getElementById( id ).style.display = 'none';
					const showElement = id => getElementById( id ).style.display = 'block';

					wp.customize( 'difl_preloader_type', value => {
						value.bind( new_val => {
							showPreloader();
							if ( new_val === 'image' ) {
								hideElement( 'customize-control-difl_preloader_preset_type' );
								hideElement( 'customize-control-difl_preloader_element_scale' );
								hideElement( 'customize-control-difl_preloader_icon_color' );


								showElement( 'customize-control-difl_preloader_image' );
								showElement( 'customize-control-difl_preloader_image_size' );
							} else if ( new_val === 'preset' ) {
								hideElement( 'customize-control-difl_preloader_image' );
								hideElement( 'customize-control-difl_preloader_image_size' );

								showElement( 'customize-control-difl_preloader_preset_type' );
								showElement( 'customize-control-difl_preloader_element_scale' );
								showElement( 'customize-control-difl_preloader_icon_color' );
							}
						} )
					} );
					wp.customize( 'difl_preloader_preset_type', value => {
						value.bind( new_val => {
							showPreloader();
						} )
					} );

					wp.customize( 'difl_preloader_image', value => {
						value.bind( new_val => {
							const image = document.querySelector( '.difl_preloader_wrapper_inner.image img' );
							image.src = new_val;
						} )
					} );

					wp.customize( 'difl_preloader_icon_color', value => {
						value.bind( new_val => {
							showPreloader();
							const presets = element.querySelectorAll( '.difl_preloader_wrapper_inner div' );
							[ ...presets ].forEach( preset => {
								preset.style.backgroundColor = new_val;
							} );
							element.style.setProperty( '--difl--icon--color', new_val );
						} );
					} );
					wp.customize( 'difl_preloader_element_scale', value => {
						let base_width = '';
						let base_height = '';
						value.bind( new_val => {
							showPreloader();
							const presets = element.querySelectorAll( '.difl_preloader_wrapper_inner div' );
							[ ...presets ].forEach( preset => {
								if ( '' === base_width ) {
									base_width = preset.offsetWidth;
									base_height = preset.offsetHeight;
								}
								preset.style.width = base_width * new_val + 'px';
								preset.style.height = base_height * new_val + 'px';
							} );
						} );
					} );
					wp.customize( 'difl_preloader_background_color', value => {
						value.bind( new_val => {
							showPreloader();
							element.style.setProperty( 'background-color', new_val, 'important' );
							element.style.setProperty( '--difl--brand--color', new_val );
						} )
					} );
					wp.customize( 'difl_preloader_image_size', value => {
						value.bind( new_val => {
							showPreloader();
							const image = element.querySelector( '.difl_preloader_wrapper_inner.image img' );

							if ( ! image ) {
								return;
							}
							image.style.width = get_resposnive_value( new_val );
						} );
					} );
					wp.customize( 'difl_preloader_reveal_animation', value => {
						value.bind( new_val => {
							showPreloader();
							const animations = [ 'fade', 'slideup', 'slidedown', 'slideright', 'slideleft' ];
							animations.forEach( animation => {
								if ( animation === new_val ) {
									element.classList.add( `difl-preloader-${ animation }` )
								} else {
									element.classList.remove( `difl-preloader-${ animation }` )
								}
							} )
						} )
					} );
					wp.customize( 'difl_preloader_enable_native_preloader', value => {
						value.bind( new_val => {
							if ( new_val ) {
								window.parent.document.getElementById( 'customize-control-difl_preloader_reveal_delay' ).style.display = 'none';
								window.parent.document.getElementById( 'customize-control-difl_preloader_reveal_duration' ).style.display = 'none';
							} else {
								window.parent.document.getElementById( 'customize-control-difl_preloader_reveal_delay' ).style.display = 'block';
								window.parent.document.getElementById( 'customize-control-difl_preloader_reveal_duration' ).style.display = 'block';
							}
						} )
					} );
				}
				window.addEventListener( 'load', handlePreview );
			})();
        </script>
		<?php
	}

	public function add_controls() {
		$this->add_sections();
		$this->add_content_controls();
		$this->add_style_controls();
	}

	private function add_sections() {

		$this->add_section(
			new Section(
				self::SECTION,
				[
					'priority' => 80,
					'title'    => esc_html__( 'Preloader', 'divi_flash' ),
					'panel'    => 'difl_advanced_genaral',
				]
			)
		);

	}

	private function add_content_controls() {
		$this->add_control(
			new Control(
				self::SECTION . 'enable_preloader',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'    => esc_html__( 'Enable Preloader', 'divi_flash' ),
					'section'  => self::SECTION,
					'type'     => 'difl_toggle_control',
					'priority' => 8,
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'only_for_homepage',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Homepage Only', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 8,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'general',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Content', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 9,
					'class'            => 'difl-preloader-to-top-general',
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
				self::SECTION . 'type',
				[
					'default'           => 'preset',
					'sanitize_callback' => [ $this, 'sanitize_preloader_type' ],
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Preloader Type', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 11,
					'type'            => 'select',
					'choices'         => [
						'preset' => esc_html__( 'Preset', 'divi_flash' ),
						'image'  => esc_html__( 'Image', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'preset_type',
				[
					'default'   => 'ball-pulse',
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Select Preset', 'divi_flash' ),
					'section'         => self::SECTION,
					'choices'         => \DIFL\Customizer\Extensions\Preloader::PRESET,
					'priority'        => 12,
					'active_callback' => [ $this, 'is_preloader_type_preset' ],
				],
				'DIFL\Customizer\Controls\Preloader'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'image',
				[
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Image Uploader', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 13,
					'active_callback' => [ $this, 'is_preloader_type_image' ],
					'width'           => '',
					'height'          => '',
					'flex_height'     => true,
					'flex_width'      => true,
				],
				'\WP_Customize_Upload_Control'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'image_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '{ "mobile": "160", "tablet": "160", "desktop": "160" }',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Image Size', 'divi_flash' ),
					'section'               => self::SECTION,
					'media_query'           => true,
					'step'                  => 1,
					'input_attr'            => [
						'mobile'  => [
							'min'     => 10,
							'max'     => 500,
							'default' => 160,
						],
						'tablet'  => [
							'min'     => 10,
							'max'     => 760,
							'default' => 160,
						],
						'desktop' => [
							'min'     => 10,
							'max'     => 1200,
							'default' => 160,
						],
					],
					'input_attrs'           => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 1200,
						'defaultVal' => [
							'mobile'  => 160,
							'tablet'  => 160,
							'desktop' => 160,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => self::UNIT,
					],
					'priority'              => 15,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => '--size',
							'selector'   => '.scroll-to-top-icon, .scroll-to-top-image',
							'responsive' => true,
							'suffix'     => 'px',
						],
						'responsive' => true,
						'template'   => 'body .scroll-to-top.icon .scroll-to-top-icon, body .scroll-to-top.image .scroll-to-top-image {
							width: {{value}}px;
							height: {{value}}px;
						}',
					],
					'active_callback'       => [ $this, 'is_preloader_type_image' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'text',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
					'default'           => 'PRELOADER TEXT',
				],
				[
					'priority'        => 14,
					'section'         => self::SECTION,
					'label'           => esc_html__( 'Enter Text', 'divi_flash' ),
					'type'            => 'text',
					'active_callback' => [ $this, 'is_preloader_type_text' ],
				]
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'text_type',
				[
					'default'   => 'one',
					'transport' => 'refresh',
				],
				[
					'label'           => esc_html__( 'Text Style', 'divi_flash' ),
					'section'         => self::SECTION,
					'choices'         => \DIFL\Customizer\Extensions\Preloader::TEXT_STYLE,
					'priority'        => 15,
					'active_callback' => [ $this, 'is_preloader_type_text' ],
				],
				'DIFL\Customizer\Controls\Text_Preloader'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'text_container_padding',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '{ "mobile": "5", "tablet": "8", "desktop": "10" }',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Container Padding (%)', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'mobile'  => [
							'min'     => 1,
							'max'     => 20,
							'default' => 5,
						],
						'tablet'  => [
							'min'     => 1,
							'max'     => 20,
							'default' => 8,
						],
						'desktop' => [
							'min'     => 1,
							'max'     => 50,
							'default' => 10,
						],
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 5,
							'tablet'  => 5,
							'desktop' => 10,
							'suffix'  => [
								'mobile'  => '%',
								'tablet'  => '%',
								'desktop' => '%',
							],
						],
					],
					'priority'        => 16,
					'active_callback' => [ $this, 'is_preloader_type_text' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'text_animation_duration',
				[
					'sanitize_callback' => 'absint',
					'default'           => 4000,
				],
				[
					'label'           => esc_html__( 'Animation Duration (ms)', 'divi_flash' ),
					'description'     => esc_html__( 'Text animation timing in MS', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'min'  => 1000,
						'max'  => 10000,
						'step' => 100,
					],
					'input_attrs'     => [
						'min'        => 1000,
						'max'        => 10000,
						'step'       => 100,
						'defaultVal' => 4000,
					],
					'priority'        => 17,
					'active_callback' => [ $this, 'is_preloader_type_text' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'text_letter_spacing',
				[
					'default' => - 6,
				],
				[
					'label'           => esc_html__( 'Letter Spacing (PX)', 'divi_flash' ),
					'description'     => esc_html__( 'Letter Spacing in PX', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'min'  => - 20,
						'max'  => 30,
						'step' => 1,
					],
					'input_attrs'     => [
						'min'        => - 20,
						'max'        => 30,
						'step'       => 1,
						'defaultVal' => - 6,
					],
					'priority'        => 18,
					'active_callback' => [ $this, 'is_preloader_type_text' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);
		$this->add_control(
			new Control(
				self::SECTION . 'text_word_spacing',
				[
					'sanitize_callback' => 'absint',
					'default'           => 5,
				],
				[
					'label'           => esc_html__( 'Word Spacing (PX)', 'divi_flash' ),
					'description'     => esc_html__( 'Word Spacing in PX', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'min'  => 1,
						'max'  => 30,
						'step' => 1,
					],
					'input_attrs'     => [
						'min'        => 1,
						'max'        => 30,
						'step'       => 1,
						'defaultVal' => 5,
					],
					'priority'        => 19,
					'active_callback' => [ $this, 'is_preloader_type_text' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);
		$this->add_control(
			new Control(
				self::SECTION . 'text_stroke_width',
				[
					'sanitize_callback' => 'absint',
					'default'           => '{ "mobile": "4", "tablet": "5", "desktop": "4" }',
				],
				[
					'label'           => esc_html__( 'Stroke Thickness (PX)', 'divi_flash' ),
					'description'     => esc_html__( 'Stroke Thickness in PX', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'mobile'  => [
							'min'     => 1,
							'max'     => 20,
							'default' => 4,
						],
						'tablet'  => [
							'min'     => 1,
							'max'     => 20,
							'default' => 5,
						],
						'desktop' => [
							'min'     => 1,
							'max'     => 20,
							'default' => 4,
						],
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 20,
						'defaultVal' => [
							'mobile'  => 4,
							'tablet'  => 5,
							'desktop' => 4,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'        => 20,
					'active_callback' => [ $this, 'is_preloader_type_text' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range' )
		);

		$this->add_control(
			new Control(
				self::SECTION . 'text_font_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '{ "mobile": "80", "tablet": "100", "desktop": "120" }',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Font Size', 'divi_flash' ),
					'section'         => self::SECTION,
					'media_query'     => true,
					'step'            => 1,
					'input_attr'      => [
						'mobile'  => [
							'min'     => 10,
							'max'     => 300,
							'default' => 80,
						],
						'tablet'  => [
							'min'     => 10,
							'max'     => 300,
							'default' => 100,
						],
						'desktop' => [
							'min'     => 10,
							'max'     => 300,
							'default' => 120,
						],
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 20,
						'max'        => 300,
						'defaultVal' => [
							'mobile'  => 80,
							'tablet'  => 100,
							'desktop' => 120,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'        => 21,
					'active_callback' => [ $this, 'is_preloader_type_text' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$color_controls = [
			self::SECTION . 'text_stroke_color' => [
				'priority'        => 22,
				'default'         => '#d0e7ca',
				'label'           => esc_html__( 'Stroke Color', 'divi_flash' ),
				'active_callback' => 'is_preloader_type_text',
			],
			self::SECTION . 'text_fill_color'   => [
				'priority'        => 23,
				'default'         => '#d0e7ca',
				'label'           => esc_html__( 'Fill Color', 'divi_flash' ),
				'active_callback' => 'is_preloader_type_text',
			],
		];

		/**
		 * Color controls
		 */
		foreach ( $color_controls as $control_id => $control_properties ) {
			$this->add_control(
				new Control(
					$control_id,
					[
						'sanitize_callback' => 'difl_sanitize_colors',
						'default'           => $control_properties['default'],
						'transport'         => $this->selective_refresh,
					],
					[
						'label'                 => $control_properties['label'],
						'section'               => self::SECTION,
						'priority'              => $control_properties['priority'],
						'input_attrs'           => isset( $control_properties['input_attrs'] ) ? $control_properties['input_attrs'] : [],
						'live_refresh_selector' => true,
						'live_refresh_css_prop' => array_key_exists( 'live_refresh_css_prop', $control_properties ) ? $control_properties['live_refresh_css_prop'] : '',
						'active_callback'       => [
							$this,
							array_key_exists( 'active_callback', $control_properties ) ? $control_properties['active_callback'] : 'is_extension_enabled',
						],
					],
					'\DIFL\Customizer\Controls\React\Color'
				)
			);
		}

	}

	private function add_style_controls() {

		$this->add_control(
			new Control(
				self::SECTION . 'style',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Design', 'divi_flash' ),
					'section'          => self::SECTION,
					'priority'         => 26,
					'class'            => 'difl-to-top-accordion',
					'accordion'        => true,
					'expanded'         => false,
					'controls_to_wrap' => 9,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'element_scale',
				[
					'default'   => 1,
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Scale Element ', 'divi_flash' ),
					'description'     => esc_html__( 'Preloader element scale', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => .1,
					'input_attr'      => [
						'min'  => 1,
						'max'  => 5,
						'step' => .1,
					],
					'input_attrs'     => [
						'min'        => 1,
						'max'        => 5,
						'step'       => .1,
						'defaultVal' => 1,
					],
					'priority'        => 23,
					'active_callback' => [ $this, 'is_preloader_type_preset' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$color_controls = [
			self::SECTION . 'icon_color'       => [
				'priority'              => 22,
				'label'                 => esc_html__( 'Icon Color', 'divi_flash' ),
				'default'               => 'var(--difl--icon--color)',
				'active_callback'       => 'is_preloader_type_preset',
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--color',
						'selector' => '.scroll-to-top',
					],
					'template' => '
					body .scroll-to-top {
						color: {{value}};
					}',
				],
			],
			self::SECTION . 'background_color' => [
				'priority'              => 32,
				'label'                 => esc_html__( 'Background Color', 'divi_flash' ),
				'default'               => 'var(--difl--brand--color)',
				'input_attrs'           => [
					'allow_gradient' => true,
				],
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--bgcolor',
						'selector' => '.scroll-to-top',
					],
					'template' => '
					body .scroll-to-top {
						background: {{value}};
					}',
					'fallback' => '#ffffff',
				],
			],
		];

		/**
		 * Color controls
		 */
		foreach ( $color_controls as $control_id => $control_properties ) {
			$this->add_control(
				new Control(
					$control_id,
					[
						'sanitize_callback' => 'difl_sanitize_colors',
						'default'           => $control_properties['default'],
						'transport'         => $this->selective_refresh,
					],
					[
						'label'                 => $control_properties['label'],
						'section'               => self::SECTION,
						'priority'              => $control_properties['priority'],
						'input_attrs'           => isset( $control_properties['input_attrs'] ) ? $control_properties['input_attrs'] : [],
						'live_refresh_selector' => true,
						'live_refresh_css_prop' => $control_properties['live_refresh_css_prop'],
						'active_callback'       => [
							$this,
							array_key_exists( 'active_callback', $control_properties ) ? $control_properties['active_callback'] : 'is_extension_enabled',
						],
					],
					'\DIFL\Customizer\Controls\React\Color'
				)
			);
		}
		$this->add_control(
			new Control(
				self::SECTION . 'reveal_animation',
				[
					'default'           => 'fade',
					'sanitize_callback' => [ $this, 'sanitize_animation_type' ],
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Reveal Animation', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 34,
					'type'            => 'select',
					'choices'         => [
						'fade'       => esc_html__( 'Fade', 'divi_flash' ),
						'slideup'    => esc_html__( 'Slide Up', 'divi_flash' ),
						'slidedown'  => esc_html__( 'Slide Down', 'divi_flash' ),
						'slideright' => esc_html__( 'Slide Right', 'divi_flash' ),
						'slideleft'  => esc_html__( 'Slide Left', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'enable_native_preloader',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => true,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Enable Native Loading', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 35,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'reveal_delay',
				[
					'sanitize_callback' => 'absint',
					'default'           => 300,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Reveal Delay (ms)', 'divi_flash' ),
					'description'     => esc_html__( 'Reveal Delay in Miliseconds', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
					'input_attrs'     => [
						'min'        => 0,
						'max'        => 5000,
						'step'       => 100,
						'defaultVal' => 300,
					],
					'priority'        => 36,
					'active_callback' => [ $this, 'is_native_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);
		$this->add_control(
			new Control(
				self::SECTION . 'reveal_duration',
				[
					'sanitize_callback' => 'absint',
					'default'           => 300,
				],
				[
					'label'           => esc_html__( 'Reveal Duration (ms)', 'divi_flash' ),
					'description'     => esc_html__( 'Reveal Duration', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
					'input_attrs'     => [
						'min'        => 0,
						'max'        => 5000,
						'step'       => 100,
						'defaultVal' => 300,
					],
					'priority'        => 36,
					'active_callback' => [ $this, 'is_native_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);
	}

	public function is_preloader_type_text() {
		if ( ! $this->is_extension_enabled() ) {
			return false;
		}

		return get_theme_mod( self::SECTION . 'type', 'preset' ) === 'text';
	}

	public function is_preloader_type_image() {
		if ( ! $this->is_extension_enabled() ) {
			return false;
		}

		return get_theme_mod( self::SECTION . 'type', 'preset' ) === 'image';
	}

	public function is_preloader_type_preset() {
		if ( ! $this->is_extension_enabled() ) {
			return false;
		}

		return get_theme_mod( self::SECTION . 'type', 'preset' ) === 'preset';
	}

	public function sanitize_preloader_type( $value ) {
		$allowed_values = [ 'preset', 'image', 'text' ];
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'preset';
		}

		return esc_html( $value );
	}

	public function sanitize_animation_type( $value ) {
		$allowed_values = [
			'fade',
			'slideup',
			'slidedown',
			'slideright',
			'slideleft',
			'zoom',
		];
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'fade';
		}

		return esc_html( $value );
	}

	public static function is_extension_enabled() {
		return get_theme_mod( self::SECTION . 'enable_preloader', false );
	}

	public function is_native_enabled() {
		if ( ! $this->is_extension_enabled() ) {
			return false;
		}

		return ! get_theme_mod( self::SECTION . 'enable_native_preloader', 'true' );
	}

	public static function enable_for_homepage() {
		return get_theme_mod( self::SECTION . 'only_for_homepage', false );
	}
}
