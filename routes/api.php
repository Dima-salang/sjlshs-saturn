<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\WorkOS;
use WorkOS\UserManagement;

// ── Public ───────────────────────────────────────────────────────────────────

Route::post('logout', function (Request $request) {
    $accessToken = $request->session()->get('workos_access_token');

    $workOsSession = $accessToken
        ? WorkOS::decodeAccessToken($accessToken)
        : null;

    // Clear the Laravel session
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if ($workOsSession) {
        $logoutUrl = (new UserManagement)->getLogoutUrl(
            $workOsSession['sid'],
            url(config('app.frontend_url')),
        );

        return response()->json(['url' => $logoutUrl]);
    }

    // No WorkOS session found — just tell the frontend to go to login
    return response()->json(['url' => null]);
})->middleware(['auth:sanctum'])->name('api.logout');

Route::get('me', function (Request $request) {
    $user = $request->user();

    if (! $user) {
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
