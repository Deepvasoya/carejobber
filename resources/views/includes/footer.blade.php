<section class="footer-app-cta">
    <div class="container">
        <div class="footer-app-cta__inner">
            <div class="footer-app-cta__copy">
                <span class="footer-app-cta__eyebrow">{{ __('Mobile App') }}</span>
                <h2>{{ __('Take Jobs in Your Pocket!') }}</h2>
                <p>{{ __('Apply anytime, anywhere with Medojob free apps') }}</p>
                <a href="#" class="footer-app-cta__note">{{ __('Download Free App') }}</a>
            </div>
            <div class="footer-app-cta__qr">
                <span class="footer-app-cta__label">{{ __('Scan QR Code') }}</span>
                <div class="footer-app-cta__qr-card">
                    <img src="{{ asset('theme/images/qr.png') }}" style="width:80px;height:80px" alt="{{ __('Scan QR Code') }}">
                </div>
            </div>
            <div class="footer-app-cta__stores">
                <span class="footer-app-cta__label">{{ __('Download App') }}</span>
                <div class="footer-app-cta__store-list">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="#" style="display: inline-block; background: #000; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                        <i class="fab fa-apple"></i> {{__('App Store')}}
                    </a>
                    <a href="#" style="display: inline-block; background: #000; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">
                        <i class="fab fa-google-play"></i> {{__('Google Play')}}
                    </a>
                </div>
                </div>
            </div>
            <div class="footer-app-cta__phone" aria-hidden="true">
                <div class="footer-app-cta__device">
                    <div class="footer-app-cta__device-notch"></div>
                    <div class="footer-app-cta__device-screen">
                        <div class="footer-app-cta__screen-card footer-app-cta__screen-card--top"></div>
                        <div class="footer-app-cta__screen-brand">medojob</div>
                        <div class="footer-app-cta__screen-card footer-app-cta__screen-card--bottom"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="footerWrap"> 
    <div class="container">
        <div class="row"> 

            <!--Quick Links-->
            <div class="col-md-3 col-sm-6">
                <h5>{{__('Quick Links')}}</h5>
                <!--Quick Links menu Start-->
                <ul class="quicklinks">
                    <li><a href="{{ route('index') }}">{{__('Home')}}</a></li>
                    <li class="postad"><a href="https://medojob.com/blog">{{__('Blog')}}</a></li>
                    <li><a href="{{ route('help.centre') }}" target="_blank" rel="noopener noreferrer">{{ __('Help Desk') }}</a></li>
                    <li><a href="{{ route('contact.us') }}">{{__('Contact Us')}}</a></li>
                    @foreach($show_in_footer_menu as $footer_menu)
                    @php
                    $cmsContent = App\CmsContent::getContentBySlug($footer_menu->page_slug);
                    @endphp

                    <li class="{{ Request::url() == route('cms', $footer_menu->page_slug) ? 'active' : '' }}"><a href="{{ route('cms', $footer_menu->page_slug) }}">{{ $cmsContent->page_title }}</a></li>
                    @endforeach
                </ul>
            </div>
            <!--Quick Links menu end-->

            <div class="col-md-3 col-sm-6">
                <h5>{{__('Jobs By Category')}}</h5>
                <!--Quick Links menu Start-->
                <ul class="quicklinks">
                    @php
                    $functionalAreas = App\FunctionalArea::getUsingFunctionalAreas(10);
                    @endphp
                    @foreach($functionalAreas as $functionalArea)
                    <li><a href="{{ route('job.list', ['functional_area_id[]'=>$functionalArea->functional_area_id]) }}">{{$functionalArea->functional_area}}</a></li>
                    @endforeach
                </ul>
            </div>

            <!--Jobs By Industry-->
            <div class="col-md-3 col-sm-6">
                <h5>{{__('Job seekers')}}</h5>
                <!--Industry menu Start-->
                <ul class="quicklinks">
                    <li><a href="{{ route('my.profile') }}">{{ __('My Profile') }}</a></li>
                    <li><a href="{{ route('build.resume') }}">{{ __('Build Resume') }}</a></li>
                    <li><a href="{{ route('my.job.applications') }}">{{ __('My Job Applications') }}</a></li>
                    <li><a href="{{ route('my.favourite.jobs') }}">{{ __('My Favourite Jobs') }}</a></li>
                    <li><a href="{{ route('my-alerts') }}">{{ __('My Job Alerts') }}</a></li>
                    <li><a href="{{ route('my.messages') }}">{{ __('My Messages') }}</a></li>
                    <li><a href="{{ route('my.followings') }}">{{ __('My Followings') }}</a></li>
                    <li><a href="{{ route('user.referral.program') }}">{{ __('Referral Program') }}</a></li>
                </ul>
                <!--Industry menu End-->
                <div class="clear"></div>
            </div>

            <!--About Us-->
            <div class="col-md-3 col-sm-6">
                <h5>{{__('Employers')}}</h5>
                <!--Industry menu Start-->
                <ul class="quicklinks">
                    <li><a href="{{ route('company.home') }}">{{ __('Dashboard') }}</a></li>
                    <li><a href="{{ route('post.job') }}">{{ __('Post a Job') }}</a></li>
                    <li><a href="{{ route('posted.jobs') }}">{{ __('Manage Jobs') }}</a></li>
                    <li><a href="{{ route('company.unloced-users') }}">{{ __('Unlocked Users') }}</a></li>
                    <li><a href="{{ route('company.messages') }}">{{ __('Company Messages') }}</a></li>
                    <li><a href="{{ route('company.followers') }}">{{ __('Company Followers') }}</a></li>
                    <li><a href="{{ route('company.profile') }}">{{ __('Company Public Profile') }}</a></li>
                    <li><a href="{{ route('company.referral.program') }}">{{ __('Employers Referral Program') }}</a></li>
                </ul>
                <!--Industry menu End-->
                <div class="clear"></div>
            </div>
                <!-- Social Icons -->
                <div class="social">@include('includes.footer_social')</div>
                <!-- Social Icons end --> 

            </div>
            <!--About us End--> 


        </div>
    </div>
</div>
<!--Footer end--> 
<!--Copyright-->
<div class="copyright">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="bttxt">{{__('Copyright')}} &copy; {{date('Y')}} {{ $siteSetting->site_name }}. {{__('All Rights Reserved')}}.</div>
            </div>
            <div class="col-md-4">
                <div class="paylogos"><img src="{{asset('/')}}images/payment-icons.png" alt="" /></div>	
            </div>
        </div>

    </div>
</div>
