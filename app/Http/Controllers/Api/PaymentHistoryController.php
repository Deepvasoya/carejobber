<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentHistoryController extends BaseController
{
    /**
     * Get company payment history
     */
    public function getCompanyPaymentHistory(Request $request)
    {
        try {
            $company = Auth::guard('company-Api')->user();
            if (!$company) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $query = Order::where('company_id', $company->id)
                ->with(['package:id,name,price,package_type']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by package type
            if ($request->filled('package_type')) {
                $query->whereHas('package', function($q) use ($request) {
                    $q->where('package_type', $request->package_type);
                });
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
            }

            // Sort and paginate
            $perPage = $request->get('per_page', 15);
            $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform data
            $orders->getCollection()->transform(function ($order) {
                return [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'package' => [
                        'id' => $order->package->id,
                        'name' => $order->package->name,
                        'package_type' => $order->package->package_type,
                        'price' => $order->package->price,
                    ],
                    'amount' => $order->amount,
                    'currency' => $order->currency ?? 'USD',
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'transaction_id' => $order->transaction_id,
                    'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                    'expiry_date' => $order->expiry_date ? $order->expiry_date->format('Y-m-d') : null,
                    'features' => [
                        'job_posts' => $order->job_posts ?? 0,
                        'cv_views' => $order->cv_views ?? 0,
                        'featured_jobs' => $order->featured_jobs ?? 0,
                        'resume_search' => $order->resume_search ?? false,
                    ],
                ];
            });

            return $this->sendResponse($orders, 'Company payment history retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving company payment history', [], 500);
        }
    }

    /**
     * Get user payment history
     */
    public function getUserPaymentHistory(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $query = Order::where('user_id', $user->id)
                ->with(['package:id,name,price,package_type']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by package type
            if ($request->filled('package_type')) {
                $query->whereHas('package', function($q) use ($request) {
                    $q->where('package_type', $request->package_type);
                });
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
            }

            // Sort and paginate
            $perPage = $request->get('per_page', 15);
            $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Transform data
            $orders->getCollection()->transform(function ($order) {
                return [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'package' => [
                        'id' => $order->package->id,
                        'name' => $order->package->name,
                        'package_type' => $order->package->package_type,
                        'price' => $order->package->price,
                    ],
                    'amount' => $order->amount,
                    'currency' => $order->currency ?? 'USD',
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'transaction_id' => $order->transaction_id,
                    'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                    'expiry_date' => $order->expiry_date ? $order->expiry_date->format('Y-m-d') : null,
                    'features' => [
                        'profile_views' => $order->profile_views ?? 0,
                        'featured_profile' => $order->featured_profile ?? false,
                        'cv_downloads' => $order->cv_downloads ?? 0,
                    ],
                ];
            });

            return $this->sendResponse($orders, 'User payment history retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving user payment history', [], 500);
        }
    }

    /**
     * Get payment statistics for company
     */
    public function getCompanyPaymentStats()
    {
        try {
            $company = Auth::guard('company-Api')->user();
            if (!$company) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $stats = [
                'total_orders' => Order::where('company_id', $company->id)->count(),
                'total_spent' => Order::where('company_id', $company->id)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'pending_orders' => Order::where('company_id', $company->id)
                    ->where('status', 'pending')
                    ->count(),
                'completed_orders' => Order::where('company_id', $company->id)
                    ->where('status', 'completed')
                    ->count(),
                'failed_orders' => Order::where('company_id', $company->id)
                    ->where('status', 'failed')
                    ->count(),
                'monthly_spending' => Order::where('company_id', $company->id)
                    ->where('status', 'completed')
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('amount'),
                'package_distribution' => Order::where('company_id', $company->id)
                    ->where('status', 'completed')
                    ->join('packages', 'orders.package_id', '=', 'packages.id')
                    ->select('packages.package_type', DB::raw('count(*) as count'))
                    ->groupBy('packages.package_type')
                    ->get(),
            ];

            return $this->sendResponse($stats, 'Company payment statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving company payment statistics', [], 500);
        }
    }

    /**
     * Get payment statistics for user
     */
    public function getUserPaymentStats()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                'total_spent' => Order::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'pending_orders' => Order::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->count(),
                'completed_orders' => Order::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'failed_orders' => Order::where('user_id', $user->id)
                    ->where('status', 'failed')
                    ->count(),
                'monthly_spending' => Order::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('amount'),
                'package_distribution' => Order::where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->join('packages', 'orders.package_id', '=', 'packages.id')
                    ->select('packages.package_type', DB::raw('count(*) as count'))
                    ->groupBy('packages.package_type')
                    ->get(),
            ];

            return $this->sendResponse($stats, 'User payment statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving user payment statistics', [], 500);
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails($orderId)
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company-Api')->user();

            if (!$user && !$company) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $query = Order::with(['package:id,name,price,package_type,description,features']);

            if ($user) {
                $query->where('user_id', $user->id);
            }

            if ($company) {
                $query->where('company_id', $company->id);
            }

            $order = $query->where('id', $orderId)->first();

            if (!$order) {
                return $this->sendError('Order not found', [], 404);
            }

            $orderData = [
                'id' => $order->id,
                'order_id' => $order->order_id,
                'package' => [
                    'id' => $order->package->id,
                    'name' => $order->package->name,
                    'package_type' => $order->package->package_type,
                    'price' => $order->package->price,
                    'description' => $order->package->description,
                    'features' => $order->package->features,
                ],
                'amount' => $order->amount,
                'currency' => $order->currency ?? 'USD',
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'transaction_id' => $order->transaction_id,
                'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                'expiry_date' => $order->expiry_date ? $order->expiry_date->format('Y-m-d') : null,
                'billing_address' => [
                    'name' => $order->billing_name,
                    'email' => $order->billing_email,
                    'phone' => $order->billing_phone,
                    'address' => $order->billing_address,
                    'city' => $order->billing_city,
                    'state' => $order->billing_state,
                    'country' => $order->billing_country,
                    'postal_code' => $order->billing_postal_code,
                ],
                'features' => [
                    'job_posts' => $order->job_posts ?? 0,
                    'cv_views' => $order->cv_views ?? 0,
                    'featured_jobs' => $order->featured_jobs ?? 0,
                    'resume_search' => $order->resume_search ?? false,
                    'profile_views' => $order->profile_views ?? 0,
                    'featured_profile' => $order->featured_profile ?? false,
                    'cv_downloads' => $order->cv_downloads ?? 0,
                ],
            ];

            return $this->sendResponse($orderData, 'Order details retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving order details', [], 500);
        }
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(Request $request)
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company-Api')->user();

            if (!$user && !$company) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $query = Order::with(['package:id,name,package_type']);

            if ($user) {
                $query->where('user_id', $user->id);
            }

            if ($company) {
                $query->where('company_id', $company->id);
            }

            $limit = $request->get('limit', 10);
            $transactions = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            // Transform data
            $transactions->transform(function ($order) {
                return [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'package_name' => $order->package->name,
                    'package_type' => $order->package->package_type,
                    'amount' => $order->amount,
                    'currency' => $order->currency ?? 'USD',
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return $this->sendResponse($transactions, 'Recent transactions retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving recent transactions', [], 500);
        }
    }

    /**
     * Get payment methods used
     */
    public function getPaymentMethods()
    {
        try {
            $user = Auth::user();
            $company = Auth::guard('company-Api')->user();

            if (!$user && !$company) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $query = Order::select('payment_method', DB::raw('count(*) as count'));

            if ($user) {
                $query->where('user_id', $user->id);
            }

            if ($company) {
                $query->where('company_id', $company->id);
            }

            $paymentMethods = $query->groupBy('payment_method')
                ->orderBy('count', 'desc')
                ->get();

            return $this->sendResponse($paymentMethods, 'Payment methods retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving payment methods', [], 500);
        }
    }
} 