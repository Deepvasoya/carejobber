<?php

namespace App\Console\Commands;

use App\Job;
use App\City;
use App\Models\Medo\Job as MedoJob;
use App\Models\Medo\City as MedoCity;
use App\Models\Medo\Category;
use App\Models\Medo\Employer;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SyncLegacyJobsToMedo extends Command
{
    protected $signature = 'jobs:sync-legacy-to-medo {--force : Force sync all jobs}';
    protected $description = 'Sync employer-posted jobs from legacy jobs table to medo_jobs table';

    public function handle()
    {
        $this->info('Starting sync of legacy jobs to medo_jobs...');
        
        // Category mapping
        $categoryMapping = [
            655 => 1, // HCA
            656 => 2, // LPN
            657 => 3, // RN
        ];
        
        // City mapping
        $cityMapping = [
            10107 => 1, // Calgary
            10125 => 2, // Edmonton
            10169 => 3, // Red Deer
            10150 => 4, // Lethbridge
            10156 => 5, // Medicine Hat
            10135 => 7, // Grande Prairie
        ];
        
        $synced = 0;
        $skipped = 0;
        $errors = 0;
        
        // Get active jobs from legacy table
        $legacyJobs = Job::where('is_active', 1)
            ->whereIn('functional_area_id', array_keys($categoryMapping))
            ->whereIn('city_id', array_keys($cityMapping))
            ->where('expiry_date', '>', now())
            ->get();
        
        $this->info("Found {$legacyJobs->count()} eligible legacy jobs");
        
        foreach ($legacyJobs as $legacyJob) {
            try {
                $categoryId = $categoryMapping[$legacyJob->functional_area_id] ?? null;
                $cityId = $cityMapping[$legacyJob->city_id] ?? null;
                
                if (!$categoryId || !$cityId) {
                    $skipped++;
                    continue;
                }
                
                // Check if already synced
                $externalId = 'legacy-' . $legacyJob->id;
                $existing = MedoJob::where('external_id', $externalId)
                    ->where('source', 'legacy')
                    ->first();
                
                if ($existing && !$this->option('force')) {
                    $skipped++;
                    continue;
                }
                
                // Get city for province_id
                $medoCity = MedoCity::find($cityId);
                if (!$medoCity) {
                    $this->warn("Medo city not found for ID: {$cityId}");
                    $errors++;
                    continue;
                }
                
                // Create or update medo job
                $medoJobData = [
                    'external_id' => $externalId,
                    'source' => 'legacy',
                    'slug' => $legacyJob->slug,
                    'title' => $legacyJob->title,
                    'description' => $legacyJob->description ?? '',
                    'category_id' => $categoryId,
                    'province_id' => $medoCity->province_id,
                    'city_id' => $cityId,
                    'employer_id' => null, // Legacy jobs don't have medo employers
                    'facility_name' => $legacyJob->company->name ?? null,
                    'employment_type' => $this->mapEmploymentType($legacyJob),
                    'shift_type' => null,
                    'setting' => null,
                    'wage_min' => $legacyJob->salary_from,
                    'wage_max' => $legacyJob->salary_to,
                    'wage_period' => $this->mapWagePeriod($legacyJob),
                    'posted_at' => $legacyJob->created_at,
                    'expires_at' => Carbon::parse($legacyJob->expiry_date),
                    'apply_url' => route('job.detail', $legacyJob->slug),
                    'is_new_grad_friendly' => false,
                    'has_signing_bonus' => false,
                ];
                
                if ($existing) {
                    $existing->update($medoJobData);
                    $this->line("Updated: {$legacyJob->title}");
                } else {
                    MedoJob::create($medoJobData);
                    $this->line("Created: {$legacyJob->title}");
                }
                
                $synced++;
                
            } catch (\Exception $e) {
                $this->error("Error syncing job {$legacyJob->id}: " . $e->getMessage());
                $errors++;
            }
        }
        
        $this->info("\nSync complete!");
        $this->info("Synced: {$synced}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");
        
        // Clear cache
        $this->call('cache:clear');
        
        return 0;
    }
    
    private function mapEmploymentType($job)
    {
        if (!$job->job_type_id) return 'full_time';
        
        // Map common job types
        $typeMap = [
            1 => 'full_time',
            2 => 'part_time',
            3 => 'casual',
        ];
        
        return $typeMap[$job->job_type_id] ?? 'full_time';
    }
    
    private function mapWagePeriod($job)
    {
        if (!$job->salary_period_id) return 'hourly';
        
        // Map salary periods
        $periodMap = [
            1 => 'hourly',
            2 => 'annual',
        ];
        
        return $periodMap[$job->salary_period_id] ?? 'hourly';
    }
}
