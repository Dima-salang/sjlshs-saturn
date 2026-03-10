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

        // Admins always pass through.
        if ($user && $user->is_admin) {
            return $next($request);
        }

        // Inactive users always receive a JSON 403 — the frontend handles the redirect.
        if ($user && !$user->is_active) {
            return response()->json([
                'message' => 'Your account is currently inactive. Please contact an administrator.',
                'inactive' => true,
            ], 403);
        }

        return $next($request);
    }
}
