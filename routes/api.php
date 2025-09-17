<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// --------------------
// Public Routes
// --------------------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    // Allow refresh without access token middleware; uses refresh_token cookie
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// --------------------
// Protected Routes Group
// --------------------
Route::middleware('jwt.cookie')->group(function () {

    // Auth management
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // User profile routes
    // Route::prefix('profile')->group(function () {
    //     Route::get('/', [ProfileController::class, 'show']);
    //     Route::put('/', [ProfileController::class, 'update']);
    // });

    // Other resource routes
    // Route::apiResource('posts', PostController::class);
});
