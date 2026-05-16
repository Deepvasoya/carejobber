<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Job;

class FetchCareerjetJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:fetch-careerjet 
                            {--what=health care aide : Job title or keywords to search for}
                            {--where=Edmonton, Alberta : Location to search in}
                            {--pages=1 : Number of pages to fetch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch jobs from Careerjet API and import them into the system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apiKey = env('CAREERJET_API_KEY', '59eb8abcd3426ad9e01a713a08530f6a');
        $what = $this->option('what');
        $where = $this->option('where');
        $pages = (int) $this->option('pages');

        if (!$apiKey) {
            $this->error('Careerjet API key (CAREERJET_API_KEY) is missing.');
            return 1;
        }

        $this->info("Fetching Careerjet jobs for '{$what}' in '{$where}'...");

        $totalImported = 0;
        $totalSkipped = 0;

        // Careerjet max pages is 10
        if ($pages > 10) {
            $pages = 10;
        }

        for ($page = 1; $page <= $pages; $page++) {
            $this->line("Fetching page {$page}...");

            $response = Http::withBasicAuth($apiKey, '')
                ->withHeaders([
                    'Referer' => env('APP_URL', 'https://medojob.com')
                ])
                ->get("https://search.api.careerjet.net/v4/query", [
                    'locale_code' => 'en_CA',
                    'keywords' => $what,
                    'location' => $where,
                    'page' => $page,
                    'page_size' => 20,
                    'user_ip' => '127.0.0.1', // Console command doesn't have a real user IP
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                ]);

            if ($response->failed()) {
                $this->error("Failed to fetch page {$page} from Careerjet API: " . $response->body());
                Log::error('Careerjet API Error', ['body' => $response->body(), 'status' => $response->status()]);
                break; // Stop fetching if API errors out
            }

            $data = $response->json();

            // dd($data);

            if (isset($data['type']) && $data['type'] === 'ERROR') {
                $this->error("Careerjet API returned an error: " . ($data['error'] ?? 'Unknown error'));
                Log::error('Careerjet API Error Response', ['data' => $data]);
                break;
            }

            if (isset($data['type']) && $data['type'] === 'LOCATIONS') {
                $this->error("Careerjet API location issue: " . ($data['message'] ?? 'Check location parameter'));
                break;
            }

            $results = $data['jobs'] ?? [];

            if (empty($results)) {
                $this->info("No more jobs found on page {$page}. Stopping.");
                break;
            }

            foreach ($results as $result) {
                $jobTitle = $result['title'] ?? 'Unknown Title';
                $companyName = $result['company'] ?? 'Unknown Company';
                $description = $result['description'] ?? '';
                $location = $result['locations'] ?? $where;

                // Careerjet URLs change on every request, and there is no unique job ID returned.
                // We prevent duplicates by checking the title, company, and location.
                $existingJob = Job::where('title', $jobTitle)
                    ->where('company_name', $companyName)
                    ->where('location', $location)
                    ->first();

                $redirectUrl = $result['url'] ?? '';
                $createdAt = isset($result['date']) ? \Carbon\Carbon::parse($result['date'])->format('Y-m-d H:i:s') : now();

                // Extract salary if available
                $salaryString = $result['salary'] ?? '';
                $salaryFrom = null;
                $salaryTo = null;
                $salaryCurrency = null;

                if (!empty($salaryString)) {
                    // Try to parse out the numbers
                    preg_match_all('/[\d,.]+/', $salaryString, $matches);
                    if (!empty($matches[0])) {
                        $numbers = array_map(function ($num) {
                            return (float) str_replace(',', '', $num);
                        }, $matches[0]);

                        $numbers = array_filter($numbers);
                        sort($numbers);

                        if (count($numbers) > 0) {
                            $salaryFrom = $numbers[0];
                            $salaryTo = count($numbers) > 1 ? end($numbers) : $salaryFrom;
                        }
                    }

                    if (strpos($salaryString, '$') !== false) {
                        $salaryCurrency = 'CAD';
                    } elseif (strpos($salaryString, '€') !== false) {
                        $salaryCurrency = 'EUR';
                    } elseif (strpos($salaryString, '£') !== false) {
                        $salaryCurrency = 'GBP';
                    }
                }

                if ($existingJob) {
                    $existingJob->update([
                        'description' => $description,
                        'application_url' => $redirectUrl,
                        'salary_from' => $salaryFrom,
                        'salary_to' => $salaryTo,
                        'salary_currency' => $salaryCurrency,
                        'display_end_date' => now()->addDays(30),
                        'expiry_date' => now()->addDays(30),
                        'json_object' => json_encode($result),
                        'updated_at' => now(),
                    ]);
                    $totalSkipped++; // Keeping it as skipped for the final count, but let's log it.
                    $this->info("Updated existing job: {$jobTitle} at {$companyName}");
                    continue; // Done updating, move to next
                }

                // Find or create company so it shows up on frontend
                $companySlug = \Illuminate\Support\Str::slug($companyName, '-');
                $company = \App\Company::firstOrCreate(
                    ['name' => $companyName],
                    [
                        'email' => $companySlug . '-' . time() . '@carejet.ca',
                        'is_active' => 1,
                        'slug' => $companySlug . '-' . time(),
                        'password' => bcrypt(\Illuminate\Support\Str::random(16))
                    ]
                );

                // Create the job
                $job = Job::create([
                    'title' => $jobTitle,
                    'company_name' => $companyName,
                    'company_id' => $company->id,
                    'description' => $description,
                    'location' => $location,
                    'application_url' => $redirectUrl,
                    'reference' => null,
                    'external_job' => 'yes',
                    'is_active' => 1,
                    'is_draft' => 0,
                    'salary_from' => $salaryFrom,
                    'salary_to' => $salaryTo,
                    'salary_currency' => $salaryCurrency,
                    'display_duration_days' => 30,
                    'display_end_date' => now()->addDays(30),
                    'expiry_date' => now()->addDays(30),
                    'created_at' => $createdAt,
                    'json_object' => json_encode($result),
                ]);

                $job->slug = \Illuminate\Support\Str::slug($jobTitle, '-') . '-' . $job->id;

                // Map basic fields based on search parameters so it shows up in filters
                if (stripos($location, 'Edmonton') !== false) {
                    $job->medo_city_id = 2; // Edmonton
                    $job->medo_province_id = 1; // Alberta
                    $job->city_id = 10125;
                }
                if (stripos($jobTitle, 'Health Care Aide') !== false || stripos($jobTitle, 'HCA') !== false || stripos($what, 'health care aide') !== false) {
                    $job->medo_category_id = 1; // HCA
                    $job->functional_area_id = 655;
                }

                $job->save();
                $this->info("Imported NEW job: {$jobTitle} at {$companyName}");
                $totalImported++;
            }
        }

        $this->info("Fetch complete. Imported: {$totalImported}, Skipped (already exist): {$totalSkipped}");

        return 0;
    }
}
