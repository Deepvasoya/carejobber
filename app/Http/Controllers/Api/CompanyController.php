<?php

namespace App\Http\Controllers\Api;

use Mail;
use Hash;
use File;
use ImgUploader;
use Auth;
use Validator;
use DB;
use Input;
use Redirect;
use App\Subscription;
use Newsletter;
use App\User;
use App\Company;
use App\CompanyMessage;
use App\ApplicantMessage;
use App\Country;
use App\CountryDetail;
use App\JobApplyRejected;
use App\State;
use App\City;
use App\Unlocked_users;
use App\Industry;
use App\FavouriteCompany;
use App\Package;
use App\FavouriteApplicant;
use App\OwnershipType;
use App\JobApply;
use Carbon\Carbon;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Http\Requests;
use App\Mail\CompanyContactMail;
use App\Mail\ApplicantContactMail;
use App\Mail\JobSeekerRejectedMailable;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Front\CompanyFrontFormRequest;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Traits\CompanyApiTrait;
use App\Traits\Cron;
use Illuminate\Support\Str;

class CompanyController extends BaseController
{

    use CompanyApiTrait;
    use Cron;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('company', ['except' => ['companyDetail', 'sendContactForm']]);
        //$this->runCheckPackageValidity();
    }

    public function index()
    {
        return view('company_home');
    }
    public function company_listing()
    {
        $data['companies']=Company::paginate(20);

        $company_id_values = array();

        if(null!==($data['companies'])){
            foreach ($data['companies'] as $key => $value) {//dd($value);
                if(isset($value)){
                    $company_id_values[$key] = (object)array(
                        'company_logo' => $value->logo ? asset('company_logos/' . $value->logo) : asset('admin_assets/no-image.png'),
                        'slug' => route('company.detail',$value->slug),
                        'current_jobs' => $value->countNumJobs('company_id',$value->id),
                    );
                }
                
            }
        }

        $data['company_id_values'] = $company_id_values;


        //dd($data);

        $success['success'] =  'done';

        return $this->sendResponse($success, $data);

        //return view('company.listing')->with($data);
    }

    public function companyProfile()
    {
        $countries = DataArrayHelper::defaultCountriesArray();
        $industries = DataArrayHelper::defaultIndustriesArray();
        $ownershipTypes = DataArrayHelper::defaultOwnershipTypesArray();
        $company = Company::findOrFail(Auth::guard('company-api')->user()->id);


        $arr = array(
            'company'=>$company,
            'countries'=>$countries,
            'industries'=>$industries,
            'ownershipTypes'=>$ownershipTypes,
        );

        $success['success'] =  'done';

        return $this->sendResponse($success, $arr);
    }

    public function updateCompanyProfile(CompanyFrontFormRequest $request)
    {
        $company = Company::findOrFail(Auth::guard('company-api')->user()->id);
        /*         * **************************************** */
        if ($request->hasFile('logo')) {
            $is_deleted = $this->deleteCompanyLogo($company->id);
            $image = $request->file('logo');
            $fileName = ImgUploader::UploadImage('company_logos', $image, $request->input('name'), 300, 300, false);
            $company->logo = $fileName;
        }
        /*         * ************************************** */
        $company->name = $request->input('name');
        $company->email = $request->input('email');
        if (!empty($request->input('password'))) {
            $company->password = Hash::make($request->input('password'));
        }
        $company->ceo = $request->input('ceo');
        $company->industry_id = $request->input('industry_id');
        $company->ownership_type_id = $request->input('ownership_type_id');
        $company->description = $request->input('description');
        $company->location = $request->input('location');
        $company->map = $request->input('map');
        $company->no_of_offices = $request->input('no_of_offices');
        $website = $request->input('website');
        $company->website = (false === strpos($website, 'http')) ? 'http://' . $website : $website;
        $company->no_of_employees = $request->input('no_of_employees');
        $company->established_in = $request->input('established_in');
        $company->fax = $request->input('fax');
        $company->phone = $request->input('phone');
        $company->facebook = $request->input('facebook');
        $company->twitter = $request->input('twitter');
        $company->linkedin = $request->input('linkedin');
        $company->google_plus = $request->input('google_plus');
        $company->pinterest = $request->input('pinterest');
        $company->country_id = $request->input('country_id');
        $company->state_id = $request->input('state_id');
        $company->city_id = $request->input('city_id');
		$company->is_subscribed = $request->input('is_subscribed', 0);
		
        $company->slug = Str::slug($company->name, '-') . '-' . $company->id;
        $company->update();
		/*************************/
		Subscription::where('email', 'like', $company->email)->delete();
		if((bool)$company->is_subscribed)
		{			
			$subscription = new Subscription();
			$subscription->email = $company->email;
			$subscription->name = $company->name;
			$subscription->save();
			/*************************/
			Newsletter::subscribeOrUpdate($subscription->email, ['FNAME'=>$subscription->name]);
			/*************************/
		}
		else
		{
			/*************************/
			Newsletter::unsubscribe($company->email);
			/*************************/
		}

        $success['success'] =  'done';

        return $this->sendResponse($success, 'Company has been updated');
        /*flash(__('Company has been updated'))->success();
        return \Redirect::route('company.profile');*/
    }

    public function addToFavouriteApplicant(Request $request, $application_id, $user_id, $job_id, $company_id)
    {
        $data['user_id'] = $user_id;
        $data['job_id'] = $job_id;
        $data['company_id'] = $company_id;

        $data_save = FavouriteApplicant::create($data);

        $success['success'] =  'done';

        return $this->sendResponse($success, 'Job seeker has been added in favorites list');
        //flash(__('Job seeker has been added in favorites list'))->success();
        //return \Redirect::route('applicant.profile', $application_id);
    }

    public function removeFromFavouriteApplicant(Request $request, $application_id, $user_id, $job_id, $company_id)
    {
        $data['user_id'] = $user_id;
        $data['job_id'] = $job_id;
        $data['company_id'] = $company_id;
        FavouriteApplicant::where('user_id', $user_id)
                ->where('job_id', '=', $job_id)
                ->where('company_id', '=', $company_id)
                ->delete();

        $success['success'] =  'done';

        return $this->sendResponse($success, 'Job seeker has been removed from favorites list');          

        //flash(__('Job seeker has been removed from favorites list'))->success();
       // return \Redirect::route('applicant.profile', $application_id);
    } 


    public function hireFromFavouriteApplicant(Request $request, $application_id, $user_id, $job_id, $company_id)
    {
        $data['user_id'] = $user_id;
        $data['job_id'] = $job_id;
        $data['company_id'] = $company_id;
        $fev = FavouriteApplicant::where('user_id', $user_id)
                ->where('job_id', '=', $job_id)
                ->where('company_id', '=', $company_id)
                ->first();
        $fev->status = 'hired';
        $fev->update();        

        //flash(__('Job seeker has been Hired from favorites list'))->success();
        $success['success'] =  'done';

        return $this->sendResponse($success, 'Job seeker has been Hired from favorites list');  
    }

    public function removehireFromFavouriteApplicant(Request $request, $application_id, $user_id, $job_id, $company_id)
    {
        $data['user_id'] = $user_id;
        $data['job_id'] = $job_id;
        $data['company_id'] = $company_id;
        $fev = FavouriteApplicant::where('user_id', $user_id)
                ->where('job_id', '=', $job_id)
                ->where('company_id', '=', $company_id)
                ->first();
        $fev->status = null;
        $fev->update();


        $success['success'] =  'done';

   

        return $this->sendResponse($success, 'Job seeker has been removed from hired list');        

        /*flash(__('Job seeker has been removed from hired list'))->success();
        return \Redirect::route('applicant.profile', $application_id);*/
    }

    public function companyDetail(Request $request, $company_slug)
    {
        $company = Company::where('slug', 'like', $company_slug)->firstOrFail();
        /*         * ************************************************** */
        $seo = $this->getCompanySEO($company);

        $data['id_values'] = array(
            'industry_id' => $company->getIndustry('industry'),
            'ownership_type_id' => $company->getOwnershipType('ownership_type'),
            'country_id' => $company->getCountry('country'),
            'state_id' => $company->getState('state'),
            'city_id' => $company->getCity('city'),
        );


        $data['company']= $company;
        $data['seo']= $seo;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);



        /*         * ************************************************** */
        /*return view('company.detail')
                        ->with('company', $company)
                        ->with('seo', $seo);*/
    }

    public function companyJobs(Request $request)
    {
        $company_slug = $request->get('company_slug');
        $page = $request->get('page', 1);
        $per_page = $request->get('per_page', 10);

        if (!$company_slug) {
            return $this->sendError('Company slug is required', [], 400);
        }

        $company = Company::where('slug', 'like', $company_slug)->first();
        
        if (!$company) {
            return $this->sendError('Company not found', [], 404);
        }

        // Get jobs for this company with pagination
        $jobs = \App\Job::where('company_id', $company->id)
            ->where('is_active', 1)
            ->where('expiry_date', '>', Carbon::now())
            ->with([
                'company',
                'city',
                'state', 
                'country',
                'jobSkills.jobSkill'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'page', $page);

        // Transform the jobs data to match the expected format
        $transformedJobs = $jobs->getCollection()->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'slug' => $job->slug,
                'company_id' => $job->company_id,
                'company_name' => $job->company->name ?? '',
                'company_logo' => $job->company->logo ? asset('company_logos/' . $job->company->logo) : null,
                'location' => $job->location,
                'city_name' => $job->city->city ?? '',
                'state_name' => $job->state->state ?? '',
                'country_name' => $job->country->country ?? '',
                'salary_from' => $job->salary_from,
                'salary_to' => $job->salary_to,
                'salary_currency' => $job->salary_currency,
                'salary_period' => $job->getSalaryPeriod('salary_period'),
                'job_type' => $job->getJobType('job_type'),
                'career_level' => $job->getCareerLevel('career_level'),
                'job_shift' => $job->getJobShift('job_shift'),
                'degree_level' => $job->getDegreeLevel('degree_level'),
                'job_experience' => $job->getJobExperience('job_experience'),
                'description' => $job->description,
                'requirements' => $job->requirements,
                'benefits' => $job->benefits,
                'functional_area' => $job->getFunctionalArea('functional_area'),
                'num_of_positions' => $job->num_of_positions,
                'gender' => $job->getGender('gender'),
                'expiry_date' => $job->expiry_date,
                'created_at' => $job->created_at,
                'updated_at' => $job->updated_at,
                'is_active' => $job->is_active,
                'hide_salary' => $job->hide_salary,
                'job_skills' => $job->jobSkills->map(function ($jobSkill) {
                    return [
                        'id' => $jobSkill->id,
                        'job_id' => $jobSkill->job_id,
                        'job_skill_id' => $jobSkill->job_skill_id,
                        'job_skill' => [
                            'id' => $jobSkill->jobSkill->id ?? 0,
                            'job_skill' => $jobSkill->jobSkill->job_skill ?? ''
                        ]
                    ];
                })
            ];
        });

        $data = [
            'jobs' => $transformedJobs,
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
                'from' => $jobs->firstItem(),
                'to' => $jobs->lastItem(),
            ]
        ];

        $success['success'] = true;

        return $this->sendResponse($success, $data);
    }

    public function sendContactForm(Request $request)
    {
        $msgresponse = Array();
        $rules = array(
            'from_name' => 'required|max:100|between:4,70',
            'from_email' => 'required|email|max:100',
            'subject' => 'required|max:200',
            'message' => 'required',
            'to_id' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        );
        $rules_messages = array(
            'from_name.required' => __('Name is required'),
            'from_email.required' => __('E-mail address is required'),
            'from_email.email' => __('Valid e-mail address is required'),
            'subject.required' => __('Subject is required'),
            'message.required' => __('Message is required'),
            'to_id.required' => __('Recieving Company details missing'),
            'g-recaptcha-response.required' => __('Please verify that you are not a robot'),
            'g-recaptcha-response.captcha' => __('Captcha error! try again'),
        );
        $validation = Validator::make($request->all(), $rules, $rules_messages);
        if ($validation->fails()) {
            $msgresponse = $validation->messages()->toJson();
            echo $msgresponse;
            exit;
        } else {
            $receiver_company = Company::findOrFail($request->input('to_id'));
            $data['company_id'] = $request->input('company_id');
            $data['company_name'] = $request->input('company_name');
            $data['from_id'] = $request->input('from_id');
            $data['to_id'] = $request->input('to_id');
            $data['from_name'] = $request->input('from_name');
            $data['from_email'] = $request->input('from_email');
            $data['from_phone'] = $request->input('from_phone');
            $data['subject'] = $request->input('subject');
            $data['message_txt'] = $request->input('message');
            $data['to_email'] = $receiver_company->email;
            $data['to_name'] = $receiver_company->name;
            $msg_save = CompanyMessage::create($data);
            $when = Carbon::now()->addMinutes(5);
            Mail::send(new CompanyContactMail($data));
            $msgresponse = ['success' => 'success', 'message' => __('Message sent successfully')];
            echo json_encode($msgresponse);
            exit;
        }
    }

    public function sendApplicantContactForm(Request $request)
    {
        $msgresponse = Array();
        $rules = array(
            'from_name' => 'required|max:100|between:4,70',
            'from_email' => 'required|email|max:100',
            'subject' => 'required|max:200',
            'message' => 'required',
            'to_id' => 'required',
        );
        $rules_messages = array(
            'from_name.required' => __('Name is required'),
            'from_email.required' => __('E-mail address is required'),
            'from_email.email' => __('Valid e-mail address is required'),
            'subject.required' => __('Subject is required'),
            'message.required' => __('Message is required'),
            'to_id.required' => __('Recieving applicant details missing'),
            'g-recaptcha-response.required' => __('Please verify that you are not a robot'),
            'g-recaptcha-response.captcha' => __('Captcha error! try again'),
        );
        $validation = Validator::make($request->all(), $rules, $rules_messages);
        if ($validation->fails()) {
            $msgresponse = $validation->messages()->toJson();
            echo $msgresponse;
            exit;
        } else {
            $receiver_user = User::findOrFail($request->input('to_id'));
            $data['user_id'] = $request->input('user_id');
            $data['user_name'] = $request->input('user_name');
            $data['from_id'] = $request->input('from_id');
            $data['to_id'] = $request->input('to_id');
            $data['from_name'] = $request->input('from_name');
            $data['from_email'] = $request->input('from_email');
            $data['from_phone'] = $request->input('from_phone');
            $data['subject'] = $request->input('subject');
            $data['message_txt'] = $request->input('message');
            $data['to_email'] = $receiver_user->email;
            $data['to_name'] = $receiver_user->getName();
            $msg_save = ApplicantMessage::create($data);
            $when = Carbon::now()->addMinutes(5);
            Mail::send(new ApplicantContactMail($data));
            $msgresponse = ['success' => 'success', 'message' => __('Message sent successfully')];
            echo json_encode($msgresponse);
            exit;
        }
    }

    public function postedJobs(Request $request)
    {
        $jobs = Auth::guard('company-api')->user()->jobs()->paginate(10);


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

        $data['jobs']= $jobs;
        $data['jobs_id_values']= $jobs_id_values;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);
       /* return view('job.company_posted_jobs')
                        ->with('jobs', $jobs);*/
    }

    public function listAppliedUsers(Request $request, $job_id)
    {
        $job_applications = JobApply::where('job_id', '=', $job_id)->get();

        $data['job_applications']= $job_applications;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);

        /*return view('job.job_applications')
                        ->with('job_applications', $job_applications);*/
    }

    public function listHiredUsers(Request $request, $job_id)
    {
        $company_id = Auth::guard('company-api')->user()->id;
        $user_ids = FavouriteApplicant::where('job_id', '=', $job_id)->where('company_id', '=', $company_id)->where('status','hired')->pluck('user_id')->toArray();
        $job_applications = JobApply::where('job_id', '=', $job_id)->whereIn('user_id', $user_ids)->get();

        $data['job_applications']= $job_applications;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);

        /*return view('job.hired_applications')
                        ->with('job_applications', $job_applications);*/
    }

    public function listRejectedUsers(Request $request, $job_id)
    {
        $job_applications = JobApplyRejected::where('job_id', '=', $job_id)->get();
        $arr = array(
            'job_applications'=>$job_applications,
        );

        $success['token'] =  'success';

        return $this->sendResponse($success, $arr);
    }

    public function listFavouriteAppliedUsers(Request $request, $job_id)
    {
        $company_id = Auth::guard('company-api')->user()->id;
        $user_ids = FavouriteApplicant::where('job_id', '=', $job_id)->where('company_id', '=', $company_id)->where('status',null)->pluck('user_id')->toArray();
        $job_applications = JobApply::where('job_id', '=', $job_id)->whereIn('user_id', $user_ids)->get();

        $data['job_applications']= $job_applications;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);

        //return view('job.job_applications')
                       // ->with('job_applications', $job_applications);
    }

    public function applicantProfile($application_id)
    {

        $job_application = JobApply::findOrFail($application_id);
        $user = $job_application->getUser();
        $job = $job_application->getJob();
        $company = $job->getCompany();
        $profileCv = $job_application->getProfileCv();

        /*         * ********************************************** */
        $num_profile_views = $user->num_profile_views + 1;
        $user->num_profile_views = $num_profile_views;
        $user->update();
        $is_applicant = 'yes';


        $data['job_application']= $job_application;
        $data['user']= $user;
        $data['job']= $job;
        $data['company']= $company;
        $data['profileCv']= $profileCv;
        $data['page_title']= 'Applicant Profile';
        $data['form_title']= 'Contact Applicant';
        $data['is_applicant']= $is_applicant;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);

        /*         * ********************************************** */
      /*  return view('user.applicant_profile')
                        ->with('job_application', $job_application)
                        ->with('user', $user)
                        ->with('job', $job)
                        ->with('company', $company)
                        ->with('profileCv', $profileCv)
                        ->with('page_title', 'Applicant Profile')
                        ->with('form_title', 'Contact Applicant')
                        ->with('is_applicant', $is_applicant);*/
    }
    public function rejectApplicantProfile($application_id)
    {

        $job_application = JobApply::findOrFail($application_id);

        $rej = new JobApplyRejected();
        $rej->apply_id = $job_application->id;
        $rej->user_id = $job_application->user_id;
        $rej->job_id = $job_application->job_id;
        $rej->cv_id = $job_application->cv_id;
        $rej->current_salary = $job_application->current_salary;
        $rej->expected_salary = $job_application->expected_salary;
        $rej->salary_currency = $job_application->salary_currency;
        $rej->save();

        $job = $rej->getJob();

        $job_application->delete();
        Mail::send(new JobSeekerRejectedMailable($job,$rej));

        $success['success'] =  'done';

        return $this->sendResponse($success, 'Job seeker has been rejected successfully');
    }

    public function userProfile($id)
    {

        $user = User::findOrFail($id);
        $profileCv = $user->getDefaultCv();

        /*         * ********************************************** */
        $num_profile_views = $user->num_profile_views + 1;
        $user->num_profile_views = $num_profile_views;
        $user->update();

        $data['user']= $user;
        $data['profileCv']= $profileCv;
        $data['page_title']= 'Job Seeker Profile';
        $data['form_title']= 'Contact Job Seeker';

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);



        /*         * ********************************************** */
       /* return view('user.applicant_profile')
                        ->with('user', $user)
                        ->with('profileCv', $profileCv)
                        ->with('page_title', 'Job Seeker Profile')
                        ->with('form_title', 'Contact Job Seeker');*/
    }

    public function companyFollowers()
    {
        $company = Company::findOrFail(Auth::guard('company-api')->user()->id);
        $userIdsArray = $company->getFollowerIdsArray();
        $users = User::whereIn('id', $userIdsArray)->get();

        $data['company']= $company;
        $data['users']= $users;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);

       /* return view('company.follower_users')
                        ->with('users', $users)
                        ->with('company', $company);*/
    }

    public function companyMessages()
    {
        $data['company'] = Company::findOrFail(Auth::guard('company-api')->user()->id);
        $messages = CompanyMessage::where('company_id', '=', $company->id)
                ->orderBy('is_read', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

        $data['message']= $message;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);
    }

    public function companyMessageDetail($message_id)
    {
        $data['company'] = Company::findOrFail(Auth::guard('company-api')->user()->id);
        $message = CompanyMessage::findOrFail($message_id);
        $message->update(['is_read' => 1]);

        $data['message']= $message;

        $success['token'] =  'success';

        return $this->sendResponse($success, $data);


        /*return view('company.company_message_detail')
                        ->with('company', $company)
                        ->with('message', $message);*/
    }

    
    public function resume_search_packages()

    {
        //dd('yrdy');
        $packages = Package::where('package_for', 'cv_search')->get();
        //dd(Auth::guard('company-api')->user()->cvs_package_id);
        if (Auth::guard('company-api')->user()->cvs_package_id > 0) {
            $success_package = Package::findorfail(Auth::guard('company-api')->user()->cvs_package_id);
        } else {
            $success_package = '';
        }

        $arr = array(
            'success_package'=>$success_package,
            'packages'=>$packages,
        );

        $success['token'] =  '';

        return $this->sendResponse($success, $arr);

        //dd($data['success_package']);
        //return view('company_resume_search_packages')->with($data);
    }
    public function unlocked_users()

    {
        $data = array();
        $unlocked_users = Unlocked_users::where('company_id', Auth::guard('company-api')->user()->id)->first();
        if (null !== ($unlocked_users)) {
            $data['users'] = User::whereIn('id', explode(',', $unlocked_users->unlocked_users_ids))->get();
        }

        $success['token'] =  '';

        return $this->sendResponse($success, $data);

        //return view('company.unlocked_users')->with($data);
    }

    public function unlock($user_id)
    {
        $cvsSearch = Auth::guard('company-api')->user();
        if (null !== ($cvsSearch)) {
            if ($cvsSearch->availed_cvs_ids != '') {

                $newString = $this->addtoString($cvsSearch->availed_cvs_ids, $user_id);
            } else {
                $newString = $user_id;
            }

            $cvsSearch->availed_cvs_ids  = $newString;
            $cvsSearch->availed_cvs_quota += 1;
            $cvsSearch->update();

            $unlock = Unlocked_users::where('company_id', Auth::guard('company-api')->user()->id)->first();
            if (null !== ($unlock)) {
                $unlock->unlocked_users_ids  = $newString;
                $unlock->update();
            } else {
                $unlock = new Unlocked_users();

                $unlock->company_id  = Auth::guard('company-api')->user()->id;
                $unlock->unlocked_users_ids  = $newString;
                $unlock->save();
            }

            $success['success'] =  'done';

   

            return $this->sendResponse($success, 'Good Job!');


            //return redirect()->back();
        } 
    }
    function addtoString($str, $item)
    {
        $parts = explode(',', $str);
        $parts[] = $item;

        return implode(',', $parts);
    }

}
