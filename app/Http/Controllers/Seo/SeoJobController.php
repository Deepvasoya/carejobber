<?php

namespace App\Http\Controllers\Seo;

use App\City;
use App\FunctionalArea;
use App\Http\Controllers\Controller;
use App\Job;

class SeoJobController extends Controller
{
    const PER_PAGE = 20;
    const NOINDEX_THRESHOLD = 5;

    public function roleCity(string $role, string $city)
    {
        $allowedRoles = config('seo_locations.roles');
        $allowedCities = $this->allCities();

        if (!array_key_exists($role, $allowedRoles) || !array_key_exists($city, $allowedCities)) {
            abort(404);
        }

        $functionalArea = FunctionalArea::where('slug', $role)
            ->where('is_active', 1)
            ->first();

        $stateId = $this->firstStateId();

        $cityModel = City::where('slug', $city)
            ->where('state_id', $stateId)
            ->where('is_active', 1)
            ->first();

        if (! $functionalArea || ! $cityModel) {
            abort(404);
        }

        $query = Job::with('company')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_draft', 0)
            ->where('jobs.functional_area_id', $functionalArea->functional_area_id)
            ->where('jobs.city_id', $cityModel->city_id)
            ->where(function ($q) {
                $q->whereNull('jobs.display_end_date')
                    ->orWhere('jobs.display_end_date', '>=', now());
            })
            ->notExpire();

        $jobCount = (clone $query)->count();

        Job::orderByPromotionPriority($query);
        $jobs = $query->paginate(self::PER_PAGE);

        $noIndex = $jobCount < self::NOINDEX_THRESHOLD;
        $roleLabel = $allowedRoles[$role];
        $cityName = $cityModel->city;

        $metaTitle = strtoupper($role) . ' Jobs in ' . $cityName . ' | Medojob';
        $metaDescription = 'Find current ' . $roleLabel . ' jobs in ' . $cityName . '. Browse healthcare opportunities and apply online with Medojob.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $role, $city, $noIndex);

        $relatedLinks = $this->relatedLinks($role, $city, $cityName, $roleLabel);

        return view('seo.role-city')
            ->with('role', $role)
            ->with('city', $city)
            ->with('roleLabel', $roleLabel)
            ->with('cityName', $cityName)
            ->with('jobs', $jobs)
            ->with('jobCount', $jobCount)
            ->with('noIndex', $noIndex)
            ->with('metaTitle', $metaTitle)
            ->with('metaDescription', $metaDescription)
            ->with('relatedLinks', $relatedLinks)
            ->with('seo', $seo);
    }

    private function buildSeo(string $title, string $description, string $role, string $city, bool $noIndex): object
    {
        $robots = $noIndex
            ? '<meta name="robots" content="noindex,follow">'
            : '<meta name="robots" content="index,follow">';

        $canonical = '<link rel="canonical" href="' . e(route('seo.role.city', ['role' => $role, 'city' => $city])) . '">';

        return (object) [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => implode(', ', [
                strtoupper($role) . ' jobs ' . $city,
                $description,
            ]),
            'seo_other' => $robots . "\n" . $canonical,
        ];
    }

    private function relatedLinks(string $role, string $city, string $cityName, string $roleLabel): array
    {
        $links = [];
        $allCities = $this->allCities();

        $otherCities = array_filter($allCities, function ($name, $slug) use ($city) {
            return $slug !== $city;
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($otherCities as $otherSlug => $otherName) {
            $links[] = [
                'label' => strtoupper($role) . ' Jobs in ' . $otherName,
                'url' => route('seo.role.city', ['role' => $role, 'city' => $otherSlug]),
            ];
        }

        $otherRoles = array_filter(config('seo_locations.roles'), function ($label, $slug) use ($role) {
            return $slug !== $role;
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($otherRoles as $otherSlug => $otherLabel) {
            $links[] = [
                'label' => $this->shortRoleLabel($otherSlug) . ' Jobs in ' . $cityName,
                'url' => route('seo.role.city', ['role' => $otherSlug, 'city' => $city]),
            ];
        }

        $firstStateSlug = array_key_first(config('seo_locations.states'));
        $firstStateName = config('seo_locations.states')[$firstStateSlug]['name'];

        $links[] = [
            'label' => 'Healthcare Jobs in ' . $firstStateName,
            'url' => url('/healthcare-jobs-' . $firstStateSlug),
        ];

        return $links;
    }

    private function firstStateId(): int
    {
        $states = config('seo_locations.states');
        $key = array_key_first($states);
        return (int) $states[$key]['id'];
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
