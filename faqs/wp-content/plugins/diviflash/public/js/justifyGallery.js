(function($){
    if (jQuery().justifiedGallery) {
        jQuery('.df_jsg_container').each(function(index, element) {

            var settings = JSON.parse(element.dataset.settings);
            var _this = jQuery(this);
            var image_obj = settings.gallery.split(",");
            var image_count = parseInt(settings.image_count);
            var target = settings.url_target;
            var lg_options = {
                use_lightbox: settings.use_lightbox,
                download: settings.use_lightbox_download === 'on' ? true : false
            }

            if (typeof imagesLoaded === "function") {
                // js gallery
                df_init_jsgallery(
                    _this.find('.justified-gallery'), 
                    settings
                );

                // load more functionality
                _this.find('.jsg-more-image-btn')
                .on('click', function(event) {
                    event.preventDefault();
                    event.target.classList.add('loading')

                    var pageCount = parseInt(event.target.dataset.page);
                    var loaded = parseInt(event.target.dataset.loaded);

                    fetch(window.et_pb_custom.ajaxurl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'Cache-Control': 'no-cache',
                        },
                        body: new URLSearchParams({
                            et_frontend_nonce: window.et_pb_custom.et_frontend_nonce,
                            action: 'df_jsg_fetch',
                            gallery: settings.gallery,
                            image_count: image_count,
                            show_caption: settings.show_caption,
                            ini_count: settings.ini_count,
                            show_content_lg: settings.show_content_lg,
                            loaded: loaded,
                            options: JSON.stringify(settings)
                        })
                    })
                    .then(function (response) { return response.json()})
                    .then(function (response) {
                        let parsedHtml = jQuery.parseHTML(response.data);


                        if ( loaded >= image_obj.length) {
                            event.target.style.display = "none";
                        } else {
                            _this
                            .find('.justified-gallery')
                            .append(parsedHtml)
                            loaded = loaded + image_count;
                            event.target.setAttribute("data-loaded", loaded);
                            if(loaded >= image_obj.length){event.target.style.display = "none";}
                        }
                        event.target.classList.remove('loading')
                        df_jsg_use_lightbox(
                            element
                            .querySelector('.justified-gallery'),
                            lg_options
                        )
                    })
                    .then(function() {
                        df_init_jsgallery(
                            _this.find('.justified-gallery'), 
                            settings
                        );
                    })
                })

                // pagination functionality start
                const pageElem = element.querySelector('.df-jsg-pagination');
                if (pageElem !== null) {
                    const pageTags = pageElem.querySelectorAll('a');

                    pageElem.addEventListener( 'click', function( event ) {
                        event.preventDefault();
                        window.scrollTo({ top: $(element).offset().top-150, behavior: 'smooth' });
                        // Ditect mouse click outside button
                        if( !event.target.classList.contains('page-numbers')){
                            return;
                        }
                        let firstPage,lastPage;
                        for ( var i=0, len = pageTags.length; i < len; i++ ) {
                            var pageTag = pageTags[i];
                            pageTag.classList.remove('current');
                        }

                        // only work with buttons
						if ( ! event.target.classList.contains( 'page-numbers' ) ) {
							return;
						}

                        let pageNumber = '';
                        const imgCount = event.target.getAttribute('data-count');
                        if('on' === settings.use_number_pagination){
                            if(event.target.classList.contains('prev')){
                                const currentPage = event.target.getAttribute('data-current');
                                if(currentPage === pageTags[1].getAttribute('data-page')){
                                    pageTags[1].classList.add('current');
                                    return;
                                }
                                pageNumber = parseInt(currentPage)-1;
                                pageTags[pageNumber].classList.add('current');
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            else if(event.target.classList.contains('next')){
                                const currentPage = event.target.getAttribute('data-current');
                                if(currentPage === pageTags[(parseInt(pageTags.length)-2)].getAttribute('data-page')){
                                    pageTags[pageTags.length-2].classList.add('current');
                                    return;
                                }
                                pageNumber = parseInt(currentPage)+1;
                                pageTags[pageNumber].classList.add('current');
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            else{
                                pageNumber = event.target.getAttribute('data-page');
                                event.target.classList.add('current');
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            (parseInt(pageNumber) > 1)?pageTags[0].style.display = 'flex':pageTags[0].style.display = 'none';
                            (parseInt(pageNumber) === pageTags.length-2)?pageTags[pageTags.length-1].style.display = 'none':pageTags[pageTags.length-1].style.display = 'flex';
                        }
                        else{
                            if(event.target.classList.contains('prev')){
                                const currentPage = event.target.getAttribute('data-current');
                                pageNumber = parseInt(currentPage)-1;
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            else if(event.target.classList.contains('next')){
                                const currentPage = event.target.getAttribute('data-current');
                                pageNumber = parseInt(currentPage)+1;
                                pageTags[0].setAttribute('data-current', pageNumber);
                                pageTags[pageTags.length-1].setAttribute('data-current', pageNumber);
                            }
                            (parseInt(pageNumber) > 1)?pageTags[0].style.display = 'block':pageTags[0].style.display = 'none';
                            (parseInt(pageNumber) === Math.ceil(settings.gallery.split(",").length/parseInt(imgCount)))?pageTags[pageTags.length-1].style.display = 'none':pageTags[pageTags.length-1].style.display = 'block';

                        }

                        const ajaxurl = window.et_pb_custom.ajaxurl;
                        fetch(ajaxurl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'Cache-Control': 'no-cache',
                            },
                            body: new URLSearchParams({
                                et_frontend_nonce: window.et_pb_custom.et_frontend_nonce,
                                action: 'df_jsg_fetch_page_data',
                                gallery: settings.gallery,
                                page: parseInt(pageNumber),
                                image_count: parseInt(settings.image_count),
                                options: JSON.stringify(settings)
                            })
                        })
                            .then(function(response) { return response.json()})
                            .then(function(response) {
                                let parsedHtml = jQuery.parseHTML(response.data);
                                if ( parseInt(imgCount) >= image_obj.length) {
                                    event.target.style.display = "none";
                                } else {
                                    _this
                                        .find('.justified-gallery')
                                        .empty()
                                        .append(parsedHtml)
                                    if(parseInt(imgCount) >= image_obj.length){event.target.style.display = "none";}
                                }
                                df_jsg_use_lightbox(
                                    element
                                        .querySelector('.justified-gallery'),
                                    lg_options
                                );
                            })
                            .then(function() {
                                df_init_jsgallery(
                                    _this.find('.justified-gallery'),
                                    settings
                                );
                            })
                    });
                }
                // pagination functionality end

                // lightbox functionality
                df_jsg_use_lightbox(
                    element
                    .querySelector('.justified-gallery'),
                    lg_options
                ); 
            }
            
            
        })
    }
    jQuery(document.body).on('click','.df_jsg_image', function(event) {
        if(jQuery(this).data('customurl')) {
            var url = jQuery(this).data('customurl');
            var target = jQuery(this).data('target')
            if (url !== '') {
                if (target === 'same_window') {
                    window.location = url;
                } else {
                    window.open(url)
                }
            }
        } 
    })
})(jQuery)

function df_init_jsgallery(selector, settings) {
    selector.imagesLoaded()
    .done(function(){
        selector.justifiedGallery({
            rowHeight : parseInt(settings.rowHeight),
            margins : parseInt(settings.margin),
            selector : 'figure, div:not(.spinner)',
            captions: false,
            imagesAnimationDuration: 500,
            waitThumbnailsLoad: false
        }).on('jg.complete', function(e){
            
            jQuery(this).find('img')
                .css('transition', 'all 600ms ease')
    
            jQuery(this).find('.df_jsg_image')
                .fadeIn("slow")
                .removeClass('image_loading');

            // support for lazy load for smush

            const lazyImages = [].slice.call( document.querySelectorAll( '.df_jsg_container img.lazyloaded' ) );
            lazyImages.forEach( function ( image ) {
                image.src = image.dataset.src;
            } )
        })
    })
    
}

function df_jsg_use_lightbox(selector, options) {
    if (options.use_lightbox === 'on') {
        var settings = {
            subHtmlSelectorRelative: true,
            addClass: 'df_jsg_lightbox', 
            counter: false,
            download: options.download
        };
        lightGallery(selector,settings);
    }
}

