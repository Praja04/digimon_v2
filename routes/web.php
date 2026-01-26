<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');

Auth::routes();

/*------------------------------------------
--------------------------------------------
Routes berdasarkan Role Access
--------------------------------------------
--------------------------------------------*/

Route::middleware(['auth'])->group(function () {
    /*------------------------------------------
    Halaman utama
    Roles: All
    --------------------------------------------*/
    Route::get('/halaman-utama', [App\Http\Controllers\HomepageController::class, 'index'])->name('homepage.index');

    Route::get('/press-test-data', [App\Http\Controllers\PressTestDataController::class, 'index'])->name('press-test-data.index');
    Route::post('/press-test-data', [App\Http\Controllers\PressTestDataController::class, 'store'])->name('press-test-data.store');
    Route::get('/press-test-data/{id}', [App\Http\Controllers\PressTestDataController::class, 'edit'])->name('press-test-data.edit');
    Route::delete('/press-test-data/{id}', [App\Http\Controllers\PressTestDataController::class, 'destroy'])->name('press-test-data.destroy');


    /*------------------------------------------
    Dashboard
    Roles: Head Of Dapartement, Supervisor, Foreman
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman'])->group(function () {
        // Dashboard - Analisis GGA GGAS
        Route::get('/dashboard/gga-ggas', [App\Http\Controllers\Dashboard\GgaGgasController::class, 'index'])->name('dashboard.gga-ggas.index');

        // Dashboard - Blending Awal
        Route::get('/dashboard/blending-awal', [App\Http\Controllers\Dashboard\BlendingAwalController::class, 'index'])->name('dashboard.blending-awal.index');

        // Dashboard - Blending After Adjust
        Route::get('/dashboard/blending-after-adjust', [App\Http\Controllers\Dashboard\BlendingAfterAdjustController::class, 'index'])->name('dashboard.blending-after-adjust.index');

        // Monitoring - Turun Blending
        Route::get('/dashboard/monitoring-turun-blending', [App\Http\Controllers\Dashboard\MonitoringTurunBlendingController::class, 'index'])->name('dashboard.monitoring-turun-blending.index');

        // Monitoring - Pasteurisasi
        Route::get('/dashboard/monitoring-pasteurisasi', [App\Http\Controllers\Dashboard\MonitoringPasteurisasiController::class, 'index'])->name('dashboard.monitoring-pasteurisasi.index');

        // Monitoring - Storage Kimia
        Route::get('/dashboard/monitoring-storage-kimia', [App\Http\Controllers\Dashboard\MonitoringStorageKimiaController::class, 'index'])->name('dashboard.monitoring-storage-kimia.index');

        // Monitoring - Storage Mikro
        Route::get('/dashboard/monitoring-storage-mikro', [App\Http\Controllers\Dashboard\MonitoringStorageMikroController::class, 'index'])->name('dashboard.monitoring-storage-mikro.index');

        // Dashboard - Shelf Life
        Route::get('/dashboard/shelf-life', [App\Http\Controllers\Dashboard\ShelfLifeController::class, 'index'])->name('dashboard.shelf-life.index');
        Route::get('/dashboard/shelf-life/chart-data', [App\Http\Controllers\Dashboard\ShelfLifeController::class, 'getChartData'])->name('dashboard.shelf-life.chart-data');
        Route::get('/dashboard/shelf-life/kelompok-tanggal', [App\Http\Controllers\Dashboard\ShelfLifeController::class, 'getKelompokTanggal'])->name('dashboard.shelf-life.kelompok-tanggal');
        Route::get('/dashboard/shelf-life/filter-options', [App\Http\Controllers\Dashboard\ShelfLifeController::class, 'getFilterOptions'])->name('dashboard.shelf-life.filter-options');

        // Dashboard - Press Test Mesin
        Route::get('/dashboard/press-test-mesin', [App\Http\Controllers\Dashboard\PressTestMesinController::class, 'index'])->name('dashboard.press-test-mesin.index');
        Route::get('/dashboard/press-test-mesin/export', [App\Http\Controllers\Dashboard\PressTestMesinController::class, 'export'])->name('dashboard.press-test-mesin.export');

        // Dashboard - Timbangan Retail
        Route::get('/dashboard/timbangan-retail', [App\Http\Controllers\Dashboard\TimbanganRetailController::class, 'index'])->name('dashboard.timbangan-retail.index');
    });

    /*------------------------------------------
    Monitoring - On Going Kimia
    Roles: Head Of Dapartement, Supervisor, Foreman, Analis Kimia, Analis Field
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Analis Kimia,Analis Field'])->group(function () {
        Route::get('/monitoring-ongoing-kimia', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'index'])->name('monitoring-ongoing-kimia.index');
        Route::get('/monitoring-ongoing-kimia/edit/{id}', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'edit'])->name('monitoring-ongoing-kimia.edit');
        Route::get('/monitoring-ongoing-kimia/show/{id}', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'show'])->name('monitoring-ongoing-kimia.show');
        Route::get('/analisa/monitoring-ongoing-kimia/{id}', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'analisa'])->name('monitoring-ongoing-kimia.analisa');
        Route::post('/monitoring-ongoing-kimia', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'store'])->name('monitoring-ongoing-kimia.store');
        Route::post('/monitoring-ongoing-kimia/analisa', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'storeAnalisa'])->name('monitoring-ongoing-kimia.store.analisa');
        Route::delete('/monitoring-ongoing-kimia/{id}', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'destroy'])->name('monitoring-ongoing-kimia.destroy');
        Route::post('/monitoring-ongoing-kimia/get-po', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'getPoByDateAndStorage'])->name('monitoring-ongoing-kimia.get-po');
        Route::post('/monitoring-ongoing-kimia/get-variant', [App\Http\Controllers\MonitoringOnGoingKimiaController::class, 'getVariantByPo'])->name('monitoring-ongoing-kimia.get-variant');
    });

    /*------------------------------------------
    Monitoring - On Going Mikro
    Roles: Head Of Dapartement, Supervisor, Foreman, Analis Mikro, Analis Kimia, Analis Field
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Analis Mikro,Analis Kimia,Analis Field'])->group(function () {
        Route::get('/monitoring-ongoing-mikro', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'index'])->name('monitoring-ongoing-mikro.index');
        Route::get('/monitoring-ongoing-mikro/edit/{id}', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'edit'])->name('monitoring-ongoing-mikro.edit');
        Route::get('/monitoring-ongoing-mikro/show/{id}', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'show'])->name('monitoring-ongoing-mikro.show');
        Route::get('/analisa/monitoring-ongoing-mikro/{id}', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'analisa'])->name('monitoring-ongoing-mikro.analisa');
        Route::post('/monitoring-ongoing-mikro', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'store'])->name('monitoring-ongoing-mikro.store');
        Route::post('/monitoring-ongoing-mikro/analisa/mikro', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'storeAnalisaMikro'])->name('monitoring-ongoing-mikro.analisa.mikro');
        Route::post('/monitoring-ongoing-mikro/analisa/kimia', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'storeAnalisaKimia'])->name('monitoring-ongoing-mikro.analisa.kimia');
        Route::delete('/monitoring-ongoing-mikro/{id}', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'destroy'])->name('monitoring-ongoing-mikro.destroy');
        Route::post('/monitoring-ongoing-mikro/get-po', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'getPoByDateAndStorage'])->name('monitoring-ongoing-mikro.get-po');
        Route::post('/monitoring-ongoing-mikro/get-variant', [App\Http\Controllers\MonitoringOnGoingMikroController::class, 'getVariantByPo'])->name('monitoring-ongoing-mikro.get-variant');
    });

    /*------------------------------------------
    Scan
    Roles: Analis Kimia, Analis Mikro
    --------------------------------------------*/
    Route::middleware(['user-access:Analis Kimia,Analis Mikro'])->group(function () {
        Route::get('/scan', [App\Http\Controllers\ScanController::class, 'index'])->name('scan.index');
        Route::post('/scan', [App\Http\Controllers\ScanController::class, 'store'])->name('scan.store');
    });

    /*------------------------------------------
    Shelf Life - Main Index
    Roles: Head Of Dapartement, Supervisor, Foreman, Analis Kimia, Analis Mikro
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Helper,Analis Kimia,Analis Mikro'])->group(function () {
        Route::get('/shelf-life', [App\Http\Controllers\ShelfLife\ShelfLifeController::class, 'index'])->name('shelf-life.index');
    });

    /*------------------------------------------
    Shelf Life - Sample
    Roles: Head Of Dapartement, Supervisor, Foreman, Helper
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Helper'])->group(function () {
        Route::get('/shelf-life/sample', [App\Http\Controllers\ShelfLife\SampleController::class, 'index'])->name('shelf-life.sample.index');
        Route::get('/shelf-life/sample/edit/{id}', [App\Http\Controllers\ShelfLife\SampleController::class, 'edit'])->name('shelf-life.sample.edit');
        Route::post('/shelf-life/sample', [App\Http\Controllers\ShelfLife\SampleController::class, 'store'])->name('shelf-life.sample.store');
        Route::post('/shelf-life/sample/get-po', [App\Http\Controllers\ShelfLife\SampleController::class, 'getPoByDateAndStorage'])->name('shelf-life.sample.get-po');
        Route::delete('/shelf-life/sample/{id}', [App\Http\Controllers\ShelfLife\SampleController::class, 'destroy'])->name('shelf-life.sample.destroy');
        Route::get('/shelf-life/sample/show/{id}', [App\Http\Controllers\ShelfLife\SampleController::class, 'show'])->name('shelf-life.sample.show');
        Route::post('/shelf-life/sample/detail', [App\Http\Controllers\ShelfLife\SampleController::class, 'storeSamplingDetail'])->name('shelf-life.sample.detail.store');
        Route::get('/shelf-life/sample/detail/edit/{id}', [App\Http\Controllers\ShelfLife\SampleController::class, 'editSamplingDetail'])->name('shelf-life.sample.detail.edit');
        Route::delete('/shelf-life/sample/detail/{id}', [App\Http\Controllers\ShelfLife\SampleController::class, 'destroySamplingDetail'])->name('shelf-life.sample.detail.destroy');
    });

    /*------------------------------------------
    Shelf Life - Checksheet
    Roles: Head Of Dapartement, Supervisor, Foreman, Helper
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Helper'])->group(function () {
        Route::get('/shelf-life/checksheet', [App\Http\Controllers\ShelfLife\ChecksheetController::class, 'index'])->name('shelf-life.checksheet.index');
        Route::post('/shelf-life/checksheet/update-status', [App\Http\Controllers\ShelfLife\ChecksheetController::class, 'updateStatus'])->name('shelf-life.checksheet.update-status');
    });

    /*------------------------------------------
    Shelf Life - Analisis Kimia
    Roles: Head Of Dapartement, Supervisor, Foreman, Analis Kimia
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Analis Kimia'])->group(function () {
        Route::get('/shelf-life/analisis-kimia', [App\Http\Controllers\ShelfLife\AnalysisKimiaController::class, 'index'])->name('shelf-life.analysis-kimia.index');
        Route::get('/shelf-life/analisis-kimia/show/{id}', [App\Http\Controllers\ShelfLife\AnalysisKimiaController::class, 'show'])->name('shelf-life.analysis-kimia.show');
        Route::post('/shelf-life/analisis-kimia', [App\Http\Controllers\ShelfLife\AnalysisKimiaController::class, 'store'])->name('shelf-life.analysis-kimia.store');
        Route::get('/shelf-life/analisis-kimia/edit/{id}', [App\Http\Controllers\ShelfLife\AnalysisKimiaController::class, 'edit'])->name('shelf-life.analysis-kimia.edit');
    });

    /*------------------------------------------
    Shelf Life - Analisis Mikro
    Roles: Head Of Dapartement, Supervisor, Foreman, Analis Mikro
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Analis Mikro'])->group(function () {
        Route::get('/shelf-life/analisis-mikro', [App\Http\Controllers\ShelfLife\AnalysisMikroController::class, 'index'])->name('shelf-life.analysis-mikro.index');
        Route::get('/shelf-life/analisis-mikro/show/{id}', [App\Http\Controllers\ShelfLife\AnalysisMikroController::class, 'show'])->name('shelf-life.analysis-mikro.show');
        Route::post('/shelf-life/analisis-mikro', [App\Http\Controllers\ShelfLife\AnalysisMikroController::class, 'store'])->name('shelf-life.analysis-mikro.store');
        Route::get('/shelf-life/analisis-mikro/get-mikro', [App\Http\Controllers\ShelfLife\AnalysisMikroController::class, 'getMikroData'])->name('shelf-life.analysis-mikro.get-mikro');
    });

    /*------------------------------------------
    Shelf Life - Result
    Roles: Head Of Dapartement, Supervisor, Foreman
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman'])->group(function () {
        Route::get('/shelf-life/hasil', [App\Http\Controllers\ShelfLife\ResultController::class, 'index'])->name('shelf-life.result.index');
        Route::get('/shelf-life/hasil/get-data', [App\Http\Controllers\ShelfLife\ResultController::class, 'getData'])->name('shelf-life.result.get-data');
        Route::post('/shelf-life/hasil/get-po', [App\Http\Controllers\ShelfLife\ResultController::class, 'getPoByDateAndStorage'])->name('shelf-life.result.get-po');
    });

    /*------------------------------------------
    RMPM
    Roles: Head Of Dapartement, Supervisor, Foreman, Analis RM
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Analis RM'])->group(function () {
        Route::get('/rmpm', [App\Http\Controllers\RMPMController::class, 'index'])->name('rmpm.index');
        Route::get('/rmpm/konfirmasi/{id}', [App\Http\Controllers\RMPMController::class, 'getKonfirmasi'])->name('rmpm.konfirmasi');
        Route::post('/rmpm/konfirmasi/update', [App\Http\Controllers\RMPMController::class, 'updateKonfirmasi'])->name('rmpm.konfirmasi.update');
        Route::get('/rmpm/{id}', [App\Http\Controllers\RMPMController::class, 'show'])->name('rmpm.show');
        Route::get('/rmpm/qrcode/{id}', [App\Http\Controllers\RMPMController::class, 'getQRCode'])->name('rmpm.qrcode');
        Route::post('/rmpm', [App\Http\Controllers\RMPMController::class, 'store'])->name('rmpm.store');
        Route::post('/rmpm/update-disposisi/long-term', [App\Http\Controllers\RMPMController::class, 'updateDisposisiLongTerm'])->name('rmpm.update-disposisi.long-term');

        Route::post('analisa/rmpm/long-term', [App\Http\Controllers\RMPMController::class, 'storeLongTerm'])->name('rmpm.store.long-term');
        Route::post('analisa/rmpm/short-term', [App\Http\Controllers\RMPMController::class, 'storeShortTerm'])->name('rmpm.store.short-term');
        Route::post('analisa/rmpm/garam-gula', [App\Http\Controllers\RMPMController::class, 'storeGaramGula'])->name('rmpm.store.garam-gula');

        // Sampling - Kondisi Mobil
        Route::get('/sampling/kondisi_mobil/{id}', [App\Http\Controllers\Sampling\KondisiMobilController::class, 'show'])->name('sampling-kondisi-mobil.show');
        Route::post('/sampling/kondisi_mobil', [App\Http\Controllers\Sampling\KondisiMobilController::class, 'store'])->name('sampling-kondisi-mobil.store');
        Route::post('/sampling/kondisi_mobil/update', [App\Http\Controllers\Sampling\KondisiMobilController::class, 'update'])->name('sampling-kondisi-mobil.update');

        // Sampling - Dokumen
        Route::get('/sampling/dokumen/{id}', [App\Http\Controllers\Sampling\DokumenController::class, 'show'])->name('sampling-dokumen.show');
        Route::post('/sampling/dokumen', [App\Http\Controllers\Sampling\DokumenController::class, 'store'])->name('sampling-dokumen.store');
        Route::post('/sampling/dokumen/update', [App\Http\Controllers\Sampling\DokumenController::class, 'update'])->name('sampling-dokumen.update');

        // Sampling - Kemasan
        Route::get('/sampling/kemasan/{id}', [App\Http\Controllers\Sampling\KemasanController::class, 'show'])->name('sampling-kemasan.show');
        Route::post('/sampling/kemasan', [App\Http\Controllers\Sampling\KemasanController::class, 'store'])->name('sampling-kemasan.store');
        Route::post('/sampling/kemasan/update', [App\Http\Controllers\Sampling\KemasanController::class, 'update'])->name('sampling-kemasan.update');

        // Sampling - Raw
        Route::get('/sampling/raw/{id}', [App\Http\Controllers\Sampling\RawController::class, 'show'])->name('sampling-raw.show');
        Route::post('/sampling/raw', [App\Http\Controllers\Sampling\RawController::class, 'store'])->name('sampling-raw.store');
        Route::post('/sampling/raw/update', [App\Http\Controllers\Sampling\RawController::class, 'update'])->name('sampling-raw.update');
    });

    /*------------------------------------------
    Persiapan Masak
    Roles: Head Of Dapartement, Supervisor, Foreman, Operator
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Operator'])->group(function () {
        Route::get('/persiapan-masak', [App\Http\Controllers\ProductionBatchController::class, 'index'])->name('productionbatch.index');
        Route::get('/persiapan-masak/tambah', [App\Http\Controllers\ProductionBatchController::class, 'create'])->name('productionbatch.create');
        Route::post('/persiapan-masak', [App\Http\Controllers\ProductionBatchController::class, 'store'])->name('productionbatch.store');

        // GGA - GGAS
        Route::get('/gga-ggas', [App\Http\Controllers\GgaGgasController::class, 'index'])->name('gga-ggas.index');
        Route::post('/gga-ggas', [App\Http\Controllers\GgaGgasController::class, 'store'])->name('gga-ggas.store');
        Route::post('/gga-ggas/update', [App\Http\Controllers\GgaGgasController::class, 'update'])->name('gga-ggas.update');
        Route::get('/gga-ggas/{id}', [App\Http\Controllers\GgaGgasController::class, 'edit'])->name('gga-ggas.edit');
        Route::get('/gga-ggas/detail/{id}', [App\Http\Controllers\GgaGgasController::class, 'show'])->name('gga-ggas.show');
        Route::delete('/gga-ggas/{id}', [App\Http\Controllers\GgaGgasController::class, 'destroy'])->name('gga-ggas.destroy');
        Route::get('/gga-ggas/revisi/gga/{id}', [App\Http\Controllers\GgaGgasController::class, 'show_revisi_gga'])->name('gga-ggas.show_revisi_gga');
        Route::post('/gga-ggas/revisi/gga', [App\Http\Controllers\GgaGgasController::class, 'update_revisi_gga'])->name('gga-ggas.update_revisi_gga');
        Route::get('/gga-ggas/revisi/ggas/{id}', [App\Http\Controllers\GgaGgasController::class, 'show_revisi_ggas'])->name('gga-ggas.show_revisi_ggas');
        Route::post('/gga-ggas/revisi/ggas', [App\Http\Controllers\GgaGgasController::class, 'update_revisi_ggas'])->name('gga-ggas.update_revisi_ggas');

        // Blending Awal (Foreman/Operator Input)
        Route::get('/blending-awal', [App\Http\Controllers\BlendingAwalController::class, 'index'])->name('blending-awal.index');
        Route::get('/blending-awal/get-last-revisi', [App\Http\Controllers\BlendingAwalController::class, 'getLastRevisiBlendingAwal'])->name('blending-awal.getLastRevisiBlendingAwal');
        Route::get('/blending-awal/get-available-additional-batch', [App\Http\Controllers\BlendingAwalController::class, 'getAvailableAdditionalBatch'])->name('blending-awal.getAvailableAdditionalBatch');
        Route::get('/blending-awal/get-jalan-bareng', [App\Http\Controllers\BlendingAwalController::class, 'getJalanBareng'])->name('blending-awal.getJalanBareng');
        Route::get('/blending-awal/edit/{id}', [App\Http\Controllers\BlendingAwalController::class, 'edit'])->name('blending-awal.edit');
        Route::post('/blending-awal/update', [App\Http\Controllers\BlendingAwalController::class, 'update'])->name('blending-awal.update');
        Route::delete('/blending-awal/{id}', [App\Http\Controllers\BlendingAwalController::class, 'destroy'])->name('blending-awal.destroy');
        Route::get('/blending-awal/{id}', [App\Http\Controllers\BlendingAwalController::class, 'show'])->name('blending-awal.show');
        Route::post('/blending-awal', [App\Http\Controllers\BlendingAwalController::class, 'store'])->name('blending-awal.store');
        Route::post('/blending-awal/revisi', [App\Http\Controllers\BlendingAwalController::class, 'storeRevisi'])->name('blending-awal.storeRevisi');

        // Monitoring Turun Blending
        Route::get('/monitoring-turun-blending', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'index'])->name('monitoring-turun-blending.index');
        Route::get('/monitoring-turun-blending/get-last-revisi', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'getLastRevisi'])->name('monitoring-turun-blending.getLastRevisi');
        Route::get('/monitoring-turun-blending/get-available-additional-batch', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'getAvailableAdditionalBatch'])->name('monitoring-turun-blending.getAvailableAdditionalBatch');
        Route::get('/monitoring-turun-blending/get-jalan-bareng', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'getJalanBareng'])->name('monitoring-turun-blending.getJalanBareng');
        Route::get('/monitoring-turun-blending/edit/{id}', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'edit'])->name('monitoring-turun-blending.edit');
        Route::post('/monitoring-turun-blending/update', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'update'])->name('monitoring-turun-blending.update');
        Route::delete('/monitoring-turun-blending/{id}', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'destroy'])->name('monitoring-turun-blending.destroy');
        Route::get('/monitoring-turun-blending/{id}', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'show'])->name('monitoring-turun-blending.show');
        Route::post('/monitoring-turun-blending', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'store'])->name('monitoring-turun-blending.store');
        Route::post('/monitoring-turun-blending/revisi', [App\Http\Controllers\MonitoringTurunBlendingController::class, 'storeRevisi'])->name('monitoring-turun-blending.storeRevisi');

        // Monitoring Pasteurisasi
        Route::get('/monitoring-pasteurisasi', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'index'])->name('monitoring-pasteurisasi.index');
        Route::get('/monitoring-pasteurisasi/get-last-revisi', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'getLastRevisi'])->name('monitoring-pasteurisasi.getLastRevisi');
        Route::get('/monitoring-pasteurisasi/get-available-additional-batch', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'getAvailableAdditionalBatch'])->name('monitoring-pasteurisasi.getAvailableAdditionalBatch');
        Route::get('/monitoring-pasteurisasi/get-jalan-bareng', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'getJalanBareng'])->name('monitoring-pasteurisasi.getJalanBareng');
        Route::get('/monitoring-pasteurisasi/edit/{id}', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'edit'])->name('monitoring-pasteurisasi.edit');
        Route::post('/monitoring-pasteurisasi/update', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'update'])->name('monitoring-pasteurisasi.update');
        Route::delete('/monitoring-pasteurisasi/{id}', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'destroy'])->name('monitoring-pasteurisasi.destroy');
        Route::get('/monitoring-pasteurisasi/{id}', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'show'])->name('monitoring-pasteurisasi.show');
        Route::post('/monitoring-pasteurisasi', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'store'])->name('monitoring-pasteurisasi.store');
        Route::post('/monitoring-pasteurisasi/revisi', [App\Http\Controllers\MonitoringPasteurisasiController::class, 'storeRevisi'])->name('monitoring-pasteurisasi.storeRevisi');
    });

    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Analis Field'])->group(function () {
        // Monitoring Storage Kimia
        Route::get('/monitoring-storage-kimia/getBatchData', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'getBatchData'])->name('monitoring-storage-kimia.getBatchData');
        Route::get('/monitoring-storage-kimia', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'index'])->name('monitoring-storage-kimia.index');
        Route::get('/monitoring-storage-kimia/get-last-revisi', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'getLastRevisi'])->name('monitoring-storage-kimia.getLastRevisi');
        Route::get('/monitoring-storage-kimia/get-available-additional-batch', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'getAvailableAdditionalBatch'])->name('monitoring-storage-kimia.getAvailableAdditionalBatch');
        Route::get('/monitoring-storage-kimia/get-jalan-bareng', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'getJalanBareng'])->name('monitoring-storage-kimia.getJalanBareng');
        Route::get('/monitoring-storage-kimia/edit/{id}', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'edit'])->name('monitoring-storage-kimia.edit');
        Route::post('/monitoring-storage-kimia/update', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'update'])->name('monitoring-storage-kimia.update');
        Route::delete('/monitoring-storage-kimia/{id}', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'destroy'])->name('monitoring-storage-kimia.destroy');
        Route::get('/monitoring-storage-kimia/{id}', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'show'])->name('monitoring-storage-kimia.show');
        Route::post('/monitoring-storage-kimia', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'store'])->name('monitoring-storage-kimia.store');
        Route::post('/monitoring-storage-kimia/revisi', [App\Http\Controllers\MonitoringStorageKimiaController::class, 'storeRevisi'])->name('monitoring-storage-kimia.storeRevisi');
    });

    /*------------------------------------------
    GGA & GGAS
    Roles: Head Of Dapartement, Supervisor, Foreman, Analis Kimia
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Analis Kimia'])->group(function () {
        // Analisa - GGA
        Route::get('/gga/menu', [App\Http\Controllers\Analisa\GGaController::class, 'menu'])->name('gga.menu');
        Route::get('/gga', [App\Http\Controllers\Analisa\GGaController::class, 'index'])->name('gga.index');
        Route::get('/gga/formulasi/', [App\Http\Controllers\Analisa\GGaController::class, 'formulasi'])->name('gga.formulasi');
        Route::get('/gga/{id}', [App\Http\Controllers\Analisa\GGaController::class, 'show'])->name('gga.show');
        Route::get('/gga/edit/{id}', [App\Http\Controllers\Analisa\GGaController::class, 'edit'])->name('gga.edit');
        Route::post('/gga', [App\Http\Controllers\Analisa\GGaController::class, 'update'])->name('gga.update');
        Route::get('/scan/batch/gga/{id}', [App\Http\Controllers\Analisa\GGaController::class, 'show_batch'])->name('gga.show_batch');

        // Analisa - GGAS
        Route::get('/ggas', [App\Http\Controllers\Analisa\GgasController::class, 'index'])->name('ggas.index');
        Route::get('/ggas/formulasi/', [App\Http\Controllers\Analisa\GgasController::class, 'formulasi'])->name('ggas.formulasi');
        Route::get('/ggas/{id}', [App\Http\Controllers\Analisa\GgasController::class, 'show'])->name('ggas.show');
        Route::get('/ggas/batch/{id}', [App\Http\Controllers\Analisa\GgasController::class, 'show_batch'])->name('ggas.show_batch');
        Route::get('/ggas/edit/{id}', [App\Http\Controllers\Analisa\GgasController::class, 'edit'])->name('ggas.edit');
        Route::post('/ggas', [App\Http\Controllers\Analisa\GgasController::class, 'update'])->name('ggas.update');
        Route::get('/scan/batch/ggas/{id}', [App\Http\Controllers\Analisa\GgasController::class, 'show_batch'])->name('ggas.show_batch');

        // Analisa - Blending Awal - Kimia
        Route::get('/analisa/blending-awal/index', [App\Http\Controllers\Analisa\BlendingAwalController::class, 'index'])->name('analisa.blending-awal.index');
        Route::get('/analisa/blending-awal/formulasi', [App\Http\Controllers\Analisa\BlendingAwalController::class, 'formulasi'])->name('analisa.blending-awal.formulasi');
        Route::get('/analisa/blending-awal/edit/{id}', [App\Http\Controllers\Analisa\BlendingAwalController::class, 'edit'])->name('analisa.blending-awal.edit');
        Route::get('/analisa/blending-awal/show/{id}', [App\Http\Controllers\Analisa\BlendingAwalController::class, 'show'])->name('analisa.blending-awal.show');
        Route::post('/analisa/blending-awal/update', [App\Http\Controllers\Analisa\BlendingAwalController::class, 'update'])->name('analisa.blending-awal.update');
        Route::get('scan/batch/blending-awal/{id}', [App\Http\Controllers\Analisa\BlendingAwalController::class, 'show_batch'])->name('analisa.blending-awal.show_batch');

        // Analisa - Monitoring Turun Blending
        Route::get('/analisa/monitoring-turun-blending', [App\Http\Controllers\Analisa\MonitoringTurunBlendingController::class, 'index'])->name('analisa.monitoring-turun-blending.index');
        Route::get('/analisa/monitoring-turun-blending/edit/{id}', [App\Http\Controllers\Analisa\MonitoringTurunBlendingController::class, 'edit'])->name('analisa.monitoring-turun-blending.edit');
        Route::get('/analisa/monitoring-turun-blending/show/{id}', [App\Http\Controllers\Analisa\MonitoringTurunBlendingController::class, 'show'])->name('analisa.monitoring-turun-blending.show');
        Route::post('/analisa/monitoring-turun-blending/update', [App\Http\Controllers\Analisa\MonitoringTurunBlendingController::class, 'update'])->name('analisa.monitoring-turun-blending.update');
        Route::get('/scan/batch/monitoring-turun-blending/{id}', [App\Http\Controllers\Analisa\MonitoringTurunBlendingController::class, 'show_batch'])->name('analisa.monitoring-turun-blending.show_batch');

        // Analisa - Monitoring Pasteurisasi
        Route::get('/analisa/monitoring-pasteurisasi', [App\Http\Controllers\Analisa\MonitoringPasteurisasiController::class, 'index'])->name('analisa.monitoring-pasteurisasi.index');
        Route::get('/analisa/monitoring-pasteurisasi/edit/{id}', [App\Http\Controllers\Analisa\MonitoringPasteurisasiController::class, 'edit'])->name('analisa.monitoring-pasteurisasi.edit');
        Route::get('/analisa/monitoring-pasteurisasi/show/{id}', [App\Http\Controllers\Analisa\MonitoringPasteurisasiController::class, 'show'])->name('analisa.monitoring-pasteurisasi.show');
        Route::post('/analisa/monitoring-pasteurisasi/update', [App\Http\Controllers\Analisa\MonitoringPasteurisasiController::class, 'update'])->name('analisa.monitoring-pasteurisasi.update');
        Route::get('/scan/batch/monitoring-pasteurisasi/{id}', [App\Http\Controllers\Analisa\MonitoringPasteurisasiController::class, 'show_batch'])->name('analisa.monitoring-pasteurisasi.show_batch');

        // Analisa - Monitoring Storage Kimia
        Route::get('/analisa/monitoring-storage-kimia', [App\Http\Controllers\Analisa\MonitoringStorageKimiaController::class, 'index'])->name('analisa.monitoring-storage-kimia.index');
        Route::get('/analisa/monitoring-storage-kimia/edit/{id}', [App\Http\Controllers\Analisa\MonitoringStorageKimiaController::class, 'edit'])->name('analisa.monitoring-storage-kimia.edit');
        Route::get('/analisa/monitoring-storage-kimia/show/{id}', [App\Http\Controllers\Analisa\MonitoringStorageKimiaController::class, 'show'])->name('analisa.monitoring-storage-kimia.show');
        Route::post('/analisa/monitoring-storage-kimia/update', [App\Http\Controllers\Analisa\MonitoringStorageKimiaController::class, 'update'])->name('analisa.monitoring-storage-kimia.update');
        Route::get('/scan/batch/monitoring-storage-kimia/{id}', [App\Http\Controllers\Analisa\MonitoringStorageKimiaController::class, 'show_batch'])->name('analisa.monitoring-storage-kimia.show_batch');

        // Analis - Monitoring Before Use
        Route::get('/scan/monitoring-storage-before-use/{id}', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'analisa'])->name('monitoring-storage-before-use.analisa');
        Route::post('/analisa/monitoring-storage-before-use', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'storeAnalisa'])->name('monitoring-storage-before-use.storeAnalisa');
    });

    /*------------------------------------------
    Monitoring Pasteurisasi & Storage
    Roles: Head Of Dapartement, Supervisor, Foreman, Analis Kimia, Analis Field, Analis Mikro
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman,Analis Kimia,Analis Field,Analis Mikro'])->group(function () {
        // Menu Blending
        Route::get('/analisa/blending-awal/menu', [App\Http\Controllers\Analisa\BlendingAwalController::class, 'menu'])->name('analisa.blending-awal.menu');

        // Menu Monitoring
        Route::get('/analisa/monitoring-turun-blending/menu', [App\Http\Controllers\Analisa\MonitoringTurunBlendingController::class, 'menu'])->name('analisa.monitoring-turun-blending.menu');

        // Analisa - Monitoring Storage Mikro
        Route::get('/analisa/monitoring-storage-mikro/index', [App\Http\Controllers\Analisa\MonitoringStorageMikroController::class, 'index'])->name('analisa.monitoring-storage-mikro.index');
        Route::get('/analisa/monitoring-storage-mikro/get-blending-data', [App\Http\Controllers\Analisa\MonitoringStorageMikroController::class, 'getData'])->name('analisa.monitoring-storage-mikro.getData');
        Route::get('/analisa/monitoring-storage-mikro/edit/{id}', [App\Http\Controllers\Analisa\MonitoringStorageMikroController::class, 'edit'])->name('analisa.monitoring-storage-mikro.edit');
        Route::get('/analisa/monitoring-storage-mikro/show/{id}', [App\Http\Controllers\Analisa\MonitoringStorageMikroController::class, 'show'])->name('analisa.monitoring-storage-mikro.show');
        Route::post('/analisa/monitoring-storage-mikro/update', [App\Http\Controllers\Analisa\MonitoringStorageMikroController::class, 'update'])->name('analisa.monitoring-storage-mikro.update');
        Route::get('/scan/batch/monitoring-storage-mikro/{id}', [App\Http\Controllers\Analisa\MonitoringStorageMikroController::class, 'show_batch'])->name('analisa.monitoring-storage-mikro.show_batch');

        // Analisa - Blending Awal - Mikro
        Route::get('/analisa/blending-awal/mikro/index', [App\Http\Controllers\Analisa\BlendingAwalMikroController::class, 'index'])->name('analisa.blending-awal-mikro.index');
        Route::get('/analisa/blending-awal/mikro/get-blending-data', [App\Http\Controllers\Analisa\BlendingAwalMikroController::class, 'getBlendingData'])->name('analisa.blending-awal-mikro.getBlendingData');
        Route::get('/analisa/blending-awal/mikro/edit/{id}', [App\Http\Controllers\Analisa\BlendingAwalMikroController::class, 'edit'])->name('analisa.blending-awal-mikro.edit');
        Route::get('/analisa/blending-awal/mikro/show/{id}', [App\Http\Controllers\Analisa\BlendingAwalMikroController::class, 'show'])->name('analisa.blending-awal-mikro.show');
        Route::post('/analisa/blending-awal/mikro/update', [App\Http\Controllers\Analisa\BlendingAwalMikroController::class, 'update'])->name('analisa.blending-awal-mikro.update');
        Route::get('/scan/batch/blending-awal-mikro/{id}', [App\Http\Controllers\Analisa\BlendingAwalMikroController::class, 'show_batch'])->name('analisa.blending-awal-mikro.show_batch');

        // Monitoring Storage Before Use
        Route::get('/monitoring-storage-before-use', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'index'])->name('monitoring-storage-before-use.index');
        Route::get('/monitoring-storage-before-use/show/{id}', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'show'])->name('monitoring-storage-before-use.show');
        Route::get('/monitoring-storage-before-use/edit/{id}', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'edit'])->name('monitoring-storage-before-use.edit');
        Route::post('/monitoring-storage-before-use', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'store'])->name('monitoring-storage-before-use.store');
        Route::delete('/monitoring-storage-before-use/{id}', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'destroy'])->name('monitoring-storage-before-use.destroy');
        Route::post('monitoring-storage-before-use/{id}/approve', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'approve'])->name('monitoring-storage-before-use.approve');
        Route::post('monitoring-storage-before-use/{id}/flushing', [App\Http\Controllers\MonitoringStorageBeforeUseController::class, 'flushing'])->name('monitoring-storage-before-use.flushing');

        // Monitoring Daily Tank
        Route::get('/monitoring-daily-tank/menu', [App\Http\Controllers\MonitoringDailyTankController::class, 'menu'])->name('monitoring-daily-tank.menu');
        Route::get('/monitoring-daily-tank', [App\Http\Controllers\MonitoringDailyTankController::class, 'index'])->name('monitoring-daily-tank.index');
        Route::post('/monitoring-daily-tank', [App\Http\Controllers\MonitoringDailyTankController::class, 'store'])->name('monitoring-daily-tank.store');
        Route::get('/monitoring-daily-tank/show/{id}', [App\Http\Controllers\MonitoringDailyTankController::class, 'show'])->name('monitoring-daily-tank.show');
        Route::get('/monitoring-daily-tank/edit/{id}', [App\Http\Controllers\MonitoringDailyTankController::class, 'edit'])->name('monitoring-daily-tank.edit');
        Route::delete('/monitoring-daily-tank/{id}', [App\Http\Controllers\MonitoringDailyTankController::class, 'destroy'])->name('monitoring-daily-tank.destroy');
        Route::post('/monitoring-daily-tank/get-po', [App\Http\Controllers\MonitoringDailyTankController::class, 'getPoByDateAndStorage'])->name('monitoring-daily-tank.get-po');

        // Analisa - Monitoring Daily Tank - Kimia
        Route::get('/analisa/monitoring-daily-tank/kimia/get-data', [App\Http\Controllers\Analisa\MonitoringDailyTankKimiaController::class, 'getData'])->name('analisa.monitoring-daily-tank-kimia.getData');
        Route::get('/analisa/monitoring-daily-tank-kimia/{id}', [App\Http\Controllers\Analisa\MonitoringDailyTankKimiaController::class, 'show'])->name('analisa.monitoring-daily-tank-kimia.show');
        Route::post('/analisa/monitoring-daily-tank/kimia/update', [App\Http\Controllers\Analisa\MonitoringDailyTankKimiaController::class, 'update'])->name('analisa.monitoring-daily-tank-kimia.update');

        // Analisa - Monitoring Daily Tank - Mikro
        Route::get('/analisa/monitoring-daily-tank/mikro/get-data', [App\Http\Controllers\Analisa\MonitoringDailyTankMikroController::class, 'getData'])->name('analisa.monitoring-daily-tank-mikro.getData');
        Route::get('/analisa/monitoring-daily-tank-mikro/{id}', [App\Http\Controllers\Analisa\MonitoringDailyTankMikroController::class, 'show'])->name('analisa.monitoring-daily-tank-mikro.show');
        Route::post('/analisa/monitoring-daily-tank/mikro/update', [App\Http\Controllers\Analisa\MonitoringDailyTankMikroController::class, 'update'])->name('analisa.monitoring-daily-tank-mikro.update');
    });

    /*------------------------------------------
    Notifikasi
    Roles: Head Of Dapartement, Supervisor, Foreman
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor,Foreman'])->group(function () {
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'unreadNotifications'])->name('notifications.unreadNotifications');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::post('/notifications/mark-read/{id}', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    });

    /*------------------------------------------
    Master Data - Pengguna
    Roles: Head Of Dapartement, Supervisor Only
    --------------------------------------------*/
    Route::middleware(['user-access:Head Of Dapartement,Supervisor'])->group(function () {
        Route::get('/pengguna', [App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [App\Http\Controllers\UsersController::class, 'store'])->name('users.store');
        Route::get('/pengguna/{id}', [App\Http\Controllers\UsersController::class, 'edit'])->name('users.edit');
        Route::delete('/pengguna/{id}', [App\Http\Controllers\UsersController::class, 'destroy'])->name('users.destroy');
    });

    /*------------------------------------------
    Master Data - Warna
    Roles: Foreman Only
    --------------------------------------------*/
    Route::middleware(['user-access:Foreman'])->group(function () {
        Route::get('/warna', [App\Http\Controllers\ColorController::class, 'index'])->name('colors.index');
        Route::post('/warna', [App\Http\Controllers\ColorController::class, 'store'])->name('colors.store');
        Route::get('/warna/{id}', [App\Http\Controllers\ColorController::class, 'edit'])->name('colors.edit');
        Route::delete('/warna/{id}', [App\Http\Controllers\ColorController::class, 'destroy'])->name('colors.destroy');
    });
});
