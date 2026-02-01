<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Enums\UserRole;
use App\Services\Audit;
use App\Models\User; // for Intelephense type hints

class EnsureAdminPanelAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/admin/login');
        }

        /** @var User|null $user */
        $user = Auth::user();

        // Only allow admin/root users to access the admin panel
        if ($user->role === UserRole::ADMIN || $user->role === UserRole::ROOT) {
            return $next($request);
        }

        // Log and audit denied access
        Log::warning('Unauthorized admin panel access attempt', ['user_id' => $user->id ?? null, 'path' => $request->path()]);

        try {
            Audit::recordPanelAccessDenied($request, $user);
        } catch (\Throwable $e) {
            Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
        }

        // If not admin/root, redirect to the user's own panel
        return redirect($user->panelPath());
    }
}
