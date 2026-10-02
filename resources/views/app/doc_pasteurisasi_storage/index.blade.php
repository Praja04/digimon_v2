@extends('layouts.component.main')
@section('title', 'Form Hasil Analisis Pasteurisasi dan Storage Tank')

@section('styles')
<style>
    .document-paper {
        background: #ffffff;
        border: 1px solid #dcdfe6;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        padding: 30px;
        position: relative;
    }

    .doc-header-title {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #1a202c;
        text-transform: uppercase;
    }

    .doc-meta-table td {
        padding: 4px 8px;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .doc-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 0.85rem;
    }

    .doc-table th, .doc-table td {
        border: 1px solid #2d3748;
        padding: 6px 4px;
        text-align: center;
        vertical-align: middle;
    }

    .doc-table th.th-main-past {
        background-color: #e2efda;
        color: #1e4620;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .doc-table th.th-main-storage {
        background-color: #ddebf7;
        color: #1a3b5c;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .doc-table th.th-sub {
        background-color: #f7fafc;
        color: #4a5568;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .doc-table .table-input {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        text-align: center;
        font-size: 0.85rem;
        padding: 4px 2px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .doc-table .table-input:focus, .doc-table .table-input:hover {
        border-color: #405189;
        background: #ffffff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(64, 81, 137, 0.2);
    }

    .doc-table .table-select {
        width: 100%;
        border: 1px solid transparent;
        background: transparent;
        font-size: 0.82rem;
        padding: 4px 0;
        border-radius: 4px;
        text-align-last: center;
    }

    .doc-table .table-select:focus, .doc-table .table-select:hover {
        border-color: #405189;
        background: #ffffff;
        outline: none;
    }

    .doc-signature-box {
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        padding: 12px;
        background: #f8fafc;
        text-align: center;
        height: 100%;
    }

    .btn-row-action {
        padding: 2px 6px;
        font-size: 0.75rem;
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
        .doc-table th, .doc-table td {
            border: 1px solid #000000 !important;
        }
        .doc-table .table-input {
            border: none !important;
            background: transparent !important;
        }
        .doc-table .table-select {
            border: none !important;
            appearance: none;
            -webkit-appearance: none;
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
                            <li class="breadcrumb-item"><a href="{{ route('analisa.monitoring-turun-blending.menu') }}">Menu</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('analisa.monitoring-turun-blending.export.index') }}">Export & Cetak</a></li>
                            <li class="breadcrumb-item active">Dokumen FRM/QLB/04/104/006-01</li>
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
                                    <i class="ri-file-excel-2-line me-1"></i> Export Excel (FRM)
                                </button>
                                <button type="button" id="btnPrint" class="btn btn-dark d-flex align-items-center shadow-sm">
                                    <i class="ri-printer-line me-1"></i> Cetak / Print PDF
                                </button>
                                <a href="{{ route('analisa.monitoring-turun-blending.export.index') }}" class="btn btn-soft-secondary d-flex align-items-center">
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
                    
                    <!-- Document Header -->
                    <div class="row border-bottom pb-3 mb-3 align-items-center">
                        <div class="col-12 col-md-8 text-center text-md-start mb-2 mb-md-0">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                                <div class="doc-logo-box p-1 border rounded" style="border: 2px solid #000 !important; background: #fff;">
                                    <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 48px; object-fit: contain;">
                                </div>
                                <div>
                                    <h4 class="doc-header-title mb-0">HASIL ANALISIS PASTEURISASI DAN STORAGE TANK</h4>
                                    <span class="badge bg-primary-subtle text-primary no-print">Format Resmi Quality Control</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-md-end">
                            <div class="d-flex flex-column align-items-md-end">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-bold small text-nowrap">Tanggal Record Doc :</span>
                                    <input type="date" id="tanggal_record_doc" class="form-control form-control-sm text-center" style="max-width: 150px;" value="{{ $initialData['tanggal_record_doc'] ?? date('Y-m-d') }}">
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold small text-nowrap">Halaman :</span>
                                    <input type="text" id="halaman" class="form-control form-control-sm text-center" style="max-width: 100px;" value="{{ $initialData['halaman'] ?? '1' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Information Grid (Sesuai Form Resmi) -->
                    <div class="row mb-4 bg-light p-3 rounded mx-0">
                        <div class="col-12 mb-2">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-nowrap" style="min-width: 140px;">Jenis Produk :</span>
                                        <input type="text" id="jenis_produk" class="form-control form-control-sm bg-white" placeholder="Nama Produk / Varian" value="{{ $initialData['jenis_produk'] ?? ($initialData['variant'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <table class="w-100 doc-meta-table">
                                <tr>
                                    <td width="35%" class="fw-bold">Tanggal Produksi</td>
                                    <td width="5%">:</td>
                                    <td>
                                        <input type="date" id="tanggal_produksi" class="form-control form-control-sm bg-white" value="{{ $initialData['tanggal_produksi'] ?? date('Y-m-d') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Jam Produksi</td>
                                    <td>:</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="text" id="jam_produksi_start" class="form-control form-control-sm bg-white text-center" placeholder="00:00" value="{{ $initialData['jam_produksi_start'] ?? '' }}" style="max-width: 90px;">
                                            <span class="small fw-semibold text-muted">s/d</span>
                                            <input type="text" id="jam_produksi_end" class="form-control form-control-sm bg-white text-center" placeholder="00:00" value="{{ $initialData['jam_produksi_end'] ?? '' }}" style="max-width: 90px;">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <table class="w-100 doc-meta-table">
                                <tr>
                                    <td width="35%" class="fw-bold">Kode Shift & Grup</td>
                                    <td width="5%">:</td>
                                    <td>
                                        <input type="text" id="kode_shift_grup" class="form-control form-control-sm bg-white" placeholder="Contoh: Shift 1 / Grup A" value="{{ $initialData['kode_shift_grup'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Batch</td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" id="batch" class="form-control form-control-sm bg-white" placeholder="Contoh: 01 s/d 10" value="{{ $initialData['batch'] ?? ($initialData['batch_range'] ?? '') }}">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- SECTION 1: TABEL PASTEURISASI -->
                    <div class="section-container mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-success mb-0">
                                <i class="ri-temp-hot-line me-1"></i> PASTEURISASI
                            </h6>
                            <button type="button" id="btnAddPastRow" class="btn btn-sm btn-outline-success no-print">
                                <i class="ri-add-line me-1"></i> Tambah Baris Pasteurisasi
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="tablePasteurisasi" class="doc-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="th-main-past" style="min-width: 90px;">Sampling</th>
                                        <th colspan="2" class="th-main-past">Serah Terima</th>
                                        <th colspan="10" class="th-main-past">Analisis</th>
                                        <th rowspan="2" class="th-main-past" style="min-width: 140px;">Disposisi / Keterangan</th>
                                        <th rowspan="2" class="th-main-past no-print" width="40">Aksi</th>
                                    </tr>
                                    <tr>
                                        <th class="th-sub" style="min-width: 65px;">Jam</th>
                                        <th class="th-sub" style="min-width: 90px;">PIC</th>
                                        <th class="th-sub" style="min-width: 80px;">BJ (g/mL)</th>
                                        <th class="th-sub" style="min-width: 65px;">Brix</th>
                                        <th class="th-sub" style="min-width: 60px;">pH</th>
                                        <th class="th-sub" style="min-width: 60px;">Aw</th>
                                        <th class="th-sub" style="min-width: 90px;">Viskositas (ps)</th>
                                        <th class="th-sub" style="min-width: 75px;">Organo</th>
                                        <th class="th-sub" style="min-width: 75px;">Aroma</th>
                                        <th class="th-sub" style="min-width: 85px;">Warna</th>
                                        <th class="th-sub" style="min-width: 65px;">Buih</th>
                                        <th class="th-sub" style="min-width: 80px;">Endapan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($initialData['pasteurisasi_rows']))
                                        @foreach($initialData['pasteurisasi_rows'] as $idx => $pRow)
                                        <tr data-row-index="{{ $idx }}">
                                            <td><input type="text" class="table-input past-sampling" placeholder="Sampling {{ $idx + 1 }}" value="{{ $pRow['sampling'] ?? ('Sampling ' . ($idx + 1)) }}"></td>
                                            <td><input type="text" class="table-input past-jam" value="{{ $pRow['jam'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-pic" value="{{ $pRow['pic'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-bj" value="{{ $pRow['bj'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-brix" value="{{ $pRow['brix'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-ph" value="{{ $pRow['ph'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-aw" value="{{ $pRow['aw'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-visko" value="{{ $pRow['viskositas'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-organo" value="{{ $pRow['organo'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-aroma" value="{{ $pRow['aroma'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-warna" value="{{ $pRow['warna'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-buih" value="{{ $pRow['buih'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input past-endapan" value="{{ $pRow['endapan'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input text-start past-disposisi" value="{{ $pRow['disposisi'] ?? '' }}"></td>
                                            <td class="no-print">
                                                <button type="button" class="btn btn-sm btn-soft-danger btn-row-action btn-delete-row" title="Hapus Baris">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SECTION 2: TABEL STORAGE TANK -->
                    <div class="section-container mb-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold text-primary mb-0">
                                    <i class="ri-inbox-line me-1"></i> STORAGE TANK
                                </h6>
                                <div class="d-flex align-items-center gap-1 ms-3">
                                    <span class="fw-bold text-dark small">KODE ST :</span>
                                    <input type="text" id="kode_st" class="form-control form-control-sm text-center fw-bold text-primary" style="max-width: 100px;" value="{{ $initialData['kode_st'] ?? 'ST 01' }}" placeholder="ST 01">
                                </div>
                            </div>
                            <button type="button" id="btnAddStorageRow" class="btn btn-sm btn-outline-primary no-print">
                                <i class="ri-add-line me-1"></i> Tambah Baris Storage Tank
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="tableStorage" class="doc-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="th-main-storage" style="min-width: 90px;">Sampling</th>
                                        <th colspan="2" class="th-main-storage">Serah Terima</th>
                                        <th colspan="12" class="th-main-storage">Analisis</th>
                                        <th rowspan="2" class="th-main-storage" style="min-width: 140px;">Disposisi / Keterangan</th>
                                        <th rowspan="2" class="th-main-storage no-print" width="40">Aksi</th>
                                    </tr>
                                    <tr>
                                        <th class="th-sub" style="min-width: 65px;">Jam</th>
                                        <th class="th-sub" style="min-width: 90px;">PIC</th>
                                        <th class="th-sub" style="min-width: 75px;">BJ</th>
                                        <th class="th-sub" style="min-width: 65px;">Brix</th>
                                        <th class="th-sub" style="min-width: 60px;">pH</th>
                                        <th class="th-sub" style="min-width: 70px;">% NaCl</th>
                                        <th class="th-sub" style="min-width: 90px;">Viskositas (ps)</th>
                                        <th class="th-sub" style="min-width: 75px;">Organo</th>
                                        <th class="th-sub" style="min-width: 75px;">Aroma</th>
                                        <th class="th-sub" style="min-width: 85px;">Warna</th>
                                        <th class="th-sub" style="min-width: 65px;">Buih</th>
                                        <th class="th-sub" style="min-width: 80px;">Endapan</th>
                                        <th class="th-sub" style="min-width: 75px;">Kristal</th>
                                        <th class="th-sub" style="min-width: 60px;">Aw</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($initialData['storage_rows']))
                                        @foreach($initialData['storage_rows'] as $idx => $sRow)
                                        <tr data-row-index="{{ $idx }}">
                                            <td><input type="text" class="table-input st-sampling" placeholder="Sampling {{ $idx + 1 }}" value="{{ $sRow['sampling'] ?? ('Sampling ' . ($idx + 1)) }}"></td>
                                            <td><input type="text" class="table-input st-jam" value="{{ $sRow['jam'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-pic" value="{{ $sRow['pic'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-bj" value="{{ $sRow['bj'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-brix" value="{{ $sRow['brix'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-ph" value="{{ $sRow['ph'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-nacl" value="{{ $sRow['nacl'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-visko" value="{{ $sRow['viskositas'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-organo" value="{{ $sRow['organo'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-aroma" value="{{ $sRow['aroma'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-warna" value="{{ $sRow['warna'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-buih" value="{{ $sRow['buih'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-endapan" value="{{ $sRow['endapan'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-kristal" value="{{ $sRow['kristal'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input st-aw" value="{{ $sRow['aw'] ?? '' }}"></td>
                                            <td><input type="text" class="table-input text-start st-disposisi" value="{{ $sRow['disposisi'] ?? '' }}"></td>
                                            <td class="no-print">
                                                <button type="button" class="btn btn-sm btn-soft-danger btn-row-action btn-delete-row" title="Hapus Baris">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SECTION 3: CATATAN & TANDA TANGAN (Sesuai Form Resmi) -->
                    <div class="row pt-2 mt-2">
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="p-2 border rounded bg-light h-100">
                                <label class="fw-bold small mb-1">Catatan :</label>
                                <textarea id="catatan" class="form-control form-control-sm bg-white" rows="5" placeholder="Ketik catatan khusus proses di sini...">{{ $initialData['catatan'] ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8">
                            <div class="row">
                                <div class="col-4 mb-3">
                                    <div class="doc-signature-box">
                                        <div class="fw-bold small mb-4">Disampling oleh,</div>
                                        <input type="text" id="pic_sampling" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( ________________ )" value="{{ $initialData['pic_sampling'] ?? '' }}">
                                        <div class="fw-bold text-muted small">Produksi</div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="doc-signature-box">
                                        <div class="fw-bold small mb-4">Dianalisis oleh,</div>
                                        <input type="text" id="pic_analis" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( ________________ )" value="{{ $initialData['pic_analis'] ?? (auth()->user()->name ?? '') }}">
                                        <div class="fw-bold text-muted small">QC Analis</div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="doc-signature-box">
                                        <div class="fw-bold small mb-4">Dicek oleh,</div>
                                        <input type="text" id="pic_checker" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( ________________ )" value="{{ $initialData['pic_checker'] ?? '' }}">
                                        <div class="fw-bold text-muted small">Staff/SPV/MNG QC</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-1">
                                <span class="badge bg-light text-dark border">FRM/QLB/04/104/006-01</span>
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

        // Inisialisasi Select2 jika tersedia
        if ($.fn.select2) {
            $('#po_selector').select2({
                placeholder: "-- Pilih Nomor PO --",
                allowClear: true,
                width: '100%'
            });
        }

        // Template Baris Baru Pasteurisasi
        function getNewPasteurisasiRow(samplingLabel = '') {
            var currentRows = $('#tablePasteurisasi tbody tr').length + 1;
            var label = samplingLabel || ('Sampling ' + currentRows);
            return `
                <tr>
                    <td><input type="text" class="table-input past-sampling" placeholder="${label}" value="${label}"></td>
                    <td><input type="text" class="table-input past-jam"></td>
                    <td><input type="text" class="table-input past-pic"></td>
                    <td><input type="text" class="table-input past-bj"></td>
                    <td><input type="text" class="table-input past-brix"></td>
                    <td><input type="text" class="table-input past-ph"></td>
                    <td><input type="text" class="table-input past-aw"></td>
                    <td><input type="text" class="table-input past-visko"></td>
                    <td><input type="text" class="table-input past-organo"></td>
                    <td><input type="text" class="table-input past-aroma"></td>
                    <td><input type="text" class="table-input past-warna"></td>
                    <td><input type="text" class="table-input past-buih"></td>
                    <td><input type="text" class="table-input past-endapan"></td>
                    <td><input type="text" class="table-input text-start past-disposisi"></td>
                    <td class="no-print">
                        <button type="button" class="btn btn-sm btn-soft-danger btn-row-action btn-delete-row" title="Hapus Baris">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Template Baris Baru Storage Tank
        function getNewStorageRow(samplingLabel = '') {
            var currentRows = $('#tableStorage tbody tr').length + 1;
            var label = samplingLabel || ('Sampling ' + currentRows);
            return `
                <tr>
                    <td><input type="text" class="table-input st-sampling" placeholder="${label}" value="${label}"></td>
                    <td><input type="text" class="table-input st-jam"></td>
                    <td><input type="text" class="table-input st-pic"></td>
                    <td><input type="text" class="table-input st-bj"></td>
                    <td><input type="text" class="table-input st-brix"></td>
                    <td><input type="text" class="table-input st-ph"></td>
                    <td><input type="text" class="table-input st-nacl"></td>
                    <td><input type="text" class="table-input st-visko"></td>
                    <td><input type="text" class="table-input st-organo"></td>
                    <td><input type="text" class="table-input st-aroma"></td>
                    <td><input type="text" class="table-input st-warna"></td>
                    <td><input type="text" class="table-input st-buih"></td>
                    <td><input type="text" class="table-input st-endapan"></td>
                    <td><input type="text" class="table-input st-kristal"></td>
                    <td><input type="text" class="table-input st-aw"></td>
                    <td><input type="text" class="table-input text-start st-disposisi"></td>
                    <td class="no-print">
                        <button type="button" class="btn btn-sm btn-soft-danger btn-row-action btn-delete-row" title="Hapus Baris">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Tambah Baris Pasteurisasi
        $('#btnAddPastRow').on('click', function() {
            $('#tablePasteurisasi tbody').append(getNewPasteurisasiRow());
        });

        // Tambah Baris Storage Tank
        $('#btnAddStorageRow').on('click', function() {
            $('#tableStorage tbody').append(getNewStorageRow());
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
                    text: 'Minimal harus ada 1 baris pada tabel.'
                });
            }
        });

        // Fungsi Load Data berdasarkan PO
        function loadDataByPo(poId, forceAutoDb = false) {
            if (!poId) return;

            Swal.fire({
                title: 'Memuat Data Dokumen...',
                text: 'Mohon tunggu sejenak',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('doc-pasteurisasi-storage.fetch') }}",
                type: "GET",
                data: { po_id: poId },
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success' && response.data) {
                        var doc = response.data;
                        
                        // Populate Meta
                        $('#tanggal_record_doc').val(doc.tanggal_record_doc || '');
                        $('#halaman').val(doc.halaman || '1');
                        $('#jenis_produk').val(doc.jenis_produk || doc.variant || '');
                        $('#tanggal_produksi').val(doc.tanggal_produksi || '');
                        $('#jam_produksi_start').val(doc.jam_produksi_start || '');
                        $('#jam_produksi_end').val(doc.jam_produksi_end || '');
                        $('#kode_shift_grup').val(doc.kode_shift_grup || '');
                        $('#batch').val(doc.batch || doc.batch_range || '');
                        $('#kode_st').val(doc.kode_st || 'ST 01');

                        // Populate Catatan & Signatures
                        $('#catatan').val(doc.catatan || '');
                        $('#pic_sampling').val(doc.pic_sampling || '');
                        $('#pic_serah_terima').val(doc.pic_serah_terima || '');
                        $('#pic_analis').val(doc.pic_analis || '');
                        $('#pic_checker').val(doc.pic_checker || '');

                        // Populate Pasteurisasi Table
                        var pastTbody = $('#tablePasteurisasi tbody');
                        pastTbody.empty();
                        if (doc.pasteurisasi_rows && doc.pasteurisasi_rows.length > 0) {
                            $.each(doc.pasteurisasi_rows, function(i, row) {
                                pastTbody.append(`
                                    <tr data-row-index="${i}">
                                        <td><input type="text" class="table-input past-sampling" placeholder="Sampling ${i + 1}" value="${row.sampling || ('Sampling ' + (i + 1))}"></td>
                                        <td><input type="text" class="table-input past-jam" value="${row.jam || ''}"></td>
                                        <td><input type="text" class="table-input past-pic" value="${row.pic || ''}"></td>
                                        <td><input type="text" class="table-input past-bj" value="${row.bj || ''}"></td>
                                        <td><input type="text" class="table-input past-brix" value="${row.brix || ''}"></td>
                                        <td><input type="text" class="table-input past-ph" value="${row.ph || ''}"></td>
                                        <td><input type="text" class="table-input past-aw" value="${row.aw || ''}"></td>
                                        <td><input type="text" class="table-input past-visko" value="${row.viskositas || ''}"></td>
                                        <td><input type="text" class="table-input past-organo" value="${row.organo || ''}"></td>
                                        <td><input type="text" class="table-input past-aroma" value="${row.aroma || ''}"></td>
                                        <td><input type="text" class="table-input past-warna" value="${row.warna || ''}"></td>
                                        <td><input type="text" class="table-input past-buih" value="${row.buih || ''}"></td>
                                        <td><input type="text" class="table-input past-endapan" value="${row.endapan || ''}"></td>
                                        <td><input type="text" class="table-input text-start past-disposisi" value="${row.disposisi || ''}"></td>
                                        <td class="no-print">
                                            <button type="button" class="btn btn-sm btn-soft-danger btn-row-action btn-delete-row" title="Hapus Baris">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `);
                            });
                        } else {
                            pastTbody.append(getNewPasteurisasiRow());
                        }

                        // Populate Storage Table
                        var stTbody = $('#tableStorage tbody');
                        stTbody.empty();
                        if (doc.storage_rows && doc.storage_rows.length > 0) {
                            $.each(doc.storage_rows, function(i, row) {
                                stTbody.append(`
                                    <tr data-row-index="${i}">
                                        <td><input type="text" class="table-input st-sampling" placeholder="Sampling ${i + 1}" value="${row.sampling || ('Sampling ' + (i + 1))}"></td>
                                        <td><input type="text" class="table-input st-jam" value="${row.jam || ''}"></td>
                                        <td><input type="text" class="table-input st-pic" value="${row.pic || ''}"></td>
                                        <td><input type="text" class="table-input st-bj" value="${row.bj || ''}"></td>
                                        <td><input type="text" class="table-input st-brix" value="${row.brix || ''}"></td>
                                        <td><input type="text" class="table-input st-ph" value="${row.ph || ''}"></td>
                                        <td><input type="text" class="table-input st-nacl" value="${row.nacl || ''}"></td>
                                        <td><input type="text" class="table-input st-visko" value="${row.viskositas || ''}"></td>
                                        <td><input type="text" class="table-input st-organo" value="${row.organo || ''}"></td>
                                        <td><input type="text" class="table-input st-aroma" value="${row.aroma || ''}"></td>
                                        <td><input type="text" class="table-input st-warna" value="${row.warna || ''}"></td>
                                        <td><input type="text" class="table-input st-buih" value="${row.buih || ''}"></td>
                                        <td><input type="text" class="table-input st-endapan" value="${row.endapan || ''}"></td>
                                        <td><input type="text" class="table-input st-kristal" value="${row.kristal || ''}"></td>
                                        <td><input type="text" class="table-input st-aw" value="${row.aw || ''}"></td>
                                        <td><input type="text" class="table-input text-start st-disposisi" value="${row.disposisi || ''}"></td>
                                        <td class="no-print">
                                            <button type="button" class="btn btn-sm btn-soft-danger btn-row-action btn-delete-row" title="Hapus Baris">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `);
                            });
                        } else {
                            stTbody.append(getNewStorageRow());
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
                            title: doc.is_saved ? 'Memuat Dokumen Tersimpan' : 'Data Terisi Otomatis dari Monitoring Database'
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

        // Event saat ganti PO
        $('#po_selector').on('change', function() {
            var poId = $(this).val();
            if (poId) {
                loadDataByPo(poId);
            }
        });

        // Event Tombol Tarik Data Database
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
            loadDataByPo(poId, true);
        });

        // Event Simpan Dokumen
        $('#btnSaveDoc').on('click', function() {
            var poId = $('#po_selector').val();
            if (!poId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih PO Terlebih Dahulu',
                    text: 'Silakan pilih nomor PO sebelum menyimpan formulir dokumen.'
                });
                return;
            }

            // Kumpulkan baris Pasteurisasi
            var pasteurisasiRows = [];
            $('#tablePasteurisasi tbody tr').each(function() {
                var row = $(this);
                pasteurisasiRows.push({
                    sampling: row.find('.past-sampling').val(),
                    jam: row.find('.past-jam').val(),
                    pic: row.find('.past-pic').val(),
                    bj: row.find('.past-bj').val(),
                    brix: row.find('.past-brix').val(),
                    ph: row.find('.past-ph').val(),
                    aw: row.find('.past-aw').val(),
                    viskositas: row.find('.past-visko').val(),
                    organo: row.find('.past-organo').val(),
                    aroma: row.find('.past-aroma').val(),
                    warna: row.find('.past-warna').val(),
                    buih: row.find('.past-buih').val(),
                    endapan: row.find('.past-endapan').val(),
                    disposisi: row.find('.past-disposisi').val()
                });
            });

            // Kumpulkan baris Storage
            var storageRows = [];
            $('#tableStorage tbody tr').each(function() {
                var row = $(this);
                storageRows.push({
                    sampling: row.find('.st-sampling').val(),
                    jam: row.find('.st-jam').val(),
                    pic: row.find('.st-pic').val(),
                    bj: row.find('.st-bj').val(),
                    brix: row.find('.st-brix').val(),
                    ph: row.find('.st-ph').val(),
                    nacl: row.find('.st-nacl').val(),
                    viskositas: row.find('.st-visko').val(),
                    organo: row.find('.st-organo').val(),
                    aroma: row.find('.st-aroma').val(),
                    warna: row.find('.st-warna').val(),
                    buih: row.find('.st-buih').val(),
                    endapan: row.find('.st-endapan').val(),
                    kristal: row.find('.st-kristal').val(),
                    aw: row.find('.st-aw').val(),
                    disposisi: row.find('.st-disposisi').val()
                });
            });

            var payload = {
                production_batch_id: poId,
                jenis_produk: $('#jenis_produk').val(),
                tanggal_produksi: $('#tanggal_produksi').val(),
                jam_produksi_start: $('#jam_produksi_start').val(),
                jam_produksi_end: $('#jam_produksi_end').val(),
                tanggal_record_doc: $('#tanggal_record_doc').val(),
                halaman: $('#halaman').val(),
                kode_shift_grup: $('#kode_shift_grup').val(),
                batch: $('#batch').val(),
                kode_st: $('#kode_st').val(),
                pasteurisasi_rows: pasteurisasiRows,
                storage_rows: storageRows,
                catatan: $('#catatan').val(),
                pic_sampling: $('#pic_sampling').val(),
                pic_serah_terima: $('#pic_serah_terima').val(),
                pic_analis: $('#pic_analis').val(),
                pic_checker: $('#pic_checker').val(),
                doc_code: 'FRM/QLB/04/104/006-01'
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
                url: "{{ route('doc-pasteurisasi-storage.store') }}",
                type: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                dataType: "json",
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Disimpan!',
                        text: response.message || 'Formulir Dokumen FRM/QLB/04/104/006-01 berhasil disimpan.',
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

        // Event Export Excel
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

            var exportUrl = "{{ route('doc-pasteurisasi-storage.export') }}?po_id=" + poId;
            window.location.href = exportUrl;
        });

        // Event Print
        $('#btnPrint').on('click', function() {
            window.print();
        });
    });
</script>
@endsection
