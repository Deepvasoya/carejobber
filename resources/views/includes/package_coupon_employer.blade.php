@include('includes.package_coupon_card', [
    'applyRoute' => 'recruiter.posting.apply.coupon',
    'clearRoute' => 'recruiter.posting.clear.coupon',
    'appliedCoupon' => session('employer_package_coupon_code'),
    'ribbonSubtitle' => __('Save on job posting or CV packages at checkout'),
    'footerNote' => __('Valid codes reduce the price at Stripe checkout when the coupon applies to the package you buy (job posting, CV search, etc.). Subscriptions with a fixed Stripe Price ID may not support these coupons—use Stripe promotions or a custom-priced package.'),
    'includeActiveOffersList' => true,
    'portalCoupons' => \App\PackageCoupon::activeForEmployerPortalDisplay(),
    'emptyOffersMessage' => __('No coupons listed here yet. Admin coupons must be active, in date, and set to Employer, CV search, or “any” scope—not job seeker only.'),
    'couponApplyContext' => $couponApplyContext ?? null,
])
