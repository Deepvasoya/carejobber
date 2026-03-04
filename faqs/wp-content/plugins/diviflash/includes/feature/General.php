<?php

namespace DIFL\Feature;

use DIFL\Customizer\Frontend\Frontend;

class General {
	public function __construct() {
		$this->clear_cache_hanlder();
		add_filter( 'difl_setting_keys', [ $this, 'add_settings_key' ] );
		add_filter( 'upload_mimes', [ $this, 'extend_upload_mime' ] );
		add_filter( 'et_pb_supported_font_formats', [ $this, 'extend_divi_font' ] );
		add_action( 'wp_footer', [ $this, 'handle_footer_options' ] );
	}

	public function add_settings_key( $keys ) {
		$new_keys = [
			'df_general_ttf_woff_support',
			'df_general_enable_cache_menu',
			'df_general_hide_footer_bar',
			'df_general_stick_footer_bottom',
		];

		return array_merge( $keys, $new_keys );
	}

	public function clear_cache_menu( $admin_bar ) {
		$admin_bar->add_menu( [
			'id'    => 'difl_clear_divi_cache',
			'title' => sprintf( '<span data-wpnonce="%1$s">%2$s</span>', wp_create_nonce( 'difl_clear_divi_cache' ), esc_html( 'Clear Divi Cache' ) ),
			'href'  => 'javascript:void(0)',
		] );

	}

	public function handle_clear_cache() {
		if ( ! isset( $_POST['_wpnonce'] ) ) {
			exit();
		}

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ), 'difl_clear_divi_cache' ) ) {
			exit();
		}
		if ( class_exists( 'ET_Core_PageResource' ) ) {
			$post_id = 'all';
			$owner   = 'all';
			\ET_Core_PageResource::remove_static_resources( $post_id, $owner );
			wp_send_json_success( esc_html( 'The static CSS file generation has been cleared!' ), 200 );
		}
	}

	public function admin_script() { ?>
        <script>
			(( $ ) => {
				$( document ).ready( function () {
					var ajax_url = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
					var is_admin = '<?php echo esc_html( is_admin() ); ?>';
					$( '#wp-admin-bar-difl_clear_divi_cache' ).click( function ( e ) {
						e.preventDefault();
						$.ajax( {
							type: 'post',
							dataType: 'json',
							url: ajax_url,
							data: {
								'action': 'difl_clear_divi_cache',
								'_wpnonce': $( this ).find( 'span' ).data( 'wpnonce' )
							},
							success: function ( response ) {
								if ( response.success ) {
									let res = response.data;
									if ( is_admin ) {
										let notice = '<div class="notice notice-success is-dismissible difl-cache-notice"><p>' + res + '</p></div>';
										if ( $( 'body .wrap h1' ).length > 0 ) {
											$( 'body .wrap h1' ).after( notice );
										} else {
											$( 'body #wpbody-content' ).prepend( notice );
										}
										setTimeout( function () {
											$( '.difl-cache-notice' ).fadeOut( 300, function () {
												$( this ).remove()
											} );
										}, 3500 );
									} else {
										alert( res );
									}
								}
							},
						} );
					} );
				} );
			})( jQuery )

        </script>
		<?php

	}

	public function handle_footer_options() {
		if ( ( isset( $_GET['et_fb'] ) && '1' === $_GET['et_fb'] ) || ( isset( $_GET['page'] ) && 'et_theme_builder' === $_GET['page'] ) || is_customize_preview() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended  -- general check for et_theme_builder doesn't require to check nonce
			return;
		}

		if ( ! get_option( 'df_general_hide_footer_bar' ) && ! get_option( 'df_general_stick_footer_bottom' ) ) {
			return;
		}

		?>
        <style>
            .difl-fixed-footer {
                position: fixed;
                width: 100%;
                bottom: 0;
                z-index: 2
            }

            <?php if (get_option( 'df_general_hide_footer_bar' )){?>
            #main-footer #footer-bottom {
                display: none;
            }

            <?php }?>
        </style>
		<?php if ( get_option( 'df_general_stick_footer_bottom' ) ) { ?>
            <script>
				(() => {
					window.addEventListener( 'load', () => {
						let footer = document.getElementById( 'main-footer' );
						if ( null === footer || undefined === footer ) {
							footer = document.querySelector( 'footer' );
						}
                        const handleFixBottom = () => {
							const body_width = parseInt( getComputedStyle( document.querySelector( 'body' ) ).height.replace( 'px', '' ) );
							const window_height = window.innerHeight;
							if ( body_width < window_height ) {
								footer.classList.add( 'difl-fixed-footer' );
							} else {
								footer.classList.remove( 'difl-fixed-footer' );
							}
						}

						handleFixBottom();

						window.addEventListener( 'scroll', handleFixBottom );
						window.addEventListener( 'resize', handleFixBottom );
					} )
				})()
            </script>
		<?php } ?>
		<?php
	}

	protected function clear_cache_hanlder() {
		if ( ! get_option( 'df_general_enable_cache_menu' ) ) {
			return;
		}
		add_action( 'admin_bar_menu', [ $this, 'clear_cache_menu' ], PHP_INT_MAX );
		add_action( 'admin_footer', [ $this, 'admin_script' ], PHP_INT_MAX );
		add_action( 'wp_ajax_difl_clear_divi_cache', [ $this, 'handle_clear_cache' ] );
	}

	public function extend_divi_font( $mimes ) {
		if ( ! get_option( 'df_general_ttf_woff_support' ) ) {
			return $mimes;
		}
		if ( ! defined( 'ALLOW_UNFILTERED_UPLOADS' ) ) {
			define( 'ALLOW_UNFILTERED_UPLOADS', true );
		}

		return array_merge( $mimes, [ 'otf', 'ttf', 'woff', 'woff2' ] );
	}

	public function extend_upload_mime( $mimes ) {
		if ( ! get_option( 'df_general_ttf_woff_support' ) ) {
			return $mimes;
		}
		if ( ! defined( 'ALLOW_UNFILTERED_UPLOADS' ) ) {
			define( 'ALLOW_UNFILTERED_UPLOADS', true );
		}
		$mimes['ttf']   = 'font/ttf|application/font-ttf|application/x-font-ttf|application/octet-stream';
		$mimes['otf']   = 'font/otf|application/font-otf|application/x-font-otf|application/octet-stream';
		$mimes['woff']  = 'font/woff|application/font-woff|application/x-font-woff|application/octet-stream';
		$mimes['woff2'] = 'font/woff2|application/font-woff2|application/x-font-woff2|application/octet-stream';

		return $mimes;
	}

}

new General();