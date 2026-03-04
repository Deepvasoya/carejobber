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
use PaytmWallet;

class PaytmOrderController extends Controller
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

    public function paytmOrderForm($package_id, $new_or_upgrade)
    {
        $package = Package::findOrFail($package_id);
        $siteSetting = SiteSetting::findOrFail(1272);
        
        $payment = PaytmWallet::with('receive');
        $payment->prepare([
            'order' => uniqid(),
            'user' => Auth::guard('company')->check() ? Auth::guard('company')->user()->id : Auth::user()->id,
            'mobile_number' => Auth::guard('company')->check() ? Auth::guard('company')->user()->phone : Auth::user()->phone,
            'email' => Auth::guard('company')->check() ? Auth::guard('company')->user()->email : Auth::user()->email,
            'amount' => $package->package_price,
            'callback_url' => route('paytm.callback')
        ]);
        
        return $payment->receive();
    }

    public function paytmCallback(Request $request)
    {
        $transaction = PaytmWallet::with('receive');
        $response = $transaction->response();
        
        if($transaction->isSuccessful()){
            $package_id = Session::get('package_id');
            $package = Package::findOrFail($package_id);
            
            if (Auth::guard('company')->check()) {
                $company = Auth::guard('company')->user();
                $this->addCompanyPackage($company, $package, 'Paytm');
            } else {
                $user = Auth::user();
                $this->addJobSeekerPackage($user, $package, 'Paytm');
            }
            
            flash('Payment successful!')->success();
            return redirect()->route($this->redirectTo);
        } else if($transaction->isFailed()){
            flash('Payment failed!')->error();
            return redirect()->route($this->redirectTo);
        }
    }

    public function paytmOrderPackage(Request $request)
    {
        $package = Package::findOrFail($request->package_id);
        Session::put('package_id', $package->id);
        
        $payment = PaytmWallet::with('receive');
        $payment->prepare([
            'order' => uniqid(),
            'user' => Auth::guard('company')->check() ? Auth::guard('company')->user()->id : Auth::user()->id,
            'mobile_number' => Auth::guard('company')->check() ? Auth::guard('company')->user()->phone : Auth::user()->phone,
            'email' => Auth::guard('company')->check() ? Auth::guard('company')->user()->email : Auth::user()->email,
            'amount' => $package->package_price,
            'callback_url' => route('paytm.callback')
        ]);
        
        return $payment->receive();
    }
} 