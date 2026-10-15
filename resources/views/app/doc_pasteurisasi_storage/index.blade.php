@extends('layouts.component.main')
@section('title', 'Hasil Analisis Pasteurisasi dan Storage Tank')

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
        font-size: 1.22rem;
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
        border: 1px solid #cbd5e1;
        border-radius: 4px;
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

    /* Table QC Data (Pasteurisasi & Storage Tank) */
    .table-qc-data {
        width: 100%;
        min-width: 1400px;
        border-collapse: collapse;
        font-size: 0.82rem;
        margin-bottom: 0;
        table-layout: fixed;
        background: #ffffff;
    }

    .table-qc-data th, .table-qc-data td {
        border: 1px solid #64748b;
        padding: 5px 3px !important;
        text-align: center;
        vertical-align: middle;
        position: relative;
    }

    /* Header styling */
    .table-qc-data th.th-past-main {
        background-color: #dcfce7 !important;
        color: #14532d !important;
        font-weight: 700;
        font-size: 0.80rem;
        padding: 6px 3px !important;
        border-color: #475569;
        line-height: 1.25;
    }

    .table-qc-data th.th-storage-main {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        font-weight: 700;
        font-size: 0.80rem;
        padding: 6px 3px !important;
        border-color: #475569;
        line-height: 1.25;
    }

    .table-qc-data th.th-sub {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 4px 2px !important;
        border-color: #64748b;
        line-height: 1.2;
        vertical-align: middle;
    }

    /* Row Hover */
    .table-qc-data tbody tr.data-row {
        transition: background-color 0.12s ease;
    }

    .table-qc-data tbody tr.data-row:hover td {
        background-color: #f8fafc;
    }

    /* Footer: Signature Container */
    .signature-container {
        border: 1px solid #1e293b;
        background: #ffffff;
        border-radius: 4px;
        overflow: hidden;
    }

    .sig-col {
        border-right: 1px solid #cbd5e1;
        padding: 6px 8px;
        text-align: center;
    }

    .sig-col:last-child {
        border-right: none;
    }

    .sig-space {
        height: 38px;
    }

    /* Scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title & Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dokumen Analisis Pasteurisasi & Storage Tank (FRM/QLB/04/104/006-01)</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('homepage.index') }}">Halaman Utama</a></li>
                            <li class="breadcrumb-item">Dokumen QC QLB</li>
                            <li class="breadcrumb-item active">FRM/QLB/04/104/006-01</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Control Bar: Pilih PO & Action Buttons -->
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body p-3">
                <div class="row align-items-center g-3">
                    <div class="col-lg-4 col-md-6">
                        <label for="po_selector" class="form-label fw-bold fs-13 mb-1">
                            <i class="ri-folder-open-line me-1 text-primary"></i> Pilih Nomor PO Batch Produksi:
                        </label>
                        <select id="po_selector" class="form-select form-select-sm">
                            <option value="">-- Pilih Production Batch / PO --</option>
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}" {{ $b->id == $selectedBatchId ? 'selected' : '' }}>
                                    {{ $b->po_number }} - {{ $b->variant ?? 'Kecap Manis' }} (Batch {{ $b->batch_range ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-8 col-md-12 text-md-end text-start d-flex flex-wrap gap-2 justify-content-md-end justify-content-start align-items-end">
                        <a href="{{ route('homepage.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                        @if(auth()->check() && in_array(strtolower(trim(auth()->user()->role ?? '')), ['foreman', 'supervisor', 'head of dapartement', 'head of department', 'dept head'], true))
                        <a href="#" class="btn btn-success btn-sm" id="btnExportExcel">
                            <i class="ri-file-excel-2-line me-1"></i> Download Excel
                        </a>
                        <a href="#" target="_blank" class="btn btn-info btn-sm text-white" id="btnPrintView">
                            <i class="ri-printer-line me-1"></i> Cetak / Print
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Container Lembar-Lembar Dokumen Resmi Dinamis (1 Lembar = 1 Blending / Pasangan Batch) -->
        <div id="batchBlocksContainer">
            <!-- Di-render dinamis per Pasangan Batch lewat JavaScript renderBatchBlocks() -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Data Dokumen Aktif
    var currentDocData = @json($initialData);

    $(document).ready(function() {
        // Setup CSRF Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Inisialisasi Tampilan Seluruh Lembar Batch & Link Aksi
        renderBatchBlocks(currentDocData ? (currentDocData.batch_blocks || []) : []);

        function updateActionLinks(poId) {
            if (!poId) return;
            $('#btnExportExcel').attr('href', "{{ route('doc-pasteurisasi-storage.export') }}?po_id=" + poId);
            $('#btnPrintView').attr('href', "{{ url('doc-pasteurisasi-storage/print') }}/" + poId);
        }

        var currentPoId = $('#po_selector').val();
        if (currentPoId) {
            updateActionLinks(currentPoId);
        }

        // Dropdown Ganti PO
        $('#po_selector').on('change', function() {
            var poId = $(this).val();
            if (poId) {
                updateActionLinks(poId);
                window.location.href = "{{ url('doc-pasteurisasi-storage') }}?po_id=" + poId;
            }
        });

        // Validasi tombol Export Excel & Cetak / Print jika PO belum dipilih
        $('#btnPrintView, #btnExportExcel').on('click', function(e) {
            var poId = $('#po_selector').val();
            if (!poId) {
                e.preventDefault();
                Swal.fire('Perhatian', 'Pilih nomor PO terlebih dahulu.', 'warning');
            }
        });
    });

    // Load Data via Ajax
    function loadDataByPo(poId, forceDb) {
        Swal.fire({
            title: 'Memuat Data...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        var url = "{{ route('doc-pasteurisasi-storage.fetch') }}?po_id=" + poId;
        if (forceDb) url += "&force_db=1";

        $.ajax({
            url: url,
            type: 'GET',
            success: function(res) {
                Swal.close();
                if (res.status === 'success' && res.data) {
                    currentDocData = res.data;
                    renderBatchBlocks(currentDocData.batch_blocks || []);
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Berhasil Dimuat',
                        timer: 1100,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire('Error', 'Gagal memuat data dokumen.', 'error');
            }
        });
    }

    // Render Seluruh Lembar Pasteurisasi & Storage Tank Per Pasangan Batch (Mirip Blending)
    function renderBatchBlocks(blocks) {
        var container = $('#batchBlocksContainer');
        container.empty();

        if (!blocks || blocks.length === 0) {
            if (currentDocData && (currentDocData.pasteurisasi_rows || currentDocData.storage_rows)) {
                blocks = [{
                    block_index: 0,
                    halaman: currentDocData.halaman || '1 / 1',
                    batch: currentDocData.batch || currentDocData.batch_range || '-',
                    kode_st: currentDocData.kode_st || 'ST 01',
                    jenis_produk: currentDocData.jenis_produk || currentDocData.variant || '-',
                    tanggal_produksi: currentDocData.tanggal_produksi || currentDocData.tanggal_record_doc || '-',
                    jam_produksi_start: currentDocData.jam_produksi_start || '',
                    jam_produksi_end: currentDocData.jam_produksi_end || '',
                    jam_produksi: currentDocData.jam_produksi || '-',
                    kode_shift_grup: currentDocData.kode_shift_grup || '-',
                    pasteurisasi_rows: currentDocData.pasteurisasi_rows || [],
                    storage_rows: currentDocData.storage_rows || [],
                    catatan: currentDocData.catatan || '',
                    pic_sampling_1: currentDocData.pic_sampling_1 || '',
                    pic_sampling_2: currentDocData.pic_sampling_2 || '',
                    pic_analis: currentDocData.pic_analis || '',
                    pic_checker: currentDocData.pic_checker || ''
                }];
            } else {
                container.html('<div class="text-center py-5 text-muted border border-dashed rounded bg-white mb-4">Belum ada data lembar siklus pasteurisasi & storage tank.</div>');
                return;
            }
        }

        var totalBlocks = blocks.length;
        var globalDocDate = (currentDocData && currentDocData.tanggal_record_doc) ? currentDocData.tanggal_record_doc : '{{ date("Y-m-d") }}';

        blocks.forEach(function(block, bIdx) {
            var halamanStr = block.halaman || ((bIdx + 1) + ' / ' + totalBlocks);
            var jamRange = '-';
            if (block.jam_produksi && block.jam_produksi !== '-') {
                jamRange = block.jam_produksi;
            } else if (block.jam_produksi_start) {
                jamRange = block.jam_produksi_start + (block.jam_produksi_end ? ' s/d ' + block.jam_produksi_end : '');
            } else if (currentDocData && currentDocData.jam_produksi_start) {
                jamRange = currentDocData.jam_produksi_start + (currentDocData.jam_produksi_end ? ' s/d ' + currentDocData.jam_produksi_end : '');
            }

            // 1. Render Baris Pasteurisasi (Default min 6 baris)
            var pastRows = block.pasteurisasi_rows || [];
            var pastHtml = '';
            var pastCount = Math.max(6, pastRows.length);
            for (var i = 0; i < pastCount; i++) {
                var row = pastRows[i] || {};
                var sampVal = row.sampling ? String(row.sampling).trim() : (i === 0 ? 'Tengah' : '');
                var dispColor = 'text-secondary';
                var dispVal = (row.disposisi || '').trim();
                if (dispVal.toLowerCase().includes('reject') || dispVal.toLowerCase().includes('not ok')) {
                    dispColor = 'text-danger fw-bold';
                } else if (dispVal.toLowerCase().includes('release') || dispVal.toLowerCase().includes('ok')) {
                    dispColor = 'text-success fw-bold';
                }

                pastHtml += `
                    <tr class="data-row past-tr">
                        <td class="fw-semibold bg-light">${escapeHtml(sampVal)}</td>
                        <td>${escapeHtml(row.jam || '-')}</td>
                        <td>${escapeHtml(row.pic || '-')}</td>
                        <td class="fw-semibold">${escapeHtml(row.bj || '-')}</td>
                        <td class="fw-semibold">${escapeHtml(row.brix || '-')}</td>
                        <td>${escapeHtml(row.ph || '-')}</td>
                        <td>${escapeHtml(row.aw || '-')}</td>
                        <td>${escapeHtml(row.viskositas || '-')}</td>
                        <td>${escapeHtml(row.organo || '-')}</td>
                        <td>${escapeHtml(row.aroma || '-')}</td>
                        <td>${escapeHtml(row.warna || '-')}</td>
                        <td>${escapeHtml(row.buih || '-')}</td>
                        <td>${escapeHtml(row.endapan || '-')}</td>
                        <td class="${dispColor} text-start px-2" style="white-space: pre-line;">${escapeHtml(dispVal || '-')}</td>
                    </tr>
                `;
            }

            // 2. Render Baris Storage Tank (Default min 5 baris)
            var stRows = block.storage_rows || [];
            var stHtml = '';
            var stCount = Math.max(5, stRows.length);
            for (var j = 0; j < stCount; j++) {
                var sRow = stRows[j] || {};
                var sampSt = sRow.sampling || '';
                if (!sampSt) {
                    if (j === 0) sampSt = 'Awal';
                    else if (j === 1) sampSt = 'Outlet';
                    else if (j < stRows.length) sampSt = 'Akhir';
                    else sampSt = '-';
                }
                var dispStColor = 'text-secondary';
                var dispStVal = (sRow.disposisi || '').trim();
                if (dispStVal.toLowerCase().includes('reject') || dispStVal.toLowerCase().includes('not ok')) {
                    dispStColor = 'text-danger fw-bold';
                } else if (dispStVal.toLowerCase().includes('release') || dispStVal.toLowerCase().includes('ok')) {
                    dispStColor = 'text-success fw-bold';
                }

                stHtml += `
                    <tr class="data-row st-tr">
                        <td class="fw-semibold bg-light">${escapeHtml(sampSt)}</td>
                        <td>${escapeHtml(sRow.jam || '-')}</td>
                        <td>${escapeHtml(sRow.pic || '-')}</td>
                        <td class="fw-semibold">${escapeHtml(sRow.bj || '-')}</td>
                        <td class="fw-semibold">${escapeHtml(sRow.brix || '-')}</td>
                        <td>${escapeHtml(sRow.ph || '-')}</td>
                        <td>${escapeHtml(sRow.nacl || '-')}</td>
                        <td>${escapeHtml(sRow.viskositas || '-')}</td>
                        <td>${escapeHtml(sRow.organo || '-')}</td>
                        <td>${escapeHtml(sRow.aroma || '-')}</td>
                        <td>${escapeHtml(sRow.warna || '-')}</td>
                        <td>${escapeHtml(sRow.buih || '-')}</td>
                        <td>${escapeHtml(sRow.endapan || '-')}</td>
                        <td>${escapeHtml(sRow.kristal || '-')}</td>
                        <td>${escapeHtml(sRow.aw || '-')}</td>
                        <td class="${dispStColor} text-start px-2" style="white-space: pre-line;">${escapeHtml(dispStVal || '-')}</td>
                    </tr>
                `;
            }

            var blockCardHtml = `
            <div class="lembar-sheet-card" data-block-index="${bIdx}">
                <!-- Top Header Action Bar -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="lembar-header-badge">
                            <i class="ri-pages-line"></i> LEMBAR PASTEURISASI & STORAGE #${bIdx + 1}
                        </span>
                        <span class="badge bg-light text-dark border">
                            Pasangan Batch ${escapeHtml(block.batch || '-')}
                        </span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                            1 Halaman = 1 Blending (1 Pasangan Batch)
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-light text-muted border fs-12">
                            <i class="ri-lock-line me-1"></i> Data Otentik
                        </span>
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
                        <div class="doc-title-main">HASIL ANALISIS PASTEURISASI DAN STORAGE TANK</div>
                    </div>
                    <div class="col-md-3 col-sm-4 text-end">
                        <table class="float-end border mb-1" style="font-size: 0.80rem;">
                            <tr>
                                <td class="p-1 border-bottom text-start fw-bold bg-light">Tanggal Record Doc</td>
                                <td class="p-1 border-bottom text-start fw-semibold">: ${escapeHtml(globalDocDate)}</td>
                            </tr>
                            <tr>
                                <td class="p-1 text-start fw-bold bg-light">Halaman</td>
                                <td class="p-1 text-start fw-semibold">: ${escapeHtml(halamanStr)}</td>
                            </tr>
                        </table>
                        <div class="clearfix"></div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border fs-11 fst-italic">Notes: 1 lembar: 1 blending (1 pasangan batch)</span>
                    </div>
                </div>

                <!-- 2. Metadata Grid (Clean Presentation) -->
                <table class="w-100 meta-input-group mb-3">
                    <tr>
                        <td width="14%" class="fw-bold bg-light">Jenis Produk</td>
                        <td width="36%">: <span class="fw-bold text-dark fs-13">${escapeHtml(block.jenis_produk || (currentDocData ? (currentDocData.jenis_produk || currentDocData.variant) : '') || '-')}</span></td>
                        <td width="14%" class="fw-bold bg-light">Kode Shift & Grup</td>
                        <td width="36%">: <span class="fw-bold text-primary fs-13">${escapeHtml(block.kode_shift_grup || (currentDocData ? currentDocData.kode_shift_grup : '') || '-')}</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Tanggal Produksi</td>
                        <td>: <span class="fw-semibold text-dark fs-13">${escapeHtml(block.tanggal_produksi || (currentDocData ? currentDocData.tanggal_produksi : '') || globalDocDate)}</span></td>
                        <td class="fw-bold bg-light">Batch</td>
                        <td>: <span class="fw-bold text-dark fs-13">${escapeHtml(block.batch || '-')}</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light">Jam Produksi</td>
                        <td colspan="3">: <span class="fw-semibold text-dark fs-13">${escapeHtml(jamRange)}</span></td>
                    </tr>
                </table>

                <!-- 3. Tabel 1: PASTEURISASI (Integrated Header Bar, 1040px Proportional Grid) -->
                <div class="table-responsive mb-4">
                    <table class="table-qc-data" style="min-width: 1040px;">
                        <colgroup>
                            <col style="width: 85px;">  <!-- Sampling -->
                            <col style="width: 55px;">  <!-- Jam -->
                            <col style="width: 80px;">  <!-- PIC -->
                            <col style="width: 65px;">  <!-- BJ (g/mL) -->
                            <col style="width: 55px;">  <!-- Brix -->
                            <col style="width: 50px;">  <!-- pH -->
                            <col style="width: 55px;">  <!-- Aw -->
                            <col style="width: 75px;">  <!-- Viskositas (ps) -->
                            <col style="width: 60px;">  <!-- Organo -->
                            <col style="width: 60px;">  <!-- Aroma -->
                            <col style="width: 65px;">  <!-- Warna -->
                            <col style="width: 55px;">  <!-- Buih -->
                            <col style="width: 60px;">  <!-- Endapan -->
                            <col style="width: 220px;"> <!-- Disposisi / Keterangan -->
                        </colgroup>
                        <thead>
                            <tr>
                                <th colspan="14" class="text-start px-3 py-2" style="background-color: #dcfce7; border: 1.5px solid #166534;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-temp-hot-line text-success fs-5"></i>
                                        <span class="fw-bold text-success fs-13" style="letter-spacing: 0.5px;">PASTEURISASI</span>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th rowspan="2" class="th-past-main">Sampling</th>
                                <th colspan="2" class="th-past-main">Serah Terima</th>
                                <th colspan="10" class="th-past-main">Analisis</th>
                                <th rowspan="2" class="th-past-main">Disposisi /<br>Keterangan<br><small class="d-block fw-normal text-muted" style="font-size: 0.68rem;">(Disposisi After Cooling)</small></th>
                            </tr>
                            <tr>
                                <th class="th-sub">Jam</th>
                                <th class="th-sub">PIC</th>
                                <th class="th-sub">BJ<br>(g/mL)</th>
                                <th class="th-sub">Brix</th>
                                <th class="th-sub">pH</th>
                                <th class="th-sub">Aw</th>
                                <th class="th-sub">Viskositas<br>(ps)</th>
                                <th class="th-sub">Organo</th>
                                <th class="th-sub">Aroma</th>
                                <th class="th-sub">Warna</th>
                                <th class="th-sub">Buih</th>
                                <th class="th-sub">Endapan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pastHtml}
                        </tbody>
                    </table>
                </div>

                <!-- 4. Tabel 2: STORAGE TANK (Integrated Header Bar, 1040px Proportional Grid) -->
                <div class="table-responsive mb-4">
                    <table class="table-qc-data" style="min-width: 1040px;">
                        <colgroup>
                            <col style="width: 80px;">  <!-- Sampling -->
                            <col style="width: 55px;">  <!-- Jam -->
                            <col style="width: 80px;">  <!-- PIC -->
                            <col style="width: 55px;">  <!-- BJ -->
                            <col style="width: 50px;">  <!-- Brix -->
                            <col style="width: 45px;">  <!-- pH -->
                            <col style="width: 55px;">  <!-- % NaCl -->
                            <col style="width: 70px;">  <!-- Viskositas (ps) -->
                            <col style="width: 55px;">  <!-- Organo -->
                            <col style="width: 55px;">  <!-- Aroma -->
                            <col style="width: 55px;">  <!-- Warna -->
                            <col style="width: 50px;">  <!-- Buih -->
                            <col style="width: 55px;">  <!-- Endapan -->
                            <col style="width: 55px;">  <!-- Kristal -->
                            <col style="width: 50px;">  <!-- Aw -->
                            <col style="width: 185px;"> <!-- Disposisi / Keterangan -->
                        </colgroup>
                        <thead>
                            <tr>
                                <th colspan="16" class="text-start px-3 py-2" style="background-color: #e0f2fe; border: 1.5px solid #0369a1;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ri-inbox-line text-primary fs-5"></i>
                                            <span class="fw-bold text-primary fs-13" style="letter-spacing: 0.5px;">STORAGE TANK</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 ms-2">
                                            <span class="fw-bold small text-dark">KODE ST :</span>
                                            <span class="badge bg-white text-primary border border-primary px-2 py-1 fw-bold fs-12">${escapeHtml(block.kode_st || (currentDocData ? currentDocData.kode_st : '') || '-')}</span>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th rowspan="2" class="th-storage-main">Sampling</th>
                                <th colspan="2" class="th-storage-main">Serah Terima</th>
                                <th colspan="12" class="th-storage-main">Analisis</th>
                                <th rowspan="2" class="th-storage-main">Disposisi /<br>Keterangan</th>
                            </tr>
                            <tr>
                                <th class="th-sub">Jam</th>
                                <th class="th-sub">PIC</th>
                                <th class="th-sub">BJ</th>
                                <th class="th-sub">Brix</th>
                                <th class="th-sub">pH</th>
                                <th class="th-sub">% NaCl</th>
                                <th class="th-sub">Viskositas<br>(ps)</th>
                                <th class="th-sub">Organo</th>
                                <th class="th-sub">Aroma</th>
                                <th class="th-sub">Warna</th>
                                <th class="th-sub">Buih</th>
                                <th class="th-sub">Endapan</th>
                                <th class="th-sub">Kristal</th>
                                <th class="th-sub">Aw</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${stHtml}
                        </tbody>
                    </table>
                </div>

                <!-- 5. Footer: Catatan & Tanda Tangan Resmi Sesuai Google Sheets -->
                <div class="signature-container mt-3">
                    <div class="row g-0">
                        <div class="col-md-4 p-2 border-end">
                            <strong class="d-block mb-1 fs-12 text-dark">Catatan :</strong>
                            <div class="text-dark fs-12 fst-italic p-1" style="min-height: 48px;">
                                ${escapeHtml(block.catatan || (currentDocData ? currentDocData.catatan : '') || '-')}
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-0 text-center h-100">
                                <div class="col-3 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Disampling oleh,</div>
                                    <div class="sig-space" style="height: 38px;"></div>
                                    <div>
                                        <div class="fw-bold text-dark border-bottom border-dark pb-1 mx-2 text-truncate">
                                            ${(block.pic_sampling_1 || (currentDocData ? currentDocData.pic_sampling_1 : '')) ? escapeHtml(block.pic_sampling_1 || currentDocData.pic_sampling_1) : '(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)'}
                                        </div>
                                        <small class="text-muted d-block mt-1">Produksi</small>
                                    </div>
                                </div>
                                <div class="col-3 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Disampling oleh,</div>
                                    <div class="sig-space" style="height: 38px;"></div>
                                    <div>
                                        <div class="fw-bold text-dark border-bottom border-dark pb-1 mx-2 text-truncate">
                                            ${(block.pic_sampling_2 || (currentDocData ? currentDocData.pic_sampling_2 : '')) ? escapeHtml(block.pic_sampling_2 || currentDocData.pic_sampling_2) : '(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)'}
                                        </div>
                                        <small class="text-muted d-block mt-1">Produksi</small>
                                    </div>
                                </div>
                                <div class="col-3 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Dianalisis oleh,</div>
                                    <div class="sig-space" style="height: 38px;"></div>
                                    <div>
                                        <div class="fw-bold text-dark border-bottom border-dark pb-1 mx-2 text-truncate">
                                            ${(block.pic_analis || (currentDocData ? currentDocData.pic_analis : '')) ? escapeHtml(block.pic_analis || currentDocData.pic_analis) : '(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)'}
                                        </div>
                                        <small class="text-muted d-block mt-1">QC Analis</small>
                                    </div>
                                </div>
                                <div class="col-3 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Dicek oleh,</div>
                                    <div class="sig-space" style="height: 38px;"></div>
                                    <div>
                                        <div class="fw-bold text-dark border-bottom border-dark pb-1 mx-2 text-truncate">
                                            ${(block.pic_checker || (currentDocData ? currentDocData.pic_checker : '')) ? escapeHtml(block.pic_checker || currentDocData.pic_checker) : '(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)'}
                                        </div>
                                        <small class="text-muted d-block mt-1">SPV/MNG QC</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0 border-top bg-light">
                        <div class="col-12 text-end px-3 py-1">
                            <span class="fs-11 fw-bold text-muted fst-italic">FRM/QLB/04/104/006-01</span>
                        </div>
                    </div>
                </div>

            </div>
            `;
            container.append(blockCardHtml);
        });
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
