<?php

namespace DIFL\Feature;

class Maintenance {
	public function __construct() {
		add_action( 'template_redirect', [ $this, 'load_page' ] );
		add_filter( 'difl_setting_keys', [ $this, 'add_setting_keys' ] );
		add_filter( 'difl_dashboard_local_vars', [ $this, 'add_dashboard_vars' ] );
	}

	public function add_dashboard_vars( $vars ) {
		$pages = get_pages(
			[
				'posts_per_page' => - 1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
				'fields'         => [ 'ids', 'title' ],
			]
		);

		$vars['df_maintenance_mode_page'] = array_map( function ( $page ) {
			return [
				'value' => $page->ID,
				'label' => $page->post_title,
			];
		}, $pages );

		return $vars;
	}

	public function add_setting_keys( $keys ) {
		$maintenance_keys = [
			'df_maintenance_mode_enabled',
			'df_maintenance_mode_page',
			'df_maintenance_mode_disable_header',
			'df_maintenance_mode_disable_footer',
			'df_maintenance_mode_enable_full_height',
		];

		return array_merge( $keys, $maintenance_keys );
	}

	public function load_page() {
		if ( is_user_logged_in() ) {
			return;
		}
		$is_vb_tb = ( isset( $_GET['et_fb'] ) && '1' === $_GET['et_fb'] ) || ( isset( $_GET['page'] ) && 'et_theme_builder' === $_GET['page'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- As this is general handling nonce checking can be escaped
		if ( $is_vb_tb ) {
			return;
		}

		if ( ! get_option( 'df_maintenance_mode_enabled' ) && ! is_admin() ) {
			return;
		}

		$page        = get_option( 'df_maintenance_mode_page' );
		$current_url = get_permalink();
		$page_url    = get_permalink( $page );
		$header      = get_option( 'df_maintenance_mode_disable_header', 0 );
		$footer      = get_option( 'df_maintenance_mode_disable_footer', 0 );
		$full_height = get_option( 'df_maintenance_mode_enable_full_height', 0 );

		if ( $header || $footer || $full_height ) { ?>
            <script>
				(() => {
					window.addEventListener( 'load', () => {
						<?php if ( $header ) { ?>
						const header = document.querySelector( 'header' );

						if ( header ) {
							header.remove();
						}
						<?php } ?>

						<?php if ( $footer ) { ?>
						const footer = document.querySelector( 'footer' );

						if ( footer ) {
							footer.remove();
						}
						<?php } ?>
						<?php if ( $full_height ) { ?>
						const body = document.getElementById( 'page-container' );
						if ( body ) {
							body.style.padding = '0 !important';
							body.style.margin = '0 !important';
						}
						<?php } ?>
					} )
				})()
            </script>
		<?php }
		if ( $page_url !== $current_url ) {
			$page_url = get_permalink( $page );
			wp_safe_redirect( $page_url );
			exit;
		}

	}
}

new Maintenance();