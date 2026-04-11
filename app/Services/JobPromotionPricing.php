<?php

namespace App\Services;

use App\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobPromotionPricing
{
    public static function config(): array
    {
        return [
            'currency' => strtolower(config('job_promotions.currency', 'cad')),
            'urgent_7_price' => (float) config('job_promotions.urgent_7_price', 19),
            'urgent_15_price' => (float) config('job_promotions.urgent_15_price', 30),
            'featured_15_price' => (float) config('job_promotions.featured_15_price', 25),
            'featured_30_price' => (float) config('job_promotions.featured_30_price', 40),
            'highlighted_price' => (float) config('job_promotions.highlighted_price', 20),
            'highlighted_days' => (int) config('job_promotions.highlighted_days', 30),
        ];
    }

    /**
     * @return array{urgent_days: int, featured_days: int, highlighted: int}
     */
    public static function selectionFromRequest(Request $request): array
    {
        $urgent = (int) $request->input('promote_urgent_days', 0);
        if (! in_array($urgent, [0, 7, 15], true)) {
            $urgent = 0;
        }

        $featured = (int) $request->input('promote_featured_days', 0);
        if (! in_array($featured, [0, 15, 30], true)) {
            $featured = 0;
        }

        $highlighted = (int) $request->input('promote_highlighted', 0);
        if (! in_array($highlighted, [0, 1], true)) {
            $highlighted = 0;
        }

        return [
            'urgent_days' => $urgent,
            'featured_days' => $featured,
            'highlighted' => $highlighted,
        ];
    }

    public static function capPromotionEnd(?Carbon $displayEnd, Carbon $candidateEnd): Carbon
    {
        if ($displayEnd === null) {
            return $candidateEnd->copy();
        }

        return $candidateEnd->greaterThan($displayEnd) ? $displayEnd->copy() : $candidateEnd->copy();
    }

    /**
     * Apply employer selection when no Stripe checkout is required (free tiers or already paid elsewhere).
     */
    public static function reconcilePromotionsAfterSave(Job $job, Request $request, array $pending): void
    {
        $sel = self::selectionFromRequest($request);
        $cap = $job->display_end_date ? Carbon::parse($job->display_end_date) : null;

        if ($sel['urgent_days'] === 0) {
            $job->is_urgent = false;
            $job->promotion_urgent_until = null;
        } elseif (! empty($pending['pay_urgent'])) {
            $job->is_urgent = false;
            $job->promotion_urgent_until = null;
        } elseif (! $job->isPromotionUrgentActive()) {
            $job->is_urgent = true;
            $job->promotion_urgent_until = self::capPromotionEnd($cap, Carbon::now()->addDays($sel['urgent_days']));
        }

        if ($sel['featured_days'] === 0) {
            $job->is_featured = false;
            $job->promotion_featured_until = null;
        } elseif (! empty($pending['pay_featured'])) {
            $job->is_featured = false;
            $job->promotion_featured_until = null;
        } elseif (! $job->isPromotionFeaturedActive()) {
            $job->is_featured = true;
            $job->promotion_featured_until = self::capPromotionEnd($cap, Carbon::now()->addDays($sel['featured_days']));
        }

        $hlDays = self::config()['highlighted_days'];
        if ($sel['highlighted'] !== 1) {
            $job->is_highlighted = false;
            $job->promotion_highlighted_until = null;
        } elseif (! empty($pending['pay_highlighted'])) {
            $job->is_highlighted = false;
            $job->promotion_highlighted_until = null;
        } elseif (! $job->isPromotionHighlightedActive()) {
            $job->is_highlighted = true;
            $job->promotion_highlighted_until = self::capPromotionEnd($cap, Carbon::now()->addDays($hlDays));
        }

        $job->save();
    }

    /**
     * After successful Stripe payment.
     *
     * @param  array<string, mixed>  $metadata
     */
    public static function fulfillFromStripeMetadata(Job $job, array $metadata): void
    {
        $cap = $job->display_end_date ? Carbon::parse($job->display_end_date) : null;
        $c = self::config();

        $uDays = (int) ($metadata['promote_urgent_days'] ?? 0);
        if (($metadata['promote_urgent'] ?? '0') === '1') {
            if (! in_array($uDays, [7, 15], true)) {
                $uDays = 7;
            }
            $job->is_urgent = true;
            $job->promotion_urgent_until = self::capPromotionEnd($cap, Carbon::now()->addDays($uDays));
        }

        $fDays = (int) ($metadata['promote_featured_days'] ?? 0);
        if (($metadata['promote_featured'] ?? '0') === '1') {
            if (! in_array($fDays, [15, 30], true)) {
                $fDays = 15;
            }
            $job->is_featured = true;
            $job->promotion_featured_until = self::capPromotionEnd($cap, Carbon::now()->addDays($fDays));
        }

        if (($metadata['promote_highlighted'] ?? '0') === '1') {
            $job->is_highlighted = true;
            $job->promotion_highlighted_until = self::capPromotionEnd($cap, Carbon::now()->addDays($c['highlighted_days']));
        }

        $job->save();
    }

    /**
     * @return array{
     *   pay_urgent: bool,
     *   pay_featured: bool,
     *   pay_highlighted: bool,
     *   promote_urgent_days: int,
     *   promote_featured_days: int,
     *   promote_highlighted: int,
     *   line_items: array,
     *   total_cents: int,
     *   currency: string
     * }
     */
    public static function buildPendingPack(
        Request $request,
        ?Job $existingJob,
        bool $isNewJob
    ): array {
        $sel = self::selectionFromRequest($request);
        $c = self::config();
        $currency = $c['currency'];
        $lineItems = [];
        $totalCents = 0;
        $payUrgent = false;
        $payFeatured = false;
        $payHighlighted = false;

        $canBuyUrgent = $sel['urgent_days'] > 0
            && ($isNewJob || ! $existingJob || ! $existingJob->isPromotionUrgentActive());
        if ($canBuyUrgent) {
            $price = $sel['urgent_days'] === 15 ? $c['urgent_15_price'] : $c['urgent_7_price'];
            if ($price > 0) {
                $payUrgent = true;
                $cents = (int) round($price * 100);
                $lineItems[] = [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => __('Urgent job listing (:days days)', ['days' => $sel['urgent_days']]),
                            'description' => __('Top placement in job search results'),
                        ],
                        'unit_amount' => $cents,
                    ],
                ];
                $totalCents += $cents;
            }
        }

        $canBuyFeatured = $sel['featured_days'] > 0
            && ($isNewJob || ! $existingJob || ! $existingJob->isPromotionFeaturedActive());
        if ($canBuyFeatured) {
            $price = $sel['featured_days'] === 30 ? $c['featured_30_price'] : $c['featured_15_price'];
            if ($price > 0) {
                $payFeatured = true;
                $cents = (int) round($price * 100);
                $lineItems[] = [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => __('Featured job listing (:days days)', ['days' => $sel['featured_days']]),
                            'description' => __('Featured placement in search (after urgent jobs)'),
                        ],
                        'unit_amount' => $cents,
                    ],
                ];
                $totalCents += $cents;
            }
        }

        $canBuyHighlighted = $sel['highlighted'] === 1
            && ($isNewJob || ! $existingJob || ! $existingJob->isPromotionHighlightedActive());
        if ($canBuyHighlighted && $c['highlighted_price'] > 0) {
            $payHighlighted = true;
            $cents = (int) round($c['highlighted_price'] * 100);
            $lineItems[] = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => __('Highlighted job listing (:days days)', ['days' => $c['highlighted_days']]),
                        'description' => __('Highlighted background on job cards'),
                    ],
                    'unit_amount' => $cents,
                ],
            ];
            $totalCents += $cents;
        }

        return [
            'pay_urgent' => $payUrgent,
            'pay_featured' => $payFeatured,
            'pay_highlighted' => $payHighlighted,
            'promote_urgent_days' => $sel['urgent_days'],
            'promote_featured_days' => $sel['featured_days'],
            'promote_highlighted' => $sel['highlighted'],
            'line_items' => $lineItems,
            'total_cents' => $totalCents,
            'currency' => $currency,
        ];
    }

    public static function pendingForNewJob(Request $request): array
    {
        return self::buildPendingPack($request, null, true);
    }

    public static function pendingForUpdate(Request $request, Job $job): array
    {
        return self::buildPendingPack($request, $job, false);
    }

    /**
     * Normalize flags from session / serialized storage (bool, 0/1, "0"/"1").
     *
     * @return array{pay_urgent: bool, pay_featured: bool, pay_highlighted: bool}
     */
    public static function paymentFlagsFromPending(array $pending): array
    {
        $toBool = static function ($v): bool {
            if ($v === true || $v === 1 || $v === '1') {
                return true;
            }
            if ($v === false || $v === 0 || $v === '0' || $v === null || $v === '') {
                return false;
            }
            if (is_string($v)) {
                $s = strtolower(trim($v));
                if (in_array($s, ['false', 'no', 'off', '0'], true)) {
                    return false;
                }

                return in_array($s, ['true', 'yes', 'on', '1'], true);
            }

            return (bool) $v;
        };

        return [
            'pay_urgent' => $toBool($pending['pay_urgent'] ?? $pending['promote_urgent'] ?? false),
            'pay_featured' => $toBool($pending['pay_featured'] ?? $pending['promote_featured'] ?? false),
            'pay_highlighted' => $toBool($pending['pay_highlighted'] ?? false),
        ];
    }

    /**
     * @deprecated Use paymentFlagsFromPending()
     *
     * @return array{promote_featured: bool, promote_urgent: bool, promote_highlighted: bool}
     */
    public static function promotionBoolsFromPending(array $pending): array
    {
        $b = self::paymentFlagsFromPending($pending);

        return [
            'promote_featured' => $b['pay_featured'],
            'promote_urgent' => $b['pay_urgent'],
            'promote_highlighted' => $b['pay_highlighted'],
        ];
    }

    public static function packFromPending(array $pending): array
    {
        $b = self::paymentFlagsFromPending($pending);
        $uDays = (int) ($pending['promote_urgent_days'] ?? 0);
        if ($uDays === 0 && $b['pay_urgent']) {
            $uDays = 7;
        }
        $fDays = (int) ($pending['promote_featured_days'] ?? 0);
        if ($fDays === 0 && $b['pay_featured']) {
            $fDays = 15;
        }
        $h = (int) ($pending['promote_highlighted'] ?? 0);

        return self::buildLineItemsFromPaidSelection(
            $b['pay_urgent'],
            $uDays,
            $b['pay_featured'],
            $fDays,
            $b['pay_highlighted'],
            $h === 1
        );
    }

    /**
     * @return array{
     *   promote_featured: bool,
     *   promote_urgent: bool,
     *   promote_highlighted: bool,
     *   line_items: array,
     *   total_cents: int,
     *   currency: string
     * }
     */
    public static function buildLineItemsFromPaidSelection(
        bool $chargeUrgent,
        int $urgentDays,
        bool $chargeFeatured,
        int $featuredDays,
        bool $chargeHighlighted,
        bool $highlightedSelected
    ): array {
        $c = self::config();
        $currency = $c['currency'];
        $lineItems = [];
        $totalCents = 0;

        if ($chargeUrgent && in_array($urgentDays, [7, 15], true)) {
            $price = $urgentDays === 15 ? $c['urgent_15_price'] : $c['urgent_7_price'];
            if ($price > 0) {
                $cents = (int) round($price * 100);
                $lineItems[] = [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => __('Urgent job listing (:days days)', ['days' => $urgentDays]),
                            'description' => __('Top placement in job search results'),
                        ],
                        'unit_amount' => $cents,
                    ],
                ];
                $totalCents += $cents;
            }
        }

        if ($chargeFeatured && in_array($featuredDays, [15, 30], true)) {
            $price = $featuredDays === 30 ? $c['featured_30_price'] : $c['featured_15_price'];
            if ($price > 0) {
                $cents = (int) round($price * 100);
                $lineItems[] = [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => __('Featured job listing (:days days)', ['days' => $featuredDays]),
                            'description' => __('Featured placement in search (after urgent jobs)'),
                        ],
                        'unit_amount' => $cents,
                    ],
                ];
                $totalCents += $cents;
            }
        }

        if ($chargeHighlighted && $highlightedSelected && $c['highlighted_price'] > 0) {
            $cents = (int) round($c['highlighted_price'] * 100);
            $lineItems[] = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => __('Highlighted job listing (:days days)', ['days' => $c['highlighted_days']]),
                        'description' => __('Highlighted background on job cards'),
                    ],
                    'unit_amount' => $cents,
                ],
            ];
            $totalCents += $cents;
        }

        return [
            'promote_featured' => $chargeFeatured,
            'promote_urgent' => $chargeUrgent,
            'promote_highlighted' => $chargeHighlighted,
            'line_items' => $lineItems,
            'total_cents' => $totalCents,
            'currency' => $currency,
        ];
    }
}
