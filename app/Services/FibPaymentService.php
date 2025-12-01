<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FibPaymentService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $environment;
    protected $accessToken;

    public function __construct()
    {
        $this->environment = config('fib.environment', 'stage');
        $this->clientId = config('fib.client_id');
        $this->clientSecret = config('fib.client_secret');

        // Set base URL based on environment
        $baseUrls = [
            'dev' => 'https://fib.dev.fib.iq',
            'stage' => 'https://fib.stage.fib.iq',
            'prod' => 'https://fib.fib.iq',
        ];

        $this->baseUrl = $baseUrls[$this->environment] ?? $baseUrls['stage'];

        // Load token from cache or authenticate
        $this->loadToken();
    }

    /**
     * Load token from cache or authenticate if not available
     */
    protected function loadToken()
    {
        $cacheKey = "fib_access_token_{$this->environment}";
        
        // Try to get token from cache
        $this->accessToken = Cache::get($cacheKey);
        
        // If no cached token, authenticate
        if (!$this->accessToken) {
            $this->authenticate();
        }
    }

    /**
     * Authenticate with FIB and cache the token
     */
    protected function authenticate()
    {
        try {
            $response = Http::withoutVerifying() // Disable SSL verification
                ->asForm()
                ->post("{$this->baseUrl}/auth/realms/fib-online-shop/protocol/openid-connect/token", [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

            if ($response->successful()) {
                $tokenData = $response->json();
                $this->accessToken = $tokenData['access_token'];
                
                // Cache the token (typically expires in 3600 seconds / 1 hour)
                // Cache for 50 minutes to be safe (3000 seconds)
                $expiresIn = $tokenData['expires_in'] ?? 3600;
                $cacheTime = max(300, $expiresIn - 300); // Cache for at least 5 minutes, or expires_in - 5 minutes
                
                $cacheKey = "fib_access_token_{$this->environment}";
                Cache::put($cacheKey, $this->accessToken, now()->addSeconds($cacheTime));
                
                Log::info('FIB token cached', [
                    'expires_in' => $expiresIn,
                    'cached_for' => $cacheTime,
                ]);
            } else {
                Log::error('FIB Authentication failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to authenticate with FIB: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('FIB Authentication error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function create($amount, $description = '')
    {
        try {
            // Ensure we have a valid token
            if (!$this->accessToken) {
                $this->authenticate();
            }

            $response = Http::withoutVerifying() // Disable SSL verification
                ->withToken($this->accessToken)
                ->post("{$this->baseUrl}/protected/v1/payments", [
                    'monetaryValue' => [
                        'amount' => $amount,
                        'currency' => 'IQD',
                    ],
                    'statusCallbackUrl' => config('fib.callback_url'),
                    'description' => $description,
                ]);

            // If unauthorized, clear cache and try to re-authenticate
            if ($response->status() === 401) {
                $cacheKey = "fib_access_token_{$this->environment}";
                Cache::forget($cacheKey);
                $this->authenticate();
                $response = Http::withoutVerifying()
                    ->withToken($this->accessToken)
                    ->post("{$this->baseUrl}/protected/v1/payments", [
                        'monetaryValue' => [
                            'amount' => $amount,
                            'currency' => 'IQD',
                        ],
                        'statusCallbackUrl' => config('fib.callback_url'),
                        'description' => $description,
                    ]);
            }

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('FIB Payment creation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to create payment: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('FIB Payment creation error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function status($paymentId)
    {
        try {
            // Ensure we have a valid token
            if (!$this->accessToken) {
                $this->authenticate();
            }

            $response = Http::withoutVerifying() // Disable SSL verification
                ->withToken($this->accessToken)
                ->get("{$this->baseUrl}/protected/v1/payments/{$paymentId}/status");

            // If unauthorized, clear cache and try to re-authenticate
            if ($response->status() === 401) {
                $cacheKey = "fib_access_token_{$this->environment}";
                Cache::forget($cacheKey);
                $this->authenticate();
                $response = Http::withoutVerifying()
                    ->withToken($this->accessToken)
                    ->get("{$this->baseUrl}/protected/v1/payments/{$paymentId}/status");
            }

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('FIB Payment status failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to get payment status: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('FIB Payment status error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function cancel($paymentId)
    {
        try {
            // Ensure we have a valid token
            if (!$this->accessToken) {
                $this->authenticate();
            }

            $response = Http::withoutVerifying() // Disable SSL verification
                ->withToken($this->accessToken)
                ->post("{$this->baseUrl}/protected/v1/payments/{$paymentId}/cancel");

            // If unauthorized, clear cache and try to re-authenticate
            if ($response->status() === 401) {
                $cacheKey = "fib_access_token_{$this->environment}";
                Cache::forget($cacheKey);
                $this->authenticate();
                $response = Http::withoutVerifying()
                    ->withToken($this->accessToken)
                    ->post("{$this->baseUrl}/protected/v1/payments/{$paymentId}/cancel");
            }

            if ($response->successful()) {
                return true;
            } else {
                Log::error('FIB Payment cancellation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to cancel payment: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('FIB Payment cancellation error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function refund($paymentId)
    {
        try {
            // Ensure we have a valid token
            if (!$this->accessToken) {
                $this->authenticate();
            }

            $response = Http::withoutVerifying() // Disable SSL verification
                ->withToken($this->accessToken)
                ->post("{$this->baseUrl}/protected/v1/payments/{$paymentId}/refund");

            // If unauthorized, clear cache and try to re-authenticate
            if ($response->status() === 401) {
                $cacheKey = "fib_access_token_{$this->environment}";
                Cache::forget($cacheKey);
                $this->authenticate();
                $response = Http::withoutVerifying()
                    ->withToken($this->accessToken)
                    ->post("{$this->baseUrl}/protected/v1/payments/{$paymentId}/refund");
            }

            // Refund returns 202 Accepted
            if ($response->status() === 202) {
                return true;
            } else {
                Log::error('FIB Payment refund failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to refund payment: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('FIB Payment refund error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

