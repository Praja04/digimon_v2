@extends('layouts.component.main')
@section('title', 'Hasil Analisis Proses Blending')

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

    .doc-logo-box {
        border: 1px solid #000000;
        padding: 4px 8px;
        display: inline-block;
        background: #ffffff;
    }

    .doc-title-main {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #000000;
        text-transform: uppercase;
        text-align: center;
    }

    /* Unified Official Document Table */
    .doc-unified-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
        margin-bottom: 0;
        color: #000000;
    }

    .doc-unified-table th, .doc-unified-table td {
        border: 1px solid #000000;
        padding: 3px 2px;
        vertical-align: middle;
    }

    .doc-unified-table th {
        background-color: #f2f2f2;
        font-weight: 700;
        text-align: center;
        color: #000000;
    }

    /* Clean cell inputs */
    .clean-cell-input {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        text-align: center;
        font-size: 0.82rem;
        font-weight: 500;
        padding: 2px 1px;
        border-radius: 3px;
        color: #000000;
    }

    .clean-cell-input:focus, .clean-cell-input:hover {
        border-color: #405189;
        background: #f8fafc;
        outline: none;
        box-shadow: 0 0 0 2px rgba(64, 81, 137, 0.15);
    }

    .clean-cell-textarea {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        font-size: 0.80rem;
        font-weight: 500;
        line-height: 1.25;
        padding: 3px 4px;
        border-radius: 3px;
        color: #000000;
        resize: vertical;
        min-height: 38px;
        text-align: left;
        word-break: break-word;
        white-space: pre-wrap;
    }

    .clean-cell-textarea:focus, .clean-cell-textarea:hover {
        border-color: #405189;
        background: #f8fafc;
        outline: none;
        box-shadow: 0 0 0 2px rgba(64, 81, 137, 0.15);
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

        <!-- Alert Info Notes -->
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-3 py-2 px-3">
            <i class="ri-information-line fs-18 me-2"></i>
            <div class="fs-12">
                <strong>Format Resmi QLB:</strong> Formulir dipisahkan menjadi <strong>Per Siklus Blending (1 Lembar = 1 Tangki / Blending)</strong>. Setiap lembar memiliki header, metadata tangki, tabel sampling (termasuk baris Turun Blending), dan tanda tangan masing-masing.
            </div>
        </div>

        <!-- Dokumen Sheet Container -->
        <div id="batchBlocksContainer">
            <!-- Di-render dinamis per Siklus Blending lewat JavaScript -->
        </div>

        <!-- Tombol Tambah Blok Tangki/Batch -->
        <div class="text-center mb-5">
            <button type="button" class="btn btn-outline-primary btn-md shadow-sm px-4" id="btnAddBatchBlock">
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
            var defaultDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';

            var newBlock = {
                jenis_produk: (currentDocData && currentDocData.variant) ? currentDocData.variant : 'Kecap Sedap',
                tanggal_produksi: defaultDate,
                jam_produksi: '08:00',
                kode_shift_grup: 'Shift 1 / Grup A',
                batch: String(nextNum),
                no_blending: String(nextNum),
                volume_awal: '10000 L',
                catatan: '',
                pic_sampling: '',
                pic_analis: (currentDocData && currentDocData.pic_analis) ? currentDocData.pic_analis : '',
                pic_checker: '',
                rows: []
            };

            for (var r = 1; r <= 4; r++) {
                newBlock.rows.push({
                    sampling_ke: String(r),
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
            }

            currentBlocks.push(newBlock);
            renderBatchBlocks(currentBlocks);
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
                            <i class="ri-pages-line"></i> LEMBAR SIKLUS BLENDING #${bIdx + 1}
                        </span>
                        <span class="badge bg-light text-dark border ms-2">
                            Tangki / Blending No. ${escapeHtml(block.no_blending || String(bIdx + 1))}
                        </span>
                    </div>
                    <div>
                        ${totalBlocks > 1 ? `
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-delete-block" data-block-index="${bIdx}">
                            <i class="ri-delete-bin-line me-1"></i> Hapus Lembar Ini
                        </button>
                        ` : ''}
                    </div>
                </div>

                <!-- 1. Header Dokumen Resmi Paper Grid -->
                <table class="doc-unified-table mb-2">
                    <tr>
                        <td width="20%" class="text-center p-2" style="background: #ffffff;">
                            <div class="doc-logo-box">
                                <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 38px; object-fit: contain;">
                            </div>
                        </td>
                        <td width="55%" class="text-center" style="background: #ffffff;">
                            <div class="doc-title-main">HASIL ANALISIS PROSES BLENDING</div>
                        </td>
                        <td width="25%" class="p-0" style="background: #ffffff;">
                            <table class="w-100" style="font-size: 0.80rem; border-collapse: collapse;">
                                <tr>
                                    <td class="p-1 border-bottom fw-bold bg-light" width="55%">Tanggal Record Doc</td>
                                    <td class="p-1 border-bottom text-start doc-date-display" width="45%">: ${globalDocDate}</td>
                                </tr>
                                <tr>
                                    <td class="p-1 fw-bold bg-light">Halaman</td>
                                    <td class="p-1 text-start fw-semibold">: ${bIdx + 1} / ${totalBlocks}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- 2. Metadata Siklus Blending (Tangki) -->
                <table class="doc-unified-table mb-2">
                    <tr>
                        <td width="14%" class="fw-bold bg-light px-2">Jenis Produk</td>
                        <td width="36%" class="p-1">
                            <input type="text" class="clean-meta-input block-meta-jenis" value="${escapeHtml(block.jenis_produk || '')}">
                        </td>
                        <td width="14%" class="fw-bold bg-light px-2">Batch</td>
                        <td width="36%" class="p-1">
                            <input type="text" class="clean-meta-input block-meta-batch" value="${escapeHtml(block.batch || '')}">
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light px-2">Tanggal Produksi</td>
                        <td class="p-1">
                            <input type="date" class="clean-meta-input block-meta-tanggal" value="${escapeHtml(block.tanggal_produksi || globalDocDate)}">
                        </td>
                        <td class="fw-bold bg-light px-2">No. Blending</td>
                        <td class="p-1">
                            <input type="text" class="clean-meta-input block-meta-no-blending" value="${escapeHtml(block.no_blending || String(bIdx + 1))}">
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light px-2">Jam Produksi</td>
                        <td class="p-1">
                            <input type="text" class="clean-meta-input block-meta-jam" placeholder="HH:mm atau HH:mm - HH:mm" value="${escapeHtml(block.jam_produksi || '')}">
                        </td>
                        <td class="fw-bold bg-light px-2">Volume awal</td>
                        <td class="p-1">
                            <input type="text" class="clean-meta-input block-meta-volume" value="${escapeHtml(block.volume_awal || '')}">
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold bg-light px-2">Kode Shift & Grup</td>
                        <td class="p-1" colspan="3">
                            <input type="text" class="clean-meta-input block-meta-shift" value="${escapeHtml(block.kode_shift_grup || 'Shift 1 / Grup A')}">
                        </td>
                    </tr>
                </table>

                <!-- 3. Tabel Analisis Proses Blending -->
                <div class="table-responsive">
                    <table class="doc-unified-table">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 4.5%;">Sampling ke-</th>
                                <th rowspan="2" style="width: 6%;">Vol Tangki (L)</th>
                                <th colspan="2" style="width: 10%;">Serah Terima</th>
                                <th colspan="10" style="width: 46.5%;">Analisis</th>
                                <th rowspan="2" style="width: 24%;">Waktu & Adjustment</th>
                                <th rowspan="2" style="width: 9%;">Disposisi/ Keterangan</th>
                            </tr>
                            <tr>
                                <th style="width: 4%;">Jam</th>
                                <th style="width: 6%;">PIC</th>
                                <th style="width: 5%;">BJ (g/mL)</th>
                                <th style="width: 4.5%;">Brix</th>
                                <th style="width: 4%;">pH</th>
                                <th style="width: 4.5%;">% NaCl</th>
                                <th style="width: 5.5%;">Viskositas (ps)</th>
                                <th style="width: 4.5%;">Organo</th>
                                <th style="width: 4.5%;">Aroma</th>
                                <th style="width: 5%;">Warna</th>
                                <th style="width: 4.5%;">Buih</th>
                                <th style="width: 4.5%;">Aw</th>
                            </tr>
                        </thead>
                        <tbody class="block-table-body" data-block-index="${bIdx}">
                            ${renderBlockRows(block.rows || [], bIdx)}
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-2 mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 btn-add-row" data-block-index="${bIdx}">
                        <i class="ri-add-line"></i> Tambah Baris
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-delete-row" data-block-index="${bIdx}">
                        <i class="ri-subtract-line"></i> Hapus Baris Terakhir
                    </button>
                </div>

                <!-- 4. Footer: Catatan & Tanda Tangan Per Lembar -->
                <table class="doc-unified-table mt-2">
                    <tr>
                        <td width="40%" class="p-2 align-top" rowspan="4">
                            <strong class="d-block mb-1">Catatan :</strong>
                            <textarea class="clean-textarea block-catatan" rows="4" placeholder="Ketik catatan khusus proses blending lembar ini...">${escapeHtml(block.catatan || '')}</textarea>
                        </td>
                        <td width="20%" class="text-center fw-semibold bg-light">Disampling oleh,</td>
                        <td width="20%" class="text-center fw-semibold bg-light">Dianalisis oleh,</td>
                        <td width="20%" class="text-center fw-semibold bg-light">Dicek oleh,</td>
                    </tr>
                    <tr>
                        <td class="sign-space-cell"></td>
                        <td class="sign-space-cell"></td>
                        <td class="sign-space-cell"></td>
                    </tr>
                    <tr>
                        <td class="text-center p-1">
                            <input type="text" class="clean-sign-input block-pic-sampling" placeholder="Nama PIC" value="${escapeHtml(block.pic_sampling || '')}">
                        </td>
                        <td class="text-center p-1">
                            <input type="text" class="clean-sign-input block-pic-analis" placeholder="Nama Analis" value="${escapeHtml(block.pic_analis || '')}">
                        </td>
                        <td class="text-center p-1">
                            <input type="text" class="clean-sign-input block-pic-checker" placeholder="Nama SPV/MNG" value="${escapeHtml(block.pic_checker || '')}">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold fs-11 bg-light py-1">Produksi</td>
                        <td class="text-center fw-bold fs-11 bg-light py-1">QC Analis</td>
                        <td class="text-center fw-bold fs-11 bg-light py-1">Staff/SPV/MNG QC</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end px-3 py-1 bg-light">
                            <span class="fs-11 fw-bold text-muted fst-italic">FRM/QLB/04/104/005-01</span>
                        </td>
                    </tr>
                </table>

            </div>
            `;
            container.append(blockHtml);
        });

        attachBlockEvents();
    }

    function renderBlockRows(rows, bIdx) {
        if (!rows || rows.length === 0) return '';
        var html = '';
        rows.forEach(function(row, rIdx) {
            var sampValue = row.sampling_ke !== undefined && row.sampling_ke !== null ? String(row.sampling_ke).trim() : '';
            var volValue = row.vol_tangki !== undefined && row.vol_tangki !== null ? String(row.vol_tangki).trim() : '';
            var adjValue = row.waktu_adjustment !== undefined && row.waktu_adjustment !== null ? String(row.waktu_adjustment).trim() : '';
            var dispValue = row.disposisi !== undefined && row.disposisi !== null ? String(row.disposisi).trim() : '';

            var isAwalTurun = (volValue.toLowerCase() === 'awal');

            var buihRaw = row.buih ? String(row.buih).trim() : 'Tidak Ada';
            if (buihRaw.toLowerCase() === 'tidak ada' || buihRaw.toLowerCase() === 'tidak\nada') {
                buihRaw = "Tidak\nAda";
            }

            html += `
            <tr class="data-row ${isAwalTurun ? 'bg-light' : ''}" data-row-index="${rIdx}">
                <td><input type="text" class="clean-cell-input r-sampling-ke fw-bold text-center" value="${escapeHtml(sampValue)}"></td>
                <td><input type="text" class="clean-cell-input r-vol-tangki fw-semibold text-center ${isAwalTurun ? 'text-primary' : ''}" value="${escapeHtml(volValue)}"></td>
                <td><input type="text" class="clean-cell-input r-serah-jam text-center" value="${escapeHtml(row.serah_terima_jam || '')}"></td>
                <td><input type="text" class="clean-cell-input r-serah-pic text-center" value="${escapeHtml(row.serah_terima_pic || '')}"></td>
                <td><input type="text" class="clean-cell-input r-bj text-center" value="${escapeHtml(row.bj || '')}"></td>
                <td><input type="text" class="clean-cell-input r-brix text-center" value="${escapeHtml(row.brix || '')}"></td>
                <td><input type="text" class="clean-cell-input r-ph text-center" value="${escapeHtml(row.ph || '')}"></td>
                <td><input type="text" class="clean-cell-input r-nacl text-center" value="${escapeHtml(row.nacl || '')}"></td>
                <td><input type="text" class="clean-cell-input r-visco text-center" value="${escapeHtml(row.visco || '')}"></td>
                <td><input type="text" class="clean-cell-input r-organo text-center" value="${escapeHtml(row.organo || '')}"></td>
                <td><input type="text" class="clean-cell-input r-aroma text-center" value="${escapeHtml(row.aroma || '')}"></td>
                <td><input type="text" class="clean-cell-input r-warna text-center" value="${escapeHtml(row.warna || '')}"></td>
                <td style="padding: 1px 0;">
                    <textarea class="clean-cell-input r-buih" rows="2" style="resize:none; padding:1px 0; line-height:1.15; font-size:0.75rem; height:34px; overflow:hidden; text-align:center; white-space:pre-wrap;">${escapeHtml(buihRaw)}</textarea>
                </td>
                <td><input type="text" class="clean-cell-input r-aw text-center" value="${escapeHtml(row.aw || '')}"></td>
                <td class="p-1">
                    <textarea class="clean-cell-textarea r-waktu-adj text-center" rows="2" placeholder="Waktu & Adjustment...">${escapeHtml(adjValue)}</textarea>
                </td>
                <td class="p-1">
                    <textarea class="clean-cell-textarea r-disposisi text-center fw-semibold ${dispValue.includes('Reject') ? 'text-danger' : 'text-success'}" rows="2" placeholder="Disposisi / Keterangan...">${escapeHtml(dispValue)}</textarea>
                </td>
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
</script>
@endsection
