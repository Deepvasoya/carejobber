<?php

namespace DIFL\Customizer\Frontend;

class Frontend {
	public static $instances = [];

	public static $theme_mods = [];

	public function __construct() {
		self::set_theme_mod_options();
		$this->reconcile_with_preview();
		$this->load_extension();
		$this->instantiate();
		$this->load_divi_icon();
	}

	protected function load_extension() {
		require DIFL_MAIN_DIR . '/customizer/frontend/Preloader.php';
		require DIFL_MAIN_DIR . '/customizer/frontend/Back_To_Top.php';
		require DIFL_MAIN_DIR . '/customizer/frontend/Login_Form.php';
	}

	public static function set_theme_mod_options() {
		$is_divi = 'Divi' === wp_get_theme()->get( 'Template' ) || wp_get_theme()->get( 'Name' );

		if ( ! $is_divi ) {
			return [];
		}

		$theme            = get_option( 'stylesheet' );
		self::$theme_mods = get_option( "theme_mods_{$theme}" );
	}

	public static function get_option_by_prefix( $prefix ) {
		if ( empty( $prefix ) || ! str_starts_with( $prefix, 'difl' ) || empty( self::$theme_mods ) ) {
			return [];
		}

		return array_filter( self::$theme_mods, function ( $key ) use ( $prefix ) {
			return strpos( $key, $prefix ) === 0;
		}, ARRAY_FILTER_USE_KEY );
	}

	public static function get_responsive_sizes( $string, $device = 'desktop', $type = 'single' ) {
		if ( empty( $string ) ) {
			return '';
		}

		static $sizes = '';

		if ( empty( $sizes ) ) {
			$sizes = $string;
		}

		if ( $sizes !== $string ) {
			$sizes = $string;
		}

		if ( 'single' === $type ) {
			$data = json_decode( $sizes, true );
			$unit = ! empty( $data['suffix'] ) && array_key_exists( $device, $data['suffix'] ) ? $data['suffix'][ $device ] : 'px';

			return esc_html( $data[ $device ] . $unit );
		}

		if ( 'quad' === $type ) {
			$data        = ! is_array( $sizes ) ? json_decode( $sizes, true ) : $sizes;
			$unit        = ! empty( $data[ $device . '-unit' ] ) ? $data[ $device . '-unit' ] : 'px';
			$device_data = array_map( function ( $side ) use ( $unit ) {
				return $side . $unit;
			}, $data[ $device ] );

			return esc_html( implode( ' ', array_values( $device_data ) ) );
		}

	}

	public static function get_bg_type( $background_color ) {
		return str_starts_with( $background_color, 'linear-gradient' ) || str_starts_with( $background_color, 'radial-gradient' ) ? 'background-image' : 'background';
	}

	protected function instantiate() {
		self::$instances['preloader']   = new Preloader();
		self::$instances['back_to_top'] = new Back_To_Top();
		self::$instances['login_form']  = new Login_Form();
	}

	protected function reconcile_with_preview() {
		if ( ! array_key_exists( 'nonce', $_REQUEST ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- handled below.
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'preview-customize_' . wp_get_theme()->get_stylesheet() ) ) {
			return;
		}

		if ( ! array_key_exists( 'customized', $_REQUEST ) || ! array_key_exists( 'wp_customize', $_REQUEST ) ) {
			return;
		}

		if ( 'on' !== $_REQUEST['wp_customize'] ) {
			return;
		}

		$customized_data = json_decode( stripslashes( sanitize_text_field( $_REQUEST['customized'] ) ), true );
		$theme           = get_option( 'stylesheet' );

		$updated_options = array_merge( self::$theme_mods, apply_filters( 'difl_reconcile_customizer_preview', $customized_data ) );
		update_option( "theme_mods_{$theme}", $updated_options );
		self::$theme_mods = $updated_options;
	}

	protected function populate_settings() {
		$this->settings = self::get_option_by_prefix( static::PREFIX );
	}

	protected function get_value( $key, $default = '' ) {
		$key = static::PREFIX . '_' . $key;
		if ( ! array_key_exists( $key, $this->settings ) ) {
			return $default;
		}

		$value = $this->settings[ $key ];
		// Support min zero value, and empty color value
		if ( ( is_int( $value ) && 0 === $value ) || is_string( $value ) && '' === $value ) {
			return $value;
		}

		return ! empty( $value ) ? $value : $default;
	}

	protected function load_divi_icon() {
		add_filter( 'et_late_global_assets_list', function ( $assets, $assets_args, $et_dynamic_assets ) {
			$assets_prefix          = et_get_dynamic_assets_path();
			$assets['et_icons_all'] = [
				'css' => "{$assets_prefix}/css/icons_all.css",
			];

			return $assets;
		}, 100, 3 );
	}

	public static function is_vb_or_tb() {

		return ( isset( $_GET['et_fb'] ) && '1' === $_GET['et_fb'] ) || ( isset( $_GET['page'] ) && 'et_theme_builder' === $_GET['page'] ); //phpcs:ignore
	}
}

new Frontend();