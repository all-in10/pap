<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check() ? redirect('/redirect-by-role') : redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/redirect-by-role', function () {
    $user = Auth::user();
    if (!$user) return redirect('/login');
    if ($user->role === 'admin') {
        return redirect('/admin');
    } elseif ($user->role === 'hr') {
        return redirect('/hR');
    } else {
        return redirect('/employee');
    }
})->middleware('auth');
