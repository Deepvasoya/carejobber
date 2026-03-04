<?php

class InlineContents extends ET_Builder_Module {
	use \DIFL\Handler\Fa_Icon_Handler;
	use DF_UTLS;

	public $icon_path;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	function init() {
		$this->name            = esc_html__( 'Inline Content', 'divi_flash' );
		$this->plural          = esc_html__( 'Inline Content', 'divi_flash' );
		$this->slug            = 'difl_inline_contents';
		$this->vb_support      = 'on';
		$this->icon_path       = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/inline-contents.svg';
		$this->child_slug      = 'difl_inline_contents_item';
		$this->child_item_text = esc_html__( 'Inline Content', 'divi_flash' );

		$this->main_css_element = "%%order_class%% .difl_inline_contents_container";
	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'content_main' => esc_html__( 'Settings', 'divi_flash' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'alignment'          => esc_html__( 'Alignment', 'divi_flash' ),
					'design_child_text'  => esc_html__( 'Text', 'divi_flash' ),
					'design_child_icon'  => esc_html__( 'Icon', 'divi_flash' ),
					'design_child_media' => esc_html__( 'Image', 'divi_flash' ),
				],
			],
		];
	}

	public function get_advanced_fields_config() {
		$advanced_fields = [];
		$advanced_fields['text']                  = false;

		$advanced_fields['fonts']['content_text'] = [
			'label'            => esc_html__( '', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_child_text',
			'hide_text_shadow' => false,
			'hide_text_align'  => true,
			'line_height'      => [
				'default' => '1em',
			],
			'font_size'        => [
				'default' => '14px',
			],
			'css'              => [
				'main' => "{$this->main_css_element} .difl_inline_content_text",
			],
		];
		$advanced_fields['borders']['content_text']    = [
			'css'         => [
				'main' => [
					'border_radii'  => "
					{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_text ), 
					{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text
					",
					'border_styles' => "
					{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_text ), 
					{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text
					",
				],
			],
			'defaults'    => [
				'border_radii'  => 'on|0px|0px|0px|0px',
				'border_styles' => [
					'width' => '0px',
					'color' => '#333333',
					'style' => 'solid'
				]
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_text',
		];
		$advanced_fields['box_shadow']['content_text'] = [
			'css'         => [
				'main'    => "
				{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_text ), 
				{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text
				",
				'overlay' => 'inset',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_text',
		];

		$advanced_fields['borders']['content_icon']    = [
			'css'         => [
				'main' => [
					'border_radii'  => "
					{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_icon ), 
					{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon
					",
					'border_styles' => "
					{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_icon ), 
					{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon
					",
				],
			],
			'defaults'    => [
				'border_radii'  => 'on|0px|0px|0px|0px',
				'border_styles' => [
					'width' => '0px',
					'color' => '#333333',
					'style' => 'solid'
				]
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_icon',
		];
		$advanced_fields['box_shadow']['content_icon'] = [
			'css'         => [
				'main'    => "
				{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_icon ), 
				{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon
				",
				'overlay' => 'inset',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_icon',
		];

		$advanced_fields['borders']['content_image']    = [
			'css'         => [
				'main' => [
					'border_radii'  => "
					{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_image ), 
					{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image )
					",
					'border_styles' => "
					{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_image ), 
					{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image )
					",
				],
			],
			'defaults'    => [
				'border_radii'  => 'on|0px|0px|0px|0px',
				'border_styles' => [
					'width' => '0px',
					'color' => '#333333',
					'style' => 'solid'
				]
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_media',
		];
		$advanced_fields['box_shadow']['content_image'] = [
			'css'         => [
				'main'    => "
				{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_image ), 
				{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image )
				",
				'overlay' => 'inset',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_media',
		];

		$advanced_fields['background']            = [
			'css'                           => [
				'main'      => "%%order_class%%",
				'important' => 'all',
			],
		];
		$advanced_fields['max_width']             = [
			'css'                  => [
				'main'             => "%%order_class%%",
				'module_alignment' => "%%order_class%%",
				'important'        => 'all',
			],
		];
		$advanced_fields['height']                = [
			'css'                  => [
				'main' => '%%order_class%%',
			],
		];
		$advanced_fields['margin_padding'] = [
			'css' => [
				'main'      => '%%order_class%%',
				'important' => 'all',
			],
		];

		return $advanced_fields;
	}

	public function get_fields() {
		$fields = [];

		$fields['text_bg_color']  = [
			'default'         => '',
			'label'           => esc_html__( 'Background Color', 'divi_flash' ),
			'type'            => 'color-alpha',
			'description'     => esc_html__( 'Here you can define a custom Background Color for your icon.', 'divi_flash' ),
			'depends_show_if' => 'on',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_child_text',
			'hover'           => false,
			'mobile_options'  => false,
			'sticky'          => false,
		];
		$fields['icon_color']     = [
			'default'         => '#2ea3f2',
			'label'           => esc_html__( 'Icon Color', 'divi_flash' ),
			'type'            => 'color-alpha',
			'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'divi_flash' ),
			'depends_show_if' => 'on',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_child_icon',
			'hover'           => false,
			'mobile_options'  => false,
			'sticky'          => false,
		];
		$fields['icon_bg_color']  = [
			'default'         => '',
			'label'           => esc_html__( 'Background Color', 'divi_flash' ),
			'type'            => 'color-alpha',
			'description'     => esc_html__( 'Here you can define a custom Background Color for your icon.', 'divi_flash' ),
			'depends_show_if' => 'on',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_child_icon',
			'hover'           => false,
			'mobile_options'  => false,
			'sticky'          => false,
		];
		$fields['icon_size']      = [
			'label'           => esc_html__( 'Icon Size', 'divi_flash' ),
			'default'         => '24px',
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Icon Size.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_child_icon',
			'validate_unit'   => true,
			'allowed_units'   => [ 'px' ],
			'responsive'      => false,
			'mobile_options'  => true,
			'sticky'          => false,
			'hover'           => false,
		];
		$fields['media_size']     = [
			'label'           => esc_html__( 'Image Size', 'divi_flash' ),
			'default'         => '40px',
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Image Size.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_child_media',
			'validate_unit'   => true,
			'allowed_units'   => [ 'px' ],
			'responsive'      => false,
			'mobile_options'  => true,
			'sticky'          => false,
			'hover'           => false,
		];
		$fields['media_bg_color'] = [
			'default'         => '',
			'label'           => esc_html__( 'Background Color', 'divi_flash' ),
			'type'            => 'color-alpha',
			'description'     => esc_html__( 'Here you can define a custom Background Color for your Media.', 'divi_flash' ),
			'depends_show_if' => 'on',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'design_child_media',
			'hover'           => false,
			'mobile_options'  => false,
			'sticky'          => false,
		];
		$fields['content_alignment'] = [
			'label'           => esc_html__( 'Content Alignment', 'divi_flash' ),
			'description'     => esc_html__( 'Align your content to the left, right or center of the module.', 'divi_flash' ),
			'type'            => 'multiple_buttons',
			'options'         => [
				'flex-start'  => [
					'title' => esc_html__( 'Left', 'divi_flash' ),
					'icon'  => 'align-left', // Any svg icon that is defined on ETBuilderIcon component
				],
				'center' => [
					'title' => esc_html__( 'Center', 'divi_flash' ),
					'icon'  => 'align-center', // Any svg icon that is defined on ETBuilderIcon component
				],
				'flex-end'    => [
					'title' => esc_html__( 'Right', 'divi_flash' ),
					'icon'  => 'align-right', // Any svg icon that is defined on ETBuilderIcon component
				],
			],
			'default'         => 'flex-start',
			'toggleable'      => true,
			'multi_selection' => false,
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'alignment',
			'mobile_options'  => true,
		];
		$fields['items_position'] = [
			'label'           => esc_html__( 'Items Position', 'divi_flash' ),
			'type'             => 'select',
			'option_category'  => 'configuration',
			'options'          => [
				'flex-start' => esc_html__( 'Top', 'divi_flash' ),
				'center' => esc_html__( 'Center', 'divi_flash' ),
				'flex-end' => esc_html__( 'Bottom', 'divi_flash' ),
			],
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'alignment',
		];
		$fields['main_wrapper_tag'] = [
			'label'             => esc_html__('Main Wrapper tag', 'divi_flash'),
			'type'              => 'select',
			'toggle_slug'       => 'content_main',
			'options'           => [
				'h1'            => 'h1',
				'h2'            => 'h2',
				'h3'            => 'h3',
				'h4'            => 'h4',
				'h5'            => 'h5',
				'h6'            => 'h6',
				'span'          => 'span',
				'p'             => 'p',
				'div'           => 'div'
			],
			'default'           => 'div'
		];
		$fields['column_gap']      = [
			'label'           => esc_html__( 'Column Gap', 'divi_flash' ),
			'default'         => '5px',
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Column Gap.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'general',
			'toggle_slug'     => 'content_main',
			'validate_unit'   => true,
			'allowed_units'   => [ 'px' ],
			'responsive'      => false,
			'mobile_options'  => true,
			'sticky'          => false,
			'hover'           => false,
		];
		$fields['row_gap']      = [
			'label'           => esc_html__( 'Row Gap', 'divi_flash' ),
			'default'         => '5px',
			'range_settings'  => [
				'min'  => '1',
				'max'  => '200',
				'step' => '1',
			],
			'description'     => esc_html__( 'Here you can choose Row Gap.', 'divi_flash' ),
			'type'            => 'range',
			'option_category' => 'layout',
			'tab_slug'        => 'general',
			'toggle_slug'     => 'content_main',
			'validate_unit'   => true,
			'allowed_units'   => [ 'px' ],
			'responsive'      => false,
			'mobile_options'  => true,
			'sticky'          => false,
			'hover'           => false,
		];

		/*------ Spacing ------*/
		$text_margin  = $this->add_margin_padding( [
			'title'       => 'Text',
			'key'         => 'text_container',
			'toggle_slug' => 'margin_padding',
		] );
		$icon_margin  = $this->add_margin_padding( [
			'title'       => 'Icon',
			'key'         => 'icon_container',
			'toggle_slug' => 'margin_padding',
		] );
		$media_margin = $this->add_margin_padding( [
			'title'       => 'Image',
			'key'         => 'media_container',
			'toggle_slug' => 'margin_padding',
		] );

		return array_merge( $fields, $icon_margin, $media_margin, $text_margin );
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		/*------ Spacing ------*/
		// Text
		$fields['text_container_margin']['margin']   = "
		{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_text ), 
		{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text
		";
		$fields['text_container_padding']['padding'] = "
		{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_text ), 
		{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text
		";
		// Icon
		$fields['icon_container_margin']['margin']   = "
		{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_icon ), 
		{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon
		";
		$fields['icon_container_padding']['padding'] = "
		{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_icon ), 
		{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon
		";
		// Media
		$fields['media_container_margin']['margin']   = "
		{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_image ), 
		{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image )
		";
		$fields['media_container_padding']['padding'] = "
		{$this->main_css_element} .difl_inline_contents_item:has( div > .difl_inline_content_image ), 
		{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image )
		";

		return $fields;
	}

	public function additional_css_styles( $render_slug ) {
		// Text
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_bg_color',
				'type'        => 'background-color',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text:hover"
			]
		);

		// Icon
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_color',
				'type'        => 'color',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon:hover"
			]
		);
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'icon_size',
			'type'        => 'font-size',
			'default'     => '24px',
			'selector'    => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon",
			'hover'       => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon:hover",
		] );
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_bg_color',
				'type'        => 'background-color',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon:hover"
			]
		);

		// Media
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'media_size',
			'type'        => 'width',
			'default'     => '40px',
			'selector'    => "{$this->main_css_element} .difl_inline_contents_item .difl_inline_content_image",
			'hover'       => "{$this->main_css_element} .difl_inline_contents_item:hover .difl_inline_content_image",
		] );
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug'        => 'media_bg_color',
				'type'        => 'background-color',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image )",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image ):hover"
			]
		);

		/*------ Spacing ------*/
		// Text
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_container_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text:hover",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_container_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_text:hover",
				'important'   => false
			]
		);
		// Icon
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_container_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon:hover",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_container_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item.difl_inline_content_icon:hover",
				'important'   => false
			]
		);
		// Media
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'media_container_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image )",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image ):hover",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'media_container_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image )",
				'hover'       => "{$this->main_css_element} .difl_inline_contents_item:has( .difl_inline_content_image ):hover",
				'important'   => false
			]
		);

		// Content Alignment
		if ( ! empty( $this->props['content_alignment'] ) ) {
			$content_alignment        = isset( $this->props['content_alignment'] ) ? $this->props['content_alignment'] : 'flex-start';
			$content_alignment_tablet = isset( $this->props['content_alignment_tablet'] ) ? $this->props['content_alignment_tablet'] : $content_alignment;
			$content_alignment_phone  = isset( $this->props['content_alignment_phone'] ) ? $this->props['content_alignment_phone'] : $content_alignment_tablet;
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element}",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($content_alignment) ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element}",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($content_alignment_tablet) ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			] );
			ET_Builder_Element::set_style( $render_slug, [
				'selector'    => "{$this->main_css_element}",
				'declaration' => sprintf( 'justify-content: %1$s;', esc_attr($content_alignment_phone) ),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			] );
		}

		// Items Alignment
		$items_position        = isset( $this->props['items_position'] ) ? $this->props['items_position'] : 'flex-start';
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "{$this->main_css_element}",
			'declaration' => sprintf( 'align-items: %1$s;', esc_attr($items_position) ),
		] );

		// Gap
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'column_gap',
			'type'        => 'column-gap',
			'default'     => '5px',
			'selector'    => "{$this->main_css_element}",
			'hover'       => "{$this->main_css_element}",
		] );
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'row_gap',
			'type'        => 'row-gap',
			'default'     => '5px',
			'selector'    => "{$this->main_css_element}",
			'hover'       => "{$this->main_css_element}",
		] );


	}

	public function render( $attrs, $content, $render_slug ) {
		$multi_view = et_pb_multi_view_options( $this );
		$this->handle_fa_icon();
		$this->additional_css_styles( $render_slug );

		$main_wrapper_tag = ! empty( $attrs['main_wrapper_tag'] ) ? $attrs['main_wrapper_tag'] : 'div';
		$output_content = $multi_view->render_element(
			[
				'tag'     => $main_wrapper_tag,
				'content' => et_core_sanitized_previously( $this->content ),
				'attrs'   => [
					'class' => 'difl_inline_contents_container',
					'id'    => 'difl-inline-contents-container'
				],
			]
		);

		return $output_content;
	}
}
new InlineContents;