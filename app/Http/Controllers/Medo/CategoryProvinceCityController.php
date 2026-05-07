<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Models\Medo\Category;
use App\Models\Medo\Province;
use App\Models\Medo\City;
use App\Services\Medo\ContentGenerator;
use Illuminate\Support\Facades\Cache;

class CategoryProvinceCityController extends Controller
{
    public function show(
        Category $category,
        Province $province,
        City $city,
        ContentGenerator $content
    ) {
        // Province must be active (only AB at launch)
        if (!$province->is_active) {
            abort(404);
        }

        abort_unless($city->province_id === $province->id, 404);

        $jobs = Cache::remember(
            "jobs.{$category->slug}.{$province->slug}.{$city->slug}",
            now()->addHour(),
            fn() => $category->jobs()
                ->where('medo_city_id', $city->id)
                ->where('expiry_date', '>', now())
                ->with('medoEmployer')
                ->orderByDesc('created_at')
                ->get()
        );

        // Quality gate — no thin pages indexed
        if ($jobs->count() < 3) {
            return response()
                ->view('medo.jobs.thin-page', [
                    'category' => $category,
                    'province' => $province,
                    'city' => $city,
                    'jobCount' => $jobs->count(),
                ])
                ->header('X-Robots-Tag', 'noindex');
        }

        // Get province-specific category settings (union, college, wages)
        $settings = $category->settingsFor($province);

        $salaryRange = $settings ? $settings->wageRange() : ['min' => 0, 'max' => 0];
        $min = number_format($salaryRange['min'] ?: $salaryRange['max'], 2);
        $max = number_format($salaryRange['max'] ?: $salaryRange['min'], 2);
        $provCode = strtoupper($province->slug);
        
        $seoTitle = "{$category->name} Jobs in {$city->name}, {$provCode} | Medojob";
        $seoDesc = "{$jobs->count()} {$category->name} jobs in {$city->name}, {$province->name}. Updated daily. Wages from \${$min}–\${$max}/hr. Apply directly.";
        
        $seo = (object)[
            'seo_title' => $seoTitle,
            'seo_description' => $seoDesc,
            'seo_keywords' => "{$category->name} jobs, {$city->name} jobs, healthcare jobs, {$province->name}",
            'seo_other' => '
                <meta property="og:title" content="'.e($seoTitle).'"/>
                <meta property="og:description" content="'.e($seoDesc).'"/>
                <meta property="og:site_name" content="Medojob"/>
                <meta property="og:url" content="'.url()->current().'"/>
            '
        ];

        return response()
            ->view('medo.jobs.category-province-city', [
                'category' => $category,
                'province' => $province,
                'city' => $city,
                'settings' => $settings,
                'jobs' => $jobs,
                'jobCount' => $jobs->count(),
                'topEmployers' => $this->topEmployers($jobs),
                'salaryRange' => $salaryRange,
                'seo' => $seo,
                'intro' => $content->intro($category, $province, $city, $jobs, $settings),
                'faqs' => $content->faqs($category, $province, $city, $jobs, $settings),
                'relatedCities' => $city->nearby(75)->take(5),
                'relatedCategories' => $category->related(),
            ])
            ->header('Cache-Control', 'public, max-age=3600, stale-while-revalidate=86400');
    }

    private function topEmployers($jobs)
    {
        return $jobs->groupBy('medo_employer_id')
            ->map(fn($g) => [
                'employer' => $g->first()->medoEmployer,
                'count' => $g->count(),
            ])
            ->sortByDesc('count')
            ->take(5);
    }
}
