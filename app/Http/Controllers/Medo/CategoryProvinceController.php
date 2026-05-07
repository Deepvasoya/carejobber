<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Models\Medo\Category;
use App\Models\Medo\Province;

class CategoryProvinceController extends Controller
{
    public function show(Category $category, Province $province)
    {
        if (!$province->is_active) {
            abort(404);
        }

        $cities = $province->cities()->whereHas('jobs', function ($query) use ($category) {
            $query->where('category_id', $category->id)->where('expires_at', '>', now());
        })->get();

        return view('medo.jobs.category-province', [
            'category' => $category,
            'province' => $province,
            'cities' => $cities,
        ]);
    }
}
