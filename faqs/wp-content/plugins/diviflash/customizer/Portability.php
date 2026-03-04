<?php

namespace DIFL\Customizer;


class Portability {
	public function __construct() {
		add_filter( 'option_et_divi', [ $this, 'handle_on_export' ] );
		add_filter( 'pre_update_option_et_divi', [ $this, 'handle_on_import' ] );
	}


	public function handle_on_export( $data ) {
		//phpcs:disable -- Hooked on the core which handled nonce previously
		if ( ! array_key_exists( 'action', $_REQUEST ) || ! array_key_exists( 'context', $_REQUEST ) ) {
			return $data;
		}

		if ( 'et_divi_mods' !== $_REQUEST['context'] || 'et_core_portability_export' !== $_REQUEST['action'] ) {
			return $data;
		}
		$theme              = get_option( 'stylesheet' );
		$data['theme_mods'] = get_option( "theme_mods_{$theme}" );

		return maybe_unserialize( $data );
	}

	public function handle_on_import( $value ) {
		if ( ! array_key_exists( 'action', $_REQUEST ) || ! array_key_exists( 'context', $_REQUEST ) ) {
			return $value;
		}

		if ( 'et_divi_mods' !== $_REQUEST['context'] || 'et_core_portability_import' !== $_REQUEST['action'] ) {
			return $value;
		}

		if ( ! array_key_exists( 'theme_mods', $value ) ) {
			return $value;
		}

		$imported_val = $value['theme_mods'];
		$theme        = get_option( 'stylesheet' );
		$option       = "theme_mods_{$theme}";
		$old_val      = get_option( $option );
		update_option( $option, array_merge( $old_val, $imported_val ) );
		unset( $value['theme_mods'] );

		return $value;

		//phpcs:enable
	}
}

new Portability();