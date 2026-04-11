<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class JobPostedMailable extends Mailable
{

    use SerializesModels;

    public $job;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job)
    {
        $this->job = $job;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $company = $this->job->getCompany();
        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');

        // Prepare data for template
        $data = [
            'COMPANY_NAME' => $company->name,
            'JOB_TITLE' => $this->job->title,
            'JOB_LINK' => route('job.detail', [$this->job->slug]),
            'JOB_ADMIN_LINK' => route('edit.job', ['id' => $this->job->id])
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('job-posted-admin', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->to($recipientAddress, $recipientName)
                ->subject('Employer/Company "' . $company->name . '" has posted a new job on "' . config('app.name'))
                ->view('emails.job_posted_message')
                ->with([
                    'name' => $company->name,
                    'link' => route('job.detail', [$this->job->slug]),
                    'link_admin' => route('edit.job', ['id' => $this->job->id])
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
