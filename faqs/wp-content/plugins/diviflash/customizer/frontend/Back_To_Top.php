<?php

namespace DIFL\Customizer\Frontend;

class Back_To_Top extends Frontend {

	protected $settings = [];

	const PREFIX = 'difl_back_to_top';

	public function __construct() {
		$this->populate_settings();
		add_action( 'wp_footer', [ $this, 'back_to_top' ], PHP_INT_MAX );
	}

	protected function populate_settings() {
		if ( ! \DIFL\Customizer\Extensions\Back_To_Top::is_divi_btt_enabled() ) {
			return;
		}
		parent::populate_settings();
	}

	public function back_to_top() {
		if ( $this->is_vb_or_tb() ) {
			return;
		}

		if ( ! \DIFL\Customizer\Extensions\Back_To_Top::is_extension_enabled() ) {
			return;
		}

		$default_spacing        = [
			'mobile'       => [
				'top'    => 8,
				'right'  => 10,
				'bottom' => 8,
				'left'   => 10,
			],
			'tablet'       =>
				[
					'top'    => 8,
					'right'  => 10,
					'bottom' => 8,
					'left'   => 10,
				],
			'desktop'      => [
				'top'    => 8,
				'right'  => 10,
				'bottom' => 8,
				'left'   => 10,
			],
			'mobile-unit'  => 'px',
			'tablet-unit'  => 'px',
			'desktop-unit' => 'px',
		];
		$styles                 = [];
		$label                  = esc_html( $this->get_value( 'label', '' ) );
		$label_font_size        = $this->get_value( 'label_font_size' );
		$label_font_color       = esc_html( $this->get_value( 'label_font_color', 'var(--difl--icon--color)' ) );
		$label_font_hover_color = esc_html( $this->get_value( 'label_font_hover_color', 'var(--difl--brand--color)' ) );
		$media                  = esc_html( $this->get_value( 'type', 'icon' ) );
		$icon                   = $this->get_value( 'icon', '!' );
		$icon_color             = esc_html( $this->get_value( 'icon_color', 'var(--difl--icon--color)' ) );
		$icon_hover_color       = esc_html( $this->get_value( 'icon_hover_color', 'var(--difl--brand--color)' ) );
		$icon_size              = $this->get_value( 'icon_size' );
		$image                  = ! empty( $this->get_value( 'image' ) )  && is_int( $this->get_value( 'image' ) ) ? wp_get_attachment_image_url( $this->get_value( 'image' ) ) : $this->get_value( 'image' );
		$image_size             = $this->get_value( 'image_size',  '{"mobile": "16", "tablet": "16", "desktop": "16" }');
		$position               = $this->get_value( 'position', 'right' );
		$alignment              = $this->get_value( 'alignment', 'horizontally' );
		$side_offset            = $this->get_value( 'offset', 10 ) . 'px';
		$bottom_offset          = $this->get_value( 'bottom_offset', 30 ) . 'px';
		$hover_animation        = 'difl-' . $this->get_value( 'hover_animation', 'zoomin' );
		$hide_on_mobile         = $this->get_value( 'on_mobile' );
		$background_color       = $this->get_value( 'background_color', '#FFF' );
		$background_color_hover = $this->get_value( 'background_hover_color', 'var(--difl--icon--color)' );
		$space_between          = $this->get_value( 'space_between', 5 ) . 'px';
		$margin                 = $this->get_value( 'margin', $default_spacing );
		$padding                = $this->get_value( 'padding', $default_spacing );
		$border_radius          = $this->get_value( 'border_radius', $default_spacing );

		$styles['background']       = $this->get_bg_type( $background_color );
		$styles['background_hover'] = $this->get_bg_type( $background_color_hover );
		$styles['alignment']        = 'vertically' === $alignment ? 'column' : 'row';
		$styles['image']            = 'url(' . $image . ')';
		$margin_desktop             = $this->get_responsive_sizes( $margin, 'desktop', 'quad' );
		$margin_tablet              = $this->get_responsive_sizes( $margin, 'tablet', 'quad' );
		$margin_mobile              = $this->get_responsive_sizes( $margin, 'mobile', 'quad' );

		$padding_desktop = $this->get_responsive_sizes( $padding, 'desktop', 'quad' );
		$padding_tablet  = $this->get_responsive_sizes( $padding, 'tablet', 'quad' );
		$padding_mobile  = $this->get_responsive_sizes( $padding, 'mobile', 'quad' );

		$border_radius_desktop = $this->get_responsive_sizes( $border_radius, 'desktop', 'quad' );
		$border_radius_tablet  = $this->get_responsive_sizes( $border_radius, 'tablet', 'quad' );
		$border_radius_mobile  = $this->get_responsive_sizes( $border_radius, 'mobile', 'quad' );
		?>
        <!--		Markup-->

        <!--		Style-->
        <style>
            .et_pb_scroll_top::before {
                content: none;
                display: none;
            }

            :root {
                --difl--brand--color: #F0E9FE;
                --difl--icon--color: #FFF;
                --difl--btt--font: 'initial';
                --difl--btt--font--weight: 400;
                --difl--btt--font--size: '18px';
            }

            .et_pb_scroll_top {
                opacity: 0;
                visibility: hidden;
            }

            /*Hide On mobile*/
            <?php if ($hide_on_mobile){?>
            @media screen and (max-width: 720px) {
                body .et_pb_scroll_top {
                    display: none !important;
                }
            }

            <?php }?>

            /*Animation*/
            /* Zoom In Animation with Hover Effect */
            .difl-zoomin {
                transition: transform 0.3s;
            }

            .difl-zoomin:hover {
                transform: scale(1.2);
            }

            /* Zoom Out Animation with Hover Effect */
            .difl-zoomout {
                transition: transform 0.3s;
            }

            .difl-zoomout:hover {
                transform: scale(0.8);
            }

            /* Move Up Animation with Hover Effect */
            .difl-moveup {
                transition: transform 0.3s;
            }

            .difl-moveup:hover {
                transform: translateY(-20px);
            }

            /* Move Down Animation with Hover Effect */
            .difl-movedown {
                transition: transform 0.3s;
            }

            .difl-movedown:hover {
                transform: translateY(20px);
            }


            /*Icon*/
            <?php if ('none'!==$media && 'icon' === $media){
                $desktop = empty($this->get_responsive_sizes($icon_size)) ? '16px' :$this->get_responsive_sizes($icon_size);
                $tablet = empty($this->get_responsive_sizes($icon_size, 'tablet')) ? '16px' :$this->get_responsive_sizes($icon_size, 'tablet');
                $mobile = empty($this->get_responsive_sizes($icon_size, 'mobile')) ? '16px' :$this->get_responsive_sizes($icon_size, 'mobile');
                //phpcs:disable -- Output escaped properly prior to template rendering
                ?>

            body .et_pb_scroll_top .difl-btt-media {
                color: <?php echo $icon_color;?>;
                font-family: ETModules;
                font-size: <?php echo $desktop ?>;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            @media screen and (min-width: 426px) and (max-width: 1024px) {
                body .et_pb_scroll_top .difl-btt-media {
                    font-size: <?php echo $tablet; ?>;
                }
            }

            @media screen and (max-width: 425px) {
                body .et_pb_scroll_top .difl-btt-media {
                    font-size: <?php echo $mobile ?>;
                }
            }

            body .et_pb_scroll_top:hover .difl-btt-media {
                color: <?php echo $icon_hover_color;?>;
                content: '<?php echo $icon?>';
                transition: .3s;
            }

            <?php } ?>
            /*Image*/
            <?php if ('none'!==$media && 'image' === $media) {
                $desktop = $this->get_responsive_sizes($image_size);
                $tablet = $this->get_responsive_sizes($image_size, 'tablet');
                $mobile = $this->get_responsive_sizes($image_size, 'mobile');
                ?>

            body .et_pb_scroll_top .difl-btt-media {
                background-image: <?php echo $styles['image'] ?>;
                width: <?php echo $desktop ?>;
                height: <?php echo $desktop?>;
                content: '';
                font-family: initial;
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
            }

            @media screen and (min-width: 426px) and (max-width: 1024px) {
                body .et_pb_scroll_top .difl-btt-media {
                    width: <?php echo $tablet ?>;
                    height: <?php echo $tablet?>;
                }

            }

            @media screen and (max-width: 425px) {
                body .et_pb_scroll_top .difl-btt-media {
                    width: <?php echo $mobile ?>;
                    height: <?php echo $mobile?>;
                }
            }

            <?php }?>

            /*None*/
            <?php if ('none' === $media ){?>

            body .et_pb_scroll_top .difl-btt-media {
                content: initial;
            }

            <?php }?>
            /*Position*/
            <?php if ('left'===$position){?>
            body .et_pb_scroll_top {
                left: <?php echo esc_html( $side_offset)?> !important;
                right: auto !important;
            }

            body .et_pb_scroll_top.et-hidden {
                opacity: 0;
                -webkit-animation: fadeOutLeft 1s cubic-bezier(.77, 0, .175, 1) 1;
                animation: fadeOutLeft 1s cubic-bezier(.77, 0, .175, 1) 1
            }

            body .et_pb_scroll_top.et-visible {
                opacity: 1;
                -webkit-animation: fadeInLeft 1s cubic-bezier(.77, 0, .175, 1) 1;
                animation: fadeInLeft 1s cubic-bezier(.77, 0, .175, 1) 1
            }

            <?php }?>

            <?php if ('right'===$position){?>
            body .et_pb_scroll_top {
                right: <?php echo esc_html( $side_offset)?> !important;
                left: auto !important;
            }

            <?php }?>

            /*Label*/
            body .et_pb_scroll_top .difl-btt-label:empty {
                display: none;
            }
            <?php if (!empty($label)){
                $desktop = empty($this->get_responsive_sizes($label_font_size)) ? '16px' :$this->get_responsive_sizes($label_font_size);
                $tablet = empty($this->get_responsive_sizes($label_font_size, 'tablet')) ? '16px' :$this->get_responsive_sizes($label_font_size, 'tablet');
                $mobile = empty($this->get_responsive_sizes($label_font_size, 'mobile')) ? '16px' :$this->get_responsive_sizes($label_font_size, 'mobile');
                ?>
            body .et_pb_scroll_top .difl-btt-label {
                content: '<?php echo ($label) ?>';
                font-family: var(--difl--btt--font);
                font-weight: var(--difl--btt--font--weight);
                font-size: <?php echo $desktop;?>;
                color: <?php echo $label_font_color;?>
            }

            <?php if ('vertically' === $alignment){?>
            body .et_pb_scroll_top .difl-btt-label {
                writing-mode: vertical-lr;
                transform: rotate(180deg);
            }

            }
            <?php }?>

            @media screen and (min-width: 426px) and (max-width: 1024px) {
                body .et_pb_scroll_top .difl-btt-label {
                    font-size: <?php echo $tablet; ?>;
                }
            }

            @media screen and (max-width: 425px) {
                body .et_pb_scroll_top .difl-btt-label {
                    font-size: <?php echo $mobile ?>;
                }
            }

            body .et_pb_scroll_top:hover .difl-btt-label {
                color: <?php echo $label_font_hover_color;?>
            }

            <?php }?>

            body .et_pb_scroll_top {
                margin: <?php echo $margin_desktop;?> !important;
                padding: <?php echo $padding_desktop;?> !important;
                border-radius: <?php echo $border_radius_desktop;?> !important;
                bottom: <?php echo esc_html($bottom_offset)?> !important;
                display: flex !important;
                align-items: center;
                gap: <?php echo $space_between;?>;
                flex-direction: <?php echo esc_html($styles['alignment']);?> !important;
            <?php echo esc_html($styles['background'])?>: <?php echo esc_html($background_color)?> !important;
            }

            @media screen and (min-width: 426px) and (max-width: 1024px) {
                body .et_pb_scroll_top {
                    margin: <?php echo $margin_tablet;?> !important;
                    padding: <?php echo $padding_tablet;?> !important;
                    border-radius: <?php echo $border_radius_tablet;?> !important;
                }
            }

            @media screen and (max-width: 425px) {
                body .et_pb_scroll_top {
                    margin: <?php echo $margin_mobile;?> !important;
                    padding: <?php echo $padding_mobile;?> !important;
                    border-radius: <?php echo $border_radius_mobile;?> !important;
                }
            }

            body .et_pb_scroll_top:hover {
            <?php echo esc_html($styles['background_hover'])?>: <?php echo esc_html($background_color_hover)?> !important;
            }

            body .et_pb_scroll_top.et_pb_scroll_top.et-hidden {
                transition: 1s visibility;
                visibility: hidden;
            }

            body .et_pb_scroll_top.et_pb_scroll_top.et-visible {
                visibility: visible;
            }
           <?php if ( 'icon' === $media || 'none' === $media ) { ?>
            body .et_pb_scroll_top .difl-btt-media:empty {
                display: none;
            }
            <?php } ?>
        </style>

        <script>
			(function () {
				let found = false;

				const load = () => {
					const select = selector => document.querySelector( selector );
					const selectAll = selector => document.querySelectorAll( selector );
					let btt_btn = [ ...selectAll( 'body .et_pb_scroll_top' ) ];

					if ( ! btt_btn.length ) {
						return;
					}

					if ( btt_btn.length ) {
						found = true;
					}

					if ( btt_btn.length > 1 ) {
						btt_btn[btt_btn.length - 1].remove();
					}
					btt_btn = btt_btn[0];

					const btn_style = btt_btn.style;
                    //btt_btn.setAttribute('data-difl_settings', '<?php //echo json_encode($this->settings);?>//');
					let media = `<span class='difl-btt-media'><?php echo ( 'icon' === $media ? $icon : '' ) ?></span>`;

					const label = `<span class='difl-btt-label'><?php echo $label?></span>`
                    btt_btn.innerHTML = `${media}${label}`;

					btt_btn.classList.add( "<?php echo 'none' === $hover_animation ? '' : $hover_animation;?>" );
					<?php if ($hide_on_mobile){?>
					const handleMediaQueryChange = ( mediaQueryList ) => {
						const mediaQuery = window.matchMedia( '(max-width: 720px)' );

						if ( mediaQuery.matches ) {
							btt_btn.remove();
						}
					};

					handleMediaQueryChange();
					window.addEventListener( 'resize', handleMediaQueryChange );
					<?php };?>
					window.addEventListener( 'DOMContentLoaded', () => {
						if ( select( 'body' ).offsetTop === 0 && ! btt_btn.classList.contains( 'et-hidden' ) ) {
							btn_style.display = 'none!important';
							// btt_btn.classList.add( 'et-hidden' );
						}
					} );

					const computed = getComputedStyle( select( '#page-container' ) );
					btn_style.setProperty( '--difl--btt--font', computed.fontFamily );
					btn_style.setProperty( '--difl--btt--font--weight', computed.fontWeight );
					btn_style.setProperty( '--difl--btt--font--size', computed.fontSize );

					window.addEventListener( 'scroll', () => {
						if ( btt_btn.classList.contains( 'et-hidden' ) ) {
							btn_style.display = 'none!important';
						}
					} );

				}

				let intervalId = setInterval( () => {
					load();
					if ( found ) {
						clearInterval( intervalId )
					}
				}, 50 )
			})();
        </script>
		<?php
		//phpcs:enable
	}
}