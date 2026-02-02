<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for change-password routes
        if ($request->routeIs('filament.admin.pages.change-password', 'filament.employee.pages.change-password')) {
            return $next($request);
        }

        // Skip for logout, login, and API routes
        if ($request->is('admin/logout', 'admin/login', 'employee/logout', 'employee/login', 'api/*')) {
            return $next($request);
        }

        // Check if user is authenticated and must change password
        $user = $request->user();
        if ($user && $user->must_change_password) {
            // Redirect to appropriate change-password page based on current panel
            if ($request->is('admin/*')) {
                return redirect('/admin/change-password');
            } elseif ($request->is('employee/*')) {
                return redirect('/employee/change-password');
            }
        }

        return $next($request);
    }
}