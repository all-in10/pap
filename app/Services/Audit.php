<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Audit
{
    public static function recordModelEvent(string $event, Model $model): void
    {
        // Prevent recursion
        if ($model instanceof AuditLog) {
            return;
        }

        try {
            $user = Auth::user();

            $old = null;
            $new = null;

            if ($event === 'created') {
                $new = $model->getAttributes();
            } elseif ($event === 'updated') {
                $old = $model->getOriginal();
                $new = $model->getChanges();
            } elseif ($event === 'deleted') {
                $old = $model->getOriginal();
            }

            AuditLog::create([
                'user_id' => $user?->id,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'event' => $event,
                'old_values' => $old,
                'new_values' => $new,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'url' => request()?->fullUrl(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to persist audit log for model event: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public static function recordPanelAccessDenied(Request $request, $user = null): void
    {
        try {
            AuditLog::create([
                'user_id' => $user?->id,
                'auditable_type' => null,
                'auditable_id' => null,
                'event' => 'panel_access_denied',
                'old_values' => null,
                'new_values' => ['path' => $request->path(), 'method' => $request->method()],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to persist audit log for panel access denied: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}
