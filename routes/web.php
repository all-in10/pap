<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordChangeController;

Route::get('/', function () {
    return view('welcome');
});

// Redirect authenticated users from login pages to their panels
Route::middleware(['web'])->group(function () {
    Route::get('/login', fn() => redirect('/app/login'))->name('login');

    // Unified app login (view and POST handler)
    Route::get('/app/login', [\App\Http\Controllers\Auth\AppLoginController::class, 'show'])
        ->name('app.login')
        ->middleware('guest');

    Route::post('/app/login', [\App\Http\Controllers\Auth\AppLoginController::class, 'login'])
        ->name('app.login.post')
        ->middleware('guest');

    // Lightweight '/app' entry that redirects authenticated users to their panel
    Route::get('/app', function () {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect('/app/login');
        }

        $user = \Illuminate\Support\Facades\Auth::user();

        return match ($user->role) {
            \App\Enums\UserRole::EMPLOYEE => redirect('/employee'),
            \App\Enums\UserRole::HR => redirect('/hr'),
            \App\Enums\UserRole::ADMIN, \App\Enums\UserRole::ROOT => redirect('/admin'),
            default => redirect('/'),
        };
    })->name('app.index');

    // Optional: app logout that invalidates session
    Route::post('/app/logout', [\App\Http\Controllers\Auth\AppLoginController::class, 'logout'])
        ->name('app.logout');

    // /admin/login and /employee/login are provided by Filament; don't override them here.

    // Legacy employee dashboard URL — redirect to Filament panel
    Route::get('/employee/employee-dashboard', function () {
        // Prefer the Filament panel home route if registered, otherwise fallback to the /employee URL
        return redirect()->to(Route::has('filament.employee.home') ? route('filament.employee.home') : url('/employee'));
    })->name('filament.employee.pages.employee-dashboard');

    // Register the panel root directly to render the EmployeeDashboard page at /employee
    Route::get('/employee', \App\Filament\Pages\EmployeeDashboard::class)
        ->name('filament.employee.pages.dashboard');

});

// Password change routes
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/update', [PasswordChangeController::class, 'update'])->name('password.update');
});

