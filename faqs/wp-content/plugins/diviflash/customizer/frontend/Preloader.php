<?php

namespace DIFL\Customizer\Frontend;

class Preloader extends Frontend {
	protected $settings = [];

	const PREFIX = 'difl_preloader';

	public function __construct() {
		$this->populate_settings();
		add_action( 'wp_head', [ $this, 'add_preloader_style' ], - PHP_INT_MAX );
		add_action( 'wp_body_open', [ $this, 'add_preloader' ], - PHP_INT_MAX );
	}

	public function add_preloader() {

		if ( $this->is_vb_or_tb() || is_customize_preview() ) {
			return;
		}

		if ( ! \DIFL\Customizer\Extensions\Preloader::is_extension_enabled() ) {
			return;
		}

		if ( ! is_front_page() && \DIFL\Customizer\Extensions\Preloader::enable_for_homepage() ) {
			return;
		}

		$preloader_type  = esc_attr( $this->get_value( 'type', 'preset' ) );
		$preset          = esc_attr( $this->get_value( 'preset_type', 'ball-pulse' ) );
		$image_url       = ! empty( $this->get_value( 'image' ) ) && is_int( $this->get_value( 'image' ) ) ? wp_get_attachment_image_url( $this->get_value( 'image' ) ) : $this->get_value( 'image' );
		$image_alt       = get_the_title( $this->get_value( 'image' ) );
		$svg_text        = esc_html( $this->get_value( 'text', 'PRELOADER TEXT' ) );
		$scale           = (float) $this->get_value( 'element_scale', 1 );
		$icon_color      = esc_attr( $this->get_value( 'icon_color', 'var(--difl--icon--color)' ) );
		$reveal_anim     = esc_attr( $this->get_value( 'reveal_animation', 'fade' ) );
		$native_loading  = isset( $this->settings[ self::PREFIX . '_enable_native_preloader' ] ) ? $this->settings[ self::PREFIX . '_enable_native_preloader' ] : true;
		$reveal_delay    = empty( $native_loading ) ? $this->get_value( 'reveal_delay', 300 ) . 'ms' : '300ms';
		$reveal_duration = empty( $native_loading ) ? $this->get_value( 'reveal_duration', 300 ) . 'ms' : '300ms';

		//phpcs:disable -- Output escaped properly prior to template rendering
		?>
        <div class="difl_preloader_wrapper ">
            <div class="difl_preloader_wrapper_inner <?php echo 'preset' === $preloader_type ? $preset : $preloader_type; ?>">
				<?php if ( 'preset' === $preloader_type ) {
					echo str_repeat( '<div></div>', \DIFL\Customizer\Extensions\Preloader::PRESET[ $preset ] );
				} ?>
				<?php if ( 'image' === $preloader_type ) { ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
				<?php } ?>
				<?php if ( 'text' === $preloader_type ) { ?>
                    <svg xmlns='http://www.w3.org/2000/svg' preserveAspectRatio="xMidYMid">
                        <text x='50%' y='50%' dy='.3em' text-anchor="middle" class="svg-text">
							<?php echo esc_html( $svg_text ); ?>
                        </text>
                    </svg>
				<?php } ?>
            </div>
        </div>

        <script>
			// Handle effect, duration etc, some element has exception
			(() => {
				let found = false;

				const load = () => {
					const select = selector => document.querySelector( selector );
					let wrapper = select( 'body .difl_preloader_wrapper' );

					if ( ! wrapper ) {
						return;
					}

					if ( wrapper ) {
						found = true;
					}

					const handle_presets = () => {
						const setWH = element => {
							element.style.width = element.offsetWidth * <?php echo $scale;?>+'px';
							element.style.height = element.offsetHeight * <?php echo $scale;?>+'px';
						}

						const elements = [ ...document.querySelectorAll( '.difl_preloader_wrapper .difl_preloader_wrapper_inner div' ) ];
						const exceptional_elements = [ ...document.querySelectorAll( '.difl_preloader_wrapper .difl_preloader_wrapper_inner.ball-grid-pulse,.difl_preloader_wrapper .difl_preloader_wrapper_inner.ball-grid-beat' ) ];
						const spin_fade = [ ...document.querySelectorAll( '.difl_preloader_wrapper .difl_preloader_wrapper_inner.line-spin-fade-loader div,.difl_preloader_wrapper .difl_preloader_wrapper_inner.ball-spin-fade-loader div' ) ];
						// handle delay and duration

						elements.forEach( element => {
							setWH( element )
							element.style.borderColor = "<?php echo $icon_color;?>";
						} )

						exceptional_elements.forEach( element => {
							element.style.width = element.offsetWidth * <?php echo $scale;?>+'px';
						} )

						spin_fade.forEach( element => {
							element.style.borderRadius = window.getComputedStyle( element ).borderRadius.replace( 'px', '' ) * <?php echo $scale;?>+'px'
							element.style.top = window.getComputedStyle( element ).top.replace( 'px', '' ) * <?php echo $scale;?>+'px'
							element.style.left = window.getComputedStyle( element ).left.replace( 'px', '' ) * <?php echo $scale;?>+'px'
						} )
					}

					<?php if ( $native_loading ) { ?>
					const loadOnComplete = () => {
						wrapper.classList.remove( "<?php echo 'difl-preloader-' . $reveal_anim; ?>" );
						wrapper.classList.add( 'finished' );
						document.querySelector( '#page-container' ).style.display = 'block';
						document.querySelector( '#page-container' ).style.visibility = 'visible';
						document.querySelector( '#page-container' ).style.opacity = 1;
						setTimeout( () => {
							wrapper.style.opacity = 0;
							wrapper.classList.remove( 'finished' );
							wrapper.style.display = 'none';
						}, 300 )
					}
					wrapper.style.display = 'flex';
					handle_presets();
					wrapper.classList.add( "<?php echo 'difl-preloader-' . $reveal_anim; ?>" );
					if ( document.readyState === 'complete' ) {
						loadOnComplete();
					}
					window.addEventListener( 'load', loadOnComplete );
					<?php } else { ?>
					const loadOnDomLoad = () => {
						setTimeout( function () {
							const wrapper = select( '.difl_preloader_wrapper' );
							wrapper.style.display = 'flex';
							handle_presets();
							wrapper.classList.add( "<?php echo 'difl-preloader-' . $reveal_anim; ?>" );
							setTimeout( function () {
								wrapper.classList.remove( "<?php echo 'difl-preloader-' . $reveal_anim; ?>" );
								wrapper.classList.add( 'finished' );
								setTimeout( () => {
									wrapper.classList.remove( 'finished' );
									wrapper.style.display = 'none';
								},<?php echo esc_html( str_replace( 'ms', '', $reveal_duration ) );?>)
							}, <?php echo esc_html( str_replace( 'ms', '', $reveal_duration ) );?>);
						}, <?php echo esc_html( str_replace( 'ms', '', $reveal_delay ) );?>);
					}
					if ( document.readyState === 'interactive' ) {
						loadOnDomLoad();
					}
					window.addEventListener( 'DOMContentLoaded', loadOnDomLoad );
					<?php }?>

					// svg
					const handleViewBox = () => {
						let svg = select( '#et-main-area .difl_preloader_wrapper .difl_preloader_wrapper_inner.text svg' );
						if ( ! svg ) {
							return;
						}
						const bbox = svg.getBBox();
						svg.setAttribute( 'viewBox', `0 0 ${ bbox.width } ${ bbox.height }` );
						svg.setAttribute( 'width', `${ bbox.width }` );
						svg.setAttribute( 'height', `${ bbox.height }` );
					}

					window.addEventListener( 'DOMContentLoaded', () => {
						handleViewBox();
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
		//phpcs::enable
	}

	public function add_preloader_style() {
		if ( $this->is_vb_or_tb() ) {
			return;
		}

		$styles                     = [];
		$preloader_type             = esc_attr( $this->get_value( 'type', 'preset' ) );
		$svg_text_type              = esc_html( $this->get_value( 'text_type', 'one' ) );
		$svg_text_container_padding = $this->get_value( 'text_container_padding' );
		$svg_text_anim_duration     = esc_attr( $this->get_value( 'text_animation_duration', 4000 ) . 'ms' );
		$svg_text_letter_spacing    = esc_attr( $this->get_value( 'text_letter_spacing', - 6 ) . 'px' );
		$svg_text_word_spacing      = esc_attr( $this->get_value( 'text_word_spacing', 5 ) . 'px' );
		$svg_text_thickness         = $this->get_value( 'text_stroke_width' );
		$svg_text_font_size         = $this->get_value( 'text_font_size' );
		$svg_text_stroke_color      = esc_attr( $this->get_value( 'text_stroke_color', '#d0e7ca' ) );
		$svg_text_fill_color        = esc_attr( $this->get_value( 'text_fill_color', '#d0e7ca' ) );
		$icon_color                 = esc_attr( $this->get_value( 'icon_color', 'var(--difl--icon--color)' ) );
		$bg_color                   = esc_attr( $this->get_value( 'background_color', 'var(--difl--brand--color)' ) );
		$reveal_anim                = esc_attr( $this->get_value( 'reveal_animation', 'fade' ) );
		$native_loading             = $this->get_value( 'enable_native_preloader' );
		$reveal_delay               = empty( $native_loading ) ? $this->get_value( 'reveal_delay', 300 ) . 'ms' : '300ms';
		$reveal_duration            = empty( $native_loading ) ? $this->get_value( 'reveal_duration', 300 ) . 'ms' : '300ms';
		$styles['background']       = $this->get_bg_type( $bg_color );
		$image_size                 = $this->get_value( 'image_size', '{"mobile": "160", "tablet": "160", "desktop": "160" }' );
		$desktop                    = $this->get_responsive_sizes( $image_size );
		$tablet                     = $this->get_responsive_sizes( $image_size, 'tablet' );
		$mobile                     = $this->get_responsive_sizes( $image_size, 'mobile' );

		if ( ! \DIFL\Customizer\Extensions\Preloader::is_extension_enabled() ) {
			return;
		}

		if ( ! is_front_page() && \DIFL\Customizer\Extensions\Preloader::enable_for_homepage() ) {
			return;
		}
		?>
        <style>
            /*Style goes here*/
            <?php if ($native_loading) { ?>
            #page-container {
                visibility: hidden;
                opacity: 0;
                display: none;
            }

            <?php } ?>

            :root {
                --difl--brand--color: #F0E9FE;
                --difl--icon--color: #FFF;
            }

            /*Make container invisible at the beginning*/
            /* Container Animations */
            .difl-preloader-fade {
                animation: difl-preloader-fade <?php echo esc_html($reveal_delay);?> forwards;
            }

            .difl_preloader_wrapper .difl_preloader_wrapper_inner img {
                width: <?php echo $desktop ?>;
            }

            @media screen and (min-width: 426px) and (max-width: 1024px) {
                .difl_preloader_wrapper .difl_preloader_wrapper_inner img {
                    width: <?php echo $tablet ?>;
                }

            }

            @media screen and (max-width: 425px) {
                .difl_preloader_wrapper .difl_preloader_wrapper_inner img {
                    width: <?php echo $mobile ?>;
                }
            }

            @keyframes difl-preloader-fade {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                }
            }

            @keyframes difl-preloader-fade-backwards {
                from {
                    opacity: 1;
                }
                to {
                    opacity: 0;
                }
            }

            /* Slide Up */
            .difl-preloader-slideup {
                animation: difl-preloader-slideup <?php echo esc_html($reveal_delay);?> forwards;
            }

            @keyframes difl-preloader-slideup {
                from {
                    transform: translateY(100%);
                }
                to {
                    transform: translateY(0);
                }
            }

            @keyframes difl-preloader-slideup-backwards {
                from {
                    transform: translateY(0);
                }
                to {
                    transform: translateY(100%);
                }
            }

            /* Slide Down */
            .difl-preloader-slidedown {
                animation: difl-preloader-slidedown <?php echo esc_html($reveal_delay);?> forwards;
            }

            @keyframes difl-preloader-slidedown {
                from {
                    transform: translateY(-100%);
                }
                to {
                    transform: translateY(0);
                }
            }

            @keyframes difl-preloader-slidedown-backwards {
                from {
                    transform: translateY(0);
                }
                to {
                    transform: translateY(-100%);
                }
            }

            /* Slide Right */
            .difl-preloader-slideright {
                animation: difl-preloader-slideright <?php echo esc_html($reveal_delay);?> forwards;
            }

            @keyframes difl-preloader-slideright {
                from {
                    transform: translateX(-100%);
                }
                to {
                    transform: translateX(0);
                }
            }

            @keyframes difl-preloader-slideright-backwards {
                from {
                    transform: translateX(0);
                }
                to {
                    transform: translateX(-100%);
                }
            }

            /* Slide Left */
            .difl-preloader-slideleft {
                animation: difl-preloader-slideleft <?php echo esc_html($reveal_delay);?> forwards;
            }

            @keyframes difl-preloader-slideleft {
                from {
                    transform: translateX(100%);
                }
                to {
                    transform: translateX(0);
                }
            }

            @keyframes difl-preloader-slideleft-backwards {
                from {
                    transform: translateX(0);
                }
                to {
                    transform: translateX(100%);
                }
            }

            /* Zoom In */
            .difl-preloader-zoomin {
                animation: difl-preloader-zoomin <?php echo esc_html($reveal_delay);?> forwards;
            }

            @keyframes difl-preloader-zoomin {
                from {
                    transform: scale(0);
                }
                to {
                    transform: scale(1);
                }
            }

            @keyframes difl-preloader-zoomin-backwards {
                from {
                    transform: scale(1);
                }
                to {
                    transform: scale(0);
                }
            }

            /*Colors*/
            body .difl_preloader_wrapper {
                display: none;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                z-index: 10000000;
            <?php echo esc_html($styles['background'])?>: <?php echo esc_html($bg_color)?> !important;
            }

            .difl_preloader_wrapper.finished {
                animation-name: <?php echo 'difl-preloader-' . $reveal_anim.'-backwards'; ?>;
                animation-duration: <?php echo $reveal_duration;?>;
            }

            .difl_preloader_wrapper .difl_preloader_wrapper_inner div {
                background-color: <?php echo $icon_color;?>
            }

            /*SVG text styles*/
            <?php if ('text'===$preloader_type){
                 $desktop_container_padding = empty($this->get_responsive_sizes($svg_text_container_padding)) ? '10%' :str_replace('px','%',$this->get_responsive_sizes($svg_text_container_padding));
                $tablet_container_padding = empty($this->get_responsive_sizes($svg_text_container_padding, 'tablet')) ? '8%' :str_replace('px','%',$this->get_responsive_sizes($svg_text_container_padding, 'tablet'));
                $mobile_container_padding = empty($this->get_responsive_sizes($svg_text_container_padding,'mobile')) ? '5%' :str_replace('px','%',$this->get_responsive_sizes($svg_text_container_padding, 'mobile'));

                $desktop_font_size = empty($this->get_responsive_sizes($svg_text_font_size)) ? '120px' :$this->get_responsive_sizes($svg_text_font_size);
                $tablet_font_size = empty($this->get_responsive_sizes($svg_text_font_size, 'tablet')) ? '100px' :$this->get_responsive_sizes($svg_text_font_size, 'tablet');
                $mobile_font_size = empty($this->get_responsive_sizes($svg_text_font_size, 'mobile')) ? '80px' :$this->get_responsive_sizes($svg_text_font_size, 'mobile');

                 $desktop_stroke_thickness = empty($this->get_responsive_sizes($svg_text_thickness)) ? '4px' :$this->get_responsive_sizes($svg_text_thickness);
                $tablet_stroke_thickness = empty($this->get_responsive_sizes($svg_text_thickness, 'tablet')) ? '5px' :$this->get_responsive_sizes($svg_text_thickness, 'tablet');
                $mobile_stroke_thickness= empty($this->get_responsive_sizes($svg_text_thickness,'mobile')) ? '4px' :$this->get_responsive_sizes($svg_text_thickness, 'mobile');
                ?>
            .difl_preloader_wrapper .difl_preloader_wrapper_inner.text {
                width: 100%;
                padding: <?php echo $desktop_container_padding;?>;
                height: auto;
                text-align: center;
            }

            .difl_preloader_wrapper .difl_preloader_wrapper_inner.text svg {
                width: 100%;
                font-weight: 700;
            }

            .difl_preloader_wrapper .difl_preloader_wrapper_inner.text svg text {
                font-size: <?php echo esc_attr($desktop_font_size)?>;
                stroke: <?php echo $svg_text_stroke_color;?>;
                fill: <?php echo $svg_text_fill_color;?>;
                stroke-width: <?php echo $desktop_stroke_thickness;?>;
                letter-spacing: <?php echo $svg_text_letter_spacing;?>;
                word-spacing: <?php echo $svg_text_word_spacing;?>;
                animation: <?php echo $svg_text_anim_duration;?> infinite alternate difl-text-animate-preset-<?php echo $svg_text_type;?>
            }

            @media screen and (min-width: 426px) and (max-width: 1024px) {
                .difl_preloader_wrapper .difl_preloader_wrapper_inner.text {
                    padding: <?php echo $tablet_container_padding;?>;
                }

                .difl_preloader_wrapper .difl_preloader_wrapper_inner.text svg text {
                    font-size: <?php echo esc_attr($tablet_font_size)?>;
                    stroke-width: <?php echo esc_attr($tablet_stroke_thickness)?>;
                }
            }

            @media screen and (max-width: 425px) {
                .difl_preloader_wrapper .difl_preloader_wrapper_inner.text {
                    padding: <?php echo $mobile_container_padding;?>;
                }

                .difl_preloader_wrapper .difl_preloader_wrapper_inner.text svg text {
                    font-size: <?php echo esc_attr($mobile_font_size)?>;
                    stroke-width: <?php echo esc_attr($mobile_stroke_thickness)?>;
                }
            }

            @keyframes difl-text-animate-preset-one {
                0% {
                    fill: transparent;
                    stroke-dashoffset: 25%;
                    stroke-dasharray: 0 32%;
                }

                50% {
                    fill: transparent;
                }

                80%, 100% {
                    fill: <?php echo $svg_text_fill_color;?>;
                    stroke-dashoffset: -25%;
                    stroke-dasharray: 32% 0;
                }
            }

            @keyframes difl-text-animate-preset-two {
                0% {
                    fill: transparent;
                    stroke-dashoffset: 20%;
                    stroke-dasharray: 0 40%;
                }

                30% {
                    fill: transparent;
                    stroke-dashoffset: 10%;
                    stroke-dasharray: 20% 20%;
                }

                60% {
                    fill: transparent;
                    stroke-dashoffset: -10%;
                    stroke-dasharray: 40% 0;
                }

                90%, 100% {
                    fill: <?php echo $svg_text_fill_color;?>;
                    stroke-dashoffset: -30%;
                    stroke-dasharray: 50% 0;
                }
            }

            @keyframes difl-text-animate-preset-three {
                0% {
                    fill: transparent;
                    stroke-dashoffset: 30%;
                    stroke-dasharray: 0 50%;
                }

                40% {
                    fill: transparent;
                    stroke-dashoffset: 15%;
                    stroke-dasharray: 25% 25%;
                }

                70% {
                    fill: transparent;
                    stroke-dashoffset: 0;
                    stroke-dasharray: 50% 0;
                }

                100% {
                    fill: <?php echo $svg_text_fill_color;?>;
                    stroke: transparent;
                    stroke-width: <?php echo $desktop_stroke_thickness?>;
                    stroke-dashoffset: 0;
                    stroke-dasharray: 50% 0;
                }
            }

            @keyframes difl-text-animate-preset-four {
                0% {
                    fill: transparent;
                    stroke-dashoffset: 40%;
                    stroke-dasharray: 0 60%;
                }

                25% {
                    fill: transparent;
                    stroke-dashoffset: 20%;
                    stroke-dasharray: 30% 30%;
                }

                50% {
                    fill: transparent;
                    stroke-dashoffset: 0;
                    stroke-dasharray: 60% 0;
                }

                75%, 100% {
                    fill: <?php echo $svg_text_fill_color;?>;
                    stroke-dashoffset: -20%;
                    stroke-dasharray: 60% 0;
                }
            }

            <?php }?>
        </style>
		<?php
	}
}