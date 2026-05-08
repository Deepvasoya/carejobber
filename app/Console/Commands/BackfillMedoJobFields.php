<?php

namespace App\Console\Commands;

use App\Job;
use App\City;
use App\Models\Medo\City as MedoCity;
use Illuminate\Console\Command;

class BackfillMedoJobFields extends Command
{
    protected $signature = 'jobs:backfill-medo-fields';
    protected $description = 'Backfill medo_city_id and medo_province_id for existing jobs';

    public function handle()
    {
        $this->info('Starting backfill of medo fields...');
        
        // Get all jobs that need medo_city_id updated
        $jobs = Job::whereNull('medo_city_id')
            ->whereNotNull('city_id')
            ->whereNotNull('medo_category_id')
            ->get();

        $this->info("Found {$jobs->count()} jobs needing medo_city_id update");

        $updated = 0;
        $notFound = 0;

        foreach ($jobs as $job) {
            // Get the legacy city
            $legacyCity = City::find($job->city_id);
            if (!$legacyCity) {
                $notFound++;
                continue;
            }
            
            // Try to find matching medo city by name
            $medoCity = MedoCity::where('name', $legacyCity->city)->first();
            
            if ($medoCity) {
                $job->medo_city_id = $medoCity->id;
                $job->medo_province_id = $medoCity->province_id;
                $job->save();
                $updated++;
                $this->line("✓ Updated job {$job->id}: {$legacyCity->city} -> medo_city_id {$medoCity->id}");
            } else {
                $notFound++;
                $this->warn("✗ No match for job {$job->id}: {$legacyCity->city}");
            }
        }

        $this->info("\nBackfill complete!");
        $this->info("Updated: {$updated}");
        $this->warn("Not found: {$notFound}");

        return 0;
    }
}
