<?php

namespace App\Http\Controllers;

use App\PackageCoupon;
use Illuminate\Http\Request;

class PackageCouponSessionController extends Controller
{
    public function applyEmployer(Request $request)
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
}
