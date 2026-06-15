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
    
public function roleCitySlug(string $seoSlug)
{
    if (! str_contains($seoSlug, '-jobs-')) {
        abort(404);
    }

    [$role, $city] = explode('-jobs-', $seoSlug, 2);

    return $this->roleCity($role, $city);
}
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
        $roleLabel = $functionalArea->functional_area;
        $cityName = $cityModel->city;

        $metaTitle = strtoupper($role) . ' Jobs in ' . $cityName . ' | Medojob';
        $metaDescription = 'Find current ' . $roleLabel . ' jobs in ' . $cityName . '. Browse healthcare opportunities and apply online with Medojob.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $role, $city, $noIndex);

        $relatedLinks = $this->relatedLinks($role, $city, $cityName, $roleLabel, $cityModel->city_id);

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

        $canonical = '<link rel="canonical" href="' . e(url('/' . $role . '-jobs-' . $city)) . '">';

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

    private function relatedLinks(string $role, string $city, string $cityName, string $roleLabel, int $cityId): array
    {
        $links = [];

        $otherCities = City::where('state_id', self::ALBERTA_STATE_ID)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->where('city_id', '!=', $cityId)
            ->orderBy('city')
            ->get();

        foreach ($otherCities as $other) {
            $links[] = [
                'label' => $roleLabel . ' Jobs in ' . $other->city,
                'url' => url('/' . $role . '-jobs-' . $other->slug),
            ];
        }

        $otherRoles = FunctionalArea::where('slug', '!=', $role)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->get();

        foreach ($otherRoles as $other) {
            $links[] = [
    'label' => $other->functional_area . ' Jobs in ' . $cityName,
    'url' => url('/' . $other->slug . '-jobs-' . $city),
       ];
        }

        $links[] = [
            'label' => 'Healthcare Jobs in Alberta',
            'url' => url('/healthcare-jobs-alberta'),
        ];

        return $links;
    }

    private function shortRoleLabel(string $slug): string
{
    $short = [
        'hca' => 'HCA',
        'lpn' => 'LPN',
        'rn' => 'RN',
        'hr'  => 'Human Resources',
    ];

    return $short[$slug] ?? ucwords(str_replace('-', ' ', $slug));
}
}
