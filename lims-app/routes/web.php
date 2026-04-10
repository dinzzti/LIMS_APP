<?php

use App\Http\Controllers\ThermalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('thermal')->group(function () {
    Route::get('/create', [ThermalController::class, 'create'])->name('thermal.create');
    Route::post('/store', [ThermalController::class, 'store'])->name('thermal.store');
});
