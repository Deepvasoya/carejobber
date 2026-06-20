<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyClaimRequest extends Model
{
    protected $table = 'company_claim_requests';
    
    protected $fillable = [
        'company_id',
        'user_id',
        'status',
        'message',
        'admin_notes',
        'requested_at',
        'reviewed_at',
        'reviewed_by',
    ];
    
    protected $dates = [
        'requested_at',
        'reviewed_at',
        'created_at',
        'updated_at',
    ];
    
    /**
     * Get the company associated with this claim request
     */
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }
    
    /**
     * Get the user who requested to claim the company
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    /**
     * Get the admin who reviewed the claim request
     */
    public function reviewer()
    {
        return $this->belongsTo('App\Admin', 'reviewed_by');
    }
    
    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
