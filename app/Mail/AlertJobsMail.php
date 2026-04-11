<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;

class AlertJobsMail extends Mailable
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
        // Prepare data for template
        $templateData = [
            'SUBJECT' => $this->data['subject'],
            'JOB_LIST' => $this->data['jobs'],
            'DISABLE_LINK' => route('disable.job.alerts') ?? '#'
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('job-alerts', $templateData);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from(config('mail.recieve_to.address'), config('mail.recieve_to.name'))
                        ->to($this->data['email'], $this->data['email'])
                        ->subject($this->data['subject'])
                        ->view('emails.send_job_alerts')
                        ->with('subject',$this->data['subject'])
                        ->with('jobs',$this->data['jobs']);
        }

        return $this->from(config('mail.recieve_to.address'), config('mail.recieve_to.name'))
                    ->to($this->data['email'], $this->data['email'])
                    ->subject($parsed['subject'])
                    ->html($parsed['body']);
    }

}
