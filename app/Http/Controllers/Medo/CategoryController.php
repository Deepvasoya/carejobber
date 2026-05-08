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

        $seoTitle = "{$category->name} Jobs in Canada | Medojob";
        $seoDesc = "Find {$category->name} jobs across Canada. Browse active listings by province and city. Updated daily.";
        
        $seo = (object)[
            'seo_title' => $seoTitle,
            'seo_description' => $seoDesc,
            'seo_keywords' => "{$category->name} jobs, healthcare jobs, Canada",
            'seo_other' => '
                <meta property="og:type" content="website"/>
                <meta property="og:title" content="'.e($seoTitle).'"/>
                <meta property="og:description" content="'.e($seoDesc).'"/>
                <meta property="og:site_name" content="Medojob"/>
                <meta property="og:url" content="'.url()->current().'"/>
            '
        ];

        return view('medo.jobs.category', [
            'category' => $category,
            'provinces' => $provinces,
            'seo' => $seo,
        ]);
    }
}
