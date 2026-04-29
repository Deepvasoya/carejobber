<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Input;
use File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use ImgUploader;
use Carbon\Carbon;
use Redirect;
use Newsletter;
use App\User;
use App\Subscription;
use App\ApplicantMessage;
use App\Company;
use App\Package;
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
use App\Traits\UserReferralTrait;
use App\Http\Requests\Front\UserFrontFormRequest;
use App\Helpers\DataArrayHelper;
use App\Mail\ResumePostedNotificationMailable;
use Illuminate\Support\Facades\Mail;

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
    use UserReferralTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth', ['only' => ['myProfile', 'updateMyProfile', 'viewPublicProfile']]);
        $this->middleware('auth', ['except' => ['showApplicantProfileEducation', 'showApplicantProfileProjects', 'showApplicantProfileExperience', 'showApplicantProfileSkills', 'showApplicantProfileLanguages']]);
    }

    public function viewPublicProfile($id)
    {

        $user = User::findOrFail($id);
        $profileCv = $user->getDefaultCv();
        
        return view('user.applicant_profile')
                        ->with('user', $user)
                        ->with('profileCv', $profileCv)
                        ->with('page_title', $user->getName())
                        ->with('form_title', 'Contact ' . $user->getName());
    }

    public function myProfile()
    {
        $genders = DataArrayHelper::langGendersArray();
        $maritalStatuses = DataArrayHelper::langMaritalStatusesArray();
        $nationalities = DataArrayHelper::langNationalitiesArray();
        $countries = DataArrayHelper::langCountriesArray();
        $jobExperiences = DataArrayHelper::langJobExperiencesArray();
        $careerLevels = DataArrayHelper::langCareerLevelsArray();
        $industries = DataArrayHelper::langIndustriesArray();
        $functionalAreas = DataArrayHelper::langFunctionalAreasArray();

        $upload_max_filesize = UploadedFile::getMaxFilesize() / (1048576);
        $user = User::findOrFail(Auth::user()->id);
        return view('user.edit_profile')
                        ->with('genders', $genders)
                        ->with('maritalStatuses', $maritalStatuses)
                        ->with('nationalities', $nationalities)
                        ->with('countries', $countries)
                        ->with('jobExperiences', $jobExperiences)
                        ->with('careerLevels', $careerLevels)
                        ->with('industries', $industries)
                        ->with('functionalAreas', $functionalAreas)
                        ->with('user', $user)
                        ->with('upload_max_filesize', $upload_max_filesize);
    }

    public function updateMyProfile(UserFrontFormRequest $request)
    {
        app(\App\Services\UserSubmittedLookupService::class)->mergeUserSubmittedProfileRequest($request);

        $user = User::findOrFail(Auth::user()->id);
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
        // Check if industry is being set or changed
        $oldIndustryId = $user->industry_id;
        $newIndustryId = $request->input('industry_id');
        $industryChanged = ($oldIndustryId != $newIndustryId) || ($newIndustryId && !$oldIndustryId);
        
        $user->industry_id = $request->input('industry_id');
        $user->functional_area_id = $request->input('functional_area_id');
        $user->current_salary = $request->input('current_salary');
        $user->expected_salary = $request->input('expected_salary');
        $user->salary_currency = $request->input('salary_currency');
        $user->video_link = $request->video_link;
        $user->other_certifications = $request->input('other_certifications');
        $user->street_address = $request->input('street_address');
		$user->is_subscribed = $request->input('is_subscribed', 0);
        $user->visible_in_employer_resume_search = $request->boolean('visible_in_employer_resume_search');

        $cf = app(\App\Services\CustomFieldValueService::class);
        $norm = $cf->normalizeForContext($request, \App\Models\CustomField::CONTEXT_PROFILE);
        $user->custom_field_data = $cf->mergeStored($user->custom_field_data ?? null, $norm);

        $user->update();

        $this->updateUserFullTextSearch($user);
        
        // Send notification to companies with matching industry
        if ($industryChanged && $newIndustryId) {
            $this->notifyCompaniesAboutNewResume($user, $newIndustryId);
        }
		/*************************/
		Subscription::where('email', 'like', $user->email)->delete();
		if((bool)$user->is_subscribed)
		{			
			$subscription = new Subscription();
			$subscription->email = $user->email;
			$subscription->name = $user->name;
			$subscription->save();
			
			/*************************/
			//Newsletter::subscribeOrUpdate($subscription->email, ['FNAME'=>$subscription->name]);
			/*************************/
		}
		else
		{
			/*************************/
			//Newsletter::unsubscribe($user->email);
			/*************************/
		}
		
        flash(__('You have updated your profile successfully'))->success();
        return \Redirect::route('my.profile', ['profile_updated' => 1]);
    }

    public function addToFavouriteCompany(Request $request, $company_slug)
    {
        $data['company_slug'] = $company_slug;
        $data['user_id'] = Auth::user()->id;
        $data_save = FavouriteCompany::create($data);
        flash(__('Company has been added in favorites list'))->success();
        return \Redirect::route('company.detail', $company_slug);
    }

    public function removeFromFavouriteCompany(Request $request, $company_slug)
    {
        $user_id = Auth::user()->id;
        FavouriteCompany::where('company_slug', 'like', $company_slug)->where('user_id', $user_id)->delete();

        flash(__('Company has been removed from favorites list'))->success();
        return \Redirect::route('company.detail', $company_slug);
    }

    public function myFollowings()
    {
        $user = User::findOrFail(Auth::user()->id);
        $companiesSlugArray = $user->getFollowingCompaniesSlugArray();
        $companies = Company::whereIn('slug', $companiesSlugArray)->get();

        return view('user.following_companies')
                        ->with('user', $user)
                        ->with('companies', $companies);
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
        $user = User::findOrFail(Auth::user()->id);
        $message = ApplicantMessage::findOrFail($message_id);
        $message->update(['is_read' => 1]);

        return view('user.applicant_message_detail')
                        ->with('user', $user)
                        ->with('message', $message);
    }

    public function myAlerts()
    {
        $alerts = Alert::where('email', Auth::user()->email)
            ->orderBy('created_at', 'desc')
            ->get();
        //dd($alerts);
        return view('user.applicant_alerts')
            ->with('alerts', $alerts);
    }
    public function delete_alert($id)
    {
        $alert = Alert::findOrFail($id);
        $alert->delete();
        $arr = array('msg' => 'A Alert has been successfully deleted. ', 'status' => true);
        return Response()->json($arr);
    }
    public function ResumeFetch($id) {
        $user = User::findOrFail($id);
         $profileCv = $user->getDefaultCv();
         return view('user.resume')
                         ->with('user', $user)
                         ->with('profileCv', $profileCv)
                         ->with('page_title', $user->getName())
                         ->with('form_title', 'Contact ' . $user->getName());
    }


    public function buildResume()
{
    $upload_max_filesize = UploadedFile::getMaxFilesize() / (1048576);
    $user = User::findOrFail(Auth::user()->id);

    return view('user.build_resume')
        ->with('user', $user)
        ->with('upload_max_filesize', $upload_max_filesize);
}


public function indexCandidateHistory()
{
    $user = auth()->user();
    
    // Try to get payment history from payment_history table first
    $candidatePayments = \App\PaymentHistory::where('user_id', $user->id)
        ->with('package')
        ->orderBy('created_at', 'DESC')
        ->get();
    
    // If no records in payment_history, show current package as fallback
    if ($candidatePayments->isEmpty() && $user->package_id) {
        // Create a temporary collection with current package data
        $currentPackage = new \stdClass();
        $currentPackage->package = $user->getPackage();
        $currentPackage->payment_method = $user->payment_method ?? 'Admin Assign';
        $currentPackage->package_start_date = $user->package_id == 9 
            ? $user->featured_package_start_at 
            : $user->package_start_date;
        $currentPackage->package_end_date = $user->package_id == 9 
            ? $user->featured_package_end_at 
            : $user->package_end_date;
        $currentPackage->jobs_quota = $user->jobs_quota ?? 0;
        $currentPackage->package_type = $user->package_id == 9 ? 'featured_profile' : 'job_seeker';
        
        $candidatePayments = collect([$currentPackage]);
    }
    
    $siteSetting = \App\SiteSetting::first();

    return view('user.payment_history', compact('candidatePayments', 'siteSetting'));
}





public function fetchCandidatesHistory(Request $request)
{


    $candidates = User::select('*')->whereNotNull('featured_package_start_at');

    return Datatables::of($candidates)
        ->filter(function ($query) use ($request) {
            if ($request->has('name') && !empty($request->name)) {
                $query->where('users.name', 'like', "%{$request->get('name')}%");
            }
            if ($request->has('payment_method') && !empty($request->payment_method)) {
                $query->where('users.payment_method', 'like', "%{$request->get('payment_method')}%");
            }
            if ($request->has('package') && !empty($request->package)) {
                $query->where('users.package_id', $request->get('package'));
            }
            $query->orderBy('featured_package_start_at', 'DESC');
        })
        ->addColumn('payment_method', function ($candidates) {
            return !empty($candidates->payment_method) && $candidates->payment_method !== 'offline'
                ? $candidates->payment_method
                : 'Offline (Added by Admin)';
        })
        ->addColumn('package', function ($candidates) {
            $package = Package::find($candidates->package_id);
            return $package ? $package->package_title : 'N/A';
        })
        ->addColumn('package_num_days', function ($candidates) {
            return $candidates->package_num_days ?? 'N/A';
        })
        ->addColumn('featured_package_start_at', function ($candidates) {
            return $candidates->featured_package_start_at
                ? date('d-m-Y', strtotime($candidates->featured_package_start_at))
                : 'N/A';
        })
        ->addColumn('featured_package_end_at', function ($candidates) {
            return $candidates->featured_package_end_at
                ? date('d-m-Y', strtotime($candidates->featured_package_end_at))
                : 'N/A';
        })
        ->rawColumns(['featured_package_start_at', 'featured_package_end_at'])
        ->setRowId(function ($candidates) {
            return 'candidateDtRow' . $candidates->id;
        })
        ->make(true);
}

public function package()
{
    $user = Auth::user();
    $package = $user->getPackage();
    
    // Only fetch packages for purchase/upgrade if the feature is enabled
    // But ALWAYS allow users to see their existing package
    if ((bool)config('jobseeker.is_jobseeker_package_active')) {
        if (null !== $package) {
            // User has package - show upgrade options
            $packages = Package::where('package_for', 'like', 'job_seeker')
                ->where('id', '<>', $package->id)
                ->where('package_price', '>=', $package->package_price)
                ->get();
        } else {
            // User has no package - show all available packages
            $packages = Package::where('package_for', 'like', 'job_seeker')->get();
        }
    } else {
        // Package system is disabled - don't show any packages for purchase
        // But user can still see their existing package in the view
        $packages = collect();
    }

    return view('user.package')
        ->with('user', $user)
        ->with('package', $package)
        ->with('packages', $packages);
}

public function privacyDataSettings()
{
    $user = Auth::user();
    
    return view('user.privacy_data_settings')
        ->with('user', $user);
}

public function updateResumeSearchVisibility(Request $request)
{
    $user = Auth::user();
    $user->visible_in_employer_resume_search = $request->boolean('visible_in_employer_resume_search');
    $user->save();

    flash(__('Your resume search visibility has been updated.'))->success();

    return redirect()->route('privacy.data.settings');
}

public function saveCookieConsent(Request $request)
{
    $user = Auth::user();
    
    if (!$user->gdpr_consent_date) {
        $user->gdpr_consent_date = Carbon::now();
        $user->save();
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Consent saved successfully'
    ]);
}

public function givePrivacyConsent(Request $request)
{
    $user = Auth::user();
    
    if (!$user->gdpr_consent_date) {
        $user->gdpr_consent_date = Carbon::now();
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Privacy consent has been recorded successfully'
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Consent already recorded'
    ]);
}

public function requestDataExport(Request $request)
{
    $user = Auth::user();
    
    // Update user record with export request
    $user->data_export_requested_at = Carbon::now();
    $user->save();
    
    // Here you can add logic to prepare and send the data export
    // For now, we'll just record the request
    
    flash(__('Your data export request has been submitted. You will receive an email with your data shortly.'))->success();
    return redirect()->route('privacy.data.settings');
}

public function requestAccountDeletion(Request $request)
{
    $user = Auth::user();
    
    // Validate password for security
    $request->validate([
        'password' => 'required|string',
    ]);
    
    if (!Hash::check($request->password, $user->password)) {
        flash(__('Invalid password. Please try again.'))->error();
        return redirect()->route('privacy.data.settings');
    }
    
    // Update user record with deletion request
    $user->account_deletion_requested_at = Carbon::now();
    $user->save();
    
    // Here you can add logic to process account deletion
    // For now, we'll just record the request
    
    flash(__('Your account deletion request has been submitted. Your account will be deleted within 30 days unless you cancel this request.'))->warning();
    return redirect()->route('privacy.data.settings');
}

    /**
     * Notify companies about new resume posted in their industry
     */
    private function notifyCompaniesAboutNewResume($user, $industryId)
    {
        if (!$industryId || !$user || !$user->is_active) {
            return;
        }

        // Find all active companies with matching industry
        $companies = Company::where('industry_id', $industryId)
            ->where('is_active', 1)
            ->where('email_verified_at', '!=', null)
            ->get();

        if ($companies->isEmpty()) {
            return;
        }

        // Send email to each company
        foreach ($companies as $company) {
            try {
                Mail::to($company->email)->send(new ResumePostedNotificationMailable($user, $company));
            } catch (\Exception $e) {
                \Log::error('Failed to send resume posted notification to company ' . $company->id . ': ' . $e->getMessage());
            }
        }
    }

}
