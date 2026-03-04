<?php
use App\Http\Controllers\OrderController;


/* * ******** OrderController ************ */
Route::get('order-free-package/{id}', 'OrderController@orderFreePackage')->name('order.free.package');

Route::get('order-package/{id}', 'OrderController@orderPackage')->name('order.package');
Route::get('order-upgrade-package/{id}', 'OrderController@orderUpgradePackage')->name('order.upgrade.package');
Route::get('paypal-payment-status/{id}', 'OrderController@getPaymentStatus')->name('payment.status');
Route::get('paypal-upgrade-payment-status/{id}', 'OrderController@getUpgradePaymentStatus')->name('upgrade.payment.status');

Route::get('/paypal/order', [OrderController::class, 'createOrder']);
Route::post('/paypal/order/{orderId}/capture', [OrderController::class, 'captureOrder'])->name('captureOrder.payment');



Route::get('stripe-order-form/{id}/{new_or_upgrade}', 'StripeOrderController@stripeOrderForm')->name('stripe.order.form');
Route::post('stripe-order-package', 'StripeOrderController@stripeOrderPackage')->name('stripe.order.package');
Route::post('stripe-order-upgrade-package', 'StripeOrderController@stripeOrderUpgradePackage')->name('stripe.order.upgrade.package');

Route::get('razorpay-order-form/{id}/{new_or_upgrade}', 'RazorpayOrderController@razorpayOrderForm')->name('razorpay.order.form');
Route::post('razorpay-order-package', 'RazorpayOrderController@razorpayOrderPackage')->name('razorpay.order.package');
Route::post('razorpay-verify-payment', 'RazorpayOrderController@verifyRazorpayPayment')->name('razorpay.verify.payment');



Route::get('payu-order-package', 'PayuController@orderPackage')->name('payu.order.package');
Route::get('payu-order-package-status/', 'PayuController@orderPackageStatus')->name('payu.order.package.status');
Route::get('payu-order-cvsearch-package', 'PayuController@orderCvSearchPackage')->name('payu.order.cvsearch.package');
Route::get('payu-order-package.cvsearch-status/', 'PayuController@orderPackageCvSearchStatus')->name('payu.order.package.cvsearch.status');

Route::get('paystack-order-form/{package_id}/{new_or_upgrade}', ['as' => 'paystack.order.form', 'uses' => 'PaystackOrderController@paystackOrderForm']);
Route::post('paystack-order-package', ['as' => 'paystack.order.package', 'uses' => 'PaystackOrderController@paystackOrderPackage']);
Route::post('paystack-order-upgrade-package', ['as' => 'paystack.order.upgrade.package', 'uses' => 'PaystackOrderController@paystackOrderUpgradePackage']);

Route::get('iyzico-order-form/{package_id}/{new_or_upgrade}', ['as' => 'iyzico.order.form', 'uses' => 'IyzicoOrderController@iyzicoOrderForm']);
Route::post('iyzico-order-package', ['as' => 'iyzico.order.package', 'uses' => 'IyzicoOrderController@iyzicoOrderPackage']);
Route::post('iyzico-order-upgrade-package', ['as' => 'iyzico.order.upgrade.package', 'uses' => 'IyzicoOrderController@iyzicoOrderUpgradePackage']);
Route::post('iyzico-callback', ['as' => 'iyzico.callback', 'uses' => 'IyzicoOrderController@iyzicoCallback']);
Route::get('iyzico-callback', ['as' => 'iyzico.callback.get', 'uses' => 'IyzicoOrderController@iyzicoCallback']);