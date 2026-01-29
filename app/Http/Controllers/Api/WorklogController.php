<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Worklog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class WorklogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('manage-worklogs');

        $perPage = (int) $request->query('per_page', 25);
        $query = Worklog::query();

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->query('employee_id'));
        }

        if ($request->has('work_date')) {
            $query->where('work_date', $request->query('work_date'));
        }

        return response()->json($query->orderByDesc('work_date')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('manage-worklogs');

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'work_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        $worklog = Worklog::create($validated);
        return response()->json($worklog, 201);
    }

    public function show(Worklog $worklog): JsonResponse
    {
        Gate::authorize('manage-worklogs');
        return response()->json($worklog->load('employee'));
    }

    public function update(Request $request, Worklog $worklog): JsonResponse
    {
        Gate::authorize('manage-worklogs');

        $validated = $request->validate([
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        $worklog->update($validated);
        return response()->json($worklog);
    }

    public function destroy(Worklog $worklog): JsonResponse
    {
        Gate::authorize('manage-worklogs');
        $worklog->delete();
        return response()->json(['message' => 'Worklog deleted']);
    }
}
