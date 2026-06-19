<?php

namespace App\Http\Controllers\Seo;

use App\City;
use App\Company;
use App\FunctionalArea;
use App\Job;
use App\State;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class EmployerSeoController extends Controller
{
    const PER_PAGE = 20;
    const NOINDEX_THRESHOLD = 5;

    public function show(string $slug)
    {
        $company = Company::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        $jobsQuery = Job::with('company')
            ->where('company_id', $company->id)
            ->where('is_active', 1)
            ->where('is_draft', 0)
            ->where(function ($q) {
                $q->whereNull('display_end_date')
                    ->orWhere('display_end_date', '>=', now());
            })
            ->notExpire();

        $jobCount = (clone $jobsQuery)->count();

        Job::orderByPromotionPriority($jobsQuery);
        $jobs = $jobsQuery->paginate(self::PER_PAGE);

        $noIndex = $jobCount < self::NOINDEX_THRESHOLD;

        $metaTitle = $company->name . ' Jobs and Careers | Medojob';
        $metaDescription = 'Browse current ' . $company->name . ' jobs, healthcare careers, hiring locations, and employment opportunities on Medojob.';

        $seo = $this->buildSeo($metaTitle, $metaDescription, $company, $noIndex);

        $relatedEmployers = $this->relatedEmployers($company->id);

        // --- employer description data ---
        $cities = $this->hiringCities($company->id);
        $roles = $this->hiringRoles($company->id);
        $templateIndex = $this->deterministicTemplateIndex($company->id);

        $state = $company->state_id ? State::where('state_id', $company->state_id)->first() : null;
        $provinceName = $state ? $state->state : 'Alberta';

        $parsedDescription = $this->parseDescription($company, $jobCount, $cities, $roles, $templateIndex, $provinceName);

        return view('seo.employer-show')
            ->with('company', $company)
            ->with('jobs', $jobs)
            ->with('jobCount', $jobCount)
            ->with('noIndex', $noIndex)
            ->with('metaTitle', $metaTitle)
            ->with('metaDescription', $metaDescription)
            ->with('seo', $seo)
            ->with('relatedEmployers', $relatedEmployers)
            ->with('parsedDescription', $parsedDescription)
            ->with('provinceName', $provinceName);
    }

    // --- SEO ---

    private function buildSeo(string $title, string $description, Company $company, bool $noIndex): object
    {
        $robots = $noIndex
            ? '<meta name="robots" content="noindex,follow">'
            : '<meta name="robots" content="index,follow">';

        $canonical = '<link rel="canonical" href="' . e(url('/employers/' . $company->slug)) . '">';

        return (object) [
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => $company->name . ' jobs, ' . $company->name . ' careers, healthcare jobs',
            'seo_other' => $robots . "\n" . $canonical,
        ];
    }

    // --- related employers ---

    private function relatedEmployers(int $currentCompanyId): array
    {
        $links = [];

        $others = Company::where('id', '!=', $currentCompanyId)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereHas('jobs', function ($q) {
                $q->where('is_active', 1)
                    ->where('is_draft', 0)
                    ->where(function ($sq) {
                        $sq->whereNull('display_end_date')
                            ->orWhere('display_end_date', '>=', now());
                    })
                    ->notExpire();
            })
            ->orderBy('name')
            ->limit(28)
            ->get();

        foreach ($others as $other) {
            $links[] = [
                'label' => $other->name . ' Jobs',
                'url' => url('/employers/' . $other->slug),
            ];
        }

        return $links;
    }

    // --- description helpers ---

    private function hiringCities(int $companyId): array
    {
        return City::whereIn('city_id', function ($q) use ($companyId) {
                $q->select('city_id')->from('jobs')
                    ->where('company_id', $companyId)
                    ->where('is_active', 1)->where('is_draft', 0)
                    ->whereNotNull('city_id');
            })
            ->where('is_active', 1)
            ->whereNotNull('slug')->where('slug', '!=', '')
            ->orderBy('city')
            ->pluck('city')
            ->take(6)
            ->toArray();
    }

    private function hiringRoles(int $companyId): array
    {
        return FunctionalArea::whereIn('functional_area_id', function ($q) use ($companyId) {
                $q->select('functional_area_id')->from('jobs')
                    ->where('company_id', $companyId)
                    ->where('is_active', 1)->where('is_draft', 0)
                    ->whereNotNull('functional_area_id');
            })
            ->where('is_active', 1)
            ->orderBy('functional_area')
            ->pluck('functional_area')
            ->take(6)
            ->toArray();
    }

    private function deterministicTemplateIndex(int $companyId): int
    {
        return crc32((string) $companyId) % 5;
    }

    private function parseDescription(Company $company, int $jobCount, array $cities, array $roles, int $templateIndex, string $provinceName): object
    {
        $plainDescription = strip_tags($company->description ?? '');
        $hasDescription = !empty(trim($plainDescription));
        $words = str_word_count($plainDescription, 1);
        $originalWordCount = count($words);
        $wordLimit = 150;
        $midPoint = 75;

        $p1 = '';
        $p2 = '';
        $fullDescription = '';
        $isTruncated = false;
        $fallbackP1 = '';
        $fallbackP2 = '';

        if ($hasDescription) {
            $fullDescription = $plainDescription;
            if ($originalWordCount > $wordLimit) {
                $isTruncated = true;
                $truncated = implode(' ', array_slice($words, 0, $wordLimit));
                $truncatedWords = explode(' ', $truncated);
                $p1 = implode(' ', array_slice($truncatedWords, 0, $midPoint));
                $p2 = implode(' ', array_slice($truncatedWords, $midPoint)) . '...';
            } else {
                $p1 = implode(' ', array_slice($words, 0, $midPoint));
                $p2 = implode(' ', array_slice($words, $midPoint));
            }
        } else {
            $fallbackP1 = $company->name . ' is a healthcare employer with active hiring activity in ' . $provinceName . '. Job seekers can use this page to learn more about current opportunities, hiring locations, and available healthcare roles connected to this employer.';
            $fallbackP2 = 'Medojob updates this employer page as new jobs are added or removed, helping candidates find current healthcare openings from ' . $company->name . ' in one place.';
        }

        $citiesText = !empty($cities) ? implode(', ', $cities) : '';
        $rolesText = !empty($roles) ? implode(', ', $roles) : '';

        $templates = [
            function ($name, $count, $citiesText, $rolesText, $provinceName) {
                $loc = $citiesText ? 'across ' . $citiesText : 'across ' . $provinceName;
                return "{$name} currently has {$count} active healthcare job opening" . ($count == 1 ? '' : 's') . " available {$loc}. Current opportunities include {$rolesText} positions. Hiring is taking place across different healthcare settings, offering healthcare professionals a variety of career opportunities throughout {$provinceName}.";
            },
            function ($name, $count, $citiesText, $rolesText, $provinceName) {
                $loc = $citiesText ? 'in ' . $citiesText : 'across ' . $provinceName;
                return "Healthcare professionals interested in working with {$name} can explore {$count} active job opportunit" . ($count == 1 ? 'y' : 'ies') . " currently available {$loc}. Available roles include {$rolesText} positions across different healthcare facilities. Browse the latest openings below to find opportunities that match your experience and career goals.";
            },
            function ($name, $count, $citiesText, $rolesText, $provinceName) {
                $loc = $citiesText ? 'in ' . $citiesText : 'across ' . $provinceName;
                return "{$name} continues to recruit healthcare professionals across {$provinceName}, with {$count} active position" . ($count == 1 ? '' : 's') . " currently available. The employer is hiring {$loc} for roles such as {$rolesText}. Opportunities are available across different healthcare settings and may include full-time, part-time, temporary, and permanent positions.";
            },
            function ($name, $count, $citiesText, $rolesText, $provinceName) {
                $loc = $citiesText ? 'across ' . $citiesText : 'across ' . $provinceName;
                return "Job seekers exploring careers with {$name} will find {$count} active opportunit" . ($count == 1 ? 'y' : 'ies') . " {$loc}. Current vacancies include {$rolesText} positions. These opportunities are available within various healthcare environments and are updated regularly as hiring needs change.";
            },
            function ($name, $count, $citiesText, $rolesText, $provinceName) {
                $loc = $citiesText ? 'across ' . $citiesText : 'across ' . $provinceName;
                return "With {$count} active healthcare job" . ($count == 1 ? '' : 's') . " currently available, {$name} is hiring {$loc}. Candidates can apply for {$rolesText} positions and explore opportunities within different healthcare settings throughout {$provinceName}.";
            },
        ];

        $render = $templates[$templateIndex]($company->name, $jobCount, $citiesText ?: 'across ' . $provinceName, $rolesText ?: 'healthcare, nursing, support, and clinical', $provinceName);

        return (object) [
            'hasDescription' => $hasDescription,
            'p1' => $p1,
            'p2' => $p2,
            'fullDescription' => $fullDescription,
            'isTruncated' => $isTruncated,
            'fallbackP1' => $fallbackP1,
            'fallbackP2' => $fallbackP2,
            'dynamicParagraph' => $render,
        ];
    }
}
