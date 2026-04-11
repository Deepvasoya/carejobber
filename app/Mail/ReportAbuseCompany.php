<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;

class ReportAbuseCompany extends Mailable
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
            'YOUR_NAME' => $this->data['your_name'],
            'YOUR_EMAIL' => $this->data['your_email'],
            'REPORTED_URL' => $this->data['link'] ?? $this->data['reported_url'] ?? '#'
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('report-abuse', $templateData);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->replyTo($this->data['your_email'], $this->data['your_name'])
            ->to($recipientAddress, $recipientName)
            ->subject($this->data['your_name'] . ' has reported a "' . config('app.name') . '" link')
            ->view('emails.report_abuse_company_message')
            ->with($this->data);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->replyTo($this->data['your_email'], $this->data['your_name'])
        ->to($recipientAddress, $recipientName)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }


}
