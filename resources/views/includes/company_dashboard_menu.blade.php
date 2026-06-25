<style>
    .custom-dashboard-sidebar {
        flex: 0 0 300px !important;
        max-width: 300px !important;
        padding-right: 30px !important;
    }
    @media (min-width: 992px) {
        .custom-dashboard-sidebar + div {
            flex: 1 1 0% !important;
            max-width: 100% !important;
            width: auto !important;
            min-width: 0 !important;
        }
    }
    @media (max-width: 991px) {
        .custom-dashboard-sidebar {
            flex: 0 0 100% !important;
            max-width: 100% !important;
            padding-right: 15px !important;
            margin-bottom: 20px;
        }
    }
    .custom-dashboard-sidebar .usernavwrap {
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
        border-radius: 16px;
        background: #ffffff;
        padding-bottom: 0;
        border: 1px solid #e8edf2 !important;
    }
    .custom-dashboard-sidebar .usernavwrap ul.usernavdash {
        padding: 15px 0;
        margin: 0;
    }
    .custom-dashboard-sidebar .usernavwrap ul.usernavdash li {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    .custom-dashboard-sidebar .usernavwrap ul.usernavdash li:hover {
        background: #f8fafc;
    }
    .custom-dashboard-sidebar .usernavwrap ul.usernavdash li.active {
        background: #eff6ff;
        border-left: 3px solid #3b82f6;
    }
    .custom-dashboard-sidebar .usernavwrap ul.usernavdash li a {
        padding: 14px 20px;
        display: flex;
        align-items: center;
        color: #475569;
        font-weight: 500;
        font-size: 14px;
    }
    .custom-dashboard-sidebar .usernavwrap ul.usernavdash li.active a {
        color: #3b82f6;
    }
    .custom-dashboard-sidebar .usernavwrap ul.usernavdash li a i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
        font-size: 16px;
    }
    /* Scrollbar styling for sticky sidebar */
    .custom-dashboard-sidebar .usernavwrap::-webkit-scrollbar {
        width: 5px;
    }
    .custom-dashboard-sidebar .usernavwrap::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-dashboard-sidebar .usernavwrap::-webkit-scrollbar-thumb {
        background: #e2e8f0; 
        border-radius: 10px;
    }
    .custom-dashboard-sidebar .usernavwrap::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1; 
    }
    
    .custom-dashboard-sidebar .dashbarad {
        margin-top: 20px;
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e8edf2;
    }
</style>
<div class="custom-dashboard-sidebar">
	<div class="usernavwrap">
    @php $companyMenu = Auth::guard('company')->user(); @endphp
    <div style="padding: 15px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 13px; color: #64748b; font-weight: 500;">{{ __('Company Status') }}:</span>
        @if($companyMenu->isEmployerVerified())
            <span style="color: #16a34a; font-weight: 600; font-size: 13px;">{{ __('Verified') }}</span>
        @elseif($companyMenu->isEmployerReviewed())
            <span style="color: #ca8a04; font-weight: 600; font-size: 13px;">{{ __('Reviewed') }}</span>
        @else
            <span style="color: #dc2626; font-weight: 600; font-size: 13px;">{{ __('Unverified') }}</span>
        @endif
    </div>
    <ul class="usernavdash">
        <li class="{{ Request::url() == route('company.home') ? 'active' : '' }}"><a href="{{route('company.home')}}"><i class="fas fa-tachometer" aria-hidden="true"></i> {{__('Dashboard')}}</a></li>
        <li class="{{ Request::url() == route('company.profile') ? 'active' : '' }}"><a href="{{ route('company.profile') }}"><i class="fas fa-pencil" aria-hidden="true"></i> {{__('Edit Account Details')}}</a></li>
        <li><a href="{{ route('company.detail', Auth::guard('company')->user()->slug) }}"><i class="fas fa-user-alt" aria-hidden="true"></i> {{__('Company Public Profile')}}</a></li>
        <li class="{{ Request::url() == route('post.job') ? 'active' : '' }}"><a href="{{ route('post.job') }}"><i class="fas fa-desktop" aria-hidden="true"></i> {{__('Post a Job')}}</a></li>
        <li class="{{ Request::url() == route('posted.jobs') ? 'active' : '' }}"><a href="{{ route('posted.jobs') }}"><i class="fab fa-black-tie"></i> {{__('Manage Jobs & Applications')}}</a></li>

        <li class="{{ Request::routeIs('recruiter.posting.*') ? 'active' : '' }}"><a href="{{ route('recruiter.posting.packages') }}"><i class="fas fa-credit-card" aria-hidden="true"></i> {{__('Packages & Subscriptions')}}</a></li>
        <li class="{{ Request::url() == route('company.packages') ? 'active' : '' }}"><a href="{{ route('company.packages') }}"><i class="fas fa-search" aria-hidden="true"></i> {{__('CV Search Packages')}}</a></li>
        <li class="{{ Request::url() == route('company.verification.upload') ? 'active' : '' }}">
            <a href="{{ route('company.verification.upload') }}">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
                @if(Auth::guard('company')->user()->isVerified())
                    {{ __('Company Verification') }} <span class="text-success">({{ __('Verified') }})</span>
                @elseif(Auth::guard('company')->user()->isVerificationRejected())
                    {{ __('Company Verification') }} <span class="text-danger">({{ __('Rejected') }})</span>
                @elseif(Auth::guard('company')->user()->hasBusinessRegistration())
                    {{ __('Company Verification') }} <span class="text-warning">({{ __('Under Review') }})</span>
                @else
                    {{ __('Company Verification') }}
                @endif
            </a>
        </li>

        <li class="{{ Request::url() == url('/list-payment-history') ? 'active' : '' }}"><a href="{{ url('/list-payment-history') }}"><i class="fas fa-file-invoice"></i> {{__('Payment History')}}</a></li>
        
        <li class="{{ Request::url() == route('company.unloced-users') ? 'active' : '' }}"><a href="{{ route('company.unloced-users') }}"><i class="fas fa-user" aria-hidden="true"></i> {{__('Unlocked Users')}}</a></li>

        <li class="{{ Request::url() == route('company.followers') ? 'active' : '' }}"><a href="{{route('company.followers')}}"><i class="fas fa-users" aria-hidden="true"></i> {{__('Company Followers')}}</a></li>
        <li class="{{ Request::url() == route('company.referral.program') ? 'active' : '' }}"><a href="{{ route('company.referral.program') }}"><i class="fas fa-gift" aria-hidden="true"></i> {{__('Referral Program')}}</a></li>
        <li><a href="{{ route('company.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out" aria-hidden="true"></i> {{__('Logout')}}</a>
            <form id="logout-form" action="{{ route('company.logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
        </li>
    </ul>
	</div>

    <div class="dashbarad">
        {!! $siteSetting->dashboard_page_ad !!}
    </div>
</div>
