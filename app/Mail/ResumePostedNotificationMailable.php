<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\Company;

class ResumePostedNotificationMailable extends Mailable
{
    use SerializesModels;

    public $user;
    public $company;
    public $profileUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Company $company)
    {
        $this->user = $user;
        $this->company = $company;
        $this->profileUrl = route('user.profile', $user->id);
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
        ->subject(__('New Resume Posted in Your Industry') . ' - ' . config('app.name'))
        ->view('emails.resume_posted_notification')
        ->with([
            'user' => $this->user,
            'company' => $this->company,
            'profileUrl' => $this->profileUrl
        ]);
    }
}
