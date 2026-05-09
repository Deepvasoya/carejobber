<?php

namespace App\Services\Medo\Scrapers;

use App\Models\Medo\Category;
use App\Models\Medo\City;
use App\Models\Medo\Employer;
use App\Models\Medo\Job;
use App\Models\Medo\Province;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Scraper adapter for provider type "sitemap".
 *
 * The source_url in job_feed_sources should point to a sitemap.xml
 * (or sitemap index) that lists individual job-detail page URLs.
 * Each job page must emit a schema.org/JobPosting JSON-LD block.
 */
class SitemapScraper
{
    // Keyword → medo_categories.slug mapping (expand as categories grow)
    private const CATEGORY_KEYWORDS = [
        'hca'                  => ['hca', 'health care aide', 'healthcare aide', 'personal care aide',
                                   'care aide', 'homecare', 'home care'],
        'lpn'                  => ['lpn', 'licensed practical nurse', 'practical nurse'],
        'rn'                   => ['rn', 'registered nurse'],
    ];

    // City name variations → medo_cities.slug mapping (AB only at launch)
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

    private Province $alberta;
    /** @var Category[] keyed by slug */
    private array $categories = [];
    /** @var City[] keyed by slug */
    private array $cities = [];
    /** @var Employer[] keyed by normalised name */
    private array $employerCache = [];

    public function __construct()
    {
        $this->alberta    = Province::where('slug', 'ab')->firstOrFail();
        $this->categories = Category::all()->keyBy('slug')->toArray();
        $this->cities     = City::where('province_id', $this->alberta->id)->get()->keyBy('slug')->toArray();
    }

    /**
     * @param  string $sitemapUrl
     * @param  bool   $dryRun
     * @return array{discovered:int, imported:int, updated:int, skipped:int, errors:list<string>}
     */
    public function run(string $sitemapUrl, bool $dryRun = false): array
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

        $urls = $this->fetchSitemapUrls($sitemapUrl);
        $stats['discovered'] = count($urls);

        foreach ($urls as $pageUrl) {
            try {
                $jsonLd = $this->fetchJobJsonLd($pageUrl);
                if (! $jsonLd) {
                    $stats['skipped']++;
                    $stats['skipped_list'][] = ['url' => $pageUrl, 'reason' => 'No JSON-LD found'];
                    continue;
                }

                $row = $this->normalise($jsonLd, $pageUrl);
                if (! $row) {
                    $stats['skipped']++;
                    $title = strip_tags($jsonLd['title'] ?? '');
                    $stats['skipped_list'][] = ['url' => $pageUrl, 'title' => $title, 'reason' => 'Failed normalisation (category/city mismatch)'];
                    continue;
                }

                if ($dryRun) {
                    $stats['imported']++;
                    $stats['imported_list'][] = ['url' => $pageUrl, 'title' => $row['title']];
                    Log::info('[SitemapScraper][dry-run] Would import: ' . $row['title'] . ' in ' . ($row['city_slug'] ?? '?'));
                    continue;
                }

                $result = $this->upsert($row);
                $stats[$result]++;
                if ($result === 'imported' || $result === 'updated') {
                    $stats['imported_list'][] = ['url' => $pageUrl, 'title' => $row['title'], 'status' => $result];
                    Cache::forget("jobs.{$row['category_slug']}.{$this->alberta->slug}.{$row['city_slug']}");
                } else {
                    $stats['skipped_list'][] = ['url' => $pageUrl, 'title' => $row['title'], 'reason' => 'Skipped during upsert'];
                }
            } catch (\Throwable $e) {
                $stats['errors'][] = $pageUrl . ': ' . $e->getMessage();
                Log::error('[SitemapScraper] ' . $pageUrl . ': ' . $e->getMessage());
            }

            // Rate limit: 1 request per 2 seconds
            usleep(2000000);
        }

        return $stats;
    }

    // ─── Private helpers ────────────────────────────────────────────────────

    private function fetchSitemapUrls(string $sitemapUrl): array
    {
        $response = Http::timeout(20)
            ->withHeaders(['User-Agent' => 'Medojob-Scraper/1.0'])
            ->get($sitemapUrl);

        if (! $response->ok()) {
            throw new \RuntimeException("Sitemap unreachable ({$response->status()}): {$sitemapUrl}");
        }

        $xml  = simplexml_load_string($response->body());
        $urls = [];

        // Sitemap index — recurse one level
        if (isset($xml->sitemap)) {
            foreach ($xml->sitemap as $entry) {
                $subUrls = $this->fetchSitemapUrls((string) $entry->loc);
                $urls    = array_merge($urls, $subUrls);
            }
            return $urls;
        }

        // Standard urlset
        foreach ($xml->url as $entry) {
            $urls[] = (string) $entry->loc;
        }

        return $urls;
    }

    private function fetchJobJsonLd(string $pageUrl): ?array
    {
        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => 'Medojob-Scraper/1.0'])
            ->get($pageUrl);

        if (! $response->ok()) {
            return null;
        }

        $html = $response->body();
        preg_match_all(
            '/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si',
            $html,
            $matches
        );

        foreach ($matches[1] ?? [] as $block) {
            $data = json_decode($block, true);
            if (is_array($data) && ($data['@type'] ?? '') === 'JobPosting') {
                return $data;
            }
        }

        return null;
    }

    private function normalise(array $data, string $pageUrl): ?array
    {
        $title       = strip_tags($data['title'] ?? '');
        $description = strip_tags($data['description'] ?? '');

        if (! $title) {
            return null;
        }

        // ── Category detection ─────────────────────────────────────
        $categorySlug = $this->detectCategory($title, $description);
        if (! $categorySlug) {
            return null; // Not a healthcare category we track yet
        }

        // ── Location detection (AB cities only at launch) ──────────
        $location    = $data['jobLocation'] ?? [];
        $address     = $location['address'] ?? [];
        $localityRaw = strtolower($address['addressLocality'] ?? '');

        $citySlug = $this->detectCity($localityRaw);
        if (! $citySlug) {
            return null;
        }

        // ── Employer ──────────────────────────────────────────────
        $orgData      = $data['hiringOrganization'] ?? [];
        $employerName = trim($orgData['name'] ?? '');
        $employerUrl  = $orgData['sameAs'] ?? null;

        // ── Dates ─────────────────────────────────────────────────
        $postedAt  = $this->parseDate($data['datePosted'] ?? null) ?? now();
        $expiresAt = $this->parseDate($data['validThrough'] ?? null)
            ?? now()->addDays(60);

        // ── Wages ─────────────────────────────────────────────────
        $salary    = $data['baseSalary'] ?? [];
        $wageMin   = $salary['value']['minValue'] ?? ($salary['minValue'] ?? null);
        $wageMax   = $salary['value']['maxValue'] ?? ($salary['maxValue'] ?? null);
        $wagePeriod = null;
        if (! empty($salary['value']['unitText'] ?? $salary['unitText'] ?? '')) {
            $unit = strtolower($salary['value']['unitText'] ?? $salary['unitText'] ?? '');
            $wagePeriod = str_contains($unit, 'hour') ? 'hourly' : 'annual';
        }

        // ── Employment type ───────────────────────────────────────
        $empTypeRaw = strtolower($data['employmentType'] ?? '');
        $empType    = null;
        if (str_contains($empTypeRaw, 'full')) {
            $empType = 'full_time';
        } elseif (str_contains($empTypeRaw, 'part')) {
            $empType = 'part_time';
        } elseif (str_contains($empTypeRaw, 'casual') || str_contains($empTypeRaw, 'temp')) {
            $empType = 'casual';
        }

        // ── Apply URL ─────────────────────────────────────────────
        $applyUrl = $data['apply'] ?? $data['url'] ?? $pageUrl;

        // ── External ID (from the page URL slug) ──────────────────
        $externalId = basename(parse_url($pageUrl, PHP_URL_PATH));

        return [
            'external_id'     => $externalId,
            'source'          => 'sitemap',
            'title'           => $title,
            'description'     => $description,
            'category_slug'   => $categorySlug,
            'city_slug'       => $citySlug,
            'employer_name'   => $employerName,
            'employer_url'    => $employerUrl,
            'employment_type' => $empType,
            'wage_min'        => $wageMin ? (float) $wageMin : null,
            'wage_max'        => $wageMax ? (float) $wageMax : null,
            'wage_period'     => $wagePeriod,
            'posted_at'       => $postedAt,
            'expires_at'      => $expiresAt,
            'apply_url'       => $applyUrl,
        ];
    }

    private function upsert(array $row): string
    {
        $category = Category::where('slug', $row['category_slug'])->first();
        $city     = City::where('slug', $row['city_slug'])->where('province_id', $this->alberta->id)->first();

        if (! $category || ! $city) {
            return 'skipped';
        }

        // Employer: find or create
        $employer = $this->resolveEmployer($row['employer_name'], $row['employer_url']);

        // Slug: title-city
        $slug = Str::slug($row['title'] . '-' . $city->slug);

        // Upsert on (external_id, source)
        $existing = Job::where('external_id', $row['external_id'])
            ->where('source', $row['source'])
            ->first();

        $payload = [
            'title'           => $row['title'],
            'description'     => $row['description'],
            'category_id'     => $category->id,
            'province_id'     => $this->alberta->id,
            'city_id'         => $city->id,
            'employer_id'     => $employer->id,
            'employment_type' => $row['employment_type'],
            'wage_min'        => $row['wage_min'],
            'wage_max'        => $row['wage_max'],
            'wage_period'     => $row['wage_period'],
            'posted_at'       => $row['posted_at'],
            'expires_at'      => $row['expires_at'],
            'apply_url'       => $row['apply_url'],
        ];

        if ($existing) {
            $existing->update($payload);
            return 'updated';
        }

        // Ensure unique slug per city
        $baseSlug   = $slug;
        $counter    = 1;
        while (Job::where('slug', $slug)->where('city_id', $city->id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        Job::create(array_merge($payload, [
            'external_id' => $row['external_id'],
            'source'      => $row['source'],
            'slug'        => $slug,
        ]));

        return 'imported';
    }

    private function resolveEmployer(string $name, ?string $website): Employer
    {
        $key = Str::slug($name) ?: 'unknown';

        if (isset($this->employerCache[$key])) {
            return $this->employerCache[$key];
        }

        $employer = Employer::firstOrCreate(
            ['slug' => $key],
            [
                'name'        => $name ?: 'Unknown Employer',
                'type'        => 'agency', // must match enum: public_health, ltc, agency, private_clinic
                'province_id' => $this->alberta->id,
                'website'     => $website,
            ]
        );

        $this->employerCache[$key] = $employer;
        return $employer;
    }

    private function detectCategory(string $title, string $description): ?string
    {
        $haystack = strtolower($title . ' ' . substr($description, 0, 200));
        foreach (self::CATEGORY_KEYWORDS as $slug => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($haystack, $kw)) {
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

    private function parseDate(?string $value): ?\Illuminate\Support\Carbon
    {
        if (! $value) {
            return null;
        }
        try {
            return \Illuminate\Support\Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
