<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Dashboard - GGA GGAS
Route::get('/dashboard/gga-ggas/analisa', [App\Http\Controllers\Api\Dashboard\GgaGgasController::class, 'analisaGgaGgas'])->name('api.dashboard.gga-ggas.analisa');
Route::get('/dashboard/gga-ggas/analisa/disposisi', [App\Http\Controllers\Api\Dashboard\GgaGgasController::class, 'analisaDisposisi'])->name('api.dashboard.gga-ggas.analisa.disposisi');

// Dashboard - Blending Awal
Route::get('/dashboard/blending-awal/analisa', [App\Http\Controllers\Api\Dashboard\BlendingAwalController::class, 'analisaBlendingAwal'])->name('api.dashboard.blending-awal.analisa');
Route::get('/dashboard/blending-awal/analisa/disposisi', [App\Http\Controllers\Api\Dashboard\BlendingAwalController::class, 'analisaDisposisi'])->name('api.dashboard.blending-awal.analisa.disposisi');

// Dashboard - Blending After Adjust
Route::get('/dashboard/blending-after-adjust/analisa', [App\Http\Controllers\Api\Dashboard\BlendingAfterAdjustController::class, 'analisaBlendingAfterAdjust'])->name('api.dashboard.blending-after-adjust.analisa');
Route::get('/dashboard/blending-after-adjust/analisa/disposisi', [App\Http\Controllers\Api\Dashboard\BlendingAfterAdjustController::class, 'analisaDisposisi'])->name('api.dashboard.blending-after-adjust.analisa.disposisi');

// Dashboard - Monitoring Turun Blending
Route::get('/dashboard/monitoring-turun-blending/analisa', [App\Http\Controllers\Api\Dashboard\MonitoringTurunBlendingController::class, 'analisaMonitoringTurunBlending'])->name('api.dashboard.monitoring-turun-blending.analisa');
Route::get('/dashboard/monitoring-turun-blending/analisa/disposisi', [App\Http\Controllers\Api\Dashboard\MonitoringTurunBlendingController::class, 'analisaDisposisi'])->name('api.dashboard.monitoring-turun-blending.analisa.disposisi');

// Dashboard - Monitoring Pasteurisasi
Route::get('/dashboard/monitoring-pasteurisasi/analisa', [App\Http\Controllers\Api\Dashboard\MonitoringPasteurisasiController::class, 'analisaMonitoringPasteurisasi'])->name('api.dashboard.monitoring-pasteurisasi.analisa');
Route::get('/dashboard/monitoring-pasteurisasi/analisa/disposisi', [App\Http\Controllers\Api\Dashboard\MonitoringPasteurisasiController::class, 'analisaDisposisi'])->name('api.dashboard.monitoring-pasteurisasi.analisa.disposisi');

// Dashboard - Monitoring Storage Kimia
Route::get('/dashboard/monitoring-storage-kimia/analisa', [App\Http\Controllers\Api\Dashboard\MonitoringStorageKimiaController::class, 'analisaMonitoringStorageKimia'])->name('api.dashboard.monitoring-storage-kimia.analisa');
Route::get('/dashboard/monitoring-storage-kimia/analisa/disposisi', [App\Http\Controllers\Api\Dashboard\MonitoringStorageKimiaController::class, 'analisaDisposisi'])->name('api.dashboard.monitoring-storage-kimia.analisa.disposisi');

// Dashboard - Monitoring Storage Mikro
Route::get('/dashboard/monitoring-storage-mikro/analisa', [App\Http\Controllers\Api\Dashboard\MonitoringStorageMikroController::class, 'analisaMonitoringStorageMikro'])->name('api.dashboard.monitoring-storage-mikro.analisa');
Route::get('/dashboard/monitoring-storage-mikro/analisa/disposisi', [App\Http\Controllers\Api\Dashboard\MonitoringStorageMikroController::class, 'analisaDisposisi'])->name('api.dashboard.monitoring-storage-mikro.analisa.disposisi');


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
Route::get('/blending-awal/{id}', [App\Http\Controllers\Api\BlendingAwalController::class, 'show'])->name('api.blending.awal.show');
Route::post('/blending-awal', [App\Http\Controllers\Api\BlendingAwalController::class, 'store'])->name('api.blending.awal.store');
Route::post('/blending-awal/revisi', [App\Http\Controllers\Api\BlendingAwalController::class, 'update_revisi'])->name('api.blending.awal.update_revisi');

// Blending After Adjust Mikro
Route::post('/blending-after-adjust-mikro', [App\Http\Controllers\Api\BlendingAwalMikroController::class, 'store'])->name('api.blending.after.adjust.mikro.store');

// Monitoring Turun Blending
Route::get('/monitoring-turun-blending/{id}', [App\Http\Controllers\Api\MonitoringTurunBlendingController::class, 'show'])->name('api.monitoring.turun.blending.show');
Route::post('/monitoring-turun-blending', [App\Http\Controllers\Api\MonitoringTurunBlendingController::class, 'store'])->name('api.monitoring.turun.blending.store');
Route::post('/monitoring-turun-blending/revisi', [App\Http\Controllers\Api\MonitoringTurunBlendingController::class, 'update_revisi'])->name('api.monitoring.turun.blending.update_revisi');

// Monitoring Pasteurisasi
Route::get('/monitoring-pasteurisasi/{id}', [App\Http\Controllers\Api\MonitoringPasteurisasiController::class, 'show'])->name('api.monitoring.pasteurisasi.show');
Route::post('/monitoring-pasteurisasi', [App\Http\Controllers\Api\MonitoringPasteurisasiController::class, 'store'])->name('api.monitoring.pasteurisasi.store');
Route::post('/monitoring-pasteurisasi/revisi', [App\Http\Controllers\Api\MonitoringPasteurisasiController::class, 'update_revisi'])->name('api.monitoring.pasteurisasi.update_revisi');

// Monitoring Storage Kimia
Route::get('/monitoring-storage-kimia/{id}', [App\Http\Controllers\Api\MonitoringStorageKimiaController::class, 'show'])->name('api.monitoring.storage.kimia.show');
Route::post('/monitoring-storage-kimia', [App\Http\Controllers\Api\MonitoringStorageKimiaController::class, 'store'])->name('api.monitoring.storage.kimia.store');
Route::post('/monitoring-storage-kimia/revisi', [App\Http\Controllers\Api\MonitoringStorageKimiaController::class, 'update_revisi'])->name('api.monitoring.storage.kimia.update_revisi');

// Monitoring Storage Mikro
Route::post('/monitoring-storage-mikro', [App\Http\Controllers\Api\MonitoringStorageMikroController::class, 'store'])->name('api.monitoring.storage.mikro.store');


Route::get('/mesin/dashboard', [App\Http\Controllers\Api\TimbanganRetailMesinController::class, 'getDashboardData']);
Route::post('mesin', [App\Http\Controllers\Api\TimbanganRetailMesinController::class, 'store']);



// Press Test Data (CRUD)
Route::get('/press-test-data', [App\Http\Controllers\Api\PressTestDataController::class, 'index'])->name('api.press-test-data.index');
Route::post('/press-test-data', [App\Http\Controllers\Api\PressTestDataController::class, 'store'])->name('api.press-test-data.store');
Route::get('/press-test-data/{id}', [App\Http\Controllers\Api\PressTestDataController::class, 'show'])->name('api.press-test-data.show');
Route::post('/press-test-data/{id}', [App\Http\Controllers\Api\PressTestDataController::class, 'update'])->name('api.press-test-data.update');
Route::delete('/press-test-data/{id}', [App\Http\Controllers\Api\PressTestDataController::class, 'destroy'])->name('api.press-test-data.destroy');

// Press Test Mesin 1 (MESIN)
Route::get('/press-test-mesin-1', [App\Http\Controllers\Api\PressTestMesin1Controller::class, 'index'])->name('api.press-test-mesin-1.index');
Route::get('/press-test-mesin-1/all', [App\Http\Controllers\Api\PressTestMesin1Controller::class, 'getAll'])->name('api.press-test-mesin-1.getAll');
Route::post('/press-test-mesin-1/store', [App\Http\Controllers\Api\PressTestMesin1Controller::class, 'store'])->name('api.press-test-mesin-1.store');
