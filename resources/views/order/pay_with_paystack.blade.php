@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Pay with Paystack') }}</div>
                <div class="card-body">
                    <h4>{{ $package->package_title }}</h4>
                    <p>{{ __('Amount') }}: <strong>{{ $package->package_price }}</strong></p>
                    <form method="POST" action="{{ route('paystack.order.package') }}">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <button type="submit" class="btn btn-success">{{ __('Pay with Paystack') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 