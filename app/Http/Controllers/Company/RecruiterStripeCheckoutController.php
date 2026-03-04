<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Package;
use App\StripeCheckoutSession;
use App\Traits\CompanyPackageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class RecruiterStripeCheckoutController extends Controller
{
    use CompanyPackageTrait;

    /**
     * Get Stripe secret from .env (services.stripe) or site_settings (stripe.stripe_secret).
     */
    protected static function getStripeSecret(): ?string
    {
        $secret = config('services.stripe.secret') ?: config('stripe.stripe_secret');
        return $secret ? (string) $secret : null;
    }

    public function __construct()
    {
        // Set in createSession/success when we have the key
    }

    /**
     * Build signed token and redirect to createSession. Called when user clicks "Buy now".
     */
    public function redirectToCheckout(Request $request, int $packageId)
    {
        $company = \Auth::guard('company')->user();
        $package = Package::where('id', $packageId)->where('package_for', 'employer')->firstOrFail();
        $countryCode = $request->query('cc', '');
        $tab = $request->query('tab', 'packages');
        $payload = [
            'package_id' => $package->id,
            'company_id' => $company->id,
            'country_code' => $countryCode,
            'tab' => $tab,
            'currency' => $request->query('currency', 'cad'),
            'exp' => time() + 900,
        ];
        $token = encrypt($payload);
        return redirect()->route('recruiter.stripe.checkout', ['token' => $token]);
    }

    /**
     * Create Stripe Checkout Session and redirect to Stripe.
     * Token = encrypted payload: package_id, company_id, country_code, tab, exp.
     */
    public function createSession(Request $request, string $token)
    {
        $payload = decrypt($token);
        if (!$payload || !isset($payload['package_id'], $payload['company_id'], $payload['exp'])) {
            return redirect()->route('recruiter.posting.packages')->with('error', __('Invalid link.'));
        }
        if ($payload['exp'] < time()) {
            return redirect()->route('recruiter.posting.packages')->with('error', __('Link expired.'));
        }
        $company = \Auth::guard('company')->user();
        if ((int) $company->id !== (int) $payload['company_id']) {
            return redirect()->route('recruiter.posting.packages')->with('error', __('Invalid link.'));
        }

        $package = Package::find($payload['package_id']);
        if (!$package || $package->package_for !== 'employer') {
            return redirect()->route('recruiter.posting.packages')->with('error', __('Package not found.'));
        }

        $secret = static::getStripeSecret();
        if (!$secret) {
            \Log::error('Stripe: No API key. Set STRIPE_SECRET in .env and run: php artisan config:clear');
            return redirect()->route('recruiter.posting.packages')->with('error', __('Stripe is not configured. Please set STRIPE_SECRET in .env and run: php artisan config:clear'));
        }
        Stripe::setApiKey($secret);

        $countryCode = $payload['country_code'] ?? null;
        $isSubscription = $package->type === 'monthly_recurring';
        $productName = $package->package_title ?: ($package->package_num_listings . ' job postings');
        if ($isSubscription && $package->duration_days) {
            $months = (int) round($package->duration_days / 30);
            $productName = $package->package_title ?: ($months . ' months unlimited job postings');
        }

        $lineItem = [
            'quantity' => 1,
            'price_data' => [
                'currency' => strtolower($payload['currency'] ?? 'cad'),
                'product_data' => [
                    'name' => $productName,
                    'description' => $countryCode ? __('Country') . ': ' . $countryCode : '',
                ],
                'unit_amount' => (int) round($package->package_price * 100),
            ],
        ];

        // Stripe replaces {CHECKOUT_SESSION_ID} with the real session ID on redirect.
        // Do not use route() with the placeholder as a param — it gets URL-encoded and Stripe won't replace it.
        $successUrl = route('recruiter.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $payload['tab'] === 'subscriptions'
            ? route('recruiter.posting.subscriptions', ['cc' => $countryCode])
            : route('recruiter.posting.packages', ['cc' => $countryCode]);

        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => [$lineItem],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $company->id,
            'metadata' => [
                'company_id' => (string) $company->id,
                'package_id' => (string) $package->id,
                'country_code' => (string) $countryCode,
            ],
            'customer_email' => $company->email,
        ];

        try {
            $session = StripeSession::create($sessionParams);
        } catch (\Exception $e) {
            \Log::error('Stripe Checkout create failed: ' . $e->getMessage());
            return redirect()->to($cancelUrl)->with('error', __('Payment setup failed. Please try again.'));
        }

        StripeCheckoutSession::create([
            'session_id' => $session->id,
            'company_id' => $company->id,
            'package_id' => $package->id,
            'country_code' => $countryCode,
            'status' => 'pending',
        ]);

        return redirect()->away($session->url);
    }

    /**
     * Success return from Stripe Checkout: fulfill order (add credits / activate subscription).
     * This route is public (no auth middleware) so that when Stripe redirects the browser
     * after payment, fulfillment still runs even if the session cookie is not sent (e.g.
     * cross-site redirect from stripe.com). We verify payment via Stripe API and identify
     * the company from our StripeCheckoutSession record.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $logCtx = [
            'session_id' => $sessionId,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'has_cookie' => $request->hasCookie(config('session.cookie')),
            'company_authenticated' => \Auth::guard('company')->check(),
        ];

        \Log::info('[StripeSuccess] Hit success URL', $logCtx);


        // dd($sessionId, $logCtx, $request->all());
        if (!$sessionId) {
            \Log::warning('[StripeSuccess] Abort: missing session_id');
            return redirect()->route('company.login')->with('error', __('Invalid session.'));
        }

        // Find by session_id only so it works when user is not logged in (redirect from Stripe)
        $record = StripeCheckoutSession::where('session_id', $sessionId)
            ->where('status', 'pending')
            ->first();

        if (!$record) {
            $anyRecord = StripeCheckoutSession::where('session_id', $sessionId)->first();
            \Log::warning('[StripeSuccess] Abort: no pending record for session_id', [
                'session_id' => $sessionId,
                'existing_record' => $anyRecord ? [
                    'id' => $anyRecord->id,
                    'status' => $anyRecord->status,
                    'company_id' => $anyRecord->company_id,
                ] : null,
            ]);
            return redirect()->route('company.login')->with('error', __('Session already processed or invalid.'));
        }

        \Log::info('[StripeSuccess] Found pending record', [
            'session_id' => $sessionId,
            'record_id' => $record->id,
            'company_id' => $record->company_id,
            'package_id' => $record->package_id,
        ]);

        $secret = static::getStripeSecret();
        if (!$secret) {
            \Log::error('[StripeSuccess] Abort: Stripe secret not configured');
            return redirect()->route('company.login')->with('error', __('Stripe is not configured.'));
        }
        try {
            Stripe::setApiKey($secret);
            $session = StripeSession::retrieve($sessionId);
            \Log::info('[StripeSuccess] Stripe session retrieved', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status ?? null,
                'status' => $session->status ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('[StripeSuccess] Stripe session retrieve failed', [
                'session_id' => $sessionId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('company.login')->with('error', __('Could not verify payment.'));
        }

        if ($session->payment_status !== 'paid' && $session->status !== 'complete') {
            \Log::warning('[StripeSuccess] Abort: payment not complete', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status ?? null,
                'status' => $session->status ?? null,
            ]);
            return redirect()->route('company.login')->with('error', __('Payment not completed.'));
        }

        try {
            $record->update(['status' => 'completed']);
            \Log::info('[StripeSuccess] Updated record status to completed', [
                'session_id' => $sessionId,
                'record_id' => $record->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('[StripeSuccess] Failed to update record status', [
                'session_id' => $sessionId,
                'record_id' => $record->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('company.login')->with('error', __('Could not update order status.'));
        }

        $company = \App\Company::find($record->company_id);
        if (!$company) {
            \Log::error('[StripeSuccess] Abort: company not found', [
                'session_id' => $sessionId,
                'company_id' => $record->company_id,
            ]);
            return redirect()->route('company.login')->with('error', __('Company not found.'));
        }

        $package = Package::find($record->package_id);
        if (!$package) {
            \Log::error('[StripeSuccess] Abort: package not found', [
                'session_id' => $sessionId,
                'package_id' => $record->package_id,
            ]);
            return redirect()->route('company.login')->with('error', __('Package not found.'));
        }

        if ($package->type === 'monthly_recurring') {
            $package->package_num_days = $package->duration_days ?: 30;
            $package->package_num_listings = 99999;
        }

        try {
            $this->addCompanyPackage($company, $package, 'Stripe');
            \Log::info('[StripeSuccess] addCompanyPackage completed', [
                'session_id' => $sessionId,
                'company_id' => $company->id,
                'package_id' => $package->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('[StripeSuccess] addCompanyPackage failed', [
                'session_id' => $sessionId,
                'company_id' => $company->id,
                'package_id' => $package->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('company.login')->with('error', __('Could not add package.'));
        }

        $message = __('Thank you! Your job posting credits have been added.');
        $goHome = \Auth::guard('company')->check() && (int) \Auth::guard('company')->id() === (int) $company->id;
        \Log::info('[StripeSuccess] Success; redirecting', [
            'session_id' => $sessionId,
            'redirect_to' => $goHome ? 'company.home' : 'company.login',
        ]);
        if ($goHome) {
            return redirect()->route('company.home')->with('success', $message);
        }
        return redirect()->route('company.login')->with('success', $message);
    }
}
