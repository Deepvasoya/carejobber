@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Pay with Paytm') }}</div>

                <div class="card-body">
                    <form action="{{ route('paytm.order.package') }}" method="POST" id="paytm-form">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">

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
                                <button type="submit" class="btn btn-primary">
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
@endsection 