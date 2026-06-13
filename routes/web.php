<?php

use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TelegramSetWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PostSaja Telegram Bot Routes
|--------------------------------------------------------------------------
*/

// Telegram webhook receiver — called by Telegram servers
Route::post('/api/telegram/webhook', TelegramWebhookController::class);

// Setup webhook (call once after deployment)
Route::get('/api/telegram/set-webhook', TelegramSetWebhookController::class);

// Health check
Route::get('/api/health', function () {
    return response()->json(['ok' => true, 'time' => now()->toIso8601String()]);
});
