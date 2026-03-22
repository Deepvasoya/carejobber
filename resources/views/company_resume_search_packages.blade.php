@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('Cvs Search Packages')])
<!-- Inner Page Title end -->
<?php $company = Auth::guard('company')->user(); ?>

<div class="listpgWraper">
    <div class="container">@include('flash::message')
        <div class="row"> @include('includes.company_dashboard_menu')
            <div class="col-md-9 col-sm-8">
                @if($company->isOnExhaustedFreeCvSearchPeriod())
                    <div class="alert alert-warning" role="alert">
                        {{ __('You have used all CV unlocks on a previous free trial. Purchase a paid CV package below to keep unlocking résumés. Free monthly job posts are only under Job Packages.') }}
                    </div>
                @endif
                @if(null!==($success_package) && !empty($success_package))
                    @php
                        $isExpired = $company->cvs_package_end_date ? \Carbon\Carbon::parse($company->cvs_package_end_date)->isPast() : true;
                    @endphp
                    
                    @if($isExpired)
                        <!-- Expired Package Message -->
                        <div class="company-payment-no-records">
                            <i class="fas fa-exclamation-triangle" style="color: #ff6348; font-size: 64px; margin-bottom: 20px;"></i>
                            <h3>{{__('Your CVs Package Has Expired')}}</h3>
                            <p>{{__('Your package expired on')}} <strong>{{ \Carbon\Carbon::parse($company->cvs_package_end_date)->format('d M, Y') }}</strong></p>
                            <p>{{__('Please purchase a new package to continue accessing candidate CVs')}}</p>
                            <a href="#package-list" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> {{__('Buy New Package')}}
                            </a>
                        </div>
                    @else
                        <div class="company-cvs-package-details">
                            <div class="package-header">
                                <h3><i class="fas fa-file-alt"></i> {{__('Purchased CVs Package Details')}}</h3>
                            </div>
                            
                            <div class="package-info-grid">
                                <!-- Package Name Card -->
                                <div class="package-info-card package-name-card cvs-package">
                                    <div class="package-icon">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div class="package-content">
                                        <span class="package-label">{{__('Package Name')}}</span>
                                        <h4 class="package-value">{{$success_package->package_title}}</h4>
                                    </div>
                                </div>

                                <!-- Price Card -->
                                <div class="package-info-card cvs-package">
                                    <div class="package-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="package-content">
                                        <span class="package-label">{{__('Price')}}</span>
                                        <h4 class="package-value">{{ $siteSetting->default_currency_code }} {{$success_package->package_price}}</h4>
                                    </div>
                                </div>

                                <!-- CV Quota Card -->
                                <div class="package-info-card quota-card cvs-package">
                                    <div class="package-icon">
                                        <i class="fas fa-file-download"></i>
                                    </div>
                                    <div class="package-content">
                                        <span class="package-label">{{__('Available CV Quota')}}</span>
                                        <h4 class="package-value">
                                            <span class="quota-available">{{ $company->getRemainingCvsQuota() }}</span>
                                            <span class="quota-separator">/</span>
                                            <span class="quota-total">{{$company->cvs_quota}}</span>
                                        </h4>
                                    </div>
                                </div>

                                <!-- Start Date Card -->
                                <div class="package-info-card cvs-package">
                                    <div class="package-icon">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <div class="package-content">
                                        <span class="package-label">{{__('Purchased On')}}</span>
                                        <h4 class="package-value">{{Carbon\Carbon::parse($company->cvs_package_start_date)->format('d M, Y')}}</h4>
                                    </div>
                                </div>

                                <!-- End Date Card -->
                                <div class="package-info-card cvs-package">
                                    <div class="package-icon">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <div class="package-content">
                                        <span class="package-label">{{__('Package Expires')}}</span>
                                        <h4 class="package-value">{{Carbon\Carbon::parse($company->cvs_package_end_date)->format('d M, Y')}}</h4>
                                    </div>
</div>
</div>
            @endif
                                @else
                                    <!-- No Package Message -->
                                    <div class="company-payment-no-records">
                                        <i class="fas fa-inbox"></i>
                                        <h3>{{__('No Active CVs Package Found')}}</h3>
                                        <p>{{__('You haven\'t purchased any CVs package yet')}}</p>
                                        <p>{{__('Purchase a package to unlock and view candidate CVs')}}</p>
                                        <a href="#package-list" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart"></i> {{__('Buy Package')}}
                                        </a>
                                    </div>
                                @endif
</div>
                
                        <div class="paypackages" id="package-list">
    <!---four-paln-->
    <?php 
        $package = Auth::guard('company')->user()->cvs_getPackage();
     ?>
     @if(null!==($package))
       <div class="four-plan">
        <h3>{{__('Upgrade CV Search Packages')}}</h3>
        <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> {{ __('Paid plans only — unlock and view applicant résumés. Free monthly job posts are under Job Packages on your dashboard (not here).') }}</p>
        <div class="row"> @foreach($packages as $package)
        @if($package->package_for === 'cv_search' && (float) $package->package_price <= 0)
            @continue
        @endif
        <div class="col-md-4 col-sm-6 col-xs-12">
                            <ul class="boxes">
                                <li class="plan-name">{{$package->package_title}}</li>
                                <li>
                                    <div class="main-plan">
                                        <div class="plan-price1-1">{{ $siteSetting->default_currency_code }}</div>
                                        <div class="plan-price1-2">{{$package->package_price}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li class="plan-pages"><i class="far fa-check-square"></i> {{__('Applicant CV Views')}} {{$package->package_num_listings}}</li>
                                <li class="plan-pages"><i class="far fa-check-square"></i> {{__('CV View Access')}} {{$package->package_num_days}} {{__('Days')}}</li>
                                <li class="plan-pages"><i class="far fa-check-square"></i> {{__('Premium Support 24/7')}}</li> 
                                <li class="order paypal"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#buypack{{$package->id}}" class="reqbtn">{{__('Buy Now')}} <i class="fas fa-arrow-right"></i></a></li>
                            </ul>
                        </div>

                        <div class="modal fade" id="buypack{{$package->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-body">
                        <button type="button" class="close ms-auto" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                        </button>
                        <div class="invitereval">
                        <h3>{{__('Choose Your Payment Method')}}</h3>	
                            
                        <div class="totalpay">{{__('Total Amount to pay')}}: <strong>{{ $siteSetting->default_currency_code }} {{$package->package_price}}</strong></div>
                            
                        <ul class="btn2s">
                        
                                @if((bool)$siteSetting->is_paypal_active)
                                <li class="order paypal p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paypal', '{{!empty($siteSetting->paypal_client_id) && !empty($siteSetting->paypal_secret)}}', '{{route('order.upgrade.package', $package->id)}}')" class="paypal">
                                            {{__('PayPal')}} <i class="fab fa-cc-paypal" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_stripe_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('stripe', '{{!empty($siteSetting->stripe_key) && !empty($siteSetting->stripe_secret)}}', '{{route('stripe.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Stripe')}} <i class="fab fa-cc-stripe" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_paystack_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paystack', '{{!empty($siteSetting->paystack_key) && !empty($siteSetting->paystack_secret)}}', '{{route('paystack.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Paystack')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_razorpay_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('razorpay', '{{!empty($siteSetting->razorpay_key) && !empty($siteSetting->razorpay_secret)}}', '{{route('razorpay.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Razorpay')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_paytm_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paytm', '{{!empty($siteSetting->paytm_merchant_key) && !empty($siteSetting->paytm_merchant_id)}}', '{{route('paytm.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Paytm')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_payu_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('payu', '{{!empty($siteSetting->payu_money_key) && !empty($siteSetting->salt)}}', '{{route('payu.order.package', ['package_id='.$package->id, 'type=upgrade'])}}')">
                                            {{__('PayU')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif

                        </ul>		
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
            @endforeach </div>
    </div>
     @else
    <div class="four-plan">
        <h3>{{__('CV Search Packages')}}</h3>
        <p class="text-muted small mb-3"><i class="fas fa-info-circle"></i> {{ __('Paid plans only — résumé / CV unlocks. Free monthly job posts are under Job Packages, not here.') }}</p>
        <div class="row"> @foreach($packages as $package)
        @if($package->package_for === 'cv_search' && (float) $package->package_price <= 0)
            @continue
        @endif
        <div class="col-md-4 col-sm-6 col-xs-12">
                            <ul class="boxes">
                                <li class="plan-name">{{$package->package_title}}</li>
                                <li>
                                    <div class="main-plan">
                                        <div class="plan-price1-1">{{ $siteSetting->default_currency_code }}</div>
                                        <div class="plan-price1-2">{{$package->package_price}}</div>
                                        <div class="clearfix"></div>
                                    </div>
                                </li>
                                <li class="plan-pages"><i class="far fa-check-square"></i> {{__('Applicant CV Views')}} {{$package->package_num_listings}}</li>
                                <li class="plan-pages"><i class="far fa-check-square"></i> {{__('CV View Access')}} {{$package->package_num_days}} {{__('Days')}}</li>
                                <li class="plan-pages"><i class="far fa-check-square"></i> {{__('Premium Support 24/7')}}</li> 
                                <li class="order paypal"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#buypack{{$package->id}}" class="reqbtn">{{__('Buy Now')}} <i class="fas fa-arrow-right"></i></a></li>

                            </ul>
                        </div>

                        <div class="modal fade" id="buypack{{$package->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-body">
                        <button type="button" class="close ms-auto" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                        </button>
                        <div class="invitereval">
                        <h3>{{__('Choose Your Payment Method')}}</h3>	
                            
                        <div class="totalpay">{{__('Total Amount to pay')}}: <strong>{{ $siteSetting->default_currency_code }} {{$package->package_price}}</strong></div>
                            
                        <ul class="btn2s">
                        
                                @if((bool)$siteSetting->is_paypal_active)
                                <li class="order paypal p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paypal', '{{!empty($siteSetting->paypal_client_id) && !empty($siteSetting->paypal_secret)}}', '{{route('order.upgrade.package', $package->id)}}')" class="paypal">
                                            {{__('PayPal')}} <i class="fab fa-cc-paypal" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_stripe_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('stripe', '{{!empty($siteSetting->stripe_key) && !empty($siteSetting->stripe_secret)}}', '{{route('stripe.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Stripe')}} <i class="fab fa-cc-stripe" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_paystack_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paystack', '{{!empty($siteSetting->paystack_key) && !empty($siteSetting->paystack_secret)}}', '{{route('paystack.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Paystack')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_razorpay_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('razorpay', '{{!empty($siteSetting->razorpay_key) && !empty($siteSetting->razorpay_secret)}}', '{{route('razorpay.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Razorpay')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_paytm_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('paytm', '{{!empty($siteSetting->paytm_merchant_key) && !empty($siteSetting->paytm_merchant_id)}}', '{{route('paytm.order.form', [$package->id, 'upgrade'])}}')">
                                            {{__('Paytm')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif
                                @if((bool)$siteSetting->is_payu_active)
                                <li class="order p-2">
                                        <a href="javascript:void(0)" onclick="checkPaymentGateway('payu', '{{!empty($siteSetting->payu_money_key) && !empty($siteSetting->salt)}}', '{{route('payu.order.package', ['package_id='.$package->id, 'type=upgrade'])}}')">
                                            {{__('PayU')}} <i class="fas fa-credit-card" aria-hidden="true"></i>
                                        </a>
                                        </li>
                                @endif

                        </ul>		
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
            @endforeach </div>
    </div>
    @endif
    <!---end four-paln-->
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

@include('includes.footer')
@endsection
@push('scripts')
@include('includes.immediate_available_btn')

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
@endpush