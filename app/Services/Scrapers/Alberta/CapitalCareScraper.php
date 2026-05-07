<?php

namespace App\Services\Scrapers\Alberta;

use App\Services\Scrapers\BaseScraper;
use App\Models\Medo\Province;

class CapitalCareScraper extends BaseScraper
{
    /**
     * Fetch jobs from CapitalCare
     */
    public function fetchJobs(): array
    {
        $provinceId = Province::where('slug', 'ab')->value('id');
        
        // TODO: Implement actual scraping logic for CapitalCare
        
        return [];
    }
}
