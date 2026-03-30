@include('includes.package_coupon_card', [
    'applyRoute' => 'user.package.apply.coupon',
    'clearRoute' => 'user.package.clear.coupon',
    'appliedCoupon' => session('jobseeker_package_coupon_code'),
    'ribbonSubtitle' => __('Apply before you pay with Stripe'),
    'footerNote' => __('Valid codes reduce the price on this Pay with Stripe page when the coupon applies to the package you buy (applications package or featured profile).'),
    'includeActiveOffersList' => true,
    'portalCoupons' => \App\PackageCoupon::activeForJobSeekerPortalDisplay(),
    'emptyOffersMessage' => __('No coupons listed here yet. In admin, create an active coupon with audience “Job seeker”, “Make featured”, or “Any”—not employer-only.'),
    'appliedHelpText' => __('Discount applies when you pay with Stripe, if the coupon is valid for the package you choose.'),
])
