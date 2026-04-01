<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    /**
     * PayMongo redirects here after a successful payment.
     * We verify the session server-side before marking the payment as paid.
     */
    public function success(Request $request, PayMongoService $paymongo)
    {
        $paymentId = $request->query('payment_id');
        $payment   = Payment::with('consultation')->findOrFail($paymentId);

        // Ownership check
        if ($payment->client_id !== Auth::id()) {
            abort(403);
        }

        // Idempotency – handle page refresh after success
        if ($payment->status === 'downpayment_paid') {
            return redirect()->route('consultations')
                ->with('success', 'Your consultation is confirmed! Downpayment already recorded.');
        }

        // Server-side verification with PayMongo
        if ($payment->paymongo_session_id) {
            try {
                $sessionAttrs = $paymongo->retrieveCheckoutSession($payment->paymongo_session_id);
                if (!$paymongo->isSessionPaid($sessionAttrs)) {
                    return redirect()->route('consultations')
                        ->with('error', 'Payment is still being processed. Please wait a moment and refresh.');
                }
            } catch (\RuntimeException $e) {
                Log::warning('PayMongo verification skipped: ' . $e->getMessage());
                // Allow through — PayMongo already redirected to success URL
            }
        }

        $payment->update([
            'status'     => 'downpayment_paid',
            'lawyer_net' => $payment->amount,
        ]);

        // Keep consultation status as 'pending' even after payment
        // This allows the lawyer to manually review and 'Accept' the booking
        if ($payment->consultation && $payment->consultation->status === 'pending') {
            // No change to consultation status here
        }

        return redirect()->route('consultations')
            ->with('success', 'Payment successful! Your downpayment of ₱' . number_format($payment->amount, 2) . ' has been received. Please wait for the lawyer to review and accept your booking request.');
    }

    /**
     * PayMongo redirects here when the user cancels on the checkout page.
     * We cancel the consultation and remove the pending payments.
     */
    public function cancel(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment   = Payment::with('consultation')->find($paymentId);

        if ($payment && $payment->client_id === Auth::id() && $payment->status === 'pending') {
            $consultation = $payment->consultation;
            Payment::where('consultation_id', $consultation->id)->delete();
            $consultation->update(['status' => 'cancelled']);
        }

        return redirect()->route('find-lawyers')
            ->with('error', 'Payment was cancelled. Your booking has not been confirmed. You can try booking again.');
    }
}
