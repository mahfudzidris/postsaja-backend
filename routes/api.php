<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\PlatformAuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Posts
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/drafts', [PostController::class, 'drafts']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::post('/posts/{post}/publish', [PostController::class, 'publish']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Plans
    Route::get('/plans', [PlanController::class, 'index']);
    Route::get('/plan/current', [PlanController::class, 'current']);

    // Platforms (Social Media Connections)
    Route::get('/platforms', [PlatformAuthController::class, 'index']);
    Route::post('/platforms/{platform}/connect', [PlatformAuthController::class, 'connect'])->name('platform.connect');
    Route::post('/platforms/{platform}/callback', [PlatformAuthController::class, 'callback'])->name('platform.callback');
    Route::delete('/platforms/{platform}/disconnect', [PlatformAuthController::class, 'disconnect']);
    Route::post('/platforms/{platform}/verify', [PlatformAuthController::class, 'verify']);
});
