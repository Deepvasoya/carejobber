<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('canPostJob', function ($user) {
            if (!$user) {
                return false;
            }
            return method_exists($user, 'getRemainingJobsQuota') && $user->getRemainingJobsQuota() > 0;
        });

        Gate::define('view-full-resume', function ($company, $jobSeeker) {
            if (!$company || !$jobSeeker) {
                return false;
            }
            
            // Check if unlocked via Stripe or credits
            if (\App\Models\ResumeUnlock::isUnlockedBy($jobSeeker->id, $company->id)) {
                return true;
            }

            // Check if unlocked via old credit system (UnlockedUser)
            $unlock = \App\UnlockedUser::where('company_id', $company->id)->first();
            if ($unlock && $unlock->unlocked_users_ids) {
                $unlockedIds = explode(',', $unlock->unlocked_users_ids);
                if (in_array((string) $jobSeeker->id, $unlockedIds, true)) {
                    return true;
                }
            }

            // Check if company has active CV package with remaining quota
            if ($company->cvs_package_id 
                && $company->cvs_package_end_date >= date('Y-m-d') 
                && ($company->cvs_quota - $company->availed_cvs_quota) > 0) {
                return true;
            }

            return false;
        });
    }

}
