<?php

namespace App\Http\Controllers;

use App\PackageCoupon;
use App\Services\PackageCouponService;
use Illuminate\Http\Request;

class PackageCouponSessionController extends Controller
{
    public function applyEmployer(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:64',
            'apply_context' => 'nullable|string|in:employer_job_posting,employer_cv_search',
        ]);

        $code = PackageCoupon::normalizeCode($request->input('code'));
        if ($code === '') {
            flash(__('Please enter a coupon code.'))->error();

            return redirect()->back();
        }

        $coupon = PackageCoupon::where('code', $code)->first();
        if (!$coupon) {
            flash(__('Invalid coupon code.'))->error();

            return redirect()->back();
        }
        if (!$coupon->is_active) {
            flash(__('This coupon is not active.'))->error();

            return redirect()->back();
        }
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            flash(__('This coupon is not valid yet.'))->error();

            return redirect()->back();
        }
        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            flash(__('This coupon has expired.'))->error();

            return redirect()->back();
        }

        $scope = $coupon->package_for_scope;
        if ($scope === 'resume_promotion') {
            flash(__('This coupon is for resume promotion only. Open “Promote Your Resume” in your candidate menu.'))->error();

            return redirect()->back();
        }

        $ctx = $request->input('apply_context');
        if ($ctx === 'employer_job_posting') {
            if (in_array($scope, ['cv_search', 'job_seeker', 'make_featured'], true)) {
                flash(__('This coupon does not apply to job posting packages. Open CV search packages if your code is for CV unlocks, or use an employer job-package coupon.'))->error();

                return redirect()->back();
            }
        }
        if ($ctx === 'employer_cv_search') {
            if (in_array($scope, ['employer', 'job_seeker', 'make_featured'], true)) {
                flash(__('This coupon does not apply to CV search packages. Job posting or job seeker codes cannot be used here.'))->error();

                return redirect()->back();
            }
        }

        session(['employer_package_coupon_code' => $code]);
        flash(__('Coupon saved. It will be applied at checkout when valid for your selected package.'))->success();

        return redirect()->back();
    }

    public function clearEmployer()
    {
        session()->forget('employer_package_coupon_code');
        flash(__('Coupon removed.'))->success();

        return redirect()->back();
    }

    public function applyJobSeeker(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:64',
        ]);

        $code = PackageCoupon::normalizeCode($request->input('code'));
        if ($code === '') {
            flash(__('Please enter a coupon code.'))->error();

            return redirect()->back();
        }

        $coupon = PackageCoupon::where('code', $code)->first();
        if (!$coupon || !$coupon->is_active) {
            flash(__('Invalid coupon code.'))->error();

            return redirect()->back();
        }
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            flash(__('This coupon is not valid yet.'))->error();

            return redirect()->back();
        }
        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            flash(__('This coupon has expired.'))->error();

            return redirect()->back();
        }

        $scope = $coupon->package_for_scope;
        if (in_array($scope, ['employer', 'cv_search', 'resume_promotion'], true)) {
            flash(__('This coupon is for employers or resume promotion. Use the right page: employer dashboard, or Promote Your Resume for resume promotion codes.'))->error();

            return redirect()->back();
        }

        session(['jobseeker_package_coupon_code' => $code]);
        flash(__('Coupon saved. It will be applied when you pay with Stripe if valid for your package.'))->success();

        return redirect()->back();
    }

    public function clearJobSeeker()
    {
        session()->forget('jobseeker_package_coupon_code');
        flash(__('Coupon removed.'))->success();

        return redirect()->back();
    }

    public function applyResumePromotion(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:64',
        ]);

        $code = PackageCoupon::normalizeCode($request->input('code'));
        if ($code === '') {
            flash(__('Please enter a coupon code.'))->error();

            return redirect()->back();
        }

        $coupon = PackageCoupon::where('code', $code)->first();
        if (!$coupon || !$coupon->is_active) {
            flash(__('Invalid coupon code.'))->error();

            return redirect()->back();
        }
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            flash(__('This coupon is not valid yet.'))->error();

            return redirect()->back();
        }
        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            flash(__('This coupon has expired.'))->error();

            return redirect()->back();
        }

        if (!app(PackageCouponService::class)->couponScopeAllowsResumePromotion($coupon->package_for_scope)) {
            flash(__('This coupon does not apply to resume promotion. Use “Any” or “Resume promotion” audience in admin, or apply job seeker / employer codes on the correct checkout page.'))->error();

            return redirect()->back();
        }

        session(['resume_promotion_coupon_code' => $code]);
        flash(__('Coupon saved. It will be applied when you pay for a promotion package.'))->success();

        return redirect()->back();
    }

    public function clearResumePromotion()
    {
        session()->forget('resume_promotion_coupon_code');
        flash(__('Coupon removed.'))->success();

        return redirect()->back();
    }
}
