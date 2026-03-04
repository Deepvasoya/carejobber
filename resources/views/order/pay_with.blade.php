@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Select Payment Method') }}</div>

                <div class="card-body">
                    <div class="row">
                        @if($siteSetting->is_paypal_active)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/paypal.png') }}" alt="PayPal" class="img-fluid mb-3" style="max-height: 50px;">
                                    <a href="{{ route('paypal.order.form', [$package->id, $new_or_upgrade]) }}" class="btn btn-primary">
                                        {{ __('Pay with PayPal') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($siteSetting->is_stripe_active)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/stripe.png') }}" alt="Stripe" class="img-fluid mb-3" style="max-height: 50px;">
                                    <a href="{{ route('stripe.order.form', [$package->id, $new_or_upgrade]) }}" class="btn btn-primary">
                                        {{ __('Pay with Stripe') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($siteSetting->is_razorpay_active)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/razorpay.png') }}" alt="Razorpay" class="img-fluid mb-3" style="max-height: 50px;">
                                    <a href="{{ route('razorpay.order.form', [$package->id, $new_or_upgrade]) }}" class="btn btn-primary">
                                        {{ __('Pay with Razorpay') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($siteSetting->is_paytm_active)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="{{ asset('images/paytm.png') }}" alt="Paytm" class="img-fluid mb-3" style="max-height: 50px;">
                                    <a href="{{ route('paytm.order.form', [$package->id, $new_or_upgrade]) }}" class="btn btn-primary">
                                        {{ __('Pay with Paytm') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 