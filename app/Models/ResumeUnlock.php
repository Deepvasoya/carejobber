<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeUnlock extends Model
{
    use HasFactory;

    protected $table = 'resume_unlocks';
    protected $fillable = [
        'company_id',
        'user_id',
        'paid_amount',
        'currency',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'payment_method',
        'unlocked_at',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'unlocked_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Company::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public static function isUnlockedBy($userId, $companyId): bool
    {
        return self::where('user_id', $userId)
            ->where('company_id', $companyId)
            ->exists();
    }
}
