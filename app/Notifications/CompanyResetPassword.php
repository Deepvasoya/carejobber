<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CompanyResetPassword extends Notification
{

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $resetLink = url('company/password/reset/' . $this->token . '?email=' . urlencode($notifiable->email));
        
        // Try to use custom email template
        $template = \App\EmailTemplate::where('slug', 'password-reset')->where('is_active', 1)->first();
        
        if ($template) {
            $data = [
                'NAME' => $notifiable->name,
                'RESET_LINK' => $resetLink,
                'SITE_NAME' => config('app.name'),
                'SITE_URL' => url('/')
            ];
            
            $parsed = $template->parseShortcodes($data);
            
            return (new MailMessage)
                ->subject($parsed['subject'])
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->view('emails.custom-html', ['content' => $parsed['body']]);
        }
        
        // Fallback to default
        return (new MailMessage)
                        ->subject('Company Password Reset')
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->line('You are receiving this email because we received a password reset request for your account.')
                        ->action('Reset Password', $resetLink)
                        ->line('If you did not request a password reset, no further action is required.');
    }

}
