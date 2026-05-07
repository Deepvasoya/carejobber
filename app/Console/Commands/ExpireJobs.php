<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExpireJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire jobs that have passed their expiration date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting job expiration process...');

        // Delete jobs that expired more than 30 days ago to keep the table clean.
        $deletedCount = \App\Job::where('expiry_date', '<', now()->subDays(30))->delete();

        $this->info("Deleted {$deletedCount} jobs that expired over 30 days ago.");
        
        // Let's also expire legacy jobs
        $legacyCount = \App\Job::where('expiry_date', '<', now())->where('is_active', 1)->update(['is_active' => 0]);
        $this->info("Deactivated {$legacyCount} legacy jobs.");

        return 0;
    }
}
