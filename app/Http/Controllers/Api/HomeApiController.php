<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Job;
use App\FavouriteCompany;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeApiController extends BaseController
{
    /**
     * Test database connection and basic queries
     */
    public function testDatabase(Request $request)
    {
        try {
            $results = [];
            
            // Test 1: Basic database connection
            try {
                DB::connection()->getPdo();
                $results['connection'] = 'OK';
            } catch (\Exception $e) {
                $results['connection'] = 'Failed: ' . $e->getMessage();
            }
            
            // Test 2: Check if users table exists
            try {
                $userCount = DB::table('users')->count();
                $results['users_table'] = 'OK - ' . $userCount . ' users found';
            } catch (\Exception $e) {
                $results['users_table'] = 'Failed: ' . $e->getMessage();
            }
            
            // Test 3: Check if companies table exists
            try {
                $companyCount = DB::table('companies')->count();
                $results['companies_table'] = 'OK - ' . $companyCount . ' companies found';
            } catch (\Exception $e) {
                $results['companies_table'] = 'Failed: ' . $e->getMessage();
            }
            
            // Test 4: Check if jobs table exists
            try {
                $jobCount = DB::table('jobs')->count();
                $results['jobs_table'] = 'OK - ' . $jobCount . ' jobs found';
            } catch (\Exception $e) {
                $results['jobs_table'] = 'Failed: ' . $e->getMessage();
            }
            
            // Test 5: Check if functional_areas table exists
            try {
                $functionalAreaCount = DB::table('functional_areas')->count();
                $results['functional_areas_table'] = 'OK - ' . $functionalAreaCount . ' areas found';
            } catch (\Exception $e) {
                $results['functional_areas_table'] = 'Failed: ' . $e->getMessage();
            }
            
            // Test 6: Check if job_types table exists
            try {
                $jobTypeCount = DB::table('job_types')->count();
                $results['job_types_table'] = 'OK - ' . $jobTypeCount . ' types found';
            } catch (\Exception $e) {
                $results['job_types_table'] = 'Failed: ' . $e->getMessage();
            }
            
            // Test 7: Check if career_levels table exists
            try {
                $careerLevelCount = DB::table('career_levels')->count();
                $results['career_levels_table'] = 'OK - ' . $careerLevelCount . ' levels found';
            } catch (\Exception $e) {
                $results['career_levels_table'] = 'Failed: ' . $e->getMessage();
            }
            
            // Test 8: Check if job_experiences table exists
            try {
                $jobExperienceCount = DB::table('job_experiences')->count();
                $results['job_experiences_table'] = 'OK - ' . $jobExperienceCount . ' experiences found';
            } catch (\Exception $e) {
                $results['job_experiences_table'] = 'Failed: ' . $e->getMessage();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Database test completed',
                'results' => $results,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Database test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Database test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Main home dashboard endpoint - WITHOUT matching jobs
     */
    public function getHomeData(Request $request)
{
    try {
        
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $user = Auth::guard('api')->user();
        
        // Load location relationships and stats relationships
        $user->load(['city', 'state', 'country', 'profileCvs', 'favouriteJobs', 'appliedJobs', 'followingCompanies']);

        // Get location in English only (avoid multiple languages)
        $constructedLocation = $this->getUserLocationEnglish($user);
        
        // Debug: Log the location data
        \Log::info('Location Debug - Raw user data:', [
            'user_id' => $user->id,
            'location_field' => $user->location,
            'city_id' => $user->city_id,
            'state_id' => $user->state_id,
            'country_id' => $user->country_id,
            'city_name' => $user->city->city ?? 'N/A',
            'state_name' => $user->state->state ?? 'N/A',
            'country_name' => $user->country->country ?? 'N/A',
            'constructed_location' => $constructedLocation
        ]);


        // Get actual user statistics using model methods
        $followingsCount = $user->countFollowings();
        $cvCount = $user->countProfileCvs();
        $messagesCount = $user->countUserMessages();
        $appliedJobsCount = count($user->getAppliedJobIdsArray());
        $favouriteJobsCount = count($user->getFavouriteJobSlugsArray());

        // Simple data structure
        $data = [
            'user_stats' => [
                'profile_views' => $user->num_profile_views ?? 0,
                'followings' => $followingsCount,
                'cv_count' => $cvCount,
                'messages' => $messagesCount,
                'applied_jobs_count' => $appliedJobsCount,
                'favourite_jobs_count' => $favouriteJobsCount
            ],
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'location' => $constructedLocation, // Use the properly formatted location
                'city' => $user->city,
                'state' => $user->state,
                'country' => $user->country,
                'avatar' => '',
                'cover_image' => '',
                'image' => $user->image,
                'cover_image_filename' => $user->cover_image
            ],
            'user_profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->image,
                'cover_image' => $user->cover_image,
                'location' => $constructedLocation, // Use the properly formatted location
                'resume_complete' => false
            ],
            'matching_jobs' => [],
            'followings' => [],
            'applied_jobs' => [],
            'profile_completion' => [
                'fields' => [
                    'profile_summary' => false,
                    'profile_cvs' => false,
                    'profile_experience' => false,
                    'profile_education' => false,
                    'profile_skills' => false,
                    'profile_projects' => false
                ],
                'completed_count' => 0,
                'total_count' => 6,
                'percentage' => 0,
                'is_complete' => false
            ]
        ];

        
        return response()->json([
            'success' => true,
            'message' => 'Home dashboard data retrieved successfully',
            'data' => $data
        ]);

    } catch (\Exception $e) {
        \Log::error('HomeApiController getHomeData error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve home dashboard data',
            'data' => [
                'user_stats' => [
                    'profile_views' => 0,
                    'followings' => 0,
                    'cv_count' => 0,
                    'messages' => 0,
                    'applied_jobs_count' => 0,
                    'favourite_jobs_count' => 0
                ],
                'matching_jobs' => [],
                'followings' => [],
                'applied_jobs' => [],
                'profile_completion' => [
                    'fields' => [
                        'profile_summary' => false,
                        'profile_cvs' => false,
                        'profile_experience' => false,
                        'profile_education' => false,
                        'profile_skills' => false,
                        'profile_projects' => false
                    ],
                    'completed_count' => 0,
                    'total_count' => 6,
                    'percentage' => 0,
                    'is_complete' => false
                ],
                'user_info' => [
                    'id' => 0,
                    'name' => 'User',
                    'email' => '',
                    'phone' => '',
                    'location' => 'Location not set',
                    'avatar' => '',
                    'cover_image' => ''
                ],
                'user_profile' => [
                    'name' => 'User',
                    'email' => '',
                    'location' => 'Location not set',
                    'resume_complete' => false
                ]
            ],
            'error' => $e->getMessage()
        ], 500);
    }
}

// Add this helper method to your controller
private function constructLocationFromFields($user)
{
    $locationParts = [];
    
    if (!empty($user->city)) $locationParts[] = $user->city;
    if (!empty($user->state)) $locationParts[] = $user->state;
    if (!empty($user->country)) $locationParts[] = $user->country;
    
    return count($locationParts) > 0 ? implode(', ', $locationParts) : 'Location not set';
}

// Get user location in English only (avoid multiple languages)
private function getUserLocationEnglish($user)
{
    try {
        $locationParts = [];
        
        // Get city in English only
        if ($user->city_id && isset($user->city) && $user->city) {
            $city = $user->city->city;
            // Remove any non-English text (keep only English part)
            $city = $this->extractEnglishText($city);
            if (!empty($city)) $locationParts[] = $city;
        }
        
        // Get state in English only
        if ($user->state_id && isset($user->state) && $user->state) {
            $state = $user->state->state;
            // Remove any non-English text (keep only English part)
            $state = $this->extractEnglishText($state);
            if (!empty($state)) $locationParts[] = $state;
        }
        
        // Get country in English only
        if ($user->country_id && isset($user->country) && $user->country) {
            $country = $user->country->country;
            // Remove any non-English text (keep only English part)
            $country = $this->extractEnglishText($country);
            if (!empty($country)) $locationParts[] = $country;
        }
        
        $result = count($locationParts) > 0 ? implode(', ', $locationParts) : 'Location not set';
        
        // Final cleanup - remove any remaining non-ASCII characters
        $result = preg_replace('/[^\x20-\x7E]/u', '', $result);
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result, ' ,');
        
        return $result;
    } catch (\Exception $e) {
        \Log::error('Error getting user location in English: ' . $e->getMessage());
        return 'Location not set';
    }
}

// Extract English text from mixed language text
private function extractEnglishText($text)
{
    if (empty($text)) return '';
    
    \Log::info('ExtractEnglishText Debug:', [
        'original_text' => $text,
        'text_length' => strlen($text)
    ]);
    
    // First, try to split by common separators and find English parts
    $parts = preg_split('/[\s,]+/', $text);
    $englishParts = [];
    
    foreach ($parts as $part) {
        $part = trim($part);
        if (empty($part)) continue;
        
        // Check if this part contains only English characters
        if (preg_match('/^[a-zA-Z0-9\s\-\.]+$/', $part)) {
            $englishParts[] = $part;
        }
    }
    
    \Log::info('ExtractEnglishText Debug - Parts:', [
        'all_parts' => $parts,
        'english_parts' => $englishParts
    ]);
    
    // If we found English parts, join them
    if (!empty($englishParts)) {
        $result = implode(', ', $englishParts);
        \Log::info('ExtractEnglishText Debug - Result from parts:', ['result' => $result]);
        return $result;
    }
    
    // Fallback: Remove all non-ASCII characters
    $englishText = preg_replace('/[^\x20-\x7E]/u', '', $text);
    $englishText = preg_replace('/\s+/', ' ', $englishText);
    $englishText = trim($englishText, ' ,');
    
    \Log::info('ExtractEnglishText Debug - Fallback result:', ['result' => $englishText]);
    
    // If we have English text, return it
    if (!empty($englishText)) {
        return $englishText;
    }
    
    // If no English text found, return original text
    return $text;
}

    /**
     * Separate matching jobs API endpoint
     */
    public function getMatchingJobs(Request $request)
    {
        try {
            
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = Auth::guard('api')->user();
            
            // Get recommended jobs using improved function
            $matchingJobs = [];
            try {
                $jobs = $this->getMatchingJobsForUserImproved($user);
                
                // Jobs are already in the correct format from the improved function
                $matchingJobs = $jobs;

            } catch (\Exception $e) {
                \Log::error('🔍 MATCHING JOBS ENDPOINT - Error getting matching jobs: ' . $e->getMessage());
                \Log::error('🔍 MATCHING JOBS ENDPOINT - Error trace: ' . $e->getTraceAsString());
                $matchingJobs = [];
            }

            
            return response()->json([
                'success' => true,
                'message' => 'Matching jobs retrieved successfully',
                'data' => [
                    'matching_jobs' => $matchingJobs,
                    'total_count' => count($matchingJobs)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('HomeApiController getMatchingJobs error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve matching jobs',
                'data' => [
                    'matching_jobs' => [],
                    'total_count' => 0
                ],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simple test endpoint to verify authentication
     */
    public function testAuth(Request $request)
    {
        try {
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = Auth::guard('api')->user();
            
            return response()->json([
                'success' => true,
                'message' => 'Authentication working',
                'user_id' => $user->id,
                'user_email' => $user->email,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Test auth error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Authentication test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Minimal home dashboard - skips complex queries
     */
    public function getHomeDataMinimal(Request $request)
    {
        try {
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = Auth::guard('api')->user();
            
            // Load location relationships and stats relationships
            $user->load(['city', 'state', 'country', 'profileCvs', 'favouriteJobs', 'appliedJobs', 'followingCompanies']);
            
            // Debug image data
            $profileImageUrl = $user->printUserImage();
            $coverImageUrl = $user->printUserCoverImage();
            
            
            // Get basic user data
            $profileViews = $user->num_profile_views ?? 0;
            $followings = $user->countFollowings();
            $cvCount = $user->countProfileCvs();
            $messages = $user->countUserMessages();
            $appliedJobs = count($user->getAppliedJobIdsArray());
            $favouriteJobs = count($user->getFavouriteJobSlugsArray());
            
            
            $userStats = [
                'profile_views' => $profileViews,
                'followings' => $followings,
                'cv_count' => $cvCount,
                'messages' => $messages,
                'applied_jobs_count' => $appliedJobs,
                'favourite_jobs_count' => $favouriteJobs
            ];

            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'location' => $this->getUserLocationEnglish($user),
                'avatar' => $profileImageUrl,
                'cover_image' => $coverImageUrl,
                'image' => $user->image, // Raw filename for profile image
                'cover_image_filename' => $user->cover_image // Raw filename for cover image
            ];

            // Create user_profile section for frontend compatibility
            $userProfile = [
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->image, // Raw filename
                'cover_image' => $user->cover_image, // Raw filename
                'location' => $this->getUserLocationEnglish($user),
                'resume_complete' => false // This would need to be calculated
            ];

            // Get recommended jobs
            $matchingJobs = [];
            try {
                $matchingJobs = $this->getMatchingJobs($user);
                
                // Log each job details
                foreach ($matchingJobs as $index => $job) {
                }
            } catch (\Exception $e) {
                \Log::error('🔍 RECOMMENDED JOBS DEBUG - Error getting matching jobs: ' . $e->getMessage());
                \Log::error('🔍 RECOMMENDED JOBS DEBUG - Error trace: ' . $e->getTraceAsString());
                $matchingJobs = [];
            }

            $data = [
                'user_profile' => $userProfile,
                'user_stats' => $userStats,
                'user_info' => $userInfo,
                'matching_jobs' => $matchingJobs,
                'followings' => [],
                'applied_jobs' => [],
                'profile_completion' => [
                    'fields' => [
                        'profile_summary' => false,
                        'profile_cvs' => false,
                        'profile_experience' => false,
                        'profile_education' => false,
                        'profile_skills' => false,
                        'profile_projects' => false
                    ],
                    'completed_count' => 0,
                    'total_count' => 6,
                    'percentage' => 0,
                    'is_complete' => false
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Minimal home dashboard data retrieved successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Minimal home dashboard error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Minimal home dashboard failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug version of home dashboard with minimal data
     */
    public function getHomeDataDebug(Request $request)
    {
        try {
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = Auth::guard('api')->user();
            
            // Load location relationships and stats relationships
            $user->load(['city', 'state', 'country', 'profileCvs', 'favouriteJobs', 'appliedJobs', 'followingCompanies']);
            
            // Test database connection
            $dbStatus = 'OK';
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                $dbStatus = 'Failed: ' . $e->getMessage();
            }
            
            // Debug image data
            $profileImageUrl = $user->printUserImage();
            $coverImageUrl = $user->printUserCoverImage();
            
            
            // Get basic user data for debug
            $userStats = [
                'profile_views' => $user->num_profile_views ?? 0,
                'followings' => $user->countFollowings(),
                'cv_count' => $user->countProfileCvs(),
                'messages' => $user->countUserMessages(),
                'applied_jobs_count' => count($user->getAppliedJobIdsArray()),
                'favourite_jobs_count' => count($user->getFavouriteJobSlugsArray())
            ];

            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'location' => $this->getUserLocationEnglish($user),
                'avatar' => $profileImageUrl,
                'cover_image' => $coverImageUrl,
                'image' => $user->image, // Raw filename for profile image
                'cover_image_filename' => $user->cover_image // Raw filename for cover image
            ];

            // Create user_profile section for frontend compatibility
            $userProfile = [
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->image, // Raw filename
                'cover_image' => $user->cover_image, // Raw filename
                'location' => $this->getUserLocationEnglish($user),
                'resume_complete' => false // This would need to be calculated
            ];

            // Get recommended jobs
            $matchingJobs = [];
            try {
                $matchingJobs = $this->getMatchingJobs($user);
                
                // Log each job details
                foreach ($matchingJobs as $index => $job) {
                }
            } catch (\Exception $e) {
                \Log::error('🔍 RECOMMENDED JOBS DEBUG - Error getting matching jobs: ' . $e->getMessage());
                \Log::error('🔍 RECOMMENDED JOBS DEBUG - Error trace: ' . $e->getTraceAsString());
                $matchingJobs = [];
            }

            $data = [
                'debug_info' => [
                    'database_status' => $dbStatus,
                    'user_authenticated' => true,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'timestamp' => now()->toISOString()
                ],
                'user_profile' => $userProfile,
                'user_stats' => $userStats,
                'user_info' => $userInfo,
                'matching_jobs' => $matchingJobs,
                'followings' => [],
                'applied_jobs' => [],
                'profile_completion' => [
                    'fields' => [
                        'profile_summary' => false,
                        'profile_cvs' => false,
                        'profile_experience' => false,
                        'profile_education' => false,
                        'profile_skills' => false,
                        'profile_projects' => false
                    ],
                    'completed_count' => 0,
                    'total_count' => 6,
                    'percentage' => 0,
                    'is_complete' => false
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Debug home dashboard data retrieved successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Debug home dashboard error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Debug home dashboard failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get matching jobs based on user profile - IMPROVED VERSION
     */
    private function getMatchingJobsForUser($user)
    {
        try {
            
            // Test if Job model exists
            
            // Step 1: Get user skills
            try {
        $userSkills = $user->getProfileSkills();
        $skillIds = $userSkills->pluck('job_skill_id')->toArray();
            } catch (\Exception $e) {
                \Log::error('Error getting user skills: ' . $e->getMessage());
                $skillIds = [];
            }
        
            // Step 2: Get applied job IDs
            try {
        $appliedJobIds = $user->getAppliedJobIdsArray();
            } catch (\Exception $e) {
                \Log::error('Error getting applied job IDs: ' . $e->getMessage());
                $appliedJobIds = [];
            }
        
            // Step 3: Build the main query
            try {
        $query = Job::select(
            'jobs.id',
            'jobs.slug',
            'jobs.title',
            'jobs.description',
            'jobs.salary_from',
            'jobs.salary_to',
            'jobs.salary_currency',
            'jobs.hide_salary',
            'jobs.is_featured',
            'jobs.created_at',
            'jobs.expiry_date',
            'companies.name AS company_name',
            'companies.slug AS company_slug',
            'companies.logo AS company_logo',
            'countries.country',
            'states.state',
            'cities.city',
            'functional_areas.functional_area',
            'job_types.job_type',
            'career_levels.career_level',
            'job_experiences.job_experience'
                );
            } catch (\Exception $e) {
                \Log::error('Error building SELECT query: ' . $e->getMessage());
                throw $e;
            }
            
            // Step 4: Add JOINs
            try {
                $query->leftJoin('companies', 'companies.id', '=', 'jobs.company_id');
                
                $query->leftJoin('countries', 'countries.id', '=', 'jobs.country_id');
                
                $query->leftJoin('states', 'states.id', '=', 'jobs.state_id');
                
                $query->leftJoin('cities', 'cities.id', '=', 'jobs.city_id');
                
                $query->leftJoin('functional_areas', 'functional_areas.id', '=', 'jobs.functional_area_id');
                
                $query->leftJoin('job_types', 'job_types.id', '=', 'jobs.job_type_id');
                
                $query->leftJoin('career_levels', 'career_levels.id', '=', 'jobs.career_level_id');
                
                $query->leftJoin('job_experiences', 'job_experiences.id', '=', 'jobs.job_experience_id');
            } catch (\Exception $e) {
                \Log::error('Error adding JOINs: ' . $e->getMessage());
                throw $e;
            }
            
            // Step 5: Add WHERE clauses
            try {
                $query->where('jobs.is_active', 1);
                $query->where('jobs.expiry_date', '>', now());
            } catch (\Exception $e) {
                \Log::error('Error adding basic WHERE clauses: ' . $e->getMessage());
                throw $e;
            }
            
            // Step 6: Add complex WHERE clauses
            try {
                $query->where(function($q) use ($user, $skillIds) {
            // Match by functional area
            if ($user->functional_area_id) {
                $q->orWhere('jobs.functional_area_id', $user->functional_area_id);
            }
            
            // Match by industry through company
            if ($user->industry_id) {
                $q->orWhereHas('company', function($subq) use ($user) {
                    $subq->where('industry_id', $user->industry_id);
                });
            }
            
            // Match by career level
            if ($user->career_level_id) {
                $q->orWhere('jobs.career_level_id', $user->career_level_id);
            }
            
            // Match by job experience
            if ($user->job_experience_id) {
                $q->orWhere('jobs.job_experience_id', $user->job_experience_id);
            }
            
            // Match by salary range (with 20% flexibility)
            if ($user->expected_salary) {
                $minSalary = $user->expected_salary * 0.8;
                $maxSalary = $user->expected_salary * 1.2;
                $q->orWhere(function($salaryQ) use ($minSalary, $maxSalary) {
                    $salaryQ->whereBetween('jobs.salary_from', [$minSalary, $maxSalary])
                           ->orWhereBetween('jobs.salary_to', [$minSalary, $maxSalary]);
                });
            }
            
            // Match by skills
            if (!empty($skillIds)) {
                $q->orWhereHas('jobSkills', function($skillQ) use ($skillIds) {
                    $skillQ->whereIn('job_skill_id', $skillIds);
                });
            }
        });
            } catch (\Exception $e) {
                \Log::error('Error adding complex WHERE clauses: ' . $e->getMessage());
                throw $e;
            }
        
            // Step 7: Exclude applied jobs
            try {
        if (!empty($appliedJobIds)) {
            $query->whereNotIn('jobs.id', $appliedJobIds);
                }
            } catch (\Exception $e) {
                \Log::error('Error excluding applied jobs: ' . $e->getMessage());
                throw $e;
        }
        
            // Step 8: Add ordering and limits
            try {
        $query->orderBy('jobs.is_featured', 'desc')
              ->orderBy('jobs.created_at', 'desc')
                      ->take(6);
            } catch (\Exception $e) {
                \Log::error('Error adding ordering and limits: ' . $e->getMessage());
                throw $e;
            }
            
            // Step 9: Execute query
            try {
                $results = $query->get();
            } catch (\Exception $e) {
                \Log::error('Error executing query: ' . $e->getMessage());
                throw $e;
            }
            
            // Step 10: Process results
            try {
                foreach ($results as $job) {
            if ($job->company_logo) {
                $job->company_logo = asset('company_logos/' . $job->company_logo);
            }
            
            // Format salary
            if (!$job->hide_salary && $job->salary_from && $job->salary_to) {
                $job->formatted_salary = $job->salary_currency . $job->salary_from . ' - ' . $job->salary_currency . $job->salary_to;
            } else {
                $job->formatted_salary = 'Salary not disclosed';
            }
            
            // Format date
            $job->formatted_date = $job->created_at->format('M d, Y');
            
            // Check if job is expired
            $job->is_expired = $job->expiry_date < now();
        }
            } catch (\Exception $e) {
                \Log::error('Error processing results: ' . $e->getMessage());
                throw $e;
            }
            
            // Convert to array for proper JSON serialization
            $finalResults = $results->toArray();
            if (count($finalResults) > 0) {
            } else {
            }
            return $finalResults;
            
        } catch (\Exception $e) {
            \Log::error('getMatchingJobs failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            throw $e;
        }
    }

    /**
     * Get user's followings
     */
    private function getUserFollowings($user)
    {
        $followings = FavouriteCompany::where('user_id', $user->id)
            ->with(['company' => function($query) {
                $query->select('id', 'name', 'slug', 'logo', 'industry_id', 'location')
                      ->where('is_active', 1);
            }])
            ->take(6)
            ->get()
            ->map(function($following) {
                $company = $following->company;
                if ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'slug' => $company->slug,
                        'logo' => $company->logo ? asset('company_logos/' . $company->logo) : null,
                        'industry' => $company->getIndustry('industry'),
                        'location' => $company->location,
                        'open_jobs_count' => $company->countNumJobs('company_id', $company->id)
                    ];
                }
                return null;
            })
            ->filter()
            ->values();

        return $followings;
    }

    /**
     * Get applied jobs summary
     */
    private function getAppliedJobsSummary($user)
    {
        $appliedJobIds = $user->getAppliedJobIdsArray();
        
        if (empty($appliedJobIds)) {
            return [];
        }

        $appliedJobs = Job::select(
            'jobs.id',
            'jobs.slug',
            'jobs.title',
            'jobs.created_at',
            'companies.name AS company_name',
            'companies.slug AS company_slug',
            'cities.city'
        )
        ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
        ->leftJoin('cities', 'cities.id', '=', 'jobs.city_id')
        ->whereIn('jobs.id', $appliedJobIds)
        ->orderBy('jobs.created_at', 'desc')
        ->take(5)
        ->get()
        ->map(function($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'slug' => $job->slug,
                'company_name' => $job->company_name,
                'company_slug' => $job->company_slug,
                'city' => $job->city,
                'applied_date' => $job->created_at->format('M d, Y')
            ];
        });

        return $appliedJobs;
    }

    /**
     * Get profile completion status
     */
    private function getProfileCompletionStatus($user)
    {
        $completion = [
            'profile_summary' => $user->profileSummary()->count() > 0,
            'profile_cvs' => $user->profileCvs()->count() > 0,
            'profile_experience' => $user->profileExperience()->count() > 0,
            'profile_education' => $user->profileEducation()->count() > 0,
            'profile_skills' => $user->profileSkills()->count() > 0,
            'profile_projects' => $user->profileProjects()->count() > 0
        ];

        $totalFields = count($completion);
        $completedFields = count(array_filter($completion));
        $completionPercentage = round(($completedFields / $totalFields) * 100);

        return [
            'fields' => $completion,
            'completed_count' => $completedFields,
            'total_count' => $totalFields,
            'percentage' => $completionPercentage,
            'is_complete' => $completionPercentage == 100
        ];
    }

    /**
     * Get matching jobs based on user profile - NEW IMPROVED VERSION
     * Based on web controller logic but optimized for API
     */
    private function getMatchingJobsForUserImproved($user)
    {
        try {
            
            // Get user's skills
            $userSkills = $user->getProfileSkills();
            $skillIds = $userSkills->pluck('job_skill_id')->toArray();
            
            // Get applied job IDs to exclude them
            $appliedJobIds = $user->getAppliedJobIdsArray();
            
            // Build the query using the same logic as web controller
            $query = Job::where('is_active', 1)
                ->where('expiry_date', '>', now())
                ->where(function($q) use ($user, $skillIds) {
                    // Match by functional area
                    if ($user->functional_area_id) {
                        $q->orWhere('functional_area_id', $user->functional_area_id);
                    }
                    
                    // Match by industry through company
                    if ($user->industry_id) {
                        $q->orWhereHas('company', function($subq) use ($user) {
                            $subq->where('industry_id', $user->industry_id);
                        });
                    }
                    
                    // Match by career level
                    if ($user->career_level_id) {
                        $q->orWhere('career_level_id', $user->career_level_id);
                    }
                    
                    // Match by job experience
                    if ($user->job_experience_id) {
                        $q->orWhere('job_experience_id', $user->job_experience_id);
                    }
                    
                    // Match by salary range (with 20% flexibility)
                    if ($user->expected_salary) {
                        $minSalary = $user->expected_salary * 0.8;
                        $maxSalary = $user->expected_salary * 1.2;
                        $q->orWhere(function($salaryQ) use ($minSalary, $maxSalary) {
                            $salaryQ->whereBetween('salary_from', [$minSalary, $maxSalary])
                                   ->orWhereBetween('salary_to', [$minSalary, $maxSalary]);
                        });
                    }
                    
                    // Match by skills
                    if (!empty($skillIds)) {
                        $q->orWhereHas('jobSkills', function($skillQ) use ($skillIds) {
                            $skillQ->whereIn('job_skill_id', $skillIds);
                        });
                    }
                });
            
            // Exclude applied jobs
            if (!empty($appliedJobIds)) {
                $query->whereNotIn('id', $appliedJobIds);
            }
            
            // Order by featured status and creation date
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('created_at', 'desc');
            
            // Get jobs with relationships
            $jobs = $query->with([
                'company:id,name,slug,logo',
                'country:country_id,country',
                'state:state_id,state', 
                'city:city_id,city',
                'functionalArea:id,functional_area',
                'jobType:id,job_type',
                'careerLevel:id,career_level',
                'jobExperience:id,job_experience'
            ])->take(6)->get();
            
            
            
            // Transform to array format for API response
            $jobsArray = $jobs->map(function($job) {
                // Format salary
                $formattedSalary = 'Not specified';
                if (!$job->hide_salary && $job->salary_from && $job->salary_to) {
                    $currency = $job->salary_currency ?: '';
                    $formattedSalary = $currency . number_format($job->salary_from) . ' - ' . number_format($job->salary_to);
                }
                
                // Format date
                $formattedDate = $job->created_at ? $job->created_at->format('M d, Y') : 'Recent';
                
                // Format location using the Job model's getLocation method
                $formattedLocation = $job->getLocation() ?: 'Remote';
                
                return [
                    'id' => $job->id,
                    'slug' => $job->slug,
                    'title' => $job->title,
                    'description' => $job->description,
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to,
                    'salary_currency' => $job->salary_currency,
                    'hide_salary' => $job->hide_salary,
                    'is_featured' => $job->is_featured,
                    'created_at' => $job->created_at,
                    'expiry_date' => $job->expiry_date,
                    'company_name' => $job->company->name ?? '',
                    'company_slug' => $job->company->slug ?? '',
                    'company_logo' => $job->company->logo ?? null,
                    'country' => $job->country->country ?? '',
                    'state' => $job->state->state ?? '',
                    'city' => $job->city->city ?? '',
                    'functional_area' => $job->functionalArea->functional_area ?? '',
                    'job_type' => $job->jobType->job_type ?? '',
                    'career_level' => $job->careerLevel->career_level ?? '',
                    'job_experience' => $job->jobExperience->job_experience ?? '',
                    // Formatted fields for frontend
                    'formatted_salary' => $formattedSalary,
                    'formatted_date' => $formattedDate,
                    'formatted_location' => $formattedLocation,
                ];
            })->toArray();
            
            return $jobsArray;
            
        } catch (\Exception $e) {
            \Log::error('🔍 NEW MATCHING JOBS - Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return [];
        }
    }

    /**
     * Get user location safely
     */
    private function getUserLocation($user)
    {
        try {
            
            if (method_exists($user, 'getLocation')) {
                $location = $user->getLocation() ?? '';
                return $location;
            }
            
            // Fallback: construct location from available fields
            $locationParts = [];
            if ($user->city_id && isset($user->city) && $user->city) {
                $locationParts[] = $user->city->city;
            }
            if ($user->state_id && isset($user->state) && $user->state) {
                $locationParts[] = $user->state->state;
            }
            if ($user->country_id && isset($user->country) && $user->country) {
                $locationParts[] = $user->country->country;
            }
            
            $location = implode(', ', $locationParts);
            return $location;
        } catch (\Exception $e) {
            \Log::error('Error getting user location: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Get user avatar safely
     */
    private function getUserAvatar($user)
    {
        try {
            // Return simple asset URL instead of HTML
            if (!empty($user->image)) {
                return asset('user_images/' . $user->image);
            } else {
                return asset('/admin_assets/no-image.png');
            }
        } catch (\Exception $e) {
            return asset('/admin_assets/no-image.png');
        }
    }

    /**
     * Get user cover image safely
     */
    private function getUserCoverImage($user)
    {
        try {
            // Return simple asset URL instead of HTML
            if (!empty($user->cover_image)) {
                return asset('user_images/' . $user->cover_image);
            } else {
                return asset('/admin_assets/no-cover.jpg');
            }
        } catch (\Exception $e) {
            return asset('/admin_assets/no-cover.jpg');
        }
    }

    /**
     * Get public home data (no authentication required)
     */
    public function getPublicHomeData(Request $request)
    {
        try {
            // Get featured jobs
            $featuredJobs = Job::select(
                'jobs.id',
                'jobs.slug',
                'jobs.title',
                'jobs.description',
                'jobs.salary_from',
                'jobs.salary_to',
                'jobs.salary_currency',
                'jobs.hide_salary',
                'jobs.created_at',
                'companies.name AS company_name',
                'companies.slug AS company_slug',
                'companies.logo AS company_logo',
                'cities.city',
                'countries.country',
                'functional_areas.functional_area',
                'job_types.job_type'
            )
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('cities', 'cities.id', '=', 'jobs.city_id')
            ->leftJoin('countries', 'countries.id', '=', 'jobs.country_id')
            ->leftJoin('functional_areas', 'functional_areas.id', '=', 'jobs.functional_area_id')
            ->leftJoin('job_types', 'job_types.id', '=', 'jobs.job_type_id')
            ->where('jobs.is_active', 1)
            ->where('jobs.is_featured', 1)
            ->where('jobs.expiry_date', '>', now())
            ->orderBy('jobs.created_at', 'desc')
            ->take(6)
            ->get();

            // Process company logos and format data
            foreach ($featuredJobs as $job) {
                if ($job->company_logo) {
                    $job->company_logo = asset('company_logos/' . $job->company_logo);
                }
                
                // Format salary
                if (!$job->hide_salary && $job->salary_from && $job->salary_to) {
                    $job->formatted_salary = $job->salary_currency . $job->salary_from . ' - ' . $job->salary_currency . $job->salary_to;
                } else {
                    $job->formatted_salary = 'Salary not disclosed';
                }
                
                // Format date
                $job->formatted_date = $job->created_at->format('M d, Y');
            }

            // Get job categories with count
            $jobCategories = DB::table('jobs as jb')
                ->select(
                    'jb.functional_area_id',
                    'func_area.functional_area'
                )
                ->addSelect(DB::raw('COUNT(jb.functional_area_id) as jobs_count'))
                ->leftJoin('functional_areas AS func_area', function($join) {
                    $join->on('func_area.id', '=', 'jb.functional_area_id');
                })
                ->where('jb.is_active', 1)
                ->where('jb.expiry_date', '>', now())
                ->groupBy('jb.functional_area_id', 'func_area.functional_area')
                ->orderBy('jobs_count', 'desc')
                ->take(8)
                ->get();

            // Get latest jobs
            $latestJobs = Job::select(
                'jobs.id',
                'jobs.slug',
                'jobs.title',
                'jobs.created_at',
                'companies.name AS company_name',
                'companies.slug AS company_slug',
                'cities.city'
            )
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company_id')
            ->leftJoin('cities', 'cities.id', '=', 'jobs.city_id')
            ->where('jobs.is_active', 1)
            ->where('jobs.expiry_date', '>', now())
            ->orderBy('jobs.created_at', 'desc')
            ->take(8)
            ->get()
            ->map(function($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'slug' => $job->slug,
                    'company_name' => $job->company_name,
                    'company_slug' => $job->company_slug,
                    'city' => $job->city,
                    'posted_date' => $job->created_at->format('M d, Y')
                ];
            });

            $data = [
                'featured_jobs' => $featuredJobs,
                'job_categories' => $jobCategories,
                'latest_jobs' => $latestJobs
            ];

            return response()->json([
                'success' => true,
                'message' => 'Public home data retrieved successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve public home data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test user data endpoint to debug stats
     */
    public function testUserData(Request $request)
    {
        try {
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $user = Auth::guard('api')->user();
            
            // Test direct database queries
            $profileViews = $user->num_profile_views ?? 0;
            $followings = $user->countFollowings();
            $cvCount = $user->countProfileCvs();
            $messages = $user->countUserMessages();
            $appliedJobs = count($user->getAppliedJobIdsArray());
            $favouriteJobs = count($user->getFavouriteJobSlugsArray());
            
            // Test raw database queries
            $rawProfileViews = \DB::table('users')->where('id', $user->id)->value('num_profile_views');
            $rawFollowings = \DB::table('favourites_company')->where('user_id', $user->id)->count();
            $rawCvCount = \DB::table('profile_cvs')->where('user_id', $user->id)->count();
            $rawMessages = \DB::table('company_messages')->where('seeker_id', $user->id)->where('status', 'unviewed')->where('type', 'message')->count();
            $rawAppliedJobs = \DB::table('job_apply')->where('user_id', $user->id)->count();
            $rawFavouriteJobs = \DB::table('favourites_job')->where('user_id', $user->id)->count();
            
            return response()->json([
                'success' => true,
                'message' => 'User data test completed',
                'data' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'model_methods' => [
                        'profile_views' => $profileViews,
                        'followings' => $followings,
                        'cv_count' => $cvCount,
                        'messages' => $messages,
                        'applied_jobs_count' => $appliedJobs,
                        'favourite_jobs_count' => $favouriteJobs
                    ],
                    'raw_database_queries' => [
                        'profile_views' => $rawProfileViews,
                        'followings' => $rawFollowings,
                        'cv_count' => $rawCvCount,
                        'messages' => $rawMessages,
                        'applied_jobs_count' => $rawAppliedJobs,
                        'favourite_jobs_count' => $rawFavouriteJobs
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Test user data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test recommended jobs functionality
     */
    public function testRecommendedJobs(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Test basic job count
            $totalJobs = \DB::table('jobs')->where('is_active', 1)->count();
            $expiredJobs = \DB::table('jobs')->where('is_active', 1)->where('expiry_date', '<', now())->count();
            $activeJobs = $totalJobs - $expiredJobs;

            // Test user profile data
            $userProfile = [
                'id' => $user->id,
                'name' => $user->name,
                'functional_area_id' => $user->functional_area_id,
                'industry_id' => $user->industry_id,
                'career_level_id' => $user->career_level_id,
                'job_experience_id' => $user->job_experience_id,
                'expected_salary' => $user->expected_salary
            ];

            // Test getMatchingJobs method
            $matchingJobs = [];
            try {
                $matchingJobs = $this->getMatchingJobs($user);
                // Ensure it's an array
                if (!is_array($matchingJobs)) {
                    $matchingJobs = [];
                }
            } catch (\Exception $e) {
                $matchingJobs = ['error' => $e->getMessage()];
            }

            // Test individual queries
            $functionalAreaJobs = \DB::table('jobs')
                ->where('is_active', 1)
                ->where('expiry_date', '>', now())
                ->where('functional_area_id', $user->functional_area_id)
                ->count();

            $careerLevelJobs = \DB::table('jobs')
                ->where('is_active', 1)
                ->where('expiry_date', '>', now())
                ->where('career_level_id', $user->career_level_id)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Recommended jobs test completed',
                'data' => [
                    'user_profile' => $userProfile,
                    'job_counts' => [
                        'total_active_jobs' => $totalJobs,
                        'expired_jobs' => $expiredJobs,
                        'valid_jobs' => $activeJobs,
                        'functional_area_matches' => $functionalAreaJobs,
                        'career_level_matches' => $careerLevelJobs
                    ],
                    'matching_jobs_result' => $matchingJobs,
                    'matching_jobs_count' => is_array($matchingJobs) ? count($matchingJobs) : 0
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get search suggestions
     */
    public function getSearchSuggestions(Request $request)
    {
        $search = $request->get('search', '');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search term must be at least 2 characters'
            ], 400);
        }

        try {
            // Search jobs
            $jobs = Job::select('id', 'title', 'slug')
                ->where('title', 'like', '%' . $search . '%')
                ->where('is_active', 1)
                ->take(5)
                ->get();

            // Search companies
            $companies = Company::select('id', 'name', 'slug')
                ->where('name', 'like', '%' . $search . '%')
                ->where('is_active', 1)
                ->take(5)
                ->get();

            // Search skills
            $skills = DB::table('job_skills')
                ->select('id', 'job_skill')
                ->where('job_skill', 'like', '%' . $search . '%')
                ->take(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'jobs' => $jobs,
                    'companies' => $companies,
                    'skills' => $skills
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get search suggestions'
            ], 500);
        }
    }
}
