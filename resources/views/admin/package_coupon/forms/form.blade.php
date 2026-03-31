<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Coupon code') }} <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control" required maxlength="64"
                   value="{{ old('code', isset($coupon) ? $coupon->code : '') }}"
                   placeholder="{{ __('e.g. SUMMER25') }}">
            <span class="help-block">{{ __('Stored uppercase; spaces removed.') }}</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Admin note') }}</label>
            <input type="text" name="admin_note" class="form-control" maxlength="255"
                   value="{{ old('admin_note', isset($coupon) ? $coupon->admin_note : '') }}"
                   placeholder="{{ __('Internal label only') }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Discount type') }} <span class="text-danger">*</span></label>
            <select name="discount_type" class="form-control" required id="discount_type">
                <option value="percent" @selected(old('discount_type', isset($coupon) ? $coupon->discount_type : 'percent') == 'percent')>{{ __('Percent (%)') }}</option>
                <option value="fixed" @selected(old('discount_type', isset($coupon) ? $coupon->discount_type : '') == 'fixed')>{{ __('Fixed amount') }}</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Discount value') }} <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" name="discount_value" class="form-control" required
                   value="{{ old('discount_value', isset($coupon) ? $coupon->discount_value : '') }}">
        </div>
    </div>
    <div class="col-md-4" id="max_discount_wrap">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Max discount (cap for %)') }}</label>
            <input type="number" step="0.01" min="0" name="max_discount_amount" class="form-control"
                   value="{{ old('max_discount_amount', isset($coupon) ? $coupon->max_discount_amount : '') }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Minimum package price') }}</label>
            <input type="number" step="0.01" min="0" name="min_package_price" class="form-control"
                   value="{{ old('min_package_price', isset($coupon) ? $coupon->min_package_price : '') }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Valid from') }}</label>
            <input type="datetime-local" name="starts_at" class="form-control"
                   value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Valid until') }}</label>
            <input type="datetime-local" name="ends_at" class="form-control"
                   value="{{ old('ends_at', isset($coupon) && $coupon->ends_at ? $coupon->ends_at->format('Y-m-d\TH:i') : '') }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Total redemptions limit') }}</label>
            <input type="number" min="1" name="usage_limit_total" class="form-control"
                   value="{{ old('usage_limit_total', isset($coupon) ? $coupon->usage_limit_total : '') }}"
                   placeholder="{{ __('Leave empty for unlimited') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Per buyer limit (user or company)') }}</label>
            <input type="number" min="1" name="usage_limit_per_buyer" class="form-control"
                   value="{{ old('usage_limit_per_buyer', isset($coupon) ? $coupon->usage_limit_per_buyer : '') }}"
                   placeholder="{{ __('Leave empty for unlimited') }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Restrict to package audience') }}</label>
            <select name="package_for_scope" class="form-control">
                <option value="">{{ __('Any (all package types)') }}</option>
                <option value="job_seeker" @selected(old('package_for_scope', isset($coupon) ? $coupon->package_for_scope : '') == 'job_seeker')>{{ __('Job seeker') }}</option>
                <option value="employer" @selected(old('package_for_scope', isset($coupon) ? $coupon->package_for_scope : '') == 'employer')>{{ __('Employer') }}</option>
                <option value="cv_search" @selected(old('package_for_scope', isset($coupon) ? $coupon->package_for_scope : '') == 'cv_search')>{{ __('CV search') }}</option>
                <option value="make_featured" @selected(old('package_for_scope', isset($coupon) ? $coupon->package_for_scope : '') == 'make_featured')>{{ __('Make featured') }}</option>
                <option value="resume_promotion" @selected(old('package_for_scope', isset($coupon) ? $coupon->package_for_scope : '') == 'resume_promotion')>{{ __('Resume promotion (Promote Your Resume)') }}</option>
            </select>
            <span class="help-block">{{ __('Employer / CV search codes only work on company purchases. For job seekers use Job seeker (also covers featured profile packages), Make featured, or Any. Resume promotion codes only work on /resume-promotion-packages.') }}</span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="control-label">{{ __('Allow employer subscription packages') }}</label>
            <div class="mt-2">
                <input type="hidden" name="allow_subscription_packages" value="0">
                <label class="me-3">
                    <input type="checkbox" name="allow_subscription_packages" value="1"
                           @checked(old('allow_subscription_packages', isset($coupon) ? $coupon->allow_subscription_packages : false))>
                    {{ __('Yes (only packages without a fixed Stripe Price ID)') }}
                </label>
            </div>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <label class="control-label">{{ __('Limit to specific packages') }}</label>
    <select name="package_ids[]" class="form-control" multiple size="8">
        @foreach($packages as $p)
            @php
                $sel = collect(old('package_ids', isset($coupon) ? ($coupon->package_ids ?? []) : []))->map(fn ($v) => (int) $v)->contains((int) $p->id);
            @endphp
            <option value="{{ $p->id }}" @selected($sel)>{{ $p->package_title }} — {{ $p->package_for }} (${{ number_format($p->package_price, 2) }})</option>
        @endforeach
    </select>
    <span class="help-block">{{ __('Hold Ctrl/Cmd to select multiple. Leave none selected for all packages in scope.') }}</span>
</div>

<div class="form-group mb-3">
    <label class="control-label">{{ __('Limit to specific resume promotion durations') }}</label>
    <select name="resume_promotion_package_ids[]" class="form-control" multiple size="6">
        @foreach($resumePromotionPackages ?? [] as $rp)
            @php
                $selRp = collect(old('resume_promotion_package_ids', isset($coupon) ? ($coupon->resume_promotion_package_ids ?? []) : []))->map(fn ($v) => (int) $v)->contains((int) $rp->id);
            @endphp
            <option value="{{ $rp->id }}" @selected($selRp)>{{ $rp->name }} — {{ $rp->duration_days }} {{ __('days') }} (${{ number_format($rp->price, 2) }} {{ $rp->currency }})</option>
        @endforeach
    </select>
    <span class="help-block">{{ __('Optional. Only used for “Resume promotion” or “Any” coupons on the Promote Your Resume checkout. Leave empty to allow all active promotion tiers.') }}</span>
</div>

<div class="form-group mb-3">
    <input type="hidden" name="is_active" value="0">
    <label>
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', isset($coupon) ? $coupon->is_active : true))>
        {{ __('Active') }}
    </label>
</div>

@push('scripts')
<script>
(function() {
    var sel = document.getElementById('discount_type');
    var wrap = document.getElementById('max_discount_wrap');
    function toggle() {
        if (!sel || !wrap) return;
        wrap.style.display = sel.value === 'percent' ? '' : 'none';
    }
    if (sel) { sel.addEventListener('change', toggle); toggle(); }
})();
</script>
@endpush
