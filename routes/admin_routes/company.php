<?php



/* * ******  Company Start ********** */

Route::get('list-companies', array_merge(['uses' => 'Admin\CompanyController@indexCompanies'], $all_users))->name('list.companies');

Route::get('list-payment-history', array_merge(['uses' => 'Admin\CompanyController@indexCompaniesHistory'], $all_users))->name('list.payment.hostory');
Route::get('public-job/{id}','AjaxController@jobdetail')->name('public.job');
Route::get('public-company/{id}','AjaxController@companyProfile')->name('admin.public.company');
Route::get('fetch-payment-history', array_merge(['uses' => 'Admin\CompanyController@fetchCompaniesHistory'], $all_users))->name('fetch.data.companiesHistory');
Route::get('get-company-payment-details', array_merge(['uses' => 'Admin\CompanyController@getCompanyPaymentDetails'], $all_users))->name('get.company.payment.details');

Route::get('create-company', array_merge(['uses' => 'Admin\CompanyController@createCompany'], $all_users))->name('create.company');

Route::post('store-company', array_merge(['uses' => 'Admin\CompanyController@storeCompany'], $all_users))->name('store.company');

Route::get('edit-company/{id}', array_merge(['uses' => 'Admin\CompanyController@editCompany'], $all_users))->name('edit.company');
Route::post('change-document-status/{id}', array_merge(['uses' => 'Admin\CompanyController@changeStatus'], $all_users))->name('edit.changeStatus');

Route::put('update-company/{id}', array_merge(['uses' => 'Admin\CompanyController@updateCompany'], $all_users))->name('update.company');

Route::delete('delete-company', array_merge(['uses' => 'Admin\CompanyController@deleteCompany'], $all_users))->name('delete.company');
Route::post('bulk-delete-companies', array_merge(['uses' => 'Admin\CompanyController@bulkDeleteCompanies'], $all_users))->name('bulk.delete.companies');

Route::get('fetch-companies', array_merge(['uses' => 'Admin\CompanyController@fetchCompaniesData'], $all_users))->name('fetch.data.companies');

Route::put('make-active-company', array_merge(['uses' => 'Admin\CompanyController@makeActiveCompany'], $all_users))->name('make.active.company');

Route::put('make-not-active-company', array_merge(['uses' => 'Admin\CompanyController@makeNotActiveCompany'], $all_users))->name('make.not.active.company');

Route::put('make-featured-company', array_merge(['uses' => 'Admin\CompanyController@makeFeaturedCompany'], $all_users))->name('make.featured.company');

Route::put('make-not-featured-company', array_merge(['uses' => 'Admin\CompanyController@makeNotFeaturedCompany'], $all_users))->name('make.not.featured.company');



Route::get('list-applied-users/{job_id}', 'Admin\CompanyController@listAppliedUsers')->name('admin.list.applied.users');

Route::get('company-unlocked-candidates/{id}', array_merge(['uses' => 'Admin\CompanyController@viewUnlockedCandidates'], $all_users))->name('admin.company.unlocked.candidates');

Route::get('company-verification-requests', array_merge(['uses' => 'Admin\CompanyController@verificationRequests'], $all_users))->name('admin.verification.requests');

Route::post('company-approve-verification/{id}', array_merge(['uses' => 'Admin\CompanyController@approveVerification'], $all_users))->name('admin.company.approve.verification');
Route::post('company-reject-verification/{id}', array_merge(['uses' => 'Admin\CompanyController@rejectVerification'], $all_users))->name('admin.company.reject.verification');

/* * ****** End Company ********** */
