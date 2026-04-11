<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\Job;
use App\Services\EmailTemplateService;

class CandidateRecommendationEmployerMailable extends Mailable
{
    use SerializesModels;

    public $user;
    public $job;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Job $job)
    {
        $this->user = $user;
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

        // Prepare user location
        $userLocation = '';
        if ($this->user->getCity('city')) {
            $userLocation = $this->user->getCity('city');
            if ($this->user->getCountry('country')) {
                $userLocation .= ', ' . $this->user->getCountry('country');
            }
        } elseif ($this->user->getCountry('country')) {
            $userLocation = $this->user->getCountry('country');
        } else {
            $userLocation = 'Not specified';
        }

        // Prepare data for template
        $data = [
            'COMPANY_NAME' => $company->name,
            'USER_NAME' => $this->user->name,
            'FUNCTIONAL_AREA' => $this->user->getFunctionalArea ? $this->user->getFunctionalArea->functional_area : 'Not specified',
            'CAREER_LEVEL' => $this->user->getCareerLevel ? $this->user->getCareerLevel->career_level : 'Not specified',
            'USER_LOCATION' => $userLocation,
            'JOB_TITLE' => $this->job->title,
            'JOB_POSTED_DATE' => $this->job->created_at->format('M d, Y'),
            'USER_PROFILE_LINK' => route('user.profile', $this->user->id)
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('candidate-recommendation-employer', $data);

        if (!$parsed) {
            // Fallback if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->to($company->email, $company->name)
            ->subject('New Candidate Match: ' . $this->user->name . ' for ' . $this->job->title)
            ->view('emails.candidate_recommendation_employer')
            ->with([
                'user' => $this->user,
                'job' => $this->job,
                'company' => $company,
                'userLocation' => $userLocation
            ]);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->to($company->email, $company->name)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }
}
