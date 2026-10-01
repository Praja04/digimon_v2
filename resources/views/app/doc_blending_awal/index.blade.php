@extends('layouts.component.main')
@section('title', 'Hasil Analisis Proses Blending')

@section('styles')
<style>
    .document-paper {
        background: #ffffff;
        border: 1px solid #c2c7d0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 6px;
        padding: 30px;
        position: relative;
    }

    .doc-logo-box {
        border: 2px solid #2d3748;
        padding: 6px 12px;
        display: inline-block;
        border-radius: 4px;
        background: #f8fafc;
    }

    .doc-title-main {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #1a202c;
        text-transform: uppercase;
        text-align: center;
    }

    .doc-box-border {
        border: 1px solid #000000;
    }

    .table-blending {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.80rem;
        margin-bottom: 15px;
    }

    .table-blending th, .table-blending td {
        border: 1px solid #000000;
        padding: 4px 2px;
        text-align: center;
        vertical-align: middle;
    }

    .table-blending th {
        background-color: #f2f2f2;
        font-weight: 700;
        color: #000;
    }

    .table-blending .table-input {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        text-align: center;
        font-size: 0.80rem;
        padding: 3px 2px;
        border-radius: 3px;
    }

    .table-blending .table-input:focus, .table-blending .table-input:hover {
        border-color: #405189;
        background: #ffffff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(64, 81, 137, 0.2);
    }

    .batch-block-card {
        border: 1px solid #000000;
        padding: 15px;
        margin-bottom: 25px;
        background: #ffffff;
        position: relative;
    }

    .signature-container {
        border: 1px solid #000000;
        margin-top: 15px;
    }

    .sig-col {
        border-right: 1px solid #000000;
        padding: 8px 12px;
        text-align: center;
    }

    .sig-col:last-child {
        border-right: none;
    }

    .sig-space {
        height: 50px;
    }

    .meta-input-group td {
        padding: 2px 4px;
        font-size: 0.88rem;
    }

    .meta-input-group .form-control-sm {
        border: none;
        border-bottom: 1px dotted #888;
        border-radius: 0;
        padding: 2px 5px;
        background: transparent;
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

                    <div class="col-lg-8 col-md-6 text-md-end text-start d-flex flex-wrap gap-2 justify-content-md-end justify-content-start align-items-end">
                        <a href="{{ route('analisa.blending-awal.export.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri-arrow-left-line me-1"></i> Kembali ke Menu
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

        <!-- Dokumen Sheet -->
        <div class="document-paper mb-5" id="printableArea">
            <form id="docForm">
                <input type="hidden" id="doc_production_batch_id" name="production_batch_id" value="{{ $initialData['production_batch_id'] ?? '' }}">

                <!-- Header Dokumen Sesuai Format -->
                <div class="row align-items-center mb-3 pb-2 border-bottom">
                    <div class="col-3">
                        <div class="doc-logo-box p-1">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 48px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="doc-title-main">HASIL ANALISIS PROSES BLENDING</div>
                    </div>
                    <div class="col-3 text-end">
                        <table class="float-end border" style="font-size: 0.82rem;">
                            <tr>
                                <td class="p-1 border-bottom text-start fw-bold bg-light">Tanggal Record Doc</td>
                                <td class="p-1 border-bottom">
                                    <input type="date" class="form-control form-control-sm border-0 p-0" id="tanggal_record_doc" name="tanggal_record_doc" value="{{ $initialData['tanggal_record_doc'] ?? date('Y-m-d') }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1 text-start fw-bold bg-light">Halaman</td>
                                <td class="p-1">
                                    <input type="text" class="form-control form-control-sm border-0 p-0 text-center" id="halaman" name="halaman" value="{{ $initialData['halaman'] ?? '1' }}" style="width: 50px;">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Container Blok Batch Dinamis -->
                <div id="batchBlocksContainer">
                    <!-- Di-render otomatis lewat JS -->
                </div>

                <!-- Tombol Tambah Blok Tangki/Batch -->
                <div class="text-center my-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddBatchBlock">
                        <i class="ri-add-circle-line me-1"></i> Tambah Blok Tangki / Batch Blending
                    </button>
                </div>

                <!-- Footer: Catatan & Tanda Tangan -->
                <div class="signature-container">
                    <div class="row g-0">
                        <div class="col-md-5 p-2 border-end">
                            <label class="fw-bold fs-12 mb-1">Catatan :</label>
                            <textarea class="form-control form-control-sm" id="catatan" name="catatan" rows="4" placeholder="Ketik catatan khusus proses blending di sini...">{{ $initialData['catatan'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-0 text-center h-100">
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Disampling oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="pic_sampling" name="pic_sampling" placeholder="Nama PIC" value="{{ $initialData['pic_sampling'] ?? '' }}">
                                        <small class="text-muted d-block mt-1">Produksi</small>
                                    </div>
                                </div>
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Dianalisis oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="pic_analis" name="pic_analis" placeholder="Nama Analis" value="{{ $initialData['pic_analis'] ?? '' }}">
                                        <small class="text-muted d-block mt-1">QC Analis</small>
                                    </div>
                                </div>
                                <div class="col-4 sig-col d-flex flex-column justify-content-between">
                                    <div class="fw-semibold fs-12">Dicek oleh,</div>
                                    <div class="sig-space"></div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="pic_checker" name="pic_checker" placeholder="Nama SPV/MNG" value="{{ $initialData['pic_checker'] ?? '' }}">
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

            </form>
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

        // Event Tambah Blok Batch Baru
        $('#btnAddBatchBlock').on('click', function() {
            var newBlock = {
                jenis_produk: 'Kecap Sedap',
                tanggal_produksi: $('#tanggal_record_doc').val() || '{{ date("Y-m-d") }}',
                jam_produksi: '08:00',
                kode_shift_grup: 'Shift 1 / Grup A',
                batch: '1',
                no_blending: '1',
                volume_awal: '5000 L',
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

            var currentBlocks = collectBatchBlocksFromDOM();
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

            var payload = {
                production_batch_id: poId,
                tanggal_record_doc: $('#tanggal_record_doc').val(),
                halaman: $('#halaman').val(),
                catatan: $('#catatan').val(),
                pic_sampling: $('#pic_sampling').val(),
                pic_analis: $('#pic_analis').val(),
                pic_checker: $('#pic_checker').val(),
                batch_blocks: collectBatchBlocksFromDOM()
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
        $('#doc_production_batch_id').val(data.production_batch_id);
        $('#tanggal_record_doc').val(data.tanggal_record_doc);
        $('#halaman').val(data.halaman || '1');
        $('#catatan').val(data.catatan || '');
        $('#pic_sampling').val(data.pic_sampling || '');
        $('#pic_analis').val(data.pic_analis || '');
        $('#pic_checker').val(data.pic_checker || '');

        renderBatchBlocks(data.batch_blocks || []);
    }

    function renderBatchBlocks(blocks) {
        var container = $('#batchBlocksContainer');
        container.empty();

        if (!blocks || blocks.length === 0) {
            container.html('<div class="text-center py-4 text-muted border border-dashed mb-3">Klik tombol di bawah untuk menambahkan blok Batch Blending.</div>');
            return;
        }

        blocks.forEach(function(block, bIdx) {
            var blockHtml = `
            <div class="batch-block-card" data-block-index="${bIdx}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold fs-12">
                        <i class="ri-flask-line me-1"></i> Tangki / Batch Blending #${bIdx + 1}
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 btn-delete-block" data-block-index="${bIdx}">
                        <i class="ri-delete-bin-line"></i> Hapus Blok
                    </button>
                </div>

                <!-- Meta Blok Info -->
                <table class="w-100 meta-input-group mb-3">
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
                                <input type="date" class="form-control form-control-sm block-meta-tanggal" value="${escapeHtml(block.tanggal_produksi || '')}">
                            </div>
                        </td>
                        <td class="fw-bold">No. Blending</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-no-blending" value="${escapeHtml(block.no_blending || '')}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Jam Produksi</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1">:</span>
                                <input type="time" class="form-control form-control-sm block-meta-jam" value="${escapeHtml(block.jam_produksi || '')}">
                            </div>
                        </td>
                        <td class="fw-bold">Volume awal</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-volume" value="${escapeHtml(block.volume_awal || '')}">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Kode Shift & Grup</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-1">:</span>
                                <input type="text" class="form-control form-control-sm block-meta-shift" value="${escapeHtml(block.kode_shift_grup || '')}">
                            </div>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </table>

                <!-- Tabel Analisis Proses Blending -->
                <div class="table-responsive">
                    <table class="table-blending">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 4%;">Sampling ke-</th>
                                <th rowspan="2" style="width: 6%;">Vol Tangki (L)</th>
                                <th colspan="2" style="width: 10%;">Serah Terima</th>
                                <th colspan="10" style="width: 56%;">Analisis</th>
                                <th rowspan="2" style="width: 14%;">Waktu & Adjustment</th>
                                <th rowspan="2" style="width: 10%;">Disposisi/ Keterangan</th>
                            </tr>
                            <tr>
                                <th style="width: 4%;">Jam</th>
                                <th style="width: 6%;">PIC</th>
                                <th style="width: 6%;">BJ (g/mL)</th>
                                <th style="width: 5%;">Brix</th>
                                <th style="width: 5%;">pH</th>
                                <th style="width: 5%;">% NaCl</th>
                                <th style="width: 6%;">Viskositas (ps)</th>
                                <th style="width: 6%;">Organo</th>
                                <th style="width: 6%;">Aroma</th>
                                <th style="width: 6%;">Warna</th>
                                <th style="width: 5%;">Buih</th>
                                <th style="width: 6%;">Aw</th>
                            </tr>
                        </thead>
                        <tbody class="block-table-body" data-block-index="${bIdx}">
                            ${renderBlockRows(block.rows || [], bIdx)}
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 btn-add-row" data-block-index="${bIdx}">
                        <i class="ri-add-line"></i> Tambah Baris
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-delete-row" data-block-index="${bIdx}">
                        <i class="ri-subtract-line"></i> Hapus Baris Terakhir
                    </button>
                </div>
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
            html += `
            <tr class="data-row" data-row-index="${rIdx}">
                <td><input type="text" class="table-input r-sampling-ke" value="${escapeHtml(row.sampling_ke || String(rIdx + 1))}"></td>
                <td><input type="text" class="table-input r-vol-tangki" value="${escapeHtml(row.vol_tangki || '')}"></td>
                <td><input type="text" class="table-input r-serah-jam" value="${escapeHtml(row.serah_terima_jam || '')}"></td>
                <td><input type="text" class="table-input r-serah-pic" value="${escapeHtml(row.serah_terima_pic || '')}"></td>
                <td><input type="text" class="table-input r-bj" value="${escapeHtml(row.bj || '')}"></td>
                <td><input type="text" class="table-input r-brix" value="${escapeHtml(row.brix || '')}"></td>
                <td><input type="text" class="table-input r-ph" value="${escapeHtml(row.ph || '')}"></td>
                <td><input type="text" class="table-input r-nacl" value="${escapeHtml(row.nacl || '')}"></td>
                <td><input type="text" class="table-input r-visco" value="${escapeHtml(row.visco || '')}"></td>
                <td><input type="text" class="table-input r-organo" value="${escapeHtml(row.organo || '')}"></td>
                <td><input type="text" class="table-input r-aroma" value="${escapeHtml(row.aroma || '')}"></td>
                <td><input type="text" class="table-input r-warna" value="${escapeHtml(row.warna || '')}"></td>
                <td><input type="text" class="table-input r-buih" value="${escapeHtml(row.buih || '')}"></td>
                <td><input type="text" class="table-input r-aw" value="${escapeHtml(row.aw || '')}"></td>
                <td><input type="text" class="table-input r-waktu-adj text-start" value="${escapeHtml(row.waktu_adjustment || '')}"></td>
                <td><input type="text" class="table-input r-disposisi" value="${escapeHtml(row.disposisi || '')}"></td>
            </tr>
            `;
        });
        return html;
    }

    function attachBlockEvents() {
        $('.btn-delete-block').off('click').on('click', function() {
            var bIdx = $(this).data('block-index');
            var currentBlocks = collectBatchBlocksFromDOM();
            currentBlocks.splice(bIdx, 1);
            renderBatchBlocks(currentBlocks);
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
                Swal.fire('Info', 'Minimal satu baris tersisa pada setiap blok.', 'info');
            }
        });
    }

    function collectBatchBlocksFromDOM() {
        var blocks = [];
        $('.batch-block-card').each(function() {
            var blockCard = $(this);
            var blockObj = {
                jenis_produk: blockCard.find('.block-meta-jenis').val() || '',
                tanggal_produksi: blockCard.find('.block-meta-tanggal').val() || '',
                jam_produksi: blockCard.find('.block-meta-jam').val() || '',
                kode_shift_grup: blockCard.find('.block-meta-shift').val() || '',
                batch: blockCard.find('.block-meta-batch').val() || '',
                no_blending: blockCard.find('.block-meta-no-blending').val() || '',
                volume_awal: blockCard.find('.block-meta-volume').val() || '',
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
