<?php

namespace App\Http\Controllers;

use App\Job;
use App\City;

class HealthcareHubController extends Controller
{
    const PER_PAGE = 20;
    const NOINDEX_THRESHOLD = 5;

    public function alberta()
    {
        $firstState = $this->firstState();
        $stateId = $firstState['id'];
        $stateName = $firstState['name'];

        $jobsQuery = Job::with('company')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_draft', 0)
            ->where('jobs.state_id', $stateId)
            ->where(function ($q) {
                $q->whereNull('jobs.display_end_date')
                    ->orWhere('jobs.display_end_date', '>=', now());
            })
            ->notExpire();

        $jobCount = (clone $jobsQuery)->count();

        Job::orderByPromotionPriority($jobsQuery);
        $jobs = $jobsQuery->paginate(self::PER_PAGE);

        $noIndex = $jobCount < self::NOINDEX_THRESHOLD;

        $metaTitle = 'Healthcare Jobs in ' . $stateName . ' | Medojob';
        $metaDescription = 'Browse current healthcare jobs across ' . $stateName . ', including HCA, LPN, RN, and other healthcare careers in ' . $this->cityNames()->join(', ') . ', and surrounding communities.';

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
        $allowedCities = $this->allCities();

        if (!array_key_exists($city, $allowedCities)) {
            abort(404);
        }

        $cityName = $allowedCities[$city];

        $cityModel = City::where('city', $cityName)
            ->where('state_id', $this->firstState()['id'])
            ->where('is_active', 1)
            ->first();

        if (!$cityModel) {
            abort(404);
        }

        $jobsQuery = Job::with('company')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_draft', 0)
            ->where('jobs.state_id', $this->firstState()['id'])
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

    private function firstState(): array
    {
        $states = config('seo_locations.states');
        $key = array_key_first($states);
        return $states[$key];
    }

    private function allCities(): array
    {
        $cities = [];
        foreach (config('seo_locations.states') as $state) {
            foreach ($state['cities'] as $slug => $name) {
                $cities[$slug] = $name;
            }
        }
        return $cities;
    }

    private function cityNames(): \Illuminate\Support\Collection
    {
        return collect($this->allCities())->values();
    }

    private function cityLinks(): array
    {
        $links = [];
        foreach ($this->allCities() as $slug => $name) {
            $links[] = [
                'label' => $name,
                'url' => url('/healthcare-jobs-' . $slug),
            ];
        }
        return $links;
    }

    private function roleLinks(string $city): array
    {
        $links = [];
        foreach (config('seo_locations.roles') as $roleSlug => $roleLabel) {
            $shortLabel = $this->shortRoleLabel($roleSlug, $roleLabel);
            $links[] = [
                'label' => $shortLabel . ' Jobs in ' . $this->allCities()[$city],
                'url' => url('/' . $roleSlug . '-jobs-' . $city),
            ];
        }
        return $links;
    }

    private function relatedCities(string $currentCity): array
    {
        $links = [
            ['label' => $this->firstState()['name'] . ' (All)', 'url' => url('/healthcare-jobs-' . array_key_first(config('seo_locations.states')))],
        ];

        foreach ($this->allCities() as $slug => $name) {
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

    private function shortRoleLabel(string $slug, string $fullLabel): string
    {
        $short = [
            'hca' => 'HCA',
            'lpn' => 'LPN',
            'rn' => 'RN',
        ];
        return $short[$slug] ?? strtoupper($slug);
    }

    private function roles(): array
    {
        return config('seo_locations.roles');
    }
}
