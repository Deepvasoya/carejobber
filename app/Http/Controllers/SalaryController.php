<?php

namespace App\Http\Controllers;

use App\City;
use App\FunctionalArea;
use App\Job;
use App\State;
use Illuminate\Support\Str;

class SalaryController extends Controller
{
    const NOINDEX_THRESHOLD = 5;

    public function show(string $role, string $location)
    {
        $fa = FunctionalArea::where('slug', $role)->where('is_active', 1)->firstOrFail();
        $roleLabel = $fa->functional_area;

        $state = State::where('is_active', 1)->get()->first(fn($s) => Str::slug($s->state) === $location);

        if ($state) {
            return $this->provinceView($fa, $roleLabel, $role, $state, $location);
        }

        $city = City::where('slug', $location)->where('is_active', 1)->first();
        if ($city) {
            return $this->cityView($fa, $roleLabel, $role, $city);
        }

        abort(404);
    }

    private function provinceView(FunctionalArea $fa, string $roleLabel, string $role, State $state, string $locationSlug)
    {
        $query = $this->baseQuery($fa->functional_area_id)->where('jobs.state_id', $state->state_id);

        $jobCount = (clone $query)->count();
        $salaryData = $this->salaryStats(clone $query);
        $cities = $this->cityBreakdown($fa->functional_area_id, $state->state_id);
        $employers = $this->employerList(clone $query);
        $latestJobs = (clone $query)->with('company')->orderBy('created_at', 'desc')->take(10)->get();
        $noIndex = $jobCount < self::NOINDEX_THRESHOLD || empty($salaryData);

        $metaTitle = $roleLabel . ' Salary in ' . $state->state . ' | Medojob';
        $metaDescription = $salaryData
            ? 'View advertised ' . $roleLabel . ' salary data in ' . $state->state . '. Based on ' . $salaryData->count . ' active job postings with salary information on Medojob.'
            : 'Explore ' . $roleLabel . ' jobs, salary trends, employers, and career opportunities in ' . $state->state . ' on Medojob.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $role . '-salary-' . $locationSlug, $noIndex);

        return view('seo.salary-show')
            ->with('pageType', 'province')
            ->with('role', $role)
            ->with('roleLabel', $roleLabel)
            ->with('location', $state->state)
            ->with('locationSlug', $locationSlug)
            ->with('jobCount', $jobCount)
            ->with('salaryData', $salaryData)
            ->with('cities', $cities)
            ->with('employers', $employers)
            ->with('latestJobs', $latestJobs)
            ->with('noIndex', $noIndex)
            ->with('metaTitle', $metaTitle)
            ->with('metaDescription', $metaDescription)
            ->with('seo', $seo);
    }

    private function cityView(FunctionalArea $fa, string $roleLabel, string $role, City $city)
    {
        $query = $this->baseQuery($fa->functional_area_id)->where('jobs.city_id', $city->city_id);

        $jobCount = (clone $query)->count();
        $salaryData = $this->salaryStats(clone $query);
        $employers = $this->employerList(clone $query);
        $latestJobs = (clone $query)->with('company')->orderBy('created_at', 'desc')->take(10)->get();
        $noIndex = $jobCount < self::NOINDEX_THRESHOLD || empty($salaryData);

        $state = State::where('state_id', $city->state_id)->first();
        $provinceSlug = $state ? Str::slug($state->state) : '';

        $metaTitle = $roleLabel . ' Salary in ' . $city->city . ' | Medojob';
        $metaDescription = $salaryData
            ? 'View advertised ' . $roleLabel . ' salary data in ' . $city->city . '. Based on ' . $salaryData->count . ' active job postings with salary information on Medojob.'
            : 'Explore ' . $roleLabel . ' jobs, salary trends, employers, and career opportunities in ' . $city->city . ' on Medojob.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $role . '-salary-' . $city->slug, $noIndex);

        $relatedCitySalaries = $this->relatedCitySalaries($fa, $city);

        return view('seo.salary-show')
            ->with('pageType', 'city')
            ->with('role', $role)
            ->with('roleLabel', $roleLabel)
            ->with('location', $city->city)
            ->with('locationSlug', $city->slug)
            ->with('provinceSlug', $provinceSlug)
            ->with('provinceName', $state->state ?? '')
            ->with('jobCount', $jobCount)
            ->with('salaryData', $salaryData)
            ->with('employers', $employers)
            ->with('latestJobs', $latestJobs)
            ->with('relatedCitySalaries', $relatedCitySalaries)
            ->with('noIndex', $noIndex)
            ->with('metaTitle', $metaTitle)
            ->with('metaDescription', $metaDescription)
            ->with('seo', $seo);
    }

    private function baseQuery(int $functionalAreaId)
    {
        return Job::where('jobs.functional_area_id', $functionalAreaId)
            ->where('jobs.is_active', 1)->where('jobs.is_draft', 0)
            ->where(function ($q) {
                $q->whereNull('jobs.display_end_date')->orWhere('jobs.display_end_date', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNotNull('jobs.salary_from')->orWhereNotNull('jobs.salary_to');
            })
            ->notExpire();
    }

    private function salaryStats($query): ?object
    {
        $rates = (clone $query)->where('salary_period_id', 6)->get(['salary_from', 'salary_to']);
        if ($rates->isEmpty()) return null;

        $values = [];
        foreach ($rates as $r) {
            if ($r->salary_from) $values[] = (float) $r->salary_from;
            if ($r->salary_to) $values[] = (float) $r->salary_to;
        }
        if (empty($values)) return null;

        return (object) [
            'avg' => round(array_sum($values) / count($values), 2),
            'min' => round(min($values), 2),
            'max' => round(max($values), 2),
            'count' => $rates->count(),
        ];
    }

    private function cityBreakdown(int $functionalAreaId, int $stateId): array
    {
        $rows = [];
        $cityIds = Job::where('functional_area_id', $functionalAreaId)
            ->where('state_id', $stateId)
            ->where('is_active', 1)->where('is_draft', 0)
            ->whereNotNull('city_id')
            ->distinct()->pluck('city_id');

        foreach ($cityIds as $cid) {
            $city = City::where('city_id', $cid)->where('is_active', 1)->first();
            if (!$city) continue;

            $q = $this->baseQuery($functionalAreaId)->where('jobs.city_id', $cid);
            $stats = $this->salaryStats(clone $q);
            $cnt = (clone $q)->count();

            $rows[] = [
                'city' => $city->city,
                'slug' => $city->slug,
                'avg' => $stats ? $stats->avg : null,
                'min' => $stats ? $stats->min : null,
                'max' => $stats ? $stats->max : null,
                'count' => $cnt,
                'salaryCount' => $stats ? $stats->count : 0,
            ];
        }

        usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
        return $rows;
    }

    private function employerList($query): array
    {
        return (clone $query)
            ->whereNotNull('jobs.company_id')
            ->join('companies', 'jobs.company_id', '=', 'companies.id')
            ->where('companies.is_active', 1)
            ->select('companies.id', 'companies.name', 'companies.slug')
            ->distinct()
            ->orderBy('companies.name')
            ->take(20)
            ->get()
            ->toArray();
    }

    private function relatedCitySalaries(FunctionalArea $fa, City $currentCity): array
    {
        $links = [];
        $others = City::where('state_id', $currentCity->state_id)
            ->where('is_active', 1)->whereNotNull('slug')->where('slug', '!=', '')
            ->where('city_id', '!=', $currentCity->city_id)
            ->orderBy('city')->take(28)->get();

        foreach ($others as $c) {
            $links[] = [
                'label' => $fa->functional_area . ' Salary in ' . $c->city,
                'url' => url('/' . $fa->slug . '-salary-' . $c->slug),
            ];
        }
        return $links;
    }

    private function buildSeo(string $title, string $description, string $canonicalSlug, bool $noIndex): object
    {
        $robots = $noIndex
            ? '<meta name="robots" content="noindex,follow">'
            : '<meta name="robots" content="index,follow">';
        $canonical = '<link rel="canonical" href="' . e(url('/' . $canonicalSlug)) . '">';

        return (object) [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => $title,
            'seo_other' => $robots . "\n" . $canonical,
        ];
    }
}
