<?php

namespace App;

use DB;
use App;
use App\Traits\Active;
use App\Traits\Featured;
use App\Traits\JobTrait;
use App\Traits\CountryStateCity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{

    use Active;
    use featured;
    use JobTrait;
    use CountryStateCity;

    protected $table = 'jobs';
    public $timestamps = true;
    protected $guarded = ['id'];
    //protected $dateFormat = 'U';

    protected $casts = [
        'expiry_date' => 'datetime',
        'display_end_date' => 'datetime',
        'promotion_urgent_until' => 'datetime',
        'promotion_featured_until' => 'datetime',
        'promotion_highlighted_until' => 'datetime',
        'is_draft' => 'boolean',
        'custom_field_data' => 'array',
    ];

    public function isPromotionUrgentActive(): bool
    {
        if (! (int) $this->is_urgent) {
            return false;
        }
        if ($this->promotion_urgent_until === null) {
            return $this->display_end_date === null || $this->display_end_date >= now();
        }

        return $this->promotion_urgent_until >= now();
    }

    public function isPromotionFeaturedActive(): bool
    {
        if (! (int) $this->is_featured) {
            return false;
        }
        if ($this->promotion_featured_until === null) {
            return $this->display_end_date === null || $this->display_end_date >= now();
        }

        return $this->promotion_featured_until >= now();
    }

    public function isPromotionHighlightedActive(): bool
    {
        if (! (int) $this->is_highlighted) {
            return false;
        }
        if ($this->promotion_highlighted_until === null) {
            return $this->display_end_date === null || $this->display_end_date >= now();
        }

        return $this->promotion_highlighted_until >= now();
    }

    public function scopeWherePromotionUrgentActive(Builder $query): Builder
    {
        return $query->where('jobs.is_urgent', 1)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('jobs.promotion_urgent_until')
                        ->where('jobs.promotion_urgent_until', '>=', now());
                })->orWhere(function ($q2) {
                    $q2->whereNull('jobs.promotion_urgent_until')
                        ->where(function ($q3) {
                            $q3->whereNull('jobs.display_end_date')
                                ->orWhere('jobs.display_end_date', '>=', now());
                        });
                });
            });
    }

    public function scopeWherePromotionFeaturedActive(Builder $query): Builder
    {
        return $query->where('jobs.is_featured', 1)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->whereNotNull('jobs.promotion_featured_until')
                        ->where('jobs.promotion_featured_until', '>=', now());
                })->orWhere(function ($q2) {
                    $q2->whereNull('jobs.promotion_featured_until')
                        ->where(function ($q3) {
                            $q3->whereNull('jobs.display_end_date')
                                ->orWhere('jobs.display_end_date', '>=', now());
                        });
                });
            });
    }

    public function scopeWherePromotionUrgentOrFeaturedActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where(function ($sub) {
                $sub->wherePromotionUrgentActive();
            })->orWhere(function ($sub) {
                $sub->wherePromotionFeaturedActive();
            });
        });
    }

    public static function orderByPromotionPriority(Builder $query): Builder
    {
        $now = now();

        return $query
            ->orderByRaw(
                'CASE WHEN jobs.is_urgent = 1 AND ((jobs.promotion_urgent_until IS NOT NULL AND jobs.promotion_urgent_until >= ?) OR (jobs.promotion_urgent_until IS NULL AND (jobs.display_end_date IS NULL OR jobs.display_end_date >= ?))) THEN 1 ELSE 0 END DESC',
                [$now, $now]
            )
            ->orderByRaw(
                'CASE WHEN jobs.is_featured = 1 AND ((jobs.promotion_featured_until IS NOT NULL AND jobs.promotion_featured_until >= ?) OR (jobs.promotion_featured_until IS NULL AND (jobs.display_end_date IS NULL OR jobs.display_end_date >= ?))) THEN 1 ELSE 0 END DESC',
                [$now, $now]
            )
            ->orderBy('jobs.id', 'DESC');
    }
    
    /**
     * Scope to filter jobs that are still within display duration.
     */
    public function scopeWithinDisplayDuration($query)
    {
        return $query->where(function($q) {
            $q->whereNull('display_end_date')
              ->orWhere('display_end_date', '>=', now());
        });
    }
    
    /**
     * Check if job display period has ended.
     */
    public function isDisplayExpired()
    {
        return $this->display_end_date && $this->display_end_date < now();
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id', 'id');
    }

    public function getCompany($field = '')
    {
        if (null !== $company = $this->company()->first()) {
            if (!empty($field)) {
                return $company->$field;
            } else {
                return $company;
            }
        }
    }

    public function jobSkills()
    {
        return $this->hasMany('App\JobSkillManager', 'job_id', 'id');
    }
    
    public function jobQuestions()
    {
        return $this->hasMany('App\JobQuestion', 'job_id', 'id')->orderBy('order', 'asc');
    }

    public function getJobSkillsArray()
    {
        return $this->jobSkills->pluck('job_skill_id')->toArray();
    }

    public function getJobSkillsStr()
    {
        $str = '';
        if ($this->jobSkills->count()) {
            $jobSkills = $this->jobSkills;
            foreach ($jobSkills as $jobSkillManager) {
                $str .= ' ' . $jobSkillManager->getJobSkill('job_skill');
            }
        }
        return $str;
    }

    public function getJobSkillsList()
    {
        $str = '';
        if ($this->jobSkills->count()) {
            $jobSkills = $this->jobSkills;
            foreach ($jobSkills as $jobSkillManager) {
                $skill = $jobSkillManager->getJobSkill();
                $str .= '<li><a href="' . route('job.list', ['job_skill_id[]' => $skill->job_skill_id]) . '">' . $skill->job_skill . '</a></li>';
            }
        }
        return $str;
    }

    public function careerLevel()
    {
        return $this->belongsTo('App\CareerLevel', 'career_level_id', 'career_level_id');
    }

    public function getCareerLevel($field = '')
    {
        $careerLevel = $this->careerLevel()->lang()->first();
        if (null === $careerLevel) {
            $careerLevel = $this->careerLevel()->first();
        }
        if (null !== $careerLevel) {
            if (!empty($field)) {
                return $careerLevel->$field;
            } else {
                return $careerLevel;
            }
        }
    }

    public function functionalArea()
    {
        return $this->belongsTo('App\FunctionalArea', 'functional_area_id', 'functional_area_id');
    }

    public function getFunctionalArea($field = '')
    {
        $functionalArea = $this->functionalArea()->lang()->first();
        if (null === $functionalArea) {
            $functionalArea = $this->functionalArea()->first();
        }
        if (null !== $functionalArea) {
            if (!empty($field)) {
                return $functionalArea->$field;
            } else {
                return $functionalArea;
            }
        }
    }

    public function jobType()
    {
        return $this->belongsTo('App\JobType', 'job_type_id', 'job_type_id');
    }

    public function getJobType($field = '')
    {
        $jobType = $this->jobType()->lang()->first();
        if (null === $jobType) {
            $jobType = $this->jobType()->first();
        }
        if (null !== $jobType) {
            if (!empty($field)) {
                return $jobType->$field;
            } else {
                return $jobType;
            }
        }
    }

    public function jobShift()
    {
        return $this->belongsTo('App\JobShift', 'job_shift_id', 'job_shift_id');
    }

    public function getJobShift($field = '')
    {
        $jobShift = $this->jobShift()->lang()->first();
        if (null === $jobShift) {
            $jobShift = $this->jobShift()->first();
        }
        if (null !== $jobShift) {
            if (!empty($field)) {
                return $jobShift->$field;
            } else {
                return $jobShift;
            }
        }
    }

    public function salaryPeriod()
    {
        return $this->belongsTo('App\SalaryPeriod', 'salary_period_id', 'salary_period_id');
    }

    public function getSalaryPeriod($field = '')
    {
        $salaryPeriod = $this->salaryPeriod()->lang()->first();
        if (null === $salaryPeriod) {
            $salaryPeriod = $this->salaryPeriod()->first();
        }
        if (null !== $salaryPeriod) {
            if (!empty($field)) {
                return $salaryPeriod->$field;
            } else {
                return $salaryPeriod;
            }
        }
    }

    public function gender()
    {
        return $this->belongsTo('App\Gender', 'gender_id', 'gender_id');
    }

    public function getGender($field = '')
    {
        $gender = $this->gender()->lang()->first();
        if (null === $gender) {
            $gender = $this->gender()->first();
        }
        if (null !== $gender) {
            if (!empty($field)) {
                return $gender->$field;
            } else {
                return $gender;
            }
        } else {
            return __('No Preference');
        }
    }

    public function degreeLevel()
    {
        return $this->belongsTo('App\DegreeLevel', 'degree_level_id', 'degree_level_id');
    }

    public function getDegreeLevel($field = '')
    {
        $degreeLevel = $this->degreeLevel()->lang()->first();
        if (null === $degreeLevel) {
            $degreeLevel = $this->degreeLevel()->first();
        }
        if (null !== $degreeLevel) {
            if (!empty($field)) {
                return $degreeLevel->$field;
            } else {
                return $degreeLevel;
            }
        }
    }

    public function jobExperience()
    {
        return $this->belongsTo('App\JobExperience', 'job_experience_id', 'job_experience_id');
    }

    public function getJobExperience($field = '')
    {
        $jobExperience = $this->jobExperience()->lang()->first();
        if (null === $jobExperience) {
            $jobExperience = $this->jobExperience()->first();
        }
        if (null !== $jobExperience) {
            if (!empty($field)) {
                return $jobExperience->$field;
            } else {
                return $jobExperience;
            }
        }
    }

    public function industry()
    {
        return $this->belongsTo('App\Industry', 'industry_id', 'industry_id');
    }

    public function getIndustry($field = '')
    {
        $industry = $this->industry()->lang()->first();
        if (null === $industry) {
            $industry = $this->industry()->first();
        }
        if (null === $industry && null !== $this->company) {
            return $this->company->getIndustry($field);
        }
        if (null !== $industry) {
            if (!empty($field)) {
                return $industry->$field;
            } else {
                return $industry;
            }
        }
    }

    /**
     * Parsed benefit lines for list cards (HTML list, newlines, or comma-separated).
     * Caps at $max items (default 4) for compact display.
     *
     * @return array<int, string>
     */
    public function getBenefitsPreviewLines(int $max = 4): array
    {
        $raw = trim((string) $this->benefits);
        if ($raw === '') {
            return [];
        }

        if (stripos($raw, '<li') !== false) {
            preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $raw, $m);
            $lines = array_map(function ($html) {
                return trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
            }, $m[1] ?? []);
        } else {
            $normalized = preg_replace('/<\s*br\s*\/?>/i', "\n", $raw);
            $normalized = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n", $normalized);
            $normalized = preg_replace('/<\/p>/i', "\n", $normalized);
            $normalized = preg_replace('/<p[^>]*>/i', '', $normalized);
            $plain = trim(strip_tags($normalized));
            $plain = html_entity_decode($plain, ENT_QUOTES, 'UTF-8');
            $lines = preg_split('/\r\n|\r|\n/', $plain) ?: [];
            $lines = array_map('trim', $lines);
            $lines = array_values(array_filter($lines, static function ($l) {
                return $l !== '';
            }));
            if (count($lines) === 1 && isset($lines[0]) && strpos($lines[0], ',') !== false) {
                $split = array_map('trim', explode(',', $lines[0]));
                $lines = array_values(array_filter($split, static function ($l) {
                    return $l !== '';
                }));
            }
        }

        $lines = array_values(array_filter($lines, static function ($l) {
            return $l !== '';
        }));

        return array_slice($lines, 0, $max);
    }

    /*     * ****************************** */

    public function appliedUsers()
    {
        return $this->hasMany('App\JobApply', 'job_id', 'id');
    }

    public function getAppliedUserIdsArray()
    {
        return $this->appliedUsers->pluck('user_id')->toArray();
    }

    /*     * ***************************** */

    public function medoCategory()
    {
        return $this->belongsTo('App\Models\Medo\Category', 'medo_category_id');
    }

    public function medoProvince()
    {
        return $this->belongsTo('App\Models\Medo\Province', 'medo_province_id');
    }

    public function medoCity()
    {
        return $this->belongsTo('App\Models\Medo\City', 'medo_city_id');
    }

    public function medoEmployer()
    {
        return $this->belongsTo('App\Models\Medo\Employer', 'medo_employer_id');
    }

    protected static function booted()
    {
        static::saving(function ($job) {
            // The form field functional_area_id actually contains a job_categories.id
            // Auto-map to the correct functional_area by matching slug
            if ($job->functional_area_id) {
                $jobCategory = \App\JobCategory::find($job->functional_area_id);
                if ($jobCategory && $jobCategory->slug) {
                    $functionalArea = \App\FunctionalArea::where('slug', $jobCategory->slug)->first();
                    if ($functionalArea) {
                        $job->functional_area_id = $functionalArea->functional_area_id;
                    }
                    $job->job_category_id = $jobCategory->id;
                } elseif (!$job->job_category_id) {
                    $job->job_category_id = $job->functional_area_id;
                }
            }

            // Auto-map legacy functional area to medo category
            if (!$job->medo_category_id && $job->functional_area_id) {
                $categoryMapping = [
                    655 => 1, // HCA
                    656 => 2, // LPN
                    657 => 3, // RN
                ];
                if (isset($categoryMapping[$job->functional_area_id])) {
                    $job->medo_category_id = $categoryMapping[$job->functional_area_id];
                }
            }

            // Auto-map legacy city to medo city and province
            if (!$job->medo_city_id && $job->city_id) {
                $cityMapping = [
                    10125 => 2, // Edmonton
                    10107 => 1, // Calgary
                    10169 => 3, // Red Deer
                    10150 => 4, // Lethbridge
                    10156 => 5, // Medicine Hat
                    10135 => 7, // Grande Prairie
                ];
                if (isset($cityMapping[$job->city_id])) {
                    $job->medo_city_id = $cityMapping[$job->city_id];
                    $job->medo_province_id = 1; // Alberta (default for launch)
                }
            }
        });
    }
}
