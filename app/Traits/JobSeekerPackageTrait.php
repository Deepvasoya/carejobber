<?php

namespace App\Traits;

use DB;
use Auth;
use Carbon\Carbon;
use App\User;
use App\PaymentHistory;

trait JobSeekerPackageTrait
{

    public function addJobSeekerPackage($user, $package, $paymentMethod = null)    
{
    $now = Carbon::now();
    $startDate = $now->toDateTimeString();
    $endDate = $now->copy()->addDays($package->package_num_days)->toDateTimeString();
    
    if ($package->id == 9) {
        // For featured package
        $user->featured_package_start_at = $startDate;
        $user->featured_package_end_at = $endDate;
        $user->is_featured = 1;
        $user->package_id = $package->id;
        
        // Store payment method if provided
        if ($paymentMethod !== null && \Schema::hasColumn('users', 'payment_method')) {
            $user->payment_method = $paymentMethod;
        }
        
        $user->update();
        
        // Log transaction in payment_history
        $this->logJobseekerPayment($user, $package, $paymentMethod, $startDate, $endDate, 0, 0);
        
    } else {
        // For other packages
        $user->package_id = $package->id;
        $user->package_start_date = $now;
        $user->package_end_date = $now->copy()->addDays($package->package_num_days);
        $user->jobs_quota = $package->package_num_listings;
        $user->availed_jobs_quota = 0;
        
        // Store payment method if provided
        if ($paymentMethod !== null && \Schema::hasColumn('users', 'payment_method')) {
            $user->payment_method = $paymentMethod;
        }
        
        $user->update();
        
        // Log transaction in payment_history
        $this->logJobseekerPayment($user, $package, $paymentMethod, $startDate, $endDate, $package->package_num_listings, 0);
    }
}

    /**
     * Log jobseeker payment to payment_history table
     */
    protected function logJobseekerPayment($user, $package, $paymentMethod, $startDate, $endDate, $jobsQuota, $cvsQuota)
    {
        try {
            // Determine if this was admin assigned
            $assignedBy = null;
            if ($paymentMethod === 'Admin Assign' || $paymentMethod === null) {
                $paymentMethod = 'Admin Assign';
                $assignedBy = Auth::guard('admin')->check() ? Auth::guard('admin')->id() : null;
            }
            
            // Determine package type
            $packageType = 'job_seeker';
            if ($package->id == 9 || $package->package_for == 'make_featured') {
                $packageType = 'featured_profile';
            }
            
            PaymentHistory::create([
                'company_id' => null,
                'user_id' => $user->id,
                'user_type' => 'jobseeker',
                'package_id' => $package->id,
                'package_type' => $packageType,
                'package_title' => $package->package_title,
                'package_price' => $package->package_price,
                'payment_method' => $paymentMethod,
                'assigned_by' => $assignedBy,
                'transaction_id' => null, // Will be updated by payment gateway if needed
                'package_start_date' => $startDate,
                'package_end_date' => $endDate,
                'jobs_quota' => $jobsQuota,
                'cvs_quota' => $cvsQuota,
                'payment_status' => 'completed'
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the package assignment
            \Log::error('Failed to log jobseeker payment: ' . $e->getMessage());
        }
    }

    



public function updateJobSeekerPackage($user, $package)
{
    $now = Carbon::now();

    if ($package->is_featured) { // Dynamically check if the package is featured
        $user->package_start_date = $now;
        $user->featured_package_end_at = $now->addDays($package->package_num_days);
        $user->is_featured = 1;
    } else {
        $package_end_date = $user->package_end_date;
        $current_end_date = Carbon::parse($package_end_date);

        $user->package_id = $package->id;
        $user->package_end_date = $current_end_date->addDays($package->package_num_days);
        $user->jobs_quota = ($user->jobs_quota - $user->availed_jobs_quota) + $package->package_num_listings;
        $user->availed_jobs_quota = 0;
    }

    $user->update();
}


}
