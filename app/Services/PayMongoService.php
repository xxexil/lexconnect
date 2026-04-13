<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey = (string) config('services.paymongo.secret_key', '');
        $this->publicKey = (string) config('services.paymongo.public_key', '');
        $this->baseUrl = rtrim((string) config('services.paymongo.base_url', 'https://api.paymongo.com/v1'), '/');
    }

    /**
     * Create a PayMongo Checkout Session.
     * Returns ['checkout_url' => ..., 'session_id' => ...]
     */
    public function createCheckoutSession(
        float  $amountPhp,
        string $itemName,
        string $itemDescription,
        string $clientName,
        string $clientEmail,
        string $successUrl,
        string $cancelUrl,
        array  $metadata = [],
        array  $paymentMethodTypes = ['gcash', 'paymaya', 'card', 'grab_pay']
    ): array {
        $amountCentavos = (int) round($amountPhp * 100);
        $paymentMethodTypes = $this->normalizePaymentMethodTypes($paymentMethodTypes);

        $payload = [
            'data' => [
                'attributes' => [
                    'billing' => [
                        'name'  => $clientName,
                        'email' => $clientEmail,
                    ],
                    'send_email_receipt' => true,
                    'show_description'   => true,
                    'show_line_items'    => true,
                    'line_items' => [
                        [
                            'currency'    => 'PHP',
                            'amount'      => $amountCentavos,
                            'name'        => $itemName,
                            'description' => $itemDescription,
                            'quantity'    => 1,
                        ],
                    ],
                    'payment_method_types' => $paymentMethodTypes,
                    'success_url' => $successUrl,
                    'cancel_url'  => $cancelUrl,
                    'metadata'    => $metadata,
                ],
            ],
        ];

        $response = $this->client()->post("{$this->baseUrl}/checkout_sessions", $payload);

        while ($response->failed()) {
            Log::error('PayMongo createCheckoutSession failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'payment_method_types' => $paymentMethodTypes,
            ]);

            $invalidMethod = $this->extractInvalidPaymentMethod($response->json(), $response->body());

            if ($invalidMethod && count($paymentMethodTypes) > 1 && in_array($invalidMethod, $paymentMethodTypes, true)) {
                $paymentMethodTypes = array_values(array_filter(
                    $paymentMethodTypes,
                    static fn (string $method) => $method !== $invalidMethod
                ));

                $payload['data']['attributes']['payment_method_types'] = $paymentMethodTypes;
                $response = $this->client()->post("{$this->baseUrl}/checkout_sessions", $payload);
                continue;
            }

            if (count($paymentMethodTypes) > 1 && in_array('card', $paymentMethodTypes, true)) {
                $paymentMethodTypes = ['card'];
                $payload['data']['attributes']['payment_method_types'] = $paymentMethodTypes;
                $response = $this->client()->post("{$this->baseUrl}/checkout_sessions", $payload);

                if (!$response->failed()) {
                    break;
                }

                Log::warning('PayMongo checkout retry with card only also failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }

            throw new \RuntimeException('Payment gateway error. Please try again later.');
        }

        $body = $response->json();

        return [
            'session_id'   => $body['data']['id'],
            'checkout_url' => $body['data']['attributes']['checkout_url'],
        ];
    }

    /**
     * Attempt to refund a payment by its payment intent ID.
     * Returns true if refund succeeded, false otherwise.
     */
    public function refundPaymentIntent(string $paymentIntentId, int $amountCentavos = null): bool
    {
        $payload = [
            'data' => [
                'attributes' => [
                    'payment_intent_id' => $paymentIntentId,
                ],
            ],
        ];

        if ($amountCentavos !== null) {
            $payload['data']['attributes']['amount'] = $amountCentavos;
        }

        $response = $this->client()->post("{$this->baseUrl}/refunds", $payload);

        if ($response->failed()) {
            Log::error('PayMongo refund failed', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Retrieve a Checkout Session to verify payment status.
     * Returns the full session attributes array.
     */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        $response = $this->client()
            ->get("{$this->baseUrl}/checkout_sessions/{$sessionId}");

        if ($response->failed()) {
            Log::error('PayMongo retrieveCheckoutSession failed', [
                'session_id' => $sessionId,
                'status'     => $response->status(),
                'body'       => $response->body(),
            ]);
            throw new \RuntimeException('Could not verify payment status. Please contact support.');
        }

        return $response->json('data.attributes');
    }

    /**
     * Check whether a retrieved checkout session represents a completed payment.
     */
    public function isSessionPaid(array $sessionAttributes): bool
    {
        // PayMongo sets payment_intent status to 'succeeded' when paid
        $intentStatus = $sessionAttributes['payment_intent']['attributes']['status'] ?? null;
        return $intentStatus === 'succeeded';
    }

    /**
     * Return the payment methods the merchant account allows for checkout.
     */
    public function allowedPaymentMethods(): array
    {
        $response = $this->client()->get("{$this->baseUrl}/merchants/capabilities/payment_methods");

        if ($response->failed()) {
            Log::warning('PayMongo allowed payment methods lookup failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $payload = $response->json();
        $methods = data_get($payload, 'data.attributes.payment_method_types')
            ?? data_get($payload, 'data.attributes.payment_methods')
            ?? data_get($payload, 'data.payment_method_types')
            ?? data_get($payload, 'data.payment_methods')
            ?? [];

        return array_values(array_unique(array_filter(array_map(
            static fn ($method) => is_string($method) ? trim($method) : null,
            is_array($methods) ? $methods : []
        ))));
    }

    private function normalizePaymentMethodTypes(array $requestedMethods): array
    {
        $requestedMethods = array_values(array_unique(array_filter(array_map(
            static fn ($method) => is_string($method) ? trim(strtolower($method)) : null,
            $requestedMethods
        ))));

        $requestedMethods = array_map(
            static fn (string $method) => $method === 'maya' ? 'paymaya' : $method,
            $requestedMethods
        );

        if ($requestedMethods === []) {
            return ['card'];
        }

        $allowedMethods = $this->allowedPaymentMethods();
        if ($allowedMethods === []) {
            return $requestedMethods;
        }

        $filteredMethods = array_values(array_intersect($requestedMethods, $allowedMethods));

        if ($filteredMethods !== []) {
            return $filteredMethods;
        }

        if (in_array('card', $allowedMethods, true)) {
            return ['card'];
        }

        return $requestedMethods;
    }

    private function extractInvalidPaymentMethod(array $json, string $body): ?string
    {
        $details = data_get($json, 'errors', []);

        if (is_array($details)) {
            foreach ($details as $error) {
                $detail = strtolower((string) ($error['detail'] ?? ''));
                if (preg_match('/([a-z_]+) is an invalid payment_method/', $detail, $matches) === 1) {
                    return $matches[1];
                }
            }
        }

        $normalizedBody = strtolower($body);
        if (preg_match('/([a-z_]+) is an invalid payment_method/', $normalizedBody, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    private function client()
    {
        return Http::withBasicAuth($this->secretKey, '')
            ->acceptJson()
            ->asJson();
    }
}
