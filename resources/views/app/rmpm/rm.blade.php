@extends('layouts.component.main')
@section('title', 'Raw Material')
@section('content')
    <style>
        .rm-page {
            --rm-primary: #16b89f;
            --rm-primary-dark: #0d927d;
            --rm-indigo: #4f46e5;
            --rm-orange: #f97316;
            --rm-cyan: #0891b2;
            --rm-border: #e5e7eb;
            --rm-muted: #64748b;
            --rm-dark: #0f172a;
            --rm-soft: #f8fafc;
        }

        .rm-page .page-title-box h4 {
            font-weight: 700;
            color: var(--rm-dark);
        }

        .rm-page .rm-hero {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--rm-primary) 0%, var(--rm-primary-dark) 100%);
            box-shadow: 0 14px 30px rgba(13, 146, 125, .18);
        }

        .rm-page .rm-hero-icon {
            width: 76px;
            height: 76px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: rgba(255, 255, 255, .18);
            color: #fff;
            font-size: 40px;
        }

        .rm-page .rm-hero-label {
            display: block;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, .74);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .rm-page .rm-process-card {
            min-height: 255px;
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .07);
            transition: transform .22s ease, box-shadow .22s ease;
        }

        .rm-page .rm-process-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 38px rgba(15, 23, 42, .13);
        }

        .rm-page .rm-process-card.incoming {
            border-top: 5px solid #14b8a6;
        }

        .rm-page .rm-process-card.sampling {
            border-top: 5px solid #f97316;
        }

        .rm-page .rm-process-card.analysis {
            border-top: 5px solid #06b6d4;
        }

        .rm-page .rm-step-badge {
            display: inline-flex;
            padding: 6px 12px;
            border-radius: 50px;
            background: #eef2ff;
            color: var(--rm-indigo);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .rm-page .rm-process-icon {
            width: 72px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            margin-top: 20px;
            font-size: 37px;
        }

        .rm-page .incoming .rm-process-icon {
            background: #ecfdf5;
            color: #14b8a6;
        }

        .rm-page .sampling .rm-process-icon {
            background: #fff7ed;
            color: #f97316;
        }

        .rm-page .analysis .rm-process-icon {
            background: #ecfeff;
            color: #0891b2;
        }

        .rm-page .rm-process-action {
            margin-top: auto;
            padding-top: 18px;
        }

        .rm-page .rm-process-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 0;
            background: transparent;
            padding: 0;
            font-weight: 700;
            color: #475569;
        }

        .rm-page .rm-process-btn:hover {
            color: var(--rm-indigo);
        }

        .rm-page .rm-data-toggle {
            border: 1px solid var(--rm-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .05);
            padding: 18px 20px;
        }

        .rm-page .rm-data-card {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .07);
        }

        .rm-page .rm-data-card .card-header {
            background: #fff;
            border-bottom: 1px solid #eef2f7;
            padding: 20px 22px;
        }

        .rm-page .rm-data-card .card-body {
            padding: 22px;
        }

        .rm-page .rm-filter-panel {
            padding: 18px;
            margin-bottom: 22px;
            border: 1px solid var(--rm-border);
            border-radius: 16px;
            background: var(--rm-soft);
        }

        .rm-page .form-control,
        .rm-page .form-select {
            min-height: 42px;
            border-radius: 10px;
            border-color: #dbe3ee;
            box-shadow: none;
        }

        .rm-page .form-control:focus,
        .rm-page .form-select:focus {
            border-color: rgba(79, 70, 229, .55);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .08);
        }

        .rm-page .btn {
            border-radius: 10px;
            font-weight: 700;
        }

        .rm-page .rm-table-wrap {
            border: 1px solid var(--rm-border);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }

        .rm-page #datatable thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 12px 13px;
        }

        .rm-page #datatable tbody td {
            padding: 12px 13px;
            vertical-align: middle;
            border-color: #eef2f7;
        }

        .rm-page .modal-content {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .2);
        }



        .rm-page .rm-process-column {
            transition: all .25s ease;
        }

        .rm-page .rm-process-column.is-hidden {
            display: none;
        }

        .rm-page .rm-process-card.incoming.is-expanded {
            min-height: auto;
        }

        .rm-page .rm-process-card.incoming.is-expanded:hover {
            transform: none;
        }

        .rm-page .incoming-form-inside {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eef2f7;
        }

        .rm-page .incoming-form-inside .form-label {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 7px;
        }

        .rm-page .incoming-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
        }


        .rm-page .rm-card-column {
            transition: all .25s ease;
        }

        .rm-page .rm-card-column.is-hidden {
            display: none;
        }

        .rm-page .rm-process-card.is-expanded {
            min-height: auto;
        }

        .rm-page .rm-process-card.is-expanded:hover {
            transform: none;
        }

        .rm-page .rm-card-detail {
            margin-top: 22px;
            padding-top: 22px;
            border-top: 1px solid #eef2f7;
        }

        .rm-page .rm-card-detail-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .rm-page .rm-card-detail .form-label {
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
        }

        .rm-page .rm-card-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
        }

        .rm-page .rm-data-detail .rm-filter-panel {
            margin-bottom: 18px;
        }

        @media (max-width: 575.98px) {
            .rm-page .rm-hero-icon {
                width: 64px;
                height: 64px;
                font-size: 32px;
            }

            .rm-page .rm-process-card {
                min-height: auto;
            }
        }
    </style>

    <div class="page-content rm-page">
        <div class="container-fluid">

            {{-- PAGE TITLE --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0">Raw Material</h4>
                            <p class="text-muted mb-0 mt-1">
                                Pilih proses Raw Material yang akan dikerjakan.
                            </p>
                        </div>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('rmpm.index') }}">RMPM</a>
                                </li>
                                <li class="breadcrumb-item active">RM</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HERO --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card rm-hero">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="rm-hero-icon">
                                    <i class="mdi mdi-flask-outline"></i>
                                </div>

                                <div class="ms-4">
                                    <span class="rm-hero-label">RAW MATERIAL</span>
                                    <h2 class="text-white mb-1">RM</h2>
                                    <p class="text-white-50 mb-0">
                                        Penerimaan, sampling, dan analisa bahan baku.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PILIH PROSES --}}
            <div class="row mb-2">
                <div class="col-12">
                    <div class="d-flex align-items-end justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase">Alur Kerja</span>
                            <h5 class="mb-0 mt-1 fw-bold">Pilih proses yang akan dikerjakan</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4" id="rmProcessRow">

                {{-- INPUT INCOMING --}}
                <div class="col-xl-6 col-lg-6 col-md-6 rm-card-column" id="incomingCardColumn">
                    <div class="card rm-process-card incoming h-100" id="incomingProcessCard">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="rm-step-badge">Raw Material</span>

                                    <div class="rm-process-icon">
                                        <i class="mdi mdi-truck-delivery-outline"></i>
                                    </div>

                                    <h4 class="text-dark mt-4 mb-2">Input Incoming</h4>

                                    <p class="text-muted mb-0">
                                        Input identitas dan data kedatangan Raw Material.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-light d-none"
                                    id="btnCloseIncomingCard"
                                    title="Tutup"
                                >
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>

                            <div class="rm-process-action" id="incomingOpenAction">
                                <button type="button" id="btnOpenIncomingCard" class="rm-process-btn">
                                    Tambah Incoming
                                    <i class="mdi mdi-arrow-right"></i>
                                </button>
                            </div>

                            {{-- FORM BENAR-BENAR ADA DI DALAM CARD INPUT INCOMING --}}
                            <div class="collapse rm-card-detail" id="incomingCardDetail">
                                <div class="rm-card-detail-header">
                                    <div>
                                        <h5 class="fw-bold mb-1">Tambah Incoming Raw Material</h5>
                                        <p class="text-muted small mb-0">
                                            Lengkapi identitas kedatangan Raw Material.
                                        </p>
                                    </div>
                                </div>

                                <form id="form">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="jenis" class="form-label">
                                                Jenis Bahan <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" name="jenis" id="jenis">
                                                <option value="">-- Pilih Jenis Bahan --</option>
                                                <option value="Gula Tebu">Gula Tebu</option>
                                                <option value="Gula Kelapa">Gula Kelapa</option>
                                                <option value="Gula">Gula</option>
                                                <option value="Garam">Garam</option>
                                            </select>
                                            <small class="text-danger errorJenis"></small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="tanggal_kedatangan" class="form-label">
                                                Tanggal & Jam Kedatangan <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="datetime-local"
                                                class="form-control"
                                                id="tanggal_kedatangan"
                                                name="tanggal_kedatangan"
                                                value="{{ now()->format('Y-m-d\TH:i') }}"
                                            >
                                            <small class="text-danger errorTanggalKedatangan"></small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="supplier" class="form-label">
                                                Supplier / Manufactur <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="supplier" name="supplier">
                                            <small class="text-danger errorSupplier"></small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="asal_bahan" class="form-label">
                                                Asal Bahan <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="asal_bahan" name="asal_bahan">
                                            <small class="text-danger errorAsalBahan"></small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="no_plat" class="form-label">
                                                No Plat <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="no_plat" name="no_plat">
                                            <small class="text-danger errorNoPlat"></small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="no_spb" class="form-label">
                                                No SPB <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" class="form-control" id="no_spb" name="no_spb">
                                            <small class="text-danger errorNoSPB"></small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="jumlah_kedatangan" class="form-label">
                                                Jumlah Kedatangan (kg) <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="jumlah_kedatangan"
                                                name="jumlah_kedatangan"
                                                placeholder="Dalam kilogram"
                                            >
                                            <small class="text-danger errorJumlahKedatangan"></small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="lot_batch" class="form-label">
                                                Lot / Batch <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="lot_batch" name="lot_batch">
                                            <small class="text-danger errorLotBatch"></small>
                                        </div>
                                    </div>

                                    <div class="rm-card-actions">
                                        <button type="button" class="btn btn-light px-4" id="btnCancelIncomingCard">
                                            Batal
                                        </button>

                                        <button type="submit" class="btn btn-primary px-4" id="save">
                                            <i class="mdi mdi-content-save-outline me-1"></i>
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DATA RAW MATERIAL --}}
                <div class="col-xl-6 col-lg-6 col-md-6 rm-card-column" id="dataCardColumn">
                    <div class="card rm-process-card analysis h-100" id="dataProcessCard">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="rm-step-badge">Raw Material</span>

                                    <div class="rm-process-icon">
                                        <i class="mdi mdi-format-list-bulleted-square"></i>
                                    </div>

                                    <h4 class="text-dark mt-4 mb-2">Data Raw Material</h4>

                                    <p class="text-muted mb-0">
                                        Lihat seluruh data incoming Raw Material dan lanjutkan proses yang sudah ada.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-light d-none"
                                    id="btnCloseDataCard"
                                    title="Tutup"
                                >
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>

                            <div class="rm-process-action" id="dataOpenAction">
                                <button type="button" id="btnOpenDataCard" class="rm-process-btn">
                                    Lihat Data Raw Material
                                    <i class="mdi mdi-arrow-right"></i>
                                </button>
                            </div>

                            {{-- TABEL BENAR-BENAR ADA DI DALAM CARD DATA RAW MATERIAL --}}
                            <div class="collapse rm-card-detail rm-data-detail" id="dataCardDetail">
                                <div class="rm-card-detail-header">
                                    <div>
                                        <h5 class="fw-bold mb-1">Daftar Raw Material</h5>
                                        <p class="text-muted small mb-0">
                                            Pilih data melalui tombol Lihat untuk melanjutkan proses yang sudah ada.
                                        </p>
                                    </div>
                                </div>

                                <div class="rm-filter-panel">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-12 col-md-2">
                                            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                                            <input type="date" id="start_date" class="form-control">
                                        </div>

                                        <div class="col-12 col-md-2">
                                            <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                                            <input type="date" id="end_date" class="form-control">
                                        </div>

                                        <div class="col-12 col-md-3">
                                            <label for="filter_jenis" class="form-label fw-semibold">Jenis</label>
                                            <select id="filter_jenis" class="form-select">
                                                <option value="">-- Semua --</option>
                                                <option value="Gula Tebu">Gula Tebu</option>
                                                <option value="Gula Kelapa">Gula Kelapa</option>
                                                <option value="Gula">Gula</option>
                                                <option value="Garam">Garam</option>
                                            </select>
                                        </div>

                                        <div class="col-6 col-md-2">
                                            <button type="button" id="btnFilter" class="btn btn-primary w-100">
                                                <i class="mdi mdi-filter-variant me-1"></i> Filter
                                            </button>
                                        </div>

                                        <div class="col-6 col-md-1">
                                            <button type="button" id="btnReset" class="btn btn-light w-100">
                                                <i class="mdi mdi-refresh"></i>
                                            </button>
                                        </div>

                                        <div class="col-12 col-md-2">
                                            <button type="button" id="btnAdd" class="btn btn-success w-100">
                                                <i class="mdi mdi-plus me-1"></i> Tambah Data
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="rm-table-wrap">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table nowrap align-middle" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>No SPB</th>
                                                    <th>Jenis</th>
                                                    <th>Supplier</th>
                                                    <th>Tanggal Kedatangan</th>
                                                    <th>Asal Bahan</th>
                                                    <th>Jumlah Kedatangan</th>
                                                    <th>Selesai Analisa</th>
                                                    <th>Keterangan</th>
                                                    <th>QR Code</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- QR modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-sm">
                <div class="modal-header bg-light py-2">
                    <h6 class="modal-title" id="qrModalLabel">QR Code</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-3" id="qrPrintArea">
                    <div id="qrImageArea">
                        {{-- QR diinjeksi via JS --}}
                    </div>
                    <div class="mt-2 small text-muted" id="qrLabelText"></div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button onclick="printQR('qrPrintArea')" class="btn btn-sm btn-primary">
                        <span class="mdi mdi-printer"></span> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function printQR(id) {
            const printArea = document.getElementById(id);
            if (!printArea) { Swal.fire({ icon: 'error', title: 'Error', text: 'Area print tidak ditemukan' }); return; }
            const qrImage = printArea.querySelector('img');
            const qrLabel = printArea.querySelector('.small.text-muted');
            if (!qrImage) { Swal.fire({ icon: 'error', title: 'Error', text: 'QR Code tidak ditemukan' }); return; }
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (isMobile) { printQRMobile(qrImage, qrLabel); } else { printQRDesktop(qrImage, qrLabel); }
        }

        function printQRDesktop(qrImage, qrLabel) {
            const printWindow = window.open('', '_blank', 'width=320,height=400');
            if (!printWindow) { Swal.fire({ icon: 'error', title: 'Pop-up Diblokir', text: 'Mohon izinkan pop-up untuk print.' }); return; }
            printWindow.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Print QR</title><style>@page{size:75mm 100mm;margin:0;}*{margin:0;padding:0;box-sizing:border-box;}html,body{width:75mm;height:100mm;margin:0 auto;font-family:Arial,sans-serif;background:white;-webkit-print-color-adjust:exact;print-color-adjust:exact;}.container{width:75mm;height:100mm;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:4mm;}.qr-image{width:58mm;height:58mm;display:block;flex-shrink:0;}.qr-label{font-size:8pt;color:#000;word-wrap:break-word;line-height:1.4;margin-top:3mm;width:100%;overflow:hidden;}@media print{html,body{width:75mm;height:100mm;overflow:hidden;}}</style></head><body><div class="container"><img src="${qrImage.src}" alt="QR" class="qr-image"><div class="qr-label"><strong>${qrLabel ? qrLabel.textContent.trim() : ''}</strong></div></div></body></html>`);
            printWindow.document.close();
            printWindow.onload = function() { setTimeout(function() { printWindow.focus(); printWindow.print(); setTimeout(function() { printWindow.close(); }, 500); }, 250); };
        }

        function printQRMobile(qrImage, qrLabel) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = 302; canvas.height = 378;
            ctx.fillStyle = 'white'; ctx.fillRect(0, 0, canvas.width, canvas.height);
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                const qrSize = 220, qrX = (canvas.width - qrSize) / 2, qrY = (canvas.height - qrSize) / 2 - 15;
                ctx.drawImage(img, qrX, qrY, qrSize, qrSize);
                ctx.fillStyle = 'black'; ctx.font = 'bold 11px Arial'; ctx.textAlign = 'center';
                const labelText = qrLabel ? qrLabel.textContent.trim() : '';
                const maxWidth = 270, lineHeight = 15, words = labelText.split('/');
                let line = '', y = qrY + qrSize + 20;
                words.forEach((word, index) => {
                    if (index > 0) line += '/';
                    const testLine = line + word;
                    if (ctx.measureText(testLine).width > maxWidth && index > 0) { ctx.fillText(line, canvas.width / 2, y); line = word; y += lineHeight; } else { line = testLine; }
                });
                ctx.fillText(line, canvas.width / 2, y);
                canvas.toBlob(function(blob) {
                    if (navigator.share && navigator.canShare && navigator.canShare({ files: [new File([blob], 'qr.png', { type: 'image/png' })] })) {
                        navigator.share({ files: [new File([blob], 'qr-code.png', { type: 'image/png' })], title: 'Print QR Code' }).catch(() => fallbackPrint(blob));
                    } else { fallbackPrint(blob); }
                }, 'image/png');
            };
            img.onerror = function() { Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memuat QR code' }); };
            img.src = qrImage.src;
        }

        function fallbackPrint(blob) {
            const url = URL.createObjectURL(blob);
            const printWindow = window.open(url, '_blank');
            if (!printWindow) { Swal.fire({ icon: 'error', title: 'Pop-up Diblokir', text: 'Mohon izinkan pop-up untuk print.' }); return; }
            printWindow.onload = function() { setTimeout(function() { printWindow.print(); }, 500); };
        }

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rmpm.rm') }}",

                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.jenis = $('#filter_jenis').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',        name: 'DT_RowIndex',        orderable: false, searchable: false },
                    { data: 'no_spb',              name: 'no_spb' },
                    { data: 'jenis',               name: 'jenis' },
                    { data: 'supplier',            name: 'supplier' },
                    { data: 'tanggal_kedatangan',  name: 'tanggal_kedatangan' },
                    { data: 'asal_bahan',          name: 'asal_bahan' },
                    { data: 'jumlah_kedatangan',   name: 'jumlah_kedatangan' },
                    { data: 'status',              name: 'status',    orderable: false, searchable: false },
                    {
                        // Kolom Keterangan — tampilkan teks jika ada, dash jika kosong
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';
                            // Potong jika terlalu panjang, tampilkan tooltip untuk full text
                            const short = data.length > 60 ? data.substring(0, 60) + '…' : data;
                            return `<span title="${data.replace(/"/g, '&quot;')}" style="cursor:default;">${short}</span>`;
                        }
                    },
                    { data: 'qr_code',  name: 'qr_code',  orderable: false, searchable: false },
                    { data: 'action',   name: 'action',   orderable: false, searchable: false },
                ]
            });

            // INPUT INCOMING: card melebar dan form tampil di dalam card.
            function openIncomingCard() {
                const detailElement = document.getElementById('incomingCardDetail');
                const detailCollapse = bootstrap.Collapse.getOrCreateInstance(detailElement, {
                    toggle: false
                });

                $('#form').trigger('reset');
                $('#tanggal_kedatangan').val("{{ now()->format('Y-m-d\TH:i') }}");
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.text-danger').html('');

                $('#incomingCardColumn')
                    .removeClass('col-xl-6 col-lg-6 col-md-6')
                    .addClass('col-12');

                $('#incomingProcessCard').addClass('is-expanded');
                $('#dataCardColumn').addClass('is-hidden');
                $('#incomingOpenAction').addClass('d-none');
                $('#btnCloseIncomingCard').removeClass('d-none');

                detailCollapse.show();

                setTimeout(function() {
                    document.getElementById('incomingProcessCard').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 160);
            }

            function closeIncomingCard() {
                const detailElement = document.getElementById('incomingCardDetail');
                const detailCollapse = bootstrap.Collapse.getOrCreateInstance(detailElement, {
                    toggle: false
                });

                detailCollapse.hide();

                $('#incomingCardColumn')
                    .removeClass('col-12')
                    .addClass('col-xl-6 col-lg-6 col-md-6');

                $('#incomingProcessCard').removeClass('is-expanded');
                $('#dataCardColumn').removeClass('is-hidden');
                $('#incomingOpenAction').removeClass('d-none');
                $('#btnCloseIncomingCard').addClass('d-none');
            }

            $('#btnOpenIncomingCard').on('click', openIncomingCard);
            $('#btnAdd').on('click', openIncomingCard);
            $('#btnCloseIncomingCard, #btnCancelIncomingCard').on('click', closeIncomingCard);

            // DATA RAW MATERIAL: card melebar dan tabel tampil di dalam card.
            function openDataCard() {
                const detailElement = document.getElementById('dataCardDetail');
                const detailCollapse = bootstrap.Collapse.getOrCreateInstance(detailElement, {
                    toggle: false
                });

                $('#dataCardColumn')
                    .removeClass('col-xl-6 col-lg-6 col-md-6')
                    .addClass('col-12');

                $('#dataProcessCard').addClass('is-expanded');
                $('#incomingCardColumn').addClass('is-hidden');
                $('#dataOpenAction').addClass('d-none');
                $('#btnCloseDataCard').removeClass('d-none');

                detailCollapse.show();

                setTimeout(function() {
                    table.columns.adjust().draw(false);

                    document.getElementById('dataProcessCard').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 200);
            }

            function closeDataCard() {
                const detailElement = document.getElementById('dataCardDetail');
                const detailCollapse = bootstrap.Collapse.getOrCreateInstance(detailElement, {
                    toggle: false
                });

                detailCollapse.hide();

                $('#dataCardColumn')
                    .removeClass('col-12')
                    .addClass('col-xl-6 col-lg-6 col-md-6');

                $('#dataProcessCard').removeClass('is-expanded');
                $('#incomingCardColumn').removeClass('is-hidden');
                $('#dataOpenAction').removeClass('d-none');
                $('#btnCloseDataCard').addClass('d-none');
            }

            $('#btnOpenDataCard').on('click', openDataCard);
            $('#btnCloseDataCard').on('click', closeDataCard);

            $('#dataCardDetail').on('shown.bs.collapse', function() {
                table.columns.adjust().draw(false);
            });

            $('#btnFilter').click(function() { table.ajax.reload(); });

            $('#btnReset').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#filter_jenis').val('');
                table.ajax.reload();
            });

            $('#start_date, #end_date').on('keypress', function(e) {
                if (e.which == 13) table.ajax.reload();
            });

            $('body').on('click', '#btnQRCode', function() {
                let id = $(this).data('id');
                $('#qrModalLabel').html("QR Code - RMPM #" + id);
                $('#qrImageArea').html('<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>');
                $('#qrLabelText').html('Memuat QR Code...');
                $('#qrModal').modal('show');
                $.ajax({
                    url: "{{ route('rmpm.qrcode', '') }}/" + id,
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#qrImageArea').html('<img src="data:image/png;base64,' + response.qrCode + '" alt="QR Code" style="max-width: 300px;">');
                            $('#qrLabelText').html('<strong>' + response.label + '</strong>');
                        } else {
                            $('#qrImageArea').html('<p class="text-danger">Gagal memuat QR Code</p>');
                            $('#qrLabelText').html('');
                        }
                    },
                    error: function() {
                        $('#qrImageArea').html('<p class="text-danger">Terjadi kesalahan saat memuat QR Code</p>');
                        $('#qrLabelText').html('');
                    }
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('rmpm.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...');
                        $('.form-control, .form-select').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() { $('#save').prop('disabled', false).text('Simpan'); },
                    success: function(response) {
                        closeIncomingCard();
                        $('#form').trigger("reset");
                        Swal.fire({ icon: 'success', title: 'Sukses', text: response.message });
                        $('#datatable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.jenis)               { $('#jenis').addClass('is-invalid');               $('.errorJenis').html(errors.jenis.join('<br>')); }
                            if (errors.tanggal_kedatangan)  { $('#tanggal_kedatangan').addClass('is-invalid');  $('.errorTanggalKedatangan').html(errors.tanggal_kedatangan.join('<br>')); }
                            if (errors.supplier)            { $('#supplier').addClass('is-invalid');            $('.errorSupplier').html(errors.supplier.join('<br>')); }
                            if (errors.asal_bahan)          { $('#asal_bahan').addClass('is-invalid');          $('.errorAsalBahan').html(errors.asal_bahan.join('<br>')); }
                            if (errors.no_plat)             { $('#no_plat').addClass('is-invalid');             $('.errorNoPlat').html(errors.no_plat.join('<br>')); }
                            if (errors.no_spb)              { $('#no_spb').addClass('is-invalid');              $('.errorNoSPB').html(errors.no_spb.join('<br>')); }
                            if (errors.jumlah_kedatangan)   { $('#jumlah_kedatangan').addClass('is-invalid');   $('.errorJumlahKedatangan').html(errors.jumlah_kedatangan.join('<br>')); }
                            if (errors.lot_batch)           { $('#lot_batch').addClass('is-invalid');           $('.errorLotBatch').html(errors.lot_batch.join('<br>')); }
                        } else {
                            Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Terjadi kesalahan, silakan coba lagi.' });
                        }
                    }
                });
            });
        });
    </script>
@endsection