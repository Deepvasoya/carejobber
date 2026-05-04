<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProvinceSetting extends Model
{
    use HasFactory;

    protected $table = 'medo_category_province_settings';

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function union()
    {
        return $this->belongsTo(Union::class);
    }

    public function regulatoryCollege()
    {
        return $this->belongsTo(RegulatoryCollege::class);
    }

    public function wageRange()
    {
        return [
            'min' => $this->wage_min,
            'max' => $this->wage_max,
        ];
    }
}
