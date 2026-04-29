<?php

namespace App\Http\Controllers;

use App\City;
use App\FunctionalArea;
use App\Job;
use App\SeoGuide;
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
        ];

        return $this->xml($this->sitemapIndex($urls));
    }

    public function jobs()
    {
        $urls = [];

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
                'loc' => route('seo.jobs.category', $category->slug),
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
                'loc' => route('seo.jobs.city', [$category->slug, $city->slug]),
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
