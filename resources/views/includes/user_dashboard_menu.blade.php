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
    .custom-dashboard-sidebar .sidebar-inner-scroll {
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #e8edf2;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .featuredprofile {
        box-shadow: none;
        border-bottom: 1px solid #e8edf2;
        margin-bottom: 0;
        border-radius: 16px 16px 0 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 25px 20px;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .featuredprofile h5 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .featuredprofile p {
        color: rgba(255,255,255,0.95);
        font-size: 14px;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .featuredprofile .pckfeatlist li {
        color: rgba(255,255,255,0.95);
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .featuredprofile .order a,
    .custom-dashboard-sidebar .sidebar-inner-scroll .featuredprofile .order strong {
        color: #fff;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap {
        box-shadow: none;
        margin-bottom: 0;
        padding: 15px 0 20px 0;
        border: none !important;
        background: #ffffff;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap .switchbox {
        background: #f8fafc;
        padding: 15px 20px;
        margin: 0 0 10px 0;
        border-radius: 12px;
        border: 1px solid #e8edf2;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap ul.usernavdash {
        padding: 0;
        margin: 0;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap ul.usernavdash li {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap ul.usernavdash li:hover {
        background: #f8fafc;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap ul.usernavdash li.active {
        background: #eff6ff;
        border-left: 3px solid #3b82f6;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap ul.usernavdash li a {
        padding: 14px 20px;
        display: flex;
        align-items: center;
        color: #475569;
        font-weight: 500;
        font-size: 14px;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap ul.usernavdash li.active a {
        color: #3b82f6;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll .usernavwrap ul.usernavdash li a i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
        font-size: 16px;
    }
    
    /* Scrollbar styling for sticky sidebar */
    .custom-dashboard-sidebar .sidebar-inner-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll::-webkit-scrollbar-thumb {
        background: #e2e8f0; 
        border-radius: 10px;
    }
    .custom-dashboard-sidebar .sidebar-inner-scroll::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1; 
    }
</style>
<div class="custom-dashboard-sidebar">
<div class="sidebar-inner-scroll">    <!-- Featured Profile Package -->
    @if((bool) config('jobseeker.is_featured_package_active_jobseeker'))
            <?php 
            $featured_package = App\Package::where('package_for', 'make_featured')->first();
            $packageEndDate = auth()->user()->featured_package_end_at ?? auth()->user()->package_end_date;
            $isExpired = $packageEndDate ? \Carbon\Carbon::parse($packageEndDate)->isPast() : true;
        ?>

            @if(!auth()->user()->is_featured || $isExpired)
                <div class="featuredprofile">
                    <div class="packginfor">
                        <h5><i class="fas fa-bolt"></i> {{$featured_package->package_title}}</h5>
                        <div class="featprice">
                            {{ $siteSetting->default_currency_code ?? '' }}{{$featured_package->package_price}}<span>{{__('For')}}
                                {{$featured_package->package_num_days}} {{__('Days')}}</span></div>
                        <p>{{__('Gain a competitive edge in the job market with exclusive features')}}</p>
                        <ul class="pckfeatlist">
                            <li><i class="fas fa-crown"></i> {{ __('Premium Badge') }}</li>
                            <li><i class="fas fa-chart-line"></i> {{ __('Rank Booster') }}</li>
                            <li><i class="fas fa-ribbon"></i> {{ __('Your CV above all others') }}</li>
                            <li><i class="fas fa-briefcase"></i> {{ __('Increased Job Opportunities') }}</li>
                            <li><i class="fas fa-eye"></i> {{ __('Higher Profile Views') }}</li>
                            <li><i class="fas fa-bell"></i> {{ __('Exclusive Alerts') }}</li>
                        </ul>

                    </div>
                    <div class="order">
                        @if(count(auth()->user()->getProfileCvsArray()) == 0 || count(auth()->user()->profileExperience()->get()) == 0 || count(auth()->user()->profileEducation()->get()) == 0 || count(auth()->user()->profileSkills()->get()) == 0)
                            <a href="javascript:void();" data-bs-toggle="modal" data-bs-target="#buyfeatured">{{__('Buy Now')}}</a>
                        @else
                            <a href="javascript:void(0);" data-bs-toggle="modal"
                                data-bs-target="#paymentModalFeatured{{$featured_package->id}}">{{__('Buy Now')}}</a>
                        @endif
                    </div>
                </div>
            @else
                <div class="featuredprofile purchased">
                    <div class="packginfor">
                        <h5>{{ __('Featured Profile') }}</h5>
                        <p>{{ __('Congratulations! Your profile is now prominently displayed to attract more attention from recruiters and employers.') }}
                        </p>
                    </div>
                    <ul class="pckfeatlist">
                        <li><i class="fas fa-crown"></i> {{ __('Premium Badge') }}</li>
                        <li><i class="fas fa-chart-line"></i> {{ __('Rank Booster') }}</li>
                        <li><i class="fas fa-ribbon"></i> {{ __('Your CV above all others') }}</li>
                        <li><i class="fas fa-briefcase"></i> {{ __('Increased Job Opportunities') }}</li>
                        <li><i class="fas fa-eye"></i> {{ __('Higher Profile Views') }}</li>
                        <li><i class="fas fa-bell"></i> {{ __('Exclusive Alerts') }}</li>
                    </ul>

                    <div class="">
                        <div class="order">
                            <span>{{__('Package Start On')}}</span>
                            <strong>{{ \Carbon\Carbon::parse(auth()->user()->featured_package_start_at ?? auth()->user()->package_start_date)->format('d M Y') }}</strong>
                        </div>
                        <div class="order">
                            <span>{{__('Package Ends On')}}</span>
                            <strong>{{ \Carbon\Carbon::parse($packageEndDate)->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Payment Gateway Modal for Featured Package -->
            <div class="modal fade" id="paymentModalFeatured{{$featured_package->id}}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('Select Payment Method')}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h6 class="mb-3">{{__('Package')}}: <strong>{{$featured_package->package_title}}
                                    ({{ $siteSetting->default_currency_code ?? '' }}{{$featured_package->package_price}})</strong>
                            </h6>
                            <div class="payment-methods">
                                @if((bool) $siteSetting->is_paypal_active)
                                    <a href="javascript:void(0)"
                                        onclick="checkPaymentGateway('paypal', '{{!empty($siteSetting->paypal_client_id) && !empty($siteSetting->paypal_secret)}}', '{{route('order.package', $featured_package->id)}}')"
                                        class="payment-method-btn">
                                        <i class="fab fa-cc-paypal"></i> {{__('Pay with PayPal')}}
                                    </a>
                                @endif
                                @if((bool) $siteSetting->is_stripe_active)
                                    <a href="javascript:void(0)"
                                        onclick="checkPaymentGateway('stripe', '{{!empty($siteSetting->stripe_key) && !empty($siteSetting->stripe_secret)}}', '{{route('stripe.order.form', [$featured_package->id, 'new'])}}')"
                                        class="payment-method-btn">
                                        <i class="fab fa-cc-stripe"></i> {{__('Pay with Stripe')}}
                                    </a>
                                @endif
                                @if((bool) $siteSetting->is_razorpay_active)
                                    <a href="javascript:void(0)"
                                        onclick="checkPaymentGateway('razorpay', '{{!empty($siteSetting->razorpay_key) && !empty($siteSetting->razorpay_secret)}}', '{{route('razorpay.order.form', [$featured_package->id, 'new'])}}')"
                                        class="payment-method-btn">
                                        <i class="fas fa-credit-card"></i> {{__('Pay with Razorpay')}}
                                    </a>
                                @endif
                                @if((bool) $siteSetting->is_paytm_active)
                                    <a href="javascript:void(0)"
                                        onclick="checkPaymentGateway('paytm', '{{!empty($siteSetting->paytm_merchant_key) && !empty($siteSetting->paytm_merchant_id)}}', '{{route('paytm.order.form', [$featured_package->id, 'new'])}}')"
                                        class="payment-method-btn">
                                        <i class="fas fa-credit-card"></i> {{__('Pay with Paytm')}}
                                    </a>
                                @endif
                                @if((bool) $siteSetting->is_payu_active)
                                    <a href="javascript:void(0)"
                                        onclick="checkPaymentGateway('payu', '{{!empty($siteSetting->payu_money_key) && !empty($siteSetting->salt)}}', '{{route('payu.order.package', ['package_id=' . $featured_package->id, 'type=new'])}}')"
                                        class="payment-method-btn">
                                        <i class="fas fa-credit-card"></i> {{__('Pay with PayU')}}
                                    </a>
                                @endif
                                @if((bool) $siteSetting->is_paystack_active)
                                    <a href="javascript:void(0)"
                                        onclick="checkPaymentGateway('paystack', '{{!empty($siteSetting->paystack_key) && !empty($siteSetting->paystack_secret)}}', '{{route('paystack.order.form', [$featured_package->id, 'new'])}}')"
                                        class="payment-method-btn">
                                        <i class="fas fa-credit-card"></i> {{__('Pay with Paystack')}}
                                    </a>
                                @endif
                                @if((bool) $siteSetting->is_iyzico_active)
                                    <a href="javascript:void(0)"
                                        onclick="checkPaymentGateway('iyzico', '{{!empty($siteSetting->iyzico_api_key) && !empty($siteSetting->iyzico_secret_key)}}', '{{route('iyzico.order.form', [$featured_package->id, 'new'])}}')"
                                        class="payment-method-btn">
                                        <i class="fas fa-credit-card"></i> {{__('Pay with Iyzico')}}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    <!-- Modal for incomplete profile -->
    <div class="modal fade mypremodal" id="buyfeatured" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="preuserinfo">
                        <h3>{{__('To Buy Featured Package you need to first complete your profile.')}}</h3>
                        <a href="{{ route('my.profile') }}" class="btn btn-yellow mt-3">{{__('Edit Profile')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Gateway Error Modal -->
    <div class="modal fade" id="paymentGatewayErrorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Payment Gateway Error')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <p id="paymentGatewayErrorMsg"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkPaymentGateway(gateway, isConfigured, redirectUrl) {
            // Close the payment selection modal if one is open
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                const modalInstance = bootstrap.Modal.getInstance(openModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }

            if (isConfigured === '1') {
                window.location.href = redirectUrl;
            } else {
                document.getElementById('paymentGatewayErrorMsg').innerHTML = '{{__("This payment gateway is not properly configured. Please contact the administrator.")}}';
                new bootstrap.Modal(document.getElementById('paymentGatewayErrorModal')).show();
            }
        }
    </script>

    <div class="usernavwrap">
        <div class="switchbox">
            <div class="txtlbl">{{__('Open to Work')}} <i class="fas fa-question-circle"
                    title="{{__('Are you immediate available')}}?"></i>
            </div>
            <div class="">
                <label class="switch switch-green"> @php
                    $checked = ((bool) Auth::user()->is_immediate_available) ? 'checked="checked"' : '';
                @endphp
                    <input type="checkbox" name="is_immediate_available" id="is_immediate_available"
                        class="switch-input" {{$checked}}
                        onchange="changeImmediateAvailableStatus({{Auth::user()->id}}, {{Auth::user()->is_immediate_available}});">
                    <span class="switch-label" data-on="Yes" data-off="No"></span> <span class="switch-handle"></span>
                </label>
            </div>
            <div class="clearfix"></div>
        </div>
        <ul class="usernavdash">
            <li class="{{ Request::url() == route('home') ? 'active' : '' }}"><a href="{{route('home')}}"><i
                        class="fas fa-tachometer" aria-hidden="true"></i> {{__('Dashboard')}}</a>
            </li>
            <li class="{{ Request::url() == route('my.profile') ? 'active' : '' }}"><a
                    href="{{ route('my.profile') }}"><i class="fas fa-pencil" aria-hidden="true"></i>
                    {{__('Edit Profile')}}</a>
            </li>
            <li class="{{ Request::url() == route('build.resume') ? 'active' : '' }}"><a
                    href="{{ route('build.resume') }}"><i class="fas fa-file" aria-hidden="true"></i>
                    {{ __('Build Resume') }}</a></li>
            <li><a href="{{ route('resume', Auth::user()->id) }}"><i class="fa fa-print" aria-hidden="true"></i>
                    {{__('Download CV')}}</a></li>
            <li><a href="{{ route('view.public.profile', Auth::user()->id) }}"><i class="fas fa-eye"
                        aria-hidden="true"></i> {{__('View Public Profile')}}</a>
            </li>
            <li class="{{ Request::url() == route('my.job.applications') ? 'active' : '' }}"><a
                    href="{{ route('my.job.applications') }}"><i class="fas fa-desktop" aria-hidden="true"></i>
                    {{__('My Job Applications')}}</a>
            </li>
            <li class="{{ Request::url() == route('recommended.jobs') ? 'active' : '' }}"><a
                    href="{{ route('recommended.jobs') }}"><i class="fas fa-thumbs-up" aria-hidden="true"></i>
                    {{__('Recommended Jobs')}}</a>
            </li>
            <li class="{{ Request::url() == route('my.favourite.jobs') ? 'active' : '' }}"><a
                    href="{{ route('my.favourite.jobs') }}"><i class="fas fa-heart" aria-hidden="true"></i>
                    {{__('My Favourite Jobs')}}</a>
            </li>
            <li class="{{ Request::url() == route('my-alerts') ? 'active' : '' }}"><a href="{{ route('my-alerts') }}"><i
                        class="fas fa-bullhorn" aria-hidden="true"></i> {{__('My Job Alerts')}}</a>
            </li>



            <li><a href="{{url('my-profile#cvs')}}"><i class="fas fa-file" aria-hidden="true"></i>
                    {{__('Manage Resume')}}</a>
            </li>
            
            <li class="{{ Request::url() == route('my.followings') ? 'active' : '' }}"><a
                    href="{{route('my.followings')}}"><i class="fas fa-user" aria-hidden="true"></i>
                    {{__('My Followings')}}</a>
            </li>
            <li class="{{ Request::url() == route('user.package') ? 'active' : '' }}"><a
                    href="{{ route('user.package') }}"><i class="fas fa-box" aria-hidden="true"></i>
                    {{__('My Packages')}}</a></li>

            <li class="{{ Request::url() == route('candidate.list-payment-history') ? 'active' : '' }}">
                <a href="{{ route('candidate.list-payment-history') }}"><i class="fas fa-file-invoice-dollar"></i>
                    {{__('Payment History')}}</a>
            </li>

            <li class="{{ Request::url() == route('user.referral.program') ? 'active' : '' }}">
                <a href="{{ route('user.referral.program') }}"><i class="fas fa-gift"></i>
                    {{__('Referral Program')}}</a>
            </li>

            <li class="{{ Request::url() == route('privacy.data.settings') ? 'active' : '' }}">
                <a href="{{ route('privacy.data.settings') }}"><i class="fas fa-shield-alt"></i>
                    {{__('Privacy & Data Settings')}}</a>
            </li>

            <li><a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                        class="fas fa-sign-out" aria-hidden="true"></i> {{__('Logout')}}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
    </div>


</div>
</div>