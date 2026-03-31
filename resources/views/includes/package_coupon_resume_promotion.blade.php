@include('includes.package_coupon_card', [
    'applyRoute' => 'resume.promotion.apply.coupon',
    'clearRoute' => 'resume.promotion.clear.coupon',
    'appliedCoupon' => session('resume_promotion_coupon_code'),
    'ribbonSubtitle' => __('Apply before you pay with Stripe'),
    'footerNote' => __('Valid codes lower the price at checkout for resume promotion packages when the coupon is active and allowed for this product.'),
    'includeActiveOffersList' => true,
    'portalCoupons' => \App\PackageCoupon::activeForResumePromotionPortalDisplay(),
    'emptyOffersMessage' => __('No coupons listed here yet. In admin, set audience to “Resume promotion” or “Any”, and optionally limit to specific promotion durations.'),
])
