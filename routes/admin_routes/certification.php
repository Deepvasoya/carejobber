<?php

/* * ******  Certifications Start ********** */
Route::get('list-certifications', array_merge(['uses' => 'Admin\CertificationController@index'], $all_users))->name('list.certifications');
Route::get('create-certification', array_merge(['uses' => 'Admin\CertificationController@create'], $all_users))->name('create.certification');
Route::post('store-certification', array_merge(['uses' => 'Admin\CertificationController@store'], $all_users))->name('store.certification');
Route::get('edit-certification/{id}', array_merge(['uses' => 'Admin\CertificationController@edit'], $all_users))->name('edit.certification');
Route::put('update-certification/{id}', array_merge(['uses' => 'Admin\CertificationController@update'], $all_users))->name('update.certification');
Route::delete('delete-certification', array_merge(['uses' => 'Admin\CertificationController@delete'], $all_users))->name('delete.certification');
Route::get('fetch-certifications', array_merge(['uses' => 'Admin\CertificationController@fetchData'], $all_users))->name('fetch.data.certifications');
Route::put('make-active-certification', array_merge(['uses' => 'Admin\CertificationController@makeActive'], $all_users))->name('make.active.certification');
Route::put('make-not-active-certification', array_merge(['uses' => 'Admin\CertificationController@makeNotActive'], $all_users))->name('make.not.active.certification');
/* * ****** End Certifications ********** */