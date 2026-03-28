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

        $pattern = '%' . $this->escapeLike($term) . '%';

        $titles = DB::table('jobs')
            ->where('is_active', 1)
            ->where(function ($q) use ($pattern) {
                $q->where('title', 'LIKE', $pattern)
                    ->orWhere('search', 'LIKE', $pattern);
            })
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->orderBy('title')
            ->limit(40)
            ->pluck('title');

        return response()->json($titles->unique()->values()->all());
    }

    public function locations(Request $request)
    {
        $term = trim((string) $request->get('term', ''));
        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $pattern = '%' . $this->escapeLike($term) . '%';
        $lang = app()->getLocale();

        $rows = $this->locationRows($pattern, $lang);
        if ($rows->isEmpty() && $lang !== 'en') {
            $rows = $this->locationRows($pattern, 'en');
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

    private function locationRows(string $pattern, string $lang)
    {
        return DB::table('cities as c')
            ->join('states as s', 'c.state_id', '=', 's.id')
            ->where('c.is_active', 1)
            ->where('s.is_active', 1)
            ->where('c.lang', $lang)
            ->where('s.lang', $lang)
            ->where(function ($q) use ($pattern) {
                $q->where('c.city', 'LIKE', $pattern)
                    ->orWhere('s.state', 'LIKE', $pattern);
            })
            ->select('c.city', 's.state')
            ->orderBy('c.city')
            ->limit(25)
            ->get();
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
