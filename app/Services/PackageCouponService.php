<?php

namespace App\Services;

use App\Package;
use App\PackageCoupon;
use App\PackageCouponRedemption;
use App\StripeCheckoutSession;

class PackageCouponService
{
    /**
     * @return array{ok:bool, reason:?string, coupon:?PackageCoupon, subtotal:float, discount:float, total:float}
     */
    public function evaluateCheckout(
        ?string $rawCode,
        Package $package,
        ?int $companyId,
        ?int $userId
    ): array {
        $base = [
            'ok' => false,
            'reason' => null,
            'coupon' => null,
            'subtotal' => (float) $package->package_price,
            'discount' => 0.0,
            'total' => (float) $package->package_price,
        ];

        $code = PackageCoupon::normalizeCode($rawCode);
        if ($code === '') {
            $base['ok'] = true;
            $base['reason'] = 'no_code';

            return $base;
        }

        $coupon = PackageCoupon::where('code', $code)->first();
        if (!$coupon) {
            $base['reason'] = 'invalid_code';

            return $base;
        }

        if (!$coupon->is_active) {
            $base['reason'] = 'inactive';

            return $base;
        }

        $now = now();
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            $base['reason'] = 'not_started';

            return $base;
        }
        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            $base['reason'] = 'expired';

            return $base;
        }

        if ($base['subtotal'] <= 0) {
            $base['reason'] = 'free_package';

            return $base;
        }

        if (!$this->couponScopeAllowsPackage($coupon->package_for_scope, $package)) {
            $base['reason'] = 'wrong_audience';

            return $base;
        }

        $ids = $coupon->package_ids;
        if (is_array($ids) && count($ids) > 0) {
            $ids = array_map('intval', $ids);
            if (!in_array((int) $package->id, $ids, true)) {
                $base['reason'] = 'package_not_allowed';

                return $base;
            }
        }

        if ($coupon->min_package_price !== null && $base['subtotal'] < (float) $coupon->min_package_price) {
            $base['reason'] = 'below_minimum';

            return $base;
        }

        $isSubscription = $package->package_for === 'employer'
            && ($package->type ?? null) === Package::TYPE_MONTHLY_RECURRING;

        if ($isSubscription) {
            if (!$coupon->allow_subscription_packages) {
                $base['reason'] = 'subscriptions_not_allowed';

                return $base;
            }
            if (!empty($package->stripe_price_id)) {
                $base['reason'] = 'stripe_price_fixed';

                return $base;
            }
        }

        if ($coupon->usage_limit_total !== null) {
            $used = PackageCouponRedemption::where('package_coupon_id', $coupon->id)->count();
            if ($used >= (int) $coupon->usage_limit_total) {
                $base['reason'] = 'usage_limit_total';

                return $base;
            }
        }

        if ($coupon->usage_limit_per_buyer !== null) {
            $limit = (int) $coupon->usage_limit_per_buyer;
            if ($companyId) {
                $n = PackageCouponRedemption::where('package_coupon_id', $coupon->id)
                    ->where('company_id', $companyId)
                    ->count();
                if ($n >= $limit) {
                    $base['reason'] = 'usage_limit_buyer';

                    return $base;
                }
            }
            if ($userId) {
                $n = PackageCouponRedemption::where('package_coupon_id', $coupon->id)
                    ->where('user_id', $userId)
                    ->count();
                if ($n >= $limit) {
                    $base['reason'] = 'usage_limit_buyer';

                    return $base;
                }
            }
        }

        $discount = $this->computeDiscount($coupon, $base['subtotal']);
        $total = round(max(0, $base['subtotal'] - $discount), 2);

        if ($total < 0.5) {
            $base['reason'] = 'amount_below_stripe_minimum';

            return $base;
        }

        $base['ok'] = true;
        $base['reason'] = 'applied';
        $base['coupon'] = $coupon;
        $base['discount'] = $discount;
        $base['total'] = $total;

        return $base;
    }

    /**
     * Whether a coupon's audience scope allows this package (matches admin "Restrict to package audience").
     * Empty scope = any package type. Job seeker–scoped codes also apply to "make featured" profile packages.
     */
    public function couponScopeAllowsPackage(?string $scope, Package $package): bool
    {
        $s = $scope !== null ? trim((string) $scope) : '';
        if ($s === '') {
            return true;
        }

        $pf = (string) $package->package_for;
        if ($s === $pf) {
            return true;
        }

        if ($s === 'job_seeker' && $pf === 'make_featured') {
            return true;
        }

        return false;
    }

    public function computeDiscount(PackageCoupon $coupon, float $subtotal): float
    {
        $subtotal = max(0, $subtotal);
        if ($subtotal <= 0) {
            return 0;
        }

        if ($coupon->discount_type === 'fixed') {
            $d = min((float) $coupon->discount_value, $subtotal);
        } else {
            $pct = min(100, max(0, (float) $coupon->discount_value));
            $d = round($subtotal * ($pct / 100), 2);
            if ($coupon->max_discount_amount !== null) {
                $d = min($d, (float) $coupon->max_discount_amount);
            }
            $d = min($d, $subtotal);
        }

        return round(min($d, $subtotal), 2);
    }

    /**
     * After successful employer Stripe Checkout (browser or webhook).
     */
    public function redeemEmployerStripeCheckout(
        StripeCheckoutSession $record,
        Package $package,
        ?int $amountTotalCents,
        string $currency
    ): void {
        if (!$record->package_coupon_id || (float) ($record->coupon_discount_amount ?? 0) <= 0) {
            return;
        }
        $coupon = PackageCoupon::find($record->package_coupon_id);
        if (!$coupon) {
            return;
        }
        $paid = $amountTotalCents !== null ? $amountTotalCents / 100 : null;
        $this->recordRedemption(
            $coupon,
            $package,
            (float) $record->coupon_discount_amount,
            (int) $record->company_id,
            null,
            $record->session_id,
            null,
            $paid,
            $currency
        );
    }

    public function recordRedemption(
        PackageCoupon $coupon,
        Package $package,
        float $discountAmount,
        ?int $companyId,
        ?int $userId,
        ?string $stripeCheckoutSessionId,
        ?string $stripeChargeId,
        ?float $amountPaid,
        string $currency = 'USD'
    ): void {
        if ($discountAmount <= 0) {
            return;
        }

        if ($stripeCheckoutSessionId) {
            $exists = PackageCouponRedemption::where('stripe_checkout_session_id', $stripeCheckoutSessionId)->exists();
            if ($exists) {
                return;
            }
        }
        if ($stripeChargeId) {
            $exists = PackageCouponRedemption::where('stripe_charge_id', $stripeChargeId)->exists();
            if ($exists) {
                return;
            }
        }

        PackageCouponRedemption::create([
            'package_coupon_id' => $coupon->id,
            'package_id' => $package->id,
            'company_id' => $companyId,
            'user_id' => $userId,
            'discount_amount' => $discountAmount,
            'amount_paid' => $amountPaid,
            'currency' => strtoupper(substr($currency, 0, 8)),
            'stripe_checkout_session_id' => $stripeCheckoutSessionId,
            'stripe_charge_id' => $stripeChargeId,
            'created_at' => now(),
        ]);
    }

    public static function humanMessage(string $reason): string
    {
        return match ($reason) {
            'invalid_code' => __('Invalid coupon code.'),
            'inactive' => __('This coupon is not active.'),
            'not_started' => __('This coupon is not valid yet.'),
            'expired' => __('This coupon has expired.'),
            'free_package' => __('Coupons cannot be applied to free packages.'),
            'wrong_audience' => __('This coupon does not apply to this type of package.'),
            'package_not_allowed' => __('This coupon does not apply to the selected package.'),
            'below_minimum' => __('The package price is below the minimum required for this coupon.'),
            'subscriptions_not_allowed' => __('This coupon cannot be used for subscription packages.'),
            'stripe_price_fixed' => __('This coupon cannot be used for this subscription (fixed Stripe price). Use a package billed with a custom price or create a promotion in Stripe.'),
            'usage_limit_total' => __('This coupon has reached its maximum number of uses.'),
            'usage_limit_buyer' => __('You have already used this coupon the maximum number of times.'),
            'amount_below_stripe_minimum' => __('This discount would make the total too low for card payment (minimum about :amount).', ['amount' => '$0.50']),
            default => __('This coupon cannot be applied.'),
        };
    }
}
