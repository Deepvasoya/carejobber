<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class JobAppliedJobSeekerMailable extends Mailable
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
            'USER_NAME' => $user->name,
            'COMPANY_NAME' => $company->name,
            'COMPANY_LINK' => route('company.detail', $company->slug),
            'JOB_TITLE' => $this->job->title,
            'JOB_LINK' => route('job.detail', [$this->job->slug])
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('job-applied-jobseeker', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->replyTo($recipientAddress, $recipientName)
            ->to($user->email, $user->name)
            ->subject('Application Received for '.$this->job->title.' at '.$company->name)
            ->view('emails.job_applied_job_seeker_message')
            ->with([
                'job_title' => $this->job->title,
                'company_name' => $company->name,
                'user_name' => $user->name,
                'company_link' => route('company.detail', $company->slug),
                'job_link' => route('job.detail', [$this->job->slug])
            ]);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->replyTo($recipientAddress, $recipientName)
        ->to($user->email, $user->name)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }


}
