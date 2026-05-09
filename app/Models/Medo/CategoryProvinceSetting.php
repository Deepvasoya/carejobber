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
        $grid = SalaryGrid::where('category_id', $this->category_id)
            ->where('province_id', $this->province_id)
            ->selectRaw('MIN(hourly_rate) as min_rate, MAX(hourly_rate) as max_rate')
            ->first();

        if ($grid && ($grid->min_rate || $grid->max_rate)) {
            return [
                'min' => (float) $grid->min_rate,
                'max' => (float) $grid->max_rate,
            ];
        }

        return [
            'min' => $this->wage_min,
            'max' => $this->wage_max,
        ];
    }
}
