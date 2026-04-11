<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class UserRegisteredMailable extends Mailable
{

    use SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
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
            'USER_EMAIL' => $this->user->email,
            'USER_LINK' => route('user.profile', $this->user->id),
            'USER_ADMIN_LINK' => route('edit.user', ['id' => $this->user->id])
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('user-registered', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->to($recipientAddress, $recipientName)
            ->subject('Job Seeker "' . $this->user->name . '" has been registered on "' . config('app.name'))
            ->view('emails.user_registered_message')
            ->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'link' => route('user.profile', $this->user->id),
                'link_admin' => route('edit.user', ['id' => $this->user->id])
            ]);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->to($recipientAddress, $recipientName)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }


}
