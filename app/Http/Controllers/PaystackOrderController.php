<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
use Config;
use App\Package;
use App\User;
use Carbon\Carbon;
use Cake\Chronos\Chronos;
use App\Traits\CompanyPackageTrait;
use App\Traits\JobSeekerPackageTrait;
use Unicodeveloper\Paystack\Paystack;

class PaystackOrderController extends Controller
{
    use CompanyPackageTrait;
    use JobSeekerPackageTrait;

    private $redirectTo = 'home';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::guard('company')->check()) {
                $this->redirectTo = 'company.home';
            }
            return $next($request);
        });
    }

    public function paystackOrderForm($package_id, $new_or_upgrade)
    {
        $package = Package::findOrFail($package_id);
        return view('order.pay_with_paystack')
            ->with('package', $package)
            ->with('package_id', $package_id)
            ->with('new_or_upgrade', $new_or_upgrade);
    }

    public function paystackOrderPackage(Request $request)
    {
        $package = Package::findOrFail($request->package_id);
        $order_amount = $package->package_price;
        $buyer_id = '';
        $buyer_name = '';
        if (Auth::guard('company')->check()) {
            $buyer_id = Auth::guard('company')->user()->id;
            $buyer_name = Auth::guard('company')->user()->name . '(' . Auth::guard('company')->user()->email . ')';
        }
        if (Auth::check()) {
            $buyer_id = Auth::user()->id;
            $buyer_name = Auth::user()->getName() . '(' . Auth::user()->email . ')';
        }
        $package_for = ($package->package_for == 'employer') ? __('Employer') : __('Job Seeker');
        $description = $package_for . ' ' . $buyer_name . ' - ' . $buyer_id . ' ' . __('Package') . ':' . $package->package_title;

        try {
            $paystack = new Paystack();
            $paymentDetails = [
                'amount' => $order_amount * 100, // Paystack expects amount in kobo
                'email' => Auth::user() ? Auth::user()->email : Auth::guard('company')->user()->email,
                'reference' => Paystack::genTranxRef(),
                'currency' => 'NGN',
                'callback_url' => route('paystack.order.package'),
                'metadata' => [
                    'buyer_id' => $buyer_id,
                    'buyer_name' => $buyer_name,
                    'description' => $description,
                ],
            ];
            $response = $paystack->getAuthorizationUrl($paymentDetails)->redirectNow();
        } catch (\Exception $e) {
            flash($e->getMessage());
            return Redirect::route($this->redirectTo);
        }
    }

    public function paystackOrderUpgradePackage(Request $request)
    {
        $package = Package::findOrFail($request->package_id);
        $order_amount = $package->package_price;
        $buyer_id = '';
        $buyer_name = '';
        if (Auth::guard('company')->check()) {
            $buyer_id = Auth::guard('company')->user()->id;
            $buyer_name = Auth::guard('company')->user()->name . '(' . Auth::guard('company')->user()->email . ')';
        }
        if (Auth::check()) {
            $buyer_id = Auth::user()->id;
            $buyer_name = Auth::user()->getName() . '(' . Auth::user()->email . ')';
        }
        $package_for = ($package->package_for == 'employer') ? __('Employer') : __('Job Seeker');
        $description = $package_for . ' ' . $buyer_name . ' - ' . $buyer_id . ' ' . __('Upgrade Package') . ':' . $package->package_title;

        try {
            $paystack = new Paystack();
            $paymentDetails = [
                'amount' => $order_amount * 100, // Paystack expects amount in kobo
                'email' => Auth::user() ? Auth::user()->email : Auth::guard('company')->user()->email,
                'reference' => Paystack::genTranxRef(),
                'currency' => 'NGN',
                'callback_url' => route('paystack.order.upgrade.package'),
                'metadata' => [
                    'buyer_id' => $buyer_id,
                    'buyer_name' => $buyer_name,
                    'description' => $description,
                ],
            ];
            $response = $paystack->getAuthorizationUrl($paymentDetails)->redirectNow();
        } catch (\Exception $e) {
            flash($e->getMessage());
            return Redirect::route($this->redirectTo);
        }
    }
} 