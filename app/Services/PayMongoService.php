<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
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

            $response = \Illuminate\Support\Facades\Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/refunds", $payload);

            if ($response->failed()) {
                \Illuminate\Support\Facades\Log::error('PayMongo refund failed', [
                    'payment_intent_id' => $paymentIntentId,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }
            return true;
        }

    private string $secretKey;
    private string $publicKey;
    private string $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
        $this->publicKey = config('services.paymongo.public_key');
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

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post("{$this->baseUrl}/checkout_sessions", $payload);

        if ($response->failed()) {
            Log::error('PayMongo createCheckoutSession failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Payment gateway error. Please try again later.');
        }

        $body = $response->json();

        return [
            'session_id'   => $body['data']['id'],
            'checkout_url' => $body['data']['attributes']['checkout_url'],
        ];
    }

    /**
     * Retrieve a Checkout Session to verify payment status.
     * Returns the full session attributes array.
     */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        $response = Http::withBasicAuth($this->secretKey, '')
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
}
