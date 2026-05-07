<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IndexNowPing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indexnow:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping IndexNow API with recently updated jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting IndexNow ping...');

        // Find jobs updated in the last 15 minutes
        $recentJobs = \App\Job::where('updated_at', '>=', now()->subMinutes(15))
            ->where('expiry_date', '>', now())
            ->get();

        if ($recentJobs->isEmpty()) {
            $this->info('No recent jobs to ping.');
            return 0;
        }

        $urls = [];
        foreach ($recentJobs as $job) {
            $category = \App\Models\Medo\Category::find($job->medo_category_id);
            $province = \App\Models\Medo\Province::find($job->medo_province_id);
            $city = \App\Models\Medo\City::find($job->medo_city_id);

            if ($category && $province && $city) {
                // Ensure correct pSEO URL format
                $urls[] = route('jobs.detail', [$category->slug, $province->slug, $city->slug, $job->slug]);
            }
        }

        if (empty($urls)) {
            $this->info('No valid URLs generated.');
            return 0;
        }

        $host = parse_url(config('app.url'), PHP_URL_HOST) ?? 'medojob.com';
        $key = config('services.indexnow.key', '1234567890'); // Fallback dummy key if not set

        $payload = [
            'host' => $host,
            'key' => $key,
            'keyLocation' => config('app.url') . '/' . $key . '.txt',
            'urlList' => $urls
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::post('https://api.indexnow.org/indexnow', $payload);

            if ($response->successful()) {
                $this->info('Successfully pinged IndexNow with ' . count($urls) . ' URLs.');
            } else {
                $this->error('Failed to ping IndexNow: ' . $response->body());
            }
        } catch (\Throwable $e) {
            $this->error('Error pinging IndexNow: ' . $e->getMessage());
        }

        return 0;
    }
}
