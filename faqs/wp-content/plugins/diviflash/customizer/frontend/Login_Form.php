<?php

namespace DIFL\Customizer\Frontend;

class Login_Form extends Frontend {
	protected $settings = [];

	const PREFIX = 'difl_login_form_';

	public function __construct() {
		$this->populate_settings();
		add_action( 'login_head', [ $this, 'handle_login_form_styles' ], PHP_INT_MAX );
	}

	protected function populate_settings() {
		if ( ! \DIFL\Customizer\Extensions\Login_Form::is_extension_enabled() ) {
			return;
		}
		parent::populate_settings();
		$base_settings = [];
		foreach ( $this->settings as $key => $value ) {
			$base_settings[ str_replace( self::PREFIX, '', $key ) ] = $value;
		}

		$this->settings = $base_settings;

		$this->settings['home_url'] = get_home_url();
	}

	public function handle_login_form_styles() {
		if ( ! get_option( 'df_custom_login_enabled' ) ) {
			return;
		}
		wp_enqueue_style( 'et-core-main-fonts' );
		wp_enqueue_style( 'et_google_fonts_style' );
		if ( array_key_exists( 'font_family', $this->settings ) ) {
			$font_name = $this->settings['font_family'];
			et_divi_load_scripts_styles();
			et_builder_enqueue_font( $font_name );
			et_builder_print_font();
		}
		if ( is_customize_preview() ) {
			return;
		}
		?>
        <style>
            body:has(#login) {
                overflow-x: hidden;
            }

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

            body #login .wp-login-logo a{
                background-position: center !important;
                background-size: contain !important;
            }
        </style>
        <script>
			(() => {
				const settings = <?php echo wp_json_encode( $this->settings ); ?>;
				const defaults = {
					form_box_shadow_horizontal: '2',
					form_box_shadow_vertical: '2',
					form_box_shadow_blur: '2',
					form_box_shadow_spread: '2',
					form_box_shadow_color: '#FFFFFF',
					form_border_width: '2',
					form_border_radius: '5',
					form_border_color: '#000000',
					form_footer_copyright_text: 'Copyright',
					form_footer_copyright_text_color: '#000',
					form_footer_copyright_text_background_color: '#fff',
					form_footer_lost_password_text_color: '#000',
					form_footer_lost_password_text_hover_color: '#000',
					form_input_field_label_color: '#000',
					form_input_field_border_color: '#000000',
					form_input_field_text_color: '#000',
					button_shadow_color: '#000000',
					button_shadow_blur: '2',
					button_shadow_horizontal: '2',
					button_shadow_vertical: '2',
					button_shadow_spread: '2',
					button_background_color: '#fff',
					button_text_color: '#000',
					button_text_hover_color: '#000',
					button_shadow_hover_blur: '2',
                    button_shadow_hover_color: '#000',
					form_input_field_background_color_active: '#fff',
					form_input_field_remember_me_label_color: '#000',
                    background_size: 'cover',
                    background_repeat: 'no-repeat',
                    background_position: 'center',
				}
				Object.entries( defaults ).forEach( ( [ key, value ] ) => {
					if ( ! settings[key] ) {
						settings[key] = value;
					}
				} )

				const get_current_device = () => {
					let device = 'desktop';
					const is_mobile = window.matchMedia( '(max-width: 720px)' ).matches;
					const is_tablet = window.matchMedia( '(min-width: 426px) and (max-width: 1024px)' ).matches;

					if ( is_mobile ) {
						device = 'mobile';
					} else if ( is_tablet ) {
						device = 'tablet';
					}
					return device;
				}

				const get_responsive_value = ( string, type = 'single' ) => {
					if ( ! string ) {
						return '';
					}

					const device = get_current_device();
					let sizes = string;

					if ( type === 'single' ) {
						const data = JSON.parse( sizes );
						const unit =
							data.suffix && device in data.suffix ? data.suffix[device] : 'px';

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

				const handleLoginForm = () => {
					if ( ! settings ) {
						return;
					}
					const login = document.getElementById( 'login' );

					if ( ! login ) {
						return;
					}
					const body = document.querySelector( 'body.login' );
					const logo_heading = login.querySelector( '.wp-login-logo' );
					const logo_link = login.querySelector( '.wp-login-logo a' );
					const login_form = login.querySelector( '#loginform' );
					const form_input_field = login_form.querySelectorAll( 'input[type="text"], input[type="password"]' );
					const form_inputs = login_form.querySelectorAll( 'input[id="user_login"], input[id="user_pass"]' );
					const form_labels = login_form.querySelectorAll( 'label[for="user_login"], label[for="user_pass"]' );
					const remember_me_checkbox = login_form.querySelector( '#rememberme' );
					const user_name = login_form.querySelector( '#user_login' );
					const user_name_label = login_form.querySelector( 'label[for="user_login"]' );
					const password = login_form.querySelector( '#user_pass' );
					const password_label = login_form.querySelector( 'label[for="user_pass"]' );
					const remember_me = login_form.querySelector( '#rememberme' );
					const remember_me_label = login_form.querySelector( 'label[for="rememberme"]' );
					const login_button = login_form.querySelector( '#wp-submit' );
					const back_to_site = body.querySelector( '#backtoblog' );
					const back_to_site_text = back_to_site.querySelector( 'a' );
					const lost_password_container = body.querySelector( '#nav' );
					const lost_password = body.querySelector( '#nav' );

					if ( settings.background_image ) {
						body.style.backgroundImage = `url(${ settings.background_image })`;
						body.style.backgroundSize = `${ settings.background_size }`;
						body.style.backgroundRepeat = `${ settings.background_repeat }`;
						body.style.backgroundPosition = `${ settings.background_position }`;
					}

					if ( ! settings.background_size ) {
						body.style.backgroundSize = 'cover';
					}
					if ( ! settings.background_repeat ) {
						body.style.backgroundRepeat = 'no-repeat';
					}
					if ( ! settings.background_position ) {
						body.style.backgroundPosition = 'center';
					}
					if ( settings.background_color && ! settings.background_color.startsWith( 'linear-gradient' ) ) {
						body.style.backgroundColor = settings.background_color;
					}

					if ( settings.background_color && settings.background_color.startsWith( 'linear-gradient' ) ) {
						body.style.backgroundImage = settings.background_color;
					}

					if ( settings.background_color && ! settings.background_image ) {
						body.style.backgroundColor = settings.background_color;
						if ( settings.background_color.startsWith( 'linear-gradient' ) ) {
							body.style.backgroundImage = settings.background_color;
						}
					}

					// Logo
					if ( ! settings.disable_logo ) {
						if ( settings.logo_image ) {
							logo_link.style.backgroundImage = `url(${ settings.logo_image })`;
						}
						if ( settings.logo_bottom_spacing ) {
							logo_heading.style.marginBottom = get_responsive_value( settings.logo_bottom_spacing );
						}

						if ( settings.logo_width ) {
							const logo_width = get_responsive_value( settings.logo_width );
							logo_link.style.width = logo_width;
							logo_link.style.height = logo_width;
							logo_link.style.backgroundSize = logo_width;
						}
						if ( settings.logo_height ) {
							logo_link.style.height = get_responsive_value( settings.logo_height );
						}

						logo_link.href = settings.home_url;
					} else {
						logo_heading.style.display = 'none';
					}

					//Font Family
					if ( settings.font_family ) {
						login.style.fontFamily = `'${ settings.font_family }'`;
					}


					// Form
					if ( settings.form_remove_background ) {
						login_form.style.backgroundColor = 'transparent';
						login_form.style.backgroundImage = 'none';
					}

					if ( settings.form_alignment ) {
						login_form.style.alignItems = settings.form_alignment;
					}

					if ( settings.form_background_image && ! settings.form_remove_background ) {
						login_form.style.backgroundImage = `url(${ settings.form_background_image })`;
						login_form.style.backgroundSize = 'cover';
						login_form.style.backgroundPosition = 'center';
						login_form.style.backgroundRepeat = 'no-repeat';
					}

					if ( settings.form_background_color && ! settings.form_remove_background && ! settings.form_background_image ) {
						login_form.style.backgroundColor = settings.form_background_color;
						if ( settings.form_background_color.startsWith( 'linear-gradient' ) ) {
							login_form.style.backgroundImage = settings.form_background_color;
						}
					}

					if ( settings.form_width ) {
						login_form.style.width = get_responsive_value( settings.form_width );
					} else {
						login_form.style.width = '400px';
					}

					if ( settings.form_height ) {
						login_form.style.height = get_responsive_value( settings.form_height );
					} else {
						login_form.style.height = '400px';
					}
					if ( settings.form_border_width ) {
						login_form.style.borderWidth = get_responsive_value( settings.form_border_width );
					}
					if ( settings.form_border_radius ) {
						login_form.style.borderRadius = get_responsive_value( settings.form_border_radius );
					}
					if ( settings.form_border_color ) {
						login_form.style.borderColor = settings.form_border_color;
					}
					if ( settings.form_box_shadow ) {
						login_form.style.boxShadow = `${ settings.form_box_shadow_horizontal }px ${ settings.form_box_shadow_vertical }px ${ settings.form_box_shadow_blur }px ${ settings.form_box_shadow_spread }px ${ settings.form_box_shadow_color }`;
					}

					if ( settings.form_padding ) {
						login_form.style.padding = get_responsive_value( settings.form_padding, 'quad' );
					}

					form_inputs.forEach( element => {
						element.style.backgroundColor = settings.form_input_field_background_color;
						element.style.color = settings.form_input_field_text_color;
						element.style.fontSize = get_responsive_value( settings.form_input_field_text_size );
						element.style.borderColor = settings.form_input_field_border_color;
						element.style.borderRadius = get_responsive_value( settings.form_input_field_border_radius, 'quad' );
						element.style.borderWidth = get_responsive_value( settings.form_input_field_border_width );
						element.style.padding = get_responsive_value( settings.form_input_field_padding, 'quad' );
						element.style.margin = get_responsive_value( settings.form_input_field_margin, 'quad' );
						if ( ! settings.form_input_field_border_color ) {
							element.style.borderColor = '#000';
						}
						if ( ! settings.form_input_field_border_radius ) {
							element.style.borderRadius = '8px 10px';
						}
						if ( ! settings.form_input_field_margin ) {
							element.style.margin = '8px 10px';
						}
						if ( ! settings.form_input_field_padding ) {
							element.style.padding = '8px 10px';
						}
						element.addEventListener( 'focus', () => {
							element.style.backgroundColor = settings.form_input_field_background_color_active;
						} );
						element.addEventListener( 'focusout', () => {
							element.style.backgroundColor = settings.form_input_field_background_color;
						} );
					} );

					form_labels.forEach( element => {
						element.style.fontSize = get_responsive_value( settings.form_input_field_label_font_size );
						element.style.color = settings.form_input_field_label_color;
					} )

					if ( document.querySelector( '#login-message' ) ) {
						document.querySelector( '#login-message p' ).style.fontSize = get_responsive_value( settings.form_input_field_label_font_size );
						document.querySelector( '#login-message.notice.notice-info.message p' ).style.color = 'green';
					}

					if ( document.querySelector( '#login_error' ) ) {
						document.querySelector( '#login_error.notice.notice-error p' ).style.color = 'red';
						document.querySelector( '#login_error.notice.notice-error p' ).style.fontSize = get_responsive_value( settings.form_input_field_label_font_size );
						const paragraph = document.querySelector( '#login_error.notice.notice-error p' );

						const childNodes = Array.from( paragraph.childNodes );

						const lastTextNode = childNodes.reverse().find( node => node.nodeType === Node.TEXT_NODE );

						if ( lastTextNode ) {
							const span = document.createElement( 'span' );

							span.textContent = lastTextNode.textContent;
							lastTextNode.textContent = '';
							paragraph.appendChild( span );

							span.style.display = 'block';
						}
					}

					remember_me_checkbox.style.width = get_responsive_value( settings.form_input_field_remember_me_label_font_size );
					remember_me_checkbox.style.height = get_responsive_value( settings.form_input_field_remember_me_label_font_size );
					remember_me_label.style.fontSize = get_responsive_value( settings.form_input_field_remember_me_label_font_size );
					remember_me_label.style.color = settings.form_input_field_remember_me_label_color;
					remember_me_label.style.verticalAlign = 'middle';


					// Adjust password icon
					const password_height = getComputedStyle( password ).height;
					document.querySelector( '.wp-pwd button' ).style.height = password_height;
					document.querySelector( '.wp-pwd span.dashicons' ).style.display = 'inline';
					// Button
					if ( settings.button_text_size ) {
						login_button.style.fontSize = get_responsive_value( settings.button_text_size );
					}
					if ( settings.button_text_color ) {
						login_button.style.color = settings.button_text_color;
					}
					if ( settings.button_background_color ) {
						login_button.style.backgroundColor = settings.button_background_color;
						if ( settings.button_background_color.startsWith( 'linear-gradient' ) ) {
							login_button.style.backgroundImage = settings.button_background_color;
						}
					}

					if ( settings.button_border_width ) {
						login_button.style.borderWidth = get_responsive_value( settings.button_border_width );
					}
					if ( settings.button_border_radius ) {
						login_button.style.borderRadius = get_responsive_value( settings.button_border_radius );
					}
					if ( settings.button_border_color ) {
						login_button.style.borderColor = settings.button_border_color;
					}

					if ( settings.button_padding ) {
						login_button.style.padding = get_responsive_value( settings.button_padding, 'quad' );
					}else{
						login_button.style.padding = '8px 10px 8px 10px';
					}
					const applyButtonBoxShadow = () => {
						login_button.style.boxShadow = `${ settings.button_shadow_horizontal }px ${ settings.button_shadow_vertical }px ${ settings.button_shadow_blur }px ${ settings.button_shadow_spread }px ${ settings.button_shadow_color }`;
					}
					const applyButtonHoverBoxShadow = () => {
						login_button.style.boxShadow = `${ settings.button_shadow_horizontal }px ${ settings.button_shadow_vertical }px ${ settings.button_shadow_hover_blur }px ${ settings.button_shadow_spread }px ${ settings.button_shadow_hover_color }`;
					}

					if ( settings.button_enable_shadow ) {
						login_button.style.boxShadow = `${ settings.button_shadow_horizontal }px ${ settings.button_shadow_vertical }px ${ settings.button_shadow_blur }px ${ settings.button_shadow_spread }px ${ settings.button_shadow_color }`;
					}

					login_button.addEventListener( 'mouseenter', () => {
						if ( settings.button_text_hover_color ) {
							login_button.style.color = settings.button_text_hover_color;
						}
						if ( settings.button_background_color_hover ) {
							login_button.style.backgroundColor = settings.button_background_color_hover;
						}
						if ( settings.button_border_color_hover ) {
							login_button.style.borderColor = settings.button_border_color_hover;
						}
					} );

					login_button.addEventListener( 'mouseleave', () => {
						if ( settings.button_text_hover_color ) {
							login_button.style.color = settings.button_text_color;
						}
						if ( settings.button_background_color_hover ) {
							login_button.style.backgroundColor = settings.button_background_color;
						}
						if ( settings.button_border_color_hover ) {
							login_button.style.borderColor = settings.button_border_color;
						}
					} );

					if ( settings.button_shadow_hover_enable ) {
						login_button.addEventListener( 'mouseenter', applyButtonHoverBoxShadow );
						login_button.addEventListener( 'mouseleave', applyButtonBoxShadow );
					}

					// Form Footer Back to Site
					if ( settings.form_footer_disable_lost_password ) {
						lost_password_container.style.display = 'none';
					} else {
						lost_password.style.display = 'block';
						lost_password.style.fontSize = get_responsive_value( settings.form_footer_lost_password_text_size );
						lost_password.style.color = settings.form_footer_lost_password_text_color;
						lost_password.style.textDecoration = settings.form_footer_lost_password_text_decoration;
						lost_password.addEventListener( 'mouseenter', () => {
							if ( settings.form_footer_lost_password_text_hover_color ) {
								lost_password.style.color = settings.form_footer_lost_password_text_hover_color;
							}
						} );
						lost_password.addEventListener( 'mouseleave', () => {
							if ( settings.form_footer_lost_password_text_hover_color ) {
								lost_password.style.color = settings.form_footer_lost_password_text_color;
							}
						} );
					}


					if ( settings.form_footer_disable_back_to_site ) {
						back_to_site.style.display = 'none';
					} else {
						back_to_site_text.style.textDecoration = settings.form_footer_back_to_site_text_decoration;
						back_to_site_text.style.fontSize = get_responsive_value( settings.form_footer_back_to_site_text_size );
						back_to_site_text.style.color = settings.form_footer_back_to_site_text_color;
						back_to_site.addEventListener( 'mouseenter', () => {
							if ( settings.form_footer_back_to_site_text_hover_color ) {
								back_to_site_text.style.color = settings.form_footer_back_to_site_text_hover_color;
							}
						} );
						back_to_site.addEventListener( 'mouseleave', () => {
							if ( settings.form_footer_back_to_site_text_hover_color ) {
								back_to_site_text.style.color = settings.form_footer_back_to_site_text_color;
							}
						} );
					}

					// Form Footer Copyright
					if ( settings.form_footer_enable_copyright ) {
						const copyright = document.createElement( 'p' );
						copyright.classList.add( 'copyright' );
						copyright.innerHTML = settings.form_footer_copyright_text;
						copyright.style.color = settings.form_footer_copyright_text_color;
						copyright.style.backgroundColor = settings.form_footer_copyright_text_background_color;
						if ( settings.form_footer_copyright_text_font_size ) {
							copyright.style.fontSize = get_responsive_value( settings.form_footer_copyright_text_font_size );
						} else {
							copyright.style.fontSize = '16px';
						}
						if ( settings.form_footer_copyright_text_padding ) {
							copyright.style.padding = get_responsive_value( settings.form_footer_copyright_text_padding );
						} else {
							copyright.style.padding = '10px';
						}
						copyright.style.textAlign = 'center';
						body.appendChild( copyright );
					}
				}

				window.addEventListener( 'DOMContentLoaded', handleLoginForm );
			})();
        </script>
		<?php
	}
}