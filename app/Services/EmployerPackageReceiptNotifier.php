<?php

namespace App\Services;

use App\Company;
use App\Mail\EmployerPackageReceiptMailable;
use App\Package;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmployerPackageReceiptNotifier
{
    /**
     * Send a receipt email once per Stripe Checkout session (webhook + success URL may both run).
     */
    public static function sendOnce(
        Company $company,
        Package $package,
        float $amountPaid,
        ?float $listPrice,
        string $stripeSessionId,
        string $currencyCode
    ): void {
        if ($company->email === null || trim((string) $company->email) === '') {
            return;
        }

        $cacheKey = 'employer_pkg_receipt_email_'.$stripeSessionId;
        if (! Cache::add($cacheKey, 1, 86400)) {
            return;
        }

        try {
            Mail::to($company->email)->send(new EmployerPackageReceiptMailable(
                $company,
                $package,
                $amountPaid,
                $listPrice,
                $stripeSessionId,
                $currencyCode
            ));
        } catch (\Throwable $e) {
            Log::error('Employer package receipt email failed', [
                'company_id' => $company->id,
                'session' => $stripeSessionId,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
