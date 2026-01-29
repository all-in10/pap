<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('manage-employees');

        $perPage = (int) $request->query('per_page', 25);
        $query = Employee::query();

        if ($request->has('department_id')) {
            $query->where('department_id', $request->query('department_id'));
        }

        if ($request->has('designation_id')) {
            $query->where('designation_id', $request->query('designation_id'));
        }

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('manage-employees');

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees',
            'phone' => 'nullable|string',
            'nif' => 'nullable|string|unique:employees',
            'nss' => 'nullable|string|unique:employees',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        $employee = Employee::create($validated);
        return response()->json($employee, 201);
    }

    public function show(Employee $employee): JsonResponse
    {
        Gate::authorize('manage-employees');
        return response()->json($employee->load(['user', 'department', 'designation']));
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        Gate::authorize('manage-employees');

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string',
            'nif' => 'nullable|string|unique:employees,nif,' . $employee->id,
            'nss' => 'nullable|string|unique:employees,nss,' . $employee->id,
            'date_of_birth' => 'nullable|date',
            'country_id' => 'sometimes|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'department_id' => 'sometimes|exists:departments,id',
            'designation_id' => 'sometimes|exists:designations,id',
        ]);

        $employee->update($validated);
        return response()->json($employee);
    }

    public function destroy(Employee $employee): JsonResponse
    {
        Gate::authorize('manage-employees');
        $employee->delete();
        return response()->json(['message' => 'Employee deleted']);
    }
}
