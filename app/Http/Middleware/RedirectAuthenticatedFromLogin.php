<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class RedirectAuthenticatedFromLogin
{
    public function handle(Request $request, Closure $next)
    {
        // If user is not authenticated, continue
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // If the user is authenticated and is requesting the login page(s), redirect to panel
        if ($request->is('login') || $request->is('admin/login') || $request->is('employee/login')) {
            if ($user->role === UserRole::EMPLOYEE) {
                return redirect()->to('/employee');
            }

            if ($user->role === UserRole::ADMIN || $user->role === UserRole::ROOT) {
                return redirect()->to('/admin');
            }
        }

        return $next($request);
    }
}
