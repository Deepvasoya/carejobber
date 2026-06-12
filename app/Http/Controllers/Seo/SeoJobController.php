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
    const ALBERTA_STATE_ID = 663;

    public function roleCity(string $role, string $city)
    {
        $functionalArea = FunctionalArea::where('slug', $role)
            ->where('is_active', 1)
            ->first();

        $cityModel = City::where('slug', $city)
            ->where('state_id', self::ALBERTA_STATE_ID)
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
        $roleLabel = $this->roleLabel($role, $functionalArea);
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

    private function roleLabel(string $role, FunctionalArea $functionalArea): string
    {
        $labels = [
            'hca' => 'Health Care Aide',
            'lpn' => 'Licensed Practical Nurse',
            'rn' => 'Registered Nurse',
        ];

        return $labels[$role] ?? $functionalArea->functional_area;
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
        $otherCity = $city === 'edmonton' ? 'calgary' : 'edmonton';
        $otherCityName = $otherCity === 'calgary' ? 'Calgary' : 'Edmonton';

        $links = [];

        $links[] = [
            'label' => strtoupper($role) . ' Jobs in ' . $otherCityName,
            'url' => route('seo.role.city', ['role' => $role, 'city' => $otherCity]),
        ];

        $otherRoles = array_filter(['hca', 'lpn', 'rn'], function ($r) use ($role) {
            return $r !== $role;
        });

        $otherRoleLabels = [
            'hca' => 'HCA',
            'lpn' => 'LPN',
            'rn' => 'RN',
        ];

        foreach ($otherRoles as $otherRole) {
            $links[] = [
                'label' => $otherRoleLabels[$otherRole] . ' Jobs in ' . $cityName,
                'url' => route('seo.role.city', ['role' => $otherRole, 'city' => $city]),
            ];
        }

        $links[] = [
            'label' => 'Healthcare Jobs in Alberta',
            'url' => url('/healthcare-jobs-alberta'),
        ];

        return $links;
    }
}
