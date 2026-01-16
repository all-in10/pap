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
        // Skip middleware for change-password route
        if ($request->routeIs('filament.admin.pages.change-password')) {
            return $next($request);
        }

        // Skip for logout, login, and API routes
        if ($request->is('admin/logout', 'admin/login', 'api/*')) {
            return $next($request);
        }

        // Check if user is authenticated and must change password
        $user = $request->user();
        if ($user && $user->must_change_password && (
            str_starts_with($request->path(), 'app/') ||
            str_starts_with($request->path(), 'hr/') ||
            str_starts_with($request->path(), 'admin/')
        )) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}

