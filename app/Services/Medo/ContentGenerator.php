<?php

namespace App\Services\Medo;

use App\Models\Medo\Category;
use App\Models\Medo\Province;
use App\Models\Medo\City;
use App\Models\Medo\Job;
use App\Models\Medo\CategoryProvinceSetting;
use Illuminate\Support\Collection;

class ContentGenerator
{
    public function intro(Category $category, Province $province, City $city, Collection $jobs, ?CategoryProvinceSetting $settings)
    {
        // Simple stub for Phase 1. Will be expanded in Phase 3.
        return "Find the latest {$category->name} jobs in {$city->name}, {$province->name}. We have {$jobs->count()} active positions available.";
    }

    public function faqs(Category $category, Province $province, City $city, Collection $jobs, ?CategoryProvinceSetting $settings)
    {
        // Simple stub for Phase 1. Will be expanded in Phase 3.
        return [];
    }
}
