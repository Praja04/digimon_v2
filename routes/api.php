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

// Blending Awal
Route::post('/blending-awal', [App\Http\Controllers\Api\BlendingAwalController::class, 'store'])->name('api.blending.awal.store');
Route::post('/blending-awal/revisi', [App\Http\Controllers\Api\BlendingAwalController::class, 'update_revisi'])->name('api.blending.awal.update_revisi');

// Blending After Adjust Mikro
Route::post('/blending-after-adjust-mikro', [App\Http\Controllers\Api\BlendingAwalMikroController::class, 'store'])->name('api.blending.after.adjust.mikro.store');

// Monitoring Turun Blending
Route::post('/monitoring-turun-blending', [App\Http\Controllers\Api\MonitoringTurunBlendingController::class, 'store'])->name('api.monitoring.turun.blending.store');
Route::post('/monitoring-turun-blending/revisi', [App\Http\Controllers\Api\MonitoringTurunBlendingController::class, 'update_revisi'])->name('api.monitoring.turun.blending.update_revisi');

// Monitoring Pasteurisasi
Route::post('/monitoring-pasteurisasi', [App\Http\Controllers\Api\MonitoringPasteurisasiController::class, 'store'])->name('api.monitoring.pasteurisasi.store');
Route::post('/monitoring-pasteurisasi/revisi', [App\Http\Controllers\Api\MonitoringPasteurisasiController::class, 'update_revisi'])->name('api.monitoring.pasteurisasi.update_revisi');

// Monitoring Storage Kimia
Route::post('/monitoring-storage-kimia', [App\Http\Controllers\Api\MonitoringStorageKimiaController::class, 'store'])->name('api.monitoring.storage.kimia.store');
Route::post('/monitoring-storage-kimia/revisi', [App\Http\Controllers\Api\MonitoringStorageKimiaController::class, 'update_revisi'])->name('api.monitoring.storage.kimia.update_revisi');

// Monitoring Storage Mikro
Route::post('/monitoring-storage-mikro', [App\Http\Controllers\Api\MonitoringStorageMikroController::class, 'store'])->name('api.monitoring.storage.mikro.store');
