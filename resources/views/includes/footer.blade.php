<div class="footerWrap"> 
    <div class="container">
        <div class="row"> 

            <!--Quick Links-->
            <div class="col-md-3 col-sm-6">
                <h5>{{__('Quick Links')}}</h5>
                <!--Quick Links menu Start-->
                <ul class="quicklinks">
                    <li><a href="{{ route('index') }}">{{__('Home')}}</a></li>
                    <li><a href="https://carejobber.com/faqs/" target="_blank" rel="noopener noreferrer">
                    {{ __('Help Desk') }}</a></li>
                    <li><a href="{{ route('contact.us') }}">{{__('Contact Us')}}</a></li>
                    <li class="postad"><a href="{{ route('post.job') }}">{{__('Post a Job')}}</a></li>
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
                <h5>{{__('For Jobseekers')}}</h5>
                <!--Industry menu Start-->
                <ul class="quicklinks">
                    <li><a href="https://carejobber.com/my-profile">My Profile</a></li>
                    <li><a href="https://carejobber.com/build-resume">Build Resume</a></li>
                    <li><a href="https://carejobber.com/my-job-applications">My Job Applications</a></li>
                    <li><a href="https://carejobber.com/my-favourite-jobs">My Favourite Jobs</a></li>
                    <li><a href="https://carejobber.com/my-alerts">My Job Alerts</a></li>
                    <li><a href="https://carejobber.com/my-messages">My Messages</a></li>
                    <li><a href="https://carejobber.com/my-followings">My Followings</a></li>
                    <li><a href="https://carejobber.com/user-referral-program">Referral Program</a></li>
                </ul>
                <!--Industry menu End-->
                <div class="clear"></div>
            </div>

            <!--About Us-->
            <div class="col-md-3 col-sm-6">
                <h5>{{__('For Employers')}}</h5>
                <!--Industry menu Start-->
                <ul class="quicklinks">
                    <li><a href="https://carejobber.com/company-home">Dashboard</a></li>
                    <li><a href="https://carejobber.com/post-job">Post a Job</a></li>
                    <li><a href="https://carejobber.com/posted-jobs">Manage Jobs</a></li>
                    <li><a href="https://carejobber.com/unloced-seekers">Unlocked Users</a></li>
                    <li><a href="https://carejobber.com/company-messages">Company Messages</a></li>
                    <li><a href="https://carejobber.com/company-followers">Company Followers</a></li>
                    <li><a href="https://carejobber.com/company-profile">Company Public Profile</a></li>
                    <li><a href="https://carejobber.com/referral-program">Employers Referral Program</a></li>
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
