<?php

namespace App\Services\Scrapers;

use App\Models\Medo\Category;
use App\Models\Medo\City;
use App\Models\Medo\Employer;
use App\Models\Medo\Province;
use App\Job;
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

    private const MEDO_TO_LEGACY_CATEGORY = [
        1 => 655, // HCA
        2 => 656, // LPN
        3 => 657, // RN
    ];

    private const MEDO_TO_LEGACY_CITY = [
        2 => 10125, // Edmonton
        1 => 10107, // Calgary
        3 => 10169, // Red Deer
        4 => 10150, // Lethbridge
        5 => 10156, // Medicine Hat
        7 => 10135, // Grande Prairie
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
        
        $legacyCityId = self::MEDO_TO_LEGACY_CITY[$city->id] ?? 10125; // default edmonton
        $legacyFAId = self::MEDO_TO_LEGACY_CATEGORY[$category->id] ?? 655; // default hca

        $existing = Job::where('external_id', $row['external_id'])
            ->where('source', $sourceSlug)
            ->first();

        $payload = [
            'title'              => $row['title'],
            'description'        => $row['description'] ?? '',
            'medo_category_id'   => $category->id,
            'medo_province_id'   => $row['province_id'],
            'medo_city_id'       => $city->id,
            'medo_employer_id'   => $employer->id,
            'wage_min'           => $row['wage_min'] ?? null,
            'wage_max'           => $row['wage_max'] ?? null,
            'wage_period'        => $row['wage_period'] ?? null,
            'expiry_date'        => $row['expires_at'] ?? now()->addDays(60),
            'apply_url'          => $row['apply_url'] ?? null,
            'apply_type'         => 'external', // legacy field
            'is_active'          => 1, // legacy field
            'is_featured'        => 0, // legacy field
            'functional_area_id' => $legacyFAId, // legacy field
            'city_id'            => $legacyCityId, // legacy field
            'state_id'           => 663, // legacy field (Alberta)
            'country_id'         => 38, // legacy field (Canada)
        ];

        if ($existing) {
            $existing->update($payload);
            return 'updated';
        }

        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (Job::where('slug', $slug)->where('city_id', $legacyCityId)->exists()) {
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
        return 'hca'; // fallback
    }

    private function detectCity(string $locality): ?string
    {
        $locality = strtolower(trim($locality));
        return self::CITY_ALIASES[$locality] ?? 'edmonton'; // fallback
    }
}
