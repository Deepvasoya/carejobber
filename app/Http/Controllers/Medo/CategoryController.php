<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Models\Medo\Category;
use App\Models\Medo\Province;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $provinces = Province::where('is_active', true)->whereHas('cities.jobs', function ($query) use ($category) {
            $query->where('category_id', $category->id)->where('expires_at', '>', now());
        })->get();

        return view('medo.jobs.category', [
            'category' => $category,
            'provinces' => $provinces,
        ]);
    }
}
