<?php

namespace App\Http\Controllers\Seo;

use App\City;
use App\FunctionalArea;
use App\Http\Controllers\Controller;
use App\Job;
use App\State;

class RoleHubController extends Controller
{
    const PER_PAGE = 20;
    const NOINDEX_THRESHOLD = 5;
    const ALBERTA_STATE_ID = 663;

    public function show(string $role)
    {
        $functionalArea = FunctionalArea::where('slug', $role)
            ->where('is_active', 1)
            ->first();

        if (!$functionalArea) {
            abort(404);
        }

        $state = State::where('state_id', self::ALBERTA_STATE_ID)->first();
        $provinceName = $state ? $state->state : 'Alberta';
        $provinceSlug = $state ? strtolower($state->state) : 'alberta';

        $roleLabel = $functionalArea->functional_area;

        $jobsQuery = Job::with('company')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_draft', 0)
            ->where('jobs.functional_area_id', $functionalArea->functional_area_id)
            ->where('jobs.state_id', self::ALBERTA_STATE_ID)
            ->where(function ($q) {
                $q->whereNull('jobs.display_end_date')
                    ->orWhere('jobs.display_end_date', '>=', now());
            })
            ->notExpire();

        $jobCount = (clone $jobsQuery)->count();

        $cityIds = (clone $jobsQuery)
            ->select('jobs.city_id')
            ->distinct()
            ->whereNotNull('jobs.city_id')
            ->pluck('jobs.city_id');

        $cities = City::whereIn('city_id', $cityIds)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('city')
            ->get();

        Job::orderByPromotionPriority($jobsQuery);
        $jobs = $jobsQuery->paginate(self::PER_PAGE);

        $noIndex = $jobCount < self::NOINDEX_THRESHOLD;

        $metaTitle = $roleLabel . ' Jobs in ' . $provinceName . ' | Medojob';
        $metaDescription = 'Browse ' . $roleLabel . ' jobs across ' . $provinceName . '. Explore opportunities in ' . $cities->pluck('city')->join(', ') . ', and other ' . $provinceName . ' communities.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $role, $provinceName, $noIndex);

        $cityLinks = $this->cityLinks($role, $cities);
        $relatedRoles = $this->relatedRoles($role, $functionalArea->functional_area_id, $provinceName);

        return view('seo.role-hub')
            ->with('role', $role)
            ->with('roleLabel', $roleLabel)
            ->with('provinceName', $provinceName)
            ->with('jobs', $jobs)
            ->with('jobCount', $jobCount)
            ->with('cities', $cities)
            ->with('cityLinks', $cityLinks)
            ->with('relatedRoles', $relatedRoles)
            ->with('noIndex', $noIndex)
            ->with('metaTitle', $metaTitle)
            ->with('metaDescription', $metaDescription)
            ->with('seo', $seo);
    }

    private function buildSeo(string $title, string $description, string $role, string $provinceName, bool $noIndex): object
    {
        $robots = $noIndex
            ? '<meta name="robots" content="noindex,follow">'
            : '<meta name="robots" content="index,follow">';

        $provinceSlug = strtolower($provinceName);

        $canonical = '<link rel="canonical" href="' . e(url('/' . $role . '-jobs-' . $provinceSlug)) . '">';

        return (object) [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => $role . ' jobs ' . $provinceSlug . ', ' . $description,
            'seo_other' => $robots . "\n" . $canonical,
        ];
    }

    private function cityLinks(string $role, $cities): array
    {
        $links = [];
        foreach ($cities as $city) {
            $links[] = [
                'label' => $this->shortRoleLabel($role) . ' Jobs in ' . $city->city,
                'url' => url('/' . $role . '-jobs-' . $city->slug),
            ];
        }
        return $links;
    }

    private function relatedRoles(string $currentRole, int $currentFaId, string $provinceName): array
    {
        $provinceSlug = strtolower($provinceName);
        $links = [];
        $others = FunctionalArea::where('functional_area_id', '!=', $currentFaId)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->limit(14)
            ->get();

        foreach ($others as $other) {
            $links[] = [
                'label' => $other->functional_area . ' Jobs in ' . $provinceName,
                'url' => url('/' . $other->slug . '-jobs-' . $provinceSlug),
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
            'hr' => 'Human Resources',
        ];
        return $short[$slug] ?? ucwords(str_replace('-', ' ', $slug));
    }
}
