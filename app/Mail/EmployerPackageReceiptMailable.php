<?php

namespace App\Mail;

use App\Company;
use App\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
        return $this->subject(__('Payment receipt — :title', ['title' => $this->package->package_title]))
            ->view('emails.employer_package_receipt');
    }
}
