<?php

namespace App\Http\Controllers\Api;

use Auth;
use DB;
use Input;
use Redirect;
use Carbon\Carbon;
use App\Job;
use App\JobApply;
use App\JobQuestion;
use App\JobQuestionAnswer;
use App\FavouriteJob;
use App\Company;
use App\JobSkill;
use App\JobSkillManager;
use App\Country;
use App\CountryDetail;
use App\State;
use App\City;
use App\CareerLevel;
use App\FunctionalArea;
use App\JobType;
use App\JobShift;
use App\Gender;
use App\Seo;
use App\JobExperience;
use App\DegreeLevel;
use App\ProfileCv;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\Http\Requests\JobFormRequest;
use App\Http\Requests\Front\ApplyJobFormRequest;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Traits\FetchJobs;
use App\Events\JobApplied;
use App\Http\Resources\JobResource;
use Mail ;
class JobController extends BaseController
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
        //$this->middleware('auth', ['except' => ['jobsBySearch', 'jobDetail']]);

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
        $country_ids = $request->query('country_id', array());
        $state_ids = $request->query('state_id', array());
        $city_ids = $request->query('city_id', array());
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
        $limit = 15;
        
        $jobs = $this->fetchJobs($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, $order_by, $limit);

        $jobs_id_values = array();

        if(null!==($jobs)){
            foreach ($jobs as $key => $value) {//dd($value);
                $company = $value->getCompany();
                if(isset($company)){
                    $jobs_id_values[$key] = (object)array(
                        'company_logo' => $value->getCompany()->logo ? asset('company_logos/' . $value->getCompany()->logo) : asset('admin_assets/no-image.png'),
                        'company_name' => $value->getCompany()->name,
                        'company_slug' => $value->getCompany()->slug,
                        'job_type' => $value->getJobType('job_type'),
                        'city' => $value->getCity('city'),
                        'country' => $value->getCountry('country'),
                        'state' => $value->getState('state'),
                        'career_level' => $value->getCareerLevel('career_level'),
                        'functional_area' => $value->getFunctionalArea('functional_area'),
                        'job_shift' => $value->getJobShift('job_shift'),
                        'gender' => $value->getGender('gender'),
                        'degree_level' => $value->getDegreeLevel('degree_level'),
                        'job_experience' => $value->getJobExperience('job_experience'),
                        'description' => \Illuminate\Support\Str::limit(strip_tags($value->description), 150, '...'),
                    );
                }
                
            }
        }
        //$jobs = new JobResource($jobs_a);

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

        $industryIdsArray = $this->fetchIndustryIdsArray($companyIdsArray);

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


        $arr = array(
            'functionalAreas'=>$this->functionalAreas,
            'countries'=>$this->countries,
            'currencies'=>array_unique($currencies),
            'jobs'=>$jobs,
            'id_values'=>$jobs_id_values,
            'jobTitlesArray'=>$jobTitlesArray,
            'skillIdsArray'=>$skillIdsArray,
            'countryIdsArray'=>$countryIdsArray,
            'stateIdsArray'=>$stateIdsArray,
            'cityIdsArray'=>$cityIdsArray,
            'companyIdsArray'=>$companyIdsArray,
            'industryIdsArray'=>$industryIdsArray,
            'functionalAreaIdsArray'=>$functionalAreaIdsArray,
            'careerLevelIdsArray'=>$careerLevelIdsArray,
            'jobTypeIdsArray'=>$jobTypeIdsArray,
            'jobShiftIdsArray'=>$jobShiftIdsArray,
            'genderIdsArray'=>$genderIdsArray,
            'degreeLevelIdsArray'=>$degreeLevelIdsArray,
            'jobExperienceIdsArray'=>$jobExperienceIdsArray,
            'seo'=>$seo,
        );

        $success['token'] =  '';

        //dd($arr);

        return $this->sendResponse($success, $arr);

        
    }

    public function jobDetail(Request $request, $job_slug)
    {
        try {
            $job = Job::select(
                'jobs.id',
                'companies.name AS company_name',
                'jobs.title',
                'jobs.description',
                'jobs.benefits',
                'countries.country As country_name',
                'states.state As state_name',
                'cities.city AS city_name',
                'jobs.is_freelance',
                'career_levels.career_level',
                'jobs.salary_from',
                'jobs.salary_to',
                'jobs.hide_salary',
                'jobs.salary_currency',
                'salary_periods.salary_period',
                'functional_areas.functional_area',
                'job_types.job_type',
                'job_shifts.job_shift',
                'jobs.num_of_positions',
                'genders.gender',
                'jobs.expiry_date',
                'degree_levels.degree_level',
                'job_experiences.job_experience',
                'jobs.is_active',
                'jobs.is_featured',
                'jobs.created_at',
                'jobs.updated_at',
                'jobs.search',
                'jobs.slug',
                'jobs.reference',
                'jobs.location',
                'companies.name AS company_name',
                'companies.logo AS company_logo',
                'jobs.type',
                'jobs.postal_code',
                'jobs.job_advertiser',
                'jobs.application_url',
                'jobs.json_object' )
                ->leftJoin('companies','companies.id','=','jobs.company_id')
                ->leftJoin('countries','countries.id','=','jobs.country_id')
                ->leftJoin('states','states.id','=','jobs.state_id')
                ->leftJoin('cities','cities.id','=','jobs.city_id')
                ->leftJoin('career_levels','career_levels.id','=','jobs.career_level_id')
                ->leftJoin('salary_periods','salary_periods.id','=','jobs.salary_period_id')
                ->leftJoin('functional_areas','functional_areas.id','=','jobs.functional_area_id')
                ->leftJoin('job_types','job_types.id','=','jobs.job_type_id')
                ->leftJoin('job_shifts','job_shifts.id','=','jobs.job_shift_id')
                ->leftJoin('genders','genders.id','=','jobs.gender_id')
                ->leftJoin('degree_levels','degree_levels.id','=','jobs.degree_level_id')
                ->leftJoin('job_experiences','job_experiences.id','=','jobs.job_experience_id')
                ->where('jobs.slug', 'like', $job_slug)
                ->first();
                
            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found'
                ], 404);
            }
            
            // Increment view count if num_views column exists
            if (\Schema::hasColumn('jobs', 'num_views')) {
                \DB::table('jobs')->where('slug', $job_slug)->increment('num_views');
            }
            
            if ($job->company_logo) {
                $job->company_logo = asset('company_logos/' . $job->company_logo);
            }

            /*         * ************************************************** */
            $search = '';
            $job_titles = array();
            $company_ids = array();
            $industry_ids = array();
            $job_skill_ids = (array) $job->getJobSkillsArray();
            $functional_area_ids = (array) $job->getFunctionalArea('functional_area_id');
            $country_ids = (array) $job->getCountry('country_id');
            $state_ids = (array) $job->getState('state_id');
            $city_ids = (array) $job->getCity('city_id');
            $is_freelance = $job->is_freelance;
            $career_level_ids = (array) $job->getCareerLevel('career_level_id');
            $job_type_ids = (array) $job->getJobType('job_type_id');
            $job_shift_ids = (array) $job->getJobShift('job_shift_id');
            $gender_ids = (array) $job->getGender('gender_id');
            $degree_level_ids = (array) $job->getDegreeLevel('degree_level_id');
            $job_experience_ids = (array) $job->getJobExperience('job_experience_id');
            $salary_from = 0;
            $salary_to = 0;
            $salary_currency = '';
            $is_featured = 2;
            $order_by = 'id';
            $limit = 5;

            $relatedJobs = $this->fetchJobs($search, $job_titles, $company_ids, $industry_ids, $job_skill_ids, $functional_area_ids, $country_ids, $state_ids, $city_ids, $is_freelance, $career_level_ids, $job_type_ids, $job_shift_ids, $gender_ids, $degree_level_ids, $job_experience_ids, $salary_from, $salary_to, $salary_currency, $is_featured, $order_by, $limit);

            $related_jobs_id_values = array();

            if(null!==($relatedJobs)){
                foreach ($relatedJobs as $key => $value) {
                    $company = $value->getCompany();
                    if(isset($company)){
                        $related_jobs_id_values[$key] = (object)array(
                            'company_logo' => $value->getCompany()->logo ? asset('company_logos/' . $value->getCompany()->logo) : asset('admin_assets/no-image.png'),
                            'company_name' => $value->getCompany()->name,
                            'company_slug' => $value->getCompany()->slug,
                            'job_type' => $value->getJobType('job_type'),
                            'city' => $value->getCity('city'),
                            'country' => $value->getCountry('country'),
                            'state' => $value->getState('state'),
                            'career_level' => $value->getCareerLevel('career_level'),
                            'functional_area' => $value->getFunctionalArea('functional_area'),
                            'job_shift' => $value->getJobShift('job_shift'),
                            'gender' => $value->getGender('gender'),
                            'degree_level' => $value->getDegreeLevel('degree_level'),
                            'job_experience' => $value->getJobExperience('job_experience'),
                            'description' => \Illuminate\Support\Str::limit(strip_tags($value->description), 150, '...'),
                        );
                    }
                }
            }
            /*         * ***************************************** */

            $seoArray = $this->getSEO((array) $job->functional_area_id, (array) $job->country_id, (array) $job->state_id, (array) $job->city_id, (array) $job->career_level_id, (array) $job->job_type_id, (array) $job->job_shift_id, (array) $job->gender_id, (array) $job->degree_level_id, (array) $job->job_experience_id);
            /*         * ************************************************** */
            $seo = (object) array(
                        'seo_title' => $job->title,
                        'seo_description' => $seoArray['description'],
                        'seo_keywords' => $seoArray['keywords'],
                        'seo_other' => ''
            );

            // Get job questions
            $jobQuestions = JobQuestion::where('job_id', $job->id)
                ->orderBy('order', 'asc')
                ->select('id', 'job_id', 'question_title', 'order', 'created_at', 'updated_at')
                ->get();

            $arr = array(
                'job'=>$job,
                'job_questions'=>$jobQuestions,
                'relatedJobs'=>$relatedJobs,
                'related_jobs_id_values'=>$related_jobs_id_values,
                'seo'=>$seo,
            );

            $success['token'] =  '';

            return $this->sendResponse($success, $arr);
            
        } catch (\Exception $e) {
            \Log::error('Error in jobDetail method: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching job details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /*     * ************************************************** */

    public function addToFavouriteJob(Request $request, $job_slug)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $user_id = Auth::guard('api')->user()->id;
        
        // Check if job exists
        $job = Job::where('slug', $job_slug)->first();
        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }
        
        // Check if already in favourites
        $check_list = FavouriteJob::where([
            ['user_id', '=', $user_id],
            ['job_slug', '=', $job_slug]
        ])->exists();
        
        if ($check_list) {
            return response()->json([
                'success' => false,
                'message' => 'Job is already in your favourites list'
            ], 400);
        }
        
        // Add to favourites
        try {
            FavouriteJob::create([
                'user_id' => $user_id,
                'job_slug' => $job_slug
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Job has been added to favourites list',
                'data' => [
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                    'job_slug' => $job_slug
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add job to favourites. Please try again.'
            ], 500);
        }
    }

    public function removeFromFavouriteJob(Request $request, $job_slug)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $user_id = Auth::guard('api')->user()->id;
        
        // Check if job exists
        $job = Job::where('slug', $job_slug)->first();
        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }
        
        // Check if job is in user's favourites
        $favourite_job = FavouriteJob::where([
            ['user_id', '=', $user_id],
            ['job_slug', '=', $job_slug]
        ])->first();
        
        if (!$favourite_job) {
            return response()->json([
                'success' => false,
                'message' => 'Job is not in your favourites list'
            ], 400);
        }
        
        // Remove from favourites
        try {
            $favourite_job->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Job has been removed from favourites list',
                'data' => [
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                    'job_slug' => $job_slug
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove job from favourites. Please try again.'
            ], 500);
        }
    }

    public function checkFavouriteStatus(Request $request, $job_slug)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $user_id = Auth::guard('api')->user()->id;
        
        // Check if job exists
        $job = Job::where('slug', $job_slug)->first();
        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found',
                'is_favourite' => false
            ], 404);
        }
        
        // Check if job is in user's favourites
        $is_favourite = FavouriteJob::where([
            ['user_id', '=', $user_id],
            ['job_slug', '=', $job_slug]
        ])->exists();
        
        return response()->json([
            'success' => true,
            'is_favourite' => $is_favourite,
            'job_id' => $job->id,
            'job_title' => $job->title
        ]);
    }

    public function applyJob(Request $request, $job_slug)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $user = Auth::guard('api')->user();

        // Check if user account is active
        if ((bool)$user->is_active === false) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact site admin to activate it.'
            ], 403);
        }

        // Check if job exists
        $job = Job::select(
            'jobs.id',
            'companies.name AS company_name',
            'companies.logo AS company_logo',
            'companies.slug AS company_slug',
            'jobs.title',
            'jobs.description',
            'jobs.benefits',
            'countries.country As country_name',
            'states.state As state_name',
            'cities.city AS city_name',
            'jobs.is_freelance',
            'career_levels.career_level',
            'jobs.salary_from',
            'jobs.salary_to',
            'jobs.hide_salary',
            'jobs.salary_currency',
            'salary_periods.salary_period',
            'functional_areas.functional_area',
            'job_types.job_type',
            'job_shifts.job_shift',
            'jobs.num_of_positions',
            'genders.gender',
            'jobs.expiry_date',
            'degree_levels.degree_level',
            'job_experiences.job_experience',
            'jobs.is_active',
            'jobs.is_featured',
            'jobs.created_at',
            'jobs.updated_at',
            'jobs.search',
            'jobs.slug',
            'jobs.reference',
            'jobs.location',
            'jobs.logo',
            'jobs.type',
            'jobs.postal_code',
            'jobs.job_advertiser',
            'jobs.application_url',
            'jobs.json_object'
        )
        ->leftJoin('companies','companies.id','=','jobs.company_id')
        ->leftJoin('countries','countries.id','=','jobs.country_id')
        ->leftJoin('states','states.id','=','jobs.state_id')
        ->leftJoin('cities','cities.id','=','jobs.city_id')
        ->leftJoin('career_levels','career_levels.id','=','jobs.career_level_id')
        ->leftJoin('salary_periods','salary_periods.id','=','jobs.salary_period_id')
        ->leftJoin('functional_areas','functional_areas.id','=','jobs.functional_area_id')
        ->leftJoin('job_types','job_types.id','=','jobs.job_type_id')
        ->leftJoin('job_shifts','job_shifts.id','=','jobs.job_shift_id')
        ->leftJoin('genders','genders.id','=','jobs.gender_id')
        ->leftJoin('degree_levels','degree_levels.id','=','jobs.degree_level_id')
        ->leftJoin('job_experiences','job_experiences.id','=','jobs.job_experience_id')
        ->where('jobs.slug', $job_slug)
        ->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        // Check if job is active
        if (!$job->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This job is no longer active'
            ], 400);
        }

        // Check package quota if enabled
        if ((bool) config('jobseeker.is_jobseeker_package_active')) {
            if (
                ($user->jobs_quota <= $user->availed_jobs_quota) ||
                ($user->package_end_date && $user->package_end_date->lt(Carbon::now()))
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please subscribe to a package first to apply for jobs'
                ], 400);
            }
        }

        // Check if already applied
        if ($user->isAppliedOnJob($job->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this job'
            ], 400);
        }

        // Get user's CVs
        $myCvs = ProfileCv::where('user_id', $user->id)
            ->select('id', 'title', 'cv_file', 'created_at')
            ->get();

        // Get job questions
        $jobQuestions = JobQuestion::where('job_id', $job->id)
            ->orderBy('order', 'asc')
            ->select('id', 'job_id', 'question_title', 'order', 'created_at', 'updated_at')
            ->get();

        $arr = array(
            'job_slug' => $job_slug,
            'job' => $job,
            'myCvs' => $myCvs,
            'job_questions' => $jobQuestions,
            'can_apply' => true
        );

        return response()->json([
            'success' => true,
            'message' => 'Job application form loaded successfully',
            'data' => $arr
        ]);
    }

    public function postApplyJob(Request $request, $job_slug)
    {
        try {
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = Auth::guard('api')->user();
            $user_id = $user->id;
            
            // Log the incoming request for debugging
            \Log::info('Job Application Request:', [
                'user_id' => $user_id,
                'job_slug' => $job_slug,
                'request_data' => $request->all()
            ]);
            
            // Check if job exists
            $job = Job::where('slug', $job_slug)->first();
            if (!$job) {
                \Log::error('Job not found for slug: ' . $job_slug);
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found'
                ], 404);
            }

            // Check if job is active
            if (!$job->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This job is no longer active'
                ], 400);
            }

            // Check if already applied
            if ($user->isAppliedOnJob($job->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already applied for this job'
                ], 400);
            }

            // Validate request data
            $request->validate([
                'cv_id' => 'required|integer|exists:profile_cvs,id',
                'current_salary' => 'required|string|max:255',
                'expected_salary' => 'required|string|max:255',
                'currency' => 'required|string|max:10',
            ]);

            // Check if CV exists and belongs to user
            $cv = ProfileCv::where('id', $request->cv_id)
                ->where('user_id', $user_id)
                ->first();
            
            if (!$cv) {
                \Log::error('CV not found or does not belong to user', [
                    'cv_id' => $request->cv_id,
                    'user_id' => $user_id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'CV not found or does not belong to you'
                ], 400);
            }

            // Create job application
            $jobApply = new JobApply();
            $jobApply->user_id = $user_id;
            $jobApply->job_id = $job->id;
            $jobApply->cv_id = $request->cv_id;
            $jobApply->current_salary = $request->current_salary;
            $jobApply->expected_salary = $request->expected_salary;
            $jobApply->salary_currency = $request->currency; // Map 'currency' to 'salary_currency'
            $jobApply->save();

            // Save job question answers if provided
            if ($request->has('question_answers') && is_array($request->question_answers)) {
                foreach ($request->question_answers as $questionId => $answer) {
                    if (!empty($answer)) {
                        // Verify that the question belongs to this job
                        $jobQuestion = JobQuestion::where('id', $questionId)
                            ->where('job_id', $job->id)
                            ->first();
                        
                        if ($jobQuestion) {
                            $jobQuestionAnswer = new JobQuestionAnswer();
                            $jobQuestionAnswer->job_question_id = $questionId;
                            $jobQuestionAnswer->job_apply_id = $jobApply->id;
                            $jobQuestionAnswer->answer = $answer;
                            $jobQuestionAnswer->save();
                        }
                    }
                }
            }

            \Log::info('Job application created successfully', [
                'application_id' => $jobApply->id,
                'user_id' => $user_id,
                'job_id' => $job->id,
                'job_title' => $job->title
            ]);

            // Update user's job quota if package system is active
            if ((bool) config('jobseeker.is_jobseeker_package_active')) {
                $user->availed_jobs_quota = $user->availed_jobs_quota + 1;
                $user->update();
            }

            // Fire job applied event
            event(new JobApplied($job, $jobApply));

            return response()->json([
                'success' => true,
                'message' => 'You have successfully applied for this job',
                'data' => [
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                    'application_id' => $jobApply->id,
                    'applied_at' => $jobApply->created_at
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in postApplyJob: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in postApplyJob: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply for job. Please try again.'
            ], 500);
        }
    }

    public function myJobApplications(Request $request)
{
    $user = Auth::guard('api')->user();

    $myAppliedJobIds = $user->getAppliedJobIdsArray();
    
    if (empty($myAppliedJobIds)) {
        $arr = array(
            'jobs' => [],
            'message' => 'No job applications found'
        );
        $success['token'] = 'success';
        return $this->sendResponse($success, $arr);
    }
    
    // Get jobs with application status from job_apply table
    $jobs = Job::select(
        'jobs.id',
        'companies.name AS company_name',
        'companies.logo AS company_logo',
        'companies.slug AS company_slug',
        'jobs.title',
        'jobs.description',
        'jobs.benefits',
        'countries.country As country_name',
        'states.state As state_name',
        'cities.city AS city_name',
        'jobs.is_freelance',
        'career_levels.career_level',
        'jobs.salary_from',
        'jobs.salary_to',
        'jobs.hide_salary',
        'jobs.salary_currency',
        'salary_periods.salary_period',
        'functional_areas.functional_area',
        'job_types.job_type',
        'job_shifts.job_shift',
        'jobs.num_of_positions',
        'genders.gender',
        'jobs.expiry_date',
        'degree_levels.degree_level',
        'job_experiences.job_experience',
        'jobs.is_active',
        'jobs.is_featured',
        'jobs.created_at',
        'jobs.updated_at',
        'jobs.search',
        'jobs.slug',
        'jobs.reference',
        'jobs.location',
        'jobs.logo',
        'jobs.type',
        'jobs.postal_code',
        'jobs.job_advertiser',
        'jobs.application_url',
        'jobs.json_object',
        // Add application status and details
        'job_apply.status as application_status',
        'job_apply.created_at as applied_at',
        'job_apply.current_salary',
        'job_apply.expected_salary',
        'job_apply.salary_currency as application_salary_currency'
    )
    ->leftJoin('companies','companies.id','=','jobs.company_id')
    ->leftJoin('countries','countries.id','=','jobs.country_id')
    ->leftJoin('states','states.id','=','jobs.state_id')
    ->leftJoin('cities','cities.id','=','jobs.city_id')
    ->leftJoin('career_levels','career_levels.id','=','jobs.career_level_id')
    ->leftJoin('salary_periods','salary_periods.id','=','jobs.salary_period_id')
    ->leftJoin('functional_areas','functional_areas.id','=','jobs.functional_area_id')
    ->leftJoin('job_types','job_types.id','=','jobs.job_type_id')
    ->leftJoin('job_shifts','job_shifts.id','=','jobs.job_shift_id')
    ->leftJoin('genders','genders.id','=','jobs.gender_id')
    ->leftJoin('degree_levels','degree_levels.id','=','jobs.degree_level_id')
    ->leftJoin('job_experiences','job_experiences.id','=','jobs.job_experience_id')
    // Add join with job_apply table to get application status
    ->leftJoin('job_apply', function($join) use ($user) {
        $join->on('job_apply.job_id', '=', 'jobs.id')
             ->where('job_apply.user_id', '=', $user->id);
    })
    ->whereIn('jobs.id', $myAppliedJobIds)
    ->orderBy('job_apply.created_at', 'desc') // Order by application date
    ->get();

    // Process company logos and add additional data
    $jobs_id_values = array();
    if ($jobs->count() > 0) {
        foreach ($jobs as $key => $value) {
            $company = $value->getCompany();
            if (isset($company)) {
                $jobs_id_values[$key] = (object)array(
                    'company_logo' => $value->company_logo ? asset('company_logos/' . $value->company_logo) : asset('admin_assets/no-image.png'),
                    'company_name' => $value->company_name,
                    'company_slug' => $value->company_slug,
                    'job_type' => $value->getJobType('job_type'),
                    'city' => $value->getCity('city'),
                    'country' => $value->getCountry('country'),
                    'state' => $value->getState('state'),
                    'career_level' => $value->getCareerLevel('career_level'),
                    'functional_area' => $value->getFunctionalArea('functional_area'),
                    'job_shift' => $value->getJobShift('job_shift'),
                    'gender' => $value->getGender('gender'),
                    'degree_level' => $value->getDegreeLevel('degree_level'),
                    'job_experience' => $value->getJobExperience('job_experience'),
                    'description' => \Illuminate\Support\Str::limit(strip_tags($value->description), 150, '...'),
                );
            }
        }
    }

    $arr = array(
        'jobs' => $jobs,
        'id_values' => $jobs_id_values,
        'total_applications' => $jobs->count()
    );

    $success['token'] = 'success';
    return $this->sendResponse($success, $arr);
}

    public function myFavouriteJobs(Request $request)
    {
        $user = Auth::guard('api')->user();

        $myFavouriteJobSlugs = $user->getFavouriteJobSlugsArray();
        
        if (empty($myFavouriteJobSlugs)) {
            $arr = array(
                'jobs' => [],
                'message' => 'No favourite jobs found'
            );
            $success['token'] = 'success';
            return $this->sendResponse($success, $arr);
        }
        
        $jobs = Job::select(
            'jobs.id',
            'companies.name AS company_name',
            'companies.logo AS company_logo',
            'companies.slug AS company_slug',
            'jobs.title',
            'jobs.description',
            'jobs.benefits',
            'countries.country As country_name',
            'states.state As state_name',
            'cities.city AS city_name',
            'jobs.is_freelance',
            'career_levels.career_level',
            'jobs.salary_from',
            'jobs.salary_to',
            'jobs.hide_salary',
            'jobs.salary_currency',
            'salary_periods.salary_period',
            'functional_areas.functional_area',
            'job_types.job_type',
            'job_shifts.job_shift',
            'jobs.num_of_positions',
            'genders.gender',
            'jobs.expiry_date',
            'degree_levels.degree_level',
            'job_experiences.job_experience',
            'jobs.is_active',
            'jobs.is_featured',
            'jobs.created_at',
            'jobs.updated_at',
            'jobs.search',
            'jobs.slug',
            'jobs.reference',
            'jobs.location',
            'jobs.logo',
            'jobs.type',
            'jobs.postal_code',
            'jobs.job_advertiser',
            'jobs.application_url',
            'jobs.json_object'
        )
        ->leftJoin('companies','companies.id','=','jobs.company_id')
        ->leftJoin('countries','countries.id','=','jobs.country_id')
        ->leftJoin('states','states.id','=','jobs.state_id')
        ->leftJoin('cities','cities.id','=','jobs.city_id')
        ->leftJoin('career_levels','career_levels.id','=','jobs.career_level_id')
        ->leftJoin('salary_periods','salary_periods.id','=','jobs.salary_period_id')
        ->leftJoin('functional_areas','functional_areas.id','=','jobs.functional_area_id')
        ->leftJoin('job_types','job_types.id','=','jobs.job_type_id')
        ->leftJoin('job_shifts','job_shifts.id','=','jobs.job_shift_id')
        ->leftJoin('genders','genders.id','=','jobs.gender_id')
        ->leftJoin('degree_levels','degree_levels.id','=','jobs.degree_level_id')
        ->leftJoin('job_experiences','job_experiences.id','=','jobs.job_experience_id')
        ->whereIn('jobs.slug', $myFavouriteJobSlugs)
        ->orderBy('jobs.created_at', 'desc')
        ->get();

        // Process company logos and add additional data
        $jobs_id_values = array();
        if ($jobs->count() > 0) {
            foreach ($jobs as $key => $value) {
                $company = $value->getCompany();
                if (isset($company)) {
                    $jobs_id_values[$key] = (object)array(
                        'company_logo' => $value->company_logo ? asset('company_logos/' . $value->company_logo) : asset('admin_assets/no-image.png'),
                        'company_name' => $value->company_name,
                        'company_slug' => $value->company_slug,
                        'job_type' => $value->getJobType('job_type'),
                        'city' => $value->getCity('city'),
                        'country' => $value->getCountry('country'),
                        'state' => $value->getState('state'),
                        'career_level' => $value->getCareerLevel('career_level'),
                        'functional_area' => $value->getFunctionalArea('functional_area'),
                        'job_shift' => $value->getJobShift('job_shift'),
                        'gender' => $value->getGender('gender'),
                        'degree_level' => $value->getDegreeLevel('degree_level'),
                        'job_experience' => $value->getJobExperience('job_experience'),
                        'description' => \Illuminate\Support\Str::limit(strip_tags($value->description), 150, '...'),
                    );
                }
            }
        }

        $arr = array(
            'jobs' => $jobs,
            'id_values' => $jobs_id_values,
            'total_favourites' => $jobs->count()
        );

        $success['token'] = 'success';
        return $this->sendResponse($success, $arr);
    }


    public function getFavouriteJobsCount(Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $user_id = Auth::guard('api')->user()->id;
        
        $count = FavouriteJob::where('user_id', $user_id)->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'favourite_jobs_count' => $count
            ]
        ]);
    }

    public function getAppliedJobsCount(Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $user = Auth::guard('api')->user();
        $myAppliedJobIds = $user->getAppliedJobIdsArray();
        
        return response()->json([
            'success' => true,
            'data' => [
                'applied_jobs_count' => count($myAppliedJobIds)
            ]
        ]);
    }

    public function job_categories()
    {
        try {
            DB::statement("SET SQL_MODE=''");
            
            $job_categories = DB::table('jobs As jb')
                ->select(
                    'jb.functional_area_id',
                    'func_area.functional_area',
                    'func_area.image as logo'
                )
                ->addSelect(DB::raw('COUNT(jb.functional_area_id) as jobs_count'))
                ->leftJoin('functional_areas AS func_area', function($join) {
                    $join->on('func_area.id', '=', 'jb.functional_area_id');
                })
                ->groupBy('jb.functional_area_id')
                ->orderBy('jobs_count', 'desc')
                ->get();

            if ($job_categories->count() > 0) {
                return response()->json([
                    'success' => true,
                    'data' => $job_categories,
                    'total_categories' => $job_categories->count()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No job categories found',
                    'data' => []
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch job categories. Please try again.',
                'data' => []
            ], 500);
        }
    }

    public function jobs_by_category(Request $request, $id)
    {
        // Validate request
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Job category ID is required',
                'data' => []
            ], 400);
        }

        try {
            \Log::info('Fetching jobs for category ID: ' . $id);
            
            $jobs = Job::select(
                'jobs.id',
                'jobs.slug',
                'companies.name AS company_name',
                'companies.logo AS company_logo',
                'jobs.title',
                'jobs.description',
                'countries.country',
                'states.state',
                'cities.city',
                'degree_levels.degree_level',
                'job_experiences.job_experience',
                'jobs.logo',
                'jobs.salary_from',
                'jobs.salary_to',
                'jobs.salary_currency',
                'jobs.created_at'
            )
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('countries', 'countries.id', '=', 'jobs.country_id')
            ->leftJoin('states', 'states.id', '=', 'jobs.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'jobs.city_id')
            ->leftJoin('degree_levels', 'degree_levels.id', '=', 'jobs.degree_level_id')
            ->leftJoin('job_experiences', 'job_experiences.id', '=', 'jobs.job_experience_id')
            ->where('jobs.functional_area_id', $id)
            ->orderBy('jobs.created_at', 'desc')
            ->get();

            \Log::info('Found ' . $jobs->count() . ' jobs for category ID: ' . $id);
            \Log::info('Jobs data: ', $jobs->toArray());

            if ($jobs->count() > 0) {
                // Process company logos
                foreach ($jobs as $job) {
                    if ($job->company_logo) {
                        $job->company_logo = asset('company_logos/' . $job->company_logo);
                    }
                    if ($job->logo) {
                        $job->logo = asset('jobs/' . $job->logo);
                    }
                }

                return response()->json([
                    'success' => true,
                    'data' => $jobs,
                    'total_jobs' => $jobs->count()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No jobs found for this category',
                    'data' => []
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch jobs for this category. Please try again.',
                'data' => []
            ], 500);
        }
    }

    public function display_job_details(Request $request)
    {
        // Validate request
        if (!$request->has('job_id') || !$request->job_id) {
            return response()->json([
                'success' => false,
                'message' => 'Job ID is required',
                'data' => []
            ], 400);
        }

        try {
            $job = Job::select(
                'jobs.id',
                'jobs.slug',
                'companies.name AS company_name',
                'companies.logo AS company_logo',
                'companies.slug AS company_slug',
                'jobs.title',
                'jobs.description',
                'jobs.benefits',
                'countries.country As country_name',
                'states.state As state_name',
                'cities.city AS city_name',
                'jobs.is_freelance',
                'career_levels.career_level',
                'jobs.salary_from',
                'jobs.salary_to',
                'jobs.hide_salary',
                'jobs.salary_currency',
                'salary_periods.salary_period',
                'functional_areas.functional_area',
                'job_types.job_type',
                'job_shifts.job_shift',
                'jobs.num_of_positions',
                'genders.gender',
                'jobs.expiry_date',
                'degree_levels.degree_level',
                'job_experiences.job_experience',
                'jobs.is_active',
                'jobs.is_featured',
                'jobs.created_at',
                'jobs.updated_at',
                'jobs.search',
                'jobs.reference',
                'jobs.location',
                'jobs.logo',
                'jobs.type',
                'jobs.postal_code',
                'jobs.job_advertiser',
                'jobs.application_url',
                'jobs.json_object'
            )
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('countries', 'countries.id', '=', 'jobs.country_id')
            ->leftJoin('states', 'states.id', '=', 'jobs.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'jobs.city_id')
            ->leftJoin('career_levels', 'career_levels.id', '=', 'jobs.career_level_id')
            ->leftJoin('salary_periods', 'salary_periods.id', '=', 'jobs.salary_period_id')
            ->leftJoin('functional_areas', 'functional_areas.id', '=', 'jobs.functional_area_id')
            ->leftJoin('job_types', 'job_types.id', '=', 'jobs.job_type_id')
            ->leftJoin('job_shifts', 'job_shifts.id', '=', 'jobs.job_shift_id')
            ->leftJoin('genders', 'genders.id', '=', 'jobs.gender_id')
            ->leftJoin('degree_levels', 'degree_levels.id', '=', 'jobs.degree_level_id')
            ->leftJoin('job_experiences', 'job_experiences.id', '=', 'jobs.job_experience_id')
            ->where('jobs.id', $request->job_id)
            ->where('jobs.is_active', 1)
            ->first();

            if ($job) {
                // Process company logo
                if ($job->company_logo) {
                    $job->company_logo = asset('company_logos/' . $job->company_logo);
                }
                if ($job->logo) {
                    $job->logo = asset('jobs/' . $job->logo);
                }

                return response()->json([
                    'success' => true,
                    'data' => $job
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found or inactive',
                    'data' => []
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch job details. Please try again.',
                'data' => []
            ], 500);
        }
    }

    public function apply_job_form(Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Validate request
        if (!$request->has('job_id') || !$request->job_id) {
            return response()->json([
                'success' => false,
                'message' => 'Job ID is required',
                'data' => []
            ], 400);
        }

        try {
            $user_id = Auth::guard('api')->user()->id;
            
            // Get job details
            $job = Job::select(
                'jobs.id',
                'jobs.slug',
                'jobs.title',
                'jobs.num_of_positions',
                'jobs.logo',
                'companies.name AS company_name',
                'companies.email AS company_email',
                'companies.logo AS company_logo'
            )
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
            ->where('jobs.id', $request->job_id)
            ->where('jobs.is_active', 1)
            ->first();

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found or inactive',
                    'data' => []
                ]);
            }

            // Check if already applied
            $alreadyApplied = JobApply::where([
                ['user_id', '=', $user_id],
                ['job_id', '=', $request->job_id]
            ])->exists();

            if ($alreadyApplied) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already applied for this job',
                    'data' => []
                ]);
            }

            // Get user's CVs
            $userCvs = ProfileCv::select('id', 'title', 'cv_file', 'created_at')
                ->where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get job questions
            $jobQuestions = JobQuestion::where('job_id', $job->id)
                ->orderBy('order', 'asc')
                ->select('id', 'job_id', 'question_title', 'order', 'created_at', 'updated_at')
                ->get();

            // Process company logo
            if ($job->company_logo) {
                $job->company_logo = asset('company_logos/' . $job->company_logo);
            }
            if ($job->logo) {
                $job->logo = asset('jobs/' . $job->logo);
            }

            return response()->json([
                'success' => true,
                'message' => 'Job application form loaded successfully',
                'data' => [
                    'job' => $job,
                    'user_cvs' => $userCvs,
                    'job_questions' => $jobQuestions,
                    'can_apply' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load job application form. Please try again.',
                'data' => []
            ], 500);
        }
    }

    public function store_apply_job(Request $request)
    {
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Validate request
        $request->validate([
            'job_cv' => 'nullable|mimes:pdf,docx|max:5048',
            'job_id' => 'required|integer',
            'cv_id' => 'nullable|integer',
            'current_salary' => 'nullable|numeric',
            'expected_salary' => 'nullable|numeric',
            'salary_currency' => 'nullable|string'
        ]);

        try {
            $user_id = Auth::guard('api')->user()->id;
            $user_name = Auth::guard('api')->user()->name;

            // Check if job exists and is active
            $job = Job::where('id', $request->job_id)
                ->where('is_active', 1)
                ->first();

            if (!$job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found or inactive'
                ], 404);
            }

            // Check if already applied
            $jobApplyValidity = JobApply::where([
                ['user_id', '=', $user_id],
                ['job_id', '=', $request->job_id]
            ])->exists();

            if ($jobApplyValidity) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already applied for this job'
                ], 400);
            }

            $user_cv_id = null;
            $file_name = null;

            // Handle CV upload or selection
            if (!isset($request->cv_id)) {
                if ($request->hasFile('job_cv')) {
                    $file_name = time() . '_' . $request->file('job_cv')->getClientOriginalName();
                    $uploaded = $request->job_cv->move(public_path('uploads'), $file_name);
                    
                    if ($uploaded) {
                        $cv_title = strtoupper(explode('.', $file_name)[0]);
                        $storeProfileCv = ProfileCv::create([
                            'user_id' => $user_id,
                            'title' => $cv_title,
                            'cv_file' => $file_name
                        ]);
                        $user_cv_id = $storeProfileCv->id;
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please provide either a CV file or select an existing CV'
                    ], 400);
                }
            } else {
                // Validate that CV belongs to user
                $cv = ProfileCv::where('id', $request->cv_id)
                    ->where('user_id', $user_id)
                    ->first();
                
                if (!$cv) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid CV selected'
                    ], 400);
                }
                $user_cv_id = $request->cv_id;
            }

            // Create job application
            $jobApplied = JobApply::create([
                'user_id' => $user_id,
                'job_id' => $request->job_id,
                'cv_id' => $user_cv_id,
                'current_salary' => $request->current_salary,
                'expected_salary' => $request->expected_salary,
                'salary_currency' => $request->salary_currency
            ]);

            // Save job question answers if provided
            if ($jobApplied && $request->has('question_answers') && is_array($request->question_answers)) {
                foreach ($request->question_answers as $questionId => $answer) {
                    if (!empty($answer)) {
                        // Verify that the question belongs to this job
                        $jobQuestion = JobQuestion::where('id', $questionId)
                            ->where('job_id', $request->job_id)
                            ->first();
                        
                        if ($jobQuestion) {
                            $jobQuestionAnswer = new JobQuestionAnswer();
                            $jobQuestionAnswer->job_question_id = $questionId;
                            $jobQuestionAnswer->job_apply_id = $jobApplied->id;
                            $jobQuestionAnswer->answer = $answer;
                            $jobQuestionAnswer->save();
                        }
                    }
                }
            }

            if ($jobApplied) {
                // Send email notification if CV file was uploaded
                if ($file_name && $request->has('company_email')) {
                    try {
                        $cv_filename = public_path() . '/uploads/' . $file_name;
                        Mail::send('emails.job_apply_email', [
                            'applicant_name' => $user_name,
                            'job_title' => $job->title
                        ], function($message) use ($request, $cv_filename, $file_name) {
                            $message->to($request->company_email);
                            $message->subject('New Job Application');
                            $message->attach($cv_filename, [
                                'as' => $file_name,
                                'mime' => 'application/pdf'
                            ]);
                        });
                    } catch (\Exception $e) {
                        // Log email error but don't fail the application
                        \Log::error('Failed to send job application email: ' . $e->getMessage());
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'You have successfully applied for the job: ' . $job->title,
                    'data' => [
                        'application_id' => $jobApplied->id,
                        'job_id' => $job->id,
                        'job_title' => $job->title,
                        'applied_at' => $jobApplied->created_at
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit job application. Please try again.'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit job application. Please try again.'
            ], 500);
        }
    }

    public function job_search(Request $request)
    {
        try {
            $search_query = Job::select(
                'jobs.id',
                'jobs.slug',
                'jobs.title',
                'jobs.description',
                'jobs.benefits',
                'jobs.location',
                'jobs.logo',
                'jobs.salary_from',
                'jobs.salary_to',
                'jobs.salary_currency',
                'jobs.created_at',
                'companies.name AS company_name',
                'companies.logo AS company_logo',
                'countries.country',
                'states.state',
                'cities.city',
                'functional_areas.functional_area',
                'job_types.job_type',
                'job_shifts.job_shift'
            )
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('countries', 'countries.id', '=', 'jobs.country_id')
            ->leftJoin('states', 'states.id', '=', 'jobs.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'jobs.city_id')
            ->leftJoin('functional_areas', 'functional_areas.id', '=', 'jobs.functional_area_id')
            ->leftJoin('job_types', 'job_types.id', '=', 'jobs.job_type_id')
            ->leftJoin('job_shifts', 'job_shifts.id', '=', 'jobs.job_shift_id')
            ->where('jobs.is_active', 1);

            // Apply search filters
            if ($request->filled('city')) {
                $search_query->whereIn('cities.city', (array) $request->city);
            }

            if ($request->filled('country')) {
                $search_query->whereIn('countries.country', (array) $request->country);
            }

            if ($request->filled('gender')) {
                $search_query->where('jobs.gender', $request->gender);
            }

            if ($request->filled('citizenship')) {
                $search_query->where('jobs.citizenship', $request->citizenship);
            }

            if ($request->filled('religiosness')) {
                $search_query->where('jobs.religiosness', $request->religiosness);
            }

            if ($request->filled('sect')) {
                $search_query->where('jobs.sect', $request->sect);
            }

            if ($request->filled('hijab')) {
                $search_query->where('jobs.hijab', $request->hijab);
            }

            if ($request->filled('beard')) {
                $search_query->where('jobs.beard', $request->beard);
            }

            if ($request->filled('converted')) {
                $search_query->where('jobs.converted', $request->converted);
            }

            if ($request->filled('employment')) {
                $search_query->where('jobs.employment', $request->employment);
            }

            if ($request->filled('subjectstudied')) {
                $search_query->whereIn('jobs.subjectstudied', (array) $request->subjectstudied);
            }

            if ($request->filled('maritalstatus')) {
                $search_query->where('jobs.maritalstatus', $request->maritalstatus);
            }

            if ($request->filled('height')) {
                $heightRange = (array) $request->height;
                if (count($heightRange) === 2) {
                    $search_query->whereBetween('jobs.height', $heightRange);
                }
            }

            if ($request->filled('smoke')) {
                $search_query->where('jobs.smoke', $request->smoke);
            }

            if ($request->filled('disabilities')) {
                $search_query->where('jobs.disabilities', $request->disabilities);
            }

            // Add text search if provided
            if ($request->filled('search')) {
                $search_query->where(function($q) use ($request) {
                    $q->where('jobs.title', 'like', '%' . $request->search . '%')
                      ->orWhere('jobs.description', 'like', '%' . $request->search . '%')
                      ->orWhere('jobs.benefits', 'like', '%' . $request->search . '%');
                });
            }

            $search_results = $search_query->orderBy('jobs.created_at', 'desc')->get();

            if ($search_results->count() > 0) {
                // Process company logos
                foreach ($search_results as $job) {
                    if ($job->company_logo) {
                        $job->company_logo = asset('company_logos/' . $job->company_logo);
                    }
                    if ($job->logo) {
                        $job->logo = asset('jobs/' . $job->logo);
                    }
                }

                return response()->json([
                    'success' => true,
                    'data' => $search_results,
                    'total_results' => $search_results->count()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No jobs found matching your criteria',
                    'data' => [],
                    'total_results' => 0
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform job search. Please try again.',
                'data' => []
            ], 500);
        }
    }

}
