<?php

/* * ******  JobCategory Start ********** */
Route::get('list-job-categories', array_merge(['uses' => 'Admin\JobCategoryController@indexJobCategories'], $all_users))->name('list.job.categories');
Route::get('create-job-category', array_merge(['uses' => 'Admin\JobCategoryController@createJobCategory'], $all_users))->name('create.job.category');
Route::post('store-job-category', array_merge(['uses' => 'Admin\JobCategoryController@storeJobCategory'], $all_users))->name('store.job.category');
Route::get('edit-job-category/{id}', array_merge(['uses' => 'Admin\JobCategoryController@editJobCategory'], $all_users))->name('edit.job.category');
Route::put('update-job-category/{id}', array_merge(['uses' => 'Admin\JobCategoryController@updateJobCategory'], $all_users))->name('update.job.category');
Route::delete('delete-job-category', array_merge(['uses' => 'Admin\JobCategoryController@deleteJobCategory'], $all_users))->name('delete.job.category');
Route::post('bulk-delete-job-categories', array_merge(['uses' => 'Admin\JobCategoryController@bulkDeleteJobCategories'], $all_users))->name('bulk.delete.job.categories');
Route::get('fetch-job-categories', array_merge(['uses' => 'Admin\JobCategoryController@fetchJobCategoriesData'], $all_users))->name('fetch.data.job.categories');
Route::put('make-active-job-category', array_merge(['uses' => 'Admin\JobCategoryController@makeActiveJobCategory'], $all_users))->name('make.active.job.category');
Route::put('make-not-active-job-category', array_merge(['uses' => 'Admin\JobCategoryController@makeNotActiveJobCategory'], $all_users))->name('make.not.active.job.category');
Route::get('sort-job-categories', array_merge(['uses' => 'Admin\JobCategoryController@sortJobCategories'], $all_users))->name('sort.job.categories');
Route::get('job-category-sort-data', array_merge(['uses' => 'Admin\JobCategoryController@jobCategorySortData'], $all_users))->name('job.category.sort.data');
Route::put('job-category-sort-update', array_merge(['uses' => 'Admin\JobCategoryController@jobCategorySortUpdate'], $all_users))->name('job.category.sort.update');
/* * ****** End JobCategory ********** */