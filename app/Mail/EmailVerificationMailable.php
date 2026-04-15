<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class EmailVerificationMailable extends Mailable
{
    use SerializesModels;

    public $user;
    public $verificationCode;
    public $expiresAt;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $verificationCode, $expiresAt)
    {
        $this->user = $user;
        $this->verificationCode = $verificationCode;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $recipientAddress = $this->user->email;
        $recipientName = $this->user->name;

        // Prepare data for template
        $data = [
            'NAME' => $this->user->name,
            'VERIFICATION_CODE' => $this->verificationCode,
            'EXPIRES_AT' => $this->expiresAt
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('email-verification', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->to($recipientAddress, $recipientName)
                ->subject('Email Verification Code - ' . config('app.name'))
                ->view('emails.verification-code')
                ->with([
                    'name' => $this->user->name,
                    'verification_code' => $this->verificationCode,
                    'expires_at' => $this->expiresAt
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
