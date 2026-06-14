<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TelegramSetWebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleBusinessController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::permanentRedirect('/', 'https://postsaja.com');

// Telegram webhook (no auth, no CSRF)
Route::post('/api/telegram/webhook', TelegramWebhookController::class);
Route::get('/api/telegram/set-webhook', TelegramSetWebhookController::class);
Route::get('/api/health', function () {
    return response()->json(['ok' => true, 'time' => now()->toIso8601String()]);
});

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Posts
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts/{post}/approve', [PostController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/reject', [PostController::class, 'reject'])->name('posts.reject');

    // Google Business
    Route::get('/google-business', [GoogleBusinessController::class, 'index'])->name('google-business');
    Route::get('/google-business/connect', [GoogleBusinessController::class, 'redirect'])->name('google-business.connect');
    Route::get('/google-business/callback', [GoogleBusinessController::class, 'callback'])->name('google-business.callback');
    Route::post('/google-business/disconnect', [GoogleBusinessController::class, 'disconnect'])->name('google-business.disconnect');

    // WhatsApp Status
    Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp');
    Route::post('/whatsapp/connect', [WhatsAppController::class, 'connect'])->name('whatsapp.connect');
    Route::post('/whatsapp/disconnect', [WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
