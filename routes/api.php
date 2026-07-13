<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParcelController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\SosAlertController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WalletSnapshotController;
use App\Http\Controllers\Api\WalletOperationsController;
use App\Http\Controllers\Api\PaymentController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/request-otp', [AuthController::class, 'requestOtp']);
Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/complete-registration', [AuthController::class, 'completeRegistration']);
Route::post('/auth/request-password-reset', [AuthController::class, 'requestPasswordReset']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Payment callbacks (no auth)
Route::post('/payments/cinetpay/notify', [PaymentController::class, 'notify']);
Route::get('/payments/cinetpay/callback', [PaymentController::class, 'callback']);


// Protected routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\EnsureUserNotSuspended::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::put('/profile/password', [UserController::class, 'updatePassword']);
    Route::put('/profile/payment-method', [UserController::class, 'updatePaymentMethod']);
    Route::delete('/profile', [UserController::class, 'deleteAccount']);

    // Reviews
    Route::get('/reviews', [UserController::class, 'reviews']);

    // Parcels
    Route::get('/parcels', [ParcelController::class, 'index']);
    Route::get('/me/parcels', [ParcelController::class, 'mine']);
    Route::post('/parcels', [ParcelController::class, 'store']);
    Route::get('/parcels/{id}', [ParcelController::class, 'show']);
    Route::put('/parcels/{id}', [ParcelController::class, 'update']);
    Route::post('/parcels/{id}/accept', [ParcelController::class, 'accept']);
    Route::post('/parcels/{id}/pickup', [ParcelController::class, 'pickup']);
    Route::post('/parcels/{id}/in-transit', [ParcelController::class, 'inTransit']);
    Route::post('/parcels/{id}/delivered', [ParcelController::class, 'delivered']);
    Route::post('/parcels/{id}/cancel', [ParcelController::class, 'cancel']);

    // Offers / Propositions (UI: propositions des relais + négociation)
    Route::get('/me/offers', [OfferController::class, 'mine']);
    Route::get('/parcels/{id}/offers', [OfferController::class, 'index']);
    Route::post('/parcels/{id}/offers', [OfferController::class, 'store']);
    Route::post('/offers/{id}/counter', [OfferController::class, 'counter']);
    Route::post('/offers/{id}/accept', [OfferController::class, 'accept']);
    Route::post('/offers/{id}/reject', [OfferController::class, 'reject']);

    // Messages
    Route::get('/parcels/{id}/messages', [MessageController::class, 'index']);
    Route::post('/parcels/{id}/messages', [MessageController::class, 'store']);

    // KYC
    Route::post('/kyc', [KycController::class, 'submit']);
    Route::get('/kyc/status', [KycController::class, 'status']);
    Route::get('/kyc', [KycController::class, 'get']);

    // Wallet
    Route::get('/wallet', [WalletController::class, 'show']);
    Route::post('/wallet/deposit', [WalletOperationsController::class, 'deposit']);
    Route::post('/wallet/withdraw', [WalletOperationsController::class, 'withdraw']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/wallet/snapshot', [WalletSnapshotController::class, 'show']);

    // Payments (init/status)
    Route::post('/payments/init', [PaymentController::class, 'init']);
    Route::get('/payments/{id}', [PaymentController::class, 'status']);

    // Conversations (UI: liste + détail + envoi)
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::get('/conversations/{id}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/{id}/messages', [ConversationController::class, 'send']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    // SOS
    Route::get('/sos', [SosAlertController::class, 'index']);
    Route::post('/sos', [SosAlertController::class, 'store']);
    Route::get('/sos/{id}', [SosAlertController::class, 'show']);
});
