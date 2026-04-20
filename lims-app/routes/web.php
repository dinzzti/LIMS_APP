<?php

use App\Http\Controllers\ThermalController;
use App\Http\Controllers\ThermalQueueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ThermalQueueController::class, 'index'])->name('thermal.queue');

// US 2.1 — Dasbor Antrean Termal
Route::get('/thermal/queue', [ThermalQueueController::class, 'index'])
    ->name('thermal.queue');

// US 2.2 (yang sudah ada) — pastikan menerima query param sample_id
Route::get('/thermal/create', [ThermalController::class, 'create'])
    ->name('thermal.create');
Route::post('/thermal', [ThermalController::class, 'store'])
    ->name('thermal.store');

// US 2.3 — Halaman timer hitung mundur
Route::get('/thermal/{thermalLog}/timer', [ThermalController::class, 'timer'])->name('thermal.timer');

// US 2.5 — Endpoint untuk menyelesaikan pemanasan dan pindahkan ke tahap PCR
Route::post('/thermal/{thermalLog}/complete', [ThermalController::class, 'complete'])->name('thermal.complete');
