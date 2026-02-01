<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Models\User; // for Intelephense type hints

class RedirectAuthenticatedFromLogin
{
    public function handle(Request $request, Closure $next)
    {
        // If user is not authenticated, continue
        if (!Auth::check()) {
            return $next($request);
        }

        /** @var User|null $user */
        $user = Auth::user();

        // If the user is authenticated and is requesting any login page(s), redirect to the user's panel
        if ($request->is('login') || $request->is('admin/login') || $request->is('employee/login') || $request->is('hr/login') || $request->is('app/login')) {
            // Delegate decision to User::panelPath() to avoid mismatches and keep a single source of truth
            return redirect()->to($user->panelPath());
        }

        return $next($request);
    }
}
