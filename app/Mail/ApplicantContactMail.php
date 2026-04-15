<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicantContactMail extends Mailable
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
        'TO_NAME' => $this->data['to_name'],
        'FROM_NAME' => $this->data['from_name'],
        'SUBJECT' => $this->data['subject'] ?? '',
        'MESSAGE' => $this->data['message_txt'] ?? ''
    ];

    // Get parsed template
    $parsed = \App\Services\EmailTemplateService::parseTemplate('applicant-contact', $templateData);

    if (!$parsed) {
        // Fallback to old method if template not found
        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->replyTo($recipientAddress, $recipientName)
        ->to($this->data['to_email'], $this->data['to_name'])
        ->subject('Contact from: ' . $this->data['from_name'])
        ->view('emails.send_applicant_contact_message')
        ->with($this->data);
    }

    return $this->from([
        'address' => $recipientAddress,
        'name' => $recipientName,
    ])
    ->replyTo($recipientAddress, $recipientName)
    ->to($this->data['to_email'], $this->data['to_name'])
    ->subject($parsed['subject'])
    ->html($parsed['body']);
}


}
