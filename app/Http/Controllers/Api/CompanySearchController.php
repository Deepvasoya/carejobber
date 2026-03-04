<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Company;
use App\Industry;
use App\City;
use App\State;
use App\Country;
use App\OwnershipType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanySearchController extends BaseController
{
    /**
     * Advanced company search with multiple filters
     */
    public function advancedSearch(Request $request)
    {
        try {
            $query = Company::with([
                'industry:id,industry',
                'city:id,city',
                'state:id,state',
                'country:id,country',
                'ownershipType:id,ownership_type'
            ])->where('is_active', 1)
              ->where('is_verified', 1);

            // Search by keyword
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('website', 'LIKE', "%{$search}%");
                });
            }

            // Filter by industry
            if ($request->filled('industry_id')) {
                $query->where('industry_id', $request->industry_id);
            }

            // Filter by city
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }

            // Filter by state
            if ($request->filled('state_id')) {
                $query->where('state_id', $request->state_id);
            }

            // Filter by country
            if ($request->filled('country_id')) {
                $query->where('country_id', $request->country_id);
            }

            // Filter by ownership type
            if ($request->filled('ownership_type_id')) {
                $query->where('ownership_type_id', $request->ownership_type_id);
            }

            // Filter by company size
            if ($request->filled('company_size')) {
                $query->where('company_size', $request->company_size);
            }

            // Filter by featured companies
            if ($request->filled('featured') && $request->featured == 1) {
                $query->where('is_featured', 1);
            }

            // Filter by companies with active jobs
            if ($request->filled('has_jobs') && $request->has_jobs == 1) {
                $query->whereHas('jobs', function($q) {
                    $q->where('is_active', 1)
                      ->where('is_posted', 1)
                      ->where('expiry_date', '>=', now());
                });
            }

            // Sort by
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            switch ($sortBy) {
                case 'name':
                    $query->orderBy('name', $sortOrder);
                    break;
                case 'featured':
                    $query->orderBy('is_featured', 'desc')
                          ->orderBy('created_at', 'desc');
                    break;
                case 'jobs_count':
                    $query->withCount(['jobs' => function($q) {
                        $q->where('is_active', 1)
                          ->where('is_posted', 1)
                          ->where('expiry_date', '>=', now());
                    }])->orderBy('jobs_count', $sortOrder);
                    break;
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('is_featured', 'desc')
                          ->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $companies = $query->paginate($perPage);

            // Transform data for mobile app
            $companies->getCollection()->transform(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug,
                    'logo' => $company->logo ? asset('storage/company_logos/' . $company->logo) : null,
                    'cover_image' => $company->cover_image ? asset('storage/company_logos/' . $company->cover_image) : null,
                    'industry' => $company->industry ? $company->industry->industry : null,
                    'location' => [
                        'city' => $company->city ? $company->city->city : null,
                        'state' => $company->state ? $company->state->state : null,
                        'country' => $company->country ? $company->country->country : null,
                    ],
                    'ownership_type' => $company->ownershipType ? $company->ownershipType->ownership_type : null,
                    'company_size' => $company->company_size,
                    'website' => $company->website,
                    'description' => $company->description,
                    'is_featured' => (bool) $company->is_featured,
                    'is_verified' => (bool) $company->is_verified,
                    'founded_year' => $company->founded_year,
                    'active_jobs_count' => $company->jobs_count ?? $company->jobs()->where('is_active', 1)
                        ->where('is_posted', 1)
                        ->where('expiry_date', '>=', now())
                        ->count(),
                    'created_date' => $company->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return $this->sendResponse($companies, 'Companies retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error searching companies', [], 500);
        }
    }

    /**
     * Get companies by industry
     */
    public function getCompaniesByIndustry($industrySlug, Request $request)
    {
        try {
            $industry = Industry::where('slug', $industrySlug)->first();
            if (!$industry) {
                return $this->sendError('Industry not found', [], 404);
            }

            $query = Company::with([
                'city:id,city',
                'state:id,state',
                'country:id,country'
            ])->where('is_active', 1)
              ->where('is_verified', 1)
              ->where('industry_id', $industry->id);

            // Apply additional filters
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }

            if ($request->filled('featured') && $request->featured == 1) {
                $query->where('is_featured', 1);
            }

            // Sort and paginate
            $perPage = $request->get('per_page', 15);
            $companies = $query->orderBy('is_featured', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->paginate($perPage);

            // Transform data
            $companies->getCollection()->transform(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug,
                    'logo' => $company->logo ? asset('storage/company_logos/' . $company->logo) : null,
                    'location' => [
                        'city' => $company->city ? $company->city->city : null,
                        'state' => $company->state ? $company->state->state : null,
                        'country' => $company->country ? $company->country->country : null,
                    ],
                    'company_size' => $company->company_size,
                    'is_featured' => (bool) $company->is_featured,
                    'active_jobs_count' => $company->jobs()->where('is_active', 1)
                        ->where('is_posted', 1)
                        ->where('expiry_date', '>=', now())
                        ->count(),
                ];
            });

            return $this->sendResponse($companies, 'Companies by industry retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving companies by industry', [], 500);
        }
    }

    /**
     * Get featured companies
     */
    public function getFeaturedCompanies(Request $request)
    {
        try {
            $query = Company::with([
                'industry:id,industry',
                'city:id,city'
            ]);

            // Only apply filters if columns exist
            if (Schema::hasColumn('companies', 'is_active')) {
                $query->where('is_active', 1);
            }
            if (Schema::hasColumn('companies', 'is_verified')) {
                $query->where('is_verified', 1);
            }
            if (Schema::hasColumn('companies', 'is_featured')) {
                $query->where('is_featured', 1);
            }

            $perPage = $request->get('per_page', 10);
            $companies = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform data
            $companies->getCollection()->transform(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug,
                    'logo' => $company->logo ? asset('company_logos/' . $company->logo) : null,
                    'industry' => $company->industry ? $company->industry->industry : null,
                    'location' => [
                        'city' => $company->city ? $company->city->city : null,
                    ],
                    'company_size' => $company->company_size ?? null,
                    'active_jobs_count' => $company->jobs()->count(),
                ];
            });

            return $this->sendResponse($companies, 'Featured companies retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving featured companies: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get companies by location
     */
    public function getCompaniesByLocation(Request $request)
    {
        try {
            $query = Company::with([
                'industry:id,industry'
            ])->where('is_active', 1)
              ->where('is_verified', 1);

            // Filter by city
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }

            // Filter by state
            if ($request->filled('state_id')) {
                $query->where('state_id', $request->state_id);
            }

            // Filter by country
            if ($request->filled('country_id')) {
                $query->where('country_id', $request->country_id);
            }

            $perPage = $request->get('per_page', 15);
            $companies = $query->orderBy('is_featured', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->paginate($perPage);

            // Transform data
            $companies->getCollection()->transform(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug,
                    'logo' => $company->logo ? asset('company_logos/' . $company->logo) : null,
                    'industry' => $company->industry ? $company->industry->industry : null,
                    'company_size' => $company->company_size,
                    'is_featured' => (bool) $company->is_featured,
                    'active_jobs_count' => $company->jobs()->where('is_active', 1)
                        ->where('is_posted', 1)
                        ->where('expiry_date', '>=', now())
                        ->count(),
                ];
            });

            return $this->sendResponse($companies, 'Companies by location retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving companies by location', [], 500);
        }
    }

    /**
     * Get company statistics
     */
    public function getCompanyStatistics()
    {
        try {
            $stats = [
                'total_companies' => Company::where('is_active', 1)->where('is_verified', 1)->count(),
                'featured_companies' => Company::where('is_active', 1)->where('is_verified', 1)->where('is_featured', 1)->count(),
                'companies_by_industry' => Company::where('is_active', 1)
                    ->where('is_verified', 1)
                    ->join('industries', 'companies.industry_id', '=', 'industries.id')
                    ->select('industries.industry', DB::raw('count(*) as count'))
                    ->groupBy('industries.id', 'industries.industry')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
                'companies_by_location' => Company::where('is_active', 1)
                    ->where('is_verified', 1)
                    ->join('cities', 'companies.city_id', '=', 'cities.id')
                    ->select('cities.city', DB::raw('count(*) as count'))
                    ->groupBy('cities.id', 'cities.city')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return $this->sendResponse($stats, 'Company statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving company statistics', [], 500);
        }
    }

    /**
     * Get companies with most active jobs
     */
    public function getCompaniesWithMostJobs(Request $request)
    {
        try {
            $query = Company::with([
                'industry:id,industry',
                'city:id,city'
            ])->where('is_active', 1)
              ->where('is_verified', 1)
              ->withCount(['jobs' => function($q) {
                  $q->where('is_active', 1)
                    ->where('is_posted', 1)
                    ->where('expiry_date', '>=', now());
              }])
              ->having('jobs_count', '>', 0)
              ->orderBy('jobs_count', 'desc');

            $perPage = $request->get('per_page', 10);
            $companies = $query->paginate($perPage);

            // Transform data
            $companies->getCollection()->transform(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'slug' => $company->slug,
                    'logo' => $company->logo ? asset('storage/company_logos/' . $company->logo) : null,
                    'industry' => $company->industry ? $company->industry->industry : null,
                    'location' => [
                        'city' => $company->city ? $company->city->city : null,
                    ],
                    'active_jobs_count' => $company->jobs_count,
                ];
            });

            return $this->sendResponse($companies, 'Companies with most jobs retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving companies with most jobs', [], 500);
        }
    }
} 