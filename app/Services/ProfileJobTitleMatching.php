<?php

namespace App\Services;

use App\FunctionalArea;
use App\Job;
use App\User;

/**
 * Match job postings to resume profession text (titles, search) instead of company industry.
 */
class ProfileJobTitleMatching
{
    /**
     * Phrases from a company's active, non-expired job titles (split on common separators).
     *
     * @return list<string>
     */
    public static function companyOpenJobTitlePhrases(int $companyId): array
    {
        $raw = Job::query()
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->where('expiry_date', '>', now())
            ->whereNotNull('title')
            ->orderByDesc('id')
            ->limit(40)
            ->pluck('title');

        return self::normalizeTitlePhrases($raw);
    }

    /**
     * Phrases from the job seeker's work experience titles and functional area label.
     *
     * @return list<string>
     */
    public static function jobSeekerProfessionPhrases(User $user): array
    {
        $phrases = collect();

        foreach ($user->profileExperience()->orderByDesc('date_start')->limit(20)->get() as $exp) {
            if (!empty($exp->title)) {
                foreach (preg_split('/[,;|\/]/u', (string) $exp->title) as $part) {
                    $t = trim($part);
                    if (mb_strlen($t) >= 3) {
                        $phrases->push($t);
                    }
                }
            }
        }

        if ($user->functional_area_id) {
            $fa = FunctionalArea::where('functional_area_id', $user->functional_area_id)->lang()->first()
                ?? FunctionalArea::where('functional_area_id', $user->functional_area_id)->first();
            if ($fa && !empty($fa->functional_area)) {
                $t = trim((string) $fa->functional_area);
                if (mb_strlen($t) >= 3) {
                    $phrases->push($t);
                }
            }
        }

        return $phrases->unique(fn ($p) => mb_strtolower($p))->values()->take(25)->all();
    }

    /**
     * @param  iterable<string>  $titles
     * @return list<string>
     */
    public static function normalizeTitlePhrases(iterable $titles): array
    {
        $phrases = collect();
        foreach ($titles as $title) {
            foreach (preg_split('/[,;|\/]/u', (string) $title) as $part) {
                $t = trim($part);
                if (mb_strlen($t) >= 3) {
                    $phrases->push($t);
                }
            }
        }

        return $phrases->unique(fn ($p) => mb_strtolower($p))->values()->take(25)->all();
    }
}
