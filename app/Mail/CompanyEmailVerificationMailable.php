<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class CompanyEmailVerificationMailable extends Mailable
{
    use SerializesModels;

    public $company;
    public $verificationLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company, $verificationLink)
    {
        $this->company = $company;
        $this->verificationLink = $verificationLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $recipientAddress = $this->company->email;
        $recipientName = $this->company->name;

        // Prepare data for template
        $data = [
            'TO_NAME' => $this->company->name,
            'VERIFICATION_LINK' => $this->verificationLink
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('web-email-verification', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->to($recipientAddress, $recipientName)
                ->subject('Email Verification - ' . config('app.name'))
                ->view('vendor.laravel-user-verification.email')
                ->with([
                    'user' => $this->company,
                    'siteSetting' => \App\SiteSetting::first()
                ]);
        }

        return $this->from([
            'address' => config('mail.recieve_to.address'),
            'name' => config('mail.recieve_to.name'),
        ])
        ->to($recipientAddress, $recipientName)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }
}
