@php
    $seekerCoupon = session('jobseeker_package_coupon_code');
@endphp
<div class="package-coupon-card card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
    <div class="card-body p-4">
        <h3 class="h5 mb-3"><i class="fas fa-ticket-alt text-success me-2"></i>{{ __('Coupon code') }}</h3>
        @if($seekerCoupon)
            <p class="mb-2">{{ __('Applied:') }} <strong class="text-success">{{ $seekerCoupon }}</strong></p>
            <p class="small text-muted mb-2">{{ __('Discount applies when you pay with Stripe, if the coupon is valid for the package you choose.') }}</p>
            <form method="post" action="{{ route('user.package.clear.coupon') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('Remove coupon') }}</button>
            </form>
        @else
            <form method="post" action="{{ route('user.package.apply.coupon') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-8">
                    <label class="form-label small text-muted mb-1">{{ __('Enter code') }}</label>
                    <input type="text" name="code" class="form-control" placeholder="{{ __('e.g. WELCOME10') }}" autocomplete="off">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100">{{ __('Apply') }}</button>
                </div>
            </form>
        @endif
    </div>
</div>
