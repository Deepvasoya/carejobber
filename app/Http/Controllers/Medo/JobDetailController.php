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
        Category $medo_category,
        Province $medo_province,
        City $medo_city,
        Job $medo_job,
        RelatedJobsService $related
    ) {
        // Validate the URL hierarchy matches the job
        abort_unless(
            $medo_job->category_id === $medo_category->id
            && $medo_job->province_id === $medo_province->id
            && $medo_job->city_id === $medo_city->id,
            404
        );

        // Expired jobs return 410 Gone (signals to Google to remove from index)
        if ($medo_job->expires_at <= now()) {
            return response()
                ->view('medo.jobs.expired', compact('category', 'province', 'city', 'job'))
                ->setStatusCode(410)
                ->header('X-Robots-Tag', 'noindex');
        }

        $medo_job->load('employer', 'category', 'city', 'province');

        return view('medo.jobs.job-detail', [
            'job' => $medo_job,
            'category' => $medo_category,
            'province' => $medo_province,
            'city' => $medo_city,
            'employer' => $medo_job->employer,
            'relatedJobs' => $related->forJob($medo_job),
            'breadcrumbs' => $this->breadcrumbs($medo_category, $medo_province, $medo_city, $medo_job),
        ]);
    }

    private function breadcrumbs(Category $medo_category, Province $medo_province, City $medo_city, Job $medo_job): array
    {
        return [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Jobs', 'url' => route('medo.jobs.index')],
            ['label' => $medo_category->name, 'url' => route('medo.jobs.category', $medo_category)],
            ['label' => $medo_province->name, 'url' => route('medo.jobs.category.province', [$medo_category, $medo_province])],
            ['label' => $medo_city->name, 'url' => route('medo.jobs.category.province.city', [$medo_category, $medo_province, $medo_city])],
            ['label' => $medo_job->title, 'url' => null], // current page
        ];
    }
}
