<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Job;
use App\PaymentHistory;
use App\Services\JobPromotionPricing;
use App\StripeCheckoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class JobPromotionCheckoutController extends Controller
{
    protected static function getStripeSecret(): ?string
    {
        $secret = config('services.stripe.secret') ?: config('stripe.stripe_secret');

        return $secret ? (string) $secret : null;
    }

    /**
     * Redirect employer to Stripe Checkout for pending job listing upgrades (session set after post/update job).
     */
    public function checkout()
    {
        $company = auth()->guard('company')->user();
        $pending = Session::get('pending_job_promotions');

        if (! is_array($pending) || empty($pending['job_id'])) {
            return redirect()->route('posted.jobs')->with('error', __('No pending listing upgrades.'));
        }

        $job = Job::where('id', (int) $pending['job_id'])->where('company_id', $company->id)->first();
        if (! $job) {
            Session::forget('pending_job_promotions');

            return redirect()->route('posted.jobs')->with('error', __('Job not found.'));
        }

        $b = JobPromotionPricing::paymentFlagsFromPending($pending);
        $pack = JobPromotionPricing::packFromPending($pending);

        if (isset($pending['total_cents']) && (int) $pending['total_cents'] !== (int) $pack['total_cents']) {
            Log::warning('[JobPromotions] Session total_cents does not match recomputed line items', [
                'job_id' => $job->id,
                'session_total_cents' => $pending['total_cents'],
                'recomputed_total_cents' => $pack['total_cents'],
                'flags' => $b,
            ]);
        }

        if ($pack['total_cents'] <= 0 || count($pack['line_items']) === 0) {
            Session::forget('pending_job_promotions');

            return redirect()->route('posted.jobs')->with('info', __('Nothing to pay for.'));
        }

        $secret = static::getStripeSecret();
        if (! $secret) {
            Log::error('[JobPromotions] Stripe not configured');

            return redirect()->route('posted.jobs')->with('error', __('Payment system not configured.'));
        }

        Stripe::setApiKey($secret);

        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => $pack['line_items'],
            'mode' => 'payment',
            'success_url' => route('job.promotions.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('posted.jobs'),
            'client_reference_id' => (string) $company->id,
            'metadata' => [
                'type' => 'job_promotions',
                'company_id' => (string) $company->id,
                'job_id' => (string) $job->id,
                'promote_featured' => $b['pay_featured'] ? '1' : '0',
                'promote_urgent' => $b['pay_urgent'] ? '1' : '0',
                'promote_highlighted' => $b['pay_highlighted'] ? '1' : '0',
                'promote_urgent_days' => (string) ($pending['promote_urgent_days'] ?? '0'),
                'promote_featured_days' => (string) ($pending['promote_featured_days'] ?? '0'),
            ],
            'customer_email' => $company->email,
        ];

        try {
            $session = StripeSession::create($sessionParams);
        } catch (\Exception $e) {
            Log::error('[JobPromotions] Stripe session failed', [
                'company_id' => $company->id,
                'job_id' => $job->id,
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('posted.jobs')->with('error', __('Payment setup failed. Please try again.'));
        }

        StripeCheckoutSession::create([
            'session_id' => $session->id,
            'company_id' => $company->id,
            'package_id' => 0,
            'job_id' => $job->id,
            'country_code' => null,
            'status' => 'pending',
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Public success URL (same pattern as resume unlock).
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (! $sessionId) {
            return redirect()->route('company.login')->with('error', __('Invalid session.'));
        }

        $record = StripeCheckoutSession::where('session_id', $sessionId)
            ->where('status', 'pending')
            ->whereNotNull('job_id')
            ->first();

        if (! $record) {
            return redirect()->route('posted.jobs')->with('error', __('Session already processed or invalid.'));
        }

        $secret = static::getStripeSecret();
        if (! $secret) {
            return redirect()->route('posted.jobs')->with('error', __('Stripe not configured.'));
        }

        try {
            Stripe::setApiKey($secret);
            $session = StripeSession::retrieve($sessionId);
        } catch (\Exception $e) {
            Log::error('[JobPromotions] Retrieve failed', ['session_id' => $sessionId, 'message' => $e->getMessage()]);

            return redirect()->route('posted.jobs')->with('error', __('Could not verify payment.'));
        }

        if ($session->payment_status !== 'paid' && $session->status !== 'complete') {
            return redirect()->route('posted.jobs')->with('error', __('Payment not completed.'));
        }

        $meta = $session->metadata ? $session->metadata->toArray() : [];

        if (($meta['type'] ?? '') !== 'job_promotions') {
            return redirect()->route('posted.jobs')->with('error', __('Invalid payment data.'));
        }

        static::fulfillJobPromotions(
            $sessionId,
            $meta,
            $record,
            isset($session->amount_total) ? (int) $session->amount_total : null
        );

        Session::forget('pending_job_promotions');

        return redirect()->route('posted.jobs')->with('success', __('Listing upgrades are now active.'));
    }

    /**
     * Apply paid flags and mark DB session row complete (idempotent).
     */
    public static function fulfillJobPromotions(
        string $sessionId,
        array $metadata,
        ?StripeCheckoutSession $record = null,
        ?int $amountTotalCents = null
    ): bool {
        if (($metadata['type'] ?? '') !== 'job_promotions') {
            return false;
        }

        $existing = StripeCheckoutSession::where('session_id', $sessionId)->first();
        if ($existing && $existing->status === 'completed'
            && PaymentHistory::where('transaction_id', $sessionId)->exists()) {
            return true;
        }

        $companyId = (int) ($metadata['company_id'] ?? 0);
        $jobId = (int) ($metadata['job_id'] ?? 0);

        if (! $companyId || ! $jobId) {
            Log::error('[JobPromotions] Missing ids', ['session_id' => $sessionId, 'metadata' => $metadata]);

            return false;
        }

        $job = Job::where('id', $jobId)->where('company_id', $companyId)->first();
        if (! $job) {
            Log::error('[JobPromotions] Job not found', compact('jobId', 'companyId'));

            return false;
        }

        JobPromotionPricing::fulfillFromStripeMetadata($job, $metadata);

        $price = $amountTotalCents !== null ? round($amountTotalCents / 100, 2) : 0.0;
        $labelParts = [];
        if (($metadata['promote_featured'] ?? '0') === '1') {
            $days = (int) ($metadata['promote_featured_days'] ?? 0);
            $labelParts[] = $days > 0 ? __('Featured (:days d)', ['days' => $days]) : __('Featured');
        }
        if (($metadata['promote_urgent'] ?? '0') === '1') {
            $days = (int) ($metadata['promote_urgent_days'] ?? 0);
            $labelParts[] = $days > 0 ? __('Urgent (:days d)', ['days' => $days]) : __('Urgent');
        }
        if (($metadata['promote_highlighted'] ?? '0') === '1') {
            $labelParts[] = __('Highlighted');
        }
        $title = __('Job listing promotion');
        if (! empty($labelParts)) {
            $title .= ' ('.implode(', ', $labelParts).')';
        }
        $title .= ' — '.$job->title;

        $now = now();
        if (! PaymentHistory::where('transaction_id', $sessionId)->exists()) {
            try {
                PaymentHistory::create([
                    'company_id' => $companyId,
                    'user_id' => null,
                    'user_type' => 'company',
                    'package_id' => 0,
                    'package_type' => 'job',
                    'package_title' => $title,
                    'package_price' => $price,
                    'payment_method' => 'Stripe',
                    'assigned_by' => null,
                    'transaction_id' => $sessionId,
                    'package_start_date' => $now,
                    'package_end_date' => $now,
                    'jobs_quota' => 0,
                    'cvs_quota' => 0,
                    'payment_status' => 'completed',
                ]);
            } catch (\Throwable $e) {
                Log::error('[JobPromotions] payment_history insert failed', [
                    'session_id' => $sessionId,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        StripeCheckoutSession::where('session_id', $sessionId)->update(['status' => 'completed']);

        Log::info('[JobPromotions] Fulfilled', ['job_id' => $jobId, 'session_id' => $sessionId]);

        return true;
    }
}
