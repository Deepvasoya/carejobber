@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i></li>
                <li><a href="{{ route('list.package.coupons') }}">{{ __('Package coupons') }}</a> <i class="fa fa-circle"></i></li>
                <li><span>{{ __('Edit') }}</span></li>
            </ul>
        </div>
        @include('flash::message')
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-green-sharp"><i class="fa fa-ticket"></i> {{ __('Edit coupon') }}: {{ $coupon->code }}</div>
            </div>
            <div class="portlet-body form">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="post" action="{{ route('update.package.coupon', $coupon->id) }}" class="form-horizontal">
                    @csrf
                    @method('PUT')
                    @include('admin.package_coupon.forms.form')
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                        <a href="{{ route('list.package.coupons') }}" class="btn btn-default">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
