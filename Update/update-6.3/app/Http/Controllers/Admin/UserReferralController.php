<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserReferral;
use App\User;
use App\SiteSetting;
use DataTables;

class UserReferralController extends Controller
{
    /**
     * Display user referral list
     */
    public function index()
    {
        $siteSetting = SiteSetting::findOrFail(1272);
        
        // Get statistics
        $totalReferrals = UserReferral::count();
        $pendingReferrals = UserReferral::where('status', 'pending')->count();
        $registeredReferrals = UserReferral::where('status', 'registered')->count();
        $rewardedReferrals = UserReferral::where('status', 'rewarded')->count();
        
        // Get total featured days given
        $totalFeaturedDaysGiven = UserReferral::where('status', 'rewarded')->sum('reward_days_given');
        
        // Get reward history - users who have earned rewards
        $rewardHistory = [];
        $usersWithReferrals = User::whereHas('referralsMade')->get();
        foreach ($usersWithReferrals as $user) {
            $totalRefs = $user->referralsMade()->count();
            $successfulRefs = $user->referralsMade()->whereIn('status', ['registered', 'rewarded'])->count();
            $daysEarned = $user->referralsMade()->where('status', 'rewarded')->sum('reward_days_given');
            
            if ($daysEarned > 0 || $successfulRefs > 0) {
                $rewardHistory[] = [
                    'user_name' => $user->getName(),
                    'total_referrals' => $totalRefs,
                    'successful_referrals' => $successfulRefs,
                    'featured_days_earned' => $daysEarned,
                    'featured_until' => $user->featured_until ? \Carbon\Carbon::parse($user->featured_until)->format('M d, Y') : null
                ];
            }
        }
        
        return view('admin.user_referral.index', compact(
            'siteSetting',
            'totalReferrals',
            'pendingReferrals',
            'registeredReferrals',
            'rewardedReferrals',
            'totalFeaturedDaysGiven',
            'rewardHistory'
        ));
    }

    /**
     * Fetch user referrals for DataTable
     */
    public function fetchUserReferrals(Request $request)
    {
        $referrals = UserReferral::with(['referrerUser', 'referredUser'])->select('user_referrals.*');

        return DataTables::of($referrals)
            ->addColumn('referrer', function ($referral) {
                return $referral->referrerUser ? $referral->referrerUser->getName() : '-';
            })
            ->addColumn('referred', function ($referral) {
                if ($referral->referredUser) {
                    return $referral->referredUser->getName();
                }
                return $referral->referred_email ?? '-';
            })
            ->addColumn('status_badge', function ($referral) {
                return $referral->getStatusBadge();
            })
            ->addColumn('date', function ($referral) {
                return $referral->created_at ? $referral->created_at->format('M d, Y H:i') : '-';
            })
            ->addColumn('action', function ($referral) {
                return '<button class="btn btn-danger btn-sm delete-referral" data-id="' . $referral->id . '"><i class="ri ri-delete-bin-line"></i></button>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Update user referral settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'user_referral_required_count' => 'required|integer|min:1',
            'user_referral_reward_days' => 'required|integer|min:1'
        ]);

        $siteSetting = SiteSetting::findOrFail(1272);
        $siteSetting->user_referral_required_count = $request->input('user_referral_required_count');
        $siteSetting->user_referral_reward_days = $request->input('user_referral_reward_days');
        $siteSetting->save();

        flash(__('User referral settings updated successfully!'))->success();
        return redirect()->route('admin.list.user.referrals');
    }

    /**
     * Delete a user referral
     */
    public function delete(Request $request)
    {
        $referral = UserReferral::findOrFail($request->input('id'));
        $referral->delete();

        return response()->json([
            'success' => true,
            'message' => __('Referral deleted successfully!')
        ]);
    }
}
