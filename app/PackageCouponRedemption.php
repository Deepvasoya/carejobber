<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageCouponRedemption extends Model
{
    public $timestamps = false;

    protected $table = 'package_coupon_redemptions';

    protected $guarded = ['id'];

    protected $casts = [
        'discount_amount' => 'float',
        'amount_paid' => 'float',
        'created_at' => 'datetime',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(PackageCoupon::class, 'package_coupon_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
