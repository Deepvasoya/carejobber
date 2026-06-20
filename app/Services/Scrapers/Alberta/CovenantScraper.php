<?php

namespace App\Services\Scrapers\Alberta;

use App\Services\Scrapers\BaseScraper;
use App\Models\Medo\Province;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CovenantScraper extends BaseScraper
{
    private string $listingUrl = 'https://careers.covenanthealth.ca/latest-jobs';

    public function fetchJobs(): array
    {
        $provinceId = Province::where('slug', 'ab')->value('id');
        $jobs = [];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Medojob Job Aggregator/1.0',
                ])
                ->get($this->listingUrl);

            if (!$response->successful()) {
                Log::warning('[CovenantScraper] Failed to fetch listing page');

                return [];
            }

            $jobUrls = $this->discoverJobUrls($response->body());

            foreach (array_slice($jobUrls, 0, 30) as $jobUrl) {
                $job = $this->fetchJobDetail($jobUrl, $provinceId);
                if ($job) {
                    $jobs[] = $job;
                }
            }
        } catch (\Exception $e) {
            Log::error('[CovenantScraper] Error: ' . $e->getMessage());
        }

        return $jobs;
    }

    private function discoverJobUrls(string $html): array
    {
        preg_match_all(
            '~https://careers\.covenanthealth\.ca/jobs/[a-z0-9-]+-(\d+)~i',
            $html,
            $matches
        );

        return collect($matches[0] ?? [])
            ->filter(fn ($url) => !str_contains($url, '/other-jobs-matching'))
            ->unique()
            ->values()
            ->all();
    }

    private function fetchJobDetail(string $url, int $provinceId): ?array
    {
        $response = Http::timeout(20)
            ->withHeaders([
                'User-Agent' => 'Medojob Job Aggregator/1.0',
            ])
            ->get($url);

        if (!$response->successful()) {
            return null;
        }

        $html = $response->body();
        $text = $this->plainText($html);

        $title = $this->match('/job:\s*\{.*?title:\s*"([^"]+)"/s', $html)
            ?: $this->match('/<h1[^>]*class="title"[^>]*>(.*?)<\/h1>/si', $html);

        if (!$title) {
            return null;
        }

        $locationJson = $this->match('/location:\s*\{\s*name:\s*"([^"]+)"/s', $html) ?: '';
        $category = $this->match('/category:\s*\{\s*name:\s*"([^"]+)"/s', $html)
            ?: $this->match('/<span[^>]*class="field_value font_header_light"[^>]*>(.*?)<\/span>/i', $html)
            ?: '';

        $externalId = $this->match('/<span class="field_value">(COV\d+)<\/span>/i', $html)
            ?: basename(parse_url($url, PHP_URL_PATH));

        $description = $this->fetchDescription($html);

        $expiresAt = $this->parseCovenantDate(
            $this->match('/Posting End Date:\s*([0-9]{1,2}-[A-Z]{3}-[0-9]{4})/i', $text)
        );

        $wageMin = $this->match('/Minimum Salary:\s*\$?([0-9.]+)/i', $text);
        $wageMax = $this->match('/Maximum Salary:\s*\$?([0-9.]+)/i', $text);

        $employmentType = $this->determineEmploymentType($text);

        return [
            'external_id' => 'cov-' . $externalId,
            'title' => html_entity_decode(trim($title), ENT_QUOTES, 'UTF-8'),
            'description' => $description,
            'category_hint' => $title . ' ' . $category . ' ' . $description,
            'province_id' => $provinceId,
            'city_hint' => $this->extractCity($locationJson . ' ' . $text),
            'employer_name' => 'Covenant Health',
            'employer_url' => 'https://www.covenanthealth.ca',
            'employment_type' => $employmentType,
            'wage_min' => $wageMin ? (float) $wageMin : null,
            'wage_max' => $wageMax ? (float) $wageMax : null,
            'wage_period' => ($wageMin || $wageMax) ? 'hourly' : null,
            'posted_at' => Carbon::now(),
            'expires_at' => $expiresAt ?: Carbon::now()->addDays(30),
            'apply_url' => $url,
        ];
    }

    private function fetchDescription(string $html): string
    {
        if (preg_match('/<div class="job_description">(.*?)<div class="job_qualifications">/s', $html, $match)) {
            $inner = strip_tags($match[1], '<p><br><ul><ol><li><b><strong><em><i><h3><h4><h5>');

            return trim(html_entity_decode($inner, ENT_QUOTES, 'UTF-8'));
        }

        return $this->match('/<meta\s+name="description"\s+content="([^"]*)"/i', $html)
            ?: '';
    }

    private function determineEmploymentType(string $text): string
    {
        $lower = strtolower($text);

        if (str_contains($lower, 'part time') || str_contains($lower, 'part-time')) {
            return 'part_time';
        }

        if (str_contains($lower, 'casual') || str_contains($lower, 'on call') || str_contains($lower, 'on-call')) {
            return 'casual';
        }

        return 'full_time';
    }

    private function plainText(string $html): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function match(string $pattern, string $value): ?string
    {
        return preg_match($pattern, $value, $matches) ? trim($matches[1]) : null;
    }

    private function parseCovenantDate(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d-M-Y', strtoupper($value))->endOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractCity(string $location): string
    {
        $cities = [
            'Edmonton', 'Calgary', 'Red Deer', 'Lethbridge', 'Medicine Hat',
            'Grande Prairie', 'Fort McMurray', 'Bonnyville', 'Killam',
            'La Crete', 'Wainwright', 'St. Paul', 'Peace River',
            'Vermilion', 'Lloydminster', 'Camrose', 'Drayton Valley',
            'Athabasca', 'Westlock', 'Barrhead', 'Sherwood Park',
            'Ft. Saskatchewan', 'Spruce Grove', 'St. Albert', 'Leduc',
            'Morinville', 'Stony Plain', 'Hinton', 'Edson', 'Jasper',
            'Banff', 'Canmore', 'Cochrane', 'Airdrie', 'Chestermere',
            'High River', 'Okotoks', 'Strathmore', 'Brooks', 'Taber',
            'Pincher Creek', 'Crowsnest Pass', 'Blairmore', 'Coleman',
        ];

        foreach ($cities as $city) {
            if (stripos($location, $city) !== false) {
                return $city;
            }
        }

        if (preg_match('/,\s*([A-Za-z\s\-]+?)(?:,\s*(?:Grey Nuns|St\.|Misericordia|University|Hospital))?\s*$/i', $location, $m)) {
            $candidate = trim($m[1]);
            if (!str_contains($candidate, 'Covenant') && !str_contains($candidate, 'Zone')) {
                return $candidate;
            }
        }

        return 'Edmonton';
    }
}
