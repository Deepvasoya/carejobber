<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class JobApplicantStatusMailable extends Mailable
{

    use SerializesModels;

    public $job;
    public $jobApply;
    public $status;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($job, $jobApply, $status)
    {
        $this->job = $job;
        $this->jobApply = $jobApply;
        $this->status = $status;
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
        $status = $this->status;
        
        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');

        // Determine status message based on status
        $statusMessages = [
            'Declined' => 'We regret to inform you that your application was not selected for this position. We appreciate your interest and encourage you to apply for other opportunities.',
            'rejected' => 'We regret to inform you that your application was not selected for this position. We appreciate your interest and encourage you to apply for other opportunities.',
            'Approved' => 'Congratulations! We are pleased to inform you that you have been selected for this position. The employer will contact you soon with further details.',
            'hired' => 'Congratulations! We are pleased to inform you that you have been selected for this position. The employer will contact you soon with further details.',
            'Short List' => 'Great news! Your application has been shortlisted. The employer will review your profile and may contact you for the next steps.',
            'shortlist' => 'Great news! Your application has been shortlisted. The employer will review your profile and may contact you for the next steps.',
            'applied' => 'Your application has been received and is currently under review by the employer.',
        ];

        // Prepare data for template
        $data = [
            'USER_NAME' => $user->name,
            'COMPANY_NAME' => $company->name,
            'COMPANY_LINK' => route('company.detail', $company->slug),
            'JOB_TITLE' => $this->job->title,
            'JOB_LINK' => route('job.detail', [$this->job->slug]),
            'APPLICATION_STATUS' => ucfirst($status),
            'STATUS_MESSAGE' => $statusMessages[$status] ?? 'Your application status has been updated.'
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('job-application-status', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            $subjectMap = [
                'Declined' => 'Application Status Update - Not Selected',
                'rejected' => 'Application Status Update - Not Selected',
                'Approved' => 'Congratulations! You\'re Hired',
                'hired' => 'Congratulations! You\'re Hired',
                'Short List' => 'Great News! You\'ve Been Shortlisted',
                'shortlist' => 'Great News! You\'ve Been Shortlisted',
                'applied' => 'Application Received - Under Review',
            ];
            
            $subject = $subjectMap[$status] ?? 'Application Status Update';
            $siteSetting = \App\SiteSetting::first();

            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->replyTo($recipientAddress, $recipientName)
            ->to($user->email, $user->name)
            ->subject($subject . ' - ' . $company->name)
            ->view('emails.job_applicant_status')
            ->with([
                'status' => $this->status,
                'job_title' => $this->job->title,
                'company_name' => $company->name,
                'user_name' => $user->name,
                'company_link' => route('company.detail', $company->slug),
                'job_link' => route('job.detail', [$this->job->slug]),
                'siteSetting' => $siteSetting
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
