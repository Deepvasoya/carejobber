<?php

class SocialShareItem extends ET_Builder_Module {
	use DF_UTLS;

	public $icon_path;
	public $social_network_data;

	protected $module_credits = [
		'module_uri' => '',
		'author'     => 'DiviFlash',
		'author_uri' => '',
	];

	function init() {
		$this->name                        = esc_html__( 'Social Share Item', 'divi_flash' );
		$this->plural                      = esc_html__( 'Social Share Items', 'divi_flash' );
		$this->slug                        = 'difl_social_share_item';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'admin_label';
		$this->child_title_fallback_var    = 'social_network';
		$this->settings_text               = esc_html__( 'Social Share Settings', 'divi_flash' );
		$this->advanced_setting_title_text = esc_html__( 'New Social Share Item', 'divi_flash' );

		$this->main_css_element    = "%%order_class%%.difl_social_share_item_wrapper";
		$this->social_network_data = [
			'amazon'        => [
				'value' => esc_html__( 'Amazon', 'divi_flash' ),
				'data'  => [
					'color' => '#ff9900',
				],
			],
			'bandcamp'      => [
				'value' => esc_html__( 'Bandcamp', 'divi_flash' ),
				'data'  => [
					'color' => '#629aa9',
				],
			],
			'behance'       => [
				'value' => esc_html__( 'Behance', 'divi_flash' ),
				'data'  => [
					'color' => '#0057ff',
				],
			],
			'bitbucket'     => [
				'value' => esc_html__( 'BitBucket', 'divi_flash' ),
				'data'  => [
					'color' => '#205081',
				],
			],
			'buffer'        => [
				'value' => esc_html__( 'Buffer', 'divi_flash' ),
				'data'  => [
					'color' => '#000000',
				],
			],
			'codepen'       => [
				'value' => esc_html__( 'CodePen', 'divi_flash' ),
				'data'  => [
					'color' => '#000000',
				],
			],
			'deviantart'    => [
				'value' => esc_html__( 'DeviantArt', 'divi_flash' ),
				'data'  => [
					'color' => '#05cc47',
				],
			],
			'dribbble'      => [
				'value' => esc_html__( 'Dribbble', 'divi_flash' ),
				'data'  => [
					'color' => '#ea4c8d',
				],
			],
			'facebook'      => [
				'value' => esc_html__( 'Facebook', 'divi_flash' ),
				'data'  => [
					'color' => '#1877F2',
				],
			],
			'flikr'         => [
				'value' => esc_html__( 'Flickr', 'divi_flash' ),
				'data'  => [
					'color' => '#ff0084',
				],
			],
			'flipboard'     => [
				'value' => esc_html__( 'FlipBoard', 'divi_flash' ),
				'data'  => [
					'color' => '#e12828',
				],
			],
			'foursquare'    => [
				'value' => esc_html__( 'Foursquare', 'divi_flash' ),
				'data'  => [
					'color' => '#f94877',
				],
			],
			'github'        => [
				'value' => esc_html__( 'GitHub', 'divi_flash' ),
				'data'  => [
					'color' => '#333333',
				],
			],
			'goodreads'     => [
				'value' => esc_html__( 'Goodreads', 'divi_flash' ),
				'data'  => [
					'color' => '#553b08',
				],
			],
			'google'        => [
				'value' => esc_html__( 'Google', 'divi_flash' ),
				'data'  => [
					'color' => '#4285f4',
				],
			],
			'houzz'         => [
				'value' => esc_html__( 'Houzz', 'divi_flash' ),
				'data'  => [
					'color' => '#7ac142',
				],
			],
			'instagram'     => [
				'value' => esc_html__( 'Instagram', 'divi_flash' ),
				'data'  => [
					'color' => '#ea2c59',
				],
			],
			'itunes'        => [
				'value' => esc_html__( 'iTunes', 'divi_flash' ),
				'data'  => [
					'color' => '#fe7333',
				],
			],
			'last_fm'       => [
				'value' => esc_html__( 'Last.fm', 'divi_flash' ),
				'data'  => [
					'color' => '#b90000',
				],
			],
			'line'          => [
				'value' => esc_html__( 'Line', 'divi_flash' ),
				'data'  => [
					'color' => '#00c300',
				],
			],
			'linkedin'      => [
				'value' => esc_html__( 'LinkedIn', 'divi_flash' ),
				'data'  => [
					'color' => '#007bb6',
				],
			],
			'medium'        => [
				'value' => esc_html__( 'Medium', 'divi_flash' ),
				'data'  => [
					'color' => '#00ab6c',
				],
			],
			'meetup'        => [
				'value' => esc_html__( 'Meetup', 'divi_flash' ),
				'data'  => [
					'color' => '#e0393e',
				],
			],
			'myspace'       => [
				'value' => esc_html__( 'MySpace', 'divi_flash' ),
				'data'  => [
					'color' => '#3b5998',
				],
			],
			'odnoklassniki' => [
				'value' => esc_html__( 'Odnoklassniki', 'divi_flash' ),
				'data'  => [
					'color' => '#ed812b',
				],
			],
			'patreon'       => [
				'value' => esc_html__( 'Patreon', 'divi_flash' ),
				'data'  => [
					'color' => '#f96854',
				],
			],
			'periscope'     => [
				'value' => esc_html__( 'Periscope', 'divi_flash' ),
				'data'  => [
					'color' => '#3aa4c6',
				],
			],
			'pinterest'     => [
				'value' => esc_html__( 'Pinterest', 'divi_flash' ),
				'data'  => [
					'color' => '#cb2027',
				],
			],
			'quora'         => [
				'value' => esc_html__( 'Quora', 'divi_flash' ),
				'data'  => [
					'color' => '#a82400',
				],
			],
			'reddit'        => [
				'value' => esc_html__( 'Reddit', 'divi_flash' ),
				'data'  => [
					'color' => '#ff4500',
				],
			],
			'researchgate'  => [
				'value' => esc_html__( 'ResearchGate', 'divi_flash' ),
				'data'  => [
					'color' => '#40ba9b',
				],
			],
			'rss'           => [
				'value' => esc_html__( 'RSS', 'divi_flash' ),
				'data'  => [
					'color' => '#ff8a3c',
				],
			],
			'skype'         => [
				'value' => esc_html__( 'Skype', 'divi_flash' ),
				'data'  => [
					'color' => '#12A5F4',
				],
			],
			'snapchat'      => [
				'value' => esc_html__( 'Snapchat', 'divi_flash' ),
				'data'  => [
					'color' => '#fffc00',
				],
			],
			'soundcloud'    => [
				'value' => esc_html__( 'SoundCloud', 'divi_flash' ),
				'data'  => [
					'color' => '#ff8800',
				],
			],
			'spotify'       => [
				'value' => esc_html__( 'Spotify', 'divi_flash' ),
				'data'  => [
					'color' => '#1db954',
				],
			],
			'steam'         => [
				'value' => esc_html__( 'Steam', 'divi_flash' ),
				'data'  => [
					'color' => '#00adee',
				],
			],
			'telegram'      => [
				'value' => esc_html__( 'Telegram', 'divi_flash' ),
				'data'  => [
					'color' => '#179cde',
				],
			],
			'tiktok'        => [
				'value' => esc_html__( 'TikTok', 'divi_flash' ),
				'data'  => [
					'color' => '#fe2c55',
				],
			],
			'tripadvisor'   => [
				'value' => esc_html__( 'TripAdvisor', 'divi_flash' ),
				'data'  => [
					'color' => '#00af87',
				],
			],
			'tumblr'        => [
				'value' => esc_html__( 'Tumblr', 'divi_flash' ),
				'data'  => [
					'color' => '#32506d',
				],
			],
			'twitch'        => [
				'value' => esc_html__( 'Twitch', 'divi_flash' ),
				'data'  => [
					'color' => '#6441a5',
				],
			],
			'twitter'       => [
				'value' => esc_html__( 'X', 'divi_flash' ),
				'data'  => [
					'color' => '#000000',
				],
			],
			'vimeo'         => [
				'value' => esc_html__( 'Vimeo', 'divi_flash' ),
				'data'  => [
					'color' => '#45bbff',
				],
			],
			'vk'            => [
				'value' => esc_html__( 'VK', 'divi_flash' ),
				'data'  => [
					'color' => '#45668e',
				],
			],
			'weibo'         => [
				'value' => esc_html__( 'Weibo', 'divi_flash' ),
				'data'  => [
					'color' => '#eb7350',
				],
			],
			'whatsapp'      => [
				'value' => esc_html__( 'WhatsApp', 'divi_flash' ),
				'data'  => [
					'color' => '#25D366',
				],
			],
			'xing'          => [
				'value' => esc_html__( 'XING', 'divi_flash' ),
				'data'  => [
					'color' => '#026466',
				],
			],
			'yelp'          => [
				'value' => esc_html__( 'Yelp', 'divi_flash' ),
				'data'  => [
					'color' => '#af0606',
				],
			],
			'youtube'       => [
				'value' => esc_html__( 'Youtube', 'divi_flash' ),
				'data'  => [
					'color' => '#a82400',
				],
			],
			'digg'          => [
				'value' => esc_html__( 'Digg', 'divi_flash' ),
				'data'  => [
					'color' => '#005be2',
				],
			],
			'stumbleupon'   => [
				'value' => esc_html__( 'Stumbleupon', 'divi_flash' ),
				'data'  => [
					'color' => '#eb4924',
				],
			],
			'mix'           => [
				'value' => esc_html__( 'Mix', 'divi_flash' ),
				'data'  => [
					'color' => '#f3782b',
				],
			],
			'pocket'        => [
				'value' => esc_html__( 'Pocket', 'divi_flash' ),
				'data'  => [
					'color' => '#ef3f56',
				],
			],
			'email'         => [
				'value' => esc_html__( 'Email', 'divi_flash' ),
				'data'  => [
					'color' => '#ea4335',
				],
			],
			'print'         => [
				'value' => esc_html__( 'Print', 'divi_flash' ),
				'data'  => [
					'color' => '#aaa',
				],
			],
		];
	}

	public function get_settings_modal_toggles() {
		return [
			'general'  => [
				'toggles' => [
					'main_content' => esc_html__( 'Content', 'divi_flash' ),
					'main_tooltip_content' => esc_html__( 'Tooltip Content', 'divi_flash' ),
					'admin_label'  => [
						'title'    => esc_html__( 'Admin Label', 'divi_flash' ),
						'priority' => 99,
					],
				],
			],
			'advanced' => [
				'toggles' => [
					'icon'            => esc_html__( 'Icon/Image', 'divi_flash' ),
					'label'           => esc_html__( 'Label', 'divi_flash' ),
					'label_container' => esc_html__( 'Label Container', 'divi_flash' ),
				],
			],
		];
	}

	public function get_advanced_fields_config() {
		$advanced_fields = [];

		$advanced_fields['background']          = [
			'css' => [
				'main'      => '%%order_class%%.difl_social_share_item_wrapper',
				'important' => 'all',
			],
//			'options'                     => [
//				'background_color'     => [
//					'default'         => '#3b5998',
//				],
//			],

		];
		$advanced_fields['borders']['default']  = [
			'css' => [
				'main' => [
					'border_radii'  => '%%order_class%%.difl_social_share_item_wrapper',
					'border_styles' => '%%order_class%%.difl_social_share_item_wrapper',
				],
			],
		];
		$advanced_fields['borders']['icon']     = [
			'css'         => [
				'main' => [
					'border_radii'  => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon',
					'border_styles' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon',
				],
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'icon',
		];
		$advanced_fields['borders']['label']    = [
			'css'         => [
				'main' => [
					'border_radii'  => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content',
					'border_styles' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content',
				],
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'label_container',
		];
		$advanced_fields['box_shadow']          = [
			'default' => [
				'css' => [
					'main'      => '%%order_class%%.difl_social_share_item_wrapper',
					'important' => true,
				],
			],
		];
		$advanced_fields['box_shadow']['icon']  = [
			'css'         => [
				'main'      => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon',
				'important' => true,
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'icon',
		];
		$advanced_fields['box_shadow']['label'] = [
			'css'         => [
				'main'      => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content',
				'important' => true,
			],
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'label_container',
		];
		$advanced_fields['margin_padding']      = [
			'css' => [
				'padding' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper',
				'main'    => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper',
			],
		];
		$advanced_fields['fonts']['label']      = [
			'label'            => esc_html__( 'Title', 'divi_flash' ),
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'label',
			'hide_text_shadow' => false,
			'line_height'      => [
				'default' => '1.7em',
			],
			'font_size'        => [
				'default' => '12px',
			],
			'css'              => [
				'main' => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text",
				'hover' => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_content .difl_social_share_text, %%order_class%%#difl-social-share-item-wrapper:hover.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text",
			],
		];
		$advanced_fields['fonts']['icon']       = [
			'label'               => esc_html__( 'Icon', 'divi_flash' ),
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'icon',
			'hide_text_shadow'    => false,
			'hide_text_align'     => true,
			'hide_text_color'     => true,
			'hide_font'           => true,
			'hide_font_size'      => true,
			'hide_line_height'    => true,
			'hide_letter_spacing' => true,
			'text_shadow'         => [
				'show_if_not' => [
					'use_custom_image_icon' => 'on',
				],
			],
			'css'                 => [
				'main' => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before",
				'hover' => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper:hover .difl_social_share_icon i:before",
			],
		];
		$advanced_fields['text']                = false;
		$advanced_fields['max_width']           = false;
		$advanced_fields['height']              = false;
		$advanced_fields['button']              = false;
		$advanced_fields['animation']           = false;
		$advanced_fields['link_options']        = false;
		$advanced_fields['sticky']              = false;
		$advanced_fields['filters']             = false;
		$advanced_fields['transform']           = false;

		return $advanced_fields;
	}

	public function get_custom_css_fields_config() {
		return [
			'icon_image_container'       => [
				'label'    => esc_html__( 'Icon/Image Container', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon'
			],
			'icon_image_container_hover' => [
				'label'    => esc_html__( 'Icon/Image Container Hover', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon:hover, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon:hover'
			],
			'label_container'            => [
				'label'    => esc_html__( 'Label Container', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content'
			],
			'label_container_hover'      => [
				'label'    => esc_html__( 'Label Container Hover', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content:hover, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content:hover'
			],
			'icon'                       => [
				'label'    => esc_html__( 'Icon', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before'
			],
			'icon_hover'                 => [
				'label'    => esc_html__( 'Icon Hover', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:hover:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:hover:before'
			],
			'image'                      => [
				'label'    => esc_html__( 'Image', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon img, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon img'
			],
			'image_hover'                => [
				'label'    => esc_html__( 'Image Hover', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon img:hover, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon img:hover'
			],
			'label'                      => [
				'label'    => esc_html__( 'Label', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text'
			],
			'label_hover'                => [
				'label'    => esc_html__( 'Label hover', 'divi_flash' ),
				'selector' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text:hover, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content .difl_social_share_text:hover'
			],
		];
	}

	public function get_fields() {
		$fields = [];

		$fields['admin_label']           = [
			'label'            => esc_html__( 'Admin Label', 'divi_flash' ),
			'type'             => 'text',
			'option_category'  => 'basic_option',
			'toggle_slug'      => 'admin_label',
			'default_on_front' => ''
		];
		$fields['social_network']        = [
			'label'              => esc_html__( 'Social Network', 'divi_flash' ),
			'type'               => 'select',
			'option_category'    => 'basic_option',
			'class'              => 'et-pb-social-network',
			'options'            => [
				''            => esc_html__( 'Select a Network', 'divi_flash' ),
				'facebook'    => esc_html__( 'Facebook', 'divi_flash' ),
				'twitter'     => esc_html__( 'Twitter', 'divi_flash' ),
				'linkedin'    => esc_html__( 'LinkedIn', 'divi_flash' ),
				'pinterest'   => esc_html__( 'Pinterest', 'divi_flash' ),
				'reddit'      => esc_html__( 'Reddit', 'divi_flash' ),
				'vk'          => esc_html__( 'Vk', 'divi_flash' ),
				'tumblr'      => esc_html__( 'Tumblr', 'divi_flash' ),
				'digg'        => esc_html__( 'Digg', 'divi_flash' ),
				'skype'       => esc_html__( 'Skype', 'divi_flash' ),
				'stumbleupon' => esc_html__( 'Stumbleupon', 'divi_flash' ),
				'mix'         => esc_html__( 'Mix', 'divi_flash' ),
				'telegram'    => esc_html__( 'Telegram', 'divi_flash' ),
				'pocket'      => esc_html__( 'Pocket', 'divi_flash' ),
				'xing'        => esc_html__( 'Xing', 'divi_flash' ),
				'whatsapp'    => esc_html__( 'WhatsApp', 'divi_flash' ),
				'email'       => esc_html__( 'Email', 'divi_flash' ),
				'print'       => esc_html__( 'Print', 'divi_flash' ),

				'buffer'        => esc_html__( 'Buffer', 'divi_flash' ),
				'flipboard'     => esc_html__( 'FlipBoard', 'divi_flash' ),
				'line'          => esc_html__( 'Line', 'divi_flash' ),
				'myspace'       => esc_html__( 'MySpace', 'divi_flash' ),
				'odnoklassniki' => esc_html__( 'Odnoklassniki', 'divi_flash' ),
				'weibo'         => esc_html__( 'Weibo', 'divi_flash' ),


			],
			'overwrite_onchange' => [
//				'icon_bg_color_bgcolor',
				'background_color'
			],
			'affects'          => [
				'use_custom_image_icon',
				'custom_label'
			],
			'description'        => esc_html__( 'Choose the social network', 'divi_flash' ),
			'toggle_slug'        => 'main_content',
		];
		$fields['use_custom_image_icon'] = [
			'label'            => esc_html__( 'Use Custom Image Icon', 'divi_flash' ),
			'description'      => esc_html__( 'You can add custom image for your icon ', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'off' => et_builder_i18n( 'No' ),
				'on'  => et_builder_i18n( 'Yes' ),
			],
			'default_on_front' => 'off',
			'affects'          => [
				'src',
			],
			'depends_show_if_not' => '',
			'tab_slug'         => 'general',
			'toggle_slug'      => 'main_content',
		];
		$fields['src']                   = [
			'type'               => 'upload',
			'option_category'    => 'basic_option',
			'upload_button_text' => esc_attr__( 'Upload an image icon', 'divi_flash' ),
			'choose_text'        => esc_attr__( 'Choose an Image Icon', 'divi_flash' ),
			'update_text'        => esc_attr__( 'Set As Image Icon', 'divi_flash' ),
			'hide_metadata'      => true,
			'affects'            => [
				'alt',
				'title_text',
			],
			'description'        => esc_html__( 'Upload your desired image icon, or type in the URL to the image you would like to display.', 'divi_flash' ),
			'depends_show_if'    => 'on',
			'tab_slug'           => 'general',
			'toggle_slug'        => 'main_content',
			'dynamic_content'    => 'image',
		];
		$fields['alt']                   = [
			'label'           => esc_html__( 'Image Alternative Text', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'depends_show_if' => 'on',
			'depends_on'      => [
				'src',
			],
			'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'divi_flash' ),
			'tab_slug'        => 'custom_css',
			'toggle_slug'     => 'attributes',
			'dynamic_content' => 'text',
		];
		$fields['title_text']            = [
			'label'           => esc_html__( 'Image Title Text', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'depends_show_if' => 'on',
			'depends_on'      => [
				'src',
			],
			'description'     => esc_html__( 'This defines the HTML Title text.', 'divi_flash' ),
			'tab_slug'        => 'custom_css',
			'toggle_slug'     => 'attributes',
			'dynamic_content' => 'text',
		];
		$fields['custom_label']          = [
			'label'           => esc_html__( 'Custom Label', 'divi_flash' ),
			'description'     => esc_html__( 'This defines the Custom Label.', 'divi_flash' ),
			'type'            => 'text',
			'option_category' => 'basic_option',
			'depends_show_if_not' => '',
			'tab_slug'        => 'general',
			'toggle_slug'     => 'main_content',
		];
		$fields['icon_color']            = [
			'label'          => esc_html__( 'Icon Color', 'divi_flash' ),
			'description'    => esc_html__( 'Here you can define a custom color for the social network icon.', 'divi_flash' ),
			'type'           => 'color-alpha',
			'custom_color'   => true,
			'tab_slug'       => 'advanced',
			'toggle_slug'    => 'icon',
			'hover'          => 'tabs',
			'mobile_options' => true,
			'sticky'         => true,
			'show_if_not'    => [
				'use_custom_image_icon' => 'on'
			]
		];
		$fields['use_icon_font_size']    = [
			'label'            => esc_html__( 'Use Custom Icon/Image Size', 'divi_flash' ),
			'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'divi_flash' ),
			'type'             => 'yes_no_button',
			'options'          => [
				'off' => et_builder_i18n( 'No' ),
				'on'  => et_builder_i18n( 'Yes' ),
			],
			'default_on_front' => 'off',
			'affects'          => [
				'icon_font_size',
			],
			'depends_show_if'  => 'on',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'icon',
			'option_category'  => 'font_option',
		];
		$fields['icon_font_size']        = [
			'label'            => esc_html__( 'Icon/Image Size', 'divi_flash' ),
			'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'divi_flash' ),
			'type'             => 'range',
			'option_category'  => 'font_option',
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'icon',
			'allowed_units'    => [ '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ],
			'default'          => '16px',
			'default_unit'     => 'px',
			'default_on_front' => '',
			'range_settings'   => [
				'min'  => '1',
				'max'  => '120',
				'step' => '1',
			],
			'mobile_options'   => true,
			'depends_show_if'  => 'on',
			'responsive'       => true,
			'hover'            => 'tabs',
			'sticky'           => true,
		];

		$fields = array_merge(
			$fields,
			$this->add_margin_padding( [
				'title'       => 'Icon/Image Container',
				'key'         => 'icon_container',
				'toggle_slug' => 'margin_padding',
			] ),
			$this->add_margin_padding( [
				'title'       => 'Label Container',
				'key'         => 'label_container',
				'toggle_slug' => 'margin_padding',
			] ),
			$this->df_add_bg_field(
				[
					'label'       => 'Icon Background',
					'key'         => 'icon_bg_color',
					'toggle_slug' => 'icon',
					'tab_slug'    => 'advanced'
				]
			),
			$this->df_add_bg_field(
				[
					'label'       => 'Background',
					'key'         => 'text_container_bg_color',
					'toggle_slug' => 'label_container',
					'tab_slug'    => 'advanced'
				]
			)
		);

		// Tooltip
		$fields['field_tooltip_content'] = [
			'label'           => esc_html__( 'Content', 'divi_flash' ),
			'type'            => 'tiny_mce',
			'option_category' => 'basic_option',
			'description'     => esc_html__( 'Note: Html tags, shortcode are supported and shortcode will be view only frontend ', 'divi_flash' ),
			'toggle_slug'     => 'main_tooltip_content',
			'dynamic_content' => 'text',
		];


		// Automatically parse social_network's option as value_overwrite
		foreach ( $fields['social_network']['options'] as $value_overwrite_key => $value_overwrite ) {
			if ( ! empty( $value_overwrite_key ) ) {
				$fields['social_network']['value_overwrite'][ $value_overwrite_key ] = $this->social_network_data[ $value_overwrite_key ]['data']['color'];
			}
		}

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['icon_color']['color'] = '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before';
		$fields['icon_font_size']      = [
			'font-size'   => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before',
			'line-height' => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before',
			'height'      => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before',
			'width'       => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before',
			'height'      => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon',
			'width'       => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon',
		];

		// Icon Background
		$fields = $this->df_background_transition(
			[
				'fields'   => $fields,
				'key'      => 'icon_bg_color',
				'selector' => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon"
			]
		);

		// Text Container Background
		$fields = $this->df_background_transition(
			[
				'fields'   => $fields,
				'key'      => 'text_container_bg_color',
				'selector' => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content"
			]
		);

		/*------ Spacing ------*/
		// Icon
		$fields['icon_container_margin']['margin']   = ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon";
		$fields['icon_container_padding']['padding'] = ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon";
		// Media
		$fields['label_container_margin']['margin']   = ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content_container, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content_container";
		$fields['label_container_padding']['padding'] = ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content";

		return $fields;
	}

	public function additional_css_styles( $render_slug ) {
		// Icon Background Color
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_bg_color',
				'selector'    => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon",
				'hover'       => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper:hover .difl_social_share_icon",
			]
		);

		// Text Container Background Color
		$this->df_process_bg(
			[
				'render_slug' => $render_slug,
				'slug'        => 'text_container_bg_color',
				'selector'    => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content",
				'hover'       => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper:hover .difl_social_share_content",
			]
		);

		/*------ Spacing ------*/
		// Icon
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_container_margin',
				'type'        => 'margin',
				'selector'    => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon",
				'hover'       => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper:hover .difl_social_share_icon",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'icon_container_padding',
				'type'        => 'padding',
				'selector'    => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon",
				'hover'       => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper:hover .difl_social_share_icon",
				'important'   => false
			]
		);
		// Label
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'label_container_margin',
				'type'        => 'margin',
				'selector'    => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content_container, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content_container",
				'hover'       => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_content_container, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper:hover .difl_social_share_content_container",
				'important'   => false
			]
		);
		$this->set_margin_padding_styles(
			[
				'render_slug' => $render_slug,
				'slug'        => 'label_container_padding',
				'type'        => 'padding',
				'selector'    => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_content",
				'hover'       => ".difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_content, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper:hover .difl_social_share_content",
				'important'   => false
			]
		);
	}

	private function get_current_page_url() {
		return get_permalink();
	}

	private function get_current_page_title() {
		return wp_get_document_title() ?: 'Check this out!';
	}

	public function generate_share_link( $network_name ) {
		$list_share_links = [
			'facebook'    => 'https://www.facebook.com/sharer/sharer.php?u=%1$s',
			'twitter'     => 'https://twitter.com/intent/tweet?text=%1$s',
			'linkedin'    => 'https://www.linkedin.com/shareArticle?mini=true&url=%1$s/&title=&summary=&source=',
			'pinterest'   => 'https://www.pinterest.com/pin/create/button/?url=%1$s&media=',
			'reddit'      => 'https://www.reddit.com/submit?url=%1$s&title=',
			'vk'          => 'https://vk.com/share.php?url=%1$s',
			'tumblr'      => 'https://tumblr.com/share/link?url=%1$s',
			'digg'        => 'https://digg.com/submit?url=%1$s',
			'skype'       => 'https://web.skype.com/share?url=%1$s',
			'stumbleupon' => 'https://www.stumbleupon.com/submit?url=%1$s',
			'mix'         => 'https://mix.com/add?url=%1$s',
			'telegram'    => 'https://telegram.me/share/url?url=%1$s&text=',
			'pocket'      => 'https://getpocket.com/edit?url=%1$s',
			'xing'        => 'https://www.xing.com/spi/shares/new?url=%1$s',
			'whatsapp'    => 'https://api.whatsapp.com/send?text=**%1$s',
			'email'       => 'mailto:?body=%1$s',
			'print'       => '#',

			'buffer'        => 'https://buffer.com/add?url=%1$s&text=',
			'flipboard'     => 'https://share.flipboard.com/bookmarklet/popout?v=2&url=%1$s',
			'line'          => 'https://social-plugins.line.me/lineit/share?url=%1$s',
			'myspace'       => 'https://myspace.com/post?u=%1$s',
			'odnoklassniki' => 'https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl=%1$s',
			'weibo'         => 'https://service.weibo.com/share/share.php?url=%1$s',
		];

		if ( ! isset( $list_share_links[ $network_name ] ) ) {
			return '#';
		}

		// Properly encode URL and title for sharing
		$encoded_url   = urlencode( $this->get_current_page_url() );
		$encoded_title = urlencode( $this->get_current_page_title() );

		return sprintf( $list_share_links[ $network_name ], $encoded_url );
	}

	public function render( $attrs, $content, $render_slug ) {
		global $difl_ss_helper_data;
		$url_new_window = self::$_->array_get( $difl_ss_helper_data, 'url_new_window', '' );
//		$show_label      = self::$_->array_get( $difl_ss_helper_data, 'show_label', '' );
		$item_view       = self::$_->array_get( $difl_ss_helper_data, 'item_view', '' );
		$hover_animation = self::$_->array_get( $difl_ss_helper_data, 'hover_animation', '' );

		$this->additional_css_styles( $render_slug );
		$module_class = $this->module_classname( $render_slug );

		$social_network                = $this->props['social_network'];
		$icon_container_padding        = $this->props['icon_container_padding'];
		$icon_container_padding_tablet = $this->props['icon_container_padding_tablet'];
		$icon_container_padding_phone  = $this->props['icon_container_padding_phone'];
		$network_name                  = '';
		$use_icon_font_size            = $this->props['use_icon_font_size'];

		if ( '' !== $icon_container_padding || '' !== $icon_container_padding_tablet || '' !== $icon_container_padding_phone ) {
			$el_style = [
				'selector'    => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon',
				'declaration' => 'width: auto; height: auto;',
			];
			ET_Builder_Element::set_style( $render_slug, $el_style );
		}

		// Icon Color.
		$this->generate_styles(
			[
				'base_attr_name'                  => 'icon_color',
				'selector'                        => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before',
				'hover_selector'                  => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper:hover .difl_social_share_icon i:before, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper:hover .difl_social_share_icon i:before',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			]
		);

		// Icon Size.
		if ( 'off' !== $use_icon_font_size ) {
			// Calculate icon size + its wrapper dimension.
			$this->generate_styles(
				[
					'base_attr_name'                  => 'icon_font_size',
					'selector'                        => "
					.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon i:before,
					 %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon i:before, 
					 .difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon img, 
					 %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon img
					 ",
					'selector_wrapper'                => '.difl_social_share #difl-social-share-container.difl_social_share_container %%order_class%%.difl_social_share_item_wrapper .difl_social_share_icon, %%order_class%%#difl-social-share-item-wrapper.difl_temp_1.difl_temp_2.difl_social_share_item_wrapper .difl_social_share_icon',
					'hover_pseudo_selector_location'  => 'suffix',
					'sticky_pseudo_selector_location' => 'prefix',
					'render_slug'                     => $render_slug,
					'type'                            => 'range',
					'css_property'                    => 'right',

					// processed attr value can't be directly assigned to single css property so
					// custom processor is needed to render this attr. Processor required is 100%
					// identical to social media follow module's. Thus it is being re-used.
					'processor'                       => [
						'ET_Builder_Module_Helper_Style_Processor',
						'process_social_media_icon_font_size',
					],
				]
			);
		}

		$social_network_link_url = $this->generate_share_link( $social_network );
		$social_icon_class       = '';
		if ( '' !== $social_network ) {
			$network_name      = esc_attr( $this->social_network_data[ $social_network ]['value'] );
			$social_icon_class = sprintf( ' df-social-share-%s', esc_attr( $social_network ) );
			if ( ! empty( $this->props['social_network'] ) && in_array( $this->props['social_network'], et_pb_get_social_net_fa_icons(), true ) ) {
				$social_icon_class .= ' df-social-share-fa-icon';
			}
		}

		$use_custom_image_icon = $this->props['use_custom_image_icon'];
		$src                   = $this->props['src'];
		$alt                   = $this->props['alt'];
		$title_text            = $this->props['title_text'];

		// Handle svg image behaviour.
		$src_pathinfo = pathinfo( $src );
		$is_src_svg   = isset( $src_pathinfo['extension'] ) ? 'svg' === $src_pathinfo['extension'] : false;
		if ( empty( $alt ) || empty( $title_text ) ) {
			$raw_src   = et_()->array_get( $this->attrs_unprocessed, 'src' );
			$src_value = et_builder_parse_dynamic_content( $raw_src );

			if ( $src_value->is_dynamic() && $src_value->get_content() === 'post_featured_image' ) {
				// If there is no user-specified ALT attribute text, check the WP
				// Media Library entry for text that may have been added there.
				if ( empty( $alt ) ) {
					$alt = et_builder_resolve_dynamic_content( 'post_featured_image_alt_text', [], get_the_ID(), 'display' );
				}

				// If there is no user-specified TITLE attribute text, check the WP
				// Media Library entry for text that may have been added there.
				if ( empty( $title_text ) ) {
					$title_text = et_builder_resolve_dynamic_content( 'post_featured_image_title_text', [], get_the_ID(), 'display' );
				}
			}
		}

		// Set display block for svg image to avoid disappearing svg image.
		if ( $is_src_svg ) {
			ET_Builder_Element::set_style(
				$render_slug,
				[
					'selector'    => '%%order_class%%.difl_social_share_item_wrapper .difl_custom_image_icon',
					'declaration' => 'width: 100%;',
				]
			);
		}

		$images_output = sprintf(
			'<img src="%1$s" class="difl_custom_image_icon" alt="%2$s"%3$s />',
			esc_url( $src ),
			esc_attr( $alt ),
			( '' !== $title_text ? sprintf( ' title="%1$s"', esc_attr( $title_text ) ) : '' )
		);

		$share_icon    = '';
		$share_content = '';

		if ( 'iconAndText' === $item_view ) {
			$share_icon    = sprintf(
				'<div class="difl_social_share_icon">
						%1$s
					</div>', ( 'on' === $use_custom_image_icon && '' !== $social_network ) ? $images_output : sprintf( '<i class="%1$s"></i>', $social_icon_class ) );
			$share_content = sprintf(
				'<div class="difl_social_share_content_container">
                        <div class="difl_social_share_content">
                            <span class="difl_social_share_text">%1$s</span>
                        </div>
                    </div>', ! empty( $this->props['custom_label'] ) ? esc_html($this->props['custom_label']) : $network_name );
		}
		if ( 'icon' === $item_view ) {
			$share_icon = sprintf(
				'<div class="difl_social_share_icon">
						%1$s
					</div>', ( 'on' === $use_custom_image_icon && '' !== $social_network ) ? $images_output : sprintf( '<i class="%1$s"></i>', $social_icon_class ) );
		}
		if ( 'text' === $item_view ) {
			$share_content = sprintf(
				'<div class="difl_social_share_content_container">
                        <div class="difl_social_share_content">
                            <span class="difl_social_share_text">%1$s</span>
                        </div>
                    </div>', ! empty( $this->props['custom_label'] ) ? esc_html($this->props['custom_label']) : $network_name );
		}

		// Tooltip
		$tooltip_content      = $this->props['field_tooltip_content'];
		$tooltip_content_data = "";
		if ( ! empty( $tooltip_content ) ) {
			$tooltip_content_data = sprintf( '<noscript>%1$s</noscript>', $tooltip_content );
		}

		add_filter( 'et_global_assets_list', [ $this, 'difl_load_required_divi_assets' ], 10, 3 );
		add_filter( 'et_late_global_assets_list', [ $this, 'difl_load_required_divi_assets' ], 10, 3 );
		return sprintf(
			'<a 
						href="%1$s" 
						class="%6$s difl_social_share_item_wrapper%8$s" 
						data-animation="%7$s"
						title="%4$s" %5$s role="link" 
						rel="noopener noreferrer"
					>%2$s%3$s%9$s</a>',
			$social_network_link_url,
			$share_icon,
			$share_content,
			$network_name,
			( 'on' === $url_new_window ? 'target="_blank"' : '' ),
			$module_class,
			esc_html( $hover_animation ),
			('print' === $social_network) ? ' difl_print' : '',
			$tooltip_content_data
		);
	}

	protected function _render_module_wrapper( $output = '', $render_slug = '' ) {
		return $output;
	}

	public function difl_load_required_divi_assets( $assets_list, $assets_args, $instance ) {
		$assets_prefix  = et_get_dynamic_assets_path();
		$all_shortcodes = $instance->get_saved_page_shortcodes();

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

new SocialShareItem;