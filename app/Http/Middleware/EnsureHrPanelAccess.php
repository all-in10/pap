<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Models\User; // for Intelephense type hints
use App\Services\Audit;

class EnsureHrPanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/hr/login');
        }

        /** @var User|null $user */
        $user = Auth::user();

        // If this user's primary panel is not HR, don't let them stay here — show a 403 page
        if (! str_starts_with($user->panelPath(), '/hr')) {
            \Illuminate\Support\Facades\Log::warning('User tried to access HR panel but primary panel differs', ['user_id' => $user->id ?? null, 'user_panel' => $user->panelPath(), 'path' => $request->path()]);
            try {
                Audit::recordPanelAccessDenied($request, $user);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
            }

            return response()->view('errors.panel_unauthorized', [
                'panel' => 'HR',
                'redirectTo' => $user->panelPath(),
            ], 403);
        }

        // Finally, allow HR users
        if ($user->role === UserRole::HR) {
            return $next($request);
        }

        // Log and audit denied access for all other roles
        \Illuminate\Support\Facades\Log::warning('Unauthorized HR panel access attempt', ['user_id' => $user->id ?? null, 'path' => $request->path()]);
        try {
            Audit::recordPanelAccessDenied($request, $user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
        }

        // If not authorized, show 403 page
        return response()->view('errors.panel_unauthorized', [
            'panel' => 'HR',
            'redirectTo' => $user->panelPath(),
        ], 403);
    }
} 