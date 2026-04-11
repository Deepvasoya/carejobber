<?php

namespace App\Listeners;

use Mail;
use App\Events\JobPosted;
use App\Mail\JobRecommendationJobSeekerMailable;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendJobRecommendationsListener implements ShouldQueue
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
     * @param  JobPosted  $event
     * @return void
     */
    public function handle(JobPosted $event)
    {
        $job = $event->job;

        // Only send recommendations for active jobs
        if (!$job->is_active) {
            return;
        }

        // Find matching job seekers based on functional area and career level
        $matchingUsers = User::where('is_active', 1)
            ->where('verified', 1)
            ->whereNotNull('functional_area_id')
            ->where('functional_area_id', $job->functional_area_id);

        // Also match career level if specified
        if ($job->career_level_id) {
            $matchingUsers->where('career_level_id', $job->career_level_id);
        }

        $matchingUsers = $matchingUsers->get();

        // Send recommendation emails to matching users
        foreach ($matchingUsers as $user) {
            try {
                // Check if user has job match notifications enabled
                $preferences = \DB::table('user_notification_preferences')
                    ->where('user_id', $user->id)
                    ->first();
                
                if ($preferences && !$preferences->new_job_matches) {
                    continue;
                }

                Mail::send(new JobRecommendationJobSeekerMailable($job, $user));
                
                Log::info('Job recommendation sent', [
                    'job_id' => $job->id,
                    'user_id' => $user->id,
                    'job_title' => $job->title,
                    'user_email' => $user->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send job recommendation', [
                    'job_id' => $job->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
