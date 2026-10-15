@extends('layouts.component.main')
@section('title', 'Hasil Analisis Proses Pelarutan')

@section('styles')
<style>
    .lembar-sheet-card {
        background: #ffffff;
        border: 2px solid #1e293b;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        padding: 24px;
        position: relative;
        margin-bottom: 35px;
    }

    .lembar-header-badge {
        background: #0f172a;
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 0.88rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .doc-logo-box {
        border: 2px solid #2d3748;
        padding: 4px 8px;
        display: inline-block;
        border-radius: 4px;
        background: #f8fafc;
    }

    .doc-title-main {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #1a202c;
        text-transform: uppercase;
        text-align: center;
    }

    /* Horizontal Scrollbar Styling Resmi */
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #94a3b8 #f1f5f9;
        padding-bottom: 6px;
    }
    .table-responsive::-webkit-scrollbar {
        height: 10px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 6px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 6px;
        border: 2px solid #f1f5f9;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    /* Table Pelarutan */
    .table-pelarutan {
        width: 100%;
        min-width: 1600px;
        border-collapse: collapse;
        font-size: 0.82rem;
        margin-bottom: 8px;
        table-layout: fixed;
        background: #ffffff;
    }

    .table-pelarutan th, .table-pelarutan td {
        border: 1px solid #334155;
        padding: 3px 2px;
        text-align: center;
        vertical-align: middle;
        position: relative;
    }

    .table-pelarutan th {
        background-color: #f1f5f9;
        font-weight: 700;
        color: #0f172a;
        font-size: 0.82rem;
        padding: 6px 3px;
    }

    .table-pelarutan th.th-samp {
        width: 85px !important;
        min-width: 85px !important;
        white-space: nowrap;
        font-size: 0.80rem;
        line-height: 1.25;
        padding: 5px 4px;
    }

    .table-pelarutan th.th-gga {
        background-color: #eff6ff !important;
        color: #1e3a8a !important;
        border-bottom: 2px solid #3b82f6 !important;
    }

    .table-pelarutan th.th-ggas {
        background-color: #f0fdf4 !important;
        color: #14532d !important;
        border-bottom: 2px solid #22c55e !important;
    }

    /* Row Hover & Focus-within Active Row Highlight (Mata user langsung tahu baris mana yang aktif) */
    .table-pelarutan tbody tr.data-row {
        transition: background-color 0.12s ease;
    }

    .table-pelarutan tbody tr.data-row:hover {
        background-color: #f8fafc;
    }

    .table-pelarutan tbody tr.data-row:focus-within {
        background-color: #e0f2fe !important;
    }

    .table-pelarutan tbody tr.data-row:focus-within td {
        background-color: #e0f2fe !important;
    }

    /* Table Input Fields */
    .table-pelarutan .table-input {
        width: 100%;
        height: 32px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        font-size: 0.84rem;
        font-weight: 500;
        padding: 3px 4px;
        border-radius: 4px;
        color: #0f172a;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .table-pelarutan .table-input:hover {
        border-color: #64748b;
        background: #f8fafc;
    }

    .table-pelarutan .table-input:focus {
        border: 2px solid #2563eb !important;
        background: #ffffff !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.28) !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        z-index: 10;
        position: relative;
    }

    /* Table Textarea Fields (Multiline Jam Disposisi & Rekomendasi Adj) */
    .table-pelarutan .table-textarea {
        width: 100%;
        height: 42px;
        min-height: 42px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        font-size: 0.79rem;
        font-weight: 500;
        padding: 3px 4px;
        border-radius: 4px;
        resize: none;
        line-height: 1.25;
        color: #0f172a;
        white-space: pre-wrap;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .table-pelarutan .table-textarea:hover {
        border-color: #64748b;
        background: #f8fafc;
    }

    .table-pelarutan .table-textarea:focus {
        border: 2px solid #2563eb !important;
        background: #ffffff !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.28) !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        z-index: 10;
        position: relative;
    }

    /* Sequence number columns (Sampling ke-) */
    .table-pelarutan .table-input.r-p1-sampling-ke,
    .table-pelarutan .table-input.r-p2-sampling-ke {
        background-color: #f1f5f9;
        font-weight: 700;
        color: #475569;
        border-color: #e2e8f0;
        cursor: default;
    }

    /* Disposisi input */
    .table-pelarutan .table-input.r-disposisi {
        font-weight: 700;
        color: #1e3a8a;
    }

    /* Soft, clear placeholders */
    .table-pelarutan .table-input::placeholder,
    .table-pelarutan .table-textarea::placeholder,
    .meta-input-group input::placeholder,
    .meta-input-group textarea::placeholder {
        color: #94a3b8 !important;
        font-style: italic !important;
        font-weight: 400 !important;
        font-size: 0.74rem !important;
        opacity: 0.8 !important;
    }

    /* Scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 9px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .batch-sub-card {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 6px;
        padding: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .signature-container {
        border: 1px solid #000000;
        margin-top: 10px;
    }

    .sig-col {
        border-right: 1px solid #000000;
        padding: 6px 10px;
        text-align: center;
    }

    .sig-col:last-child {
        border-right: none;
    }

    .sig-space {
        height: 45px;
    }

    .meta-input-group td {
        padding: 4px 6px;
        font-size: 0.84rem;
    }

    .meta-input-group .form-control-sm {
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 4px 8px;
        background: #ffffff;
        font-size: 0.84rem;
        font-weight: 500;
        color: #0f172a;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .meta-input-group .form-control-sm:hover {
        border-color: #64748b;
        background: #f8fafc;
    }

    .meta-input-group .form-control-sm:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        background: #ffffff;
        font-weight: 600;
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
                    <h4 class="mb-sm-0">Dokumen Analisis Pelarutan (FRM/QLB/04/104/004-01)</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('pelarutan-1.menu') }}">Menu</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pelarutan.export.index') }}">Export & Cetak</a></li>
                            <li class="breadcrumb-item active">Formulir Dokumen</li>
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
                                    {{ $b->po_number }} - {{ $b->variant ?? 'Kecap Manis' }} ({{ \Carbon\Carbon::parse($b->date)->format('d/m/Y') }})
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
                        <a href="{{ route('pelarutan.export.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                        @if(auth()->check() && in_array(strtolower(trim(auth()->user()->role ?? '')), ['foreman', 'supervisor', 'head of dapartement', 'head of department', 'dept head'], true))
                        <a href="#" id="btnExportExcel" class="btn btn-success btn-sm">
                            <i class="ri-file-excel-2-line me-1"></i> Download Excel
                        </a>
                        <a href="#" id="btnPrintView" target="_blank" class="btn btn-info btn-sm text-white">
                            <i class="ri-printer-line me-1"></i> Cetak / Print
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Container Lembar Pelarutan -->
        <div id="lembarSheetsContainer">
            <!-- Di-render otomatis lewat JS per 2 Batch per Lembar -->
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    var currentDocData = @json($initialData);

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Inisialisasi awal
        renderLembarSheets(currentDocData ? currentDocData.batch_blocks : []);
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
                url: "{{ route('doc-pelarutan.fetch') }}",
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
                    Swal.fire('Error', 'Gagal mengambil data formulir pelarutan.', 'error');
                }
            });
        });

        // Global tanggal doc sync
        $('#globalTanggalDoc').on('change', function() {
            var newDate = $(this).val();
            $('.block-meta-tanggal').each(function() {
                if (!$(this).val()) {
                    $(this).val(newDate);
                }
            });
            $('.doc-date-display').text(newDate);
        });

        // Event Tambah 1 Lembar Baru (2 Batch)
        $('#btnAddPairBatch').on('click', function() {
            var currentBlocks = collectBatchBlocksFromDOM();
            var startBatch = currentBlocks.length + 1;
            var defaultDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';
            var variant = (currentDocData && currentDocData.variant) ? currentDocData.variant : 'Kecap Manis';

            for (var b = 0; b < 2; b++) {
                var bNum = startBatch + b;
                var newBlock = {
                    jenis_produk: variant,
                    tanggal_produksi: defaultDate,
                    jam_produksi: '06:00 - 14:00',
                    kode_shift_grup: '',
                    batch: String(bNum),
                    no_dissolver: String(bNum),
                    volume: '',
                    rows: []
                };

                for (var r = 1; r <= 5; r++) {
                    newBlock.rows.push({
                        p1_sampling_ke: String(r),
                        p1_jam: '',
                        p1_pic: '',
                        p1_brix: '',
                        p1_nacl: '',
                        p1_warna: '',
                        p1_organo: '',
                        p1_waktu_adjustment: '',
                        p2_sampling_ke: String(r),
                        p2_jam: '',
                        p2_pic: '',
                        p2_brix: '',
                        p2_nacl: '',
                        p2_warna: '',
                        p2_organo: '',
                        p2_waktu_adjustment: '',
                        disposisi: ''
                    });
                }
                currentBlocks.push(newBlock);
            }

            renderLembarSheets(currentBlocks);
        });

        // Event Simpan Dokumen
        $('#btnSaveDoc').on('click', function() {
            var poId = $('#selectPo').val();
            if (!poId) {
                Swal.fire('Peringatan', 'Silakan pilih Nomor PO terlebih dahulu.', 'warning');
                return;
            }

            var blocks = collectBatchBlocksFromDOM();
            var totalSheets = Math.max(1, Math.ceil(blocks.length / 2));

            var payload = {
                production_batch_id: poId,
                tanggal_record_doc: $('#globalTanggalDoc').val(),
                halaman: '1 / ' + totalSheets,
                catatan: $('#globalCatatan').val() || (currentDocData ? currentDocData.catatan : ''),
                pic_sampling: $('#globalPicSampling').val() || (currentDocData ? currentDocData.pic_sampling : ''),
                pic_analis: $('#globalPicAnalis').val() || (currentDocData ? currentDocData.pic_analis : ''),
                pic_checker: $('#globalPicChecker').val() || (currentDocData ? currentDocData.pic_checker : ''),
                batch_blocks: blocks
            };

            Swal.fire({
                title: 'Menyimpan Dokumen...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('doc-pelarutan.store') }}",
                type: 'POST',
                data: payload,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Formulir Pelarutan berhasil disimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan formulir.', 'error');
                }
            });
        });

        // 1. Auto-select text on focus (Membuat input via Tab langsung mengganti isi tanpa perlu backspace)
        $(document).on('focus', '.table-input, .table-textarea, .meta-input-group input, .signature-container input', function() {
            var el = this;
            setTimeout(function() {
                if (typeof el.select === 'function') {
                    el.select();
                }
            }, 30);
        });

        // 2. Keyboard Navigation: Enter (turun baris), Shift+Enter (naik baris), Panah Bawah/Atas (Excel Mode)
        $(document).on('keydown', '.table-pelarutan .table-input', function(e) {
            var $input = $(this);
            var $td = $input.closest('td');
            var colIdx = $td.index();
            var $tr = $input.closest('tr.data-row');

            if (e.key === 'Enter') {
                e.preventDefault();
                if (e.shiftKey) {
                    // Shift + Enter: Move up 1 row in same column
                    var $prevTr = $tr.prev('tr.data-row');
                    if ($prevTr.length) {
                        var $target = $prevTr.children('td').eq(colIdx).find('.table-input, .table-textarea');
                        if ($target.length) $target.focus();
                    }
                } else {
                    // Enter: Move down 1 row in same column
                    var $nextTr = $tr.next('tr.data-row');
                    if ($nextTr.length) {
                        var $target = $nextTr.children('td').eq(colIdx).find('.table-input, .table-textarea');
                        if ($target.length) $target.focus();
                    }
                }
            } else if (e.key === 'ArrowDown') {
                var $nextTr = $tr.next('tr.data-row');
                if ($nextTr.length) {
                    var $target = $nextTr.children('td').eq(colIdx).find('.table-input, .table-textarea');
                    if ($target.length) {
                        e.preventDefault();
                        $target.focus();
                    }
                }
            } else if (e.key === 'ArrowUp') {
                var $prevTr = $tr.prev('tr.data-row');
                if ($prevTr.length) {
                    var $target = $prevTr.children('td').eq(colIdx).find('.table-input, .table-textarea');
                    if ($target.length) {
                        e.preventDefault();
                        $target.focus();
                    }
                }
            }
        });
    });

    function updateActionLinks(poId) {
        if (!poId) return;
        $('#btnExportExcel').attr('href', "{{ route('doc-pelarutan.export') }}?po_id=" + poId);
        $('#btnPrintView').attr('href', "{{ url('/doc-pelarutan/print') }}/" + poId);
    }

    function populateDocData(data) {
        $('#globalTanggalDoc').val(data.tanggal_record_doc || '{{ date("Y-m-d") }}');
        renderLembarSheets(data.batch_blocks || []);
    }

    function renderLembarSheets(blocks) {
        var container = $('#lembarSheetsContainer');
        container.empty();

        if (!blocks || blocks.length === 0) {
            container.html('<div class="text-center py-5 text-muted border border-dashed rounded bg-white mb-4">Belum ada data lembar pelarutan. Silakan klik tombol di bawah untuk menambah lembar.</div>');
            return;
        }

        // Pastikan genap (1 Lembar = 2 Batch)
        var pairedBlocks = [];
        for (var i = 0; i < blocks.length; i += 2) {
            pairedBlocks.push([blocks[i], blocks[i + 1] || createEmptyBlock(i + 2)]);
        }

        var totalSheets = pairedBlocks.length;
        var globalDocDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';
        var catatanVal = currentDocData ? (currentDocData.catatan || '') : '';
        var picSamp = currentDocData ? (currentDocData.pic_sampling || '') : '';
        var picAnalis = currentDocData ? (currentDocData.pic_analis || '') : '';
        var picCheck = currentDocData ? (currentDocData.pic_checker || '') : '';

        pairedBlocks.forEach(function(pair, pIdx) {
            var b1 = pair[0];
            var b2 = pair[1];
            var b1Idx = pIdx * 2;
            var b2Idx = pIdx * 2 + 1;

            var sheetHtml = `
            <div class="lembar-sheet-card">
                <!-- Top Header Bar -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <span class="lembar-header-badge">
                            <i class="ri-pages-line"></i> LEMBAR PELARUTAN #${pIdx + 1}
                        </span>
                        <span class="badge bg-light text-dark border ms-2">
                            Batch ${escapeHtml(b1.batch || String(b1Idx + 1))} & Batch ${escapeHtml(b2.batch || String(b2Idx + 1))}
                        </span>
                        <span class="badge bg-info bg-opacity-10 text-info border border-info ms-2">
                            1 Lembar = 2 Batch
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-light text-muted border fs-12">
                            <i class="ri-lock-line me-1"></i> Data Otentik
                        </span>
                    </div>
                </div>

                <!-- Format Header Dokumen Resmi -->
                <div class="row align-items-center mb-3 pb-2 border-bottom">
                    <div class="col-3">
                        <div class="doc-logo-box p-1">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 42px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="doc-title-main">HASIL ANALISIS PROSES PELARUTAN</div>
                    </div>
                    <div class="col-3 text-end">
                        <table class="float-end border mb-1" style="font-size: 0.80rem;">
                            <tr>
                                <td class="p-1 border-bottom text-start fw-bold bg-light">Tanggal Record Doc</td>
                                <td class="p-1 border-bottom text-start doc-date-display">${globalDocDate}</td>
                            </tr>
                            <tr>
                                <td class="p-1 text-start fw-bold bg-light">Halaman</td>
                                <td class="p-1 text-start fw-semibold">${pIdx + 1} / ${totalSheets}</td>
                            </tr>
                        </table>
                        <div class="clearfix"></div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border fs-11 fst-italic">Notes: 1 lembar: 2 batch</span>
                    </div>
                </div>

                <!-- Blok Batch 1 -->
                <div class="batch-sub-card">
                    <div class="fw-bold fs-13 text-primary mb-2">
                        <i class="ri-flask-line me-1"></i> Batch Pertama: Batch ${escapeHtml(b1.batch || String(b1Idx + 1))}
                    </div>
                    ${renderBatchBlockForm(b1, b1Idx)}
                </div>

                <!-- Blok Batch 2 -->
                <div class="batch-sub-card">
                    <div class="fw-bold fs-13 text-primary mb-2">
                        <i class="ri-flask-line me-1"></i> Batch Kedua: Batch ${escapeHtml(b2.batch || String(b2Idx + 1))}
                    </div>
                    ${renderBatchBlockForm(b2, b2Idx)}
                </div>

                <!-- Footer: Catatan & Tanda Tangan -->
                <div class="signature-container">
                    <div class="row g-0">
                        <div class="col-md-5 p-2 border-end">
                            <label class="fw-bold fs-12 mb-1">Catatan :</label>
                            <div class="p-2 text-muted fs-12 bg-light rounded border min-vh-25" style="min-height: 80px;">${escapeHtml(catatanVal || '-')}</div>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-0 text-center h-100">
                                <div class="col-4 sig-col d-flex flex-column justify-content-between p-2">
                                    <div class="fw-semibold fs-12">Disampling oleh,</div>
                                    <div class="sig-space" style="height: 40px;"></div>
                                    <div>
                                        <div class="fw-bold border-bottom pb-1">${escapeHtml(picSamp || '-')}</div>
                                        <small class="text-muted d-block mt-1">Produksi</small>
                                    </div>
                                </div>
                                <div class="col-4 sig-col d-flex flex-column justify-content-between p-2">
                                    <div class="fw-semibold fs-12">Dianalisis oleh,</div>
                                    <div class="sig-space" style="height: 40px;"></div>
                                    <div>
                                        <div class="fw-bold border-bottom pb-1">${escapeHtml(picAnalis || '-')}</div>
                                        <small class="text-muted d-block mt-1">QC Analis</small>
                                    </div>
                                </div>
                                <div class="col-4 sig-col d-flex flex-column justify-content-between p-2">
                                    <div class="fw-semibold fs-12">Dicek oleh,</div>
                                    <div class="sig-space" style="height: 40px;"></div>
                                    <div>
                                        <div class="fw-bold border-bottom pb-1">${escapeHtml(picCheck || '-')}</div>
                                        <small class="text-muted d-block mt-1">Staff/SPV/MNG QC</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0 border-top bg-light">
                        <div class="col-12 text-end px-3 py-1">
                            <span class="fs-11 fw-bold text-muted fst-italic">FRM/QLB/04/104/004-01</span>
                        </div>
                    </div>
                </div>

            </div>
            `;
            container.append(sheetHtml);
        });

        attachSheetEvents();
    }

    function renderBatchBlockForm(block, bIdx) {
        var globalDocDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';
        return `
        <div class="batch-block-data" data-block-index="${bIdx}">
            <!-- Meta Table (Read-Only Authentic) -->
            <table class="w-100 meta-table mb-2">
                <tr>
                    <td width="15%" class="fw-bold">Jenis Produk</td>
                    <td width="35%">: <span class="fw-semibold">${escapeHtml(block.jenis_produk || '-')}</span></td>
                    <td width="15%" class="fw-bold">Batch</td>
                    <td width="35%">: <span class="fw-bold text-primary">${escapeHtml(block.batch || String(bIdx + 1))}</span></td>
                </tr>
                <tr>
                    <td class="fw-bold">Tanggal Produksi</td>
                    <td>: <span>${escapeHtml(block.tanggal_produksi || globalDocDate)}</span></td>
                    <td class="fw-bold">No. Dissolver</td>
                    <td>: <span class="fw-semibold">${escapeHtml(block.no_dissolver || '-')}</span></td>
                </tr>
                <tr>
                    <td class="fw-bold">Jam Produksi</td>
                    <td>: <span>${escapeHtml(block.jam_produksi || '-')}</span></td>
                    <td class="fw-bold">Volume</td>
                    <td>: <span>${escapeHtml(block.volume || '-')}</span></td>
                </tr>
                <tr>
                    <td class="fw-bold">Kode Shift & Grup</td>
                    <td>: <span class="badge bg-light text-dark border fs-12">${escapeHtml(block.kode_shift_grup || '-')}</span></td>
                    <td colspan="2"></td>
                </tr>
            </table>

            <!-- Tabel Analisis Pelarutan (5 Baris Standar) -->
            <div class="table-responsive">
                <table class="table-pelarutan">
                    <colgroup>
                        <col style="width: 80px;">
                        <col style="width: 75px;">
                        <col style="width: 130px;">
                        <col style="width: 65px;">
                        <col style="width: 65px;">
                        <col style="width: 75px;">
                        <col style="width: 65px;">
                        <col style="width: 180px;">
                        <col style="width: 80px;">
                        <col style="width: 75px;">
                        <col style="width: 130px;">
                        <col style="width: 65px;">
                        <col style="width: 65px;">
                        <col style="width: 75px;">
                        <col style="width: 65px;">
                        <col style="width: 180px;">
                        <col style="width: 90px;">
                    </colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2" class="bg-light text-muted th-samp" style="width: 80px;">Sampling<br>ke-</th>
                            <th colspan="2" style="width: 205px;" class="bg-light">Serah Terima</th>
                            <th colspan="5" style="width: 450px;" class="th-gga">PELARUTAN I (GGA)</th>
                            <th rowspan="2" class="bg-light text-muted th-samp" style="width: 80px;">Sampling<br>ke-</th>
                            <th colspan="2" style="width: 205px;" class="bg-light">Serah Terima</th>
                            <th colspan="5" style="width: 450px;" class="th-ggas">PELARUTAN II (GGAS)</th>
                            <th rowspan="2" style="width: 90px;" class="bg-light">Disposisi</th>
                        </tr>
                        <tr>
                            <th style="width: 75px;">Jam</th>
                            <th style="width: 130px;">PIC</th>
                            <th style="width: 65px;">Brix</th>
                            <th style="width: 65px;">%NaCl</th>
                            <th style="width: 75px;">Warna</th>
                            <th style="width: 65px;">Organo</th>
                            <th style="width: 180px;">Waktu & Adjustment</th>
                            <th style="width: 75px;">Jam</th>
                            <th style="width: 130px;">PIC</th>
                            <th style="width: 65px;">Brix</th>
                            <th style="width: 65px;">Visco</th>
                            <th style="width: 75px;">Warna</th>
                            <th style="width: 65px;">Organo</th>
                            <th style="width: 180px;">Waktu & Adjustment</th>
                        </tr>
                    </thead>
                    <tbody class="block-table-body" data-block-index="${bIdx}">
                        ${renderBlockRows(block.rows || [], bIdx)}
                    </tbody>
                </table>
            </div>
        </div>
        `;
    }

    function renderBlockRows(rows, bIdx) {
        if (!rows || rows.length === 0) return '';
        var html = '';
        rows.forEach(function(r, rIdx) {
            var dispColor = 'text-secondary';
            var dispVal = (r.disposisi || '').trim();
            if (dispVal.toLowerCase().includes('reject') || dispVal.toLowerCase().includes('not ok')) {
                dispColor = 'text-danger fw-bold';
            } else if (dispVal.toLowerCase().includes('release') || dispVal.toLowerCase().includes('ok')) {
                dispColor = 'text-success fw-bold';
            }

            html += `
            <tr class="data-row" data-row-index="${rIdx}">
                <td class="text-center py-2 bg-light fw-semibold">${escapeHtml(r.p1_sampling_ke || String(rIdx + 1))}</td>
                <td class="text-center py-2">${escapeHtml(r.p1_jam || '-')}</td>
                <td class="text-center py-2">${escapeHtml(r.p1_pic || '-')}</td>
                <td class="text-center py-2 fw-semibold">${escapeHtml(r.p1_brix || '-')}</td>
                <td class="text-center py-2">${escapeHtml(r.p1_nacl || '-')}</td>
                <td class="text-center py-2">${escapeHtml(r.p1_warna || 'Standar')}</td>
                <td class="text-center py-2">${escapeHtml(r.p1_organo || 'OK')}</td>
                <td class="text-center py-2" style="white-space: pre-line; line-height: 1.2;">${escapeHtml(r.p1_waktu_adjustment || '-')}</td>

                <td class="text-center py-2 bg-light fw-semibold">${escapeHtml(r.p2_sampling_ke || String(rIdx + 1))}</td>
                <td class="text-center py-2">${escapeHtml(r.p2_jam || '-')}</td>
                <td class="text-center py-2">${escapeHtml(r.p2_pic || '-')}</td>
                <td class="text-center py-2 fw-semibold">${escapeHtml(r.p2_brix || '-')}</td>
                <td class="text-center py-2">${escapeHtml(r.p2_visco || '-')}</td>
                <td class="text-center py-2">${escapeHtml(r.p2_warna || 'Standar')}</td>
                <td class="text-center py-2">${escapeHtml(r.p2_organo || 'OK')}</td>
                <td class="text-center py-2" style="white-space: pre-line; line-height: 1.2;">${escapeHtml(r.p2_waktu_adjustment || '-')}</td>

                <td class="text-center py-2 ${dispColor}">${escapeHtml(dispVal || '-')}</td>
            </tr>
            `;
        });
        return html;
    }

    function attachSheetEvents() {
        // Mode Otentik: Tidak ada manipulasi tambah/hapus baris atau lembar
    }

    function collectBatchBlocksFromDOM() {
        var blocks = [];
        $('.batch-block-data').each(function(idx) {
            var blockCard = $(this);
            var blockObj = {
                jenis_produk: blockCard.find('.block-meta-jenis').val() || '',
                tanggal_produksi: blockCard.find('.block-meta-tanggal').val() || '',
                jam_produksi: blockCard.find('.block-meta-jam').val() || '',
                kode_shift_grup: blockCard.find('.block-meta-shift').val() || '',
                batch: blockCard.find('.block-meta-batch').val() || String(idx + 1),
                no_dissolver: blockCard.find('.block-meta-no-dissolver').val() || String(idx + 1),
                volume: blockCard.find('.block-meta-volume').val() || '',
                rows: []
            };

            blockCard.find('.data-row').each(function() {
                var row = $(this);
                blockObj.rows.push({
                    p1_sampling_ke: row.find('.r-p1-sampling-ke').val() || '',
                    p1_jam: row.find('.r-p1-jam').val() || '',
                    p1_pic: row.find('.r-p1-pic').val() || '',
                    p1_brix: row.find('.r-p1-brix').val() || '',
                    p1_nacl: row.find('.r-p1-nacl').val() || '',
                    p1_warna: row.find('.r-p1-warna').val() || '',
                    p1_organo: row.find('.r-p1-organo').val() || '',
                    p1_waktu_adjustment: row.find('.r-p1-waktu-adj').val() || '',
                    p2_sampling_ke: row.find('.r-p2-sampling-ke').val() || '',
                    p2_jam: row.find('.r-p2-jam').val() || '',
                    p2_pic: row.find('.r-p2-pic').val() || '',
                    p2_brix: row.find('.r-p2-brix').val() || '',
                    p2_nacl: row.find('.r-p2-nacl').val() || '',
                    p2_warna: row.find('.r-p2-warna').val() || '',
                    p2_organo: row.find('.r-p2-organo').val() || '',
                    p2_waktu_adjustment: row.find('.r-p2-waktu-adj').val() || '',
                    disposisi: row.find('.r-disposisi').val() || ''
                });
            });

            blocks.push(blockObj);
        });
        return blocks;
    }

    function createEmptyBlock(batchNum) {
        var rows = [];
        for (var r = 1; r <= 5; r++) {
            rows.push({
                p1_sampling_ke: String(r),
                p1_jam: '',
                p1_pic: '',
                p1_brix: '',
                p1_nacl: '',
                p1_warna: '',
                p1_organo: '',
                p1_waktu_adjustment: '',
                p2_sampling_ke: String(r),
                p2_jam: '',
                p2_pic: '',
                p2_brix: '',
                p2_nacl: '',
                p2_warna: '',
                p2_organo: '',
                p2_waktu_adjustment: '',
                disposisi: ''
            });
        }
        return {
            jenis_produk: 'Kecap Manis',
            tanggal_produksi: $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}',
            jam_produksi: '06:00 - 14:00',
            kode_shift_grup: '',
            batch: String(batchNum),
            no_dissolver: String(batchNum),
            volume: '',
            rows: rows
        };
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
