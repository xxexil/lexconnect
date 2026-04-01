<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives and processes payment event webhooks from PayMongo.
 * This endpoint is public (no auth) so PayMongo can POST to it freely,
 * but every request is verified with an HMAC-SHA256 signature check.
 */
class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $rawBody  = $request->getContent();
        $sigHeader = $request->header('Paymongo-Signature');

        $webhookSecret = config('services.paymongo.webhook_secret');

        // Reject unsigned requests when a secret is configured
        if ($webhookSecret && $sigHeader) {
            if (!$this->verifySignature($rawBody, $sigHeader, $webhookSecret)) {
                Log::warning('PayMongo webhook: invalid signature rejected');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        } elseif ($webhookSecret && !$sigHeader) {
            Log::warning('PayMongo webhook: request missing Paymongo-Signature header');
            return response()->json(['error' => 'Missing signature'], 401);
        }

        $payload   = json_decode($rawBody, true);
        $eventType = $payload['data']['attributes']['type'] ?? null;

        Log::info('PayMongo webhook received', ['type' => $eventType]);

        match ($eventType) {
            'checkout_session.payment.paid' => $this->handleCheckoutPaid($payload),
            'payment.failed' => $this->handlePaymentFailed($payload),
            default => null,
        };
        return response()->json(['received' => true]);
    }

    private function handlePaymentFailed(array $payload): void
    {
        // The payment data is nested inside data.attributes.data
        $sessionData = $payload['data']['attributes']['data'] ?? null;
        $sessionId   = $sessionData['id'] ?? null;
        $metadata    = $sessionData['attributes']['metadata'] ?? [];
        $paymentId   = $metadata['payment_id'] ?? null;

        // Look up the local payment record
        $payment = $paymentId
            ? Payment::with('consultation')->find((int) $paymentId)
            : ($sessionId
                ? Payment::with('consultation')->where('paymongo_session_id', $sessionId)->first()
                : null);

        if (!$payment) {
            Log::warning('PayMongo webhook: payment.failed - payment record not found', [
                'payment_id' => $paymentId,
                'session_id' => $sessionId,
            ]);
            return;
        }


        // Only update if still pending
        if ($payment->status === 'pending') {
            $payment->update([
                'status' => 'failed',
            ]);

            // Attempt refund if payment_intent exists
            $intentId = $sessionData['attributes']['payment_intent_id'] ?? null;
            if ($intentId) {
                $paymongo = app(PayMongoService::class);
                $amountCentavos = (int) round($payment->amount * 100);
                $refundResult = $paymongo->refundPaymentIntent($intentId, $amountCentavos);
                Log::info('PayMongo refund attempted on payment.failed', [
                    'payment_id' => $payment->id,
                    'intent_id' => $intentId,
                    'amount' => $amountCentavos,
                    'refund_success' => $refundResult,
                ]);
            }
        }

        Log::info('PayMongo webhook: payment.failed processed', [
            'payment_id'      => $payment->id,
            'consultation_id' => $payment->consultation_id,
        ]);
    }

    // ---------------------------------------------------------------

    private function handleCheckoutPaid(array $payload): void
    {
        // The session data is nested inside data.attributes.data
        $sessionData = $payload['data']['attributes']['data'] ?? null;
        $sessionId   = $sessionData['id'] ?? null;
        $metadata    = $sessionData['attributes']['metadata'] ?? [];
        $paymentId   = $metadata['payment_id'] ?? null;

        // Look up the local payment record
        $payment = $paymentId
            ? Payment::with('consultation')->find((int) $paymentId)
            : ($sessionId
                ? Payment::with('consultation')->where('paymongo_session_id', $sessionId)->first()
                : null);

        if (!$payment) {
            Log::warning('PayMongo webhook: payment record not found', [
                'payment_id' => $paymentId,
                'session_id' => $sessionId,
            ]);
            return;
        }

        // Idempotent – skip if already processed
        if ($payment->status === 'downpayment_paid') {
            return;
        }

        $payment->update([
            'status'     => 'downpayment_paid',
            'lawyer_net' => $payment->amount,
        ]);

        // If consultation exists and is not already pending, set to pending
        if ($payment->consultation && $payment->consultation->status !== 'pending') {
            $payment->consultation->update(['status' => 'pending']);
        }

        Log::info('PayMongo webhook: downpayment confirmed via webhook', [
            'payment_id'      => $payment->id,
            'consultation_id' => $payment->consultation_id,
        ]);
    }

    /**
     * Verify the Paymongo-Signature header using HMAC-SHA256.
     *
     * Header format: t={timestamp},te={test_hmac},li={live_hmac}
     * Signed payload: "{timestamp}.{raw_body}"
     */
    private function verifySignature(string $rawBody, string $sigHeader, string $secret): bool
    {
        $parts = [];
        foreach (explode(',', $sigHeader) as $part) {
            if (!str_contains($part, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $part, 2);
            $parts[trim($key)] = trim($value);
        }

        $timestamp = $parts['t'] ?? null;
        if (!$timestamp) {
            return false;
        }

        $signedPayload = "{$timestamp}.{$rawBody}";
        $expected      = hash_hmac('sha256', $signedPayload, $secret);

        // Accept either the test (te) or live (li) HMAC
        $provided = $parts['te'] ?? $parts['li'] ?? null;
        if (!$provided) {
            return false;
        }

        return hash_equals($expected, $provided);
    }
}
