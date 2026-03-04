<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Company;

class ReferralInviteMailable extends Mailable
{
    use SerializesModels;

    public $referrerCompany;
    public $invitedEmail;
    public $referralLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Company $referrerCompany, $invitedEmail)
    {
        $this->referrerCompany = $referrerCompany;
        $this->invitedEmail = $invitedEmail;
        $this->referralLink = $referrerCompany->getReferralLink();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');
    
        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->subject(__('You have been invited to join') . ' ' . config('app.name'))
        ->view('emails.referral_invite')
        ->with([
            'referrerCompany' => $this->referrerCompany,
            'referralLink' => $this->referralLink
        ]);
    }
}
