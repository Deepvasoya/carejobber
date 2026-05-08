<?php

namespace App\Console\Commands;

use App\Job;
use Illuminate\Console\Command;

class DiagnoseLegacyJobs extends Command
{
    protected $signature = 'jobs:diagnose-legacy';
    protected $description = 'Diagnose legacy jobs table to understand what jobs exist';

    public function handle()
    {
        $this->info('Diagnosing legacy jobs table...');
        $this->newLine();
        
        // Get all active jobs
        $activeJobs = Job::where('is_active', 1)
            ->where('expiry_date', '>', now())
            ->with('company', 'city', 'functionalArea')
            ->get();
        
        $this->info("Total active jobs: {$activeJobs->count()}");
        $this->newLine();
        
        // Show each job
        $this->table(
            ['ID', 'Title', 'City ID', 'City Name', 'Func Area ID', 'Func Area', 'Expires'],
            $activeJobs->map(function($job) {
                return [
                    $job->id,
                    substr($job->title, 0, 40),
                    $job->city_id,
                    $job->city ? $job->city->city_name : 'N/A',
                    $job->functional_area_id,
                    $job->functionalArea ? $job->functionalArea->functional_area : 'N/A',
                    $job->expiry_date->format('Y-m-d'),
                ];
            })->toArray()
        );
        
        $this->newLine();
        
        // Show Edmonton jobs specifically
        $edmontonJobs = $activeJobs->where('city_id', 10125);
        $this->info("Edmonton jobs (city_id=10125): {$edmontonJobs->count()}");
        
        if ($edmontonJobs->count() > 0) {
            $this->table(
                ['ID', 'Title', 'Functional Area ID', 'Functional Area'],
                $edmontonJobs->map(function($job) {
                    return [
                        $job->id,
                        $job->title,
                        $job->functional_area_id,
                        $job->functionalArea ? $job->functionalArea->functional_area : 'N/A',
                    ];
                })->toArray()
            );
        }
        
        $this->newLine();
        
        // Show functional area distribution
        $this->info('Functional area distribution:');
        $functionalAreas = $activeJobs->groupBy('functional_area_id')
            ->map(function($group) {
                $first = $group->first();
                return [
                    'id' => $first->functional_area_id,
                    'name' => $first->functionalArea ? $first->functionalArea->functional_area : 'N/A',
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count');
        
        $this->table(
            ['Func Area ID', 'Name', 'Count'],
            $functionalAreas->values()->toArray()
        );
        
        return 0;
    }
}
