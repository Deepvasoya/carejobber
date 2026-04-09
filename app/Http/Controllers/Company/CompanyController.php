<?php



namespace App\Http\Controllers\Company;



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

use App\UnlockedUser;
use App\UnlockedUserStatus;
use App\Job;
use App\JobApply;

use App\Industry;

use App\FavouriteCompany;

use App\Package;

use App\FavouriteApplicant;

use App\OwnershipType;

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

use App\Http\Controllers\Controller;

use App\Traits\CompanyTrait;
use App\Traits\ReferralTrait;
use App\Traits\Cron;
use App\Traits\FetchJobSeekers;

use Illuminate\Support\Str;
use App\Mail\DocumentsUpload;



class CompanyController extends Controller

{



    use CompanyTrait;
    use ReferralTrait;
    use Cron;
    use FetchJobSeekers;



    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('company', ['except' => ['companyDetail', 'sendContactForm','featuredcompanies']]);

        $this->runCheckPackageValidity();

    }



    public function index()
    {
        Auth::guard('company')->setUser(Auth::guard('company')->user()->fresh());
        $company = Auth::guard('company')->user();
        
        // Suggested candidates: profile job category (functional_area) matches an active job's category
        $suggestedCandidates = $this->fetchJobSeekersMatchingPostedFunctionalAreas((int) $company->id, 6);
        
        return view('company_home', compact('suggestedCandidates'));
    }

    public function company_listing()

    {

        $data['companies']=Company::paginate(20);
        $data['countries'] = DataArrayHelper::langCountriesArray();
        $data['industries'] = DataArrayHelper::defaultIndustriesArray();

        return view('company.listing')->with($data);

    } 

    public function indexCompaniesHistory()
    {
        $company = auth()->guard('company')->user();

        $payments = \App\PaymentHistory::where('company_id', $company->id)
            ->where('user_type', 'company')
            ->with('package')
            ->orderBy('created_at', 'DESC')
            ->get();

        if ($payments->isEmpty() && $company->package_id) {
            $pkg = $company->package;
            if ($pkg) {
                $fallback = new \stdClass();
                $fallback->package = $pkg;
                $fallback->package_title = $pkg->package_title;
                $fallback->package_price = $pkg->package_price;
                $fallback->payment_method = $company->payment_method ?? 'Admin Assign';
                $fallback->package_start_date = $company->package_start_date;
                $fallback->package_end_date = $company->package_end_date;
                $fallback->jobs_quota = $company->jobs_quota;
                $fallback->package_type = 'job';
                $fallback->created_at = $company->package_start_date;
                $payments = collect([$fallback]);
            }
        }

        return view('company.payment_history', compact('payments'));
    }
public function fetchCompaniesHistory(Request $request)

{

    $companies = Company::select('*');

    return Datatables::of($companies)

                    ->filter(function ($query) use ($request) {

                        
                        $query->where('companies.id',auth()->guard('company')->user()->id);

                       

                        if ($request->has('payment_method') && !empty($request->payment_method)) {
                            // If a specific payment method is requested, filter by it
                            $query->where('companies.payment_method', 'like', "%{$request->get('payment_method')}%");
                        } else {
                            // If no specific payment method is requested, include "offline" payments added by admin
                            $query->orWhere('companies.payment_method', 'offline')
                                  ->orWhereNull('companies.payment_method') // Null payment method if admin didn't set one
                                  ->where('companies.added_by', 'admin'); // Assuming `added_by` tracks the creator
                        }
                        

                        if ($request->filled('package')) {
                            $query->where('companies.package_id', $request->get('package'));
                        }

                        $query->where('package_start_date','!=','')->orderBy('package_start_date', 'DESC');                           

                    })

                    ->addColumn('payment_method', function ($companies) {
                        if ($companies->payment_method === 'offline') {
                            return 'Offline (Added by Admin)';
                        }
                        return $companies->payment_method ?: 'N/A';
                    })

                    ->addColumn('package', function ($companies) {
                        $package = Package::findOrFail($companies->package_id);
                        return $package->package_title;

                    })

                    ->addColumn('package_start_date', function ($companies) {                            
                        return date('d-m-Y',strtotime($companies->package_start_date));
                    })

                    ->addColumn('package_end_date', function ($companies) {                            
                        return date('d-m-Y',strtotime($companies->package_end_date));
                    })


                    ->rawColumns(['package_start_date', 'package_end_date'])
                    ->setRowId(function($companies) {
                        return 'companyDtRow' . $companies->id;
                    })

                    ->make(true);
  

}


public function downloadReceipt($companyId)
{
    // Fetch the company record
    $company = Company::findOrFail($companyId);
    $siteSetting = siteSetting(); // Assuming you have a method to get site settings
    
    // Prepare the data for the PDF
    $data = [
        'company' => $company,
        'siteSetting' => $siteSetting,
        'package' => $company->package,  // Assuming `package` is a relationship or a field in the company table
    ];

    // Generate the PDF (using the PDF facade or any package you've chosen)
    $pdf = PDF::loadView('company.receipt', $data);

    // Return the PDF as a download
    return $pdf->download('receipt_' . $company->id . '.pdf');
}

    
    public function featuredcompanies(Request $request)

    {   
        
        $search = $request->get('search');
		$country_id = $request->get('country_id');
		$state_id = $request->get('state_id');
		$city_id = $request->get('city_id');
		$industry_id = $request->get('industry_id');
        $query = Company::select('*');
        if ($search != '') {
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        if ($country_id != '') {
            $query->where('country_id', $country_id);
        }
        if ($state_id != '') {
            $query->where('state_id', $state_id);
        }
        
        if ($city_id != '') {
            $query->where('city_id', $city_id);
        }
        
        if ($industry_id != '') {
            $query->where('industry_id', $industry_id);
        }
               
        $query->where('is_active', true)->where('verified', true);

        $data['companies'] = $query->where('is_featured',1)->paginate(20);
        $data['countries'] = DataArrayHelper::langCountriesArray();
        $data['industries'] = DataArrayHelper::defaultIndustriesArray();

        return view('company.featuredcompanies')->with($data);

    }



    public function companyProfile()

    {
        

        $countries = DataArrayHelper::defaultCountriesArray();

        $industries = DataArrayHelper::defaultIndustriesArray();

        $ownershipTypes = DataArrayHelper::defaultOwnershipTypesArray();

        $company = Company::findOrFail(Auth::guard('company')->user()->id);

        return view('company.edit_profile')

                        ->with('company', $company)

                        ->with('countries', $countries)

                        ->with('industries', $industries)

                        ->with('ownershipTypes', $ownershipTypes);

    }
    
    
    
    public function company_documents()

    {
        

        $countries = DataArrayHelper::defaultCountriesArray();

        $industries = DataArrayHelper::defaultIndustriesArray();

        $ownershipTypes = DataArrayHelper::defaultOwnershipTypesArray();

        $company = Company::findOrFail(Auth::guard('company')->user()->id);

        return view('company.company_documents')

                        ->with('company', $company)

                        ->with('countries', $countries)

                        ->with('industries', $industries)

                        ->with('ownershipTypes', $ownershipTypes);

    }
    
    public function uploadDocuments(Request $request)
    {
        // Get the authenticated company
        $company = auth()->guard('company')->user();
    
        // Define the directory path
        $directoryPath = public_path('/company_documents/' . $company->id);
    
        // Check if the directory exists, if not, create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }
    
        // Define the document fields
        $documentFields = [
            'incorporation_or_formation_certificate',
            'valid_tax_clearance',
            'proof_of_address',
            'other_supporting_documents',
        ];
    
        // Loop through the document fields
        foreach ($documentFields as $field) {
            if ($request->hasFile($field)) {
                // Store the file in the company-specific directory
                $file = $request->file($field);
                $filename = $field . '.' . $file->getClientOriginalExtension();
                $file->move($directoryPath, $filename);
    
                // Optionally update the company record with the file path (e.g., in the database)
                $company->$field = '/company_documents/' . $company->id . '/' . $filename;
                $status_field = $field.'_status';
                $company->$status_field = 0;
                $company->is_active = 0;;
            }
        }
        
        // Save the company with the updated document paths
        $company->save();
        
        $data['company'] = $company;
        $data['id'] = $company->id;
        $data['full_name'] = $company->name;
        $data['email'] = $company->email;
        $data['phone'] = $company->phone;
        $data['subject'] = 'Company Documents Uploaded by '.$company->name;
        $data['message_txt'] = $company->name.' has uploaded there documents please verify <a href="'.url('admin/public-company/'.$company->id).'">View Detail</a>';
        $data['is_admin'] = false;
        $when = Carbon::now()->addMinutes(5);
        Mail::send(new DocumentsUpload($data));
        flash(__('Documents uploaded successfully!'))->success();
        // Redirect back with a success message
        return redirect()->back();
    }



    public function updateCompanyProfile(CompanyFrontFormRequest $request)

    {

        $company = Company::findOrFail(Auth::guard('company')->user()->id);

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
        
        $company->registration_number = $request->input('registration_number');
                      
        
        $company->contact_name = $request->input('contact_name');
        
        $company->contact_email = $request->input('contact_email');

		$company->is_subscribed = $request->input('is_subscribed', 0);

        $cf = app(\App\Services\CustomFieldValueService::class);
        $norm = $cf->normalizeForContext($request, \App\Models\CustomField::CONTEXT_COMPANY_PROFILE);
        $company->custom_field_data = $cf->mergeStored($company->custom_field_data ?? null, $norm);

		

        $company->slug = Str::slug($company->name, '-') . '-' . $company->id;

        $company->update();

		/*************************/

		// Subscription::where('email', 'like', $company->email)->delete();
		// if((bool)$company->is_subscribed)
		// {			

		// 	$subscription = new Subscription();
		// 	$subscription->email = $company->email;
		// 	$subscription->name = $company->name;
		// 	$subscription->save();

		// 	Newsletter::subscribeOrUpdate($subscription->email, ['FNAME'=>$subscription->name]);
		// }
		// else
		// {
		// 	Newsletter::unsubscribe($company->email);
		// }



        

        flash(__('Company has been updated'))->success();

        return \Redirect::route('company.profile');

    }



    public function addToFavouriteApplicant(Request $request, $application_id, $user_id, $job_id, $company_id)

    {

        $data['user_id'] = $user_id;

        $data['job_id'] = $job_id;

        $data['company_id'] = $company_id;



        $data_save = FavouriteApplicant::create($data);

        flash(__('Job seeker has been added in favorites list'))->success();

        return \Redirect::route('applicant.profile', $application_id);

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



        flash(__('Job seeker has been removed from favorites list'))->success();

        return \Redirect::route('applicant.profile', $application_id);

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



        flash(__('Job seeker has been Hired from favorites list'))->success();

        return \Redirect::route('applicant.profile', $application_id);

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



        flash(__('Job seeker has been removed from hired list'))->success();

        return \Redirect::route('applicant.profile', $application_id);

    }



    public function companyDetail(Request $request, $company_slug)

    {
        
      

        $company = Company::where('slug', 'like', $company_slug)->firstOrFail();

        /*         * ************************************************** */

        $seo = $this->getCompanySEO($company);

        /*         * ************************************************** */

        return view('company.detail')

                        ->with('company', $company)

                        ->with('seo', $seo);

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

            // 'g-recaptcha-response' => 'required|captcha',

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

        $jobs = Auth::guard('company')->user()->jobs()->paginate(10);

        return view('job.company_posted_jobs')

                        ->with('jobs', $jobs);

    }



    public function listAppliedUsers(Request $request, $job_id)

    {

        $job_applications = JobApply::where('job_id', '=', $job_id)->get();
        $job = Job::findOrFail($job_id);

        return view('job.job_applications')

                        ->with('job_applications', $job_applications)
                        ->with('job', $job);

    }



    public function listHiredUsers(Request $request, $job_id)

    {

        $company_id = Auth::guard('company')->user()->id;

        $user_ids = FavouriteApplicant::where('job_id', '=', $job_id)->where('company_id', '=', $company_id)->where('status','hired')->pluck('user_id')->toArray();

        $job_applications = JobApply::where('job_id', '=', $job_id)->whereIn('user_id', $user_ids)->get();



        return view('job.hired_applications')

                        ->with('job_applications', $job_applications);

    }



    public function listRejectedUsers(Request $request, $job_id)

    {

        $job_applications = JobApplyRejected::where('job_id', '=', $job_id)->get();



        return view('job.job_rejected_users')

                        ->with('job_applications', $job_applications);

    }



    public function listFavouriteAppliedUsers(Request $request, $job_id)

    {

        $company_id = Auth::guard('company')->user()->id;

        $user_ids = FavouriteApplicant::where('job_id', '=', $job_id)->where('company_id', '=', $company_id)->where('status',null)->pluck('user_id')->toArray();

        $job_applications = JobApply::where('job_id', '=', $job_id)->whereIn('user_id', $user_ids)->get();



        return view('job.job_applications')

                        ->with('job_applications', $job_applications);

    }

    public function downloadCsv()
{
    $applications = JobApplication::with(['user', 'job'])->get();

    $csvData = "Name,Location,Expected Salary,Salary Currency,Job Title,Status\n";

    foreach ($applications as $app) {
        $csvData .= "{$app->user->getName()},{$app->user->getLocation()},{$app->expected_salary},{$app->salary_currency},{$app->job->title},{$app->status}\n";
    }

    $fileName = 'job_applications.csv';

    return Response::make($csvData, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename={$fileName}",
    ]);
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

        /*         * ********************************************** */

        return view('user.applicant_profile')

                        ->with('job_application', $job_application)

                        ->with('user', $user)

                        ->with('job', $job)

                        ->with('company', $company)

                        ->with('profileCv', $profileCv)

                        ->with('page_title', 'Applicant Profile')

                        ->with('form_title', 'Contact Applicant')

                        ->with('is_applicant', $is_applicant);

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





        flash(__('Job seeker has been rejected successfully'))->success();

        return \Redirect::route('rejected-users',$job->id);

    }



    public function userProfile($id)

    {



        $user = User::findOrFail($id);

        $profileCv = $user->getDefaultCv();



        /*         * ********************************************** */

        $num_profile_views = $user->num_profile_views + 1;

        $user->num_profile_views = $num_profile_views;

        $user->update();

        /*         * ********************************************** */

        return view('user.applicant_profile')

                        ->with('user', $user)

                        ->with('profileCv', $profileCv)

                        ->with('page_title', 'Job Seeker Profile')

                        ->with('form_title', 'Contact Job Seeker');

    }



    public function companyFollowers()

    {

        $company = Company::findOrFail(Auth::guard('company')->user()->id);

        $userIdsArray = $company->getFollowerIdsArray();

        $users = User::whereIn('id', $userIdsArray)->get();



        return view('company.follower_users')

                        ->with('users', $users)

                        ->with('company', $company);

    }



    public function companyMessages()

    {

        $company = Company::findOrFail(Auth::guard('company')->user()->id);

        $messages = CompanyMessage::where('company_id', '=', $company->id)

                ->orderBy('is_read', 'asc')

                ->orderBy('created_at', 'desc')

                ->get();



        return view('company.company_messages')

                        ->with('company', $company)

                        ->with('messages', $messages);

    }



    public function companyMessageDetail($message_id)

    {

        $company = Company::findOrFail(Auth::guard('company')->user()->id);

        $message = CompanyMessage::findOrFail($message_id);

        $message->update(['is_read' => 1]);



        return view('company.company_message_detail')

                        ->with('company', $company)

                        ->with('message', $message);

    }



    

    public function resume_search_packages()
{

    

    $data['packages'] = Package::where('package_for', 'cv_search')->get();

    Auth::guard('company')->setUser(Auth::guard('company')->user()->fresh());
    $company = Auth::guard('company')->user();

   

    $data['success_package'] = null;

    if (!empty($company->cvs_package_id) && Package::where('id', $company->cvs_package_id)->exists()) {
        $data['success_package'] = Package::find($company->cvs_package_id);
    }

    return view('company_resume_search_packages')->with($data);
}


    public function UnlockedUser()
    {
        $data = array();
        $company_id = Auth::guard('company')->user()->id;

        // Get all unlocked users with their status
        $unlocked_user_statuses = UnlockedUserStatus::where('company_id', $company_id)
            ->with('user')
            ->get();

        $data['unlocked_user_statuses'] = $unlocked_user_statuses;

        return view('company.unlocked_users')->with($data);
    }

    public function setUnlockedUserStatus(Request $request)
    {
        $company_id = Auth::guard('company')->user()->id;
        
        $unlocked = json_decode($request->unlocked, true);
        $shortlist = json_decode($request->shortlist, true);
        $hired = json_decode($request->hired, true);
        $rejected = json_decode($request->rejected, true);
        
        if($unlocked){
            UnlockedUserStatus::where('company_id', $company_id)
                ->whereIn('user_id', $unlocked)
                ->update(['status' => 'unlocked']);
        }
        if($shortlist){
            UnlockedUserStatus::where('company_id', $company_id)
                ->whereIn('user_id', $shortlist)
                ->update(['status' => 'shortlist']);
        }
        if($hired){
            UnlockedUserStatus::where('company_id', $company_id)
                ->whereIn('user_id', $hired)
                ->update(['status' => 'hired']);
        }
        if($rejected){
            UnlockedUserStatus::where('company_id', $company_id)
                ->whereIn('user_id', $rejected)
                ->update(['status' => 'rejected']);
        }

        return response()->json(['success' => true]);
    }



    public function unlock($user_id)
    {
        $company = Auth::guard('company')->user();
        
        // Check if already unlocked via new system (Stripe or credits)
        if (\App\Models\ResumeUnlock::isUnlockedBy($user_id, $company->id)) {
            return redirect()->back()->with('info', __('You have already unlocked this profile.'));
        }

        // Check if company has CV package credits available (active package + remaining credits)
        if ($company->hasActiveCvSearchPackage()
            && $company->getRemainingCvsQuota() > 0) {
            
            // Use credit to unlock
            if ($company->availed_cvs_ids != '') {
                $newString = $this->addtoString($company->availed_cvs_ids, $user_id);
            } else {
                $newString = $user_id;
            }

            $company->availed_cvs_ids = $newString;
            $company->availed_cvs_quota += 1;
            $company->update();

            // Update old UnlockedUser table
            $unlock = UnlockedUser::where('company_id', $company->id)->first();
            if ($unlock) {
                $unlock->unlocked_users_ids = $newString;
                $unlock->update();
            } else {
                $unlock = new UnlockedUser();
                $unlock->company_id = $company->id;
                $unlock->unlocked_users_ids = $newString;
                $unlock->save();
            }

            // Create entry in new resume_unlocks table
            \App\Models\ResumeUnlock::create([
                'company_id' => $company->id,
                'user_id' => $user_id,
                'paid_amount' => 0,
                'currency' => 'CAD',
                'payment_method' => 'credits',
                'unlocked_at' => now(),
            ]);

            // Also create/update entry in unlocked_user_status table
            $unlockedUserStatus = UnlockedUserStatus::firstOrNew([
                'company_id' => $company->id,
                'user_id' => $user_id
            ]);
            $unlockedUserStatus->status = 'unlocked';
            $unlockedUserStatus->save();

            \Log::info('[Unlock] Via credits', [
                'company_id' => $company->id,
                'user_id' => $user_id,
            ]);

            Auth::guard('company')->setUser($company->fresh());

            return redirect()->back()->with('success', __('Profile unlocked successfully!'));
        } else {
            \Log::info('[Unlock] No credits; redirect to Stripe', [
                'company_id' => $company->id,
                'user_id' => $user_id,
            ]);
            $companyFresh = $company->fresh();
            if ($companyFresh && $companyFresh->isOnExhaustedFreeCvSearchPeriod()) {
                $until = $companyFresh->getFreeCvPackageNextAvailableAt();
                $when = $until ? $until->format('d M Y H:i') : __('the end of your current period.');

                return redirect()->route('resume.unlock.checkout', ['userId' => $user_id])
                    ->with('info', __('You have used all CV unlocks from your free package for this 30-day period. You cannot activate the free package again until :date. You can purchase a paid CV package to unlock now.', ['date' => $when]));
            }

            return redirect()->route('resume.unlock.checkout', ['userId' => $user_id])
                ->with('info', __('No credits available. Please purchase to unlock this profile.'));
        }
    }

    function addtoString($str, $item)

    {

        $parts = explode(',', $str);

        $parts[] = $item;



        return implode(',', $parts);

    }

   





}

