<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'packages';
    public $timestamps = true;
    protected $guarded = ['id'];
    protected $casts = [
        'package_price' => 'float',
        'package_num_days' => 'integer',
        'package_num_listings' => 'integer',
        'rebate_percent' => 'integer',
        'is_active' => 'boolean',
    ];

    const TYPE_ONE_TIME_CREDITS = 'one_time_credits';
    const TYPE_MONTHLY_RECURRING = 'monthly_recurring';
    const TYPE_RESUME_BOOST = 'resume_boost';

    public function scopeForEmployer($q)
    {
        return $q->where('package_for', 'employer');
    }

    public function scopeCreditsPackages($q)
    {
        return $q->where('type', self::TYPE_ONE_TIME_CREDITS);
    }

    public function scopeSubscriptions($q)
    {
        return $q->where('type', self::TYPE_MONTHLY_RECURRING);
    }
}
