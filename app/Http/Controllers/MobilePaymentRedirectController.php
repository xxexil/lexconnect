<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\ConsultationPaymentService;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobilePaymentRedirectController extends Controller
{
    public function success(Request $request, Payment $payment, PayMongoService $paymongo, ConsultationPaymentService $paymentService)
    {
        abort_unless($request->hasValidSignature(), 403);

        $payment->loadMissing(['consultation']);

        if ($payment->status === 'pending' && $payment->paymongo_session_id) {
            try {
                $sessionAttrs = $paymongo->retrieveCheckoutSession($payment->paymongo_session_id);
                if ($paymongo->isSessionPaid($sessionAttrs)) {
                    $payment = $paymentService->recordSuccessfulPayment($payment);
                }
            } catch (\RuntimeException $e) {
                Log::warning('Mobile payment success verification skipped', [
                    'payment_id' => $payment->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return response()->view('mobile.payment-return', [
            'targetUrl' => $this->buildReturnUrl($payment, 'success'),
            'result' => 'success',
        ]);
    }

    public function cancel(Request $request, Payment $payment)
    {
        abort_unless($request->hasValidSignature(), 403);

        $payment->loadMissing(['consultation']);

        if ($payment->type === 'downpayment' && $payment->status === 'pending' && $payment->consultation) {
            Payment::where('consultation_id', $payment->consultation_id)->delete();
            $payment->consultation->update(['status' => 'cancelled']);
        }

        return response()->view('mobile.payment-return', [
            'targetUrl' => $this->buildReturnUrl($payment->fresh(['consultation']) ?? $payment, 'cancelled'),
            'result' => 'cancelled',
        ]);
    }

    private function buildReturnUrl(Payment $payment, string $result): string
    {
        $baseUrl = rtrim(config('services.mobile.payment_return_url', 'lexconnect://payment-return'), '/');
        $separator = str_contains($baseUrl, '?') ? '&' : '?';

        return $baseUrl . $separator . http_build_query([
            'result' => $result,
            'payment_id' => $payment->id,
            'consultation_id' => $payment->consultation_id,
            'type' => $payment->type,
            'status' => $payment->status,
        ]);
    }
}
