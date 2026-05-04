<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'medo_provinces';

    protected $guarded = [];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
