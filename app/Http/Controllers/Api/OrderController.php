<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use App\Company;
use App\Package;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\SiteSetting;
use App\Http\Controllers\Controller;
use App\Traits\CompanyPackageTrait;
use App\Traits\JobSeekerPackageTrait;

class OrderController extends Controller
{
    use CompanyPackageTrait;
    use JobSeekerPackageTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:company');
    }

    /**
     * Order free package
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderFreePackage(Request $request, $id)
    {
        try {
            $package = Package::findOrFail($id);
            
            if ($package->package_price > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not a free package'
                ], 400);
            }

            $company = Auth::guard('company')->user();

            if ($package->package_for === 'cv_search' && ! $company->canActivateFreeCvSearchPackage()) {
                $until = $company->getFreeCvPackageNextAvailableAt();

                return response()->json([
                    'success' => false,
                    'message' => $until
                        ? 'You already activated the free CV package for this 30-day period. Try again from ' . $until->format('d M Y H:i') . ' (3 CV unlocks per activation) or purchase a paid package.'
                        : 'You cannot activate the free CV package right now. Please purchase a paid package.',
                ], 422);
            }

            if ($package->package_for === 'cv_search') {
                $activated = DB::transaction(function () use ($company, $package) {
                    $locked = Company::where('id', $company->id)->lockForUpdate()->first();
                    if (! $locked || ! $locked->canActivateFreeCvSearchPackage()) {
                        return false;
                    }
                    $this->addCompanySearchPackage($locked, $package, 'Free Package');
                    $locked->has_used_free_cv_package = 1;
                    $locked->update();

                    return true;
                });
                if ($activated === false) {
                    $until = Company::find($company->id)->getFreeCvPackageNextAvailableAt();

                    return response()->json([
                        'success' => false,
                        'message' => $until
                            ? 'You already activated the free CV package for this 30-day period. Try again from ' . $until->format('d M Y H:i') . ' (3 CV unlocks per activation) or purchase a paid package.'
                            : 'You cannot activate the free CV package right now. Please purchase a paid package.',
                    ], 422);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Free package ordered successfully',
                'data' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ordering package: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Order package
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderPackage(Request $request, $id)
    {
        try {
            $package = Package::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Package details retrieved',
                'data' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving package: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Order upgrade package
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderUpgradePackage(Request $request, $id)
    {
        try {
            $package = Package::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Upgrade package details retrieved',
                'data' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving upgrade package: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PayPal payment status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentStatus(Request $request, $id)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Payment status retrieved',
                'data' => ['payment_id' => $id]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PayPal upgrade payment status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpgradePaymentStatus(Request $request, $id)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Upgrade payment status retrieved',
                'data' => ['payment_id' => $id]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving upgrade payment status: ' . $e->getMessage()
            ], 500);
        }
    }
}
