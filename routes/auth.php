<?php

use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLoginRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLogoutRequest;

Route::get('login', function (AuthKitLoginRequest $request) {
    return $request->redirect();
})->middleware(['guest'])->name('login');

Route::get('authenticate', function (AuthKitAuthenticationRequest $request) {
    $request->authenticate();

    // Always redirect to the Next.js frontend after authentication.
    // We intentionally avoid redirect()->intended() because this Laravel app is a pure
    // API backend — any stale "intended" URL in the session (e.g. /dashboard) would
    // incorrectly land the user on the Laravel Inertia page instead of the Next.js app.
    return redirect(config('app.frontend_url'));
})->middleware(['guest']);

Route::post('logout', function (AuthKitLogoutRequest $request) {
    return $request->logout(config('app.frontend_url'));
})->middleware(['auth'])->name('logout');
