<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $table = 'referrals';
    
    protected $fillable = [
        'referrer_company_id',
        'referred_company_id',
        'referral_code',
        'referred_email',
        'status',
        'reward_jobs_given'
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Get the company who made the referral
     */
    public function referrerCompany()
    {
        return $this->belongsTo(Company::class, 'referrer_company_id');
    }

    /**
     * Get the company who was referred
     */
    public function referredCompany()
    {
        return $this->belongsTo(Company::class, 'referred_company_id');
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
