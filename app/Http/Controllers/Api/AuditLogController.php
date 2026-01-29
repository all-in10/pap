<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AuditLog::class);

        $perPage = (int) $request->query('per_page', 25);

        $query = AuditLog::query()->orderByDesc('created_at');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->has('model_type')) {
            $query->where('model_type', $request->query('model_type'));
        }

        return $query->paginate($perPage);
    }
}
