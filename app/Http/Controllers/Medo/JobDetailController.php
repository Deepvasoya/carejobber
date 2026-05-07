<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Models\Medo\Category;
use App\Models\Medo\Province;
use App\Models\Medo\City;
use App\Models\Medo\Job;
use App\Services\Medo\RelatedJobsService;

class JobDetailController extends Controller
{
    public function show(
        Category $category,
        Province $province,
        City $city,
        Job $job,
        RelatedJobsService $related
    ) {
        // Validate the URL hierarchy matches the job
        abort_unless(
            $job->category_id === $category->id
            && $job->province_id === $province->id
            && $job->city_id === $city->id,
            404
        );

        // Expired jobs return 410 Gone (signals to Google to remove from index)
        if ($job->expires_at <= now()) {
            return response()
                ->view('medo.jobs.expired', [
                    'category' => $category,
                    'province' => $province,
                    'city' => $city,
                    'job' => $job,
                ])
                ->setStatusCode(410)
                ->header('X-Robots-Tag', 'noindex');
        }

        $job->load('employer', 'category', 'city', 'province');

        return view('medo.jobs.job-detail', [
            'job' => $job,
            'category' => $category,
            'province' => $province,
            'city' => $city,
            'employer' => $job->employer,
            'relatedJobs' => $related->forJob($job),
            'breadcrumbs' => $this->breadcrumbs($category, $province, $city, $job),
        ]);
    }

    private function breadcrumbs(Category $category, Province $province, City $city, Job $job): array
    {
        return [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Jobs', 'url' => url('/jobs')],
            ['label' => $category->name, 'url' => route('jobs.category', $category)],
            ['label' => $province->name, 'url' => route('jobs.category.province', [$category, $province])],
            ['label' => $city->name, 'url' => route('jobs.category.province.city', [$category, $province, $city])],
            ['label' => $job->title, 'url' => null],
        ];
    }
}
