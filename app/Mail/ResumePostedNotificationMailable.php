<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\Company;
use App\Services\EmailTemplateService;

class ResumePostedNotificationMailable extends Mailable
{
    use SerializesModels;

    public $user;
    public $company;
    public $profileUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Company $company)
    {
        $this->user = $user;
        $this->company = $company;
        $this->profileUrl = route('user.profile', $user->id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $recipientAddress = config('mail.recieve_to.address');
        $recipientName = config('mail.recieve_to.name');

        // Prepare data for template
        $data = [
            'COMPANY_NAME' => $this->company->name,
            'USER_NAME' => $this->user->name,
            'FUNCTIONAL_AREA' => $this->user->getFunctionalArea->functional_area ?? 'N/A',
            'CAREER_LEVEL' => $this->user->getCareerLevel->career_level ?? 'N/A',
            'LOCATION' => ($this->user->getCity ? $this->user->getCity->city . ', ' : '') . ($this->user->getCountry ? $this->user->getCountry->country : ''),
            'PROFILE_URL' => $this->profileUrl
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('resume-posted', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->from([
                'address' => $recipientAddress,
                'name' => $recipientName,
            ])
            ->subject(__('New Resume Posted in Your Industry') . ' - ' . config('app.name'))
            ->view('emails.resume_posted_notification')
            ->with([
                'user' => $this->user,
                'company' => $this->company,
                'profileUrl' => $this->profileUrl
            ]);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->to($this->company->email, $this->company->name)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }
}
