<?php

/* Manage Roles & Permissions */
Route::get('list-roles', array_merge(['uses' => 'Admin\RoleController@index'], $sup_only))->name('list.roles');
Route::get('create-role', array_merge(['uses' => 'Admin\RoleController@create'], $sup_only))->name('create.role');
Route::post('store-role', array_merge(['uses' => 'Admin\RoleController@store'], $sup_only))->name('store.role');
Route::get('edit-role/{id}', array_merge(['uses' => 'Admin\RoleController@edit'], $sup_only))->name('edit.role');
Route::put('update-role/{id}', array_merge(['uses' => 'Admin\RoleController@update'], $sup_only))->name('update.role');
Route::delete('delete-role', array_merge(['uses' => 'Admin\RoleController@destroy'], $sup_only))->name('delete.role');
Route::get('fetch-roles', array_merge(['uses' => 'Admin\RoleController@fetchData'], $sup_only))->name('fetch.data.roles');
