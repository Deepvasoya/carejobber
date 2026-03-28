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
use App\PackageCoupon;
use App\Services\PackageCouponService;
use App\User;
use Carbon\Carbon;
use Cake\Chronos\Chronos;
use App\Traits\CompanyPackageTrait;
use App\Traits\JobSeekerPackageTrait;
/** All Stripe Details class * */
use Stripe\Stripe;
use Stripe\Charge;

class StripeOrderController extends Controller
{

    use CompanyPackageTrait;
    use JobSeekerPackageTrait;

    private $redirectTo = 'home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*         * ****************************************** */
        $this->middleware(function ($request, $next) {
            if (Auth::guard('company')->check()) {
                $this->redirectTo = 'company.home';
            }
            return $next($request);
        });
        /*         * ****************************************** */
    }

    public function stripeOrderForm($package_id, $new_or_upgrade)
    {
        $package = Package::findOrFail($package_id);
        $companyId = Auth::guard('company')->check() ? Auth::guard('company')->user()->id : null;
        $userId = Auth::check() ? Auth::user()->id : null;
        $rawCoupon = session('jobseeker_package_coupon_code');
        $couponEval = app(PackageCouponService::class)->evaluateCheckout($rawCoupon, $package, $companyId, $userId);
        $couponWarning = null;
        if ($rawCoupon && PackageCoupon::normalizeCode((string) $rawCoupon) !== '' && !$couponEval['ok']) {
            $couponWarning = PackageCouponService::humanMessage($couponEval['reason'] ?? 'default');
        }

        return view('order.pay_with_stripe')
                        ->with('package', $package)
                        ->with('package_id', $package_id)
                        ->with('new_or_upgrade', $new_or_upgrade)
                        ->with('couponEval', $couponEval)
                        ->with('storedCouponCode', $rawCoupon)
                        ->with('couponWarning', $couponWarning);
    }

    /**
     * Store a details of payment with paypal.
     *
     * @param IlluminateHttpRequest $request
     * @return IlluminateHttpResponse
     */
    public function stripeOrderPackage(Request $request)
    {
        $package = Package::findOrFail($request->package_id);

        $companyId = Auth::guard('company')->check() ? Auth::guard('company')->user()->id : null;
        $userId = Auth::check() ? Auth::user()->id : null;
        $svc = app(PackageCouponService::class);
        $eval = $svc->evaluateCheckout(session('jobseeker_package_coupon_code'), $package, $companyId, $userId);

        if (PackageCoupon::normalizeCode((string) session('jobseeker_package_coupon_code')) !== '' && !$eval['ok']) {
            flash(PackageCouponService::humanMessage($eval['reason'] ?? 'default'))->error();

            return Redirect::route('stripe.order.form', [$request->package_id, 'new']);
        }

        $order_amount = $eval['ok'] ? $eval['total'] : $package->package_price;

        /*         * ************************ */
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
        /*         * ************************ */
        Stripe::setApiKey(Config::get('stripe.stripe_secret'));
        try {
            $charge = Charge::create(array(
                        "amount" => round($order_amount * 100),
                        "currency" => "USD",
                        "source" => $request->input('stripeToken'), // obtained with Stripe.js
                        "description" => $description
            ));
            if ($charge['status'] == 'succeeded') {
                /**
                 * Write Here Your Database insert logic.
                 */
                if (Auth::guard('company')->check()) {
                    $company = Auth::guard('company')->user();
                    if($package->package_for=='cv_search'){
                        $this->addCompanySearchPackage($company, $package,'Stripe');
                    }else{
                        $this->addCompanyPackage($company, $package,'Stripe');
                    }
                }
                if (Auth::check()) {
                    $user = Auth::user();
                    $this->addJobSeekerPackage($user, $package, 'Stripe');
                }

                if (!empty($eval['coupon']) && ($eval['discount'] ?? 0) > 0) {
                    $svc->recordRedemption(
                        $eval['coupon'],
                        $package,
                        (float) $eval['discount'],
                        $companyId,
                        $userId,
                        null,
                        $charge['id'] ?? null,
                        $order_amount,
                        'USD'
                    );
                }

                flash(__('You have successfully subscribed to selected package'))->success();
                return Redirect::route($this->redirectTo);
            } else {
                flash(__('Package subscription failed'));
                return Redirect::route($this->redirectTo);
            }
        } catch (\Exception $e) {
            flash($e->getMessage());
            return Redirect::route($this->redirectTo);
        }
    }

    public function StripeOrderUpgradePackage(Request $request)
    {

        $package = Package::findOrFail($request->package_id);

        $companyId = Auth::guard('company')->check() ? Auth::guard('company')->user()->id : null;
        $userId = Auth::check() ? Auth::user()->id : null;
        $svc = app(PackageCouponService::class);
        $eval = $svc->evaluateCheckout(session('jobseeker_package_coupon_code'), $package, $companyId, $userId);

        if (PackageCoupon::normalizeCode((string) session('jobseeker_package_coupon_code')) !== '' && !$eval['ok']) {
            flash(PackageCouponService::humanMessage($eval['reason'] ?? 'default'))->error();

            return Redirect::route('stripe.order.form', [$request->package_id, 'upgrade']);
        }

        $order_amount = $eval['ok'] ? $eval['total'] : $package->package_price;

        /*         * ************************ */
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
        /*         * ************************* */

        $package_for = ($package->package_for == 'employer') ? __('Employer') : __('Job Seeker');
        $description = $package_for . ' ' . $buyer_name . ' - ' . $buyer_id . ' ' . __('Upgrade Package') . ':' . $package->package_title;
        /*         * ************************ */
        Stripe::setApiKey(Config::get('stripe.stripe_secret'));
        try {
            $charge = Charge::create(array(
                        "amount" => round($order_amount * 100),
                        "currency" => "USD",
                        "source" => $request->input('stripeToken'), // obtained with Stripe.js
                        "description" => $description
            ));
            if ($charge['status'] == 'succeeded') {
                /**
                 * Write Here Your Database insert logic.
                 */
                if (Auth::guard('company')->check()) {
                    $company = Auth::guard('company')->user();
                    if($package->package_for=='cv_search'){
                        $this->updateCompanySearchPackage($company, $package);
                    }else{
                        $this->updateCompanyPackage($company, $package,'Stripe');
                    }
                }
                if (Auth::check()) {
                    $user = Auth::user();
                    $this->updateJobSeekerPackage($user, $package,'Stripe');
                }

                if (!empty($eval['coupon']) && ($eval['discount'] ?? 0) > 0) {
                    $svc->recordRedemption(
                        $eval['coupon'],
                        $package,
                        (float) $eval['discount'],
                        $companyId,
                        $userId,
                        null,
                        $charge['id'] ?? null,
                        $order_amount,
                        'USD'
                    );
                }

                flash(__('You have successfully subscribed to selected package'))->success();
                return Redirect::route($this->redirectTo);
            } else {
                flash(__('Package subscription failed'));
                return Redirect::route($this->redirectTo);
            }
        } catch (\Exception $e) {
            flash($e->getMessage());
            return Redirect::route($this->redirectTo);
        }
    }

}
