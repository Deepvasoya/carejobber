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

    <link rel="shortcut icon" href="{{asset('/')}}favicon.ico">

       <!-- Owl carousel -->

    <link href="{{asset('/')}}css/owl.carousel.css" rel="stylesheet">
    <link href="{{asset('/')}}css/jquery-ui.min.css" rel="stylesheet">

    <!-- Slider -->
    <link href="{{asset('/')}}js/revolution-slider/css/settings.css" rel="stylesheet">

    <!-- Bootstrap -->

    <link href="{{asset('/')}}css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->

    <link href="{{asset('/')}}css/all.min.css" rel="stylesheet">

    <!-- Custom Style -->

    <link href="{{asset('/')}}css/main.css" rel="stylesheet">
    
    <!-- Instant Chat Addon CSS -->
    <link href="{{asset('/')}}css/chat-widget.css" rel="stylesheet">

    @if((session('localeDir', 'ltr') == 'rtl'))

    <!-- Rtl Style -->

    <link href="{{asset('/')}}css/rtl-style.css" rel="stylesheet">

    @endif

    <link href="{{ asset('/') }}admin_assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />

    <link href="{{ asset('/') }}admin_assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <link href="{{ asset('/') }}admin_assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

    @stack('styles')

    @livewireStyles

    {!! $siteSetting->ganalytics !!}

    {!! $siteSetting->google_tag_manager_for_head !!}



</head>



<body>

    @yield('content')

    <script src="{{asset('/')}}js/jquery.min.js"></script>
    <script src="{{asset('/')}}js/popper.js"></script>
    <script src="{{asset('/')}}js/bootstrap.bundle.min.js"></script>



    <!-- Owl carousel -->

    <script src="{{asset('/')}}js/jquery-ui.min.js"></script>

    <script src="{{asset('/')}}js/owl.carousel.js"></script>

    <script src="{{ asset('/') }}admin_assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

    <script src="{{ asset('/') }}admin_assets/global/plugins/Bootstrap-3-Typeahead/bootstrap3-typeahead.min.js" type="text/javascript"></script>

    <!-- END PAGE LEVEL PLUGINS -->

    <script src="{{ asset('/') }}admin_assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>

    <script src="{{ asset('/') }}admin_assets/global/plugins/jquery.scrollTo.min.js" type="text/javascript"></script>

   
<!-- Revolution Slider -->

<script type="text/javascript" src="{{ asset('/') }}js/revolution-slider/js/jquery.themepunch.tools.min.js"></script>

<script type="text/javascript" src="{{ asset('/') }}js/revolution-slider/js/jquery.themepunch.revolution.min.js"></script>
    

    <script src="{{ asset('js/sweetalert.min.js') }}"></script>

    <script src="{{ asset('js/jquery.validate.js') }}"></script>

    <script src="{{ asset('js/jquery.validate.additional-methods.min.js') }}"></script>
    <script src="{{ asset('js/dragula.min.js') }}"></script>

   
    {!! NoCaptcha::renderJs() !!}

    @stack('scripts')

    @livewireScripts

    <!-- Custom js -->

    <script src="{{asset('/')}}js/script.js"></script>
    
    <!-- Instant Chat Addon JS (Self-Hosted - No WebSockets) -->
    <script>
        // Set base URL for chat API calls
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
    <script src="{{asset('/')}}js/chat-widget-polling.js"></script>

    <script type="text/javascript">
    (function($) {
        "use strict";
        
        // Scroll to error on page load
        $(document).ready(function(){
            $(document).scrollTo('.has-error', 2000);
        });

        // Show processing form state
        function showProcessingForm(btn_id) {
            $("#" + btn_id).val('{{ __("Processing...") }}');
            $("#" + btn_id).attr('disabled', 'disabled');
        }
        
        // Make function globally available
        window.showProcessingForm = showProcessingForm;

        // Hide saved alert function
        function hide_savedAlert() {
            $(document).find('.svjobalert').hide();
        }
        
        // Use function reference instead of string for setInterval
        setInterval(hide_savedAlert, 7000);

        // Load notifications
        $(document).ready(function(){
            $.ajax({
                type: 'get',
                url: "{{ route('check-time') }}",
                success: function(res) {
                    $('.notification').html(res);
                },
                error: function() {
                    // Silently fail if route doesn't exist
                }
            });
        });

        // Job search autocomplete
        $(document).ready(function() {
            var $jbsearch = $("#jbsearch");
            if ($jbsearch.length) {
                $jbsearch.autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "{{ route('jobs.autocomplete') }}",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                            },
                            error: function() {
                                response([]);
                            }
                        });
                    },
                    minLength: 2,
                    select: function(event, ui) {
                        // Action after selecting a suggestion
                    }
                });
            }
        });

        // Initialize select2 for job types
        $(document).ready(function() {
            $('.select2-multiple-jobtype').select2({
                placeholder: "{{ __('Select Multiple Jobtypes') }}",
                allowClear: true
            });
        });

        // Show advance search
        function showAdvanceSearch() {
            $("#showAdvanceSearchRow").show();
            $('.select2-multiple-jobtype').select2({
                placeholder: "{{ __('Select Multiple Jobtypes') }}",
                allowClear: true
            });
            $("#advSearch").hide();
        }
        
        // Make function globally available
        window.showAdvanceSearch = showAdvanceSearch;
        
    })(jQuery);
    </script>

</body>



</html>