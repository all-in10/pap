<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractPdfController;
use App\Http\Controllers\ExportController;

Route::get('/', function () {
    return view('welcome');
});

// PWA Service Worker - served with correct headers for HTTPS
Route::get('/js/sw.js', function () {
    $swPath = public_path('build/js/sw.js');
    if (!file_exists($swPath)) {
        // Fallback for development
        $swPath = public_path('js/sw.js');
    }
    
    if (!file_exists($swPath)) {
        abort(404, 'Service Worker not found');
    }
    
    return response()->file($swPath, [
        'Cache-Control' => 'public, no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
        'Service-Worker-Allowed' => '/',
        'Content-Type' => 'application/javascript; charset=utf-8',
    ]);
});

// PWA Offline page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// Download de contratos em PDF
Route::get('/contracts/{contract}/download', [ContractPdfController::class, 'download'])->name('contracts.download');

// Rotas de Exportação (requer autenticação e autorização)
Route::middleware(['auth'])->prefix('export')->name('export.')->group(function () {
    Route::get('{model}/csv', [ExportController::class, 'exportCSV'])->name('csv');
    Route::get('{model}/excel', [ExportController::class, 'exportExcel'])->name('excel');
    Route::get('{model}/json', [ExportController::class, 'exportJSON'])->name('json');
});

// Password change routes
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [\App\Http\Controllers\UserPasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/change', [\App\Http\Controllers\UserPasswordController::class, 'update'])->name('password.update');
});

