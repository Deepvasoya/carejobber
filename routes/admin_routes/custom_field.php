<?php

/* Custom fields (dynamic forms: profile, job listing, resume builder) */
Route::get('list-custom-fields', array_merge(['uses' => 'Admin\CustomFieldController@index'], $all_users))->name('list.custom.fields');
Route::get('create-custom-field', array_merge(['uses' => 'Admin\CustomFieldController@create'], $all_users))->name('create.custom.field');
Route::post('store-custom-field', array_merge(['uses' => 'Admin\CustomFieldController@store'], $all_users))->name('store.custom.field');
Route::get('edit-custom-field/{id}', array_merge(['uses' => 'Admin\CustomFieldController@edit'], $all_users))->name('edit.custom.field');
Route::put('update-custom-field/{id}', array_merge(['uses' => 'Admin\CustomFieldController@update'], $all_users))->name('update.custom.field');
Route::delete('delete-custom-field/{id}', array_merge(['uses' => 'Admin\CustomFieldController@destroy'], $all_users))->name('delete.custom.field');
