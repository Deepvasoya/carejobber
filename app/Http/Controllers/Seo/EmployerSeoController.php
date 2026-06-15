<?php

namespace App\Http\Controllers\Seo;

use App\Company;
use App\Job;
use App\Http\Controllers\Controller;

class EmployerSeoController extends Controller
{
    const PER_PAGE = 20;
    const NOINDEX_THRESHOLD = 5;

    public function show(string $slug)
    {
        $company = Company::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        $jobsQuery = Job::with('company')
            ->where('company_id', $company->id)
            ->where('is_active', 1)
            ->where('is_draft', 0)
            ->where(function ($q) {
                $q->whereNull('display_end_date')
                    ->orWhere('display_end_date', '>=', now());
            })
            ->notExpire();

        $jobCount = (clone $jobsQuery)->count();

        Job::orderByPromotionPriority($jobsQuery);
        $jobs = $jobsQuery->paginate(self::PER_PAGE);

        $noIndex = $jobCount < self::NOINDEX_THRESHOLD;

        $metaTitle = $company->name . ' Jobs and Careers | Medojob';
        $metaDescription = 'Browse current ' . $company->name . ' jobs, healthcare careers, hiring locations, and employment opportunities on Medojob.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $company, $noIndex);

        $relatedEmployers = $this->relatedEmployers($company->id);

        return view('seo.employer-show')
            ->with('company', $company)
            ->with('jobs', $jobs)
            ->with('jobCount', $jobCount)
            ->with('noIndex', $noIndex)
            ->with('metaTitle', $metaTitle)
            ->with('metaDescription', $metaDescription)
            ->with('seo', $seo)
            ->with('relatedEmployers', $relatedEmployers);
    }

    private function buildSeo(string $title, string $description, Company $company, bool $noIndex): object
    {
        $robots = $noIndex
            ? '<meta name="robots" content="noindex,follow">'
            : '<meta name="robots" content="index,follow">';

        $canonical = '<link rel="canonical" href="' . e(url('/employers/' . $company->slug)) . '">';

        return (object) [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => $company->name . ' jobs, ' . $company->name . ' careers, healthcare jobs',
            'seo_other' => $robots . "\n" . $canonical,
        ];
    }

    private function relatedEmployers(int $currentCompanyId): array
    {
        $links = [];

        $others = Company::where('id', '!=', $currentCompanyId)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereHas('jobs', function ($q) {
                $q->where('is_active', 1)
                    ->where('is_draft', 0)
                    ->where(function ($sq) {
                        $sq->whereNull('display_end_date')
                            ->orWhere('display_end_date', '>=', now());
                    })
                    ->notExpire();
            })
            ->orderBy('name')
            ->limit(28)
            ->get();

        foreach ($others as $other) {
            $links[] = [
                'label' => $other->name . ' Jobs',
                'url' => url('/employers/' . $other->slug),
            ];
        }

        return $links;
    }
}
