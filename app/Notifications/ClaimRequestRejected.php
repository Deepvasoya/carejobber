<?php

namespace App\Notifications;

use App\CompanyClaimRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ClaimRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public CompanyClaimRequest $claimRequest;

    public function __construct(CompanyClaimRequest $claimRequest)
    {
        $this->claimRequest = $claimRequest;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $reason = $this->claimRequest->admin_notes
            ?: 'No specific reason was provided. Please contact support for more information.';

        return (new MailMessage)
            ->subject('Your Company Claim Request Has Been Reviewed')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your request to claim the company profile "' . $this->claimRequest->company->name . '" has been reviewed.')
            ->line('Unfortunately, your claim request was not approved at this time.')
            ->line('Reason: ' . $reason)
            ->line('If you believe this was a mistake, please contact our support team.')
            ->line('Thank you for your understanding!');
    }
}
