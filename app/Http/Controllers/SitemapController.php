<?php

namespace App\Http\Controllers;

use App\City;
use App\Company;
use App\FunctionalArea;
use App\Job;
use App\SeoGuide;
use App\Models\Medo\Category as MedoCategory;
use App\Models\Medo\City as MedoCity;
use App\Models\Medo\Employer as MedoEmployer;
use App\Models\Medo\Job as MedoJob;
use App\Models\Medo\Province as MedoProvince;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [
            [
                'loc' => url('/sitemap-jobs.xml'),
                'lastmod' => now()->toDateString(),
            ],
            [
                'loc' => url('/sitemap-categories.xml'),
                'lastmod' => now()->toDateString(),
            ],
            [
                'loc' => url('/sitemap-employers.xml'),
                'lastmod' => now()->toDateString(),
            ],
        ];

        return $this->xml($this->sitemapIndex($urls));
    }

    public function jobs()
    {
        $urls = [];

        $activeCities = City::where('state_id', 663)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('city')
            ->get();

        $roles = FunctionalArea::where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->pluck('slug')
            ->toArray();

        $stateJobCount = Job::where('is_active', 1)
            ->where('is_draft', 0)
            ->where('state_id', 663)
            ->where(function ($q) {
                $q->whereNull('display_end_date')
                    ->orWhere('display_end_date', '>=', now());
            })
            ->notExpire()
            ->count();

        if ($stateJobCount >= 5) {
            $urls[] = [
                'loc' => url('/healthcare-jobs-alberta'),
                'lastmod' => now()->toDateString(),
            ];
        }

        foreach ($roles as $role) {
            $fa = FunctionalArea::where('slug', $role)->where('is_active', 1)->first();
            if (!$fa) {
                continue;
            }

            $roleHubCount = Job::where('is_active', 1)
                ->where('is_draft', 0)
                ->where('functional_area_id', $fa->functional_area_id)
                ->where('state_id', 663)
                ->where(function ($q) {
                    $q->whereNull('display_end_date')
                        ->orWhere('display_end_date', '>=', now());
                })
                ->notExpire()
                ->count();

            if ($roleHubCount >= 5) {
                $urls[] = [
                    'loc' => url('/' . $role . '-jobs-alberta'),
                    'lastmod' => now()->toDateString(),
                ];
            }
        }

        foreach ($activeCities as $cityModel) {
            $cityJobCount = Job::where('is_active', 1)
                ->where('is_draft', 0)
                ->where('state_id', 663)
                ->where('city_id', $cityModel->city_id)
                ->where(function ($q) {
                    $q->whereNull('display_end_date')
                        ->orWhere('display_end_date', '>=', now());
                })
                ->notExpire()
                ->count();

            if ($cityJobCount >= 5) {
                $urls[] = [
                    'loc' => url('/healthcare-jobs-' . $cityModel->slug),
                    'lastmod' => now()->toDateString(),
                ];
            }
        }

        foreach ($roles as $role) {
            $fa = FunctionalArea::where('slug', $role)->where('is_active', 1)->first();
            if (! $fa) {
                continue;
            }

            foreach ($activeCities as $cityModel) {
                $count = Job::where('is_active', 1)
                    ->where('is_draft', 0)
                    ->where('functional_area_id', $fa->functional_area_id)
                    ->where('city_id', $cityModel->city_id)
                    ->where(function ($q) {
                        $q->whereNull('display_end_date')
                            ->orWhere('display_end_date', '>=', now());
                    })
                    ->notExpire()
                    ->count();

                if ($count >= 5) {
                    $urls[] = [
                        'loc' => url('/' . $role . '-jobs-' . $cityModel->slug),
                        'lastmod' => now()->toDateString(),
                    ];
                }
            }
        }

        Job::where('is_active', 1)
            ->where('is_draft', 0)
            ->where(function ($q) {
                $q->whereNull('display_end_date')
                    ->orWhere('display_end_date', '>=', now());
            })
            ->notExpire()
            ->orderBy('updated_at', 'desc')
            ->chunk(500, function ($jobs) use (&$urls) {
                foreach ($jobs as $job) {
                    $urls[] = [
                        'loc' => route('job.detail', $job->slug),
                        'lastmod' => optional($job->updated_at)->toDateString() ?: now()->toDateString(),
                    ];
                }
            });

        $categoryCounts = $this->activeJobCounts(['functional_area_id']);
        foreach ($categoryCounts as $row) {
            $category = FunctionalArea::where('functional_area_id', $row->functional_area_id)
                ->whereNotNull('slug')
                ->active()
                ->first();
            if (! $category) {
                continue;
            }
            $urls[] = [
                'loc' => route('medo.jobs.category', $category->slug),
                'lastmod' => now()->toDateString(),
            ];
            $urls[] = [
                'loc' => route('seo.salary', $category->slug),
                'lastmod' => now()->toDateString(),
            ];
        }

        $cityCounts = $this->activeJobCounts(['functional_area_id', 'city_id']);
        foreach ($cityCounts as $row) {
            $category = FunctionalArea::where('functional_area_id', $row->functional_area_id)
                ->whereNotNull('slug')
                ->active()
                ->first();
            $city = City::where('city_id', $row->city_id)
                ->whereNotNull('slug')
                ->active()
                ->first();
            if (! $category || ! $city) {
                continue;
            }
            $urls[] = [
                'loc' => route('medo.jobs.category.province.city', [$category->slug, 'ab', $city->slug]),
                'lastmod' => now()->toDateString(),
            ];
        }

        SeoGuide::published()
            ->orderBy('updated_at', 'desc')
            ->get()
            ->each(function (SeoGuide $guide) use (&$urls) {
                $urls[] = [
                    'loc' => route('seo.guide', $guide->slug),
                    'lastmod' => optional($guide->updated_at)->toDateString() ?: now()->toDateString(),
                ];
            });

        return $this->xml($this->urlSet($urls));
    }

    public function categories()
    {
        $urls = [];

        MedoCategory::all()->each(function (MedoCategory $category) use (&$urls) {
            $urls[] = [
                'loc' => route('medo.jobs.category', $category),
                'lastmod' => now()->toDateString(),
            ];

            MedoProvince::where('is_active', true)->each(function (MedoProvince $province) use ($category, &$urls) {
                $provinceCount = MedoJob::where('category_id', $category->id)
                    ->where('province_id', $province->id)
                    ->where('expires_at', '>', now())
                    ->count();

                if ($provinceCount >= 3) {
                    $urls[] = [
                        'loc' => route('medo.jobs.category.province', [$category, $province]),
                        'lastmod' => now()->toDateString(),
                    ];
                }

                MedoCity::where('province_id', $province->id)->each(function (MedoCity $city) use ($category, $province, &$urls) {
                    $count = MedoJob::where('category_id', $category->id)
                        ->where('city_id', $city->id)
                        ->where('expires_at', '>', now())
                        ->count();

                    if ($count >= 3) {
                        $urls[] = [
                            'loc' => route('medo.jobs.category.province.city', [$category, $province, $city]),
                            'lastmod' => now()->toDateString(),
                            'changefreq' => 'daily',
                            'priority' => '0.8',
                        ];
                    }
                });
            });
        });

        $activeCities = City::where('state_id', 663)
            ->where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('city')
            ->get();

        $roles = FunctionalArea::where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->pluck('slug')
            ->toArray();

        foreach ($roles as $role) {
            $fa = FunctionalArea::where('slug', $role)->where('is_active', 1)->first();
            if (!$fa) {
                continue;
            }

            foreach ($activeCities as $cityModel) {
                $count = Job::where('is_active', 1)
                    ->where('is_draft', 0)
                    ->where('functional_area_id', $fa->functional_area_id)
                    ->where('city_id', $cityModel->city_id)
                    ->where(function ($q) {
                        $q->whereNull('display_end_date')
                            ->orWhere('display_end_date', '>=', now());
                    })
                    ->notExpire()
                    ->count();

                if ($count >= 5) {
                    $urls[] = [
                        'loc' => url('/' . $role . '-jobs-' . $cityModel->slug),
                        'lastmod' => now()->toDateString(),
                    ];
                }
            }
        }

        return $this->xml($this->urlSet($urls));
    }

    public function employers()
    {
        $urls = [];

        MedoEmployer::whereHas('jobs', function ($query) {
            $query->where('expires_at', '>', now());
        })->orderBy('slug')->each(function (MedoEmployer $employer) use (&$urls) {
            $urls[] = [
                'loc' => route('seo.employer.show', ['slug' => $employer->slug]),
                'lastmod' => optional($employer->updated_at)->toDateString() ?: now()->toDateString(),
            ];
        });
        
        Company::where('is_active', 1)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereIn('id', function ($q) {
                $q->select('company_id')
                    ->from('jobs')
                    ->where('is_active', 1)
                    ->where('is_draft', 0)
                    ->where(function ($sq) {
                        $sq->whereNull('display_end_date')
                            ->orWhere('display_end_date', '>=', now());
                    })
                    ->groupBy('company_id')
                    ->havingRaw('COUNT(*) >= 5');
            })
            ->orderBy('name')
            ->chunk(200, function ($companies) use (&$urls) {
                foreach ($companies as $company) {
                    $urls[] = [
                        'loc' => url('/employers/' . $company->slug),
                        'lastmod' => now()->toDateString(),
                    ];
                }
            });
          $urls = collect($urls)->unique('loc')->values()->all();
          
        return $this->xml($this->urlSet($urls));
    }

    public function companies()
    {
        return response()->json(['message' => 'Companies sitemap']);
    }

    private function activeJobCounts(array $groupBy)
    {
        return DB::table('jobs')
            ->select(array_merge($groupBy, [DB::raw('COUNT(*) as total')]))
            ->where('is_active', 1)
            ->where('is_draft', 0)
            ->whereDate('expiry_date', '>', now())
            ->where(function ($q) {
                $q->whereNull('display_end_date')
                    ->orWhere('display_end_date', '>=', now());
            })
            ->groupBy($groupBy)
            ->havingRaw('COUNT(*) >= 3')
            ->get();
    }

    private function sitemapIndex(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <sitemap>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";
            $xml .= '    <lastmod>' . e($url['lastmod']) . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        $xml .= '</sitemapindex>';

        return $xml;
    }

    private function urlSet(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";
            $xml .= '    <lastmod>' . e($url['lastmod']) . "</lastmod>\n";
            if (isset($url['changefreq'])) {
                $xml .= '    <changefreq>' . e($url['changefreq']) . "</changefreq>\n";
            }
            if (isset($url['priority'])) {
                $xml .= '    <priority>' . e($url['priority']) . "</priority>\n";
            }
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return $xml;
    }

    private function xml(string $xml)
    {
        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

}
