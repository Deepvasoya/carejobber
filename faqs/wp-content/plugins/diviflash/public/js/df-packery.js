(function(){
    if (typeof imagesLoaded === "function" && typeof Packery === 'function') {
        var df_pg_gallery = document.querySelectorAll('.df_pg_container');
        [].forEach.call(df_pg_gallery, function(ele, index) {
            var options = JSON.parse(ele.dataset.settings);
            var selector = ele.querySelector('.df_pg_inner');
            var target = options.url_target;
            var image_obj = options.gallery.split(",");
            var image_count = parseInt(options.image_count);
            var pg_lightbox_options = {
                pg_lightbox: options.use_lightbox,
                download: options.use_lightbox_download === 'on' ? true : false
            };
            var pakry = new Packery(selector, {
                itemSelector: '.df_pg_item',
                percentPosition: true,
                gutter: 0
            })

            jQuery(window).resize(function(){
                df_pakry_layout(selector, pakry, options);
            })

            if (options['load_more'] === 'on') {
                ele.querySelector('.pg-more-image-btn')
                .addEventListener('click', function(event){
                    event.preventDefault();
                    ele.querySelector('.pg-more-image-btn')
                        .classList.add('loading')

                    var ajaxurl = window.et_pb_custom.ajaxurl;
                    var load_more = ele.querySelector('.pg-load-more-btn');
                    var loaded = parseInt(event.target.dataset.loaded);

                    fetch(ajaxurl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'Cache-Control': 'no-cache',
                        },
                        body: new URLSearchParams({
                            et_frontend_nonce: window.et_pb_custom.et_frontend_nonce,
                            action: 'df_pg_fetch',
                            gallery: options.gallery,
                            // page: load_more.dataset.page,
                            loaded: loaded,
                            image_count: parseInt(options.image_count),
                            options: JSON.stringify(options)
                        })
                    })
                    .then(function(response) { return response.json() })
                    .then(function(response) {
                        let parser = new DOMParser();
                        let parsedHtml = parser.parseFromString(response.data, 'text/html');
                        var items = parsedHtml.querySelectorAll('.df_pg_item');

                        if ( loaded >= image_obj.length) {
                            event.target.style.display = "none";
                        } else {
                            items.forEach(function(item) {
                                selector.appendChild(item)
                                pakry.appended( item )
                            })
                            loaded = loaded + image_count;
                            event.target.setAttribute("data-loaded", loaded);
                            if(loaded >= image_obj.length){event.target.style.display = "none";}
                        }

                        df_pakry_layout(selector, pakry, options);
                        ele.querySelector('.pg-more-image-btn')
                            .classList.remove('loading')

                        df_pg_url_open(target, ele, options.use_url);

                        df_pg_use_lightbox(
                            selector, 
                            pg_lightbox_options
                        );
                    })

                })
            }

            // pagination functionality start
            const pageElem = ele.querySelector('.df-pg-pagination');
            if (pageElem !== null) {
                const pageTags = pageElem.querySelectorAll('a');

                pageElem.addEventListener( 'click', function( event ) {
                    event.preventDefault();
                    window.scrollTo({ top: $(ele).offset().top-150, behavior: 'smooth' });
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
                    if ( !matchesSelector( event.target, 'a' ) ) {
                        return;
                    }

                    let pageNumber = '';
                    const imgCount = event.target.getAttribute('data-count');
                    if('on' === options.use_number_pagination){
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
                        (parseInt(pageNumber) === Math.ceil(options.gallery.split(",").length/parseInt(imgCount)))?pageTags[pageTags.length-1].style.display = 'none':pageTags[pageTags.length-1].style.display = 'block';

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
                            action: 'df_pg_fetch_page_data',
                            gallery: options.gallery,
                            page: parseInt(pageNumber),
                            image_count: parseInt(options.image_count),
                            options: JSON.stringify(options)
                        })
                    })
                        .then(function(response) { return response.json()})
                        .then(function(response) {
                            let parser = new DOMParser();
                            let parsedHtml = parser.parseFromString(response.data, 'text/html');
                            var items = parsedHtml.querySelectorAll('.df_pg_item');
                            selector.innerHTML = ''
                            if ( parseInt(imgCount) >= image_obj.length) {
                                event.target.style.display = "none";
                            } else {
                                items.forEach(function(item) {
                                    selector.appendChild(item)
                                    pakry.appended( item )
                                })
                                if(parseInt(imgCount) >= image_obj.length){event.target.style.display = "none";}
                            }
                            df_pakry_layout(selector, pakry, options);
                        })
                        .then(function() {
                            df_pg_url_open(target, ele, options.use_url);

                            df_pg_use_lightbox(
                                selector,
                                pg_lightbox_options
                            );
                        })
                });
            }
            // pagination functionality end

            df_pg_url_open(target, ele, options.use_url)
            
            df_pg_use_lightbox(
                selector, 
                pg_lightbox_options
            );
            
        })
    }
})()
function df_pakry_layout(selector, pakry, options) {
    imagesLoaded(selector,{ background: '.df_pg_image' })
    .on('progress', function(instance, image) {
        var width = image.element.offsetWidth;
        var classes = image.element.parentNode.classList;
        var check_width = options.keep_dsk_tab === 'on' ? 768 : 980

        if (window.innerWidth >= check_width) {
            if(classes.contains('df_pg_item--width-height2')) {
                image.element.parentNode.style.height = (width - parseInt(options.space_between) ) + 'px';
            } else if(classes.contains('df_pg_item--width2')) {
                image.element.parentNode.style.height = width/2 - (parseInt(options.space_between)/ 2) + 'px';
            } else if(classes.contains('df_pg_item--height2')) {
                image.element.parentNode.style.height = (width*2 ) + 'px';
            } else {
                image.element.parentNode.style.height = width + 'px';
            }
        } else {
            image.element.parentNode.style.height = width + 'px';
        }
        pakry.layout()
    })
}
function df_pg_use_lightbox(selector, options) {
    if (options.pg_lightbox === 'on') {
        var settings = {
            subHtmlSelectorRelative: true,
            addClass: 'df_pg_lightbox',
            counter: false,
            download: options.download
        };
        lightGallery(selector,settings);
    }
}
function df_pg_url_open(target, ele, use_url) {
    if(use_url !== 'on') return;
    var elements = ele.querySelectorAll('.df_pg_item');
    [].forEach.call(elements, function(image, index) {
        var url = image.dataset.customurl;
        if(url !== '') {
            image.addEventListener('click', function(event) {
                if (target === 'same_window') {
                    window.location = url;
                } else {
                    window.open(url)
                }
            })
        }
    })
}
