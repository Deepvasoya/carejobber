<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = 'medo_jobs';

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'posted_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_new_grad_friendly' => 'boolean',
        'has_signing_bonus' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
