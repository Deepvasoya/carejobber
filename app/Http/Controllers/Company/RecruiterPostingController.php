<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Package;
use Illuminate\Http\Request;

class RecruiterPostingController extends Controller
{
    /**
     * Packages tab: pay-per-post credits (one_time_credits).
     */
    public function packages(Request $request)
    {
        $all = config('package_countries', []);
        $defaultCode = $all['default'] ?? 'CA';
        $countryCode = $request->query('cc', $defaultCode);
        $countries = array_diff_key($all, ['default' => 0]);
        if (!isset($countries[$countryCode])) {
            $countryCode = $defaultCode;
        }

        $packages = Package::where('package_for', 'employer')
            ->where('type', 'one_time_credits')
            ->where(function ($q) use ($countryCode) {
                $q->whereNull('country_code')->orWhere('country_code', $countryCode);
            })
            ->where('is_active', true)
            ->orderBy('package_num_listings')
            ->get();

        return view('company.recruiter.posting_packages', [
            'tab' => 'packages',
            'packages' => $packages,
            'country_code' => $countryCode,
            'countries' => $countries,
        ]);
    }

    /**
     * Subscriptions tab: recurring (monthly_recurring).
     */
    public function subscriptions(Request $request)
    {
        $all = config('package_countries', []);
        $defaultCode = $all['default'] ?? 'CA';
        $countryCode = $request->query('cc', $defaultCode);
        $countries = array_diff_key($all, ['default' => 0]);
        if (!isset($countries[$countryCode])) {
            $countryCode = $defaultCode;
        }

        $packages = Package::where('package_for', 'employer')
            ->where('type', 'monthly_recurring')
            ->where(function ($q) use ($countryCode) {
                $q->whereNull('country_code')->orWhere('country_code', $countryCode);
            })
            ->where('is_active', true)
            ->orderBy('duration_days')
            ->get();

        return view('company.recruiter.posting_packages', [
            'tab' => 'subscriptions',
            'packages' => $packages,
            'country_code' => $countryCode,
            'countries' => $countries,
        ]);
    }
}
