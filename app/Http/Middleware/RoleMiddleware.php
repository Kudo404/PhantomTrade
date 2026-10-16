<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage in routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,merchant')
     *   ->middleware('role:ghost_hunter')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Not logged in? Send to login page.
        if (! $user) {
            return redirect()->route('login');
        }

        // Logged in but role not in the allowed list? 403.
        if (! in_array($user->role, $roles)) {
            abort(403, 'You do not have permission to view this page.');
        }

        return $next($request);
    }
}