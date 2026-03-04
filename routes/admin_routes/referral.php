<?php

$all_users = ['allowed_roles' => ['SUP_ADM', 'SUB_ADM']];

// Company Referral Management Routes
Route::get('list-referrals', array_merge(['uses' => 'Admin\ReferralController@index'], $all_users))->name('admin.list.referrals');
Route::get('fetch-referrals', array_merge(['uses' => 'Admin\ReferralController@fetchReferrals'], $all_users))->name('admin.fetch.referrals');
Route::get('referral-settings', array_merge(['uses' => 'Admin\ReferralController@settings'], $all_users))->name('admin.referral.settings');
Route::post('update-referral-settings', array_merge(['uses' => 'Admin\ReferralController@updateSettings'], $all_users))->name('admin.update.referral.settings');
Route::delete('delete-referral', array_merge(['uses' => 'Admin\ReferralController@delete'], $all_users))->name('admin.delete.referral');

// Job Seeker Referral Management Routes
Route::get('list-user-referrals', array_merge(['uses' => 'Admin\UserReferralController@index'], $all_users))->name('admin.list.user.referrals');
Route::get('fetch-user-referrals', array_merge(['uses' => 'Admin\UserReferralController@fetchUserReferrals'], $all_users))->name('admin.fetch.user.referrals');
Route::post('update-user-referral-settings', array_merge(['uses' => 'Admin\UserReferralController@updateSettings'], $all_users))->name('admin.update.user.referral.settings');
Route::delete('delete-user-referral', array_merge(['uses' => 'Admin\UserReferralController@delete'], $all_users))->name('admin.delete.user.referral');
