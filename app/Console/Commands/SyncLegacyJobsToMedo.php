<?php

namespace App\Console\Commands;

use App\Job;
use App\Services\Medo\LegacyJobSyncService;
use Illuminate\Console\Command;

class SyncLegacyJobsToMedo extends Command
{
    protected $signature = 'jobs:sync-legacy-to-medo {--force : Force sync all jobs}';
    protected $description = 'Sync employer-posted jobs from legacy jobs table to medo_jobs table';

    public function handle()
    {
        $this->info('Starting sync of legacy jobs to medo_jobs...');

        $syncService = app(LegacyJobSyncService::class);
        $synced = 0;
        $skipped = 0;
        $errors = 0;

        $legacyJobs = Job::query()
            ->when(! $this->option('force'), function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('is_draft')->orWhere('is_draft', 0);
                });
            })
            ->with('functionalArea')
            ->get();

        $this->info("Found {$legacyJobs->count()} legacy jobs to evaluate");

        foreach ($legacyJobs as $legacyJob) {
            try {
                $medoJob = $syncService->sync($legacyJob);

                if (! $medoJob) {
                    $skipped++;
                    continue;
                }

                $this->line("Synced legacy job {$legacyJob->id}: {$legacyJob->title}");
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
}
