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
        if ($user->role === UserRole::HR || $user->role === UserRole::ADMIN || $user->role === UserRole::ROOT) {
            return $next($request);
        }

        // Log and audit denied access
        \Illuminate\Support\Facades\Log::warning('Unauthorized HR panel access attempt', ['user_id' => $user->id ?? null, 'path' => $request->path()]);
        try {
            \App\Services\Audit::recordPanelAccessDenied($request, $user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
        }

        // If not authorized, redirect to appropriate panel
        if ($user->role === UserRole::EMPLOYEE) {
            return redirect('/employee');
        }

        return redirect('/admin');
    }
}