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

            app(\App\Services\Medo\LegacyJobSyncService::class)->deleteForLegacyJob($job);

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

    private function makeJobSlug(Job $job): string
    {
        $t = trim((string) $job->title);
        if ($t !== '') {
            return Str::slug($t, '-') . '-' . $job->id;
        }

        return 'draft-' . $job->id;
    }

    public function getEffectiveApplyType(): string
    {
       
        $externalJob = $this->external_job ?? 'no';
        return ((string) $externalJob === 'yes') ? 'external' : 'internal';
    }

    public function isExternalApplyType(): bool
    {
        return $this->getEffectiveApplyType() !== 'internal';
    }

    public function getApplyActionUrl(): ?string
    {
        $value = trim((string) ($this->job_link ?? ''));
        if ($value === '' || ! $this->isExternalApplyType()) {
            return null;
        }

        switch ($this->getEffectiveApplyType()) {
            case 'email':
                $email = preg_replace('/^mailto:/i', '', $value);
                return 'mailto:' . trim($email);

            case 'phone':
                $phone = preg_replace('/^tel:/i', '', $value);
                $phone = preg_replace('/(?!^)\+|[^\d+]/', '', $phone);
                return $phone !== '' ? 'tel:' . $phone : null;

            case 'external':
                if (! preg_match('~^https?://~i', $value)) {
                    $value = 'https://' . $value;
                }

                return $value;
        }

        return null;
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

        $job->industry_id = $request->input('industry_id');

        $job->functional_area_id = $request->input('functional_area_id');

        $job->job_type_id = $request->input('job_type_id');

        $job->job_shift_id = $request->input('job_shift_id');

        $job->num_of_positions = $request->input('num_of_positions');

        $job->gender_id = $request->input('gender_id');

        $expiryIn = $request->input('expiry_date');
        $job->expiry_date = ($expiryIn === '' || $expiryIn === null) ? null : $expiryIn;

        $job->degree_level_id = $request->input('degree_level_id');

        $job->job_experience_id = $request->input('job_experience_id');

        $job->salary_period_id = $request->input('salary_period_id');
        $applyType = $this->normalizeApplyType($request->input('apply_type'), $request->input('external_job'));
        $job->apply_type = $applyType;
        $job->external_job = ($applyType === 'internal') ? 'no' : 'yes';
        $job->job_link = ($applyType === 'internal') ? null : $request->input('job_link');

        $cf = app(\App\Services\CustomFieldValueService::class);
        $norm = $cf->normalizeForContext($request, \App\Models\CustomField::CONTEXT_JOB_LISTING);
        $job->custom_field_data = $cf->mergeStored($job->custom_field_data ?? null, $norm);

        return $job;

    }



    public function createJob()

    {

        $companies = DataArrayHelper::companiesArray();

        $countries = DataArrayHelper::defaultCountriesArray();

        $currencies = DataArrayHelper::currenciesArray();

        $careerLevels = DataArrayHelper::defaultCareerLevelsArray();

        $jobCategories = DataArrayHelper::defaultJobCategoriesArray();

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

                        ->with('jobCategories', $jobCategories)

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

        app(\App\Services\Medo\LegacyJobSyncService::class)->sync($job);

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

        $jobCategories = DataArrayHelper::defaultJobCategoriesArray();

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

                        ->with('jobCategories', $jobCategories)

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

        app(\App\Services\Medo\LegacyJobSyncService::class)->sync($job);

        /*         * ************************************ */

        flash('Job has been updated!')->success();

        return \Redirect::route('edit.job', array($job->id));

    }



    /*     * *************************************** */

    /*     * *************************************** */



    public function createFrontJob()

    {

        $company = Auth::guard('company')->user();

        if (Gate::forUser($company)->denies('canPostJob')) {
            Session::flash('error', __('You have no job posting credits left. Please choose a package before continuing.'));

            return Redirect::route('recruiter.posting.packages', ['cc' => $company->country_code ?? 'CA']);
        }

		

		$countries = DataArrayHelper::langCountriesArray();

        $currencies = DataArrayHelper::currenciesArray();

        $careerLevels = DataArrayHelper::langCareerLevelsArray();

        $jobCategories = DataArrayHelper::langJobCategoriesArray();

        $jobTypes = DataArrayHelper::langJobTypesArray();

        $jobShifts = DataArrayHelper::langJobShiftsArray();

        $genders = DataArrayHelper::langGendersArray();

        $jobExperiences = DataArrayHelper::langJobExperiencesArray();

        $jobSkills = DataArrayHelper::langJobSkillsArray();

        $degreeLevels = DataArrayHelper::langDegreeLevelsArray();

        $salaryPeriods = DataArrayHelper::langSalaryPeriodsArray();

        $industries = DataArrayHelper::langIndustriesArray();



        $jobSkillIds = array();

        return view('job.add_edit_job')

                        ->with('countries', $countries)

                        ->with('currencies', array_unique($currencies))

                        ->with('careerLevels', $careerLevels)

                        ->with('jobCategories', $jobCategories)

                        ->with('jobTypes', $jobTypes)

                        ->with('jobShifts', $jobShifts)

                        ->with('genders', $genders)

                        ->with('jobExperiences', $jobExperiences)

                        ->with('jobSkills', $jobSkills)

                        ->with('jobSkillIds', $jobSkillIds)

                        ->with('degreeLevels', $degreeLevels)

                        ->with('salaryPeriods', $salaryPeriods)

                        ->with('industries', $industries);

    }



    public function storeFrontJob(JobFrontFormRequest $request)
{
    $settings = SiteSetting::findOrFail(1272);
    $company = Auth::guard('company')->user();
    $isDraft = $request->isDraftAction();

    if (! $isDraft && Gate::forUser($company)->denies('canPostJob')) {
        Session::flash('error', __('You have no job posting credits left. Please choose a package before continuing.'));
        return Redirect::route('recruiter.posting.packages', ['cc' => $company->country_code ?? 'CA']);
    }

    if (! $isDraft) {
        app(\App\Services\UserSubmittedLookupService::class)->mergeUserSubmittedJobRequest($request);
    }

    $pending = $isDraft ? ['total_cents' => 0, 'pay_urgent' => false, 'pay_featured' => false, 'pay_highlighted' => false] : JobPromotionPricing::pendingForNewJob($request);

    $job = new Job();
    $job->company_id = $company->id;
    $job = $this->assignJobValues($job, $request);

    if ($isDraft) {
        $job->is_draft = true;
        $job->is_active = false;
        $job->is_featured = false;
        $job->is_urgent = false;
        $job->is_highlighted = false;
    } elseif ($pending['total_cents'] > 0) {
        if (! empty($pending['pay_urgent'])) {
            $job->is_urgent = false;
            $job->promotion_urgent_until = null;
        }
        if (! empty($pending['pay_featured'])) {
            $job->is_featured = false;
            $job->promotion_featured_until = null;
        }
        if (! empty($pending['pay_highlighted'])) {
            $job->is_highlighted = false;
            $job->promotion_highlighted_until = null;
        }
    }
    $job->save();

    $job->slug = $this->makeJobSlug($job);

    if ($isDraft) {
        $job->display_duration_days = (int) $request->input('display_duration_days', 30);
        $job->display_end_date = null;
        $job->update();
    } else {
        $job->is_active = ($settings->auto_approval_job == 1) ? 1 : 0;
        $displayDuration = (int) $request->input('display_duration_days', 30);
        $job->display_duration_days = $displayDuration;
        $job->display_end_date = Carbon::now()->addDays($displayDuration);
        $job->update();
        JobPromotionPricing::reconcilePromotionsAfterSave($job, $request, $pending);
    }

    $this->storeJobSkills($request, $job->id);
    $this->storeJobQuestions($request, $job->id);
    $this->updateFullTextSearch($job);
    app(\App\Services\Medo\LegacyJobSyncService::class)->sync($job);

    if ($isDraft) {
        flash(__('Draft saved. You can finish and submit it from Manage Jobs.'))->success();

        return Redirect::route('posted.jobs', ['tab' => 'drafts']);
    }

    $company->availed_jobs_quota += 1;
    $company->update();

    if ($job->is_active == 1) {
        Mail::send(new JobApprovalMailable($job));
        event(new JobPosted($job));
    } else {
        Mail::send(new JobPostedMailableFront($job));
    }

    if ($pending['total_cents'] > 0) {
        Session::put('pending_job_promotions', [
            'job_id' => $job->id,
            'pay_urgent' => ! empty($pending['pay_urgent']) ? 1 : 0,
            'pay_featured' => ! empty($pending['pay_featured']) ? 1 : 0,
            'pay_highlighted' => ! empty($pending['pay_highlighted']) ? 1 : 0,
            'promote_urgent_days' => (int) $pending['promote_urgent_days'],
            'promote_featured_days' => (int) $pending['promote_featured_days'],
            'promote_highlighted' => (int) $pending['promote_highlighted'],
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

        $jobCategories = DataArrayHelper::langJobCategoriesArray();

        $jobTypes = DataArrayHelper::langJobTypesArray();

        $jobShifts = DataArrayHelper::langJobShiftsArray();

        $genders = DataArrayHelper::langGendersArray();

        $jobExperiences = DataArrayHelper::langJobExperiencesArray();

        $jobSkills = DataArrayHelper::langJobSkillsArray();

        $degreeLevels = DataArrayHelper::langDegreeLevelsArray();

        $salaryPeriods = DataArrayHelper::langSalaryPeriodsArray();

        $industries = DataArrayHelper::langIndustriesArray();



        $company = Auth::guard('company')->user();
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $jobSkillIds = $job->getJobSkillsArray();

        return view('job.add_edit_job')

                        ->with('countries', $countries)

                        ->with('currencies', array_unique($currencies))

                        ->with('careerLevels', $careerLevels)

                        ->with('jobCategories', $jobCategories)

                        ->with('jobTypes', $jobTypes)

                        ->with('jobShifts', $jobShifts)

                        ->with('genders', $genders)

                        ->with('jobExperiences', $jobExperiences)

                        ->with('jobSkills', $jobSkills)

                        ->with('jobSkillIds', $jobSkillIds)

                        ->with('degreeLevels', $degreeLevels)

                        ->with('salaryPeriods', $salaryPeriods)

                        ->with('industries', $industries)

                        ->with('job', $job);

    }



    public function updateFrontJob($id, JobFrontFormRequest $request)

    {

        $settings = SiteSetting::findOrFail(1272);
        $company = Auth::guard('company')->user();
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $wasExpired = $job->expiry_date && $job->expiry_date < now();
        $wasDraft = (bool) $job->is_draft;
        $isDraft = $request->isDraftAction();

        if ($isDraft) {
            if (! $wasDraft) {
                flash(__('You cannot save a published job as a draft from this form.'))->error();

                return Redirect::route('edit.front.job', $job->id);
            }

            $job = $this->assignJobValues($job, $request);
            $job->is_draft = true;
            $job->is_active = false;
            $job->is_featured = false;
            $job->is_urgent = false;
            $job->is_highlighted = false;
            $job->promotion_urgent_until = null;
            $job->promotion_featured_until = null;
            $job->promotion_highlighted_until = null;
            $job->slug = $this->makeJobSlug($job);
            $job->display_duration_days = (int) $request->input('display_duration_days', $job->display_duration_days ?? 30);
            $job->display_end_date = null;
            $job->update();

            $this->storeJobSkills($request, $job->id);
            $this->storeJobQuestions($request, $job->id);
            $this->updateFullTextSearch($job);
            app(\App\Services\Medo\LegacyJobSyncService::class)->sync($job);

            flash(__('Draft saved.'))->success();

            return Redirect::route('posted.jobs', ['tab' => 'drafts']);
        }

        if (Gate::forUser($company)->denies('canPostJob')) {
            Session::flash('error', __('You have no job posting credits left. Please choose a package before continuing.'));

            return Redirect::route('recruiter.posting.packages', ['cc' => $company->country_code ?? 'CA']);
        }

        app(\App\Services\UserSubmittedLookupService::class)->mergeUserSubmittedJobRequest($request);

        $job = $this->assignJobValues($job, $request);

        if ($wasDraft) {
            $pending = JobPromotionPricing::pendingForNewJob($request);
            if ($pending['total_cents'] > 0) {
                if (! empty($pending['pay_urgent'])) {
                    $job->is_urgent = false;
                    $job->promotion_urgent_until = null;
                }
                if (! empty($pending['pay_featured'])) {
                    $job->is_featured = false;
                    $job->promotion_featured_until = null;
                }
                if (! empty($pending['pay_highlighted'])) {
                    $job->is_highlighted = false;
                    $job->promotion_highlighted_until = null;
                }
            }
            $job->is_draft = false;
            $job->is_active = ($settings->auto_approval_job == 1) ? 1 : 0;
            $displayDuration = (int) $request->input('display_duration_days', 30);
            $job->display_duration_days = $displayDuration;
            $job->display_end_date = Carbon::now()->addDays($displayDuration);
            $job->slug = $this->makeJobSlug($job);
            $job->update();

            JobPromotionPricing::reconcilePromotionsAfterSave($job, $request, $pending);

            $company->availed_jobs_quota = $company->availed_jobs_quota + 1;
            $company->update();

            if ($job->is_active == 1) {
                Mail::send(new JobApprovalMailable($job));
                event(new JobPosted($job));
            } else {
                Mail::send(new JobPostedMailableFront($job));
            }

            $this->storeJobSkills($request, $job->id);
            $this->storeJobQuestions($request, $job->id);
            $this->updateFullTextSearch($job);
            app(\App\Services\Medo\LegacyJobSyncService::class)->sync($job);

            if ($pending['total_cents'] > 0) {
                Session::put('pending_job_promotions', [
                    'job_id' => $job->id,
                    'pay_urgent' => ! empty($pending['pay_urgent']) ? 1 : 0,
                    'pay_featured' => ! empty($pending['pay_featured']) ? 1 : 0,
                    'pay_highlighted' => ! empty($pending['pay_highlighted']) ? 1 : 0,
                    'promote_urgent_days' => (int) $pending['promote_urgent_days'],
                    'promote_featured_days' => (int) $pending['promote_featured_days'],
                    'promote_highlighted' => (int) $pending['promote_highlighted'],
                    'total_cents' => (int) $pending['total_cents'],
                ]);
                flash(__('Job saved. Complete payment to activate your listing upgrades.'))->info();

                return Redirect::route('job.promotions.checkout');
            }

            flash('Job has been updated!')->success();

            return Redirect::route('posted.jobs');
        }

        $pending = JobPromotionPricing::pendingForUpdate($request, $job);

        $job->slug = $this->makeJobSlug($job);

        $displayDuration = (int) $request->input('display_duration_days', 30);
        if ($job->display_duration_days != $displayDuration) {
            $job->display_duration_days = $displayDuration;
            $job->display_end_date = Carbon::now()->addDays($displayDuration);
        }

        if ($pending['total_cents'] > 0) {
            if (! empty($pending['pay_urgent'])) {
                $job->is_urgent = false;
                $job->promotion_urgent_until = null;
            }
            if (! empty($pending['pay_featured'])) {
                $job->is_featured = false;
                $job->promotion_featured_until = null;
            }
            if (! empty($pending['pay_highlighted'])) {
                $job->is_highlighted = false;
                $job->promotion_highlighted_until = null;
            }
        }

        $job->update();

        JobPromotionPricing::reconcilePromotionsAfterSave($job, $request, $pending);

        if ($wasExpired) {
            $company->availed_jobs_quota = $company->availed_jobs_quota + 1;
            $company->update();
        }

        $this->storeJobSkills($request, $job->id);
        $this->storeJobQuestions($request, $job->id);

        $this->updateFullTextSearch($job);
        app(\App\Services\Medo\LegacyJobSyncService::class)->sync($job);

        if ($pending['total_cents'] > 0) {
            Session::put('pending_job_promotions', [
                'job_id' => $job->id,
                'pay_urgent' => ! empty($pending['pay_urgent']) ? 1 : 0,
                'pay_featured' => ! empty($pending['pay_featured']) ? 1 : 0,
                'pay_highlighted' => ! empty($pending['pay_highlighted']) ? 1 : 0,
                'promote_urgent_days' => (int) $pending['promote_urgent_days'],
                'promote_featured_days' => (int) $pending['promote_featured_days'],
                'promote_highlighted' => (int) $pending['promote_highlighted'],
                'total_cents' => (int) $pending['total_cents'],
            ]);
            flash(__('Job updated. Complete payment to activate new listing upgrades.'))->info();

            return Redirect::route('job.promotions.checkout');
        }

        flash('Job has been updated!')->success();

        return Redirect::route('posted.jobs');

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
                return DB::table('jobs')->where('industry_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'job_skill_id') {
                $job_ids = JobSkillManager::where('job_skill_id', '=', $value)->pluck('job_id')->toArray();
                return DB::table('jobs')->whereIn('id', array_unique($job_ids))->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'functional_area_id') {
                return DB::table('jobs')->where('functional_area_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
            }
            if ($field == 'job_category_id') {
                return DB::table('jobs')->where('job_category_id', '=', $value)->where('is_active', '=', 1)->where('expiry_date', '>',  \Carbon\Carbon::now())->count('id');
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

