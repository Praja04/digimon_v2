@extends('layouts.component.main')
@section('title', 'Hasil Analisis Proses Blending')

@section('styles')
<style>
    .lembar-sheet-card {
        background: #ffffff;
        border: 2px solid #1e293b;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        padding: 24px;
        position: relative;
        margin-bottom: 35px;
        max-width: 100%;
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

    /* Metadata Input Table (Fits 100% Card Width) */
    .meta-input-group {
        border: 1px solid #cbd5e1;
        border-collapse: collapse;
        border-radius: 4px;
        width: 100%;
        background: #ffffff;
    }

    .meta-input-group td {
        padding: 6px 10px;
        font-size: 0.84rem;
        vertical-align: middle;
        border: 1px solid #e2e8f0;
    }

    .meta-input-group input.form-control-sm {
        height: 31px;
        font-size: 0.84rem;
        font-weight: 500;
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

    /* Table Blending Data Grid (Only the wide data table in table-responsive) */
    .table-blending-data {
        width: 100%;
        min-width: 1475px;
        border-collapse: collapse;
        font-size: 0.82rem;
        margin-bottom: 0;
        table-layout: fixed;
        background: #ffffff;
    }

    .table-blending-data th, .table-blending-data td {
        border: 1px solid #94a3b8;
        padding: 0 !important;
        text-align: center;
        vertical-align: middle;
        position: relative;
    }

    .table-blending-data th {
        background-color: #f1f5f9;
        font-weight: 700;
        color: #0f172a;
        font-size: 0.80rem;
        padding: 6px 3px !important;
        border-color: #64748b;
    }

    .table-blending-data th.th-samp {
        width: 135px !important;
        min-width: 135px !important;
        white-space: nowrap;
        font-size: 0.80rem;
        line-height: 1.25;
        padding: 5px 4px !important;
    }

    /* Row Hover & Active Row Highlight */
    .table-blending-data tbody tr.data-row {
        transition: background-color 0.12s ease;
    }

    .table-blending-data tbody tr.data-row:hover td {
        background-color: #f8fafc;
    }

    .table-blending-data tbody tr.data-row:focus-within td {
        background-color: #f0fdf4;
    }

    /* Khusus Baris Turun Blending (Kuning Lembut Sesuai Google Sheets) */
    .table-blending-data tbody tr.data-row.row-turun-blending td {
        background-color: #fef9c3 !important;
    }

    .table-blending-data tbody tr.data-row.row-turun-blending:hover td {
        background-color: #fef08a !important;
    }

    /* Seamless Spreadsheet Cells - TANPA BORDER DI DALEM BORDER (NO BOX IN BOX) */
    .clean-cell-input {
        width: 100%;
        height: 100%;
        min-height: 36px;
        border: none !important;
        background: transparent !important;
        text-align: center;
        font-size: 0.84rem;
        font-weight: 500;
        padding: 6px 3px;
        border-radius: 0 !important;
        color: #0f172a;
        outline: none;
        box-shadow: none !important;
        box-sizing: border-box;
        display: block;
        transition: background-color 0.15s ease;
    }

    .clean-cell-textarea {
        width: 100%;
        min-height: 48px;
        height: auto;
        border: none !important;
        background: transparent !important;
        text-align: center;
        font-size: 0.77rem;
        font-weight: 500;
        padding: 6px 4px;
        border-radius: 0 !important;
        resize: none;
        line-height: 1.25;
        color: #0f172a;
        white-space: pre-wrap;
        overflow: hidden;
        outline: none;
        box-shadow: none !important;
        box-sizing: border-box;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        display: block;
        transition: background-color 0.15s ease;
    }

    .clean-cell-input::-webkit-scrollbar,
    .clean-cell-textarea::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    /* Saat Sel Aktif / Diklik (Persis Seleksi Sel Excel / Google Sheets) */
    .clean-cell-input:focus,
    .clean-cell-textarea:focus {
        background-color: #ffffff !important;
        outline: 2px solid #2563eb !important;
        outline-offset: -2px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        z-index: 10;
        position: relative;
    }

    .table-blending-data tbody tr.data-row.row-turun-blending .clean-cell-input:focus,
    .table-blending-data tbody tr.data-row.row-turun-blending .clean-cell-textarea:focus {
        background-color: #ffffff !important;
        outline: 2px solid #ca8a04 !important;
        outline-offset: -2px !important;
    }

    /* Disposisi Color Themes */
    .disp-release {
        color: #15803d !important;
        font-weight: 700 !important;
    }
    .disp-adjustment {
        color: #b45309 !important;
        font-weight: 700 !important;
    }
    .disp-resampling {
        color: #0369a1 !important;
        font-weight: 700 !important;
    }
    .disp-reject {
        color: #b91c1c !important;
        font-weight: 700 !important;
    }

    /* Signature & Notes Container */
    .signature-container {
        border: 1px solid #334155;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 10px;
        background: #ffffff;
    }

    .sig-col {
        border-right: 1px solid #334155;
        padding: 6px 10px;
        text-align: center;
    }

    .sig-col:last-child {
        border-right: none;
    }

    .sig-space {
        height: 45px;
    }

    .clean-cell-input::placeholder,
    .clean-cell-textarea::placeholder,
    .meta-input-group input::placeholder,
    .signature-container input::placeholder,
    .signature-container textarea::placeholder {
        color: #94a3b8 !important;
        font-style: italic !important;
        font-weight: 400 !important;
        font-size: 0.74rem !important;
        opacity: 0.8 !important;
    }

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
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dokumen Analisis Blending (FRM/QLB/04/104/005-01)</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.menu') }}">Menu</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.export.index') }}">Export & Cetak</a></li>
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

        <!-- Banner Petunjuk Input Cepat (Keyboard & Tab Friendly) -->
        <div class="alert border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between mb-3 py-2 px-3 bg-light border-start border-primary border-4">
            <div class="d-flex align-items-center fs-12 text-dark">
                <i class="ri-keyboard-line fs-18 me-2 text-primary"></i>
                <div>
                    <strong>Navigasi Cepat (Keyboard Friendly):</strong>
                    Gunakan tombol <kbd class="bg-white text-primary border px-1 fw-bold">Tab</kbd> untuk pindah ke kolom berikutnya &bull;
                    Tekan <kbd class="bg-white text-dark border px-1 fw-bold">Enter</kbd> atau <kbd class="bg-white text-dark border px-1 fw-bold">&darr;</kbd> untuk turun ke baris berikutnya &bull;
                    Teks otomatis ter-blok saat sel aktif untuk mempermudah penggantian nilai.
                </div>
            </div>
            <div class="fs-11 text-muted fw-semibold mt-1 mt-md-0">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1">
                    <i class="ri-pages-line me-1"></i> 1 Lembar = 1 Blending (1 Pasangan Batch)
                </span>
            </div>
        </div>

        <!-- Dokumen Sheet Container -->
        <div id="batchBlocksContainer">
            <!-- Di-render dinamis per Siklus Blending lewat JavaScript -->
        </div>

        <!-- Tombol Tambah Blok Tangki/Batch -->
        <div class="text-center mb-5">
            <button type="button" class="btn btn-outline-primary btn-md shadow-sm px-4" id="btnAddBatchBlock" tabindex="-1">
                <i class="ri-add-circle-line me-1 fs-16 align-middle"></i> Tambah Lembar Siklus Blending (Tangki Baru)
            </button>
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
        renderBatchBlocks(currentDocData ? currentDocData.batch_blocks : []);
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
                url: "{{ route('doc-blending-awal.fetch') }}",
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
                    Swal.fire('Error', 'Gagal mengambil data formulir blending.', 'error');
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

        // Event Tambah Blok Batch Baru
        $('#btnAddBatchBlock').on('click', function() {
            var currentBlocks = collectBatchBlocksFromDOM();
            var nextNum = currentBlocks.length + 1;
            var nextBatchStart = (currentBlocks.length * 2) + 1;
            var nextBatchEnd = nextBatchStart + 1;
            var defaultDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';

            var newBlock = {
                jenis_produk: (currentDocData && currentDocData.variant) ? currentDocData.variant : 'Kecap Sedap',
                tanggal_produksi: defaultDate,
                jam_produksi: '08:00',
                kode_shift_grup: '',
                batch: nextBatchStart + '-' + nextBatchEnd,
                no_blending: String(nextNum),
                volume_awal: '10000 L',
                catatan: '',
                pic_sampling: '',
                pic_analis: (currentDocData && currentDocData.pic_analis) ? currentDocData.pic_analis : '',
                pic_checker: '',
                rows: [
                    { sampling_ke: '*1 (BL Awal)', vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: 'OK', aroma: 'OK', warna: 'Hitam', buih: "Tidak\nAda", aw: '', waktu_adjustment: '', disposisi: 'Release' },
                    { sampling_ke: "*2 (BL After Adj/\nResampling 1)", vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' },
                    { sampling_ke: "*3 (BL After Adj/\nResampling 2)", vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' },
                    { sampling_ke: '(dst)', vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' },
                    { sampling_ke: '', vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' },
                    { sampling_ke: '', vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' },
                    { sampling_ke: '', vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' },
                    { sampling_ke: '', vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' },
                    { sampling_ke: '', vol_tangki: 'Awal', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: 'Release' },
                    { sampling_ke: '', vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' },
                    { sampling_ke: '', vol_tangki: '', serah_terima_jam: '', serah_terima_pic: '', bj: '', brix: '', ph: '', nacl: '', visco: '', organo: '', aroma: '', warna: '', buih: '', aw: '', waktu_adjustment: '', disposisi: '' }
                ]
            };

            currentBlocks.push(newBlock);
            renderBatchBlocks(currentBlocks);
        });

        // 1. Auto-select text on focus (Membuat input via Tab langsung mengganti isi tanpa perlu backspace)
        $(document).on('focus', '.clean-cell-input, .clean-cell-textarea, .clean-meta-input, .clean-sign-input, .clean-textarea', function() {
            var el = this;
            setTimeout(function() {
                if (typeof el.select === 'function') {
                    el.select();
                }
            }, 30);
        });

        // Auto-expand textarea saat diketik agar teks tidak terpotong
        $(document).on('input', '.clean-cell-textarea', function() {
            this.style.height = 'auto';
            var newH = Math.max(48, this.scrollHeight + 2);
            this.style.height = newH + 'px';
        });

        $(window).on('resize', adjustAllTextareaHeights);

        // 2. Keyboard Navigation: Enter (turun baris), Shift+Enter (naik baris), Panah Bawah/Atas (Excel Mode)
        $(document).on('keydown', '.table-blending-data .clean-cell-input, .table-blending-data .clean-cell-textarea', function(e) {
            var $input = $(this);
            var $td = $input.closest('td');
            var colIdx = $td.index();
            var $tr = $input.closest('tr.data-row');

            if (e.key === 'Enter') {
                // Jika sedang di dalam textarea dan bukan Ctrl+Enter, biarkan Enter membuat baris baru ke bawah (newline)
                if ($input.is('textarea') && !e.ctrlKey) {
                    return; // Normal Enter memasukkan baris baru di textarea
                }

                if (e.shiftKey) {
                    e.preventDefault();
                    var $prevTr = $tr.prev('tr.data-row');
                    if ($prevTr.length) {
                        var $target = $prevTr.children('td').eq(colIdx).find('.clean-cell-input, .clean-cell-textarea');
                        if ($target.length) $target.focus();
                    }
                } else if (!e.altKey) {
                    e.preventDefault();
                    var $nextTr = $tr.next('tr.data-row');
                    if ($nextTr.length) {
                        var $target = $nextTr.children('td').eq(colIdx).find('.clean-cell-input, .clean-cell-textarea');
                        if ($target.length) $target.focus();
                    }
                }
            } else if (e.key === 'ArrowDown') {
                if ($input.is('input') || $input.val() === '') {
                    var $nextTr = $tr.next('tr.data-row');
                    if ($nextTr.length) {
                        var $target = $nextTr.children('td').eq(colIdx).find('.clean-cell-input, .clean-cell-textarea');
                        if ($target.length) {
                            e.preventDefault();
                            $target.focus();
                        }
                    }
                }
            } else if (e.key === 'ArrowUp') {
                if ($input.is('input') || $input.val() === '') {
                    var $prevTr = $tr.prev('tr.data-row');
                    if ($prevTr.length) {
                        var $target = $prevTr.children('td').eq(colIdx).find('.clean-cell-input, .clean-cell-textarea');
                        if ($target.length) {
                            e.preventDefault();
                            $target.focus();
                        }
                    }
                }
            }
        });

        // Event Simpan Dokumen
        $('#btnSaveDoc').on('click', function() {
            var poId = $('#selectPo').val();
            if (!poId) {
                Swal.fire('Peringatan', 'Silakan pilih Nomor PO terlebih dahulu.', 'warning');
                return;
            }

            var blocks = collectBatchBlocksFromDOM();
            var payload = {
                production_batch_id: poId,
                tanggal_record_doc: $('#globalTanggalDoc').val(),
                halaman: blocks.length > 0 ? ('1 / ' + blocks.length) : '1',
                catatan: blocks.length > 0 ? (blocks[0].catatan || '') : '',
                pic_sampling: blocks.length > 0 ? (blocks[0].pic_sampling || '') : '',
                pic_analis: blocks.length > 0 ? (blocks[0].pic_analis || '') : '',
                pic_checker: blocks.length > 0 ? (blocks[0].pic_checker || '') : '',
                batch_blocks: blocks
            };

            Swal.fire({
                title: 'Menyimpan Dokumen...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: "{{ route('doc-blending-awal.store') }}",
                type: 'POST',
                data: payload,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Formulir Blending berhasil disimpan.',
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
        $('#btnExportExcel').attr('href', "{{ route('doc-blending-awal.export') }}?po_id=" + poId);
        $('#btnPrintView').attr('href', "{{ url('/doc-blending-awal/print') }}/" + poId);
    }

    function populateDocData(data) {
        $('#globalTanggalDoc').val(data.tanggal_record_doc || '{{ date("Y-m-d") }}');
        renderBatchBlocks(data.batch_blocks || []);
    }

    function renderBatchBlocks(blocks) {
        var container = $('#batchBlocksContainer');
        container.empty();

        if (!blocks || blocks.length === 0) {
            container.html('<div class="text-center py-5 text-muted border border-dashed rounded bg-white mb-4">Belum ada data lembar siklus blending. Silakan klik tombol di bawah untuk menambah lembar blending.</div>');
            return;
        }

        var totalBlocks = blocks.length;
        var globalDocDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';

        blocks.forEach(function(block, bIdx) {
            var blockHtml = `
            <div class="lembar-sheet-card" data-block-index="${bIdx}">
                <!-- Top Header Action Bar -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <span class="lembar-header-badge">
                            <i class="ri-pages-line"></i> LEMBAR BLENDING #${bIdx + 1}
                        </span>
                        <span class="badge bg-light text-dark border ms-2">
                            Pasangan Batch ${escapeHtml(block.batch || '')} (Tangki No. ${escapeHtml(block.no_blending || String(bIdx + 1))})
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary ms-2">
                            1 Halaman = 1 Pasangan Batch (1-2, 3-4, 5-6, …)
                        </span>
                    </div>
                    <div>
                        ${totalBlocks > 1 ? `
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-delete-block" data-block-index="${bIdx}" tabindex="-1">
                            <i class="ri-delete-bin-line me-1"></i> Hapus Lembar Ini
                        </button>
                        ` : ''}
                    </div>
                </div>

                <!-- 1. Header Dokumen Resmi Responsive Grid -->
                <div class="row align-items-center mb-3 pb-2 border-bottom">
                    <div class="col-md-3 col-sm-4">
                        <div class="doc-logo-box p-1">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 40px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-4 text-center">
                        <div class="doc-title-main">HASIL ANALISIS PROSES BLENDING</div>
                    </div>
                    <div class="col-md-3 col-sm-4 text-end">
                        <table class="float-end border mb-1" style="font-size: 0.80rem;">
                            <tr>
                                <td class="p-1 border-bottom text-start fw-bold bg-light">Tanggal Record Doc</td>
                                <td class="p-1 border-bottom text-start doc-date-display">: ${globalDocDate}</td>
                            </tr>
                            <tr>
                                <td class="p-1 text-start fw-bold bg-light">Halaman</td>
                                <td class="p-1 text-start fw-semibold">: ${bIdx + 1} / ${totalBlocks}</td>
                            </tr>
                        </table>
                        <div class="clearfix"></div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border fs-11 fst-italic">Notes: 1 lembar: 1 blending (1 pasangan batch)</span>
                    </div>
                </div>

                <!-- 2. Metadata Siklus Blending (Tangki) -->
                <table class="w-100 meta-input-group mb-3">
                    <tr>
                        <td width="14%" class="fw-bold bg-light">Jenis Produk</td>
                        <td width="36%">
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-jenis" placeholder="Kecap Sedap 550ml" value="${escapeHtml(block.jenis_produk || '')}">
                            </div>
                        </td>
                        <td width="14%" class="fw-bold bg-light">Batch</td>
                        <td width="36%">
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-batch" placeholder="1-2" value="${escapeHtml(block.batch || '')}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tanggal Produksi</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="date" class="form-control form-control-sm block-meta-tanggal" value="${escapeHtml(block.tanggal_produksi || globalDocDate)}">
                            </div>
                        </td>
                        <td class="fw-bold bg-light">No. Blending</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-no-blending" placeholder="1" value="${escapeHtml(block.no_blending || String(bIdx + 1))}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jam Produksi</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-jam" placeholder="02:33" value="${escapeHtml(block.jam_produksi || '')}">
                            </div>
                        </td>
                        <td class="fw-bold bg-light">Volume awal</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-volume" placeholder="10000 L" value="${escapeHtml(block.volume_awal || '')}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Kode Shift & Grup</td>
                        <td colspan="3">
                            <div class="d-flex align-items-center">
                                <span class="me-1 fw-bold">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-shift" placeholder="3/C" value="${escapeHtml(block.kode_shift_grup || '')}">
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- 3. Tabel Analisis Proses Blending -->
                <div class="d-flex justify-content-between align-items-center mb-1 text-muted fs-11">
                    <span class="badge bg-light text-secondary border"><i class="ri-arrow-left-right-line me-1"></i> Geser horizontal untuk melihat seluruh kolom</span>
                </div>
                <div class="table-responsive">
                    <table class="table-blending-data">
                        <colgroup>
                            <col style="width: 135px;"> <!-- Sampling ke- -->
                            <col style="width: 85px;">  <!-- Vol Tangki (L) -->
                            <col style="width: 65px;">  <!-- Jam -->
                            <col style="width: 90px;">  <!-- PIC -->
                            <col style="width: 65px;">  <!-- BJ (g/mL) -->
                            <col style="width: 65px;">  <!-- Brix -->
                            <col style="width: 60px;">  <!-- pH -->
                            <col style="width: 65px;">  <!-- % NaCl -->
                            <col style="width: 80px;">  <!-- Viskositas (ps) -->
                            <col style="width: 65px;">  <!-- Organo -->
                            <col style="width: 65px;">  <!-- Aroma -->
                            <col style="width: 70px;">  <!-- Warna -->
                            <col style="width: 65px;">  <!-- Buih -->
                            <col style="width: 60px;">  <!-- Aw -->
                            <col style="width: 205px;"> <!-- Waktu & Adjustment -->
                            <col style="width: 245px;"> <!-- Disposisi/ Keterangan -->
                        </colgroup>
                        <thead>
                            <tr>
                                <th rowspan="2" class="th-samp">Sampling<br>ke-</th>
                                <th rowspan="2">Vol Tangki<br>(L)</th>
                                <th colspan="2">Serah Terima</th>
                                <th colspan="10">Analisis</th>
                                <th rowspan="2">Waktu &<br>Adjustment</th>
                                <th rowspan="2">Disposisi/<br>Keterangan</th>
                            </tr>
                            <tr>
                                <th>Jam</th>
                                <th>PIC</th>
                                <th>BJ<br>(g/mL)</th>
                                <th>Brix</th>
                                <th>pH</th>
                                <th>% NaCl</th>
                                <th>Viskositas<br>(ps)</th>
                                <th>Organo</th>
                                <th>Aroma</th>
                                <th>Warna</th>
                                <th>Buih</th>
                                <th>Aw</th>
                            </tr>
                        </thead>
                        <tbody class="block-table-body" data-block-index="${bIdx}">
                            ${renderBlockRows(block.rows || [], bIdx)}
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between mt-2 mb-2">
                    <div class="text-muted fst-italic fs-11">
                        <i class="ri-information-line text-warning me-1"></i> *Data turun blending diisi di baris berwarna kuning (Vol Tangki: Awal)
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 btn-add-row" data-block-index="${bIdx}" tabindex="-1">
                            <i class="ri-add-line"></i> Tambah Baris
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-delete-row" data-block-index="${bIdx}" tabindex="-1">
                            <i class="ri-subtract-line"></i> Hapus Baris Terakhir
                        </button>
                    </div>
                </div>

                <!-- 4. Footer: Catatan & Tanda Tangan Per Lembar -->
                <div class="signature-container mt-2">
                    <div class="row g-0">
                        <div class="col-md-5 p-2 border-end">
                            <strong class="d-block mb-1 fs-12">Catatan :</strong>
                            <textarea class="form-control form-control-sm block-catatan" rows="4" placeholder="Ketik catatan khusus proses blending lembar ini...">${escapeHtml(block.catatan || '')}</textarea>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-0 text-center h-100">
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Disampling oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom block-pic-sampling" placeholder="Nama PIC" value="${escapeHtml(block.pic_sampling || '')}">
                                        <small class="text-muted d-block mt-1">Produksi</small>
                                    </div>
                                </div>
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Dianalisis oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom block-pic-analis" placeholder="Nama Analis" value="${escapeHtml(block.pic_analis || '')}">
                                        <small class="text-muted d-block mt-1">QC Analis</small>
                                    </div>
                                </div>
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Dicek oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom block-pic-checker" placeholder="Nama SPV/MNG" value="${escapeHtml(block.pic_checker || '')}">
                                        <small class="text-muted d-block mt-1">Staff/SPV/MNG QC</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0 border-top bg-light">
                        <div class="col-12 text-end px-3 py-1">
                            <span class="fs-11 fw-bold text-muted fst-italic">FRM/QLB/04/104/005-01</span>
                        </div>
                    </div>
                </div>

            </div>
            `;
            container.append(blockHtml);
        });

        attachBlockEvents();
        adjustAllTextareaHeights();
    }

    function renderBlockRows(rows, bIdx) {
        if (!rows || rows.length === 0) return '';
        var html = '';
        rows.forEach(function(row, rIdx) {
            var sampValue = row.sampling_ke !== undefined && row.sampling_ke !== null ? String(row.sampling_ke).trim() : '';

            // Otomatis enter ke bawah jika label After Adj / Resampling belum memiliki enter
            if (sampValue === '*2 (BL After Adj/Resampling 1)' || sampValue.includes('*2 (BL After Adj/Resampling 1)')) {
                sampValue = "*2 (BL After Adj/\nResampling 1)";
            } else if (sampValue === '*3 (BL After Adj/Resampling 2)' || sampValue.includes('*3 (BL After Adj/Resampling 2)')) {
                sampValue = "*3 (BL After Adj/\nResampling 2)";
            } else if (sampValue.includes('(BL After Adj/Resampling') && !sampValue.includes("\n")) {
                sampValue = sampValue.replace('(BL After Adj/Resampling', "(BL After Adj/\nResampling");
            }

            var volValue = row.vol_tangki !== undefined && row.vol_tangki !== null ? String(row.vol_tangki).trim() : '';
            var serahJam = row.serah_terima_jam !== undefined && row.serah_terima_jam !== null ? String(row.serah_terima_jam).trim() : '';
            var serahPic = row.serah_terima_pic !== undefined && row.serah_terima_pic !== null ? String(row.serah_terima_pic).trim() : '';
            var bjVal = row.bj !== undefined && row.bj !== null ? String(row.bj).trim() : '';
            var brixVal = row.brix !== undefined && row.brix !== null ? String(row.brix).trim() : '';
            var phVal = row.ph !== undefined && row.ph !== null ? String(row.ph).trim() : '';
            var naclVal = row.nacl !== undefined && row.nacl !== null ? String(row.nacl).trim() : '';
            var viscoVal = row.visco !== undefined && row.visco !== null ? String(row.visco).trim() : '';
            var organoVal = row.organo !== undefined && row.organo !== null ? String(row.organo).trim() : '';
            var aromaVal = row.aroma !== undefined && row.aroma !== null ? String(row.aroma).trim() : '';
            var warnaVal = row.warna !== undefined && row.warna !== null ? String(row.warna).trim() : '';
            var awVal = row.aw !== undefined && row.aw !== null ? String(row.aw).trim() : '';
            var adjValue = row.waktu_adjustment !== undefined && row.waktu_adjustment !== null ? String(row.waktu_adjustment).trim() : '';
            var dispValue = row.disposisi !== undefined && row.disposisi !== null ? String(row.disposisi).trim() : '';

            var isAwalTurun = (volValue.toLowerCase() === 'awal') || (sampValue.toLowerCase() === 'turun blending');

            var buihRaw = row.buih ? String(row.buih).trim() : '';
            if (buihRaw.toLowerCase() === 'tidak ada' || buihRaw.toLowerCase() === 'tidak\nada') {
                buihRaw = "Tidak\nAda";
            }

            // Bersih tanpa placeholder berulang di baris kosong
            var sampPh = '';
            var volPh = '';
            var jamPh = '';
            var adjPh = '';
            var dispPh = '';

            if (isAwalTurun) {
                sampPh = '';
                volPh = 'Awal';
                jamPh = 'Scan Turun';
                adjPh = 'Disposisi Turun...';
                dispPh = 'Keterangan Turun...';
                if (!volValue) volValue = 'Awal';
            } else {
                sampPh = String(rIdx + 1);
                volPh = '10000';
                jamPh = '03:39';
                adjPh = 'Contoh: 02:50\nAir: 280 L; Garam: 20 kg';
                dispPh = 'Contoh: Release / Adjustment';
            }

            // Klasifikasi warna teks Disposisi
            var dispClass = '';
            if (dispValue) {
                var dispLower = dispValue.toLowerCase();
                if (dispLower.includes('reject')) {
                    dispClass = 'disp-reject';
                } else if (dispLower.includes('release')) {
                    dispClass = 'disp-release';
                } else if (dispLower.includes('adj') || dispLower.includes('adjustment')) {
                    dispClass = 'disp-adjustment';
                } else if (dispLower.includes('resampling') || dispLower.includes('hold')) {
                    dispClass = 'disp-resampling';
                } else {
                    dispClass = 'fw-semibold text-dark';
                }
            }

            html += `
            <tr class="data-row ${isAwalTurun ? 'row-turun-blending' : ''}" data-row-index="${rIdx}">
                <td><textarea class="clean-cell-textarea r-sampling-ke fw-bold text-center" rows="2" placeholder="${sampPh}">${escapeHtml(sampValue)}</textarea></td>
                <td><input type="text" class="clean-cell-input r-vol-tangki text-center ${isAwalTurun ? 'fw-bold text-dark' : 'fw-semibold'}" placeholder="${volPh}" value="${escapeHtml(volValue)}"></td>
                <td><input type="text" class="clean-cell-input r-serah-jam text-center" placeholder="${jamPh}" value="${escapeHtml(serahJam)}"></td>
                <td><input type="text" class="clean-cell-input r-serah-pic text-center" value="${escapeHtml(serahPic)}"></td>
                <td><input type="text" class="clean-cell-input r-bj text-center" value="${escapeHtml(bjVal)}"></td>
                <td><input type="text" class="clean-cell-input r-brix text-center" value="${escapeHtml(brixVal)}"></td>
                <td><input type="text" class="clean-cell-input r-ph text-center" value="${escapeHtml(phVal)}"></td>
                <td><input type="text" class="clean-cell-input r-nacl text-center" value="${escapeHtml(naclVal)}"></td>
                <td><input type="text" class="clean-cell-input r-visco text-center" value="${escapeHtml(viscoVal)}"></td>
                <td><input type="text" class="clean-cell-input r-organo text-center" value="${escapeHtml(organoVal)}"></td>
                <td><input type="text" class="clean-cell-input r-aroma text-center" value="${escapeHtml(aromaVal)}"></td>
                <td><input type="text" class="clean-cell-input r-warna text-center" value="${escapeHtml(warnaVal)}"></td>
                <td><textarea class="clean-cell-input r-buih" rows="2" style="resize:none; padding:4px 0; line-height:1.15; font-size:0.75rem; min-height:36px; overflow:hidden; text-align:center; white-space:pre-wrap;">${escapeHtml(buihRaw)}</textarea></td>
                <td><input type="text" class="clean-cell-input r-aw text-center" value="${escapeHtml(awVal)}"></td>
                <td><textarea class="clean-cell-textarea r-waktu-adj text-center" rows="2" placeholder="${adjPh}">${escapeHtml(adjValue)}</textarea></td>
                <td><textarea class="clean-cell-textarea r-disposisi text-center ${dispClass}" rows="2" placeholder="${dispPh}">${escapeHtml(dispValue)}</textarea></td>
            </tr>
            `;
        });
        return html;
    }

    function attachBlockEvents() {
        $('.btn-delete-block').off('click').on('click', function() {
            var bIdx = $(this).data('block-index');
            Swal.fire({
                title: 'Hapus Lembar Ini?',
                text: 'Lembar Siklus Blending #' + (bIdx + 1) + ' akan dihapus dari tampilan formulir.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var currentBlocks = collectBatchBlocksFromDOM();
                    currentBlocks.splice(bIdx, 1);
                    renderBatchBlocks(currentBlocks);
                }
            });
        });

        $('.btn-add-row').off('click').on('click', function() {
            var bIdx = $(this).data('block-index');
            var currentBlocks = collectBatchBlocksFromDOM();
            var nextSamp = (currentBlocks[bIdx].rows.length || 0) + 1;
            currentBlocks[bIdx].rows.push({
                sampling_ke: String(nextSamp),
                vol_tangki: '',
                serah_terima_jam: '',
                serah_terima_pic: '',
                bj: '',
                brix: '',
                ph: '',
                nacl: '',
                visco: '',
                organo: '',
                aroma: '',
                warna: '',
                buih: '',
                aw: '',
                waktu_adjustment: '',
                disposisi: ''
            });
            renderBatchBlocks(currentBlocks);
        });

        $('.btn-delete-row').off('click').on('click', function() {
            var bIdx = $(this).data('block-index');
            var currentBlocks = collectBatchBlocksFromDOM();
            if (currentBlocks[bIdx].rows.length > 1) {
                currentBlocks[bIdx].rows.pop();
                renderBatchBlocks(currentBlocks);
            } else {
                Swal.fire('Info', 'Minimal satu baris tersisa pada lembar ini.', 'info');
            }
        });
    }

    function collectBatchBlocksFromDOM() {
        var blocks = [];
        $('.lembar-sheet-card').each(function(idx) {
            var blockCard = $(this);
            var blockObj = {
                jenis_produk: blockCard.find('.block-meta-jenis').val() || '',
                tanggal_produksi: blockCard.find('.block-meta-tanggal').val() || '',
                jam_produksi: blockCard.find('.block-meta-jam').val() || '',
                kode_shift_grup: blockCard.find('.block-meta-shift').val() || '',
                batch: blockCard.find('.block-meta-batch').val() || '',
                no_blending: blockCard.find('.block-meta-no-blending').val() || String(idx + 1),
                volume_awal: blockCard.find('.block-meta-volume').val() || '',
                catatan: blockCard.find('.block-catatan').val() || '',
                pic_sampling: blockCard.find('.block-pic-sampling').val() || '',
                pic_analis: blockCard.find('.block-pic-analis').val() || '',
                pic_checker: blockCard.find('.block-pic-checker').val() || '',
                rows: []
            };

            blockCard.find('.data-row').each(function() {
                var row = $(this);
                blockObj.rows.push({
                    sampling_ke: row.find('.r-sampling-ke').val() || '',
                    vol_tangki: row.find('.r-vol-tangki').val() || '',
                    serah_terima_jam: row.find('.r-serah-jam').val() || '',
                    serah_terima_pic: row.find('.r-serah-pic').val() || '',
                    bj: row.find('.r-bj').val() || '',
                    brix: row.find('.r-brix').val() || '',
                    ph: row.find('.r-ph').val() || '',
                    nacl: row.find('.r-nacl').val() || '',
                    visco: row.find('.r-visco').val() || '',
                    organo: row.find('.r-organo').val() || '',
                    aroma: row.find('.r-aroma').val() || '',
                    warna: row.find('.r-warna').val() || '',
                    buih: row.find('.r-buih').val() || '',
                    aw: row.find('.r-aw').val() || '',
                    waktu_adjustment: row.find('.r-waktu-adj').val() || '',
                    disposisi: row.find('.r-disposisi').val() || ''
                });
            });

            blocks.push(blockObj);
        });
        return blocks;
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

    function adjustAllTextareaHeights() {
        setTimeout(function() {
            $('.clean-cell-textarea').each(function() {
                this.style.height = 'auto';
                var scrollH = this.scrollHeight;
                if (scrollH > 48) {
                    this.style.height = (scrollH + 2) + 'px';
                } else {
                    this.style.height = '48px';
                }
            });
        }, 15);
    }
</script>
@endsection
