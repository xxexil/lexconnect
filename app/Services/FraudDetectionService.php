<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Payment;
use App\Models\PaymentRiskEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FraudDetectionService
{
    public function assessConsultationBooking(User $client, User $lawyer, Request $request, float $amount): array
    {
        $score = 0;
        $flags = [];
        $now = now();
        $ipAddress = $request->ip();
        $scheduledAt = Carbon::parse($request->input('scheduled_at'));

        $recentAttemptCount = Consultation::where('client_id', $client->id)
            ->where('created_at', '>=', $now->copy()->subMinutes(30))
            ->count();

        if ($recentAttemptCount >= 3) {
            $score += 25;
            $flags[] = $this->makeFlag(
                'rapid_attempts',
                25,
                "Client created {$recentAttemptCount} consultation attempts in the last 30 minutes."
            );
        }

        $sameLawyerAttempts = Consultation::where('client_id', $client->id)
            ->where('lawyer_id', $lawyer->id)
            ->where('created_at', '>=', $now->copy()->subHours(12))
            ->count();

        if ($sameLawyerAttempts >= 2) {
            $score += 15;
            $flags[] = $this->makeFlag(
                'same_lawyer_repeat',
                15,
                "Client attempted to book the same lawyer {$sameLawyerAttempts} times in the last 12 hours."
            );
        }

        $recentRefunds = Payment::where('client_id', $client->id)
            ->where('status', 'refunded')
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->count();

        if ($recentRefunds >= 2) {
            $score += 20;
            $flags[] = $this->makeFlag(
                'refund_pattern',
                20,
                "Client has {$recentRefunds} refunded payments in the last 30 days."
            );
        }

        $pendingDownpayments = Payment::where('client_id', $client->id)
            ->where('type', 'downpayment')
            ->whereIn('status', ['pending', 'downpayment_paid'])
            ->where('created_at', '>=', $now->copy()->subDay())
            ->count();

        if ($pendingDownpayments >= 3) {
            $score += 15;
            $flags[] = $this->makeFlag(
                'pending_velocity',
                15,
                "Client has {$pendingDownpayments} recent downpayment records in the last 24 hours."
            );
        }

        if ($amount >= 25000) {
            $score += 25;
            $flags[] = $this->makeFlag(
                'very_high_amount',
                25,
                'Booking amount is significantly higher than a normal consultation purchase.'
            );
        } elseif ($amount >= 12000) {
            $score += 10;
            $flags[] = $this->makeFlag(
                'high_amount',
                10,
                'Booking amount is unusually high and should be monitored.'
            );
        }

        if ($scheduledAt->gt($now->copy()->addDays(90))) {
            $score += 10;
            $flags[] = $this->makeFlag(
                'far_future_booking',
                10,
                'Booking was scheduled far in the future compared with normal consultation usage.'
            );
        }

        if ($ipAddress) {
            $otherAccountsOnIp = PaymentRiskEvent::where('ip_address', $ipAddress)
                ->whereNotNull('client_id')
                ->where('client_id', '!=', $client->id)
                ->where('created_at', '>=', $now->copy()->subDays(7))
                ->distinct('client_id')
                ->count('client_id');

            if ($otherAccountsOnIp >= 2) {
                $score += 35;
                $flags[] = $this->makeFlag(
                    'shared_ip_accounts',
                    35,
                    "The same IP address was recently used by {$otherAccountsOnIp} other client accounts."
                );
            }
        }

        if (!$request->userAgent()) {
            $score += 10;
            $flags[] = $this->makeFlag(
                'missing_user_agent',
                10,
                'Request arrived without a user agent string.'
            );
        }

        $riskLevel = 'low';
        $recommendation = 'allow';

        if ($score >= 60) {
            $riskLevel = 'high';
            $recommendation = 'block';
        } elseif ($score >= 30) {
            $riskLevel = 'medium';
            $recommendation = 'review';
        }

        return [
            'risk_score' => $score,
            'risk_level' => $riskLevel,
            'recommendation' => $recommendation,
            'flags' => $flags,
            'ip_address' => $ipAddress,
            'user_agent' => $request->userAgent(),
            'email' => $client->email,
        ];
    }

    public function logAssessment(
        array $assessment,
        User $client,
        User $lawyer,
        float $amount,
        ?int $consultationId = null,
        ?int $paymentId = null,
        string $context = 'consultation_booking'
    ): PaymentRiskEvent {
        return PaymentRiskEvent::create([
            'client_id' => $client->id,
            'lawyer_id' => $lawyer->id,
            'consultation_id' => $consultationId,
            'payment_id' => $paymentId,
            'context' => $context,
            'amount' => round($amount, 2),
            'currency' => 'PHP',
            'risk_score' => $assessment['risk_score'],
            'risk_level' => $assessment['risk_level'],
            'recommendation' => $assessment['recommendation'],
            'ip_address' => $assessment['ip_address'] ?? null,
            'user_agent' => $assessment['user_agent'] ?? null,
            'email' => $assessment['email'] ?? null,
            'flags' => $assessment['flags'] ?? [],
        ]);
    }

    private function makeFlag(string $code, int $weight, string $reason): array
    {
        return [
            'code' => $code,
            'weight' => $weight,
            'reason' => $reason,
        ];
    }
}
