<?php

namespace App\Services;

use Razorpay\Api\Api;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function createOrder($amount, $currency = 'INR', $receipt = null)
    {
        return $this->api->order->create([
            'receipt' => $receipt ?? uniqid(),
            'amount' => $amount * 100, // Amount in paise
            'currency' => $currency,
        ]);
    }

    public function verifySignature($attributes)
    {
        try {
            $this->api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
