<?php

namespace DIFL\Customizer;

class Assets {
	private $plugin_path;
	private $plugin_url;

	private $path = 'admin/customizer/';

	public function __construct() {
		$this->plugin_path = plugin_dir_path( __DIR__ );
		$this->plugin_url  = plugin_dir_url( __DIR__ );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'enqueue_customizer_style' ], 9999 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_customizer_style' ], 999 );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'enqueue_customizer_components' ], 10 );
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'enqueue_customizer_controls' ] );
	}

	public function enqueue_customizer_style() {
		if ( ! \DIFL\Customizer\Extensions\Back_To_Top::is_extension_enabled() && ! \DIFL\Customizer\Extensions\Preloader::is_extension_enabled() && ! is_customize_preview() ) {
			return;
		}

		$this->enqueue_customizer_components();
		$path   = $this->path . 'css/';
		$handle = 'difl-customizer';

		wp_enqueue_style(
			$handle . '-loaders',
			$this->plugin_url . $path . 'loaders.min.css'
		);

		wp_enqueue_style(
			$handle,
			$this->plugin_url . $path . 'customizer-style.css',
			[ 'difl-customizer-components' ]
		);
	}

	public function enqueue_customizer_controls() {
		$handle       = 'difl-customizer-controls';
		$dependencies = include_once $this->plugin_path . $this->path . 'controls.asset.php';


		wp_enqueue_script(
			$handle,
			$this->plugin_url . $this->path . 'controls.js',
			$dependencies['dependencies'],
			$dependencies['version'], true );
	}

	public function enqueue_customizer_components() {
		$handle       = 'difl-customizer-components';
		$dependencies = include_once $this->plugin_path . $this->path . 'components.asset.php';
		$version      = DIFL_VERSION;
		if ( is_array( $dependencies ) && array_key_exists( 'version', $dependencies ) ) {
			$version = $dependencies['version'];
		}
		wp_enqueue_style(
			$handle,
			$this->plugin_url . $this->path . 'style-components.css',
			[ 'wp-components' ],
			$version );
	}
}

new Assets();