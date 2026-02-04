<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractPdfController;

Route::get('/', function () {
    return view('welcome');
});

// Download de contratos em PDF
Route::get('/contracts/{contract}/download', [ContractPdfController::class, 'download'])->name('contracts.download');
