<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\LawyerBlockedDate;
use App\Models\Payment;
use App\Models\User;
use App\Services\FraudDetectionService;
use App\Services\PayMongoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function index() {
        $user = Auth::user();

        Consultation::expireOverdue('client_id', $user->id);

        $consultations = Consultation::with(['lawyer','lawyer.lawyerProfile','review','payment'])
            ->where('client_id', $user->id)
            ->where(function($q) {
                $q->where('status', 'pending')
                  ->whereHas('payment', function($q2) {
                      $q2->where('status', 'downpayment_paid');
                  })
                  ->orWhereIn('status', ['upcoming','completed','cancelled','expired']);
            })
            ->orderByRaw("FIELD(status,'pending','upcoming','completed','cancelled','expired')")
            ->orderBy('scheduled_at')
            ->get();
        $activeConsultation = $consultations->where('status','upcoming')->first();
        return view('consultations', compact('consultations','activeConsultation'));
    }

    public function create(Request $request, User $lawyer)
    {
        $lawyer->load([
            'lawyerProfile.upcomingConsultations',
        ]);

        abort_unless($lawyer->isLawyer() && $lawyer->lawyerProfile, 404);

        $profile = $lawyer->lawyerProfile;
        $blockedDates = LawyerBlockedDate::where('lawyer_id', $lawyer->id)
            ->where('blocked_date', '>=', today())
            ->orderBy('blocked_date')
            ->get();

        $bookedSlots = $profile->upcomingConsultations->map(fn ($consultation) => [
            'start' => Carbon::parse($consultation->scheduled_at)->toIso8601String(),
            'end' => Carbon::parse($consultation->scheduled_at)
                ->addMinutes($consultation->duration_minutes ?? 60)
                ->toIso8601String(),
        ]);

        $blockedDateStrings = $blockedDates
            ->pluck('blocked_date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->values();

        $quickSlots = [];
        $workHours = [9, 10, 11, 13, 14, 15, 16, 17];
        $bookedWindows = $profile->upcomingConsultations->map(fn ($consultation) => [
            'start' => Carbon::parse($consultation->scheduled_at),
            'end' => Carbon::parse($consultation->scheduled_at)->addMinutes($consultation->duration_minutes ?? 60),
        ]);

        for ($dayOffset = 0; $dayOffset <= 14 && count($quickSlots) < 6; $dayOffset++) {
            $day = Carbon::today()->addDays($dayOffset);
            if ($blockedDateStrings->contains($day->format('Y-m-d'))) {
                continue;
            }

            foreach ($workHours as $hour) {
                if (count($quickSlots) >= 6) {
                    break;
                }

                $slot = $day->copy()->setHour($hour)->setMinute(0)->setSecond(0);

                if ($slot->isPast()) {
                    continue;
                }

                $isFree = !$bookedWindows->contains(function ($window) use ($slot) {
                    return $slot->lt($window['end']) && $slot->copy()->addHour()->gt($window['start']);
                });

                if ($isFree) {
                    $quickSlots[] = $slot;
                }
            }
        }

        $selectedAt = old('scheduled_at', $request->query('scheduled_at'));
        $returnTo = $request->query('return_to', route('find-lawyers'));

        return view('consultations-book', [
            'lawyer' => $lawyer,
            'profile' => $profile,
            'blockedDates' => $blockedDates,
            'blockedDateStrings' => $blockedDateStrings,
            'bookedSlots' => $bookedSlots,
            'quickSlots' => $quickSlots,
            'selectedAt' => $selectedAt,
            'returnTo' => $returnTo,
        ]);
    }

    public function book(Request $request, PayMongoService $paymongo, FraudDetectionService $fraudDetection) {
        $request->validate([
            'lawyer_id'     => 'required|exists:users,id',
            'scheduled_at'  => 'required|date|after:now',
            'duration'      => 'required|integer|min:15',
            'type'          => 'required|in:video,phone,in-person',
            'case_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'payment_method'=> 'nullable|in:gcash,paymaya,grab_pay,card,all',
        ]);

        $lawyer = User::with('lawyerProfile')->findOrFail($request->lawyer_id);

        // Check if the lawyer has blocked the requested date
        $scheduledDate = \Carbon\Carbon::parse($request->scheduled_at)->toDateString();
        $isBlocked = LawyerBlockedDate::where('lawyer_id', $request->lawyer_id)
            ->where('blocked_date', $scheduledDate)
            ->exists();
        if ($isBlocked) {
            return back()->with('error', 'This lawyer is unavailable on the selected date. Please choose a different date.');
        }

        $scheduledAt = Carbon::parse($request->scheduled_at);
        $endsAt = $scheduledAt->copy()->addMinutes((int) $request->duration);

        $hasConflict = Consultation::where('lawyer_id', $request->lawyer_id)
            ->whereIn('status', ['pending', 'upcoming'])
            ->whereHas('payment', function ($query) {
                $query->where('status', 'downpayment_paid');
            })
            ->get()
            ->contains(function ($consultation) use ($scheduledAt, $endsAt) {
                $existingStart = Carbon::parse($consultation->scheduled_at);
                $existingEnd = $existingStart->copy()->addMinutes($consultation->duration_minutes ?? 60);

                return $scheduledAt->lt($existingEnd) && $endsAt->gt($existingStart);
            });

        if ($hasConflict) {
            return back()
                ->withInput()
                ->with('error', 'That time slot has just been taken. Please choose a different time.');
        }

        $price = ($lawyer->lawyerProfile->hourly_rate / 60) * $request->duration;
        $assessment = $fraudDetection->assessConsultationBooking($request->user(), $lawyer, $request, $price);

        if ($assessment['recommendation'] === 'block') {
            $fraudDetection->logAssessment($assessment, $request->user(), $lawyer, $price);

            Log::warning('Fraud screening blocked consultation booking', [
                'client_id' => $request->user()->id,
                'lawyer_id' => $lawyer->id,
                'risk_score' => $assessment['risk_score'],
                'flags' => $assessment['flags'],
            ]);

            return back()
                ->withInput()
                ->with('error', 'We could not process this booking automatically for security reasons. Please contact support if you believe this was a mistake.');
        }

        $docPath = null;
        if ($request->hasFile('case_document')) {
            $docPath = $request->file('case_document')->store('case-documents', 'public');
        }

        $consultation = Consultation::create([
            'code'             => 'CONS-'.now()->format('Y').'-'.strtoupper(Str::random(6)),
            'client_id'        => Auth::id(),
            'lawyer_id'        => $request->lawyer_id,
            'scheduled_at'     => $request->scheduled_at,
            'duration_minutes' => $request->duration,
            'type'             => $request->type,
            'status'           => 'pending',
            'price'            => round($price, 2),
            'notes'            => $request->notes,
            'case_document'    => $docPath,
        ]);

        $downpayment = round($consultation->price * 0.50, 2);
        $balance     = round($consultation->price - $downpayment, 2);

        // Downpayment record – starts as pending until PayMongo confirms
        $downpaymentPayment = Payment::create([
            'client_id'       => Auth::id(),
            'lawyer_id'       => $request->lawyer_id,
            'consultation_id' => $consultation->id,
            'amount'          => $downpayment,
            'status'          => 'pending',
            'type'            => 'downpayment',
            'lawyer_net'      => $downpayment,
        ]);

        $fraudDetection->logAssessment(
            $assessment,
            $request->user(),
            $lawyer,
            $price,
            $consultation->id,
            $downpaymentPayment->id
        );

        if ($assessment['recommendation'] === 'review') {
            Log::notice('Fraud screening flagged consultation booking for review', [
                'consultation_id' => $consultation->id,
                'payment_id' => $downpaymentPayment->id,
                'risk_score' => $assessment['risk_score'],
                'flags' => $assessment['flags'],
            ]);
        }

        // Balance record – settled when lawyer ends the session
        Payment::create([
            'client_id'       => Auth::id(),
            'lawyer_id'       => $request->lawyer_id,
            'consultation_id' => $consultation->id,
            'amount'          => $balance,
            'status'          => 'pending',
            'type'            => 'balance',
        ]);

        // Create PayMongo checkout session and redirect client to pay
        $appUrl      = rtrim(config('app.url'), '/');
        $scheduledAt = \Carbon\Carbon::parse($consultation->scheduled_at)->format('M d, Y g:i A');

        $secretKey = config('services.paymongo.secret_key', '');
        $keysConfigured = $secretKey && !str_contains($secretKey, 'REPLACE_WITH_YOUR_KEY');

        $selectedMethod = $request->input('payment_method', 'all');

        // Handle GCash/PayPal manual payment (Prioritize this even if PayMongo is not configured)
        // All payments are now processed via PayMongo checkout session.

        if (!$keysConfigured) {
            // PayMongo not yet configured – mark downpayment as paid immediately (sandbox mode)
            // But keep status as pending so lawyer can review
            $downpaymentPayment->update(['status' => 'downpayment_paid']);
            $consultation->update(['status' => 'pending']);
            return redirect()->route('consultations')
                ->with('success', 'Consultation booked! Please wait for the lawyer to review your request.');
        }

        try {
            // Map selected method to PayMongo types
            $allMethods   = ['gcash', 'paymaya', 'card', 'grab_pay'];
            $methodMap    = [
                'gcash'    => ['gcash'],
                'paymaya'  => ['paymaya'],
                'grab_pay' => ['grab_pay'],
                'card'     => ['card'],
                'all'      => $allMethods,
            ];
            $paymentMethods = $methodMap[$selectedMethod] ?? $allMethods;

            $checkout = $paymongo->createCheckoutSession(
                amountPhp:       $downpayment,
                itemName:        'Legal Consultation – Downpayment (50%)',
                itemDescription: "Booking with {$lawyer->name} on {$scheduledAt} ({$consultation->duration_minutes} min)",
                clientName:      Auth::user()->name,
                clientEmail:     Auth::user()->email,
                successUrl:      $appUrl . '/payment/success?payment_id=' . $downpaymentPayment->id,
                cancelUrl:       $appUrl . '/payment/cancel?payment_id='  . $downpaymentPayment->id,
                metadata: [
                    'payment_id'      => (string) $downpaymentPayment->id,
                    'consultation_id' => (string) $consultation->id,
                ],
                paymentMethodTypes: $paymentMethods,
            );

            $downpaymentPayment->update(['paymongo_session_id' => $checkout['session_id']]);

            return redirect($checkout['checkout_url']);
        } catch (\RuntimeException $e) {
            // Roll back the created records if PayMongo fails
            Payment::where('consultation_id', $consultation->id)->delete();
            $consultation->delete();

            return redirect()->back()
                ->with('error', 'Could not connect to the payment gateway. Please try again later.')
                ->withInput();
        }
    }

    public function cancel(Consultation $consultation) {
        if ($consultation->client_id !== Auth::id()) abort(403);
        $consultation->update(['status' => 'cancelled']);
        return back()->with('success', 'Consultation cancelled.');
    }

    public function attachDocument(Request $request) {
        $request->validate([
            'consultation_id' => 'required|integer|exists:consultations,id',
            'case_document'   => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        $consultation = Consultation::findOrFail($request->consultation_id);
        if ($consultation->client_id !== Auth::id()) abort(403);
        if (!in_array($consultation->status, ['pending', 'upcoming'])) abort(403);

        $path = $request->file('case_document')->store('case-documents', 'public');
        $consultation->update(['case_document' => $path]);

        return back()->with('success', 'Document uploaded successfully.');
    }
}
