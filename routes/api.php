<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



/*

  |--------------------------------------------------------------------------

  | Api Routes

  |--------------------------------------------------------------------------

  |

  | Here is where you can register Api routes for your application. These

  | routes are loaded by the RouteServiceProvider within a group which

  | is assigned the "Api" middleware group. Enjoy building your Api!

  |

 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('login', 'Api\Auth\AuthController@login');
Route::post('register', 'Api\Auth\AuthController@register');
Route::post('verify-email-code', 'Api\Auth\AuthController@verifyEmailCode');
Route::post('resend-verification-code', 'Api\Auth\AuthController@resendVerificationCode');

// Password reset endpoints
Route::post('verify-reset-code', 'Api\Auth\AuthController@verifyResetCode');
Route::post('reset-password', 'Api\Auth\AuthController@resetPassword');



Route::post('company-register', 'Api\RegisterController@employerRegister');
Route::get('jobs', 'Api\JobController@jobsBySearch');
Route::get('job/{slug}', 'Api\JobController@jobDetail');

Route::post('company-login', 'Api\RegisterController@employerLogin');

Route::get('view-public-profile/{id}', 'Api\UserController@viewPublicProfile');

Route::get('job-seekers', 'Api\JobSeekerController@jobSeekersBySearch');

Route::get('subscribe-alert', 'Api\SubscriptionController@submitAlert');

// Contact and Report Abuse routes
Route::get('contact', 'Api\ContactController@index');
Route::post('contact', 'Api\ContactController@postContact');
Route::get('contact-thanks', 'Api\ContactController@thanks');

// Email to Friend routes
Route::get('email-to-friend/{job_slug}', 'Api\ContactController@emailToFriend');
Route::post('email-to-friend/{job_slug}', 'Api\ContactController@emailToFriendPost');
Route::get('email-to-friend-thanks', 'Api\ContactController@emailToFriendThanks');

// Report Abuse routes
Route::get('report-abuse/{job_slug}', 'Api\ContactController@reportAbuse');
Route::post('report-abuse/{job_slug}', 'Api\ContactController@reportAbusePost');
Route::get('report-abuse-thanks', 'Api\ContactController@reportAbuseThanks');

// Report Abuse Company routes
Route::get('report-abuse-company/{company_slug}', 'Api\ContactController@reportAbuseCompany');
Route::post('report-abuse-company/{company_slug}', 'Api\ContactController@reportAbuseCompanyPost');
Route::get('report-abuse-company-thanks', 'Api\ContactController@reportAbuseCompanyThanks');

Route::middleware('auth:company')->group(function () {

    Route::get('order-free-package/{id}', 'Api\OrderController@orderFreePackage');



Route::get('order-package/{id}', 'Api\OrderController@orderPackage');

Route::get('order-upgrade-package/{id}', 'Api\OrderController@orderUpgradePackage');

Route::get('paypal-payment-status/{id}', 'Api\OrderController@getPaymentStatus');

Route::get('paypal-upgrade-payment-status/{id}', 'Api\OrderController@getUpgradePaymentStatus');

Route::get('stripe-order-form/{id}/{new_or_upgrade}', 'Api\StripeOrderController@stripeOrderForm');

Route::post('stripe-order-package', 'Api\StripeOrderController@stripeOrderPackage');

Route::post('stripe-order-upgrade-package', 'Api\StripeOrderController@stripeOrderUpgradePackage');





Route::get('payu-order-package', 'Api\PayuController@orderPackage');





Route::get('payu-order-package-status/', 'Api\PayuController@orderPackageStatus');





Route::get('payu-order-cvsearch-package', 'Api\PayuController@orderCvSearchPackage');





Route::get('payu-order-package.cvsearch-status/', 'Api\PayuController@orderPackageCvSearchStatus');







    Route::get('post-job', 'Api\JobPublishController@createFrontJob');
    Route::post('store-front-job', 'Api\JobPublishController@storeFrontJob');
    Route::get('edit-front-job/{id}', 'Api\JobPublishController@editFrontJob');
    Route::put('update-front-job/{id}', 'Api\JobPublishController@updateFrontJob');
    Route::delete('delete-front-job', 'Api\JobPublishController@deleteJob');
    Route::get('list-rejected-users/{id}', 'Api\CompanyController@listRejectedUsers');
    Route::post('submit-message', 'Api\SeekerSendController@submit_message');


    Route::get('company-packages', 'Api\CompanyController@resume_search_packages');

    Route::get('unloced-seekers', 'Api\CompanyController@unlocked_users');
    Route::get('unlock/{user}', 'Api\CompanyController@unlock');

    Route::get('company-home', 'Api\CompanyController@index');



Route::get('company-profile', 'Api\CompanyController@companyProfile');
Route::put('update-company-profile', 'Api\CompanyController@updateCompanyProfile');



Route::post('contact-company-message-send', 'Api\CompanyController@sendContactForm');

Route::post('contact-applicant-message-send', 'Api\CompanyController@sendApplicantContactForm');

Route::get('list-applied-users/{job_id}', 'Api\CompanyController@listAppliedUsers');

Route::get('list-hired-users/{job_id}', 'Api\CompanyController@listHiredUsers');
Route::get('list-favourite-applied-users/{job_id}', 'Api\CompanyController@listFavouriteAppliedUsers');

Route::get('add-to-favourite-applicant/{application_id}/{user_id}/{job_id}/{company_id}', 'Api\CompanyController@addToFavouriteApplicant');

Route::get('remove-from-favourite-applicant/{application_id}/{user_id}/{job_id}/{company_id}', 'Api\CompanyController@removeFromFavouriteApplicant');

Route::get('hire-from-favourite-applicant/{application_id}/{user_id}/{job_id}/{company_id}', 'Api\CompanyController@hireFromFavouriteApplicant');





Route::get('removed-from-hired-applicant/{application_id}/{user_id}/{job_id}/{company_id}', 'Api\CompanyController@removehireFromFavouriteApplicant');

Route::get('applicant-profile/{application_id}', 'Api\CompanyController@applicantProfile');

Route::get('reject-applicant-profile/{application_id}', 'Api\CompanyController@rejectApplicantProfile');
Route::get('user-profile/{id}', 'Api\CompanyController@userProfile');

Route::get('company-followers', 'Api\CompanyController@companyFollowers');

/* Route::get('company-messages', 'Api\CompanyController@companyMessages')->name('company.messages'); */

Route::post('submit-message-seeker', 'CompanyMessagesController@submitnew_message_seeker');



Route::get('company-messages', 'CompanyMessagesController@all_messages');
Route::get('append-messages', 'CompanyMessagesController@append_messages');

Route::get('append-only-messages', 'CompanyMessagesController@appendonly_messages');

Route::post('company-submit-messages', 'CompanyMessagesController@submit_message');

Route::get('company-message-detail/{id}', 'Api\CompanyController@companyMessageDetail');






});



Route::get('companies', 'Api\CompanyController@company_listing');
Route::get('company/{slug}', 'Api\CompanyController@companyDetail');
Route::get('company-jobs', 'Api\CompanyController@companyJobs');
Route::post('forgot-password', 'Api\Auth\AuthController@forgotPassword');

// Home dashboard routes (no authentication required for search suggestions)
Route::get('search-suggestions', 'Api\HomeApiController@getSearchSuggestions');
Route::get('public-home-data', 'Api\HomeApiController@getPublicHomeData');

// Public job routes (no authentication required)
Route::get('jobs/categories', 'Api\JobController@job_categories');
Route::get('jobs/category-{id}/show', 'Api\JobController@jobs_by_category');

// Test route without authentication
Route::get('test-public-route', function() {
    return response()->json(['message' => 'Public test route is working']);
});

// Debug route to check job_skills data
Route::get('debug-job-skills', function() {
    return response()->json(['message' => 'Debug route is working']);
});

// Debug route to check states data
Route::get('debug-states', function() {
    try {
        $states = DB::table('states')
            ->select('state_id as id', 'state', 'country_id', 'lang', 'is_active')
            ->where('country_id', 1)
            ->where('is_active', 1)
            ->orderBy('state')
            ->limit(5)
            ->get();
        
        return response()->json([
            'success' => true,
            'count' => $states->count(),
            'data' => $states->toArray(),
            'all_states_count' => DB::table('states')->count(),
            'active_states_count' => DB::table('states')->where('is_active', 1)->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Debug route to test degree types directly
Route::get('debug-degree-types', function() {
    try {
        $degreeTypes = DB::table('degree_types')
            ->select('degree_type_id as id', 'degree_type as name')
            ->where('is_active', 1)
            ->where('is_default', 1)
            ->orderBy('degree_type')
            ->limit(5)
            ->get();
        
        return response()->json([
            'success' => true,
            'count' => $degreeTypes->count(),
            'data' => $degreeTypes->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Debug route to test states by country
Route::get('debug-states-by-country/{countryId}', function($countryId) {
    try {
        $states = DB::table('states')
            ->select('state_id as id', 'state', 'state as name', 'country_id', 'lang', 'is_active')
            ->where('country_id', $countryId)
            ->where('is_active', 1)
            ->orderBy('state')
            ->get();
        
        $allStates = DB::table('states')
            ->where('country_id', $countryId)
            ->get();
        
        return response()->json([
            'success' => true,
            'country_id' => $countryId,
            'active_states_count' => $states->count(),
            'all_states_count' => $allStates->count(),
            'active_states' => $states->toArray(),
            'all_states' => $allStates->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Debug route to test cities by state
Route::get('debug-cities-by-state/{stateId}', function($stateId) {
    try {
        $cities = DB::table('cities')
            ->select('city_id as id', 'city', 'city as name', 'state_id', 'lang', 'is_active')
            ->where('state_id', $stateId)
            ->where('is_active', 1)
            ->orderBy('city')
            ->get();
        
        $allCities = DB::table('cities')
            ->where('state_id', $stateId)
            ->get();
        
        return response()->json([
            'success' => true,
            'state_id' => $stateId,
            'active_cities_count' => $cities->count(),
            'all_cities_count' => $allCities->count(),
            'active_cities' => $cities->toArray(),
            'all_cities' => $allCities->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Debug route to test career levels
Route::get('debug-career-levels', function() {
    try {
        $careerLevels = DB::table('career_levels')
            ->select('career_level_id as id', 'career_level', 'lang', 'is_active')
            ->where('is_active', 1)
            ->orderBy('career_level')
            ->get();
        
        return response()->json([
            'success' => true,
            'count' => $careerLevels->count(),
            'data' => $careerLevels->toArray(),
            'all_count' => DB::table('career_levels')->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Debug route to test job experiences
Route::get('debug-job-experiences', function() {
    try {
        $jobExperiences = DB::table('job_experiences')
            ->select('job_experience_id as id', 'job_experience', 'lang', 'is_active')
            ->where('is_active', 1)
            ->orderBy('job_experience')
            ->get();
        
        return response()->json([
            'success' => true,
            'count' => $jobExperiences->count(),
            'data' => $jobExperiences->toArray(),
            'all_count' => DB::table('job_experiences')->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Debug route to test industries
Route::get('debug-industries', function() {
    try {
        $industries = DB::table('industries')
            ->select('industry_id as id', 'industry', 'lang', 'is_active')
            ->where('is_active', 1)
            ->orderBy('industry')
            ->get();
        
        return response()->json([
            'success' => true,
            'count' => $industries->count(),
            'data' => $industries->toArray(),
            'all_count' => DB::table('industries')->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Master Data APIs (public routes)
Route::get('master-data/job-types', 'Api\MasterDataController@getJobTypes');
Route::get('master-data/career-levels', 'Api\MasterDataController@getCareerLevels');
Route::get('master-data/job-shifts', 'Api\MasterDataController@getJobShifts');
Route::get('master-data/degree-levels', 'Api\MasterDataController@getDegreeLevels');
Route::get('master-data/job-experiences', 'Api\MasterDataController@getJobExperiences');
Route::get('master-data/functional-areas', 'Api\MasterDataController@getFunctionalAreas');
Route::get('master-data/countries', 'Api\MasterDataController@getCountries');
Route::get('master-data/states', 'Api\MasterDataController@getStates');
Route::get('master-data/cities', 'Api\MasterDataController@getCities');
Route::get('master-data/job-skills', 'Api\MasterDataController@getJobSkills');
Route::get('master-data/languages', 'Api\MasterDataController@getLanguages');
Route::get('master-data/language-levels', 'Api\MasterDataController@getLanguageLevels');
Route::get('master-data/degree-types', 'Api\MasterDataController@getDegreeTypes');
Route::get('master-data/major-subjects', 'Api\MasterDataController@getMajorSubjects');
Route::get('master-data/result-types', 'Api\MasterDataController@getResultTypes');

Route::middleware('auth:api')->group( function () {

    //forgot password and change password passport Api's
    Route::post('logout', 'Api\Auth\AuthController@logout');
    Route::post('change-password', 'Api\Auth\AuthController@change_password');
    
    // Test authentication endpoint
    Route::get('test-auth', 'Api\HomeApiController@testAuth');
    
    // Test database connection and tables
    Route::get('test-database', 'Api\HomeApiController@testDatabase');
    
    // Debug home dashboard (minimal data)
    Route::get('home-dashboard-debug', 'Api\HomeApiController@getHomeDataDebug');
    
    // Minimal home dashboard (no complex queries)
    Route::get('home-dashboard-minimal', 'Api\HomeApiController@getHomeDataMinimal');
    
    // Home dashboard data (requires authentication)
    Route::get('home-dashboard', 'Api\HomeApiController@getHomeData');
    
    // Matching jobs (separate endpoint)
    Route::get('matching-jobs', 'Api\HomeApiController@getMatchingJobs');
    
    // Test user data endpoint
    Route::get('test-user-data', 'Api\HomeApiController@testUserData');
    
    // Test recommended jobs endpoint
    Route::get('test-recommended-jobs', 'Api\HomeApiController@testRecommendedJobs');
    
    //change password from app when you are login
    Route::post('jobs/search', 'Api\JobController@job_search');

    Route::get('jobs/show', 'Api\JobController@display_job_details');
    Route::get('jobs/apply', 'Api\JobController@apply_job_form');
    Route::post('jobs/apply', 'Api\JobController@store_apply_job');


    Route::get('apply/{slug}', 'Api\JobController@applyJob');
    Route::post('apply/{slug}', 'Api\JobController@postApplyJob');
    Route::get('add-to-favourite-job/{job_slug}', 'Api\JobController@addToFavouriteJob');
    Route::get('remove-from-favourite-job/{job_slug}', 'Api\JobController@removeFromFavouriteJob');
    Route::get('my-job-applications', 'Api\JobController@myJobApplications');
    Route::get('my-favourite-jobs', 'Api\JobController@myFavouriteJobs');
    Route::get('check-favourite-status/{job_slug}', 'Api\JobController@checkFavouriteStatus');
    Route::get('favourite-jobs-count', 'Api\JobController@getFavouriteJobsCount');
    Route::get('applied-jobs-count', 'Api\JobController@getAppliedJobsCount');


    Route::get('my-profile', 'Api\UserController@myProfile');
    Route::put('my-profile', 'Api\UserController@updateMyProfile');
    Route::post('show-front-profile-summary/{id}', 'Api\UserController@showProfileSummaryApi');
    Route::post('update-front-profile-summary/{id}', 'Api\UserController@updateFrontProfileSummary');
    Route::post('update-immediate-available-status', 'Api\UserController@updateImmediateAvailableStatus');
    Route::get('add-to-favourite-company/{company_slug}', 'Api\UserController@addToFavouriteCompany');
    Route::delete('remove-from-favourite-company/{company_slug}', 'Api\UserController@removeFromFavouriteCompany');
    Route::get('my-followings', 'Api\UserController@myFollowings');    
    Route::get('my-messages', 'Api\SeekerSendController@all_messages');
    Route::get('seeker-append-messages', 'Api\SeekerSendController@append_messages');
    Route::get('seeker-append-only-messages', 'Api\SeekerSendController@appendonly_messages');
    Route::post('seeker-submit-messages', 'Api\SeekerSendController@submit_message');
    Route::get('applicant-message-detail/{id}', 'Api\UserController@applicantMessageDetail');
    Route::post('change-message-status', 'Api\SeekerSendController@change_message_status');

/* * *********************************** */
    Route::post('show-front-profile-projects/{id}', 'Api\UserController@showFrontProfileProjects');
    Route::post('show-applicant-profile-projects/{id}', 'Api\UserController@showApplicantProfileProjects');
    Route::post('upload-front-project-temp-image', 'Api\UserController@uploadProjectTempImage');
    Route::post('get-front-profile-project-form/{id}', 'Api\UserController@getFrontProfileProjectForm');
    Route::post('store-front-profile-project/{id}', 'Api\UserController@storeFrontProfileProject');
    Route::post('get-front-profile-project-edit-form/{user_id}', 'Api\UserController@getFrontProfileProjectEditForm');
    Route::put('update-front-profile-project/{id}/{user_id}', 'Api\UserController@updateFrontProfileProject');;
    Route::delete('delete-front-profile-project', 'Api\UserController@deleteProfileProject');
/* * *********************************** */
    Route::post('show-front-profile-experience/{id}', 'Api\UserController@showFrontProfileExperience');
    Route::post('show-applicant-profile-experience/{id}', 'Api\UserController@showApplicantProfileExperience');
    Route::post('get-front-profile-experience-form/{id}', 'Api\UserController@getFrontProfileExperienceForm');
    Route::post('store-front-profile-experience/{id}', 'Api\UserController@storeFrontProfileExperience');
    Route::post('get-front-profile-experience-edit-form/{id}', 'Api\UserController@getFrontProfileExperienceEditForm');
    Route::put('update-front-profile-experience/{profile_experience_id}/{user_id}', 'Api\UserController@updateFrontProfileExperience');
    Route::delete('delete-front-profile-experience', 'Api\UserController@deleteProfileExperience');
/* * *********************************** */
    Route::post('show-front-profile-education/{id}', 'Api\UserController@showFrontProfileEducation');
    Route::post('show-applicant-profile-education/{id}', 'Api\UserController@showApplicantProfileEducation');
    Route::post('get-front-profile-education-form/{id}', 'Api\UserController@getFrontProfileEducationForm');
    Route::post('store-front-profile-education/{id}', 'Api\UserController@storeFrontProfileEducation');
    Route::post('get-front-profile-education-edit-form/{id}', 'Api\UserController@getFrontProfileEducationEditForm');
    Route::put('update-front-profile-education/{education_id}/{user_id}', 'Api\UserController@updateFrontProfileEducation');
    Route::delete('delete-front-profile-education', 'Api\UserController@deleteProfileEducation');
/* * *********************************** */
    Route::post('show-front-profile-skills/{id}', 'Api\UserController@showProfileSkills');
    Route::post('show-applicant-profile-skills/{id}', 'Api\UserController@showApplicantProfileSkills');
    Route::post('get-front-profile-skill-form/{id}', 'Api\UserController@getFrontProfileSkillForm');
    Route::post('store-front-profile-skill/{id}', 'Api\UserController@storeFrontProfileSkill');
    Route::post('get-front-profile-skill-edit-form/{id}', 'Api\UserController@getFrontProfileSkillEditForm');
    Route::put('update-front-profile-skill/{skill_id}/{user_id}', 'Api\UserController@updateFrontProfileSkill');
    Route::delete('delete-front-profile-skill', 'Api\UserController@deleteProfileSkill');
/* * *********************************** */
    Route::post('show-front-profile-languages/{id}', 'Api\UserController@showProfileLanguages');
    Route::post('show-applicant-profile-languages/{id}', 'Api\UserController@showApplicantProfileLanguages');
    Route::post('get-front-profile-language-form/{id}', 'Api\UserController@getFrontProfileLanguageForm');
    Route::post('store-front-profile-language/{id}', 'Api\UserController@storeFrontProfileLanguage');
    Route::post('get-front-profile-language-edit-form/{id}', 'Api\UserController@getFrontProfileLanguageEditForm');
    Route::put('update-front-profile-language/{language_id}/{user_id}', 'Api\UserController@updateFrontProfileLanguage');
    Route::delete('delete-front.profile-language', 'Api\UserController@deleteProfileLanguage');
/*************************************/

});






/* * ******** UserController ************ */

// Route::get('my-messages', 'Api\UserController@myMessages')->name('my.messages'); 









/* * *********************************** */
/*************************************/


Route::get('my-alerts', 'Api\UserController@myAlerts');
Route::get('delete-alert/{id}', 'Api\UserController@delete_alert');



Route::get('email-to-friend/{job_slug}', 'Api\ContactController@emailToFriend');
Route::post('email-to-friend/{job_slug}', 'Api\ContactController@emailToFriendPost');
Route::get('email-to-friend-thanks', 'Api\ContactController@emailToFriendThanks');

Route::get('report-abuse/{job_slug}', 'Api\ContactController@reportAbuse');
Route::post('report-abuse/{job_slug}', 'Api\ContactController@reportAbusePost');
Route::get('report-abuse-thanks', 'Api\ContactController@reportAbuseThanks');

Route::get('report-abuse-company/{company_slug}', 'Api\ContactController@reportAbuseCompany');

Route::post('report-abuse-company/{company_slug}', 'Api\ContactController@reportAbuseCompanyPost');

Route::get('report-abuse-company-thanks', 'Api\ContactController@reportAbuseCompanyThanks');


Route::get('cms/{slug}', 'Api\CmsController@getPage');

Route::get('terms-of-use', 'Api\CmsController@termsOfUse');

Route::get('contact-us', 'Api\ContactController@index');

Route::post('contact-us', 'Api\ContactController@postContact');

Route::get('contact-us-thanks', 'Api\ContactController@thanks');



Route::get('blog', 'Api\BlogController@index');
Route::get('blog/search', 'Api\BlogController@search');
Route::get('blog/{slug}', 'Api\BlogController@details');
Route::get('/blog/category/{blog}', 'Api\BlogController@categories');





Route::post('filter-default-cities-dropdown', 'Api\AjaxController@filterDefaultCities');

Route::post('filter-default-states-dropdown', 'Api\AjaxController@filterDefaultStates');

Route::post('filter-lang-cities-dropdown', 'Api\AjaxController@filterLangCities');
Route::post('filter-lang-states-dropdown', 'Api\AjaxController@filterLangStates');

Route::post('filter-cities-dropdown', 'Api\AjaxController@filterCities');

Route::post('filter-states-dropdown', 'Api\AjaxController@filterStates');

Route::post('filter-degree-types-dropdown', 'Api\AjaxController@filterDegreeTypes');


// Master Data APIs
Route::get('master-data/countries', 'Api\MasterDataController@getCountries');
Route::get('master-data/states/{countryId}', 'Api\MasterDataController@getStatesByCountry');
Route::get('master-data/cities/{stateId}', 'Api\MasterDataController@getCitiesByState');
Route::get('master-data/functional-areas', 'Api\MasterDataController@getFunctionalAreas');
Route::get('master-data/industries', 'Api\MasterDataController@getIndustries');
Route::get('master-data/job-types', 'Api\MasterDataController@getJobTypes');
Route::get('master-data/job-shifts', 'Api\MasterDataController@getJobShifts');
Route::get('master-data/job-experiences', 'Api\MasterDataController@getJobExperiences');
Route::get('master-data/career-levels', 'Api\MasterDataController@getCareerLevels');
Route::get('master-data/degree-levels', 'Api\MasterDataController@getDegreeLevels');
Route::get('master-data/degree-types', 'Api\MasterDataController@getDegreeTypes');
Route::get('master-data/major-subjects', 'Api\MasterDataController@getMajorSubjects');
Route::get('master-data/result-types', 'Api\MasterDataController@getResultTypes');
Route::get('master-data/salary-periods', 'Api\MasterDataController@getSalaryPeriods');
Route::get('master-data/genders', 'Api\MasterDataController@getGenders');
Route::get('master-data/marital-statuses', 'Api\MasterDataController@getMaritalStatuses');
Route::get('master-data/languages', 'Api\MasterDataController@getLanguages');
Route::get('master-data/language-levels', 'Api\MasterDataController@getLanguageLevels');
Route::get('master-data/ownership-types', 'Api\MasterDataController@getOwnershipTypes');
Route::get('master-data/all', 'Api\MasterDataController@getAllMasterData');

// Enhanced Job Search APIs
Route::get('jobs/search/advanced', 'Api\JobSearchController@advancedSearch');
Route::get('jobs/category/{categorySlug}', 'Api\JobSearchController@getJobsByCategory');
Route::get('jobs/{jobId}/similar', 'Api\JobSearchController@getSimilarJobs');
Route::get('jobs/statistics', 'Api\JobSearchController@getJobStatistics');
Route::get('jobs/featured', 'Api\JobSearchController@getFeaturedJobs');

// Enhanced Company Search APIs
Route::get('companies/search/advanced', 'Api\CompanySearchController@advancedSearch');
Route::get('companies/industry/{industrySlug}', 'Api\CompanySearchController@getCompaniesByIndustry');
Route::get('companies/featured', 'Api\CompanySearchController@getFeaturedCompanies');
Route::get('companies/location', 'Api\CompanySearchController@getCompaniesByLocation');
Route::get('companies/statistics', 'Api\CompanySearchController@getCompanyStatistics');
Route::get('companies/most-jobs', 'Api\CompanySearchController@getCompaniesWithMostJobs');

// Notification APIs
Route::middleware('auth:api')->group(function () {
    Route::get('notifications', 'Api\NotificationController@getUserNotifications');
    Route::get('notifications/count', 'Api\NotificationController@getNotificationCount');
    Route::put('notifications/{id}/read', 'Api\NotificationController@markAsRead');
    Route::put('notifications/read-all', 'Api\NotificationController@markAllAsRead');
    Route::delete('notifications/{id}', 'Api\NotificationController@deleteNotification');
    Route::post('job-alerts', 'Api\NotificationController@createJobAlert');
    Route::get('job-alerts', 'Api\NotificationController@getUserJobAlerts');
    Route::put('job-alerts/{id}', 'Api\NotificationController@updateJobAlert');
    Route::delete('job-alerts/{id}', 'Api\NotificationController@deleteJobAlert');

    // Resume Management APIs
    Route::get('test-cv-route', function() {
        return response()->json(['message' => 'CV test route is working']);
    });
    
    // CV Management APIs
    Route::post('show-front-profile-cvs/{id}', 'Api\UserController@showProfileCvsApi');
    Route::post('get-front-profile-cv-form/{id}', 'Api\UserController@getFrontProfileCvForm');
    Route::post('store-front-profile-cv/{id}', 'Api\UserController@storeProfileCv');
    Route::post('get-front-profile-cv-edit-form/{user_id}', 'Api\UserController@getFrontProfileCvEditForm');
    Route::post('update-front-profile-cv/{id}/{user_id}', 'Api\UserController@updateFrontProfileCv');
    Route::delete('delete-front-profile-cv', 'Api\UserController@deleteProfileCv');
    Route::get('debug-cv-tables', 'Api\UserController@debugCvTablesApi');
    
    // Projects Management APIs
    Route::post('show-front-profile-projects/{id}', 'Api\UserController@showProfileProjectsApi');
    Route::post('get-front-profile-project-form/{id}', 'Api\UserController@getFrontProfileProjectForm');
    Route::post('store-front-profile-project/{id}', 'Api\UserController@storeProfileProject');
    Route::post('get-front-profile-project-edit-form/{user_id}', 'Api\UserController@getFrontProfileProjectEditForm');
    Route::put('update-front-profile-project/{id}/{user_id}', 'Api\UserController@updateFrontProfileProject');
    Route::post('delete-front-profile-project', 'Api\UserController@deleteProfileProject');
    
    // Experience Management APIs
    Route::post('show-front-profile-experience/{id}', 'Api\UserController@showProfileExperienceApi');
    Route::post('get-front-profile-experience-form/{id}', 'Api\UserController@getFrontProfileExperienceForm');
    Route::post('store-front-profile-experience/{id}', 'Api\UserController@storeProfileExperience');
    Route::post('get-front-profile-experience-edit-form/{user_id}', 'Api\UserController@getFrontProfileExperienceEditForm');
    Route::put('update-front-profile-experience/{id}/{user_id}', 'Api\UserController@updateFrontProfileExperience');
    Route::post('delete-front-profile-experience', 'Api\UserController@deleteProfileExperience');
    
    // Education Management APIs
    Route::post('show-front-profile-education/{id}', 'Api\UserController@showProfileEducationApi');
    Route::post('get-front-profile-education-form/{id}', 'Api\UserController@getFrontProfileEducationForm');
    Route::post('store-front-profile-education/{id}', 'Api\UserController@storeProfileEducation');
    Route::post('get-front-profile-education-edit-form/{user_id}', 'Api\UserController@getFrontProfileEducationEditForm');
    Route::put('update-front-profile-education/{id}/{user_id}', 'Api\UserController@updateFrontProfileEducation');
    Route::post('delete-front-profile-education', 'Api\UserController@deleteProfileEducation');
    
    // Skills Management APIs
    Route::post('show-front-profile-skills/{id}', 'Api\UserController@showProfileSkillsApi');
    Route::post('get-front-profile-skill-form/{id}', 'Api\UserController@getFrontProfileSkillForm');
    Route::post('store-front-profile-skill/{id}', 'Api\UserController@storeProfileSkill');
    Route::post('get-front-profile-skill-edit-form/{user_id}', 'Api\UserController@getFrontProfileSkillEditForm');
    Route::put('update-front-profile-skill/{id}/{user_id}', 'Api\UserController@updateFrontProfileSkill');
    Route::post('delete-front-profile-skill', 'Api\UserController@deleteProfileSkill');
    
    // Languages Management APIs
    Route::post('show-front-profile-languages/{id}', 'Api\UserController@showProfileLanguagesApi');
    Route::post('get-front-profile-language-form/{id}', 'Api\UserController@getFrontProfileLanguageForm');
    Route::post('store-front-profile-language/{id}', 'Api\UserController@storeProfileLanguage');
    Route::post('get-front-profile-language-edit-form/{user_id}', 'Api\UserController@getFrontProfileLanguageEditForm');
    Route::put('update-front-profile-language/{id}/{user_id}', 'Api\UserController@updateFrontProfileLanguage');
    Route::post('delete-front-profile-language', 'Api\UserController@deleteProfileLanguage');
    
    // Summary Management APIs
    Route::post('show-front-profile-summary/{id}', 'Api\UserController@showProfileSummaryApi');
    Route::post('update-front-profile-summary/{id}', 'Api\UserController@updateFrontProfileSummary');
});

Route::middleware('auth:company-api')->group(function () {
    Route::get('company/notifications', 'Api\NotificationController@getCompanyNotifications');
    Route::get('company/notifications/count', 'Api\NotificationController@getNotificationCount');
    Route::put('company/notifications/{id}/read', 'Api\NotificationController@markAsRead');
    Route::put('company/notifications/read-all', 'Api\NotificationController@markAllAsRead');
    Route::delete('company/notifications/{id}', 'Api\NotificationController@deleteNotification');
    Route::post('company/job-alerts', 'Api\NotificationController@createJobAlert');
    Route::get('company/job-alerts', 'Api\NotificationController@getUserJobAlerts');
    Route::put('company/job-alerts/{id}', 'Api\NotificationController@updateJobAlert');
    Route::delete('company/job-alerts/{id}', 'Api\NotificationController@deleteJobAlert');
});

// File Upload APIs
Route::middleware('auth:api')->group(function () {
    Route::post('upload/profile-image', 'Api\FileUploadController@uploadProfileImage');
    Route::post('upload/cover-image', 'Api\FileUploadController@uploadCoverImage');
    Route::post('upload/cv', 'Api\FileUploadController@uploadCV');
    Route::post('upload/project-image', 'Api\FileUploadController@uploadProjectImage');
    Route::delete('upload/file', 'Api\FileUploadController@deleteFile');
    Route::get('upload/file-info', 'Api\FileUploadController@getFileInfo');
});

Route::middleware('auth:company-api')->group(function () {
    Route::post('upload/company-logo', 'Api\FileUploadController@uploadCompanyLogo');
    Route::post('upload/company-cover', 'Api\FileUploadController@uploadCompanyCoverImage');
    Route::post('upload/company-document', 'Api\FileUploadController@uploadCompanyDocument');
    Route::delete('upload/file', 'Api\FileUploadController@deleteFile');
    Route::get('upload/file-info', 'Api\FileUploadController@getFileInfo');
});

// Payment History APIs
Route::middleware('auth:api')->group(function () {
    Route::get('payment-history', 'Api\PaymentHistoryController@getUserPaymentHistory');
    Route::get('payment-history/stats', 'Api\PaymentHistoryController@getUserPaymentStats');
    Route::get('payment-history/order/{orderId}', 'Api\PaymentHistoryController@getOrderDetails');
    Route::get('payment-history/recent', 'Api\PaymentHistoryController@getRecentTransactions');
    Route::get('payment-history/methods', 'Api\PaymentHistoryController@getPaymentMethods');
});

Route::middleware('auth:company-api')->group(function () {
    Route::get('company/payment-history', 'Api\PaymentHistoryController@getCompanyPaymentHistory');
    Route::get('company/payment-history/stats', 'Api\PaymentHistoryController@getCompanyPaymentStats');
    Route::get('company/payment-history/order/{orderId}', 'Api\PaymentHistoryController@getOrderDetails');
    Route::get('company/payment-history/recent', 'Api\PaymentHistoryController@getRecentTransactions');
    Route::get('company/payment-history/methods', 'Api\PaymentHistoryController@getPaymentMethods');
});

Route::get('/testing',function(){
    
  $email =  Mail::send('emails.emailVerificationEmail', ['token' => 'dfssdfsadfsadfsadfw3w34324342'],function($message) {
       $message->to("vikashkeswani@gmail.com");
       $message->subject('Email Verification Mail -- OLD Project Jobeify');
   });

   return response()->json([
       'message' => 'Email Successfully Sent FROM OLD Jobeify Project Vikash'
   ]);
}) ;

// Notification routes
Route::middleware('auth:api')->group(function () {
    // Update push token
    Route::post('update-push-token', 'Api\NotificationController@updatePushToken');
    
    // Get notification preferences
    Route::get('notification-preferences', 'Api\NotificationController@getPreferences');
    
    // Update notification preferences
    Route::post('update-notification-preferences', 'Api\NotificationController@updatePreferences');
    
    // Send push notification
    Route::post('send-push-notification', 'Api\NotificationController@sendPushNotification');
    
    // Get notification history
    Route::get('notification-history', 'Api\NotificationController@getNotificationHistory');
    
    // Mark notification as read
    Route::post('mark-notification-read', 'Api\NotificationController@markAsRead');
    
    // Delete notification
    Route::delete('delete-notification/{id}', 'Api\NotificationController@deleteNotification');
});