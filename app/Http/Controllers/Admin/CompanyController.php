<?php
namespace App\Http\Controllers\Admin;
use Hash;
use File;
use ImgUploader;
use Auth;
use DB;
use Input;
use Redirect;
use App\Package;
use App\Company;
use App\User;
use App\JobApply;
use App\Country;
use App\Job;
use App\State;
use App\City;
use App\Industry;
use App\OwnershipType;
use Carbon\Carbon;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\Http\Requests\CompanyFormRequest;
use App\Http\Controllers\Controller;
use App\Traits\CompanyTrait;
use App\Traits\CompanyPackageTrait;
use Illuminate\Support\Str;
use App\Mail\DocumentsUpload;
use App\Services\EmailTemplateService;
use App\UnlockedUser;
use App\VerificationDocument;
use App\Notifications\ClaimRequestApproved;
use App\Notifications\ClaimRequestRejected;
use Illuminate\Support\Facades\Password;
use Mail;
class CompanyController extends Controller
{
    use CompanyTrait;
    use CompanyPackageTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function indexCompanies()
    {
        return view('admin.company.index');
    }
    public function indexCompaniesHistory()
    {
        $employerPackages = Package::where('package_for', 'employer')
            ->pluck('package_title', 'id')
            ->toArray();
        $cvSearchPackages = Package::where('package_for', 'cv_search')
            ->pluck('package_title', 'id')
            ->toArray();
        $packages = $employerPackages + $cvSearchPackages;
        
        // Calculate statistics from payment_history table
        $stats = [
            'total_payments' => \App\PaymentHistory::companyTransactions()->count(),
            'total_revenue' => $this->calculateTotalRevenue(),
            'active_subscriptions' => \App\PaymentHistory::companyTransactions()
                ->where('package_end_date', '>=', now())
                ->where('payment_status', 'completed')
                ->count(),
            'expired_subscriptions' => \App\PaymentHistory::companyTransactions()
                ->where('package_end_date', '<', now())
                ->where('payment_status', 'completed')
                ->count(),
            'total_cv_packages' => \App\PaymentHistory::companyTransactions()
                ->where('package_type', 'cv_search')
                ->count(),
            'total_job_packages' => \App\PaymentHistory::companyTransactions()
                ->where('package_type', 'job')
                ->count(),
        ];
        
        return view('admin.company.payment_history')
            ->with('packages', $packages)
            ->with('stats', $stats);
    }
    
    private function calculateTotalRevenue()
    {
        return \App\PaymentHistory::companyTransactions()->completed()->sum('package_price');
    }
    
    public function getCompanyPaymentDetails(Request $request)
    {
        if ($request->filled('payment_id')) {
            $payment = \App\PaymentHistory::with('company')->findOrFail((int) $request->get('payment_id'));
            $company = $payment->company;
            if (!$company) {
                return response()->json(['error' => 'Company not found'], 404);
            }
            $jobPackage = $payment->package_type === 'job' && $payment->package_id
                ? Package::find($payment->package_id)
                : ($company->package_id ? Package::find($company->package_id) : null);
            $cvPackage = $payment->package_type === 'cv_search' && $payment->package_id
                ? Package::find($payment->package_id)
                : ($company->cvs_package_id ? Package::find($company->cvs_package_id) : null);

            return response()->json([
                'company' => $company,
                'job_package' => $jobPackage,
                'cv_package' => $cvPackage,
                'payment' => $payment,
            ]);
        }

        $company = Company::with([
                'industry' => function ($query) {
                    $query->where('is_default', 1);
                },
                'ownershipType' => function ($query) {
                    $query->where('is_default', 1);
                },
                'country' => function ($query) {
                    $query->where('is_default', 1);
                },
                'state' => function ($query) {
                    $query->where('is_default', 1);
                },
                'city' => function ($query) {
                    $query->where('is_default', 1);
                }
            ])
            ->findOrFail($request->id);

        $jobPackage = $company->package_id ? Package::find($company->package_id) : null;
        $cvPackage = $company->cvs_package_id ? Package::find($company->cvs_package_id) : null;

        return response()->json([
            'company' => $company,
            'job_package' => $jobPackage,
            'cv_package' => $cvPackage,
            'payment' => null,
        ]);
    }
    public function fetchCompaniesHistory(Request $request)
    {
        // Query from payment_history table for all company transactions
        $payments = \App\PaymentHistory::select('payment_history.*')
            ->with('company')
            ->companyTransactions();
            
        return Datatables::of($payments)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('name') && !empty($request->name)) {
                                $query->whereHas('company', function($q) use ($request) {
                                    $q->where('name', 'like', "%{$request->get('name')}%");
                                });
                            }
                            
                            if ($request->has('email') && !empty($request->email)) {
                                $query->whereHas('company', function($q) use ($request) {
                                    $q->where('email', 'like', "%{$request->get('email')}%");
                                });
                            }
                            
                            if ($request->has('package_type') && !empty($request->package_type)) {
                                $typeMap = ['job' => 'job', 'cv' => 'cv_search'];
                                $query->where('payment_history.package_type', $typeMap[$request->package_type]);
                            }
                            
                            if ($request->has('payment_method') && !empty($request->payment_method)) {
                                $query->where('payment_history.payment_method', 'like', "%{$request->get('payment_method')}%");
                            }
                            
                            if ($request->has('package') && !empty($request->package)) {
                                $query->where('payment_history.package_id', $request->get('package'));
                            }
                            
                            $query->orderBy('payment_history.created_at', 'DESC');
                        })
                        ->addColumn('name', function ($payment) {
                            return $payment->company ? $payment->company->name : 'N/A';
                        })
                        ->addColumn('email', function ($payment) {
                            return $payment->company ? $payment->company->email : 'N/A';
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
                        ->addColumn('package_type_badge', function ($payment) {
                            if ($payment->package_type == 'job') {
                                return '<span class="label label-primary">Job Package</span>';
                            } else {
                                return '<span class="label label-success">CV Package</span>';
                            }
                        })
                        ->addColumn('package', function ($payment) {
                            $badgeClass = ($payment->package_type == 'cv_search') ? 'badge-success' : 'badge-primary';
                            $listPrice = $payment->package_list_price ?? $payment->package_price;
                            $paidPrice = $payment->package_price;
                            $hasDiscount = abs($listPrice - $paidPrice) > 0.009;
                            
                            $html = '<span class="badge ' . $badgeClass . '">' . $payment->package_title . '</span><br>';
                            
                            if ($hasDiscount) {
                                $discount = $listPrice - $paidPrice;
                                $html .= '<small class="text-muted" style="text-decoration: line-through;">$' . number_format($listPrice, 2) . '</small> ';
                                $html .= '<strong style="color: #17D27C;">$' . number_format($paidPrice, 2) . '</strong><br>';
                                $html .= '<small class="text-success"><i class="fa fa-tag"></i> Saved $' . number_format($discount, 2) . '</small>';
                            } else {
                                $html .= '<strong>$' . number_format($paidPrice, 2) . '</strong>';
                            }
                            
                            return $html;
                        })
                        ->addColumn('quota', function ($payment) {
                            if ($payment->package_type == 'job') {
                                $company = $payment->company;
                                $availedQuota = $company ? ($company->availed_jobs_quota ?? 0) : 0;
                                return 'Jobs: ' . $availedQuota . '/' . $payment->jobs_quota;
                            } else {
                                $company = $payment->company;
                                $availedQuota = $company ? ($company->availed_cvs_quota ?? 0) : 0;
                                return 'CVs: ' . $availedQuota . '/' . $payment->cvs_quota;
                            }
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
                        ->addColumn('action', function ($payment) {
                            return '<button type="button" class="btn btn-sm btn-info view-details" data-payment-id="' . $payment->id . '" data-id="' . $payment->company_id . '" data-type="' . $payment->package_type . '"><i class="ri ri-eye-line me-1"></i> View</button>';
                        })
                        ->rawColumns(['payment_method', 'package_type_badge', 'package', 'quota', 'package_start_date', 'package_end_date', 'action'])
                        ->setRowId(function($payment) {
                            return 'payment_' . $payment->id;
                        })
                        ->make(true);
    }
    public function createCompany()
    {
        $countries = DataArrayHelper::defaultCountriesArray();
        $industries = DataArrayHelper::defaultIndustriesArray();
        $ownershipTypes = DataArrayHelper::defaultOwnershipTypesArray();
        
        // Fetch employer packages
        $employerPackages = Package::where('package_for', 'employer')
            ->select('id', DB::raw("CONCAT(`package_title`, ', $', `package_price`, ', Days:', `package_num_days`, ', Listings:', `package_num_listings`) AS package_detail"))
            ->pluck('package_detail', 'id')
            ->toArray();

        // Fetch CV packages
        $cvSearchPackages = Package::where('package_for', 'cv_search')
            ->select('id', DB::raw("CONCAT(`package_title`, ', $', `package_price`, ', Days:', `package_num_days`) AS package_detail"))
            ->pluck('package_detail', 'id')
            ->toArray();

        return view('admin.company.add', compact(
            'countries',
            'industries',
            'ownershipTypes',
            'employerPackages',
            'cvSearchPackages'
        ))->with([
            'company' => null, // No existing company
            'selectedEmployerPackage' => null, // No package selected
            'selectedCvPackage' => null // No CV package selected
        ]);
    }


    public function storeCompany(CompanyFormRequest $request)
    {
        $company = new Company();
        /*         * **************************************** */
        if ($request->hasFile('logo')) {
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
        $company->industry_id = $request->input('industry_id', 0);
        $company->ownership_type_id = $request->input('ownership_type_id', 0);
        $company->description = $request->input('description');
        $company->location = $request->input('location');
        $company->map = $request->input('map');
        $company->no_of_offices = $request->input('no_of_offices');
        $website = $request->input('website');
        if (!empty($website)) {
            $company->website = (false === strpos($website, 'http')) ? 'http://' . $website : $website;
        }
        $company->no_of_employees = $request->input('no_of_employees');
        $company->established_in = $request->input('established_in');
        $company->fax = $request->input('fax');
        $company->phone = $request->input('phone');
        $company->facebook = $request->input('facebook');
        $company->twitter = $request->input('twitter');
        $company->linkedin = $request->input('linkedin');
        $company->google_plus = $request->input('google_plus');
        $company->pinterest = $request->input('pinterest');
        $company->country_id = $request->input('country_id', 0);
        $company->state_id = $request->input('state_id', 0);
        $company->city_id = $request->input('city_id', 0);
        $company->is_active = $request->input('is_active');
        $company->is_featured = $request->input('is_featured');
        
        // Set claim-related fields for admin-created companies
        if ($request->input('created_by_admin')) {
            $company->created_by_admin = 1;
            $company->is_claimed = 0;
            $company->claimed_by_user_id = null;
            $company->claimed_at = null;
        } else {
            $company->created_by_admin = 0;
            $company->is_claimed = 1; // Regular companies are considered claimed by default
        }
        
        $company->save();
        

        /*         * ******************************* */
        $company->slug = Str::slug($company->name, '-') . '-' . $company->id;
        /*         * ******************************* */
        $company->update();
        /*         * ************************************ */
        if ($request->has('company_package_id') && $request->input('company_package_id') > 0) {
            $package_id = $request->input('company_package_id');
            $package = Package::find($package_id);
            $this->addCompanyPackage($company, $package);
        }
        // Handling CV package
        if ($request->has('cvs_package_id') && $request->input('cvs_package_id') > 0) {
            $cvs_package_id = $request->input('cvs_package_id');
            $cvsPackage = Package::find($cvs_package_id);
            if ($company->cvs_package_id > 0) {
                $this->updateCvsPackage($company, $cvsPackage);
            } else {
                $this->addCvsPackage($company, $cvsPackage);
            }
        }
        /*         * ************************************ */
        flash('Company has been added!')->success();
        return \Redirect::route('edit.company', array($company->id));
    }
    private function addCvsPackage($company, $cvsPackage)
    {
        $company->cvs_package_id = $cvsPackage->id;
        $company->cvs_package_start_date = now();
        $company->cvs_package_end_date = now()->addDays($cvsPackage->package_num_days);
        $company->save();
    }
    
    private function updateCvsPackage($company, $cvsPackage)
    {
        $company->cvs_package_id = $cvsPackage->id;
        $company->cvs_package_end_date = now()->addDays($cvsPackage->package_num_days);
        $company->save();
    }
    
    public function editCompany($id)
{
    $countries = DataArrayHelper::defaultCountriesArray();
    $industries = DataArrayHelper::defaultIndustriesArray();
    $ownershipTypes = DataArrayHelper::defaultOwnershipTypesArray();
    $company = Company::findOrFail($id);
    // Get the currently selected packages
    $selectedEmployerPackage = $company->package_id ?? null;
    $selectedCvPackage = $company->cv_package_id ?? null;
    // Fetch employer packages
    $employerPackages = Package::where('package_for', 'employer')
        ->select('id', DB::raw("CONCAT(`package_title`, ', $', `package_price`, ', Days:', `package_num_days`, ', Listings:', `package_num_listings`) AS package_detail"))
        ->pluck('package_detail', 'id')
        ->toArray();
    // Fetch CV packages
    $cvSearchPackages = Package::where('package_for', 'cv_search')
        ->select('id', DB::raw("CONCAT(`package_title`, ', $', `package_price`, ', Days:', `package_num_days`) AS package_detail"))
        ->pluck('package_detail', 'id')
        ->toArray();
    return view('admin.company.edit', compact(
        'company',
        'countries',
        'industries',
        'ownershipTypes',
        'employerPackages',
        'cvSearchPackages',
        'selectedEmployerPackage',
        'selectedCvPackage'
    ));
}
public function updateCompany($id, CompanyFormRequest $request)
{
    
    $company = Company::findOrFail($id);
    // Handle logo upload
    if ($request->hasFile('logo')) {
        $this->deleteCompanyLogo($company->id);
        $image = $request->file('logo');
        $fileName = ImgUploader::UploadImage('company_logos', $image, $request->input('name'), 300, 300, false);
        $company->logo = $fileName;
    }
    // Assign other company details
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
    $company->is_active = $request->input('is_active');
    $company->is_featured = $request->input('is_featured');
    $company->slug = Str::slug($company->name, '-') . '-' . $company->id;
    if ($request->has('created_by_admin')) {
        $company->created_by_admin = 1;
        $company->is_claimed = 0;
    } elseif (!$company->created_by_admin) {
        $company->created_by_admin = 0;
        $company->is_claimed = 1;
    }
    // Assign employer package
    if ($request->has('company_package_id') && $request->input('company_package_id') > 0) {
        $package_id = $request->input('company_package_id');
        $package = Package::find($package_id);
        if ($company->package_id > 0) {
            $this->updateCompanyPackage($company, $package);
        } else {
            $this->addCompanyPackage($company, $package);
        }
    }
    // Assign CV package
    if ($request->has('cv_package_id') && $request->input('cv_package_id') > 0) {
        $cvPackageId = $request->input('cv_package_id');
        $cvsPackage = Package::find($cvPackageId);
        
        if ($cvsPackage) {
            $company->cvs_package_id = $cvPackageId;
            $company->cvs_package_start_date = now();
            $company->cvs_package_end_date = now()->addDays($cvsPackage->package_num_days);
        }
    }
    
    // Save company data
    $company->update();
    flash('Company has been updated!')->success();
    return \Redirect::route('edit.company', [$company->id]);
}
    public function changeStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'field' => 'required|string',
            'status' => 'required|boolean',
        ]);
        // Find the company by ID
        $company = Company::findOrFail($id);
        if ($company) {
            // Update the specified status field
            $company->{$validated['field'] . '_status'} = $validated['status'];
            $company->{$validated['field'] . '_comment'} = $request->comments;
            // Check if all required statuses are valid
            $allStatusesValid = $company->incorporation_or_formation_certificate_status == 1 &&
                                $company->valid_tax_clearance_status == 1 &&
                                $company->proof_of_address_status == 1 &&
                                $company->other_supporting_documents_status == 1;
            // Update is_active based on the check
            if ($allStatusesValid) {
                $company->is_active = 1;
            }else{
                $company->is_active = 0;
            }
            // Save the company
            $company->save();
            if($company->is_active == 1){
                $package = Package::findOrFail(13);
                $this->addCompanyPackage($company, $package);
            }
            $data['status'] = $validated['status'];
            $data['company'] = $company;
            $data['id'] = $company->id;
            $data['full_name'] = $company->name;
            $data['email'] = $company->email;
            $data['phone'] = $company->phone;
              $data['subject'] = $company->is_active == 0?ucwords(str_replace('_',' ',$validated['field'])):'Congratulations Your account is Active now';
            $data['message_txt'] = $company->is_active == 0?'Your account is currently inactive because document verification is still pending.. <br><strong>Note</strong>: '.$request->comments:'Your account is active, but in order to post jobs, you need to buy a plan to Post a New Job';
            $data['notes'] = $request->comments;
            $data['status'] = $request->comments?'rejected':'approved';
            $data['is_admin'] = true;
            $when = Carbon::now()->addMinutes(5);
            if($company->is_active == 1){
                Mail::send(new DocumentsUpload($data));
            }elseif($validated['status'] == 0 ){
                Mail::send(new DocumentsUpload($data));
            }
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 500);
    }
    public function deleteCompany(Request $request)
    {
        $id = $request->input('id');
        try {
            $company = Company::findOrFail($id);
            $this->deleteCompanyLogo($company->id);
            $company->delete();
            return 'ok';
        } catch (ModelNotFoundException $e) {
            return 'notok';
        }
    }

    public function bulkDeleteCompanies(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 400);
        }
        
        try {
            foreach ($ids as $id) {
                $company = Company::find($id);
                if ($company) {
                    $this->deleteCompanyLogo($company->id);
                    $company->delete();
                }
            }
            return response()->json(['success' => true, 'message' => 'Selected companies deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting companies'], 500);
        }
    }
    public function fetchCompaniesData(Request $request)
    {
        $companies = Company::select([
                    'companies.id',
                    'companies.name',
                    'companies.email',
                    'companies.password',
                    'companies.ceo',
                    'companies.industry_id',
                    'companies.ownership_type_id',
                    'companies.description',
                    'companies.location',
                    'companies.no_of_offices',
                    'companies.website',
                    'companies.no_of_employees',
                    'companies.established_in',
                    'companies.fax',
                    'companies.phone',
                    'companies.logo',
                    'companies.country_id',
                    'companies.state_id',
                    'companies.city_id',
                    'companies.is_active',
                    'companies.is_featured',
                    'companies.employer_trust_status',
                    'companies.is_claimed',
                    'companies.created_by_admin',
        ]);
        return Datatables::of($companies)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('name') && !empty($request->name)) {
                                $query->where('companies.name', 'like', "%{$request->get('name')}%");
                            }
                            if ($request->has('email') && !empty($request->email)) {
                                $query->where('companies.email', 'like', "%{$request->get('email')}%");
                            }
                            if ($request->has('is_active') && $request->is_active != -1) {
                                $query->where('companies.is_active', '=', "{$request->get('is_active')}");
                            }
                            if ($request->has('is_featured') && $request->is_featured != -1) {
                                $query->where('companies.is_featured', '=', "{$request->get('is_featured')}");
                            }
                            if ($request->has('claim_status')) {
                                if ($request->claim_status === 'claimed') {
                                    $query->where('companies.is_claimed', 1);
                                } elseif ($request->claim_status === 'unclaimed') {
                                    $query->where('companies.created_by_admin', 1)->where('companies.is_claimed', 0);
                                } elseif ($request->claim_status === 'admin_created') {
                                    $query->where('companies.created_by_admin', 1);
                                }
                            }
                        })
                        ->addColumn('is_active', function ($companies) {
                            return ((bool) $companies->is_active) ? 'Yes' : 'No';
                        })
                        ->addColumn('is_featured', function ($companies) {
                            return ((bool) $companies->is_featured) ? 'Yes' : 'No';
                        })
                        ->addColumn('claim_status', function ($companies) {
                            if ($companies->created_by_admin && !$companies->is_claimed) {
                                return '<span class="badge badge-warning">Unclaimed</span>';
                            } elseif ($companies->created_by_admin && $companies->is_claimed) {
                                return '<span class="badge badge-success">Claimed</span>';
                            }
                            return '<span class="badge badge-secondary">N/A</span>';
                        })
                        ->addColumn('employer_trust_status', function ($companies) {
                            $status = $companies->getEmployerTrustStatus();
                            $badges = [
                                'verified'   => '<span class="badge" style="background:#28a745;color:#fff;"><i class="fas fa-check-circle me-1"></i>Verified</span>',
                                'reviewed'   => '<span class="badge" style="background:#ffc107;color:#000;"><i class="fas fa-clock me-1"></i>Reviewed</span>',
                                'unverified' => '<span class="badge" style="background:#dc3545;color:#fff;"><i class="fas fa-times-circle me-1"></i>Unverified</span>',
                            ];
                            return $badges[$status] ?? $badges['unverified'];
                        })
                        ->addColumn('checkbox', function ($companies) {
                            return '<input class="checkboxes" type="checkbox" id="check_'.$companies->id.'" name="company_ids[]" autocomplete="off" value="'.$companies->id.'">';
                        })
                        ->addColumn('action', function ($companies) {
                            $activeTxt = 'Make Active';
                            $activeHref = 'makeActive(' . $companies->id . ');';
                            $activeIcon = 'checkbox-blank-line';
                            if ((int) $companies->is_active == 1) {
                                $activeTxt = 'Make InActive';
                                $activeHref = 'makeNotActive(' . $companies->id . ');';
                                $activeIcon = 'checkbox-line';
                            }
                            $featuredTxt = 'Make Featured';
                            $featuredHref = 'makeFeatured(' . $companies->id . ');';
                            $featuredIcon = 'checkbox-blank-line';
                            if ((int) $companies->is_featured == 1) {
                                $featuredTxt = 'Make Not Featured';
                                $featuredHref = 'makeNotFeatured(' . $companies->id . ');';
                                $featuredIcon = 'checkbox-line';
                            }
                            return '
				<div class="btn-group">
					<button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action
						<i class="ri ri-arrow-down-s-line"></i>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a class="dropdown-item" href="' . route('list.jobs', ['company_id' => $companies->id]) . '" target="_blank"><i class="ri ri-list-unordered me-1"></i>List Jobs</a>
						</li>
						<li>
							<a class="dropdown-item" href="' . route('edit.company', ['id' => $companies->id]) . '"><i class="ri ri-pencil-line me-1"></i>Edit</a>
						</li>						
						<li>
							<a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteCompany(' . $companies->id . ');"><i class="ri ri-delete-bin-line me-1"></i>Delete</a>
						</li>
                        <li>
                            <a class="dropdown-item" href="' . route('admin.public.company', ['id' => $companies->id]) . '"><i class="ri ri-eye-line me-1"></i>View Company Details</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="' . route('admin.company.unlocked.candidates', ['id' => $companies->id]) . '"><i class="ri ri-team-line me-1"></i>View Unlocked Candidates</a>
                        </li>
<li><a class="dropdown-item" href="javascript:void(0);" onClick="' . $activeHref . '" id="onclickActive' . $companies->id . '"><i class="ri ri-' . $activeIcon . ' me-1"></i>' . $activeTxt . '</a></li>
<li><a class="dropdown-item" href="javascript:void(0);" onClick="' . $featuredHref . '" id="onclickFeatured' . $companies->id . '"><i class="ri ri-' . $featuredIcon . ' me-1"></i>' . $featuredTxt . '</a></li>
<li><hr class="dropdown-divider"></li>
<li><a class="dropdown-item text-primary" href="javascript:void(0);" onclick="openTrustStatusModal(' . $companies->id . ', &apos;' . $companies->getEmployerTrustStatus() . '&apos;)"><i class="fas fa-shield-alt me-1"></i>Set Verification Status</a></li>
					</ul>
				</div>';
                        })
                        ->rawColumns(['action', 'is_active', 'is_featured', 'claim_status', 'employer_trust_status', 'checkbox'])
                        ->setRowId(function($companies) {
                            return 'companyDtRow' . $companies->id;
                        })
                        ->make(true);
    }

    /**
     * Assign employer trust/verification status (admin-only).
     */
    public function setEmployerTrustStatus(Request $request, $id)
    {
        $request->validate([
            'trust_status' => 'required|in:unverified,reviewed,verified',
        ]);

        try {
            $company = Company::findOrFail($id);
            $company->employer_trust_status = $request->input('trust_status');
            $company->save();

            return response()->json([
                'success' => true,
                'message' => 'Employer verification status updated to "' . ucfirst($request->input('trust_status')) . '".',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function makeActiveCompany(Request $request)
    {
        $id = $request->input('id');
        try {
            $company = Company::findOrFail($id);
            $company->is_active = 1;
            $company->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }
    public function makeNotActiveCompany(Request $request)
    {
        $id = $request->input('id');
        try {
            $company = Company::findOrFail($id);
            $company->is_active = 0;
            $company->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }
    public function makeFeaturedCompany(Request $request)
    {
        $id = $request->input('id');
        try {
            $company = Company::findOrFail($id);
            $company->is_featured = 1;
            $company->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }
    public function makeNotFeaturedCompany(Request $request)
    {
        $id = $request->input('id');
        try {
            $company = Company::findOrFail($id);
            $company->is_featured = 0;
            $company->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }
    public function listAppliedUsers(Request $request, $job_id)
    {
        $job_applications = JobApply::where('job_id', '=', $job_id)->get();
        $job = Job::findorFail($job_id);
        return view('admin.job.job_applications')
                        ->with('job_applications', $job_applications)
                        ->with('job', $job)
                        ->with('job_id', $job->id)
                        ->with('company_id', $job->company_id);
    }
    public function viewUnlockedCandidates($id)
    {
        $company = Company::findOrFail($id);
        $data = array();
        $data['company'] = $company;
        
        $unlocked_users = UnlockedUser::where('company_id', $id)->first();
        
        if (null !== ($unlocked_users) && !empty($unlocked_users->unlocked_users_ids)) {
            $user_ids = explode(',', $unlocked_users->unlocked_users_ids);
            $data['users'] = User::whereIn('id', $user_ids)->get();
        } else {
            $data['users'] = collect();
        }
        
        return view('admin.company.unlocked_candidates')->with($data);
    }

    /**
     * Display verification requests page
     */
    public function verificationRequests()
    {
        $pendingCompanyIds = VerificationDocument::query()
            ->select('company_id')
            ->groupBy('company_id')
            ->pluck('company_id');

        $pendingVerifications = Company::whereIn('id', $pendingCompanyIds)
            ->where(function ($query) {
                $query->where('verification_status', 'pending')
                    ->orWhere(function ($fallback) {
                        $fallback->whereNull('verification_status')
                            ->where(function ($status) {
                                $status->where('verified', false)
                                    ->orWhereNull('verified');
                            });
                    });
            })
            ->with(['verificationDocuments' => function ($query) {
                $query->orderBy('uploaded_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $recentlyVerified = Company::where('verified', true)
            ->whereNotNull('verified_at')
            ->with(['verificationDocuments' => function ($query) {
                $query->orderBy('uploaded_at', 'desc');
            }])
            ->orderBy('verified_at', 'desc')
            ->limit(10)
            ->get();

        $recentlyRejected = Company::where('verification_status', 'rejected')
            ->with(['verificationDocuments' => function ($query) {
                $query->orderBy('uploaded_at', 'desc');
            }])
            ->orderBy('verification_reviewed_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.company.verification_requests')
            ->with('pendingVerifications', $pendingVerifications)
            ->with('recentlyVerified', $recentlyVerified)
            ->with('recentlyRejected', $recentlyRejected);
    }

    /**
     * Approve company verification
     */
    public function approveVerification($id)
    {
        try {
            $company = Company::findOrFail($id);
            
            // Check if company has uploaded business registration (mandatory)
            if (!$company->hasBusinessRegistration()) {
                $payload = [
                    'success' => false,
                    'message' => 'Company must upload business registration document before verification can be approved.'
                ];

                if (request()->expectsJson()) {
                    return response()->json($payload, 400);
                }

                return redirect()->back()->with('error', $payload['message']);
            }
            
            // Check if already verified
            if ($company->isVerified()) {
                $payload = [
                    'success' => false,
                    'message' => 'Company is already verified.'
                ];

                if (request()->expectsJson()) {
                    return response()->json($payload, 400);
                }

                return redirect()->back()->with('error', $payload['message']);
            }
            
            // Approve verification
            $company->is_active = 1;
            $company->verified = true;
            $company->verified_at = Carbon::now();
            $company->verification_status = 'approved';
            $company->employer_trust_status = 'verified';
            $company->verification_rejection_reason = null;
            $company->verification_reviewed_at = Carbon::now();
            $company->save();

            $this->sendCompanyVerificationStatusEmail($company, 'company-verification-approved');

            $payload = [
                'success' => true,
                'message' => 'Company verification approved successfully.'
            ];

            if (request()->expectsJson()) {
                return response()->json($payload);
            }

            return redirect()->back()->with('success', $payload['message']);
            
        } catch (\Exception $e) {
            $payload = [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];

            if (request()->expectsJson()) {
                return response()->json($payload, 500);
            }

            return redirect()->back()->with('error', $payload['message']);
        }
    }

    public function rejectVerification(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);
            $reason = trim((string) $request->input('reason'));

            if (!$company->verificationDocuments()->exists()) {
                $message = 'Company has no verification documents to reject.';

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }

                return redirect()->back()->with('error', $message);
            }

            if ($reason === '') {
                $message = 'Rejection reason is required.';

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->with('error', $message);
            }

            $company->is_active = 0;
            $company->verified = false;
            $company->verified_at = null;
            $company->verification_status = 'rejected';
            $company->employer_trust_status = 'unverified';
            $company->verification_rejection_reason = $reason;
            $company->verification_reviewed_at = Carbon::now();
            $company->save();

            $this->sendCompanyVerificationStatusEmail($company, 'company-verification-rejected');

            $payload = [
                'success' => true,
                'message' => 'Company verification rejected successfully.'
            ];

            if ($request->expectsJson()) {
                return response()->json($payload);
            }

            return redirect()->back()->with('success', $payload['message']);
        } catch (\Exception $e) {
            $payload = [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];

            if ($request->expectsJson()) {
                return response()->json($payload, 500);
            }

            return redirect()->back()->with('error', $payload['message']);
        }
    }

    private function sendCompanyVerificationStatusEmail(Company $company, string $templateSlug): void
    {
        try {
            EmailTemplateService::send(
                $templateSlug,
                $company->email,
                $company->name,
                [
                    'FULL_NAME' => $company->name,
                    'COMPANY_NAME' => $company->name,
                    'COMPANY_LINK' => route('company.detail', $company->slug),
                    'COMPANY_ADMIN_LINK' => route('edit.company', ['id' => $company->id]),
                    'VERIFICATION_PAGE_URL' => route('company.verification.upload'),
                    'REJECTION_REASON' => $company->verification_rejection_reason ?: 'No reason provided.',
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send company verification status email', [
                'company_id' => $company->id,
                'template' => $templateSlug,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display company claim requests
     */
    public function companyClaimRequests()
    {
        $pendingRequests = \App\CompanyClaimRequest::with(['company', 'user'])
            ->where('status', 'pending')
            ->orderBy('requested_at', 'desc')
            ->get();
            
        $recentReviewed = \App\CompanyClaimRequest::with(['company', 'user', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('reviewed_at', 'desc')
            ->limit(20)
            ->get();
            
        return view('admin.company.claim_requests')
            ->with('pendingRequests', $pendingRequests)
            ->with('recentReviewed', $recentReviewed);
    }

    /**
     * Approve a company claim request
     */
    public function approveClaimRequest(Request $request, $id)
    {
        try {
            $claimRequest = \App\CompanyClaimRequest::with('company')->findOrFail($id);
            
            if ($claimRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This claim request has already been reviewed.'
                ], 400);
            }
            
            $company = $claimRequest->company;
            
            // Update company to mark as claimed
            $company->is_claimed = 1;
            $company->claimed_by_user_id = $claimRequest->user_id;
            $company->claimed_at = now();
            
            // Set company email to claiming user's email so they can log in
            if (empty($company->email)) {
                $company->email = $claimRequest->user->email;
            }
            $company->save();
            
            // Update claim request
            $claimRequest->status = 'approved';
            $claimRequest->reviewed_at = now();
            $claimRequest->reviewed_by = Auth::guard('admin')->id();
            $claimRequest->admin_notes = $request->input('admin_notes');
            $claimRequest->save();
            
            // Send password setup link to the claiming user
            $token = Password::broker('companies')->createToken($company);
            $passwordSetupUrl = route('company.password.reset', ['token' => $token, 'email' => $company->email]);
            
            try {
                $claimRequest->user->notify(new ClaimRequestApproved($claimRequest, $passwordSetupUrl));
            } catch (\Exception $e) {
                \Log::warning('[ClaimApproval] Failed to send notification: ' . $e->getMessage());
            }
            
            flash('Company claim request has been approved successfully!')->success();
            return response()->json([
                'success' => true,
                'message' => 'Company claim request approved successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a company claim request
     */
    public function rejectClaimRequest(Request $request, $id)
    {
        try {
            $claimRequest = \App\CompanyClaimRequest::findOrFail($id);
            
            if ($claimRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This claim request has already been reviewed.'
                ], 400);
            }
            
            // Update claim request
            $claimRequest->status = 'rejected';
            $claimRequest->reviewed_at = now();
            $claimRequest->reviewed_by = Auth::guard('admin')->id();
            $claimRequest->admin_notes = $request->input('admin_notes', 'Claim request rejected.');
            $claimRequest->save();
            
            // Notify the user
            try {
                $claimRequest->user->notify(new ClaimRequestRejected($claimRequest));
            } catch (\Exception $e) {
                \Log::warning('[ClaimRejection] Failed to send notification: ' . $e->getMessage());
            }
            
            flash('Company claim request has been rejected.')->success();
            return response()->json([
                'success' => true,
                'message' => 'Company claim request rejected successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
