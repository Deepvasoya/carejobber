<?php
class DIFL_TextHighlighter extends ET_Builder_Module {
	use DIFL\Handler\Fa_Icon_Handler;

	public $slug       = 'difl_text_highlighter';
	public $vb_support = 'on';
	public $icon_path;
	use DF_UTLS;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	public function init() {
		$this->name             = esc_html__( 'Text Highlighter', 'divi_flash' );
		$this->main_css_element = '%%order_class%%';
		$this->icon_path        = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/text-highlighter.svg';
	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'content'              => esc_html__( 'Content', 'divi_flash' ),
					'highlighter_settings' => esc_html__( 'Highlighter Settings', 'divi_flash' ),
					'divider'              => esc_html__( 'Divider', 'divi_flash' ),
					'divider_background'   => esc_html__( 'Divider Line Background', 'divi_flash' ),
					'prefix_background'    => esc_html__( 'Prefix Background', 'divi_flash' ),
					'infix_background'     => esc_html__( 'Infix Background', 'divi_flash' ),
					'suffix_background'    => esc_html__( 'Suffix Background', 'divi_flash' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'title'          => esc_html__( 'Title', 'divi_flash' ),
					'dual_text'      => esc_html__( 'Dual Text', 'divi_flash' ),
					'prefix'         => esc_html__( 'Prefix Text', 'divi_flash' ),
					'infix'          => esc_html__( 'Infix Text', 'divi_flash' ),
					'suffix'         => esc_html__( 'Suffix Text', 'divi_flash' ),
					'highlighter'    => esc_html__( 'Highlighter', 'divi_flash' ),
					'border'         => esc_html__( 'Border', 'divi_flash' ),
					'custom_borders' => esc_html__( 'Custom border', 'divi_flash' ),
					'custom_spacing' => esc_html__( 'Custom Spacing', 'divi_flash' ),
				],
			],
		];
	}

	public function get_advanced_fields_config() {
		$advanced_fields          = [];

		$advanced_fields['fonts']['title']    = [
			'label'       => esc_html__( 'Title', 'divi_flash' ),
			'toggle_slug' => 'title',
			'tab_slug'    => 'advanced',
			'line_height' => [
				'default' => '1em',
			],
			'font_size'   => [
				'default' => '24',
			],
			'css'         => [
				'main'      => "{$this->main_css_element} h1,
                                {$this->main_css_element} h2,
                                {$this->main_css_element} h3,
                                {$this->main_css_element} h4,
                                {$this->main_css_element} h5,
                                {$this->main_css_element} h6,
                                {$this->main_css_element} p,
                                {$this->main_css_element} span,
                                {$this->main_css_element} div,
                                {$this->main_css_element} h1 span,
                                {$this->main_css_element} h2 span,
                                {$this->main_css_element} h3 span,
                                {$this->main_css_element} h4 span,
                                {$this->main_css_element} h5 span,
                                {$this->main_css_element} h6 span,
                                {$this->main_css_element} p span,
                                {$this->main_css_element} span span,
                                {$this->main_css_element} div span",
				'hover'     => "{$this->main_css_element}:hover h1,
                                {$this->main_css_element}:hover h2,
                                {$this->main_css_element}:hover h3,
                                {$this->main_css_element}:hover h4,
                                {$this->main_css_element}:hover h5,
                                {$this->main_css_element}:hover h6,
                                {$this->main_css_element}:hover p,
                                {$this->main_css_element}:hover span,
                                {$this->main_css_element}:hover div,
                                {$this->main_css_element}:hover h1 span,
                                {$this->main_css_element}:hover h2 span,
                                {$this->main_css_element}:hover h3 span,
                                {$this->main_css_element}:hover h4 span,
                                {$this->main_css_element}:hover h5 span,
                                {$this->main_css_element}:hover h6 span,
                                {$this->main_css_element}:hover p span,
                                {$this->main_css_element}:hover span span,
                                {$this->main_css_element}:hover div span",
				'important' => 'all',
			],
		];
		$advanced_fields['fonts']['t_dual']   = [
			'label'       => esc_html__( 'Dual Text', 'divi_flash' ),
			'toggle_slug' => 'dual_text',
			'tab_slug'    => 'advanced',
			'text_color'  => [
				'default' => '#e0e0e0',
			],
			'line_height' => [
				'default' => '1em',
			],
			'font_size'   => [
				'default' => '30px',
			],
			'css'         => [
				'main'      => "{$this->main_css_element} .df-heading-dual_text",
				'hover'     => "{$this->main_css_element}:hover .df-heading-dual_text",
				'important' => 'all',
			],
		];
		$advanced_fields['fonts']['t_prefix'] = [
			'label'       => esc_html__( 'Prefix', 'divi_flash' ),
			'toggle_slug' => 'prefix',
			'tab_slug'    => 'advanced',
			'line_height' => [
				'default' => '1em',
			],
			'font_size'   => [
				'default' => '24px',
			],
			'css'         => [
				'main'      => "{$this->main_css_element} span.prefix",
				'hover'     => "{$this->main_css_element}:hover span.prefix",
				'important' => 'all',
			],
		];
		$advanced_fields['fonts']['t_infix']  = [
			'label'       => esc_html__( 'Infix', 'divi_flash' ),
			'toggle_slug' => 'infix',
			'tab_slug'    => 'advanced',
			'line_height' => [
				'default' => '1em',
			],
			'font_size'   => [
				'default' => '24px',
			],
			'css'         => [
				'main'      => "{$this->main_css_element} span.infix",
				'hover'     => "{$this->main_css_element}:hover span.infix",
				'important' => 'all',
			],
		];
		$advanced_fields['fonts']['t_suffix'] = [
			'label'       => esc_html__( 'Suffix', 'divi_flash' ),
			'toggle_slug' => 'suffix',
			'tab_slug'    => 'advanced',
			'line_height' => [
				'default' => '1em',
			],
			'font_size'   => [
				'default' => '24px',
			],
			'css'         => [
				'main'      => "{$this->main_css_element} span.suffix",
				'hover'     => "{$this->main_css_element}:hover span.suffix",
				'important' => 'all',
			],
		];

		$advanced_fields['borders']['default']       = [];
		$advanced_fields['borders']['prefix_border'] = [
			'css'          => [
				'main' => [
					'border_radii'        => "{$this->main_css_element} span.prefix",
					'border_styles'       => "{$this->main_css_element} span.prefix",
					'border_styles_hover' => "{$this->main_css_element}:hover span.prefix",
				],
			],
			'label_prefix' => esc_html__( 'Prefix', 'divi_flash' ),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'custom_borders',
		];
		$advanced_fields['borders']['infix_border']  = [
			'css'          => [
				'main' => [
					'border_radii'        => "{$this->main_css_element} span.infix",
					'border_styles'       => "{$this->main_css_element} span.infix",
					'border_styles_hover' => "{$this->main_css_element}:hover span.infix",
				],
			],
			'label_prefix' => esc_html__( 'Infix', 'divi_flash' ),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'custom_borders',
		];
		$advanced_fields['borders']['suffix_border'] = [
			'css'          => [
				'main' => [
					'border_radii'        => "{$this->main_css_element} span.suffix",
					'border_styles'       => "{$this->main_css_element} span.suffix",
					'border_styles_hover' => "{$this->main_css_element}:hover span.suffix",
				],
			],
			'label_prefix' => esc_html__( 'Suffix', 'divi_flash' ),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'custom_borders',
		];

		$advanced_fields['box_shadow']['default'] = [];
		$advanced_fields['box_shadow']['prefix']  = [
			'css'         => [
				'main'  => '%%order_class%% span.prefix',
				'hover' => '%%order_class%%:hover span.prefix',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'prefix',
		];
		$advanced_fields['box_shadow']['infix']   = [
			'css'         => [
				'main'  => '%%order_class%% span.infix',
				'hover' => '%%order_class%%:hover span.infix',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'infix',
		];
		$advanced_fields['box_shadow']['suffix']  = [
			'css'         => [
				'main'  => '%%order_class%% span.suffix',
				'hover' => '%%order_class%%:hover span.suffix',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'suffix',
		];

		$advanced_fields['margin_padding'] = [
			'css' => [
				'important' => 'all',
			],
		];
		$advanced_fields['text']           = false;
		$advanced_fields['filters']        = false;

		return $advanced_fields;
	}

	public function get_fields() {
		$heading = [
			'title_tag'            => [
				'label'       => esc_html__( 'Title Tag', 'divi_flash' ),
				'description' => esc_html__( 'Choose a tag to display title.', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'h1'   => esc_html__( 'H1 tag', 'divi_flash' ),
					'h2'   => esc_html__( 'H2 tag', 'divi_flash' ),
					'h3'   => esc_html__( 'H3 tag', 'divi_flash' ),
					'h4'   => esc_html__( 'H4 tag', 'divi_flash' ),
					'h5'   => esc_html__( 'H5 tag', 'divi_flash' ),
					'h6'   => esc_html__( 'H6 tag', 'divi_flash' ),
					'p'    => esc_html__( 'P tag', 'divi_flash' ),
					'span' => esc_html__( 'Span tag', 'divi_flash' ),
					'div'  => esc_html__( 'Div tag', 'divi_flash' ),
				],
				'toggle_slug' => 'title',
				'tab_slug'    => 'advanced',
				'default'     => 'h3',
			],
			'title_prefix'         => [
				'label'           => esc_html__( 'Title Prefix', 'divi_flash' ),
				'type'            => 'text',
				'toggle_slug'     => 'content',
				'dynamic_content' => 'text',
			],
			'title_prefix_block'   => [
				'label'            => esc_html__( 'Display Element', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'inline-block' => esc_html__( 'Default', 'divi_flash' ),
					'block'        => esc_html__( 'Block', 'divi_flash' ),
					'inline'       => esc_html__( 'Inline', 'divi_flash' ),
				],
				'default'          => 'inline-block',
				'default_on_front' => 'inline-block',
				'toggle_slug'      => 'prefix',
				'tab_slug'         => 'advanced',
				'responsive'       => true,
				'mobile_options'   => true,
				'dynamic_content'  => 'text',
			],
			'title_infix'          => [
				'label'           => esc_html__( 'Title Infix (Highlight)', 'divi_flash' ),
				'type'            => 'text',
				'toggle_slug'     => 'content',
				'dynamic_content' => 'text',
			],
			'title_infix_block'    => [
				'label'            => esc_html__( 'Display Block', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'inline-block' => esc_html__( 'Default', 'divi_flash' ),
					'block'        => esc_html__( 'Block', 'divi_flash' ),
					'inline'       => esc_html__( 'Inline', 'divi_flash' ),
				],
				'default'          => 'inline-block',
				'default_on_front' => 'inline-block',
				'toggle_slug'      => 'infix',
				'tab_slug'         => 'advanced',
				'responsive'       => true,
				'mobile_options'   => true,
			],
			'title_suffix'         => [
				'label'           => esc_html__( 'Title Suffix', 'divi_flash' ),
				'type'            => 'text',
				'toggle_slug'     => 'content',
				'dynamic_content' => 'text',
			],
			'title_suffix_block'   => [
				'label'            => esc_html__( 'Display Block', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'inline-block' => esc_html__( 'Default', 'divi_flash' ),
					'block'        => esc_html__( 'Block', 'divi_flash' ),
					'inline'       => esc_html__( 'Inline', 'divi_flash' ),
				],
				'default'          => 'inline-block',
				'default_on_front' => 'inline-block',
				'toggle_slug'      => 'suffix',
				'tab_slug'         => 'advanced',
				'responsive'       => true,
				'mobile_options'   => true,
			],
			'use_dual_text'        => [
				'label'       => esc_html__( 'Use Dual Text', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'default'     => 'off',
				'toggle_slug' => 'dual_text',
				'tab_slug'    => 'advanced',
			],
			'use_dual_text_custom' => [
				'label'       => esc_html__( 'Custom Text', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'default'     => 'off',
				'toggle_slug' => 'dual_text',
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'use_dual_text' => 'on',
				],
			],
			'custom_text_input'    => [
				'label'       => esc_html__( 'Custom Text Input', 'divi_flash' ),
				'type'        => 'text',
				'toggle_slug' => 'dual_text',
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'use_dual_text_custom' => 'on',
				],
			],
		];

		$highlighter_settings = [
			'highlighter_type'                => [
				'label'       => esc_html__( 'Type', 'divi_flash' ),
				'description' => esc_html__( 'Here you can chose highlighter element type.', 'divi_flash' ),
				'type'        => 'df_text_highlighter_select',
				'default'     => 'underline',
				'toggle_slug' => 'highlighter_settings',
			],
			'highlighter_color'               => [
				'label'       => esc_html__( 'Color', 'divi_flash' ),
				'description' => esc_html__( 'Here you can define a custom color for highlighter stroke.', 'divi_flash' ),
				'type'        => 'color-alpha',
				'default'     => '#6A33D7',
				'toggle_slug' => 'highlighter',
				'tab_slug'    => 'advanced',
				'hover'       => 'tabs',
				'show_if_not' => [
					'enable_gradient_color' => 'on',
				],
			],
			'enable_gradient_color'           => [
				'label'       => esc_html__( 'Use Gradient Color', 'divi_flash' ),
				'description' => esc_html__( 'Here you can set gradient color for highlighter stroke.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'default'     => 'off',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' ),
				],
				'toggle_slug' => 'highlighter',
				'tab_slug'    => 'advanced',
			],
			'gradient_color_start'            => [
				'label'       => esc_html__( 'Start Color', 'divi_flash' ),
				'description' => esc_html__( 'Here you can define start gradient color for highlighter stroke.', 'divi_flash' ),
				'type'        => 'color-alpha',
				'default'     => '#2b87da',
				'toggle_slug' => 'highlighter',
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'enable_gradient_color' => 'on',
				],
			],
			'gradient_color_end'              => [
				'label'       => esc_html__( 'End Color', 'divi_flash' ),
				'description' => esc_html__( 'Here you can define end gradient color for highlighter stroke.', 'divi_flash' ),
				'type'        => 'color-alpha',
				'default'     => '#29c4a9',
				'toggle_slug' => 'highlighter',
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'enable_gradient_color' => 'on',
				],
			],
			'gradient_type'                   => [
				'label'       => esc_html__( 'Gradient Type', 'divi_flash' ),
				'description' => esc_html__( 'Here you can set gradient type.', 'divi_flash' ),
				'type'        => 'select',
				'default'     => 'linearGradient',
				'options'     => [
					'linearGradient' => esc_html__( 'Linear', 'divi_flash' ),
					'radialGradient' => esc_html__( 'Radial', 'divi_flash' ),
				],
				'toggle_slug' => 'highlighter',
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'enable_gradient_color' => 'on',
				],
			],
			'gradient_direction'              => [
				'label'          => esc_html__( 'Gradient Direction', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can specify both color angle value.', 'divi_flash' ),
				'type'           => 'range',
				'range_settings' => [
					'min'       => '0',
					'min_limit' => '0',
					'max'       => '360',
					'step'      => '1',
				],
				'default_unit'   => 'deg',
				'default'        => '180deg',
				'toggle_slug'    => 'highlighter',
				'tab_slug'       => 'advanced',
				'show_if'        => [
					'enable_gradient_color' => 'on',
					'gradient_type'         => 'linearGradient',
				],
			],
			'gradient_direction_radial'       => [
				'label'       => esc_html__( 'Radial Direction', 'divi_flash' ),
				'description' => esc_html__( 'Here you can specify both color radial position value..', 'divi_flash' ),
				'type'        => 'select',
				'default'     => 'top',
				'options'     => [
					'top'    => esc_html__( 'Top', 'divi_flash' ),
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
					'left'   => esc_html__( 'Left', 'divi_flash' ),
					'right'  => esc_html__( 'Right', 'divi_flash' ),
					'center' => esc_html__( 'Center', 'divi_flash' ),
				],
				'toggle_slug' => 'highlighter',
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'enable_gradient_color' => 'on',
					'gradient_type'         => 'radialGradient',
				],
			],
			'gradient_start_position'         => [
				'label'          => esc_html__( 'Start position', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can specify the start position of the first gradient color.', 'divi_flash' ),
				'type'           => 'range',
				'range_settings' => [
					'min'       => '0',
					'min_limit' => '0',
					'max'       => '100',
					'max_limit' => '100',
					'step'      => '1',
				],
				'default'        => '0%',
				'default_unit'   => '%',
				'toggle_slug'    => 'highlighter',
				'tab_slug'       => 'advanced',
				'show_if'        => [
					'enable_gradient_color' => 'on',
				],
			],
			'gradient_end_position'           => [
				'label'          => esc_html__( 'End position', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can specify the end position of the first gradient color.', 'divi_flash' ),
				'type'           => 'range',
				'range_settings' => [
					'min'       => '0',
					'min_limit' => '0',
					'max'       => '100',
					'max_limit' => '100',
					'step'      => '1',
				],
				'default'        => '100%',
				'default_unit'   => '%',
				'toggle_slug'    => 'highlighter',
				'tab_slug'       => 'advanced',
				'show_if'        => [
					'enable_gradient_color' => 'on',
				],
			],
			'highlighter_stroke_width'        => [
				'label'          => esc_html__( 'Stroke Width', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can define stroke width for highlighter element.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'highlighter',
				'tab_slug'       => 'advanced',
				'default'        => '8px',
				'default_unit'   => 'px',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'range_settings' => [
					'min'       => '0',
					'min_limit' => '0',
					'max'       => '100',
					'step'      => '1',
				],
			],
			'highlighter_size'                => [
				'label'          => esc_html__( 'Size (by scale)', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can define a custom size for highlighter element by scale. The amount of scaling is defined by a vector [sx, sy], it can resize the horizontal and vertical dimensions at different scales.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'highlighter',
				'tab_slug'       => 'advanced',
				'default'        => '1',
				'default_unit'   => '',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'range_settings' => [
					'min'       => '0',
					'min_limit' => '0',
					'max'       => '10',
					'step'      => '.01',
				],
			],
			'highlighter_opacity'             => [
				'label'          => esc_html__( 'Opacity', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can set the opacity level for highlighter element.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'highlighter',
				'tab_slug'       => 'advanced',
				'range_settings' => [
					'min'       => '0',
					'min_limit' => '0',
					'max'       => '1',
					'step'      => '.01',
				],
				'hover'          => 'tabs',
				'mobile_options' => true,
				'default'        => '1',
			],
			'highlighter_position'            => [
				'label'       => esc_html__( 'Position', 'divi_flash' ),
				'description' => esc_html__( 'Select whether the highlighter element is above or below the text.', 'divi_flash' ),
				'type'        => 'select',
				'default'     => 'below',
				'options'     => [
					'below' => esc_html__( 'Below Text', 'divi_flash' ),
					'above' => esc_html__( 'Above Text', 'divi_flash' ),
				],
				'toggle_slug' => 'highlighter',
				'tab_slug'    => 'advanced',
			],
			'highlighter_vertical_position'   => [
				'label'          => esc_html__( 'Vertical Offset', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can define vertical offset of highlighter element.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'highlighter',
				'tab_slug'       => 'advanced',
				'default'        => '0px',
				'default_unit'   => 'px',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'range_settings' => [
					'min'  => '-100',
					'max'  => '100',
					'step' => '1',
				],
			],
			'highlighter_horizontal_position' => [
				'label'          => esc_html__( 'Horizontal Offset', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can define horizontal offset of highlighter element.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'highlighter',
				'tab_slug'       => 'advanced',
				'default'        => '0px',
				'default_unit'   => 'px',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'range_settings' => [
					'min'  => '-100',
					'max'  => '100',
					'step' => '1',
				],
			],
			'enable_animation'                => [
				'label'       => esc_html__( 'Enable Animation', 'divi_flash' ),
				'description' => esc_html__( 'Here you can enable animation for highlighter.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'default'     => 'on',
				'options'     => [
					'off' => esc_html__( 'Off', 'divi_flash' ),
					'on'  => esc_html__( 'On', 'divi_flash' ),
				],
				'toggle_slug' => 'highlighter_settings',
			],
			'anim_start'                      => [
				'label'       => esc_html__( 'Animation Start', 'divi_flash' ),
				'description' => esc_html__( 'Define when the animation will start.', 'divi_flash' ),
				'type'        => 'select',
				'default'     => 'page_load',
				'options'     => [
					'page_load' => esc_html__( 'On Page Load', 'divi_flash' ),
					'viewport'  => esc_html__( 'In Viewport', 'divi_flash' ),
				],
				'toggle_slug' => 'highlighter_settings',
				'show_if'     => [
					'enable_animation' => 'on',
				],
			],
			'anim_start_viewport'             => [
				'label'          => esc_html__( 'Viewport Bottom Offset', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can specifies the animation start position on viewport from bottom.', 'divi_flash' ),
				'type'           => 'range',
				'range_settings' => [
					'min'       => '1',
					'min_limit' => '1',
					'max'       => '100',
					'max_limit' => '100',
					'step'      => '1',
				],
				'default'        => '50%',
				'default_unit'   => '%',
				'toggle_slug'    => 'highlighter_settings',
				'show_if'        => [
					'anim_start'       => 'viewport',
					'enable_animation' => 'on',
				],
			],
			'anim_easing'                     => [
				'label'       => esc_html__( 'Easing', 'divi_flash' ),
				'description' => esc_html__( 'The easing option specifies the speed curve of an animation.', 'divi_flash' ),
				'type'        => 'select',
				'default'     => 'linear',
				'options'     => [
					'LINEAR'          => esc_html__( 'linear', 'divi_flash' ),
					'EASE'            => esc_html__( 'ease', 'divi_flash' ),
					'EASE_IN'         => esc_html__( 'ease-in', 'divi_flash' ),
					'EASE_OUT'        => esc_html__( 'ease-out', 'divi_flash' ),
					'EASE_OUT_BOUNCE' => esc_html__( 'ease-out-bounce', 'divi_flash' ),
				],
				'toggle_slug' => 'highlighter_settings',
				'show_if'     => [
					'enable_animation' => 'on',
				],
			],
			'anim_duration'                   => [
				'label'          => esc_html__( 'Duration', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can define duration for animation by ms.', 'divi_flash' ),
				'type'           => 'range',
				'range_settings' => [
					'min'       => '100',
					'min_limit' => '100',
					'max'       => '10000',
					'step'      => '1',
				],
				'default_unit'   => 'ms',
				'default'        => '1000ms',
				'toggle_slug'    => 'highlighter_settings',
				'show_if'        => [
					'enable_animation' => 'on',
				],
			],
			'anim_delay'                      => [
				'label'          => esc_html__( 'Delay', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can define a delay for the start of an animation ms.', 'divi_flash' ),
				'type'           => 'range',
				'range_settings' => [
					'min'       => '0',
					'min_limit' => '0',
					'max'       => '10000',
					'step'      => '1',
				],
				'default_unit'   => 'ms',
				'default'        => '0ms',
				'toggle_slug'    => 'highlighter_settings',
				'show_if'        => [
					'enable_animation' => 'on',
				],
			],
			'enable_loop'                     => [
				'label'       => esc_html__( 'Loop', 'divi_flash' ),
				'description' => esc_html__( 'Here you can enable animation for infinite times.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'default'     => 'off',
				'options'     => [
					'off' => esc_html__( 'Off', 'divi_flash' ),
					'on'  => esc_html__( 'On', 'divi_flash' ),
				],
				'toggle_slug' => 'highlighter_settings',
				'show_if'     => [
					'enable_animation' => 'on',
				],
			],
			'anim_iteration'                  => [
				'label'          => esc_html__( 'Iteration', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can specifies the number of times an animation should be played.', 'divi_flash' ),
				'type'           => 'range',
				'range_settings' => [
					'min'       => '1',
					'min_limit' => '1',
					'max'       => '1000',
					'step'      => '1',
				],
				'default'        => '1',
				'toggle_slug'    => 'highlighter_settings',
				'show_if'        => [
					'enable_loop'      => 'off',
					'enable_animation' => 'on',
				],
			],
			'anim_iteration_gap'              => [
				'label'          => esc_html__( 'Iteration Gap', 'divi_flash' ),
				'description'    => esc_html__( 'Here you can define a delay between each iteration by ms. This options will work if iteration more than one.', 'divi_flash' ),
				'type'           => 'range',
				'range_settings' => [
					'min'       => '1',
					'min_limit' => '1',
					'max'       => '10000',
					'step'      => '1',
				],
				'default_unit'   => 'ms',
				'default'        => '1000ms',
				'toggle_slug'    => 'highlighter_settings',
				'show_if'        => [
					'enable_animation' => 'on',
				],
			],
		];
		$divider              = [
			'use_divider'              => [
				'label'       => esc_html__( 'Use Divider', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'default'     => 'off',
				'toggle_slug' => 'divider',
			],
			'divider_position'         => [
				'label'       => esc_html__( 'Divider Position', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'bottom' => esc_html__( 'Bottom', 'divi_flash' ),
					'top'    => esc_html__( 'Top', 'divi_flash' ),
				],
				'toggle_slug' => 'divider',
				'default'     => 'bottom',
				'show_if'     => [
					'use_divider' => 'on',
				],
			],
			'divider_style'            => [
				'label'       => esc_html__( 'Divider Line Style', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'solid'  => esc_html__( 'Default', 'divi_flash' ),
					'dotted' => esc_html__( 'Dotted', 'divi_flash' ),
					'dashed' => esc_html__( 'Dashed', 'divi_flash' ),
					'double' => esc_html__( 'Double', 'divi_flash' ),
					'groove' => esc_html__( 'Groove', 'divi_flash' ),
					'ridge'  => esc_html__( 'Ridge', 'divi_flash' ),
				],
				'toggle_slug' => 'divider',
				'default'     => 'solid',
				'show_if'     => [
					'use_divider' => 'on',
				],
			],
			'divider_color'            => [
				'label'       => esc_html__( 'Divider Line Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'toggle_slug' => 'divider',
				'show_if'     => [
					'use_divider' => 'on',
				],
			],
			'divider_height'           => [
				'label'            => esc_html__( 'Divider Thickness', 'divi_flash' ),
				'type'             => 'range',
				'toggle_slug'      => 'divider',
				'default'          => '5px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'mobile_options'   => true,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				],
				'show_if'          => [
					'use_divider' => 'on',
				],
			],
			'divider_border_radius'    => [
				'label'            => esc_html__( 'Divider Border Radius', 'divi_flash' ),
				'type'             => 'range',
				'toggle_slug'      => 'divider',
				'default'          => '0px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'hover'            => 'tabs',
				'responsive'       => true,
				'mobile_options'   => true,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				],
				'show_if'          => [
					'use_divider' => 'on',
				],
			],
			'divider_width'            => [
				'label'            => esc_html__( 'Divider Max Width', 'divi_flash' ),
				'type'             => 'range',
				'toggle_slug'      => 'divider',
				'default'          => '100%',
				'default_unit'     => '%',
				'default_on_front' => '',
				'hover'            => 'tabs',
				'mobile_options'   => true,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				],
				'show_if'          => [
					'use_divider' => 'on',
				],
			],
			'divider_alignment'        => [
				'label'       => esc_html__( 'Divider Alignment', 'divi_flash' ),
				'type'        => 'text_align',
				'toggle_slug' => 'divider',
				'options'     => et_builder_get_text_orientation_options( [ 'justified' ] ),
				'show_if'     => [
					'use_divider' => 'on',
				],
				'show_if_not' => [
					'divider_width' => '100%',
				],
			],
			'use_divider_icon'         => [
				'label'       => esc_html__( 'Use Divider Icon', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'default'     => 'off',
				'toggle_slug' => 'divider',
				'show_if'     => [
					'use_divider' => 'on',
				],
				'show_if_not' => [
					'use_divider_image' => 'on',
				],
			],
			'divider_icon'             => [
				'label'       => esc_html__( 'Icon', 'divi_flash' ),
				'type'        => 'select_icon',
				'class'       => [ 'et-pb-font-icon' ],
				'toggle_slug' => 'divider',
				'show_if'     => [
					'use_divider'      => 'on',
					'use_divider_icon' => 'on',
				],
			],
			'divider_icon_alignment'   => [
				'label'       => esc_html__( 'Divider Icon Alignment', 'divi_flash' ),
				'type'        => 'text_align',
				'toggle_slug' => 'divider',
				'options'     => et_builder_get_text_orientation_options( [ 'justified' ] ),
				'show_if'     => [
					'use_divider'      => 'on',
					'use_divider_icon' => 'on',
				],
			],
			'divider_icon_color'       => [
				'label'       => esc_html__( 'Divider Icon Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'toggle_slug' => 'divider',
				'hover'       => 'tabs',
				'show_if'     => [
					'use_divider'      => 'on',
					'use_divider_icon' => 'on',
				],
			],
			'divider_icon_bgcolor'     => [
				'label'       => esc_html__( 'Divider Icon Background Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'toggle_slug' => 'divider',
				'hover'       => 'tabs',
				'default'     => 'rgba(0,0,0,0)',
				'show_if'     => [
					'use_divider'      => 'on',
					'use_divider_icon' => 'on',
				],
			],
			'use_divider_icon_circle'  => [
				'label'       => esc_html__( 'Icon Circle', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'default'     => 'off',
				'toggle_slug' => 'divider',
				'show_if'     => [
					'use_divider'      => 'on',
					'use_divider_icon' => 'on',
				],
			],
			'dvr_icon_font_size'       => [
				'label'            => esc_html__( 'Divider Icon Size', 'divi_flash' ),
				'type'             => 'range',
				'toggle_slug'      => 'divider',
				'default'          => '18px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'hover'            => 'tabs',
				'responsive'       => true,
				'mobile_options'   => true,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				],
				'show_if'          => [
					'use_divider'      => 'on',
					'use_divider_icon' => 'on',
				],
			],
			'use_divider_image'        => [
				'label'       => esc_html__( 'Use Divider Image', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'default'     => 'off',
				'toggle_slug' => 'divider',
				'show_if'     => [
					'use_divider' => 'on',
				],
				'show_if_not' => [
					'use_divider_icon' => 'on',
				],
			],
			'divider_image'            => [
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Image', 'divi_flash' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'divi_flash' ),
				'update_text'        => esc_attr__( 'Set As Image', 'divi_flash' ),
				'toggle_slug'        => 'divider',
				'show_if'            => [
					'use_divider'       => 'on',
					'use_divider_image' => 'on',
				],
			],
			'divider_image_alt_text'   => [
				'label'       => esc_html__( 'Image Alt Text', 'divi_flash' ),
				'type'        => 'text',
				'toggle_slug' => 'divider',
				'show_if'     => [
					'use_divider'       => 'on',
					'use_divider_image' => 'on',
				],
			],
			'divider_image_width'      => [
				'label'            => esc_html__( 'Divider Image Max Width', 'divi_flash' ),
				'type'             => 'range',
				'toggle_slug'      => 'divider',
				'default'          => '100px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'hover'            => 'tabs',
				'responsive'       => true,
				'mobile_options'   => true,
				'range_settings'   => [
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				],
				'show_if'          => [
					'use_divider'       => 'on',
					'use_divider_image' => 'on',
				],
			],
			'divider_image_alignment'  => [
				'label'       => esc_html__( 'Divider Image Alignment', 'divi_flash' ),
				'type'        => 'text_align',
				'toggle_slug' => 'divider',
				'options'     => et_builder_get_text_orientation_options( [ 'justified' ] ),
				'show_if'     => [
					'use_divider'       => 'on',
					'use_divider_image' => 'on',
				],
			],
			'divider_image_bgcolor'    => [
				'label'       => esc_html__( 'Divider Image Background Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'toggle_slug' => 'divider',
				'hover'       => 'tabs',
				'default'     => 'rgba(0,0,0,0)',
				'show_if'     => [
					'use_divider'       => 'on',
					'use_divider_image' => 'on',
				],
			],
			'use_divider_image_circle' => [
				'label'       => esc_html__( 'Image Circle', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'off' => esc_html__( 'No', 'divi_flash' ),
					'on'  => esc_html__( 'Yes', 'divi_flash' ),
				],
				'default'     => 'off',
				'toggle_slug' => 'divider',
				'show_if'     => [
					'use_divider'       => 'on',
					'use_divider_image' => 'on',
				],
			],
		];
		$prefix_max_width     = $this->df_add_max_width(
			[
				'title_pefix' => 'Prefix',
				'key'         => 'prefix',
				'toggle_slug' => 'prefix',
				'sub_toggle'  => null,
				'alignment'   => true,
				'priority'    => 30,
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'title_prefix_block' => 'block',
				],
			]
		);
		$infix_max_width      = $this->df_add_max_width(
			[
				'title_pefix' => 'Infix',
				'key'         => 'infix',
				'toggle_slug' => 'infix',
				'sub_toggle'  => null,
				'alignment'   => true,
				'priority'    => 30,
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'title_infix_block' => 'block',
				],
			]
		);
		$suffix_max_width     = $this->df_add_max_width(
			[
				'title_pefix' => 'Suffix',
				'key'         => 'suffix',
				'toggle_slug' => 'suffix',
				'sub_toggle'  => null,
				'alignment'   => true,
				'priority'    => 30,
				'tab_slug'    => 'advanced',
				'show_if'     => [
					'title_suffix_block' => 'block',
				],
			]
		);
		$divider_background   = $this->df_add_bg_field(
			[
				'label'       => 'Divider Line Background',
				'key'         => 'divider_background',
				'toggle_slug' => 'divider_background',
				'tab_slug'    => 'general',
			]
		);
		$prefix_background    = $this->df_add_bg_field(
			[
				'label'       => 'Prefix Background',
				'key'         => 'prefix_background',
				'toggle_slug' => 'prefix_background',
				'tab_slug'    => 'general',
			]
		);
		$infix_background     = $this->df_add_bg_field(
			[
				'label'       => 'Infix Background',
				'key'         => 'infix_background',
				'toggle_slug' => 'infix_background',
				'tab_slug'    => 'general',
			]
		);
		$suffix_background    = $this->df_add_bg_field(
			[
				'label'       => 'Suffix Background',
				'key'         => 'suffix_background',
				'toggle_slug' => 'suffix_background',
				'tab_slug'    => 'general',
			]
		);
		$heading_spacing      = $this->add_margin_padding(
			[
				'title'       => 'Heading',
				'key'         => 'heading',
				'toggle_slug' => 'margin_padding',
			]
		);
		$prefix_spacing       = $this->add_margin_padding(
			[
				'title'       => 'Prefix',
				'key'         => 'prefix',
				'toggle_slug' => 'margin_padding',
			]
		);
		$infix_spacing        = $this->add_margin_padding(
			[
				'title'       => 'Infix',
				'key'         => 'infix',
				'toggle_slug' => 'margin_padding',
			]
		);
		$suffix_spacing       = $this->add_margin_padding(
			[
				'title'       => 'Suffix',
				'key'         => 'suffix',
				'toggle_slug' => 'margin_padding',
			]
		);
		$divider_container_spacing = $this->add_margin_padding(
			[
				'title'       => 'Divider Container',
				'key'         => 'divider_container',
				'toggle_slug' => 'margin_padding',
			]
		);
		$divider_spacing           = $this->add_margin_padding(
			[
				'title'       => 'Divider Line',
				'key'         => 'divider',
				'toggle_slug' => 'margin_padding',
			]
		);
		$divider_icon_spacing      = $this->add_margin_padding(
			[
				'title'       => 'Divider Icon & Image',
				'key'         => 'divider_icon_image',
				'toggle_slug' => 'margin_padding',
			]
		);
		$dual_text_spacing = $this->add_margin_padding(
			[
				'title'       => 'Dual Text',
				'key'         => 'dual_text',
				'toggle_slug' => 'dual_text',
			]
		);
		$prefix_text_clip  = $this->df_text_clip(
			[
				'key'         => 'df_prefix',
				'toggle_slug' => 'prefix',
				'tab_slug'    => 'advanced',
			]
		);
		$infix_text_clip   = $this->df_text_clip(
			[
				'key'         => 'df_infix',
				'toggle_slug' => 'infix',
				'tab_slug'    => 'advanced',
			]
		);
		$suffix_text_clip  = $this->df_text_clip(
			[
				'key'         => 'df_suffix',
				'toggle_slug' => 'suffix',
				'tab_slug'    => 'advanced',
			]
		);

		return array_merge(
			$heading,
			$highlighter_settings,
			$divider,
			$divider_background,
			$prefix_max_width,
			$prefix_text_clip,
			$infix_max_width,
			$infix_text_clip,
			$suffix_max_width,
			$suffix_text_clip,
			$prefix_background,
			$infix_background,
			$suffix_background,
			$heading_spacing,
			$prefix_spacing,
			$infix_spacing,
			$suffix_spacing,
			// $highlighter_spacing,
			$divider_container_spacing,
			$divider_spacing,
			$divider_icon_spacing,
			$dual_text_spacing
		);
	}

	public function additional_css_styles( $render_slug ) {
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_background',
				'selector'    => '%%order_class%% .df-heading-divider .df-divider-line',
				'hover'       => '%%order_class%%:hover .df-heading-divider .df-divider-line',
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'prefix_background',
				'selector'    => '%%order_class%% .df-heading .prefix',
				'hover'       => '%%order_class%%:hover .df-heading .prefix',
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'infix_background',
				'selector'    => '%%order_class%% .df-heading .infix',
				'hover'       => '%%order_class%%:hover .df-heading:hover .infix',
			]
		);
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'suffix_background',
				'selector'    => '%%order_class%% .df-heading .suffix',
				'hover'       => '%%order_class%%:hover .df-heading .suffix',
			]
		);

		// heading spacing.
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'heading_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading',
				'hover'       => '%%order_class%%:hover .df-heading',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'heading_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading',
				'hover'       => '%%order_class%%:hover .df-heading',
				'important'   => true,
			]
		);
		// prefix spacing.
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'prefix_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading .prefix',
				'hover'       => '%%order_class%%:hover .df-heading .prefix',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'prefix_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading .prefix',
				'hover'       => '%%order_class%%:hover .df-heading .prefix',
				'important'   => true,
			]
		);
		// infix spacing.
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'infix_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading .infix',
				'hover'       => '%%order_class%%:hover .df-heading .infix',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'infix_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading .infix',
				'hover'       => '%%order_class%%:hover .df-heading .infix',
				'important'   => true,
			]
		);
		// suffix spacing.
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'suffix_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading .suffix',
				'hover'       => '%%order_class%%:hover .df-heading .suffix',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'suffix_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading .suffix',
				'hover'       => '%%order_class%%:hover .df-heading .suffix',
				'important'   => true,
			]
		);

		// divider spacing.
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading-divider .df-divider-line',
				'hover'       => '%%order_class%%:hover .df-heading-divider .df-divider-line',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading-divider .df-divider-line',
				'hover'       => '%%order_class%%:hover .df-heading-divider .df-divider-line',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_container_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading-divider',
				'hover'       => '%%order_class%%:hover .df-heading-divider',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_container_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading-divider',
				'hover'       => '%%order_class%%:hover .df-heading-divider',
				'important'   => true,
			]
		);
		// Divider Icon and Image text spacing.
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_icon_image_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading-divider span',
				'hover'       => '%%order_class%%:hover .df-heading-divider span',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_icon_image_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading-divider img',
				'hover'       => '%%order_class%%:hover .df-heading-divider img',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_icon_image_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading-divider span',
				'hover'       => '%%order_class%%:hover .df-heading-divider span',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_icon_image_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading-divider img',
				'hover'       => '%%order_class%%:hover .df-heading-divider img',
				'important'   => true,
			]
		);
		// dual_text text spacing.
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'dual_text_margin',
				'type'        => 'margin',
				'selector'    => '%%order_class%% .df-heading-dual_text',
				'hover'       => '%%order_class%%:hover .df-heading-dual_text',
				'important'   => true,
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'dual_text_padding',
				'type'        => 'padding',
				'selector'    => '%%order_class%% .df-heading-dual_text',
				'hover'       => '%%order_class%%:hover .df-heading-dual_text',
				'important'   => true,
			]
		);

		// Highlighter styles.
		if ( 'on' !== $this->props['enable_gradient_color'] ) {
			$this->df_process_color(
				[
					'render_slug' => $render_slug,
					'slug'        => 'highlighter_color',
					'type'        => 'stroke',
					'selector'    => '%%order_class%% .df-text-highlight svg path',
					'hover'       => '%%order_class%%:hover .df-text-highlight svg path',
				]
			);
		} else {
			$svg_gradient_color_id = 'gradient_' . $this->props['highlighter_type'] . '_' . $this->get_module_order_class( $render_slug );

			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-text-highlight svg path',
					'declaration' => 'stroke: url(#' . $svg_gradient_color_id . ');',
				]
			);
		}

		$this->df_process_range(
			[
				'render_slug' => $render_slug,
				'slug'        => 'highlighter_stroke_width',
				'type'        => 'stroke-width',
				'selector'    => '%%order_class%% .df-text-highlight svg path',
				'hover'       => '%%order_class%%:hover .df-text-highlight svg path',
				'default'     => '8px',
			]
		);

		// svg scale and transform
		$infix_text_padding_right_desktop = '0px';
		$infix_text_padding_right_tablet  = $infix_text_padding_right_desktop;
		$infix_text_padding_right_phone   = $infix_text_padding_right_tablet;

		if ( isset( $this->props['infix_padding'] ) ) {
			$infix_text_padding_right_desktop = isset( $this->props['infix_padding'] ) && $this->props['infix_padding'] !== '' ? explode( '|', $this->props['infix_padding'] )[1] : '0px';
			$infix_text_padding_right_tablet  = isset( $this->props[ 'infix_padding' . '_tablet' ] ) && $this->props[ 'infix_padding' . '_tablet' ] !== '' ? explode( '|', $this->props[ 'infix_padding' . '_tablet' ] )[1] : $infix_text_padding_right_desktop;
			$infix_text_padding_right_phone   = isset( $this->props[ 'infix_padding' . '_phone' ] ) && $this->props[ 'infix_padding' . '_phone' ] !== '' ? explode( '|', $this->props[ 'infix_padding' . '_phone' ] )[1] : $infix_text_padding_right_tablet;
		}

		$highlighter_size_desktop = isset( $this->props['highlighter_size'] ) && $this->props['highlighter_size'] !== '' ? $this->props['highlighter_size'] : 1;
		$highlighter_size_tablet  = isset( $this->props[ 'highlighter_size' . '_tablet' ] ) && $this->props[ 'highlighter_size' . '_tablet' ] !== '' ? $this->props[ 'highlighter_size' . '_tablet' ] : $highlighter_size_desktop;
		$highlighter_size_phone   = isset( $this->props[ 'highlighter_size' . '_phone' ] ) && $this->props[ 'highlighter_size' . '_phone' ] !== '' ? $this->props[ 'highlighter_size' . '_phone' ] : $highlighter_size_tablet;

		if ( '' === $infix_text_padding_right_desktop ) {
			$infix_text_padding_right_desktop = '0px';
		}

		if ( isset( $highlighter_size_desktop ) || isset( $infix_text_padding_right_desktop ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-text-highlight svg',
					'declaration' => 'transform: translateX(calc(-100% + 10px + ' . $infix_text_padding_right_desktop . ')) scale(' . $highlighter_size_desktop . ');',
				]
			);
		}

		if ( '' === $infix_text_padding_right_tablet ) {
			$infix_text_padding_right_tablet = '0px';
		}

		if ( isset( $highlighter_size_tablet ) || isset( $infix_text_padding_right_tablet ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-text-highlight svg',
					'declaration' => 'transform: translateX(calc(-100% + 10px + ' . $infix_text_padding_right_tablet . ')) scale(' . $highlighter_size_tablet . ');',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				]
			);
		}
		if ( '' === $infix_text_padding_right_phone ) {
			$infix_text_padding_right_phone = '0px';
		}
		if ( isset( $highlighter_size_phone ) || isset( $infix_text_padding_right_phone ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-text-highlight svg',
					'declaration' => 'transform: translateX(calc(-100% + 10px + ' . $infix_text_padding_right_phone . ')) scale(' . $highlighter_size_phone . ');',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				]
			);
		}

		if ( et_builder_is_hover_enabled( 'highlighter_size', $this->props ) && isset( $this->props[ 'highlighter_size' . '__hover' ] ) && '' !== $this->props['highlighter_size'] ) {
			$highlighter_size_hover = $this->props[ 'highlighter_size' . '__hover' ];
			if ( ! empty( $highlighter_size_hover ) ) {
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => '%%order_class%%:hover .df-text-highlight svg',
						'declaration' => 'transform: translateX(calc(-100% + 10px + ' . $infix_text_padding_right_desktop . ')) scale(' . $highlighter_size_hover . ') !important;',
					]
				);
			}
		}

		$this->df_process_range(
			[
				'render_slug' => $render_slug,
				'slug'        => 'highlighter_opacity',
				'type'        => 'opacity',
				'selector'    => '%%order_class%% .df-text-highlight svg',
				'hover'       => '%%order_class%%:hover .df-text-highlight svg',
			]
		);

		if ( 'above' === $this->props['highlighter_position'] ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-text-highlight svg',
					'declaration' => 'z-index: 1 !important;',
				]
			);

			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-texthighlighter-container .df-heading > span',
					'declaration' => 'z-index: 0 !important;',
				]
			);
		}

		$this->df_process_range(
			[
				'render_slug' => $render_slug,
				'slug'        => 'highlighter_vertical_position',
				'type'        => 'top',
				'selector'    => '%%order_class%% .df-text-highlight svg',
				'hover'       => '%%order_class%%:hover .df-text-highlight svg',
			]
		);

		$this->df_process_range(
			[
				'render_slug' => $render_slug,
				'slug'        => 'highlighter_horizontal_position',
				'type'        => 'margin-left',
				'selector'    => '%%order_class%% .df-text-highlight svg',
				'hover'       => '%%order_class%%:hover .df-text-highlight svg',
			]
		);

		// divider styles
		if ( isset( $this->props['divider_style'] ) && ! empty( $this->props['divider_style'] ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider .df-divider-line::before',
					'declaration' => sprintf(
						'border-top-style:%1$s !important;',
						$this->props['divider_style']
					),
				]
			);
		}
		if ( isset( $this->props['divider_color'] ) && ! empty( $this->props['divider_color'] ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider .df-divider-line::before',
					'declaration' => sprintf(
						'border-top-color:%1$s !important;',
						$this->props['divider_color']
					),
				]
			);
		}
		$divider_height = isset( $this->props['divider_height'] ) && ! empty( $this->props['divider_height'] ) ?
			$this->props['divider_height'] : '5px';
		ET_Builder_Element::set_style(
			$render_slug,
			[
				'selector'    => '%%order_class%% .df-heading-divider .df-divider-line',
				'declaration' => sprintf(
					'top:calc(%1$s - %2$s);',
					'50%',
					$this->df_get_div_value( $divider_height )
				),
			]
		);
		if ( isset( $this->props['divider_height_tablet'] ) && ! empty( $this->props['divider_height_tablet'] ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider .df-divider-line',
					'declaration' => sprintf(
						'top:calc(%1$s - %2$s);',
						'50%',
						$this->df_get_div_value( $this->props['divider_height_tablet'] )
					),
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				]
			);
		}
		if ( isset( $this->props['divider_height_phone'] ) && ! empty( $this->props['divider_height_phone'] ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider .df-divider-line',
					'declaration' => sprintf(
						'top:calc(%1$s - %2$s);',
						'50%',
						$this->df_get_div_value( $this->props['divider_height_phone'] )
					),
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				]
			);
		}
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_height',
				'type'        => 'border-top-width',
				'selector'    => '%%order_class%% .df-heading-divider .df-divider-line::before',
				'unit'        => 'px',
				'default'     => '5',
			]
		);
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_height',
				'type'        => 'height',
				'selector'    => '%%order_class%% .df-heading-divider .df-divider-line',
				'unit'        => 'px',
				'default'     => '5',
			]
		);
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_width',
				'type'        => 'max-width',
				'selector'    => '%%order_class%% .df-heading-divider',
				'unit'        => '%',
				'hover'       => '%%order_class%%:hover .df-heading-divider',
				'default'     => '100',
			]
		);
		if ( isset( $this->props['divider_alignment'] ) && ! empty( $this->props['divider_alignment'] ) ) {
			if ( 'center' === $this->props['divider_alignment'] ) {
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => '%%order_class%% .df-heading-divider',
						'declaration' => 'margin: 0 auto;',
					]
				);
			}
			if ( 'right' === $this->props['divider_alignment'] ) {
				ET_Builder_Element::set_style(
					$render_slug,
					[
						'selector'    => '%%order_class%% .df-heading-divider',
						'declaration' => 'margin: 0 0 0 auto;',
					]
				);
			}
		}
		if ( 'on' !== $this->props['use_divider_icon'] && 'on' !== $this->props['use_divider_image'] ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider::before',
					'declaration' => 'position: relative;',
				]
			);
		}
		if ( 'on' === $this->props['use_divider_icon_circle'] ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider .et-pb-icon',
					'declaration' => 'border-radius: 50%;',
				]
			);
		}
		if ( 'on' === $this->props['use_divider_image_circle'] ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider img',
					'declaration' => 'border-radius: 50%;',
				]
			);
		}
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_border_radius',
				'type'        => 'border-radius',
				'selector'    => '%%order_class%% .df-heading-divider .df-divider-line:before',
				'unit'        => 'px',
				'hover'       => '%%order_class%%:hover .df-heading-divider .df-divider-line:before',
				'default'     => '0',
			]
		);
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_border_radius',
				'type'        => 'border-radius',
				'selector'    => '%%order_class%% .df-heading-divider .df-divider-line',
				'unit'        => 'px',
				'hover'       => '%%order_class%%:hover .df-heading-divider .df-divider-line',
				'default'     => '0',
			]
		);
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug'        => 'dvr_icon_font_size',
				'type'        => 'font-size',
				'selector'    => '%%order_class%% .df-heading-divider span',
				'unit'        => 'px',
				'hover'       => '%%order_class%%:hover .df-heading-divider .et-pb-icon',
				'default'     => '18',
			]
		);
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_icon_color',
				'type'        => 'color',
				'selector'    => '%%order_class%% .df-heading-divider .et-pb-icon',
				'hover'       => '%%order_class%%:hover .df-heading-divider .et-pb-icon',
			]
		);
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_icon_bgcolor',
				'type'        => 'background-color',
				'selector'    => '%%order_class%% .df-heading-divider .et-pb-icon',
				'hover'       => '%%order_class%%:hover .df-heading-divider .et-pb-icon',
			]
		);
		$this->df_process_color(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_image_bgcolor',
				'type'        => 'background-color',
				'selector'    => '%%order_class%% .df-heading-divider img',
				'hover'       => '%%order_class%%:hover .df-heading-divider img',
			]
		);
		if ( ! empty( $this->props['divider_icon_alignment'] ) && 'on' === $this->props['use_divider_icon'] ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider',
					'declaration' => sprintf( 'text-align: %1$s;', $this->props['divider_icon_alignment'] ),
				]
			);
		}
		$this->apply_single_value(
			[
				'render_slug' => $render_slug,
				'slug'        => 'divider_image_width',
				'type'        => 'max-width',
				'selector'    => '%%order_class%% .df-heading-divider .divider-image',
				'unit'        => 'px',
				'hover'       => '%%order_class%%:hover .df-heading-divider .divider-image',
				'default'     => '100',
			]
		);
		if ( ! empty( $this->props['divider_image_alignment'] ) && 'on' === $this->props['use_divider_image'] ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%% .df-heading-divider',
					'declaration' => sprintf( 'text-align: %1$s;', $this->props['divider_image_alignment'] ),
				]
			);
		}
		// Display Element
		$this->df_process_string_attr(
			[
				'render_slug' => $render_slug,
				'slug'        => 'title_prefix_block',
				'type'        => 'display',
				'selector'    => '%%order_class%% .df-heading .prefix',
				'hover'       => '%%order_class%%:hover .df-heading .prefix',
				'default'     => 'inline-block',
			]
		);
		$this->df_process_string_attr(
			[
				'render_slug' => $render_slug,
				'slug'        => 'title_infix_block',
				'type'        => 'display',
				'selector'    => '%%order_class%% .df-heading .infix',
				'hover'       => '%%order_class%%:hover .df-heading .infix',
				'default'     => 'inline-block',
			]
		);
		$this->df_process_string_attr(
			[
				'render_slug' => $render_slug,
				'slug'        => 'title_suffix_block',
				'type'        => 'display',
				'selector'    => '%%order_class%% .df-heading .suffix',
				'hover'       => '%%order_class%%:hover .df-heading .suffix',
				'default'     => 'inline-block',
			]
		);

		// process max-width and alignment
		$this->df_process_maxwidth(
			[
				'render_slug' => $render_slug,
				'slug'        => 'prefix',
				'selector'    => '%%order_class%% .df-heading .prefix',
				'hover'       => '%%order_class%%:hover .df-heading .prefix',
				'alignment'   => true,
				'important'   => true,
			]
		);
		$this->df_process_maxwidth(
			[
				'render_slug' => $render_slug,
				'slug'        => 'infix',
				'selector'    => '%%order_class%% .df-heading .infix',
				'hover'       => '%%order_class%%:hover .df-heading .infix',
				'alignment'   => true,
				'important'   => true,
			]
		);
		$this->df_process_maxwidth(
			[
				'render_slug' => $render_slug,
				'slug'        => 'suffix',
				'selector'    => '%%order_class%% .df-heading .suffix',
				'hover'       => '%%order_class%%:hover .df-heading .suffix',
				'alignment'   => true,
				'important'   => true,
			]
		);

		// text clip
		$this->df_process_text_clip(
			[
				'render_slug' => $render_slug,
				'slug'        => 'df_prefix',
				'selector'    => '%%order_class%% .df-heading .prefix',
				'hover'       => '%%order_class%%:hover .df-heading .prefix',
			]
		);
		$this->df_process_text_clip(
			[
				'render_slug' => $render_slug,
				'slug'        => 'df_infix',
				'selector'    => '%%order_class%% .df-heading .infix',
				'hover'       => '%%order_class%%:hover .df-heading .infix',
			]
		);
		$this->df_process_text_clip(
			[
				'render_slug' => $render_slug,
				'slug'        => 'df_suffix',
				'selector'    => '%%order_class%% .df-heading .suffix',
				'hover'       => '%%order_class%%:hover .df-heading .suffix',
			]
		);

		// icon font family
		if ( method_exists( 'ET_Builder_Module_Helper_Style_Processor', 'process_extended_icon' ) ) {
			$this->generate_styles(
				[
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $render_slug,
					'base_attr_name' => 'divider_icon',
					'important'      => true,
					'selector'       => '%%order_class%% .et-pb-icon',
					'processor'      => [
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					],
				]
			);
		}
	}

	public function get_transition_fields_css_props() {
		$fields                 = parent::get_transition_fields_css_props();
		$prefix                 = '%%order_class%% .df-heading .prefix';
		$infix                  = '%%order_class%% .df-heading .infix';
		$suffix                 = '%%order_class%% .df-heading .suffix';
		$heading                = '%%order_class%% .df-heading';
		$divider_line_container = '%%order_class%% .df-heading-divider';
		$divider_line           = '%%order_class%% .df-heading-divider .df-divider-line';
		$divider_icon           = '%%order_class%% .df-heading-divider span';
		$divider_img            = '%%order_class%% .df-heading-divider img';
		$dual_text              = '%%order_class%% .df-heading-dual_text';

		// spacing
		$fields['heading_margin']['margin']   = $heading;
		$fields['heading_padding']['padding'] = $heading;

		$fields['prefix_margin']['margin']   = $prefix;
		$fields['prefix_padding']['padding'] = $prefix;

		$fields['infix_margin']['margin']   = $infix;
		$fields['infix_padding']['padding'] = $infix;

		$fields['suffix_margin']['margin']   = $suffix;
		$fields['suffix_padding']['padding'] = $suffix;

		$fields['divider_margin']['margin']   = $divider_line;
		$fields['divider_padding']['padding'] = $divider_line;

		$fields['divider_container_margin']['margin']   = $divider_line_container;
		$fields['divider_container_padding']['padding'] = $divider_line_container;

		$fields['divider_icon_image_margin']['margin']   = $divider_icon;
		$fields['divider_icon_image_padding']['padding'] = $divider_icon;
		$fields['divider_icon_image_margin']['margin']   = $divider_img;
		$fields['divider_icon_image_padding']['padding'] = $divider_img;

		$fields['dual_text_margin']['margin']   = $dual_text;
		$fields['dual_text_padding']['padding'] = $dual_text;

		// others
		$fields['divider_width']['max-width'] = $divider_line_container;

		$fields['divider_border_radius']['border-radius'] = '%%order_class%% .df-heading-divider .df-divider-line:before';
		$fields['divider_border_radius']['border-radius'] = $divider_line;

		$fields['dvr_icon_font_size']['font-size'] = $divider_icon;

		$fields['highlighter_color']['stroke'] = '%%order_class%% .df-text-highlight svg path';

		$fields['divider_icon_color']['color'] = '%%order_class%% .df-heading-divider .et-pb-icon';

		$fields['divider_icon_bgcolor']['background-color']  = '%%order_class%% .df-heading-divider .et-pb-icon';
		$fields['divider_image_bgcolor']['background-color'] = $divider_img;

		$fields['divider_image_width']['max-width'] = '%%order_class%% .df-heading-divider .divider-image';

		$fields['prefix_maxwidth']['max-width'] = $prefix;
		$fields['infix_maxwidth']['max-width']  = $infix;
		$fields['suffix_maxwidth']['max-width'] = $suffix;

		$fields['highlighter_stroke_width']['stroke-width'] = '%%order_class%% .df-text-highlight svg path';
		// $fields['highlighter_size']['transform']         = '%%order_class%% .df-text-highlight svg';
		$fields['highlighter_opacity']['opacity']                 = '%%order_class%% .df-text-highlight svg';
		$fields['highlighter_vertical_position']['bottom']        = '%%order_class%% .df-text-highlight svg';
		$fields['highlighter_horizontal_position']['margin-left'] = '%%order_class%% .df-text-highlight svg';

		$fields['df_prefix_fill_color']['-webkit-text-fill-color']     = $prefix;
		$fields['df_prefix_fill_color']['-webkit-text-stroke-color']   = $prefix;
		$fields['df_prefix_stroke_width']['-webkit-text-stroke-width'] = $prefix;

		$fields['df_infix_fill_color']['-webkit-text-fill-color']     = $infix;
		$fields['df_infix_fill_color']['-webkit-text-stroke-color']   = $infix;
		$fields['df_infix_stroke_width']['-webkit-text-stroke-width'] = $infix;

		$fields['df_suffix_fill_color']['-webkit-text-fill-color']     = $suffix;
		$fields['df_suffix_fill_color']['-webkit-text-stroke-color']   = $suffix;
		$fields['df_suffix_stroke_width']['-webkit-text-stroke-width'] = $suffix;

		// background
		$fields = $this->df_background_transition(
			[
				'fields'   => $fields,
				'key'      => 'divider_background',
				'selector' => $divider_line,
			]
		);
		$fields = $this->df_background_transition(
			[
				'fields'   => $fields,
				'key'      => 'prefix_background',
				'selector' => $prefix,
			]
		);
		$fields = $this->df_background_transition(
			[
				'fields'   => $fields,
				'key'      => 'infix_background',
				'selector' => $infix,
			]
		);
		$fields = $this->df_background_transition(
			[
				'fields'   => $fields,
				'key'      => 'suffix_background',
				'selector' => $suffix,
			]
		);

		return $fields;
	}

	public function get_custom_css_fields_config() {
		return [
			'highlighter_prefix_css' => [
				'label'    => esc_html__( 'Prefix', 'divi_flash' ),
				'selector' => '%%order_class%% .df-texthighlighter-container .df-heading .prefix',
			],
			'highlighter_infix_css'  => [
				'label'    => esc_html__( 'Infix', 'divi_flash' ),
				'selector' => '%%order_class%% .df-texthighlighter-container .df-heading .infix',
			],
			'highlighter_suffix_css' => [
				'label'    => esc_html__( 'Suffix', 'divi_flash' ),
				'selector' => '%%order_class%% .df-texthighlighter-container .df-heading .suffix',
			],
			'highlighter_svg_css'    => [
				'label'    => esc_html__( 'Highlighter', 'divi_flash' ),
				'selector' => '%%order_class%% .df-text-highlight svg',
			],
		];
	}

	public function render_divider( $position, $value ) {
		$divider_icon = '';
		if ( 'on' === $this->props['use_divider_icon'] ) {
			$divider_icon = ! empty( $this->props['divider_icon'] ) && null !== $this->props['divider_icon'] ?
				sprintf(
					'<span class="et-pb-icon">%1$s</span>',
					html_entity_decode( et_pb_process_font_icon( $this->props['divider_icon'] ) )
				) :
					'<span class="et-pb-icon">1</span>';
		}
		if ( 'on' === $this->props['use_divider_image'] ) {
			$image_alt    = '' !== $this->props['divider_image_alt_text'] ? $this->props['divider_image_alt_text'] : df_image_alt_by_url( $this->props['divider_image'] );
			$divider_icon = ! empty( $this->props['divider_image'] ) && null !== $this->props['divider_image'] ?
				sprintf(
					'<img alt="%2$s" src="%1$s" class="divider-image"/>',
					$this->props['divider_image'],
					$image_alt
				) : '';
		}
		if ( $value === $position ) {
			return 'on' === $this->props['use_divider'] ?
				sprintf( '<div class="df-heading-divider"><div class="df-divider-line"></div>%1$s</div>', $divider_icon ) : '';
		}
	}

	public function highlighter_svg() {
		$jsonString = file_get_contents( DIFL_ADMIN_DIR_PATH . "assets/svg/textHighlighter.json" );
		$svgArray   = json_decode( $jsonString, true );

		if ( empty( $this->props['highlighter_type'] ) ) {
			return $svgArray['TextHighlighterSVG']['line'];
		}

		return $svgArray['TextHighlighterSVG'][ $this->props['highlighter_type'] ];
	}

	public function render( $attrs, $content, $render_slug ) {
		$this->handle_fa_icon();
		wp_enqueue_script( 'df-vivus-svg' );
		wp_enqueue_script( 'df-text-highlighter' );

		$heading_classes  = '';
		$dual_text_title  = '';
		$divider_position = '' !== $this->props['divider_position'] ?
			$this->props['divider_position'] : 'bottom';
		// $title_level  = $this->props['title_level'];
		$title_level  = $this->props['title_tag'];
		$title_prefix = ! empty( $this->props['title_prefix'] ) ?
			sprintf( '<span class="prefix">%1$s</span>', $this->props['title_prefix'] ) : '';

		$title_infix = ! empty( $this->props['title_infix'] ) ?
			sprintf(
				'<span class="infix df-text-highlight">%1$s %2$s</span>',
				$this->props['title_infix'],
				$this->highlighter_svg()// $highlighter_svg
			) : '';

		$title_suffix = ! empty( $this->props['title_suffix'] ) ?
			sprintf( '<span class="suffix">%1$s</span>', $this->props['title_suffix'] ) : '';

		$title = sprintf( '%1$s %2$s %3$s', $title_prefix, $title_infix, $title_suffix );

		$this->additional_css_styles( $render_slug );

		if ( $this->props['use_dual_text'] === 'on' ) {
			$dual_title = sprintf( '%1$s %2$s %3$s', $this->props['title_prefix'], $this->props['title_infix'], $this->props['title_suffix'] );

			if ( $this->props['use_dual_text_custom'] !== 'on' ) {
				$dual_text_title = sprintf(
					'<div class="df-heading-dual_text" data-title="%1$s"></div>',
					wp_strip_all_tags( trim( $dual_title ) )
				);
			} else {
				$dual_text_title = sprintf(
					'<div class="df-heading-dual_text" data-title="%1$s"></div>',
					wp_strip_all_tags( $this->props['custom_text_input'] )
				);
			}

			$heading_classes .= ' has-dual-text';
		}

		$data = [
			'animation'          => $this->props['enable_animation'],
			'animationStart'     => $this->props['anim_start'],
			'viewportPosition'   => $this->props['anim_start_viewport'],
			'type'               => $this->props['highlighter_type'],
			'animTimingFunction' => $this->props['anim_easing'],
			'duration'           => (float) $this->props['anim_duration'],
			'delay'              => (float) $this->props['anim_delay'],
			'loop'               => $this->props['enable_loop'],
			'iteration'          => (int) $this->props['anim_iteration'],
			'iterationGap'       => (int) $this->props['anim_iteration_gap'],
		];

		$data_svg = [
			'isGradient'              => $this->props['enable_gradient_color'],
			'colorStart'              => $this->props['gradient_color_start'],
			'colorEnd'                => $this->props['gradient_color_end'],
			'gradientType'            => $this->props['gradient_type'],
			'gradientDirection'       => $this->props['gradient_direction'],
			'gradientDirectionRadial' => $this->props['gradient_direction_radial'],
			'startPosition'           => $this->props['gradient_start_position'],
			'endPosition'             => $this->props['gradient_end_position'],
		];

		return sprintf(
			'<div class="df-texthighlighter-container %6$s active" data-svg=\'%8$s\' data-settings=\'%7$s\'>
                %3$s
                %5$s
                <%2$s class="df-heading">%1$s</%2$s>
                %4$s
            </div>',
			/**1*/ $title,
			/**2*/ esc_html($title_level ),
			/**3*/ $this->render_divider( 'top', $divider_position ),
			/**4*/ $this->render_divider( 'bottom', $divider_position ),
			/**5*/ $dual_text_title,
			/**6*/ $heading_classes,
			/**7*/ wp_json_encode( $data ),
			/**8*/ wp_json_encode( $data_svg )
		);
	}
}
new DIFL_TextHighlighter();
