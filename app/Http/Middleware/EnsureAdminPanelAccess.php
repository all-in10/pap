<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class EnsureAdminPanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/admin/login');
        }

        $user = Auth::user();

        // Only allow admin/root users to access the admin panel
        if ($user->role === UserRole::ADMIN || $user->role === UserRole::ROOT) {
            return $next($request);
        }

        // If not admin/root, redirect to employee panel
        return redirect('/employee');
    }
}
