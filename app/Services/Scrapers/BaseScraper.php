<?php

namespace App\Services\Scrapers;

/**
 * BaseScraper is the abstract class that all specific job feed scrapers must extend.
 */
abstract class BaseScraper
{
    /**
     * Fetch jobs from the target source and return them as an array of raw/normalized job data.
     *
     * Expected array structure per job:
     * [
     *     'external_id' => '...',
     *     'title' => '...',
     *     'description' => '...',
     *     'category_hint' => '...',
     *     'province_id' => 1,
     *     'city_hint' => '...',
     *     'employer_name' => '...',
     *     'employer_url' => '...',
     *     'employment_type' => '...', // full_time, part_time, casual
     *     'wage_min' => 20.00,
     *     'wage_max' => 30.00,
     *     'wage_period' => 'hourly',
     *     'posted_at' => Carbon instance,
     *     'expires_at' => Carbon instance,
     *     'apply_url' => '...',
     * ]
     *
     * @return array
     */
    abstract public function fetchJobs(): array;
}
