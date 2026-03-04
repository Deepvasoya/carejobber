<?php

class AvatarStack extends ET_Builder_Module {
	use \DIFL\Handler\Fa_Icon_Handler;
	use DF_UTLS;
	public $icon_path;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	function init() {
		$this->name             = esc_html__( 'Stack', 'divi_flash' );
		$this->plural           = esc_html__( 'Stacks', 'divi_flash' );
		$this->slug             = 'difl_avatar_stack';
		$this->vb_support       = 'on';
		$this->icon_path        = DIFL_ADMIN_DIR_PATH . 'dashboard/static/module-icons/stack.svg';
		$this->child_slug       = 'difl_avatar_stack_item';
		$this->child_item_text  = esc_html__( 'Stack Item', 'divi_flash' );

		$this->main_css_element = "%%order_class%%";
	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'content_main'             => esc_html__( 'Stack Settings', 'divi_flash' ),
					'content_stack_animations' => esc_html__( 'Stack Animations', 'divi_flash' ),
					'content_text'             => esc_html__( 'Text', 'divi_flash' ),
					'content_tooltip'          => esc_html__( 'Tooltip Settings', 'divi_flash' ),
				],
			],
			'advanced' => [
				'toggles' => [
					'design_child_icon'           => esc_html__( 'Icon', 'divi_flash' ),
					'design_child_image'          => esc_html__( 'Image', 'divi_flash' ),
					'design_child_text'           => [
						'title'             => esc_html__( 'Text', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => [
							'title'    => [
								'name' => 'Title',
							],
							'subtitle' => [
								'name' => 'Sub-Title',
							]
						],
					],
					'design_child_text_container' => esc_html__( 'Text Container', 'divi_flash' ),
					'design_child_rating'         => esc_html__( 'Rating', 'divi_flash' ),
					'design_tooltip'              => esc_html__( 'Tooltip', 'divi_flash' ),
					'design_tooltip_text'         => [
						'title'             => esc_html__( ' Tooltip Text', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'sub_toggles'       => [
							'body'  => [
								'name' => 'p',
								'icon' => 'text-left',
							],
							'a'     => [
								'name' => 'A',
								'icon' => 'text-link',
							],
							'ul'    => [
								'name' => 'UL',
								'icon' => 'list',
							],
							'ol'    => [
								'name' => 'OL',
								'icon' => 'numbered-list',
							],
							'quote' => [
								'name' => 'QUOTE',
								'icon' => 'text-quote',
							]
						],
					],
					'design_tooltip_header'       => [
						'title'             => esc_html__( 'Tooltip Heading Text', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => [
							'h1' => [
								'name' => 'H1',
								'icon' => 'text-h1',
							],
							'h2' => [
								'name' => 'H2',
								'icon' => 'text-h2',
							],
							'h3' => [
								'name' => 'H3',
								'icon' => 'text-h3',
							],
							'h4' => [
								'name' => 'H4',
								'icon' => 'text-h4',
							],
							'h5' => [
								'name' => 'H5',
								'icon' => 'text-h5',
							],
							'h6' => [
								'name' => 'H6',
								'icon' => 'text-h6',
							],
						],
					],
					'design_border'               => [
						'title'             => esc_html__( 'Border', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => [
							'module' => [
								'name' => 'Module',
							],
							'icon'   => [
								'name' => 'Icon',
							],
							'media'  => [
								'name' => 'Image',
							],
							'rating' => [
								'name' => 'Rate',
							],
							'text'   => [
								'name' => 'Text',
							],
						],
					],
					'design_box_shadow'           => [
						'title'             => esc_html__( 'Box Shadow', 'divi_flash' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => [
							'module' => [
								'name' => 'Module',
							],
							'icon'   => [
								'name' => 'Icon',
							],
							'media'  => [
								'name' => 'Image',
							],
							'rating' => [
								'name' => 'Rate',
							],
							'text'   => [
								'name' => 'Text',
							],
						],
					],
				],
			],
		];
	}

	public function get_fields() {
		$content_main                       = [
			'field_stack_spacing' => [
				'label'           => esc_html__( 'Stack Spacing', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'hover'           => 'tabs',
				'default'         => '0px',
				'default_unit'    => 'px',
				'range_settings ' => [
					'min'  => '-100',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_main',
			],
		];
		$content_anim_item_hover_translate  = [
			'field_item_translate_enable' => [
				'label'       => esc_html__( 'Enable Translate', 'divi_flash' ),
				'description' => esc_html__( 'Translate On/ Off.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_stack_animations'
			],
			'field_item_translate_x'      => [
				'label'           => esc_html__( 'Translate X', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0px',
				'default_unit'    => 'px',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_translate_enable' => 'on'
				],
			],
			'field_item_translate_y'      => [
				'label'           => esc_html__( 'Translate Y', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0px',
				'default_unit'    => 'px',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_translate_enable' => 'on'
				],
			],
		];
		$content_anim_item_hover_rotate     = [
			'field_item_rotate_enable' => [
				'label'       => esc_html__( 'Enable Rotate', 'divi_flash' ),
				'description' => esc_html__( 'Rotate On/ Off.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_stack_animations'
			],
			'field_item_rotate_x'      => [
				'label'           => esc_html__( 'Rotate X', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0deg',
				'default_unit'    => 'deg',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_rotate_enable' => 'on'
				],
			],
			'field_item_rotate_y'      => [
				'label'           => esc_html__( 'Rotate Y', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0deg',
				'default_unit'    => 'deg',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_rotate_enable' => 'on'
				],
			],
			'field_item_rotate_z'      => [
				'label'           => esc_html__( 'Rotate Z', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0deg',
				'default_unit'    => 'deg',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_rotate_enable' => 'on'
				],
			],
		];
		$content_anim_item_hover_scale      = [
			'field_item_scale_enable' => [
				'label'       => esc_html__( 'Enable Scaling', 'divi_flash' ),
				'description' => esc_html__( 'Scaling On/ Off.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_stack_animations'
			],
			'field_item_scale_x'      => [
				'label'           => esc_html__( 'Scale X', 'divi_flash' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '1.0',
				'unitless'        => true,
				'range_settings' => [
					'min'  => '0.0',
					'max'  => '100.0',
					'step' => '0.1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_scale_enable' => 'on'
				],
			],
			'field_item_scale_y'      => [
				'label'           => esc_html__( 'Scale Y', 'divi_flash' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '1.0',
				'unitless'        => true,
				'range_settings' => [
					'min'  => '1.0',
					'max'  => '100.0',
					'step' => '0.1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_scale_enable' => 'on'
				],
			],
		];
		$content_anim_item_hover_skew       = [
			'field_item_skew_enable' => [
				'label'       => esc_html__( 'Enable Skew', 'divi_flash' ),
				'description' => esc_html__( 'Skew On/ Off.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_stack_animations'
			],
			'field_item_skew_x'      => [
				'label'           => esc_html__( 'Skew X', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0deg',
				'default_unit'    => 'deg',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_skew_enable' => 'on'
				],
			],
			'field_item_skew_y'      => [
				'label'           => esc_html__( 'Skew Y', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0deg',
				'default_unit'    => 'deg',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_item_skew_enable' => 'on'
				],
			],
		];
		$content_anim_item_hover_transition = [
			'field_item_transition_enable' => [
				'label'       => esc_html__( 'Enable Transition', 'divi_flash' ),
				'description' => esc_html__( 'Transition On/ Off.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_stack_animations'
			],
			'field_item_trans_duration'    => [
				'label'            => esc_html__( 'Duration [ms]', 'divi_flash' ),
				'type'             => 'text',
				'mobile_options'   => false,
				'responsive'       => false,
				'default'          => '300',
				'default_on_front' => '300',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'content_stack_animations',
				'show_if'          => [
					'field_item_transition_enable' => 'on'
				],
			],
			'field_item_trans_delay'       => [
				'label'            => esc_html__( 'Delay [ms]', 'divi_flash' ),
				'type'             => 'text',
				'mobile_options'   => false,
				'responsive'       => false,
				'default'          => '200',
				'default_on_front' => '200',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'content_stack_animations',
				'show_if'          => [
					'field_item_transition_enable' => 'on'
				],
			],
			'field_item_trans_easing'      => [
				'label'       => esc_html__( 'Easing', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'ease'        => esc_html__( 'Ease', 'divi_flash' ),
					'linear'      => esc_html__( 'Linear', 'divi_flash' ),
					'ease-in'     => esc_html__( 'Ease-In', 'divi_flash' ),
					'ease-out'    => esc_html__( 'Ease-Out', 'divi_flash' ),
					'ease-in-out' => esc_html__( 'Ease-In-Out', 'divi_flash' ),
				],
				'default'     => 'ease-out',
				'tab_slug'    => 'general',
				'toggle_slug' => 'content_stack_animations',
				'show_if'     => [
					'field_item_transition_enable' => 'on'
				],
			],
		];
		$content_anim_stack_hover_translate = [
			'field_stack_translate_enable' => [
				'label'       => esc_html__( 'Enable Translate', 'divi_flash' ),
				'description' => esc_html__( 'Translate On/ Off.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_stack_animations'
			],
			'field_stack_translate_x'      => [
				'label'           => esc_html__( 'Translate X', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0px',
				'default_unit'    => 'px',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_stack_translate_enable' => 'on'
				],
			],
			'field_stack_translate_y'      => [
				'label'           => esc_html__( 'Translate Y', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0px',
				'default_unit'    => 'px',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_stack_translate_enable' => 'on'
				],
			],
		];
		$content_anim_stack_hover_rotate    = [
			'field_stack_rotate_enable' => [
				'label'       => esc_html__( 'Enable Rotate', 'divi_flash' ),
				'description' => esc_html__( 'Rotate On/ Off.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_stack_animations'
			],
			'field_stack_rotate_x'      => [
				'label'           => esc_html__( 'Rotate X', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0deg',
				'default_unit'    => 'deg',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_stack_rotate_enable' => 'on'
				],
			],
			'field_stack_rotate_y'      => [
				'label'           => esc_html__( 'Rotate Y', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0deg',
				'default_unit'    => 'deg',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_stack_rotate_enable' => 'on'
				],
			],
			'field_stack_rotate_z'      => [
				'label'           => esc_html__( 'Rotate Z', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => false,
				'responsive'      => false,
				'default'         => '0deg',
				'default_unit'    => 'deg',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content_stack_animations',
				'show_if'         => [
					'field_stack_rotate_enable' => 'on'
				],
			],
		];
		$content_stack_animations           = [
			'stack_animations' => [
				'label'               => esc_html__( '', 'divi_flash' ),
				'type'                => 'composite',
				'tab_slug'            => 'general',
				'toggle_slug'         => 'content_stack_animations',
				'composite_type'      => 'default',
				'composite_structure' => [
					'tab_item_hover'  => [
						'type'     => 'text',
						'label'    => esc_html__( 'Item Hover', 'divi_flash' ),
						'controls' => array_merge(
							$content_anim_item_hover_translate,
							$content_anim_item_hover_rotate,
							$content_anim_item_hover_scale,
							$content_anim_item_hover_skew,
							$content_anim_item_hover_transition
						),
					],
					'tab_stack_hover' => [
						'type'     => 'text',
						'label'    => esc_html__( 'Stack Hover', 'divi_flash' ),
						'controls' => array_merge(
							$content_anim_stack_hover_translate,
							$content_anim_stack_hover_rotate
						),
					]
				],
			],
		];
		$icon                               = [
			'field_icon_color' => [
				'default'        => et_builder_accent_color(),
				'label'          => esc_html__( 'Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your icon.', 'divi_flash' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'design_child_icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			],
			'field_icon_size'  => [
				'label'           => esc_html__( 'Size', 'divi_flash' ),
				'default'         => '30px',
				'range_settings'  => [
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				],
				'description'     => esc_html__( 'Here you can choose Icon width.', 'divi_flash' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'design_child_icon',
				'validate_unit'   => true,
				'allowed_units'   => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
				'responsive'      => true,
				'mobile_options'  => true,
				'sticky'          => true,
				'hover'           => 'tabs',
			],
			'field_icon_background' => [
				'default'        => '#ffffff',
				'label'          => esc_html__( 'Background Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your icon.', 'divi_flash' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'design_child_icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			],
		];
		$text                               = [
			'field_text_background' => [
				'default'        => '#ffffff',
				'label'          => esc_html__( 'Background Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your text container background.', 'divi_flash' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'design_child_text_container',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			],
		];
		$rating                             = [
			'field_rating_position'  => [
				'label'            => esc_html__( 'Position', 'divi_flash' ),
				'type'             => 'select',
				'options'          => [
					'start'  => esc_html__( 'Top', 'divi_flash' ),
					'center' => esc_html__( 'Center', 'divi_flash' ),
					'end'    => esc_html__( 'Bottom', 'divi_flash' ),
				],
				'default'          => 'center',
				'default_on_front' => 'center',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'design_child_rating',
			],
			'field_rating_alignment' => [
				'label'            => esc_html__( 'Alignment', 'divi_flash' ),
				'type'             => 'text_align',
				'options'          => et_builder_get_text_orientation_options( [ 'justified' ] ),
				'default'          => 'left',
				'default_on_front' => 'left',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'design_child_rating',
			],
			'field_rating_icon_size' => [
				'label'           => esc_html__( 'Icon size', 'divi_flash' ),
				'type'            => 'range',
				'mobile_options'  => true,
				'responsive'      => true,
				'default'         => '14px',
				'default_unit'    => 'px',
				'range_settings ' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'design_child_rating',
			],
			'field_rating_color'     => [
				'label'       => esc_html__( 'Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'hover'       => 'tabs',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'design_child_rating',
			],
			'field_blank_color'      => [
				'label'       => esc_html__( 'Blank Color', 'divi_flash' ),
				'type'        => 'color-alpha',
				'hover'       => 'tabs',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'design_child_rating',
			],
			'field_rating_background'  => [
				'default'        => '#ffffff',
				'label'          => esc_html__( 'Background Color', 'divi_flash' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your rating container background.', 'divi_flash' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'design_child_rating',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			],
		];

		/*------ Spacing ------*/
		$icon_margin   = $this->add_margin_padding( [
			'title'       => 'Icon',
			'key'         => 'icon_container',
			'toggle_slug' => 'margin_padding',
		] );
		$media_margin  = $this->add_margin_padding( [
			'title'       => 'Image',
			'key'         => 'media_container',
			'toggle_slug' => 'margin_padding',
		] );
		$rating_margin = $this->add_margin_padding( [
			'title'       => 'Rating',
			'key'         => 'rating_container',
			'toggle_slug' => 'margin_padding',
		] );
		$text_margin   = $this->add_margin_padding( [
			'title'       => 'Text',
			'key'         => 'text_container',
			'toggle_slug' => 'margin_padding',
		] );

		/*------- Tooltip ------*/
		$content_tooltip = [
			'field_tooltip_enable'               => [
				'label'       => esc_html__( 'Tooltip', 'divi_flash' ),
				'description' => esc_html__( 'Tooltip On/ Off.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_tooltip'
			],
			'field_tooltip_arrow'                => [
				'label'       => esc_html__( 'Arrow', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'on',
				'toggle_slug' => 'content_tooltip',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			],
			'field_tooltip_placement'            => [
				'label'       => esc_html__( 'Placement', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'top'          => esc_html__( 'Top', 'divi_flash' ),
					'top-start'    => esc_html__( 'Top Start', 'divi_flash' ),
					'top-end'      => esc_html__( 'Top End', 'divi_flash' ),
					'right'        => esc_html__( 'Right', 'divi_flash' ),
					'right-start'  => esc_html__( 'Right Start', 'divi_flash' ),
					'right-end'    => esc_html__( 'Right End', 'divi_flash' ),
					'bottom'       => esc_html__( 'Bottom', 'divi_flash' ),
					'bottom-start' => esc_html__( 'Bottom Start', 'divi_flash' ),
					'bottom-end'   => esc_html__( 'Bottom End', 'divi_flash' ),
					'left'         => esc_html__( 'Left', 'divi_flash' ),
					'left-start'   => esc_html__( 'Left Start', 'divi_flash' ),
					'left-end'     => esc_html__( 'Left End', 'divi_flash' )
				],
				'default'     => 'top',
				'toggle_slug' => 'content_tooltip',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			],
			'field_tooltip_animation'            => [
				'label'       => esc_html__( 'Animation', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'fade'         => esc_html__( 'Fade', 'divi_flash' ),
					'scale'        => esc_html__( 'Scale', 'divi_flash' ),
					'rotate'       => esc_html__( 'Rotate', 'divi_flash' ),
					'shift-away'   => esc_html__( 'Shift-away', 'divi_flash' ),
					'shift-toward' => esc_html__( 'Shift-toward', 'divi_flash' ),
					'perspective'  => esc_html__( 'Perspective', 'divi_flash' )
				],
				'default'     => 'fade',
				'toggle_slug' => 'content_tooltip',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			],
			'field_tooltip_trigger'              => [
				'label'       => esc_html__( 'Trigger', 'divi_flash' ),
				'type'        => 'select',
				'options'     => [
					'mouseenter focus' => esc_html__( 'Hover', 'divi_flash' ),
					'click'            => esc_html__( 'Click', 'divi_flash' ),
					'mouseenter click' => esc_html__( 'Hover And Click', 'divi_flash' )
				],
				'default'     => 'mouseenter focus',
				'toggle_slug' => 'content_tooltip',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			],
			'field_tooltip_interactive'          => [
				'label'       => esc_html__( 'Hover Over Tooltip', 'divi_flash' ),
				'description' => esc_html__( 'Tooltip allowing you to hover over and click inside it.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'on',
				'toggle_slug' => 'content_tooltip',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				]
			],
			'field_tooltip_interactive_border'   => [
				'label'          => esc_html__( 'Tooltip Hover Area', 'divi_flash' ),
				'description'    => esc_html__( 'Determines the size of the invisible border around the tooltip that will prevent it from hiding if the cursor left it.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'content_tooltip',
				'default'        => 2,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1'
				],
				'show_if'        => [
					'field_tooltip_interactive' => 'on',
					'field_tooltip_enable'      => 'on'
				],
			],
			'field_tooltip_content_delay' => [
				'label'          => esc_html__( 'Tooltip Content Delay [ms]', 'divi_flash' ),
				'description'    => esc_html__( 'Determines the time in ms to show the Tooltip content when triggger.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'content_tooltip',
				'default'        => 300,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => 0,
					'step' => 100,
					'min_limit' => 0,
				],
				'show_if'        => [
					'field_tooltip_enable'      => 'on'
				],
			],
			'field_tooltip_interactive_debounce' => [
				'label'          => esc_html__( 'Tooltip Content Hide Delay', 'divi_flash' ),
				'description'    => esc_html__( 'Determines the time in ms to debounce the Tooltip content hide handler when the cursor leaves.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'content_tooltip',
				'default'        => 0,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '1000',
					'step' => '10'
				],
				'show_if'        => [
					'field_tooltip_interactive' => 'on',
					'field_tooltip_enable'      => 'on'
				],
			],
			'field_tooltip_follow_cursor'        => [
				'label'       => esc_html__( 'Follow Cursor', 'divi_flash' ),
				'description' => esc_html__( 'Tooltip move with mouse courser.', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_tooltip',
				'show_if'     => [
					'field_tooltip_enable'  => 'on',
					'field_tooltip_trigger' => 'mouseenter focus'
				],
			],
			'field_tooltip_custom_maxwidth'      => [
				'label'          => esc_html__( 'Max Width', 'divi_flash' ),
				'description'    => esc_html__( 'Specifies the maximum width of the tooltip. Useful to prevent it from being too horizontally wide to read.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'content_tooltip',
				'default'        => 350,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '1000',
					'step' => '1'
				],
				'show_if'        => [
					'field_tooltip_enable' => 'on'
				]
			],
			'field_tooltip_offset_enable'        => [
				'label'       => esc_html__( 'Tooltip Distance', 'divi_flash' ),
				'description' => esc_html__( 'Displaces the tippy from its reference element in pixels (skidding and distance).', 'divi_flash' ),
				'type'        => 'yes_no_button',
				'options'     => [
					'on'  => esc_html__( 'On', 'divi_flash' ),
					'off' => esc_html__( 'Off', 'divi_flash' )
				],
				'default'     => 'off',
				'toggle_slug' => 'content_tooltip',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			],
			'field_tooltip_offset_skidding'      => [
				'label'          => esc_html__( 'Tooltip Horizontal Position', 'divi_flash' ),
				'description'    => esc_html__( 'Tooltip horizontal distance length from element to tooltip.', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'content_tooltip',
				'default'        => 0,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '1000',
					'step' => '1'
				],
				'show_if'        => [
					'field_tooltip_offset_enable' => 'on',
					'field_tooltip_enable'        => 'on'
				],
			],
			'field_tooltip_offset_distance'      => [
				'label'          => esc_html__( 'Tooltip Vertical Position', 'divi_flash' ),
				'description'    => esc_html__( 'Tooltip vertical distance length from spot to tooltip', 'divi_flash' ),
				'type'           => 'range',
				'toggle_slug'    => 'content_tooltip',
				'default'        => 10,
				'allowed_units'  => [],
				'validate_unit'  => false,
				'range_settings' => [
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				],
				'show_if'        => [
					'field_tooltip_offset_enable' => 'on',
					'field_tooltip_enable'        => 'on'
				],
			],
		];
		$design_tooltip  = array_merge(
			[
				'field_tooltip_arrow_color' => [
					'label'       => esc_html__( 'Tooltip Arrow Color', 'divi_flash' ),
					'type'        => 'color-alpha',
					'toggle_slug' => 'design_tooltip',
					'tab_slug'    => 'advanced',
					'hover'       => 'tabs',
					'show_if'     => [
						'field_tooltip_enable' => 'on'
					],
				]
			],
			$this->df_add_bg_field( [
				'label'       => 'Background',
				'key'         => 'field_tooltip_background',
				'toggle_slug' => 'design_tooltip',
				'tab_slug'    => 'advanced',
				'image'       => false,
				'hover'       => 'tabs',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			] ),
			$this->add_margin_padding( [
				'title'       => 'Tooltips',
				'key'         => 'tooltips',
				'toggle_slug' => 'margin_padding',
				'tab_slug'    => 'advanced',
				'option'      => 'padding',
				'show_if'     => [
					'field_tooltip_enable' => 'on'
				],
			] )
		);

		return array_merge(
			$content_main,
			$content_stack_animations,
			$icon,
			$text,
			$rating,
			$icon_margin,
			$media_margin,
			$rating_margin,
			$text_margin,
			$content_tooltip,
			$design_tooltip
		);
	}

	public function get_advanced_fields_config() {
		$advanced_fields = [];

		$advanced_fields['height']                = [
			'css' => [
				'main'      => "{$this->main_css_element}",
				'important' => 'all',
			],
		];
		$advanced_fields['max_width']             = [
			'css' => [
				'main'             => "{$this->main_css_element}",
				'module_alignment' => "{$this->main_css_element} > div:first-of-type",
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

		$advanced_fields['text']    = false;
		$advanced_fields['filters'] = false;
//		$advanced_fields['link_options'] = true;

		/*------ Icon -------*/
		$advanced_fields['borders']['icon']            = [
			'css'         => [
				'main' => [
					'border_radii'  => "
					{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_rating, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
					'border_styles' => "
					{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_rating, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
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
		$advanced_fields['box_shadow']['icon']         = [
			'css'         => [
				'main'    => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_rating, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
				'overlay' => 'inset',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_icon',
		];
		$advanced_fields['height']['extra']['icon']    = [
			'options'        => [
				'height' => [
					'label'          => esc_html__( 'Icon Container Height', 'divi_flash' ),
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
					'default_unit'   => 'px',
					'default'        => '80px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'design_child_icon',
				]
			],
			'use_max_height' => false,
			'use_min_height' => false,
			'css'            => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_icon",
			],
		];
		$advanced_fields['max_width']['extra']['icon'] = [
			'options'              => [
				'width' => [
					'label'          => esc_html__( 'Icon Container Width', 'divi_flash' ),
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
					'default_unit'   => 'px',
					'default'        => '80px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'design_child_icon',
				],
			],
			'use_max_width'        => false,
			'use_module_alignment' => false,
			'css'                  => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_icon",
			],
		];

		/*------ Media -------*/
		$advanced_fields['borders']['media']            = [
			'css'         => [
				'main' => [
					'border_radii'  => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_rating, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
					'border_styles' => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_rating, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
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
			'toggle_slug' => 'design_child_image',
		];
		$advanced_fields['box_shadow']['media']         = [
			'css'         => [
				'main'    => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_rating, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
				'overlay' => 'inset',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_image',
		];
		$advanced_fields['height']['extra']['media']    = [
			'options'        => [
				'height' => [
					'label'          => esc_html__( 'Image Container Height', 'divi_flash' ),
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
					'default_unit'   => 'px',
					'default'        => '80px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'design_child_image',
				]
			],
			'use_max_height' => false,
			'use_min_height' => false,
			'css'            => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_media",
			],
		];
		$advanced_fields['max_width']['extra']['media'] = [
			'options'              => [
				'width' => [
					'label'          => esc_html__( 'Image Container Width', 'divi_flash' ),
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
					'default_unit'   => 'px',
					'default'        => '80px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'design_child_image',
				],
			],
			'use_max_width'        => false,
			'use_module_alignment' => false,
			'css'                  => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_media",
			],
		];

		/*------ Text -------*/
		$advanced_fields['borders']['text']            = [
			'css'         => [
				'main' => [
					'border_radii'  => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_rating)) .difl_avatar_stack_item_wrapper",
					'border_styles' => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_rating)) .difl_avatar_stack_item_wrapper",
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
			'toggle_slug' => 'design_child_text_container',
		];
		$advanced_fields['box_shadow']['text']         = [
			'css'         => [
				'main'    => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_rating)) .difl_avatar_stack_item_wrapper",
				'overlay' => 'inset',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_text_container',
		];
		$advanced_fields['fonts']['text_title']        = [
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_child_text',
			'sub_toggle'       => 'title',
			'hide_text_shadow' => true,
			'header_level'     => [
				'default' => 'h4',
			],
			'line_height'      => [
				'default' => '1em',
			],
			'font_size'        => [
				'default' => '20px',
			],
			'css'              => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_text .difl_avatar_stack_text_container .difl_avatar_stack_text_title",
			],
		];
		$advanced_fields['fonts']['text_subtitle']     = [
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_child_text',
			'sub_toggle'       => 'subtitle',
			'hide_text_shadow' => true,
			'header_level'     => [
				'default' => 'h6',
			],
			'line_height'      => [
				'default' => '1em',
			],
			'font_size'        => [
				'default' => '20px',
			],
			'css'              => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_text .difl_avatar_stack_text_container .difl_avatar_stack_text_subtitle",
			],
		];
		$advanced_fields['height']['extra']['text']    = [
			'options'        => [
				'height' => [
					'label'          => esc_html__( 'Text Container Height', 'divi_flash' ),
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
					'default_unit'   => 'px',
					'default'        => '80px',
//					'default_tablet' => '',
//					'default_phone'  => '',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'design_child_text_container',
				]
			],
			'use_max_height' => false,
			'use_min_height' => false,
			'css'            => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_text",
			],
		];
		$advanced_fields['max_width']['extra']['text'] = [
			'options'              => [
				'width' => [
					'label'          => esc_html__( 'Text Container Width', 'divi_flash' ),
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
					'default_unit'   => 'px',
					'default'        => '80px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'design_child_text_container',
				],
			],
			'use_max_width'        => false,
			'use_module_alignment' => false,
			'css'                  => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_text",
			],
		];

		/*------ Rating -------*/
		$advanced_fields['borders']['rating']            = [
			'css'         => [
				'main' => [
					'border_radii'  => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
					'border_styles' => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
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
			'toggle_slug' => 'design_child_rating',
		];
		$advanced_fields['box_shadow']['rating']         = [
			'css'         => [
				'main'    => "{$this->main_css_element} .difl_avatar_stack_item:not(:has(.difl_avatar_stack_item_wrapper.has_icon, .difl_avatar_stack_item_wrapper.has_media, .difl_avatar_stack_item_wrapper.has_text)) .difl_avatar_stack_item_wrapper",
				'overlay' => 'inset',
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'design_child_rating',
		];
		$advanced_fields['fonts']['rating_label']        = [
			'label'            => esc_html__( 'Label', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_child_rating',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '1em',
			],
			'font_size'        => [
				'default' => '14px',
			],
			'css'              => [
				'main'      => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating_label",
			],
		];
		$advanced_fields['height']['extra']['rating']    = [
			'options'        => [
				'height' => [
					'label'          => esc_html__( 'Rating Container Height', 'divi_flash' ),
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
					'default_unit'   => 'px',
					'default'        => '80px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'design_child_rating',
				]
			],
			'use_max_height' => false,
			'use_min_height' => false,
			'css'            => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating",
			],
		];
		$advanced_fields['max_width']['extra']['rating'] = [
			'options'              => [
				'width' => [
					'label'          => esc_html__( 'Rating Container Width', 'divi_flash' ),
					'range_settings' => [
						'min'  => 1,
						'max'  => 500,
						'step' => 1,
					],
					'hover'          => false,
					'fixed_unit'     => 'px',
					'default_unit'   => 'px',
					'default'        => '80px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'design_child_rating',
				],
			],
			'use_max_width'        => false,
			'use_module_alignment' => false,
			'css'                  => [
				'main' => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating",
			],
		];

		/*------ Tooltip -------*/
		$advanced_fields['fonts']['tooltip_text_a']           = [
			'label'            => esc_html__( 'Link', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'a',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] a",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover a",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_ul']          = [
			'label'            => esc_html__( 'Ul', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'ul',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] ul li",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover ul li",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_ol']          = [
			'label'            => esc_html__( 'Ol', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'ol',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] ol li",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover ol li",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h1']          = [
			'label'            => esc_html__( 'H1', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h1',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '32px',
			],
			'font_size'        => [
				'default' => '32px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h1",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h1",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h2']          = [
			'label'            => esc_html__( 'H2', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h2',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '28px',
			],
			'font_size'        => [
				'default' => '28px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h2",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h2",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h3']          = [
			'label'            => esc_html__( 'H3', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h3',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '24px',
			],
			'font_size'        => [
				'default' => '24px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h3",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h3",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h4']          = [
			'label'            => esc_html__( 'H4', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h4',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '20px',
			],
			'font_size'        => [
				'default' => '20px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h4",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h4",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h5']          = [
			'label'            => esc_html__( 'H5', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h5',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '16px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h5",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h5",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_h6']          = [
			'label'            => esc_html__( 'H6', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_header',
			'sub_toggle'       => 'h6',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '14px',
			],
			'font_size'        => [
				'default' => '14px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] h6",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover h6",
				'important' => 'all'
			]
		];
		$advanced_fields['fonts']['tooltip_text_body']        = [
			'label'            => esc_html__( 'Body', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'body',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}']",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover",
				'important' => 'all'
			]
		];
		$advanced_fields['borders']['tooltips_border']        = [
			'css'         => [
				'main' => [
					'border_radii'        => ".tippy-box[data-theme~='{$this->main_css_element}']",
					'border_styles'       => ".tippy-box[data-theme~='{$this->main_css_element}']",
					'border_styles_hover' => ".tippy-box[data-theme~='{$this->main_css_element}']:hover"
				]
			],
			'toggle_slug' => 'design_tooltip',
			'tab_slug'    => 'advanced'
		];
		$advanced_fields['fonts']['tooltip_text_quote']       = [
			'label'            => esc_html__( 'Quote', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'design_tooltip_text',
			'sub_toggle'       => 'quote',
			'hide_text_shadow' => true,
			'line_height'      => [
				'default' => '18px',
			],
			'font_size'        => [
				'default' => '16px',
			],
			'css'              => [
				'main'      => ".tippy-box[data-theme~='{$this->main_css_element}'] blockquote",
				'hover'     => ".tippy-box[data-theme~='{$this->main_css_element}']:hover blockquote",
				'important' => 'all'
			]
		];
		$advanced_fields['box_shadow']['tooltips_box_shadow'] = [
			'css'         => [
				'main'  => ".tippy-box[data-theme~='{$this->main_css_element}']",
				'hover' => ".tippy-box[data-theme~='{$this->main_css_element}']:hover"
			],
			'toggle_slug' => 'design_tooltip',
			'tab_slug'    => 'advanced'
		];

		return $advanced_fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		/*------ Spacing ------*/
		// Icon
		$fields['icon_container_margin']['margin']   = "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_icon) .difl_avatar_stack_item_wrapper";
		$fields['icon_container_padding']['padding'] = "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_icon) .difl_avatar_stack_item_wrapper";
		// Media
		$fields['media_container_margin']['margin']   = "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_media) .difl_avatar_stack_item_wrapper";
		$fields['media_container_padding']['padding'] = "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_media) .difl_avatar_stack_item_wrapper";
		// Rating
		$fields['rating_container_margin']['margin']   = "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_rating) .difl_avatar_stack_item_wrapper";
		$fields['rating_container_padding']['padding'] = "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_rating) .difl_avatar_stack_item_wrapper";
		// Text
		$fields['text_container_margin']['margin']   = "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_text) .difl_avatar_stack_item_wrapper";
		$fields['text_container_padding']['padding'] = "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_text) .difl_avatar_stack_item_wrapper";

		/*------ Tooltip -------*/
		$tooltips                              = "{$this->main_css_element} .tippy-box";
		$tooltips_arrow                        = "{$this->main_css_element} .tippy-arrow:before";
		$fields['tooltips_padding']['padding'] = $tooltips;
		$fields                                = $this->df_background_transition( [
			'fields'   => $fields,
			'key'      => 'field_tooltip_background',
			'selector' => $tooltips
		] );
		// Color
		$fields['field_tooltip_arrow_color']['color'] = $tooltips_arrow;
		$fields['field_icon_background']['background-color'] = "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_icon";
		$fields['field_text_background']['background-color'] = "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_text";
		$fields['field_rating_background']['background-color'] = "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating";
		// border fix
		$fields = $this->df_fix_border_transition(
			$fields,
			'tooltips_border',
			$tooltips
		);
		//box-shadow Fix
		$fields = $this->df_fix_box_shadow_transition(
			$fields,
			'tooltips_box_shadow',
			$tooltips
		);

		return $fields;
	}

	public function additional_css_styles( $render_slug ) {
		/****** Module Alignment ******/
		$module_alignment        = ! empty($this->props['module_alignment']) ? $this->props['module_alignment'] : 'left';
		$module_alignment_tablet = ! empty($this->props['module_alignment_tablet']) ? $this->props['module_alignment_tablet'] : $module_alignment;
		$module_alignment_phone  = ! empty($this->props['module_alignment_phone']) ? $this->props['module_alignment_phone'] : $module_alignment_tablet;
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "{$this->main_css_element}.difl_avatar_stack .difl_avatar_stack_container",
			'declaration' => sprintf('justify-content: %1$s;', $module_alignment)
		] );
		ET_Builder_Element::set_style($render_slug, array(
			'selector'    => "{$this->main_css_element}.difl_avatar_stack .difl_avatar_stack_container",
			'declaration' => sprintf('justify-content: %1$s;', $module_alignment_tablet),
			'media_query' => ET_Builder_Element::get_media_query('max_width_980'),
		));
		ET_Builder_Element::set_style($render_slug, array(
			'selector'    => "{$this->main_css_element}.difl_avatar_stack .difl_avatar_stack_container",
			'declaration' => sprintf('justify-content: %1$s;', $module_alignment_phone),
			'media_query' => ET_Builder_Element::get_media_query('max_width_767'),
		));

		/*--------   Stack Spacing --------*/
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'field_stack_spacing',
			'type'        => 'margin-left',
			'default'     => '0px',
			'selector'    => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:not(:first-child)",
			'hover'       => "{$this->main_css_element} #difl-avatar-stack-container:hover .difl_avatar_stack_item:not(:first-child)",
			'important'   => false
		] );
//		$field_stack_spacing = ! empty($this->props['field_stack_spacing']) ? $this->props['field_stack_spacing'] : '0px';
//		ET_Builder_Element::set_style( $render_slug, [
//			'selector'    => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:not(:first-child)",
//			'declaration' => sprintf('margin-left: %1$s;', $field_stack_spacing),
//		] );
//		$hover_value = ! empty($this->props['field_stack_spacing__hover']) ? $this->props['field_stack_spacing__hover'] : '5px';
//		ET_Builder_Element::set_style($render_slug, array(
//			'selector' => "{$this->main_css_element} #difl-avatar-stack-container:hover .difl_avatar_stack_item:not(:last-child)",
//			'declaration' => sprintf('margin-right:%1$s;', $hover_value),
//		));

		/*--------   Stack Animation --------*/
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "{$this->main_css_element}.difl_avatar_stack",
			'declaration' => $this->generate_stack_animation_data( $this->props )
		] );

		/*----- Icon -----*/
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'field_icon_size',
			'type'        => 'font-size',
			'default'     => '30px',
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon",
			'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:hover .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon",
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_icon_color',
			'type'        => 'color',
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon",
			'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:hover .difl_avatar_stack_item_wrapper.has_icon .difl_avatar_stack_icon"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_icon_background',
			'type'        => 'background-color',
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_icon",
			'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:hover .difl_avatar_stack_item_wrapper.has_icon",
			'important'   => false
		] );

		/*----- Text -----*/
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_text_background',
			'type'        => 'background-color',
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_text",
			'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:hover .difl_avatar_stack_item_wrapper.has_text",
			'important'   => false
		] );

		/*----- Rating -----*/
		$rating_position = ! empty( $this->props['field_rating_position'] ) ? $this->props['field_rating_position'] : "center";
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container",
			'declaration' => sprintf('justify-content:%1$s;', $rating_position)
		] );
		// alignment
		$rating_alignment = ! empty( $this->props['field_rating_alignment'] ) ? $this->props['field_rating_alignment'] : 'left';
		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating",
			'declaration' => sprintf('text-align: %1$s;', $rating_alignment)
		] );
		// icon size
		$this->df_process_range( [
			'render_slug' => $render_slug,
			'slug'        => 'field_rating_icon_size',
			'type'        => 'font-size',
			'default'     => '14px',
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating",
			'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:hover .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating",
		] );
		// rating color
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_rating_color',
			'type'        => 'color',
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating span.rate:before",
			'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:hover .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating span.rate:before"
		] );
		// blank color
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_blank_color',
			'type'        => 'color',
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating span.blank:before",
			'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:hover .difl_avatar_stack_item_wrapper.has_rating .difl_avatar_stack_rating_container .difl_avatar_stack_rating span.blank:before"
		] );
		// Background
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_rating_background',
			'type'        => 'background-color',
			'selector'    => "{$this->main_css_element} .difl_avatar_stack_item .difl_avatar_stack_item_wrapper.has_rating",
			'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:hover .difl_avatar_stack_item_wrapper.has_rating",
			'important'   => false
		] );

		/*------ Spacing ------*/
		// Icon
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_container_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_icon) .difl_avatar_stack_item_wrapper",
				'hover'       => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_icon):hover .difl_avatar_stack_item_wrapper",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_container_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_icon) .difl_avatar_stack_item_wrapper",
				'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_icon):hover .difl_avatar_stack_item_wrapper",
				'important'   => false
			]
		);
		// Media
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'media_container_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_media) .difl_avatar_stack_item_wrapper",
				'hover'       => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_media):hover .difl_avatar_stack_item_wrapper",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'media_container_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_media) .difl_avatar_stack_item_wrapper",
				'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_media):hover .difl_avatar_stack_item_wrapper",
				'important'   => false
			]
		);
		// Rating
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'rating_container_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_rating) .difl_avatar_stack_item_wrapper",
				'hover'       => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_rating):hover .difl_avatar_stack_item_wrapper",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'rating_container_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_rating) .difl_avatar_stack_item_wrapper",
				'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_rating):hover .difl_avatar_stack_item_wrapper",
				'important'   => false
			]
		);
		// Text
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_container_margin',
				'type'        => 'margin',
				'selector'    => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_text) .difl_avatar_stack_item_wrapper",
				'hover'       => "{$this->main_css_element} #difl-avatar-stack-container .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_text):hover .difl_avatar_stack_item_wrapper",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_container_padding',
				'type'        => 'padding',
				'selector'    => "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_text) .difl_avatar_stack_item_wrapper",
				'hover'       => "{$this->main_css_element} .difl_avatar_stack_item:has(.difl_avatar_stack_item_wrapper.has_text):hover .difl_avatar_stack_item_wrapper",
				'important'   => false
			]
		);

		/*------ Tooltip -------*/
		$this->set_margin_padding_styles( [
			'render_slug' => $render_slug,
			'slug'        => 'tooltips_padding',
			'type'        => 'padding',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element']",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element']:hover"
		] );
		$this->df_process_bg( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_background',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element']",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element']:hover"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-top-color',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='top'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='top']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-bottom-color',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='bottom'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='bottom']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-left-color',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='left'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='left']:hover > .tippy-arrow::before"
		] );
		$this->df_process_color( [
			'render_slug' => $render_slug,
			'slug'        => 'field_tooltip_arrow_color',
			'type'        => 'border-right-color',
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='right'] > .tippy-arrow::before",
			'hover'       => ".tippy-box[data-theme~='$this->main_css_element'][data-placement^='right']:hover > .tippy-arrow::before"
		] );

		ET_Builder_Element::set_style( $render_slug, [
			'selector'    => ".tippy-box[data-theme~='$this->main_css_element'] .tippy-content p",
			'declaration' => 'padding-bottom: 0px;'
		] );
	}

	public function render( $attrs, $content, $render_slug ) {
		$this->handle_fa_icon();
		if ( 'on' === $this->props['field_tooltip_enable'] ) {
			wp_enqueue_script( 'image-hotspot-popper-script' );
			wp_enqueue_script( 'image-hotspot-tippy-bundle-script' );
			wp_enqueue_script( 'df_avatar_stack' );
		}

		$this->additional_css_styles( $render_slug );

		/*------ Tooltip ------*/
		global $df_tooltip_item_data;
		$order_class = self::get_module_order_class( $render_slug );
		DF_Localize_Vars::enqueue( 'df_avatar_stack', [
			'class'           => $order_class,
			'tooltip_content' => $df_tooltip_item_data,
		] );
		$df_tooltip_item_data = [];
		$data_settings        = $this->process_tooltip_data( $this->props );


		$output = sprintf(
			'<div id="difl-avatar-stack-container" class="difl_avatar_stack_container" data-tag_attr=\'%3$s\'  data-settings=\'%2$s\'>
				%1$s
			</div>',
			et_core_sanitized_previously( $this->content ),
			wp_json_encode( $data_settings ),
			wp_json_encode( [
				"title_tag" => ! empty($this->props['text_title_level']) ? $this->props['text_title_level'] : 'h4',
				"subtitle_tag" => ! empty($this->props['text_subtitle_level']) ? $this->props['text_subtitle_level'] : 'h6',
			] )
		);

		return $output;
	}

	public function process_tooltip_data( $props ) {
		$data_settings = [
			'tooltip_enable'      => 'on' === $props['field_tooltip_enable'],
			'arrow'               => 'on' === $props['field_tooltip_arrow'],
			'interactive'         => 'on' === $props['field_tooltip_interactive'],
			'interactiveBorder'   => 'on' === $props['field_tooltip_interactive'] && isset( $props['field_tooltip_interactive_border'] ) ? $props['field_tooltip_interactive_border'] : 2,
			'interactiveDebounce' => 'on' === $props['field_tooltip_interactive'] && isset( $props['field_tooltip_interactive_debounce'] ) ? $props['field_tooltip_interactive_debounce'] : 0,
			'animation'           => isset( $props['field_tooltip_animation'] ) ? $props['field_tooltip_animation'] : 'fade',
			'placement'           => isset( $props['field_tooltip_placement'] ) ? $props['field_tooltip_placement'] : 'top',
			'trigger'             => isset( $props['field_tooltip_trigger'] ) ? $props['field_tooltip_trigger'] : 'focus',
			'followCursor'        => 'on' === $props['field_tooltip_follow_cursor'],
			'maxWidth'            => isset( $props['field_tooltip_custom_maxwidth'] ) ? $props['field_tooltip_custom_maxwidth'] : 350,
			'offsetEnable'        => 'on' === $props['field_tooltip_offset_enable'],
			'offsetSkidding'      => 'on' === $props['field_tooltip_offset_enable'] && isset( $props['field_tooltip_offset_skidding'] ) ? $props['field_tooltip_offset_skidding'] : 0,
			'offsetDistance'      => 'on' === $props['field_tooltip_offset_enable'] && isset( $props['field_tooltip_offset_distance'] ) ? $props['field_tooltip_offset_distance'] : 10,
			'delay' => 'on' === $props['field_tooltip_enable'] && isset($props['field_tooltip_content_delay']) ? $props['field_tooltip_content_delay'] : 300
		];

		return $data_settings;
	}

	public function generate_stack_animation_data( $props ) {
		$default = [
			'field_item_translate_x'    => '0px',
			'field_item_translate_y'    => '0px',
			'field_item_rotate_x'       => '0deg',
			'field_item_rotate_y'       => '0deg',
			'field_item_rotate_z'       => '0deg',
			'field_item_scale_x'        => '1',
			'field_item_scale_y'        => '1',
			'field_item_skew_x'         => '0deg',
			'field_item_skew_y'         => '0deg',
			'field_item_trans_duration' => '300ms',
			'field_item_trans_delay'    => '0ms',
			'field_item_trans_easing'   => 'ease-out',
			'field_stack_translate_x'   => '0px',
			'field_stack_translate_y'   => '0px',
			'field_stack_rotate_x'      => '0deg',
			'field_stack_rotate_y'      => '0deg',
			'field_stack_rotate_z'      => '0deg',
		];
		$field_root_transition = [];
		if('on' === $this->props['field_item_translate_enable']){
			$field_root_transition['field_item_translate_x'] = '--df-avatarStack-item-trans-x-hover';
			$field_root_transition['field_item_translate_y'] = '--df-avatarStack-item-trans-y-hover';
		}
		if('on' === $this->props['field_item_rotate_enable']){
			$field_root_transition['field_item_rotate_x'] = '--df-avatarStack-item-rotate-x-hover';
			$field_root_transition['field_item_rotate_y'] = '--df-avatarStack-item-rotate-y-hover';
			$field_root_transition['field_item_rotate_z'] = '--df-avatarStack-item-rotate-z-hover';
		}
		if('on' === $this->props['field_item_scale_enable']){
			$field_root_transition['field_item_scale_x'] = '--df-avatarStack-item-scale-x-hover';
			$field_root_transition['field_item_scale_y'] = '--df-avatarStack-item-scale-y-hover';
		}
		if('on' === $this->props['field_item_skew_enable']){
			$field_root_transition['field_item_skew_x'] = '--df-avatarStack-item-skew-x-hover';
			$field_root_transition['field_item_skew_y'] = '--df-avatarStack-item-skew-y-hover';
		}
		if('on' === $this->props['field_item_transition_enable']){
			$field_root_transition['field_item_trans_duration'] = '--df-avatarStack-item-transition-duration';
			$field_root_transition['field_item_trans_delay'] = '--df-avatarStack-item-transition-delay';
			$field_root_transition['field_item_trans_easing'] = '--df-avatarStack-item-transition-easing';
		}
		if('on' === $this->props['field_stack_translate_enable']){
			$field_root_transition['field_stack_translate_x'] = '--df-avatarStack-trans-x-normal';
			$field_root_transition['field_stack_translate_y'] = '--df-avatarStack-trans-y-normal';
		}
		if('on' === $this->props['field_stack_rotate_enable']){
			$field_root_transition['field_stack_rotate_x'] = '--df-avatarStack-rotate-x-normal';
			$field_root_transition['field_stack_rotate_y'] = '--df-avatarStack-rotate-y-normal';
			$field_root_transition['field_stack_rotate_z'] = '--df-avatarStack-rotate-z-normal';
		}

		$result = '';
		foreach ( $field_root_transition as $key => $css_var ) {
			$value = ! empty( $props[ $key ] ) ? $props[ $key ] : $default[$key];
			if ( in_array( $key, [ 'field_item_trans_duration', 'field_item_trans_delay' ] ) ) {
				$value .= ! empty( $props[ $key ] ) ? 'ms' : '';
			}
			$result .= "$css_var:$value;";
		}

		return $result;
	}


}
new AvatarStack;