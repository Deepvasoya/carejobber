<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Job;

class HcaJobsEdmontonController extends Controller
{
    const HCA_FUNCTIONAL_AREA_ID = 655;
    const EDMONTON_CITY_ID = 10125;
    const PER_PAGE = 20;
    const NOINDEX_THRESHOLD = 5;

    public function index()
    {
        $query = Job::with('company')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_draft', 0)
            ->where('jobs.functional_area_id', self::HCA_FUNCTIONAL_AREA_ID)
            ->where('jobs.city_id', self::EDMONTON_CITY_ID)
            ->where(function ($q) {
                $q->whereNull('jobs.display_end_date')
                    ->orWhere('jobs.display_end_date', '>=', now());
            })
            ->notExpire();

        $jobCount = (clone $query)->count();

        Job::orderByPromotionPriority($query);
        $jobs = $query->paginate(self::PER_PAGE);

        $noindex = $jobCount < self::NOINDEX_THRESHOLD;

        $seo = $this->buildSeo($noindex);

        return view('seo.hca_jobs_edmonton')
            ->with('jobs', $jobs)
            ->with('jobCount', $jobCount)
            ->with('seo', $seo);
    }

    private function buildSeo(bool $noindex): object
    {
        $title = 'HCA Jobs in Edmonton | Medojob';
        $description = 'Looking for HCA jobs in Edmonton? Browse current Health Care Aide opportunities in hospitals, long-term care homes, home care agencies, and healthcare facilities across Edmonton.';

        $robots = $noindex
            ? '<meta name="robots" content="noindex,follow">'
            : '<meta name="robots" content="index,follow">';

        $canonical = '<link rel="canonical" href="' . e(url('/hca-jobs-edmonton')) . '">';

        return (object) [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => 'HCA jobs Edmonton, Health Care Aide Edmonton, HCA careers Edmonton, healthcare jobs Edmonton',
            'seo_other' => $robots . "\n" . $canonical,
        ];
    }
}
