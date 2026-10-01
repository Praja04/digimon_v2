@extends('layouts.component.main')
@section('title', 'Hasil Analisis Proses Pelarutan')

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

    .table-pelarutan {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
        margin-bottom: 15px;
    }

    .table-pelarutan th, .table-pelarutan td {
        border: 1px solid #000000;
        padding: 4px 3px;
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
        font-size: 0.82rem;
        padding: 3px 2px;
        border-radius: 3px;
    }

    .table-pelarutan .table-input:focus, .table-pelarutan .table-input:hover {
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

    @media print {
        body * {
            visibility: hidden;
        }
        #printableArea, #printableArea * {
            visibility: visible;
        }
        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
            border: none;
            box-shadow: none;
        }
        .no-print {
            display: none !important;
        }
        .table-pelarutan th, .table-pelarutan td, .batch-block-card, .signature-container, .sig-col {
            border: 1px solid #000000 !important;
        }
        .table-pelarutan .table-input, .meta-input-group .form-control-sm {
            border: none !important;
            background: transparent !important;
        }
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title & Navigation -->
        <div class="row no-print">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">@yield('title')</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('pelarutan-1.menu') }}">Menu</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pelarutan.export.index') }}">Dokumen Analisis Pelarutan (Export & Cetak)</a></li>
                            <li class="breadcrumb-item active">Formulir Dokumen</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Control Panel Card -->
        <div class="row no-print mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center justify-content-between">
                            <!-- PO Selector -->
                            <div class="col-12 col-md-4 col-lg-3">
                                <label class="form-label fw-semibold text-muted small mb-1">Pilih Nomor PO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ri-file-list-3-line text-primary"></i></span>
                                    <select id="po_selector" class="form-select border-start-0 select2">
                                        <option value="">-- Pilih Nomor PO --</option>
                                        @foreach($batches as $b)
                                            <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
                                                {{ $b->po_number }} ({{ $b->variant ?? 'Batch' }} - {{ $b->date ? \Carbon\Carbon::parse($b->date)->format('d/m/Y') : '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 col-md-8 col-lg-9 d-flex flex-wrap gap-2 justify-content-md-end align-items-end">
                                <button type="button" id="btnFetchDb" class="btn btn-soft-info d-flex align-items-center">
                                    <i class="ri-refresh-line me-1"></i> Tarik Data Database
                                </button>
                                <button type="button" id="btnSaveDoc" class="btn btn-primary d-flex align-items-center shadow-sm">
                                    <i class="ri-save-3-line me-1"></i> Simpan Dokumen
                                </button>
                                <button type="button" id="btnExportExcel" class="btn btn-success d-flex align-items-center shadow-sm">
                                    <i class="ri-file-excel-2-line me-1"></i> Export Excel (Sesuai Format)
                                </button>
                                <button type="button" id="btnPrint" class="btn btn-dark d-flex align-items-center shadow-sm">
                                    <i class="ri-printer-line me-1"></i> Cetak / Print PDF
                                </button>
                                <a href="{{ route('pelarutan.export.index') }}" class="btn btn-soft-secondary d-flex align-items-center">
                                    <i class="ri-arrow-left-line me-1"></i> Kembali ke Menu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Document Canvas -->
        <div class="row">
            <div class="col-12">
                <div id="printableArea" class="document-paper mb-5">
                    
                    <!-- Header Dokumen Sesuai Gambar -->
                    <div class="row align-items-center border-bottom pb-3 mb-4">
                        <div class="col-12 col-md-3 text-center text-md-start mb-2 mb-md-0">
                            <div class="d-flex align-items-center gap-2">
                                <div class="doc-logo-box p-1">
                                    <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 48px; object-fit: contain;">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 text-center">
                            <h4 class="doc-title-main mb-0">HASIL ANALISIS PROSES PELARUTAN</h4>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="border p-2 rounded bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold small">Tanggal Record Doc :</span>
                                    <input type="date" id="tanggal_record_doc" class="form-control form-control-sm text-center" style="max-width: 130px;" value="{{ $initialData['tanggal_record_doc'] ?? date('Y-m-d') }}">
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-bold small">Halaman :</span>
                                    <input type="text" id="halaman" class="form-control form-control-sm text-center" style="max-width: 80px;" value="{{ $initialData['halaman'] ?? '1' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batch Blocks Container -->
                    <div id="batchBlocksContainer">
                        @if(!empty($initialData['batch_blocks']))
                            @foreach($initialData['batch_blocks'] as $bIndex => $block)
                            <div class="batch-block-card" data-block-index="{{ $bIndex }}">
                                
                                <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                                    <span class="badge bg-primary fs-12">Blok Batch #{{ $bIndex + 1 }}</span>
                                    <button type="button" class="btn btn-sm btn-soft-danger btn-delete-block" title="Hapus Blok Batch Ini">
                                        <i class="ri-delete-bin-line me-1"></i> Hapus Blok Batch
                                    </button>
                                </div>

                                <!-- Metadata Blok (Kiri & Kanan) -->
                                <div class="row mb-3">
                                    <div class="col-12 col-md-6">
                                        <table class="w-100 meta-input-group">
                                            <tr>
                                                <td width="35%" class="fw-bold">Jenis Produk</td>
                                                <td width="3%">:</td>
                                                <td><input type="text" class="form-control form-control-sm block-jenis-produk" value="{{ $block['jenis_produk'] ?? '' }}"></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Tanggal Produksi</td>
                                                <td>:</td>
                                                <td><input type="date" class="form-control form-control-sm block-tgl-prod" value="{{ $block['tanggal_produksi'] ?? date('Y-m-d') }}"></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Jam Produksi</td>
                                                <td>:</td>
                                                <td><input type="text" class="form-control form-control-sm block-jam-prod" value="{{ $block['jam_produksi'] ?? '08:00' }}" placeholder="08:00"></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Kode Shift & Grup</td>
                                                <td>:</td>
                                                <td><input type="text" class="form-control form-control-sm block-shift-grup" value="{{ $block['kode_shift_grup'] ?? 'Shift 1 / Grup A' }}" placeholder="Shift 1 / Grup A"></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <table class="w-100 meta-input-group">
                                            <tr>
                                                <td width="35%" class="fw-bold">Batch</td>
                                                <td width="3%">:</td>
                                                <td><input type="text" class="form-control form-control-sm block-batch" value="{{ $block['batch'] ?? '' }}" placeholder="1"></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">No. Dissolver</td>
                                                <td>:</td>
                                                <td><input type="text" class="form-control form-control-sm block-dissolver" value="{{ $block['no_dissolver'] ?? '' }}" placeholder="1"></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Volume</td>
                                                <td>:</td>
                                                <td><input type="text" class="form-control form-control-sm block-volume" value="{{ $block['volume'] ?? '5000 L' }}" placeholder="5000 L"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tabel Pelarutan I & II -->
                                <div class="table-responsive">
                                    <table class="table-pelarutan">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" style="width: 50px;">Sampling ke-</th>
                                                <th colspan="2">Serah Terima</th>
                                                <th colspan="5">Pelarutan I (GGA)</th>
                                                <th rowspan="2" style="width: 50px;">Sampling ke-</th>
                                                <th colspan="2">Serah Terima</th>
                                                <th colspan="5">Pelarutan II (GGAS)</th>
                                                <th rowspan="2" style="min-width: 90px;">Disposisi</th>
                                                <th rowspan="2" class="no-print" width="35">Aksi</th>
                                            </tr>
                                            <tr>
                                                <th style="min-width: 55px;">Jam</th>
                                                <th style="min-width: 75px;">PIC</th>
                                                <th style="min-width: 55px;">Brix</th>
                                                <th style="min-width: 55px;">%NaCl</th>
                                                <th style="min-width: 65px;">Warna</th>
                                                <th style="min-width: 60px;">Organo</th>
                                                <th style="min-width: 110px;">Waktu & Adjustment</th>
                                                <th style="min-width: 55px;">Jam</th>
                                                <th style="min-width: 75px;">PIC</th>
                                                <th style="min-width: 55px;">Brix</th>
                                                <th style="min-width: 55px;">%NaCl</th>
                                                <th style="min-width: 65px;">Warna</th>
                                                <th style="min-width: 60px;">Organo</th>
                                                <th style="min-width: 110px;">Waktu & Adjustment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($block['rows']))
                                                @foreach($block['rows'] as $rIdx => $r)
                                                <tr>
                                                    <td><input type="text" class="table-input p1-samp" value="{{ $r['p1_sampling_ke'] ?? ($rIdx + 1) }}"></td>
                                                    <td><input type="text" class="table-input p1-jam" value="{{ $r['p1_jam'] ?? '' }}" placeholder="08:00"></td>
                                                    <td><input type="text" class="table-input p1-pic" value="{{ $r['p1_pic'] ?? '' }}" placeholder="PIC"></td>
                                                    <td><input type="text" class="table-input p1-brix" value="{{ $r['p1_brix'] ?? '' }}" placeholder="65.0"></td>
                                                    <td><input type="text" class="table-input p1-nacl" value="{{ $r['p1_nacl'] ?? '' }}" placeholder="12.0"></td>
                                                    <td><input type="text" class="table-input p1-warna" value="{{ $r['p1_warna'] ?? 'Standar' }}"></td>
                                                    <td><input type="text" class="table-input p1-organo" value="{{ $r['p1_organo'] ?? 'OK' }}"></td>
                                                    <td><input type="text" class="table-input text-start p1-adj" value="{{ $r['p1_waktu_adjustment'] ?? '-' }}"></td>

                                                    <td><input type="text" class="table-input p2-samp" value="{{ $r['p2_sampling_ke'] ?? ($rIdx + 1) }}"></td>
                                                    <td><input type="text" class="table-input p2-jam" value="{{ $r['p2_jam'] ?? '' }}" placeholder="09:00"></td>
                                                    <td><input type="text" class="table-input p2-pic" value="{{ $r['p2_pic'] ?? '' }}" placeholder="PIC"></td>
                                                    <td><input type="text" class="table-input p2-brix" value="{{ $r['p2_brix'] ?? '' }}" placeholder="65.0"></td>
                                                    <td><input type="text" class="table-input p2-nacl" value="{{ $r['p2_nacl'] ?? '' }}" placeholder="12.0"></td>
                                                    <td><input type="text" class="table-input p2-warna" value="{{ $r['p2_warna'] ?? 'Standar' }}"></td>
                                                    <td><input type="text" class="table-input p2-organo" value="{{ $r['p2_organo'] ?? 'OK' }}"></td>
                                                    <td><input type="text" class="table-input text-start p2-adj" value="{{ $r['p2_waktu_adjustment'] ?? '-' }}"></td>

                                                    <td><input type="text" class="table-input p-disposisi" value="{{ $r['disposisi'] ?? 'Release' }}"></td>
                                                    <td class="no-print">
                                                        <button type="button" class="btn btn-sm btn-soft-danger btn-delete-row" title="Hapus Baris">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end no-print">
                                    <button type="button" class="btn btn-sm btn-soft-success btn-add-row-block">
                                        <i class="ri-add-line me-1"></i> Tambah Baris Sampling
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Tombol Tambah Blok Batch Baru -->
                    <div class="mb-4 text-center no-print">
                        <button type="button" id="btnAddBlock" class="btn btn-outline-primary">
                            <i class="ri-add-circle-line me-1"></i> + Tambah Blok Batch Baru
                        </button>
                    </div>

                    <!-- Catatan Section -->
                    <div class="mb-4">
                        <label class="fw-bold small mb-1">Catatan :</label>
                        <textarea id="catatan" class="form-control form-control-sm" rows="2" placeholder="Tuliskan catatan disposisi atau keterangan tambahan disini...">{{ $initialData['catatan'] ?? '' }}</textarea>
                    </div>

                    <!-- Signatures Section (3 Kolom Sesuai Gambar) -->
                    <div class="signature-container">
                        <div class="row g-0">
                            <div class="col-4 sig-col">
                                <div class="fw-bold small mb-3 text-start">Disampling oleh,</div>
                                <div class="sig-space"></div>
                                <input type="text" id="pic_sampling" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( Nama Petugas )" value="{{ $initialData['pic_sampling'] ?? '' }}">
                                <div class="small text-muted">&nbsp;</div>
                            </div>
                            <div class="col-4 sig-col">
                                <div class="fw-bold small mb-3 text-start">Dianalisis oleh,</div>
                                <div class="sig-space"></div>
                                <input type="text" id="pic_analis" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( Nama Analis )" value="{{ $initialData['pic_analis'] ?? (auth()->user()->name ?? '') }}">
                                <div class="fw-bold small">QC Analis</div>
                            </div>
                            <div class="col-4 sig-col">
                                <div class="fw-bold small mb-3 text-start">Dicek oleh,</div>
                                <div class="sig-space"></div>
                                <input type="text" id="pic_checker" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( Nama SPV )" value="{{ $initialData['pic_checker'] ?? '' }}">
                                <div class="fw-bold small">Staff/SPV/MNG QC</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if ($.fn.select2) {
            $('#po_selector').select2({
                placeholder: "-- Pilih Nomor PO --",
                allowClear: true,
                width: '100%'
            });
        }

        // Template Baris Baru
        function getNewRowTemplate(samplingNum = 1) {
            return `
                <tr>
                    <td><input type="text" class="table-input p1-samp" value="${samplingNum}"></td>
                    <td><input type="text" class="table-input p1-jam" placeholder="08:00"></td>
                    <td><input type="text" class="table-input p1-pic" placeholder="PIC"></td>
                    <td><input type="text" class="table-input p1-brix" placeholder="65.0"></td>
                    <td><input type="text" class="table-input p1-nacl" placeholder="12.0"></td>
                    <td><input type="text" class="table-input p1-warna" value="Standar"></td>
                    <td><input type="text" class="table-input p1-organo" value="OK"></td>
                    <td><input type="text" class="table-input text-start p1-adj" value="-"></td>

                    <td><input type="text" class="table-input p2-samp" value="${samplingNum}"></td>
                    <td><input type="text" class="table-input p2-jam" placeholder="09:00"></td>
                    <td><input type="text" class="table-input p2-pic" placeholder="PIC"></td>
                    <td><input type="text" class="table-input p2-brix" placeholder="65.0"></td>
                    <td><input type="text" class="table-input p2-nacl" placeholder="12.0"></td>
                    <td><input type="text" class="table-input p2-warna" value="Standar"></td>
                    <td><input type="text" class="table-input p2-organo" value="OK"></td>
                    <td><input type="text" class="table-input text-start p2-adj" value="-"></td>

                    <td><input type="text" class="table-input p-disposisi" value="Release"></td>
                    <td class="no-print">
                        <button type="button" class="btn btn-sm btn-soft-danger btn-delete-row" title="Hapus Baris">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Template Blok Batch Baru
        function getNewBlockTemplate(blockNum) {
            return `
                <div class="batch-block-card" data-block-index="${blockNum}">
                    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                        <span class="badge bg-primary fs-12">Blok Batch #${blockNum + 1}</span>
                        <button type="button" class="btn btn-sm btn-soft-danger btn-delete-block" title="Hapus Blok Batch Ini">
                            <i class="ri-delete-bin-line me-1"></i> Hapus Blok Batch
                        </button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <table class="w-100 meta-input-group">
                                <tr>
                                    <td width="35%" class="fw-bold">Jenis Produk</td>
                                    <td width="3%">:</td>
                                    <td><input type="text" class="form-control form-control-sm block-jenis-produk" value="Kecap Manis"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Produksi</td>
                                    <td>:</td>
                                    <td><input type="date" class="form-control form-control-sm block-tgl-prod" value="{{ date('Y-m-d') }}"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jam Produksi</td>
                                    <td>:</td>
                                    <td><input type="text" class="form-control form-control-sm block-jam-prod" value="08:00" placeholder="08:00"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Kode Shift & Grup</td>
                                    <td>:</td>
                                    <td><input type="text" class="form-control form-control-sm block-shift-grup" value="Shift 1 / Grup A" placeholder="Shift 1 / Grup A"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <table class="w-100 meta-input-group">
                                <tr>
                                    <td width="35%" class="fw-bold">Batch</td>
                                    <td width="3%">:</td>
                                    <td><input type="text" class="form-control form-control-sm block-batch" value="${blockNum + 1}" placeholder="1"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">No. Dissolver</td>
                                    <td>:</td>
                                    <td><input type="text" class="form-control form-control-sm block-dissolver" value="${blockNum + 1}" placeholder="1"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Volume</td>
                                    <td>:</td>
                                    <td><input type="text" class="form-control form-control-sm block-volume" value="5000 L" placeholder="5000 L"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table-pelarutan">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 50px;">Sampling ke-</th>
                                    <th colspan="2">Serah Terima</th>
                                    <th colspan="5">Pelarutan I (GGA)</th>
                                    <th rowspan="2" style="width: 50px;">Sampling ke-</th>
                                    <th colspan="2">Serah Terima</th>
                                    <th colspan="5">Pelarutan II (GGAS)</th>
                                    <th rowspan="2" style="min-width: 90px;">Disposisi</th>
                                    <th rowspan="2" class="no-print" width="35">Aksi</th>
                                </tr>
                                <tr>
                                    <th style="min-width: 55px;">Jam</th>
                                    <th style="min-width: 75px;">PIC</th>
                                    <th style="min-width: 55px;">Brix</th>
                                    <th style="min-width: 55px;">%NaCl</th>
                                    <th style="min-width: 65px;">Warna</th>
                                    <th style="min-width: 60px;">Organo</th>
                                    <th style="min-width: 110px;">Waktu & Adjustment</th>
                                    <th style="min-width: 55px;">Jam</th>
                                    <th style="min-width: 75px;">PIC</th>
                                    <th style="min-width: 55px;">Brix</th>
                                    <th style="min-width: 55px;">%NaCl</th>
                                    <th style="min-width: 65px;">Warna</th>
                                    <th style="min-width: 60px;">Organo</th>
                                    <th style="min-width: 110px;">Waktu & Adjustment</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${getNewRowTemplate(1)}
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end no-print">
                        <button type="button" class="btn btn-sm btn-soft-success btn-add-row-block">
                            <i class="ri-add-line me-1"></i> Tambah Baris Sampling
                        </button>
                    </div>
                </div>
            `;
        }

        // Tambah Baris dalam Blok
        $(document).on('click', '.btn-add-row-block', function() {
            var tbody = $(this).closest('.batch-block-card').find('table.table-pelarutan tbody');
            var nextIndex = tbody.find('tr').length + 1;
            tbody.append(getNewRowTemplate(nextIndex));
        });

        // Hapus Baris
        $(document).on('click', '.btn-delete-row', function() {
            var tbody = $(this).closest('tbody');
            if (tbody.find('tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pemberitahuan',
                    text: 'Minimal harus ada 1 baris sampling pada blok batch.'
                });
            }
        });

        // Tambah Blok Batch Baru
        $('#btnAddBlock').on('click', function() {
            var count = $('#batchBlocksContainer .batch-block-card').length;
            $('#batchBlocksContainer').append(getNewBlockTemplate(count));
        });

        // Hapus Blok Batch
        $(document).on('click', '.btn-delete-block', function() {
            var totalBlocks = $('#batchBlocksContainer .batch-block-card').length;
            if (totalBlocks > 1) {
                $(this).closest('.batch-block-card').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pemberitahuan',
                    text: 'Minimal harus ada 1 blok batch pada dokumen.'
                });
            }
        });

        // Load Data Berdasarkan PO
        function loadPelarutanDataByPo(poId) {
            if (!poId) return;

            Swal.fire({
                title: 'Memuat Data Pelarutan...',
                text: 'Mohon tunggu sejenak',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('doc-pelarutan.fetch') }}",
                type: "GET",
                data: { po_id: poId },
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success' && response.data) {
                        var doc = response.data;
                        
                        $('#tanggal_record_doc').val(doc.tanggal_record_doc || '');
                        $('#halaman').val(doc.halaman || '1');
                        $('#catatan').val(doc.catatan || '');
                        $('#pic_sampling').val(doc.pic_sampling || '');
                        $('#pic_analis').val(doc.pic_analis || '');
                        $('#pic_checker').val(doc.pic_checker || '');

                        var container = $('#batchBlocksContainer');
                        container.empty();

                        if (doc.batch_blocks && doc.batch_blocks.length > 0) {
                            $.each(doc.batch_blocks, function(bIdx, block) {
                                var blockHtml = $(getNewBlockTemplate(bIdx));
                                blockHtml.find('.block-jenis-produk').val(block.jenis_produk || '');
                                blockHtml.find('.block-tgl-prod').val(block.tanggal_produksi || '');
                                blockHtml.find('.block-jam-prod').val(block.jam_produksi || '');
                                blockHtml.find('.block-shift-grup').val(block.kode_shift_grup || '');
                                blockHtml.find('.block-batch').val(block.batch || '');
                                blockHtml.find('.block-dissolver').val(block.no_dissolver || '');
                                blockHtml.find('.block-volume').val(block.volume || '');

                                var tbody = blockHtml.find('table.table-pelarutan tbody');
                                tbody.empty();

                                if (block.rows && block.rows.length > 0) {
                                    $.each(block.rows, function(rIdx, r) {
                                        tbody.append(`
                                            <tr>
                                                <td><input type="text" class="table-input p1-samp" value="${r.p1_sampling_ke || (rIdx + 1)}"></td>
                                                <td><input type="text" class="table-input p1-jam" value="${r.p1_jam || ''}" placeholder="08:00"></td>
                                                <td><input type="text" class="table-input p1-pic" value="${r.p1_pic || ''}" placeholder="PIC"></td>
                                                <td><input type="text" class="table-input p1-brix" value="${r.p1_brix || ''}" placeholder="65.0"></td>
                                                <td><input type="text" class="table-input p1-nacl" value="${r.p1_nacl || ''}" placeholder="12.0"></td>
                                                <td><input type="text" class="table-input p1-warna" value="${r.p1_warna || 'Standar'}"></td>
                                                <td><input type="text" class="table-input p1-organo" value="${r.p1_organo || 'OK'}"></td>
                                                <td><input type="text" class="table-input text-start p1-adj" value="${r.p1_waktu_adjustment || '-'}"></td>

                                                <td><input type="text" class="table-input p2-samp" value="${r.p2_sampling_ke || (rIdx + 1)}"></td>
                                                <td><input type="text" class="table-input p2-jam" value="${r.p2_jam || ''}" placeholder="09:00"></td>
                                                <td><input type="text" class="table-input p2-pic" value="${r.p2_pic || ''}" placeholder="PIC"></td>
                                                <td><input type="text" class="table-input p2-brix" value="${r.p2_brix || ''}" placeholder="65.0"></td>
                                                <td><input type="text" class="table-input p2-nacl" value="${r.p2_nacl || ''}" placeholder="12.0"></td>
                                                <td><input type="text" class="table-input p2-warna" value="${r.p2_warna || 'Standar'}"></td>
                                                <td><input type="text" class="table-input p2-organo" value="${r.p2_organo || 'OK'}"></td>
                                                <td><input type="text" class="table-input text-start p2-adj" value="${r.p2_waktu_adjustment || '-'}"></td>

                                                <td><input type="text" class="table-input p-disposisi" value="${r.disposisi || 'Release'}"></td>
                                                <td class="no-print">
                                                    <button type="button" class="btn btn-sm btn-soft-danger btn-delete-row" title="Hapus Baris">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        `);
                                    });
                                } else {
                                    tbody.append(getNewRowTemplate(1));
                                }

                                container.append(blockHtml);
                            });
                        } else {
                            container.append(getNewBlockTemplate(0));
                        }

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: doc.is_saved ? 'Memuat Dokumen Tersimpan' : 'Data Terisi Otomatis dari Database Pelarutan'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem saat mengambil data PO.'
                    });
                }
            });
        }

        // PO Selector change
        $('#po_selector').on('change', function() {
            var poId = $(this).val();
            if (poId) {
                loadPelarutanDataByPo(poId);
            }
        });

        // Tarik Data DB
        $('#btnFetchDb').on('click', function() {
            var poId = $('#po_selector').val();
            if (!poId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih PO Terlebih Dahulu',
                    text: 'Silakan pilih nomor PO pada dropdown di atas.'
                });
                return;
            }
            loadPelarutanDataByPo(poId);
        });

        // Simpan Dokumen
        $('#btnSaveDoc').on('click', function() {
            var poId = $('#po_selector').val();
            if (!poId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih PO Terlebih Dahulu',
                    text: 'Silakan pilih nomor PO sebelum menyimpan formulir.'
                });
                return;
            }

            var batchBlocks = [];
            $('#batchBlocksContainer .batch-block-card').each(function() {
                var card = $(this);
                var rows = [];

                card.find('table.table-pelarutan tbody tr').each(function() {
                    var tr = $(this);
                    rows.push({
                        p1_sampling_ke: tr.find('.p1-samp').val(),
                        p1_jam: tr.find('.p1-jam').val(),
                        p1_pic: tr.find('.p1-pic').val(),
                        p1_brix: tr.find('.p1-brix').val(),
                        p1_nacl: tr.find('.p1-nacl').val(),
                        p1_warna: tr.find('.p1-warna').val(),
                        p1_organo: tr.find('.p1-organo').val(),
                        p1_waktu_adjustment: tr.find('.p1-adj').val(),

                        p2_sampling_ke: tr.find('.p2-samp').val(),
                        p2_jam: tr.find('.p2-jam').val(),
                        p2_pic: tr.find('.p2-pic').val(),
                        p2_brix: tr.find('.p2-brix').val(),
                        p2_nacl: tr.find('.p2-nacl').val(),
                        p2_warna: tr.find('.p2-warna').val(),
                        p2_organo: tr.find('.p2-organo').val(),
                        p2_waktu_adjustment: tr.find('.p2-adj').val(),

                        disposisi: tr.find('.p-disposisi').val()
                    });
                });

                batchBlocks.push({
                    jenis_produk: card.find('.block-jenis-produk').val(),
                    tanggal_produksi: card.find('.block-tgl-prod').val(),
                    jam_produksi: card.find('.block-jam-prod').val(),
                    kode_shift_grup: card.find('.block-shift-grup').val(),
                    batch: card.find('.block-batch').val(),
                    no_dissolver: card.find('.block-dissolver').val(),
                    volume: card.find('.block-volume').val(),
                    rows: rows
                });
            });

            var payload = {
                production_batch_id: poId,
                tanggal_record_doc: $('#tanggal_record_doc').val(),
                halaman: $('#halaman').val(),
                batch_blocks: batchBlocks,
                catatan: $('#catatan').val(),
                pic_sampling: $('#pic_sampling').val(),
                pic_analis: $('#pic_analis').val(),
                pic_checker: $('#pic_checker').val()
            };

            Swal.fire({
                title: 'Menyimpan Dokumen...',
                text: 'Mohon tunggu sejenak',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('doc-pelarutan.store') }}",
                type: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                dataType: "json",
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Disimpan!',
                        text: response.message || 'Formulir Analisis Proses Pelarutan berhasil disimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Dokumen',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan formulir dokumen.'
                    });
                }
            });
        });

        // Export Excel
        $('#btnExportExcel').on('click', function() {
            var poId = $('#po_selector').val();
            if (!poId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih PO Terlebih Dahulu',
                    text: 'Silakan pilih nomor PO yang ingin diexport ke Excel.'
                });
                return;
            }

            var exportUrl = "{{ route('doc-pelarutan.export') }}?po_id=" + poId;
            window.location.href = exportUrl;
        });

        // Print
        $('#btnPrint').on('click', function() {
            window.print();
        });
    });
</script>
@endsection
