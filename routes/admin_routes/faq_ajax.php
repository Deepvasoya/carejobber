<?php

/* * ******  FAQ AJAX Routes ********** */
Route::get('get-faq-categories-by-lang', array_merge(['uses' => 'Admin\FaqController@getCategoriesByLang'], $all_users))->name('get.faq.categories.by.lang');
/* * ****** End FAQ AJAX Routes ********** */
?>
