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
            $job->category_id === $category->id &&
            $job->province_id === $province->id &&
            $job->city_id === $city->id,
            404
        );

        // Expired jobs return 410 Gone (signals to Google to remove from index)
        if ($job->expires_at <= now()) {
            return response()
                ->view('medo.jobs.expired', compact('category', 'province', 'city', 'job'), 410)
                ->header('X-Robots-Tag', 'noindex');
        }

        $job->load('employer', 'category', 'city', 'province');

        // Dynamic SEO
        $seoTitle = "{$job->title} — {$job->employer->name}, {$city->name} | Medojob";
        $seoDesc = \Illuminate\Support\Str::limit(strip_tags($job->description), 155);
        
        $seo = (object)[
            'seo_title' => $seoTitle,
            'seo_description' => $seoDesc,
            'seo_keywords' => "{$job->title}, {$category->name} jobs, {$city->name} jobs, {$job->employer->name}",
            'seo_other' => '
                <meta property="og:type" content="website"/>
                <meta property="og:title" content="'.e($seoTitle).'"/>
                <meta property="og:description" content="'.e($seoDesc).'"/>
                <meta property="og:site_name" content="Medojob"/>
                <meta property="og:url" content="'.url()->current().'"/>
                <meta name="twitter:card" content="summary"/>
                <meta name="twitter:title" content="'.e($seoTitle).'"/>
                <meta name="twitter:description" content="'.e($seoDesc).'"/>
            '
        ];

        return view('medo.jobs.job-detail', [
            'job' => $job,
            'category' => $category,
            'province' => $province,
            'city' => $city,
            'employer' => $job->employer,
            'relatedJobs' => $related->forJob($job),
            'breadcrumbs' => $this->breadcrumbs($category, $province, $city, $job),
            'seo' => $seo,
        ]);
    }

    private function breadcrumbs(Category $category, Province $province, City $city, Job $job): array
    {
        return [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Jobs', 'url' => url('/jobs')],
            ['label' => $category->name, 'url' => route('medo.jobs.category', $category)],
            ['label' => $province->name, 'url' => route('medo.jobs.category.province', [$category, $province])],
            ['label' => $city->name, 'url' => route('medo.jobs.category.province.city', [$category, $province, $city])],
            ['label' => $job->title, 'url' => null],
        ];
    }
}
