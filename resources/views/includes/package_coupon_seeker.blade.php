@include('includes.package_coupon_card', [
    'applyRoute' => 'user.package.apply.coupon',
    'clearRoute' => 'user.package.clear.coupon',
    'appliedCoupon' => session('jobseeker_package_coupon_code'),
    'ribbonSubtitle' => __('Apply before you pay with Stripe'),
    'footerNote' => __('Valid codes reduce the price at Stripe checkout when the coupon applies to your selected package.'),
    'includeActiveOffersList' => false,
    'appliedHelpText' => __('Discount applies when you pay with Stripe, if the coupon is valid for the package you choose.'),
])
