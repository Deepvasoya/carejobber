<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SearchAutocompleteController extends Controller
{
    public function jobs(Request $request)
    {
        $term = trim((string) $request->get('term', ''));
        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $len = mb_strlen($term);
        $prefix = $this->escapeLike($term) . '%';
        $contains = '%' . $this->escapeLike($term) . '%';

        $query = DB::table('jobs')
            ->where('is_active', 1)
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->where(function ($q) use ($len, $prefix, $contains) {
                if ($len <= 2) {
                    $q->where('title', 'LIKE', $prefix);
                } else {
                    $q->where('title', 'LIKE', $contains);
                    if ($len >= 4) {
                        $q->orWhere('search', 'LIKE', $contains);
                    }
                }
            });

        $query->orderByRaw(
            '(CASE WHEN jobs.title LIKE ? THEN 1 WHEN jobs.title LIKE ? THEN 2 ELSE 3 END) ASC, CHAR_LENGTH(jobs.title) ASC, jobs.title ASC',
            [$prefix, $contains]
        );

        $titles = $query->limit(60)->pluck('title');

        return response()->json($titles->unique()->values()->take(25)->all());
    }

    public function locations(Request $request)
    {
        $term = trim((string) $request->get('term', ''));
        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $lang = app()->getLocale();

        $rows = $this->locationRows($term, $lang);
        if ($rows->isEmpty() && $lang !== 'en') {
            $rows = $this->locationRows($term, 'en');
        }

        $labels = $rows->map(function ($r) {
            return $r->city . ', ' . $r->state;
        })->unique()->values()->all();

        return response()->json($labels);
    }

    public function reverseGeocode(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        if (!is_numeric($lat) || !is_numeric($lon)) {
            return response()->json(['label' => '', 'error' => 'invalid'], 422);
        }

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => config('app.name', 'CareJobber') . '/1.0',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $lat,
                    'lon' => $lon,
                    'format' => 'json',
                    'addressdetails' => 1,
                ]);
        } catch (\Throwable $e) {
            return response()->json(['label' => '', 'error' => 'service'], 502);
        }

        if (!$response->successful()) {
            return response()->json(['label' => '', 'error' => 'upstream'], 502);
        }

        $addr = $response->json('address');
        if (!is_array($addr)) {
            return response()->json(['label' => '', 'error' => 'parse'], 422);
        }

        $city = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['municipality'] ?? $addr['hamlet'] ?? '';
        $state = $addr['state'] ?? '';

        if ($city !== '' && $state !== '') {
            return response()->json(['label' => $city . ', ' . $state]);
        }
        if ($city !== '') {
            return response()->json(['label' => $city]);
        }

        return response()->json(['label' => '', 'error' => 'parse'], 422);
    }

    private function locationRows(string $term, string $lang)
    {
        $term = trim($term);
        if (mb_strlen($term) < 2) {
            return collect();
        }

        $len = mb_strlen($term);
        $prefix = $this->escapeLike($term) . '%';
        $contains = '%' . $this->escapeLike($term) . '%';
        $termNorm = mb_strtolower(trim($term));

        $cityPattern = $len <= 2 ? $prefix : $contains;

        $cityQuery = DB::table('cities as c')
            ->join('states as s', 'c.state_id', '=', 's.id')
            ->where('c.is_active', 1)
            ->where('s.is_active', 1)
            ->where('c.lang', $lang)
            ->where('s.lang', $lang)
            ->where('c.city', 'LIKE', $cityPattern);

        if ($len <= 2) {
            $cityQuery->orderByRaw('CHAR_LENGTH(c.city) ASC')->orderBy('c.city');
        } else {
            $cityQuery->orderByRaw(
                '(CASE WHEN LOWER(TRIM(c.city)) = ? THEN 0 WHEN c.city LIKE ? THEN 1 ELSE 2 END) ASC, CHAR_LENGTH(c.city) ASC, c.city ASC',
                [$termNorm, $prefix]
            );
        }

        $cityResults = $cityQuery->select('c.city', 's.state')->limit(25)->get();
        $limit = 25;

        if ($cityResults->count() >= $limit || $len < 3) {
            return $cityResults;
        }

        $seen = $cityResults->mapWithKeys(function ($r) {
            $k = mb_strtolower($r->city . '|' . $r->state);

            return [$k => true];
        });

        $remaining = $limit - $cityResults->count();

        $stateRows = DB::table('cities as c')
            ->join('states as s', 'c.state_id', '=', 's.id')
            ->where('c.is_active', 1)
            ->where('s.is_active', 1)
            ->where('c.lang', $lang)
            ->where('s.lang', $lang)
            ->where('s.state', 'LIKE', $contains)
            ->select('c.city', 's.state')
            ->orderBy('s.state')
            ->orderBy('c.city')
            ->limit(max(50, $remaining * 3))
            ->get()
            ->filter(function ($r) use ($seen) {
                $k = mb_strtolower($r->city . '|' . $r->state);

                return !$seen->has($k);
            })
            ->take($remaining)
            ->values();

        return $cityResults->concat($stateRows);
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
