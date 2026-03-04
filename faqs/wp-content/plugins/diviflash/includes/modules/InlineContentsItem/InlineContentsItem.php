<?php

class InlineContentsItem extends ET_Builder_Module {
	use DF_UTLS;

	public $icon_path;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	function init() {
		$this->name                        = esc_html__( 'Inline Content', 'divi_flash' );
		$this->plural                      = esc_html__( 'Inline Content', 'divi_flash' );
		$this->slug                        = 'difl_inline_contents_item';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'admin_label';
		$this->child_title_fallback_var    = 'content_type';
		$this->settings_text               = esc_html__( 'Inline Content Settings', 'divi_flash' );
		$this->advanced_setting_title_text = esc_html__( 'Inline Content', 'divi_flash' );

		$this->main_css_element = "#difl-inline-contents-container %%order_class%%.difl_inline_contents_item";
	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'content_main'             => esc_html__( 'Content', 'divi_flash' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'content_text'           => esc_html__( 'Text', 'divi_flash' ),
					'content_icon'           => esc_html__( 'Icon', 'divi_flash' ),
					'content_media'          => esc_html__( 'Image', 'divi_flash' ),
				],
			],
		];
	}

	public function get_advanced_fields_config() {
		$advanced_fields                          = [];

		$advanced_fields['height']                = [
			'css' => [
				'main'      => "{$this->main_css_element}",
				'important' => 'all',
			],
		];
		$advanced_fields['max_width']             = [
			'use_module_alignment' => false,
			'css' => [
				'main'             => "{$this->main_css_element}",
				'important'        => 'all',
			],
		];
		$advanced_fields['background']            = [
			'use_background_image'          => true,
			'use_background_color_gradient' => true,
			'use_background_video'          => false,
			'use_background_pattern'        => false,
			'use_background_mask'           => false,
			'css'                           => [
				'main'      => "{$this->main_css_element}",
				'important' => 'all',
			],
		];
		$advanced_fields['margin_padding']        = [
			'css' => [
				'main'      => "{$this->main_css_element}",
				'important' => 'all',
			],
		];
		$advanced_fields['borders']['default']    = [
			'css'      => [
				'main' => [
					'border_radii'  => "{$this->main_css_element}",
					'border_styles' => "{$this->main_css_element}",
				],
			],
			'defaults' => [
				'border_radii'  => 'on|0px|0px|0px|0px',
				'border_styles' => [
					'width' => '0px',
					'color' => '#333333',
					'style' => 'solid'
				]
			],
		];
		$advanced_fields['box_shadow']['default'] = [
			'css' => [
				'main'    => "{$this->main_css_element}",
				'overlay' => 'inset',
			],
		];

		$advanced_fields['fonts']['content_text'] = [
			'label'            => esc_html__( '', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'content_text',
			'hide_text_shadow' => false,
			'hide_text_align' => true,
			'depends_show_if' => 'Text',
			'text_shadow'     => [
				'show_if'  => [
					'content_type' => 'Text',
				],
			],
			'css'             => [
				'main' => "{$this->main_css_element} .difl_inline_content_text, {$this->main_css_element}.difl_inline_content_text",
			],
		];

		$advanced_fields['text']                  = false;
		$advanced_fields['display_conditions']    = false;
		$advanced_fields['position_fields']       = false;
		$advanced_fields['z_index']               = false;
		$advanced_fields['overflow']              = false;
		$advanced_fields['transitions']           = false;
		$advanced_fields['transition_fields']     = false;
		$advanced_fields['scroll_effects']        = false;
		$advanced_fields['sticky']                = false;

		return $advanced_fields;
	}

	public function get_fields() {
		$fields = [];

		$fields['admin_label']     = [
			'label'            => esc_html__( 'Admin Label', 'divi_flash' ),
			'type'             => 'text',
			'option_category'  => 'basic_option',
			'toggle_slug'      => 'admin_label',
			'default_on_front' => ''
		];
		$fields['content_type']    = [
			'label'            => esc_html__( 'Content Type', 'divi_flash' ),
			'type'             => 'select',
			'default'          => 'Text',
			'default_in_front' => 'Text',
			'options'          => [
				'Text'       => esc_html__( 'Text', 'divi_flash' ),
				'Icon'       => esc_html__( 'Icon', 'divi_flash' ),
				'Image'      => esc_html__( 'Image', 'divi_flash' ),
				'Line_Break' => esc_html__( 'Line Break', 'divi_flash' ),
			],
			 'affects' => [
			     'content_text_font',
			     'content_text_text_color',
			     'content_text_line_height',
			     'content_text_font_size',
			     'content_text_all_caps',
			     'content_text_letter_spacing',
			     'content_text_text_shadow',
			 ],
			'tab_slug'         => 'general',
			'toggle_slug'      => 'content_main',
		];
		$fields['content_text']    = [
			'label'           => esc_html__( 'Content Text', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Write your desire text.', 'divi_flash' ),
			'tab_slug'        => 'general',
			'toggle_slug'     => 'content_main',
			'dynamic_content' => 'text',
			'show_if'         => [
				'content_type' => 'Text',
			],
		];
		$fields['content_icon']    = [
			'label'           => esc_html__( 'Content Icon', 'divi_flash' ),
			'description'     => esc_html__( 'Choose an icon to display with your Inline Content.', 'divi_flash' ),
			'type'            => 'select_icon',
			'option_category' => 'basic_option',
			'default'         => '&#xe08a;||divi',
			'class'           => [ 'et-pb-font-icon' ],
			'toggle_slug'     => 'content_main',
			'mobile_options'  => true,
			'hover'           => 'tabs',
			'show_if'         => [
				'content_type' => 'Icon',
			],
		];
		$fields['content_image']   = [
			'label'              => et_builder_i18n( 'Content Image' ),
			'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'divi_flash' ),
			'type'               => 'upload',
			'option_category'    => 'basic_option',
			'upload_button_text' => et_builder_i18n( 'Upload an image' ),
			'choose_text'        => esc_attr__( 'Choose an Image', 'divi_flash' ),
			'update_text'        => esc_attr__( 'Set As Image', 'divi_flash' ),
			'hide_metadata'      => true,
			'toggle_slug'        => 'content_main',
			'dynamic_content'    => 'image',
			'mobile_options'     => true,
			'hover'              => 'tabs',
			'show_if'            => [
				'content_type' => 'Image',
			],
		];

		$fields['icon_color']            = [
			'label'           => esc_html__( 'Icon Color', 'divi_flash' ),
			'type'            => 'color-alpha',
			'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'divi_flash' ),
			'depends_show_if' => 'on',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'content_icon',
			'hover'           => false,
			'mobile_options'  => false,
			'sticky'          => false,
			'show_if'         => [
				'content_type' => 'Icon'
			],
		];
		$fields['icon_size']             = [
			'label'           => esc_html__( 'Icon Size', 'divi_flash' ),
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Icon Size.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'content_icon',
			'validate_unit'   => true,
			'allowed_units'   => [ 'px' ],
			'responsive'      => false,
			'mobile_options'  => true,
			'sticky'          => false,
			'hover'           => false,
			'show_if'         => [
				'content_type' => 'Icon'
			],
		];
		$fields['media_size']             = [
			'label'           => esc_html__( 'Image Size', 'divi_flash' ),
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Image Size.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'content_media',
			'validate_unit'   => true,
			'allowed_units'   => [ 'px' ],
			'responsive'      => false,
			'mobile_options'  => true,
			'sticky'          => false,
			'hover'           => false,
			'show_if'         => [
				'content_type' => 'Image'
			],
		];

		return $fields;
	}

	public function additional_css_styles( $render_slug ) {
		if('Icon' === $this->props['content_type']){
			$this->df_process_color(
				[
					'render_slug' => $render_slug,
					'slug'        => 'icon_color',
					'type'        => 'color',
					'selector'    => "{$this->main_css_element}.difl_inline_content_icon",
					'hover'       => "{$this->main_css_element}.difl_inline_content_icon:hover"
				]
			);
			$this->df_process_range( [
				'render_slug' => $render_slug,
				'slug'        => 'icon_size',
				'type'        => 'font-size',
				'default'     => '',
				'selector'    => "{$this->main_css_element}.difl_inline_content_icon",
				'hover'       => "{$this->main_css_element}.difl_inline_content_icon:hover",
			] );

        }
		if('Image' === $this->props['content_type']){
			if( ! empty($this->props['media_size'] ) && '40px' !== $this->props['media_size'] ){
				$this->df_process_range( [
					'render_slug' => $render_slug,
					'slug'        => 'media_size',
					'type'        => 'width',
					'default'     => '',
					'selector'    => "{$this->main_css_element} .difl_inline_content_image",
					'hover'       => "{$this->main_css_element}:hover .difl_inline_content_image",
				] );
			}

		}
	}

	public function render( $attrs, $content, $render_slug ) {
		$this->additional_css_styles( $render_slug );

		$module_class = $this->module_classname( $render_slug );

		$output = sprintf('%1$s %2$s %3$s %4$s',
			$this->process_text( $this->props, $module_class ),
			$this->process_icon( $this->props, $render_slug, $module_class ),
			$this->process_image( $this->props, $module_class ),
			$this->process_line_break($this->props, $module_class)
		);

		add_filter( 'et_global_assets_list', [ $this, 'difl_load_required_divi_assets' ], 10, 3 );
		add_filter( 'et_late_global_assets_list', [ $this, 'difl_load_required_divi_assets' ], 10, 3 );

		return $output;
	}

	public function process_line_break( $props, $module_class ) {
		if('Line_Break' !== $props['content_type']) return "";
		return sprintf('<span class="%1$s df_break_line"></span>', $module_class);
	}
	public function process_text($props, $module_class) {
		if('Icon' === $props['content_type'] || 'Image' === $props['content_type'] || 'Line_Break' === $props['content_type']) return "";

		return sprintf('<span class="%2$s difl_inline_content_text">%1$s</span>', esc_html($props['content_text']), $module_class );
	}
	public function process_icon($props, $render_slug, $module_class) {
		if('Icon' !== $props['content_type']) return "";
		if( ! $props['content_icon'] && "" === $props['content_icon'] ) return "";

		$font_icon = ! empty( $props['content_icon'] ) ? $props['content_icon'] : '&#xe08a;||divi||400';
		// Font Icon Style.
		$this->generate_styles(
			[
				'utility_arg'    => 'icon_font_family',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'content_icon',
				'important'      => true,
				'selector'       => "{$this->main_css_element}.difl_inline_content_icon",
				'hover_selector' => "{$this->main_css_element}.difl_inline_content_icon:hover",
				'processor'      => [
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				],
			]
		);

		return sprintf( '<span class="%2$s et-pb-icon difl_inline_content_icon">%1$s</span>', esc_attr( et_pb_process_font_icon( $font_icon ) ), $module_class );

	}
	public function process_image($props, $module_class) {
		if('Image' !== $props['content_type']) return "";
		if( ! $props['content_image'] && "" === $props['content_image'] ) return "";
		return  sprintf('<span class="%2$s"><img src="%1$s" alt="" class="difl_inline_content_image"/></span>', esc_html($props['content_image']), $module_class );
	}
	protected function _render_module_wrapper( $output = '', $render_slug = '' ) {
		return $output;
	}
	public function difl_load_required_divi_assets( $assets_list, $assets_args, $instance ) {
		$assets_prefix  = et_get_dynamic_assets_path();

		if ( ! isset( $assets_list['et_icons_all'] ) ) {
			$assets_list['et_icons_all'] = [
				'css' => "{$assets_prefix}/css/icons_all.css",
			];
		}

		if ( ! isset( $assets_list['et_icons_fa'] ) ) {
			$assets_list['et_icons_fa'] = [
				'css' => "{$assets_prefix}/css/icons_fa_all.css",
			];
		}

		return $assets_list;
	}
}
new InlineContentsItem;