<?php
$all_users = ['middleware' => ['admin', 'verified']];

/* * ******  FAQ Section Start ********** */
Route::get('list-faq-sections', array_merge(['uses' => 'Admin\FaqSectionController@indexFaqSections'], $all_users))->name('list.faq.sections');
Route::get('create-faq-section', array_merge(['uses' => 'Admin\FaqSectionController@createFaqSection'], $all_users))->name('create.faq.section');
Route::post('store-faq-section', array_merge(['uses' => 'Admin\FaqSectionController@storeFaqSection'], $all_users))->name('store.faq.section');
Route::get('edit-faq-section/{id}', array_merge(['uses' => 'Admin\FaqSectionController@editFaqSection'], $all_users))->name('edit.faq.section');
Route::put('update-faq-section/{id}', array_merge(['uses' => 'Admin\FaqSectionController@updateFaqSection'], $all_users))->name('update.faq.section');
Route::delete('delete-faq-section', array_merge(['uses' => 'Admin\FaqSectionController@deleteFaqSection'], $all_users))->name('delete.faq.section');

Route::get('fetch-faq-sections', array_merge(['uses' => 'Admin\FaqSectionController@fetchFaqSectionsData'], $all_users))->name('fetch.data.faq.sections');
Route::get('sort-faq-sections', array_merge(['uses' => 'Admin\FaqSectionController@sortFaqSections'], $all_users))->name('sort.faq.sections');
Route::get('faq-section-sort-data', array_merge(['uses' => 'Admin\FaqSectionController@faqSectionSortData'], $all_users))->name('faq.section.sort.data');
Route::put('faq-section-sort-update', array_merge(['uses' => 'Admin\FaqSectionController@faqSectionSortUpdate'], $all_users))->name('faq.section.sort.update');

/* * ****** End FAQ Section ********** */
?>
