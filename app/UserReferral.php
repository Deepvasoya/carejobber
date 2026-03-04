<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReferral extends Model
{
    protected $table = 'user_referrals';
    
    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'referral_code',
        'referred_email',
        'status',
        'reward_days_given'
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the user who made the referral
     */
    public function referrerUser()
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    /**
     * Get the user who was referred
     */
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * Scope for pending referrals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for registered referrals
     */
    public function scopeRegistered($query)
    {
        return $query->where('status', 'registered');
    }

    /**
     * Scope for rewarded referrals
     */
    public function scopeRewarded($query)
    {
        return $query->where('status', 'rewarded');
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadge()
    {
        switch ($this->status) {
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            case 'registered':
                return '<span class="badge badge-info">Registered</span>';
            case 'rewarded':
                return '<span class="badge badge-success">Rewarded</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }
}
