<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class EnsureEmployeePanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/employee/login');
        }

        $user = Auth::user();

        // Only allow employees to access the employee panel
        if ($user->role === UserRole::EMPLOYEE) {
            return $next($request);
        }

        // If not employee, redirect to admin panel
        return redirect('/admin');
    }
}
