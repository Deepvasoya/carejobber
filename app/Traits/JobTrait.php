<?php



namespace App\Traits;



use Auth;

use DB;

use Input;

use Redirect;

use Carbon\Carbon;

use App\Job;

use App\Company;

use App\JobSkill;

use App\JobSkillManager;
use App\JobQuestion;

use App\Country;

use App\CountryDetail;

use App\State;

use App\City;

use App\CareerLevel;

use App\FunctionalArea;

use App\JobType;

use App\JobShift;

use App\Gender;

use App\JobExperience;

use App\DegreeLevel;

use App\SalaryPeriod;

use App\Helpers\MiscHelper;

use App\Helpers\DataArrayHelper;

use App\Http\Requests;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests\JobFormRequest;

use App\Http\Requests\Front\JobFrontFormRequest;

use App\Http\Controllers\Controller;

use App\Traits\Skills;

use App\Events\JobPosted;

use Illuminate\Support\Str;

use App\SiteSetting;

use App\Helpers\LocationHelper;

use App\Services\JobPromotionPricing;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Mail;
use App\Mail\JobApprovalMailable;
use App\Mail\JobPostedMailableFront;
trait JobTrait

{



    use Skills;



    public function deleteJob(Request $request)

    {

        $id = $request->input('id');

        try {

            $job = Job::findOrFail($id);

            JobSkillManager::where('job_id', '=', $id)->delete();

            $job->delete();

            return 'ok';

        } catch (ModelNotFoundException $e) {

            return 'notok';

        }

    }



    private function updateFullTextSearch($job)

    {

        $str = '';

        $str .= $job->getCompany('name');

        $str .= ' ' . $job->getCountry('country');

        $str .= ' ' . $job->getState('state');

        $str .= ' ' . $job->getCity('city');

        $str .= ' ' . $job->title;

        $str .= ' ' . $job->description;

        $str .= $job->getJobSkillsStr();

        $str .= ((bool) $job->is_freelance) ? ' freelance remote work from home multiple cities' : '';

        $str .= ' ' . $job->getCareerLevel('career_level');

        $str .= ((bool) $job->hide_salary === false) ? ' ' . $job->salary_from . ' ' . $job->salary_to : '';

        $str .= $job->getSalaryPeriod('salary_period');

        $str .= ' ' . $job->getFunctionalArea('functional_area');

        $str .= ' ' . $job->getJobType('job_type');

        $str .= ' ' . $job->getJobShift('job_shift');

        $str .= ' ' . $job->getGender('gender');

        $str .= ' ' . $job->getDegreeLevel('degree_level');

        $str .= ' ' . $job->getJobExperience('job_experience');



        $job->search = $str;

        $job->update();

    }



    private function assignJobValues($job, $request)

    {

        $job->title = $request->input('title');

        $job->description = $request->input('description');

        $job->benefits = $request->input('benefits');

        $settings = SiteSetting::first();
        $defaultCountryId = $settings->default_country_id ?? null;

        if (LocationHelper::showCountry()) {
            $job->country_id = $request->input('country_id');
        } else {
            $job->country_id = $request->input('country_id') ?: $defaultCountryId;
        }

        $level = LocationHelper::getLocationLevels();
        if ((int) $level === 1 && $request->filled('city_id')) {
            $cityRow = City::where('city_id', (int) $request->input('city_id'))->first();
            $job->state_id = $cityRow ? $cityRow->state_id : $request->input('state_id');
        } else {
            $job->state_id = $request->input('state_id');
        }

        $job->city_id = $request->input('city_id');

        $job->is_freelance = $request->input('is_freelance');

        $job->career_level_id = $request->input('career_level_id');

        $job->salary_from = (int) $request->input('salary_from');

        $job->salary_to = (int) $request->input('salary_to');

        $job->salary_currency = $request->input('salary_currency');

        $job->hide_salary = $request->input('hide_salary');

        $job->functional_area_id = $request->input('functional_area_id');

        $job->job_type_id = $request->input('job_type_id');

        $job->job_shift_id = $request->input('job_shift_id');

        $job->num_of_positions = $request->input('num_of_positions');

        $job->gender_id = $request->input('gender_id');

        $job->expiry_date = $request->input('expiry_date');

        $job->degree_level_id = $request->input('degree_level_id');

        $job->job_experience_id = $request->input('job_experience_id');

        $job->salary_period_id = $request->input('salary_period_id');
        $job->external_job = $request->input('external_job');
        $job->job_link = $request->input('job_link');

        return $job;

    }



    public function createJob()

    {

        $companies = DataArrayHelper::companiesArray();

        $countries = DataArrayHelper::defaultCountriesArray();

        $currencies = DataArrayHelper::currenciesArray();

        $careerLevels = DataArrayHelper::defaultCareerLevelsArray();

        $functionalAreas = DataArrayHelper::defaultFunctionalAreasArray();

        $jobTypes = DataArrayHelper::defaultJobTypesArray();

        $jobShifts = DataArrayHelper::defaultJobShiftsArray();

        $genders = DataArrayHelper::defaultGendersArray();

        $jobExperiences = DataArrayHelper::defaultJobExperiencesArray();

        $jobSkills = DataArrayHelper::defaultJobSkillsArray();

        $degreeLevels = DataArrayHelper::defaultDegreeLevelsArray();

        $salaryPeriods = DataArrayHelper::defaultSalaryPeriodsArray();

        $jobSkillIds = array();

        return view('admin.job.add')

                        ->with('companies', $companies)

                        ->with('countries', $countries)

                        ->with('currencies', array_unique($currencies))

                        ->with('careerLevels', $careerLevels)

                        ->with('functionalAreas', $functionalAreas)

                        ->with('jobTypes', $jobTypes)

                        ->with('jobShifts', $jobShifts)

                        ->with('genders', $genders)

                        ->with('jobExperiences', $jobExperiences)

                        ->with('jobSkills', $jobSkills)

                        ->with('jobSkillIds', $jobSkillIds)

                        ->with('degreeLevels', $degreeLevels)

                        ->with('salaryPeriods', $salaryPeriods);

    }



    public function storeJob(JobFormRequest $request)

    {

        $job = new Job();

        $job->company_id = $request->input('company_id');

        $job = $this->assignJobValues($job, $request);

        $job->is_active = $request->input('is_active');

        $job->is_featured = $request->input('is_featured');

        $job->is_urgent = $request->input('is_urgent', 0);

        $job->is_highlighted = $request->input('is_highlighted', 0);

        $job->save();

        /*         * ******************************* */

        $job->slug = Str::slug($job->title, '-') . '-' . $job->id;

        /*         * ******************************* */

        $job->update();

        /*         * ************************************ */

        /*         * ************************************ */

        $this->storeJobSkills($request, $job->id);

        /*         * ************************************ */

        $this->updateFullTextSearch($job);

        /*         * ************************************ */

        flash('Job has been added!')->success();

        return \Redirect::route('edit.job', array($job->id));

    }



    public function editJob($id)

    {

        $companies = DataArrayHelper::companiesArray();

        $countries = DataArrayHelper::defaultCountriesArray();

        $currencies = DataArrayHelper::currenciesArray();

        $careerLevels = DataArrayHelper::defaultCareerLevelsArray();

        $functionalAreas = DataArrayHelper::defaultFunctionalAreasArray();

        $jobTypes = DataArrayHelper::defaultJobTypesArray();

        $jobShifts = DataArrayHelper::defaultJobShiftsArray();

        $genders = DataArrayHelper::defaultGendersArray();

        $jobExperiences = DataArrayHelper::defaultJobExperiencesArray();

        $jobSkills = DataArrayHelper::defaultJobSkillsArray();

        $degreeLevels = DataArrayHelper::defaultDegreeLevelsArray();

        $salaryPeriods = DataArrayHelper::defaultSalaryPeriodsArray();



        $job = Job::findOrFail($id);

        $jobSkillIds = $job->getJobSkillsArray();

        return view('admin.job.edit')

                        ->with('companies', $companies)

                        ->with('countries', $countries)

                        ->with('currencies', array_unique($currencies))

                        ->with('careerLevels', $careerLevels)

                        ->with('functionalAreas', $functionalAreas)

                        ->with('jobTypes', $jobTypes)

                        ->with('jobShifts', $jobShifts)

                        ->with('genders', $genders)

                        ->with('jobExperiences', $jobExperiences)

                        ->with('jobSkills', $jobSkills)

                        ->with('jobSkillIds', $jobSkillIds)

                        ->with('degreeLevels', $degreeLevels)

                        ->with('salaryPeriods', $salaryPeriods)

                        ->with('job', $job);

    }



    public function updateJob($id, JobFormRequest $request)

    {

        $job = Job::findOrFail($id);

        $job->company_id = $request->input('company_id');

        $job = $this->assignJobValues($job, $request);

        $job->is_active = $request->input('is_active');

        $job->is_featured = $request->input('is_featured');

        $job->is_urgent = $request->input('is_urgent', 0);

        $job->is_highlighted = $request->input('is_highlighted', 0);



        /*         * ******************************* */

        $job->slug = Str::slug($job->title, '-') . '-' . $job->id;

        /*         * ******************************* */



        /*         * ************************************ */

        $job->update();

        /*         * ************************************ */

        $this->storeJobSkills($request, $job->id);
        $this->storeJobQuestions($request, $job->id);

        /*         * ************************************ */

        $this->updateFullTextSearch($job);

        /*         * ************************************ */

        flash('Job has been updated!')->success();

        return \Redirect::route('edit.job', array($job->id));

    }



    /*     * *************************************** */

    /*     * *************************************** */



    public function createFrontJob()

    {

        $company = Auth::guard('company')->user();

		

		$countries = DataArrayHelper::langCountriesArray();

        $currencies = DataArrayHelper::currenciesArray();

        $careerLevels = DataArrayHelper::langCareerLevelsArray();

        $functionalAreas = DataArrayHelper::langFunctionalAreasArray();

        $jobTypes = DataArrayHelper::langJobTypesArray();

        $jobShifts = DataArrayHelper::langJobShiftsArray();

        $genders = DataArrayHelper::langGendersArray();

        $jobExperiences = DataArrayHelper::langJobExperiencesArray();

        $jobSkills = DataArrayHelper::langJobSkillsArray();

        $degreeLevels = DataArrayHelper::langDegreeLevelsArray();

        $salaryPeriods = DataArrayHelper::langSalaryPeriodsArray();



        $jobSkillIds = array();

        return view('job.add_edit_job')

                        ->with('countries', $countries)

                        ->with('currencies', array_unique($currencies))

                        ->with('careerLevels', $careerLevels)

                        ->with('functionalAreas', $functionalAreas)

                        ->with('jobTypes', $jobTypes)

                        ->with('jobShifts', $jobShifts)

                        ->with('genders', $genders)

                        ->with('jobExperiences', $jobExperiences)

                        ->with('jobSkills', $jobSkills)

                        ->with('jobSkillIds', $jobSkillIds)

                        ->with('degreeLevels', $degreeLevels)

                        ->with('salaryPeriods', $salaryPeriods);

    }



    public function storeFrontJob(JobFrontFormRequest $request)
{
    $settings = SiteSetting::findOrFail(1272);
    $company = Auth::guard('company')->user();

    if (Gate::forUser($company)->denies('canPostJob')) {
        Session::flash('error', __('Please purchase a package to post jobs.'));
        return Redirect::route('recruiter.posting.packages', ['cc' => $company->country_code ?? 'CA']);
    }

    $pending = JobPromotionPricing::pendingForNewJob($request);

    $job = new Job();
    $job->company_id = $company->id;
    $job = $this->assignJobValues($job, $request);
    if ($pending['total_cents'] > 0) {
        $job->is_featured = false;
        $job->is_urgent = false;
        $job->is_highlighted = false;
    } else {
        $job->is_featured = $request->boolean('promote_featured');
        $job->is_urgent = $request->boolean('promote_urgent');
        $job->is_highlighted = $request->boolean('promote_highlighted');
    }
    $job->save();

    // Generate slug
    $job->slug = Str::slug($job->title, '-') . '-' . $job->id;

    // Set active status based on auto approval setting
    $job->is_active = ($settings->auto_approval_job == 1) ? 1 : 0;
    
    // Calculate display end date based on duration
    $displayDuration = (int) $request->input('display_duration_days', 30);
    $job->display_duration_days = $displayDuration;
    $job->display_end_date = Carbon::now()->addDays($displayDuration);
    
    $job->update();

    // Store skills and update search index
    $this->storeJobSkills($request, $job->id);
    $this->storeJobQuestions($request, $job->id);
    $this->updateFullTextSearch($job);

    // Update company's job quota
    $company->availed_jobs_quota += 1;
    $company->update();

    // Email and event logic based on active status
    if ($job->is_active == 1) {
        Mail::send(new JobApprovalMailable($job));
        event(new JobPosted($job));
    } else {
        Mail::send(new JobPostedMailableFront($job));
    }

    if ($pending['total_cents'] > 0) {
        Session::put('pending_job_promotions', [
            'job_id' => $job->id,
            'promote_featured' => $pending['promote_featured'] ? 1 : 0,
            'promote_urgent' => $pending['promote_urgent'] ? 1 : 0,
            'promote_highlighted' => $pending['promote_highlighted'] ? 1 : 0,
            'total_cents' => (int) $pending['total_cents'],
        ]);
        flash(__('Job saved. Complete payment to activate your listing upgrades.'))->info();

        return Redirect::route('job.promotions.checkout');
    }

    flash('Job has been added!')->success();
    return Redirect::route('posted.jobs');
}




    public function editFrontJob($id)

    {

        $countries = DataArrayHelper::langCountriesArray();

        $currencies = DataArrayHelper::currenciesArray();

        $careerLevels = DataArrayHelper::langCareerLevelsArray();

        $functionalAreas = DataArrayHelper::langFunctionalAreasArray();

        $jobTypes = DataArrayHelper::langJobTypesArray();

        $jobShifts = DataArrayHelper::langJobShiftsArray();

        $genders = DataArrayHelper::langGendersArray();

        $jobExperiences = DataArrayHelper::langJobExperiencesArray();

        $jobSkills = DataArrayHelper::langJobSkillsArray();

        $degreeLevels = DataArrayHelper::langDegreeLevelsArray();

        $salaryPeriods = DataArrayHelper::langSalaryPeriodsArray();



        $company = Auth::guard('company')->user();
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $jobSkillIds = $job->getJobSkillsArray();

        return view('job.add_edit_job')

                        ->with('countries', $countries)

                        ->with('currencies', array_unique($currencies))

                        ->with('careerLevels', $careerLevels)

                        ->with('functionalAreas', $functionalAreas)

                        ->with('jobTypes', $jobTypes)

                        ->with('jobShifts', $jobShifts)

                        ->with('genders', $genders)

                        ->with('jobExperiences', $jobExperiences)

                        ->with('jobSkills', $jobSkills)

                        ->with('jobSkillIds', $jobSkillIds)

                        ->with('degreeLevels', $degreeLevels)

                        ->with('salaryPeriods', $salaryPeriods)

                        ->with('job', $job);

    }



    public function updateFrontJob($id, JobFrontFormRequest $request)

    {

        $company = Auth::guard('company')->user();
        $job = Job::where('company_id', $company->id)->findOrFail($id);
        
        // Check if the job was expired before the update
        $wasExpired = $job->expiry_date && $job->expiry_date < now();

		$job = $this->assignJobValues($job, $request);

        $pending = JobPromotionPricing::pendingForUpdate($request, $job);

        $wantF = $request->boolean('promote_featured');
        $wantU = $request->boolean('promote_urgent');
        $wantH = $request->boolean('promote_highlighted');

        if (! $wantF) {
            $job->is_featured = false;
        } elseif ($pending['promote_featured']) {
            $job->is_featured = false;
        } else {
            $job->is_featured = true;
        }

        if (! $wantU) {
            $job->is_urgent = false;
        } elseif ($pending['promote_urgent']) {
            $job->is_urgent = false;
        } else {
            $job->is_urgent = true;
        }

        if (! $wantH) {
            $job->is_highlighted = false;
        } elseif ($pending['promote_highlighted']) {
            $job->is_highlighted = false;
        } else {
            $job->is_highlighted = true;
        }

        /*         * ******************************* */

        $job->slug = Str::slug($job->title, '-') . '-' . $job->id;

        /*         * ******************************* */

        // Update display end date if duration changed
        $displayDuration = (int) $request->input('display_duration_days', 30);
        if ($job->display_duration_days != $displayDuration) {
            $job->display_duration_days = $displayDuration;
            $job->display_end_date = Carbon::now()->addDays($displayDuration);
        }

        /*         * ************************************ */

        $job->update();
        
        if ($wasExpired) {
            $company = Auth::guard('company')->user();
            $company->availed_jobs_quota = $company->availed_jobs_quota + 1;
            $company->update();
        }

        /*         * ************************************ */

        $this->storeJobSkills($request, $job->id);
        $this->storeJobQuestions($request, $job->id);

        /*         * ************************************ */

        $this->updateFullTextSearch($job);

        /*         * ************************************ */

        if ($pending['total_cents'] > 0) {
            Session::put('pending_job_promotions', [
                'job_id' => $job->id,
                'promote_featured' => $pending['promote_featured'] ? 1 : 0,
                'promote_urgent' => $pending['promote_urgent'] ? 1 : 0,
                'promote_highlighted' => $pending['promote_highlighted'] ? 1 : 0,
                'total_cents' => (int) $pending['total_cents'],
            ]);
            flash(__('Job updated. Complete payment to activate new listing upgrades.'))->info();

            return \Redirect::route('job.promotions.checkout');
        }

        flash('Job has been updated!')->success();

        return \Redirect::route('posted.jobs');

    }
    
    private function storeJobQuestions($request, $job_id)
    {
        if ($request->has('questions')) {
            $questions = $request->input('questions');
            $existingQuestionIds = [];
            
            foreach ($questions as $index => $questionData) {
                if (!empty($questionData['title'])) {
                    if (isset($questionData['id']) && !empty($questionData['id'])) {
                        // Update existing question
                        $question = JobQuestion::find($questionData['id']);
                        if ($question && $question->job_id == $job_id) {
                            $question->question_title = $questionData['title'];
                            $question->order = $index;
                            $question->update();
                            $existingQuestionIds[] = $question->id;
                        }
                    } else {
                        // Create new question
                        $question = new JobQuestion();
                        $question->job_id = $job_id;
                        $question->question_title = $questionData['title'];
                        $question->order = $index;
                        $question->save();
                        $existingQuestionIds[] = $question->id;
                    }
                }
            }
            
            // Delete questions that were removed
            JobQuestion::where('job_id', $job_id)
                ->whereNotIn('id', $existingQuestionIds)
                ->delete();
        } else {
            // If no questions submitted, delete all existing questions
            JobQuestion::where('job_id', $job_id)->delete();
        }
    }



    public static function countNumJobs($field = 'title', $value = '')
    {
        if (!empty($value)) {
            if ($field == 'title') {
                return DB::table('jobs')->where('title', 'like', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'company_id') {
                return DB::table('jobs')->where('company_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'industry_id') {
                $company_ids = Company::where('industry_id', '=', $value)->where('is_active', '=', 1)->pluck('id')->toArray();
                return DB::table('jobs')->whereIn('company_id', $company_ids)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'job_skill_id') {
                $job_ids = JobSkillManager::where('job_skill_id', '=', $value)->pluck('job_id')->toArray();
                return DB::table('jobs')->whereIn('id', array_unique($job_ids))->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'functional_area_id') {
                return DB::table('jobs')->where('functional_area_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'careel_level_id') {
                return DB::table('jobs')->where('careel_level_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'job_type_id') {
                return DB::table('jobs')->where('job_type_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'job_shift_id') {
                return DB::table('jobs')->where('job_shift_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'gender_id') {
                return DB::table('jobs')->where('gender_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'degree_level_id') {
                return DB::table('jobs')->where('degree_level_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'job_experience_id') {
                return DB::table('jobs')->where('job_experience_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'country_id') {
                return DB::table('jobs')->where('country_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'state_id') {
                return DB::table('jobs')->where('state_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'city_id') {
                return DB::table('jobs')->where('city_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
        }
    }



    public function scopeNotExpire($query)

    {

        return $query->whereDate('expiry_date', '>', Carbon::now()); //where('expiry_date', '>=', date('Y-m-d'));

    }

    

    public function isJobExpired()

    {

        return ($this->expiry_date < Carbon::now())? true:false;

    }



}

