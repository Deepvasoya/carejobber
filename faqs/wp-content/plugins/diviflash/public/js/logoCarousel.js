
(function ($, bxSlider) {
    $(document).ready(function(){
        df_init_logo_carousel();
        [ ...document.querySelectorAll( '.difl_logocarouselitem' ) ].filter( li => ! li.querySelector( '.df_lci_container' ) ).forEach( li => li.remove() )
    })
    function df_init_logo_carousel() {
        var container = document.querySelectorAll('.df_lc_container');


        container.forEach(each_container => {
            each_container.querySelectorAll("a").forEach(link => {
                let touchStartX = 0;
                let touchStartY = 0;
                let touchEndX = 0;
                let touchEndY = 0;

                // Capture the start position
                link.addEventListener("touchstart", function (event) {
                    touchStartX = event.touches[0].clientX;
                    touchStartY = event.touches[0].clientY;
                }, { passive: true });

                // Capture the end position
                link.addEventListener("touchend", function (event) {
                    touchEndX = event.changedTouches[0].clientX;
                    touchEndY = event.changedTouches[0].clientY;

                    // Calculate movement distance
                    let deltaX = Math.abs(touchEndX - touchStartX);
                    let deltaY = Math.abs(touchEndY - touchStartY);

                    // If there was very little movement, treat it as a click
                    if (deltaX < 10 && deltaY < 10) {
                        if (this.target === "_blank") {
                            window.open(this.href, "_blank");
                        } else {
                            window.location.href = this.href;
                        }
                    }
                }, { passive: true });
            });
        });

        $('.df_lc_container').each(function(index, ele) {
            var _this = $(this);
            var data = JSON.parse(ele.dataset.settings);
            var config = {
                minSlides: parseInt(data.desktop),
                maxSlides: parseInt(data.desktop),
                speed: parseInt(data.speed),
                slideMargin: parseInt(data.spacingbetween),
                slideWidth: parseInt(data.width),
                moveSlides: 1,
                shrinkItems: true,
                responsive: true,
                adaptiveHeight: false,
            }
            if (data.ticker !== 'on') {
                config['infiniteLoop'] = data.loop;
                config['pager'] = data.dots;
                config['controls'] = data.arrows;
                config['auto'] = data.autoplay;
                config['pause'] = parseInt(data.auto_delay);
                config['autoHover'] = data.pause_hover;
                config['prevText'] = '4';
                config['nextText'] = '5';
                config['hideControlOnEnd'] = !data.loop;
            } else {
                config['ticker'] = true;
                config['tickerHover'] = data.ticker_hover;
                config['speed'] = parseInt(data.ticker_speed);
            }

            if ( $(window).width() < 981 ) {
                config['minSlides'] = parseInt(data.tablet);
                config['maxSlides'] = parseInt(data.tablet);
            }
            if ( $(window).width() < 768 ) {
                config['minSlides'] = parseInt(data.mobile);
                config['maxSlides'] = parseInt(data.mobile);
            }

            var df_lc_slider = _this.bxSlider(config);

            $(window).resize(function(){
                config['minSlides'] = parseInt(data.desktop);
                config['maxSlides'] = parseInt(data.desktop);
                if ( $(window).width() < 981 ) {
                    config['minSlides'] = parseInt(data.tablet);
                    config['maxSlides'] = parseInt(data.tablet);
                }
                if ( $(window).width() < 768 ) {
                    config['minSlides'] = parseInt(data.mobile);
                    config['maxSlides'] = parseInt(data.mobile);
                }
                df_lc_slider.reloadSlider();
            })

            $(window).scroll(function(){
                df_lc_slider.redrawSlider();
            })
            if (data.ticker !== 'on' && data.autoplay) {
                $( ele ).mouseleave( function(){
                    df_lc_slider.startAuto();
                } )
            }
        });

    }
})(jQuery)

