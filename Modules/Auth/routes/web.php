<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::prefix('auth/user/')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'checkingTwoFactorLogin']);
    Route::post('/verify/code', [AuthController::class, 'verify_otp_code']);
    Route::post('/verify/twofactor/code', [AuthController::class, 'verify_twofactor_code']);
    Route::post('/resend/code', [AuthController::class, 'ResendCode']);
});

Route::middleware('auth:api')->prefix('auth/user/')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('userinfo', [AuthController::class, 'user']);
});
