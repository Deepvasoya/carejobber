<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Job;
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

        $pack = JobPromotionPricing::buildLineItems(
            ! empty($pending['promote_featured']),
            ! empty($pending['promote_urgent']),
            ! empty($pending['promote_highlighted'])
        );

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
                'promote_featured' => ! empty($pending['promote_featured']) ? '1' : '0',
                'promote_urgent' => ! empty($pending['promote_urgent']) ? '1' : '0',
                'promote_highlighted' => ! empty($pending['promote_highlighted']) ? '1' : '0',
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

        static::fulfillJobPromotions($sessionId, $meta, $record);

        Session::forget('pending_job_promotions');

        return redirect()->route('posted.jobs')->with('success', __('Listing upgrades are now active.'));
    }

    /**
     * Apply paid flags and mark DB session row complete (idempotent).
     */
    public static function fulfillJobPromotions(string $sessionId, array $metadata, ?StripeCheckoutSession $record = null): bool
    {
        if (($metadata['type'] ?? '') !== 'job_promotions') {
            return false;
        }

        $existing = StripeCheckoutSession::where('session_id', $sessionId)->first();
        if ($existing && $existing->status === 'completed') {
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

        if (($metadata['promote_featured'] ?? '0') === '1') {
            $job->is_featured = true;
        }
        if (($metadata['promote_urgent'] ?? '0') === '1') {
            $job->is_urgent = true;
        }
        if (($metadata['promote_highlighted'] ?? '0') === '1') {
            $job->is_highlighted = true;
        }

        $job->save();

        StripeCheckoutSession::where('session_id', $sessionId)->update(['status' => 'completed']);

        Log::info('[JobPromotions] Fulfilled', ['job_id' => $jobId, 'session_id' => $sessionId]);

        return true;
    }
}
