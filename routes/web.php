<?php
/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\SearchAutocompleteController;
use Illuminate\Support\Facades\File;

Route::get('make-login/{guard}', 'IndexController@login')->name('make.login');

// Stripe webhook (public, no CSRF)
Route::post('stripe/webhook', 'StripeWebhookController@handle')->name('stripe.webhook');

// Theme translation file (app.js requests assets/data/translations/en.json)
Route::get('assets/data/translations/{lang}.json', function ($lang) {
    $path = public_path("assets/data/translations/" . basename($lang) . ".json");
    if (!File::exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => 'application/json']);
})->where('lang', '[a-z]{2}');
Route::get('admin/assets/data/translations/{lang}.json', function ($lang) {
    $path = public_path("assets/data/translations/" . basename($lang) . ".json");
    if (!File::exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => 'application/json']);
})->where('lang', '[a-z]{2}');

// Serve widget/preview images from public (avoids 404 when docroot is not public/)
Route::get('images/thumb/{filename}', function ($filename) {
    $safe = basename(preg_replace('/[^a-zA-Z0-9._-]/', '', $filename));
    $path = public_path('images/thumb/' . $safe);
    if (!File::exists($path)) {
        $path = public_path('images/' . $safe);
    }
    if (!File::exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => mime_content_type($path)]);
})->where('filename', '[a-zA-Z0-9._-]+');
Route::get('images/{filename}', function ($filename) {
    $safe = basename(preg_replace('/[^a-zA-Z0-9._-]/', '', $filename));
    if (in_array(strtolower($safe), ['thumb', 'mid'], true)) {
        abort(404);
    }
    $path = public_path('images/' . $safe);
    if (!File::exists($path) || !is_file($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => mime_content_type($path)]);
})->where('filename', '[a-zA-Z0-9._-]+');
Route::get('company/email/verify', 'Company\CompanyVerificationController@show')->name('company.verification.notice');
Route::post('company/email/resend', 'Company\CompanyVerificationController@resend')->name('company.verification.resend');
$real_path = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'front_routes' . DIRECTORY_SEPARATOR;
Route::get('jobs-autocomplete', [SearchAutocompleteController::class, 'jobs'])->name('jobs.autocomplete');
Route::get('locations-autocomplete', [SearchAutocompleteController::class, 'locations'])->name('locations.autocomplete');
Route::get('geocode-city', [SearchAutocompleteController::class, 'reverseGeocode'])->middleware('throttle:30,1')->name('geocode.city');
/* * ******** IndexController ************ */
Route::get('/', 'IndexController@index')->name('index');
Route::get('sitemap.xml', 'SitemapController@index')->name('sitemap.index');
Route::get('sitemap-jobs.xml', 'SitemapController@jobs')->name('sitemap.jobs');
Route::get('employers/{company:slug}', 'Seo\ProgrammaticSeoController@employer')->name('seo.employer');
Route::get('salary/{categorySlug}-alberta', 'Seo\ProgrammaticSeoController@salary')->name('seo.salary');
Route::get('guides/{guide:slug}', 'Seo\ProgrammaticSeoController@guide')->name('seo.guide');


Route::get('/check-time', 'IndexController@checkTime')->name('check-time');
Route::post('set-locale', 'IndexController@setLocale')->name('set.locale');
/* * ******** HomeController ************ */
Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
Route::middleware(['verified'])->group(function(){
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('recommended-jobs', 'HomeController@recommendedJobs')->name('recommended.jobs');
});
Route::get('all-categories', 'IndexController@allCategories')->name('all-categories');
/* * ******** TypeAheadController ******* */
Route::get('typeahead-currency_codes', 'TypeAheadController@typeAheadCurrencyCodes')->name('typeahead.currency_codes');
/* * ******** FaqController ******* */
Route::get('faq', 'FaqController@index')->name('faq');
/* * ******** CronController ******* */
Route::get('check-package-validity', 'CronController@checkPackageValidity');
/* * ******** Verification ******* */
Route::get('email-verification/error', 'Auth\RegisterController@getVerificationError')->name('email-verification.error');
Route::get('email-verification/check/{token}', 'Auth\RegisterController@getVerification')->name('email-verification.check');
Route::get('not-verified', 'Auth\RegisterController@notVerified')->name('not-verified');
Route::get('company-email-verification/error', 'Company\Auth\RegisterController@getVerificationError')->name('company.email-verification.error');
Route::get('company-email-verification/check/{token}', 'Company\Auth\RegisterController@getVerification')->name('company.email-verification.check');
/* * ***************************** */
// Sociallite Start
// OAuth Routes
Route::get('login/jobseeker/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('company-login', 'Auth\LoginController@companyLogin')->name('company.login.landing');
Route::get('company-register', 'Auth\LoginController@companyRegister')->name('company.register.landing');

// Help Centre should land on the Laravel FAQ page.
Route::redirect('help-centre', '/faq', 302)->name('help.centre');
Route::get('login/jobseeker/{provider}/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('login/employer/{provider}', 'Company\Auth\LoginController@redirectToProvider');
Route::get('login/employer/{provider}/callback', 'Company\Auth\LoginController@handleProviderCallback');

Route::post('/import-records', [ImportController::class,'store'])->name('import');



Route::prefix('chat')->group(function () {
  Route::get('/', 'ChatController@index')->name('chat.index'); // Full-page chat
  Route::get('conversations', 'ChatController@getConversations');
  Route::get('jobs', 'ChatController@getJobs');
  Route::get('conversations/{id}/messages', 'ChatController@getMessages');
  Route::get('conversations/{id}/messages/new', 'ChatController@getNewMessages');
  Route::post('conversations/{id}/messages', 'ChatController@sendMessage');
  Route::put('conversations/{id}/read', 'ChatController@markAsRead');
  Route::post('start/{userId}', 'ChatController@startConversation')->name('chat.start');
  Route::post('messages/{id}/reaction', 'ChatController@toggleReaction')->name('chat.reaction');
  Route::get('attachments/{id}/download', 'ChatController@downloadAttachment')->name('chat.attachment.download');
  Route::put('messages/{id}', 'ChatController@updateMessage')->name('chat.message.update');
  Route::delete('messages/{id}', 'ChatController@deleteMessage')->name('chat.message.delete');
});

Route::prefix('chat/status')->group(function () {
  Route::post('activity', 'ChatStatusController@updateActivity');
  Route::post('typing', 'ChatStatusController@updateTyping');
  Route::get('typing/{conversationId}', 'ChatStatusController@getTypingStatus');
  Route::get('{userId}/{userType}', 'ChatStatusController@getStatus');
});



// Sociallite End
/* * ***************************** */
Route::redirect('/for-employers', '/employer-zone', 301);
Route::get('/employer-zone', 'EmployerLandingController@index')->name('employer.landing');
Route::get('/for-jobseekers', function () {return view('for_jobseekers');});
Route::post('tinymce-image_upload-front', 'TinyMceController@uploadImage')->name('tinymce.image_upload.front');
Route::get('cronjob/send-alerts', 'AlertCronController@index')->name('send-alerts');
Route::post('subscribe-newsletter', 'SubscriptionController@getSubscription')->name('subscribe.newsletter');
/* * ******** OrderController ************ */
include_once($real_path . 'order.php');
/* * ******** CmsController ************ */
include_once($real_path . 'cms.php');
/* * ******** JobController ************ */
include_once($real_path . 'job.php');
/* * ******** ContactController ************ */
include_once($real_path . 'contact.php');
/* * ******** Company Auth (must be before company.php so /company/login is not caught by company/{slug}) ************ */
include_once($real_path . 'company_auth.php');
/* * ******** CompanyController ************ */
include_once($real_path . 'company.php');
/* * ******** AjaxController ************ */
include_once($real_path . 'ajax.php');
/* * ******** UserController ************ */
include_once($real_path . 'site_user.php');
/* * ******** User Auth ************ */
Auth::routes(['verify' => true]);
/* * ******** Admin Auth ************ */
include_once($real_path . 'admin_auth.php');
Route::get('blog', 'BlogController@index')->name('blogs');
Route::get('blog/search', 'BlogController@search')->name('blog-search');
Route::get('blog/{slug}', 'BlogController@details')->name('blog-detail');
Route::get('/blog/category/{blog}', 'BlogController@categories')->name('blog-category');
Route::get('/company-change-message-status', 'CompanyMessagesController@change_message_status')->name('company-change-message-status');
Route::get('/seeker-change-message-status', 'Job\SeekerSendController@change_message_status')->name('seeker-change-message-status');
Route::post('/api/users', 'AjaxController@create');
Route::get('/sitemap/companies', 'SitemapController@companies');
Route::get('job8', 'Job8Controller@job8')->name('job8');
Route::get('cronjob/delete-jobs', 'Job8Controller@delete_jobs')->name('delete-jobs');
Route::get('cronjob/amend-jobs', 'Job8Controller@amend_jobs')->name('amend-jobs');
Route::get('cronjob/set-count-industry', 'Job8Controller@set_count_industry')->name('set_count_industry');
Route::get('cronjob/set-total-count', 'Job8Controller@set_total_count')->name('set_total_count');
Route::get('cronjob/set-total-country', 'Job8Controller@set_count_country')->name('set_count_country');
Route::get('cronjob/set-total-companies', 'Job8Controller@set_count_company')->name('set_count_company');
Route::get('cronjob/set-total-jobType', 'Job8Controller@set_count_jobType')->name('set_count_jobType');
Route::get('cronjob/remove-duplicates', 'Job8Controller@remove_duplicates')->name('remove_duplicates');
Route::get('cronjob/set-count-company', 'Job8Controller@set_count_company')->name('set_count_company');
Route::get('cronjob/remove-duplicate-companies', 'Job8Controller@remove_duplicates')->name('remove-duplicate-companies');
Route::get('cronjob/recover-companies', 'Job8Controller@recover_companies')->name('recover-companies');
Route::get('cronjob/recover-jobs', 'Job8Controller@recover_jobs')->name('recover-jobs');
Route::get('set-location', 'Job8Controller@set_location')->name('set_location');
Route::post('ajax_upload_file', 'FilerController@upload')->name('filer.image-upload');
Route::post('ajax_remove_file', 'FilerController@fileDestroy')->name('filer.image-remove');
Route::get('/clear-cache', function () {
  $exitCode = Artisan::call('config:clear');
  $exitCode = Artisan::call('cache:clear');
  $exitCode = Artisan::call('config:cache');
  return 'DONE'; //Return anything
});

// Razorpay Routes
Route::get('razorpay-order-form/{package_id}/{new_or_upgrade}', 'RazorpayOrderController@razorpayOrderForm')->name('razorpay.order.form');
Route::post('razorpay-order-package', 'RazorpayOrderController@razorpayOrderPackage')->name('razorpay.order.package');
Route::post('verify-razorpay-payment', 'RazorpayOrderController@verifyRazorpayPayment')->name('razorpay.verify');

// Paytm Routes
Route::get('paytm-order-form/{package_id}/{new_or_upgrade}', 'PaytmOrderController@paytmOrderForm')->name('paytm.order.form');
Route::post('paytm-order-package', 'PaytmOrderController@paytmOrderPackage')->name('paytm.order.package');
Route::post('paytm-callback', 'PaytmOrderController@paytmCallback')->name('paytm.callback');
