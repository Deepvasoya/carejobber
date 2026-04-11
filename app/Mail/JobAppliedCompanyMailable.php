<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class JobAppliedCompanyMailable extends Mailable
{

    use SerializesModels;

    public $job;
    public $jobApply;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job, $jobApply)
    {
        $this->job = $job;
        $this->jobApply = $jobApply;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $company = $this->job->getCompany();
        $user = $this->jobApply->getUser();

        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');

        // Prepare data for template
        $data = [
            'COMPANY_NAME' => $company->name,
            'USER_NAME' => $user->name,
            'USER_LINK' => route('user.profile', $user->id),
            'JOB_TITLE' => $this->job->title,
            'JOB_LINK' => route('job.detail', [$this->job->slug])
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('job-applied-company', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->replyTo($recipientAddress, $recipientName)
            ->to($company->email, $company->name)
            ->subject('New Job Application Received')
            ->view('emails.job_applied_company_message')
            ->with([
                'job_title' => $this->job->title,
                'company_name' => $company->name,
                'user_name' => $user->name,
                'user_link' => route('user.profile', $user->id),
                'job_link' => route('job.detail', [$this->job->slug])
            ]);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->replyTo($recipientAddress, $recipientName)
        ->to($company->email, $company->name)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }


}
