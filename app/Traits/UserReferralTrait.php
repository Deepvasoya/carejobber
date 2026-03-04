<?php

namespace App\Traits;

use Auth;
use App\User;
use App\UserReferral;
use App\SiteSetting;
use Illuminate\Http\Request;

trait UserReferralTrait
{
    /**
     * Show referral program page for job seekers
     */
    public function userReferralProgram()
    {
        $user = Auth::user();
        $siteSetting = SiteSetting::findOrFail(1272);
        
        // Generate referral code if not exists
        $user->generateReferralCode();
        
        // Get referral statistics
        $totalReferrals = $user->referralsMade()->count();
        $successfulReferrals = $user->getSuccessfulReferralsCount();
        $pendingReferrals = $user->getPendingReferralsCount();
        $featuredDays = $user->getTotalFeaturedDays();
        $remainingFeaturedDays = $user->getRemainingFeaturedDays();
        $isProfileFeatured = $user->isProfileFeatured();
        
        // Get referral settings
        $requiredCount = $siteSetting->user_referral_required_count ?? 3;
        $rewardDays = $siteSetting->user_referral_reward_days ?? 7;
        
        // Calculate progress to next reward
        $progressToReward = $successfulReferrals % $requiredCount;
        $referralsNeeded = $requiredCount - $progressToReward;
        
        return view('user.referral_program', compact(
            'user',
            'totalReferrals',
            'successfulReferrals',
            'pendingReferrals',
            'featuredDays',
            'remainingFeaturedDays',
            'isProfileFeatured',
            'requiredCount',
            'rewardDays',
            'progressToReward',
            'referralsNeeded'
        ));
    }

    /**
     * Get referral history data for DataTable
     */
    public function fetchUserReferralHistory(Request $request)
    {
        $user = Auth::user();
        
        $referrals = UserReferral::where('referrer_user_id', $user->id)
            ->with('referredUser')
            ->select('user_referrals.*');
        
        return \DataTables::of($referrals)
            ->addColumn('referred_info', function ($referral) {
                if ($referral->referredUser) {
                    return $referral->referredUser->getName();
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
    public function sendUserReferralInvite(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = Auth::user();
        $email = $request->input('email');

        // Check if email already registered as user
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => __('This email is already registered.')
            ]);
        }

        // Check if already invited by this user
        $existingReferral = UserReferral::where('referrer_user_id', $user->id)
            ->where('referred_email', $email)
            ->first();
        
        if ($existingReferral) {
            return response()->json([
                'success' => false,
                'message' => __('You have already sent an invitation to this email.')
            ]);
        }

        // Generate referral code
        $user->generateReferralCode();

        // Create referral record
        $referral = new UserReferral();
        $referral->referrer_user_id = $user->id;
        $referral->referral_code = $user->referral_code;
        $referral->referred_email = $email;
        $referral->status = 'pending';
        $referral->save();

        // Send invitation email
        try {
            \Mail::to($email)->send(new \App\Mail\UserReferralInviteMailable($user, $email));
        } catch (\Exception $e) {
            \Log::error('Failed to send user referral invite: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => __('Invitation sent successfully!')
        ]);
    }

    /**
     * Activate featured profile
     */
    public function activateUserFeatured(Request $request)
    {
        $user = Auth::user();
        $days = $request->input('days', $user->referral_featured_days);
        
        if ($days > $user->referral_featured_days) {
            return response()->json([
                'success' => false,
                'message' => __('You do not have enough featured days.')
            ]);
        }
        
        if ($user->activateFeaturedProfile($days)) {
            return response()->json([
                'success' => true,
                'message' => __('Your profile is now featured for :days days!', ['days' => $days])
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => __('Unable to activate featured profile.')
        ]);
    }

    /**
     * Process referral reward for a user
     */
    public static function processUserReferralReward($referrerUserId)
    {
        $siteSetting = SiteSetting::findOrFail(1272);
        $requiredCount = $siteSetting->user_referral_required_count ?? 3;
        $rewardDays = $siteSetting->user_referral_reward_days ?? 7;

        $user = User::find($referrerUserId);
        if (!$user) return;

        $successfulReferrals = $user->getSuccessfulReferralsCount();
        
        // Calculate how many rewards should have been given
        $totalRewardsEarned = floor($successfulReferrals / $requiredCount);
        
        // Get already rewarded count
        $alreadyRewarded = UserReferral::where('referrer_user_id', $referrerUserId)
            ->where('status', 'rewarded')
            ->count();
        
        $rewardsToGive = $totalRewardsEarned - floor($alreadyRewarded / $requiredCount);
        
        if ($rewardsToGive > 0) {
            // Add featured days
            $user->referral_featured_days = ($user->referral_featured_days ?? 0) + ($rewardsToGive * $rewardDays);
            $user->save();

            // Mark referrals as rewarded
            $unrewardedReferrals = UserReferral::where('referrer_user_id', $referrerUserId)
                ->where('status', 'registered')
                ->orderBy('created_at', 'asc')
                ->limit($requiredCount)
                ->get();

            foreach ($unrewardedReferrals as $ref) {
                $ref->status = 'rewarded';
                $ref->reward_days_given = $rewardDays;
                $ref->save();
            }
        }
    }
}
