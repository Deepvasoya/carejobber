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

    /**
     * Coupons employers may use (job posting packages, CV search, etc.) — for portal display.
     */
    public function scopeForEmployerPortal($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('package_for_scope')
                ->orWhere('package_for_scope', '')
                ->orWhereIn('package_for_scope', ['employer', 'cv_search']);
        });
    }

    /**
     * Coupons job seekers may see (CV packages, featured profile, etc.) — for portal display.
     */
    public function scopeForJobSeekerPortal($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('package_for_scope')
                ->orWhere('package_for_scope', '')
                ->orWhereIn('package_for_scope', ['job_seeker', 'make_featured']);
        });
    }

    /**
     * @return \Illuminate\Support\Collection<int, static>
     */
    public static function activeForEmployerPortalDisplay()
    {
        $coupons = static::query()
            ->active()
            ->forEmployerPortal()
            ->withCount('redemptions')
            ->orderBy('code')
            ->get();

        return $coupons->filter(function (self $c) {
            if ($c->usage_limit_total === null) {
                return true;
            }

            return (int) $c->redemptions_count < (int) $c->usage_limit_total;
        })->values();
    }

    /**
     * @return \Illuminate\Support\Collection<int, static>
     */
    public static function activeForJobSeekerPortalDisplay()
    {
        $coupons = static::query()
            ->active()
            ->forJobSeekerPortal()
            ->withCount('redemptions')
            ->orderBy('code')
            ->get();

        return $coupons->filter(function (self $c) {
            if ($c->usage_limit_total === null) {
                return true;
            }

            return (int) $c->redemptions_count < (int) $c->usage_limit_total;
        })->values();
    }
}
