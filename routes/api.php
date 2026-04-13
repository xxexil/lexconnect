<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Api\Client\LawyerController as ClientLawyerController;
use App\Http\Controllers\Api\Client\ConsultationController as ClientConsultationController;
use App\Http\Controllers\Api\Client\MessageController as ClientMessageController;
use App\Http\Controllers\Api\Client\PaymentController as ClientPaymentController;
use App\Http\Controllers\Api\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Api\Client\ReviewController as ClientReviewController;
use App\Http\Controllers\Api\Lawyer\DashboardController as LawyerDashboard;
use App\Http\Controllers\Api\Lawyer\ConsultationController as LawyerConsultationController;
use App\Http\Controllers\Api\Lawyer\MessageController as LawyerMessageController;
use App\Http\Controllers\Api\Lawyer\EarningsController as LawyerEarningsController;
use App\Http\Controllers\Api\Lawyer\ProfileController as LawyerProfileController;
use App\Models\LawyerProfile;
use App\Http\Controllers\Api\LawFirm\DashboardController as LawFirmDashboardController;
use App\Http\Controllers\Api\LawFirm\TeamController as LawFirmTeamController;
use App\Http\Controllers\Api\LawFirm\ConsultationController as LawFirmConsultationController;
use App\Http\Controllers\Api\LawFirm\EarningsController as LawFirmEarningsController;
use App\Http\Controllers\Api\LawFirm\MessageController as LawFirmMessageController;
use App\Http\Controllers\Api\LawFirm\ProfileController as LawFirmProfileController;

// PayMongo webhook – public, no auth (signature is verified inside the controller)
Route::post('/webhooks/paymongo', [\App\Http\Controllers\PaymentWebhookController::class, 'handle']);

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Public lawyer browsing (accessible to all authenticated users)
    Route::get('/lawyers', [ClientLawyerController::class, 'index']);
    Route::get('/lawyers/{id}', [ClientLawyerController::class, 'show']);

    // Client routes
    Route::prefix('client')->group(function () {
        Route::get('/dashboard', [ClientDashboard::class, 'index']);
        Route::get('/consultations', [ClientConsultationController::class, 'index']);
        Route::post('/consultations', [ClientConsultationController::class, 'book']);
        Route::post('/consultations/{id}/cancel', [ClientConsultationController::class, 'cancel']);
        Route::get('/payments', [ClientPaymentController::class, 'index']);
        Route::post('/payments/{id}/resume', [ClientPaymentController::class, 'resume']);
        Route::get('/payments/{id}/status', [ClientPaymentController::class, 'status']);
        Route::get('/messages', [ClientMessageController::class, 'index']);
        Route::get('/messages/{conversationId}', [ClientMessageController::class, 'show']);
        Route::post('/messages/start', [ClientMessageController::class, 'start']);
        Route::post('/messages/send', [ClientMessageController::class, 'send']);
        Route::get('/profile', [ClientProfileController::class, 'show']);
        Route::put('/profile', [ClientProfileController::class, 'update']);
        Route::post('/reviews', [ClientReviewController::class, 'store']);
    });

    // Lawyer routes
    Route::prefix('lawyer')->group(function () {
        Route::get('/dashboard', [LawyerDashboard::class, 'index']);
        Route::get('/consultations', [LawyerConsultationController::class, 'index']);
        Route::post('/consultations/{id}/accept', [LawyerConsultationController::class, 'accept']);
        Route::post('/consultations/{id}/decline', [LawyerConsultationController::class, 'decline']);
        Route::post('/consultations/{id}/complete', [LawyerConsultationController::class, 'complete']);
        Route::get('/messages', [LawyerMessageController::class, 'index']);
        Route::get('/messages/{conversationId}', [LawyerMessageController::class, 'show']);
        Route::post('/messages/send', [LawyerMessageController::class, 'send']);
        Route::get('/earnings', [LawyerEarningsController::class, 'index']);
        Route::get('/profile', [LawyerProfileController::class, 'show']);
        Route::put('/profile', [LawyerProfileController::class, 'update']);
        Route::put('/profile/availability', [LawyerProfileController::class, 'updateAvailability']);
    });

    Route::get('/lawyer-profile/{id}', function($id) {
        $profile = LawyerProfile::where('user_id', $id)->first();
        if (!$profile) return response()->json(['error'=>'Not found'], 404);
        return response()->json([
            'gcash_number' => $profile->gcash_number,
            'gcash_qr'     => $profile->gcash_qr,
            'gcash_qr_url' => $profile->gcash_qr ? asset('storage/'.$profile->gcash_qr) : null,
        ]);
    });

    // Law Firm routes
    Route::prefix('lawfirm')->group(function () {
        Route::get('/dashboard', [LawFirmDashboardController::class, 'index']);
        Route::get('/team', [LawFirmTeamController::class, 'index']);
        Route::get('/consultations', [LawFirmConsultationController::class, 'index']);
        Route::get('/earnings', [LawFirmEarningsController::class, 'index']);
        Route::get('/messages', [LawFirmMessageController::class, 'index']);
        Route::get('/messages/{conversationId}', [LawFirmMessageController::class, 'show']);
        Route::post('/messages/start', [LawFirmMessageController::class, 'start']);
        Route::post('/messages/send', [LawFirmMessageController::class, 'send']);
        Route::get('/profile', [LawFirmProfileController::class, 'show']);
    });
});

// Public lawyer profile API (for payment info)
Route::get('/lawyer-profile/{id}', function($id) {
    $profile = LawyerProfile::where('user_id', $id)->first();
    if (!$profile) return response()->json(['error'=>'Not found'], 404);
    return response()->json([
        'gcash_number' => $profile->gcash_number,
        'gcash_qr'     => $profile->gcash_qr,
        'gcash_qr_url' => $profile->gcash_qr ? asset('storage/'.$profile->gcash_qr) : null,
    ]);
});
