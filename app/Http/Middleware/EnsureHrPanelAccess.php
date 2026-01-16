<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class EnsureHrPanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/hr/login');
        }

        $user = Auth::user();

        // Allow HR, Admin, and Root users to access the HR panel
        if (in_array($user->role, [UserRole::HR, UserRole::ADMIN, UserRole::ROOT])) {
            return $next($request);
        }

        // If not authorized, redirect to appropriate panel
        if ($user->role === UserRole::EMPLOYEE) {
            return redirect('/employee');
        }

        return redirect('/admin');
    }
}