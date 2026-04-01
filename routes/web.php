<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\ClientSettingsController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\PayMongoChildMerchantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VideoCallController;
use App\Http\Controllers\Lawyer\LawyerDashboardController;
use App\Http\Controllers\Lawyer\LawyerConsultationController;
use App\Http\Controllers\Lawyer\LawyerEarningsController;
use App\Http\Controllers\Lawyer\LawyerProfileController;
use App\Http\Controllers\Lawyer\LawyerFirmController;
use App\Http\Controllers\Lawyer\LawyerMessageController;
use App\Http\Controllers\Lawyer\LawyerBlockedDateController;
use App\Http\Controllers\LawFirm\LawFirmDashboardController;
use App\Http\Controllers\LawFirm\LawFirmLawyerController;
use App\Http\Controllers\LawFirm\LawFirmProfileController;
use App\Http\Controllers\LawFirm\LawFirmConsultationController;
use App\Http\Controllers\LawFirm\LawFirmEarningsController;
use App\Http\Controllers\LawFirm\LawFirmMessageController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminLawFirmController;
use App\Http\Controllers\Admin\AdminLawyerController;
use App\Http\Controllers\Admin\AdminConsultationController;
use App\Http\Controllers\Admin\AdminFraudRiskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Admin auth routes (separate from main auth)
Route::get('/admin/login',  [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout',[AdminAuthController::class, 'logout'])->name('admin.logout');

// Guest routes
Route::get('/', fn() => view('landing'))->name('home');
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// Legal pages
Route::get('/terms',   fn() => view('legal.terms'))->name('terms');
Route::get('/privacy', fn() => view('legal.privacy'))->name('privacy');

// Password Reset Routes
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Broadcasting authentication for WebSocket private channels
    Broadcast::routes();
    
    // Test route for broadcasting
    Route::get('/test-broadcast/{conversationId}', function($conversationId) {
        try {
            \Log::info('Test broadcast route called for conversation: ' . $conversationId);
            
            // Create a simple test event
            $testData = [
                'id' => 999,
                'conversation_id' => $conversationId,
                'sender_id' => 1,
                'body' => 'Test message from broadcast route',
                'time' => now()->format('g:i A'),
                'sender_name' => 'Test User'
            ];
            
            \Log::info('Broadcasting test data: ' . json_encode($testData));
            
            // Use the correct Laravel broadcasting syntax
            broadcast(new class($testData) implements \Illuminate\Contracts\Broadcasting\ShouldBroadcastNow {
                use \Illuminate\Broadcasting\InteractsWithSockets;
                
                public $data;
                
                public function __construct($data) {
                    $this->data = $data;
                }
                
                public function broadcastOn() {
                    return new \Illuminate\Broadcasting\PrivateChannel('conversation.' . $this->data['conversation_id']);
                }
                
                public function broadcastAs() {
                    return 'MessageSent';
                }
                
                public function broadcastWith() {
                    return $this->data;
                }
            });
                
            \Log::info('Test broadcast sent successfully');
            
            return response()->json(['status' => 'Test broadcast sent', 'data' => $testData]);
        } catch (\Exception $e) {
            \Log::error('Test broadcast failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
    
    // Test route for lawyer broadcasting
    Route::get('/test-lawyer-broadcast/{conversationId}', function($conversationId) {
        try {
            \Log::info('Test lawyer broadcast route called for conversation: ' . $conversationId);
            
            // Create a simple test event
            $testData = [
                'id' => 999,
                'conversation_id' => $conversationId,
                'sender_id' => 1, // Simulate client sending message
                'body' => 'Test message from lawyer broadcast route',
                'time' => now()->format('g:i A'),
                'sender_name' => 'Test Client'
            ];
            
            \Log::info('Broadcasting lawyer test data: ' . json_encode($testData));
            
            // Use the correct Laravel broadcasting syntax
            broadcast(new class($testData) implements \Illuminate\Contracts\Broadcasting\ShouldBroadcastNow {
                use \Illuminate\Broadcasting\InteractsWithSockets;
                
                public $data;
                
                public function __construct($data) {
                    $this->data = $data;
                }
                
                public function broadcastOn() {
                    return new \Illuminate\Broadcasting\PrivateChannel('conversation.' . $this->data['conversation_id']);
                }
                
                public function broadcastAs() {
                    return 'MessageSent';
                }
                
                public function broadcastWith() {
                    return $this->data;
                }
            });
                
            \Log::info('Test lawyer broadcast sent successfully');
            
            return response()->json(['status' => 'Test lawyer broadcast sent', 'data' => $testData]);
        } catch (\Exception $e) {
            \Log::error('Test lawyer broadcast failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
    
    Route::get('/dashboard',    [DashboardController::class,  'index'])->name('dashboard');
    Route::get('/find-lawyers',         [LawyerController::class,     'index'])->name('find-lawyers');
    Route::get('/lawyers/{id}/profile', [LawyerController::class,     'show'])->name('lawyer.public-profile');
    Route::get('/consultations/book/{lawyer}',           [ConsultationController::class,'create'])->name('consultations.create');
    Route::post('/messages/start',      [MessageController::class,    'startConversation'])->name('messages.start');

    Route::get('/consultations',                          [ConsultationController::class,'index'])->name('consultations');
    Route::post('/consultations/book',                    [ConsultationController::class,'book'])->name('consultations.book');
    Route::post('/consultations/{consultation}/cancel',   [ConsultationController::class,'cancel'])->name('consultations.cancel');
    Route::post('/consultations/attach-document',         [ConsultationController::class,'attachDocument'])->name('consultations.attach-document');
    Route::get('/consultations/{consultation}/video',     [VideoCallController::class,   'join'])->name('consultations.video');
    Route::post('/consultations/{consultation}/video/end',[VideoCallController::class,   'end'])->name('consultations.video.end');

    Route::get('/messages',      [MessageController::class,'index'])->name('messages');
    Route::post('/messages/send',[MessageController::class,'send'])->name('messages.send');

    Route::get('/payments', [PaymentController::class,'index'])->name('payments');

    // PayMongo payment gateway callbacks
    Route::get('/payment/success', [PaymentGatewayController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel',  [PaymentGatewayController::class, 'cancel'])->name('payment.cancel');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Client profile & settings
    Route::get('/profile',           [ClientProfileController::class, 'show'])->name('client.profile');
    Route::put('/profile',           [ClientProfileController::class, 'update'])->name('client.profile.update');
    Route::get('/settings',          [ClientSettingsController::class, 'show'])->name('client.settings');
    Route::put('/settings/password', [ClientSettingsController::class, 'updatePassword'])->name('client.settings.password');
    Route::get('/help',              fn() => view('client.help'))->name('client.help');
});

// Lawyer portal routes
Route::middleware(['auth', 'lawyer'])->prefix('lawyer')->name('lawyer.')->group(function () {
    Route::get('/dashboard', [LawyerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/consultations', [LawyerConsultationController::class, 'index'])->name('consultations');
    Route::post('/consultations/{id}/accept',  [LawyerConsultationController::class, 'accept'])->name('consultations.accept');
    Route::post('/consultations/{id}/decline', [LawyerConsultationController::class, 'decline'])->name('consultations.decline');
    Route::post('/consultations/{id}/complete',[LawyerConsultationController::class, 'complete'])->name('consultations.complete');

    Route::get('/messages', [LawyerMessageController::class, 'index'])->name('messages');
    
    Route::post('/messages/send', [LawyerMessageController::class, 'send'])->name('messages.send');

    Route::get('/earnings', [LawyerEarningsController::class, 'index'])->name('earnings');

    Route::get('/profile',               [LawyerProfileController::class, 'show'])->name('profile');
    Route::post('/profile',              [LawyerProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar',       [LawyerProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/availability', [LawyerProfileController::class, 'updateAvailability'])->name('profile.availability');
    Route::post('/profile/paymongo-child-merchant/start', [PayMongoChildMerchantController::class, 'startForLawyer'])->name('paymongo-child-merchant.start');

    Route::get('/firms',        [LawyerFirmController::class, 'index'])->name('firms');
    Route::post('/firms/apply', [LawyerFirmController::class, 'apply'])->name('firms.apply');
    Route::post('/firms/leave', [LawyerFirmController::class, 'leave'])->name('firms.leave');

    Route::get('/blocked-dates',          [LawyerBlockedDateController::class, 'index'])->name('blocked-dates.index');
    Route::post('/blocked-dates',         [LawyerBlockedDateController::class, 'store'])->name('blocked-dates.store');
    Route::delete('/blocked-dates/{id}',  [LawyerBlockedDateController::class, 'destroy'])->name('blocked-dates.destroy');
});

// Law Firm portal routes
Route::middleware(['auth', 'lawfirm'])->prefix('lawfirm')->name('lawfirm.')->group(function () {
    Route::get('/dashboard', [LawFirmDashboardController::class, 'index'])->name('dashboard');

    Route::get('/lawyers',                   [LawFirmLawyerController::class, 'index'])->name('lawyers');
    Route::post('/lawyers/{id}/accept',      [LawFirmLawyerController::class, 'accept'])->name('lawyers.accept');
    Route::post('/lawyers/{id}/reject',      [LawFirmLawyerController::class, 'reject'])->name('lawyers.reject');
    Route::post('/lawyers/{id}/remove',      [LawFirmLawyerController::class, 'remove'])->name('lawyers.remove');

    Route::get('/consultations', [LawFirmConsultationController::class, 'index'])->name('consultations');
    Route::get('/earnings',      [LawFirmEarningsController::class, 'index'])->name('earnings');

    Route::get('/messages',        [LawFirmMessageController::class, 'index'])->name('messages');
    Route::post('/messages/start', [LawFirmMessageController::class, 'startConversation'])->name('messages.start');
    Route::post('/messages/send',  [LawFirmMessageController::class, 'send'])->name('messages.send');

    Route::get('/profile',  [LawFirmProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [LawFirmProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/paymongo-child-merchant/start', [PayMongoChildMerchantController::class, 'startForLawFirm'])->name('paymongo-child-merchant.start');
});

// Admin portal routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',    [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users',                [AdminUserController::class, 'index'])->name('users');
    Route::delete('/users/{user}',      [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/lawyers',                          [AdminLawyerController::class, 'index'])->name('lawyers');
    Route::get('/lawyers/{lawyer}',                 [AdminLawyerController::class, 'show'])->name('lawyers.show');
    Route::post('/lawyers/{lawyer}/certify',        [AdminLawyerController::class, 'certify'])->name('lawyers.certify');
    Route::post('/lawyers/{lawyer}/uncertify',      [AdminLawyerController::class, 'uncertify'])->name('lawyers.uncertify');

    Route::get('/law-firms',                        [AdminLawFirmController::class, 'index'])->name('law-firms');
    Route::post('/law-firms/{firm}/verify',         [AdminLawFirmController::class, 'verify'])->name('law-firms.verify');
    Route::post('/law-firms/{firm}/unverify',       [AdminLawFirmController::class, 'unverify'])->name('law-firms.unverify');

    Route::get('/consultations', [AdminConsultationController::class, 'index'])->name('consultations');
    Route::get('/fraud-risk-events', [AdminFraudRiskController::class, 'index'])->name('risk-events');
});
