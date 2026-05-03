<?php

if (!isset($seo)) {

    $seo = (object)array('seo_title' => $siteSetting->site_name, 'seo_description' => $siteSetting->site_name, 'seo_keywords' => $siteSetting->site_name, 'seo_other' => '');

}

?>

<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}" class="{{ (session('localeDir', 'ltr'))}}" dir="{{ (session('localeDir', 'ltr'))}}">



<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{__($seo->seo_title) }}</title>

    <meta name="Description" content="{!! $seo->seo_description !!}">

    <meta name="Keywords" content="{!! $seo->seo_keywords !!}">

    {!! $seo->seo_other !!}

    <!-- Fav Icon -->

    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">

    <!-- Owl carousel -->
    <link href="{{ asset('/css/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery-ui.min.css') }}" rel="stylesheet">

    <!-- Slider -->
    <link href="{{ asset('/js/revolution-slider/css/settings.css') }}" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="{{ asset('/css/all.min.css') }}" rel="stylesheet">

    <!-- Custom Style -->
    <link href="{{ asset('/css/main.css') }}" rel="stylesheet">

    <!-- Instant Chat Addon CSS -->
    <link href="{{ asset('/css/chat-widget.css') }}" rel="stylesheet">

    @if((session('localeDir', 'ltr') == 'rtl'))
    <!-- Rtl Style -->
    <link href="{{ asset('/css/rtl-style.css') }}" rel="stylesheet">
    @endif

    <link href="{{ asset('/admin_assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/admin_assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/admin_assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')

    @livewireStyles

    {!! $siteSetting->ganalytics !!}

    {!! $siteSetting->google_tag_manager_for_head !!}


    <style>
        /* Top-left corner — same pattern as .promotepof-badge (top-right), not width:0 (that broke layout/overflow) */
.promotepof-badge-left {
    position: absolute;
    left: 0;
    top: 0;
    right: auto;
    z-index: 4;
}

.promotepof-badge-left:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    border-top: 50px solid #28a745;
    border-right: 50px solid transparent;
    border-radius: 18px 0 0 0;
}

.promotepof-badge-left i {
    z-index: 5;
    position: relative;
    color: #fff;
    float: left;
    margin: 10px 0 0 10px;
    font-size: 12px;
}

    /* Job / location search autocomplete (jQuery UI) */
    .ui-autocomplete.search-ac-dropdown {
        z-index: 10050 !important;
        max-height: 280px;
        overflow-y: auto;
        overflow-x: hidden;
        background: #fff;
        border: 1px solid #ddd !important;
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        padding: 4px 0;
        margin-top: 4px;
    }
    .ui-autocomplete.search-ac-dropdown .ui-menu-item-wrapper {
        padding: 8px 14px;
        font-size: 15px;
        border: none;
    }
    .ui-autocomplete.search-ac-dropdown .ui-menu-item-wrapper.ui-state-active {
        background: #e8f4fd !important;
        color: #222 !important;
        border: none;
        margin: 0;
    }
    .search-ac-geo-row i { color: #28a745; margin-right: 8px; }
    .search-ac-job-typed { color: #888; }
    .search-ac-job-bold { font-weight: 600; color: #111; }
    .search-ac-loc-prefix { font-weight: 600; }
    .search-ac-loc-rest { font-weight: 400; color: #333; }

    /* Page Loader Styles */
    #page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    #page-loader.loaded {
        opacity: 0;
        visibility: hidden;
    }

    .loader-content {
        text-align: center;
    }

    .loader-spinner {
        width: 60px;
        height: 60px;
        margin: 0 auto 20px;
        position: relative;
    }

    .loader-spinner::before,
    .loader-spinner::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        border: 3px solid transparent;
        border-top-color: #28a745;
        animation: spin 1.2s linear infinite;
    }

    .loader-spinner::before {
        width: 60px;
        height: 60px;
        top: 0;
        left: 0;
    }

    .loader-spinner::after {
        width: 45px;
        height: 45px;
        top: 7.5px;
        left: 7.5px;
        border-top-color: #17a2b8;
        animation-duration: 0.8s;
        animation-direction: reverse;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loader-text {
        font-size: 16px;
        color: #333;
        font-weight: 500;
        margin-top: 10px;
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .loader-logo {
        max-width: 150px;
        margin-bottom: 20px;
        opacity: 0.9;
    }

    /* Blur content while loading */
    body.loading .page-content {
        filter: blur(5px);
        pointer-events: none;
    }
    </style>
</head>



<body>

    <!-- Page Loader -->
    <div id="page-loader">
        <div class="loader-content">
            @if(!empty($siteSetting->site_logo))
            <img src="{{ asset('sitesetting_images/thumb/' . $siteSetting->site_logo) }}" alt="{{ $siteSetting->site_name }}" class="loader-logo">
            @endif
            <div class="loader-spinner"></div>
            <div class="loader-text">{{ __('Loading') }}...</div>
        </div>
    </div>

    <div class="page-content">
        @yield('content')
    </div>

    {{-- jQuery must load first. Use consistent asset() paths so production (ASSET_URL/APP_URL) serves from public/ --}}
    <script src="{{ asset('/js/jquery.min.js') }}"></script>
    <script>
        // Fallback: if jQuery failed to load (e.g. 404 when document root is not public/), load from CDN
        if (typeof window.jQuery === 'undefined') {
            document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"><\/script>');
        }
    </script>
    <script src="{{ asset('/js/popper.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Owl carousel -->
    <script src="{{ asset('/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/owl.carousel.js') }}"></script>

    <script src="{{ asset('/admin_assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_assets/global/plugins/Bootstrap-3-Typeahead/bootstrap3-typeahead.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/admin_assets/global/plugins/jquery.scrollTo.min.js') }}" type="text/javascript"></script>

    <!-- TinyMCE Editor -->
    <script src="{{ asset('/admin_assets/global/plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
      (function () {
        var editorSelector = [
          'textarea.tinymce',
          'textarea#content',
          'textarea#summary',
          'textarea[name="content"]',
          'textarea[name="description"]',
          'textarea[name="benefits"]',
          'textarea[name="summary"]',
          'textarea[name="Summary"]',
          'textarea[name="body"]',
          'textarea[name="terms_content"]'
        ].join(',');
        var generatedId = 0;

        function getEditorHeight(element) {
          if (element && element.getAttribute('data-tinymce-height')) {
            return parseInt(element.getAttribute('data-tinymce-height'), 10) || 260;
          }

          if (element && (element.id === 'summary' || element.name === 'Summary' || element.name === 'summary')) {
            return 260;
          }

          return 240;
        }

        function getEditorConfig(element) {
          return {
            target: element,
            height: getEditorHeight(element),
            menubar: false,
            branding: false,
            entity_encoding: 'raw',
            forced_root_block: '',
            convert_urls: false,
            relative_urls: true,
            paste_as_text: false,
            paste_data_images: true,
            plugins: [
              'advlist autolink lists link image charmap preview anchor',
              'searchreplace visualblocks code fullscreen',
              'insertdatetime media table contextmenu paste help wordcount'
            ],
            toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | code',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
            images_upload_url: "{{ route('tinymce.image_upload.front') }}",
            images_upload_handler: function (blobInfo, success, failure) {
              var xhr = new XMLHttpRequest();
              var formData = new FormData();

              xhr.withCredentials = false;
              xhr.open('POST', "{{ route('tinymce.image_upload.front') }}");
              xhr.onload = function () {
                var json;

                if (xhr.status !== 200) {
                  failure('HTTP Error: ' + xhr.status);
                  return;
                }

                try {
                  json = JSON.parse(xhr.responseText);
                } catch (error) {
                  failure('Invalid JSON: ' + xhr.responseText);
                  return;
                }

                if (!json || typeof json.location !== 'string') {
                  failure('Invalid JSON: ' + xhr.responseText);
                  return;
                }

                success(json.location);
              };
              formData.append('image', blobInfo.blob(), blobInfo.filename());
              xhr.send(formData);
            }
          };
        }

        function collectTextareas(context, selector) {
          var root = context || document;
          var matches = [];
          var activeSelector = selector || editorSelector;

          if (root.nodeType === 1 && root.matches && root.matches(activeSelector)) {
            matches.push(root);
          }

          if (root.querySelectorAll) {
            matches = matches.concat(Array.prototype.slice.call(root.querySelectorAll(activeSelector)));
          }

          return matches;
        }

        function initTinyMCE(context, selector) {
          if (typeof tinymce === 'undefined') {
            return;
          }

          collectTextareas(context, selector).forEach(function (element) {
            var existingEditor;

            if (!element.id) {
              generatedId += 1;
              element.id = 'tinymce_front_' + generatedId;
            }

            existingEditor = tinymce.get(element.id);
            if (existingEditor) {
              if (existingEditor.getElement() === element) {
                return;
              }

              existingEditor.remove();
            }

            tinymce.init(getEditorConfig(element));
          });
        }

        function removeDetachedEditors() {
          if (typeof tinymce === 'undefined' || !tinymce.get) {
            return;
          }

          tinymce.get().slice().forEach(function (editor) {
            var element = editor.getElement();

            if (!element || !document.documentElement.contains(element)) {
              editor.remove();
            }
          });
        }

        function saveTinyMCE() {
          if (typeof tinymce !== 'undefined') {
            removeDetachedEditors();
            tinymce.triggerSave();
          }
        }

        function observeDynamicForms() {
          if (!window.MutationObserver || !document.body) {
            return;
          }

          var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
              Array.prototype.forEach.call(mutation.addedNodes, function (node) {
                if (node.nodeType === 1) {
                  initTinyMCE(node);
                }
              });
            });
          });

          observer.observe(document.body, { childList: true, subtree: true });
        }

        function patchJquerySerialize() {
          if (!window.jQuery || window.jQuery.fn.carejobberTinyMceSerializePatched) {
            return;
          }

          var originalSerialize = window.jQuery.fn.serialize;
          var originalSerializeArray = window.jQuery.fn.serializeArray;

          window.jQuery.fn.serialize = function () {
            saveTinyMCE();
            return originalSerialize.apply(this, arguments);
          };

          window.jQuery.fn.serializeArray = function () {
            saveTinyMCE();
            return originalSerializeArray.apply(this, arguments);
          };

          window.jQuery.fn.carejobberTinyMceSerializePatched = true;
        }

        window.CarejobberTinyMCE = {
          init: initTinyMCE,
          save: saveTinyMCE,
          selector: editorSelector
        };

        document.addEventListener('submit', saveTinyMCE, true);
        document.addEventListener('DOMContentLoaded', function () {
          initTinyMCE(document);
          observeDynamicForms();
          patchJquerySerialize();
        });
      })();
    </script>

    <!-- Revolution Slider -->
    <script type="text/javascript" src="{{ asset('/js/revolution-slider/js/jquery.themepunch.tools.min.js') }}"></script>
    <!-- Revolution Slider -->
    <script type="text/javascript" src="{{ asset('/js/revolution-slider/js/jquery.themepunch.tools.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/revolution-slider/js/jquery.themepunch.revolution.min.js') }}"></script>

    <script src="{{ asset('/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('/js/jquery.validate.additional-methods.min.js') }}"></script>
    <script src="{{ asset('/js/dragula.min.js') }}"></script>

    {!! NoCaptcha::renderJs() !!}

    @stack('scripts')

    @livewireScripts

    <!-- Custom js -->
    <script src="{{ asset('/js/script.js') }}"></script>

    <!-- Instant Chat Addon JS (Self-Hosted - No WebSockets) -->
    <script>
        window.CHAT_BASE_URL = '{{ url("/") }}';
        window.CHAT_TRANSLATIONS = {
            loadingMessages: '{{ __("Loading messages...") }}',
            isTyping: '{{ __("is typing...") }}',
            typeMessage: '{{ __("Type a message...") }}',
            attachFile: '{{ __("Attach File") }}',
            send: '{{ __("Send") }}',
            close: '{{ __("Close") }}',
            minimize: '{{ __("Minimize") }}',
            loadingConversations: '{{ __("Loading conversations...") }}',
            justNow: '{{ __("Just now") }}'
        };
    </script>
    <script src="{{ asset('/js/chat-widget-polling.js') }}"></script>

    <script type="text/javascript">
    if (typeof window.jQuery !== 'undefined') {
    (function($) {
        "use strict";

        $(document).ready(function(){
            if ($.fn.scrollTo) $(document).scrollTo('.has-error', 2000);
        });

        function showProcessingForm(btn_id) {
            $("#" + btn_id).val('{{ __("Processing...") }}');
            $("#" + btn_id).attr('disabled', 'disabled');
        }
        window.showProcessingForm = showProcessingForm;

        function hide_savedAlert() {
            $(document).find('.svjobalert').hide();
        }
        setInterval(hide_savedAlert, 7000);

        $(document).ready(function(){
            $.ajax({
                type: 'get',
                url: "{{ route('check-time') }}",
                success: function(res) {
                    $('.notification').html(res);
                },
                error: function() {}
            });
        });

        $(document).ready(function() {
            if (!$.fn.autocomplete) {
                return;
            }

            window.SEARCH_AC_I18N = window.SEARCH_AC_I18N || {
                useMyLocation: @json(__('Use my current location')),
                geoNotSupported: @json(__('Geolocation is not supported by your browser.')),
                geoDenied: @json(__('Unable to retrieve your location. Please allow location access or type a city.')),
                geoFailed: @json(__('Could not resolve your location. Try typing a city name.'))
            };

            function escapeHtml(s) {
                return String(s).replace(/[&<>"']/g, function(c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }

            function prefixLen(label, term) {
                if (!term || !label) {
                    return 0;
                }
                var l = label.toLowerCase();
                var t = term.toLowerCase();
                if (l.indexOf(t) !== 0) {
                    return 0;
                }
                return t.length;
            }

            function renderJobSuggestion(label, term) {
                var n = prefixLen(label, term);
                if (n <= 0) {
                    return '<span class="search-ac-job-rest">' + escapeHtml(label) + '</span>';
                }
                var matched = label.substring(0, n);
                var rest = label.substring(n);
                return '<span class="search-ac-job-typed">' + escapeHtml(matched) + '</span>'
                    + '<strong class="search-ac-job-bold">' + escapeHtml(rest) + '</strong>';
            }

            function renderLocationSuggestion(label, term) {
                var n = prefixLen(label, term);
                if (n <= 0) {
                    return '<span>' + escapeHtml(label) + '</span>';
                }
                var matched = label.substring(0, n);
                var rest = label.substring(n);
                return '<strong class="search-ac-loc-prefix">' + escapeHtml(matched) + '</strong>'
                    + '<span class="search-ac-loc-rest">' + escapeHtml(rest) + '</span>';
            }

            function bindAcMenuClasses($input) {
                $input.on('autocompleteopen', function() {
                    $(this).autocomplete('widget').addClass('search-ac-dropdown');
                });
            }

            function fillLocationFromGeocode($input) {
                var i18n = window.SEARCH_AC_I18N;
                if (!navigator.geolocation) {
                    window.alert(i18n.geoNotSupported);
                    return;
                }
                $input.prop('disabled', true);
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        $.getJSON("{{ route('geocode.city') }}", {
                            lat: pos.coords.latitude,
                            lon: pos.coords.longitude
                        })
                            .done(function(res) {
                                if (res && res.label) {
                                    $input.val(res.label);
                                } else {
                                    window.alert(i18n.geoFailed);
                                }
                            })
                            .fail(function() {
                                window.alert(i18n.geoFailed);
                            })
                            .always(function() {
                                $input.prop('disabled', false);
                            });
                    },
                    function() {
                        $input.prop('disabled', false);
                        window.alert(i18n.geoDenied);
                    },
                    { enableHighAccuracy: false, timeout: 12000, maximumAge: 600000 }
                );
            }

            var $jbsearch = $("#jbsearch");
            if ($jbsearch.length) {
                $jbsearch.autocomplete({
                    minLength: 2,
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('jobs.autocomplete') }}",
                            dataType: "json",
                            data: { term: request.term },
                            success: function(data) { response(data); },
                            error: function() { response([]); }
                        });
                    }
                });
                bindAcMenuClasses($jbsearch);
                var jbInst = $jbsearch.data('ui-autocomplete');
                if (jbInst) {
                    jbInst._renderItem = function(ul, item) {
                        var label = item.label != null ? item.label : item.value;
                        var term = this.term || '';
                        var html = renderJobSuggestion(String(label), term);
                        return $('<li class="ui-menu-item">').append('<div class="ui-menu-item-wrapper search-ac-item">' + html + '</div>').appendTo(ul);
                    };
                }
            }

            var $locInputs = $('#location_search, #location_search_inner, #location_search_sidebar');
            if ($locInputs.length) {
                $locInputs.each(function() {
                    var $loc = $(this);
                    $loc.autocomplete({
                        minLength: 2,
                        source: function(request, response) {
                            var term = request.term;
                            $.ajax({
                                url: "{{ route('locations.autocomplete') }}",
                                dataType: "json",
                                data: { term: term },
                                success: function(data) {
                                    var geo = {
                                        label: window.SEARCH_AC_I18N.useMyLocation,
                                        value: '',
                                        geo: true
                                    };
                                    response([geo].concat(data || []));
                                },
                                error: function() { response([]); }
                            });
                        },
                        select: function(event, ui) {
                            if (ui.item && ui.item.geo) {
                                event.preventDefault();
                                $loc.autocomplete('close');
                                fillLocationFromGeocode($loc);
                                return false;
                            }
                        }
                    });
                    bindAcMenuClasses($loc);
                    var locInst = $loc.data('ui-autocomplete');
                    if (locInst) {
                        locInst._renderItem = function(ul, item) {
                            if (item.geo) {
                                return $('<li class="ui-menu-item">')
                                    .append(
                                        '<div class="ui-menu-item-wrapper search-ac-item search-ac-geo-row">'
                                        + '<i class="fas fa-crosshairs" aria-hidden="true"></i>'
                                        + escapeHtml(item.label)
                                        + '</div>'
                                    )
                                    .appendTo(ul);
                            }
                            var label = item.label != null ? item.label : item.value;
                            var term = this.term || '';
                            var html = renderLocationSuggestion(String(label), term);
                            return $('<li class="ui-menu-item">').append('<div class="ui-menu-item-wrapper search-ac-item">' + html + '</div>').appendTo(ul);
                        };
                    }
                });
            }
        });

        $(document).ready(function() {
            if ($.fn.select2) {
                $('.select2-multiple-jobtype').select2({
                    placeholder: "{{ __('Select Multiple Jobtypes') }}",
                    allowClear: true
                });
            }
        });

        window.initCustomFieldMultiselect = function ($root) {
            if (!$.fn.select2) {
                return;
            }
            var $scope = ($root && $root.length) ? $root : $(document);
            $scope.find('select.custom-field-select2').each(function () {
                var $s = $(this);
                if ($s.hasClass('select2-hidden-accessible')) {
                    $s.select2('destroy');
                }
                var ph = $s.data('cf-placeholder') || @json(__('Select one or more…'));
                $s.select2({
                    width: '100%',
                    placeholder: ph,
                    allowClear: true,
                    closeOnSelect: false
                });
            });
        };
        $(document).ready(function () {
            window.initCustomFieldMultiselect($(document));
        });

        function showAdvanceSearch() {
            $("#showAdvanceSearchRow").show();
            if ($.fn.select2) {
                $('.select2-multiple-jobtype').select2({
                    placeholder: "{{ __('Select Multiple Jobtypes') }}",
                    allowClear: true
                });
            }
            $("#advSearch").hide();
        }
        window.showAdvanceSearch = showAdvanceSearch;

    })(jQuery);
    }
    </script>

    <!-- Page Loader Script -->
    <script>
        // Show loader immediately
        document.body.classList.add('loading');

        // Hide loader when page is fully loaded
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            document.body.classList.remove('loading');
            
            // Add a small delay for smooth transition
            setTimeout(function() {
                loader.classList.add('loaded');
            }, 300);
        });

        // Fallback: Hide loader after 5 seconds if something goes wrong
        setTimeout(function() {
            const loader = document.getElementById('page-loader');
            if (loader && !loader.classList.contains('loaded')) {
                document.body.classList.remove('loading');
                loader.classList.add('loaded');
            }
        }, 5000);
    </script>

</body>



</html>
