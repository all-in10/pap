<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Models\User; // for Intelephense type hints
use App\Services\Audit;

class EnsureEmployeePanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/employee/login');
        }

        /** @var User|null $user */
        $user = Auth::user();

        // If this user's primary panel is not employee, show 403 page
        if (! str_starts_with($user->panelPath(), '/employee')) {
            \Illuminate\Support\Facades\Log::warning('User tried to access Employee panel but primary panel differs', ['user_id' => $user->id ?? null, 'user_panel' => $user->panelPath(), 'path' => $request->path()]);
            try {
                Audit::recordPanelAccessDenied($request, $user);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
            }

            return response()->view('errors.panel_unauthorized', [
                'panel' => 'Employee',
                'redirectTo' => $user->panelPath(),
            ], 403);
        }

        // Only allow employees after the primary panel check
        if ($user->role === UserRole::EMPLOYEE) {
            return $next($request);
        }

        // Log and audit denied access
        \Illuminate\Support\Facades\Log::warning('Unauthorized employee panel access attempt', ['user_id' => $user->id ?? null, 'path' => $request->path()]);
        try {
            Audit::recordPanelAccessDenied($request, $user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
        }

        // If not employee, show 403 page
        return response()->view('errors.panel_unauthorized', [
            'panel' => 'Employee',
            'redirectTo' => $user->panelPath(),
        ], 403);
    }
} 
