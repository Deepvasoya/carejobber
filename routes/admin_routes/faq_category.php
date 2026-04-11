<?php

/* * ******  FAQ Category Start ********** */
Route::get('list-faq-categories', array_merge(['uses' => 'Admin\FaqCategoryController@indexFaqCategories'], $all_users))->name('list.faq.categories');
Route::get('create-faq-category', array_merge(['uses' => 'Admin\FaqCategoryController@createFaqCategory'], $all_users))->name('create.faq.category');
Route::post('store-faq-category', array_merge(['uses' => 'Admin\FaqCategoryController@storeFaqCategory'], $all_users))->name('store.faq.category');
Route::get('edit-faq-category/{id}', array_merge(['uses' => 'Admin\FaqCategoryController@editFaqCategory'], $all_users))->name('edit.faq.category');
Route::put('update-faq-category/{id}', array_merge(['uses' => 'Admin\FaqCategoryController@updateFaqCategory'], $all_users))->name('update.faq.category');
Route::delete('delete-faq-category', array_merge(['uses' => 'Admin\FaqCategoryController@deleteFaqCategory'], $all_users))->name('delete.faq.category');
Route::get('fetch-faq-categories', array_merge(['uses' => 'Admin\FaqCategoryController@fetchFaqCategoriesData'], $all_users))->name('fetch.data.faq.categories');
Route::get('sort-faq-categories', array_merge(['uses' => 'Admin\FaqCategoryController@sortFaqCategories'], $all_users))->name('sort.faq.categories');
Route::get('faq-category-sort-data', array_merge(['uses' => 'Admin\FaqCategoryController@faqCategorySortData'], $all_users))->name('faq.category.sort.data');
Route::put('faq-category-sort-update', array_merge(['uses' => 'Admin\FaqCategoryController@faqCategorySortUpdate'], $all_users))->name('faq.category.sort.update');
/* * ****** End FAQ Category ********** */
?>
