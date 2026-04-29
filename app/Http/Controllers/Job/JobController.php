<?php

namespace App\Http\Controllers\Job;

use Auth;
use DB;
use Input;
use Redirect;
use Carbon\Carbon;
use App\Job;
use App\JobApply;
use App\JobQuestionAnswer;
use App\FavouriteJob;
use App\Company;
use App\JobSkill;
use App\JobSkillManager;
use App\Country;
use App\CountryDetail;
use App\CareerLevel;
use App\FunctionalArea;
use App\JobType;
use App\JobShift;
use App\Gender;
use App\Seo;
use App\JobExperience;
use App\DegreeLevel;
use App\ProfileCv;
use App\External_applied;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\Http\Requests\JobFormRequest;
use App\Http\Requests\Front\ApplyJobFormRequest;
use App\Http\Controllers\Controller;
use App\Traits\FetchJobs;
use App\Events\JobApplied;
use Mail;
use App\Mail\JobApplicantStatusMailable;
use App\Models\JobApplication;
use App\Services\ProgrammaticSeoService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobController extends Controller
{

    //use Skills;
    use FetchJobs;

    private $functionalAreas = '';
    private $countries = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['jobsBySearch', 'jobDetail', 'setStatus','jobApplyExt','postJobApply']]);

        $this->functionalAreas = DataArrayHelper::langFunctionalAreasArray();
        $this->countries = DataArrayHelper::langCountriesArray();
    }

    public function jobsBySearch(Request $request)
    {
        $search = $request->query('search', '');
        $job_titles = $request->query('job_title', array());
        $company_ids = $request->query('company_id', array());
        $industry_ids = $request->query('industry_id', array());
        $job_skill_ids = $request->query('job_skill_id', array());
        $functional_area_ids = $request->query('functional_area_id', array());
        $functionalAreaName = $request->input('functional_area_name');
        
        // Handle text-based location search
        $locationSearch = $request->query('location_search', '');
        $country_ids = $request->query('country_id', array());
        $state_ids = $request->query('state_id', array());
        $city_ids = $request->query('city_id', array());
        
        // If location_search is provided, find matching cities and states.
        // jobs.city_id / jobs.state_id store logical city_id / state_id (same as dropdowns), not PKs.
        // Autocomplete uses "City, State" labels; see MiscHelper::locationSearchToCityStateIds().
        if (!empty($locationSearch)) {
            $locationTerm = trim($locationSearch);
            [$matchingCityIds, $matchingStateIds] = MiscHelper::locationSearchToCityStateIds($locationTerm);

            if (!empty($matchingCityIds)) {
                $city_ids = array_merge((array) $city_ids, $matchingCityIds);
            }
            if (!empty($matchingStateIds)) {
                $state_ids = array_merge((array) $state_ids, $matchingStateIds);
            }
            $city_ids = array_values(array_unique(array_map('intval', (array) $city_ids)));
            $state_ids = array_values(array_unique(array_map('intval', (array) $state_ids)));
        }
        $is_freelance = $request->query('is_freelance', array());
        $career_level_ids = $request->query('career_level_id', array());
        $job_type_ids = $request->query('job_type_id', array());
        $job_shift_ids = $request->query('job_shift_id', array());
        $gender_ids = $request->query('gender_id', array());
        $degree_level_ids = $request->query('degree_level_id', array());
        $job_experience_ids = $request->query('job_experience_id', array());
        $salary_from = $request->query('salary_from', '');
        $salary_to = $request->query('salary_to', '');
        $salary_currency = $request->query('salary_currency', '');
        $is_featured = $request->query('is_featured', 2);
        $order_by = $request->query('order_by', 'id');        
        $limit = 24;
        $feature_jobs = Job::wherePromotionFeaturedActive()->notExpire()->get();
        

        
        $jobs = $this->fetchJobs($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, $order_by, $limit);
        

        /*         * ************************************************** */

        $jobTitlesArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.title');

        /*         * ************************************************* */

        $jobIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.id');

        /*         * ************************************************** */

        $skillIdsArray = $this->fetchSkillIdsArray($jobIdsArray);

        /*         * ************************************************** */

        $countryIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.country_id');

        /*         * ************************************************** */

        $stateIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.state_id');

        /*         * ************************************************** */

        $cityIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.city_id');

        /*         * ************************************************** */

        $companyIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.company_id');

        /*         * ************************************************** */

        $industryIdsArray = $this->fetchIndustryIdsArray($jobIdsArray);

        /*         * ************************************************** */


        /*         * ************************************************** */

        $functionalAreaIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.functional_area_id');

        /*         * ************************************************** */

        $careerLevelIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.career_level_id');

        /*         * ************************************************** */

        $jobTypeIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.job_type_id');

        /*         * ************************************************** */

        $jobShiftIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.job_shift_id');

        /*         * ************************************************** */

        $genderIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.gender_id');

        /*         * ************************************************** */

        $degreeLevelIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.degree_level_id');

        /*         * ************************************************** */

        $jobExperienceIdsArray = $this->fetchIdsArray($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, 'jobs.job_experience_id');

        /*         * ************************************************** */

        $seoArray = $this->getSEO($functional_area_ids, $country_ids, $state_ids, $city_ids, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids);

        /*         * ************************************************** */

        $currencies = DataArrayHelper::currenciesArray();

        /*         * ************************************************** */

        $seo = Seo::where('seo.page_title', 'like', 'jobs')->first();
        $facilityTypes = DataArrayHelper::langIndustriesArray();
        return view('job.list')
                        ->with('functionalAreas', $this->functionalAreas)
                        ->with('countries', $this->countries)
                        ->with('currencies', array_unique($currencies))
                        ->with('jobs', $jobs)
                        ->with('jobTitlesArray', $jobTitlesArray)
                        ->with('skillIdsArray', $skillIdsArray)
                        ->with('countryIdsArray', $countryIdsArray)
                        ->with('stateIdsArray', $stateIdsArray)
                        ->with('cityIdsArray', $cityIdsArray)
                        ->with('companyIdsArray', $companyIdsArray)
                        ->with('industryIdsArray', $industryIdsArray)
                        ->with('functionalAreaIdsArray', $functionalAreaIdsArray)
                        ->with('careerLevelIdsArray', $careerLevelIdsArray)
                        ->with('jobTypeIdsArray', $jobTypeIdsArray)
                        ->with('jobShiftIdsArray', $jobShiftIdsArray)
                        ->with('genderIdsArray', $genderIdsArray)
                        ->with('degreeLevelIdsArray', $degreeLevelIdsArray)
                        ->with('jobExperienceIdsArray', $jobExperienceIdsArray)
                        ->with('feature_jobs', $feature_jobs)
                        ->with('facilityTypes', $facilityTypes)
                        ->with('seo', $seo);                        
    }

    public function jobDetail(Request $request, $job_slug)
    {
        $job = Job::where('slug', 'like', $job_slug)->where('is_draft', 0)->firstOrFail();
        
        // Increment view count if num_views column exists
        if (\Schema::hasColumn('jobs', 'num_views')) {
            $job->increment('num_views');
        }
        
        // Get related jobs based on multiple criteria
        $relatedJobs = Job::where('id', '!=', $job->id)
            ->where(function($query) use ($job) {
                // Match by functional area
                $query->orWhere('functional_area_id', $job->functional_area_id);
                
                // Match by skills
                $jobSkills = $job->getJobSkillsArray();
                if (!empty($jobSkills)) {
                    $query->orWhereHas('jobSkills', function($q) use ($jobSkills) {
                        $q->whereIn('job_skill_id', $jobSkills);
                    });
                }
                
                // Match by career level
                $query->orWhere('career_level_id', $job->career_level_id);
                
                // Match by job type
                $query->orWhere('job_type_id', $job->job_type_id);
                
                // Match by location
                $query->orWhere(function($q) use ($job) {
                    $q->where('country_id', $job->country_id)
                      ->orWhere('state_id', $job->state_id)
                      ->orWhere('city_id', $job->city_id);
                });
            })
            ->where('is_active', 1)
            ->where('expiry_date', '>', Carbon::now())
            ->orderBy('is_urgent', 'desc')
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $seoArray = $this->getSEO((array) $job->functional_area_id, (array) $job->country_id, (array) $job->state_id, (array) $job->city_id, (array) $job->career_level_id, (array) $job->job_type_id, (array) $job->job_shift_id, (array) $job->gender_id, (array) $job->degree_level_id, (array) $job->job_experience_id);
        
        $seo = (object) array(
            'seo_title' => $job->title,
            'seo_description' => $seoArray['description'],
            'seo_keywords' => $seoArray['keywords'],
            'seo_other' => app(ProgrammaticSeoService::class)->jobPostingJsonLd($job)
        );
        
        return view('job.detail')
            ->with('job', $job)
            ->with('relatedJobs', $relatedJobs)
            ->with('seo', $seo);
    }


    public function setStatus(Request $request) {

      
        
        $applied = json_decode($request->applied, true);
        $shortlist = json_decode($request->shortlist, true);
        $hired = json_decode($request->hired, true);
        $rejected = json_decode($request->rejected, true);
        
        

        if($applied){
            JobApply::whereIn('id', $applied)->update(['status' => 'applied']);
        }
        if($shortlist){
            JobApply::whereIn('id', $shortlist)->update(['status' => 'shortlist']);
            $updatedJobApplies = JobApply::whereIn('id', $shortlist)->first();
            $job = Job::where('id', $updatedJobApplies->job_id)->first();
            Mail::send(new JobApplicantStatusMailable($job,$updatedJobApplies,'Short List'));
        }
        if($hired){
            $jobbb = JobApply::whereIn('id', $hired)->update(['status' => 'hired']);
            $updatedJobApplies = JobApply::whereIn('id', $hired)->first();
            $job = Job::where('id', $updatedJobApplies->job_id)->first();
            Mail::send(new JobApplicantStatusMailable($job,$updatedJobApplies,'Approved'));
        }
        if($rejected){
            JobApply::whereIn('id', $rejected)->update(['status' => 'rejected']);
            $updatedJobApplies = JobApply::whereIn('id', $rejected)->first();
            $job = Job::where('id', $updatedJobApplies->job_id)->first();
            Mail::send(new JobApplicantStatusMailable($job,$updatedJobApplies,'Declined'));
        }


        
        
        
        

         
    }



    /*     * ************************************************** */

    public function addToFavouriteJob(Request $request, $job_slug)
    {
        $data['job_slug'] = $job_slug;
        $data['user_id'] = Auth::user()->id;
        $data_save = FavouriteJob::create($data);
        flash(__('Job has been added in favorites list'))->success();

        return $this->redirectAfterFavouriteChange($request, $job_slug);
    }

    public function removeFromFavouriteJob(Request $request, $job_slug)
    {
        $user_id = Auth::user()->id;
        FavouriteJob::where('job_slug', 'like', $job_slug)->where('user_id', $user_id)->delete();

        flash(__('Job has been removed from favorites list'))->success();

        return $this->redirectAfterFavouriteChange($request, $job_slug);
    }

    /**
     * Stay on job search/list when favouriting from there; otherwise open the job detail.
     */
    private function redirectAfterFavouriteChange(Request $request, string $job_slug)
    {
        $referer = $request->headers->get('referer') ?? '';
        $base = rtrim((string) config('app.url'), '/');
        if ($referer !== '' && str_starts_with($referer, $base)) {
            return \Redirect::to($referer);
        }

        return \Redirect::route('job.detail', $job_slug);
    }
    
    public function jobApplyExt(Request $request, $job_slug)
    {
        $user = Auth::user();
        $job = Job::where('slug', 'like', $job_slug)->first();

        return view('job.job_apply_form')
                        ->with('job_slug', $job_slug)
                        ->with('job', $job);
    }

    /**
     * HTML fragment for the apply modal on the job search list (loaded via AJAX).
     */
    public function applyModalFragment(Request $request, string $slug)
    {
        $job = Job::where('slug', 'like', $slug)->first();

        if (!$job) {
            return response()->json(['success' => false, 'message' => __('Job not found.')], 404);
        }

        if ($job->isJobExpired()) {
            return response()->json(['success' => false, 'message' => __('Job is expired.')], 422);
        }

        $user = Auth::user();
        $already = JobApply::where('job_id', $job->id)->where('user_id', $user->id)->exists();
        if ($already) {
            return response()->json(['success' => false, 'message' => __('You have already applied for this job.')], 422);
        }

        $html = view('job.partials.apply_modal_list_fragment', ['job' => $job])->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function postJobApply(Request $request, $job_slug)
    {
        $wantsJson = $request->expectsJson() || $request->ajax();

        $job = Job::where('slug', 'like', $job_slug)->first();

        if (!$job) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => __('Job not found')], 404);
            }
            flash(__('Job not found'))->error();
            return redirect()->back();
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => __('Please login to apply for this job')], 401);
            }
            flash(__('Please login to apply for this job'))->error();
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has already applied
        $existingApplication = \App\JobApply::where('job_id', $job->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingApplication) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => __('You have already applied for this job')], 422);
            }
            flash(__('You have already applied for this job'))->warning();
            return redirect()->route('job.detail', $job_slug);
        }

        // Create new job application
        $jobApply = new \App\JobApply();
        $jobApply->job_id = $job->id;
        $jobApply->user_id = $user->id;
        
        // Handle CV - either from existing profile CV or new upload
        if ($request->cv_id) {
            // User selected an existing CV from their profile
            $profileCv = \App\ProfileCv::find($request->cv_id);
            if ($profileCv && $profileCv->user_id == $user->id) {
                $jobApply->cv_id = $profileCv->id;
                $jobApply->resume_source = 'existing';
            } else {
                if ($wantsJson) {
                    return response()->json(['success' => false, 'message' => __('Invalid CV selected')], 422);
                }
                flash(__('Invalid CV selected'))->error();
                return redirect()->back()->withInput();
            }
        } elseif ($request->hasFile('cv')) {
            // User uploaded a new CV - save it to their profile first
            $resume = $request->file('cv');
            
            // Generate a unique name for the file
            $fileName = time() . '_' . $resume->getClientOriginalName();
            
            // Move the file to the public/cvs folder
            $resume->move(public_path('cvs'), $fileName);
            
            // Create a new ProfileCv record
            $profileCv = new \App\ProfileCv();
            $profileCv->user_id = $user->id;
            $profileCv->cv_file = $fileName;
            $profileCv->title = 'Application CV - ' . $job->title;
            $profileCv->is_default = 0;
            $profileCv->save();
            
            $jobApply->cv_id = $profileCv->id;
            $jobApply->resume_source = 'upload';
        } else {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => __('Please select or upload a CV')], 422);
            }
            flash(__('Please select or upload a CV'))->error();
            return redirect()->back()->withInput();
        }
        
        // Save cover letter if provided
        if ($request->cover_letter) {
            $jobApply->cover_letter = $request->cover_letter;
        }
        
        $jobApply->status = 'applied';
        $jobApply->save();

        // Save question answers if provided
        if ($request->has('question_answers')) {
            foreach ($request->input('question_answers') as $questionId => $answer) {
                if (!empty($answer)) {
                    $qa = new \App\JobQuestionAnswer();
                    $qa->job_question_id = $questionId;
                    $qa->job_apply_id = $jobApply->id;
                    $qa->answer = $answer;
                    $qa->save();
                }
            }
        }

        // Fire JobApplied event if it exists
        try {
            event(new \App\Events\JobApplied($job, $jobApply));
        } catch (\Exception $e) {
            // Event doesn't exist or failed, continue anyway
        }

        flash(__('You have successfully applied for this job'))->success();

        $externalApplyUrl = $job->getApplyActionUrl();
        if ($externalApplyUrl) {
            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => __('You have successfully applied for this job'),
                    'redirect_url' => $externalApplyUrl,
                ]);
            }

            return redirect()->away($externalApplyUrl);
        }

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => __('You have successfully applied for this job'),
            ]);
        }

        return redirect()->route('job.detail', $job_slug);
    }

    public function applyJob(Request $request, $job_slug)
    {
        $user = Auth::user();
        $job = Job::where('slug', 'like', $job_slug)->first();
        
        if ((bool)$user->is_active === false) {
            flash(__('Your account is inactive contact site admin to activate it'))->error();
            return \Redirect::route('job.detail', $job_slug);
            exit;
        }
        
        if ((bool) config('jobseeker.is_jobseeker_package_active')) {
            if (
                    ($user->jobs_quota <= $user->availed_jobs_quota) ||
                    ($user->package_end_date->lt(Carbon::now()))
            ) {
                flash(__('Please subscribe to package first'))->error();
                return \Redirect::route('home');
                exit;
            }
        }
        if ($user->isAppliedOnJob($job->id)) {
            flash(__('You have already applied for this job'))->success();
            return \Redirect::route('job.detail', $job_slug);
            exit;
        }
        
        

        $myCvs = ProfileCv::where('user_id', '=', $user->id)->pluck('title', 'id')->toArray();

        return view('job.apply_job_form')
                        ->with('job_slug', $job_slug)
                        ->with('job', $job)
                        ->with('myCvs', $myCvs);
    }

    public function postApplyJob(ApplyJobFormRequest $request, $job_slug)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $job = Job::where('slug', 'like', $job_slug)->first();

        $jobApply = new JobApply();
        $jobApply->user_id = $user_id;
        $jobApply->job_id = $job->id;
        $jobApply->cv_id = $request->post('cv_id');
        $jobApply->cover_letter = $request->post('cover_letter');
        $jobApply->resume_source = $request->post('resume_source', 'existing_cv');
        $jobApply->current_salary = $request->post('current_salary');
        $jobApply->expected_salary = $request->post('expected_salary');
        $jobApply->salary_currency = $request->post('salary_currency');
        $jobApply->save();
        
        // Save question answers
        if ($request->has('question_answers')) {
            $questionAnswers = $request->post('question_answers');
            foreach ($questionAnswers as $questionId => $answer) {
                if (!empty($answer)) {
                    $questionAnswer = new JobQuestionAnswer();
                    $questionAnswer->job_question_id = $questionId;
                    $questionAnswer->job_apply_id = $jobApply->id;
                    $questionAnswer->answer = $answer;
                    $questionAnswer->save();
                }
            }
        }

        if ((bool) config('jobseeker.is_jobseeker_package_active')) {
            if ($user->jobs_quota > 0) {
                $user->availed_jobs_quota = $user->availed_jobs_quota + 1;
                $user->update();
            }
        }

        $myCv = ProfileCv::find($request->post('cv_id'));
        
        if($job->external_job =='yes'){
            $url = $job->getApplyActionUrl();
            if ($url) {
                return redirect()->away($url)->withHeaders(['target' => '_blank']);
            }
        }
        
        if ($myCv) {
            event(new JobApplied($job, $jobApply, $myCv));
        }

        flash(__('You have successfully applied for this job'))->success();
        return \Redirect::route('job.detail', $job_slug);
    }

    public function myJobApplications(Request $request)
    {
        // Get applied jobs with application details including status
        $appliedJobs = JobApply::where('user_id', Auth::user()->id)
            ->with(['job.company'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('job.my_applied_jobs')
                        ->with('appliedJobs', $appliedJobs);
    }

    public function myFavouriteJobs(Request $request)
    {
        $myFavouriteJobSlugs = Auth::user()->getFavouriteJobSlugsArray();
        $jobs = Job::whereIn('slug', $myFavouriteJobSlugs)->paginate(10);
        return view('job.my_favourite_jobs')
                        ->with('jobs', $jobs);
    }

    public function downloadAppliedUsersCsv($jobId)
{
    if (!Auth::guard('company')->check()) {
        return redirect()->route('employer.login'); // Make sure this is the correct login route
    }
    $employer = Auth::guard('company')->user();

    $job = Job::findOrFail($jobId);
    $jobApplications = $job->jobApplications()->with('user')->get();

    $csvContent = "Name,Location,Expected Salary,Experience,Career Level,Phone\n";

    foreach ($jobApplications as $jobApplication) {
        $user = $jobApplication->user;
        if ($user) {
            $csvContent .= "\"{$user->getName()}\",\"{$user->getLocation()}\",\"{$jobApplication->expected_salary} {$jobApplication->salary_currency}\",\"{$user->getJobExperience('job_experience')}\",\"{$user->getCareerLevel('career_level')}\",\"{$user->phone}\"\n";
        }
    }

    $filename = "applied_users_{$job->title}.csv";
    return response()->streamDownload(function () use ($csvContent) {
        echo $csvContent;
    }, $filename, ['Content-Type' => 'text/csv']);
}


public function downloadCsv(Request $request, $jobId)
{
    $job = Job::findOrFail($jobId);
    $jobApplications = JobApplication::where('job_id', $jobId)->get();

    $csvFileName = "applied_users_{$job->title}.csv";

    $response = new StreamedResponse(function () use ($jobApplications) {
        $handle = fopen('php://output', 'w');

        // Add CSV headers
        fputcsv($handle, ['Name', 'Location', 'Expected Salary', 'Experience', 'Career Level', 'Phone']);

        // Add data
        foreach ($jobApplications as $jobApplication) {
            $user = $jobApplication->getUser();
            if ($user) {
                fputcsv($handle, [
                    $user->getName(),
                    $user->getLocation(),
                    $jobApplication->expected_salary . ' ' . $jobApplication->salary_currency,
                    $user->getJobExperience('job_experience'),
                    $user->getCareerLevel('career_level'),
                    $user->phone
                ]);
            }
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', "attachment; filename={$csvFileName}");

    return $response;
}



}
