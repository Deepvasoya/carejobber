<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\Services\EmailTemplateService;

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

        // Prepare data for template
        $data = [
            'USER_NAME' => $this->user->name,
            'PROFILE_URL' => $this->profileUrl,
            'MISSING_FIELDS' => !empty($this->missingFields) ? implode(', ', $this->missingFields) : 'several fields'
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('incomplete-profile', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
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

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->to($this->user->email, $this->user->name)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }
}
