<?php



namespace App;



use Auth;

use App;

use Carbon\Carbon;

use App\PaymentHistory;

use App\Package;

use App\Traits\Active;

use App\Traits\Featured;

use App\Traits\JobTrait;

use App\Traits\CountryStateCity;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;

use App\Notifications\CompanyResetPassword;

use Illuminate\Foundation\Auth\User as Authenticatable;



class Company extends Authenticatable

{



    use Active;

    use Featured;

    use Notifiable;

    use JobTrait;

    use CountryStateCity;



    protected $table = 'companies';

    public $timestamps = true;

    protected $guarded = ['id'];

    //protected $dateFormat = 'U';

    protected $dates = ['created_at', 'updated_at', 'package_start_date', 'package_end_date'];

    protected $fillable = [
        'name', 'email', 'password', 'slug', 'email_verified_at', 'verified',
        'cvs_package_id', 'cvs_package_start_date', 'cvs_package_end_date', 
        'cvs_quota', 'availed_cvs_quota', 'referral_code', 'referred_by_company_id', 'referral_bonus_jobs',
        'stripe_customer_id', 'stripe_subscription_id', 'stripe_subscription_status', 'stripe_subscription_ends_at',
        'employer_trust_status',
    ];
    
    

    protected $hidden = [

        'password', 'remember_token',

    ];

    protected $casts = [
        'custom_field_data' => 'array',
        'verified' => 'boolean',
        'email_verified_at' => 'datetime',
        'verified_at' => 'datetime',
        'verification_reviewed_at' => 'datetime',
    ];



    public function sendPasswordResetNotification($token)

    {

        $this->notify(new CompanyResetPassword($token));

    }

    /**
     * Send the email verification notification.
     * Override Laravel's default to use our admin email template system
     */
    public function sendEmailVerificationNotification()
    {
        // Ensure verification token exists
        if (!$this->verification_token) {
            $this->verification_token = \Illuminate\Support\Str::random(40);
            $this->save();
        }
        
        // Generate verification link
        $verificationLink = route('company.email-verification.check', $this->verification_token) . '?email=' . urlencode($this->email);
        
        // Send using our custom Mailable that uses EmailTemplateService
        \Mail::send(new \App\Mail\CompanyEmailVerificationMailable($this, $verificationLink));
    }



    public function printCompanyImage($width = 0, $height = 0)

    {

        $logo = (string)$this->logo;

        $logo = (null!==($logo) && !empty($logo)) ? $logo : 'no-image.png';
        return \ImgUploader::print_image("company_logos/$logo", $width, $height, '/admin_assets/no-image.png', $this->name);

    }
    
      public function printUserImage($width = 0, $height = 0)

    {



        $logo = (string)$this->logo;

        $logo = (null!==($logo) && !empty($logo)) ? $logo : 'no-image.png';
        return \ImgUploader::print_image("company_logos/$logo", $width, $height, '/admin_assets/no-image.png', $this->name);

    }



    // =========================================================
    // Employer Trust Status helpers
    // =========================================================

    /**
     * The 3-tier trust status: 'unverified' | 'reviewed' | 'verified'.
     */
    public function getEmployerTrustStatus(): string
    {
        $status = $this->employer_trust_status;
        
        // Always return 'unverified' as default if employer_trust_status is not explicitly set
        if (in_array($status, ['unverified', 'reviewed', 'verified'], true)) {
            return $status;
        }

        // Default to unverified for any NULL or invalid status
        return 'unverified';
    }

    public function isEmployerVerified(): bool
    {
        return $this->getEmployerTrustStatus() === 'verified';
    }

    public function isEmployerReviewed(): bool
    {
        return in_array($this->getEmployerTrustStatus(), ['reviewed', 'verified'], true);
    }

    /**
     * Maximum number of active jobs allowed by trust tier.
     * Returns null for unlimited (verified with active package).
     */
    public function getMaxJobPostings(): ?int
    {
        $status = $this->getEmployerTrustStatus();
        
        // Check if employer is private/individual
        if ($this->isPrivateIndividual()) {
            return 3; // Private/individual always limited to 3
        }
        
        switch ($status) {
            case 'verified': 
                // Verified employers: unlimited when no package, package-based when active
                return null;
            case 'reviewed': 
                return 3;
            default:         
                return 2; // unverified
        }
    }
    
    /**
     * Check if employer is private/individual type
     */
    public function isPrivateIndividual(): bool
    {
        // Check if ownership_type_id corresponds to private/individual
        // Assuming ownership_type_id for private/individual is stored
        $privateOwnershipTypes = ['Private/Individual', 'Individual', 'Private'];
        
        if ($this->ownershipType) {
            $ownershipTypeName = $this->getOwnershipType('ownership_type');
            return in_array($ownershipTypeName, $privateOwnershipTypes, true);
        }
        
        return false;
    }

    /**
     * Count of currently active (non-expired) jobs posted by this company.
     */
    public function countActiveJobs(): int
    {
        return \App\Job::where('company_id', $this->id)
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>', now());
            })
            ->count();
    }

    /**
     * Whether the employer can access the resume/CV database for searching/browsing.
     * Only verified employers with active CV package can search all job seekers.
     */
    public function canAccessResumeDatabase(): bool
    {
        // Private/individual employers cannot access resume database
        if ($this->isPrivateIndividual()) {
            return false;
        }
        
        // Must be verified
        if (!$this->isEmployerVerified()) {
            return false;
        }
        
        // Must have active CV search package
        return $this->hasActiveCvSearchPackage();
    }
    
    /**
     * Whether employer can view applicant profiles (those who applied to their jobs).
     * ALL employers can view their own applicants regardless of verification status.
     */
    public function canViewApplicantProfiles(): bool
    {
        // All employers can view applicants who applied to their jobs
        return true;
    }
    
    /**
     * Check if employer can post more jobs based on their status and limits
     */
    public function canPostJob(): bool
    {
        $maxJobs = $this->getMaxJobPostings();
        
        // Verified non-private employers with active package use package quota
        if ($this->isEmployerVerified() && !$this->isPrivateIndividual()) {
            // Check if they have an active job package
            if ($this->package_id && $this->package_end_date && \Carbon\Carbon::parse($this->package_end_date)->isFuture()) {
                return $this->getRemainingJobsQuota() > 0;
            }
            // No active package = unlimited for verified
            return true;
        }
        
        // For unverified, reviewed, and private/individual: check against max limit
        if ($maxJobs === null) {
            return true; // unlimited
        }
        
        $activeJobs = $this->countActiveJobs();
        return $activeJobs < $maxJobs;
    }
    
    /**
     * Get remaining job slots for current status
     */
    public function getRemainingJobSlots(): ?int
    {
        $maxJobs = $this->getMaxJobPostings();
        
        if ($maxJobs === null) {
            return null; // unlimited
        }
        
        $activeJobs = $this->countActiveJobs();
        return max(0, $maxJobs - $activeJobs);
    }
    
    /**
     * Get verification status badge HTML
     */
    public function getVerificationBadgeHtml(): string
    {
        $status = $this->getEmployerTrustStatus();
        
        $badges = [
            'unverified' => '<span class="badge badge-danger">🔴 Unverified</span>',
            'reviewed' => '<span class="badge badge-warning">🟡 Reviewed</span>',
            'verified' => '<span class="badge badge-success">🟢 Verified</span>',
        ];
        
        return $badges[$status] ?? $badges['unverified'];
    }
    
    /**
     * Get verification status badge class
     */
    public function getVerificationBadgeClass(): string
    {
        $status = $this->getEmployerTrustStatus();
        
        $classes = [
            'unverified' => 'badge-danger',
            'reviewed' => 'badge-warning',
            'verified' => 'badge-success',
        ];
        
        return $classes[$status] ?? 'badge-danger';
    }
    
    /**
     * Get verification status text
     */
    public function getVerificationStatusText(): string
    {
        $status = $this->getEmployerTrustStatus();
        
        $texts = [
            'unverified' => 'Unverified',
            'reviewed' => 'Reviewed',
            'verified' => 'Verified',
        ];
        
        return $texts[$status] ?? 'Unverified';
    }

    // =========================================================

    /**
     * Remaining job post credits (for canPostJob gate).
     */
    public function getRemainingJobsQuota()
    {
        if ($this->package_end_date && \Carbon\Carbon::parse($this->package_end_date)->isPast()) {
            return 0;
        }
        $quota = (int) $this->jobs_quota;
        $availed = (int) $this->availed_jobs_quota;
        return max(0, $quota - $availed);
    }

    /**
     * Remaining CV unlock credits while the CV search package is active.
     */
    public function getRemainingCvsQuota(): int
    {
        if (! $this->hasActiveCvSearchPackage()) {
            return 0;
        }
        $quota = (int) ($this->cvs_quota ?? 0);
        $availed = (int) ($this->availed_cvs_quota ?? 0);
        return max(0, $quota - $availed);
    }

    /**
     * Whether the company's CV search package is still within its end date (end of that calendar day).
     */
    public function hasActiveCvSearchPackage(): bool
    {
        if (empty($this->cvs_package_id)) {
            return false;
        }
        if (empty($this->cvs_package_end_date)) {
            return false;
        }

        return ! Carbon::parse($this->cvs_package_end_date)->endOfDay()->isPast();
    }

    /**
     * Start of the most recent free (or $0) employer job package activation from payment history, or current row fallback.
     */
    public function getLastFreeEmployerJobPackageStartDate(): ?Carbon
    {
        $last = PaymentHistory::where('company_id', $this->id)
            ->where('package_type', 'job')
            ->where('payment_status', 'completed')
            ->where(function ($q) {
                $q->whereRaw('LOWER(TRIM(payment_method)) = ?', ['free package'])
                    ->orWhere('package_price', '<=', 0);
            })
            ->orderByDesc('id')
            ->first();

        if ($last && $last->package_start_date) {
            return Carbon::parse($last->package_start_date);
        }

        if (empty($this->package_start_date) || ! $this->package_id) {
            return null;
        }

        $pkg = Package::find($this->package_id);
        if (! $pkg || $pkg->package_for !== 'employer' || (float) $pkg->package_price > 0) {
            return null;
        }

        return Carbon::parse($this->package_start_date);
    }

    /**
     * Free employer job package: at most one activation every :periodDays (30-day package; includes Stripe $0 checkouts).
     */
    public function canActivateFreeEmployerJobPackage(int $periodDays = 30): bool
    {
        $start = $this->getLastFreeEmployerJobPackageStartDate();
        if ($start === null) {
            return true;
        }

        return Carbon::now()->greaterThanOrEqualTo($start->copy()->addDays($periodDays));
    }

    public function getFreeEmployerJobPackageNextAvailableAt(int $periodDays = 30): ?Carbon
    {
        if ($this->canActivateFreeEmployerJobPackage($periodDays)) {
            return null;
        }

        $start = $this->getLastFreeEmployerJobPackageStartDate();

        return $start ? $start->copy()->addDays($periodDays) : null;
    }

    /**
     * Job posting quota is used up but the 30-day window from the last free/$0 activation is still open — must buy paid or wait.
     */
    public function isOnExhaustedFreeJobPostingPeriod(int $periodDays = 30): bool
    {
        if ($this->getRemainingJobsQuota() > 0) {
            return false;
        }

        $start = $this->getLastFreeEmployerJobPackageStartDate();
        if ($start === null || Carbon::now()->greaterThanOrEqualTo($start->copy()->addDays($periodDays))) {
            return false;
        }

        return ! empty($this->package_id)
            && $this->package_end_date
            && Carbon::parse($this->package_end_date)->startOfDay()->gte(Carbon::now()->startOfDay());
    }

    public function jobs()

    {

        return $this->hasMany('App\Job', 'company_id', 'id');

    }



    public function openJobs()

    {

        return Job::where('company_id', '=', $this->id)->notExpire();

    }



    public function getOpenJobs()

    {

        return $this->openJobs()->get();

    }



    public function countOpenJobs()

    {

        return $this->openJobs()->count();

    }



    public function industry()

    {

        return $this->belongsTo('App\Industry', 'industry_id', 'industry_id');

    }



    public function getIndustry($field = '')

    {

        $industry = $this->industry()->lang()->first();

        if (null === $industry) {

            $industry = $this->industry()->first();

        }

        if (null !== $industry) {

            if (!empty($field)) {

                return $industry->$field;

            } else {

                return $industry;

            }

        }

    }



    public function ownershipType()

    {

        return $this->belongsTo('App\OwnershipType', 'ownership_type_id', 'id');

    }



    public function getOwnershipType($field = '')

    {

        $ownershipType = $this->ownershipType()->lang()->first();

        if (null === $ownershipType) {

            $ownershipType = $this->ownershipType()->first();

        }

        if (null !== $ownershipType) {

            if (!empty($field)) {

                return $ownershipType->$field;

            } else {

                return $ownershipType;

            }

        }

    }



    public function countFollowers()

    {

        return FavouriteCompany::where('company_slug', 'like', $this->slug)->count();

    }



    public function getFollowerIdsArray()

    {

        return FavouriteCompany::where('company_slug', 'like', $this->slug)->pluck('user_id')->toArray();

    }



    public function countCompanyMessages()

    {

        return CompanyMessage::where('company_id', '=', $this->id)->where('status', '=', 'unviewed')->where('type', '=', 'reply')->count();

    }

    /**
     * Total job applications across all of this company's jobs.
     */
    public function countJobApplications()

    {

        return \App\JobApply::whereHas('job', function ($q) {

            $q->where('company_id', $this->id);

        })->count();

    }

    public function countMessages($id)

    {

        return CompanyMessage::where('company_id', '=', $this->id)->where('seeker_id', '=', $id)->where('type', 'reply')->where('status', '=', 'unviewed')->count();

    }



    public function getSocialNetworkHtml()

    {

        $html = '';

        if (!empty($this->facebook))

            $html .= '<a href="' . $this->facebook . '" target="_blank"><i class="fab fa-facebook" aria-hidden="true"></i></a>';



        if (!empty($this->twitter))

            $html .= '<a href="' . $this->twitter . '" target="_blank"><i class="fab fa-twitter" aria-hidden="true"></i></a>';



        if (!empty($this->linkedin))

            $html .= '<a href="' . $this->linkedin . '" target="_blank"><i class="fab fa-linkedin" aria-hidden="true"></i></a>';



        // if (!empty($this->google_plus))

        //     $html .= '<a href="' . $this->google_plus . '" target="_blank"><i class="fab fa-google-plus" aria-hidden="true"></i></a>';



        if (!empty($this->pinterest))

            $html .= '<a href="' . $this->pinterest . '" target="_blank"><i class="fab fa-pinterest" aria-hidden="true"></i></a>';



        return $html;

    }



    public function isFavouriteApplicant($user_id, $job_id, $company_id)

    {

        $return = false;

        if (Auth::guard('company')->check()) {

            $count = FavouriteApplicant::where('user_id', $user_id)

                ->where('job_id', $job_id)

                ->where('company_id', $company_id)

                ->count();

            if ($count > 0)

                $return = true;

        }

        return $return;

    }


    public function isHiredApplicant($user_id, $job_id, $company_id)

    {

        $return = false;

        if (Auth::guard('company')->check()) {

            $count = FavouriteApplicant::where('user_id', $user_id)

                ->where('job_id', $job_id)

                ->where('company_id', $company_id)

                ->where('status', 'hired')

                ->count();

            if ($count > 0)

                $return = true;

        }

        return $return;

    }



    public function package()

    {

        return $this->hasOne('App\Package', 'id', 'package_id');

    }



    public function getPackage($field = '')

    {

        $package = $this->package()->first();

        if (null !== $package) {

            if (!empty($field)) {

                return $package->$field;

            } else {

                return $package;

            }

        }

    }


    public function cvsPackageRelation()
    {
        return $this->hasOne('App\Package', 'id', 'cvs_package_id');
    }

    public function cvs_getPackage($field = '')
    {
        $package = $this->cvsPackageRelation()->first();

        if ($package !== null) {
            return !empty($field) ? $package->$field : $package;
        }

        return null; // Return null if no package found
    }

    /**
     * Get referrals made by this company
     */
    public function referralsMade()
    {
        return $this->hasMany('App\Referral', 'referrer_company_id');
    }

    /**
     * Get the company that referred this company
     */
    public function referredBy()
    {
        return $this->belongsTo('App\Company', 'referred_by_company_id');
    }

    /**
     * Generate unique referral code for this company
     */
    public function generateReferralCode()
    {
        if (empty($this->referral_code)) {
            $code = strtoupper(substr(md5($this->id . $this->email . time()), 0, 8));
            $this->referral_code = 'REF' . $code;
            $this->save();
        }
        return $this->referral_code;
    }

    /**
     * Get referral link
     */
    public function getReferralLink()
    {
        $code = $this->generateReferralCode();
        return url('/company-register?ref=' . $code);
    }

    /**
     * Count successful referrals
     */
    public function getSuccessfulReferralsCount()
    {
        return $this->referralsMade()->whereIn('status', ['registered', 'rewarded'])->count();
    }

    /**
     * Count pending referrals (not yet registered)
     */
    public function getPendingReferralsCount()
    {
        return $this->referralsMade()->where('status', 'pending')->count();
    }

    /**
     * Get total bonus jobs available
     */
    public function getTotalBonusJobs()
    {
        return $this->referral_bonus_jobs ?? 0;
    }

    /**
     * Get verification documents for this company
     */
    public function verificationDocuments()
    {
        return $this->hasMany('App\VerificationDocument', 'company_id', 'id');
    }

    /**
     * Check if company is verified
     */
    public function isVerified(): bool
    {
        if ($this->verification_status === 'approved') {
            return true;
        }

        return (bool) $this->verified && $this->verified_at !== null;
    }

    /**
     * Check if company has uploaded business registration document
     */
    public function hasBusinessRegistration(): bool
    {
        return $this->verificationDocuments()
            ->where('document_type', \App\VerificationDocument::TYPE_BUSINESS_REGISTRATION)
            ->exists();
    }

    public function hasPendingVerification(): bool
    {
        if ($this->verification_status === 'pending') {
            return $this->hasBusinessRegistration() && ! $this->isVerified();
        }

        return $this->hasBusinessRegistration()
            && ! $this->isVerified()
            && $this->verification_status !== 'rejected';
    }

    public function isVerificationRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }


}
