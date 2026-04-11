<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;

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

        // Prepare data for template
        $templateData = [
            'SENDER_NAME' => $this->data['sender_name'],
            'RECIPIENT_NAME' => $this->data['recipient_name'],
            'MESSAGE_PREVIEW' => $this->data['message_preview'] ?? $this->data['message'] ?? 'New message',
            'MESSAGE_TYPE' => $this->data['message_type'] ?? 'text',
            'CHAT_URL' => $this->data['chat_url'] ?? url('/messages')
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('chat-message', $templateData);

        if (!$parsed) {
            // Fallback to old method if template not found
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

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->to($this->data['recipient_email'], $this->data['recipient_name'])
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }
}
