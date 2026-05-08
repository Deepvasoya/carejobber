<?php

namespace App\Services\Medo;

use App\Models\Medo\Job;
use Illuminate\Support\Collection;

class RelatedJobsService
{
    public function forJob(Job $job): array
    {
        return [
            'same_city' => $this->sameCityDifferentCategory($job),
            'same_category' => $this->sameCategoryDifferentCity($job),
            'same_province' => $this->sameProvinceDifferentCityCategory($job),
        ];
    }

    private function sameCityDifferentCategory(Job $job): Collection
    {
        return Job::where('city_id', $job->city_id)
            ->where('category_id', '!=', $job->category_id)
            ->where('id', '!=', $job->id)
            ->where('expires_at', '>', now())
            ->with(['employer', 'category'])
            ->orderByDesc('posted_at')
            ->limit(3)
            ->get();
    }

    private function sameCategoryDifferentCity(Job $job): Collection
    {
        return Job::where('category_id', $job->category_id)
            ->where('city_id', '!=', $job->city_id)
            ->where('province_id', $job->province_id)
            ->where('id', '!=', $job->id)
            ->where('expires_at', '>', now())
            ->with(['employer', 'city'])
            ->orderByDesc('posted_at')
            ->limit(3)
            ->get();
    }

    private function sameProvinceDifferentCityCategory(Job $job): Collection
    {
        return Job::where('province_id', $job->province_id)
            ->where(function($q) use ($job) {
                $q->where('city_id', '!=', $job->city_id)
                  ->orWhere('category_id', '!=', $job->category_id);
            })
            ->where('id', '!=', $job->id)
            ->where('expires_at', '>', now())
            ->with(['employer', 'category', 'city'])
            ->orderByDesc('posted_at')
            ->limit(3)
            ->get();
    }
}
