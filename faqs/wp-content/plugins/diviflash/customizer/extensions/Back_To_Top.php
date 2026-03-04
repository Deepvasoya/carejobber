<?php

namespace DIFL\Customizer\Extensions;

use DIFL\Customizer\Base_Customizer;
use DIFL\Customizer\Types\Control;
use DIFL\Customizer\Types\Section;

class Back_To_Top extends Base_Customizer {
	const SECTION = 'difl_back_to_top_';

	const UNIT = [ 'px', 'em', 'rem' ];

	public function init() {
		parent::init();
		add_filter( 'difl_reconcile_customizer_preview', [ $this, 'handle_divi_btt_option' ] );
		add_action( 'wp_head', [ $this, 'live_refresh_scripts' ] );
	}

	/**
	 * Handle divi btt enable if disable by chance
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function handle_divi_btt_option( $data ) {
		if ( ! array_key_exists( 'difl_back_to_top_enable_difl_btt', $data ) || 'on' === et_get_option( 'divi_back_to_top' ) ) {
			return $data;
		}

		if ( array_key_exists( 'difl_back_to_top_enable_difl_btt', $data ) ) {
			et_update_option( 'divi_back_to_top', 'on' );
		}

		return $data;

	}

	public function live_refresh_scripts() {
		if ( ! is_customize_preview() ) {
			return;
		}
		?>
        <script type="text/javascript">
			(() => {
					let label = '';
					let media = '';

					const is_editing_section = () => {
						return window.parent.document.querySelector( '.customize-pane-child.accordion-section-content.accordion-section.control-section.control-section-default.open#sub-accordion-section-difl_back_to_top_' )
					}

					const get_current_device = () => {
						let device = null;
						device = window.parent.document.querySelector( '.devices-wrapper button.active' );
						if ( ! device ) {
							return 'desktop';
						}
						return device.dataset.device;
					}

					const get_resposnive_value = ( string, type = 'single' ) => {
						const device = get_current_device();

						if ( ! string ) {
							return '';
						}

						let sizes = '';

						if ( ! sizes ) {
							sizes = string;
						}

						if ( sizes !== string ) {
							sizes = string;
						}

						if ( type === 'single' ) {
							const data = JSON.parse( sizes );
							const unit = data.suffix && data.suffix[device] ? data.suffix[device] : 'px';

							return `${ data[device] }${ unit }`;
						}

						if ( type === 'quad' ) {
							const data = typeof sizes === 'object' ? sizes : JSON.parse( sizes );
							const unit = data[`${ device }-unit`] || 'px';
							const deviceData = Object.values( data[device] ).map( side => `${ side }${ unit }` );

							return deviceData.join( ' ' );
						}
					}

					const controls = [
						{ key: 'difl_back_to_top_label', type: 'string', selector: 'label', prop: 'innerText' },
						{
							key: 'difl_back_to_top_label_font_size',
							type: 'normal',
							is_responsive: true,
							selector: 'label',
							styleProperty: 'font-size'
						},
						{
							key: 'difl_back_to_top_label_font_color',
							type: 'normal',
							selector: 'label',
							styleProperty: 'color'
						},
						{ key: 'difl_back_to_top_icon', type: 'string', selector: 'media', prop: 'innerText' },
						{
							key: 'difl_back_to_top_icon_size',
							type: 'normal',
							is_responsive: true,
							selector: 'media',
							styleProperty: 'font-size'
						},
						{
							key: 'difl_back_to_top_icon_color',
							type: 'normal',
							selector: 'media',
							styleProperty: 'color'
						},
						{ key: 'difl_back_to_top_offset', type: 'normal', append_unit: 'px' },
						{ key: 'difl_back_to_top_bottom_offset', type: 'normal', append_unit: 'px' },
						{
							key: 'difl_back_to_top_image_size',
							type: 'normal',
							is_responsive: true,
							selector: 'media',
							styleProperty: 'width'
						},
					];

					const get_bg_type = ( bg_color ) => {
						return bg_color.startsWith( 'linear-gradient' ) || bg_color.startsWith( 'radial-gradient' ) ? 'background-image' : 'background';
					}

					const handlePreview = () => {
						document.querySelector( 'body .et_pb_scroll_top' ).setAttribute( 'id', 'difl_btt' );
						const element = document.querySelector( 'body span#difl_btt.et_pb_scroll_top' );
						media = document.querySelector( '.et_pb_scroll_top .difl-btt-media' );
						label = document.querySelector( '.et_pb_scroll_top .difl-btt-label' );
						media_hover = document.querySelector( '.et_pb_scroll_top .difl-btt-media:hover' );
						label_hover = document.querySelector( '.et_pb_scroll_top .difl-btt-label:hover' );

						controls.forEach( control => {
							if ( control.hover ) {
								control.selector = control.selector === 'media' ? media_hover : label_hover;
							} else {
								control.selector = control.selector === 'media' ? media : label;
							}

							wp.customize( [ control.key ], value => {
								value.bind( new_val => {

									if ( control.key === 'difl_back_to_top_label' ) {
										control.selector.style.fontFamily = 'var(--difl--btt--font)';
										showElement( 'customize-control-difl_back_to_top_label_font_size' );
										showElement( 'customize-control-difl_back_to_top_label_font_color' );
										showElement( 'customize-control-difl_back_to_top_label_font_hover_color' );
									}

									if ( control.prop === 'innerText' ) {
										control.selector.innerText = new_val;
										return;
									}
									if ( control.key === 'difl_back_to_top_offset' ) {
										const position = wp.customize( 'difl_back_to_top_position' ).get();
										if ( position === 'left' ) {
											element.style.setProperty( 'left', new_val + 'px', 'important' );
										} else {
											element.style.setProperty( 'right', new_val + 'px', 'important' );
										}
										return;
									}
									if ( control.key === 'difl_back_to_top_bottom_offset' ) {
										element.style.setProperty( 'bottom', new_val + 'px', 'important' );
										return;
									}
									const device = get_current_device();
									const key = control.is_responsive ? `--${ control.key }__${ device }` : `--${ control.key }`;
									let value_to_set = control.is_responsive ? get_resposnive_value( new_val ) : new_val;

									if ( control.append_unit ) {
										value_to_set = `${ value_to_set }${ control.append_unit }`;
									}

									if ( control.styleProperty ) {
										control.selector.style.setProperty( `${ control.styleProperty }`, 'string' === control.type ? `'${ value_to_set }'` : value_to_set )
									}

									if ( control.key === 'difl_back_to_top_image_size' ) {
										control.selector.style.setProperty( 'height', 'string' === control.type ? `'${ value_to_set }'` : value_to_set )
									}
								} )
							} )
						} )
						element.addEventListener( 'mouseenter', () => {
							element.style.setProperty( 'background-color', wp.customize( 'difl_back_to_top_background_hover_color' ).get(), 'important' );
							label.style.setProperty( 'color', wp.customize( 'difl_back_to_top_label_font_hover_color' ).get(), 'important' );
							media.style.setProperty( 'color', wp.customize( 'difl_back_to_top_icon_hover_color' ).get(), 'important' );
							media.style.setProperty( 'transition', '.3s' );
						} );

						element.addEventListener( 'mouseleave', () => {
							element.style.setProperty( 'background-color', wp.customize( 'difl_back_to_top_background_color' ).get(), 'important' );
							label.style.setProperty( 'color', wp.customize( 'difl_back_to_top_label_font_color' ).get(), 'important' );
							media.style.setProperty( 'color', wp.customize( 'difl_back_to_top_icon_color' ).get(), 'important' );
						} );
						wp.customize( 'difl_back_to_top_background_color', value => {
							value.bind( new_val => {
								element.style.setProperty( get_bg_type( new_val ), new_val, 'important' );
							} )
						} );
						wp.customize( 'difl_back_to_top_background_hover_color', value => {
							value.bind( new_val => {
								element.addEventListener( 'mouseenter', () => {
									element.style.setProperty( 'background-color', new_val, 'important' );
								} );
							} )
						} );
						wp.customize( 'difl_back_to_top_label_font_hover_color', value => {
							const label = document.querySelector( '.et_pb_scroll_top .difl-btt-label' );
							value.bind( new_val => {
								label.addEventListener( 'mouseenter', () => {
									label.style.setProperty( 'color', new_val, 'important' );
								} );
								label.addEventListener( 'mouseleave', () => {
									label.style.setProperty( 'color', wp.customize( 'difl_back_to_top_label_font_color' ).get(), 'important' );
								} );
							} )
						} );
						wp.customize( 'difl_back_to_top_icon_hover_color', value => {
							const icon = document.querySelector( '.et_pb_scroll_top .difl-btt-media' );
							value.bind( new_val => {
								icon.addEventListener( 'mouseenter', () => {
									icon.style.setProperty( 'color', new_val, 'important' );
								} );
								icon.addEventListener( 'mouseleave', () => {
									icon.style.setProperty( 'color', wp.customize( 'difl_back_to_top_icon_color' ).get(), 'important' );
								} );
							} )
						} );
						wp.customize( 'difl_back_to_top_space_between', value => {
							value.bind( new_val => {
								element.style.setProperty( 'gap', new_val + 'px' );
							} );
						} );
						wp.customize( 'difl_back_to_top_margin', value => {
							value.bind( new_val => {
								element.style.setProperty( 'margin', get_resposnive_value( new_val, 'quad' ), 'important' );
							} );
						} );
						wp.customize( 'difl_back_to_top_padding', value => {
							value.bind( new_val => {
								element.style.setProperty( 'padding', get_resposnive_value( new_val, 'quad' ), 'important' );
							} );
						} );
						wp.customize( 'difl_back_to_top_border_radius', value => {
							value.bind( new_val => {
								element.style.setProperty( 'border-radius', get_resposnive_value( new_val, 'quad' ), 'important' );
							} );
						} );
						wp.customize( 'difl_back_to_top_alignment', value => {
							const label = document.querySelector( '.et_pb_scroll_top .difl-btt-label' );
							value.bind( new_val => {
								if ( 'vertically' === new_val ) {
									element.style.setProperty( 'flex-direction', 'column', 'important' );
									label.style.setProperty( 'writing-mode', 'vertical-lr', 'important' );
									label.style.setProperty( 'transform', 'rotate(180deg)', 'important' );
								}

								if ( 'horizontally' === new_val ) {
									element.style.setProperty( 'flex-direction', 'row', 'important' );
									label.style.setProperty( 'writing-mode', 'initial', 'important' );
									label.style.setProperty( 'transform', 'none', 'important' );
								}
							} );
						} );
						wp.customize( 'difl_back_to_top_position', value => {
							value.bind( new_val => {
								if ( new_val === 'left' ) {
									element.style.setProperty( 'left', wp.customize( 'difl_back_to_top_offset' ).get() + 'px', 'important' );
									element.style.setProperty( 'right', 'auto', 'important' );
								}
								if ( new_val === 'right' ) {
									element.style.setProperty( 'right', wp.customize( 'difl_back_to_top_offset' ).get() + 'px', 'important' );
									element.style.setProperty( 'left', 'auto', 'important' );
								}
							} );
						} );

						const position = wp.customize( 'difl_back_to_top_position' ).get();

						if ( 'left' === position ) {
							element.style.setProperty( 'left', wp.customize( 'difl_back_to_top_offset' ).get() + 'px', 'important' );
							element.style.setProperty( 'right', 'auto', 'important' );
						}

						if ( 'right' === position ) {
							element.style.setProperty( 'right', wp.customize( 'difl_back_to_top_offset' ).get() + 'px', 'important' );
							element.style.setProperty( 'left', 'auto', 'important' );
						}

						const getElementById = id => window.parent.document.getElementById( id );
						const hideElement = id => getElementById( id ).style.display = 'none';
						const showElement = id => {
							const element = getElementById( id );
							if ( element.style.display === 'none' ) {
								element.style.display = 'block';
							}
						}

						wp.customize( 'difl_back_to_top_type', value => {
							value.bind( new_val => {
								const value = new_val;
								if ( value === 'icon' ) {
									showElement( 'customize-control-difl_back_to_top_icon' );
									showElement( 'customize-control-difl_back_to_top_icon_size' );
									showElement( 'customize-control-difl_back_to_top_icon_color' );
									showElement( 'customize-control-difl_back_to_top_icon_hover_color' );

									hideElement( 'customize-control-difl_back_to_top_image' );
									hideElement( 'customize-control-difl_back_to_top_image_size' );
									media.style.backgroundImage = 'none';
									media.style.width = 'unset';
									media.style.height = 'unset';
									media.style.display = 'block';
									media.style.fontFamily = 'ETModules';
									media.innerText = wp.customize( 'difl_back_to_top_icon' ).get();
									media.style.setProperty( 'color', wp.customize( 'difl_back_to_top_icon_color' ).get() );
									media.style.setProperty( 'font-size', wp.customize( 'difl_back_to_top_icon_size' ).get() );
								} else if ( value === 'image' ) {
									hideElement( 'customize-control-difl_back_to_top_icon' );
									hideElement( 'customize-control-difl_back_to_top_icon_color' );
									hideElement( 'customize-control-difl_back_to_top_icon_hover_color' );
									hideElement( 'customize-control-difl_back_to_top_icon_size' );

									showElement( 'customize-control-difl_back_to_top_image' );
									showElement( 'customize-control-difl_back_to_top_image_size' );
									media.style.fontFamily = 'initial';
									media.innerText = '';
									const width = get_resposnive_value( wp.customize( 'difl_back_to_top_image_size' ).get() );
									media.style.width = width;
									media.style.height = width;
									media.style.display = 'block';
									media.style.backgroundSize = 'contain';
									media.style.backgroundImage = `url(${ wp.customize( 'difl_back_to_top_image' ).get() })`;
								} else if ( value === 'none' ) {
									media.style.display = 'none';
									hideElement( 'customize-control-difl_back_to_top_icon' );
									hideElement( 'customize-control-difl_back_to_top_icon_color' );
									hideElement( 'customize-control-difl_back_to_top_icon_hover_color' );
									hideElement( 'customize-control-difl_back_to_top_icon_size' );

									hideElement( 'customize-control-difl_back_to_top_image' );
									hideElement( 'customize-control-difl_back_to_top_image_size' );
								}
							} );
						} );
						wp.customize( 'difl_back_to_top_image', value => {
							value.bind( new_val => {
								media.style.backgroundImage = `url(${ new_val })`;
							} )
						} );
						wp.customize( 'difl_back_to_top_hover_animation', value => {
							value.bind( new_val => {
								[ ...element.classList ].forEach( className => {
									if ( className.startsWith( 'difl-' ) ) {
										element.classList.remove( className );
									}
								} );
								element.classList.add( `difl-${ new_val }` );
							} );
						} );

						window.parent.document.addEventListener( 'click', function ( e ) {
							if ( (e.target.classList.contains( 'dashicons' ) && e.target.closest( '[id^="customize-control-difl_back_to_top"]' )) || e.target.classList.contains( 'preview-mobile' ) || e.target.classList.contains( 'preview-desktop' ) || e.target.classList.contains( 'preview-tablet' ) ) {
								if ( ! is_editing_section() ) {
									return;
								}
								const responsive_controls = controls.filter( control => control.is_responsive );
								responsive_controls.forEach( control => {
									const current_value = get_resposnive_value( wp.customize( control.key ).get() );
									control.selector.style.setProperty( `${ control.styleProperty }`, 'string' === control.type ? `'${ current_value }'` : current_value )
								} )
								element.style.setProperty( 'margin', get_resposnive_value( wp.customize( 'difl_back_to_top_margin' ).get(), 'quad' ), 'important' );
								element.style.setProperty( 'padding', get_resposnive_value( wp.customize( 'difl_back_to_top_padding' ).get(), 'quad' ), 'important' );
								element.style.setProperty( 'border-radius', get_resposnive_value( wp.customize( 'difl_back_to_top_border_radius' ).get(), 'quad' ), 'important' );
							}
						} );
					}
					window.addEventListener( 'load', handlePreview );
				}
			)
			();
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
					'title'    => esc_html__( 'Back To Top Button', 'divi_flash' ),
					'panel'    => 'difl_advanced_genaral',
				]
			)
		);

	}

	private function add_content_controls() {
		$this->add_control(
			new Control(
				self::SECTION . 'enable_difl_btt',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'    => esc_html__( 'Enable Back To Top', 'divi_flash' ),
					'section'  => self::SECTION,
					'type'     => 'difl_toggle_control',
					'priority' => 8,
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
					'class'            => 'scroll-to-top-general',
					'accordion'        => true,
					'expanded'         => true,
					'controls_to_wrap' => 17,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);

		/*
		 * Label
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'label',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				],
				[
					'priority'        => 10,
					'section'         => self::SECTION,
					'label'           => esc_html__( 'Label', 'divi_flash' ),
					'type'            => 'text',
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'label_font_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '{ "mobile": "16", "tablet": "16", "desktop": "16" }',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Font Size', 'divi_flash' ),
					'section'               => self::SECTION,
					'media_query'           => true,
					'step'                  => 1,
					'input_attr'            => [
						'mobile'  => [
							'min'     => 10,
							'max'     => 100,
							'default' => 16,
						],
						'tablet'  => [
							'min'     => 10,
							'max'     => 100,
							'default' => 16,
						],
						'desktop' => [
							'min'     => 10,
							'max'     => 100,
							'default' => 16,
						],
					],
					'input_attrs'           => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => self::UNIT,
					],
					'priority'              => 10,
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
					'active_callback'       => [ $this, 'label_has_value' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		/**
		 * Label Color
		 */
		$color_controls = [
			self::SECTION . 'label_font_color'       => [
				'priority'              => 10,
				'label'                 => esc_html__( 'Label Color', 'divi_flash' ),
				'default'               => 'var(--difl--icon--color)',
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
			self::SECTION . 'label_font_hover_color' => [
				'priority'              => 10,
				'label'                 => esc_html__( 'Label Hover Color', 'divi_flash' ),
				'default'               => 'var(--difl--brand--color)',
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--hovercolor',
						'selector' => '.scroll-to-top:hover',
					],
					'template' => '
					body .scroll-to-top:hover {
						color: {{value}};
					}',
				],
			],
		];

		/**
		 * Color controls for label
		 */
		foreach ( $color_controls as $control_id => $control_properties ) {
			$this->add_control(
				new Control(
					$control_id,
					[
						'sanitize_callback' => 'difl_sanitize_colors',
						'default'           => array_key_exists( 'default', $control_properties ) ? $control_properties['default'] : '',
						'transport'         => $this->selective_refresh,
					],
					[
						'label'                 => $control_properties['label'],
						'section'               => self::SECTION,
						'priority'              => $control_properties['priority'],
						'input_attrs'           => isset( $control_properties['input_attrs'] ) ? $control_properties['input_attrs'] : [],
						'live_refresh_selector' => true,
						'live_refresh_css_prop' => $control_properties['live_refresh_css_prop'],
						'active_callback'       => [ $this, 'label_has_value' ],
					],
					'\DIFL\Customizer\Controls\React\Color'
				)
			);
		}


		/**
		 * Scroll to top Media
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'type',
				[
					'default'           => 'icon',
					'sanitize_callback' => [ $this, 'sanitize_scroll_to_top_type' ],
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Media', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 11,
					'type'            => 'select',
					'choices'         => [
						'icon'  => esc_html__( 'Icon', 'divi_flash' ),
						'image' => esc_html__( 'Image', 'divi_flash' ),
						'none'  => esc_html__( 'None', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'icon',
				[
					'default'   => false,
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Select Icon', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'icon_picker',
					'priority'        => 12,
					'active_callback' => [ $this, 'is_icon_type_control' ],
				],
				'DIFL\Customizer\Controls\Divi_Icon'
			)
		);

		/**
		 * Image button
		 */
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
					'active_callback' => [ $this, 'is_image_type_control' ],
					'flex_height'     => true,
					'flex_width'      => true,
				],
				'\WP_Customize_Upload_Control'
			)
		);

		/**
		 * Icon size
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'icon_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '{ "mobile": "16", "tablet": "16", "desktop": "16" }',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Icon Size', 'divi_flash' ),
					'section'               => self::SECTION,
					'media_query'           => true,
					'step'                  => 1,
					'input_attr'            => [
						'mobile'  => [
							'min'     => 10,
							'max'     => 100,
							'default' => 16,
						],
						'tablet'  => [
							'min'     => 10,
							'max'     => 100,
							'default' => 16,
						],
						'desktop' => [
							'min'     => 10,
							'max'     => 100,
							'default' => 16,
						],
					],
					'input_attrs'           => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => self::UNIT,
					],
					'priority'              => 14,
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
					'active_callback'       => [ $this, 'is_icon_type_control' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		/**
		 * Image size
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'image_size',
				[
					'sanitize_callback' => 'difl_sanitize_range_value',
					'default'           => '{ "mobile": "16", "tablet": "16", "desktop": "16" }',
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
							'max'     => 100,
							'default' => 16,
						],
						'tablet'  => [
							'min'     => 10,
							'max'     => 100,
							'default' => 16,
						],
						'desktop' => [
							'min'     => 10,
							'max'     => 100,
							'default' => 16,
						],
					],
					'input_attrs'           => [
						'step'       => 1,
						'min'        => 1,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 16,
							'tablet'  => 16,
							'desktop' => 16,
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
					'active_callback'       => [ $this, 'is_image_type_control' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Responsive_Range', false ) ? 'DIFL\Customizer\Controls\React\Responsive_Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		/**
		 * Icon Color
		 */
		$color_controls = [
			self::SECTION . 'icon_color'       => [
				'priority'              => 16,
				'label'                 => esc_html__( 'Icon Color', 'divi_flash' ),
				'default'               => 'var(--difl--icon--color)',
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
			self::SECTION . 'icon_hover_color' => [
				'priority'              => 17,
				'label'                 => esc_html__( 'Icon Hover Color', 'divi_flash' ),
				'default'               => 'var(--difl--brand--color)',
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--hovercolor',
						'selector' => '.scroll-to-top:hover',
					],
					'template' => '
					body .scroll-to-top:hover {
						color: {{value}};
					}',
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
						'default'           => array_key_exists( 'default', $control_properties ) ? $control_properties['default'] : '',
						'transport'         => $this->selective_refresh,
					],
					[
						'label'                 => $control_properties['label'],
						'section'               => self::SECTION,
						'priority'              => $control_properties['priority'],
						'input_attrs'           => isset( $control_properties['input_attrs'] ) ? $control_properties['input_attrs'] : [],
						'live_refresh_selector' => true,
						'live_refresh_css_prop' => $control_properties['live_refresh_css_prop'],
						'active_callback'       => [ $this, 'is_icon_type_control' ],
					],
					'\DIFL\Customizer\Controls\React\Color'
				)
			);
		}

		/**
		 * Position
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'position',
				[
					'default'           => 'right',
					'sanitize_callback' => [ $this, 'sanitize_scroll_to_top_side' ],
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Position', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 18,
					'type'            => 'select',
					'choices'         => [
						'left'  => esc_html__( 'Left', 'divi_flash' ),
						'right' => esc_html__( 'Right', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		/**
		 * Alignment
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'alignment',
				[
					'default'           => 'horizontally',
					'sanitize_callback' => [ $this, 'sanitize_scroll_to_top_alignemnt' ],
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Alignment', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 19,
					'type'            => 'select',
					'choices'         => [
						'horizontally' => esc_html__( 'Horizontally', 'divi_flash' ),
						'vertically'   => esc_html__( 'Vertically', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);


		/**
		 * Side Offset
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'offset',
				[
					'sanitize_callback' => 'absint',
					'default'           => 10,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Side Offset (PX)', 'divi_flash' ),
					'description'     => esc_html__( 'Gap from page side', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 1000,
						'default' => 10,
					],
					'input_attrs'     => [
						'min'        => 0,
						'max'        => 1000,
						'defaultVal' => 10,
					],
					'priority'        => 20,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		/**
		 * Bottom Offset
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'bottom_offset',
				[
					'sanitize_callback' => 'absint',
					'default'           => 30,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Bottom Offset (PX)', 'divi_flash' ),
					'description'     => esc_html__( 'Gap from page bottom', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 400,
						'default' => 30,
					],
					'input_attrs'     => [
						'min'        => 0,
						'max'        => 400,
						'defaultVal' => 30,
					],
					'priority'        => 21,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'hover_animation',
				[
					'default'   => 'zoomin',
					'transport' => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Hover Animation', 'divi_flash' ),
					'section'         => self::SECTION,
					'priority'        => 22,
					'type'            => 'select',
					'choices'         => [
						'none'     => esc_html__( 'None', 'divi_flash' ),
						'moveup'   => esc_html__( 'Move Up', 'divi_flash' ),
						'movedown' => esc_html__( 'Move Down', 'divi_flash' ),
						'zoomin'   => esc_html__( 'Zoom In', 'divi_flash' ),
						'zoomout'  => esc_html__( 'Zoom Out', 'divi_flash' ),
					],
					'active_callback' => [ $this, 'is_extension_enabled' ],
				]
			)
		);

		/**
		 * Hide on mobile
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'on_mobile',
				[
					'sanitize_callback' => 'difl_sanitize_checkbox',
					'default'           => false,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Hide on small devices', 'divi_flash' ),
					'section'         => self::SECTION,
					'type'            => 'difl_toggle_control',
					'priority'        => 23,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Checkbox'
			)
		);

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
					'class'            => 'scroll-to-top-accordion',
					'accordion'        => true,
					'expanded'         => false,
					'controls_to_wrap' => 7,
					'active_callback'  => [ $this, 'is_extension_enabled' ],
				],
				'DIFL\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'font_family',
				[
					'transport'         => $this->selective_refresh,
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'Arial, Helvetica, sans-serif',
				],
				[
					'settings'              => [
						'default'  => 'difl_body_font_family',
						'variants' => 'difl_body_font_family_variants',
					],
					'label'                 => esc_html__( 'Body', 'divi_flash' ),
					'section'               => self::SECTION,
					'priority'              => 28,
					'type'                  => 'difl_font_family_control',
					'live_refresh_selector' => apply_filters( 'difl_body_font_family_selectors', 'body, .site-title' ),
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--bodyfontfamily',
							'selector' => 'body',
							'fallback' => 'Arial, Helvetica, sans-serif',
							'suffix'   => ', var(--difl-fallback-ff)',
						],
					],
				],
				'\DIFL\Customizer\Controls\React\Font_Family'
			)
		);

		$color_controls = [
			self::SECTION . 'background_color'       => [
				'priority'              => 32,
				'label'                 => esc_html__( 'Background Color', 'divi_flash' ),
				'default'               => 'var(--difl--icon--color)',
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
			self::SECTION . 'background_hover_color' => [
				'priority'              => 34,
				'label'                 => esc_html__( 'Background Hover Color', 'divi_flash' ),
				'default'               => 'var(--difl--icon--color)',
				'input_attrs'           => [
					'allow_gradient' => true,
				],
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--hoverbgcolor',
						'selector' => '.scroll-to-top:hover',
					],
					'template' => '
					body .scroll-to-top:hover {
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
						'default'           => array_key_exists( 'default', $control_properties ) ? $control_properties['default'] : '',
						'transport'         => $this->selective_refresh,
					],
					[
						'label'                 => $control_properties['label'],
						'section'               => self::SECTION,
						'priority'              => $control_properties['priority'],
						'input_attrs'           => isset( $control_properties['input_attrs'] ) ? $control_properties['input_attrs'] : [],
						'live_refresh_selector' => true,
						'live_refresh_css_prop' => $control_properties['live_refresh_css_prop'],
						'active_callback'       => [ $this, 'is_extension_enabled' ],
					],
					'\DIFL\Customizer\Controls\React\Color'
				)
			);
		}
		/**
		 * Space Between
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'space_between',
				[
					'sanitize_callback' => 'absint',
					'default'           => 5,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'           => esc_html__( 'Space Between (PX)', 'divi_flash' ),
					'description'     => esc_html__( 'Gap between icon and label', 'divi_flash' ),
					'section'         => self::SECTION,
					'step'            => 1,
					'input_attr'      => [
						'min'     => 0,
						'max'     => 200,
						'default' => 5,
					],
					'input_attrs'     => [
						'min'        => 0,
						'max'        => 200,
						'defaultVal' => 5,
					],
					'priority'        => 36,
					'active_callback' => [ $this, 'is_extension_enabled' ],
				],
				class_exists( 'DIFL\Customizer\Controls\React\Range' ) ? 'DIFL\Customizer\Controls\React\Range' : 'DIFL\Customizer\Controls\Range'
			)
		);

		$this->add_control(
			new Control(
				self::SECTION . 'margin',
				[
					'default'   => self::$default_space_values,
					'transport' => $this->selective_refresh,
				],
				[
					'label'                 => __( 'Margin', 'divi_flash' ),
					'section'               => self::SECTION,
					'input_attrs'           => [
						'units' => self::UNIT,
					],
					'default'               => self::$default_space_values,
					'priority'              => 38,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'      => [
							'vars'       => '--margin',
							'selector'   => '#scroll-to-top',
							'responsive' => true,
						],
						'responsive'  => true,
						'directional' => true,
						'template'    =>
							'#scroll-to-top {
							padding-top: {{value.top}};
							padding-right: {{value.right}};
							padding-bottom: {{value.bottom}};
							padding-left: {{value.left}};
						}',
					],
					'active_callback'       => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);
		$this->add_control(
			new Control(
				self::SECTION . 'padding',
				[
					'default'   => self::$default_space_values,
					'transport' => $this->selective_refresh,
				],
				[
					'label'                 => __( 'Padding', 'divi_flash' ),
					'section'               => self::SECTION,
					'input_attrs'           => [
						'units' => self::UNIT,
					],
					'default'               => self::$default_space_values,
					'priority'              => 40,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'      => [
							'vars'       => '--padding',
							'selector'   => '#scroll-to-top',
							'responsive' => true,
						],
						'responsive'  => true,
						'directional' => true,
						'template'    =>
							'#scroll-to-top {
							padding-top: {{value.top}};
							padding-right: {{value.right}};
							padding-bottom: {{value.bottom}};
							padding-left: {{value.left}};
						}',
					],
					'active_callback'       => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);
		/**
		 * Button border radius
		 */
		$this->add_control(
			new Control(
				self::SECTION . 'border_radius',
				[
					'default'   => self::$default_space_values,
					'transport' => $this->selective_refresh,
				],
				[
					'label'                 => __( 'Border Radius', 'divi_flash' ),
					'section'               => self::SECTION,
					'input_attrs'           => [
						'units' => self::UNIT,
					],
					'default'               => self::$default_space_values,
					'priority'              => 42,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'      => [
							'vars'       => '--margin',
							'selector'   => '#scroll-to-top',
							'responsive' => true,
						],
						'responsive'  => true,
						'directional' => true,
						'template'    =>
							'#scroll-to-top {
							padding-top: {{value.top}};
							padding-right: {{value.right}};
							padding-bottom: {{value.bottom}};
							padding-left: {{value.left}};
						}',
					],
					'active_callback'       => [ $this, 'is_extension_enabled' ],
				],
				'\DIFL\Customizer\Controls\React\Spacing'
			)
		);
	}

	public function is_image_type_control() {
		if ( ! $this->is_extension_enabled() ) {
			return false;
		}

		return get_theme_mod( self::SECTION . 'type', 'none' ) === 'image';
	}

	public function is_icon_type_control() {
		if ( ! $this->is_extension_enabled() ) {
			return false;
		}

		return get_theme_mod( self::SECTION . 'type', 'none' ) === 'icon';
	}

	public function sanitize_scroll_to_top_type( $value ) {
		$allowed_values = [ 'icon', 'image', 'none' ];
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'icon';
		}

		return esc_html( $value );
	}

	public function sanitize_scroll_to_top_side( $value ) {
		$allowed_values = [ 'left', 'right' ];
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'right';
		}

		return esc_html( $value );
	}

	public function sanitize_scroll_to_top_alignemnt( $value ) {
		$allowed_values = [ 'horizontally', 'vertically' ];
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'horizontally';
		}

		return esc_html( $value );
	}

	public static function is_divi_btt_enabled() {
		if ( ! function_exists( 'et_get_option' ) ) {
			return false;
		}

		return et_get_option( 'divi_back_to_top', false ) === 'on';
	}

	public static function is_extension_enabled() {
		if ( ! self::is_divi_btt_enabled() ) {
			return false;
		}

		return get_theme_mod( self::SECTION . 'enable_difl_btt', false );
	}

	public static function label_has_value() {
		if ( ! self::is_divi_btt_enabled() || ! self::is_extension_enabled() ) {
			return false;
		}

		return get_theme_mod( self::SECTION . 'label', '' ) !== '';
	}

	public static function get_all_settings() {
		$settings = [];
	}
}
