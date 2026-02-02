<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\Audit;
use App\Models\User;

class ValidateUserPanelRole
{
    /**
     * Ensure authenticated users access only the panels compatible with their role.
     * If the user's primary panel doesn't match the requested panel, record an audit
     * and respond with a 403 view that includes a link back to the user's panel.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = Auth::user();

        $segment = explode('/', trim($request->path(), '/'))[0] ?? '';

        // Only validate for known panel slugs
        if (! in_array($segment, ['admin', 'hr', 'employee', 'filament'], true)) {
            return $next($request);
        }

        if ($user->canAccessPanel($segment)) {
            return $next($request);
        }

        Log::warning('ValidateUserPanelRole: mismatched panel access attempt', ['user_id' => $user->id ?? null, 'segment' => $segment, 'user_panel' => $user->panelPath()]);
        try {
            Audit::recordPanelAccessDenied($request, $user);
        } catch (\Throwable $e) {
            Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
        }

        return response()->view('errors.panel_unauthorized', [
            'panel' => ucfirst($segment),
            'redirectTo' => $user->panelPath(),
        ], 403);
    }
}
