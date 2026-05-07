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
                ->where('city_id', $city->id)
                ->where('expires_at', '>', now())
                ->with('employer')
                ->orderByDesc('posted_at')
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

        return view('medo.jobs.category-province-city', [
            'category' => $category,
            'province' => $province,
            'city' => $city,
            'settings' => $settings,
            'jobs' => $jobs,
            'jobCount' => $jobs->count(),
            'topEmployers' => $this->topEmployers($jobs),
            'salaryRange' => $settings ? $settings->wageRange() : ['min' => 0, 'max' => 0],
            'intro' => $content->intro($category, $province, $city, $jobs, $settings),
            'faqs' => $content->faqs($category, $province, $city, $jobs, $settings),
            'relatedCities' => $city->nearby(75)->take(5),
            'relatedCategories' => $category->related(),
        ]);
    }

    private function topEmployers($jobs)
    {
        return $jobs->groupBy('employer_id')
            ->map(fn($g) => [
                'employer' => $g->first()->employer,
                'count' => $g->count(),
            ])
            ->sortByDesc('count')
            ->take(5);
    }
}
