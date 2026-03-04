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
use App\Company;
use Carbon\Carbon;
use App\SiteSetting;
use Cake\Chronos\Chronos;
use App\Traits\CompanyPackageTrait;
use App\Traits\JobSeekerPackageTrait;
use Iyzipay\Options;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Request\RetrieveCheckoutFormRequest;
use Iyzipay\Model\CheckoutFormInitialize;
use Iyzipay\Model\CheckoutForm;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\Buyer;

class IyzicoOrderController extends Controller
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

    public function iyzicoOrderForm(Request $request, $package_id, $new_or_upgrade)
    {
        $package = Package::findOrFail($package_id);
        $siteSetting = SiteSetting::findOrFail(1272);
        
        // Check if Iyzico is configured
        if (empty($siteSetting->iyzico_api_key) || empty($siteSetting->iyzico_secret_key)) {
            flash('Iyzico payment gateway is not configured. Please add API Key and Secret Key in admin settings.')->error();
            return Redirect::route($this->redirectTo);
        }
        
        // Configure Iyzico Options
        $options = new Options();
        $options->setApiKey(trim($siteSetting->iyzico_api_key));
        $options->setSecretKey(trim($siteSetting->iyzico_secret_key));
        $baseUrl = ($siteSetting->iyzico_live_sandbox ?? 'sandbox') == 'live' 
            ? 'https://api.iyzipay.com' 
            : 'https://sandbox-api.iyzipay.com';
        $options->setBaseUrl($baseUrl);
        
        // Get user information
        $user = Auth::guard('company')->check() ? Auth::guard('company')->user() : Auth::user();
        $email = $user->email;
        $name = Auth::guard('company')->check() ? $user->name : $user->getName();
        $phone = Auth::guard('company')->check() ? ($user->phone ?? '5555555555') : ($user->phone ?? '5555555555');
        
        // Clean phone number (remove spaces, dashes, etc.)
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        // Remove +90 if present
        $phone = preg_replace('/^\+90/', '', $phone);
        // Remove leading 0 if present (Turkish phone format)
        if (substr($phone, 0, 1) == '0') {
            $phone = substr($phone, 1);
        }
        // Ensure exactly 10 digits for Turkish phone numbers
        if (empty($phone) || strlen($phone) < 10) {
            $phone = '5555555555';
        } else {
            $phone = substr($phone, 0, 10); // Take only first 10 digits
        }
        // Format as +90XXXXXXXXXX for Iyzico
        $phone = '+90' . $phone;
        
        // Generate unique conversation ID
        $conversationId = 'PKG_' . $package_id . '_' . time() . '_' . $user->id;
        
        // Format price correctly
        $price = number_format((float)$package->package_price, 2, '.', '');
        
        // Map currency code - Iyzico uses TRY for Turkish Lira, not TL
        $currencyCode = $siteSetting->default_currency_code ?? 'TRY';
        if (strtoupper($currencyCode) == 'TL') {
            $currencyCode = 'TRY';
        }
        
        // Get callback URL - ensure it's absolute
        $callbackUrl = route('iyzico.callback');
        if (!filter_var($callbackUrl, FILTER_VALIDATE_URL)) {
            $callbackUrl = url($callbackUrl);
        }
        
        // Create checkout form initialize request using official SDK
        $checkoutFormRequest = new CreateCheckoutFormInitializeRequest();
        $checkoutFormRequest->setLocale('tr');
        $checkoutFormRequest->setConversationId($conversationId);
        $checkoutFormRequest->setPrice($price);
        $checkoutFormRequest->setPaidPrice($price);
        $checkoutFormRequest->setCurrency(strtoupper($currencyCode));
        $checkoutFormRequest->setBasketId('BASKET_' . $package_id . '_' . time());
        $checkoutFormRequest->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $checkoutFormRequest->setCallbackUrl($callbackUrl);
        
        // Create buyer
        $buyer = new Buyer();
        $buyer->setId((string)$user->id);
        $buyer->setName(mb_substr($name, 0, 50));
        $buyer->setSurname(mb_substr($name, 0, 50));
        $buyer->setGsmNumber($phone);
        $buyer->setEmail($email);
        $buyer->setIdentityNumber('11111111111');
        $buyer->setLastLoginDate(date('Y-m-d H:i:s'));
        $buyer->setRegistrationDate($user->created_at->format('Y-m-d H:i:s'));
        $buyer->setRegistrationAddress('Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1');
        $buyer->setIp($request->ip());
        $buyer->setCity('Istanbul');
        $buyer->setCountry('TR');
        $buyer->setZipCode('34732');
        $checkoutFormRequest->setBuyer($buyer);
        
        // Create shipping address
        $shippingAddress = new Address();
        $shippingAddress->setContactName(mb_substr($name, 0, 50));
        $shippingAddress->setCity('Istanbul');
        $shippingAddress->setCountry('TR');
        $shippingAddress->setAddress('Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1');
        $shippingAddress->setZipCode('34732');
        $checkoutFormRequest->setShippingAddress($shippingAddress);
        
        // Create billing address
        $billingAddress = new Address();
        $billingAddress->setContactName(mb_substr($name, 0, 50));
        $billingAddress->setCity('Istanbul');
        $billingAddress->setCountry('TR');
        $billingAddress->setAddress('Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1');
        $billingAddress->setZipCode('34732');
        $checkoutFormRequest->setBillingAddress($billingAddress);
        
        // Create basket items
        $basketItems = [];
        $firstBasketItem = new BasketItem();
        $firstBasketItem->setId((string)$package->id);
        $firstBasketItem->setName(mb_substr($package->package_title, 0, 100));
        $firstBasketItem->setCategory1('Package');
        $firstBasketItem->setCategory2('Subscription');
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $firstBasketItem->setPrice($price);
        $basketItems[] = $firstBasketItem;
        $checkoutFormRequest->setBasketItems($basketItems);
        
        // Initialize checkout form
        $checkoutFormInitialize = CheckoutFormInitialize::create($checkoutFormRequest, $options);
        
        // Log response for debugging
        \Log::info('Iyzico Payment Response', [
            'status' => $checkoutFormInitialize->getStatus(),
            'errorMessage' => $checkoutFormInitialize->getErrorMessage(),
            'errorCode' => $checkoutFormInitialize->getErrorCode()
        ]);
        
        if ($checkoutFormInitialize->getStatus() == 'success') {
            // Store package info in session for callback
            Session::put('iyzico_package_id', $package_id);
            Session::put('iyzico_new_or_upgrade', $new_or_upgrade);
            Session::put('iyzico_conversation_id', $conversationId);
            
            return view('order.pay_with_iyzico')
                ->with('package', $package)
                ->with('package_id', $package_id)
                ->with('new_or_upgrade', $new_or_upgrade)
                ->with('siteSetting', $siteSetting)
                ->with('checkoutFormContent', $checkoutFormInitialize->getCheckoutFormContent())
                ->with('paymentPageUrl', $checkoutFormInitialize->getPaymentPageUrl());
        } else {
            $errorMessage = $checkoutFormInitialize->getErrorMessage() ?? 'Payment initialization failed';
            $errorCode = $checkoutFormInitialize->getErrorCode() ?? '';
            $fullError = $errorCode ? "Error Code: {$errorCode} - {$errorMessage}" : $errorMessage;
            flash('Payment initialization failed: ' . $fullError)->error();
            return Redirect::route($this->redirectTo);
        }
    }

    public function iyzicoOrderPackage(Request $request)
    {
        // This method is called when form is submitted, but we handle it via callback
        return $this->iyzicoOrderForm($request->package_id, 'new');
    }

    public function iyzicoOrderUpgradePackage(Request $request)
    {
        // This method is called when form is submitted, but we handle it via callback
        return $this->iyzicoOrderForm($request->package_id, 'upgrade');
    }
    
    /**
     * Handle Iyzico payment callback
     */
    public function iyzicoCallback(Request $request)
    {
        $siteSetting = SiteSetting::findOrFail(1272);
        $token = $request->input('token');
        
        if (empty($token)) {
            flash('Payment token is missing.')->error();
            return Redirect::route($this->redirectTo);
        }
        
        // Configure Iyzico Options
        $options = new Options();
        $options->setApiKey(trim($siteSetting->iyzico_api_key));
        $options->setSecretKey(trim($siteSetting->iyzico_secret_key));
        $baseUrl = ($siteSetting->iyzico_live_sandbox ?? 'sandbox') == 'live' 
            ? 'https://api.iyzipay.com' 
            : 'https://sandbox-api.iyzipay.com';
        $options->setBaseUrl($baseUrl);
        
        // Retrieve payment details using official SDK
        $checkoutFormRequest = new RetrieveCheckoutFormRequest();
        $checkoutFormRequest->setToken($token);
        
        $checkoutForm = CheckoutForm::retrieve($checkoutFormRequest, $options);
        
        if ($checkoutForm->getStatus() == 'success' && 
            $checkoutForm->getPaymentStatus() == 'SUCCESS') {
            
            // Get package info from session
            $package_id = Session::get('iyzico_package_id');
            $new_or_upgrade = Session::get('iyzico_new_or_upgrade');
            
            if (!$package_id) {
                flash('Package information not found.')->error();
                return Redirect::route($this->redirectTo);
            }
            
            $package = Package::findOrFail($package_id);
            
            // Save package based on user type
            if (Auth::guard('company')->check()) {
                $company = Auth::guard('company')->user();
                
                if ($package->package_for == 'cv_search') {
                    if ($new_or_upgrade == 'new') {
                        $this->addCompanySearchPackage($company, $package, 'Iyzico');
                    } else {
                        $this->updateCompanySearchPackage($company, $package, 'Iyzico');
                    }
                } else {
                    if ($new_or_upgrade == 'new') {
                        $this->addCompanyPackage($company, $package, 'Iyzico');
                    } else {
                        $this->updateCompanyPackage($company, $package, 'Iyzico');
                    }
                }
            } else {
                $user = Auth::user();
                if ($new_or_upgrade == 'new') {
                    $this->addJobSeekerPackage($user, $package);
                } else {
                    $this->updateJobSeekerPackage($user, $package);
                }
            }
            
            // Clear session
            Session::forget(['iyzico_package_id', 'iyzico_new_or_upgrade', 'iyzico_conversation_id']);
            
            flash('Payment successful! Package has been activated.')->success();
            return Redirect::route($this->redirectTo);
        } else {
            $errorMessage = $checkoutForm->getErrorMessage() ?? 'Payment verification failed';
            $errorCode = $checkoutForm->getErrorCode() ?? '';
            $fullError = $errorCode ? "Error Code: {$errorCode} - {$errorMessage}" : $errorMessage;
            flash('Payment failed: ' . $fullError)->error();
            return Redirect::route($this->redirectTo);
        }
    }
}

