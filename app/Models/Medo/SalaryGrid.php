<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryGrid extends Model
{
    use HasFactory;

    protected $table = 'medo_salary_grids';

    protected $guarded = [];

    protected $casts = [
        'effective_date' => 'date',
    ];

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
}
