<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;

class EmailToFriend extends Mailable
{

    use Queueable,
        SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        // Prepare data for template
        $templateData = [
            'YOUR_NAME' => $this->data['your_name'],
            'YOUR_EMAIL' => $this->data['your_email'],
            'FRIEND_NAME' => $this->data['friend_name'],
            'FRIEND_EMAIL' => $this->data['friend_email'],
            'JOB_URL' => $this->data['job_url'] ?? $this->data['link'] ?? '#'
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('email-to-friend', $templateData);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from($fromAddress, $fromName) 
                ->replyTo($this->data['your_email'], $this->data['your_name'])
                ->to($this->data['friend_email'], $this->data['friend_name'])
                ->subject(__('Your friend') . ' ' . $this->data['your_name'] . ' ' . __('has shared a link with you'))
                ->view('emails.send_to_friend_message')
                ->with($this->data);
        }

        return $this->from($fromAddress, $fromName) 
            ->replyTo($this->data['your_email'], $this->data['your_name'])
            ->to($this->data['friend_email'], $this->data['friend_name'])
            ->subject($parsed['subject'])
            ->html($parsed['body']);
    }


}
