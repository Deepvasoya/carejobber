<?php

namespace App\Http\Controllers;

use App\Job;
use App\City;

class HealthcareHubController extends Controller
{
    const PER_PAGE = 20;
    const NOINDEX_THRESHOLD = 5;
    const ALBERTA_STATE_ID = 663;

    const ALLOWED_CITIES = [
        'edmonton' => 'Edmonton',
        'calgary' => 'Calgary',
        'red-deer' => 'Red Deer',
        'lethbridge' => 'Lethbridge',
        'medicine-hat' => 'Medicine Hat',
    ];

    public function alberta()
    {
        $jobsQuery = Job::with('company')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_draft', 0)
            ->where('jobs.state_id', self::ALBERTA_STATE_ID)
            ->where(function ($q) {
                $q->whereNull('jobs.display_end_date')
                    ->orWhere('jobs.display_end_date', '>=', now());
            })
            ->notExpire();

        $jobCount = (clone $jobsQuery)->count();

        Job::orderByPromotionPriority($jobsQuery);
        $jobs = $jobsQuery->paginate(self::PER_PAGE);

        $noIndex = $jobCount < self::NOINDEX_THRESHOLD;

        $metaTitle = 'Healthcare Jobs in Alberta | Medojob';
        $metaDescription = 'Browse current healthcare jobs across Alberta, including HCA, LPN, RN, and other healthcare careers in Edmonton, Calgary, Red Deer, Lethbridge, Medicine Hat, and surrounding communities.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $noIndex);

        $cityLinks = $this->cityLinks();

        return view('seo.healthcare-alberta')
            ->with('jobs', $jobs)
            ->with('jobCount', $jobCount)
            ->with('noIndex', $noIndex)
            ->with('metaTitle', $metaTitle)
            ->with('metaDescription', $metaDescription)
            ->with('seo', $seo)
            ->with('cityLinks', $cityLinks);
    }

    public function city($city)
    {
        if (!array_key_exists($city, self::ALLOWED_CITIES)) {
            abort(404);
        }

        $cityName = self::ALLOWED_CITIES[$city];

        $cityModel = City::where('city', $cityName)
            ->where('state_id', self::ALBERTA_STATE_ID)
            ->where('is_active', 1)
            ->first();

        if (!$cityModel) {
            abort(404);
        }

        $jobsQuery = Job::with('company')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_draft', 0)
            ->where('jobs.state_id', self::ALBERTA_STATE_ID)
            ->where('jobs.city_id', $cityModel->city_id)
            ->where(function ($q) {
                $q->whereNull('jobs.display_end_date')
                    ->orWhere('jobs.display_end_date', '>=', now());
            })
            ->notExpire();

        $jobCount = (clone $jobsQuery)->count();

        Job::orderByPromotionPriority($jobsQuery);
        $jobs = $jobsQuery->paginate(self::PER_PAGE);

        $noIndex = $jobCount < self::NOINDEX_THRESHOLD;

        $metaTitle = 'Healthcare Jobs in ' . $cityName . ' | Medojob';
        $metaDescription = 'Browse current healthcare jobs in ' . $cityName . ', including HCA, LPN, RN, hospital, long-term care, home care, and clinic positions.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $noIndex);

        $roleLinks = $this->roleLinks($city);
        $relatedCities = $this->relatedCities($city);

        return view('seo.healthcare-city')
            ->with('city', $city)
            ->with('cityName', $cityName)
            ->with('jobs', $jobs)
            ->with('jobCount', $jobCount)
            ->with('noIndex', $noIndex)
            ->with('metaTitle', $metaTitle)
            ->with('metaDescription', $metaDescription)
            ->with('seo', $seo)
            ->with('roleLinks', $roleLinks)
            ->with('relatedCities', $relatedCities);
    }

    private function buildSeo(string $title, string $description, bool $noIndex): object
    {
        $robots = $noIndex
            ? '<meta name="robots" content="noindex,follow">'
            : '<meta name="robots" content="index,follow">';

        return (object) [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => $description,
            'seo_other' => $robots,
        ];
    }

    private function cityLinks(): array
    {
        return [
            ['label' => 'Edmonton', 'url' => url('/healthcare-jobs-edmonton')],
            ['label' => 'Calgary', 'url' => url('/healthcare-jobs-calgary')],
            ['label' => 'Red Deer', 'url' => url('/healthcare-jobs-red-deer')],
            ['label' => 'Lethbridge', 'url' => url('/healthcare-jobs-lethbridge')],
            ['label' => 'Medicine Hat', 'url' => url('/healthcare-jobs-medicine-hat')],
        ];
    }

    private function roleLinks(string $city): array
    {
        return [
            ['label' => 'HCA Jobs in ' . self::ALLOWED_CITIES[$city], 'url' => url('/hca-jobs-' . $city)],
            ['label' => 'LPN Jobs in ' . self::ALLOWED_CITIES[$city], 'url' => url('/lpn-jobs-' . $city)],
            ['label' => 'RN Jobs in ' . self::ALLOWED_CITIES[$city], 'url' => url('/rn-jobs-' . $city)],
        ];
    }

    private function relatedCities(string $currentCity): array
    {
        $links = [
            ['label' => 'Alberta (All)', 'url' => url('/healthcare-jobs-alberta')],
        ];

        foreach (self::ALLOWED_CITIES as $slug => $name) {
            if ($slug === $currentCity) {
                continue;
            }
            $links[] = [
                'label' => $name,
                'url' => url('/healthcare-jobs-' . $slug),
            ];
        }

        return $links;
    }
}
