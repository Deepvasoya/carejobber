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
use App\SiteSetting;
use Cake\Chronos\Chronos;
use App\Traits\CompanyPackageTrait;
use App\Traits\JobSeekerPackageTrait;
use Razorpay\Api\Api;

class RazorpayOrderController extends Controller
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

    public function razorpayOrderForm($package_id, $new_or_upgrade)
    {
        $package = Package::findOrFail($package_id);
        $siteSetting = SiteSetting::findOrFail(1272);
        
        $api = new Api($siteSetting->razorpay_key, $siteSetting->razorpay_secret);
        $orderData = [
            'receipt'         => 'rcptid_11',
            'amount'          => $package->package_price * 100, // amount in paise
            'currency'        => $siteSetting->default_currency_code,
            'payment_capture' => 1
        ];
        
        $razorpayOrder = $api->order->create($orderData);
        
        return view('order.pay_with_razorpay')
            ->with('package', $package)
            ->with('package_id', $package_id)
            ->with('new_or_upgrade', $new_or_upgrade)
            ->with('razorpayOrder', $razorpayOrder);
    }

    public function razorpayOrderPackage(Request $request)
    {
        $package = Package::findOrFail($request->package_id);
        $siteSetting = SiteSetting::findOrFail(1272);
        
        $api = new Api($siteSetting->razorpay_key, $siteSetting->razorpay_secret);
        
        try {
            $attributes = array(
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            );
            
            $api->utility->verifyPaymentSignature($attributes);
            
            if (Auth::guard('company')->check()) {
                $company = Auth::guard('company')->user();
                $this->addCompanyPackage($company, $package, 'Razorpay');
            } else {
                $user = Auth::user();
                $this->addJobSeekerPackage($user, $package, 'Razorpay');
            }
            
            flash('Payment successful!')->success();
            return redirect()->route($this->redirectTo);
        } catch (\Exception $e) {
            flash('Payment failed: ' . $e->getMessage())->error();
            return redirect()->route($this->redirectTo);
        }
    }

    public function verifyRazorpayPayment(Request $request)
    {
        $siteSetting = SiteSetting::findOrFail(1272);
        $api = new Api($siteSetting->razorpay_key, $siteSetting->razorpay_secret);
        
        try {
            $attributes = array(
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            );
            
            $api->utility->verifyPaymentSignature($attributes);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
