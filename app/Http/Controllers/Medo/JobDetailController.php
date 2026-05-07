<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;
use App\Models\Medo\Category;
use App\Models\Medo\Province;
use App\Models\Medo\City;
use App\Job;
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
            $job->medo_category_id === $category->id
            && $job->medo_province_id === $province->id
            && $job->medo_city_id === $city->id,
            404
        );

        // Expired jobs return 410 Gone (signals to Google to remove from index)
        if ($job->expiry_date <= now()) {
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

        $job->load('medoEmployer', 'medoCategory', 'medoCity', 'medoProvince');

        $empName = $job->medoEmployer ? $job->medoEmployer->name : 'Confidential Employer';
        $seoTitle = "{$job->title} at {$empName} in {$city->name} | Medojob";
        $seoDesc = "Apply for the {$job->title} position at {$empName} in {$city->name}, {$province->name}. Click to view salary, requirements, and apply directly.";
        
        $seo = (object)[
            'seo_title' => $seoTitle,
            'seo_description' => $seoDesc,
            'seo_keywords' => "{$job->title}, {$empName} jobs, {$city->name} jobs",
            'seo_other' => '
                <meta property="og:title" content="'.e($seoTitle).'"/>
                <meta property="og:description" content="'.e($seoDesc).'"/>
                <meta property="og:site_name" content="Medojob"/>
                <meta property="og:url" content="'.url()->current().'"/>
            '
        ];

        return response()
            ->view('medo.jobs.job-detail', [
                'job' => $job,
                'category' => $category,
                'province' => $province,
                'city' => $city,
                'employer' => $job->medoEmployer,
                'relatedJobs' => $related->forJob($job),
                'breadcrumbs' => $this->breadcrumbs($category, $province, $city, $job),
                'seo' => $seo,
            ])
            ->header('Cache-Control', 'public, max-age=3600, stale-while-revalidate=86400');
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
