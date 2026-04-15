<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class PasswordResetMailable extends Mailable
{
    use SerializesModels;

    public $user;
    public $resetCode;
    public $expiresAt;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $resetCode, $expiresAt)
    {
        $this->user = $user;
        $this->resetCode = $resetCode;
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
            'CODE' => $this->resetCode,
            'EXPIRES_AT' => $this->expiresAt
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('password-reset', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->to($recipientAddress, $recipientName)
                ->subject('Password Reset Code - ' . config('app.name'))
                ->view('emails.password-reset-code')
                ->with([
                    'name' => $this->user->name,
                    'code' => $this->resetCode,
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
