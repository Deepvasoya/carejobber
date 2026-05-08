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

        $seoTitle = "{$category->name} Jobs in {$province->name} | Medojob";
        $seoDesc = "Find {$category->name} jobs in {$province->name}. Browse active listings by city. Updated daily.";
        
        $seo = (object)[
            'seo_title' => $seoTitle,
            'seo_description' => $seoDesc,
            'seo_keywords' => "{$category->name} jobs, {$province->name} jobs, healthcare jobs",
            'seo_other' => '
                <meta property="og:type" content="website"/>
                <meta property="og:title" content="'.e($seoTitle).'"/>
                <meta property="og:description" content="'.e($seoDesc).'"/>
                <meta property="og:site_name" content="Medojob"/>
                <meta property="og:url" content="'.url()->current().'"/>
            '
        ];

        return view('medo.jobs.category-province', [
            'category' => $category,
            'province' => $province,
            'cities' => $cities,
            'seo' => $seo,
        ]);
    }
}
