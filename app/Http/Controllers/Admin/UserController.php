<?php

namespace App\Http\Controllers\Admin;

use File;
use ImgUploader;
use Auth;
use DB;
use Input;
use Carbon\Carbon;
use Redirect;
use App\User;
use App\JobApply;
use App\Gender;
use App\Company;
use App\Job;
use App\MaritalStatus;
use App\Country;
use App\State;
use App\City;
use App\JobExperience;
use App\CareerLevel;
use App\Industry;
use App\FunctionalArea;
use App\ProfileSummary;
use App\ProfileProject;
use App\ProfileExperience;
use App\ProfileEducation;
use App\ProfileSkill;
use App\ProfileLanguage;
use App\Package;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\Http\Requests\UserFormRequest;
use App\Http\Requests\ProfileProjectFormRequest;
use App\Http\Requests\ProfileProjectImageFormRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Traits\CommonUserFunctions;
use App\Traits\ProfileSummaryTrait;
use App\Traits\ProfileCvsTrait;
use App\Traits\ProfileProjectsTrait;
use App\Traits\ProfileExperienceTrait;
use App\Traits\ProfileEducationTrait;
use App\Traits\ProfileSkillTrait;
use App\Traits\ProfileLanguageTrait;
use App\Traits\Skills;
use App\Traits\JobSeekerPackageTrait;
use App\Traits\FetchJobSeekers;
use App\Helpers\DataArrayHelper;

class UserController extends Controller
{

    use CommonUserFunctions;
    use ProfileSummaryTrait;
    use ProfileCvsTrait;
    use ProfileProjectsTrait;
    use ProfileExperienceTrait;
    use ProfileEducationTrait;
    use ProfileSkillTrait;
    use ProfileLanguageTrait;
    use Skills;
    use JobSeekerPackageTrait;
    use FetchJobSeekers;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexUsers()
    {
        return view('admin.user.index');
    }

    public function appliedUsers()
    {
        return view('admin.user.applied_users');
    }

    public function createUser()
    {
        $genders = DataArrayHelper::defaultGendersArray();
        $maritalStatuses = DataArrayHelper::defaultMaritalStatusesArray();
        $nationalities = DataArrayHelper::defaultNationalitiesArray();
        $countries = DataArrayHelper::defaultCountriesArray();
        $jobExperiences = DataArrayHelper::defaultJobExperiencesArray();
        $careerLevels = DataArrayHelper::defaultCareerLevelsArray();
        $industries = DataArrayHelper::defaultIndustriesArray();
        $jobCategories = DataArrayHelper::defaultJobCategoriesArray();
        
        // Get all jobseeker packages (both featured and job application packages)
        $packagesData = Package::select('id', 'package_title', 'package_price', 'package_num_days', 'package_num_listings')
            ->whereIn('package_for', ['job_seeker', 'make_featured'])
            ->get();
        
        $packages = [];
        foreach ($packagesData as $pkg) {
            $detail = $pkg->package_title . ' - $' . $pkg->package_price . ' (' . $pkg->package_num_days . ' Days';
            if ($pkg->package_num_listings > 0) {
                $detail .= ', ' . $pkg->package_num_listings . ' Applications';
            }
            $detail .= ')';
            $packages[$pkg->id] = $detail;
        }
            
        $upload_max_filesize = UploadedFile::getMaxFilesize() / (1048576);

        return view('admin.user.add')
                        ->with('genders', $genders)
                        ->with('maritalStatuses', $maritalStatuses)
                        ->with('nationalities', $nationalities)
                        ->with('countries', $countries)
                        ->with('jobExperiences', $jobExperiences)
                        ->with('careerLevels', $careerLevels)
                        ->with('industries', $industries)
                        ->with('jobCategories', $jobCategories)
                        ->with('upload_max_filesize', $upload_max_filesize)
                        ->with('packages', $packages);
    }

    public function storeUser(UserFormRequest $request)
    {
        $user = new User();
        /*         * **************************************** */
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = ImgUploader::UploadImage('user_images', $image, $request->input('name'), 300, 300, false);
            $user->image = $fileName;
        }
        /*         * ************************************** */
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        if (!empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->father_name = $request->input('father_name');
        $user->date_of_birth = $request->input('date_of_birth');
        $user->gender_id = $request->input('gender_id');
        $user->marital_status_id = $request->input('marital_status_id');
        $user->nationality_id = $request->input('nationality_id');
        $user->national_id_card_number = $request->input('national_id_card_number');
        $user->country_id = $request->input('country_id');
        $user->state_id = $request->input('state_id');
        $user->city_id = $request->input('city_id');
        $user->phone = $request->input('phone');
        $user->mobile_num = $request->input('mobile_num');
        $user->job_experience_id = $request->input('job_experience_id');
        $user->career_level_id = $request->input('career_level_id');
        $user->industry_id = $request->input('industry_id');
        $user->functional_area_id = $request->input('functional_area_id');
        $user->current_salary = $request->input('current_salary');
        $user->expected_salary = $request->input('expected_salary');
        $user->salary_currency = $request->input('salary_currency');
        $user->street_address = $request->input('street_address');
        $user->is_immediate_available = $request->input('is_immediate_available');
        $user->is_active = $request->input('is_active');
        $user->verified = $request->input('verified');
        $user->is_featured = $request->input('is_featured');
        $user->save();

        /*         * *********************** */
        $user->name = $user->getName();
        $user->update();
        $this->updateUserFullTextSearch($user);
        /*         * *********************** */
        /*         * ************************************ */
        if ($request->has('job_seeker_package_id') && $request->input('job_seeker_package_id') > 0) {
            $package_id = $request->input('job_seeker_package_id');
            $package = Package::find($package_id);
            $this->addJobSeekerPackage($user, $package);
        }
        /*         * ************************************ */

        flash('User has been added!')->success();
        return \Redirect::route('edit.user', array($user->id));
    }

    public function editUser($id)
    {
        $genders = DataArrayHelper::defaultGendersArray();
        $maritalStatuses = DataArrayHelper::defaultMaritalStatusesArray();
        $nationalities = DataArrayHelper::defaultNationalitiesArray();
        $countries = DataArrayHelper::defaultCountriesArray();
        $jobExperiences = DataArrayHelper::defaultJobExperiencesArray();
        $careerLevels = DataArrayHelper::defaultCareerLevelsArray();
        $industries = DataArrayHelper::defaultIndustriesArray();
        $jobCategories = DataArrayHelper::defaultJobCategoriesArray();

        $upload_max_filesize = UploadedFile::getMaxFilesize() / (1048576);
        $user = User::findOrFail($id);
        
        // Get all jobseeker packages (both featured and job application packages)
        $packagesData = Package::select('id', 'package_title', 'package_price', 'package_num_days', 'package_num_listings')
            ->whereIn('package_for', ['job_seeker', 'make_featured'])
            ->get();
        
        $packages = [];
        foreach ($packagesData as $pkg) {
            $detail = $pkg->package_title . ' - $' . $pkg->package_price . ' (' . $pkg->package_num_days . ' Days';
            if ($pkg->package_num_listings > 0) {
                $detail .= ', ' . $pkg->package_num_listings . ' Applications';
            }
            $detail .= ')';
            $packages[$pkg->id] = $detail;
        }
        
        $selectedPackage = $user->package_id ?? null;

        return view('admin.user.edit')
                        ->with('genders', $genders)
                        ->with('maritalStatuses', $maritalStatuses)
                        ->with('nationalities', $nationalities)
                        ->with('countries', $countries)
                        ->with('jobExperiences', $jobExperiences)
                        ->with('careerLevels', $careerLevels)
                        ->with('industries', $industries)
                        ->with('jobCategories', $jobCategories)
                        ->with('user', $user)
                        ->with('upload_max_filesize', $upload_max_filesize)
                        ->with('packages', $packages)
                        ->with('selectedPackage', $selectedPackage);
    }

    public function updateUser($id, UserFormRequest $request)
    {
        $user = User::findOrFail($id);
        /*         * **************************************** */
        if ($request->hasFile('image')) {
            $is_deleted = $this->deleteUserImage($user->id);
            $image = $request->file('image');
            $fileName = ImgUploader::UploadImage('user_images', $image, $request->input('name'), 300, 300, false);
            $user->image = $fileName;
        }
		
		if ($request->hasFile('cover_image')) {
			$is_deleted = $this->deleteUserCoverImage($user->id);
            $cover_image = $request->file('cover_image');
            $fileName_cover_image = ImgUploader::UploadImage('user_images', $cover_image, $request->input('name'), 1140, 250, false);
            $user->cover_image = $fileName_cover_image;
        }
		
        /*         * ************************************** */
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        /*         * *********************** */
        $user->name = $user->getName();
        /*         * *********************** */
        $user->email = $request->input('email');
        if (!empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->father_name = $request->input('father_name');
        $user->date_of_birth = $request->input('date_of_birth');
        $user->gender_id = $request->input('gender_id');
        $user->marital_status_id = $request->input('marital_status_id');
        $user->nationality_id = $request->input('nationality_id');
        $user->national_id_card_number = $request->input('national_id_card_number');
        $user->country_id = $request->input('country_id');
        $user->state_id = $request->input('state_id');
        $user->city_id = $request->input('city_id');
        $user->phone = $request->input('phone');
        $user->mobile_num = $request->input('mobile_num');
        $user->job_experience_id = $request->input('job_experience_id');
        $user->career_level_id = $request->input('career_level_id');
        $user->industry_id = $request->input('industry_id');
        $user->functional_area_id = $request->input('functional_area_id');
        $user->current_salary = $request->input('current_salary');
        $user->expected_salary = $request->input('expected_salary');
        $user->salary_currency = $request->input('salary_currency');
        $user->street_address = $request->input('street_address');
        $user->is_immediate_available = $request->input('is_immediate_available');
        $user->is_active = $request->input('is_active');
        $user->verified = $request->input('verified');
        $user->is_featured = $request->input('is_featured');
        $user->update();

        $this->updateUserFullTextSearch($user);
        /*         * ************************************ */
        if ($request->has('job_seeker_package_id') && $request->input('job_seeker_package_id') > 0) {
            $package_id = $request->input('job_seeker_package_id');
            $package = Package::find($package_id);
            if ($user->package_id > 0) {
                $this->updateJobSeekerPackage($user, $package);
            } else {
                $this->addJobSeekerPackage($user, $package);
            }
        }
        /*         * ************************************ */

        flash('User has been updated!')->success();
        return \Redirect::route('edit.user', array($user->id));
    }

    public function fetchUsersData(Request $request)

    {

        $users = User::select(

                        [

                            'users.id',

                            'users.first_name',

                            'users.middle_name',

                            'users.last_name',

                            'users.email',

                            'users.password',

                            'users.phone',

                            'users.country_id',

                            'users.state_id',

                            'users.city_id',

                            'users.is_immediate_available',

                            'users.num_profile_views',

                            'users.is_active',

                            'users.verified',

                            'users.created_at',

                            'users.updated_at'

        ]);

        return Datatables::of($users)

                        ->filter(function ($query) use ($request) {

                            if ($request->has('id') && !empty($request->id)) {

                                $query->where('users.id', 'like', "{$request->get('id')}");

                            }

                            if ($request->has('name') && !empty($request->name)) {

                                $query->where(function($q) use ($request) {

                                    $q->where('users.first_name', 'like', "%{$request->get('name')}%")

                                    ->orWhere('users.middle_name', 'like', "%{$request->get('name')}%")

                                    ->orWhere('users.last_name', 'like', "%{$request->get('name')}%");

                                });

                            }

                            if ($request->has('email') && !empty($request->email)) {

                                $query->where('users.email', 'like', "%{$request->get('email')}%");

                            }

                            if ($request->has('cv_title') && ($request->cv_title =='yes')) {
                                //dd($request->cv_title);
                                $cvs  = ProfileCv::pluck('user_id')->toArray();

                                $query->whereIn('users.id', $cvs);

                            }else if ($request->has('cv_title') && ($request->cv_title =='no')) {
                                //dd($request->cv_title);
                                $cvs  = ProfileCv::pluck('user_id')->toArray();

                                $query->whereNotIn('users.id', $cvs);

                            }


                            if ($request->has('date_from') && !empty($request->date_from) && $request->has('date_to') && !empty($request->date_to)) {
                                $date_from = date('Y-m-d H:i:s',strtotime($request->date_from));
                                $date_to = date('Y-m-d H:i:s',strtotime($request->date_to));
                                $query->where('created_at', '>', $date_from)->where('created_at', '<', $date_to);

                            }

                            if ($request->has('is_active') && $request->is_active != -1) {

                                $query->where('users.is_active', '=', "{$request->get('is_active')}");

                            }

                            if ($request->has('is_verified') && $request->is_verified != -1) {

                                $query->where('users.verified', '=', "{$request->get('is_verified')}");

                            }

                            $query->orderBy('id', "DESC");

                        })

                        ->addColumn('name', function ($users) {

                            return $users->first_name . ' ' . $users->middle_name . ' ' . $users->last_name;

                        })

                        ->addColumn('created_at', function ($users) {

                            return date('M d,Y', strtotime($users->created_at));

                        })

                        ->addColumn('cv_added', function ($users) {
                            if(count($users->profileCvs)<=0){
                                 return '<strong style="color: #E70509">No</strong>';
                            }else{
                                return '<strong style="color: #2DC507">Yes</strong>';
                            }

                        })

                        ->addColumn('checkbox', function ($users) {

                            return '<input class="checkboxes" type="checkbox" id="check_'.$users->id.'" name="user_ids[]" autocomplete="off" value="'.$users->id.'">';

                        })

                        ->addColumn('action', function ($users) {

                            /*                             * ************************* */

                            $active_txt = 'Marked as active';

                            $active_href = 'make_active(' . $users->id . ');';

                            $active_icon = 'checkbox-blank-line';

                            if ((int) $users->is_active == 1) {

                                $active_txt = 'Mark as Inactive';

                                $active_href = 'make_not_active(' . $users->id . ');';

                                $active_icon = 'checkbox-blank-line';

                            }

                            /*                             * ************************* */

                            /*                             * ************************* */

                            $verified_txt = 'Not Verified';

                            $verified_href = 'make_verified(' . $users->id . ');';

                            $verified_icon = 'checkbox-blank-line';

                            if ((int) $users->verified == 1) {

                                $verified_txt = 'Verified';

                                $verified_href = 'make_not_verified(' . $users->id . ');';

                                $verified_icon = 'checkbox-blank-line';

                            }

                            $title = "'".$users->first_name. " " . $users->last_name."'";

                            /*                             * ************************* */

                            return '

				<div class="btn-group">

					<button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action

						<i class="ri ri-arrow-down-s-line"></i>

					</button>

					<ul class="dropdown-menu">

						<li>

							<a class="dropdown-item" href="' . route('edit.user', ['id' => $users->id]) . '"><i class="ri ri-pencil-line me-1"></i>Edit</a>

						</li>

                        <li>

                            <a class="dropdown-item" href="' . route('admin.view.public.profile',$users->id) . '"><i class="ri ri-user-line me-1"></i> View Profile Details</a>

                        </li>						

						<li>

							<a class="dropdown-item" href="javascript:void(0);" onclick="delete_user(' . $users->id . ','.$title.');"><i class="ri ri-delete-bin-line me-1"></i>Delete</a>

						</li>

						<li>

						<a class="dropdown-item" href="javascript:void(0);" onClick="' . $active_href . '" id="onclick_active_' . $users->id . '"><i class="ri ri-' . $active_icon . ' me-1"></i>' . $active_txt . '</a>

						</li>

						<li>

						<a class="dropdown-item" href="javascript:void(0);" onClick="' . $verified_href . '" id="onclick_verified_' . $users->id . '"><i class="ri ri-' . $verified_icon . ' me-1"></i>' . $verified_txt . '</a>

						</li>																																							

					</ul>

				</div>';

                        })

                        ->rawColumns(['action', 'name', 'checkbox', 'cv_added'])

                        ->setRowId(function($users) {

                            return 'user_dt_row_' . $users->id;

                        })

                        ->make(true);

    }


    public function fetchApplicantsData(Request $request)

    {
    	

        $users = JobApply::select('*');

        return Datatables::of($users)

                        ->filter(function ($query) use ($request) {

                            if ($request->has('name') && !empty($request->name)) {

                            	$user_ids = User::where('name','like',"%$request->name%")->pluck('id')->toArray();

                                $query->whereIn('user_id', $user_ids);

                            }

                            if ($request->has('job_title') && !empty($request->job_title)) {

                            	$job_ids = Job::where('title','like',"%$request->job_title%")->pluck('id')->toArray();

                                $query->whereIn('job_id', $job_ids);

                            }

                            if ($request->has('company') && !empty($request->company)) {

                            	$company_ids = Company::where('name','like',"%$request->company%")->pluck('id')->toArray();

                            	$job_ids = Job::whereIn('company_id',$company_ids)->pluck('id')->toArray();


                                $query->whereIn('job_id', $job_ids);

                            }


                            if ($request->has('date') && !empty($request->date)) {
                                
                                $query->where('created_at', '=', $request->date);

                            }

                            $today = Carbon::now();


                            $query->where('created_at', 'like', $today->toDateString() . '%');
                            $query->orderBy('id', "DESC");

                        })

                        ->addColumn('name', function ($users) {

                            return $users->getUser('name');

                        })

                        ->addColumn('job_title', function ($users) {

                            return $users->getJob('title');

                        })

                        ->addColumn('company', function ($users) {
                        	$job_id = $users->getJob('company_id');
                        	$company = Company::where('id',$job_id)->first();

                            return null!==($company)?$company->name:'';

                        })

                        ->addColumn('created_at', function ($users) {
                            return $users->created_at;

                        })

                        

                        ->addColumn('action', function ($users) {



                            /*                             * ************************* */

                            return '<div class="btn-group">

									<button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action

										<i class="ri ri-arrow-down-s-line"></i>

									</button>

									<ul class="dropdown-menu">

										

				                       					
									<li>

			                            <a class="dropdown-item" href="' . route('admin.view.public.profile',$users->user_id) . '?job_id='.$users->job_id.'"><i class="ri ri-user-line me-1"></i> View Profile Details</a>

			                        </li>
										

										
																																																

									</ul>

								</div>';

                        
                        })

                        ->rawColumns(['action', 'name', 'job_title', 'company'])

                        ->setRowId(function($users) {

                            return 'user_dt_row_' . $users->id;

                        })

                        ->make(true);

    }


    public function fetchMatchingUsersData(Request $request)

    {

        $users = User::select(

                        [

                            'users.id',

                            'users.first_name',

                            'users.middle_name',

                            'users.last_name',

                            'users.email',

                            'users.password',

                            'users.phone',

                            'users.country_id',

                            'users.state_id',

                            'users.city_id',

                            'users.is_immediate_available',

                            'users.num_profile_views',

                            'users.functional_area_id',

                            'users.is_active',

                            'users.verified',

                            'users.created_at',

                            'users.updated_at'

        ]);

        return Datatables::of($users)

                        ->filter(function ($query) use ($request) {

                            if ($request->has('id') && !empty($request->id)) {

                                $query->where('users.id', 'like', "{$request->get('id')}");

                            }

                            if ($request->has('name') && !empty($request->name)) {

                                $query->where(function($q) use ($request) {

                                    $q->where('users.first_name', 'like', "%{$request->get('name')}%")

                                    ->orWhere('users.middle_name', 'like', "%{$request->get('name')}%")

                                    ->orWhere('users.last_name', 'like', "%{$request->get('name')}%");

                                });

                            }

                            if ($request->has('email') && !empty($request->email)) {

                                $query->where('users.email', 'like', "%{$request->get('email')}%");

                            }

                            $query->where('users.functional_area_id',$request->functional_area_id);

                            $query->orderBy('id', "DESC");

                        })

                        ->addColumn('name', function ($users) {

                            return $users->first_name . ' ' . $users->middle_name . ' ' . $users->last_name;

                        }) 


                        ->addColumn('name', function ($users) {

                            return $users->first_name . ' ' . $users->middle_name . ' ' . $users->last_name;

                        })

                         ->addColumn('cv_added', function ($users) {
                            if(count($users->profileCvs)<=0){
                                 return '<strong style="color: #E70509">No</strong>';
                            }else{
                                return '<strong style="color: #2DC507">Yes</strong>';
                            }

                        })

                        ->addColumn('action', function ($users) {

                            /*                             * ************************* */

                            $active_txt = 'Marked as Verified';

                            $active_href = 'make_active(' . $users->id . ');';

                            $active_icon = 'checkbox-blank-line';

                            if ((int) $users->is_active == 1) {

                                $active_txt = 'Marked as Not Verified';

                                $active_href = 'make_not_active(' . $users->id . ');';

                                $active_icon = 'checkbox-blank-line';

                            }

                            /*                             * ************************* */

                            /*                             * ************************* */

                            $verified_txt = 'Not Verified';

                            $verified_href = 'make_verified(' . $users->id . ');';

                            $verified_icon = 'checkbox-blank-line';

                            if ((int) $users->verified == 1) {

                                $verified_txt = 'Verified';

                                $verified_href = 'make_not_verified(' . $users->id . ');';

                                $verified_icon = 'checkbox-blank-line';

                            }

                            /*                             * ************************* */

                            return '

                <div class="btn-group">

                    <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action

                        <i class="ri ri-arrow-down-s-line"></i>

                    </button>

                    <ul class="dropdown-menu">

                        <li>

                            <a class="dropdown-item" href="' . route('admin.view.public.profile', ['id' => $users->id]) . '"><i class="ri ri-user-line me-1"></i>Public Profile</a>

                        </li>                       

                                                                                                                                                                              

                    </ul>

                </div>';

                        })

                        ->rawColumns(['action', 'name','cv_added'])

                        ->setRowId(function($users) {

                            return 'user_dt_row_' . $users->id;

                        })

                        ->make(true);

    }




    public function makeActiveUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->is_active = 1;
            $user->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function viewPublicProfile($id)
    {
        $user = User::findOrFail($id);

        $profileCv = $user->getDefaultCv();

        $jobSkills = DataArrayHelper::defaultJobSkillsArray();

        $companies = Company::get();



        return view('admin.user.applicant_profile')

                        ->with('user', $user)

                        ->with('profileCv', $profileCv)

                        ->with('page_title', $user->getName())

                        ->with('jobSkills', $jobSkills)

                        ->with('companies', $companies)

                        ->with('form_title', 'Contact ' . $user->getName());



    }

    public function makeNotActiveUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->is_active = 0;
            $user->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function makeVerifiedUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->verified = 1;
            $user->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function makeNotVerifiedUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->verified = 0;
            $user->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    /*     * ******************************************** */
    
    // Jobseeker Payment History Methods
    public function indexUsersHistory()
    {
        $packages = Package::whereIn('package_for', ['job_seeker', 'make_featured'])
            ->pluck('package_title', 'id')
            ->toArray();
        
        // Calculate statistics from payment_history table
        $stats = [
            'total_payments' => \App\PaymentHistory::jobseekerTransactions()->count(),
            'total_revenue' => $this->calculateUsersTotalRevenue(),
            'active_subscriptions' => \App\PaymentHistory::jobseekerTransactions()
                ->where('package_end_date', '>=', now())
                ->where('payment_status', 'completed')
                ->count(),
            'expired_subscriptions' => \App\PaymentHistory::jobseekerTransactions()
                ->where('package_end_date', '<', now())
                ->where('payment_status', 'completed')
                ->count(),
            'featured_profiles' => \App\PaymentHistory::jobseekerTransactions()
                ->where('package_type', 'featured_profile')
                ->where('payment_status', 'completed')
                ->count(),
            'immediate_available' => User::where('is_immediate_available', 1)->count(),
        ];
        
        return view('admin.user.payment_history')
            ->with('packages', $packages)
            ->with('stats', $stats);
    }
    
    private function calculateUsersTotalRevenue()
    {
        return \App\PaymentHistory::jobseekerTransactions()->completed()->sum('package_price');
    }
    
    public function fetchUsersHistory(Request $request)
    {
        // Query from payment_history table for all jobseeker transactions
        $payments = \App\PaymentHistory::select('payment_history.*')
            ->with('user')
            ->jobseekerTransactions();
            
        return Datatables::of($payments)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('name') && !empty($request->name)) {
                                $query->whereHas('user', function($q) use ($request) {
                                    $q->where('first_name', 'like', "%{$request->get('name')}%")
                                      ->orWhere('middle_name', 'like', "%{$request->get('name')}%")
                                      ->orWhere('last_name', 'like', "%{$request->get('name')}%");
                                });
                            }
                            
                            if ($request->has('email') && !empty($request->email)) {
                                $query->whereHas('user', function($q) use ($request) {
                                    $q->where('email', 'like', "%{$request->get('email')}%");
                                });
                            }
                            
                            if ($request->has('package') && !empty($request->package)) {
                                $query->where('payment_history.package_id', $request->get('package'));
                            }
                            
                            $query->orderBy('payment_history.created_at', 'DESC');                           
                        })
                        ->addColumn('name', function ($payment) {
                            return $payment->user ? $payment->user->getName() : 'N/A';
                        })
                        ->addColumn('email', function ($payment) {
                            return $payment->user ? $payment->user->email : 'N/A';
                        })
                        ->addColumn('package', function ($payment) {
                            $badgeClass = ($payment->package_type == 'featured_profile') ? 'badge-success' : 'badge-primary';
                            return '<span class="badge ' . $badgeClass . '">' . $payment->package_title . ' ($' . $payment->package_price . ')</span>';
                        })
                        ->addColumn('amount', function ($payment) {
                            return '<strong class="text-success">$' . number_format($payment->package_price, 2) . '</strong>';
                        })
                        ->addColumn('payment_method', function ($payment) {
                            if (!empty($payment->payment_method)) {
                                $method = $payment->payment_method;
                                $badgeClass = 'badge-primary';
                                
                                // Set specific badge classes for different payment methods
                                if ($method === 'Admin Assign') {
                                    $badgeClass = 'badge-warning';
                                } elseif (stripos($method, 'PayPal') !== false) {
                                    $badgeClass = 'badge-info';
                                } elseif (stripos($method, 'Stripe') !== false) {
                                    $badgeClass = 'badge-success';
                                } elseif (stripos($method, 'Razorpay') !== false) {
                                    $badgeClass = 'badge-danger';
                                } elseif (stripos($method, 'Paystack') !== false) {
                                    $badgeClass = 'badge-primary';
                                } elseif (stripos($method, 'Paytm') !== false) {
                                    $badgeClass = 'badge-info';
                                } elseif (stripos($method, 'PayU') !== false) {
                                    $badgeClass = 'badge-warning';
                                } elseif (stripos($method, 'Iyzico') !== false) {
                                    $badgeClass = 'badge-primary';
                                    $method = '<i class="fas fa-credit-card"></i> ' . $method;
                                }
                                
                                return '<span class="badge ' . $badgeClass . '">' . $method . '</span>';
                            }
                            return '<span class="badge badge-warning">Admin Assign</span>';
                        })
                        ->addColumn('quota', function ($payment) {
                            if ($payment->package_type == 'featured_profile') {
                                return '<span class="badge badge-info">Featured Profile</span>';
                            }
                            $user = $payment->user;
                            $availedQuota = $user ? ($user->availed_jobs_quota ?? 0) : 0;
                            return 'Applications: ' . $availedQuota . '/' . $payment->jobs_quota;
                        })
                        ->addColumn('package_start_date', function ($payment) {
                            if ($payment->package_start_date) {
                                $formattedDate = date('M d, Y', strtotime($payment->package_start_date));
                                $formattedTime = date('h:i A', strtotime($payment->package_start_date));
                                return '<div style="line-height: 1.4;"><strong>' . $formattedDate . '</strong><br><small class="text-muted">' . $formattedTime . '</small></div>';
                            }
                            return 'N/A';
                        })
                        ->addColumn('package_end_date', function ($payment) {
                            if ($payment->package_end_date) {
                                $formattedDate = date('M d, Y', strtotime($payment->package_end_date));
                                $endDateTime = strtotime($payment->package_end_date);
                                $now = time();
                                $daysLeft = ceil(($endDateTime - $now) / 86400);
                                
                                if ($daysLeft > 0) {
                                    $countdown = '<small class="text-info">' . $daysLeft . ' days left</small>';
                                } else {
                                    $countdown = '<small class="text-danger">Expired</small>';
                                }
                                
                                return '<div style="line-height: 1.4;"><strong>' . $formattedDate . '</strong><br>' . $countdown . '</div>';
                            }
                            return 'N/A';
                        })
                        ->addColumn('is_featured', function ($payment) {
                            $isFeatured = ($payment->package_type == 'featured_profile');
                            return $isFeatured ? '<span class="label label-success">Yes</span>' : '<span class="label label-default">No</span>';
                        })
                        ->addColumn('action', function ($payment) {
                            return '<button class="btn btn-sm btn-info view-details" data-id="' . $payment->user_id . '"><i class="ri ri-eye-line me-1"></i> View</button>';
                        })
                        ->rawColumns(['package', 'amount', 'payment_method', 'quota', 'package_start_date', 'package_end_date', 'is_featured', 'action'])
                        ->setRowId(function($payment) {
                            return 'payment_' . $payment->id;
                        })
                        ->make(true);
    }
    
    public function getUserPaymentDetails(Request $request)
    {
        $user = User::with([
                'country' => function($query) {
                    $query->where('is_default', 1);
                },
                'state' => function($query) {
                    $query->where('is_default', 1);
                },
                'city' => function($query) {
                    $query->where('is_default', 1);
                },
                'gender' => function($query) {
                    $query->where('is_default', 1);
                },
                'maritalStatus' => function($query) {
                    $query->where('is_default', 1);
                }
            ])
            ->findOrFail($request->id);
        
        $package = $user->package_id ? Package::find($user->package_id) : null;
        
        return response()->json([
            'user' => $user,
            'package' => $package
        ]);
    }

    public function deleteUser(Request $request)
    {
        $id = $request->input('id');
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return 'ok';
        } catch (ModelNotFoundException $e) {
            return 'notok';
        }
    }

    public function bulkDeleteUsers(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 400);
        }
        
        try {
            foreach ($ids as $id) {
                $user = User::find($id);
                if ($user) {
                    $user->delete();
                }
            }
            return response()->json(['success' => true, 'message' => 'Selected users deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting users'], 500);
        }
    }
}
