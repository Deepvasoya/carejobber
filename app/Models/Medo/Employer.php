<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory;

    protected $table = 'medo_employers';

    protected $guarded = [];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function healthAuthority()
    {
        return $this->belongsTo(HealthAuthority::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
