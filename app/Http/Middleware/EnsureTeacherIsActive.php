<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacherIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = $request->user();

        // if the user is an admin, permit
        if ($user && $user->is_admin) {
            return $next($request);
        }

        // if the user is not active, deny
        if ($user && ! $user->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your account is currently inactive. Please contact an administrator.',
                ], 403);
            }

            abort(403, 'Your account is currently inactive.');
        }

        return $next($request);
    }
}
