{{--
    Shared coupon UI (employer + job seeker).
    Required: $applyRoute, $clearRoute, $ribbonSubtitle, $footerNote (route names as strings)
    Optional: $appliedCoupon (session value), $includeActiveOffersList (bool), $portalCoupons (Collection),
              $emptyOffersMessage (string when list empty), $appliedHelpText (string under code when applied),
              $couponApplyContext (optional: employer_job_posting | employer_cv_search — validates scope when applying)
--}}
@php
    $appliedCoupon = $appliedCoupon ?? null;
    $includeActiveOffersList = !empty($includeActiveOffersList);
    $portalCoupons = $portalCoupons ?? collect();
    $appliedHelpText = $appliedHelpText ?? null;
    $emptyOffersMessage = $emptyOffersMessage ?? '';
    $couponApplyContext = $couponApplyContext ?? null;
@endphp

@once
<style>
    .cj-coupon-card { --cj-teal: #0d9488; --cj-teal-light: #5eead4; --cj-teal-dark: #0f766e; --cj-teal-bg: #ecfdf5; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(13, 148, 136, 0.12), 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 1.5rem; }
    .cj-coupon-card__ribbon { background: linear-gradient(135deg, var(--cj-teal-dark) 0%, var(--cj-teal) 45%, #2dd4bf 100%); padding: 1rem 1.25rem 1.25rem; position: relative; }
    .cj-coupon-card__ribbon::after { content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 12px; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 12' preserveAspectRatio='none'%3E%3Cpath fill='%23ffffff' d='M0,12 L0,0 L60,12 L120,0 L180,12 L240,0 L300,12 L360,0 L420,12 L480,0 L540,12 L600,0 L660,12 L720,0 L780,12 L840,0 L900,12 L960,0 L1020,12 L1080,0 L1140,12 L1200,0 L1200,12 Z'/%3E%3C/svg%3E") repeat-x; background-size: 120px 12px; opacity: 0.95; }
    .cj-coupon-card__title { color: #fff; font-size: 1.15rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.65rem; text-shadow: 0 1px 2px rgba(0,0,0,0.15); }
    .cj-coupon-card__title i { width: 2.5rem; height: 2.5rem; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); border-radius: 12px; font-size: 1.1rem; }
    .cj-coupon-card__subtitle { color: rgba(255,255,255,0.88); font-size: 0.8rem; margin: 0.35rem 0 0 3.15rem; }
    .cj-coupon-card__body { background: #fff; padding: 1.35rem 1.35rem 1.25rem; margin-top: -4px; position: relative; z-index: 1; border-radius: 0 0 16px 16px; }
    .cj-coupon-card__panel { background: #fafefe; border: 1px solid rgba(13, 148, 136, 0.12); border-radius: 12px; padding: 1rem 1.1rem; margin-bottom: 1rem; }
    .cj-coupon-card__panel-label { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; color: var(--cj-teal-dark); margin-bottom: 0.65rem; }
    .cj-coupon-chip { display: flex; flex-wrap: wrap; align-items: baseline; gap: 0.5rem 0.75rem; padding: 0.65rem 0.85rem; background: #fff; border-radius: 10px; border: 1px dashed rgba(13, 148, 136, 0.35); margin-bottom: 0.5rem; box-shadow: 0 1px 3px rgba(13, 148, 136, 0.06); }
    .cj-coupon-chip:last-child { margin-bottom: 0; }
    .cj-coupon-chip__code { font-family: ui-monospace, monospace; font-weight: 800; font-size: 1rem; color: var(--cj-teal-dark); letter-spacing: 0.06em; }
    .cj-coupon-chip__meta { font-size: 0.875rem; color: #64748b; }
    .cj-coupon-chip__badge { font-size: 0.65rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 6px; background: var(--cj-teal-bg); color: var(--cj-teal-dark); }
    .cj-coupon-card__empty { font-size: 0.875rem; color: #64748b; line-height: 1.5; padding: 0.5rem 0; }
    .cj-coupon-card__applied { background: linear-gradient(90deg, var(--cj-teal-bg) 0%, #fff 100%); border: 1px solid rgba(13, 148, 136, 0.2); border-radius: 12px; padding: 1rem 1.1rem; margin-bottom: 1rem; }
    .cj-coupon-card__applied strong { color: var(--cj-teal-dark); font-family: ui-monospace, monospace; letter-spacing: 0.05em; }
    .cj-coupon-card .form-control { border-radius: 10px; border-color: #e2e8f0; padding: 0.65rem 0.9rem; }
    .cj-coupon-card .form-control:focus { border-color: var(--cj-teal); box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15); }
    .cj-coupon-card__btn { background: linear-gradient(135deg, var(--cj-teal-dark) 0%, var(--cj-teal) 100%) !important; border: none !important; color: #fff !important; font-weight: 600; border-radius: 10px !important; padding: 0.65rem 1rem !important; box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35); transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .cj-coupon-card__btn:hover { color: #fff !important; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4); }
    .cj-coupon-card__btn-outline { border-radius: 10px !important; border-color: #cbd5e1 !important; color: #64748b !important; }
    .cj-coupon-card__foot { font-size: 0.78rem; color: #94a3b8; line-height: 1.45; margin: 0; padding-top: 0.25rem; border-top: 1px solid #f1f5f9; margin-top: 1rem; padding-top: 1rem; }
</style>
@endonce


<div class="cj-coupon-card coupon-apply-section">
    <div class="cj-coupon-card__ribbon">
        <h2 class="cj-coupon-card__title">
            <i class="fas fa-ticket-alt" aria-hidden="true"></i>
            {{ __('Have a coupon code?') }}
        </h2>
        <p class="cj-coupon-card__subtitle">{{ $ribbonSubtitle }}</p>
    </div>
    <div class="cj-coupon-card__body">
        @if($includeActiveOffersList)
            @if($portalCoupons->isNotEmpty())
                <div class="cj-coupon-card__panel">
                    <div class="cj-coupon-card__panel-label">{{ __('Active offers') }}</div>
                    @foreach($portalCoupons as $pc)
                        <div class="cj-coupon-chip">
                            <span class="cj-coupon-chip__code">{{ $pc->code }}</span>
                            <span class="cj-coupon-chip__meta">
                                @if($pc->discount_type === 'percent')
                                    {{ number_format((float) $pc->discount_value, 0) }}% {{ __('off') }}
                                @else
                                    {{ __(':amount off', ['amount' => number_format((float) $pc->discount_value, 2)]) }}
                                @endif
                            </span>
                            @if($pc->package_for_scope)
                                <span class="cj-coupon-chip__badge">{{ $pc->package_for_scope }}</span>
                            @endif
                            @if(!empty($pc->admin_note))
                                <span class="cj-coupon-chip__meta w-100 small">{{ $pc->admin_note }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif($emptyOffersMessage !== '')
                <p class="cj-coupon-card__empty mb-3">{{ $emptyOffersMessage }}</p>
            @endif
        @endif

        @if($appliedCoupon)
            <div class="cj-coupon-card__applied">
                <span class="small text-muted d-block mb-1">{{ __('Applied at checkout') }}</span>
                <strong>{{ $appliedCoupon }}</strong>
                @if($appliedHelpText)
                    <p class="small text-muted mb-2 mt-2">{{ $appliedHelpText }}</p>
                @endif
                <form method="post" action="{{ route($clearRoute) }}" class="mt-2 mb-0">
                    @csrf
                    <button type="submit" class="btn btn-sm cj-coupon-card__btn-outline">{{ __('Remove code') }}</button>
                </form>
            </div>
        @else
            <div class="cj-coupon-card__panel" style="background:#fff;">
                <div class="cj-coupon-card__panel-label">{{ __('Enter your code') }}</div>
                <form method="post" action="{{ route($applyRoute) }}" class="row g-2 align-items-end">
                    @csrf
                    @if(!empty($couponApplyContext))
                        <input type="hidden" name="apply_context" value="{{ $couponApplyContext }}">
                    @endif
                    <div class="col-md-8">
                        <label class="form-label small text-muted mb-1 visually-hidden">{{ __('Coupon code') }}</label>
                        <input type="text" name="code" class="form-control" placeholder="{{ __('Type or paste code') }}" autocomplete="off">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn w-100 cj-coupon-card__btn">{{ __('Apply') }}</button>
                    </div>
                </form>
            </div>
        @endif

        <p class="cj-coupon-card__foot mb-0">{{ $footerNote }}</p>
    </div>
</div>
