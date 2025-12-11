<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordChangeController;

Route::get('/', function () {
    return view('welcome');
});

// Redirect authenticated users from login pages to their panels
Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/login', fn() => redirect()->intended('/'))->name('login');
    Route::get('/admin/login', fn() => redirect()->intended('/'))->name('admin.login');
    Route::get('/employee/login', fn() => redirect()->intended('/'))->name('employee.login');
});

// Password change routes
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/update', [PasswordChangeController::class, 'update'])->name('password.update');
});

