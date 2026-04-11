<?php

namespace App\Listeners;

use Mail;
use App\Events\UserRegistered;
use App\Mail\CandidateRecommendationEmployerMailable;
use App\Job;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendCandidateRecommendationsListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $user = $event->user;

        // Only send recommendations if user has functional area set
        if (!$user->functional_area_id) {
            return;
        }

        // Find matching active jobs based on functional area and career level
        $matchingJobs = Job::where('is_active', 1)
            ->where('is_draft', 0)
            ->whereNotNull('functional_area_id')
            ->where('functional_area_id', $user->functional_area_id)
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            });

        // Also match career level if user has it set
        if ($user->career_level_id) {
            $matchingJobs->where('career_level_id', $user->career_level_id);
        }

        // Get recent jobs (posted in last 30 days) to avoid overwhelming employers
        // Limit to 5 most recent matching jobs to avoid sending too many emails
        $matchingJobs = $matchingJobs->where('created_at', '>=', now()->subDays(30))
                                     ->orderBy('created_at', 'desc')
                                     ->limit(5)
                                     ->get();

        // Send recommendation emails to companies with matching jobs
        foreach ($matchingJobs as $job) {
            try {
                $company = $job->getCompany();
                
                if (!$company || !$company->email) {
                    continue;
                }

                Mail::send(new CandidateRecommendationEmployerMailable($user, $job));
                
                Log::info('Candidate recommendation sent', [
                    'user_id' => $user->id,
                    'job_id' => $job->id,
                    'company_id' => $company->id,
                    'user_name' => $user->name,
                    'company_email' => $company->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send candidate recommendation', [
                    'user_id' => $user->id,
                    'job_id' => $job->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
