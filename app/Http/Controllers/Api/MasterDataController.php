<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class MasterDataController extends Controller
{
    /**
     * Get job types
     */
    public function getJobTypes()
    {
        try {
            // Use direct database query to avoid helper method issues
            $jobTypes = DB::table('job_types')
                ->select('id', 'job_type')
                ->where('is_active', 1)
                ->where('is_default', 1)
                ->orderBy('job_type')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $jobTypes->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->job_type
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching job types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get genders
     */
    public function getGenders()
    {
        try {
            \Log::info('GetGenders called');
            
            // Use the same method as web version
            $gendersArray = \App\Helpers\DataArrayHelper::langGendersArray();
            
            \Log::info('Genders from helper', [
                'count' => count($gendersArray),
                'data' => $gendersArray
            ]);
            
            // Convert to the format expected by the app
            $formattedGenders = [];
            foreach ($gendersArray as $id => $name) {
                $formattedGenders[] = [
                    'id' => $id,
                    'name' => $name,
                    'gender' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedGenders,
                'count' => count($formattedGenders),
                'debug' => [
                    'method' => 'Using DataArrayHelper::langGendersArray() - same as web version',
                    'locale' => \App::getLocale()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching genders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching genders: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get marital statuses
     */
    public function getMaritalStatuses()
    {
        try {
            \Log::info('GetMaritalStatuses called');
            
            // Use the same method as web version
            $maritalStatusesArray = \App\Helpers\DataArrayHelper::langMaritalStatusesArray();
            
            \Log::info('Marital statuses from helper', [
                'count' => count($maritalStatusesArray),
                'data' => $maritalStatusesArray
            ]);
            
            // Convert to the format expected by the app
            $formattedMaritalStatuses = [];
            foreach ($maritalStatusesArray as $id => $name) {
                $formattedMaritalStatuses[] = [
                    'id' => $id,
                    'name' => $name,
                    'marital_status' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedMaritalStatuses,
                'count' => count($formattedMaritalStatuses),
                'debug' => [
                    'method' => 'Using DataArrayHelper::langMaritalStatusesArray() - same as web version',
                    'locale' => \App::getLocale()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching marital statuses', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching marital statuses: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get career levels
     */
    public function getCareerLevels()
    {
        try {
            \Log::info('GetCareerLevels called');
            
            // Query database directly - filter English only and non-empty values
            $careerLevels = DB::table('career_levels')
                ->select('career_level_id as id', 'career_level', 'career_level as name', 'lang', 'is_active')
                ->where('is_active', 1)
                ->where(function($query) {
                    $query->where('lang', 'en')
                          ->orWhereNull('lang')
                          ->orWhere('lang', '');
                })
                ->whereNotNull('career_level')
                ->where('career_level', '!=', '')
                ->orderBy('career_level')
                ->get();

            // Remove duplicates by ID (keep first occurrence)
            $uniqueCareerLevels = $careerLevels->unique('id')->values();

            \Log::info('Career levels query result', [
                'total_count' => $careerLevels->count(),
                'unique_count' => $uniqueCareerLevels->count(),
                'first_3' => $uniqueCareerLevels->take(3)->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $uniqueCareerLevels->toArray(),
                'count' => $uniqueCareerLevels->count(),
                'debug' => [
                    'query' => 'SELECT career_level_id as id, career_level FROM career_levels WHERE is_active = 1 AND (lang = "en" OR lang IS NULL) AND career_level != "" ORDER BY career_level'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching career levels', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching career levels: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get job shifts
     */
    public function getJobShifts()
    {
        try {
            // Use the helper method to get default language data
            $jobShifts = \App\Helpers\DataArrayHelper::defaultJobShiftsArray();
            
            // Convert to the format expected by frontend
            $formattedJobShifts = [];
            foreach ($jobShifts as $id => $name) {
                $formattedJobShifts[] = [
                    'id' => $id,
                    'name' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedJobShifts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching job shifts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get degree levels
     */
    public function getDegreeLevels()
    {
        try {
            $degreeLevels = DB::table('degree_levels')
                ->select('degree_level_id as id', 'degree_level as name')
                ->where('is_active', 1)
                ->where('is_default', 1)
                ->orderBy('degree_level')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $degreeLevels->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching degree levels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job experiences
     */
    public function getJobExperiences()
    {
        try {
            \Log::info('GetJobExperiences called');
            
            // Query database directly to get ALL English job experiences
            $jobExperiences = DB::table('job_experiences')
                ->select('job_experience_id as id', 'job_experience', 'job_experience as name', 'lang', 'is_active', 'sort_order')
                ->where('is_active', 1)
                ->where(function($query) {
                    $query->where('lang', 'en')
                          ->orWhereNull('lang')
                          ->orWhere('lang', '');
                })
                ->whereNotNull('job_experience')
                ->where('job_experience', '!=', '')
                ->orderBy('sort_order')
                ->orderBy('job_experience')
                ->get();

            // Remove duplicates by ID (keep first occurrence)
            $uniqueJobExperiences = $jobExperiences->unique('id')->values();

            \Log::info('Job experiences query result', [
                'total_count' => $jobExperiences->count(),
                'unique_count' => $uniqueJobExperiences->count(),
                'first_5' => $uniqueJobExperiences->take(5)->toArray(),
                'last_5' => $uniqueJobExperiences->take(-5)->toArray()
            ]);

            // Convert to the format expected by the app
            $formattedJobExperiences = [];
            foreach ($uniqueJobExperiences as $experience) {
                $formattedJobExperiences[] = [
                    'id' => $experience->id,
                    'name' => $experience->name,
                    'job_experience' => $experience->job_experience
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedJobExperiences,
                'count' => count($formattedJobExperiences),
                'debug' => [
                    'method' => 'Direct database query - all English job experiences',
                    'query' => 'SELECT job_experience_id as id, job_experience FROM job_experiences WHERE is_active = 1 AND (lang = "en" OR lang IS NULL OR lang = "") AND job_experience != "" ORDER BY sort_order, job_experience',
                    'total_found' => $jobExperiences->count(),
                    'unique_returned' => $uniqueJobExperiences->count()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching job experiences', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching job experiences: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get functional areas
     */
    public function getFunctionalAreas()
    {
        try {
            \Log::info('GetFunctionalAreas called');
            
            // Query database directly - filter English only and non-empty values
            $functionalAreas = DB::table('functional_areas')
                ->select('functional_area_id as id', 'functional_area', 'functional_area as name', 'lang', 'is_active')
                ->where('is_active', 1)
                ->where(function($query) {
                    $query->where('lang', 'en')
                          ->orWhereNull('lang')
                          ->orWhere('lang', '');
                })
                ->whereNotNull('functional_area')
                ->where('functional_area', '!=', '')
                ->orderBy('functional_area')
                ->get();

            // Remove duplicates by ID (keep first occurrence)
            $uniqueFunctionalAreas = $functionalAreas->unique('id')->values();

            \Log::info('Functional areas query result', [
                'total_count' => $functionalAreas->count(),
                'unique_count' => $uniqueFunctionalAreas->count(),
                'first_3' => $uniqueFunctionalAreas->take(3)->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $uniqueFunctionalAreas->toArray(),
                'count' => $uniqueFunctionalAreas->count(),
                'debug' => [
                    'query' => 'SELECT functional_area_id as id, functional_area FROM functional_areas WHERE is_active = 1 AND (lang = "en" OR lang IS NULL) AND functional_area != "" ORDER BY functional_area'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching functional areas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching functional areas: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get industries
     */
    public function getIndustries()
    {
        try {
            \Log::info('GetIndustries called');
            
            // Query database directly - filter English only and non-empty values
            $industries = DB::table('industries')
                ->select('industry_id as id', 'industry', 'industry as name', 'lang', 'is_active')
                ->where('is_active', 1)
                ->where(function($query) {
                    $query->where('lang', 'en')
                          ->orWhereNull('lang')
                          ->orWhere('lang', '');
                })
                ->whereNotNull('industry')
                ->where('industry', '!=', '')
                ->orderBy('industry')
                ->get();

            // Remove duplicates by ID (keep first occurrence)
            $uniqueIndustries = $industries->unique('id')->values();

            \Log::info('Industries query result', [
                'total_count' => $industries->count(),
                'unique_count' => $uniqueIndustries->count(),
                'first_3' => $uniqueIndustries->take(3)->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $uniqueIndustries->toArray(),
                'count' => $uniqueIndustries->count(),
                'debug' => [
                    'query' => 'SELECT industry_id as id, industry FROM industries WHERE is_active = 1 AND (lang = "en" OR lang IS NULL) AND industry != "" ORDER BY industry'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching industries', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching industries: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get countries
     */
    public function getCountries()
    {
        try {
            // Use the helper method to get default language data
            $countries = \App\Helpers\DataArrayHelper::defaultCountriesArray();
            
            // Convert to the format expected by frontend
            $formattedCountries = [];
            foreach ($countries as $id => $name) {
                $formattedCountries[] = [
                    'id' => $id,
                    'name' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedCountries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching countries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get states by country
     */
    public function getStates(Request $request)
    {
        try {
            $countryId = $request->get('country_id');
            
            if (!$countryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Country ID is required'
                ], 400);
            }

            // Use the same helper as the existing endpoints
            $states = \App\Helpers\DataArrayHelper::langStatesArray($countryId);
            
            // Convert to the format expected by frontend
            $formattedStates = [];
            foreach ($states as $id => $name) {
                $formattedStates[] = [
                    'id' => $id,
                    'name' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedStates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching states: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities by state
     */
    public function getCities(Request $request)
    {
        try {
            $stateId = $request->get('state_id');
            
            if (!$stateId) {
                return response()->json([
                    'success' => false,
                    'message' => 'State ID is required'
                ], 400);
            }

            // Use the same helper as the existing endpoints
            $cities = \App\Helpers\DataArrayHelper::langCitiesArray($stateId);
            
            // Convert to the format expected by frontend
            $formattedCities = [];
            foreach ($cities as $id => $name) {
                $formattedCities[] = [
                    'id' => $id,
                    'name' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedCities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job skills
     */
    public function getJobSkills()
    {
        try {
            // Use the helper method to get default language data
            $jobSkills = \App\Helpers\DataArrayHelper::defaultJobSkillsArray();
            
            // Convert to the format expected by frontend
            $formattedSkills = [];
            foreach ($jobSkills as $id => $name) {
                $formattedSkills[] = [
                    'id' => $id,
                    'name' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedSkills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching job skills: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get languages
     */
    public function getLanguages()
    {
        try {
            // Use the helper method to get languages
            $languages = \App\Helpers\DataArrayHelper::languagesArray();
            
            // Convert to the format expected by frontend
            $formattedLanguages = [];
            foreach ($languages as $id => $name) {
                $formattedLanguages[] = [
                    'id' => $id,
                    'name' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedLanguages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching languages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get language levels
     */
    public function getLanguageLevels()
    {
        try {
            // Use hardcoded fallback for now to fix the immediate issue
            $languageLevels = [
                1 => 'Beginner',
                2 => 'Intermediate', 
                3 => 'Expert',
                4 => 'Native'
            ];
            
            // Convert to the format expected by frontend
            $formattedLevels = [];
            foreach ($languageLevels as $id => $name) {
                $formattedLevels[] = [
                    'id' => $id,
                    'name' => $name
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedLevels
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching language levels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get degree types
     */
    public function getDegreeTypes()
    {
        try {
            $degreeTypes = DB::table('degree_types')
                ->select('degree_type_id as id', 'degree_type as name')
                ->where('is_active', 1)
                ->where('is_default', 1)
                ->orderBy('degree_type')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $degreeTypes->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching degree types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get major subjects
     */
    public function getMajorSubjects()
    {
        try {
            $majorSubjects = DB::table('major_subjects')
                ->select('major_subject_id as id', 'major_subject as name')
                ->where('is_active', 1)
                ->where('is_default', 1)
                ->orderBy('major_subject')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $majorSubjects->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching major subjects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get result types
     */
    public function getResultTypes()
    {
        try {
            $resultTypes = DB::table('result_types')
                ->select('result_type_id as id', 'result_type as name')
                ->where('is_active', 1)
                ->where('is_default', 1)
                ->orderBy('result_type')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $resultTypes->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching result types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get states by country ID (from URL parameter)
     */
    public function getStatesByCountry($countryId)
    {
        try {
            \Log::info('GetStatesByCountry called', ['country_id' => $countryId]);
            
            // Query database directly for states using correct column names
            $states = DB::table('states')
                ->select('state_id as id', 'state', 'state as name', 'lang', 'is_active', 'country_id')
                ->where('country_id', $countryId)
                ->where('is_active', 1)
                ->orderBy('state')
                ->get();

            \Log::info('States query result', [
                'count' => $states->count(),
                'first_3' => $states->take(3)->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $states->toArray(),
                'count' => $states->count(),
                'debug' => [
                    'country_id' => $countryId,
                    'query' => 'SELECT state_id as id, state, state as name, lang, is_active, country_id FROM states WHERE country_id = ? AND is_active = 1 ORDER BY state'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching states', [
                'country_id' => $countryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching states: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get cities by state ID (from URL parameter)
     */
    public function getCitiesByState($stateId)
    {
        try {
            \Log::info('GetCitiesByState called', ['state_id' => $stateId]);
            
            // Query database directly for cities using correct column names
            $cities = DB::table('cities')
                ->select('city_id as id', 'city', 'city as name', 'lang', 'is_active', 'state_id')
                ->where('state_id', $stateId)
                ->where('is_active', 1)
                ->orderBy('city')
                ->get();

            \Log::info('Cities query result', [
                'count' => $cities->count(),
                'first_3' => $cities->take(3)->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $cities->toArray(),
                'count' => $cities->count(),
                'debug' => [
                    'state_id' => $stateId,
                    'query' => 'SELECT city_id as id, city, city as name, lang, is_active, state_id FROM cities WHERE state_id = ? AND is_active = 1 ORDER BY city'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching cities', [
                'state_id' => $stateId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cities: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get all master data in one call
     */
    public function getAllMasterData()
    {
        try {
            \Log::info('GetAllMasterData called');
            
            $data = [
                'countries' => $this->getCountries()->getData(true),
                'genders' => $this->getGenders()->getData(true),
                'marital_statuses' => $this->getMaritalStatuses()->getData(true),
                'job_experiences' => $this->getJobExperiences()->getData(true),
                'career_levels' => $this->getCareerLevels()->getData(true),
                'industries' => $this->getIndustries()->getData(true),
                'functional_areas' => $this->getFunctionalAreas()->getData(true),
                'job_types' => $this->getJobTypes()->getData(true),
                'job_shifts' => $this->getJobShifts()->getData(true),
                'languages' => $this->getLanguages()->getData(true),
                'language_levels' => $this->getLanguageLevels()->getData(true),
                'degree_levels' => $this->getDegreeLevels()->getData(true),
                'degree_types' => $this->getDegreeTypes()->getData(true),
                'major_subjects' => $this->getMajorSubjects()->getData(true),
                'result_types' => $this->getResultTypes()->getData(true),
                'job_skills' => $this->getJobSkills()->getData(true)
            ];

            \Log::info('GetAllMasterData result', [
                'countries_count' => count($data['countries']['data'] ?? []),
                'genders_count' => count($data['genders']['data'] ?? []),
                'marital_statuses_count' => count($data['marital_statuses']['data'] ?? []),
                'job_experiences_count' => count($data['job_experiences']['data'] ?? []),
                'career_levels_count' => count($data['career_levels']['data'] ?? []),
                'industries_count' => count($data['industries']['data'] ?? []),
                'functional_areas_count' => count($data['functional_areas']['data'] ?? [])
            ]);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching all master data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching master data: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
} 