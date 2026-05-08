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
            // AHS job search URL (adjust based on actual AHS careers site structure)
            $searchUrl = 'https://careers.albertahealthservices.ca/jobs';
            
            // Keywords to search for different healthcare roles
            $keywords = [
                'Health Care Aide' => 'hca',
                'Licensed Practical Nurse' => 'lpn',
                'Registered Nurse' => 'rn',
            ];
            
            foreach ($keywords as $title => $categoryHint) {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'User-Agent' => 'Medojob Job Aggregator/1.0',
                    ])
                    ->get($searchUrl, [
                        'q' => $title,
                        'location' => 'Alberta',
                    ]);
                
                if (!$response->successful()) {
                    Log::warning("[AhsScraper] Failed to fetch jobs for: {$title}");
                    continue;
                }
                
                // Parse HTML response
                $html = $response->body();
                $jobs = array_merge($jobs, $this->parseJobListings($html, $categoryHint, $provinceId));
            }
            
        } catch (\Exception $e) {
            Log::error("[AhsScraper] Error: " . $e->getMessage());
        }
        
        return $jobs;
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
