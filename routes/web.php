<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordChangeController;

Route::get('/', function () {
    return view('welcome');
});

// Ensure the employee panel root serves the dashboard (keep URL as /employee)
// This dispatches internally to the actual dashboard route so the browser URL stays as /employee
Route::get('/employee', function (\Illuminate\Http\Request $request) {
    $subRequest = \Illuminate\Http\Request::create('/employee/employee-dashboard', 'GET', [], $request->cookies->all(), [], $request->server->all());

    // Preserve session so Page can access auth/session data
    if (method_exists($subRequest, 'setLaravelSession')) {
        $subRequest->setLaravelSession($request->session());
    }

    return app()->handle($subRequest);
})->middleware(['web', 'auth']);

// Redirect authenticated users from login pages to their panels
Route::middleware(['web'])->group(function () {
    Route::get('/login', fn() => redirect('/app/login'))->name('login');
    // /admin/login and /employee/login are provided by Filament; don't override them here.


});

// Password change routes
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/update', [PasswordChangeController::class, 'update'])->name('password.update');
});

