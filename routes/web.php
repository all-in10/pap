<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractPdfController;

Route::get('/', function () {
    return view('welcome');
});

// Download de contratos em PDF
Route::get('/contracts/{contract}/download', [ContractPdfController::class, 'download'])->name('contracts.download');

// Password change routes
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [\App\Http\Controllers\UserPasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password/change', [\App\Http\Controllers\UserPasswordController::class, 'update'])->name('password.update');
});
