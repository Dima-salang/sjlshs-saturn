<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'teacher.active'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('attendance', \App\Http\Controllers\AttendanceController::class);
    Route::apiResource('teachers', \App\Http\Controllers\TeacherController::class)->except(['store']);
    Route::apiResource('sections', \App\Http\Controllers\SectionController::class);
    Route::post('students/bulk', [\App\Http\Controllers\StudentController::class, 'bulkStore'])->name('api.students.bulk');
    Route::apiResource('students', \App\Http\Controllers\StudentController::class);
    Route::post('qrcodes', [\App\Http\Controllers\QRCodeController::class, 'store'])->name('api.qrcodes.store');
});
