<?php

namespace App\Console\Commands;

use App\JobFeedRun;
use App\JobFeedSource;
use Illuminate\Console\Command;

class ScrapeJobFeeds extends Command
{
    protected $signature = 'jobs:scrape {source? : Optional job feed source slug} {--dry-run : Discover without importing}';
    protected $description = 'Run configured external job feed scrapers and record import runs';

    public function handle()
    {
        $sources = JobFeedSource::active()
            ->when($this->argument('source'), function ($query, $source) {
                $query->where('slug', $source);
            })
            ->get();

        if ($sources->isEmpty()) {
            $this->warn('No active job feed sources found.');
            return 0;
        }

        foreach ($sources as $source) {
            $run = JobFeedRun::create([
                'job_feed_source_id' => $source->id,
                'status' => 'running',
                'started_at' => now(),
            ]);

            try {
                $this->line('Running source: ' . $source->slug);

                $run->update([
                    'status' => 'skipped',
                    'skipped_count' => 1,
                    'error_message' => 'No scraper adapter is registered for provider "' . $source->provider . '".',
                    'finished_at' => now(),
                ]);

                $source->update(['last_run_at' => now()]);
            } catch (\Throwable $e) {
                $run->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'finished_at' => now(),
                ]);

                $this->error($source->slug . ': ' . $e->getMessage());
            }
        }

        return 0;
    }
}
