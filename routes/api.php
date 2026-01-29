<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\WorklogController;
use App\Http\Controllers\Api\TimeoffController;
use App\Http\Controllers\Api\AuthController;

// API Documentation
Route::get('/docs.json', function () {
    return response()->file(storage_path('../public/api-docs.json'), [
        'Content-Type' => 'application/json',
    ]);
});

Route::middleware('auth')->group(function () {
    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});

Route::prefix('v1')->middleware('auth')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Employees
    Route::apiResource('employees', EmployeeController::class);

    // Worklogs
    Route::apiResource('worklogs', WorklogController::class);

    // Timeoffs
    Route::apiResource('timeoffs', TimeoffController::class);
});
