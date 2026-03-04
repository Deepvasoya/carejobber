@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Pay with Razorpay') }}</div>

                <div class="card-body">
                    <form action="{{ route('razorpay.order.package') }}" method="POST" id="razorpay-form">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $razorpayOrder->id }}">
                        <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Package') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="{{ $package->package_title }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ __('Amount') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="{{ $package->package_price }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-primary" id="pay-button">
                                    {{ __('Pay Now') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ $siteSetting->razorpay_key }}",
        "amount": "{{ $razorpayOrder->amount }}",
        "currency": "{{ $razorpayOrder->currency }}",
        "name": "{{ config('app.name') }}",
        "description": "Package Payment",
        "order_id": "{{ $razorpayOrder->id }}",
        "handler": function (response){
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('razorpay-form').submit();
        },
        "prefill": {
            "name": "{{ Auth::guard('company')->check() ? Auth::guard('company')->user()->name : Auth::user()->name }}",
            "email": "{{ Auth::guard('company')->check() ? Auth::guard('company')->user()->email : Auth::user()->email }}",
            "contact": "{{ Auth::guard('company')->check() ? Auth::guard('company')->user()->phone : Auth::user()->phone }}"
        },
        "theme": {
            "color": "#F37254"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('pay-button').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
</script>
@endsection
