<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordChangeController;

Route::get('/', function () {
    return view('welcome');
});

// Password change routes
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/update', [PasswordChangeController::class, 'update'])->name('password.update');
});
