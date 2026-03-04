<?php

namespace DIFL\Handler;

/**
 * Issue handler with various plugin and scenarios.
 */
class Plugin {
	public function __construct() {
		add_filter( 'difl_dashboard_local_vars', [ $this, 'extra_vars_on_dashboard' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'disable_wpforms_pro_style' ], PHP_INT_MAX );
		add_action( 'init', [$this,'difl_flush_rewrite_rules'] );
	}

	public function extra_vars_on_dashboard( $vars ) {
		if ( class_exists( 'WPForms_Pro' ) ) {
			$vars['wpforms_pro'] = true;
		}

		return $vars;
	}

	public function disable_wpforms_pro_style() {
		if ( ! class_exists( 'WPForms_Pro' ) ) {
			return;
		}
		if ( ! get_option( 'df_disable_wpforms_pro_style' ) ) {
			return;
		}
		wp_deregister_style( 'wpforms-divi-pro-modern-full' );
		wp_dequeue_style( 'wpforms-divi-pro-modern-full' );
		wp_deregister_style( 'wpforms-modern-full' );
		wp_dequeue_style( 'wpforms-modern-full' );
		wp_deregister_style( 'wpforms-pro-integrations' );
		wp_dequeue_style( 'wpforms-pro-integrations' );
	}

	public function difl_flush_rewrite_rules() {
		if ( wp_doing_ajax() ) {
			return;
		}

		$flush_it = get_transient( 'difl_flush_rewrite_rules' );
		if ( 'true' === $flush_it ) {
			flush_rewrite_rules();
			delete_transient( 'difl_flush_rewrite_rules' );
		}
	}
}

new Plugin();