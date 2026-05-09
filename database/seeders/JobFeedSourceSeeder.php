<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobFeedSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'name' => 'Alberta Health Services',
                'slug' => 'ahs',
                'provider' => 'custom',
                'source_url' => 'https://careers.albertahealthservices.ca/jobs',
                'config' => json_encode(['adapter' => 'App\Services\Scrapers\Alberta\AhsScraper']),
                'is_active' => true,
            ],
            [
                'name' => 'Covenant Health',
                'slug' => 'covenant',
                'provider' => 'custom',
                'source_url' => 'https://www.covenanthealth.ca/careers',
                'config' => json_encode(['adapter' => 'App\Services\Scrapers\Alberta\CovenantScraper']),
                'is_active' => true,
            ],
            [
                'name' => 'Bethany Care Society',
                'slug' => 'bethany',
                'provider' => 'custom',
                'source_url' => 'https://bethanyseniors.com/careers',
                'config' => json_encode(['adapter' => 'App\Services\Scrapers\Alberta\BethanyScraper']),
                'is_active' => true,
            ],
            [
                'name' => 'AgeCare',
                'slug' => 'agecare',
                'provider' => 'custom',
                'source_url' => 'https://www.agecare.ca/careers',
                'config' => json_encode(['adapter' => 'App\Services\Scrapers\Alberta\AgeCareScraper']),
                'is_active' => true,
            ],
            [
                'name' => 'CapitalCare',
                'slug' => 'capitalcare',
                'provider' => 'custom',
                'source_url' => 'https://www.capitalcare.net/Careers',
                'config' => json_encode(['adapter' => 'App\Services\Scrapers\Alberta\CapitalCareScraper']),
                'is_active' => true,
            ],
            [
                'name' => 'Demo Healthcare Jobs',
                'slug' => 'demo',
                'provider' => 'custom',
                'source_url' => null,
                'config' => json_encode(['adapter' => 'App\Services\Scrapers\Alberta\DemoScraper']),
                'is_active' => false,
            ],
        ];

        foreach ($sources as $source) {
            DB::table('job_feed_sources')->updateOrInsert(
                ['slug' => $source['slug']],
                array_merge($source, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
