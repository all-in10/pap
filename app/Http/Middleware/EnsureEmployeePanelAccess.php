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

        // Log and audit denied access
        \Illuminate\Support\Facades\Log::warning('Unauthorized employee panel access attempt', ['user_id' => $user->id ?? null, 'path' => $request->path()]);
        try {
            \App\Services\Audit::recordPanelAccessDenied($request, $user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
        }

        // If not employee, redirect to admin panel
        return redirect('/admin');
    }
}
