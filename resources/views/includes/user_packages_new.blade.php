@if($packages->count())
<div class="user-pkg-section">
    <div class="user-pkg-section-header">
        <h3><i class="fas fa-shopping-cart"></i> {{__('Available Packages')}}</h3>
        <p>{{__('Choose the perfect package to boost your job search')}}</p>
    </div>
    
    <div class="row">
        @foreach($packages as $package)
        <div class="col-md-4 col-sm-6">
            <div class="user-pkg-card">
                <div class="user-pkg-card-header">
                    <h4>{{$package->package_title}}</h4>
                    @if($package->id == 9)
                        <span class="user-pkg-badge featured">{{__('Featured')}}</span>
                    @endif
                </div>
                
                <div class="user-pkg-price">
                    <span class="user-pkg-currency">$</span>
                    <span class="user-pkg-amount">{{$package->package_price}}</span>
                </div>
                
                <div class="user-pkg-features">
                    <div class="user-pkg-feature">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{$package->package_num_days}} {{__('Days')}}</span>
                    </div>
                    @if($package->package_num_listings > 0)
                    <div class="user-pkg-feature">
                        <i class="fas fa-briefcase"></i>
                        <span>{{$package->package_num_listings}} {{__('Job Applications')}}</span>
                    </div>
                    @endif
                    @if($package->id == 9)
                    <div class="user-pkg-feature">
                        <i class="fas fa-star"></i>
                        <span>{{__('Featured Profile Badge')}}</span>
                    </div>
                    @endif
                </div>
                
                @if($package->package_price > 0)
                    <button type="button" class="user-pkg-btn" data-bs-toggle="modal" data-bs-target="#paymentModalNew{{$package->id}}">
                        <i class="fas fa-shopping-cart"></i> {{__('Buy Now')}}
                    </button>
                @else
                    <a href="{{route('order.free.package', $package->id)}}" class="user-pkg-btn">
                        <i class="fas fa-gift"></i> {{__('Get Free Package')}}
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Payment Gateway Modals -->
@foreach($packages as $package)
<div class="modal fade" id="paymentModalNew{{$package->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Select Payment Method')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="mb-3">{{__('Package')}}: <strong>{{$package->package_title}} (${{$package->package_price}})</strong></h6>
                <div class="payment-methods">
                    @if((bool)$siteSetting->is_paypal_active)
                    <a href="javascript:void(0)" onclick="checkPaymentGateway('paypal', '{{!empty($siteSetting->paypal_client_id) && !empty($siteSetting->paypal_secret)}}', '{{route('order.package', $package->id)}}')" class="payment-method-btn">
                        <i class="fab fa-cc-paypal"></i> {{__('Pay with PayPal')}}
                    </a>
                    @endif
                    @if((bool)$siteSetting->is_stripe_active)
                    <a href="javascript:void(0)" onclick="checkPaymentGateway('stripe', '{{!empty($siteSetting->stripe_key) && !empty($siteSetting->stripe_secret)}}', '{{route('stripe.order.form', [$package->id, 'new'])}}')" class="payment-method-btn">
                        <i class="fab fa-cc-stripe"></i> {{__('Pay with Stripe')}}
                    </a>
                    @endif
                    @if((bool)$siteSetting->is_razorpay_active)
                    <a href="javascript:void(0)" onclick="checkPaymentGateway('razorpay', '{{!empty($siteSetting->razorpay_key) && !empty($siteSetting->razorpay_secret)}}', '{{route('razorpay.order.form', [$package->id, 'new'])}}')" class="payment-method-btn">
                        <i class="fas fa-credit-card"></i> {{__('Pay with Razorpay')}}
                    </a>
                    @endif
                    @if((bool)$siteSetting->is_paytm_active)
                    <a href="javascript:void(0)" onclick="checkPaymentGateway('paytm', '{{!empty($siteSetting->paytm_merchant_key) && !empty($siteSetting->paytm_merchant_id)}}', '{{route('paytm.order.form', [$package->id, 'new'])}}')" class="payment-method-btn">
                        <i class="fas fa-credit-card"></i> {{__('Pay with Paytm')}}
                    </a>
                    @endif
                    @if((bool)$siteSetting->is_payu_active)
                    <a href="javascript:void(0)" onclick="checkPaymentGateway('payu', '{{!empty($siteSetting->payu_money_key) && !empty($siteSetting->salt)}}', '{{route('payu.order.package', ['package_id='.$package->id, 'type=new'])}}')" class="payment-method-btn">
                        <i class="fas fa-credit-card"></i> {{__('Pay with PayU')}}
                    </a>
                    @endif
                    @if((bool)$siteSetting->is_paystack_active)
                    <a href="javascript:void(0)" onclick="checkPaymentGateway('paystack', '{{!empty($siteSetting->paystack_key) && !empty($siteSetting->paystack_secret)}}', '{{route('paystack.order.form', [$package->id, 'new'])}}')" class="payment-method-btn">
                        <i class="fas fa-credit-card"></i> {{__('Pay with Paystack')}}
                    </a>
                    @endif
                    @if((bool)$siteSetting->is_iyzico_active)
                    <a href="javascript:void(0)" onclick="checkPaymentGateway('iyzico', '{{!empty($siteSetting->iyzico_api_key) && !empty($siteSetting->iyzico_secret_key)}}', '{{route('iyzico.order.form', [$package->id, 'new'])}}')" class="payment-method-btn">
                        <i class="fas fa-credit-card"></i> {{__('Pay with Iyzico')}}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Payment Gateway Error Modal -->
<div class="modal fade" id="paymentGatewayErrorModal" tabindex="-1" aria-hidden="true">
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
@endif
