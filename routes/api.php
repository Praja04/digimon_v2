<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Production Batch
Route::get('/persiapan-masak', [App\Http\Controllers\Api\ProductionBatchController::class, 'index'])->name('api.production.batch.index');
Route::get('/persiapan-masak/{id}', [App\Http\Controllers\Api\ProductionBatchController::class, 'show'])->name('api.production.batch.show');
Route::post('/persiapan-masak', [App\Http\Controllers\Api\ProductionBatchController::class, 'store'])->name('api.production.batch.store');
Route::post('/persiapan-masak/{id}', [App\Http\Controllers\Api\ProductionBatchController::class, 'update'])->name('api.production.batch.update');
Route::delete('/persiapan-masak/{id}', [App\Http\Controllers\Api\ProductionBatchController::class, 'destroy'])->name('api.production.batch.destroy');

// GGA
Route::post('/gga', [App\Http\Controllers\Api\GgaController::class, 'store'])->name('api.gga.store');
Route::post('/gga/revisi', [App\Http\Controllers\Api\GgaController::class, 'update_revisi'])->name('api.gga.update_revisi');

// GGAS
Route::post('/ggas', [App\Http\Controllers\Api\GgasController::class, 'store'])->name('api.ggas.store');
Route::post('/ggas/revisi', [App\Http\Controllers\Api\GgasController::class, 'update_revisi'])->name('api.ggas.update_revisi');
