<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

Route::get('/', function () {
    return view('welcome');
});

// Filament handles panel login routes; /login is handled explicitly below.

// Authentication routes
Route::get('/login', function () {
    if (Auth::check()) {        /** @var \App\Models\User $user */        $user = Auth::user();
        if ($user instanceof User) {
            return redirect($user->panelPath());
        }
    }
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Password change routes
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/update', [PasswordChangeController::class, 'update'])->name('password.update');

    // Filament HR quick actions for Timeoffs (approve / reject)
    Route::post('/filament/hr/timeoffs/{timeoff}/approve', [\App\Http\Controllers\Filament\Hr\TimeoffApprovalController::class, 'approve'])
        ->middleware('auth')
        ->name('filament.hr.timeoffs.approve');

    Route::post('/filament/hr/timeoffs/{timeoff}/reject', [\App\Http\Controllers\Filament\Hr\TimeoffApprovalController::class, 'reject'])
        ->middleware('auth')
        ->name('filament.hr.timeoffs.reject');
});

