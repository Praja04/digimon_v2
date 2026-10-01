@extends('layouts.component.main')
@section('title', 'Form Adjustment (FRM/QLB/04/104/011-00)')

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
        padding: 6px 6px;
        text-align: center;
        vertical-align: middle;
    }

    .doc-table th.th-main {
        background-color: #f2f2f2;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.9rem;
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

    .doc-signature-box {
        border: 1px solid #cbd5e0;
        border-radius: 6px;
        padding: 10px;
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
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.menu') }}">Menu</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.export.index') }}">Dokumen Blending</a></li>
                            <li class="breadcrumb-item active">FRM/QLB/04/104/011-00</li>
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
                                <a href="{{ route('analisa.blending-awal.export.index') }}" class="btn btn-soft-secondary d-flex align-items-center">
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
                                    <h4 class="doc-header-title mb-0">FORM ADJUSTMENT</h4>
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
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <table class="w-100 doc-meta-table">
                                <tr>
                                    <td width="35%" class="fw-bold">Proses</td>
                                    <td width="5%">:</td>
                                    <td>
                                        <input type="text" id="proses" class="form-control form-control-sm bg-white" placeholder="Blending" value="{{ $initialData['proses'] ?? 'Blending' }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">No. Batch</td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" id="no_batch" class="form-control form-control-sm bg-white" placeholder="Contoh: 1 / 01 s/d 05" value="{{ $initialData['no_batch'] ?? ($initialData['batch_range'] ?? '') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Volume Batch</td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" id="volume_batch" class="form-control form-control-sm bg-white" placeholder="Contoh: 5000 L" value="{{ $initialData['volume_batch'] ?? '' }}">
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <table class="w-100 doc-meta-table">
                                <tr>
                                    <td width="35%" class="fw-bold">Jenis Kecap</td>
                                    <td width="5%">:</td>
                                    <td>
                                        <input type="text" id="jenis_kecap" class="form-control form-control-sm bg-white" placeholder="Nama Produk / Varian" value="{{ $initialData['jenis_kecap'] ?? ($initialData['variant'] ?? '') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Produksi</td>
                                    <td>:</td>
                                    <td>
                                        <input type="date" id="tanggal_produksi" class="form-control form-control-sm bg-white" value="{{ $initialData['tanggal_produksi'] ?? date('Y-m-d') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Shift</td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" id="shift" class="form-control form-control-sm bg-white" placeholder="1 / Shift 1" value="{{ $initialData['shift'] ?? '1' }}">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- TABEL UTAMA: BAHAN & ADJUSTMENT -->
                    <div class="table-responsive mb-3">
                        <table id="tableAdjustment" class="doc-table">
                            <thead>
                                <tr>
                                    <th class="th-main" style="width: 25%;">BAHAN</th>
                                    <th class="th-main" style="width: 18%;">ADJUSMENT 1</th>
                                    <th class="th-main" style="width: 18%;">ADJUSMENT 2</th>
                                    <th class="th-main" style="width: 18%;">ADJUSMENT 3</th>
                                    <th class="th-main" style="width: 21%;">DISPOSISI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $bahanList = !empty($initialData['bahan_rows']) ? $initialData['bahan_rows'] : [
                                        ['bahan' => 'Gula Kelapa', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                        ['bahan' => 'Gula Tebu', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                        ['bahan' => 'Larutan Garam', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                        ['bahan' => 'Garam Kasar', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                        ['bahan' => 'Air', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                        ['bahan' => 'Garam Halus', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                        ['bahan' => 'Karamel (Jenis)', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                        ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                        ['bahan' => '', 'adj1' => '', 'adj2' => '', 'adj3' => ''],
                                    ];
                                    $totalRows = count($bahanList);
                                @endphp

                                @foreach($bahanList as $idx => $b)
                                <tr data-row-index="{{ $idx }}">
                                    <td class="text-start">
                                        <input type="text" class="table-input text-start fw-semibold row-bahan" value="{{ $b['bahan'] ?? '' }}" placeholder="Nama Bahan...">
                                    </td>
                                    <td><input type="text" class="table-input row-adj1" value="{{ $b['adj1'] ?? '' }}" placeholder="-"></td>
                                    <td><input type="text" class="table-input row-adj2" value="{{ $b['adj2'] ?? '' }}" placeholder="-"></td>
                                    <td><input type="text" class="table-input row-adj3" value="{{ $b['adj3'] ?? '' }}" placeholder="-"></td>
                                    
                                    @if($idx === 0)
                                    <td rowspan="{{ $totalRows }}" class="align-middle p-2" id="disposisiTd">
                                        <textarea id="disposisi" class="form-control form-control-sm bg-white text-center fw-bold text-success h-100" style="min-height: 280px; resize: none;" placeholder="Ketik Disposisi / Status Akhir di sini...">{{ $initialData['disposisi'] ?? 'Release' }}</textarea>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- KETERANGAN & TANDA TANGAN SECTION (Sesuai Form Resmi) -->
                    <div class="row g-2 mb-3">
                        <!-- Keterangan Box on the Left -->
                        <div class="col-12 col-md-3">
                            <div class="p-2 border rounded bg-light h-100">
                                <label class="fw-bold small mb-1">Keterangan :</label>
                                <textarea id="keterangan" class="form-control form-control-sm bg-white" style="height: calc(100% - 25px); min-height: 160px; resize: none;" placeholder="Ketik catatan keterangan proses di sini...">{{ $initialData['keterangan'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- 3 Adjustment Blocks + 1 QC Box on the Right -->
                        <div class="col-12 col-md-9">
                            <div class="row g-2">
                                <!-- Adjustment 1 Signature Block -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="doc-signature-box">
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <span class="small fw-bold">Jam:</span>
                                            <input type="text" id="adj1_jam" class="form-control form-control-sm text-center bg-white" placeholder="00:00" value="{{ $initialData['adj1_jam'] ?? '' }}">
                                        </div>
                                        <div class="d-flex align-items-center gap-1 mb-2">
                                            <span class="small fw-bold text-nowrap">Status:</span>
                                            <select id="adj1_status" class="form-select form-select-sm bg-white">
                                                <option value="Sudah dilakukan" {{ ($initialData['adj1_status'] ?? '') === 'Sudah dilakukan' ? 'selected' : '' }}>Sudah dilakukan</option>
                                                <option value="Belum dilakukan" {{ ($initialData['adj1_status'] ?? '') === 'Belum dilakukan' ? 'selected' : '' }}>Belum dilakukan</option>
                                            </select>
                                        </div>
                                        <div class="fw-bold small mb-3">Tanda Tangan,</div>
                                        <div style="height: 35px;"></div>
                                        <input type="text" id="adj1_petugas" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( Petugas Produksi )" value="{{ $initialData['adj1_petugas'] ?? '' }}">
                                        <div class="fw-bold text-muted small">Petugas Produksi</div>
                                    </div>
                                </div>

                                <!-- Adjustment 2 Signature Block -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="doc-signature-box">
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <span class="small fw-bold">Jam:</span>
                                            <input type="text" id="adj2_jam" class="form-control form-control-sm text-center bg-white" placeholder="00:00" value="{{ $initialData['adj2_jam'] ?? '' }}">
                                        </div>
                                        <div class="d-flex align-items-center gap-1 mb-2">
                                            <span class="small fw-bold text-nowrap">Status:</span>
                                            <select id="adj2_status" class="form-select form-select-sm bg-white">
                                                <option value="Sudah dilakukan" {{ ($initialData['adj2_status'] ?? '') === 'Sudah dilakukan' ? 'selected' : '' }}>Sudah dilakukan</option>
                                                <option value="Belum dilakukan" {{ ($initialData['adj2_status'] ?? '') === 'Belum dilakukan' ? 'selected' : '' }}>Belum dilakukan</option>
                                            </select>
                                        </div>
                                        <div class="fw-bold small mb-3">Tanda Tangan,</div>
                                        <div style="height: 35px;"></div>
                                        <input type="text" id="adj2_petugas" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( Petugas Produksi )" value="{{ $initialData['adj2_petugas'] ?? '' }}">
                                        <div class="fw-bold text-muted small">Petugas Produksi</div>
                                    </div>
                                </div>

                                <!-- Adjustment 3 Signature Block -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="doc-signature-box">
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <span class="small fw-bold">Jam:</span>
                                            <input type="text" id="adj3_jam" class="form-control form-control-sm text-center bg-white" placeholder="00:00" value="{{ $initialData['adj3_jam'] ?? '' }}">
                                        </div>
                                        <div class="d-flex align-items-center gap-1 mb-2">
                                            <span class="small fw-bold text-nowrap">Status:</span>
                                            <select id="adj3_status" class="form-select form-select-sm bg-white">
                                                <option value="Sudah dilakukan" {{ ($initialData['adj3_status'] ?? '') === 'Sudah dilakukan' ? 'selected' : '' }}>Sudah dilakukan</option>
                                                <option value="Belum dilakukan" {{ ($initialData['adj3_status'] ?? '') === 'Belum dilakukan' ? 'selected' : '' }}>Belum dilakukan</option>
                                            </select>
                                        </div>
                                        <div class="fw-bold small mb-3">Tanda Tangan,</div>
                                        <div style="height: 35px;"></div>
                                        <input type="text" id="adj3_petugas" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( Petugas Produksi )" value="{{ $initialData['adj3_petugas'] ?? '' }}">
                                        <div class="fw-bold text-muted small">Petugas Produksi</div>
                                    </div>
                                </div>

                                <!-- QC Analis Signature Block -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="doc-signature-box d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="fw-bold small mb-4 pt-1">Tanda Tangan,</div>
                                        </div>
                                        <div>
                                            <div style="height: 55px;"></div>
                                            <input type="text" id="qc_analis" class="form-control form-control-sm text-center border-0 border-bottom rounded-0 bg-transparent mb-1" placeholder="( Analis QC )" value="{{ $initialData['qc_analis'] ?? (auth()->user()->name ?? '') }}">
                                            <div class="fw-bold text-muted small">Analis QC</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Code Footer -->
                    <div class="text-end pt-2">
                        <span class="badge bg-light text-dark border">FRM/QLB/04/104/011-00</span>
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

        // Fungsi Load Data berdasarkan PO
        function loadDataByPo(poId) {
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
                url: "{{ route('doc-adjustment.fetch') }}",
                type: "GET",
                data: { po_id: poId },
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success' && response.data) {
                        var doc = response.data;
                        
                        // Populate Meta
                        $('#tanggal_record_doc').val(doc.tanggal_record_doc || '');
                        $('#halaman').val(doc.halaman || '1');
                        $('#proses').val(doc.proses || 'Blending');
                        $('#jenis_kecap').val(doc.jenis_kecap || doc.variant || '');
                        $('#no_batch').val(doc.no_batch || '');
                        $('#tanggal_produksi').val(doc.tanggal_produksi || '');
                        $('#volume_batch').val(doc.volume_batch || '');
                        $('#shift').val(doc.shift || '1');

                        // Populate Disposisi & Keterangan
                        $('#disposisi').val(doc.disposisi || '');
                        $('#keterangan').val(doc.keterangan || '');

                        // Populate Signatures & Adjustment meta
                        $('#adj1_jam').val(doc.adj1_jam || '');
                        $('#adj1_status').val(doc.adj1_status || 'Sudah dilakukan');
                        $('#adj1_petugas').val(doc.adj1_petugas || '');

                        $('#adj2_jam').val(doc.adj2_jam || '');
                        $('#adj2_status').val(doc.adj2_status || 'Sudah dilakukan');
                        $('#adj2_petugas').val(doc.adj2_petugas || '');

                        $('#adj3_jam').val(doc.adj3_jam || '');
                        $('#adj3_status').val(doc.adj3_status || 'Sudah dilakukan');
                        $('#adj3_petugas').val(doc.adj3_petugas || '');

                        $('#qc_analis').val(doc.qc_analis || '');

                        // Populate Bahan Rows
                        if (doc.bahan_rows && doc.bahan_rows.length > 0) {
                            $('#tableAdjustment tbody tr').each(function(i) {
                                if (doc.bahan_rows[i]) {
                                    $(this).find('.row-bahan').val(doc.bahan_rows[i].bahan || '');
                                    $(this).find('.row-adj1').val(doc.bahan_rows[i].adj1 || '');
                                    $(this).find('.row-adj2').val(doc.bahan_rows[i].adj2 || '');
                                    $(this).find('.row-adj3').val(doc.bahan_rows[i].adj3 || '');
                                }
                            });
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
            loadDataByPo(poId);
        });

        // Event Simpan Dokumen
        $('#btnSaveDoc').on('click', function() {
            var poId = $('#po_selector').val();
            if (!poId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih PO Terlebih Dahulu',
                    text: 'Silakan pilih nomor PO sebelum menyimpan dokumen.'
                });
                return;
            }

            var bahanRows = [];
            $('#tableAdjustment tbody tr').each(function() {
                var bName = $(this).find('.row-bahan').val();
                var a1 = $(this).find('.row-adj1').val();
                var a2 = $(this).find('.row-adj2').val();
                var a3 = $(this).find('.row-adj3').val();
                bahanRows.push({
                    bahan: bName,
                    adj1: a1,
                    adj2: a2,
                    adj3: a3
                });
            });

            var payload = {
                production_batch_id: poId,
                tanggal_record_doc: $('#tanggal_record_doc').val(),
                halaman: $('#halaman').val(),
                proses: $('#proses').val(),
                jenis_kecap: $('#jenis_kecap').val(),
                no_batch: $('#no_batch').val(),
                tanggal_produksi: $('#tanggal_produksi').val(),
                volume_batch: $('#volume_batch').val(),
                shift: $('#shift').val(),
                bahan_rows: bahanRows,
                disposisi: $('#disposisi').val(),
                keterangan: $('#keterangan').val(),
                adj1_jam: $('#adj1_jam').val(),
                adj1_status: $('#adj1_status').val(),
                adj1_petugas: $('#adj1_petugas').val(),
                adj2_jam: $('#adj2_jam').val(),
                adj2_status: $('#adj2_status').val(),
                adj2_petugas: $('#adj2_petugas').val(),
                adj3_jam: $('#adj3_jam').val(),
                adj3_status: $('#adj3_status').val(),
                adj3_petugas: $('#adj3_petugas').val(),
                qc_analis: $('#qc_analis').val(),
                doc_code: 'FRM/QLB/04/104/011-00'
            };

            Swal.fire({
                title: 'Menyimpan Dokumen...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('doc-adjustment.store') }}",
                type: "POST",
                data: payload,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Formulir Form Adjustment berhasil disimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.'
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
                    text: 'Silakan pilih nomor PO untuk mengunduh Excel.'
                });
                return;
            }
            window.location.href = "{{ route('doc-adjustment.export') }}?po_id=" + poId;
        });

        // Event Cetak / Print PDF
        $('#btnPrint').on('click', function() {
            var poId = $('#po_selector').val();
            if (!poId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih PO Terlebih Dahulu',
                    text: 'Silakan pilih nomor PO sebelum mencetak dokumen.'
                });
                return;
            }
            window.open("{{ url('doc-adjustment/print') }}/" + poId, '_blank');
        });
    });
</script>
@endsection
