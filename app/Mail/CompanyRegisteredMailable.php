<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;

class CompanyRegisteredMailable extends Mailable
{

    use SerializesModels;

    public $company;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company)
    {
        $this->company = $company;
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
            'COMPANY_EMAIL' => $this->company->email,
            'COMPANY_LINK' => route('company.detail', $this->company->slug),
            'COMPANY_ADMIN_LINK' => route('edit.company', ['id' => $this->company->id])
        ];

        // Get parsed template
        $parsed = EmailTemplateService::parseTemplate('company-registered', $data);

        if (!$parsed) {
            // Fallback to old method if template not found
            return $this->to($recipientAddress, $recipientName)
                ->subject('Employer/Company "' . $this->company->name . '" has been registered on "' . config('app.name'))
                ->view('emails.company_registered_message')
                ->with([
                    'name' => $this->company->name,
                    'email' => $this->company->email,
                    'link' => route('company.detail', $this->company->slug),
                    'link_admin' => route('edit.company', ['id' => $this->company->id])
                ]);
        }

        return $this->from([
            'address' => $recipientAddress,
            'name' => $recipientName,
        ])
        ->to($recipientAddress, $recipientName)
        ->subject($parsed['subject'])
        ->html($parsed['body']);
    }


}
