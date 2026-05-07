<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Models\Medo\Category;

class CategoryController extends Controller
{
    public function show(Category $medo_category)
    {
        $provinces = \App\Models\Medo\Province::where('is_active', true)->whereHas('cities.jobs', function ($query) use ($medo_category) {
            $query->where('category_id', $medo_category->id)->where('expires_at', '>', now());
        })->get();

        return view('medo.jobs.category', [
            'category' => $medo_category,
            'provinces' => $provinces,
        ]);
    }
}
