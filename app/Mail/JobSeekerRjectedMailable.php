<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobSeekerRejectedMailable extends Mailable
{

    use SerializesModels;

    public $job;
    public $jobApplyRejected;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job, $jobApplyRejected)
    {
        $this->job = $job;
        $this->jobApplyRejected = $jobApplyRejected;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $company = $this->job->getCompany();
        $user = $this->jobApplyRejected->getUser();
    
        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');

        // Prepare data for template
        $templateData = [
            'USER_NAME' => $user->name,
            'COMPANY_NAME' => $company->name,
            'COMPANY_LINK' => route('company.detail', $company->slug),
            'JOB_TITLE' => $this->job->title,
            'JOB_LINK' => route('job.detail', [$this->job->slug])
        ];

        // Get parsed template
        $parsed = \App\Services\EmailTemplateService::parseTemplate('job-seeker-rejected', $templateData);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->replyTo($recipientAddress, $recipientName)
            ->to($user->email, $user->name)
            ->subject($user->name . ' you have been rejected for the job "' . $this->job->title)
            ->view('emails.job_seeker_rejected_message')
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
