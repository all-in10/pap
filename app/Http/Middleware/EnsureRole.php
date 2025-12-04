<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Access;

/**
 * @note This middleware expects `Auth::user()` to return an instance of `\App\Models\User`.
 */

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware("role:admin,hr")
     * Root users bypass role checks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            // not authenticated — let auth middleware handle redirect
            return $next($request);
        }

        // Root bypass
        if (Access::hasRole('root', $user)) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if (Access::hasRole($role, $user)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
