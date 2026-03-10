<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Requests\AuthKitLogoutRequest;

// ── Public ───────────────────────────────────────────────────────────────────

Route::post('logout', function (AuthKitLogoutRequest $request) {
    $response = $request->logout(config('app.frontend_url'));

    if ($request->expectsJson()) {
        return response()->json([
            'url' => $response->headers->get('Location') ?: $response->headers->get('X-Inertia-Location'),
        ]);
    }

    return $response;
})->middleware(['auth:sanctum'])->name('api.logout');

Route::get('me', function (Request $request) {
    $user = $request->user();

    if (!$user) {
        return response()->json(['authenticated' => false], 401);
    }

    return response()->json([
        'authenticated' => true,
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => $user->avatar,
        'is_admin' => $user->is_admin,
        'is_active' => $user->is_active,
        'is_nonFaculty' => $user->is_nonFaculty,
    ]);
})->middleware(['auth:sanctum'])->name('api.me');

// ── Authenticated & active ────────────────────────────────────────────────────

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
