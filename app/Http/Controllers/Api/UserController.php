<?php

namespace App\Http\Controllers\Api;

use Auth;
use DB;
use Input;
use File;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\UploadedFile;
use ImgUploader;
use Carbon\Carbon;
use Redirect;
use Newsletter;
use App\User;
use App\Subscription;
use App\ApplicantMessage;
use App\Company;
use App\CompanyMessage;
use App\FavouriteCompany;
use App\Gender;
use App\MaritalStatus;
use App\Country;
use App\State;
use App\City;
use App\JobExperience;
use App\JobApply;
use App\CareerLevel;
use App\Industry;
use App\Alert;
use App\FunctionalArea;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Traits\CommonUserFunctions;
use App\Traits\ProfileSummaryTrait;
use App\Traits\ProfileCvsTrait;
use App\Traits\ProfileProjectsTrait;
use App\Traits\ProfileExperienceTrait;
use App\Traits\ProfileEducationTrait;
use App\Traits\ProfileSkillTrait;
use App\Traits\ProfileLanguageTrait;
use App\Traits\Skills;
use App\Http\Requests\Front\UserFrontFormRequest;
use App\Helpers\DataArrayHelper;

class UserController extends BaseController
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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth', ['only' => ['myProfile', 'updateMyProfile', 'viewPublicProfile']]);
        //$this->middleware('auth', ['except' => ['showApplicantProfileEducation', 'showApplicantProfileProjects', 'showApplicantProfileExperience', 'showApplicantProfileSkills', 'showApplicantProfileLanguages']]);
    }

    public function viewPublicProfile($id)
    {
        //$user = User::findOrFail($id);
        $user = User::select(
            'users.id',
            'users.first_name',
            'users.middle_name',
            'users.last_name',
            'users.name',
            'users.email',
            'users.father_name',
            'users.date_of_birth',
            'genders.gender',
            'marital_statuses.marital_status',
            'users.job_title',
            'countries.nationality',
            'users.national_id_card_number',
            'countries.country',
            'states.state',
            'cities.city',
            'users.phone',
            'users.mobile_num',
            'job_experiences.job_experience',
            'career_levels.career_level',
            'industries.industry',
            'functional_areas.functional_area',
            'users.current_salary',
            'users.expected_salary',
            'users.salary_currency',
            'users.street_address',
            'users.is_active',
            'users.verified',
            'users.verification_token',
            'users.provider',
            'users.provider_id',
            'users.image',
            'users.cover_image',
            'users.lang',
            'users.created_at',
            'users.updated_at',
            'users.is_immediate_available',
            'users.num_profile_views',
            'packages.package_title',
            'users.package_start_date',
            'users.package_end_date',
            'users.jobs_quota',
            'users.availed_jobs_quota',
            'users.search',
            'users.is_subscribed',
            'users.video_link',
            'users.email_verified_at',
            'users.num_profile_views')
        ->leftJoin('genders','genders.id','=','users.gender_id')
        ->leftJoin('marital_statuses','marital_statuses.id','=','users.marital_status_id')
        ->leftJoin('countries','countries.id','=','users.country_id')
        ->leftJoin('states','states.id','=','users.state_id')
        ->leftJoin('cities','cities.id','=','users.city_id')
        ->leftJoin('job_experiences','job_experiences.id','=','users.job_experience_id')
        ->leftJoin('career_levels','career_levels.id','=','users.career_level_id')
        ->leftJoin('industries','industries.id','=','users.industry_id')
        ->leftJoin('functional_areas','functional_areas.id','=','users.functional_area_id')
        ->leftJoin('packages','packages.id','=','users.package_id')
        ->findOrFail($id);
        $profileCv = $user->getDefaultCv();

        // $arr_values = array(
        //     'gender_id' => $user->getGender('gender'),
        //     'marital_status_id' => $user->getMaritalStatus('marital_status'),
        //     'nationality_id' => $user->getNationality('country'),
        //     'country_id' => $user->getCountry('country'),
        //     'state_id' => $user->getState('state'),
        //     'city_id' => $user->getCity('city'),
        //     'job_experience_id' => $user->getJobExperience('job_experience'),
        //     'career_level_id' => $user->getCareerLevel('career_level'),
        //     'industry_id' => $user->getIndustry('industry'),
        //     'functional_area_id' => $user->getFunctionalArea('functional_area'),
        // );

        // $arr = array(
        //     'user'=>$user,
        //     'profileCv'=>$profileCv,
        //     'page_title'=>$user->getName(),
        //     'form_title'=>'Contact ' . $user->getName(),
        //     'id_values'=>$arr_values,
        // );
        $arr = array(
            'user'=>$user,
            'profileCv'=>$profileCv,
            'page_title'=>$user->getName(),
            'form_title'=>'Contact ' . $user->getName()
        );





        $success['token'] =  $user->createToken('MyApp')->accessToken;

        return $this->sendResponse($success, $arr);

    }

    public function myProfile()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Get user with joined data matching the database structure
            $userWithData = User::select(
            'users.id',
            'users.first_name',
            'users.middle_name',
            'users.last_name',
            'users.name',
            'users.email',
            'users.father_name',
            'users.date_of_birth',
                'users.gender_id',
            'genders.gender',
                'users.marital_status_id',
            'marital_statuses.marital_status',
                'users.nationality_id',
                'nationality_country.country as nationality',
            'users.national_id_card_number',
                'users.country_id',
                'country_table.country as country',
                'users.state_id',
            'states.state',
                'users.city_id',
            'cities.city',
            'users.phone',
            'users.mobile_num',
                'users.job_title',
                'users.job_experience_id',
            'job_experiences.job_experience',
                'users.career_level_id',
            'career_levels.career_level',
                'users.industry_id',
            'industries.industry',
                'users.functional_area_id',
            'functional_areas.functional_area',
            'users.current_salary',
            'users.expected_salary',
            'users.salary_currency',
                'users.video_link',
            'users.street_address',
                'users.is_immediate_available',
                'users.is_subscribed',
            'users.image',
            'users.cover_image',
            'users.created_at',
                'users.updated_at'
            )
            ->leftJoin('genders', 'genders.id', '=', 'users.gender_id')
            ->leftJoin('marital_statuses', 'marital_statuses.id', '=', 'users.marital_status_id')
            ->leftJoin('countries as nationality_country', 'nationality_country.id', '=', 'users.nationality_id')
            ->leftJoin('countries as country_table', 'country_table.id', '=', 'users.country_id')
            ->leftJoin('states', 'states.id', '=', 'users.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'users.city_id')
            ->leftJoin('job_experiences', 'job_experiences.id', '=', 'users.job_experience_id')
            ->leftJoin('career_levels', 'career_levels.id', '=', 'users.career_level_id')
            ->leftJoin('industries', 'industries.id', '=', 'users.industry_id')
            ->leftJoin('functional_areas', 'functional_areas.id', '=', 'users.functional_area_id')
            ->where('users.id', $user->id)
            ->first();

            if (!$userWithData) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $userWithData->id,
                    'first_name' => $userWithData->first_name,
                    'middle_name' => $userWithData->middle_name,
                    'last_name' => $userWithData->last_name,
                    'name' => $userWithData->name,
                    'email' => $userWithData->email,
                    'father_name' => $userWithData->father_name,
                    'date_of_birth' => $userWithData->date_of_birth,
                    'gender_id' => $userWithData->gender_id,
                    'gender' => $userWithData->gender,
                    'marital_status_id' => $userWithData->marital_status_id,
                    'marital_status' => $userWithData->marital_status,
                    'nationality_id' => $userWithData->nationality_id,
                    'nationality' => $userWithData->nationality,
                    'national_id_card_number' => $userWithData->national_id_card_number,
                    'country_id' => $userWithData->country_id,
                    'country' => $userWithData->country,
                    'state_id' => $userWithData->state_id,
                    'state' => $userWithData->state,
                    'city_id' => $userWithData->city_id,
                    'city' => $userWithData->city,
                    'phone' => $userWithData->phone,
                    'mobile_num' => $userWithData->mobile_num,
                    'job_title' => $userWithData->job_title,
                    'job_experience_id' => $userWithData->job_experience_id,
                    'job_experience' => $userWithData->job_experience,
                    'career_level_id' => $userWithData->career_level_id,
                    'career_level' => $userWithData->career_level,
                    'industry_id' => $userWithData->industry_id,
                    'industry' => $userWithData->industry,
                    'functional_area_id' => $userWithData->functional_area_id,
                    'functional_area' => $userWithData->functional_area,
                    'current_salary' => $userWithData->current_salary,
                    'expected_salary' => $userWithData->expected_salary,
                    'salary_currency' => $userWithData->salary_currency,
                    'video_link' => $userWithData->video_link,
                    'street_address' => $userWithData->street_address,
                    'is_immediate_available' => $userWithData->is_immediate_available,
                    'is_subscribed' => $userWithData->is_subscribed,
                    'image' => $userWithData->image,
                    'cover_image' => $userWithData->cover_image,
                    'created_at' => $userWithData->created_at,
                    'updated_at' => $userWithData->updated_at
                ],
                'message' => 'Profile retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateMyProfile(UserFrontFormRequest $request)
    {

        $user_id = Auth::guard('api')->user()->id ;
        $user = User::findOrFail($user_id);
        $user_profile_updt = array() ;
        $user_profile_updt = $request->all() ;
        
        // Debug: Log the raw request data
        \Log::info('UserController: Raw request data:', $request->all());
        \Log::info('UserController: Image field value:', ['image' => $request->input('image')]);
        \Log::info('UserController: Cover image field value:', ['cover_image' => $request->input('cover_image')]);
        
        /** **************************************** */
        
        // Handle image field - could be file upload OR string filename
        if ($request->hasFile('image')) {
            // File upload - existing logic
            \Log::info('UserController: Processing image as file upload');
            $is_deleted = $this->deleteUserImage($user->id);
            $image = $request->file('image');
            $fileName = ImgUploader::UploadImage('user_images', $image, $request->input('name'), 300, 300, false);
            $user_profile_updt['image'] = $fileName;
            \Log::info('UserController: Image uploaded successfully: ' . $fileName);
        } elseif ($request->filled('image') && is_string($request->input('image'))) {
            // String filename - just use it directly (for profile updates with existing uploaded images)
            \Log::info('UserController: Processing image as string filename: ' . $request->input('image'));
            $user_profile_updt['image'] = $request->input('image');
        }
		
		// Handle cover_image field - could be file upload OR string filename
		if ($request->hasFile('cover_image')) {
			// File upload - existing logic
			\Log::info('UserController: Processing cover_image as file upload');
			$is_deleted = $this->deleteUserCoverImage($user->id);
            $cover_image = $request->file('cover_image');
            $fileName_cover_image = ImgUploader::UploadImage('user_images', $cover_image, $request->input('name'), 1140, 250, false);
            $user_profile_updt['cover_image'] = $fileName_cover_image;
            \Log::info('UserController: Cover image uploaded successfully: ' . $fileName_cover_image);
        } elseif ($request->filled('cover_image') && is_string($request->input('cover_image'))) {
            // String filename - just use it directly (for profile updates with existing uploaded images)
            \Log::info('UserController: Processing cover_image as string filename: ' . $request->input('cover_image'));
            $user_profile_updt['cover_image'] = $request->input('cover_image');
        }
        
        // Debug: Log what we have after processing image fields
        \Log::info('UserController: After processing image fields:', $user_profile_updt);
        
         if (!empty($request->input('password'))) {
            unset($user_profile_updt["password_confirmation"]);
           // $user->password = Hash::make($request->input('password'));
           $user_profile_updt['password'] = Hash::make($request->password);
        }
	
       
	
        /*         * ************************************** */
        // $user->first_name = $request->input('first_name');
        // $user->middle_name = $request->input('middle_name');
        // $user->last_name = $request->input('last_name');
        /*         * *********************** */
       // $user->name = $user->getName();
       $user_profile_updt['name'] = $user->getName();
        /*         * *********************** */
       // $user->email = $request->input('email');
      // $user_profile_updt['email'] = $request->input('email');
        // $user->father_name = $request->input('father_name');
        // $user->date_of_birth = $request->input('date_of_birth');
        // $user->gender_id = $request->input('gender_id');
        // $user->marital_status_id = $request->input('marital_status_id');
        // $user->nationality_id = $request->input('nationality_id');
        // $user->national_id_card_number = $request->input('national_id_card_number');
        // $user->country_id = $request->input('country_id');
        // $user->state_id = $request->input('state_id');
        // $user->city_id = $request->input('city_id');
        // $user->phone = $request->input('phone');
        // $user->mobile_num = $request->input('mobile_num');
        // $user->job_experience_id = $request->input('job_experience_id');
        // $user->career_level_id = $request->input('career_level_id');
        // $user->industry_id = $request->input('industry_id');
        // $user->functional_area_id = $request->input('functional_area_id');
        // $user->current_salary = $request->input('current_salary');
        // $user->expected_salary = $request->input('expected_salary');
        // $user->salary_currency = $request->input('salary_currency');
        // $user->video_link = $request->video_link;
        // $user->street_address = $request->input('street_address');
		// $user->is_subscribed = $request->input('is_subscribed', 0);
		
      // $updated =  $user->update();

      // Log what we're about to update
      \Log::info('UserController: About to update user profile with data:', $user_profile_updt);
      
      $update_user_profile = User::Where('id', $user_id)->update($user_profile_updt) ;

        $this->updateUserFullTextSearch($user);
		/*************************/
		Subscription::where('email', 'like', $user->email)->delete();
		if((bool)$user->is_subscribed)
		{			
			$subscription = new Subscription();
			$subscription->email = $user->email;
			$subscription->name = $user->name;
			$subscription->save();
			
			/*************************/
			Newsletter::subscribeOrUpdate($subscription->email, ['FNAME'=>$subscription->name]);
			/*************************/
		}
		else
		{
			/*************************/
			Newsletter::unsubscribe($user->email);
			/*************************/
		}
		
        if($update_user_profile){
            return response()->json([
                'message' => 'You have updated your profile successfully'
            ]);
        }
        //return \Redirect::route('my.profile');
    }

    public function addToFavouriteCompany(Request $request, $company_slug)
    {
        $data['company_slug'] = $company_slug;
        // $data['user_id'] = Auth::user()->id;
        $data['user_id'] = Auth::guard('api')->user()->id;
       
        $check_list = FavouriteCompany::where('user_id','=',$data['user_id'])
            ->where('company_slug', '=',$data['company_slug'])
            ->exists();
        if(!$check_list){
            $data_save = FavouriteCompany::create($data);
            return $this->sendResponse($data, 'Company has been added in favorites list');
        }else{
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'You Have Already Added in Favourite List'
            ]);
        }
        // flash(__('Company has been added in favorites list'))->success();
        // return \Redirect::route('company.detail', $company_slug);
    }

    public function removeFromFavouriteCompany(Request $request, $company_slug)
    {
        // $user_id = Auth::user()->id;
        $user_id = Auth::guard('api')->user()->id ;
        $deleted = FavouriteCompany::where('company_slug', 'like', $company_slug)
                                    ->where('user_id', $user_id)->delete();

        // flash(__('Company has been removed from favorites list'))->success();
        // return \Redirect::route('company.detail', $company_slug);

        if($deleted){
            return response()->json([
                'message' => 'Company has been removed from favorites list'
            ]) ;
        }else{
            return response()->json([
                'message' => 'Not Available in the Favourite list to Delete'
            ]) ;
        }
    }

    public function myFollowings()
    {
        $user = User::findOrFail(Auth::guard('api')->user()->id);
        $companiesSlugArray = $user->getFollowingCompaniesSlugArray();
        $companies = Company::whereIn('slug', $companiesSlugArray)->get()->toArray();

        if(!empty($companies)){
            return response()->json([
                "data" => [
                    "user" => $user,
                    "companies" => $companies
                ]
            ]);
        }
        else{
            return response()->json([
                'message' => 'No Data....'
            ]);
        }
        // return view('user.following_companies')
        //                 ->with('user', $user)
        //                 ->with('companies', $companies);
    }

    public function myMessages()
    {
        $user = User::findOrFail(Auth::user()->id);
        $messages = ApplicantMessage::where('user_id', '=', $user->id)
                ->orderBy('is_read', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

        return view('user.applicant_messages')
                        ->with('user', $user)
                        ->with('messages', $messages);
    }

    public function applicantMessageDetail($message_id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Get all messages between this seeker and company
            $messages = CompanyMessage::where('company_id', $message_id)
                ->where('seeker_id', $user->id)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($messages->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Format messages for frontend
            $formattedMessages = [];
            foreach ($messages as $message) {
                $formattedMessages[] = [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'is_seeker' => $message->seeker_id == $user->id, // true if message is from seeker
                    'sender_name' => $message->seeker_id == $user->id ? $user->name : 'Company',
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedMessages
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in applicantMessageDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching messages: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myAlerts()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $alerts = Alert::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $alerts,
                'message' => 'Job alerts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving job alerts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createAlert(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $request->validate([
                'search_title' => 'required|string|max:255',
            ]);

            $alert = Alert::create([
                'name' => $request->search_title,
                'email' => $user->email,
                'user_id' => $user->id,
                'search_title' => $request->search_title,
            ]);

            return response()->json([
                'success' => true,
                'data' => $alert,
                'message' => 'Job alert created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating job alert: ' . $e->getMessage()
            ], 500);
        }
    }
    public function delete_alert($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $alert = Alert::where('id', $id)
                ->where('email', $user->email)
                ->first();

            if (!$alert) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alert not found or you do not have permission to delete it'
                ], 404);
            }

        $alert->delete();

            return response()->json([
                'success' => true,
                'message' => 'Alert deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting alert: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's CVs for job application (API version)
     */
    public function showProfileCvsApi($id)
    {
        \Log::info('showProfileCvsApi called with ID: ' . $id . ' (type: ' . gettype($id) . ')');
        try {
            $user = Auth::guard('api')->user();
            \Log::info('showProfileCvsApi - User authenticated: ' . ($user ? 'Yes, ID: ' . $user->id : 'No'));
            if (!$user) {
                \Log::info('showProfileCvsApi - Unauthorized access');
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Verify the user is accessing their own CVs or is authorized
            $requestedId = (int) $id; // Convert to integer
            \Log::info('showProfileCvsApi - Comparing user ID: ' . $user->id . ' with requested ID: ' . $requestedId);
            if ($user->id != $requestedId) {
                \Log::info('showProfileCvsApi - ID mismatch, returning 403');
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to CVs'
                ], 403);
            }

            // Get user's CVs from the database
            // Try different possible table names
            $cvs = null;
            $tableUsed = null;
            
            // First try 'profile_cvs' table
            if (DB::getSchemaBuilder()->hasTable('profile_cvs')) {
                $tableUsed = 'profile_cvs';
                $cvs = DB::table('profile_cvs')
                    ->where('user_id', $requestedId)
                    ->select('id', 'title', 'cv_file', 'is_default', 'created_at')
                    ->orderBy('is_default', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            // Try 'user_cvs' table
            elseif (DB::getSchemaBuilder()->hasTable('user_cvs')) {
                $tableUsed = 'user_cvs';
                $cvs = DB::table('user_cvs')
                    ->where('user_id', $requestedId)
                    ->select('id', 'title', 'cv_file', 'is_default', 'created_at')
                    ->orderBy('is_default', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            // Try 'cvs' table
            elseif (DB::getSchemaBuilder()->hasTable('cvs')) {
                $tableUsed = 'cvs';
                $cvs = DB::table('cvs')
                    ->where('user_id', $requestedId)
                    ->select('id', 'title', 'cv_file', 'is_default', 'created_at')
                    ->orderBy('is_default', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            // Try using User model relationship if it exists
            else {
                $user = User::find($requestedId);
                if ($user && method_exists($user, 'cvs')) {
                    $tableUsed = 'user->cvs() relationship';
                    $cvs = $user->cvs()->get();
                } elseif ($user && method_exists($user, 'profileCvs')) {
                    $tableUsed = 'user->profileCvs() relationship';
                    $cvs = $user->profileCvs()->get();
                }
            }

            // Log the table used for debugging
            \Log::info('showProfileCvs: Using table/relationship: ' . ($tableUsed ?: 'none found'));
            \Log::info('showProfileCvs: Found ' . ($cvs ? $cvs->count() : 0) . ' CVs for user ID: ' . $requestedId);

            // If no CVs found, return empty array
            if (!$cvs) {
                $cvs = collect([]);
            }

            // Format the response
            $formattedCvs = $cvs->map(function ($cv) {
                return [
                    'id' => $cv->id,
                    'title' => $cv->title ?: 'Untitled CV',
                    'file_name' => $cv->cv_file ?? $cv->file_name ?? 'unknown.pdf', // Support both column names
                    'is_default' => (bool) $cv->is_default,
                    'created_at' => $cv->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedCvs,
                'message' => 'CVs retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in showProfileCvs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving CVs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint to check database structure
     */
    public function debugCvTablesApi()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $debugInfo = [
                'user_id' => $user->id,
                'tables_checked' => [],
                'available_tables' => [],
                'user_relationships' => []
            ];

            // Check for CV-related tables
            $possibleTables = ['profile_cvs', 'user_cvs', 'cvs', 'profile_cv', 'user_cv'];
            
            foreach ($possibleTables as $table) {
                $exists = DB::getSchemaBuilder()->hasTable($table);
                $debugInfo['tables_checked'][$table] = $exists;
                
                if ($exists) {
                    // Get table structure
                    $columns = DB::getSchemaBuilder()->getColumnListing($table);
                    $debugInfo['available_tables'][$table] = $columns;
                    
                    // Check for specific columns we need
                    $debugInfo['column_checks'][$table] = [
                        'has_id' => in_array('id', $columns),
                        'has_user_id' => in_array('user_id', $columns),
                        'has_title' => in_array('title', $columns),
                        'has_cv_file' => in_array('cv_file', $columns),
                        'has_file_name' => in_array('file_name', $columns),
                        'has_is_default' => in_array('is_default', $columns),
                        'has_created_at' => in_array('created_at', $columns),
                    ];
                    
                    // Get sample data
                    $sampleData = DB::table($table)->where('user_id', $user->id)->first();
                    $debugInfo['sample_data'][$table] = $sampleData;
                }
            }

            // Check User model relationships
            $userModel = User::find($user->id);
            $debugInfo['user_relationships'] = [
                'has_cvs_method' => method_exists($userModel, 'cvs'),
                'has_profileCvs_method' => method_exists($userModel, 'profileCvs'),
                'has_getDefaultCv_method' => method_exists($userModel, 'getDefaultCv'),
            ];

            return response()->json([
                'success' => true,
                'data' => $debugInfo,
                'message' => 'Debug information retrieved'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in debugCvTables: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving debug info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's Projects (API version)
     */
    public function showProfileProjectsApi($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $requestedId = (int) $id;
            if ($user->id != $requestedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to projects'
                ], 403);
            }

            // Get user's projects using Eloquent model
            $user = User::find($requestedId);
            $projects = collect([]);
            
            if ($user && $user->profileProjects) {
                $projects = $user->profileProjects->map(function ($project) {
                    // Format dates like the web version
                    $dateStart = $project->date_start ? \Carbon\Carbon::parse($project->date_start)->format('d M, Y') : '';
                    $dateEnd = $project->is_on_going == 1 
                        ? 'Currently ongoing' 
                        : ($project->date_end ? \Carbon\Carbon::parse($project->date_end)->format('d M, Y') : '');
                    
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'description' => $project->description,
                        'url' => $project->url,
                        'image' => $project->image,
                        'date_start' => $project->date_start,
                        'date_end' => $project->date_end,
                        'date_start_formatted' => $dateStart,
                        'date_end_formatted' => $dateEnd,
                        'is_on_going' => $project->is_on_going,
                        'created_at' => $project->created_at
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $projects,
                'message' => 'Projects retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in showProfileProjectsApi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving projects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's Experience (API version)
     */
    public function showProfileExperienceApi($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $requestedId = (int) $id;
            if ($user->id != $requestedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to experience'
                ], 403);
            }

            // Get user's experience from the database
            $experience = null;
            
            // Get user's experience using Eloquent model to access relationships
            $user = User::find($requestedId);
            $experience = collect([]);
            
            if ($user && $user->profileExperience) {
                $experience = $user->profileExperience->map(function ($exp) {
                    // Format dates like the web version
                    $dateStart = $exp->date_start ? \Carbon\Carbon::parse($exp->date_start)->format('d M, Y') : '';
                    $dateEnd = $exp->is_currently_working == 1 
                        ? 'Currently working' 
                        : ($exp->date_end ? \Carbon\Carbon::parse($exp->date_end)->format('d M, Y') : '');
                    
                    return [
                        'id' => $exp->id,
                        'title' => $exp->title,
                        'company' => $exp->company,
                        'description' => $exp->description,
                        'date_start' => $exp->date_start,
                        'date_end' => $exp->date_end,
                        'date_start_formatted' => $dateStart,
                        'date_end_formatted' => $dateEnd,
                        'is_currently_working' => $exp->is_currently_working,
                        'country_id' => $exp->country_id,
                        'state_id' => $exp->state_id,
                        'city_id' => $exp->city_id,
                        'city_name' => $exp->getCity('city'),
                        'state_name' => $exp->getState('state'),
                        'country_name' => $exp->getCountry('country'),
                        'location' => $exp->getCity('city') . ' - ' . $exp->getCountry('country'),
                        'created_at' => $exp->created_at
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $experience,
                'message' => 'Experience retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in showProfileExperienceApi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving experience: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's Education (API version)
     */
    public function showProfileEducationApi($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $requestedId = (int) $id;
            if ($user->id != $requestedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to education'
                ], 403);
            }

            // Get user's education using Eloquent model
            $user = User::find($requestedId);
            $education = collect([]);
            
            if ($user && $user->profileEducation) {
                $education = $user->profileEducation->map(function ($edu) {
                    return [
                        'id' => $edu->id,
                        'degree_level_id' => $edu->degree_level_id,
                        'degree_type_id' => $edu->degree_type_id,
                        'degree_title' => $edu->degree_title,
                        'institution' => $edu->institution,
                        'date_completion' => $edu->date_completion,
                        'degree_result' => $edu->degree_result,
                        'result_type_id' => $edu->result_type_id,
                        'country_id' => $edu->country_id,
                        'state_id' => $edu->state_id,
                        'city_id' => $edu->city_id,
                        'degree_level_name' => $edu->getDegreeLevel('degree_level'),
                        'degree_type_name' => $edu->getDegreeType('degree_type'),
                        'result_type_name' => $edu->getResultType('result_type'),
                        'city_name' => $edu->getCity('city'),
                        'state_name' => $edu->getState('state'),
                        'country_name' => $edu->getCountry('country'),
                        'location' => $edu->getCity('city') . ' - ' . $edu->getCountry('country'),
                        'major_subjects' => $edu->getProfileEducationMajorSubjectsStr(),
                        'created_at' => $edu->created_at
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $education,
                'message' => 'Education retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in showProfileEducationApi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving education: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's Skills (API version)
     */
    public function showProfileSkillsApi($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $requestedId = (int) $id;
            if ($user->id != $requestedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to skills'
                ], 403);
            }

            // Get user's skills using Eloquent model
            $user = User::find($requestedId);
            $skills = collect([]);
            
            if ($user && $user->profileSkills) {
                $skills = $user->profileSkills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'job_skill_id' => $skill->job_skill_id,
                        'job_experience_id' => $skill->job_experience_id,
                        'skill_name' => $skill->getJobSkill('job_skill'),
                        'experience_level' => $skill->getJobExperience('job_experience'),
                        'created_at' => $skill->created_at
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $skills,
                'message' => 'Skills retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in showProfileSkillsApi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving skills: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's Languages (API version)
     */
    public function showProfileLanguagesApi($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $requestedId = (int) $id;
            if ($user->id != $requestedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to languages'
                ], 403);
            }

            // Get user's languages using Eloquent model
            $user = User::find($requestedId);
            $languages = collect([]);
            
            if ($user && $user->profileLanguages) {
                $languages = $user->profileLanguages->map(function ($language) {
                    return [
                        'id' => $language->id,
                        'language_id' => $language->language_id,
                        'language_level_id' => $language->language_level_id,
                        'language_name' => $language->getLanguage('lang'),
                        'language_level_name' => $language->getLanguageLevel('language_level'),
                        'created_at' => $language->created_at
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $languages,
                'message' => 'Languages retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in showProfileLanguagesApi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving languages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's Summary (API version)
     */
    public function showProfileSummaryApi($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $requestedId = (int) $id;
            if ($user->id != $requestedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to summary'
                ], 403);
            }

            // Get user's summary from the profile_summaries table
            $summary = DB::table('profile_summaries')
                ->where('user_id', $requestedId)
                ->select('id', 'summary', 'created_at', 'updated_at')
                ->first();

            return response()->json([
                'success' => true,
                'data' => $summary ? ['summary' => $summary->summary] : ['summary' => ''],
                'message' => 'Summary retrieved successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in showProfileSummaryApi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's Summary (API version)
     */
    public function updateFrontProfileSummary(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $requestedId = (int) $id;
            if ($user->id != $requestedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to summary'
                ], 403);
            }

            $summary = $request->input('summary', '');

            // Update user's summary in the profile_summaries table
            $existingSummary = DB::table('profile_summaries')
                ->where('user_id', $requestedId)
                ->first();

            if ($existingSummary) {
                // Update existing summary
                $updated = DB::table('profile_summaries')
                    ->where('user_id', $requestedId)
                    ->update(['summary' => $summary, 'updated_at' => now()]);
            } else {
                // Create new summary record
                $updated = DB::table('profile_summaries')
                    ->insert([
                        'user_id' => $requestedId,
                        'summary' => $summary,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
            }

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Summary updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update summary'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Error in updateFrontProfileSummary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating summary: ' . $e->getMessage()
            ], 500);
        }
    }

    // Placeholder methods for other CRUD operations
    // These would need to be implemented based on the specific requirements

    public function getFrontProfileProjectForm($id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function storeProfileProject(Request $request, $id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileProjectEditForm($user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function updateFrontProfileProject(Request $request, $id, $user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function deleteProfileProject(Request $request) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileExperienceForm($id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function storeProfileExperience(Request $request, $id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileExperienceEditForm($user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function updateFrontProfileExperience(Request $request, $id, $user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function deleteProfileExperience(Request $request) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileEducationForm($id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function storeProfileEducation(Request $request, $id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileEducationEditForm($user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function updateFrontProfileEducation(Request $request, $id, $user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function deleteProfileEducation(Request $request) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileSkillForm($id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function storeProfileSkill(Request $request, $id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileSkillEditForm($user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function updateFrontProfileSkill(Request $request, $id, $user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function deleteProfileSkill(Request $request) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileLanguageForm($id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function storeProfileLanguage(Request $request, $id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function getFrontProfileLanguageEditForm($user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function updateFrontProfileLanguage(Request $request, $id, $user_id) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    public function deleteProfileLanguage(Request $request) {
        return response()->json(['success' => false, 'message' => 'Not implemented yet']);
    }

    /**
     * Delete user's CV (API version)
     */
    public function deleteProfileCv(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Validate the request
            $request->validate([
                'cv_id' => 'required|integer|min:1',
            ]);

            $cvId = $request->input('cv_id');
            $userId = $user->id;

            // Try to delete from different possible tables
            $deleted = false;
            $tableUsed = null;

            // Try 'profile_cvs' table first
            if (DB::getSchemaBuilder()->hasTable('profile_cvs')) {
                $cv = DB::table('profile_cvs')
                    ->where('id', $cvId)
                    ->where('user_id', $userId)
                    ->first();

                if ($cv) {
                    $deleted = DB::table('profile_cvs')
                        ->where('id', $cvId)
                        ->where('user_id', $userId)
                        ->delete();
                    $tableUsed = 'profile_cvs';
                }
            }
            // Try 'user_cvs' table
            elseif (DB::getSchemaBuilder()->hasTable('user_cvs')) {
                $cv = DB::table('user_cvs')
                    ->where('id', $cvId)
                    ->where('user_id', $userId)
                    ->first();

                if ($cv) {
                    $deleted = DB::table('user_cvs')
                        ->where('id', $cvId)
                        ->where('user_id', $userId)
                        ->delete();
                    $tableUsed = 'user_cvs';
                }
            }
            // Try 'cvs' table
            elseif (DB::getSchemaBuilder()->hasTable('cvs')) {
                $cv = DB::table('cvs')
                    ->where('id', $cvId)
                    ->where('user_id', $userId)
                    ->first();

                if ($cv) {
                    $deleted = DB::table('cvs')
                        ->where('id', $cvId)
                        ->where('user_id', $userId)
                        ->delete();
                    $tableUsed = 'cvs';
                }
            }

            if ($deleted) {
                \Log::info("CV deleted successfully from {$tableUsed} table. CV ID: {$cvId}, User ID: {$userId}");
                return response()->json([
                    'success' => true,
                    'message' => 'CV deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'CV not found or you do not have permission to delete it'
                ], 404);
            }

        } catch (\Exception $e) {
            \Log::error('Error in deleteProfileCv: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting CV: ' . $e->getMessage()
            ], 500);
        }
    }

}
