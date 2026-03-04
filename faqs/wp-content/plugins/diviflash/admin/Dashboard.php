<?php

namespace DIFL;

use DIFL\Feature\Contact_Form;
use DIFL\Importer\Processor;
use http\Exception;

class Dashboard {
	const PAGE_SLUG = 'diviflash';

	/**
	 * @var array
	 */
	public $response = [
		'success' => true,
		'message' => '',
	];

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_difl_settings_update', [ $this, 'handle_settings_update' ] );
		add_action( 'wp_ajax_difl_modules_update', [ $this, 'handle_modules_update' ] );
		add_action( 'wp_ajax_difl_get_options', [ $this, 'get_localize_vars' ] );
		add_filter( 'plugin_action_links_' . self::plugin_basename(), [ $this, 'add_action_links' ] );
		add_action( 'wp_ajax_df_export_dashboard_settings', [ $this, 'export_settings' ] );
		add_action( 'wp_ajax_df_import_dashboard_settings', [ $this, 'import_settings' ] );
		add_action( 'admin_head-toplevel_page_diviflash', [ $this, 'remove_notices' ] );
		add_action( 'admin_notices', [ $this, 'license_notices' ], PHP_INT_MAX );
	}

	public function send_ajax_response() {
		if ( $this->response['success'] ) {
			wp_send_json_success( $this->response );
		}
		wp_send_json_error( $this->response );
	}

	public function handle_modules_update() {
		$post_data = self::get_paylod_data();
		$updated   = update_option( 'df_active_modules', $post_data['settings'] );
		if ( $updated ) {
			$this->response['message'] = __( 'Activated modules settings updated', 'divi_flash' );
			$this->send_ajax_response();
		}
	}

	public function handle_settings_update() {
		$keys      = self::get_setting_keys();
		$post_data = self::get_paylod_data( 'settings' );

		foreach ( $post_data as $data ) {
			if ( ! in_array( $data->id, $keys ) ) {
				continue;
			}
			update_option( $data->id, $data->value );
			// Handle rewrite rules on popup enable disable
			if ( 'df_general_popup_enable' === $data->id ) {
				if ( $data->value ) {
					\DF_Popup_Extension_Init::create_popup_post_type();
				} else {
					unregister_post_type( 'difl_popup' );
				}
				flush_rewrite_rules();
				set_transient( 'difl_flush_rewrite_rules', 'true' );
			}
			if ( 'df_hide_project_cpt' === $data->id || 'df_project_cpt_rename_option' === $data->id ) {
				if ( ! $data->value ) {
					if ( function_exists( 'et_pb_register_posttypes' ) ) {
						\et_pb_register_posttypes();
					}
				} else {
					unregister_post_type( 'project' );
				}

				set_transient( 'difl_flush_rewrite_rules', 'true', 5 * 60 );
			}
		}

		$this->response['message'] = __( 'Settings value updated', 'divi_flash' );
		$this->send_ajax_response();
	}

	public static function get_paylod_data( $decode_key = '' ) {
		if ( ! check_ajax_referer( 'difl_dashboard' ) ) {
			return false;
		}
		$post_data = array_map( 'wp_unslash', $_POST );

		if ( empty( $decode_key ) ) {
			return $post_data;
		}

		return json_decode( $post_data[ $decode_key ] );
	}

	public function register_admin_menu() {
		add_menu_page(
			__( 'DiviFlash Dashboard', 'divi_flash' ),
			__( 'DiviFlash', 'divi_flash' ),
			'manage_options',
			'diviflash',
			[ $this, 'render_admin_menu' ],
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
<path d="M2 4H12.9458C16.9862 4 23.2316 7.76329 23.4632 15.0418C23.4632 15.0418 17.4493 10.169 16.4933 9.4902C15.5139 8.772 14.3674 8.31557 13.1623 8.16411H2V4Z" fill="#661FF4"/>
<path d="M2 13.0984V26.4222H12.456C15.0319 26.3797 17.5141 25.449 19.483 23.7875C21.4518 22.1259 22.7865 19.8355 23.2615 17.3034L19.3959 15.4529C19.3594 17.2219 18.6501 18.9105 17.4125 20.1749C16.1748 21.4393 14.5018 22.1846 12.734 22.2589H6.10271V13.0984H2Z" fill="#661FF4"/>
<g opacity="0.2">
<path d="M6.10189 16.8002V13.0992H2V14.7339L6.10189 16.8002Z" fill="#661FF4"/>
<path d="M22.7387 19.0759C22.9505 18.497 23.1255 17.9052 23.2624 17.3041L19.3967 15.4536C19.3706 16.0923 19.2618 16.7249 19.073 17.3356L22.7387 19.0759Z" fill="#661FF4"/>
</g>
<path d="M2 10.5289H6.10189L12.3937 15.2371V10.5289H15.8074L28.2989 20.5832L16.0444 14.6205V20.684L2.00077 13.5181L2 10.5289Z" fill="#661FF4"/>
</svg>' ), 100
		);
        if ( get_option( 'df_general_cf_support' ) ) {
			$slug = Contact_Form::SLUG;
			add_submenu_page( 'diviflash', __( 'Form Submissions', 'divi_flash' ), __( 'Form Submissions', 'divi_flash' ), 'manage_options', "edit.php?post_type={$slug}" );
        }
	}

	public function render_admin_menu() {
		echo '<div id="diviflash-admin"></div>';
	}

	public function enqueue_scripts( $hook_suffix ) {
		if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		$plugin_path  = plugin_dir_path( __DIR__ );
		$plugin_url   = plugin_dir_url( __DIR__ );
		$path         = 'admin/dashboard/assets/';
		$handle       = 'difl-dashboard-admin';
		$dependencies = include_once $plugin_path . $path . 'index.asset.php';
		wp_enqueue_style(
			$handle,
			$plugin_url . $path . 'index.css',
			[ 'wp-components' ],
			$dependencies['version'] );


		wp_enqueue_script(
			$handle,
			$plugin_url . $path . 'index.js',
			$dependencies['dependencies'],
			$dependencies['version'], true );

		wp_enqueue_media();

		wp_localize_script( $handle, 'diflSettings', $this->get_localize_vars( true ) );
	}

	public static function get_module_list() {
		return [
			[
				'parent'            => 'AdvancedBlurb',
				'parent_name'       => 'Advanced Blurb',
				'icon'              => 'advanced-blurb',
				'doc_link'          => 'https://diviflash.com/docs/advanced-blurb/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-blurb/',
				'category'          => 'Essentials',
				'release_version'   => '1.0.1',
				'is_default_active' => true,
			],
			[
				'parent'            => 'AdvancedDataTable',
				'parent_name'       => 'Data Table',
				'icon'              => 'advanceddatatable',
				'doc_link'          => 'https://diviflash.com/docs/advanced-divi-table/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-data-table/',
				'category'          => 'Dynamic',
				'release_version'   => '1.0.6',
				'is_default_active' => true,
			],
			[
				'parent'            => 'AdvancedPerson',
				'parent_name'       => 'Advanced Person',
				'icon'              => 'advanced-person',
				'doc_link'          => 'https://diviflash.com/docs/advanced-person/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-person/',
				'category'          => 'Essentials',
				'release_version'   => '1.0.7',
				'is_default_active' => true,
			],
			[
				'parent'            => 'AdvancedTab',
				'parent_name'       => 'Advanced Tabs',
				'child'             => 'AdvancedTabItem',
				'child_name'        => 'Advanced Tab Item',
				'icon'              => 'advanced-tabs',
				'doc_link'          => 'https://diviflash.com/docs/advanced-tabs/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-tabs/',
				'category'          => 'Essentials',
				'release_version'   => '1.0.2',
				'is_default_active' => true,
			],
			[
				'parent'            => 'ImageGallery',
				'parent_name'       => 'Advanced Gallery',
				'child'             => 'ImageGalleryItem',
				'child_name'        => 'Advanced Gallery Item',
				'icon'              => 'image-gallery',
				'doc_link'          => 'https://diviflash.com/docs/advanced-image-gallery/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-image-gallery/',
				'category'          => 'Image',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'Heading',
				'parent_name'       => 'Advanced Heading',
				'icon'              => 'advanced-heading',
				'doc_link'          => 'https://diviflash.com/docs/advanced-heading/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-heading/',
				'category'          => 'Essentials',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'TextHighlighter',
				'parent_name'       => 'Text Highlighter',
				'icon'              => 'text-highlighter',
				'doc_link'          => 'https://diviflash.com/docs/text-highlighter/',
				'demo_link'         => 'https://modules.diviflash.xyz/text-highlighter/',
				'category'          => 'Creative',
				'release_version'   => '1.4.4',
				'is_default_active' => false,
			],
			[
				'parent'            => 'Heading_Anim',
				'parent_name'       => 'Animated Heading',
				'icon'              => 'animated-heading',
				'doc_link'          => 'https://diviflash.com/docs/animated-heading/',
				'demo_link'         => 'https://modules.diviflash.xyz/animated-heading/',
				'category'          => 'Creative',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'BlogCarousel',
				'parent_name'       => 'Post Carousel',
				'child'             => 'PostItem',
				'child_name'        => 'Post Item',
				'icon'              => 'blogcarousel',
				'doc_link'          => 'https://diviflash.com/docs/divi-blog-carousel/',
				'demo_link'         => 'https://modules.diviflash.xyz/blog-carousel/',
				'category'          => 'Carousel',
				'release_version'   => '1.0.4',
				'is_default_active' => true,
			],
			[
				'parent'            => 'PostGrid',
				'parent_name'       => 'Post Grid',
				'child'             => 'PostItem',
				'child_name'        => 'Post Item',
				'icon'              => 'postgrid',
				'doc_link'          => 'https://diviflash.com/docs/divi-blog-grid/',
				'demo_link'         => 'https://modules.diviflash.xyz/blog-grid/',
				'category'          => 'Dynamic',
				'release_version'   => '1.0.4',
				'is_default_active' => true,
			],
			[
				'parent'            => 'PostList',
				'parent_name'       => 'Post List',
				'child'             => 'PostListItem',
				'child_name'        => 'Post Item',
				'doc_link'          => 'https://diviflash.com/docs/post-list/',
				'demo_link'         => 'https://modules.diviflash.xyz/post-list/',
				'category'          => 'Dynamic',
				'icon'              => 'postlist',
				'release_version'   => '1.0.4',
				'is_default_active' => false,
			],
			[
				'parent'            => 'BusinessHours',
				'parent_name'       => 'Business Hours',
				'child'             => 'BusinessHoursItem',
				'child_name'        => 'Business Hours Item',
				'icon'              => 'business-hours',
				'doc_link'          => 'https://diviflash.com/docs/business-hours/',
				'demo_link'         => 'https://modules.diviflash.xyz/business-hours/',
				'category'          => 'SEO',
				'release_version'   => '1.0.2',
				'is_default_active' => true,
			],
			[
				'parent'            => 'CompareImage',
				'parent_name'       => 'Before After Slider',
				'icon'              => 'image-compare',
				'doc_link'          => 'https://diviflash.com/docs/before-after-image/',
				'demo_link'         => 'https://modules.diviflash.xyz/before-after-image/',
				'category'          => 'Image',
				'release_version'   => '1.0.4',
				'is_default_active' => true,
			],
			[
				'parent'            => 'CFSeven',
				'parent_name'       => 'Contact Form 7 Styler',
				'icon'              => 'contact-form-7',
				'doc_link'          => 'https://diviflash.com/docs/contact-form-7/',
				'demo_link'         => 'https://modules.diviflash.xyz/contact-form-7/',
				'category'          => 'FormStyler',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'ContentCarousel',
				'parent_name'       => 'Advanced Carousel',
				'child'             => 'ContentCarouselItem',
				'child_name'        => 'Advanced Carousel Item',
				'icon'              => 'content-carousel',
				'doc_link'          => 'https://diviflash.com/docs/content-carousel/',
				'demo_link'         => 'https://modules.diviflash.xyz/divi-content-carousel/',
				'category'          => 'Carousel',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'DataTable',
				'parent_name'       => 'Table',
				'child'             => 'DataTableItem',
				'child_name'        => 'Table Row',
				'icon'              => 'datatable',
				'doc_link'          => 'https://diviflash.com/docs/table-module/',
				'demo_link'         => 'https://modules.diviflash.xyz/data-table/',
				'category'          => 'Essentials',
				'release_version'   => '1.0.6',
				'is_default_active' => true,
			],
			[
				'parent'            => 'DualButton',
				'parent_name'       => 'Dual Button',
				'icon'              => 'dual-button',
				'doc_link'          => 'https://diviflash.com/docs/dual-button/',
				'demo_link'         => 'https://modules.diviflash.xyz/dual-button/',
				'category'          => 'Essentials',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'FlipBox',
				'parent_name'       => 'Flip Box',
				'icon'              => 'flip',
				'doc_link'          => 'https://diviflash.com/docs/divi-flip-box/',
				'demo_link'         => 'https://modules.diviflash.xyz/divi-flip-box/',
				'category'          => 'Creative',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'FloatImage',
				'parent_name'       => 'Floating Images',
				'child'             => 'FloatImageItem',
				'child_name'        => 'Float Image Item',
				'icon'              => 'float-image',
				'doc_link'          => 'https://diviflash.com/docs/floating-multi-image/',
				'demo_link'         => 'https://modules.diviflash.xyz/floating-multi-image/',
				'category'          => 'Image',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'HoverBox',
				'parent_name'       => 'Hover Box',
				'icon'              => 'hover-box',
				'doc_link'          => 'https://diviflash.com/docs/hover-box/',
				'demo_link'         => 'https://modules.diviflash.xyz/hover-box/',
				'category'          => 'Creative',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'ImageAccordion',
				'parent_name'       => 'Image Accordion',
				'child'             => 'ImageAccordionItem',
				'child_name'        => 'Image Accordion Item',
				'icon'              => 'image-accordion',
				'doc_link'          => 'https://diviflash.com/docs/image-accordion/',
				'demo_link'         => 'https://modules.diviflash.xyz/image-accordion/',
				'category'          => 'Image',
				'release_version'   => '1.0.4',
				'is_default_active' => true,
			],
			[
				'parent'            => 'ImageCarousel',
				'parent_name'       => 'Image Carousel',
				'child'             => 'ImageCarouselItem',
				'child_name'        => 'Image Carousel Item',
				'icon'              => 'image-carousel',
				'doc_link'          => 'https://diviflash.com/docs/image-carousel/',
				'demo_link'         => 'https://modules.diviflash.xyz/image-carousel/',
				'category'          => 'Carousel',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'ImageHover',
				'parent_name'       => 'Image Hover',
				'icon'              => 'image-hover-box',
				'doc_link'          => 'https://diviflash.com/docs/image-hover-box/',
				'demo_link'         => 'https://modules.diviflash.xyz/image-hover-box/',
				'category'          => 'Image',
				'release_version'   => '1.0.1',
				'is_default_active' => true,
			],
			[
				'parent'            => 'ImageMask',
				'parent_name'       => 'Image Mask',
				'icon'              => 'image-masking',
				'doc_link'          => 'https://diviflash.com/docs/image-masking/',
				'demo_link'         => 'https://modules.diviflash.xyz/divi-image-masking/',
				'category'          => 'Image',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'ImageReveal',
				'parent_name'       => 'Image Reveal',
				'icon'              => 'image-reveal',
				'category'          => 'Image',
				'doc_link'          => 'https://diviflash.com/docs/image-reveal/',
				'demo_link'         => 'https://modules.diviflash.xyz/image-reveal/',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'MarqueeText',
				'parent_name'       => 'Marquee Text',
				'child'             => 'MarqueeTextItem',
				'child_name'        => 'Marquee Text Item',
				'icon'              => 'marquee-text',
				'category'          => 'Creative',
				'doc_link'          => 'https://diviflash.com/docs/marquee-text/',
				'demo_link'         => 'https://modules.diviflash.xyz/marquee-text/',
				'release_version'   => '1.3.40',
				'is_default_active' => true,
			],
			[
				'parent'            => 'InstagramCarousel',
				'parent_name'       => 'Instagram Feed Carousel',
				'icon'              => 'instagram-carousel',
				'doc_link'          => 'https://diviflash.com/docs/instagram-carousel/',
				'demo_link'         => 'https://modules.diviflash.xyz/instagram-carousel/',
				'category'          => 'Social',
				'release_version'   => '1.0.2',
				'is_default_active' => true,
			],
			[
				'parent'            => 'InstagramGallery',
				'parent_name'       => 'Instagram Feed',
				'icon'              => 'instagram-gallery',
				'doc_link'          => 'https://diviflash.com/docs/instagram-gallery/',
				'demo_link'         => 'https://modules.diviflash.xyz/instagram-gallery/',
				'category'          => 'Social',
				'release_version'   => '1.0.2',
				'is_default_active' => true,
			],
			[
				'parent'            => 'JustifiedGallery',
				'parent_name'       => 'Justified Gallery',
				'icon'              => 'justified-gallery',
				'doc_link'          => 'https://diviflash.com/docs/justified-image-gallery/',
				'demo_link'         => 'https://modules.diviflash.xyz/justifed-image-gallery/',
				'category'          => 'Image',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'LogoCarousel',
				'parent_name'       => 'Logo Carousel',
				'child'             => 'LogoCarouselItem',
				'child_name'        => 'Logo Carousel Item',
				'icon'              => 'logo-carousel',
				'doc_link'          => 'https://diviflash.com/docs/logo-carousel/',
				'demo_link'         => 'https://modules.diviflash.xyz/logo-carousel/',
				'category'          => 'Carousel',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'PackeryGallery',
				'parent_name'       => 'Packery Gallery',
				'icon'              => 'packery',
				'doc_link'          => 'https://diviflash.com/docs/packery-image-gallery/',
				'demo_link'         => 'https://modules.diviflash.xyz/packary-image-gallery/',
				'category'          => 'Image',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'TestimonialCarousel',
				'parent_name'       => 'Testimonial Carousel',
				'child'             => 'TestimonialCarouselItem',
				'child_name'        => 'Testimonial Carousel Item',
				'icon'              => 'test-carousel',
				'doc_link'          => 'https://diviflash.com/docs/testimonial-carousel/',
				'demo_link'         => 'https://modules.diviflash.xyz/testimonial-carousel/',
				'category'          => 'Carousel',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'TiltCard',
				'parent_name'       => 'Tilt Card',
				'icon'              => 'titlt-box',
				'doc_link'          => 'https://diviflash.com/docs/tilt-card/',
				'demo_link'         => 'https://modules.diviflash.xyz/tilt-card/',
				'category'          => 'Creative',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'TypewriterText',
				'parent_name'       => 'Typing Text',
				'icon'              => 'typewriter',
				'doc_link'          => 'https://diviflash.com/docs/typewriter-text/',
				'demo_link'         => 'https://modules.diviflash.xyz/typewriter-text/',
				'category'          => 'Creative',
				'release_version'   => '1.1.8',
				'is_default_active' => false,
			],
			[
				'parent'            => 'WPForms',
				'parent_name'       => 'WPForms Styler',
				'icon'              => 'wp-form',
				'doc_link'          => 'https://diviflash.com/docs/wp-forms/',
				'demo_link'         => 'https://modules.diviflash.xyz/wpforms/',
				'category'          => 'FormStyler',
				'release_version'   => '1.0.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'GravityForm',
				'parent_name'       => 'Gravity Forms Styler',
				'icon'              => 'gravityform',
				'doc_link'          => 'https://diviflash.com/docs/gravity-form-styler/',
				'demo_link'         => 'https://modules.diviflash.xyz/gravity-form-styler/',
				'category'          => 'FormStyler',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
			],
			[
				'parent'            => 'CptGrid',
				'parent_name'       => 'CPT Grid',
				'child'             => 'CptItem',
				'child_name'        => 'CPT Item',
				'icon'              => 'postgrid',
				'doc_link'          => 'https://diviflash.com/docs/custom-post-types-grid/',
				'demo_link'         => 'https://modules.diviflash.xyz/custom-post-types-grid/',
				'category'          => 'Dynamic',
				'release_version'   => '1.1.0',
				'is_default_active' => true,
			],
			[
				'parent'            => 'ProductGrid',
				'parent_name'       => 'Product Grid',
				'child'             => 'ProductItem',
				'child_name'        => 'Product Item',
				'icon'              => 'product-grid',
				'doc_link'          => 'https://diviflash.com/docs/product-grid/',
				'demo_link'         => 'https://modules.diviflash.xyz/product-grid/',
				'category'          => 'WooCommerce',
				'release_version'   => '1.1.2',
				'is_default_active' => false,
			],
			[
				'parent'            => 'ProductCarousel',
				'parent_name'       => 'Product Carousel',
				'child'             => 'ProductItem',
				'child_name'        => 'Product Item',
				'icon'              => 'product-carousel',
				'doc_link'          => 'https://diviflash.com/docs/product-carousel/',
				'demo_link'         => 'https://modules.diviflash.xyz/product-carousel/',
				'category'          => 'WooCommerce',
				'release_version'   => '1.1.5',
				'is_default_active' => false,
			],
			[
				'parent'            => 'ImageHotspot',
				'parent_name'       => 'Image Hotspot',
				'child'             => 'ImageHotspotItem',
				'child_name'        => 'Image Hotspot Item',
				'icon'              => 'image-hotspot',
				'doc_link'          => 'https://diviflash.com/docs/image-hotspot/',
				'demo_link'         => 'https://modules.diviflash.xyz/image-hotspot/',
				'category'          => 'Image',
				'is_default_active' => false,
				'release_version'   => '1.1.8',
			],
			[
				'parent'            => 'CptFilter',
				'parent_name'       => 'Filterable CPT',
				'child'             => 'CptItem',
				'child_name'        => 'CPT Item',
				'icon'              => 'cpt-filter',
				'doc_link'          => 'https://diviflash.com/docs/filterable-cpt/',
				'demo_link'         => 'https://modules.diviflash.xyz/filterable-cpt/',
				'category'          => 'Dynamic',
				'is_default_active' => false,
				'release_version'   => '1.1.8',
			],
			[
				'parent'            => 'LottieImage',
				'parent_name'       => 'Lottie',
				'icon'              => 'lottie-image',
				'doc_link'          => 'https://diviflash.com/docs/lottie/',
				'demo_link'         => 'https://modules.diviflash.xyz/lottie/',
				'category'          => 'Creative',
				'release_version'   => '1.2.1',
				'is_default_active' => false,
			],
			[
				'parent'            => 'CptCarousel',
				'parent_name'       => 'CPT Carousel',
				'child'             => 'CptItem',
				'child_name'        => 'CPT Item',
				'icon'              => 'cpt-carousel',
				'doc_link'          => 'https://diviflash.com/docs/cpt-carousel/',
				'demo_link'         => 'https://modules.diviflash.xyz/cpt-carousel/',
				'category'          => 'Carousel',
				'release_version'   => '1.2.1',
				'is_default_active' => false,
			],
			[
				'parent'            => 'ContentSwitcher',
				'parent_name'       => 'Content Toggle',
				'icon'              => 'content-toggle',
				'doc_link'          => 'https://diviflash.com/docs/content-toggle/',
				'demo_link'         => 'https://modules.diviflash.xyz/content-toggle/',
				'category'          => 'Essentials',
				'release_version'   => '1.2.1',
				'is_default_active' => false,
			],
			[
				'parent'            => 'ScrollImage',
				'parent_name'       => 'Scroll Image',
				'icon'              => 'scroll-image',
				'doc_link'          => 'https://diviflash.com/docs/scroll-image/',
				'demo_link'         => 'https://modules.diviflash.xyz/scroll-image/',
				'category'          => 'Image',
				'release_version'   => '1.2.3',
				'is_default_active' => false,
			],
			[
				'parent'            => 'Divider',
				'parent_name'       => 'Advanced Divider',
				'icon'              => 'divider',
				'doc_link'          => 'https://diviflash.com/docs/advanced-divider/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-divider/',
				'category'          => 'Essentials',
				'release_version'   => '1.2.3',
				'is_default_active' => false,
			],
			[
				'parent'            => 'IconList',
				'parent_name'       => 'Advanced List',
				'child'             => 'IconListItem',
				'child_name'        => 'List Item',
				'icon'              => 'icon-list',
				'doc_link'          => 'https://diviflash.com/docs/advanced-list/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-list/',
				'category'          => 'Essentials',
				'release_version'   => '1.2.4',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'Breadcrumbs',
				'parent_name'       => 'Breadcrumbs',
				'icon'              => 'breadcrumbs',
				'doc_link'          => 'https://diviflash.com/docs/breadcrumbs/',
				'demo_link'         => 'https://modules.diviflash.xyz/modules/breadcrumbs/',
				'category'          => 'SEO',
				'release_version'   => '1.2.4',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'RatingBox',
				'parent_name'       => 'Star Rating',
				'icon'              => 'star-rating',
				'doc_link'          => 'https://diviflash.com/docs/star-rating/',
				'demo_link'         => 'https://modules.diviflash.xyz/star-rating/',
				'category'          => 'SEO',
				'release_version'   => '1.2.8',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'Faq',
				'parent_name'       => 'FAQ',
				'child'             => 'FaqItem',
				'child_name'        => 'Faq Item',
				'icon'              => 'faq',
				'doc_link'          => 'https://diviflash.com/docs/faq/',
				'demo_link'         => 'https://modules.diviflash.xyz/faq/',
				'category'          => 'SEO',
				'release_version'   => '1.2.10',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'AdvancedMenu',
				'parent_name'       => 'Advanced Menu',
				'child'             => 'AdvancedMenuItem',
				'child_name'        => 'Advanced Menu Item',
				'icon'              => 'advanced-menu',
				'doc_link'          => 'https://diviflash.com/docs/advanced-menu/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-menu/',
				'category'          => 'Essentials',
				'release_version'   => '1.3.0',
				'is_default_active' => true,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'Timeline',
				'parent_name'       => 'Timeline',
				'child'             => 'TimelineItem',
				'child_name'        => 'Timeline Item',
				'doc_link'          => 'https://diviflash.com/docs/timeline/',
				'demo_link'         => 'https://modules.diviflash.xyz/timeline/',
				'category'          => 'Creative',
				'icon'              => 'timeline',
				'release_version'   => '1.3.6',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'ACFGallery',
				'parent_name'       => 'ACF Gallery',
				'doc_link'          => 'https://diviflash.com/docs/acf-gallery/',
				'demo_link'         => 'https://modules.diviflash.xyz/acf-gallery/',
				'category'          => 'Dynamic',
				'icon'              => 'acf-gallery',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'AvatarStack',
				'parent_name'       => 'Stack',
				'child'             => 'AvatarStackItem',
				'child_name'        => 'Stack Item',
				'doc_link'          => 'https://diviflash.com/docs/stack/',
				'demo_link'         => 'https://modules.diviflash.xyz/stack/',
				'category'          => 'Creative',
				'icon'              => 'stack',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
				'is_new_module'     => true,
			],

            [
	            'parent'            => 'SVGAnimator',
	            'parent_name'       => 'SVG Animator',
	            'doc_link'          => 'https://diviflash.com/docs/svg-animator/',
	            'demo_link'         => 'https://modules.diviflash.xyz/svg-animator/',
	            'category'          => 'Creative',
	            'icon'              => 'svg-animator',
	            'release_version'   => '1.0.0',
	            'is_default_active' => false,
	            'is_new_module'     => true
        ],
			[
				'parent'            => 'TableOfContents',
				'parent_name'       => 'Table Of Contents',
				'doc_link'          => 'https://diviflash.com/docs/table-of-contents/',
				'demo_link'         => 'https://modules.diviflash.xyz/table-of-contents/',
				'category'          => 'SEO',
				'icon'              => 'toc',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'PricingTable',
				'parent_name'       => 'Advanced Pricing Table',
				'doc_link'          => 'https://diviflash.com/docs/advanced-pricing-table/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-pricing-table/',
				'category'          => 'Marketing',
				'icon'              => 'pricing-table',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'AdvancedButton',
				'parent_name'       => 'Advanced Button',
				'doc_link'          => 'https://diviflash.com/docs/advanced-button/',
				'demo_link'         => 'https://modules.diviflash.xyz/advanced-button/',
				'category'          => 'Marketing',
				'icon'              => 'advanced-button',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'      => 'InlineContents',
				'parent_name' => 'Inline Content',
				'child'       => 'InlineContentsItem',
				'child_name'  => 'Inline Content Item',
				'doc_link'    => 'https://diviflash.com/docs/inline-content/',
				'demo_link'   => 'https://modules.diviflash.xyz/inline-content/',
				'category'    => 'Creative',
				'icon'        => 'inline-contents',
			],
			[
				'parent'            => 'SocialShare',
				'parent_name'       => 'Social Share',
				'child'             => 'SocialShareItem',
				'child_name'        => 'Social Share Item',
				'doc_link'          => 'https://diviflash.com/docs/social-share/',
				'demo_link'         => 'https://modules.diviflash.xyz/social-share/',
				'category'          => 'Social',
				'icon'              => 'social-share',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'TextReveal',
				'parent_name'       => 'Scroll Text Reveal',
				'icon'              => 'scroll-text-reveal',
				'doc_link'          => 'https://diviflash.com/docs/scroll-text-reveal/',
				'demo_link'         => 'https://modules.diviflash.xyz/scroll-text-reveal/',
				'category'          => 'Creative',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
			[
				'parent'            => 'VerticalMenu',
				'parent_name'       => 'Vertical Menu',
				'doc_link'          => 'https://diviflash.com/docs/vertical-menu/',
				'demo_link'         => 'https://modules.diviflash.xyz/vertical-menu/',
				'category'          => 'Essentials',
				'icon'              => 'vertical-menu',
				'release_version'   => '1.0.0',
				'is_default_active' => false,
				'is_new_module'     => true,
			],
		];
	}

	public static function get_setting_keys() {
		return apply_filters( 'difl_setting_keys', [
			'df_general_cf_support',
			'df_general_svg_support',
			'df_general_hide_wordpress_version',
			'df_general_json_support',
			'df_general_library_shortcode',
			'df_general_acf_field_support',
			'df_general_pod_field_support',
			'df_general_metabox_field_support',
			'df_general_popup_enable',
			'df_menu_bottom_line',
			'df_menu_bottom_line_color',
			'df_menu_bottom_line_height',
			'df_menu_bottom_line_distance',
			'df_menu_bottom_line_distance_fixed',
			'df_menu_line_width',
			'df_menu_line_animation',
			'df_menu_hide_bottom_border',
			'df_menu_item_distance',
			'df_hide_admin_bar',
			'df_hide_admin_bar_roles',
			'df_project_cpt_rename',
			'df_project_cpt_rename_option',
			'df_hide_media_category',
			'df_hide_page_category',
			'df_hide_project_cpt',
			'df_disable_wpforms_pro_style',
		] );
	}

	protected static function get_settings() {
		$settings = [];
        $setting_keys = self::get_setting_keys();
		foreach ( $setting_keys as $setting_key ) {
			$settings[ $setting_key ] = get_option( $setting_key );
		}

		return $settings;
	}

	public static function get_directive_list() {
		$directives = [
			[
				'key'    => 'upload_max_filesize',
				'value'  => '256M',
				'passed' => true,
			],
			[
				'key'    => 'max_input_time',
				'value'  => '300',
				'passed' => true,
			],
			[
				'key'    => 'memory_limit',
				'value'  => '256M',
				'passed' => true,
			],
			[
				'key'    => 'max_execution_time',
				'value'  => '300',
				'passed' => true,
			],
			[
				'key'    => 'post_max_size',
				'value'  => '512M',
				'passed' => true,
			],
		];
		$time       = [ 'max_input_time', 'max_execution_time' ];
		array_walk( $directives, function ( &$value ) use ( $time ) {
			$directive        = $value['key'];
			$ini_get          = in_array( $directive, $time ) ? ini_get( $directive ) : self::ini_get_mb( $directive );
			$value['current'] = $ini_get;
			$current_value    = intval( str_replace( 'M', '', $value['value'] ) );
			if ( '-1' === $ini_get ) {
				$value['passed'] = true;

				return;
			}
			if ( $ini_get < $current_value ) {
				$value['passed'] = false;
			}
		}, $directives );

		return $directives;
	}

	public static function plugin_basename() {
		return plugin_basename( plugin_dir_path( __DIR__ ) . 'diviflash.php' );
	}

	public function add_action_links( $actions ) {
		$dashboard_link = esc_url( 'admin.php?page=' . self::PAGE_SLUG );
		$settings       = [ '<a href="' . admin_url( $dashboard_link ) . '"> ' . __( 'Settings', 'divi_flash' ) . ' </a>', ];
		$actions        = array_merge( $actions, $settings );

		return $actions;
	}

	public function get_localize_vars( $is_array = '' ) {
		$activeModules = json_decode( get_option( 'df_active_modules' ) );
		$plugin_url    = plugin_dir_url( __DIR__ );

		$vars = apply_filters( 'difl_dashboard_local_vars', [
			'ajaxUrl'          => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'nonce'            => wp_create_nonce( 'difl_dashboard' ),
			'static'           => $plugin_url . 'admin/dashboard/static/',
			'actions'          => [
				'layout'   => 'difl_layout_import',
				'modules'  => 'difl_modules_update',
				'settings' => 'difl_settings_update',
			],
			'modules'          => self::get_module_list(),
			'active_modules'   => $activeModules,
			'layouts'          => RemoteData::get_file_content( 'layouts' ),
			'settings'         => self::get_settings(),
			'server_directive' => self::get_directive_list(),
			'version'          => DIFL_VERSION,
			'changelog'        => RemoteData::get_file_content( 'changelog' ),
			'update_uri'       => get_plugin_data( DIFL_MAIN_DIR . '/diviflash.php' )['UpdateURI'],
			'roles'            => wp_roles()->get_names(),
            'login_url'        => wp_login_url(),
		] );
		if ( $is_array ) {
			return $vars;
		}
		wp_send_json( $vars );
	}

	private static function ini_get_mb( $directive ) {
		$value = ini_get( $directive );

		if ( ! $value ) {
			return false;
		}

		preg_match( '/(\d+)/', $value, $matches );
		$numericValue = (int) $matches[0];

		preg_match( '/[A-Za-z]+/', $value, $matches );
		$unit = strtoupper( $matches[0] );

		switch ( $unit ) {
			case 'G':
				return $numericValue * 1024;
			case 'M':
				return $numericValue;
			case 'K':
				return $numericValue / 1024;
			default:
				return false;
		}
	}

	public function export_settings() {
		$settings_key    = self::get_setting_keys();
		$settings_object = [];

		$settings_object['context'] = 'df_settings';

		foreach ( $settings_key as $key ) {
			$settings_object[ $key ] = get_option( $key );
		}

		$settings_object['df_active_modules'] = json_decode( get_option( 'df_active_modules' ) );
		wp_send_json_success( wp_json_encode( $settings_object ) );
	}

	public function import_settings() {
		$settings_key = self::get_setting_keys();
		array_push( $settings_key, 'df_active_modules' );
		$settings = self::get_paylod_data( 'settings' );

		foreach ( $settings as $key => $value ) {
			if ( in_array( $key, $settings_key ) ) {
				if ( is_array( $value ) ) {
					update_option( $key, wp_json_encode( array_values( $value ) ) );
					continue;
				}
				update_option( $key, $value );
			}
		}

		wp_send_json_success( 'success' );
	}

	public function remove_notices() {
		$notices = [
			'network_admin_notices',
			'user_admin_notices',
			'admin_notices',
			'all_admin_notices',
		];
		foreach ( $notices as $notice ) {
			remove_all_actions( $notice );
		}
		add_action( 'admin_notices', [ $this, 'license_notices' ] );
	}

	public function license_notices( $class = '' ) {
		$plugin_meta = get_plugin_data( DIFL_MAIN_DIR . '/diviflash.php' );
		if ( ! array_key_exists( 'UpdateURI', $plugin_meta ) || 'https://www.diviflash.com' !== $plugin_meta['UpdateURI'] ) {
			return;
		}

		$is_active = get_option( \DF_License::KEY );
		$status    = get_option( \DF_License::STATUS_KEY );
		if ( $is_active && 'valid' === $status ) {
			return;
		}
		$activate_url = admin_url() . 'admin.php?page=diviflash&tab=settings&ref=license';
		$purchase_url = 'https://diviflash.com/pricing/';
		$renew_url    = 'https://diviflash.com/docs/renew/';
		$header       = __( 'Your DiviFlash license key is missing!', 'divi_flash' );
		$link         = sprintf(
			esc_html__( '%1$sActivate Now%2$s to unlock all features and updates. Don’t have a key? %3$sGet yours at Diviflash%4$s', 'divi_flash' ),
			'<a href="' . $activate_url . '">',
			'</a>',
			'<a target="_blank" href="' . $purchase_url . '">',
			'</a>'
		);

		if ( 'expired' === $status ) {
			$header = __( 'Your DiviFlash license key has expired!', 'divi_flash' );
			$link   = sprintf(
				esc_html__( ' Renew now to keep access to all premium features, updates, and priority support. %1$sLearn how to renew%2$s.', 'divi_flash' ),
				'<a target="_blank" href="' . $renew_url . '">',
				'</a>'
			);
		}

		printf( '<div class="notice difl-license-notice %3$s"><div class="notice-wrapper"> <h5 class="notice-header dashicons-warning">%1$s</h5><p class="notice-link">%2$s</p></div></div>', $header, $link, $class ); //phpcs:ignore
		?>
        <script>
			(
				() => {
					window.addEventListener( 'load', () => {
						const licensePage = () => {
							const urlParams = new URLSearchParams( location.search )

							if ( ! urlParams.has( 'ref' ) ) {
								return
							}
							const value = urlParams.get( 'ref' )

							if ( 'license' !== value ) {
								return
							}
							document.querySelector( '#settings' ).click()
						}
						licensePage()
					} )
				}
			)();
        </script>
		<?php
	}
}


require_once DIFL_MAIN_DIR . '/admin/Dashboard.php';
if ( file_exists( DIFL_MAIN_DIR . '/admin/license/DF_License.php' ) ) {
	require_once DIFL_MAIN_DIR . '/admin/license/DF_License.php';
}
require_once DIFL_MAIN_DIR . '/includes/importer/Layout.php';
require_once DIFL_MAIN_DIR . '/includes/importer/Builder.php';
require_once DIFL_MAIN_DIR . '/includes/importer/Portability.php';
require_once DIFL_MAIN_DIR . '/includes/importer/Processor.php';

new Processor();

new Dashboard();
