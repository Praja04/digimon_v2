@extends('layouts.component.main')
@section('title', 'Form Adjustment')

@section('styles')
<style>
    .lembar-sheet-card {
        background: #ffffff;
        border: 1px solid #1e293b;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        padding: 24px;
        position: relative;
        margin-bottom: 35px;
    }

    .lembar-header-badge {
        background: #1e293b;
        color: #ffffff;
        padding: 5px 12px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Unified Official Document Tables */
    .doc-unified-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.83rem;
        margin-bottom: 0;
        color: #000000;
    }

    .doc-unified-table th, .doc-unified-table td {
        border: 1px solid #000000;
        padding: 4px 6px;
        vertical-align: middle;
    }

    .doc-unified-table th {
        background-color: #f2f2f2;
        font-weight: 700;
        text-align: center;
        color: #000000;
    }

    /* Inputs inside cells */
    .clean-cell-input {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        text-align: center;
        font-size: 0.83rem;
        font-weight: 500;
        padding: 3px 2px;
        border-radius: 3px;
        color: #000000;
    }

    .clean-cell-input:focus, .clean-cell-input:hover {
        border-color: #405189;
        background: #f8fafc;
        outline: none;
        box-shadow: 0 0 0 2px rgba(64, 81, 137, 0.15);
    }

    .clean-cell-select {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        font-size: 0.80rem;
        font-weight: 500;
        padding: 2px 4px;
        border-radius: 3px;
        color: #000000;
        cursor: pointer;
    }

    .clean-cell-select:focus, .clean-cell-select:hover {
        border-color: #405189;
        background: #f8fafc;
        outline: none;
    }

    .clean-meta-input {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        font-size: 0.84rem;
        font-weight: 500;
        padding: 2px 4px;
        border-radius: 3px;
        color: #000000;
    }

    .clean-meta-input:focus, .clean-meta-input:hover {
        border-color: #405189;
        background: #f8fafc;
        outline: none;
    }

    .clean-sign-input {
        width: 100%;
        border: none;
        border-bottom: 1px dashed #666;
        background: transparent;
        text-align: center;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 2px 4px;
        border-radius: 0;
        color: #000000;
    }

    .clean-sign-input:focus {
        border-bottom: 2px solid #405189;
        outline: none;
        background: #f8fafc;
    }

    .clean-textarea {
        width: 100%;
        border: none;
        background: transparent;
        resize: none;
        font-size: 0.82rem;
        padding: 4px;
        color: #000000;
    }

    .clean-textarea:focus {
        outline: none;
        background: #f8fafc;
    }

    .sign-space-cell {
        height: 40px;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dokumen Form Adjustment (FRM/QLB/04/104/011-00)</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.menu') }}">Menu</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.export.index') }}">Export & Cetak</a></li>
                            <li class="breadcrumb-item active">Formulir Adjustment</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Control Bar: Pilih PO & Action Buttons -->
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">
                    <div class="col-lg-4 col-md-6">
                        <label for="selectPo" class="form-label fw-bold fs-13 mb-1">
                            <i class="ri-folder-open-line me-1 text-primary"></i> Pilih Nomor PO Batch Produksi:
                        </label>
                        <select id="selectPo" class="form-select select2">
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
                                    {{ $b->po_number }} - {{ $b->variant ?? 'Kecap Sedap' }} ({{ \Carbon\Carbon::parse($b->date)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label for="globalTanggalDoc" class="form-label fw-bold fs-13 mb-1">
                            <i class="ri-calendar-event-line me-1 text-primary"></i> Tanggal Record Dokumen:
                        </label>
                        <input type="date" class="form-control form-control-sm" id="globalTanggalDoc" value="{{ $initialData['tanggal_record_doc'] ?? date('Y-m-d') }}">
                    </div>

                    <div class="col-lg-5 col-md-12 text-md-end text-start d-flex flex-wrap gap-2 justify-content-md-end justify-content-start align-items-end">
                        <a href="{{ route('analisa.blending-awal.export.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                        <button type="button" id="btnSaveDoc" class="btn btn-primary btn-sm">
                            <i class="ri-save-3-line me-1"></i> Simpan Dokumen
                        </button>
                        <a href="#" id="btnExportExcel" class="btn btn-success btn-sm">
                            <i class="ri-file-excel-2-line me-1"></i> Download Excel
                        </a>
                        <a href="#" id="btnPrintView" target="_blank" class="btn btn-info btn-sm text-white">
                            <i class="ri-printer-line me-1"></i> Cetak / Print
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Info Notes -->
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-3 py-2 px-3">
            <i class="ri-information-line fs-18 me-2"></i>
            <div class="fs-12">
                <strong>Format Resmi Form Adjustment:</strong> <strong>1 Lembar = 1 Batch / Pasangan Batch</strong> (1 Lembar untuk 3x Adjustment). Setiap lembar mencatat rekomendasi penambahan 7 bahan standar, status pengerjaan produksi, dan verifikasi QC.
            </div>
        </div>

        <!-- Container Lembar Form Adjustment -->
        <div id="adjustmentSheetsContainer">
            <!-- Di-render otomatis lewat JS per Lembar (1 Lembar = 1 Batch/Pasangan Batch) -->
        </div>

        <!-- Tombol Tambah Lembar Adjustment -->
        <div class="text-center mb-5">
            <button type="button" class="btn btn-outline-primary btn-md shadow-sm px-4" id="btnAddAdjustmentSheet">
                <i class="ri-add-circle-line me-1 fs-16 align-middle"></i> Tambah Lembar Form Adjustment Baru
            </button>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    var currentDocData = @json($initialData);
    var defaultBahans = [
        'Gula Kelapa',
        'Gula Tebu',
        'Larutan Garam',
        'Garam Kasar',
        'Air',
        'Garam Halus',
        'Karamel (Jenis)'
    ];

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Inisialisasi awal
        renderAdjustmentSheets(currentDocData ? currentDocData.adjustment_sheets : []);
        updateActionLinks($('#selectPo').val());

        // Event saat PO diganti
        $('#selectPo').on('change', function() {
            var poId = $(this).val();
            updateActionLinks(poId);

            Swal.fire({
                title: 'Memuat Data PO...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('doc-adjustment.fetch') }}",
                type: 'GET',
                data: { po_id: poId },
                success: function(res) {
                    Swal.close();
                    if (res.status === 'success' && res.data) {
                        currentDocData = res.data;
                        populateDocData(res.data);
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal mengambil data form adjustment.', 'error');
                }
            });
        });

        // Global tanggal doc sync
        $('#globalTanggalDoc').on('change', function() {
            var newDate = $(this).val();
            $('.doc-date-display').text(newDate);
            $('.sheet-meta-tanggal').each(function() {
                if (!$(this).val()) {
                    $(this).val(newDate);
                }
            });
        });

        // Event Tambah Lembar Baru
        $('#btnAddAdjustmentSheet').on('click', function() {
            var currentSheets = collectSheetsFromDOM();
            var nextNum = currentSheets.length + 1;
            var defaultDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';
            var variant = (currentDocData && currentDocData.variant) ? currentDocData.variant : 'Kecap Sedap';

            var bRows = [];
            defaultBahans.forEach(function(bName) {
                bRows.push({
                    bahan: bName,
                    adj1: '',
                    adj2: '',
                    adj3: ''
                });
            });

            var newSheet = {
                proses: 'Blending',
                jenis_kecap: variant,
                no_batch: String(nextNum),
                tanggal_produksi: defaultDate,
                volume_batch: '10000 L',
                shift: 'Shift 1 / Grup A',
                bahan_rows: bRows,
                disposisi: 'Release',
                keterangan: '',
                adj1_jam: '08:00',
                adj1_status: 'Sudah dilakukan',
                adj1_petugas: '',
                adj2_jam: '',
                adj2_status: 'Sudah dilakukan',
                adj2_petugas: '',
                adj3_jam: '',
                adj3_status: 'Sudah dilakukan',
                adj3_petugas: '',
                qc_analis: (currentDocData && currentDocData.qc_analis) ? currentDocData.qc_analis : ''
            };

            currentSheets.push(newSheet);
            renderAdjustmentSheets(currentSheets);
        });

        // Event Simpan Dokumen
        $('#btnSaveDoc').on('click', function() {
            var poId = $('#selectPo').val();
            if (!poId) {
                Swal.fire('Peringatan', 'Silakan pilih Nomor PO terlebih dahulu.', 'warning');
                return;
            }

            var sheets = collectSheetsFromDOM();
            var payload = {
                production_batch_id: poId,
                tanggal_record_doc: $('#globalTanggalDoc').val(),
                halaman: '1 / ' + Math.max(1, sheets.length),
                adjustment_sheets: sheets,
                qc_analis: sheets.length > 0 ? (sheets[0].qc_analis || '') : ''
            };

            Swal.fire({
                title: 'Menyimpan Dokumen...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('doc-adjustment.store') }}",
                type: 'POST',
                data: payload,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Formulir Form Adjustment berhasil disimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan formulir.', 'error');
                }
            });
        });
    });

    function updateActionLinks(poId) {
        if (!poId) return;
        $('#btnExportExcel').attr('href', "{{ route('doc-adjustment.export') }}?po_id=" + poId);
        $('#btnPrintView').attr('href', "{{ url('/doc-adjustment/print') }}/" + poId);
    }

    function populateDocData(data) {
        $('#globalTanggalDoc').val(data.tanggal_record_doc || '{{ date("Y-m-d") }}');
        renderAdjustmentSheets(data.adjustment_sheets || []);
    }

    function renderAdjustmentSheets(sheets) {
        var container = $('#adjustmentSheetsContainer');
        container.empty();

        if (!sheets || sheets.length === 0) {
            container.html('<div class="text-center py-5 text-muted border border-dashed rounded bg-white mb-4">Belum ada data lembar adjustment. Silakan klik tombol di bawah untuk menambah lembar.</div>');
            return;
        }

        var totalSheets = sheets.length;
        var globalDocDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';

        sheets.forEach(function(sheet, sIdx) {
            var rawRows = sheet.bahan_rows || [];
            var bMap = {};
            rawRows.forEach(function(r) {
                if (r.bahan) bMap[r.bahan] = r;
            });

            var sheetHtml = `
            <div class="lembar-sheet-card" data-sheet-index="${sIdx}">
                <!-- Top Header Info Bar -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="lembar-header-badge">
                            <i class="ri-file-edit-line"></i> LEMBAR FORM ADJUSTMENT #${sIdx + 1}
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 fs-12">
                            Batch / Pasangan: <strong>${escapeHtml(sheet.no_batch || String(sIdx + 1))}</strong>
                        </span>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fs-12">
                            1 Lembar untuk 3x Adjustment
                        </span>
                    </div>
                    <div>
                        ${totalSheets > 1 ? `
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-delete-sheet" data-sheet-index="${sIdx}">
                            <i class="ri-delete-bin-line me-1"></i> Hapus Lembar
                        </button>
                        ` : ''}
                    </div>
                </div>

                <!-- 1. Header Dokumen Resmi (Format Solid Terpadu) -->
                <table class="doc-unified-table mb-0" style="border-bottom: none;">
                    <tr>
                        <td style="width: 22%; text-align: center; vertical-align: middle; padding: 6px; border-right: 1px solid #000;">
                            <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 38px; object-fit: contain;">
                        </td>
                        <td style="width: 53%; text-align: center; vertical-align: middle; padding: 8px; border-right: 1px solid #000;">
                            <div class="fw-bold text-dark mb-0" style="font-size: 1.22rem; letter-spacing: 0.8px;">FORM ADJUSTMENT</div>
                        </td>
                        <td style="width: 25%; padding: 0; vertical-align: middle;">
                            <table class="w-100 h-100" style="border-collapse: collapse; font-size: 0.80rem;">
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="px-2 py-1 bg-light fw-bold text-start" style="width: 55%; border: none; border-right: 1px solid #000;">Tanggal Record Doc</td>
                                    <td class="px-2 py-1 text-start" style="width: 45%; border: none;">: <span class="doc-date-display">${globalDocDate}</span></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1 bg-light fw-bold text-start" style="border: none; border-right: 1px solid #000;">Halaman</td>
                                    <td class="px-2 py-1 text-start fw-semibold" style="border: none;">: ${sIdx + 1} / ${totalSheets}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- 2. Metadata Informasi (3 Baris Rapi) -->
                <table class="doc-unified-table mb-0" style="border-bottom: none;">
                    <tr>
                        <td style="width: 14%; font-weight: 700; background: #fafafa;">Proses</td>
                        <td style="width: 36%;">
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="clean-meta-input sheet-meta-proses" value="${escapeHtml(sheet.proses || 'Blending')}">
                            </div>
                        </td>
                        <td style="width: 15%; font-weight: 700; background: #fafafa;">Jenis Kecap</td>
                        <td style="width: 35%;">
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="clean-meta-input sheet-meta-jenis" value="${escapeHtml(sheet.jenis_kecap || '')}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; background: #fafafa;">No. Batch</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="clean-meta-input sheet-meta-batch" value="${escapeHtml(sheet.no_batch || '')}">
                            </div>
                        </td>
                        <td style="font-weight: 700; background: #fafafa;">Tanggal Produksi</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="date" class="clean-meta-input sheet-meta-tanggal" value="${escapeHtml(sheet.tanggal_produksi || globalDocDate)}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; background: #fafafa;">Volume Batch</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="clean-meta-input sheet-meta-volume" value="${escapeHtml(sheet.volume_batch || '')}">
                            </div>
                        </td>
                        <td style="font-weight: 700; background: #fafafa;">Shift</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="clean-meta-input sheet-meta-shift" value="${escapeHtml(sheet.shift || 'Shift 1 / Grup A')}">
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- 3. Tabel Utama 7 Bahan & Adjustment -->
                <div class="table-responsive">
                    <table class="doc-unified-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 28%; height: 32px;">BAHAN</th>
                                <th style="width: 18%;">ADJUSMENT 1</th>
                                <th style="width: 18%;">ADJUSMENT 2</th>
                                <th style="width: 18%;">ADJUSMENT 3</th>
                                <th style="width: 18%;">DISPOSISI</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${defaultBahans.map(function(bName, idx) {
                                var rowData = bMap[bName] || rawRows[idx] || {};
                                var adj1 = rowData.adj1 || '';
                                var adj2 = rowData.adj2 || '';
                                var adj3 = rowData.adj3 || '';

                                return `
                                <tr class="bahan-row" data-bahan="${escapeHtml(bName)}">
                                    <td class="text-start ps-3 fw-bold bg-white">${escapeHtml(bName)}</td>
                                    <td><input type="text" class="clean-cell-input r-adj1" placeholder="-" value="${escapeHtml(adj1)}"></td>
                                    <td><input type="text" class="clean-cell-input r-adj2" placeholder="-" value="${escapeHtml(adj2)}"></td>
                                    <td><input type="text" class="clean-cell-input r-adj3" placeholder="-" value="${escapeHtml(adj3)}"></td>
                                    ${idx === 0 ? `
                                    <td rowspan="7" class="align-middle text-center bg-white p-2">
                                        <textarea class="clean-textarea text-center fw-bold fs-13 sheet-disposisi" style="height: 160px; line-height: 1.5;" placeholder="Disposisi QC">${escapeHtml(sheet.disposisi || 'Release')}</textarea>
                                    </td>
                                    ` : ''}
                                </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>

                    <!-- 4. Keterangan & Tanda Tangan (Format Rapi Presisi) -->
                    <table class="doc-unified-table" style="border-top: none;">
                        <tr>
                            <!-- Kotak Keterangan di Kiri -->
                            <td rowspan="5" class="align-top text-start p-2" style="width: 28%; border-top: none;">
                                <div class="fw-bold fs-12 mb-1 text-dark">Keterangan :</div>
                                <textarea class="clean-textarea sheet-keterangan" style="height: 110px;" placeholder="Ketik keterangan adjustment di sini...">${escapeHtml(sheet.keterangan || '')}</textarea>
                            </td>
                            <!-- Baris Jam -->
                            <td style="width: 18%; border-top: none;">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-nowrap fs-11 me-1">Jam :</span>
                                    <input type="text" class="clean-cell-input sheet-adj1-jam text-start ps-1" placeholder="HH:mm" value="${escapeHtml(sheet.adj1_jam || '')}">
                                </div>
                            </td>
                            <td style="width: 18%; border-top: none;">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-nowrap fs-11 me-1">Jam :</span>
                                    <input type="text" class="clean-cell-input sheet-adj2-jam text-start ps-1" placeholder="HH:mm" value="${escapeHtml(sheet.adj2_jam || '')}">
                                </div>
                            </td>
                            <td style="width: 18%; border-top: none;">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-nowrap fs-11 me-1">Jam :</span>
                                    <input type="text" class="clean-cell-input sheet-adj3-jam text-start ps-1" placeholder="HH:mm" value="${escapeHtml(sheet.adj3_jam || '')}">
                                </div>
                            </td>
                            <!-- Header Tanda Tangan Disposisi -->
                            <td rowspan="2" style="width: 18%; border-top: none; vertical-align: middle; background: #f8f9fa;" class="fw-bold fs-11 text-center">
                                Tanda Tangan,
                            </td>
                        </tr>
                        <tr>
                            <!-- Baris Status -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-nowrap fs-11 me-1">Status :</span>
                                    <select class="clean-cell-select sheet-adj1-status">
                                        <option value="Sudah dilakukan" ${sheet.adj1_status === 'Sudah dilakukan' ? 'selected' : ''}>Sudah dilakukan</option>
                                        <option value="Belum dilakukan" ${sheet.adj1_status === 'Belum dilakukan' ? 'selected' : ''}>Belum dilakukan</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-nowrap fs-11 me-1">Status :</span>
                                    <select class="clean-cell-select sheet-adj2-status">
                                        <option value="Sudah dilakukan" ${sheet.adj2_status === 'Sudah dilakukan' ? 'selected' : ''}>Sudah dilakukan</option>
                                        <option value="Belum dilakukan" ${sheet.adj2_status === 'Belum dilakukan' ? 'selected' : ''}>Belum dilakukan</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-nowrap fs-11 me-1">Status :</span>
                                    <select class="clean-cell-select sheet-adj3-status">
                                        <option value="Sudah dilakukan" ${sheet.adj3_status === 'Sudah dilakukan' ? 'selected' : ''}>Sudah dilakukan</option>
                                        <option value="Belum dilakukan" ${sheet.adj3_status === 'Belum dilakukan' ? 'selected' : ''}>Belum dilakukan</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <!-- Header Tanda Tangan Produksi -->
                            <td class="fw-bold fs-11 text-center" style="background: #f8f9fa;">Tanda Tangan,</td>
                            <td class="fw-bold fs-11 text-center" style="background: #f8f9fa;">Tanda Tangan,</td>
                            <td class="fw-bold fs-11 text-center" style="background: #f8f9fa;">Tanda Tangan,</td>
                            <td rowspan="2" class="align-middle">
                                <div class="sign-space-cell"></div>
                            </td>
                        </tr>
                        <tr>
                            <!-- Ruang Tanda Tangan -->
                            <td><div class="sign-space-cell"></div></td>
                            <td><div class="sign-space-cell"></div></td>
                            <td><div class="sign-space-cell"></div></td>
                        </tr>
                        <tr>
                            <!-- Nama Penandatangan -->
                            <td class="p-1 text-center">
                                <input type="text" class="clean-sign-input sheet-adj1-petugas" placeholder="Petugas Produksi" value="${escapeHtml(sheet.adj1_petugas || '')}">
                            </td>
                            <td class="p-1 text-center">
                                <input type="text" class="clean-sign-input sheet-adj2-petugas" placeholder="Petugas Produksi" value="${escapeHtml(sheet.adj2_petugas || '')}">
                            </td>
                            <td class="p-1 text-center">
                                <input type="text" class="clean-sign-input sheet-adj3-petugas" placeholder="Petugas Produksi" value="${escapeHtml(sheet.adj3_petugas || '')}">
                            </td>
                            <td class="p-1 text-center">
                                <input type="text" class="clean-sign-input sheet-qc-analis" placeholder="Analis QC" value="${escapeHtml(sheet.qc_analis || '')}">
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 5. Kode Dokumen Resmi -->
                <div class="text-end mt-2 pe-1">
                    <span class="fs-11 fw-semibold text-muted fst-italic">FRM/QLB/04/104/011-00</span>
                </div>
            </div>
            `;
            container.append(sheetHtml);
        });

        attachSheetEvents();
    }

    function attachSheetEvents() {
        $('.btn-delete-sheet').off('click').on('click', function() {
            var sIdx = $(this).data('sheet-index');
            Swal.fire({
                title: 'Hapus Lembar Ini?',
                text: 'Lembar Form Adjustment #' + (sIdx + 1) + ' akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var currentSheets = collectSheetsFromDOM();
                    currentSheets.splice(sIdx, 1);
                    renderAdjustmentSheets(currentSheets);
                }
            });
        });
    }

    function collectSheetsFromDOM() {
        var sheets = [];
        $('.lembar-sheet-card').each(function(idx) {
            var card = $(this);
            var sheetObj = {
                proses: card.find('.sheet-meta-proses').val() || 'Blending',
                jenis_kecap: card.find('.sheet-meta-jenis').val() || '',
                no_batch: card.find('.sheet-meta-batch').val() || String(idx + 1),
                tanggal_produksi: card.find('.sheet-meta-tanggal').val() || '',
                volume_batch: card.find('.sheet-meta-volume').val() || '',
                shift: card.find('.sheet-meta-shift').val() || 'Shift 1 / Grup A',
                disposisi: card.find('.sheet-disposisi').val() || 'Release',
                keterangan: card.find('.sheet-keterangan').val() || '',
                adj1_jam: card.find('.sheet-adj1-jam').val() || '',
                adj1_status: card.find('.sheet-adj1-status').val() || 'Sudah dilakukan',
                adj1_petugas: card.find('.sheet-adj1-petugas').val() || '',
                adj2_jam: card.find('.sheet-adj2-jam').val() || '',
                adj2_status: card.find('.sheet-adj2-status').val() || 'Sudah dilakukan',
                adj2_petugas: card.find('.sheet-adj2-petugas').val() || '',
                adj3_jam: card.find('.sheet-adj3-jam').val() || '',
                adj3_status: card.find('.sheet-adj3-status').val() || 'Sudah dilakukan',
                adj3_petugas: card.find('.sheet-adj3-petugas').val() || '',
                qc_analis: card.find('.sheet-qc-analis').val() || '',
                bahan_rows: []
            };

            card.find('.bahan-row').each(function() {
                var row = $(this);
                sheetObj.bahan_rows.push({
                    bahan: row.data('bahan') || row.find('td:first').text().trim(),
                    adj1: row.find('.r-adj1').val() || '',
                    adj2: row.find('.r-adj2').val() || '',
                    adj3: row.find('.r-adj3').val() || ''
                });
            });

            sheets.push(sheetObj);
        });
        return sheets;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection
