<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumePromotionPackage extends Model
{
    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'currency',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
