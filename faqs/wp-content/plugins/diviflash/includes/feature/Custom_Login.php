<?php

namespace DIFL\Feature;

class Custom_Login {
	public function __construct() {
		add_filter( 'difl_setting_keys', [ $this, 'add_setting_keys' ] );
		add_action( 'template_redirect', [ $this, 'render_login_page' ] );
//		add_action( 'login_enqueue_scripts', [ $this, 'add_login_scripts' ] );
	}

	public function add_setting_keys( $keys ) {
		$login_keys = [
			'df_custom_login_enabled',
			'df_custom_login_url',
		];

		return array_merge( $keys, $login_keys );
	}

	public function render_login_page() {
		if ( ! get_option( 'df_custom_login_enabled' ) ||  is_user_logged_in() ) {
			return;
		}
		if ( empty( get_option( 'df_custom_login_url' ) ) ) {
			return;
		}

		$slug         = get_option( 'df_custom_login_url' );
		$current_path = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( ltrim( esc_url_raw( $_SERVER['REQUEST_URI'] ), '/' ) ) : '';
		if ( $slug !== $current_path ) {
			return;
		}

		$user_login = '';
		require_once ABSPATH . 'wp-login.php';
		exit;
	}
}

new Custom_Login();