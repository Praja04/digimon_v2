@extends('layouts.component.main')
@section('title', 'Hasil Analisis Proses Pelarutan')

@section('styles')
<style>
    .lembar-sheet-card {
        background: #ffffff;
        border: 2px solid #343a40;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 6px;
        padding: 24px;
        position: relative;
        margin-bottom: 35px;
    }

    .lembar-header-badge {
        background: #1e293b;
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

    .table-pelarutan {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
        margin-bottom: 10px;
    }

    .table-pelarutan th, .table-pelarutan td {
        border: 1px solid #000000;
        padding: 3px 2px;
        text-align: center;
        vertical-align: middle;
    }

    .table-pelarutan th {
        background-color: #f2f2f2;
        font-weight: 700;
        color: #000;
    }

    .table-pelarutan .table-input {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        text-align: center;
        font-size: 0.78rem;
        padding: 2px 2px;
        border-radius: 2px;
    }

    .table-pelarutan .table-input:focus, .table-pelarutan .table-input:hover {
        border-color: #405189;
        background: #ffffff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(64, 81, 137, 0.2);
    }

    .batch-sub-card {
        border: 1px solid #94a3b8;
        background: #fafafa;
        border-radius: 4px;
        padding: 12px;
        margin-bottom: 15px;
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
        padding: 2px 4px;
        font-size: 0.84rem;
    }

    .meta-input-group .form-control-sm {
        border: none;
        border-bottom: 1px dotted #888;
        border-radius: 0;
        padding: 2px 4px;
        background: transparent;
        font-size: 0.84rem;
    }

    .meta-input-group .form-control-sm:focus {
        border-bottom: 2px solid #405189;
        box-shadow: none;
        background: #ffffff;
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
                <strong>Format Resmi Pelarutan:</strong> Formulir diatur dalam format <strong>1 Lembar = 2 Batch</strong>. Setiap lembar terdiri dari 2 blok batch pelarutan (Dissolver) dengan masing-masing 5 baris data sampling, serta footer catatan & tanda tangan di bagian bawah.
            </div>
        </div>

        <!-- Container Lembar Pelarutan -->
        <div id="lembarSheetsContainer">
            <!-- Di-render otomatis lewat JS per 2 Batch per Lembar -->
        </div>

        <!-- Tombol Tambah Pasang Batch (Lembar Baru) -->
        <div class="text-center mb-5">
            <button type="button" class="btn btn-outline-primary btn-md shadow-sm px-4" id="btnAddPairBatch">
                <i class="ri-add-circle-line me-1 fs-16 align-middle"></i> Tambah 1 Lembar Baru (2 Batch Pelarutan)
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
                    jam_produksi: '08:00',
                    kode_shift_grup: 'Shift 1 / Grup A',
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
                        ${totalSheets > 1 ? `
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-delete-sheet" data-sheet-index="${pIdx}">
                            <i class="ri-delete-bin-line me-1"></i> Hapus Lembar Ini
                        </button>
                        ` : ''}
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
                        <table class="float-end border" style="font-size: 0.80rem;">
                            <tr>
                                <td class="p-1 border-bottom text-start fw-bold bg-light">Tanggal Record Doc</td>
                                <td class="p-1 border-bottom text-start doc-date-display">${globalDocDate}</td>
                            </tr>
                            <tr>
                                <td class="p-1 text-start fw-bold bg-light">Halaman</td>
                                <td class="p-1 text-start fw-semibold">${pIdx + 1} / ${totalSheets}</td>
                            </tr>
                        </table>
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
                            <textarea class="form-control form-control-sm" id="globalCatatan" rows="4" placeholder="Ketik catatan khusus proses pelarutan di sini...">${escapeHtml(catatanVal)}</textarea>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-0 text-center h-100">
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Disampling oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="globalPicSampling" placeholder="Nama PIC" value="${escapeHtml(picSamp)}">
                                        <small class="text-muted d-block mt-1">Produksi</small>
                                    </div>
                                </div>
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Dianalisis oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="globalPicAnalis" placeholder="Nama Analis" value="${escapeHtml(picAnalis)}">
                                        <small class="text-muted d-block mt-1">QC Analis</small>
                                    </div>
                                </div>
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Dicek oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="globalPicChecker" placeholder="Nama SPV/MNG" value="${escapeHtml(picCheck)}">
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
            <!-- Meta Table -->
            <table class="w-100 meta-input-group mb-2">
                <tr>
                    <td width="15%" class="fw-bold">Jenis Produk</td>
                    <td width="35%">
                        <div class="d-flex align-items-center">
                            <span class="me-1">:</span>
                            <input type="text" class="form-control form-control-sm block-meta-jenis" value="${escapeHtml(block.jenis_produk || '')}">
                        </div>
                    </td>
                    <td width="15%" class="fw-bold">Batch</td>
                    <td width="35%">
                        <div class="d-flex align-items-center">
                            <span class="me-1">:</span>
                            <input type="text" class="form-control form-control-sm block-meta-batch" value="${escapeHtml(block.batch || '')}">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">Tanggal Produksi</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-1">:</span>
                            <input type="date" class="form-control form-control-sm block-meta-tanggal" value="${escapeHtml(block.tanggal_produksi || globalDocDate)}">
                        </div>
                    </td>
                    <td class="fw-bold">No. Dissolver</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-1">:</span>
                            <input type="text" class="form-control form-control-sm block-meta-no-dissolver" value="${escapeHtml(block.no_dissolver || String(bIdx + 1))}">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">Jam Produksi</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-1">:</span>
                            <input type="text" class="form-control form-control-sm block-meta-jam" placeholder="HH:mm - HH:mm" value="${escapeHtml(block.jam_produksi || '')}">
                        </div>
                    </td>
                    <td class="fw-bold">Volume</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-1">:</span>
                            <input type="text" class="form-control form-control-sm block-meta-volume" value="${escapeHtml(block.volume || '')}">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold">Kode Shift & Grup</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-1">:</span>
                            <input type="text" class="form-control form-control-sm block-meta-shift" value="${escapeHtml(block.kode_shift_grup || 'Shift 1 / Grup A')}">
                        </div>
                    </td>
                    <td colspan="2"></td>
                </tr>
            </table>

            <!-- Tabel Analisis Pelarutan (5 Baris Standar) -->
            <div class="table-responsive">
                <table class="table-pelarutan">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 4%;">Sampling ke-</th>
                            <th colspan="2" style="width: 10%;">Serah Terima</th>
                            <th colspan="5" style="width: 36%;">Pelarutan I (GGA)</th>
                            <th rowspan="2" style="width: 4%;">Sampling ke-</th>
                            <th colspan="2" style="width: 10%;">Serah Terima</th>
                            <th colspan="5" style="width: 36%;">Pelarutan II (GGAS)</th>
                            <th rowspan="2" style="width: 8%;">Disposisi</th>
                        </tr>
                        <tr>
                            <th style="width: 4%;">Jam</th>
                            <th style="width: 6%;">PIC</th>
                            <th style="width: 5%;">Brix</th>
                            <th style="width: 5%;">%NaCl</th>
                            <th style="width: 6%;">Warna</th>
                            <th style="width: 5%;">Organo</th>
                            <th style="width: 15%;">Waktu & Adjustment</th>
                            <th style="width: 4%;">Jam</th>
                            <th style="width: 6%;">PIC</th>
                            <th style="width: 5%;">Brix</th>
                            <th style="width: 5%;">%NaCl</th>
                            <th style="width: 6%;">Warna</th>
                            <th style="width: 5%;">Organo</th>
                            <th style="width: 15%;">Waktu & Adjustment</th>
                        </tr>
                    </thead>
                    <tbody class="block-table-body" data-block-index="${bIdx}">
                        ${renderBlockRows(block.rows || [], bIdx)}
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-1">
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fs-11 btn-add-row" data-block-index="${bIdx}">
                    <i class="ri-add-line"></i> Tambah Baris
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 fs-11 btn-delete-row" data-block-index="${bIdx}">
                    <i class="ri-subtract-line"></i> Hapus Baris
                </button>
            </div>
        </div>
        `;
    }

    function renderBlockRows(rows, bIdx) {
        if (!rows || rows.length === 0) return '';
        var html = '';
        rows.forEach(function(r, rIdx) {
            html += `
            <tr class="data-row" data-row-index="${rIdx}">
                <td><input type="text" class="table-input r-p1-sampling-ke" value="${escapeHtml(r.p1_sampling_ke || String(rIdx + 1))}"></td>
                <td><input type="text" class="table-input r-p1-jam" value="${escapeHtml(r.p1_jam || '')}"></td>
                <td><input type="text" class="table-input r-p1-pic" value="${escapeHtml(r.p1_pic || '')}"></td>
                <td><input type="text" class="table-input r-p1-brix" value="${escapeHtml(r.p1_brix || '')}"></td>
                <td><input type="text" class="table-input r-p1-nacl" value="${escapeHtml(r.p1_nacl || '')}"></td>
                <td><input type="text" class="table-input r-p1-warna" value="${escapeHtml(r.p1_warna || 'Standar')}"></td>
                <td><input type="text" class="table-input r-p1-organo" value="${escapeHtml(r.p1_organo || 'OK')}"></td>
                <td><input type="text" class="table-input r-p1-waktu-adj text-start px-1" value="${escapeHtml(r.p1_waktu_adjustment || '')}"></td>

                <td><input type="text" class="table-input r-p2-sampling-ke" value="${escapeHtml(r.p2_sampling_ke || String(rIdx + 1))}"></td>
                <td><input type="text" class="table-input r-p2-jam" value="${escapeHtml(r.p2_jam || '')}"></td>
                <td><input type="text" class="table-input r-p2-pic" value="${escapeHtml(r.p2_pic || '')}"></td>
                <td><input type="text" class="table-input r-p2-brix" value="${escapeHtml(r.p2_brix || '')}"></td>
                <td><input type="text" class="table-input r-p2-nacl" value="${escapeHtml(r.p2_nacl || '')}"></td>
                <td><input type="text" class="table-input r-p2-warna" value="${escapeHtml(r.p2_warna || 'Standar')}"></td>
                <td><input type="text" class="table-input r-p2-organo" value="${escapeHtml(r.p2_organo || 'OK')}"></td>
                <td><input type="text" class="table-input r-p2-waktu-adj text-start px-1" value="${escapeHtml(r.p2_waktu_adjustment || '')}"></td>

                <td><input type="text" class="table-input r-disposisi fw-semibold" value="${escapeHtml(r.disposisi || '')}"></td>
            </tr>
            `;
        });
        return html;
    }

    function attachSheetEvents() {
        $('.btn-delete-sheet').off('click').on('click', function() {
            var sIdx = $(this).data('sheet-index');
            Swal.fire({
                title: 'Hapus Lembar Ini?',
                text: 'Lembar Pelarutan #' + (sIdx + 1) + ' (2 Batch) akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var currentBlocks = collectBatchBlocksFromDOM();
                    currentBlocks.splice(sIdx * 2, 2);
                    renderLembarSheets(currentBlocks);
                }
            });
        });

        $('.btn-add-row').off('click').on('click', function() {
            var bIdx = $(this).data('block-index');
            var currentBlocks = collectBatchBlocksFromDOM();
            var nextSamp = (currentBlocks[bIdx].rows.length || 0) + 1;
            currentBlocks[bIdx].rows.push({
                p1_sampling_ke: String(nextSamp),
                p1_jam: '',
                p1_pic: '',
                p1_brix: '',
                p1_nacl: '',
                p1_warna: 'Standar',
                p1_organo: 'OK',
                p1_waktu_adjustment: '',
                p2_sampling_ke: String(nextSamp),
                p2_jam: '',
                p2_pic: '',
                p2_brix: '',
                p2_nacl: '',
                p2_warna: 'Standar',
                p2_organo: 'OK',
                p2_waktu_adjustment: '',
                disposisi: ''
            });
            renderLembarSheets(currentBlocks);
        });

        $('.btn-delete-row').off('click').on('click', function() {
            var bIdx = $(this).data('block-index');
            var currentBlocks = collectBatchBlocksFromDOM();
            if (currentBlocks[bIdx].rows.length > 1) {
                currentBlocks[bIdx].rows.pop();
                renderLembarSheets(currentBlocks);
            } else {
                Swal.fire('Info', 'Minimal satu baris tersisa pada tabel ini.', 'info');
            }
        });
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
            jam_produksi: '08:00',
            kode_shift_grup: 'Shift 1 / Grup A',
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
