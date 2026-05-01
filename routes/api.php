<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn() => response()->json(['status' => 'ok']));

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
    // protected routes go here
});