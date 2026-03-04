<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Referral;
use App\Company;
use App\SiteSetting;
use DataTables;

class ReferralController extends Controller
{
    /**
     * Display referral list
     */
    public function index()
    {
        $siteSetting = SiteSetting::findOrFail(1272);
        
        // Get statistics
        $totalReferrals = Referral::count();
        $pendingReferrals = Referral::where('status', 'pending')->count();
        $registeredReferrals = Referral::where('status', 'registered')->count();
        $rewardedReferrals = Referral::where('status', 'rewarded')->count();
        
        // Get total bonus jobs given
        $totalBonusJobsGiven = Company::sum('referral_bonus_jobs');
        
        // Get reward history - companies who have earned rewards
        $rewardHistory = [];
        $companiesWithReferrals = Company::whereHas('referralsMade')->get();
        foreach ($companiesWithReferrals as $company) {
            $totalRefs = $company->referralsMade()->count();
            $successfulRefs = $company->referralsMade()->whereIn('status', ['registered', 'rewarded'])->count();
            $bonusEarned = $company->referralsMade()->where('status', 'rewarded')->sum('reward_jobs_given');
            
            if ($bonusEarned > 0 || $successfulRefs > 0) {
                $rewardHistory[] = [
                    'company_name' => $company->name,
                    'total_referrals' => $totalRefs,
                    'successful_referrals' => $successfulRefs,
                    'bonus_jobs_earned' => $bonusEarned,
                    'current_balance' => $company->referral_bonus_jobs ?? 0
                ];
            }
        }
        
        return view('admin.referral.index', compact(
            'siteSetting',
            'totalReferrals',
            'pendingReferrals',
            'registeredReferrals',
            'rewardedReferrals',
            'totalBonusJobsGiven',
            'rewardHistory'
        ));
    }

    /**
     * Fetch referrals for DataTable
     */
    public function fetchReferrals(Request $request)
    {
        $referrals = Referral::with(['referrerCompany', 'referredCompany'])->select('referrals.*');

        return DataTables::of($referrals)
            ->addColumn('referrer', function ($referral) {
                return $referral->referrerCompany ? $referral->referrerCompany->name : '-';
            })
            ->addColumn('referred', function ($referral) {
                if ($referral->referredCompany) {
                    return $referral->referredCompany->name;
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
     * Show referral settings page
     */
    public function settings()
    {
        $siteSetting = SiteSetting::findOrFail(1272);
        return view('admin.referral.settings', compact('siteSetting'));
    }

    /**
     * Update referral settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'referral_required_count' => 'required|integer|min:1',
            'referral_reward_jobs' => 'required|integer|min:1'
        ]);

        $siteSetting = SiteSetting::findOrFail(1272);
        $siteSetting->referral_required_count = $request->input('referral_required_count');
        $siteSetting->referral_reward_jobs = $request->input('referral_reward_jobs');
        $siteSetting->save();

        flash(__('Referral settings updated successfully!'))->success();
        return redirect()->route('admin.list.referrals');
    }

    /**
     * Delete a referral
     */
    public function delete(Request $request)
    {
        $referral = Referral::findOrFail($request->input('id'));
        $referral->delete();

        return response()->json([
            'success' => true,
            'message' => __('Referral deleted successfully!')
        ]);
    }
}
