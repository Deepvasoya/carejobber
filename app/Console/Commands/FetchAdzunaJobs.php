<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Job;

class FetchAdzunaJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:fetch-adzuna 
                            {--what=health care aide : Job title or keywords to search for}
                            {--where=Edmonton, Alberta : Location to search in}
                            {--pages=1 : Number of pages to fetch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch jobs from Adzuna API and import them into the system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $appId = env('ADZUNA_APP_ID', '04720207');
        $appKey = env('ADZUNA_APP_KEY', '39a726f8b6a04c7e0034b4563acc489f');
        $what = $this->option('what');
        $where = $this->option('where');
        $pages = (int) $this->option('pages');

        if (!$appId || !$appKey) {
            $this->error('Adzuna API credentials (ADZUNA_APP_ID, ADZUNA_APP_KEY) are missing.');
            return 1;
        }

        $this->info("Fetching Adzuna jobs for '{$what}' in '{$where}'...");

        $totalImported = 0;
        $totalSkipped = 0;

        for ($page = 1; $page <= $pages; $page++) {
            $this->line("Fetching page {$page}...");

            $response = Http::get("https://api.adzuna.com/v1/api/jobs/ca/search/{$page}", [
                'app_id' => $appId,
                'app_key' => $appKey,
                'what' => $what,
                'where' => $where,
            ]);

            if ($response->failed()) {
                $this->error("Failed to fetch page {$page} from Adzuna API: " . $response->body());
                Log::error('Adzuna API Error', ['body' => $response->body(), 'status' => $response->status()]);
                break; // Stop fetching if API errors out
            }

            $data = $response->json();
            $results = $data['results'] ?? [];

            if (empty($results)) {
                $this->info("No more jobs found on page {$page}. Stopping.");
                break;
            }

            foreach ($results as $result) {
                // Prevent duplicates by checking if we already have this job's Adzuna ID
                $existingJob = Job::where('reference', $result['id'])->first();

                if ($existingJob) {
                    $totalSkipped++;
                    continue; // Skip if already exists
                }

                $jobTitle = $result['title'] ?? 'Unknown Title';
                $companyName = $result['company']['display_name'] ?? 'Unknown Company';
                $description = $result['description'] ?? '';
                $location = $result['location']['display_name'] ?? $where;
                $redirectUrl = $result['redirect_url'] ?? '';
                $createdAt = isset($result['created']) ? \Carbon\Carbon::parse($result['created'])->format('Y-m-d H:i:s') : now();

                // Create the job
                Job::create([
                    'title' => $jobTitle,
                    'company_name' => $companyName,
                    'description' => $description,
                    'location' => $location,
                    'application_url' => $redirectUrl,
                    'reference' => $result['id'], // Storing Adzuna ID here
                    'external_job' => 'yes',
                    'is_active' => 1,
                    'created_at' => $createdAt,
                    'json_object' => json_encode($result),
                ]);

                $totalImported++;
            }
        }

        $this->info("Fetch complete. Imported: {$totalImported}, Skipped (already exist): {$totalSkipped}");

        return 0;
    }
}
