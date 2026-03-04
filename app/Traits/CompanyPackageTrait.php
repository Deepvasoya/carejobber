<?php



namespace App\Traits;



use DB;
use Auth;
use Carbon\Carbon;
use App\Company;
use App\PaymentHistory;



trait CompanyPackageTrait

{



    public function addCompanyPackage($company, $package,$method='')

    {

        $now = Carbon::now();
        $startDate = $now->toDateTimeString();
        $endDate = $now->copy()->addDays($package->package_num_days)->toDateTimeString();

        $company->package_id = $package->id;

        $company->package_start_date = $now;

        $company->package_end_date = $now->copy()->addDays($package->package_num_days);

        $company->jobs_quota = $package->package_num_listings;

        $company->availed_jobs_quota = 0;

        $company->payment_method = $method;

        $company->update();
        
        // Log transaction in payment_history
        $this->logCompanyPayment($company, $package, $method, $startDate, $endDate, 'job', $package->package_num_listings, 0);

    }

    public function addCompanySearchPackage($company, $package,$method='')

    {

        $now = Carbon::now();
        $startDate = $now->toDateTimeString();
        $endDate = $now->copy()->addDays($package->package_num_days)->toDateTimeString();

        $company->cvs_package_id = $package->id;

        $company->cvs_package_start_date = $now;

        $company->cvs_package_end_date = $now->copy()->addDays($package->package_num_days);

        $company->cvs_quota = $package->package_num_listings;

        $company->availed_cvs_quota = 0;

        $company->payment_method = $method;

        $company->update();
        
        // Log transaction in payment_history
        $this->logCompanyPayment($company, $package, $method, $startDate, $endDate, 'cv_search', 0, $package->package_num_listings);

       

    }

    public function updateCompanyPackage($company, $package,$method='')

    {

        $package_end_date = $company->package_end_date;

        $current_end_date = Carbon::createFromDate(date('Y-m-d',strtotime($package_end_date)));



        $company->package_id = $package->id;

        $company->package_end_date = $current_end_date->addDays($package->package_num_days);

        $company->jobs_quota = ($company->jobs_quota - $company->availed_jobs_quota) + $package->package_num_listings;

        $company->availed_jobs_quota = 0;

        $company->payment_method = $method;

        $company->update();

    }

    public function updateCompanySearchPackage($company, $package,$method='')

    {

        $cvs_package_end_date = $company->cvs_package_end_date;

        $current_end_date = Carbon::createFromDate(Carbon::parse($cvs_package_end_date)->format('Y'), Carbon::parse($cvs_package_end_date)->format('m'), Carbon::parse($cvs_package_end_date)->format('d'));



        $company->cvs_package_id = $package->id;

        $company->cvs_package_end_date = $current_end_date->addDays($package->package_num_days);

        $company->cvs_quota = ($company->cvs_quota - $company->availed_cvs_quota) + $package->package_num_listings;

        $company->payment_method = $method;

        $company->availed_cvs_quota = 0;

        $company->update();

    }
    
    /**
     * Log company payment to payment_history table
     */
    protected function logCompanyPayment($company, $package, $paymentMethod, $startDate, $endDate, $packageType, $jobsQuota, $cvsQuota)
    {
        try {
            // Determine if this was admin assigned
            $assignedBy = null;
            if ($paymentMethod === 'Admin Assign' || $paymentMethod === '' || $paymentMethod === null) {
                $paymentMethod = 'Admin Assign';
                $assignedBy = Auth::guard('admin')->check() ? Auth::guard('admin')->id() : null;
            }
            
            PaymentHistory::create([
                'company_id' => $company->id,
                'user_id' => null,
                'user_type' => 'company',
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
            \Log::error('Failed to log company payment: ' . $e->getMessage());
        }
    }



}

