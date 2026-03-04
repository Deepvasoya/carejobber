<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ResumeUnlock;
use App\User;
use App\StripeCheckoutSession;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class ResumeUnlockController extends Controller
{
    protected static function getStripeSecret(): ?string
    {
        $secret = config('services.stripe.secret') ?: config('stripe.stripe_secret');
        return $secret ? (string) $secret : null;
    }

    /**
     * Show unlock options page with pricing cards.
     */
    public function showUnlockPage(int $userId)
    {
        $company = \Auth::guard('company')->user();
        $user = User::findOrFail($userId);

        if (!$user->is_active || $user->verified != 1) {
            return redirect()->back()->with('error', __('This profile is not available.'));
        }

        if (ResumeUnlock::isUnlockedBy($userId, $company->id)) {
            return redirect()->route('user.profile', $userId)
                ->with('info', __('You have already unlocked this profile.'));
        }

        return view('company.resume_unlock_page', compact('user'));
    }

    /**
     * Create Stripe Checkout session for one-time resume unlock payment.
     */
    public function createCheckout(Request $request, int $userId)
    {
        $company = \Auth::guard('company')->user();
        $user = User::findOrFail($userId);

        if (!$user->is_active || $user->verified != 1) {
            return redirect()->back()->with('error', __('This profile is not available.'));
        }

        if (ResumeUnlock::isUnlockedBy($userId, $company->id)) {
            return redirect()->route('applicant.profile', ['application_id' => $request->query('application_id', 0)])
                ->with('info', __('You have already unlocked this profile.'));
        }

        $unlockPrice = (float) config('app.resume_unlock_price', 10.00);
        $currency = strtolower(config('app.resume_unlock_currency', 'cad'));

        $secret = static::getStripeSecret();
        if (!$secret) {
            \Log::error('[ResumeUnlock] Stripe not configured');
            return redirect()->back()->with('error', __('Payment system not configured.'));
        }
        Stripe::setApiKey($secret);

        $productName = __('Unlock Resume') . ': ' . ($user->getName() ?: __('Candidate'));
        $successUrl = route('resume.unlock.success') . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $request->query('return_url') ?: route('job.seeker.list');

        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $productName,
                        'description' => __('One-time unlock to view full resume details'),
                    ],
                    'unit_amount' => (int) round($unlockPrice * 100),
                ],
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $company->id,
            'metadata' => [
                'type' => 'resume_unlock',
                'company_id' => (string) $company->id,
                'user_id' => (string) $userId,
            ],
            'customer_email' => $company->email,
        ];

        try {
            $session = StripeSession::create($sessionParams);
            \Log::info('[ResumeUnlock] Stripe session created', [
                'session_id' => $session->id,
                'company_id' => $company->id,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error('[ResumeUnlock] Stripe session create failed', [
                'company_id' => $company->id,
                'user_id' => $userId,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', __('Payment setup failed. Please try again.'));
        }

        StripeCheckoutSession::create([
            'session_id' => $session->id,
            'company_id' => $company->id,
            'package_id' => null,
            'country_code' => null,
            'status' => 'pending',
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Success callback after Stripe payment for resume unlock.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        \Log::info('[ResumeUnlock] Success URL hit', [
            'session_id' => $sessionId,
            'company_authenticated' => \Auth::guard('company')->check(),
        ]);

        if (!$sessionId) {
            \Log::warning('[ResumeUnlock] Missing session_id');
            return redirect()->route('company.login')->with('error', __('Invalid session.'));
        }

        $record = StripeCheckoutSession::where('session_id', $sessionId)
            ->where('status', 'pending')
            ->whereNull('package_id')
            ->first();

        if (!$record) {
            \Log::warning('[ResumeUnlock] No pending session', ['session_id' => $sessionId]);
            return redirect()->route('company.login')->with('error', __('Session already processed or invalid.'));
        }

        $secret = static::getStripeSecret();
        if (!$secret) {
            return redirect()->route('company.login')->with('error', __('Stripe not configured.'));
        }

        try {
            Stripe::setApiKey($secret);
            $session = StripeSession::retrieve($sessionId);
            \Log::info('[ResumeUnlock] Stripe session retrieved', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status ?? null,
                'metadata' => $session->metadata ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('[ResumeUnlock] Stripe retrieve failed', [
                'session_id' => $sessionId,
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('company.login')->with('error', __('Could not verify payment.'));
        }

        if ($session->payment_status !== 'paid' && $session->status !== 'complete') {
            \Log::warning('[ResumeUnlock] Payment not complete', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status ?? null,
            ]);
            return redirect()->route('company.login')->with('error', __('Payment not completed.'));
        }

        $metadata = $session->metadata ?? null;
        if (!$metadata || !isset($metadata['company_id'], $metadata['user_id'])) {
            \Log::error('[ResumeUnlock] Missing metadata', ['session_id' => $sessionId]);
            return redirect()->route('company.login')->with('error', __('Invalid payment data.'));
        }

        $companyId = (int) $metadata['company_id'];
        $userId = (int) $metadata['user_id'];

        if (ResumeUnlock::isUnlockedBy($userId, $companyId)) {
            \Log::info('[ResumeUnlock] Already unlocked', [
                'company_id' => $companyId,
                'user_id' => $userId,
            ]);
            $record->update(['status' => 'completed']);
            return redirect()->route('applicant.profile', ['application_id' => 0])
                ->with('info', __('Resume already unlocked.'));
        }

        try {
            ResumeUnlock::create([
                'company_id' => $companyId,
                'user_id' => $userId,
                'paid_amount' => $session->amount_total / 100,
                'currency' => strtoupper($session->currency ?? 'CAD'),
                'stripe_session_id' => $sessionId,
                'stripe_payment_intent_id' => $session->payment_intent ?? null,
                'payment_method' => 'stripe',
                'unlocked_at' => now(),
            ]);

            $record->update(['status' => 'completed']);

            \Log::info('[ResumeUnlock] Unlock created', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'amount' => $session->amount_total / 100,
            ]);
        } catch (\Exception $e) {
            \Log::error('[ResumeUnlock] Failed to create unlock', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('company.login')->with('error', __('Could not unlock resume.'));
        }

        $message = __('Resume unlocked successfully! You can now view full details.');
        $company = \App\Company::find($companyId);
        if (\Auth::guard('company')->check() && $company && (int) \Auth::guard('company')->id() === $companyId) {
            return redirect()->route('applicant.profile', ['application_id' => 0])
                ->with('success', $message);
        }
        return redirect()->route('company.login')->with('success', $message);
    }
}
