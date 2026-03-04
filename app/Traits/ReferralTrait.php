<?php

namespace App\Traits;

use Auth;
use App\Company;
use App\Referral;
use App\SiteSetting;
use Illuminate\Http\Request;

trait ReferralTrait
{
    /**
     * Show referral program page
     */
    public function referralProgram()
    {
        $company = Auth::guard('company')->user();
        $siteSetting = SiteSetting::findOrFail(1272);
        
        // Generate referral code if not exists
        $company->generateReferralCode();
        
        // Get referral statistics
        $totalReferrals = $company->referralsMade()->count();
        $successfulReferrals = $company->getSuccessfulReferralsCount();
        $pendingReferrals = $company->getPendingReferralsCount();
        $bonusJobs = $company->getTotalBonusJobs();
        
        // Get referral settings
        $requiredCount = $siteSetting->referral_required_count ?? 3;
        $rewardJobs = $siteSetting->referral_reward_jobs ?? 1;
        
        // Calculate progress to next reward
        $progressToReward = $successfulReferrals % $requiredCount;
        $referralsNeeded = $requiredCount - $progressToReward;
        
        return view('company.referral_program', compact(
            'company',
            'totalReferrals',
            'successfulReferrals',
            'pendingReferrals',
            'bonusJobs',
            'requiredCount',
            'rewardJobs',
            'progressToReward',
            'referralsNeeded'
        ));
    }

    /**
     * Get referral history data for DataTable
     */
    public function fetchReferralHistory(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        $referrals = Referral::where('referrer_company_id', $company->id)
            ->with('referredCompany')
            ->select('referrals.*');
        
        return \DataTables::of($referrals)
            ->addColumn('referred_info', function ($referral) {
                if ($referral->referredCompany) {
                    return $referral->referredCompany->name;
                }
                return $referral->referred_email ?? '-';
            })
            ->addColumn('status_badge', function ($referral) {
                return $referral->getStatusBadge();
            })
            ->addColumn('date', function ($referral) {
                return $referral->created_at ? $referral->created_at->format('M d, Y') : '-';
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    /**
     * Send referral invitation
     */
    public function sendReferralInvite(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $company = Auth::guard('company')->user();
        $email = $request->input('email');

        // Check if email already registered as company
        $existingCompany = Company::where('email', $email)->first();
        if ($existingCompany) {
            return response()->json([
                'success' => false,
                'message' => __('This email is already registered as a company.')
            ]);
        }

        // Check if already invited by this company
        $existingReferral = Referral::where('referrer_company_id', $company->id)
            ->where('referred_email', $email)
            ->first();
        
        if ($existingReferral) {
            return response()->json([
                'success' => false,
                'message' => __('You have already sent an invitation to this email.')
            ]);
        }

        // Generate referral code
        $company->generateReferralCode();

        // Create referral record
        $referral = new Referral();
        $referral->referrer_company_id = $company->id;
        $referral->referral_code = $company->referral_code;
        $referral->referred_email = $email;
        $referral->status = 'pending';
        $referral->save();

        // Send invitation email
        try {
            \Mail::to($email)->send(new \App\Mail\ReferralInviteMailable($company, $email));
        } catch (\Exception $e) {
            \Log::error('Failed to send referral invite: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => __('Invitation sent successfully!')
        ]);
    }

    /**
     * Process referral reward for a company
     */
    public static function processReferralReward($referrerCompanyId)
    {
        $siteSetting = SiteSetting::findOrFail(1272);
        $requiredCount = $siteSetting->referral_required_count ?? 3;
        $rewardJobs = $siteSetting->referral_reward_jobs ?? 1;

        $company = Company::find($referrerCompanyId);
        if (!$company) return;

        $successfulReferrals = $company->getSuccessfulReferralsCount();
        
        // Calculate how many rewards should have been given
        $totalRewardsEarned = floor($successfulReferrals / $requiredCount);
        
        // Get already rewarded count
        $alreadyRewarded = Referral::where('referrer_company_id', $referrerCompanyId)
            ->where('status', 'rewarded')
            ->count();
        
        $rewardsToGive = $totalRewardsEarned - floor($alreadyRewarded / $requiredCount);
        
        if ($rewardsToGive > 0) {
            // Add bonus jobs
            $company->referral_bonus_jobs = ($company->referral_bonus_jobs ?? 0) + ($rewardsToGive * $rewardJobs);
            $company->save();

            // Mark referrals as rewarded (mark the ones that completed this batch)
            $unrewardedReferrals = Referral::where('referrer_company_id', $referrerCompanyId)
                ->where('status', 'registered')
                ->orderBy('created_at', 'asc')
                ->limit($requiredCount)
                ->get();

            foreach ($unrewardedReferrals as $ref) {
                $ref->status = 'rewarded';
                $ref->reward_jobs_given = $rewardJobs;
                $ref->save();
            }
        }
    }
}
