<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class JobApplicationWithCVMailable extends Mailable
{
    use SerializesModels;

    public $job;
    public $user;
    public $cvPath;
    public $cvFilename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job, $user, $cvPath, $cvFilename)
    {
        $this->job = $job;
        $this->user = $user;
        $this->cvPath = $cvPath;
        $this->cvFilename = $cvFilename;
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
            'USER_NAME' => $this->user->name,
            'USER_LINK' => route('user.profile', [$this->user->id]),
            'JOB_TITLE' => $this->job->title,
            'JOB_LINK' => route('job.detail', [$this->job->slug])
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('job-applied-company', $data);

        if (!$parsed) {
            // Fallback to simple email if template not found
            $message = $this->from([
                'address' => config('mail.recieve_to.address'),
                'name' => config('mail.recieve_to.name'),
            ])
            ->to($recipientAddress, $recipientName)
            ->subject('New Job Application - ' . $this->job->title)
            ->html('<p>Dear ' . $company->name . ',</p><p>' . $this->user->name . ' has applied for the job: ' . $this->job->title . '</p><p>Please find the attached CV.</p>');
        } else {
            $message = $this->from([
                'address' => config('mail.recieve_to.address'),
                'name' => config('mail.recieve_to.name'),
            ])
            ->to($recipientAddress, $recipientName)
            ->subject($parsed['subject'])
            ->html($parsed['body']);
        }

        // Attach CV if exists
        if ($this->cvPath && file_exists($this->cvPath)) {
            $message->attach($this->cvPath, [
                'as' => $this->cvFilename,
                'mime' => 'application/pdf'
            ]);
        }

        return $message;
    }
}
