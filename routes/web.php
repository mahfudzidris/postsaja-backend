<?php

use Illuminate\Support\Facades\Route;

Route::permanentRedirect('/', 'https://postsaja.com');

Route::get('/api/health', function () {
    return response()->json(['ok' => true, 'time' => now()->toIso8601String()]);
});
