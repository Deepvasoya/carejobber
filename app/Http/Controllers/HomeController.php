<?php

namespace App\Http\Controllers;

use App\Traits\Cron;
use App\Job;
use Auth;
use App\FavouriteCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    use Cron;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->runCheckPackageValidity();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array();
        $data['appliedJobIds'] = array();
        $data['appliedJobs'] = array();
        if (Auth::check()) {
            $data['appliedJobIds'] = Auth::user()->getAppliedJobIdsArray();
            
            // Get applied jobs with details (only 3 for dashboard)
            if (!empty($data['appliedJobIds'])) {
                $data['appliedJobs'] = \App\JobApply::where('user_id', Auth::user()->id)
                    ->with(['job.company'])
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            }
        }
        
        $data['matchingJobs'] = array();
        $data['followers'] = array();
        if (Auth::check()) {
            $user = Auth::user();
            
            // Get user's followings (companies they follow)
            $data['followers'] = \App\FavouriteCompany::where('user_id', $user->id)
                ->with(['company' => function($query) {
                    $query->where('is_active', 1);
                }])
                ->take(6)
                ->get();
            
            // Get user's skills
            $userSkills = $user->getProfileSkills();
            $skillIds = $userSkills->pluck('job_skill_id')->toArray();
            
            // Build the query
            $query = Job::where('is_active', 1)
                ->where('expiry_date', '>', now())
                ->where(function($q) use ($user, $skillIds) {
                    // Match by functional area
                    if ($user->functional_area_id) {
                        $q->orWhere('functional_area_id', $user->functional_area_id);
                    }
                    
                    // Match by industry through company
                    if ($user->industry_id) {
                        $q->orWhereHas('company', function($subq) use ($user) {
                            $subq->where('industry_id', $user->industry_id);
                        });
                    }
                    
                    // Match by career level
                    if ($user->career_level_id) {
                        $q->orWhere('career_level_id', $user->career_level_id);
                    }
                    
                    // Match by job experience
                    if ($user->job_experience_id) {
                        $q->orWhere('job_experience_id', $user->job_experience_id);
                    }
                    
                    // Match by salary range (with 20% flexibility)
                    if ($user->expected_salary) {
                        $minSalary = $user->expected_salary * 0.8;
                        $maxSalary = $user->expected_salary * 1.2;
                        $q->orWhere(function($salaryQ) use ($minSalary, $maxSalary) {
                            $salaryQ->whereBetween('salary_from', [$minSalary, $maxSalary])
                                   ->orWhereBetween('salary_to', [$minSalary, $maxSalary]);
                        });
                    }
                    
                    // Match by skills
                    if (!empty($skillIds)) {
                        $q->orWhereHas('jobSkills', function($skillQ) use ($skillIds) {
                            $skillQ->whereIn('job_skill_id', $skillIds);
                        });
                    }
                });
            
            // Order by number of matching criteria and featured status
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('created_at', 'desc');
            
            $data['matchingJobs'] = $query->take(3)->get();
        }
        
        return view('home', $data);
    }

}
