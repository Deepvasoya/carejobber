<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChatMessageNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

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
        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');

        $subject = __('New Message from :sender', ['sender' => $this->data['sender_name']]);

        $siteSetting = \App\SiteSetting::first();

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->subject($subject)
        ->to($this->data['recipient_email'], $this->data['recipient_name'])
        ->view('emails.chat_message_notification')
        ->with(array_merge($this->data, ['siteSetting' => $siteSetting]));
    }
}

