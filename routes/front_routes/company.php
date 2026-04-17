<?php
Route::get('company/{slug}', 'Company\CompanyController@companyDetail')->name('company.detail');
Route::get('companies', 'Company\CompaniesController@company_listing')->name('company.listing');

// Stripe success URLs are public so redirect from Stripe (cross-site) still runs fulfillment when session cookie may not be sent
Route::get('recruiter/stripe/success', 'Company\RecruiterStripeCheckoutController@success')->name('recruiter.stripe.success');
Route::get('resume/unlock/success', 'Company\ResumeUnlockController@success')->name('resume.unlock.success');
Route::get('job/promotions/success', 'Company\JobPromotionCheckoutController@success')->name('job.promotions.success');

// Resume unlock: pricing page and Stripe checkout
Route::get('resume/unlock/page/{userId}', 'Company\ResumeUnlockController@showUnlockPage')->middleware(['auth:company', 'company.verified', 'company.documents.approved'])->name('resume.unlock.page');
Route::get('resume/unlock/{userId}', 'Company\ResumeUnlockController@createCheckout')->middleware(['auth:company', 'company.verified', 'company.documents.approved'])->name('resume.unlock.checkout');

// Employer Verification Routes
Route::middleware(['auth:company'])->group(function () {
    Route::get('company/verification/upload', 'Company\VerificationController@showUploadForm')->name('company.verification.upload');
    Route::post('company/verification/store', 'Company\VerificationController@store')->name('company.verification.store');
    Route::get('company/verification/document/{id}', 'Company\VerificationController@show')->name('company.verification.document.show');
});

Route::middleware(['auth:company', 'company.verified'])->group(function () {
Route::get('company-documents', 'Company\CompanyController@company_documents')->name('company.documents');
Route::get('company-packages', 'Company\CompanyController@resume_search_packages')->name('company.packages');

// Recruiter posting: packages & subscriptions (Stripe)
Route::get('recruiter/posting/packages', 'Company\RecruiterPostingController@packages')->name('recruiter.posting.packages');
Route::get('recruiter/posting/subscriptions', 'Company\RecruiterPostingController@subscriptions')->name('recruiter.posting.subscriptions');
Route::post('recruiter/posting/apply-package-coupon', 'PackageCouponSessionController@applyEmployer')->name('recruiter.posting.apply.coupon');
Route::post('recruiter/posting/clear-package-coupon', 'PackageCouponSessionController@clearEmployer')->name('recruiter.posting.clear.coupon');
Route::get('recruiter/checkout/package/{packageId}', 'Company\RecruiterStripeCheckoutController@redirectToCheckout')->name('recruiter.checkout.package');
Route::get('recruiter/billing-information/new-client/{token}', 'Company\RecruiterStripeCheckoutController@createSession')->name('recruiter.stripe.checkout');
Route::get('company-home', 'Company\CompanyController@index')->name('company.home');
Route::get('list-payment-history', 'Company\CompanyController@indexCompaniesHistory')->name('company.list-payment-history');
Route::get('fetch-payment-history', 'Company\CompanyController@fetchCompaniesHistory')->name('company.fetch.data.companiesHistory');

Route::get('company-profile', 'Company\CompanyController@companyProfile')->name('company.profile');
Route::put('update-company-profile', 'Company\CompanyController@updateCompanyProfile')->name('update.company.profile');
Route::put('upload-company-documents', 'Company\CompanyController@uploadDocuments')->name('update.company.upload_documents');
Route::get('posted-jobs', 'Company\CompanyController@postedJobs')->name('posted.jobs');

Route::get('featured-companies', 'Company\CompanyController@featuredcompanies')->name('company.featuredcompanies');
Route::post('contact-company-message-send', 'Company\CompanyController@sendContactForm')->name('contact.company.message.send');
Route::post('contact-applicant-message-send', 'Company\CompanyController@sendApplicantContactForm')->name('contact.applicant.message.send');
Route::get('company-followers', 'Company\CompanyController@companyFollowers')->name('company.followers');
/* Route::get('company-messages', 'Company\CompanyController@companyMessages')->name('company.messages'); */
Route::post('submit-message-seeker', 'CompanyMessagesController@submitnew_message_seeker')->name('submit-message-seeker');

Route::get('company-messages', 'CompanyMessagesController@all_messages')->name('company.messages');
Route::get('append-messages', 'CompanyMessagesController@append_messages')->name('append-message');
Route::get('append-only-messages', 'CompanyMessagesController@appendonly_messages')->name('append-only-message');
Route::post('company-submit-messages', 'CompanyMessagesController@submit_message')->name('company.submit-message');
Route::get('company-message-detail/{id}', 'Company\CompanyController@companyMessageDetail')->name('company.message.detail');

// Referral Program Routes
Route::get('referral-program', 'Company\CompanyController@referralProgram')->name('company.referral.program');
Route::get('fetch-referral-history', 'Company\CompanyController@fetchReferralHistory')->name('company.fetch.referral.history');
Route::post('send-referral-invite', 'Company\CompanyController@sendReferralInvite')->name('company.send.referral.invite');
});

Route::middleware(['auth:company', 'company.verified', 'company.documents.approved'])->group(function () {
Route::get('unloced-seekers', 'Company\CompanyController@UnlockedUser')->name('company.unloced-users');
Route::get('unlock/{user}', 'Company\CompanyController@unlock')->name('company.unlock');
Route::get('unlocked-users-change-status', 'Company\CompanyController@setUnlockedUserStatus')->name('unlocked.users.setStatus');
Route::get('list-applied-users/{job_id}', 'Company\CompanyController@listAppliedUsers')->name('list.applied.users');
Route::get('list-hired-users/{job_id}', 'Company\CompanyController@listHiredUsers')->name('list.hired.users');
Route::get('list-favourite-applied-users/{job_id}', 'Company\CompanyController@listFavouriteAppliedUsers')->name('list.favourite.applied.users');
Route::get('add-to-favourite-applicant/{application_id}/{user_id}/{job_id}/{company_id}', 'Company\CompanyController@addToFavouriteApplicant')->name('add.to.favourite.applicant');
Route::get('remove-from-favourite-applicant/{application_id}/{user_id}/{job_id}/{company_id}', 'Company\CompanyController@removeFromFavouriteApplicant')->name('remove.from.favourite.applicant');
Route::get('hire-from-favourite-applicant/{application_id}/{user_id}/{job_id}/{company_id}', 'Company\CompanyController@hireFromFavouriteApplicant')->name('hire.from.favourite.applicant');
Route::get('removed-from-hired-applicant/{application_id}/{user_id}/{job_id}/{company_id}', 'Company\CompanyController@removehireFromFavouriteApplicant')->name('remove.hire.from.favourite.applicant');
Route::get('applicant-profile/{application_id}', 'Company\CompanyController@applicantProfile')->name('applicant.profile');
Route::get('reject-applicant-profile/{application_id}', 'Company\CompanyController@rejectApplicantProfile')->name('reject.applicant.profile');
Route::get('user-profile/{id}', 'Company\CompanyController@userProfile')->name('user.profile');
});
