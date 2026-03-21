<?php

namespace App\Services;

use App\Job;
use Illuminate\Http\Request;

class JobPromotionPricing
{
    public static function config(): array
    {
        return [
            'currency' => strtolower(config('job_promotions.currency', 'cad')),
            'featured' => (float) config('job_promotions.featured_price', 10),
            'urgent' => (float) config('job_promotions.urgent_price', 15),
            'highlighted' => (float) config('job_promotions.highlighted_price', 5),
        ];
    }

    /**
     * Build Stripe line items and totals for the promotions being purchased.
     *
     * @return array{
     *   promote_featured: bool,
     *   promote_urgent: bool,
     *   promote_highlighted: bool,
     *   line_items: array,
     *   total_cents: int,
     *   currency: string
     * }
     */
    public static function buildLineItems(bool $chargeFeatured, bool $chargeUrgent, bool $chargeHighlighted): array
    {
        $c = self::config();
        $currency = $c['currency'];
        $lineItems = [];
        $totalCents = 0;

        if ($chargeFeatured && $c['featured'] > 0) {
            $cents = (int) round($c['featured'] * 100);
            $lineItems[] = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => __('Featured job listing'),
                        'description' => __('Featured placement in search (after urgent jobs)'),
                    ],
                    'unit_amount' => $cents,
                ],
            ];
            $totalCents += $cents;
        }

        if ($chargeUrgent && $c['urgent'] > 0) {
            $cents = (int) round($c['urgent'] * 100);
            $lineItems[] = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => __('Urgent job listing'),
                        'description' => __('Top placement in job search results'),
                    ],
                    'unit_amount' => $cents,
                ],
            ];
            $totalCents += $cents;
        }

        if ($chargeHighlighted && $c['highlighted'] > 0) {
            $cents = (int) round($c['highlighted'] * 100);
            $lineItems[] = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => __('Highlighted job listing'),
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

    public static function pendingForNewJob(Request $request): array
    {
        return self::buildLineItems(
            $request->boolean('promote_featured'),
            $request->boolean('promote_urgent'),
            $request->boolean('promote_highlighted')
        );
    }

    public static function pendingForUpdate(Request $request, Job $job): array
    {
        return self::buildLineItems(
            $request->boolean('promote_featured') && ! $job->is_featured,
            $request->boolean('promote_urgent') && ! $job->is_urgent,
            $request->boolean('promote_highlighted') && ! $job->is_highlighted
        );
    }
}
