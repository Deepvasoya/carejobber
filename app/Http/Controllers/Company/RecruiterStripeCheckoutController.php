<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Package;
use App\PackageCoupon;
use App\Services\EmployerPackageReceiptNotifier;
use App\Services\PackageCouponService;
use App\StripeCheckoutSession;
use App\Traits\CompanyPackageTrait;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

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

        if ((float) $package->package_price <= 0 && $package->type !== Package::TYPE_MONTHLY_RECURRING) {
            if (!$company->canActivateFreeEmployerJobPackage()) {
                $until = $company->getFreeEmployerJobPackageNextAvailableAt();

                flash($until
                    ? __('You already used the free job package in the last 30 days. You can activate it again from :date, or purchase a paid package.', ['date' => $until->format('d M Y')])
                    : __('You cannot activate the free job posting package right now.'))->error();

                return redirect()->route('recruiter.posting.packages', array_filter(['cc' => $countryCode ?: null]));
            }

            return redirect()->route('order.free.package', $package->id);
        }

        $payload = [
            'package_id' => $package->id,
            'company_id' => $company->id,
            'country_code' => $countryCode,
            'tab' => $tab,
            'currency' => $request->query('currency', 'cad'),
            'exp' => time() + 900,
            'coupon_code' => session('employer_package_coupon_code'),
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
        try {
            $payload = decrypt($token);
        } catch (DecryptException $e) {
            flash(__('That checkout link is invalid or expired. Please select your package again.'))->error();

            return redirect()->route('recruiter.posting.packages');
        }

        if (!is_array($payload) || !isset($payload['package_id'], $payload['company_id'], $payload['exp'])) {
            flash(__('That checkout link is invalid. Please select your package again.'))->error();

            return redirect()->route('recruiter.posting.packages');
        }
        if ($payload['exp'] < time()) {
            flash(__('That checkout link has expired. Please select your package again.'))->error();

            return redirect()->route('recruiter.posting.packages');
        }
        $company = \Auth::guard('company')->user();
        if ((int) $company->id !== (int) $payload['company_id']) {
            flash(__('That checkout link is not valid for your account.'))->error();

            return redirect()->route('recruiter.posting.packages');
        }

        $package = Package::find($payload['package_id']);
        if (!$package || $package->package_for !== 'employer') {
            flash(__('Package not found.'))->error();

            return redirect()->route('recruiter.posting.packages');
        }

        if ((float) $package->package_price <= 0 && $package->type !== Package::TYPE_MONTHLY_RECURRING) {
            $cc = $payload['country_code'] ?? '';
            if (!$company->canActivateFreeEmployerJobPackage()) {
                $until = $company->getFreeEmployerJobPackageNextAvailableAt();

                flash($until
                    ? __('You already used the free job package in the last 30 days. You can activate it again from :date, or purchase a paid package.', ['date' => $until->format('d M Y')])
                    : __('You cannot activate the free job posting package right now.'))->error();

                return redirect()->route('recruiter.posting.packages', array_filter(['cc' => $cc ?: null]));
            }

            return redirect()->route('order.free.package', $package->id);
        }

        $secret = static::getStripeSecret();
        if (!$secret) {
            \Log::error('Stripe: No API key. Set STRIPE_SECRET in .env and run: php artisan config:clear');
            flash(__('Stripe is not configured. Please set STRIPE_SECRET in .env and run: php artisan config:clear'))->error();

            return redirect()->route('recruiter.posting.packages');
        }
        Stripe::setApiKey($secret);

        $countryCode = $payload['country_code'] ?? null;
        $currency = strtolower($payload['currency'] ?? 'cad');
        $cancelUrl = $payload['tab'] === 'subscriptions'
            ? route('recruiter.posting.subscriptions', ['cc' => $countryCode])
            : route('recruiter.posting.packages', ['cc' => $countryCode]);

        $rawCoupon = $payload['coupon_code'] ?? null;
        $hasCouponInput = PackageCoupon::normalizeCode((string) $rawCoupon) !== '';
        $couponSvc = app(PackageCouponService::class);
        $eval = $couponSvc->evaluateCheckout($rawCoupon, $package, (int) $company->id, null);

        if ($hasCouponInput && !$eval['ok']) {
            flash(PackageCouponService::humanMessage($eval['reason'] ?? 'default') . ' ' . __('Remove the coupon or choose another package, then try again.'))->error();

            return redirect()->to($cancelUrl);
        }

        $appliedCouponId = null;
        $discountAmount = 0.0;
        $originalCents = (int) round(((float) $package->package_price) * 100);
        $finalCents = $originalCents;
        if (!empty($eval['coupon']) && ($eval['discount'] ?? 0) > 0) {
            $appliedCouponId = $eval['coupon']->id;
            $discountAmount = (float) $eval['discount'];
            $finalCents = (int) round(((float) $eval['total']) * 100);
        }

        $isSubscription = $package->type === Package::TYPE_MONTHLY_RECURRING;
        $monthsLabel = $isSubscription ? $package->subscriptionBillingMonths() : null;
        $productName = $package->package_title ?: ($package->package_num_listings . ' job postings');
        if ($isSubscription) {
            if ($package->subscription_unlimited_jobs) {
                $productName = $package->package_title ?: ($monthsLabel . ' ' . __('months unlimited job postings'));
            } else {
                $productName = $package->package_title ?: ($package->package_num_listings . ' ' . __('job postings') . ' / ' . $monthsLabel . ' ' . __('mo'));
            }
        }

        $productDescription = $countryCode ? __('Country') . ': ' . $countryCode : '';
        if ($discountAmount > 0 && !empty($eval['coupon'])) {
            $couponCode = $eval['coupon']->code ?? '';
            $originalPrice = number_format((float) $package->package_price, 2);
            $discountFormatted = number_format($discountAmount, 2);
            $finalPrice = number_format((float) $eval['total'], 2);
            
            $discountInfo = __('Regular Price') . ': ' . strtoupper($currency) . $originalPrice . ' | ' 
                          . __('Coupon') . ': ' . $couponCode . ' | '
                          . __('Discount') . ': -' . strtoupper($currency) . $discountFormatted . ' | '
                          . __('Final Price') . ': ' . strtoupper($currency) . $finalPrice;
            
            $productDescription = $productDescription ? ($productDescription . ' | ' . $discountInfo) : $discountInfo;
        }

        if ($isSubscription) {
            $billingMonths = max(1, (int) round(($package->duration_days ?: 30) / 30));
            if (!empty($package->stripe_price_id)) {
                $lineItems = [['price' => $package->stripe_price_id, 'quantity' => 1]];
            } else {
                $lineItems = [[
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $productName,
                            'description' => $productDescription,
                        ],
                        'unit_amount' => $finalCents,
                        'recurring' => [
                            'interval' => 'month',
                            'interval_count' => $billingMonths,
                        ],
                    ],
                ]];
            }
        } else {
            $lineItems = [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $productName,
                        'description' => $productDescription,
                    ],
                    'unit_amount' => $finalCents,
                ],
            ]];
        }

        // Stripe replaces {CHECKOUT_SESSION_ID} with the real session ID on redirect.
        // Do not use route() with the placeholder as a param — it gets URL-encoded and Stripe won't replace it.
        $successUrl = route('recruiter.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}';

        $metadata = [
            'company_id' => (string) $company->id,
            'package_id' => (string) $package->id,
            'country_code' => (string) $countryCode,
        ];
        if ($appliedCouponId) {
            $metadata['package_coupon_id'] = (string) $appliedCouponId;
            $metadata['coupon_discount'] = (string) $discountAmount;
        }

        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => $isSubscription ? 'subscription' : 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $company->id,
            'metadata' => $metadata,
        ];
        if ($company->stripe_customer_id) {
            $sessionParams['customer'] = $company->stripe_customer_id;
        } else {
            $sessionParams['customer_email'] = $company->email;
        }

        try {
            $session = StripeSession::create($sessionParams);
        } catch (\Exception $e) {
            \Log::error('Stripe Checkout create failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            flash(__('Payment setup failed. Please try again. If it keeps happening, remove your coupon or contact support.'))->error();

            return redirect()->to($cancelUrl);
        }

        StripeCheckoutSession::create([
            'session_id' => $session->id,
            'company_id' => $company->id,
            'package_id' => $package->id,
            'country_code' => $countryCode,
            'status' => 'pending',
            'package_coupon_id' => $appliedCouponId,
            'coupon_discount_amount' => $discountAmount > 0 ? $discountAmount : null,
            'original_amount_cents' => $originalCents,
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
            flash(__('Invalid session.'))->error();

            return redirect()->route('company.login');
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
            flash(__('Session already processed or invalid.'))->error();

            return redirect()->route('company.login');
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
            flash(__('Stripe is not configured.'))->error();

            return redirect()->route('company.login');
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
            flash(__('Could not verify payment.'))->error();

            return redirect()->route('company.login');
        }

        $paidOk = in_array($session->payment_status ?? '', ['paid', 'no_payment_required'], true);
        $subscriptionOk = ($session->status ?? '') === 'complete' && !empty($session->subscription);
        if (!$paidOk && !$subscriptionOk) {
            \Log::warning('[StripeSuccess] Abort: payment not complete', [
                'session_id' => $sessionId,
                'payment_status' => $session->payment_status ?? null,
                'status' => $session->status ?? null,
                'has_subscription' => !empty($session->subscription),
            ]);
            flash(__('Payment not completed.'))->error();

            return redirect()->route('company.login');
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
            flash(__('Could not update order status.'))->error();

            return redirect()->route('company.login');
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
            flash(__('Package not found.'))->error();

            return redirect()->route('company.login');
        }

        try {
            $paidCents = (int) ($session->amount_total ?? 0);
            $paidAmount = $paidCents > 0 ? round($paidCents / 100, 2) : (float) $package->package_price;
            $listPrice = (float) $package->package_price;

            $this->addCompanyPackage($company, $package, 'Stripe', $paidAmount, $listPrice, $sessionId);
            app(PackageCouponService::class)->redeemEmployerStripeCheckout(
                $record,
                $package,
                isset($session->amount_total) ? (int) $session->amount_total : null,
                strtoupper((string) ($session->currency ?? 'CAD'))
            );

            EmployerPackageReceiptNotifier::sendOnce(
                $company,
                $package,
                $paidAmount,
                abs($listPrice - $paidAmount) > 0.009 ? $listPrice : null,
                $sessionId,
                strtoupper((string) ($session->currency ?? 'CAD'))
            );
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
            flash(__('Could not add package.'))->error();

            return redirect()->route('company.login');
        }

        if ($package->type === Package::TYPE_MONTHLY_RECURRING) {
            $cid = $session->customer ?? null;
            $sid = $session->subscription ?? null;
            if ($cid) {
                $company->stripe_customer_id = is_object($cid) ? $cid->id : $cid;
            }
            if ($sid) {
                $company->stripe_subscription_id = is_object($sid) ? $sid->id : $sid;
                $company->stripe_subscription_status = 'active';
            }
            $company->save();
        }

        $message = $package->type === Package::TYPE_MONTHLY_RECURRING
            ? __('Thank you! Your subscription is active.')
            : __('Thank you! Your job posting credits have been added.');
        $goHome = \Auth::guard('company')->check() && (int) \Auth::guard('company')->id() === (int) $company->id;
        \Log::info('[StripeSuccess] Success; redirecting', [
            'session_id' => $sessionId,
            'redirect_to' => $goHome ? 'company.home' : 'company.login',
        ]);
        if ($goHome) {
            flash($message)->success();

            return redirect()->route('company.home');
        }
        flash($message)->success();

        return redirect()->route('company.login');
    }
}
