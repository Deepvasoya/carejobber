<?php

class DF_TextReveal extends ET_Builder_Module
{
	use DF_UTLS;

	public $slug = 'difl_text_reveal';
	public $vb_support = 'on';
	/**
	 * @var string
	 */
	public $icon_path;
	/**
	 * @var string
	 */
	public $main_css_element_core;

	protected $module_credits = [
		'module_uri' => '',
		'author' => 'DiviFlash',
		'author_uri' => '',
	];

	public function init()
	{
		$this->name = esc_html__('Scroll Text Reveal', 'divi_flash');
		$this->main_css_element = "%%order_class%% .df_text_reveal_main_container";
		$this->main_css_element_core = "%%order_class%%";
		$this->icon_path = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/scroll-text-reveal.svg';
	}
	public function get_settings_modal_toggles()
	{
		return [
			'general' => [
				'toggles' => [
					'toggle_key__content' => et_builder_i18n('Text'),
					'toggle_key__reveal_settings' => esc_html__('Reveal Settings', 'divi_flash'),
				],
			],
			'advanced' => [
				'toggles' => [
					'text' => [
						'title' => et_builder_i18n('Text'),
						'priority' => 45,
						'tabbed_subtoggles' => true,
						'bb_icons_support' => true,
						'sub_toggles' => array(
							'p' => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a' => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
							'ul' => array(
								'name' => 'UL',
								'icon' => 'list',
							),
							'ol' => array(
								'name' => 'OL',
								'icon' => 'numbered-list',
							),
							'quote' => array(
								'name' => 'QUOTE',
								'icon' => 'text-quote',
							),
						),
					],
					'header' => array(
						'title' => esc_html__('Heading Text', 'divi_flash'),
						'priority' => 49,
						'tabbed_subtoggles' => true,
						'sub_toggles' => array(
							'h1' => array(
								'name' => 'H1',
								'icon' => 'text-h1',
							),
							'h2' => array(
								'name' => 'H2',
								'icon' => 'text-h2',
							),
							'h3' => array(
								'name' => 'H3',
								'icon' => 'text-h3',
							),
							'h4' => array(
								'name' => 'H4',
								'icon' => 'text-h4',
							),
							'h5' => array(
								'name' => 'H5',
								'icon' => 'text-h5',
							),
							'h6' => array(
								'name' => 'H6',
								'icon' => 'text-h6',
							),
						),
					),
					'width' => array(
						'title' => et_builder_i18n('Sizing'),
						'priority' => 65,
					),
				],
			],
		];
	}
	public function get_advanced_fields_config()
	{
		$advanced_fields = [];
		$advanced_fields['fonts'] = [
			'text-body' => array(
				'label' => et_builder_i18n('Text'),
				'css' => array(
					'line_height' => "{$this->main_css_element}",
					'color' => "{$this->main_css_element}",
				),
				'line_height' => array(
					'default' => floatval(et_get_option('body_font_height', '1.7')) . 'em',
				),
				'font_size' => array(
					'default' => absint(et_get_option('body_font_size', '14')) . 'px',
				),
				'toggle_slug' => 'text',
				'sub_toggle' => 'p',
				'hide_text_align' => false,
			),
			'link' => array(
				'label' => et_builder_i18n('Link'),
				'css' => array(
					'main' => "{$this->main_css_element} a",
					'color' => "{$this->main_css_element} a",
				),
				'line_height' => array(
					'default' => '1em',
				),
				'font_size' => array(
					'default' => absint(et_get_option('body_font_size', '14')) . 'px',
				),
				'toggle_slug' => 'text',
				'sub_toggle' => 'a',
			),
			'ul' => array(
				'label' => esc_html__('Unordered List', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} ul li",
					'color' => "{$this->main_css_element} ul li, {$this->main_css_element} ol li > ul li",
					'line_height' => "{$this->main_css_element} ul li",
					'item_indent' => "{$this->main_css_element} ul",
				),
				'line_height' => array(
					'default' => '1em',
				),
				'font_size' => array(
					'default' => '14px',
				),
				'toggle_slug' => 'text',
				'sub_toggle' => 'ul',
			),
			'ol' => array(
				'label' => esc_html__('Ordered List', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} ol li",
					'color' => "{$this->main_css_element} ol li",
					'line_height' => "{$this->main_css_element} ol li",
					'item_indent' => "{$this->main_css_element} ol",
				),
				'line_height' => array(
					'default' => '1em',
				),
				'font_size' => array(
					'default' => '14px',
				),
				'toggle_slug' => 'text',
				'sub_toggle' => 'ol',
			),
			'quote' => array(
				'label' => esc_html__('Blockquote', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} blockquote",
					'color' => "{$this->main_css_element} blockquote",
				),
				'line_height' => array(
					'default' => '1em',
				),
				'font_size' => array(
					'default' => '14px',
				),
				'toggle_slug' => 'text',
				'sub_toggle' => 'quote',
			),
			'header' => array(
				'label' => esc_html__('Heading', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} h1",
				),
				'font_size' => array(
					'default' => absint(et_get_option('body_header_size', '30')) . 'px',
				),
				'toggle_slug' => 'header',
				'sub_toggle' => 'h1',
			),
			'header_2' => array(
				'label' => esc_html__('Heading 2', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} h2",
				),
				'font_size' => array(
					'default' => '26px',
				),
				'line_height' => array(
					'default' => '1em',
				),
				'toggle_slug' => 'header',
				'sub_toggle' => 'h2',
			),
			'header_3' => array(
				'label' => esc_html__('Heading 3', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} h3",
				),
				'font_size' => array(
					'default' => '22px',
				),
				'line_height' => array(
					'default' => '1em',
				),
				'toggle_slug' => 'header',
				'sub_toggle' => 'h3',
			),
			'header_4' => array(
				'label' => esc_html__('Heading 4', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} h4",
				),
				'font_size' => array(
					'default' => '18px',
				),
				'line_height' => array(
					'default' => '1em',
				),
				'toggle_slug' => 'header',
				'sub_toggle' => 'h4',
			),
			'header_5' => array(
				'label' => esc_html__('Heading 5', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} h5",
				),
				'font_size' => array(
					'default' => '16px',
				),
				'line_height' => array(
					'default' => '1em',
				),
				'toggle_slug' => 'header',
				'sub_toggle' => 'h5',
			),
			'header_6' => array(
				'label' => esc_html__('Heading 6', 'divi_flash'),
				'css' => array(
					'main' => "{$this->main_css_element} h6",
				),
				'font_size' => array(
					'default' => '14px',
				),
				'line_height' => array(
					'default' => '1em',
				),
				'toggle_slug' => 'header',
				'sub_toggle' => 'h6',
			),
		];
		$advanced_fields['margin_padding'] = [
			'css' => [
				'main' => $this->main_css_element_core,
				'important' => 'all',
			]
		];

		$advanced_fields['background'] = [
			'use_background_image' => true,
			'use_background_color_gradient' => true,
			'use_background_video' => false,
			'use_background_pattern' => false,
			'use_background_mask' => false,
			'css' => [
				'main' => "{$this->main_css_element_core}",
				'important' => 'all',
			],

		];
		$advanced_fields['borders'] = [

			'default' => [
				'css' => [
					'main' => [
						'border_radii' => $this->main_css_element_core,
						'border_styles' => $this->main_css_element_core,
						'border_styles_hover' => "{$this->main_css_element_core}:hover"
					]
				]
			],
		];
		$advanced_fields['box_shadow'] = [
			'default' => [
				'css' => [
					'main' => "{$this->main_css_element_core}",
					'important' => 'all',
				],
			],
		];
		// disable pre-settings 
		$advanced_fields['text'] = false;
		$advanced_fields['module_text'] = false;
		$advanced_fields['filters'] = false;
		$advanced_fields['transform'] = false;
		$advanced_fields['link_options'] = false;

		return $advanced_fields;
	}
	public function get_fields()
	{
		$fields = [];
		$fields['settings__content'] = [
			'label' => esc_html__('Text', 'divi_flash'),
			'type' => 'tiny_mce',
			'default'=>esc_html__( "Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.",'divi_flash' ),
			'toggle_slug' => 'toggle_key__content',
			// 'dynamic_content' => 'text',
		];
		$fields['settings__trigger_type'] = [
			'label' => esc_html__('Trigger Type', 'divi_flash'),
			'type' => 'select',
			'options' => [
				'auto' => esc_html__('Auto', 'divi_flash'),
				'with-scroll' => esc_html__('With Scroll', 'divi_flash'),
				'on-viewport' => esc_html__('On Viewport', 'divi_flash'),
			],
			'default' => 'with-scroll',
			'toggle_slug' => 'toggle_key__reveal_settings',
		];
		$fields['settings__split_content'] = [
			'label' => esc_html__('Split Content', 'divi_flash'),
			'type' => 'select',
			'options' => [
				'df_text_reveal_letter_by_letter' => esc_html__('Letter By Letter', 'divi_flash'),
				'df_text_reveal_word_by_word' => esc_html__('Word By Word', 'divi_flash'),
			],
			'default' => 'df_text_reveal_letter_by_letter',
			'toggle_slug' => 'toggle_key__reveal_settings',
		];
		$fields['settings__reveal_by'] = [
			'label' => esc_html__('Reveal By', 'divi_flash'),
			'type' => 'select',
			'options' => [
				'df_text_reveal_by_opacity_animationr' => esc_html__('Opacity Animation', 'divi_flash'),
				'df_text_reveal_by__dual_color_animation' => esc_html__('Dual Color', 'divi_flash'),
			],
			'default' => 'df_text_reveal_by_opacity_animationr',
			'toggle_slug' => 'toggle_key__reveal_settings',
		];
		$fields['settings__reveal_color'] = [
			'label' => esc_html__('Reveal color', 'divi_flash'),
			'type' => 'color-alpha',
			'default' => '#0038f0',
			'toggle_slug' => 'toggle_key__reveal_settings',
			'show_if' => [
				'settings__reveal_by' => 'df_text_reveal_by__dual_color_animation'
			]
		];
		$fields['settings__reveal_duration'] = [
			'label' => esc_html__('Animation Duration (ms)', 'divi_flash'),
			'type' => 'range',
			'default' => '1000',
			'range_settings' =>
				[
					'min' => 0,
					'max' => 10000,
					'step' => 1,
				]
			,
			'toggle_slug' => 'toggle_key__reveal_settings',
			'show_if' => [
				'settings__trigger_type' => ['auto', 'on-viewport']
			]
		];
		$fields['settings__reveal_delay'] = [
			'label' => esc_html__('Animation Delay (ms)', 'divi_flash'),
			'type' => 'range',
			'default' => 0,
			'range_settings' =>
				[
					'min' => 0,
					'max' => 10000,
					'step' => 1,
				]
			,

			'toggle_slug' => 'toggle_key__reveal_settings',
			'show_if' => [
				'settings__trigger_type' => ['auto', 'on-viewport']
			]
		];
		$fields['settings__reveal_initial_opacity'] = [
			'label' => esc_html__('Initial Opacity', 'divi_flash'),
			'type' => 'range',
			'default' => 0.2,
			'range_settings' =>
				[
					'min' => 0.1,
					'max' => 0.9,
					'step' => 0.01,
					'min_limit' => 0.1,
					'max_limit' => 0.9,
				]
			,
			'toggle_slug' => 'toggle_key__reveal_settings',
		];

		$fields['settings__reveal_viewport_offset_value_top'] = [
			'label' => esc_html__('Viewport offset top', 'divi_flash'),
			'type' => 'range',
			'description' => esc_html__('Unit in percentage,value calculate from viewport bottom ', 'divi_flash'),
			'default' => '0',
			'range_settings' =>
				[
					'min' => '0',
					'max' => '100',
					'step' => '1',
				]
			,
			'toggle_slug' => 'toggle_key__reveal_settings',
			'allowed_units' => array('%', 'px'),
			'validate_unit' => true,
			'default_unit' => '%',
			'show_if' => [
				'settings__trigger_type' => ['on-viewport', 'with-scroll']
			]
		];

		$fields['settings__reveal_viewport_offset_value_bottom'] = [
			'label' => esc_html__('Viewport offset Bottom', 'divi_flash'),
			'type' => 'range',
			'description' => esc_html__('Unit in percentage,value calculate from viewport bottom ', 'divi_flash'),
			'default' => '0',
			'range_settings' =>
				[
					'min' => '0',
					'max' => '100',
					'step' => '1',
				]
			,
			'toggle_slug' => 'toggle_key__reveal_settings',
			'allowed_units' => array('%', 'px'),
			'validate_unit' => true,
			'default_unit' => '%',
			'show_if' => [
				'settings__trigger_type' => ['on-viewport', 'with-scroll']
			]
		];

		return $fields;
	}
	public function additional_css_styles($render_slug)
	{
		if (isset($this->props['settings__reveal_color'])) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector' => "{$this->main_css_element}",
					'declaration' => sprintf(
						'%2$s:%1$s !important;',
						esc_html($this->props['settings__reveal_color']),
						' --secondary-reveal-color'
					),
					// 'media_query' => ET_Builder_Element::get_media_query('max_width_980')
				]
			);
		}
	}
	public function render($attrs, $content, $render_slug)
	{
		$this->additional_css_styles($render_slug);

		wp_enqueue_script('df-text-reveal');
		$content = $this->props['settings__content'];
		$settings__trigger_type = $this->props['settings__trigger_type'];
		$settings__split_content = $this->props['settings__split_content'];
		$settings__reveal_by = $this->props['settings__reveal_by'];
		$settings__reveal_color = $this->props['settings__reveal_color'] ? $this->props['settings__reveal_color'] : '';
		$settings__reveal_duration = $this->props['settings__reveal_duration'] ? $this->props['settings__reveal_duration'] : '';
		$settings__reveal_delay = $this->props['settings__reveal_delay'] ? $this->props['settings__reveal_delay'] : '';
		$settings__reveal_initial_opacity = $this->props['settings__reveal_initial_opacity'] ? $this->props['settings__reveal_initial_opacity'] : '';
		$settings__reveal_viewport_offset_value_top = $this->props['settings__reveal_viewport_offset_value_top'] ? $this->props['settings__reveal_viewport_offset_value_top'] : '';
		$settings__reveal_viewport_offset_value_bottom = $this->props['settings__reveal_viewport_offset_value_bottom'] ? $this->props['settings__reveal_viewport_offset_value_bottom'] : '';

		$args = [
			'settings__reveal_color' => $settings__reveal_color,
			'settings__reveal_duration' => $settings__reveal_duration,
			'settings__reveal_delay' => $settings__reveal_delay,
			'settings__reveal_initial_opacity' => $settings__reveal_initial_opacity,
			'settings__reveal_viewport_offset_value_top' => $settings__reveal_viewport_offset_value_top,
			'settings__reveal_viewport_offset_value_bottom' => $settings__reveal_viewport_offset_value_bottom,
		];

		return sprintf(
			'<div data-settings="%5$s" class="df_text_reveal_main_container %3$s  %2$s %4$s" >%1$s</div>',
			$content,
			esc_html($settings__trigger_type),
			esc_html($settings__split_content),
			esc_html($settings__reveal_by),
			esc_attr(wp_json_encode($args))
		);
	}
}
;

new DF_TextReveal;
