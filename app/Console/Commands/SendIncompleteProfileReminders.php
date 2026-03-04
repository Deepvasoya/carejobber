<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Mail\IncompleteProfileReminderMailable;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use DB;

class SendIncompleteProfileReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:incomplete-profile-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly email reminders to users with incomplete profiles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting to send incomplete profile reminders...');

        // Get users with incomplete profiles
        // Only send to users who are active and have verified emails
        // And haven't received a reminder in the last 7 days
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $users = User::where('is_active', 1)
            ->whereNotNull('email_verified_at')
            ->where(function($query) use ($sevenDaysAgo) {
                $query->whereNull('incomplete_profile_reminder_sent_at')
                      ->orWhere('incomplete_profile_reminder_sent_at', '<', $sevenDaysAgo);
            })
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            $missingFields = $this->getMissingProfileFields($user);
            
            // Only send if profile is actually incomplete
            if (!empty($missingFields)) {
                try {
                    Mail::to($user->email)->send(new IncompleteProfileReminderMailable($user, $missingFields));
                    
                    // Update reminder sent date
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['incomplete_profile_reminder_sent_at' => Carbon::now()]);
                    
                    $sentCount++;
                } catch (\Exception $e) {
                    \Log::error('Failed to send incomplete profile reminder to user ' . $user->id . ': ' . $e->getMessage());
                    $skippedCount++;
                }
            } else {
                $skippedCount++;
            }
        }

        $this->info("Reminders sent: {$sentCount}, Skipped: {$skippedCount}");
        $this->info('Completed sending incomplete profile reminders.');

        return 0;
    }

    /**
     * Check which fields are missing from user profile
     *
     * @param User $user
     * @return array
     */
    private function getMissingProfileFields($user)
    {
        $missingFields = [];
        $translations = [
            'first_name' => __('First Name'),
            'last_name' => __('Last Name'),
            'phone' => __('Phone Number'),
            'city_id' => __('City'),
            'functional_area_id' => __('Functional Area'),
            'industry_id' => __('Industry'),
            'career_level_id' => __('Career Level'),
            'profile_summary' => __('Profile Summary'),
            'profile_experience' => __('Work Experience'),
            'profile_education' => __('Education'),
            'profile_skills' => __('Skills'),
            'profile_cv' => __('Resume/CV'),
        ];

        // Check basic required fields
        if (empty($user->first_name)) {
            $missingFields[] = $translations['first_name'];
        }
        if (empty($user->last_name)) {
            $missingFields[] = $translations['last_name'];
        }
        if (empty($user->phone) && empty($user->mobile_num)) {
            $missingFields[] = $translations['phone'];
        }
        if (empty($user->city_id)) {
            $missingFields[] = $translations['city_id'];
        }
        if (empty($user->functional_area_id)) {
            $missingFields[] = $translations['functional_area_id'];
        }
        if (empty($user->industry_id)) {
            $missingFields[] = $translations['industry_id'];
        }
        if (empty($user->career_level_id)) {
            $missingFields[] = $translations['career_level_id'];
        }

        // Check profile sections
        if ($user->profileSummary->isEmpty()) {
            $missingFields[] = $translations['profile_summary'];
        }
        if ($user->profileExperience->isEmpty()) {
            $missingFields[] = $translations['profile_experience'];
        }
        if ($user->profileEducation->isEmpty()) {
            $missingFields[] = $translations['profile_education'];
        }
        if ($user->profileSkills->isEmpty()) {
            $missingFields[] = $translations['profile_skills'];
        }
        if ($user->profileCvs->isEmpty()) {
            $missingFields[] = $translations['profile_cv'];
        }

        return $missingFields;
    }
}
