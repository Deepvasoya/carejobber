<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResumePromotionPackage;
use Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class ResumePromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showPackages()
    {
        $user = Auth::user();
        $packages = ResumePromotionPackage::active()->orderBy('duration_days')->get();
        
        // Check if user has active promotion
        $hasActivePromotion = $user->is_resume_promoted && 
                             $user->promotion_end_date && 
                             \Carbon\Carbon::parse($user->promotion_end_date)->isFuture();
        
        return view('user.resume_promotion_packages')
            ->with('packages', $packages)
            ->with('user', $user)
            ->with('hasActivePromotion', $hasActivePromotion);
    }

    public function createCheckout(Request $request, $packageId)
    {
        $package = ResumePromotionPackage::findOrFail($packageId);
        $user = Auth::user();

        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        
        if (empty($stripeSecret)) {
            flash(__('Stripe is not configured. Please contact administrator.'))->error();
            return redirect()->route('resume.promotion.packages');
        }

        Stripe::setApiKey($stripeSecret);

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($package->currency),
                        'product_data' => [
                            'name' => $package->name,
                            'description' => $package->description,
                        ],
                        'unit_amount' => $package->price * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('resume.promotion.success') . '?session_id={CHECKOUT_SESSION_ID}&package_id=' . $packageId,
                'cancel_url' => route('resume.promotion.packages'),
                'client_reference_id' => $user->id,
                'metadata' => [
                    'user_id' => $user->id,
                    'package_id' => $packageId,
                    'type' => 'resume_promotion',
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            flash(__('Error creating checkout session: ') . $e->getMessage())->error();
            return redirect()->route('resume.promotion.packages');
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $packageId = $request->query('package_id');

        if (!$sessionId || !$packageId) {
            flash(__('Invalid payment session'))->error();
            return redirect()->route('my.profile');
        }

        $stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        Stripe::setApiKey($stripeSecret);

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $package = ResumePromotionPackage::findOrFail($packageId);
                $user = Auth::user();

                // Activate resume promotion
                $user->is_resume_promoted = 1;
                $user->promotion_start_date = now();
                $user->promotion_end_date = now()->addDays($package->duration_days);
                $user->save();

                flash(__('Your resume has been promoted successfully!'))->success();
                return redirect()->route('my.profile');
            } else {
                flash(__('Payment was not successful'))->error();
                return redirect()->route('resume.promotion.packages');
            }
        } catch (\Exception $e) {
            flash(__('Error verifying payment: ') . $e->getMessage())->error();
            return redirect()->route('resume.promotion.packages');
        }
    }
}
