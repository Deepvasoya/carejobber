<?php

namespace App\Services\Scrapers\Alberta;

use App\Services\Scrapers\BaseScraper;
use App\Models\Medo\Province;

class CovenantScraper extends BaseScraper
{
    /**
     * Fetch jobs from Covenant Health
     */
    public function fetchJobs(): array
    {
        $provinceId = Province::where('slug', 'ab')->value('id');
        
        // TODO: Implement actual scraping logic for covenanthealth.ca/careers
        // This is currently a stub returning an empty array to satisfy the pipeline architecture.
        
        return [];
    }
}
