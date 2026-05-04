<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulatoryCollege extends Model
{
    use HasFactory;

    protected $table = 'medo_regulatory_colleges';

    protected $guarded = [];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
