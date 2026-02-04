<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // If not required to change, proceed
        if (! $user->must_change_password) {
            return $next($request);
        }

        // Allow access to password change routes and logout (simple path checks)
        $current = ltrim($request->getPathInfo(), '/');

        $allowedPrefixes = [
            'password/change',
            'password/change/',
            'password/change',
            'logout',
            'admin/login',
            'hr/login',
            'employee/login',
        ];

        foreach ($allowedPrefixes as $prefix) {
            if ($current === trim($prefix, '/')) {
                return $next($request);
            }

            if (str_starts_with($current, trim($prefix, '/'))) {
                return $next($request);
            }
        }

        return redirect()->route('password.change');
    }
}
