<?php

namespace App\Services;

use App\City;
use App\FunctionalArea;
use App\Helpers\LocationHelper;
use App\Industry;
use App\JobSkill;
use App\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSubmittedLookupService
{
    public const OTHER_VALUE = '0';

    /**
     * After validation: resolve "Other" (0) selections into real FK ids on the request.
     */
    public function mergeUserSubmittedProfileRequest(Request $request): void
        {
            DB::transaction(function () use ($request) {
                if ((int) $request->input('industry_id') === 0) {
                    $name = (string) $request->input('custom_industry', '');
                    if (mb_strlen(trim($name)) >= 2) {
                        $id = $this->findOrCreateIndustry($name);
                        $request->merge(['industry_id' => $id]);
                    }
                }

                if ((int) $request->input('job_category_id') === 0) {
                    $name = (string) $request->input('custom_job_category', '');
                    if (mb_strlen(trim($name)) >= 2) {
                        $id = $this->findOrCreateJobCategory($name);
                        $request->merge(['job_category_id' => $id]);
                    }
                }

                if (LocationHelper::showCity() && (int) $request->input('city_id') === 0) {
                    $name = (string) $request->input('custom_city_name', '');
                    $stateId = $this->resolveStateIdForCity($request);
                    if (mb_strlen(trim($name)) >= 2 && $stateId > 0) {
                        $id = $this->findOrCreateCity($name, $stateId);
                        $request->merge(['city_id' => $id]);
                    }
                }
            });
        }


    /**
     * Employer job post / update: functional area, city, job skills multiselect.
     */
    public function mergeUserSubmittedJobRequest(Request $request): void
    {
        DB::transaction(function () use ($request) {
            if ((int) $request->input('industry_id') === 0) {
                $name = (string) $request->input('custom_industry', '');
                if (mb_strlen(trim($name)) >= 2) {
                    $id = $this->findOrCreateIndustry($name);
                    $request->merge(['industry_id' => $id]);
                }
            }

            if ((int) $request->input('job_category_id') === 0) {
                $name = (string) $request->input('custom_job_category', '');
                if (mb_strlen(trim($name)) >= 2) {
                    $id = $this->findOrCreateJobCategory($name);
                    $request->merge(['job_category_id' => $id]);
                }
            }

            if (LocationHelper::showCity() && (int) $request->input('city_id') === 0) {
                $name = (string) $request->input('custom_city_name', '');
                $stateId = $this->resolveStateIdForCity($request);
                if (mb_strlen(trim($name)) >= 2 && $stateId > 0) {
                    $id = $this->findOrCreateCity($name, $stateId);
                    $request->merge(['city_id' => $id]);
                }
            }

            $skills = $request->input('skills');
            if (! is_array($skills)) {
                $skills = [];
            }
            $merged = $this->mergeJobSkillIdsFromArray(
                $skills,
                (string) $request->input('custom_job_skills_lines', '')
            );
            $request->merge(['skills' => $merged]);
        });
    }

    /**
     * Profile skill modal: job_skill_id 0 + custom name.
     */
    public function resolveProfileJobSkillId(Request $request): void
    {
        if ((int) $request->input('job_skill_id') !== 0) {
            return;
        }
        DB::transaction(function () use ($request) {
            $name = (string) $request->input('custom_job_skill_name', '');
            $id = $this->findOrCreateJobSkill($name);
            $request->merge(['job_skill_id' => $id]);
        });
    }

    /**
     * @param  array<int|string>  $skillIds
     * @return array<int>
     */
    public function mergeJobSkillIdsFromArray(array $skillIds, string $customLinesRaw): array
    {
        $out = [];
        foreach ($skillIds as $sid) {
            if ((int) $sid === 0) {
                continue;
            }
            $out[] = (int) $sid;
        }
        $lines = preg_split('/\r\n|\r|\n/', $customLinesRaw) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $out[] = $this->findOrCreateJobSkill($line);
        }

        return array_values(array_unique(array_filter($out)));
    }

    public function resolveStateIdForCity(Request $request): int
    {
        $sid = (int) $request->input('state_id');
        if ($sid > 0) {
            return $sid;
        }
        $countryId = (int) $request->input('country_id');
        if ($countryId <= 0) {
            return 0;
        }
        $state = State::query()
            ->where('country_id', $countryId)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        return $state ? (int) $state->id : 0;
    }

    public function findOrCreateFunctionalArea(string $name): int
    {
        $name = trim($name);
        if (mb_strlen($name) < 2) {
            throw new \InvalidArgumentException('Functional area name too short.');
        }
        $name = mb_substr($name, 0, 200);
        $lang = app()->getLocale();

        $existing = FunctionalArea::query()
            ->where('lang', $lang)
            ->whereRaw('LOWER(TRIM(functional_area)) = ?', [mb_strtolower($name)])
            ->orderByDesc('is_default')
            ->first();
        if ($existing) {
            $fid = (int) ($existing->functional_area_id ?: $existing->id);

            return $fid > 0 ? $fid : (int) $existing->id;
        }

        $row = new FunctionalArea();
        $row->functional_area = $name;
        $row->is_active = 1;
        $row->lang = $lang;
        $row->is_default = 1;
        $row->sort_order = 99999;
        $row->image = '';
        $row->save();
        $row->functional_area_id = $row->id;
        $row->save();

        return (int) $row->functional_area_id;
    }

    public function findOrCreateJobCategory(string $name): int
    {
        $name = trim($name);
        if (mb_strlen($name) < 2) {
            throw new \InvalidArgumentException('Job category name too short.');
        }
        $name = mb_substr($name, 0, 200);
        $lang = app()->getLocale();

        $existing = \App\JobCategory::query()
            ->where('lang', $lang)
            ->whereRaw('LOWER(TRIM(job_category)) = ?', [mb_strtolower($name)])
            ->orderByDesc('is_default')
            ->first();
        if ($existing) {
            $jcid = (int) ($existing->job_category_id ?: $existing->id);

            return $jcid > 0 ? $jcid : (int) $existing->id;
        }

        $row = new \App\JobCategory();
        $row->job_category = $name;
        $row->is_active = 1;
        $row->lang = $lang;
        $row->is_default = 1;
        $row->sort_order = 99999;
        $row->image = '';
        $row->save();
        $row->job_category_id = $row->id;
        $row->save();

        return (int) $row->job_category_id;
    }

    public function findOrCreateIndustry(string $name): int
    {
        $name = trim($name);
        if (mb_strlen($name) < 2) {
            throw new \InvalidArgumentException('Industry name too short.');
        }
        $name = mb_substr($name, 0, 200);
        $lang = app()->getLocale();

        $existing = Industry::query()
            ->where('lang', $lang)
            ->whereRaw('LOWER(TRIM(industry)) = ?', [mb_strtolower($name)])
            ->orderByDesc('is_default')
            ->first();
        if ($existing) {
            $iid = (int) ($existing->industry_id ?: $existing->id);

            return $iid > 0 ? $iid : (int) $existing->id;
        }

        $row = new Industry();
        $row->industry = $name;
        $row->is_active = 1;
        $row->lang = $lang;
        $row->is_default = 1;
        $row->sort_order = 99999;
        $row->save();
        $row->industry_id = $row->id;
        $row->save();

        return (int) $row->industry_id;
    }

    public function findOrCreateJobSkill(string $name): int
    {
        $name = trim($name);
        if (mb_strlen($name) < 2) {
            throw new \InvalidArgumentException('Skill name too short.');
        }
        $name = mb_substr($name, 0, 200);
        $lang = app()->getLocale();

        $existing = JobSkill::query()
            ->where('lang', $lang)
            ->whereRaw('LOWER(TRIM(job_skill)) = ?', [mb_strtolower($name)])
            ->orderByDesc('is_default')
            ->first();
        if ($existing) {
            $kid = (int) ($existing->job_skill_id ?: $existing->id);

            return $kid > 0 ? $kid : (int) $existing->id;
        }

        $row = new JobSkill();
        $row->job_skill = $name;
        $row->is_active = 1;
        $row->lang = $lang;
        $row->is_default = 1;
        $row->sort_order = 99999;
        $row->save();
        $row->job_skill_id = $row->id;
        $row->save();

        return (int) $row->job_skill_id;
    }

    public function findOrCreateCity(string $name, int $stateId): int
    {
        $name = trim($name);
        if (mb_strlen($name) < 2) {
            throw new \InvalidArgumentException('City name too short.');
        }
        if ($stateId <= 0) {
            throw new \InvalidArgumentException('State is required to add a city.');
        }
        $name = mb_substr($name, 0, 30);
        $lang = app()->getLocale();

        $existing = City::query()
            ->where('state_id', $stateId)
            ->where('lang', $lang)
            ->whereRaw('LOWER(TRIM(city)) = ?', [mb_strtolower($name)])
            ->orderByDesc('is_default')
            ->first();
        if ($existing) {
            $cid = (int) ($existing->city_id ?: $existing->id);

            return $cid > 0 ? $cid : (int) $existing->id;
        }

        $row = new City();
        $row->city = $name;
        $row->state_id = $stateId;
        $row->is_active = 1;
        $row->lang = $lang;
        $row->is_default = 1;
        $row->sort_order = 9999;
        $row->save();
        $row->city_id = $row->id;
        $row->save();

        return (int) $row->city_id;
    }
}
