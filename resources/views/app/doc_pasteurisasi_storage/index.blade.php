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
        padding: 0 !important;
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
        padding: 5px 3px !important;
        border-color: #475569;
        line-height: 1.25;
    }

    .table-qc-data th.th-storage-main {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
        font-weight: 700;
        font-size: 0.80rem;
        padding: 5px 3px !important;
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

    /* Row Hover & Focus Highlight */
    .table-qc-data tbody tr.data-row {
        transition: background-color 0.12s ease;
    }

    .table-qc-data tbody tr.data-row:hover td {
        background-color: #f8fafc;
    }

    .table-qc-data tbody tr.data-row:focus-within td {
        background-color: #eff6ff !important;
    }

    /* Clean Spreadsheet Cells (No Box in Box, Excel Feel) */
    .clean-cell-input {
        width: 100%;
        height: 100%;
        min-height: 34px;
        border: none !important;
        background: transparent !important;
        text-align: center;
        font-size: 0.83rem;
        font-weight: 500;
        padding: 4px 3px;
        border-radius: 0 !important;
        color: #0f172a;
        outline: none;
        box-shadow: none !important;
        box-sizing: border-box;
        display: block;
        transition: background-color 0.15s ease;
    }

    .clean-cell-input:focus {
        background: #ffffff !important;
        box-shadow: inset 0 0 0 2px #2563eb !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        z-index: 5;
    }

    .clean-cell-textarea {
        width: 100%;
        min-height: 44px;
        height: auto;
        border: none !important;
        background: transparent !important;
        text-align: left;
        font-size: 0.77rem;
        font-weight: 500;
        padding: 4px 6px;
        border-radius: 0 !important;
        resize: none;
        line-height: 1.25;
        color: #0f172a;
        outline: none;
        box-shadow: none !important;
        box-sizing: border-box;
        display: block;
    }

    .clean-cell-textarea:focus {
        background: #ffffff !important;
        box-shadow: inset 0 0 0 2px #2563eb !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        z-index: 5;
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
        height: 48px;
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
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnFetchDb">
                            <i class="ri-refresh-line me-1"></i> Tarik Data DB
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" id="btnSaveDoc">
                            <i class="ri-save-3-line me-1"></i> Simpan Dokumen
                        </button>
                        <a href="#" class="btn btn-success btn-sm" id="btnExportExcel">
                            <i class="ri-file-excel-2-line me-1"></i> Download Excel
                        </a>
                        <a href="#" target="_blank" class="btn btn-info btn-sm text-white" id="btnPrintView">
                            <i class="ri-printer-line me-1"></i> Cetak / Print
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Banner (Keyboard Friendly) -->
        <div class="alert border-0 shadow-sm d-flex flex-wrap align-items-center justify-content-between mb-3 py-2 px-3 bg-light border-start border-primary border-4">
            <div class="d-flex align-items-center fs-12 text-dark">
                <i class="ri-keyboard-line fs-18 me-2 text-primary"></i>
                <div>
                    <strong>Navigasi Cepat (Excel Friendly):</strong>
                    Gunakan tombol <kbd class="bg-white text-primary border px-1 fw-bold">Tab</kbd> untuk geser kolom &bull;
                    Tekan <kbd class="bg-white text-dark border px-1 fw-bold">Enter</kbd> atau <kbd class="bg-white text-dark border px-1 fw-bold">&darr;</kbd> untuk turun ke baris berikutnya &bull;
                    Teks otomatis ter-blok saat sel aktif.
                </div>
            </div>
            <div class="fs-11 text-muted fw-semibold mt-1 mt-md-0">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1">
                    <i class="ri-pages-line me-1"></i> 1 Lembar = 1 Blending (1 Pasangan Batch)
                </span>
            </div>
        </div>

        <!-- Lembar Formulir Dokumen Resmi (Paper Canvas) -->
        <div class="lembar-sheet-card">
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
                            <td class="p-1 border-bottom text-start">
                                <input type="date" class="border-0 bg-transparent p-0 doc-tgl-record" id="doc_tgl_record" style="font-size: 0.82rem;" value="{{ $initialData['tanggal_record_doc'] ?? date('Y-m-d') }}">
                            </td>
                        </tr>
                        <tr>
                            <td class="p-1 text-start fw-bold bg-light">Halaman</td>
                            <td class="p-1 text-start fw-semibold">
                                <input type="text" class="border-0 bg-transparent p-0 doc-halaman" id="doc_halaman" style="font-size: 0.82rem; max-width: 65px;" value="{{ $initialData['halaman'] ?? '1 / 1' }}">
                            </td>
                        </tr>
                    </table>
                    <div class="clearfix"></div>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border fs-11 fst-italic">Notes: 1 lembar: 1 blending (1 pasangan batch)</span>
                </div>
            </div>

            <!-- 2. Metadata Grid -->
            <table class="w-100 meta-input-group mb-3">
                <tr>
                    <td width="14%" class="fw-bold bg-light">Jenis Produk</td>
                    <td width="36%">
                        <div class="d-flex align-items-center">
                            <span class="me-1 fw-bold">:</span>
                            <input type="text" class="form-control form-control-sm" id="meta_jenis_produk" value="{{ $initialData['jenis_produk'] ?? '' }}">
                        </div>
                    </td>
                    <td width="14%" class="fw-bold bg-light">Kode Shift & Grup</td>
                    <td width="36%">
                        <div class="d-flex align-items-center">
                            <span class="me-1 fw-bold">:</span>
                            <input type="text" class="form-control form-control-sm" id="meta_kode_shift" placeholder="3/C" value="{{ $initialData['kode_shift_grup'] ?? '' }}">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold bg-light">Tanggal Produksi</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-1 fw-bold">:</span>
                            <input type="date" class="form-control form-control-sm" id="meta_tanggal_produksi" value="{{ $initialData['tanggal_produksi'] ?? date('Y-m-d') }}">
                        </div>
                    </td>
                    <td class="fw-bold bg-light">Batch</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-1 fw-bold">:</span>
                            <input type="text" class="form-control form-control-sm" id="meta_batch" placeholder="1-4" value="{{ $initialData['batch'] ?? '' }}">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold bg-light">Jam Produksi</td>
                    <td colspan="3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold">:</span>
                            <input type="text" class="form-control form-control-sm text-center" id="meta_jam_start" placeholder="00:00" style="max-width: 75px;" value="{{ $initialData['jam_produksi_start'] ?? '' }}">
                            <span class="small fw-bold text-muted">s/d</span>
                            <input type="text" class="form-control form-control-sm text-center" id="meta_jam_end" placeholder="00:00" style="max-width: 75px;" value="{{ $initialData['jam_produksi_end'] ?? '' }}">
                        </div>
                    </td>
                </tr>
            </table>

            <!-- 3. Tabel 1: PASTEURISASI (Integrated Header Bar, 1040px Proportional Grid) -->
            <div class="d-flex justify-content-between align-items-center mb-1 text-muted fs-11">
                <span class="badge bg-light text-secondary border"><i class="ri-arrow-left-right-line me-1"></i> Geser horizontal untuk melihat seluruh kolom</span>
            </div>
            <div class="table-responsive mb-4">
                <table class="table-qc-data" id="tablePasteurisasi" style="min-width: 1040px;">
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
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-temp-hot-line text-success fs-5"></i>
                                        <span class="fw-bold text-success fs-13" style="letter-spacing: 0.5px;">PASTEURISASI</span>
                                    </div>
                                    <div class="d-flex gap-1 no-print">
                                        <button type="button" class="btn btn-xs btn-outline-success py-1 px-2 bg-white" id="btnAddPastRow">
                                            <i class="ri-add-line me-1"></i> Tambah Baris
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 bg-white" id="btnDelPastRow">
                                            <i class="ri-subtract-line me-1"></i> Hapus Baris
                                        </button>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="th-past-main">Sampling</th>
                            <th colspan="2" class="th-past-main">Serah Terima</th>
                            <th colspan="10" class="th-past-main">Analisis</th>
                            <th rowspan="2" class="th-past-main">Disposisi /<br>Keterangan</th>
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
                    <tbody id="pasteurisasiTbody">
                        <!-- Baris Pasteurisasi Di-render JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- 4. Tabel 2: STORAGE TANK (Integrated Header Bar, 1040px Proportional Grid) -->
            <div class="d-flex justify-content-between align-items-center mb-1 text-muted fs-11">
                <span class="badge bg-light text-secondary border"><i class="ri-arrow-left-right-line me-1"></i> Geser horizontal untuk melihat seluruh kolom</span>
            </div>
            <div class="table-responsive mb-4">
                <table class="table-qc-data" id="tableStorage" style="min-width: 1040px;">
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
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ri-inbox-line text-primary fs-5"></i>
                                            <span class="fw-bold text-primary fs-13" style="letter-spacing: 0.5px;">STORAGE TANK</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 ms-2">
                                            <span class="fw-bold small text-dark">KODE ST :</span>
                                            <input type="text" class="form-control form-control-sm text-center fw-bold text-primary bg-white border" id="meta_kode_st" style="width: 85px; height: 26px; padding: 2px 4px;" placeholder="ST 01" value="{{ $initialData['kode_st'] ?? 'ST 01' }}">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 no-print">
                                        <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 bg-white" id="btnAddStRow">
                                            <i class="ri-add-line me-1"></i> Tambah Baris
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 bg-white" id="btnDelStRow">
                                            <i class="ri-subtract-line me-1"></i> Hapus Baris
                                        </button>
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
                    <tbody id="storageTbody">
                        <!-- Baris Storage Tank Di-render JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- 5. Footer: Catatan & Tanda Tangan Resmi Sesuai Google Sheets -->
            <div class="signature-container mt-3">
                <div class="row g-0">
                    <div class="col-md-4 p-2 border-end">
                        <strong class="d-block mb-1 fs-12 text-dark">Catatan :</strong>
                        <textarea class="form-control form-control-sm border-0 bg-transparent p-0 text-dark" id="doc_catatan" rows="4" placeholder="Ketik catatan khusus proses di sini...">{{ $initialData['catatan'] ?? '' }}</textarea>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-0 text-center h-100">
                            <div class="col-3 sig-col d-flex flex-column justify-content-between">
                                <div class="fw-semibold fs-12">Disampling oleh,</div>
                                <div class="sig-space"></div>
                                <div>
                                    <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="sig_pic_samp_1" placeholder="( ____________ )" value="{{ $initialData['pic_sampling_1'] ?? '' }}">
                                    <small class="text-muted d-block mt-1">Produksi</small>
                                </div>
                            </div>
                            <div class="col-3 sig-col d-flex flex-column justify-content-between">
                                <div class="fw-semibold fs-12">Disampling oleh,</div>
                                <div class="sig-space"></div>
                                <div>
                                    <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="sig_pic_samp_2" placeholder="( ____________ )" value="{{ $initialData['pic_sampling_2'] ?? '' }}">
                                    <small class="text-muted d-block mt-1">Produksi</small>
                                </div>
                            </div>
                            <div class="col-3 sig-col d-flex flex-column justify-content-between">
                                <div class="fw-semibold fs-12">Dianalisis oleh,</div>
                                <div class="sig-space"></div>
                                <div>
                                    <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="sig_pic_analis" placeholder="( ____________ )" value="{{ $initialData['pic_analis'] ?? '' }}">
                                    <small class="text-muted d-block mt-1">QC Analis</small>
                                </div>
                            </div>
                            <div class="col-3 sig-col d-flex flex-column justify-content-between">
                                <div class="fw-semibold fs-12">Dicek oleh,</div>
                                <div class="sig-space"></div>
                                <div>
                                    <input type="text" class="form-control form-control-sm text-center border-0 border-bottom" id="sig_pic_checker" placeholder="( ____________ )" value="{{ $initialData['pic_checker'] ?? '' }}">
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

        // Inisialisasi Tampilan Baris Tabel & Link Aksi
        renderDocumentTables();

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

        // Tarik Data Ulang dari Database
        $('#btnFetchDb').on('click', function() {
            var poId = $('#po_selector').val();
            if (!poId) {
                Swal.fire('Perhatian', 'Pilih nomor PO terlebih dahulu.', 'warning');
                return;
            }
            Swal.fire({
                title: 'Tarik Ulang Data?',
                text: 'Data monitoring terbaru di database akan dimuat ulang ke formulir.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tarik Data',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    loadDataByPo(poId, true);
                }
            });
        });

        // Simpan Dokumen
        $('#btnSaveDoc').on('click', function() {
            saveDocumentData();
        });

        // Validasi tombol Export Excel & Cetak / Print jika PO belum dipilih
        $('#btnPrintView, #btnExportExcel').on('click', function(e) {
            var poId = $('#po_selector').val();
            if (!poId) {
                e.preventDefault();
                Swal.fire('Perhatian', 'Pilih nomor PO terlebih dahulu.', 'warning');
            }
        });

        // Tombol Tambah / Hapus Baris Pasteurisasi
        $('#btnAddPastRow').on('click', function() {
            addPasteurisasiRow();
        });
        $('#btnDelPastRow').on('click', function() {
            deleteLastPasteurisasiRow();
        });

        // Tombol Tambah / Hapus Baris Storage Tank
        $('#btnAddStRow').on('click', function() {
            addStorageRow();
        });
        $('#btnDelStRow').on('click', function() {
            deleteLastStorageRow();
        });

        // 1. Auto-select text on focus
        $(document).on('focus', '.clean-cell-input, .clean-cell-textarea, .meta-input-group input, .signature-container input', function() {
            var el = this;
            setTimeout(function() {
                if (typeof el.select === 'function') {
                    el.select();
                }
            }, 30);
        });

        // 2. Keyboard Navigation (Excel Enter / Shift+Enter / Arrow Up & Down)
        $(document).on('keydown', '.table-qc-data input, .table-qc-data textarea', function(e) {
            var $input = $(this);
            var $td = $input.closest('td');
            var colIdx = $td.index();
            var $tr = $input.closest('tr.data-row');

            if (e.key === 'Enter') {
                if ($input.is('textarea') && e.ctrlKey) return; // Allow Ctrl+Enter for newline
                e.preventDefault();
                if (e.shiftKey) {
                    var $prevTr = $tr.prev('tr.data-row');
                    if ($prevTr.length) {
                        var $target = $prevTr.children('td').eq(colIdx).find('.clean-cell-input, .clean-cell-textarea');
                        if ($target.length) $target.focus();
                    }
                } else {
                    var $nextTr = $tr.next('tr.data-row');
                    if ($nextTr.length) {
                        var $target = $nextTr.children('td').eq(colIdx).find('.clean-cell-input, .clean-cell-textarea');
                        if ($target.length) $target.focus();
                    }
                }
            } else if (e.key === 'ArrowDown') {
                var $nextTr = $tr.next('tr.data-row');
                if ($nextTr.length) {
                    var $target = $nextTr.children('td').eq(colIdx).find('.clean-cell-input, .clean-cell-textarea');
                    if ($target.length) {
                        e.preventDefault();
                        $target.focus();
                    }
                }
            } else if (e.key === 'ArrowUp') {
                var $prevTr = $tr.prev('tr.data-row');
                if ($prevTr.length) {
                    var $target = $prevTr.children('td').eq(colIdx).find('.clean-cell-input, .clean-cell-textarea');
                    if ($target.length) {
                        e.preventDefault();
                        $target.focus();
                    }
                }
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
                if (res.status === 'success') {
                    currentDocData = res.data;
                    updateFormFieldsFromData();
                    renderDocumentTables();
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

    // Update Header & Metadata Inputs
    function updateFormFieldsFromData() {
        if (!currentDocData) return;
        $('#doc_tgl_record').val(currentDocData.tanggal_record_doc || '');
        $('#doc_halaman').val(currentDocData.halaman || '1 / 1');
        $('#meta_jenis_produk').val(currentDocData.jenis_produk || currentDocData.variant || '');
        $('#meta_tanggal_produksi').val(currentDocData.tanggal_produksi || currentDocData.tanggal_record_doc || '');
        $('#meta_kode_shift').val(currentDocData.kode_shift_grup || '');
        $('#meta_jam_start').val(currentDocData.jam_produksi_start || '');
        $('#meta_jam_end').val(currentDocData.jam_produksi_end || '');
        $('#meta_batch').val(currentDocData.batch || currentDocData.batch_range || '');
        $('#meta_kode_st').val(currentDocData.kode_st || 'ST 01');
        $('#doc_catatan').val(currentDocData.catatan || '');
        $('#sig_pic_samp_1').val(currentDocData.pic_sampling_1 || '');
        $('#sig_pic_samp_2').val(currentDocData.pic_sampling_2 || '');
        $('#sig_pic_analis').val(currentDocData.pic_analis || '');
        $('#sig_pic_checker').val(currentDocData.pic_checker || '');
    }

    // Render Tabel Pasteurisasi & Storage Tank
    function renderDocumentTables() {
        if (!currentDocData) return;

        // 1. Render Pasteurisasi (Default min 6 baris)
        var pastRows = currentDocData.pasteurisasi_rows || [];
        var pastHtml = '';
        var pastCount = Math.max(6, pastRows.length);
        for (var i = 0; i < pastCount; i++) {
            var row = pastRows[i] || {};
            var sampVal = (i === 0 && !row.sampling) ? 'Tengah' : (row.sampling || '');
            pastHtml += `
                <tr class="data-row past-tr">
                    <td><input type="text" class="clean-cell-input p-samp" value="${escapeHtml(sampVal)}"></td>
                    <td><input type="text" class="clean-cell-input p-jam" value="${escapeHtml(row.jam || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-pic" value="${escapeHtml(row.pic || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-bj" value="${escapeHtml(row.bj || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-brix" value="${escapeHtml(row.brix || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-ph" value="${escapeHtml(row.ph || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-aw" value="${escapeHtml(row.aw || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-visko" value="${escapeHtml(row.viskositas || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-organo" value="${escapeHtml(row.organo || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-aroma" value="${escapeHtml(row.aroma || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-warna" value="${escapeHtml(row.warna || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-buih" value="${escapeHtml(row.buih || '')}"></td>
                    <td><input type="text" class="clean-cell-input p-endapan" value="${escapeHtml(row.endapan || '')}"></td>
                    <td><textarea class="clean-cell-textarea p-disposisi">${escapeHtml(row.disposisi || '')}</textarea></td>
                </tr>
            `;
        }
        $('#pasteurisasiTbody').html(pastHtml);

        // 2. Render Storage Tank (Default min 5 baris)
        var stRows = currentDocData.storage_rows || [];
        var stHtml = '';
        var stCount = Math.max(5, stRows.length);
        for (var j = 0; j < stCount; j++) {
            var sRow = stRows[j] || {};
            var sampSt = sRow.sampling || '';
            if (!sampSt) {
                if (j === 0) sampSt = 'Awal';
                else if (j === 1) sampSt = 'Outlet';
            }
            stHtml += `
                <tr class="data-row st-tr">
                    <td><input type="text" class="clean-cell-input s-samp" value="${escapeHtml(sampSt)}"></td>
                    <td><input type="text" class="clean-cell-input s-jam" value="${escapeHtml(sRow.jam || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-pic" value="${escapeHtml(sRow.pic || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-bj" value="${escapeHtml(sRow.bj || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-brix" value="${escapeHtml(sRow.brix || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-ph" value="${escapeHtml(sRow.ph || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-nacl" value="${escapeHtml(sRow.nacl || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-visko" value="${escapeHtml(sRow.viskositas || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-organo" value="${escapeHtml(sRow.organo || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-aroma" value="${escapeHtml(sRow.aroma || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-warna" value="${escapeHtml(sRow.warna || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-buih" value="${escapeHtml(sRow.buih || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-endapan" value="${escapeHtml(sRow.endapan || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-kristal" value="${escapeHtml(sRow.kristal || '')}"></td>
                    <td><input type="text" class="clean-cell-input s-aw" value="${escapeHtml(sRow.aw || '')}"></td>
                    <td><textarea class="clean-cell-textarea s-disposisi">${escapeHtml(sRow.disposisi || '')}</textarea></td>
                </tr>
            `;
        }
        $('#storageTbody').html(stHtml);
    }

    // Tambah Baris Pasteurisasi
    function addPasteurisasiRow() {
        var count = $('#pasteurisasiTbody tr').length + 1;
        var trHtml = `
            <tr class="data-row past-tr">
                <td><input type="text" class="clean-cell-input p-samp" value="Tengah ${count}"></td>
                <td><input type="text" class="clean-cell-input p-jam"></td>
                <td><input type="text" class="clean-cell-input p-pic"></td>
                <td><input type="text" class="clean-cell-input p-bj"></td>
                <td><input type="text" class="clean-cell-input p-brix"></td>
                <td><input type="text" class="clean-cell-input p-ph"></td>
                <td><input type="text" class="clean-cell-input p-aw"></td>
                <td><input type="text" class="clean-cell-input p-visko"></td>
                <td><input type="text" class="clean-cell-input p-organo"></td>
                <td><input type="text" class="clean-cell-input p-aroma"></td>
                <td><input type="text" class="clean-cell-input p-warna"></td>
                <td><input type="text" class="clean-cell-input p-buih"></td>
                <td><input type="text" class="clean-cell-input p-endapan"></td>
                <td><textarea class="clean-cell-textarea p-disposisi"></textarea></td>
            </tr>
        `;
        $('#pasteurisasiTbody').append(trHtml);
    }

    // Hapus Baris Terakhir Pasteurisasi
    function deleteLastPasteurisasiRow() {
        var $rows = $('#pasteurisasiTbody tr');
        if ($rows.length > 1) {
            $rows.last().remove();
        } else {
            Swal.fire('Info', 'Minimal harus ada 1 baris Pasteurisasi.', 'info');
        }
    }

    // Tambah Baris Storage Tank
    function addStorageRow() {
        var count = $('#storageTbody tr').length + 1;
        var trHtml = `
            <tr class="data-row st-tr">
                <td><input type="text" class="clean-cell-input s-samp" value="Sampling ${count}"></td>
                <td><input type="text" class="clean-cell-input s-jam"></td>
                <td><input type="text" class="clean-cell-input s-pic"></td>
                <td><input type="text" class="clean-cell-input s-bj"></td>
                <td><input type="text" class="clean-cell-input s-brix"></td>
                <td><input type="text" class="clean-cell-input s-ph"></td>
                <td><input type="text" class="clean-cell-input s-nacl"></td>
                <td><input type="text" class="clean-cell-input s-visko"></td>
                <td><input type="text" class="clean-cell-input s-organo"></td>
                <td><input type="text" class="clean-cell-input s-aroma"></td>
                <td><input type="text" class="clean-cell-input s-warna"></td>
                <td><input type="text" class="clean-cell-input s-buih"></td>
                <td><input type="text" class="clean-cell-input s-endapan"></td>
                <td><input type="text" class="clean-cell-input s-kristal"></td>
                <td><input type="text" class="clean-cell-input s-aw"></td>
                <td><textarea class="clean-cell-textarea s-disposisi"></textarea></td>
            </tr>
        `;
        $('#storageTbody').append(trHtml);
    }

    // Hapus Baris Terakhir Storage Tank
    function deleteLastStorageRow() {
        var $rows = $('#storageTbody tr');
        if ($rows.length > 1) {
            $rows.last().remove();
        } else {
            Swal.fire('Info', 'Minimal harus ada 1 baris Storage Tank.', 'info');
        }
    }

    // Kumpulkan Seluruh Data Form ke Objek
    function collectFormData() {
        var pastRows = [];
        $('#pasteurisasiTbody tr.past-tr').each(function() {
            var $tr = $(this);
            pastRows.push({
                sampling: $tr.find('.p-samp').val() || '',
                jam: $tr.find('.p-jam').val() || '',
                pic: $tr.find('.p-pic').val() || '',
                bj: $tr.find('.p-bj').val() || '',
                brix: $tr.find('.p-brix').val() || '',
                ph: $tr.find('.p-ph').val() || '',
                aw: $tr.find('.p-aw').val() || '',
                viskositas: $tr.find('.p-visko').val() || '',
                organo: $tr.find('.p-organo').val() || '',
                aroma: $tr.find('.p-aroma').val() || '',
                warna: $tr.find('.p-warna').val() || '',
                buih: $tr.find('.p-buih').val() || '',
                endapan: $tr.find('.p-endapan').val() || '',
                disposisi: $tr.find('.p-disposisi').val() || ''
            });
        });

        var stRows = [];
        $('#storageTbody tr.st-tr').each(function() {
            var $tr = $(this);
            stRows.push({
                sampling: $tr.find('.s-samp').val() || '',
                jam: $tr.find('.s-jam').val() || '',
                pic: $tr.find('.s-pic').val() || '',
                bj: $tr.find('.s-bj').val() || '',
                brix: $tr.find('.s-brix').val() || '',
                ph: $tr.find('.s-ph').val() || '',
                nacl: $tr.find('.s-nacl').val() || '',
                viskositas: $tr.find('.s-visko').val() || '',
                organo: $tr.find('.s-organo').val() || '',
                aroma: $tr.find('.s-aroma').val() || '',
                warna: $tr.find('.s-warna').val() || '',
                buih: $tr.find('.s-buih').val() || '',
                endapan: $tr.find('.s-endapan').val() || '',
                kristal: $tr.find('.s-kristal').val() || '',
                aw: $tr.find('.s-aw').val() || '',
                disposisi: $tr.find('.s-disposisi').val() || ''
            });
        });

        var singleBlock = {
            block_index: 0,
            halaman: $('#doc_halaman').val() || '1 / 1',
            batch: $('#meta_batch').val() || '',
            kode_st: $('#meta_kode_st').val() || 'ST 01',
            jenis_produk: $('#meta_jenis_produk').val() || '',
            tanggal_produksi: $('#meta_tanggal_produksi').val() || '',
            jam_produksi_start: $('#meta_jam_start').val() || '',
            jam_produksi_end: $('#meta_jam_end').val() || '',
            kode_shift_grup: $('#meta_kode_shift').val() || '',
            pasteurisasi_rows: pastRows,
            storage_rows: stRows,
            catatan: $('#doc_catatan').val() || '',
            pic_sampling_1: $('#sig_pic_samp_1').val() || '',
            pic_sampling_2: $('#sig_pic_samp_2').val() || '',
            pic_analis: $('#sig_pic_analis').val() || '',
            pic_checker: $('#sig_pic_checker').val() || ''
        };

        return {
            production_batch_id: $('#po_selector').val(),
            tanggal_record_doc: $('#doc_tgl_record').val() || '',
            halaman: $('#doc_halaman').val() || '1 / 1',
            jenis_produk: $('#meta_jenis_produk').val() || '',
            tanggal_produksi: $('#meta_tanggal_produksi').val() || '',
            jam_produksi_start: $('#meta_jam_start').val() || '',
            jam_produksi_end: $('#meta_jam_end').val() || '',
            kode_shift_grup: $('#meta_kode_shift').val() || '',
            batch: $('#meta_batch').val() || '',
            kode_st: $('#meta_kode_st').val() || 'ST 01',
            pasteurisasi_rows: pastRows,
            storage_rows: stRows,
            catatan: $('#doc_catatan').val() || '',
            pic_sampling: $('#sig_pic_samp_1').val() || '',
            pic_serah_terima: $('#sig_pic_samp_2').val() || '',
            pic_analis: $('#sig_pic_analis').val() || '',
            pic_checker: $('#sig_pic_checker').val() || '',
            batch_blocks: [singleBlock],
            doc_code: 'FRM/QLB/04/104/006-01'
        };
    }

    // Simpan ke Database
    function saveDocumentData() {
        var poId = $('#po_selector').val();
        if (!poId) {
            Swal.fire('Perhatian', 'Pilih nomor PO terlebih dahulu.', 'warning');
            return;
        }

        var payload = collectFormData();

        Swal.fire({
            title: 'Menyimpan Dokumen...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: "{{ route('doc-pasteurisasi-storage.store') }}",
            type: 'POST',
            data: payload,
            success: function(res) {
                Swal.close();
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Disimpan!',
                        text: res.message || 'Formulir Dokumen FRM/QLB/04/104/006-01 berhasil disimpan.',
                        timer: 1600,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', res.message || 'Gagal menyimpan dokumen.', 'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                var msg = 'Gagal menyimpan dokumen.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire('Error', msg, 'error');
            }
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
