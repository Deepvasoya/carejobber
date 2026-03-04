<?php

namespace DIFL\Feature;

use DIFL\Customizer\Controls\Preloader;
use DIFL\Customizer\Frontend\Frontend;

class Admin {
	protected static $settings = [];

	public function __construct() {
		self::populate_settings();

		add_filter( 'register_project_post_type_args', [ $this, 'register_post_type_args' ], PHP_INT_MAX, 2 );
		$taxonomies = [ 'project_category', 'project_tag' ];
		foreach ( $taxonomies as $taxonomy ) {
			add_filter( "register_{$taxonomy}_taxonomy_args", [ $this, 'register_taxonomy_args' ], PHP_INT_MAX, 3 );
		}

		add_action( 'init', [ $this, 'handle_taxonomy' ], PHP_INT_MAX );
		add_action( 'show_admin_bar', [ $this, 'handle_admin_bar' ] );
		add_action( 'admin_init', [ $this, 'dequeue_svg_color' ] );
	}

	public function register_post_type_args( $args, $post_type ) {
		$settings = self::$settings;
		if ( ! array_key_exists( 'df_project_cpt_rename', $settings ) || ! $settings['df_project_cpt_rename'] ) {
			return $args;
		}

		$settings = (array) $settings['df_project_cpt_rename_option'];

		$singular_name = empty( $settings['singular_name'] ) ? esc_html__( 'Project', 'divi_flash' ) : $settings['singular_name'];
		$plural_name   = empty( $settings['plural_name'] ) ? esc_html__( 'Projects', 'divi_flash' ) : $settings['plural_name'];
		$slug          = empty( $settings['slug'] ) ? 'project' : $settings['slug'];
		$icon          = empty( $settings['icon'] ) ? 'dashicons-admin-post' : $settings['icon'];

		$slug = preg_replace( '/[^A-Za-z0-9-]+/', '-', strtolower( $slug ) );

		if ( 'project' == $post_type ) {
			$args['labels']['name']           = $plural_name;
			$args['labels']['singular_name']  = $singular_name;
			$args['labels']['menu_name']      = $plural_name;
			$args['labels']['name_admin_bar'] = $singular_name;
			$args['labels']['add_new_item']   = esc_html__( 'Add New', 'divi_flash' ) . $singular_name;
			$args['labels']['edit_item']      = esc_html__( 'Edit', 'divi_flash' ) . $singular_name;
			$args['labels']['view_item']      = esc_html__( 'View ', 'divi_flash' ) . $singular_name;
			$args['labels']['search_items']   = esc_html__( 'Search ', 'divi_flash' ) . $plural_name;
			$args['labels']['all_items']      = esc_html__( 'All ', 'divi_flash' ) . $plural_name;
			$args['rewrite']['slug']          = $slug;
			if ( ! empty( $icon ) ) {
				$args['menu_icon'] = $icon;
			}
		}

		return $args;
	}

	public function register_taxonomy_args( $args, $taxonomy, $object_type ) {
		$settings = self::$settings;
		if ( ! array_key_exists( 'df_project_cpt_rename', $settings ) || ! $settings['df_project_cpt_rename'] ) {
			return $args;
		}

		$settings = (array) $settings['df_project_cpt_rename_option'];

		$singular_name = empty( $settings['singular_name'] ) ? esc_html__( 'Project', 'divi_flash' ) : $settings['singular_name'];
		$plural_name   = empty( $settings['plural_name'] ) ? esc_html__( 'Projects', 'divi_flash' ) : $settings['plural_name'];
		$slug          = preg_replace( '/[^A-Za-z0-9-]+/', '-', strtolower( empty( $settings['slug'] ) ? 'project' : $settings['slug'] ) );
		$cat_slug      = preg_replace( '/[^A-Za-z0-9-]+/', '-', strtolower( empty( $settings['category_slug'] ) ? 'project_category' : $settings['category_slug'] ) );
		$tag_slug      = preg_replace( '/[^A-Za-z0-9-]+/', '-', strtolower( empty( $settings['tag_archive'] ) ? 'project_tag' : $settings['tag_archive'] ) );

		if ( 'project_category' === $taxonomy ) {
			if ( ! empty( $cat_slug ) ) {
				$args['rewrite']['slug'] = $cat_slug;
			} else {
				$args['rewrite']['slug'] = $slug . '_category';
			}
			$args['labels']['singular_name'] = $singular_name . ' Category';
			$args['labels']['name']          = $plural_name . ' Categories';
		}

		if ( 'project_tag' == $taxonomy ) {
			if ( ! empty( $tag_slug ) ) {
				$args['rewrite']['slug'] = $tag_slug;
			} else {
				$args['rewrite']['slug'] = $slug . '_tag';
			}
			$args['labels']['name']          = $plural_name . ' Tags';
			$args['labels']['singular_name'] = $singular_name . ' Tag';
		}

		return $args;
	}

	public function handle_taxonomy() {
		$settings = self::$settings;
		if ( array_key_exists( 'df_hide_media_category', $settings ) && $settings['df_hide_media_category'] ) {
			unregister_taxonomy( \DiviFlash_Media_Category::TAXONOMY );
		}
		if ( array_key_exists( 'df_hide_page_category', $settings ) && $settings['df_hide_page_category'] ) {
			unregister_taxonomy( \DiviFlash_Page_Category::TAXONOMY );
		}

		if ( array_key_exists( 'df_hide_project_cpt', $settings ) && $settings['df_hide_project_cpt'] ) {
			unregister_post_type( 'project' );
		}
	}

	protected static function populate_settings() {
		$settings = [];
		$keys     = [
			'df_hide_admin_bar',
			'df_hide_admin_bar_roles',
			'df_project_cpt_rename',
			'df_project_cpt_rename_option',
			'df_hide_media_category',
			'df_hide_page_category',
			'df_hide_project_cpt',
		];
		foreach ( $keys as $setting_key ) {
			$settings[ $setting_key ] = get_option( $setting_key );
		}

		self::$settings = $settings;
	}

	public function handle_admin_bar( $return ) {
		if ( ( isset( $_GET['et_fb'] ) && '1' === $_GET['et_fb'] ) || ( isset( $_GET['page'] ) && 'et_theme_builder' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- As this is general handling nonce checking can be escaped
			return $return;
		}

		if ( array_key_exists( 'df_hide_admin_bar', self::$settings ) && empty( self::$settings['df_hide_admin_bar'] ) ) {
			return $return;
		}

		if ( ( ! array_key_exists( 'df_hide_admin_bar', self::$settings ) && ! empty( self::$settings['df_hide_admin_bar'] ) ) || ! wp_get_current_user() ) {
			return $return;
		}

		$roles         = (array) self::$settings['df_hide_admin_bar_roles'];
		$user_roles    = wp_get_current_user()->roles;
		$current_roles = array_filter( $roles, function ( $role ) use ( $user_roles ) {
			return in_array( $role, $user_roles );
		}, ARRAY_FILTER_USE_KEY );
		foreach ( $current_roles as $value ) {
			if ( ! empty( $value ) ) {
				return false;
			}
		}

		return $return;
	}

	public function dequeue_svg_color() {
		wp_deregister_script( 'svg-painter' );
		wp_dequeue_script( 'svg-painter' );
	}
}


new Admin();