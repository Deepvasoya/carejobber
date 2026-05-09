<?php

namespace App\Services\Medo;

use App\City as LegacyCity;
use App\Job as LegacyJob;
use App\JobCategory;
use App\Models\Medo\Category;
use App\Models\Medo\City;
use App\Models\Medo\Employer;
use App\Models\Medo\Job as MedoJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyJobSyncService
{
    private const SOURCE = 'legacy';

    private const CATEGORY_KEYWORDS = [
        'hca' => [
            'hca',
            'health care aide',
            'healthcare aide',
            'health care assistant',
            'healthcare assistant',
            'personal care aide',
            'personal support worker',
            'psw',
            'care aide',
            'caregiver',
            'care giver',
            'nursing assistant',
        ],
        'lpn' => [
            'lpn',
            'licensed practical nurse',
            'practical nurse',
        ],
        'rn' => [
            'rn',
            'registered nurse',
        ],
    ];

    private const LEGACY_CITY_SLUGS = [
        10107 => 'calgary',
        10125 => 'edmonton',
        10169 => 'red-deer',
        10150 => 'lethbridge',
        10156 => 'medicine-hat',
        10135 => 'grande-prairie',
    ];

    private const CITY_ALIASES = [
        'calgary' => 'calgary',
        'edmonton' => 'edmonton',
        'red deer' => 'red-deer',
        'lethbridge' => 'lethbridge',
        'medicine hat' => 'medicine-hat',
        'fort mcmurray' => 'fort-mcmurray',
        'grande prairie' => 'grande-prairie',
        'airdrie' => 'airdrie',
        'st. albert' => 'st-albert',
        'st albert' => 'st-albert',
        'sherwood park' => 'sherwood-park',
        'lloydminster' => 'lloydminster',
        'camrose' => 'camrose',
        'cochrane' => 'cochrane',
        'okotoks' => 'okotoks',
        'spruce grove' => 'spruce-grove',
    ];

    public function sync(LegacyJob $legacyJob): ?MedoJob
    {
        $externalId = $this->externalId($legacyJob);
        $existing = MedoJob::where('source', self::SOURCE)
            ->where('external_id', $externalId)
            ->first();

        if (! $this->isPublishable($legacyJob)) {
            $this->deleteExisting($existing);
            $this->clearLegacyMetadata($legacyJob);
            return null;
        }

        $category = $this->resolveCategory($legacyJob);
        $city = $this->resolveCity($legacyJob);

        if (! $category || ! $city) {
            $this->deleteExisting($existing);
            $this->clearLegacyMetadata($legacyJob);
            return null;
        }

        $employer = $this->resolveEmployer($legacyJob, $city);
        $slug = $this->uniqueSlug($this->baseSlug($legacyJob), $city->id, $existing ? $existing->id : null);

        $payload = [
            'external_id' => $externalId,
            'source' => self::SOURCE,
            'slug' => $slug,
            'title' => trim((string) $legacyJob->title),
            'description' => (string) ($legacyJob->description ?? ''),
            'category_id' => $category->id,
            'province_id' => $city->province_id,
            'city_id' => $city->id,
            'employer_id' => $employer ? $employer->id : null,
            'facility_name' => $legacyJob->company ? $legacyJob->company->name : null,
            'employment_type' => $this->employmentType($legacyJob),
            'shift_type' => $this->shiftType($legacyJob),
            'setting' => null,
            'wage_min' => $this->moneyOrNull($legacyJob->salary_from),
            'wage_max' => $this->moneyOrNull($legacyJob->salary_to),
            'wage_period' => $this->wagePeriod($legacyJob),
            'posted_at' => $legacyJob->created_at ?: now(),
            'expires_at' => $legacyJob->expiry_date,
            'apply_url' => $this->applyUrl($legacyJob),
            'is_new_grad_friendly' => false,
            'has_signing_bonus' => false,
        ];

        if ($existing) {
            $this->forgetJobCache($existing);
            $existing->update($payload);
            $medoJob = $existing->fresh();
        } else {
            $medoJob = MedoJob::create($payload);
        }

        $this->forgetJobCache($medoJob);
        $this->syncLegacyMetadata($legacyJob, $payload);

        return $medoJob;
    }

    public function deleteForLegacyJob(LegacyJob $legacyJob): void
    {
        $existing = MedoJob::where('source', self::SOURCE)
            ->where('external_id', $this->externalId($legacyJob))
            ->first();

        $this->deleteExisting($existing);
        $this->clearLegacyMetadata($legacyJob);
    }

    private function isPublishable(LegacyJob $job): bool
    {
        if ((bool) $job->is_draft || (int) $job->is_active !== 1) {
            return false;
        }

        if (! $job->expiry_date || $job->expiry_date <= now()) {
            return false;
        }

        if ($job->display_end_date && $job->display_end_date < now()) {
            return false;
        }

        return trim((string) $job->title) !== '';
    }

    private function resolveCategory(LegacyJob $job): ?Category
    {
        $slug = $this->categorySlugFromText($this->roleText($job));

        return $slug ? Category::where('slug', $slug)->first() : null;
    }

    private function roleText(LegacyJob $job): string
    {
        $texts = [
            (string) $job->title,
            strip_tags((string) $job->description),
        ];

        $legacyCategoryId = (int) $job->functional_area_id;
        if ($legacyCategoryId > 0) {
            $jobCategory = JobCategory::where('job_category_id', $legacyCategoryId)->first()
                ?: JobCategory::find($legacyCategoryId);

            if ($jobCategory) {
                $texts[] = (string) $jobCategory->job_category;
            }
        }

        return trim(implode(' ', array_filter($texts)));
    }

    private function categorySlugFromText(string $text): ?string
    {
        $haystack = $this->normaliseSearchText($text);

        foreach (self::CATEGORY_KEYWORDS as $slug => $keywords) {
            foreach ($keywords as $keyword) {
                $needle = $this->normaliseSearchText($keyword);
                if (preg_match('/\b' . preg_quote($needle, '/') . '\b/', $haystack)) {
                    return $slug;
                }
            }
        }

        return null;
    }

    private function resolveCity(LegacyJob $job): ?City
    {
        if ($job->medo_city_id) {
            $city = City::find($job->medo_city_id);
            if ($city) {
                return $city;
            }
        }

        $legacyCityId = (int) $job->city_id;
        if (isset(self::LEGACY_CITY_SLUGS[$legacyCityId])) {
            $city = City::where('slug', self::LEGACY_CITY_SLUGS[$legacyCityId])->first();
            if ($city) {
                return $city;
            }
        }

        $legacyCity = LegacyCity::where('city_id', $legacyCityId)->first()
            ?: LegacyCity::find($legacyCityId);
        if (! $legacyCity || ! $legacyCity->city) {
            return null;
        }

        $name = strtolower(trim((string) $legacyCity->city));
        $slug = self::CITY_ALIASES[$name] ?? Str::slug($name);

        return City::where('slug', $slug)->first();
    }

    private function resolveEmployer(LegacyJob $job, City $city): ?Employer
    {
        $company = $job->company;
        $name = trim((string) ($company ? $company->name : ''));

        if ($name === '') {
            $name = 'Healthcare Employer';
        }

        $baseSlug = $company && $company->slug
            ? Str::slug($company->slug)
            : Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = $company ? 'company-' . $company->id : 'healthcare-employer';
        }

        $employer = Employer::firstOrNew(['slug' => $baseSlug]);
        $employer->name = $name;
        $employer->type = $employer->type ?: 'agency';
        $employer->province_id = $city->province_id;
        $employer->website = $company && $company->website ? $company->website : $employer->website;
        $employer->logo_url = $company && $company->logo ? asset('company_logos/' . $company->logo) : $employer->logo_url;
        $employer->save();

        return $employer;
    }

    private function employmentType(LegacyJob $job): ?string
    {
        $label = strtolower((string) $job->getJobType('job_type'));

        if (str_contains($label, 'part')) {
            return 'part_time';
        }

        if (str_contains($label, 'casual')) {
            return 'casual';
        }

        if (str_contains($label, 'full')) {
            return 'full_time';
        }

        return null;
    }

    private function shiftType(LegacyJob $job): ?string
    {
        $label = strtolower((string) $job->getJobShift('job_shift'));

        if (str_contains($label, 'evening')) {
            return 'evenings';
        }

        if (str_contains($label, 'night')) {
            return 'nights';
        }

        if (str_contains($label, 'rotat')) {
            return 'rotating';
        }

        if (str_contains($label, 'weekend')) {
            return 'weekends';
        }

        if (str_contains($label, 'day') || str_contains($label, 'first')) {
            return 'days';
        }

        return null;
    }

    private function wagePeriod(LegacyJob $job): ?string
    {
        $label = strtolower((string) $job->getSalaryPeriod('salary_period'));

        if (str_contains($label, 'hour')) {
            return 'hourly';
        }

        if (str_contains($label, 'year') || str_contains($label, 'annual')) {
            return 'annual';
        }

        return null;
    }

    private function applyUrl(LegacyJob $job): string
    {
        $url = $job->getApplyActionUrl();

        return $url ?: route('job.detail', $job->slug);
    }

    private function moneyOrNull($value): ?float
    {
        $value = is_numeric($value) ? (float) $value : 0.0;

        return $value > 0 ? $value : null;
    }

    private function baseSlug(LegacyJob $job): string
    {
        $slug = Str::slug((string) $job->slug);

        if ($slug !== '') {
            return $slug;
        }

        $slug = Str::slug((string) $job->title);

        return $slug !== '' ? $slug . '-' . $job->id : 'legacy-job-' . $job->id;
    }

    private function uniqueSlug(string $baseSlug, int $cityId, ?int $ignoreId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $cityId, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, int $cityId, ?int $ignoreId): bool
    {
        return MedoJob::where('slug', $slug)
            ->where('city_id', $cityId)
            ->when($ignoreId, function ($query, $ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();
    }

    private function deleteExisting(?MedoJob $job): void
    {
        if (! $job) {
            return;
        }

        $this->forgetJobCache($job);
        $job->delete();
    }

    private function forgetJobCache(?MedoJob $job): void
    {
        if (! $job || ! $job->category || ! $job->province || ! $job->city) {
            return;
        }

        Cache::forget("jobs.{$job->category->slug}.{$job->province->slug}.{$job->city->slug}");
    }

    private function syncLegacyMetadata(LegacyJob $legacyJob, array $payload): void
    {
        DB::table($legacyJob->getTable())
            ->where('id', $legacyJob->id)
            ->update([
                'medo_category_id' => $payload['category_id'],
                'medo_province_id' => $payload['province_id'],
                'medo_city_id' => $payload['city_id'],
                'medo_employer_id' => $payload['employer_id'],
                'wage_min' => $payload['wage_min'],
                'wage_max' => $payload['wage_max'],
                'wage_period' => $payload['wage_period'],
                'external_id' => $payload['external_id'],
                'source' => $payload['source'],
                'apply_url' => $payload['apply_url'],
            ]);

        $legacyJob->forceFill([
            'medo_category_id' => $payload['category_id'],
            'medo_province_id' => $payload['province_id'],
            'medo_city_id' => $payload['city_id'],
            'medo_employer_id' => $payload['employer_id'],
            'wage_min' => $payload['wage_min'],
            'wage_max' => $payload['wage_max'],
            'wage_period' => $payload['wage_period'],
            'external_id' => $payload['external_id'],
            'source' => $payload['source'],
            'apply_url' => $payload['apply_url'],
        ]);
    }

    private function clearLegacyMetadata(LegacyJob $legacyJob): void
    {
        DB::table($legacyJob->getTable())
            ->where('id', $legacyJob->id)
            ->update([
                'medo_category_id' => null,
                'medo_province_id' => null,
                'medo_city_id' => null,
                'medo_employer_id' => null,
                'wage_min' => null,
                'wage_max' => null,
                'wage_period' => null,
                'external_id' => null,
                'source' => null,
                'apply_url' => null,
            ]);

        $legacyJob->forceFill([
            'medo_category_id' => null,
            'medo_province_id' => null,
            'medo_city_id' => null,
            'medo_employer_id' => null,
            'wage_min' => null,
            'wage_max' => null,
            'wage_period' => null,
            'external_id' => null,
            'source' => null,
            'apply_url' => null,
        ]);
    }

    private function externalId(LegacyJob $job): string
    {
        return 'legacy-' . $job->id;
    }

    private function normaliseSearchText(string $value): string
    {
        $value = strtolower(strip_tags($value));
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim(preg_replace('/\s+/', ' ', $value));
    }
}
