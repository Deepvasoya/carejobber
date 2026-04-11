<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\Services\EmailTemplateService;

class UserReferralInviteMailable extends Mailable
{
    use SerializesModels;

    public $referrerUser;
    public $invitedEmail;
    public $referralLink;

    public function __construct(User $referrerUser, $invitedEmail)
    {
        $this->referrerUser = $referrerUser;
        $this->invitedEmail = $invitedEmail;
        $this->referralLink = $referrerUser->getReferralLink();
    }

    public function build()
    {
        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');

        // Prepare data for template
        $data = [
            'REFERRER_NAME' => $this->referrerUser->name,
            'REFERRAL_LINK' => $this->referralLink,
            'INVITED_EMAIL' => $this->invitedEmail
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('referral-invite-user', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->subject(__('You have been invited to join') . ' ' . config('app.name'))
            ->view('emails.user_referral_invite')
            ->with([
                'referrerUser' => $this->referrerUser,
                'referralLink' => $this->referralLink
            ]);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->to($this->invitedEmail)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }
}
