<?php

namespace App\Services\Medo;

use App\Models\Medo\Job;
use Illuminate\Support\Collection;

class RelatedJobsService
{
    public function forJob(Job $job, int $limit = 3): Collection
    {
        // Simple stub for Phase 1. Return jobs in the same category and city.
        return Job::where('category_id', $job->category_id)
            ->where('city_id', $job->city_id)
            ->where('id', '!=', $job->id)
            ->active()
            ->take($limit)
            ->get();
    }
}
