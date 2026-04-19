<?php

namespace App\Services;

use App\Company;

class VerificationBadgeService
{
    /**
     * Award verification badge to a company.
     * Sets verified flag to true and records verification timestamp.
     *
     * @param Company $company
     * @return void
     */
    public function awardBadge(Company $company): void
    {
        $company->update([
            'verified' => true,
            'verified_at' => now()
        ]);
    }

    /**
     * Revoke verification badge from a company.
     * Sets verified flag to false and clears verification timestamp.
     *
     * @param Company $company
     * @return void
     */
    public function revokeBadge(Company $company): void
    {
        $company->update([
            'verified' => false,
            'verified_at' => null
        ]);
    }

    /**
     * Check if a company can display the verified badge.
     * Returns true if the company has verified status.
     *
     * @param Company $company
     * @return bool
     */
    public function canDisplayBadge(Company $company): bool
    {
        return $company->isVerified();
    }
}
