<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
    
    // Determine subject line based on status
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

    $recipientAddress = config('mail.recieve_to.address');
    $recipientName = config('mail.recieve_to.name');

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


}
