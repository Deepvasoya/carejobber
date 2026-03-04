@extends('layouts.app')
@section('content') 
<!-- Header start --> 
@include('includes.header') 
<!-- Header end --> 
<!-- Inner Page Title start --> 
@include('includes.inner_page_title', ['page_title'=>__('Pay with Iyzico')]) 
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container">
        <div class="row"> 
            @if(Auth::guard('company')->check())
            @include('includes.company_dashboard_menu')
            @else
            @include('includes.user_dashboard_menu')
            @endif
            <div class="col-md-9 col-sm-8">
                <div class="userccount">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="payment-gateway-logo text-center mb-4">
                                <div class="iyzico-logo" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; border-radius: 10px; color: white;">
                                    <i class="fas fa-credit-card" style="font-size: 60px; margin-bottom: 15px;"></i>
                                    <h3 style="color: white; margin: 0;">Iyzico</h3>
                                    <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0; font-size: 14px;">{{__('Secure Payment Gateway')}}</p>
                                </div>
                            </div>
                            <div class="strippckinfo">
                                <h5>{{__('Invoice Details')}}</h5>
                                <div class="pkginfo">{{__('Package')}}: <strong>{{ $package->package_title }}</strong></div>
                                <div class="pkginfo">{{__('Price')}}: <strong>{{ $siteSetting->default_currency_code }}{{ $package->package_price }}</strong></div>

                                @if(Auth::guard('company')->check())
                                    @if($package->package_for == 'employer')
                                        <div class="pkginfo">{{__('Can post jobs')}}: <strong>{{ $package->package_num_listings }}</strong></div>
                                    @elseif($package->package_for == 'cv_search')
                                        <div class="pkginfo">{{__('Applicant CV Views')}}: <strong>{{ $package->package_num_listings }}</strong></div>
                                    @endif
                                @elseif($package->package_for == 'job_seeker')
                                    <div class="pkginfo">{{__('Can apply on jobs')}}: <strong>{{ $package->package_num_listings }}</strong></div>
                                @endif

                                <div class="pkginfo">{{__('Package Duration')}}: <strong>{{ $package->package_num_days }} {{__('Days')}}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="formpanel"> 
                                @include('flash::message')
                                <h5>{{__('Iyzico - Payment Details')}}</h5>
                               
                                
                                @php                
                                $route = 'iyzico.order.upgrade.package';                
                                if($new_or_upgrade == 'new'){                
                                $route = 'iyzico.order.package';                
                                }                
                                @endphp
                                
                                @if(isset($checkoutFormContent) && !empty($checkoutFormContent))
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="formrow">
                                                <label>{{__('Payment Method')}}</label>
                                                <div class="payment-method-display" style="padding: 15px; background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;">
                                                    <i class="fas fa-credit-card" style="font-size: 24px; color: #667eea; margin-right: 10px;"></i>
                                                    <span style="font-size: 16px; font-weight: 500;">{{__('Iyzico Payment Gateway')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="formrow">
                                                <label>{{__('Total Amount')}}</label>
                                                <div class="total-amount-display" style="padding: 15px; background: #e7f3ff; border-radius: 5px; border: 1px solid #b3d9ff;">
                                                    <span style="font-size: 24px; font-weight: bold; color: #0066cc;">{{ $siteSetting->default_currency_code }}{{ $package->package_price }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="formrow">
                                                <div id="iyzico-checkout-form">
                                                    {!! $checkoutFormContent !!}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12 mt-3" id="payment-closed-message" style="display: none;">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-circle"></i> 
                                                {{__('Payment form was closed. Click the button below to reopen it.')}}
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12 mt-3">
                                            <div class="formrow text-center">
                                                <button type="button" id="reopen-payment-btn" class="btn btn-primary" style="width: 100%; padding: 10px; margin-bottom: 10px;">
                                                    <i class="fas fa-redo" aria-hidden="true"></i> {{__('Reload Payment Form')}}
                                                </button>
                                                <a href="{{ Auth::guard('company')->check() ? route('company.home') : route('home') }}" class="btn btn-secondary" style="width: 100%; padding: 10px;">
                                                    <i class="fa fa-arrow-left" aria-hidden="true"></i> {{__('Go Back')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i> {{__('Payment form could not be loaded. Please try again.')}}
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <div class="formrow text-center">
                                                <a href="{{ Auth::guard('company')->check() ? route('company.home') : route('home') }}" class="btn btn-secondary" style="width: 100%; padding: 10px;">
                                                    <i class="fa fa-arrow-left" aria-hidden="true"></i> {{__('Go Back')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <hr>
                                
                                <div class="payment-security-info" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                                    <h6 style="margin-bottom: 10px;"><i class="fas fa-shield-alt" style="color: #28a745;"></i> {{__('Secure Payment')}}</h6>
                                    <p style="font-size: 13px; color: #6c757d; margin: 0;">
                                        {{__('Your payment information is encrypted and secure. We do not store your credit card details.')}}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
@push('styles')
<style type="text/css">
    .userccount p{ text-align:left !important;}
    .strippckinfo {
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    .strippckinfo h5 {
        margin-bottom: 15px;
        color: #333;
        font-weight: 600;
    }
    .pkginfo {
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
        font-size: 14px;
    }
    .pkginfo:last-child {
        border-bottom: none;
    }
    .pkginfo strong {
        float: right;
        color: #667eea;
    }
    .formpanel {
        background: white;
        padding: 25px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .formpanel h5 {
        margin-bottom: 20px;
        color: #333;
        font-weight: 600;
    }
    .formrow {
        margin-bottom: 20px;
    }
    .formrow label {
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    #reopen-payment-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }
    #reopen-payment-btn:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        color: white;
    }
</style>
@endpush
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Check if Iyzico modal/iframe exists
        var checkModalInterval = setInterval(function() {
            // Check if the Iyzico checkout form iframe exists
            var iyzicoIframe = $('#iyzico-checkout-form iframe');
            var iyzicoForm = $('#iyzico-checkout-form form');
            
            // If no iframe or form is visible, show the reopen button
            if (iyzicoIframe.length === 0 && iyzicoForm.length === 0) {
                // Check if payment was initialized (form content exists)
                var checkoutContent = $('#iyzico-checkout-form').html().trim();
                if (checkoutContent && checkoutContent.length > 0 && !checkoutContent.includes('iframe')) {
                    // Payment form might have been closed
                    $('#payment-closed-message').show();
                    $('#reopen-payment-btn').show();
                    clearInterval(checkModalInterval);
                }
            }
        }, 2000);
        
        // Stop checking after 10 seconds
        setTimeout(function() {
            clearInterval(checkModalInterval);
        }, 10000);
        
        // Reopen payment button click handler
        $('#reopen-payment-btn').on('click', function() {
            // Show loading state
            $(this).html('<i class="fas fa-spinner fa-spin"></i> {{__("Loading...")}}').prop('disabled', true);
            // Reload the page to reinitialize the payment
            window.location.reload();
        });
        
        // Also listen for Iyzico modal close events
        $(window).on('message', function(e) {
            if (e.originalEvent.data && typeof e.originalEvent.data === 'string') {
                try {
                    var data = JSON.parse(e.originalEvent.data);
                    if (data.type === 'iyzico-modal-closed' || data.action === 'close') {
                        $('#payment-closed-message').show();
                        $('#reopen-payment-btn').show();
                    }
                } catch(err) {
                    // Not JSON, ignore
                }
            }
        });
    });
</script>
@endpush


