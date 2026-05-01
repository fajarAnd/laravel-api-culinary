<?php

use App\Http\Controllers\Api\Admin\RequestLogController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\TwoFactorController;
use App\Http\Controllers\Api\Restaurant\RestaurantController;
use App\Http\Controllers\Api\Telegram\TelegramController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn() => response()->json(['status' => 'ok']));

// Telegram webhook — no auth, excluded from logging middleware
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // 2FA verify — pakai tmp_token (scope: 2fa-verify)
    Route::post('2fa/verify', [TwoFactorController::class, 'verify'])
        ->middleware('auth:api');

    Route::middleware(['auth:api', 'two_factor'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        Route::prefix('2fa')->group(function () {
            Route::post('setup', [TwoFactorController::class, 'setup']);
            Route::post('enable', [TwoFactorController::class, 'enable']);
            Route::post('disable', [TwoFactorController::class, 'disable']);
        });
    });
});

Route::middleware(['auth:api', 'two_factor'])->group(function () {
    // Restaurants
    Route::prefix('restaurants')->group(function () {
        Route::get('/', [RestaurantController::class, 'index']);
        Route::get('/nearby', [RestaurantController::class, 'nearby']);
        Route::get('/{id}', [RestaurantController::class, 'show']);
        Route::get('/{id}/menu', [RestaurantController::class, 'menu']);
        Route::get('/{id}/reviews', [RestaurantController::class, 'reviews']);
    });

    Route::get('/locations', [RestaurantController::class, 'locations']);

    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/request-logs', [RequestLogController::class, 'index']);
        Route::get('/request-logs/stats', [RequestLogController::class, 'stats']);
        Route::get('/request-logs/{requestLog}', [RequestLogController::class, 'show']);
    });
});