<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthAuthority extends Model
{
    use HasFactory;

    protected $table = 'medo_health_authorities';

    protected $guarded = [];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
