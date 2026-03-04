<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IyzicoHelper
{
    private $apiKey;
    private $secretKey;
    private $baseUrl;
    
    public function __construct($apiKey, $secretKey, $liveSandbox = 'sandbox')
    {
        // Trim whitespace from API keys
        $this->apiKey = trim($apiKey);
        $this->secretKey = trim($secretKey);
        $this->baseUrl = $liveSandbox == 'live' 
            ? 'https://api.iyzipay.com' 
            : 'https://sandbox-api.iyzipay.com';
    }
    
    /**
     * Generate authorization header for Iyzico API
     */
    private function generateAuthorization($requestString, $randomString)
    {
        $hash = base64_encode(hash_hmac('sha256', $randomString . $requestString, $this->secretKey, true));
        return "IYZWS {$this->apiKey}:{$hash}";
    }
    
    /**
     * Create payment request
     */
    public function createPayment($data)
    {
        try {
            $randomString = uniqid();
            $requestString = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $authorization = $this->generateAuthorization($requestString, $randomString);
            
            $response = Http::withHeaders([
                'Authorization' => $authorization,
                'Content-Type' => 'application/json',
                'x-iyzi-rnd' => $randomString,
                'x-iyzi-client-version' => 'iyzipay-php-2.0.50'
            ])->withBody($requestString, 'application/json')
              ->post($this->baseUrl . '/payment/auth');
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Iyzico Payment Error: ' . $e->getMessage());
            return ['status' => 'failure', 'errorMessage' => $e->getMessage()];
        }
    }
    
    /**
     * Initialize payment (create checkout form)
     */
    public function initializePayment($data)
    {
        try {
            // Validate API keys
            if (empty($this->apiKey) || empty($this->secretKey)) {
                Log::error('Iyzico API keys are empty');
                return ['status' => 'failure', 'errorMessage' => 'API keys are not configured'];
            }
            
            // Generate random string (Iyzico expects alphanumeric, typically 13-15 chars)
            $randomString = bin2hex(random_bytes(8)); // 16 character hex string
            $requestString = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
            // Validate JSON encoding
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Iyzico JSON encoding error: ' . json_last_error_msg());
                return ['status' => 'failure', 'errorMessage' => 'Invalid request data format'];
            }
            
            $authorization = $this->generateAuthorization($requestString, $randomString);
            
            $url = $this->baseUrl . '/payment/iyzipos/checkoutform/initialize/auth';
            
            // Log request details (without sensitive data)
            Log::info('Iyzico Request', [
                'url' => $url,
                'apiKey' => substr($this->apiKey, 0, 10) . '...',
                'randomString' => $randomString,
                'requestSize' => strlen($requestString)
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => $authorization,
                'Content-Type' => 'application/json',
                'x-iyzi-rnd' => $randomString,
                'x-iyzi-client-version' => 'iyzipay-php-2.0.50'
            ])->withBody($requestString, 'application/json; charset=utf-8')
              ->post($url);
            
            $responseData = $response->json();
            
            // Log response for debugging
            if (isset($responseData['status']) && $responseData['status'] != 'success') {
                Log::error('Iyzico Initialize Failed', [
                    'response' => $responseData,
                    'request' => $data,
                    'statusCode' => $response->status(),
                    'rawResponse' => $response->body()
                ]);
            }
            
            return $responseData;
        } catch (\Exception $e) {
            Log::error('Iyzico Initialize Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return ['status' => 'failure', 'errorMessage' => $e->getMessage()];
        }
    }
    
    /**
     * Retrieve payment result
     */
    public function retrievePayment($token)
    {
        try {
            $randomString = uniqid();
            $data = ['token' => $token];
            $requestString = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $authorization = $this->generateAuthorization($requestString, $randomString);
            
            $response = Http::withHeaders([
                'Authorization' => $authorization,
                'Content-Type' => 'application/json',
                'x-iyzi-rnd' => $randomString,
                'x-iyzi-client-version' => 'iyzipay-php-2.0.50'
            ])->withBody($requestString, 'application/json')
              ->post($this->baseUrl . '/payment/iyzipos/checkoutform/auth/ecom/detail');
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Iyzico Retrieve Error: ' . $e->getMessage());
            return ['status' => 'failure', 'errorMessage' => $e->getMessage()];
        }
    }
}

