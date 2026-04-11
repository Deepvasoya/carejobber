<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Job;
use App\User;
use App\Services\EmailTemplateService;

class JobRecommendationJobSeekerMailable extends Mailable
{
    use SerializesModels;

    public $job;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Job $job, User $user)
    {
        $this->job = $job;
        $this->user = $user;
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

        // Prepare salary range
        $salaryRange = 'Not disclosed';
        if (!(bool)$this->job->hide_salary && $this->job->salary_from && $this->job->salary_to) {
            $salaryRange = $this->job->salary_currency . $this->job->salary_from . ' - ' . 
                          $this->job->salary_currency . $this->job->salary_to . '/' . 
                          $this->job->getSalaryPeriod('salary_period');
        }

        // Prepare data for template
        $data = [
            'USER_NAME' => $this->user->name,
            'JOB_TITLE' => $this->job->title,
            'COMPANY_NAME' => $company->name,
            'JOB_LOCATION' => $this->job->getCity('city') . ', ' . $this->job->getCountry('country'),
            'JOB_TYPE' => $this->job->getJobType('job_type'),
            'SALARY_RANGE' => $salaryRange,
            'JOB_LINK' => route('job.detail', [$this->job->slug])
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('job-recommendation-jobseeker', $data);

        if (!$parsed) {
            // Fallback if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->to($this->user->email, $this->user->name)
            ->subject('New Job Opportunity: ' . $this->job->title)
            ->view('emails.job_recommendation_jobseeker')
            ->with([
                'user' => $this->user,
                'job' => $this->job,
                'company' => $company,
                'salaryRange' => $salaryRange
            ]);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->to($this->user->email, $this->user->name)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }
}
