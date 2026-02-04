<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePanelRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // If the user is not authenticated, let the auth middleware handle it.
        if (! $user) {
            return $next($request);
        }

        $panel = $request->segment(1) ?? null;

        $allowed = [
            'admin' => ['ADMIN'],
            'hr' => ['HR'],
            'employee' => ['EMPLOYEE'],
        ];

        if (! $panel || ! isset($allowed[$panel])) {
            return $next($request);
        }

        $role = strtoupper((string) $user->role);

        if (in_array($role, $allowed[$panel], true)) {
            return $next($request);
        }

        $loginPaths = [
            'ADMIN' => '/admin/login',
            'HR' => '/hr/login',
            'EMPLOYEE' => '/employee/login',
        ];

        $loginPath = $loginPaths[$role] ?? '/login';

        return response()->view('auth.access-denied', [
            'role' => $role,
            'loginPath' => $loginPath,
            'panel' => $panel,
        ], 403);
    }
}
