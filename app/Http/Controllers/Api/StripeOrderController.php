<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use App\Package;
use App\Http\Controllers\Controller;

class StripeOrderController extends Controller
{
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
     * Show Stripe order form
     *
     * @param Request $request
     * @param int $id
     * @param string $new_or_upgrade
     * @return \Illuminate\Http\JsonResponse
     */
    public function stripeOrderForm(Request $request, $id, $new_or_upgrade)
    {
        try {
            $package = Package::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Stripe order form data retrieved',
                'data' => [
                    'package' => $package,
                    'type' => $new_or_upgrade
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving Stripe order form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process Stripe order package
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stripeOrderPackage(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:packages,id',
                'stripe_token' => 'required',
            ]);

            // Implement Stripe payment logic here
            return response()->json([
                'success' => true,
                'message' => 'Stripe order processed successfully',
                'data' => [
                    'package_id' => $request->package_id,
                    'transaction_id' => 'stripe_' . time()
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing Stripe order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process Stripe upgrade package
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stripeOrderUpgradePackage(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:packages,id',
                'stripe_token' => 'required',
            ]);

            // Implement Stripe upgrade payment logic here
            return response()->json([
                'success' => true,
                'message' => 'Stripe upgrade processed successfully',
                'data' => [
                    'package_id' => $request->package_id,
                    'transaction_id' => 'stripe_upgrade_' . time()
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing Stripe upgrade: ' . $e->getMessage()
            ], 500);
        }
    }
}
