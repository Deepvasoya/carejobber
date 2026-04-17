@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->

<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title' => __('My Package')])
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
        <div class="row">
            @include('includes.user_dashboard_menu')
            <div class="col-md-9 col-sm-8">

                {{-- Always show current package if user has one (already purchased) --}}
                @if(null !== $package)
                @include('includes.user_package_msg')
                @endif

                {{-- Only show purchase/upgrade options if packages are active --}}
                @if((bool)config('jobseeker.is_jobseeker_package_active'))
                @if((bool)($siteSetting->is_stripe_active ?? false))
                @include('includes.package_coupon_seeker')
                @endif
                @if(null !== $package)
                {{-- Show Upgrade Packages if available --}}
                @if($packages->count() > 0)
                @include('includes.user_packages_upgrade')
                @endif
                @else
                {{-- Show Available Packages for first-time purchase --}}
                @if($packages->count() > 0)
                @include('includes.user_packages_new')
                @endif
                @endif
                @else
                {{-- Package system is disabled - only show message if user doesn't have package --}}
                @if(null === $package)
                <div class="alert alert-warning">
                    <h4><i class="fa fa-info-circle"></i> {{__('Packages Not Available')}}</h4>
                    <p>{{__('The package system is currently disabled. Please contact the administrator for more information.')}}</p>
                </div>
                @endif
                {{-- If user has package, just show the package summary above, no additional message needed --}}
                @endif

            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection