<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Models\Medo\Category;
use App\Models\Medo\Province;

class CategoryProvinceController extends Controller
{
    public function show(Category $medo_category, Province $medo_province)
    {
        if (!$medo_province->is_active) {
            abort(404);
        }

        $cities = $medo_province->cities()->whereHas('jobs', function ($query) use ($medo_category) {
            $query->where('category_id', $medo_category->id)->where('expires_at', '>', now());
        })->get();

        return view('medo.jobs.category-province', [
            'category' => $medo_category,
            'province' => $medo_province,
            'cities' => $cities,
        ]);
    }
}
