<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\PanelRoleMiddleware;

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

// Rotas dos panels protegidas por role
Route::middleware(['auth', 'panel.role:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        // ... suas rotas do painel admin ...
    });
});

Route::middleware(['auth', 'panel.role:hr'])->group(function () {
    Route::prefix('hR')->group(function () {
        // ... suas rotas do painel hr ...
    });
});

Route::middleware(['auth', 'panel.role:employee'])->group(function () {
    Route::prefix('employee')->group(function () {
        // ... suas rotas do painel employee ...
    });
});
