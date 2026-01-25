<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PhonePeService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $clientVersion;

    public function __construct()
    {
        $this->baseUrl = env('PHONEPE_ENV') === 'prod' ? 'https://api.phonepe.com/apis/identity-manager' : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
        $this->clientId = env('PHONEPE_CLIENT_ID');
        $this->clientSecret = env('PHONEPE_CLIENT_SECRET');
        $this->clientVersion = env('PHONEPE_CLIENT_VERSION', '1.0');
    }

    public function getAuthToken()
    {
        $response = Http::asForm()->post("{$this->baseUrl}/v1/oauth/token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'client_version' => $this->clientVersion,
            'grant_type' => 'client_credentials',
        ]);
        return $response->json();
    }

    public function createPaymentOrder($accessToken, $orderId, $amount, $redirectUrl)
    {
        $payload = [
            'merchantOrderId' => $orderId,
            'amount' => $amount,
            'expireAfter' => 1200,
            'paymentFlow' => [
                'type' => 'PG_CHECKOUT',
                'message' => 'Pay securely via PhonePe',
                'merchantUrls' => [
                    'redirectUrl' => $redirectUrl,
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => "O-Bearer $accessToken",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/checkout/v2/pay", $payload);

        return $response->json();
    }
}
