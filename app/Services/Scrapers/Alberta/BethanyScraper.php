<?php

namespace App\Services\Scrapers\Alberta;

use App\Services\Scrapers\BaseScraper;
use App\Models\Medo\Province;

class BethanyScraper extends BaseScraper
{
    /**
     * Fetch jobs from Bethany Care
     */
    public function fetchJobs(): array
    {
        $provinceId = Province::where('slug', 'ab')->value('id');
        
        // TODO: Implement actual scraping logic for Bethany Care
        
        return [];
    }
}
