<?php

namespace App\Services;

use App\EmailTemplate;
use App\SiteSetting;
use Mail;

class EmailTemplateService
{
    /**
     * Send email using template
     */
    public static function send($templateSlug, $toEmail, $toName, $data = [], $replyTo = null)
    {
        $template = EmailTemplate::where('slug', $templateSlug)
                                  ->where('is_active', 1)
                                  ->first();
        
        if (!$template) {
            \Log::error("Email template not found or inactive: {$templateSlug}");
            return false;
        }

        // Add site settings to data
        $siteSetting = SiteSetting::first();
        $data['SITE_NAME'] = $siteSetting->site_name ?? config('app.name');
        $data['SITE_URL'] = url('/');

        // Parse shortcodes
        $parsed = $template->parseShortcodes($data);

        try {
            $fromAddress = config('mail.recieve_to.address', config('mail.from.address'));
            $fromName = config('mail.recieve_to.name', config('mail.from.name'));

            Mail::send([], [], function ($message) use ($toEmail, $toName, $parsed, $fromAddress, $fromName, $replyTo) {
                $message->to($toEmail, $toName)
                        ->from($fromAddress, $fromName)
                        ->subject($parsed['subject'])
                        ->html($parsed['body']);
                
                if ($replyTo) {
                    $message->replyTo($replyTo['email'], $replyTo['name'] ?? '');
                }
            });

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get template by slug
     */
    public static function getTemplate($slug)
    {
        return EmailTemplate::where('slug', $slug)->first();
    }

    /**
     * Parse template with data
     */
    public static function parseTemplate($templateSlug, $data = [])
    {
        $template = self::getTemplate($templateSlug);
        
        if (!$template) {
            return null;
        }

        // Add site settings to data
        $siteSetting = SiteSetting::first();
        $data['SITE_NAME'] = $siteSetting->site_name ?? config('app.name');
        $data['SITE_URL'] = url('/');

        return $template->parseShortcodes($data);
    }
}
