<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageCoupon extends Model
{
    protected $table = 'package_coupons';

    protected $guarded = ['id'];

    protected $casts = [
        'discount_value' => 'float',
        'max_discount_amount' => 'float',
        'min_package_price' => 'float',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'package_ids' => 'array',
        'allow_subscription_packages' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(PackageCouponRedemption::class, 'package_coupon_id');
    }

    public static function normalizeCode(?string $code): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim((string) $code)));
    }

    public function scopeActive($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }
}
