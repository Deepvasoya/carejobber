<?php

namespace App\Services\Scrapers\Alberta;

use App\Services\Scrapers\BaseScraper;
use App\Models\Medo\Province;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AhsScraper extends BaseScraper
{
    /**
     * Fetch jobs from Alberta Health Services
     * AHS uses a career portal - this scraper extracts job listings
     */
    public function fetchJobs(): array
    {
        $provinceId = Province::where('slug', 'ab')->value('id');
        $jobs = [];
        
        try {
            $searchUrl = 'https://careers.albertahealthservices.ca/jobs';
            $keywords = [
                'Health Care Aide',
                'Licensed Practical Nurse',
                'Registered Nurse',
            ];

            $jobUrls = [];
            
            foreach ($keywords as $keyword) {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'User-Agent' => 'Medojob Job Aggregator/1.0',
                    ])
                    ->get($searchUrl, [
                        'keywords' => $keyword,
                        'location' => 'Alberta',
                    ]);
                
                if (!$response->successful()) {
                    Log::warning("[AhsScraper] Failed to fetch jobs for: {$keyword}");
                    continue;
                }

                $jobUrls = array_merge($jobUrls, $this->discoverJobUrls($response->body()));
            }

            $jobUrls = array_values(array_unique($jobUrls));

            foreach (array_slice($jobUrls, 0, 60) as $jobUrl) {
                $job = $this->fetchJobDetail($jobUrl, $provinceId);
                if ($job) {
                    $jobs[] = $job;
                }
            }
            
        } catch (\Exception $e) {
            Log::error("[AhsScraper] Error: " . $e->getMessage());
        }
        
        return $jobs;
    }

    private function discoverJobUrls(string $html): array
    {
        preg_match_all('~https://careers\.albertahealthservices\.ca/jobs/([a-z0-9-]+)~i', $html, $matches);

        return collect($matches[0] ?? [])
            ->filter(function ($url) {
                return ! str_contains($url, '/other-jobs-matching')
                    && ! str_contains($url, '/search/')
                    && preg_match('~-\d+$~', $url);
            })
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

        if (! $response->successful()) {
            return null;
        }

        $html = $response->body();
        $text = $this->plainText($html);
        $title = $this->match('/job:\s*\{.*?title:\s*"([^"]+)"/s', $html)
            ?: $this->match('/<title>(.*?)\s+-\s+Alberta Health Services Careers<\/title>/si', $html);

        if (! $title) {
            return null;
        }

        $description = $this->match('/<meta\s+name="description"\s+content="([^"]*)"/i', $html)
            ?: $text;
        $location = $this->match('/location:\s*\{\s*name:\s*"([^"]+)"/s', $html) ?: '';
        $category = $this->match('/category:\s*\{\s*name:\s*"([^"]+)"/s', $html) ?: '';
        $externalId = $this->match('/job:\s*\{\s*id:\s*"([^"]+)"/s', $html)
            ?: basename(parse_url($url, PHP_URL_PATH));
        $expiresAt = $this->parseAhsDate($this->match('/Posting End Date:\s*([0-9]{1,2}-[A-Z]{3}-[0-9]{4})/i', $text));
        $wageMin = $this->match('/Minimum Salary:\s*\$([0-9.]+)/i', $text);
        $wageMax = $this->match('/Maximum Salary:\s*\$([0-9.]+)/i', $text);

        return [
            'external_id' => 'ahs-' . $externalId,
            'title' => html_entity_decode(trim($title), ENT_QUOTES, 'UTF-8'),
            'description' => html_entity_decode(trim($description), ENT_QUOTES, 'UTF-8'),
            'category_hint' => $title . ' ' . $category . ' ' . $description,
            'province_id' => $provinceId,
            'city_hint' => $this->extractCity($location . ' ' . $text),
            'employer_name' => 'Alberta Health Services',
            'employer_url' => 'https://www.albertahealthservices.ca',
            'employment_type' => str_contains(strtolower($text), 'part time') || str_contains(strtolower($text), 'part-time') ? 'part_time' : 'full_time',
            'wage_min' => $wageMin ? (float) $wageMin : null,
            'wage_max' => $wageMax ? (float) $wageMax : null,
            'wage_period' => ($wageMin || $wageMax) ? 'hourly' : null,
            'posted_at' => Carbon::now(),
            'expires_at' => $expiresAt ?: Carbon::now()->addDays(30),
            'apply_url' => $url,
        ];
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

    private function parseAhsDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d-M-Y', strtoupper($value))->endOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
    
    private function parseJobListings(string $html, string $categoryHint, int $provinceId): array
    {
        $jobs = [];
        
        // Use DOMDocument to parse HTML
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
        
        // Find job listing elements (adjust selectors based on actual AHS site structure)
        $jobNodes = $xpath->query("//div[contains(@class, 'job-listing')]");
        
        foreach ($jobNodes as $node) {
            try {
                $titleNode = $xpath->query(".//h3[@class='job-title']", $node)->item(0);
                $locationNode = $xpath->query(".//span[@class='job-location']", $node)->item(0);
                $linkNode = $xpath->query(".//a[@class='job-link']", $node)->item(0);
                
                if (!$titleNode || !$linkNode) {
                    continue;
                }
                
                $title = trim($titleNode->textContent);
                $location = $locationNode ? trim($locationNode->textContent) : 'Alberta';
                $applyUrl = $linkNode->getAttribute('href');
                
                // Extract city from location string
                $cityHint = $this->extractCity($location);
                
                // Generate external ID from URL
                $externalId = md5($applyUrl);
                
                $jobs[] = [
                    'external_id' => $externalId,
                    'title' => $title,
                    'description' => "Join Alberta Health Services as a {$title} in {$location}. Apply now to be part of Alberta's largest healthcare provider.",
                    'category_hint' => $categoryHint,
                    'province_id' => $provinceId,
                    'city_hint' => $cityHint,
                    'employer_name' => 'Alberta Health Services',
                    'employer_url' => 'https://www.albertahealthservices.ca',
                    'employment_type' => 'full_time',
                    'wage_min' => null,
                    'wage_max' => null,
                    'wage_period' => 'hourly',
                    'posted_at' => Carbon::now(),
                    'expires_at' => Carbon::now()->addDays(30),
                    'apply_url' => $applyUrl,
                ];
                
            } catch (\Exception $e) {
                Log::warning("[AhsScraper] Error parsing job node: " . $e->getMessage());
                continue;
            }
        }
        
        return $jobs;
    }
    
    private function extractCity(string $location): string
    {
        // Common Alberta cities
        $cities = ['Edmonton', 'Calgary', 'Red Deer', 'Lethbridge', 'Medicine Hat', 'Grande Prairie'];
        
        foreach ($cities as $city) {
            if (stripos($location, $city) !== false) {
                return $city;
            }
        }
        
        return 'Edmonton'; // Default to Edmonton
    }
}
