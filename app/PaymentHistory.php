<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    protected $table = 'payment_history';
    
    protected $fillable = [
        'company_id',
        'user_id',
        'user_type',
        'package_id',
        'package_type',
        'package_title',
        'package_price',
        'package_list_price',
        'payment_method',
        'assigned_by',
        'transaction_id',
        'package_start_date',
        'package_end_date',
        'jobs_quota',
        'cvs_quota',
        'payment_status'
    ];

    protected $dates = [
        'package_start_date',
        'package_end_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the company that owns this payment
     */
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    /**
     * Get the jobseeker that owns this payment
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Get the package for this payment
     */
    public function package()
    {
        return $this->belongsTo('App\Package', 'package_id');
    }

    /**
     * Get the admin who assigned this package
     */
    public function assignedBy()
    {
        return $this->belongsTo('App\Admin', 'assigned_by');
    }

    /**
     * Scope to get only company transactions
     */
    public function scopeCompanyTransactions($query)
    {
        return $query->where('user_type', 'company');
    }

    /**
     * Scope to get only jobseeker transactions
     */
    public function scopeJobseekerTransactions($query)
    {
        return $query->where('user_type', 'jobseeker');
    }

    /**
     * Scope to get only completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope to get only pending payments
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope to get only failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if payment was admin assigned
     */
    public function isAdminAssigned()
    {
        return !empty($this->assigned_by) || $this->payment_method === 'Admin Assign';
    }
}

