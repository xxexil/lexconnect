<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\ConsultationPaymentService;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BalancePaymentController extends Controller
{
    public function start(Payment $payment, ConsultationPaymentService $paymentService)
    {
        abort_unless($payment->client_id === Auth::id(), 403);
        abort_unless($payment->type === 'balance', 404);

        if ($payment->status === 'paid') {
            return redirect()->route('consultations')
                ->with('success', 'The remaining balance for this consultation is already paid.');
        }

        $payment = $paymentService->createBalanceCheckout(
            $payment->loadMissing(['consultation', 'client', 'lawyer']),
            forceRefresh: true
        );

        if ($payment->status === 'paid') {
            return redirect()->route('consultations')
                ->with('success', 'The remaining balance has been recorded.');
        }

        return redirect($payment->paymongo_checkout_url);
    }

    public function success(Request $request, PayMongoService $paymongo, ConsultationPaymentService $paymentService)
    {
        $payment = Payment::with('consultation')->findOrFail($request->query('payment_id'));

        abort_unless($payment->client_id === Auth::id(), 403);
        abort_unless($payment->type === 'balance', 404);

        if ($payment->status === 'paid') {
            return redirect()->route('consultations')
                ->with('success', 'Your remaining balance has already been recorded.');
        }

        if ($payment->paymongo_session_id) {
            try {
                $sessionAttrs = $paymongo->retrieveCheckoutSession($payment->paymongo_session_id);
                if (!$paymongo->isSessionPaid($sessionAttrs)) {
                    return redirect()->route('consultations')
                        ->with('error', 'Balance payment is still being processed. Please wait a moment and refresh.');
                }
            } catch (\RuntimeException $e) {
                Log::warning('PayMongo balance verification skipped: ' . $e->getMessage());
            }
        }

        $paymentService->recordSuccessfulPayment($payment);

        return redirect()->route('consultations')
            ->with('success', 'Payment successful! Your remaining balance of ₱' . number_format($payment->amount, 2) . ' has been received.');
    }

    public function cancel(Request $request)
    {
        $payment = Payment::find($request->query('payment_id'));

        if ($payment && $payment->client_id === Auth::id() && $payment->type === 'balance') {
            return redirect()->route('consultations')
                ->with('error', 'Balance payment was cancelled. You can pay the remaining balance when you are ready.');
        }

        return redirect()->route('consultations')
            ->with('error', 'Balance payment was cancelled.');
    }
}
