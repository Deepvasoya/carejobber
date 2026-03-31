<?php

namespace App\Http\Controllers;

use App\PackageCoupon;
use App\Services\PackageCouponService;
use Illuminate\Http\Request;
use App\Models\ResumePromotionPackage;
use Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class ResumePromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showPackages()
    {
        $user = Auth::user();
        $packages = ResumePromotionPackage::active()->orderBy('duration_days')->get();

        $hasActivePromotion = $user->is_resume_promoted &&
                             $user->promotion_end_date &&
                             \Carbon\Carbon::parse($user->promotion_end_date)->isFuture();

        return view('user.resume_promotion_packages')
            ->with('packages', $packages)
            ->with('user', $user)
            ->with('hasActivePromotion', $hasActivePromotion);
    }

    public function createCheckout(Request $request, $packageId)
    {
        $package = ResumePromotionPackage::findOrFail($packageId);
        $user = Auth::user();

        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');

        if (empty($stripeSecret)) {
            flash(__('Stripe is not configured. Please contact administrator.'))->error();

            return redirect()->route('resume.promotion.packages');
        }

        $rawCoupon = session('resume_promotion_coupon_code');
        $hasCouponInput = PackageCoupon::normalizeCode((string) $rawCoupon) !== '';
        $couponSvc = app(PackageCouponService::class);
        $eval = $couponSvc->evaluateResumePromotionCheckout($rawCoupon, $package, (int) $user->id);

        if ($hasCouponInput && !$eval['ok']) {
            flash(PackageCouponService::humanMessage($eval['reason'] ?? 'default'))->error();

            return redirect()->route('resume.promotion.packages');
        }

        $appliedCouponId = null;
        $discountAmount = 0.0;
        $unitAmountCents = (int) round(((float) $package->price) * 100);
        if (!empty($eval['coupon']) && ($eval['discount'] ?? 0) > 0) {
            $appliedCouponId = $eval['coupon']->id;
            $discountAmount = (float) $eval['discount'];
            $unitAmountCents = (int) round(((float) $eval['total']) * 100);
        }

        Stripe::setApiKey($stripeSecret);

        $metadata = [
            'user_id' => (string) $user->id,
            'package_id' => (string) $package->id,
            'type' => 'resume_promotion',
        ];
        if ($appliedCouponId) {
            $metadata['package_coupon_id'] = (string) $appliedCouponId;
            $metadata['coupon_discount'] = (string) $discountAmount;
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($package->currency),
                        'product_data' => [
                            'name' => $package->name,
                            'description' => $discountAmount > 0
                                ? trim((string) $package->description . ' — ' . __('Coupon discount'))
                                : $package->description,
                        ],
                        'unit_amount' => $unitAmountCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('resume.promotion.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('resume.promotion.packages'),
                'client_reference_id' => (string) $user->id,
                'metadata' => $metadata,
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            \Log::error('Resume promotion Stripe checkout failed: ' . $e->getMessage());
            flash(__('Payment setup failed. Please try again.'))->error();

            return redirect()->route('resume.promotion.packages');
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            flash(__('Invalid payment session'))->error();

            return redirect()->route('my.profile');
        }

        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        Stripe::setApiKey($stripeSecret);

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                flash(__('Payment was not successful'))->error();

                return redirect()->route('resume.promotion.packages');
            }

            $user = Auth::user();
            $meta = $session->metadata;
            $packageId = isset($meta->package_id) ? (int) $meta->package_id : null;

            if (!$packageId) {
                flash(__('Invalid payment session'))->error();

                return redirect()->route('resume.promotion.packages');
            }

            $package = ResumePromotionPackage::findOrFail($packageId);

            if ((int) ($meta->user_id ?? 0) !== (int) $user->id) {
                flash(__('Invalid payment session'))->error();

                return redirect()->route('resume.promotion.packages');
            }

            $user->is_resume_promoted = 1;
            $user->promotion_start_date = now();
            $user->promotion_end_date = now()->addDays($package->duration_days);
            $user->save();

            $couponId = isset($meta->package_coupon_id) ? (int) $meta->package_coupon_id : null;
            $discount = isset($meta->coupon_discount) ? (float) $meta->coupon_discount : 0.0;
            if ($couponId && $discount > 0) {
                $coupon = \App\PackageCoupon::find($couponId);
                if ($coupon) {
                    $paid = isset($session->amount_total) ? $session->amount_total / 100 : null;
                    app(PackageCouponService::class)->recordRedemption(
                        $coupon,
                        null,
                        $discount,
                        null,
                        (int) $user->id,
                        $sessionId,
                        null,
                        $paid,
                        strtoupper((string) ($session->currency ?? $package->currency ?? 'CAD')),
                        $package->id
                    );
                }
            }

            flash(__('Your resume has been promoted successfully!'))->success();

            return redirect()->route('my.profile');
        } catch (\Exception $e) {
            \Log::error('Resume promotion success handling failed: ' . $e->getMessage());
            flash(__('Error verifying payment. Please contact support if you were charged.'))->error();

            return redirect()->route('resume.promotion.packages');
        }
    }
}
