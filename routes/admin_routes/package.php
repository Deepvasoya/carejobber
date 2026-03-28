<?php

/* * ******  Package Start ********** */
Route::get('list-packages', array_merge(['uses' => 'Admin\PackageController@indexPackages'], $all_users))->name('list.packages');
Route::get('create-package', array_merge(['uses' => 'Admin\PackageController@createPackage'], $all_users))->name('create.package');
Route::post('store-package', array_merge(['uses' => 'Admin\PackageController@storePackage'], $all_users))->name('store.package');
Route::get('edit-package/{id}', array_merge(['uses' => 'Admin\PackageController@editPackage'], $all_users))->name('edit.package');
Route::put('update-package/{id}', array_merge(['uses' => 'Admin\PackageController@updatePackage'], $all_users))->name('update.package');
Route::delete('delete-package', array_merge(['uses' => 'Admin\PackageController@deletePackage'], $all_users))->name('delete.package');
Route::get('fetch-packages', array_merge(['uses' => 'Admin\PackageController@fetchPackagesData'], $all_users))->name('fetch.data.packages');
/* * ****** Package coupons ********** */
Route::get('list-package-coupons', array_merge(['uses' => 'Admin\PackageCouponController@index'], $all_users))->name('list.package.coupons');
Route::get('create-package-coupon', array_merge(['uses' => 'Admin\PackageCouponController@create'], $all_users))->name('create.package.coupon');
Route::post('store-package-coupon', array_merge(['uses' => 'Admin\PackageCouponController@store'], $all_users))->name('store.package.coupon');
Route::get('edit-package-coupon/{id}', array_merge(['uses' => 'Admin\PackageCouponController@edit'], $all_users))->name('edit.package.coupon');
Route::put('update-package-coupon/{id}', array_merge(['uses' => 'Admin\PackageCouponController@update'], $all_users))->name('update.package.coupon');
Route::delete('delete-package-coupon/{id}', array_merge(['uses' => 'Admin\PackageCouponController@destroy'], $all_users))->name('delete.package.coupon');
/* * ****** End Package ********** */