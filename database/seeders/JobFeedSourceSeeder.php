<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobFeedSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            [
                'name' => 'Example Healthcare Jobs',
                'slug' => 'example-healthcare',
                'provider' => 'custom',
                'source_url' => 'https://example.com/healthcare-jobs.xml',
                'config' => json_encode([
                    'format' => 'xml',
                    'mapping' => [
                        'title' => 'job_title',
                        'description' => 'job_description',
                        'location' => 'location',
                    ]
                ]),
                'is_active' => false, // Set to true when ready
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more job feed sources here
        ];

        DB::table('job_feed_sources')->insert($sources);
    }
}
