<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// --------------------
// Public Auth Routes
// --------------------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes using JWT from cookie
    Route::middleware(['jwt.cookie', 'auth:api'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
