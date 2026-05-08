<?php

namespace App\Console\Commands;

use App\Models\Medo\Job as MedoJob;
use App\Models\Medo\Category;
use App\Models\Medo\City;
use Illuminate\Console\Command;

class VerifyMedoJobsSync extends Command
{
    protected $signature = 'jobs:verify-medo';
    protected $description = 'Verify medo_jobs table has the expected jobs';

    public function handle()
    {
        $this->info('Verifying medo_jobs table...');
        $this->newLine();
        
        // Total jobs
        $totalJobs = MedoJob::count();
        $this->info("Total jobs in medo_jobs: {$totalJobs}");
        
        // Jobs by source
        $this->info("\nJobs by source:");
        $bySources = MedoJob::selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->get();
        
        foreach ($bySources as $source) {
            $this->line("  {$source->source}: {$source->count}");
        }
        
        // Edmonton HCA jobs specifically
        $this->newLine();
        $this->info('Edmonton HCA jobs (category_id=1, city_id=2):');
        
        $edmontonHcaJobs = MedoJob::where('category_id', 1)
            ->where('city_id', 2)
            ->where('expires_at', '>', now())
            ->with('category', 'city')
            ->get();
        
        $this->info("Count: {$edmontonHcaJobs->count()}");
        
        if ($edmontonHcaJobs->count() > 0) {
            $this->newLine();
            $this->table(
                ['ID', 'External ID', 'Title', 'Source', 'Employer ID', 'Posted', 'Expires'],
                $edmontonHcaJobs->map(function($job) {
                    return [
                        $job->id,
                        $job->external_id,
                        substr($job->title, 0, 40),
                        $job->source,
                        $job->employer_id ?? 'NULL',
                        $job->posted_at->format('Y-m-d'),
                        $job->expires_at->format('Y-m-d'),
                    ];
                })->toArray()
            );
        }
        
        // Jobs by city
        $this->newLine();
        $this->info('Jobs by city:');
        $byCities = MedoJob::selectRaw('city_id, COUNT(*) as count')
            ->where('expires_at', '>', now())
            ->groupBy('city_id')
            ->with('city')
            ->get();
        
        foreach ($byCities as $cityGroup) {
            $cityName = $cityGroup->city ? $cityGroup->city->name : "Unknown (ID: {$cityGroup->city_id})";
            $this->line("  {$cityName}: {$cityGroup->count}");
        }
        
        // Jobs by category
        $this->newLine();
        $this->info('Jobs by category:');
        $byCategories = MedoJob::selectRaw('category_id, COUNT(*) as count')
            ->where('expires_at', '>', now())
            ->groupBy('category_id')
            ->with('category')
            ->get();
        
        foreach ($byCategories as $catGroup) {
            $catName = $catGroup->category ? $catGroup->category->name : "Unknown (ID: {$catGroup->category_id})";
            $this->line("  {$catName}: {$catGroup->count}");
        }
        
        // Check for NULL employer_ids
        $this->newLine();
        $nullEmployerCount = MedoJob::whereNull('employer_id')->count();
        $this->info("Jobs with NULL employer_id: {$nullEmployerCount}");
        
        if ($nullEmployerCount > 0) {
            $this->line("  (These are legacy jobs synced from the old jobs table)");
        }
        
        return 0;
    }
}
