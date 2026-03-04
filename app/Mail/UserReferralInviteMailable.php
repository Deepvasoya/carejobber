<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;

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
}
