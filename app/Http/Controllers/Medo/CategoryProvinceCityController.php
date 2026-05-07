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
        Category $medo_category,
        Province $medo_province,
        City $medo_city,
        ContentGenerator $content
    ) {
        // Province must be active (only AB at launch)
        if (!$medo_province->is_active) {
            abort(404);
        }

        abort_unless($medo_city->province_id === $medo_province->id, 404);

        $jobs = Cache::remember(
            "jobs.{$medo_category->slug}.{$medo_province->slug}.{$medo_city->slug}",
            now()->addHour(),
            fn() => $medo_category->jobs()
                ->where('city_id', $medo_city->id)
                ->where('expires_at', '>', now())
                ->with('employer')
                ->orderByDesc('posted_at')
                ->get()
        );

        // Quality gate — no thin pages indexed
        if ($jobs->count() < 3) {
            return response()
                ->view('medo.jobs.thin-page', [
                    'category' => $medo_category,
                    'province' => $medo_province,
                    'city' => $medo_city,
                    'jobCount' => $jobs->count(),
                ])
                ->header('X-Robots-Tag', 'noindex');
        }

        // Get province-specific category settings (union, college, wages)
        $settings = $medo_category->settingsFor($medo_province);

        return view('medo.jobs.category-province-city', [
            'category' => $medo_category,
            'province' => $medo_province,
            'city' => $medo_city,
            'settings' => $settings,
            'jobs' => $jobs,
            'jobCount' => $jobs->count(),
            'topEmployers' => $this->topEmployers($jobs),
            'salaryRange' => $settings ? $settings->wageRange() : ['min' => 0, 'max' => 0],
            'intro' => $content->intro($medo_category, $medo_province, $medo_city, $jobs, $settings),
            'faqs' => $content->faqs($medo_category, $medo_province, $medo_city, $jobs, $settings),
            'relatedCities' => $medo_city->nearby(75)->take(5),
            'relatedCategories' => $medo_category->related(),
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
