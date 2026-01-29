<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Timeoff;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TimeoffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('manage-timeoffs');

        $perPage = (int) $request->query('per_page', 25);
        $query = Timeoff::query();

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->query('employee_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json($query->orderByDesc('start_date')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:vacation,sick_leave,personal_leave,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $timeoff = Timeoff::create([
            ...$validated,
            'status' => 'pending',
        ]);

        return response()->json($timeoff, 201);
    }

    public function show(Timeoff $timeoff): JsonResponse
    {
        return response()->json($timeoff->load('employee'));
    }

    public function update(Request $request, Timeoff $timeoff): JsonResponse
    {
        Gate::authorize('manage-timeoffs');

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,approved,rejected',
            'reason' => 'nullable|string',
        ]);

        $timeoff->update($validated);
        return response()->json($timeoff);
    }

    public function destroy(Timeoff $timeoff): JsonResponse
    {
        Gate::authorize('manage-timeoffs');
        $timeoff->delete();
        return response()->json(['message' => 'Timeoff deleted']);
    }
}
