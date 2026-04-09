<?php

namespace App\Http\Controllers;

use App\Company;
use App\Package;
use App\Services\PackageCouponService;
use App\StripeCheckoutSession;
use App\Models\ResumeUnlock;
use App\Traits\CompanyPackageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    use CompanyPackageTrait;

    protected static function getStripeSecret(): ?string
    {
        $secret = config('services.stripe.secret') ?: config('stripe.stripe_secret');
        return $secret ? (string) $secret : null;
    }

    protected static function getWebhookSecret(): ?string
    {
        return config('services.stripe.webhook_secret') ?: config('stripe.webhook_secret');
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = static::getWebhookSecret();

        \Log::info('[StripeWebhook] Received webhook', [
            'has_signature' => !empty($sigHeader),
            'payload_size' => strlen($payload),
        ]);

        if ($webhookSecret) {
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } catch (SignatureVerificationException $e) {
                \Log::error('[StripeWebhook] Signature verification failed', [
                    'message' => $e->getMessage(),
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            } catch (\Exception $e) {
                \Log::error('[StripeWebhook] Event construction failed', [
                    'message' => $e->getMessage(),
                ]);
                return response()->json(['error' => 'Webhook error'], 400);
            }
        } else {
            $event = json_decode($payload, true);
            \Log::warning('[StripeWebhook] No webhook secret; signature not verified');
        }

        $type = $event['type'] ?? null;
        $data = $event['data']['object'] ?? null;

        \Log::info('[StripeWebhook] Event type', [
            'type' => $type,
            'id' => $data['id'] ?? null,
        ]);

        try {
            switch ($type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($data);
                    break;

                case 'checkout.session.async_payment_succeeded':
                    $this->handleCheckoutSessionCompleted($data);
                    break;

                case 'checkout.session.async_payment_failed':
                    $this->handleCheckoutSessionFailed($data);
                    break;

                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($data);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($data);
                    break;

                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($data);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($data);
                    break;

                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($data);
                    break;

                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($data);
                    break;

                default:
                    \Log::info('[StripeWebhook] Unhandled event type', ['type' => $type]);
            }
        } catch (\Exception $e) {
            \Log::error('[StripeWebhook] Handler exception', [
                'type' => $type,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Handler failed'], 500);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        $sessionId = $session['id'] ?? null;
        $metadata = $session['metadata'] ?? [];
        $paymentStatus = $session['payment_status'] ?? null;

        \Log::info('[StripeWebhook] checkout.session.completed', [
            'session_id' => $sessionId,
            'payment_status' => $paymentStatus,
            'metadata' => $metadata,
        ]);

        if (!in_array($paymentStatus, ['paid', 'no_payment_required'], true) && empty($session['subscription'] ?? null)) {
            \Log::warning('[StripeWebhook] Session not paid yet', [
                'session_id' => $sessionId,
                'payment_status' => $paymentStatus,
            ]);
            return;
        }

        $type = $metadata['type'] ?? null;

        if ($type === 'resume_unlock') {
            $this->fulfillResumeUnlock($sessionId, $metadata, $session);
        } elseif ($type === 'job_promotions') {
            $meta = is_array($metadata) ? $metadata : (array) $metadata;
            \App\Http\Controllers\Company\JobPromotionCheckoutController::fulfillJobPromotions(
                $sessionId,
                $meta,
                null,
                isset($session['amount_total']) ? (int) $session['amount_total'] : null
            );
        } elseif (isset($metadata['package_id'])) {
            $this->fulfillPackagePurchase($sessionId, $metadata, $session);
        } else {
            \Log::warning('[StripeWebhook] Unknown session type', [
                'session_id' => $sessionId,
                'metadata' => $metadata,
            ]);
        }
    }

    protected function handleCheckoutSessionFailed($session)
    {
        $sessionId = $session['id'] ?? null;
        \Log::warning('[StripeWebhook] checkout.session.async_payment_failed', [
            'session_id' => $sessionId,
        ]);

        StripeCheckoutSession::where('session_id', $sessionId)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);
    }

    protected function fulfillResumeUnlock($sessionId, $metadata, $session)
    {
        $companyId = (int) ($metadata['company_id'] ?? 0);
        $userId = (int) ($metadata['user_id'] ?? 0);

        if (!$companyId || !$userId) {
            \Log::error('[StripeWebhook] Missing company_id or user_id', [
                'session_id' => $sessionId,
                'metadata' => $metadata,
            ]);
            return;
        }

        if (ResumeUnlock::isUnlockedBy($userId, $companyId)) {
            \Log::info('[StripeWebhook] Resume already unlocked', [
                'company_id' => $companyId,
                'user_id' => $userId,
            ]);
            StripeCheckoutSession::where('session_id', $sessionId)->update(['status' => 'completed']);
            return;
        }

        try {
            ResumeUnlock::create([
                'company_id' => $companyId,
                'user_id' => $userId,
                'paid_amount' => ($session['amount_total'] ?? 0) / 100,
                'currency' => strtoupper($session['currency'] ?? 'CAD'),
                'stripe_session_id' => $sessionId,
                'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
                'payment_method' => 'stripe',
                'unlocked_at' => now(),
            ]);

            StripeCheckoutSession::where('session_id', $sessionId)->update(['status' => 'completed']);

            \Log::info('[StripeWebhook] Resume unlock fulfilled', [
                'company_id' => $companyId,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error('[StripeWebhook] Resume unlock failed', [
                'company_id' => $companyId,
                'user_id' => $userId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function fulfillPackagePurchase($sessionId, $metadata, $session)
    {
        $companyId = (int) ($metadata['company_id'] ?? 0);
        $packageId = (int) ($metadata['package_id'] ?? 0);

        if (!$companyId || !$packageId) {
            \Log::error('[StripeWebhook] Missing company_id or package_id', [
                'session_id' => $sessionId,
                'metadata' => $metadata,
            ]);
            return;
        }

        $record = StripeCheckoutSession::where('session_id', $sessionId)
            ->where('company_id', $companyId)
            ->where('status', 'pending')
            ->first();

        if (!$record) {
            \Log::warning('[StripeWebhook] No pending package session', [
                'session_id' => $sessionId,
                'company_id' => $companyId,
            ]);
            return;
        }

        $company = Company::find($companyId);
        $package = Package::find($packageId);

        if (!$company || !$package) {
            \Log::error('[StripeWebhook] Company or package not found', [
                'company_id' => $companyId,
                'package_id' => $packageId,
            ]);
            return;
        }

        try {
            $this->addCompanyPackage($company, $package, 'Stripe');
            app(PackageCouponService::class)->redeemEmployerStripeCheckout(
                $record,
                $package,
                isset($session['amount_total']) ? (int) $session['amount_total'] : null,
                strtoupper((string) ($session['currency'] ?? 'CAD'))
            );
            $record->update(['status' => 'completed']);

            // Store Stripe customer and subscription IDs for recurring packages
            if ($package->type === 'monthly_recurring') {
                $customerId = $session['customer'] ?? null;
                $subscriptionId = $session['subscription'] ?? null;
                
                if ($customerId) {
                    $company->stripe_customer_id = $customerId;
                }
                if ($subscriptionId) {
                    $company->stripe_subscription_id = $subscriptionId;
                    $company->stripe_subscription_status = 'active';
                    
                    // Set subscription end date based on current_period_end if available
                    if (isset($session['subscription_details']['current_period_end'])) {
                        $company->stripe_subscription_ends_at = \Carbon\Carbon::createFromTimestamp($session['subscription_details']['current_period_end']);
                    }
                }
                $company->save();

                \Log::info('[StripeWebhook] Stored subscription details', [
                    'company_id' => $companyId,
                    'customer_id' => $customerId,
                    'subscription_id' => $subscriptionId,
                ]);
            }

            \Log::info('[StripeWebhook] Package fulfilled', [
                'company_id' => $companyId,
                'package_id' => $packageId,
            ]);
        } catch (\Exception $e) {
            \Log::error('[StripeWebhook] Package fulfillment failed', [
                'company_id' => $companyId,
                'package_id' => $packageId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        \Log::info('[StripeWebhook] payment_intent.succeeded', [
            'payment_intent_id' => $paymentIntent['id'] ?? null,
        ]);
    }

    protected function handlePaymentIntentFailed($paymentIntent)
    {
        \Log::warning('[StripeWebhook] payment_intent.payment_failed', [
            'payment_intent_id' => $paymentIntent['id'] ?? null,
        ]);
    }

    protected function handleSubscriptionUpdated($subscription)
    {
        $subscriptionId = $subscription['id'] ?? null;
        $customerId = $subscription['customer'] ?? null;
        $status = $subscription['status'] ?? null;
        $currentPeriodEnd = $subscription['current_period_end'] ?? null;

        \Log::info('[StripeWebhook] customer.subscription.updated', [
            'subscription_id' => $subscriptionId,
            'status' => $status,
            'customer' => $customerId,
            'current_period_end' => $currentPeriodEnd,
        ]);

        if (!$subscriptionId || !$customerId) {
            \Log::warning('[StripeWebhook] Missing subscription_id or customer_id');
            return;
        }

        // Find company by stripe_customer_id or stripe_subscription_id
        $company = Company::where('stripe_customer_id', $customerId)
            ->orWhere('stripe_subscription_id', $subscriptionId)
            ->first();

        if (!$company) {
            \Log::warning('[StripeWebhook] Company not found for subscription', [
                'customer_id' => $customerId,
                'subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Update subscription status
        $company->stripe_customer_id = $customerId;
        $company->stripe_subscription_id = $subscriptionId;
        $company->stripe_subscription_status = $status;
        
        if ($currentPeriodEnd) {
            $company->stripe_subscription_ends_at = \Carbon\Carbon::createFromTimestamp($currentPeriodEnd);
        }

        // If subscription is active, ensure package dates are current
        if ($status === 'active' && $company->package_id) {
            if (!$company->package_end_date || $company->package_end_date < now()) {
                $package = Package::find($company->package_id);
                if ($package && $package->type === 'monthly_recurring') {
                    $company->package_start_date = now();
                    $company->package_end_date = now()->addDays($package->duration_days ?: 30);
                    \Log::info('[StripeWebhook] Extended package dates for active subscription', [
                        'company_id' => $company->id,
                        'package_end_date' => $company->package_end_date,
                    ]);
                }
            }
        }

        // If subscription is canceled/past_due, mark package as expired
        if (in_array($status, ['canceled', 'unpaid', 'incomplete_expired'])) {
            if ($company->package_id && $company->package_end_date > now()) {
                $company->package_end_date = now();
                \Log::info('[StripeWebhook] Expired package due to subscription cancellation', [
                    'company_id' => $company->id,
                    'status' => $status,
                ]);
            }
        }

        $company->save();

        \Log::info('[StripeWebhook] Company subscription updated', [
            'company_id' => $company->id,
            'status' => $status,
        ]);
    }

    protected function handleSubscriptionDeleted($subscription)
    {
        $subscriptionId = $subscription['id'] ?? null;
        $customerId = $subscription['customer'] ?? null;

        \Log::info('[StripeWebhook] customer.subscription.deleted', [
            'subscription_id' => $subscriptionId,
            'customer' => $customerId,
        ]);

        if (!$subscriptionId || !$customerId) {
            \Log::warning('[StripeWebhook] Missing subscription_id or customer_id');
            return;
        }

        // Find company
        $company = Company::where('stripe_customer_id', $customerId)
            ->orWhere('stripe_subscription_id', $subscriptionId)
            ->first();

        if (!$company) {
            \Log::warning('[StripeWebhook] Company not found for deleted subscription', [
                'customer_id' => $customerId,
                'subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Mark subscription as canceled and expire package
        $company->stripe_subscription_status = 'canceled';
        
        if ($company->package_id && $company->package_end_date > now()) {
            $company->package_end_date = now();
            \Log::info('[StripeWebhook] Package expired due to subscription deletion', [
                'company_id' => $company->id,
            ]);
        }

        $company->save();

        \Log::info('[StripeWebhook] Company subscription deleted', [
            'company_id' => $company->id,
        ]);
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        $subscriptionId = $invoice['subscription'] ?? null;
        $customerId = $invoice['customer'] ?? null;
        $periodEnd = $invoice['period_end'] ?? null;

        \Log::info('[StripeWebhook] invoice.payment_succeeded', [
            'invoice_id' => $invoice['id'] ?? null,
            'subscription_id' => $subscriptionId,
            'customer' => $customerId,
            'amount_paid' => $invoice['amount_paid'] ?? null,
        ]);

        if (!$subscriptionId || !$customerId) {
            return;
        }

        // Find company and extend subscription
        $company = Company::where('stripe_customer_id', $customerId)
            ->orWhere('stripe_subscription_id', $subscriptionId)
            ->first();

        if ($company) {
            $company->stripe_subscription_status = 'active';
            if ($periodEnd) {
                $company->stripe_subscription_ends_at = \Carbon\Carbon::createFromTimestamp($periodEnd);
            }
            
            // Extend package end date for recurring packages
            if ($company->package_id) {
                $package = Package::find($company->package_id);
                if ($package && $package->type === 'monthly_recurring') {
                    $company->package_end_date = \Carbon\Carbon::createFromTimestamp($periodEnd);
                    \Log::info('[StripeWebhook] Extended package for successful invoice', [
                        'company_id' => $company->id,
                        'new_end_date' => $company->package_end_date,
                    ]);
                }
            }
            
            $company->save();
        }
    }

    protected function handleInvoicePaymentFailed($invoice)
    {
        $subscriptionId = $invoice['subscription'] ?? null;
        $customerId = $invoice['customer'] ?? null;

        \Log::warning('[StripeWebhook] invoice.payment_failed', [
            'invoice_id' => $invoice['id'] ?? null,
            'subscription_id' => $subscriptionId,
            'customer' => $customerId,
            'amount_due' => $invoice['amount_due'] ?? null,
        ]);

        if (!$subscriptionId || !$customerId) {
            return;
        }

        // Find company and mark subscription as past_due
        $company = Company::where('stripe_customer_id', $customerId)
            ->orWhere('stripe_subscription_id', $subscriptionId)
            ->first();

        if ($company) {
            $company->stripe_subscription_status = 'past_due';
            $company->save();

            \Log::warning('[StripeWebhook] Company subscription marked past_due', [
                'company_id' => $company->id,
            ]);
        }
    }
}
