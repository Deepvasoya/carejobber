<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;

class ContactUs extends Mailable
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
        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');

        // Prepare data for template
        $templateData = [
            'FULL_NAME' => $this->data['full_name'],
            'EMAIL' => $this->data['email'],
            'PHONE' => $this->data['phone'] ?? 'N/A',
            'SUBJECT' => $this->data['subject'],
            'MESSAGE' => $this->data['message']
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('contact-form', $templateData);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from([
                'address' => config('mail.recieve_to.address'),
                'name' => config('mail.recieve_to.name'),
            ])
            ->to($recipientAddress, $recipientName)
            ->replyTo($this->data['email'], $this->data['full_name'])
            ->subject($this->data['subject'])
            ->view('emails.send_contact_message')
            ->with($this->data);
        }

        return $this->from([
            'address' => config('mail.recieve_to.address'),
            'name' => config('mail.recieve_to.name'),
        ])
        ->to($recipientAddress, $recipientName)
        ->replyTo($this->data['email'], $this->data['full_name'])
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }

}
