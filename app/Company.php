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
        'stripe_customer_id', 'stripe_subscription_id', 'stripe_subscription_status', 'stripe_subscription_ends_at'
    ];
    
    

    protected $hidden = [

        'password', 'remember_token',

    ];

    protected $casts = [
        'custom_field_data' => 'array',
    ];



    public function sendPasswordResetNotification($token)

    {

        $this->notify(new CompanyResetPassword($token));

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
     * Start of the most recent free CV search package activation (payment history), if any.
     */
    public function getLastFreeCvSearchPackageStartDate(): ?Carbon
    {
        $last = PaymentHistory::where('company_id', $this->id)
            ->where('package_type', 'cv_search')
            ->whereRaw('LOWER(TRIM(payment_method)) = ?', ['free package'])
            ->orderByDesc('id')
            ->first();

        if ($last && $last->package_start_date) {
            return Carbon::parse($last->package_start_date);
        }

        if (empty($this->has_used_free_cv_package) || (int) $this->has_used_free_cv_package !== 1 || empty($this->cvs_package_start_date) || ! $this->cvs_package_id) {
            return null;
        }

        $pkg = Package::find($this->cvs_package_id);
        if (! $pkg || $pkg->package_for !== 'cv_search' || (float) $pkg->package_price > 0) {
            return null;
        }

        return Carbon::parse($this->cvs_package_start_date);
    }

    /**
     * When the current free-CV period ends (last free activation + N days), or null if never activated free.
     */
    public function getFreeCvPackagePeriodEndsAt(int $periodDays = 30): ?Carbon
    {
        $start = $this->getLastFreeCvSearchPackageStartDate();

        return $start ? $start->copy()->addDays($periodDays) : null;
    }

    /**
     * Earliest moment the free CV package can be activated again (after last free activation + period), or null if allowed now.
     */
    public function getFreeCvPackageNextAvailableAt(int $periodDays = 30): ?Carbon
    {
        if ($this->canActivateFreeCvSearchPackage($periodDays)) {
            return null;
        }

        return $this->getFreeCvPackagePeriodEndsAt($periodDays);
    }

    /**
     * @deprecated Use getFreeCvPackageNextAvailableAt(); kept for blade strings.
     */
    public function getFreeCvPackageCooldownEndsAt(int $periodDays = 30): ?Carbon
    {
        return $this->getFreeCvPackageNextAvailableAt($periodDays);
    }

    /**
     * Free CV package: at most one activation every :periodDays (matches 30-day package length in addCompanySearchPackage).
     */
    public function canActivateFreeCvSearchPackage(int $periodDays = 30): bool
    {
        $start = $this->getLastFreeCvSearchPackageStartDate();
        if ($start === null) {
            return true;
        }

        return Carbon::now()->greaterThanOrEqualTo($start->copy()->addDays($periodDays));
    }

    /**
     * Free tier: all CV unlock credits used but the 30-day free window (from last free activation) is still running — must wait or pay.
     */
    public function isOnExhaustedFreeCvSearchPeriod(int $periodDays = 30): bool
    {
        if ($this->getRemainingCvsQuota() > 0) {
            return false;
        }

        $last = PaymentHistory::where('company_id', $this->id)
            ->where('package_type', 'cv_search')
            ->orderByDesc('id')
            ->first();

        if (! $last || ! $last->package_start_date || strcasecmp(trim((string) $last->payment_method), 'Free Package') !== 0) {
            return false;
        }

        $start = Carbon::parse($last->package_start_date);
        if (Carbon::now()->greaterThanOrEqualTo($start->copy()->addDays($periodDays))) {
            return false;
        }

        return (int) $this->cvs_package_id > 0
            && $this->cvs_package_end_date
            && Carbon::parse($this->cvs_package_end_date)->startOfDay()->gte(Carbon::now()->startOfDay());
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

        return $this->belongsTo('App\Industry', 'industry_id', 'id');

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

}

