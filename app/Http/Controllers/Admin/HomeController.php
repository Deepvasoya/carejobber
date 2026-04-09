<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Company;
use App\Job;
use App\JobApply;
use App\PaymentHistory;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard. Data is loaded only for sections the admin has permission to see.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $today = Carbon::now();

        // Build months array for charts (used when payments or users permission exists)
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->format('Y-m'));
        }
        $revenueChartLabels = $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values()->toArray();

        // User-related stats (site_users.view)
        $totalActiveUsers = 0;
        $totalVerifiedUsers = 0;
        $totalTodaysUsers = 0;
        $totalUsersAll = 0;
        $recentUsers = collect();
        $newUsersChartData = array_fill(0, 12, 0);
        if ($admin && $admin->hasPermission('site_users.view')) {
            $totalActiveUsers = User::where('is_active', 1)->count();
            $totalVerifiedUsers = User::where('verified', 1)->count();
            $totalTodaysUsers = User::where('created_at', 'like', $today->toDateString() . '%')->count();
            $totalUsersAll = User::count();
            $recentUsers = User::orderBy('id', 'DESC')->take(25)->get();
            $newUsersByMonth = User::where('created_at', '>=', Carbon::now()->subMonths(12))
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();
            $newUsersChartData = $months->map(fn ($m) => (int) ($newUsersByMonth[$m] ?? 0))->values()->toArray();
        }

        // Payment-related (payments.view)
        $totalRevenue = 0;
        $totalPaymentTransactions = 0;
        $revenueChartData = array_fill(0, 12, 0);
        if ($admin && $admin->hasPermission('payments.view')) {
            $totalRevenue = PaymentHistory::where('payment_status', 'completed')->sum('package_price');
            $totalPaymentTransactions = PaymentHistory::where('payment_status', 'completed')->count();
            $revenueByMonth = PaymentHistory::where('payment_status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(package_price) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();
            $revenueChartData = $months->map(fn ($m) => (float) ($revenueByMonth[$m] ?? 0))->values()->toArray();
        }

        // Job-related (jobs.view)
        $totalActiveJobs = 0;
        $totalFeaturedJobs = 0;
        $totalTodaysJobs = 0;
        $totalJobsAll = 0;
        $totalInactiveJobs = 0;
        $totalJobApplications = 0;
        $recentJobs = collect();
        if ($admin && $admin->hasPermission('jobs.view')) {
            $totalActiveJobs = Job::where('is_active', 1)->count();
            $totalFeaturedJobs = Job::where('is_featured', 1)->count();
            $totalTodaysJobs = Job::where('created_at', 'like', $today->toDateString() . '%')->count();
            $totalJobsAll = Job::count();
            $totalInactiveJobs = Job::where('is_active', 0)->count();
            $totalJobApplications = JobApply::count();
            $recentJobs = Job::with('company')->orderBy('id', 'DESC')->take(25)->get();
        }

        // Company-related (companies.view)
        $totalActiveCompanies = 0;
        $totalInactiveCompanies = 0;
        $totalTodaysCompanies = 0;
        $totalCompaniesAll = 0;
        $activeCompanies = collect();
        $inActiveCompanies = collect();
        if ($admin && $admin->hasPermission('companies.view')) {
            $totalActiveCompanies = Company::where('is_active', 1)->count();
            $totalInactiveCompanies = Company::where('is_active', 0)->count();
            $totalTodaysCompanies = Company::where('created_at', 'like', $today->toDateString() . '%')->count();
            $totalCompaniesAll = Company::count();
            $activeCompanies = Company::where('is_active', 1)->get();
            $documents = [
                'incorporation_or_formation_certificate',
                'valid_tax_clearance',
                'proof_of_address',
                'other_supporting_documents'
            ];
            $inActiveCompanies = Company::where('is_active', 0)
                ->where(function ($query) use ($documents) {
                    foreach ($documents as $document) {
                        $query->orWhereNotNull($document);
                    }
                })
                ->get();
        }

        return view('admin.home')
            ->with('totalActiveUsers', $totalActiveUsers)
            ->with('totalVerifiedUsers', $totalVerifiedUsers)
            ->with('totalTodaysUsers', $totalTodaysUsers)
            ->with('totalUsersAll', $totalUsersAll)
            ->with('totalTodaysCompanies', $totalTodaysCompanies)
            ->with('recentUsers', $recentUsers)
            ->with('totalActiveJobs', $totalActiveJobs)
            ->with('totalFeaturedJobs', $totalFeaturedJobs)
            ->with('totalTodaysJobs', $totalTodaysJobs)
            ->with('totalJobsAll', $totalJobsAll)
            ->with('totalInactiveJobs', $totalInactiveJobs)
            ->with('totalJobApplications', $totalJobApplications)
            ->with('totalActiveCompanies', $totalActiveCompanies)
            ->with('totalInactiveCompanies', $totalInactiveCompanies)
            ->with('totalCompaniesAll', $totalCompaniesAll)
            ->with('activeCompanies', $activeCompanies)
            ->with('inActiveCompanies', $inActiveCompanies)
            ->with('recentJobs', $recentJobs)
            ->with('revenueChartLabels', $revenueChartLabels)
            ->with('revenueChartData', $revenueChartData)
            ->with('newUsersChartData', $newUsersChartData)
            ->with('totalRevenue', $totalRevenue)
            ->with('totalPaymentTransactions', $totalPaymentTransactions);
    }

}
