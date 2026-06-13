<?php

namespace App\Http\Controllers;

use App\Job;
use App\City;
use App\State;

class HealthcareHubController extends Controller
{
    const PER_PAGE = 20;
    const NOINDEX_THRESHOLD = 5;
    const ALBERTA_STATE_ID = 663;
    const ALBERTA_SLUG = 'alberta';

    public function alberta()
    {
        $state = State::where('state_id', self::ALBERTA_STATE_ID)->first();
        $stateName = $state ? $state->state : 'Alberta';

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

        $cityNames = $this->activeCityNames();
        $cityList = $cityNames->join(', ');

        $metaTitle = 'Healthcare Jobs in ' . $stateName . ' | Medojob';
        $metaDescription = 'Browse current healthcare jobs across ' . $stateName . ', including HCA, LPN, RN, and other healthcare careers in ' . $cityList . ', and surrounding communities.';

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
        $cityModel = City::where('slug', $city)
            ->where('state_id', self::ALBERTA_STATE_ID)
            ->where('is_active', 1)
            ->first();

        if (!$cityModel) {
            abort(404);
        }

        $cityName = $cityModel->city;

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

        $roleLinks = $this->roleLinks($city, $cityName);
        $relatedCities = $this->relatedCities($city, $cityModel->city_id);

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

    private function activeCities(): \Illuminate\Database\Eloquent\Collection
    {
        return City::where('state_id', self::ALBERTA_STATE_ID)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('city')
            ->get();
    }

    private function activeCityNames(): \Illuminate\Support\Collection
    {
        return $this->activeCities()->pluck('city');
    }

    private function cityLinks(): array
    {
        $links = [];
        foreach ($this->activeCities() as $city) {
            $links[] = [
                'label' => $city->city,
                'url' => url('/healthcare-jobs-' . $city->slug),
            ];
        }
        return $links;
    }

    private function roleLinks(string $citySlug, string $cityName): array
    {
        $links = [];
        foreach (config('seo_locations.roles') as $roleSlug => $roleLabel) {
            $links[] = [
                'label' => $this->shortRoleLabel($roleSlug) . ' Jobs in ' . $cityName,
                'url' => url('/' . $roleSlug . '-jobs-' . $citySlug),
            ];
        }
        return $links;
    }

    private function relatedCities(string $currentSlug, int $currentCityId): array
    {
        $links = [
            ['label' => 'Alberta (All)', 'url' => url('/healthcare-jobs-alberta')],
        ];

        foreach ($this->activeCities() as $city) {
            if ($city->slug === $currentSlug) {
                continue;
            }
            $links[] = [
                'label' => $city->city,
                'url' => url('/healthcare-jobs-' . $city->slug),
            ];
        }

        return $links;
    }

    private function shortRoleLabel(string $slug): string
    {
        $short = [
            'hca' => 'HCA',
            'lpn' => 'LPN',
            'rn' => 'RN',
        ];
        return $short[$slug] ?? strtoupper($slug);
    }
}
