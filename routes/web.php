<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractPdfController;
use App\Http\Controllers\ExportController;

Route::get('/', function () {
    return view('welcome');
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

