<?php

use Illuminate\Support\Facades\Route;
use Modules\Otp\Http\Controllers\OtpController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('otps', OtpController::class)->names('otp');
});
