<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * Redirect already-authenticated users who visit login pages to their canonical panel.
 */
class RedirectAuthenticatedFromLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = Auth::user();

        // If the user is on any login page, send them to their panel
        if ($request->is('login') || $request->is('admin/login') || $request->is('employee/login') || $request->is('hr/login') || $request->is('app/login')) {
            return redirect()->to($user->panelPath());
        }

        return $next($request);
    }
}
