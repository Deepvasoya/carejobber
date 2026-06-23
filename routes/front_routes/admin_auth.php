<?php

Route::prefix(config('app.admin_prefix', 'admin'))->name('admin.')->group(function () {
    Route::get('/', 'Admin\Auth\LoginController@showLoginForm');
    Route::get('/login', 'Admin\Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Admin\Auth\LoginController@login');
    Route::get('/logout', 'Admin\Auth\LoginController@logout')->name('logout');
    Route::get('/password/reset', 'Admin\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('/password/email', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('/password/reset/{token}', 'Admin\Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('/password/reset', 'Admin\Auth\ResetPasswordController@reset');
    
    Route::get('/blog_category', 'Admin\Blog_categoriesController@index')->name('blog_category');
    Route::post('/blog_category/create', 'Admin\Blog_categoriesController@create')->name('blog_category.create');
    Route::get('/blog_category/{id}', 'Admin\Blog_categoriesController@get_blog_category_by_id')->name('blog_category.show');
    Route::post('/blog_category/update', 'Admin\Blog_categoriesController@update')->name('blog_category.update');
    Route::delete('/blog_category/{id}', 'Admin\Blog_categoriesController@destroy')->name('blog_category.destroy');
    
    Route::get('/blog', 'Admin\BlogsController@index')->name('blog');
    Route::get('/blog/add', 'Admin\BlogsController@add')->name('add-new-blog');
    Route::post('/blog/create', 'Admin\BlogsController@create')->name('blog.create');
    Route::get('/blog/edit/{id}', 'Admin\BlogsController@get_blog')->name('edit-blog');
    Route::post('/blog/update', 'Admin\BlogsController@update')->name('blog.update');
    Route::delete('/blog/{id}', 'Admin\BlogsController@destroy')->name('blog.destroy');
});
