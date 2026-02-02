<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            return redirect($user->panelPath());
        }
    }
    return view('welcome');
});

// Filament handles panel login routes; /login is handled explicitly below.

// Authentication routes
Route::get('/login', function () {
    // Render the unified login page (the AppServiceProvider shares panel login links)
    return view('auth.login');
})->name('login')->middleware('redirect.login');

// Local auth attempt (email/password) and logout
Route::post('/auth/attempt', [AuthController::class, 'attempt'])->name('auth.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Panels (protected by `auth` and role middleware)
Route::middleware(['auth','role:admin,root'])->group(function () {
    Route::get('/admin', [\App\Http\Controllers\AdminPanelController::class, 'index'])->name('panel.admin');
});

Route::middleware(['auth','role:hr,admin,root'])->group(function () {
    Route::get('/hr', [\App\Http\Controllers\HrPanelController::class, 'index'])->name('panel.hr');
});

Route::middleware(['auth','role:employee'])->group(function () {
    Route::get('/employee', [\App\Http\Controllers\EmployeePanelController::class, 'index'])->name('panel.employee');
});

// Password change routes
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/update', [PasswordChangeController::class, 'update'])->name('password.update');
});