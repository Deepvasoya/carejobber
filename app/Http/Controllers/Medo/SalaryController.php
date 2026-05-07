<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Models\Medo\Category;
use App\Models\Medo\Province;

class SalaryController extends Controller
{
    public function show(Category $category, Province $province)
    {
        if (!$province->is_active) {
            abort(404);
        }

        $query = $category->jobs()
            ->where('province_id', $province->id)
            ->where('expires_at', '>', now())
            ->whereNotNull('wage_min')
            ->whereNotNull('wage_max');

        $stats = (clone $query)
            ->selectRaw('COUNT(*) as salary_count, AVG(wage_min) as avg_min, AVG(wage_max) as avg_max, MIN(wage_min) as min_wage, MAX(wage_max) as max_wage')
            ->first();

        return view('medo.salary.show', [
            'category' => $category,
            'province' => $province,
            'salary' => [
                'count' => (int) ($stats->salary_count ?? 0),
                'avg_min' => round((float) ($stats->avg_min ?? 0), 2),
                'avg_max' => round((float) ($stats->avg_max ?? 0), 2),
                'min' => round((float) ($stats->min_wage ?? 0), 2),
                'max' => round((float) ($stats->max_wage ?? 0), 2),
            ],
        ]);
    }
}
