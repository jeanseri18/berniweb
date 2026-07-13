<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminOperationsController;
use App\Http\Middleware\EnsureUserIsAdmin;

Route::get('/', function () {
    return view('home');
});

Route::get('/comment', function () {
    return view('comment');
});

Route::get('/apropos', function () {
    return view('apropos');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/cgu', function () {
    return view('cgu');
});

Route::get('/confidentialite', function () {
    return view('confidentialite');
});

Route::get('/charte', function () {
    return view('charte');
});

Route::get('/charte-expediteur', function () {
    return view('charte_expediteur');
});

Route::get('/faqs', function () {
    return view('faq');
});

// Admin Authentication Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Admin Routes (Protected)
Route::middleware(['auth', EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/verify-phone', [AdminUserController::class, 'verifyPhone'])->name('users.verify_phone');
    Route::post('/users/{id}/suspend', [AdminOperationsController::class, 'userSuspend'])->name('users.suspend');
    Route::post('/users/{id}/reactivate', [AdminOperationsController::class, 'userReactivate'])->name('users.reactivate');
    Route::post('/users/{id}/wallet-adjust', [AdminOperationsController::class, 'walletAdjust'])->name('users.wallet_adjust');
    Route::get('/couriers', [AdminController::class, 'couriers'])->name('users.couriers');
    Route::get('/senders', [AdminController::class, 'senders'])->name('users.senders');

    // KYC
    Route::get('/kyc', [AdminController::class, 'kycList'])->name('kyc.list');
    Route::get('/kyc/{id}', [AdminController::class, 'kycShow'])->name('kyc.show');
    Route::post('/kyc/{id}/approve', [AdminController::class, 'kycApprove'])->name('kyc.approve');
    Route::post('/kyc/{id}/reject', [AdminController::class, 'kycReject'])->name('kyc.reject');

    // Parcels
    Route::get('/parcels', [AdminController::class, 'parcels'])->name('parcels');
    Route::get('/parcels/{id}', [AdminOperationsController::class, 'parcelShow'])->name('parcels.show');
    Route::post('/parcels/{id}/status', [AdminOperationsController::class, 'parcelUpdateStatus'])->name('parcels.update_status');
    Route::post('/parcels/{id}/cancel', [AdminOperationsController::class, 'parcelCancel'])->name('parcels.cancel');

    // SOS
    Route::get('/sos', [AdminController::class, 'sos'])->name('sos');
    Route::post('/sos/{id}/resolve', [AdminOperationsController::class, 'sosResolve'])->name('sos.resolve');
    Route::post('/sos/{id}/close', [AdminOperationsController::class, 'sosClose'])->name('sos.close');

    // Finances
    Route::get('/finances', [AdminOperationsController::class, 'finances'])->name('finances');
    Route::get('/transactions', [AdminOperationsController::class, 'transactions'])->name('transactions');

    // CinetPay
    Route::get('/payments', [AdminOperationsController::class, 'payments'])->name('payments');
    Route::get('/payments/{id}', [AdminOperationsController::class, 'paymentShow'])->name('payments.show');

    // Offers / Negotiations
    Route::get('/offers', [AdminOperationsController::class, 'offers'])->name('offers');
    Route::post('/offers/{id}/accept', [AdminOperationsController::class, 'offerAccept'])->name('offers.accept');
    Route::post('/offers/{id}/reject', [AdminOperationsController::class, 'offerReject'])->name('offers.reject');
    Route::post('/offers/{id}/reset', [AdminOperationsController::class, 'offerResetNegotiation'])->name('offers.reset');

    // Messages / Moderation
    Route::get('/messages', [AdminOperationsController::class, 'messages'])->name('messages');
    Route::delete('/messages/{id}', [AdminOperationsController::class, 'messageDelete'])->name('messages.delete');

    // Reviews
    Route::get('/reviews', [AdminOperationsController::class, 'reviews'])->name('reviews');

    // Notifications
    Route::get('/notifications', [AdminOperationsController::class, 'notificationsPage'])->name('notifications');
    Route::post('/notifications/send', [AdminOperationsController::class, 'notificationsSend'])->name('notifications.send');
});
