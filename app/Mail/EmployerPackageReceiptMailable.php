<?php

namespace App\Mail;

use App\Company;
use App\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class EmployerPackageReceiptMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public Package $package,
        public float $amountPaid,
        public ?float $listPrice,
        public string $transactionId,
        public string $currencyCode
    ) {
    }

    public function build()
    {
        // Calculate discount if applicable
        $listPrice = $this->listPrice ?? $this->amountPaid;
        $discountAmount = $listPrice - $this->amountPaid;
        $hasDiscount = abs($discountAmount) > 0.009;
        
        // Generate discount row HTML if there's a discount
        $discountRow = '';
        if ($hasDiscount) {
            $discountRow = '<tr><th style="text-align: left; padding: 10px;">Discount</th><td style="padding: 10px; color: #17D27C;"><strong>-' . $this->currencyCode . ' ' . number_format($discountAmount, 2) . '</strong></td></tr>';
        }
        
        // Prepare data for template
        $data = [
            'COMPANY_NAME' => $this->company->name,
            'PACKAGE_TITLE' => $this->package->package_title,
            'AMOUNT_PAID' => number_format($this->amountPaid, 2),
            'LIST_PRICE' => number_format($listPrice, 2),
            'DISCOUNT_AMOUNT' => $hasDiscount ? number_format($discountAmount, 2) : '0.00',
            'DISCOUNT_ROW' => $discountRow,
            'CURRENCY_CODE' => $this->currencyCode,
            'TRANSACTION_ID' => $this->transactionId
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('package-receipt', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->subject(__('Payment receipt — :title', ['title' => $this->package->package_title]))
                ->view('emails.employer_package_receipt');
        }

        return $this->from([
            'address' => config('mail.recieve_to.address'),
            'name' => config('mail.recieve_to.name'),
        ])
        ->to($this->company->email, $this->company->name)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }
}
