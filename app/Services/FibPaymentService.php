<?php

namespace App\Services;

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

        $this->authenticate();
    }

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
                $this->accessToken = $response->json()['access_token'];
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

            // If unauthorized, try to re-authenticate
            if ($response->status() === 401) {
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

            // If unauthorized, try to re-authenticate
            if ($response->status() === 401) {
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

            // If unauthorized, try to re-authenticate
            if ($response->status() === 401) {
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
}

