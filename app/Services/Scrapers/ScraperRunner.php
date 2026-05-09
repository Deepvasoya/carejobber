<?php

namespace App\Services\Scrapers;

use App\Models\Medo\Category;
use App\Models\Medo\City;
use App\Models\Medo\Employer;
use App\Models\Medo\Job;
use App\Models\Medo\Province;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScraperRunner
{
    private const CATEGORY_KEYWORDS = [
        'hca' => ['hca', 'health care aide', 'healthcare aide', 'personal care aide', 'care aide', 'homecare', 'home care'],
        'lpn' => ['lpn', 'licensed practical nurse', 'practical nurse'],
        'rn'  => ['rn', 'registered nurse'],
    ];

    private const CITY_ALIASES = [
        'calgary'        => 'calgary',
        'edmonton'       => 'edmonton',
        'red deer'       => 'red-deer',
        'lethbridge'     => 'lethbridge',
        'medicine hat'   => 'medicine-hat',
        'fort mcmurray'  => 'fort-mcmurray',
        'grande prairie' => 'grande-prairie',
        'airdrie'        => 'airdrie',
        'st. albert'     => 'st-albert',
        'st albert'      => 'st-albert',
        'sherwood park'  => 'sherwood-park',
        'lloydminster'   => 'lloydminster',
        'camrose'        => 'camrose',
        'cochrane'       => 'cochrane',
        'okotoks'        => 'okotoks',
        'spruce grove'   => 'spruce-grove',
    ];

    private array $employerCache = [];

    /**
     * Run the given scraper pipeline.
     */
    public function run(BaseScraper $scraper, string $sourceSlug, bool $dryRun = false): array
    {
        $stats = [
            'discovered' => 0,
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
            'imported_list' => [],
            'skipped_list' => []
        ];

        try {
            $rawJobs = $scraper->fetchJobs();
            $stats['discovered'] = count($rawJobs);

            foreach ($rawJobs as $jobData) {
                try {
                    $row = $this->normalise($jobData);
                    if (! $row) {
                        $stats['skipped']++;
                        $stats['skipped_list'][] = ['url' => $jobData['apply_url'] ?? '', 'title' => $jobData['title'] ?? '', 'reason' => 'Failed normalisation (category/city mismatch)'];
                        continue;
                    }

                    if ($dryRun) {
                        $stats['imported']++;
                        $stats['imported_list'][] = ['url' => $row['apply_url'], 'title' => $row['title']];
                        continue;
                    }

                    $result = $this->upsert($row, $sourceSlug);
                    $stats[$result]++;

                    if ($result === 'imported' || $result === 'updated') {
                        $stats['imported_list'][] = ['url' => $row['apply_url'], 'title' => $row['title'], 'status' => $result];
                        $provinceSlug = Province::find($row['province_id'])->slug ?? 'ab';
                        Cache::forget("jobs.{$row['category_slug']}.{$provinceSlug}.{$row['city_slug']}");
                    } else {
                        $stats['skipped_list'][] = ['url' => $row['apply_url'], 'title' => $row['title'], 'reason' => 'Skipped during upsert'];
                    }

                } catch (\Throwable $e) {
                    $stats['errors'][] = ($jobData['apply_url'] ?? '') . ': ' . $e->getMessage();
                    Log::error('[ScraperRunner] Error processing job: ' . $e->getMessage());
                }
            }

        } catch (\Throwable $e) {
            $stats['errors'][] = 'Critical Scraper Error: ' . $e->getMessage();
            Log::error('[ScraperRunner] Critical Error: ' . $e->getMessage());
        }

        return $stats;
    }

    private function normalise(array $data): ?array
    {
        $title = strip_tags($data['title'] ?? '');
        $description = strip_tags($data['description'] ?? '');

        if (! $title) {
            return null;
        }

        // Category detection
        $categorySlug = $this->detectCategory($data['category_hint'] ?? $title . ' ' . $description);
        if (! $categorySlug) {
            return null;
        }

        // Location detection
        $citySlug = $this->detectCity($data['city_hint'] ?? '');
        if (! $citySlug) {
            return null;
        }

        $data['category_slug'] = $categorySlug;
        $data['city_slug'] = $citySlug;

        return $data;
    }

    private function upsert(array $row, string $sourceSlug): string
    {
        $category = Category::where('slug', $row['category_slug'])->first();
        $city = City::where('slug', $row['city_slug'])->where('province_id', $row['province_id'])->first();

        if (! $category || ! $city) {
            return 'skipped';
        }

        // Employer
        $employer = $this->resolveEmployer($row['employer_name'] ?? 'Confidential', $row['employer_url'] ?? null, $row['province_id']);

        $slug = Str::slug($row['title'] . '-' . $city->slug);
        
        $existing = Job::where('external_id', $row['external_id'])
            ->where('source', $sourceSlug)
            ->first();

        $payload = [
            'title' => $row['title'],
            'description' => $row['description'] ?? '',
            'category_id' => $category->id,
            'province_id' => $row['province_id'],
            'city_id' => $city->id,
            'employer_id' => $employer->id,
            'employment_type' => $row['employment_type'] ?? null,
            'shift_type' => $row['shift_type'] ?? null,
            'setting' => $row['setting'] ?? null,
            'wage_min' => $row['wage_min'] ?? null,
            'wage_max' => $row['wage_max'] ?? null,
            'wage_period' => $row['wage_period'] ?? null,
            'posted_at' => $row['posted_at'] ?? now(),
            'expires_at' => $row['expires_at'] ?? now()->addDays(60),
            'apply_url' => $row['apply_url'] ?? null,
            'is_new_grad_friendly' => (bool) ($row['is_new_grad_friendly'] ?? false),
            'has_signing_bonus' => (bool) ($row['has_signing_bonus'] ?? false),
        ];

        if ($existing) {
            $existing->update($payload);
            return 'updated';
        }

        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (Job::where('slug', $slug)->where('city_id', $city->id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        Job::create(array_merge($payload, [
            'external_id' => $row['external_id'],
            'source'      => $sourceSlug,
            'slug'        => $slug,
        ]));

        return 'imported';
    }

    private function resolveEmployer(string $name, ?string $website, int $provinceId): Employer
    {
        $key = Str::slug($name) ?: 'unknown';

        if (isset($this->employerCache[$key])) {
            return $this->employerCache[$key];
        }

        $employer = Employer::firstOrCreate(
            ['slug' => $key],
            [
                'name'        => $name ?: 'Unknown Employer',
                'type'        => 'agency',
                'province_id' => $provinceId,
                'website'     => $website,
            ]
        );

        $this->employerCache[$key] = $employer;
        return $employer;
    }

    private function detectCategory(string $text): ?string
    {
        $text = strtolower($text);
        foreach (self::CATEGORY_KEYWORDS as $slug => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) {
                    return $slug;
                }
            }
        }
        return null;
    }

    private function detectCity(string $locality): ?string
    {
        $locality = strtolower(trim($locality));
        return self::CITY_ALIASES[$locality] ?? null;
    }
}
