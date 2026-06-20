<?php

namespace App\Notifications;

use App\CompanyClaimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ClaimRequestApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public CompanyClaimRequest $claimRequest;
    public string $passwordSetupUrl;

    public function __construct(CompanyClaimRequest $claimRequest, string $passwordSetupUrl)
    {
        $this->claimRequest = $claimRequest;
        $this->passwordSetupUrl = $passwordSetupUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Company Claim Request Has Been Approved')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your request to claim the company profile "' . $this->claimRequest->company->name . '" has been approved.')
            ->line('You can now manage this company profile by logging in with your email address.')
            ->action('Set Up Your Company Account Password', $this->passwordSetupUrl)
            ->line('Once you set your password, you can log in to manage job postings and company information.')
            ->line('Thank you for using our platform!');
    }
}
