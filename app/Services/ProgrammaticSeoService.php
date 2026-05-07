<?php

namespace App\Services;

use App\City;
use App\Company;
use App\FunctionalArea;
use App\Job;
use App\SeoGuide;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProgrammaticSeoService
{
    public function activeJobsQuery(?FunctionalArea $category = null, ?City $city = null): Builder
    {
        $query = Job::query()
            ->with('company')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_draft', 0)
            ->where(function ($q) {
                $q->whereNull('jobs.display_end_date')
                    ->orWhere('jobs.display_end_date', '>=', now());
            })
            ->notExpire();

        if ($category) {
            $query->where('jobs.functional_area_id', $this->categoryId($category));
        }

        if ($city) {
            $query->where('jobs.city_id', $this->cityId($city));
        }

        return $query;
    }

    public function categoryId(FunctionalArea $category): int
    {
        return (int) ($category->functional_area_id ?: $category->id);
    }

    public function cityId(City $city): int
    {
        return (int) ($city->city_id ?: $city->id);
    }

    public function categoryLabel(FunctionalArea $category): string
    {
        $slug = strtolower((string) $category->slug);
        if (in_array($slug, ['hca', 'lpn', 'rn'], true)) {
            return strtoupper($slug);
        }

        return (string) $category->functional_area;
    }

    public function cityLabel(?City $city): string
    {
        return $city ? (string) $city->city : 'Alberta';
    }

    public function salaryStats(Builder $query): array
    {
        $stats = (clone $query)
            ->where('hide_salary', 0)
            ->where('salary_from', '>', 0)
            ->where('salary_to', '>', 0)
            ->selectRaw('COUNT(*) as salary_count, AVG(salary_from) as avg_from, AVG(salary_to) as avg_to, MIN(salary_from) as min_from, MAX(salary_to) as max_to')
            ->first();

        return [
            'count' => (int) ($stats->salary_count ?? 0),
            'avg_from' => $this->money($stats->avg_from ?? null),
            'avg_to' => $this->money($stats->avg_to ?? null),
            'min_from' => $this->money($stats->min_from ?? null),
            'max_to' => $this->money($stats->max_to ?? null),
        ];
    }

    public function landingContent(FunctionalArea $category, ?City $city, int $jobCount, array $salary): array
    {
        $categoryLabel = $this->categoryLabel($category);
        $location = $this->cityLabel($city);
        $scope = $city ? "{$location}, Alberta" : 'Alberta';

        $intro = "{$categoryLabel} jobs in {$scope} are updated from active employer listings on Medojob. Use this page to compare openings, salary ranges, shifts, and employers hiring healthcare workers across Alberta.";

        if ($jobCount < 3) {
            $intro .= ' This page is available for users, but it is marked noindex until it has at least three active listings.';
        }

        return [
            'h1' => "{$categoryLabel} jobs in {$scope}",
            'intro' => $intro,
            'salary_heading' => "Typical {$categoryLabel} salary range in {$scope}",
            'faqs' => $this->faqs($categoryLabel, $scope, $jobCount, $salary),
        ];
    }

    public function seo(string $title, string $description, string $canonical, bool $noindex = false, string $extra = ''): object
    {
        $robots = $noindex
            ? '<meta name="robots" content="noindex,follow">'
            : '<meta name="robots" content="index,follow">';

        $canonicalTag = '<link rel="canonical" href="' . e($canonical) . '">';

        return (object) [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => '',
            'seo_other' => $robots . "\n" . $canonicalTag . ($extra ? "\n" . $extra : ''),
        ];
    }

    public function internalLinks(FunctionalArea $category, ?City $city = null): array
    {
        $categoryId = $this->categoryId($category);
        $cityLinks = City::query()
            ->select('cities.*')
            ->whereNotNull('slug')
            ->where('is_active', 1)
            ->whereIn('city_id', function ($q) use ($categoryId) {
                $q->select('city_id')
                    ->from('jobs')
                    ->where('is_active', 1)
                    ->where('is_draft', 0)
                    ->where('functional_area_id', $categoryId)
                    ->whereDate('expiry_date', '>', now())
                    ->groupBy('city_id')
                    ->havingRaw('COUNT(*) >= 3');
            })
            ->orderBy('city')
            ->limit(10)
            ->get()
            ->map(function (City $linkCity) use ($category) {
                    return [
                        'label' => $this->categoryLabel($category) . ' jobs in ' . $linkCity->city,
                        'url' => route('jobs.category.province.city', [$category->slug, 'ab', $linkCity->slug]),
                    ];
                })
            ->all();

        $categoryLinks = [];
        if ($city) {
            $cityId = $this->cityId($city);
            $categoryLinks = FunctionalArea::query()
                ->select('functional_areas.*')
                ->whereNotNull('slug')
                ->where('is_active', 1)
                ->whereIn('functional_area_id', function ($q) use ($cityId) {
                    $q->select('functional_area_id')
                        ->from('jobs')
                        ->where('is_active', 1)
                        ->where('is_draft', 0)
                        ->where('city_id', $cityId)
                        ->whereDate('expiry_date', '>', now())
                        ->groupBy('functional_area_id')
                        ->havingRaw('COUNT(*) >= 3');
                })
                ->orderBy('functional_area')
                ->limit(10)
                ->get()
                ->map(function (FunctionalArea $linkCategory) use ($city) {
                    return [
                        'label' => $this->categoryLabel($linkCategory) . ' jobs in ' . $city->city,
                        'url' => route('jobs.category.province.city', [$linkCategory->slug, 'ab', $city->slug]),
                    ];
                })
                ->all();
        }

        return array_merge($cityLinks, $categoryLinks);
    }

    public function jobPostingJsonLd(Job $job): string
    {
        $company = $job->getCompany();
        $city = $job->getCity();
        $state = $job->getState();
        $country = $job->getCountry();

        $payload = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => trim(strip_tags((string) $job->description)),
            'datePosted' => optional($job->created_at)->toDateString(),
            'validThrough' => optional($job->expiry_date)->toDateString(),
            'employmentType' => $job->getJobType('job_type') ?: null,
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $company ? $company->name : config('app.name'),
                'sameAs' => $company ? route('employers.show', $company->slug) : url('/'),
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $city ? $city->city : null,
                    'addressRegion' => $state ? $state->state : 'Alberta',
                    'addressCountry' => $country ? $country->country : 'CA',
                ],
            ],
        ];

        if (! (bool) $job->hide_salary && (int) $job->salary_from > 0 && (int) $job->salary_to > 0) {
            $payload['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => $job->salary_currency ?: 'CAD',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => (int) $job->salary_from,
                    'maxValue' => (int) $job->salary_to,
                    'unitText' => strtoupper((string) ($job->getSalaryPeriod('salary_period') ?: 'YEAR')),
                ],
            ];
        }

        $payload = array_filter($payload, function ($value) {
            return $value !== null && $value !== '';
        });

        return '<script type="application/ld+json">' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public function guideSeo(SeoGuide $guide): object
    {
        return $this->seo(
            $guide->seo_title ?: $guide->title,
            $guide->seo_description ?: Str::limit(strip_tags((string) $guide->excerpt ?: (string) $guide->body), 155),
            route('seo.guide', $guide->slug)
        );
    }

    public function employerSeo(Company $company): object
    {
        $city = $company->getCity('city') ?: 'Alberta';

        return $this->seo(
            $company->name . ' healthcare jobs in ' . $city,
            'View active healthcare job postings from ' . $company->name . ' on Medojob.',
            route('employers.show', $company->slug)
        );
    }

    private function faqs(string $category, string $scope, int $jobCount, array $salary): array
    {
        $range = $salary['count'] > 0
            ? '$' . number_format($salary['avg_from']) . ' to $' . number_format($salary['avg_to'])
            : 'varies by employer, shift type, and experience';

        return [
            [
                'question' => "How many {$category} jobs are available in {$scope}?",
                'answer' => "There are {$jobCount} active {$category} listings currently available in {$scope} on Medojob.",
            ],
            [
                'question' => "What do {$category} jobs in {$scope} typically pay?",
                'answer' => "Based on visible salary data, the typical posted range {$range}. Some employers hide salary until interview.",
            ],
            [
                'question' => "Which employers hire {$category} workers in {$scope}?",
                'answer' => 'Hospitals, continuing care facilities, home care providers, clinics, and community health organizations regularly post healthcare roles on Medojob.',
            ],
        ];
    }

    private function money($value): int
    {
        return (int) round((float) $value);
    }
}
