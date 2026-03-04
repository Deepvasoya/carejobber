<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;

class IncompleteProfileReminderMailable extends Mailable
{
    use SerializesModels;

    public $user;
    public $profileUrl;
    public $missingFields;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $missingFields = [])
    {
        $this->user = $user;
        $this->profileUrl = route('my.profile');
        $this->missingFields = $missingFields;
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
        ->subject(__('Complete Your Profile') . ' - ' . config('app.name'))
        ->view('emails.incomplete_profile_reminder')
        ->with([
            'user' => $this->user,
            'profileUrl' => $this->profileUrl,
            'missingFields' => $this->missingFields
        ]);
    }
}
