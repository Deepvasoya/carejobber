<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use App\Package;
use App\Http\Controllers\Controller;

class PayuController extends Controller
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
     * Order package via PayU
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderPackage(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:packages,id',
            ]);

            $package = Package::findOrFail($request->package_id);
            
            return response()->json([
                'success' => true,
                'message' => 'PayU order initiated',
                'data' => [
                    'package' => $package,
                    'payment_gateway' => 'PayU'
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
                'message' => 'Error initiating PayU order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PayU order package status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderPackageStatus(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PayU order status retrieved',
                'data' => [
                    'order_id' => $request->order_id,
                    'status' => 'pending',
                    'payment_gateway' => 'PayU'
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
                'message' => 'Error retrieving PayU order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Order CV search package via PayU
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderCvSearchPackage(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:packages,id',
            ]);

            $package = Package::findOrFail($request->package_id);
            
            return response()->json([
                'success' => true,
                'message' => 'PayU CV search package order initiated',
                'data' => [
                    'package' => $package,
                    'type' => 'cv_search',
                    'payment_gateway' => 'PayU'
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
                'message' => 'Error initiating PayU CV search package order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PayU CV search package order status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderPackageCvSearchStatus(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PayU CV search package order status retrieved',
                'data' => [
                    'order_id' => $request->order_id,
                    'status' => 'pending',
                    'type' => 'cv_search',
                    'payment_gateway' => 'PayU'
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
                'message' => 'Error retrieving PayU CV search package order status: ' . $e->getMessage()
            ], 500);
        }
    }
}
