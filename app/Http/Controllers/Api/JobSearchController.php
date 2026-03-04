<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Job;
use App\Company;
use App\FunctionalArea;
use App\City;
use App\JobType;
use App\JobShift;
use App\JobExperience;
use App\CareerLevel;
use App\Industry;
use Illuminate\Support\Facades\DB;

class JobSearchController extends BaseController
{
    /**
     * Advanced job search with multiple filters
     */
    public function advancedSearch(Request $request)
    {
        try {
            $query = Job::with([
                'company:id,name,slug,logo',
                'functionalArea:id,functional_area',
                'city:id,city',
                'state:id,state',
                'country:id,country',
                'jobType:id,job_type',
                'jobShift:id,job_shift',
                'jobExperience:id,job_experience',
                'careerLevel:id,career_level',
                'industry:id,industry'
            ])->where('is_active', 1)
              ->where('expiry_date', '>=', now());

            // Search by keyword
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhere('requirements', 'LIKE', "%{$search}%")
                      ->orWhere('benefits', 'LIKE', "%{$search}%");
                });
            }

            // Filter by functional area
            if ($request->filled('functional_area_id')) {
                $query->where('functional_area_id', $request->functional_area_id);
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

            // Filter by job type
            if ($request->filled('job_type_id')) {
                $query->where('job_type_id', $request->job_type_id);
            }

            // Filter by job shift
            if ($request->filled('job_shift_id')) {
                $query->where('job_shift_id', $request->job_shift_id);
            }

            // Filter by experience level
            if ($request->filled('experience_id')) {
                $query->where('experience_id', $request->experience_id);
            }

            // Filter by career level
            if ($request->filled('career_level_id')) {
                $query->where('career_level_id', $request->career_level_id);
            }

            // Filter by industry
            if ($request->filled('industry_id')) {
                $query->where('industry_id', $request->industry_id);
            }

            // Filter by salary range
            if ($request->filled('salary_from')) {
                $query->where('salary_from', '>=', $request->salary_from);
            }

            if ($request->filled('salary_to')) {
                $query->where('salary_to', '<=', $request->salary_to);
            }

            // Filter by date posted
            if ($request->filled('date_posted')) {
                switch ($request->date_posted) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->where('created_at', '>=', now()->subWeek());
                        break;
                    case 'month':
                        $query->where('created_at', '>=', now()->subMonth());
                        break;
                    case '3months':
                        $query->where('created_at', '>=', now()->subMonths(3));
                        break;
                }
            }

            // Filter by featured jobs
            if ($request->filled('featured') && $request->featured == 1) {
                $query->where('is_featured', 1);
            }

            // Filter by remote jobs
            if ($request->filled('remote') && $request->remote == 1) {
                $query->where('is_remote', 1);
            }

            // Sort by
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            switch ($sortBy) {
                case 'relevance':
                    // Sort by relevance (search match + featured + recent)
                    if ($request->filled('search')) {
                        $query->orderByRaw("
                            CASE 
                                WHEN is_featured = 1 THEN 1 
                                ELSE 2 
                            END,
                            created_at DESC
                        ");
                    } else {
                        $query->orderBy('is_featured', 'desc')
                              ->orderBy('created_at', 'desc');
                    }
                    break;
                case 'date':
                    $query->orderBy('created_at', $sortOrder);
                    break;
                case 'salary':
                    $query->orderBy('salary_from', $sortOrder);
                    break;
                case 'title':
                    $query->orderBy('title', $sortOrder);
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $jobs = $query->paginate($perPage);

            // Transform data for mobile app
            $jobs->getCollection()->transform(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'slug' => $job->slug,
                    'company' => [
                        'id' => $job->company->id,
                        'name' => $job->company->name,
                        'slug' => $job->company->slug,
                        'logo' => $job->company->logo ? asset('images/company_logos/' . $job->company->logo) : null,
                    ],
                    'location' => [
                        'city' => $job->city ? $job->city->city : null,
                        'state' => $job->state ? $job->state->state : null,
                        'country' => $job->country ? $job->country->country : null,
                    ],
                    'functional_area' => $job->functionalArea ? $job->functionalArea->functional_area : null,
                    'job_type' => $job->jobType ? $job->jobType->job_type : null,
                    'job_shift' => $job->jobShift ? $job->jobShift->job_shift : null,
                    'experience' => $job->jobExperience ? $job->jobExperience->job_experience : null,
                    'career_level' => $job->careerLevel ? $job->careerLevel->career_level : null,
                    'industry' => $job->industry ? $job->industry->industry : null,
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to,
                    'salary_currency' => $job->salary_currency,
                    'salary_period' => $job->salary_period,
                    'is_featured' => (bool) $job->is_featured,
                    'is_remote' => (bool) $job->is_remote,
                    'posted_date' => $job->created_at ? $job->created_at->format('Y-m-d H:i:s') : null,
                    'expiry_date' => $job->expiry_date ? $job->expiry_date->format('Y-m-d') : null,
                    'applications_count' => $job->applications_count ?? 0,
                ];
            });

            return $this->sendResponse($jobs, 'Jobs retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error searching jobs', [], 500);
        }
    }

    /**
     * Get jobs by category
     */
    public function getJobsByCategory($categorySlug, Request $request)
    {
        try {
            $query = Job::with([
                'company:id,name,slug,logo',
                'functionalArea:id,functional_area',
                'city:id,city',
                'state:id,state',
                'country:id,country'
            ])->where('is_active', 1)
              ->where('expiry_date', '>=', now());

            // Filter by functional area slug
            $functionalArea = FunctionalArea::where('slug', $categorySlug)->first();
            if ($functionalArea) {
                $query->where('functional_area_id', $functionalArea->id);
            }

            // Apply additional filters
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }

            if ($request->filled('job_type_id')) {
                $query->where('job_type_id', $request->job_type_id);
            }

            // Sort and paginate
            $perPage = $request->get('per_page', 15);
            $jobs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform data
            $jobs->getCollection()->transform(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'slug' => $job->slug,
                    'company' => [
                        'id' => $job->company->id,
                        'name' => $job->company->name,
                        'slug' => $job->company->slug,
                        'logo' => $job->company->logo ? asset('images/company_logos/' . $job->company->logo) : null,
                    ],
                    'location' => [
                        'city' => $job->city ? $job->city->city : null,
                        'state' => $job->state ? $job->state->state : null,
                        'country' => $job->country ? $job->country->country : null,
                    ],
                    'functional_area' => $job->functionalArea ? $job->functionalArea->functional_area : null,
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to,
                    'salary_currency' => $job->salary_currency,
                    'is_featured' => (bool) $job->is_featured,
                    'posted_date' => $job->created_at ? $job->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

            return $this->sendResponse($jobs, 'Jobs by category retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving jobs by category', [], 500);
        }
    }

    /**
     * Get similar jobs
     */
    public function getSimilarJobs($jobId, Request $request)
    {
        try {
            $currentJob = Job::findOrFail($jobId);
            
            $query = Job::with([
                'company:id,name,slug,logo',
                'functionalArea:id,functional_area',
                'city:id,city'
            ])->where('is_active', 1)
              ->where('expiry_date', '>=', now())
              ->where('id', '!=', $jobId);

            // Find jobs with similar criteria
            $query->where(function($q) use ($currentJob) {
                $q->where('functional_area_id', $currentJob->functional_area_id)
                  ->orWhere('city_id', $currentJob->city_id)
                  ->orWhere('job_type_id', $currentJob->job_type_id)
                  ->orWhere('career_level_id', $currentJob->career_level_id);
            });

            $perPage = $request->get('per_page', 10);
            $jobs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform data
            $jobs->getCollection()->transform(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'slug' => $job->slug,
                    'company' => [
                        'id' => $job->company->id,
                        'name' => $job->company->name,
                        'slug' => $job->company->slug,
                        'logo' => $job->company->logo ? asset('images/company_logos/' . $job->company->logo) : null,
                    ],
                    'location' => [
                        'city' => $job->city ? $job->city->city : null,
                    ],
                    'functional_area' => $job->functionalArea ? $job->functionalArea->functional_area : null,
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to,
                    'salary_currency' => $job->salary_currency,
                    'posted_date' => $job->created_at ? $job->created_at->format('Y-m-d H:i:s') : null,
                ];
            });

            return $this->sendResponse($jobs, 'Similar jobs retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving similar jobs', [], 500);
        }
    }

    /**
     * Get job statistics
     */
    public function getJobStatistics()
    {
        try {
            $stats = [
                'total_active_jobs' => Job::where('is_active', 1)
                    ->where('expiry_date', '>=', now())
                    ->count(),
                'total_companies' => Company::where('is_active', 1)->count(),
                'jobs_by_type' => Job::where('is_active', 1)
                    ->where('expiry_date', '>=', now())
                    ->join('job_types', 'jobs.job_type_id', '=', 'job_types.id')
                    ->select('job_types.job_type', DB::raw('count(*) as count'))
                    ->groupBy('job_types.id', 'job_types.job_type')
                    ->get(),
                'jobs_by_location' => Job::where('is_active', 1)
                    ->where('expiry_date', '>=', now())
                    ->join('cities', 'jobs.city_id', '=', 'cities.id')
                    ->select('cities.city', DB::raw('count(*) as count'))
                    ->groupBy('cities.id', 'cities.city')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return $this->sendResponse($stats, 'Job statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving job statistics', [], 500);
        }
    }

    /**
     * Get featured jobs
     */
    public function getFeaturedJobs(Request $request)
    {
        try {
            $query = Job::with([
                'company',
                'functionalArea',
                'city',
                'state',
                'country'
            ])->where('is_active', 1)
              ->where('is_featured', 1)
              ->where('expiry_date', '>=', now());

            $perPage = $request->get('per_page', 10);
            $jobs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform data
            $jobs->getCollection()->transform(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'slug' => $job->slug,
                    'company' => [
                        'id' => $job->company->id ?? null,
                        'name' => $job->company->name ?? '',
                        'slug' => $job->company->slug ?? '',
                        'logo' => $job->company->logo ? asset('company_logos/' . $job->company->logo) : null,
                    ],
                    'location' => [
                        'city' => $job->city->city ?? null,
                        'state' => $job->state->state ?? null,
                        'country' => $job->country->country ?? null,
                    ],
                    'functional_area' => $job->functionalArea->functional_area ?? null,
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to,
                    'salary_currency' => $job->salary_currency,
                    'salary_period' => $job->salary_period,
                    'is_featured' => (bool) $job->is_featured,
                    'is_remote' => (bool) $job->is_remote,
                    'posted_date' => $job->created_at ? (is_object($job->created_at) ? $job->created_at->format('Y-m-d H:i:s') : $job->created_at) : null,
                    'expiry_date' => $job->expiry_date ? (is_object($job->expiry_date) ? $job->expiry_date->format('Y-m-d') : $job->expiry_date) : null,
                ];
            });

            return $this->sendResponse($jobs, 'Featured jobs retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving featured jobs: ' . $e->getMessage(), [], 500);
        }
    }
} 