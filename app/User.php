<?php
namespace App;
use Auth;
use App\JobSkill;
use App\CompanyMessage;
use App\FavouriteCompany;
use App\FavouriteJob;
use App\JobApply;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\CountryStateCity;
use App\Traits\CommonUserFunctions;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserResetPassword;
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use CountryStateCity;
    use CommonUserFunctions;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'email', 'password','email_verified_at','verified',
        'email_verification_code', 'email_verification_code_expires_at',
        'email_verification_attempts', 'is_email_verified', 'is_active',
        'password_reset_code', 'password_reset_code_expires_at',
        'gdpr_consent_date', 'data_export_requested_at', 'account_deletion_requested_at',
        'referral_code', 'referred_by_user_id', 'referral_featured_days', 'featured_until',
        'incomplete_profile_reminder_sent_at', 'partial_data', 'years_of_experience'
    ];
    protected $dates = ['created_at', 'updated_at', 'date_of_birth', 'package_start_date', 'package_end_date', 'gdpr_consent_date', 'data_export_requested_at', 'account_deletion_requested_at', 'incomplete_profile_reminder_sent_at'];
    
    protected $casts = [
        'partial_data' => 'array',
        'custom_field_data' => 'array',
        'visible_in_employer_resume_search' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function profileSummary()
    {
        return $this->hasMany('App\ProfileSummary', 'user_id', 'id');
    }
    public function getProfileSummary($field = '')
    {
        if (null !== $this->profileSummary->first()) {
            $profileSummary = $this->profileSummary->first();
            if ($field != '') {
                return $profileSummary->$field;
            } else {
                return $profileSummary;
            }
        } else {
            return '';
        }
    }
    public function profileProjects()
    {
        return $this->hasMany('App\ProfileProject', 'user_id', 'id');
    }
    public function getProfileProjectsArray()
    {
        return $this->profileProjects->pluck('id')->toArray();
    }
    public function getDefaultCv()
    {
        $cv = ProfileCv::where('user_id', '=', $this->id)->where('is_default', '=', 1)->first();
        if (null === $cv)
            $cv = ProfileCv::where('user_id', '=', $this->id)->first();
        return $cv;
    }
    public function profileCvs()
    {
        return $this->hasMany('App\ProfileCv', 'user_id', 'id');
    }
    public function getProfileCvsArray()
    {
        return $this->profileCvs->pluck('id')->toArray();
    }
    public function countProfileCvs()
    {
        return $this->profileCvs->count();
    }
    public function profileExperience()
    {
        return $this->hasMany('App\ProfileExperience', 'user_id', 'id');
    }
    public function profileEducation()
    {
        return $this->hasMany('App\ProfileEducation', 'user_id', 'id');
    }
    public function profileSkills()
    {
        return $this->hasMany('App\ProfileSkill', 'user_id', 'id');
    }
    public function getProfileSkills()
    {
        return $this->profileSkills;
    }
    public function getProfileSkillsStr()
    {
        $profileSkills = $this->profileSkills;
        $str = '';
        if ($profileSkills !== null) {
            foreach ($profileSkills as $profileSkill) {
                $jobSkill = JobSkill::where('job_skill_id', '=', $profileSkill->job_skill_id)->lang()->first();
                if ($jobSkill) {
                    $str .= ' ' . $jobSkill->job_skill;
                }
            }
        }
        return $str;
    }
    public function profileLanguages()
    {
        return $this->hasMany('App\ProfileLanguage', 'user_id', 'id');
    }
    public function getProfileLanguagesStr()
    {
        $profileLanguages = $this->profileLanguages;
        $str = '';
        if ($profileLanguages !== null) {
            foreach ($profileLanguages as $profileLanguage) {
                $language = \App\Language::find($profileLanguage->language_id);
                if ($language) {
                    $str .= ' ' . $language->lang;
                }
            }
        }
        return $str;
    }
    public function getProfileEducationStr()
    {
        $profileEducation = $this->profileEducation;
        $str = '';
        if ($profileEducation !== null) {
            foreach ($profileEducation as $education) {
                // Add degree level (e.g., "Bachelor", "Master", "Doctorate")
                $degreeLevel = $education->getDegreeLevel('degree_level');
                if ($degreeLevel) {
                    $str .= ' ' . $degreeLevel;
                }
                // Add degree type (e.g., "MBA", "BBA", "BSc")
                $degreeType = $education->getDegreeType('degree_type');
                if ($degreeType) {
                    $str .= ' ' . $degreeType;
                }
                // Add major subjects
                $str .= ' ' . $education->getProfileEducationMajorSubjectsStr();
                $str .= ' ' . $education->institution;
                $str .= ' ' . $education->school_location;
                $str .= ' ' . strip_tags((string) $education->description);
            }
        }
        return $str;
    }
    public function getProfileSummaryStr()
    {
        $profileSummary = $this->profileSummary->first();
        if ($profileSummary) {
            return ' ' . $profileSummary->summary;
        }
        return '';
    }
    public function getProfileExperienceStr()
    {
        $profileExperience = $this->profileExperience;
        $str = '';
        if ($profileExperience !== null) {
            foreach ($profileExperience as $experience) {
                $str .= ' ' . $experience->title;
                $str .= ' ' . $experience->company;
                $str .= ' ' . $experience->employer_address;
                $str .= ' ' . $experience->description;
            }
        }
        return $str;
    }
    public function favouriteJobs()
    {
        return $this->hasMany('App\FavouriteJob', 'user_id', 'id');
    }
    public function getFavouriteJobSlugsArray()
    {
        return $this->favouriteJobs->pluck('job_slug')->toArray();
    }
    public function isFavouriteJob($job_slug)
    {
        $return = false;
        if (Auth::check()) {
            $count = FavouriteJob::where('user_id', Auth::user()->id)->where('job_slug', 'like', $job_slug)->count();
            if ($count > 0)
                $return = true;
        }
        return $return;
    }
    public function favouriteCompanies()
    {
        return $this->hasMany('App\FavouriteCompany', 'user_id', 'id');
    }
    public function getFavouriteCompanies()
    {
        return $this->favouriteCompanies->pluck('company_slug')->toArray();
    }
    /*     * ****************************** */
    public function isAppliedOnJob($job_id)
    {
        $return = false;
        if (Auth::check()) {
            $count = JobApply::where('user_id', Auth::user()->id)->where('job_id', '=', $job_id)->count();
            if ($count > 0)
                $return = true;
        }
        return $return;
    }
    public function appliedJobs()
    {
        return $this->hasMany('App\JobApply', 'user_id', 'id');
    }
    public function getAppliedJobIdsArray()
    {
        return $this->appliedJobs->pluck('job_id')->toArray();
    }

    public function getAppliedJob()
    {
        return $this->appliedJobs()->orderBy('id')->get();
    }
    /*     * ***************************** */
    public function isFavouriteCompany($company_slug)
    {
        $return = false;
        if (Auth::check()) {
            $count = FavouriteCompany::where('user_id', Auth::user()->id)->where('company_slug', 'like', $company_slug)->count();
            if ($count > 0)
                $return = true;
        }
        return $return;
    }
    public function printUserImage($width = 0, $height = 0)
    {
        $image = (string)$this->image;
        $image = (!empty($image)) ? $image : 'no-no-image.gif';
        return \ImgUploader::print_image("user_images/$image", $width, $height, '/admin_assets/no-image.png', $this->getName());
    }
	public function printUserCoverImage($width = 0, $height = 0)
    {
        $cover_image = (string) $this->cover_image;
        $cover_image = (!empty($cover_image)) ? $cover_image : 'no-no-image.gif';
        return \ImgUploader::print_image("user_images/$cover_image", $width, $height, '/admin_assets/no-cover.jpg', $this->name);
    }
    public function getName()
    {
        $html = '';
        if (!empty($this->first_name))
            $html .= $this->first_name;
        // if (!empty($this->middle_name))
        //     $html .= ' ' . $this->middle_name;
        if (!empty($this->last_name))
            $html .= ' ' . $this->last_name;
        return $html;
    }
    public function getAge()
    {
        if (!empty((string)$this->date_of_birth) && null !== $this->date_of_birth) {
            // If date_of_birth is a string, convert it to Carbon
            $dob = is_string($this->date_of_birth) ? Carbon::parse($this->date_of_birth) : $this->date_of_birth;
            // Calculate and return the age
            return $dob->age;
        }
        return null; // or any default value if date_of_birth is not set
    }
    public function careerLevel()
    {
        return $this->belongsTo('App\CareerLevel', 'career_level_id', 'career_level_id');
    }
    public function getCareerLevel($field = '')
    {
        $careerLevel = $this->careerLevel()->lang()->first();
        if (null === $careerLevel) {
            $careerLevel = $this->careerLevel()->first();
        }
        if (null !== $careerLevel) {
            if (!empty($field))
                return $careerLevel->$field;
            else
                return $careerLevel;
        }
    }
    public function jobExperience()
    {
        return $this->belongsTo('App\JobExperience', 'job_experience_id', 'job_experience_id');
    }
    public function getJobExperience($field = '')
    {
        $jobExperience = $this->jobExperience()->lang()->first();
        if (null === $jobExperience) {
            $jobExperience = $this->jobExperience()->first();
        }
        if (null !== $jobExperience) {
            if (!empty($field))
                return $jobExperience->$field;
            else
                return $jobExperience;
        }
    }
    public function gender()
    {
        return $this->belongsTo('App\Gender', 'gender_id', 'gender_id');
    }
    public function getGender($field = '')
    {
        $gender = $this->gender()->lang()->first();
        if (null === $gender) {
            $gender = $this->gender()->first();
        }
        if (null !== $gender) {
            if (!empty($field))
                return $gender->$field;
            else
                return $gender;
        }
    }
    public function maritalStatus()
    {
        return $this->belongsTo('App\MaritalStatus', 'marital_status_id', 'marital_status_id');
    }
    public function getMaritalStatus($field = '')
    {
        $maritalStatus = $this->maritalStatus()->lang()->first();
        if (null === $maritalStatus) {
            $maritalStatus = $this->maritalStatus()->first();
        }
        if (null !== $maritalStatus) {
            if (!empty($field))
                return $maritalStatus->$field;
            else
                return $maritalStatus;
        }
    }
    public function followingCompanies()
    {
        return $this->hasMany('App\FavouriteCompany', 'user_id', 'id');
    }
    public function getFollowingCompaniesSlugArray()
    {
        return $this->followingCompanies()->pluck('company_slug')->toArray();
    }
    public function countFollowings()
    {
        return FavouriteCompany::where('user_id', '=', $this->id)->count();
    }
    public function countApplicantMessages()
    {
        return ApplicantMessage::where('user_id', '=', $this->id)->where('is_read', '=', 0)->count();
    }
    public function package()
    {
        return $this->belongsTo('App\Package', 'package_id', 'id');
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
            if (!empty($field))
                return $industry->$field;
            else
                return $industry;
        }
    }
    public function functionalArea()
    {
        return $this->belongsTo('App\FunctionalArea', 'functional_area_id', 'functional_area_id');
    }
    
    public function jobCategory()
    {
        return $this->belongsTo('App\JobCategory', 'job_category_id', 'job_category_id');
    }

    public function certifications()
    {
        return $this->belongsToMany('App\Certification', 'user_certifications', 'user_id', 'certification_id');
    }
    
    public function getFunctionalArea($field = '')
    {
        $functionalArea = $this->functionalArea()->lang()->first();
        if (null === $functionalArea) {
            $functionalArea = $this->functionalArea()->first();
        }
        if (null !== $functionalArea) {
            if (!empty($field))
                return $functionalArea->$field;
            else
                return $functionalArea;
        }
    }
    
    public function getJobCategory($field = '')
    {
        $jobCategory = $this->jobCategory()->lang()->first();
        if (null === $jobCategory) {
            $jobCategory = $this->jobCategory()->first();
        }
        if (null !== $jobCategory) {
            if (!empty($field))
                return $jobCategory->$field;
            else
                return $jobCategory;
        }
    }
    public function countUserMessages()
    {
        return CompanyMessage::where('seeker_id', '=', $this->id)->where('status', '=', 'unviewed')->where('type', '=', 'message')->count();
    }
    public function countMessages($id)
    {
        return CompanyMessage::where('seeker_id', '=', $this->id)->where('company_id', '=', $id)->where('status', '=', 'unviewed')->where('type', '=', 'message')->count();
    }

    /**
     * Get referrals made by this user
     */
    public function referralsMade()
    {
        return $this->hasMany('App\UserReferral', 'referrer_user_id');
    }

    /**
     * Get the user that referred this user
     */
    public function referredByUser()
    {
        return $this->belongsTo('App\User', 'referred_by_user_id');
    }

    /**
     * Generate unique referral code for this user
     */
    public function generateReferralCode()
    {
        if (empty($this->referral_code)) {
            $code = strtoupper(substr(md5($this->id . $this->email . time()), 0, 8));
            $this->referral_code = 'USR' . $code;
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
        return url('/register?ref=' . $code);
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
     * Get total featured days available
     */
    public function getTotalFeaturedDays()
    {
        return $this->referral_featured_days ?? 0;
    }

    /**
     * Check if profile is currently featured
     */
    public function isProfileFeatured()
    {
        if ($this->featured_until && \Carbon\Carbon::parse($this->featured_until)->isFuture()) {
            return true;
        }
        return (bool) $this->is_featured;
    }

    /**
     * Get remaining featured days
     */
    public function getRemainingFeaturedDays()
    {
        if ($this->featured_until && \Carbon\Carbon::parse($this->featured_until)->isFuture()) {
            return \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($this->featured_until));
        }
        return 0;
    }

    /**
     * Activate featured profile using referral days
     */
    public function activateFeaturedProfile($days = null)
    {
        if ($days === null) {
            $days = $this->referral_featured_days;
        }
        
        if ($days > 0 && $days <= $this->referral_featured_days) {
            $this->referral_featured_days -= $days;
            
            if ($this->featured_until && \Carbon\Carbon::parse($this->featured_until)->isFuture()) {
                // Extend existing featured period
                $this->featured_until = \Carbon\Carbon::parse($this->featured_until)->addDays($days);
            } else {
                // Start new featured period
                $this->featured_until = \Carbon\Carbon::now()->addDays($days);
            }
            
            $this->is_featured = 1;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Get partial resume data for employer preview (work history + education).
     * Auto-generates from profile if partial_data is null.
     */
    public function getPartialData()
    {
        if ($this->partial_data) {
            return $this->partial_data;
        }

        $history = $this->profileExperience()->get()->map(function ($exp) {
            return [
                'title' => $exp->title ?? '',
                'company' => $exp->company ?? '',
                'start_date' => $exp->date_start ?? '',
                'end_date' => $exp->date_end ?? '',
                'description' => $exp->description ?? '',
            ];
        })->toArray();

        $education = $this->profileEducation()->get()->map(function ($edu) {
            return [
                'degree' => $edu->degree_level_id ?? '',
                'institution' => $edu->institute ?? '',
                'start_date' => $edu->date_start ?? '',
                'end_date' => $edu->date_end ?? '',
            ];
        })->toArray();

        return [
            'work_history' => $history,
            'education' => $education,
        ];
    }

    /**
     * Check if this user's full resume is unlocked by a company.
     */
    public function isUnlockedBy($companyId): bool
    {
        return \App\Models\ResumeUnlock::isUnlockedBy($this->id, $companyId);
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
        $verificationLink = route('email-verification.check', $this->verification_token) . '?email=' . urlencode($this->email);
        
        // Send using our custom Mailable that uses EmailTemplateService
        \Mail::send(new \App\Mail\WebEmailVerificationMailable($this, $verificationLink));
    }

    /**
     * Send the password reset notification.
     * Override Laravel's default to use our admin email template system
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPassword($token));
    }
}
