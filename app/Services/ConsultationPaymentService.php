<?php

namespace App\Services;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class ConsultationPaymentService
{
    public function __construct(private PayMongoService $paymongo)
    {
    }

    public function payMongoConfigured(): bool
    {
        $secretKey = config('services.paymongo.secret_key', '');

        return $secretKey && !str_contains($secretKey, 'REPLACE_WITH_YOUR_KEY');
    }

    public function createDownpaymentCheckout(Payment $payment, string $context = 'web'): Payment
    {
        return $this->createCheckoutForPayment($payment, forceRefresh: true, context: $context);
    }

    public function createBalanceCheckout(Payment $payment, string $context = 'web'): ?Payment
    {
        return $this->createCheckoutForPayment($payment, context: $context);
    }

    public function createCheckoutForPayment(Payment $payment, bool $forceRefresh = false, string $context = 'web'): Payment
    {
        $payment->loadMissing(['consultation', 'lawyer', 'client']);

        if (!in_array($payment->type, ['downpayment', 'balance'], true) || $payment->status !== 'pending') {
            return $payment;
        }

        if (!$this->payMongoConfigured()) {
            return $this->recordSuccessfulPayment($payment);
        }

        if (!$forceRefresh && $payment->paymongo_checkout_url && $payment->paymongo_session_id) {
            return $payment;
        }

        $consultation = $payment->consultation;
        $lawyerName = $payment->lawyer?->name ?? 'your lawyer';
        $scheduledAt = $consultation?->scheduled_at
            ? Carbon::parse($consultation->scheduled_at)->format('M d, Y g:i A')
            : 'completed session';
        $appUrl = rtrim(config('app.url'), '/');
        $clientName = $payment->client?->name ?? 'Client';
        $clientEmail = $payment->client?->email ?? '';
        $isBalance = $payment->type === 'balance';

        $itemName = $isBalance
            ? 'Legal Consultation - Remaining Balance (50%)'
            : 'Legal Consultation - Downpayment (50%)';
        $itemDescription = $isBalance
            ? "Final payment for {$lawyerName} ({$scheduledAt})"
            : "Booking with {$lawyerName} on {$scheduledAt}";
        if ($context === 'mobile') {
            $successUrl = URL::signedRoute('mobile.payment.success', [
                'payment' => $payment->id,
            ]);
            $cancelUrl = URL::signedRoute('mobile.payment.cancel', [
                'payment' => $payment->id,
            ]);
        } else {
            $successUrl = $isBalance
                ? $appUrl . '/payment/balance/success?payment_id=' . $payment->id
                : $appUrl . '/payment/success?payment_id=' . $payment->id;
            $cancelUrl = $isBalance
                ? $appUrl . '/payment/balance/cancel?payment_id=' . $payment->id
                : $appUrl . '/payment/cancel?payment_id=' . $payment->id;
        }

        $checkout = $this->paymongo->createCheckoutSession(
            amountPhp: $payment->amount,
            itemName: $itemName,
            itemDescription: $itemDescription,
            clientName: $clientName,
            clientEmail: $clientEmail,
            successUrl: $successUrl,
            cancelUrl: $cancelUrl,
            metadata: [
                'payment_id' => (string) $payment->id,
                'consultation_id' => (string) $payment->consultation_id,
                'payment_type' => $payment->type,
            ],
        );

        $payment->update([
            'paymongo_session_id' => $checkout['session_id'],
            'paymongo_checkout_url' => $checkout['checkout_url'],
        ]);

        return $payment->fresh(['consultation', 'lawyer']);
    }

    public function syncPaymentStatus(Payment $payment): Payment
    {
        $payment->loadMissing(['consultation', 'lawyer', 'client']);

        if (($payment->type === 'downpayment' && $payment->status === 'downpayment_paid')
            || ($payment->type === 'balance' && $payment->status === 'paid')) {
            return $payment;
        }

        if ($payment->status !== 'pending' || !$payment->paymongo_session_id) {
            return $payment;
        }

        $sessionAttrs = $this->paymongo->retrieveCheckoutSession($payment->paymongo_session_id);

        if ($this->paymongo->isSessionPaid($sessionAttrs)) {
            return $this->recordSuccessfulPayment($payment);
        }

        return $payment->fresh(['consultation', 'lawyer']);
    }

    public function recordSuccessfulPayment(Payment $payment): Payment
    {
        $payment->loadMissing(['lawyer.lawyerProfile.lawFirm', 'consultation']);

        if ($payment->type === 'balance') {
            // Use pre-calculated firm_cut if available, otherwise calculate now
            if (is_null($payment->firm_cut)) {
                $firmCutPercentage = (float) optional(optional($payment->lawyer?->lawyerProfile)->lawFirm)->cut_percentage;
                $firmCut = round($payment->amount * ($firmCutPercentage / 100), 2);
                $lawyerNet = round($payment->amount - $firmCut, 2);
            } else {
                $firmCut = $payment->firm_cut;
                $lawyerNet = $payment->lawyer_net;
            }

            $payment->update([
                'status'     => 'paid',
                'firm_cut'   => $firmCut,
                'lawyer_net' => $lawyerNet,
            ]);

            return $payment->fresh(['consultation', 'lawyer']);
        }

        // Downpayment — use pre-calculated values
        $payment->update([
            'status' => 'downpayment_paid',
        ]);

        if ($payment->consultation && $payment->consultation->status !== 'pending') {
            $payment->consultation->update(['status' => 'pending']);
        }

        return $payment->fresh(['consultation', 'lawyer']);
    }
}
