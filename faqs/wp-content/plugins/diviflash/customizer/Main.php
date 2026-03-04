<?php
namespace DIFL\Customizer;

use DIFL\Customizer\Extensions\Login_Form;
use DIFL\Customizer\Extensions\Preloader;
use DIFL\Customizer\Extensions\Back_To_Top;
use DIFL\Customizer\Types\Control;
use DIFL\Customizer\Types\Panel;
use DIFL\Customizer\Types\Section;

/**
 * Main customizer handler.
 */
class Main extends Base_Customizer {

	public static function includes() {
		$main_dir     = DIFL_MAIN_DIR.'/';

		require_once $main_dir . 'customizer/Portability.php';
		$controls_dir = $main_dir . 'customizer/controls/';

		if ( ! class_exists( 'WP_Customize_Control' ) ) {
			include_once ABSPATH . 'wp-includes/class-wp-customize-control.php';
		}

		require_once $main_dir . 'customizer/sanitize-functions.php';

		// React controls
		require_once $controls_dir . 'react/radio_buttons.php';
		require_once $controls_dir . 'react/spacing.php';
		require_once $controls_dir . 'react/range.php';
		require_once $controls_dir . 'react/responsive_range.php';
		require_once $controls_dir . 'react/font_family.php';
		require_once $controls_dir . 'react/typography.php';
		require_once $controls_dir . 'react/color.php';

		// General controls
		require_once $controls_dir . 'radio_image.php';
		require_once $controls_dir . 'range.php';
		require_once $controls_dir . 'responsive_number.php';
		require_once $controls_dir . 'tabs.php';
		require_once $controls_dir . 'heading.php';
		require_once $controls_dir . 'checkbox.php';
		require_once $controls_dir . 'divi_icon.php';
		require_once $controls_dir . 'text_preloader.php';
		require_once $controls_dir . 'preloader.php';
		require_once $controls_dir . 'divi_select.php';


		// Load Types
		require_once $main_dir . 'customizer/types/control.php';
		require_once $main_dir . 'customizer/types/panel.php';
		require_once $main_dir . 'customizer/types/section.php';
		require_once $main_dir . 'customizer/types/partial.php';
	}

	/**
	 * Add controls.
	 */
	public function add_controls() {
		$this->load_assets();
		$this->register_types();
		$this->add_main_panels();
		$this->change_controls();
		$this->load_extensions();
	}

	/**
	 * Register customizer controls type.
	 */
	private function register_types() {
		$this->register_type( 'DIFL\Customizer\Controls\React\Radio_Buttons', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\React\Spacing', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\React\Color', 'control' );


		$this->register_type( 'DIFL\Customizer\Controls\Radio_Image', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\Range', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\Responsive_Number', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\Tabs', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\Heading', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\Checkbox', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\Preloader', 'control' );
		$this->register_type( 'DIFL\Customizer\Controls\Text_Preloader', 'control' );
	}

	/**
	 * Add main panels.
	 */
	private function add_main_panels() {
		$panels = [
			'difl_advanced_genaral' => [
				'priority' => 1,
				'title'    => __( 'Advanced General Settings', 'divi_flash' ),
			],
		];

		foreach ( $panels as $panel_id => $panel ) {
			$this->add_panel(
				new Panel(
					$panel_id,
					[
						'priority' => $panel['priority'],
						'title'    => $panel['title'],
					]
				)
			);
		}

	}

	public function load_extensions() {
		require_once DIFL_MAIN_DIR . '/customizer/extensions/Back_To_Top.php';
		require_once DIFL_MAIN_DIR . '/customizer/extensions/Preloader.php';
		require_once DIFL_MAIN_DIR . '/customizer/extensions/Login_Form.php';

		$stp = new Back_To_Top();
		$stp->init();

		$preloader = new Preloader();
		$preloader->init();

		$login_form = new Login_Form();
		$login_form->init();
	}

	public function load_assets() {
		require_once DIFL_MAIN_DIR . '/customizer/Assets.php';
	}
}

Main::includes();
$main = new Main();
$main->init();
$main->add_controls();