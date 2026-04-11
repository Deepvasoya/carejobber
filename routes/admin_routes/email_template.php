<?php

/* * ******  Email Template Routes ********** */
Route::get('list-email-templates', array_merge(['uses' => 'Admin\EmailTemplateController@indexEmailTemplates'], $all_users))->name('list.email.templates');
Route::get('edit-email-template/{id}', array_merge(['uses' => 'Admin\EmailTemplateController@editEmailTemplate'], $all_users))->name('edit.email.template');
Route::put('update-email-template/{id}', array_merge(['uses' => 'Admin\EmailTemplateController@updateEmailTemplate'], $all_users))->name('update.email.template');
Route::get('fetch-email-templates', array_merge(['uses' => 'Admin\EmailTemplateController@fetchEmailTemplatesData'], $all_users))->name('fetch.data.email.templates');
Route::get('preview-email-template/{id}', array_merge(['uses' => 'Admin\EmailTemplateController@previewEmailTemplate'], $all_users))->name('preview.email.template');
Route::post('reset-email-templates', array_merge(['uses' => 'Admin\EmailTemplateController@resetEmailTemplate'], $all_users))->name('reset.email.templates');
/* * ****** End Email Template Routes ********** */
?>
