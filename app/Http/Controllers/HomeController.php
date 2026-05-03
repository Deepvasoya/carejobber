<?php

namespace App\Http\Controllers;

use App\Traits\Cron;
use App\Job;
use App\Services\ProfileJobTitleMatching;
use App\User;
use Auth;
use Illuminate\Database\Eloquent\Builder;

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

            // Get applied jobs with details (only 4 for dashboard)
            if (!empty($data['appliedJobIds'])) {
                $data['appliedJobs'] = \App\JobApply::where('user_id', Auth::user()->id)
                    ->with(['job.company'])
                    ->orderBy('created_at', 'desc')
                    ->take(4)
                    ->get();
            }
        }

        $data['matchingJobs'] = array();
        $data['followers'] = array();
        if (Auth::check()) {
            $user = Auth::user();

            // Get user's followings (companies they follow)
            $data['followers'] = \App\FavouriteCompany::where('user_id', $user->id)
                ->with(['company' => function ($query) {
                    $query->where('is_active', 1);
                }])
                ->take(4)
                ->get();

            $recQuery = $this->recommendedJobsQuery($user);
            $data['matchingJobs'] = (clone $recQuery)
                ->with('company')
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
        }

        return view('home', $data);
    }

    /**
     * Full list of profile-matched recommended jobs (up to 20).
     */
    public function recommendedJobs()
    {
        $user = Auth::user();
        $recQuery = $this->recommendedJobsQuery($user);
        $matchingJobs = (clone $recQuery)
            ->with('company')
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('recommended_jobs', compact('matchingJobs'));
    }

    /**
     * Base query for jobs recommended from the seeker's profile (same rules as dashboard).
     */
    protected function recommendedJobsQuery(User $user): Builder
    {
        $userSkills = $user->getProfileSkills();
        $skillIds = $userSkills->pluck('job_skill_id')->toArray();

        $professionPhrases = ProfileJobTitleMatching::jobSeekerProfessionPhrases($user);

        $query = Job::where('is_active', 1)
            ->where('expiry_date', '>', now())
            ->where(function ($q) use ($user, $skillIds, $professionPhrases) {
                $added = false;
                foreach ($professionPhrases as $phrase) {
                    $like = '%' . addcslashes($phrase, '%_\\') . '%';
                    $q->orWhere(function ($sub) use ($like) {
                        $sub->where('title', 'like', $like)
                            ->orWhere('search', 'like', $like);
                    });
                    $added = true;
                }

                if ($user->job_category_id) {
                    $q->orWhere('job_category_id', $user->job_category_id);
                    $added = true;
                }

                if ($user->career_level_id) {
                    $q->orWhere('career_level_id', $user->career_level_id);
                    $added = true;
                }

                if ($user->job_experience_id) {
                    $q->orWhere('job_experience_id', $user->job_experience_id);
                    $added = true;
                }

                if ($user->expected_salary) {
                    $minSalary = $user->expected_salary * 0.8;
                    $maxSalary = $user->expected_salary * 1.2;
                    $q->orWhere(function ($salaryQ) use ($minSalary, $maxSalary) {
                        $salaryQ->whereBetween('salary_from', [$minSalary, $maxSalary])
                            ->orWhereBetween('salary_to', [$minSalary, $maxSalary]);
                    });
                    $added = true;
                }

                if (!empty($skillIds)) {
                    $q->orWhereHas('jobSkills', function ($skillQ) use ($skillIds) {
                        $skillQ->whereIn('job_skill_id', $skillIds);
                    });
                    $added = true;
                }

                if (!$added) {
                    $q->whereRaw('0 = 1');
                }
            });

        return $query;
    }

}
