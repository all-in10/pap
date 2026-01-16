<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordChangeController;

Route::get('/', function () {
    return view('welcome');
});

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

