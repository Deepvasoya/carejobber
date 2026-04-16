<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light" data-sidenav-size="default" data-skin="default">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', $siteSetting->site_name ?? 'Admin') | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ asset('/') }}favicon.ico" />
    <script src="{{ asset('theme/js/config.js') }}"></script>
    <link href="{{ asset('theme/css/vendors.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/css/admin-compat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    @stack('css')
    <script>var APP_URL = "{{ url('/') }}"; var base_url = "{{ url('/') }}";</script>
    <style>
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
        body.loading .wrapper {
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
            <div class="loader-text">Loading Admin Panel...</div>
        </div>
    </div>

    <div class="wrapper">
        <header class="app-topbar">
            <div class="container-fluid topbar-menu">
                <div class="d-flex align-items-center gap-2">
                    <div class="logo-topbar">
                        <a href="{{ route('admin.home') }}" class="logo-light">
                            <span class="logo-lg"><img src="{{ asset('sitesetting_images/thumb/' . ($siteSetting->site_logo ?? 'logo.png')) }}" alt="logo" /></span>
                           
                        </a>
                       
                    </div>
                    <button class="sidenav-toggle-button btn btn-primary btn-icon" type="button" aria-label="Toggle sidebar">
                        <i class="ri ri-menu-line"></i>
                    </button>
                    <button class="topnav-toggle-button px-2 d-lg-none" data-bs-toggle="collapse" data-bs-target="#topnav-menu" type="button">
                        <i class="ri ri-menu-line"></i>
                    </button>
                   
                </div>
                <div class="d-flex align-items-center gap-2">
                        

                       
                        @if(APAuthHelp::can('site_settings.manage'))
                        <div class="topbar-item d-none d-sm-flex">
                            <a href="{{ route('edit.site.setting') }}" class="topbar-link">
                            <i class="ri ri-settings-2-line topbar-link-icon"></i>
                            </a>
                        </div>
                        @endif


                        <div id="fullscreen-toggler" class="topbar-item d-none d-sm-flex">
                            <button class="topbar-link" type="button" data-toggle="fullscreen">
                                <i class="ri ri-fullscreen-line topbar-link-icon"></i>
                                <i class="ri ri-fullscreen-exit-line topbar-link-icon d-none"></i>
                            </button>
                        </div>


                    <div id="simple-user-dropdown" class="topbar-item nav-user">
                        <div class="dropdown">
                            <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown" href="#!" aria-haspopup="false" aria-expanded="false">
                                <span class="avatar rounded-circle bg-primary bg-opacity-25 d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                                    <i class="ri ri-user-line text-primary"></i>
                                </span>
                                <div class="d-none d-lg-flex align-items-center gap-1">
                                    <span class="fw-medium">{{ Auth::check() && Auth::user()->name ? Auth::user()->name : 'Admin' }}</span>
                                    <i class="ri ri-arrow-down-s-line align-middle"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Welcome!</h6>
                                </div>
                                <a href="{{ route('admin.home') }}" class="dropdown-item">
                                    <i class="ri ri-dashboard-2-line me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Dashboard</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.logout') }}" class="dropdown-item text-danger fw-semibold">
                                    <i class="ri ri-logout-box-line me-1 fs-lg align-middle"></i>
                                    <span class="align-middle">Log Out</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="sidenav-menu">
            <a href="{{ route('admin.home') }}" class="logo">
                <span class="logo logo-light">
                    <span class="logo-lg"><img src="{{ asset('sitesetting_images/thumb/' . ($siteSetting->site_logo ?? 'logo.png')) }}" alt="logo"  /></span>                    
                </span>
                
            </a>
            <button class="button-on-hover" type="button" aria-label="Toggle hover"><i class="ri ri-circle-line align-middle"></i></button>
            <button class="button-close-offcanvas d-lg-none" type="button" aria-label="Close"><i class="ri ri-menu-line align-middle"></i></button>
            <div class="scrollbar" data-simplebar="">
                <div id="user-profile-condensed" class="sidenav-user">
                    <div class="dropdown">
                        <a class="dropdown-toggle drop-arrow-none link-reset sidenav-user-set-icon" data-bs-toggle="dropdown" href="#!" aria-haspopup="false" aria-expanded="false">
                            <span class="d-flex align-items-center gap-2">
                                <span class="avatar-md rounded-circle bg-primary bg-opacity-25 d-flex align-items-center justify-content-center"><i class="ri ri-user-line text-primary"></i></span>
                                <span>
                                    <span class="sidenav-user-name fw-bold">{{ Auth::check() && Auth::user()->name ? Auth::user()->name : 'Admin' }}</span>
                                    <span class="fs-12 fw-semibold d-block">Admin</span>
                                </span>
                                <i class="ri ri-arrow-down-s-line align-middle ms-auto"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <a href="{{ route('admin.home') }}" class="dropdown-item"><i class="ri ri-dashboard-2-line me-1"></i> Dashboard</a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('admin.logout') }}" class="dropdown-item text-danger fw-semibold"><i class="ri ri-logout-box-line me-1"></i> Log Out</a>
                        </div>
                    </div>
                </div>
                <div id="sidenav-menu">
                    @include('admin.shared.sidebar')
                </div>
            </div>
        </div>

        <div class="content-page">
            <div class="container-fluid">
                @yield('content')
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center">
                            &copy; {{ date('Y') }} {{ $siteSetting->site_name ?? 'Admin' }}. All rights reserved.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('theme/js/vendors.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/select2/select2.min.js') }}"></script>
    <script>
    window.initCustomFieldMultiselect = function ($root) {
        if (typeof jQuery === 'undefined' || !jQuery.fn.select2) return;
        var $ = jQuery;
        var $scope = ($root && $root.length) ? $root : $(document);
        $scope.find('select.custom-field-select2').each(function () {
            var $s = $(this);
            if ($s.hasClass('select2-hidden-accessible')) {
                $s.select2('destroy');
            }
            var ph = $s.data('cf-placeholder') || @json(__('Select one or more…'));
            $s.select2({ width: '100%', placeholder: ph, allowClear: true, closeOnSelect: false });
        });
    };
    jQuery(function () { window.initCustomFieldMultiselect(jQuery(document)); });
    </script>
    <script src="{{ asset('theme/plugins/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('theme/js/app.js') }}"></script>
    @stack('scripts')

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
