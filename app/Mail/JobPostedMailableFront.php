<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class JobPostedMailableFront extends Mailable
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
        $recipientAddress = $company->email;
        $recipientName = $company->name;

        // Prepare data for template
        $data = [
            'COMPANY_NAME' => $company->name,
            'JOB_TITLE' => $this->job->title,
            'JOB_LINK' => route('job.detail', [$this->job->slug])
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('job-posted-company', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->to($recipientAddress, $recipientName)
                ->subject('Job Vacancy Submission Pending Approval')
                ->view('emails.job_posted_message_front')
                ->with([
                    'name' => $company->name,
                    'link' => route('job.detail', [$this->job->slug]),
                    'link_admin' => route('edit.job', ['id' => $this->job->id])
                ]);
        }

        return $this->from([
            'address' => config('mail.recieve_to.address'),
            'name' => config('mail.recieve_to.name'),
        ])
        ->to($recipientAddress, $recipientName)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }


}
