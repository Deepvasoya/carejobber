<?php

namespace App\Http\Controllers\Seo;

use App\City;
use App\Company;
use App\FunctionalArea;
use App\Http\Controllers\Controller;
use App\SeoGuide;
use App\Services\ProgrammaticSeoService;
use App\State;
use Illuminate\Http\Request;

class ProgrammaticSeoController extends Controller
{
    private $seo;

    public function __construct(ProgrammaticSeoService $seo)
    {
        $this->seo = $seo;
    }

    public function category(Request $request, FunctionalArea $category)
    {
        $this->abortInactiveCategory($category);

        return $this->jobsLanding($request, $category, null);
    }

    public function city(Request $request, FunctionalArea $category, City $city)
    {
        $this->abortInactiveCategory($category);
        abort_unless((int) $city->is_active === 1, 404);

        return $this->jobsLanding($request, $category, $city);
    }

    public function salary(Request $request, string $categorySlug)
    {
        $category = FunctionalArea::where('slug', $categorySlug)->active()->firstOrFail();
        $query = $this->seo->activeJobsQuery($category);
        $jobCount = (clone $query)->count();
        $salary = $this->seo->salaryStats($query);
        $categoryLabel = $this->seo->categoryLabel($category);
        $canonical = route('seo.salary', $category->slug);

        $state = State::where('state_id', 663)->first();
        $provinceName = $state ? $state->state : 'Alberta';

        $seo = $this->seo->seo(
            "{$categoryLabel} salary guide for {$provinceName}",
            "Compare posted {$categoryLabel} salary ranges from active {$provinceName} healthcare job listings.",
            $canonical,
            $jobCount < 3
        );

        return view('seo.salary')
            ->with('category', $category)
            ->with('categoryLabel', $categoryLabel)
            ->with('provinceName', $provinceName)
            ->with('jobCount', $jobCount)
            ->with('salary', $salary)
            ->with('seo', $seo);
    }

    public function guide(SeoGuide $guide)
    {
        abort_unless($guide->is_active && (! $guide->published_at || $guide->published_at <= now()), 404);

        return view('seo.guide')
            ->with('guide', $guide)
            ->with('seo', $this->seo->guideSeo($guide));
    }

    public function employer(Company $company)
    {
        abort_unless((int) $company->is_active === 1, 404);

        return view('company.detail')
            ->with('company', $company)
            ->with('seo', $this->seo->employerSeo($company));
    }

    private function jobsLanding(Request $request, FunctionalArea $category, ?City $city)
    {
        $query = $this->seo->activeJobsQuery($category, $city);
        $jobCount = (clone $query)->count();
        $salary = $this->seo->salaryStats($query);
        $content = $this->seo->landingContent($category, $city, $jobCount, $salary);
        $canonical = $city
            ? route('jobs.category.province.city', [$category->slug, 'ab', $city->slug])
            : route('jobs.category', $category->slug);

        \App\Job::orderByPromotionPriority($query);
        $jobs = $query->paginate(15);

        $seo = $this->seo->seo(
            $content['h1'] . ' - Medojob',
            $content['intro'],
            $canonical,
            $jobCount < 3
        );

        return view('seo.jobs_landing')
            ->with('category', $category)
            ->with('city', $city)
            ->with('jobs', $jobs)
            ->with('jobCount', $jobCount)
            ->with('salary', $salary)
            ->with('content', $content)
            ->with('internalLinks', $this->seo->internalLinks($category, $city))
            ->with('seo', $seo);
    }

    private function abortInactiveCategory(FunctionalArea $category): void
    {
        abort_unless((int) $category->is_active === 1, 404);
    }
}
