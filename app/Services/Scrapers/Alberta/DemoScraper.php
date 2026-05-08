<?php

namespace App\Services\Scrapers\Alberta;

use App\Services\Scrapers\BaseScraper;
use App\Models\Medo\Province;
use Carbon\Carbon;

/**
 * Demo scraper that generates sample jobs for testing
 * This demonstrates how the scraper system works
 */
class DemoScraper extends BaseScraper
{
    public function fetchJobs(): array
    {
        $provinceId = Province::where('slug', 'ab')->value('id');
        
        return [
            [
                'external_id' => 'demo-hca-edmonton-1',
                'title' => 'Health Care Aide - Full Time',
                'description' => '<p>We are seeking a compassionate Health Care Aide to join our team in Edmonton. You will provide essential personal care to residents including bathing, dressing, feeding, and mobility assistance.</p><p><strong>Requirements:</strong></p><ul><li>Current HCA certificate</li><li>CPR and First Aid certification</li><li>Excellent communication skills</li></ul>',
                'category_hint' => 'hca',
                'province_id' => $provinceId,
                'city_hint' => 'Edmonton',
                'employer_name' => 'Edmonton Care Center',
                'employer_url' => 'https://example.com',
                'employment_type' => 'full_time',
                'wage_min' => 22.00,
                'wage_max' => 27.50,
                'wage_period' => 'hourly',
                'posted_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(30),
                'apply_url' => 'https://example.com/apply/hca-1',
            ],
            [
                'external_id' => 'demo-hca-edmonton-2',
                'title' => 'Health Care Aide - Part Time Evenings',
                'description' => '<p>Join our evening care team! We need a dedicated HCA for part-time evening shifts (4pm-12am). Perfect for those seeking work-life balance.</p><p><strong>What we offer:</strong></p><ul><li>Competitive wages with shift differential</li><li>Flexible scheduling</li><li>Supportive team environment</li></ul>',
                'category_hint' => 'hca',
                'province_id' => $provinceId,
                'city_hint' => 'Edmonton',
                'employer_name' => 'Sunrise Senior Living',
                'employer_url' => 'https://example.com',
                'employment_type' => 'part_time',
                'wage_min' => 23.50,
                'wage_max' => 28.00,
                'wage_period' => 'hourly',
                'posted_at' => Carbon::now()->subDays(2),
                'expires_at' => Carbon::now()->addDays(28),
                'apply_url' => 'https://example.com/apply/hca-2',
            ],
            [
                'external_id' => 'demo-hca-edmonton-3',
                'title' => 'Health Care Aide - Casual Pool',
                'description' => '<p>Looking for flexible work? Join our casual pool and work when it suits your schedule. Great for students or those with other commitments.</p><p><strong>Benefits:</strong></p><ul><li>Choose your own shifts</li><li>Gain diverse experience</li><li>Premium casual rate</li></ul>',
                'category_hint' => 'hca',
                'province_id' => $provinceId,
                'city_hint' => 'Edmonton',
                'employer_name' => 'Capital Care',
                'employer_url' => 'https://example.com',
                'employment_type' => 'casual',
                'wage_min' => 24.00,
                'wage_max' => 29.00,
                'wage_period' => 'hourly',
                'posted_at' => Carbon::now()->subDays(5),
                'expires_at' => Carbon::now()->addDays(25),
                'apply_url' => 'https://example.com/apply/hca-3',
            ],
            [
                'external_id' => 'demo-hca-edmonton-4',
                'title' => 'Health Care Aide - New Grad Friendly',
                'description' => '<p>New graduates welcome! We provide comprehensive orientation and mentorship. Start your healthcare career with a supportive team.</p><p><strong>We provide:</strong></p><ul><li>Paid orientation and training</li><li>Mentorship program</li><li>Career advancement opportunities</li></ul>',
                'category_hint' => 'hca',
                'province_id' => $provinceId,
                'city_hint' => 'Edmonton',
                'employer_name' => 'Good Samaritan Society',
                'employer_url' => 'https://example.com',
                'employment_type' => 'full_time',
                'wage_min' => 22.00,
                'wage_max' => 26.00,
                'wage_period' => 'hourly',
                'posted_at' => Carbon::now()->subDay(),
                'expires_at' => Carbon::now()->addDays(29),
                'apply_url' => 'https://example.com/apply/hca-4',
            ],
        ];
    }
}
