<?php

namespace App\Console\Commands;

use App\JobFeedRun;
use App\JobFeedSource;
use App\Services\Medo\Scrapers\SitemapScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeJobFeeds extends Command
{
    protected $signature = 'jobs:scrape
        {source? : Optional job feed source slug to run a single source}
        {--dry-run : Discover and log without writing to the database}';

    protected $description = 'Run configured external job feed scrapers and write results to medo_jobs';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

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
                'status'             => 'running',
                'started_at'        => now(),
            ]);

            $this->line('▶ Running source: ' . $source->slug . ' (provider: ' . $source->provider . ')');

            try {
                $stats = $this->runAdapter($source, $isDryRun);

                $this->info(sprintf(
                    '  ✔ Discovered: %d | Imported: %d | Updated: %d | Skipped: %d | Errors: %d',
                    $stats['discovered'],
                    $stats['imported'],
                    $stats['updated'],
                    $stats['skipped'],
                    count($stats['errors'])
                ));

                foreach ($stats['errors'] as $err) {
                    $this->warn('  ⚠ ' . $err);
                }

                $run->update([
                    'status'           => count($stats['errors']) > 0 ? 'completed_with_errors' : 'completed',
                    'discovered_count' => $stats['discovered'],
                    'imported_count'   => $stats['imported'],
                    'updated_count'    => $stats['updated'],
                    'skipped_count'    => $stats['skipped'],
                    'error_message'    => count($stats['errors']) ? implode("\n", $stats['errors']) : null,
                    'imported_log'     => json_encode($stats['imported_list'] ?? []),
                    'skipped_log'      => json_encode($stats['skipped_list'] ?? []),
                    'finished_at'      => now(),
                ]);

                $source->update(['last_run_at' => now()]);

            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                $this->error('  ✘ ' . $source->slug . ': ' . $msg);
                Log::error('[jobs:scrape] ' . $source->slug . ': ' . $msg, ['trace' => $e->getTraceAsString()]);

                $run->update([
                    'status'        => 'failed',
                    'error_message' => $msg,
                    'finished_at'   => now(),
                ]);
            }
        }

        return 0;
    }

    /**
     * Dispatch the correct adapter based on the source's provider field.
     *
     * Supported providers:
     *   sitemap  — fetches a sitemap XML, scrapes JSON-LD from each job page
     *   custom   — alias for sitemap (legacy seeded value)
     */
    private function runAdapter(JobFeedSource $source, bool $dryRun): array
    {
        $provider = strtolower(trim($source->provider ?? ''));

        switch ($provider) {
            case 'sitemap':
            case 'custom': // existing seeded source uses "custom" — treat as sitemap
                if (! $source->source_url) {
                    throw new \RuntimeException('No source_url configured for sitemap provider.');
                }
                $scraper = new SitemapScraper();
                return $scraper->run($source->source_url, $dryRun);

            default:
                throw new \RuntimeException(
                    "No scraper adapter registered for provider \"{$provider}\". "
                    . "Supported: sitemap, custom."
                );
        }
    }
}
