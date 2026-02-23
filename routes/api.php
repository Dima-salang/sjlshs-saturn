<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('attendance', \App\Http\Controllers\AttendanceController::class)->only(['index', 'store', 'destroy']);
    Route::post('qrcodes', [\App\Http\Controllers\QRCodeController::class, 'store'])->name('api.qrcodes.store');
});
