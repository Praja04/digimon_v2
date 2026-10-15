@extends('layouts.component.main')
@section('title', 'Form Adjustment')

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

    /* Unified Official Document Tables */
    .doc-unified-table {
        width: 100%;
        min-width: 960px;
        border-collapse: collapse;
        font-size: 0.83rem;
        margin-bottom: 0;
        color: #000000;
    }

    .doc-unified-table th, .doc-unified-table td {
        border: 1px solid #000000;
        padding: 6px 8px;
        vertical-align: middle;
    }

    .doc-unified-table th {
        background-color: #f2f2f2;
        font-weight: 700;
        text-align: center;
        color: #000000;
    }

    .sign-space-cell {
        height: 45px;
    }

    .doc-notes-box {
        border: 1px solid #1e293b;
        background: #f8fafc;
        padding: 6px 12px;
        font-size: 0.78rem;
        line-height: 1.35;
        color: #1e293b;
        border-radius: 4px;
    }

    /* Disposisi Theme */
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
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dokumen Form Adjustment (FRM/QLB/04/104/011-00)</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.menu') }}">Menu</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.export.index') }}">Export & Cetak</a></li>
                            <li class="breadcrumb-item active">Formulir Adjustment</li>
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

        <!-- Container Lembar Form Adjustment -->
        <div id="adjustmentSheetsContainer">
            <!-- Di-render otomatis lewat JS per Lembar (1 Lembar = 1 Batch/Pasangan Batch) -->
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    var currentDocData = @json($initialData);
    var defaultBahans = [
        'Gula Kelapa',
        'Gula Tebu',
        'Larutan Garam',
        'Garam Kasar',
        'Air',
        'Garam Halus',
        'Karamel (Jenis)'
    ];

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Inisialisasi awal
        renderAdjustmentSheets(currentDocData ? currentDocData.adjustment_sheets : []);
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
                url: "{{ route('doc-adjustment.fetch') }}",
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
                    Swal.fire('Error', 'Gagal mengambil data form adjustment.', 'error');
                }
            });
        });

        // Global tanggal doc sync
        $('#globalTanggalDoc').on('change', function() {
            var newDate = $(this).val();
            $('.doc-date-display').text(newDate);
        });
    });

    function updateActionLinks(poId) {
        if (!poId) return;
        $('#btnExportExcel').attr('href', "{{ route('doc-adjustment.export') }}?po_id=" + poId);
        $('#btnPrintView').attr('href', "{{ url('/doc-adjustment/print') }}/" + poId);
    }

    function populateDocData(data) {
        $('#globalTanggalDoc').val(data.tanggal_record_doc || '{{ date("Y-m-d") }}');
        renderAdjustmentSheets(data.adjustment_sheets || []);
    }

    function renderAdjustmentSheets(sheets) {
        var container = $('#adjustmentSheetsContainer');
        container.empty();

        if (!sheets || sheets.length === 0) {
            container.html('<div class="text-center py-5 text-muted border border-dashed rounded bg-white mb-4">Belum ada data lembar adjustment untuk PO ini.</div>');
            return;
        }

        var totalSheets = sheets.length;
        var globalDocDate = $('#globalTanggalDoc').val() || '{{ date("Y-m-d") }}';

        sheets.forEach(function(sheet, sIdx) {
            var rawRows = sheet.bahan_rows || [];
            var bMap = {};
            rawRows.forEach(function(r) {
                if (r && r.bahan) bMap[r.bahan] = r;
            });

            // Siapkan 9 baris bahan: 7 standar + 2 baris tambahan sesuai Google Spreadsheet resmi
            var all9Rows = [];
            for (var i = 0; i < defaultBahans.length; i++) {
                var bName = defaultBahans[i];
                var rData = bMap[bName] || rawRows[i] || {};
                all9Rows.push({
                    bahan: bName,
                    isCustom: false,
                    adj1: rData.adj1 || '',
                    adj2: rData.adj2 || '',
                    adj3: rData.adj3 || ''
                });
            }

            var extra1 = rawRows[7] || {};
            var extra2 = rawRows[8] || {};
            all9Rows.push({
                bahan: extra1.bahan || '',
                isCustom: true,
                adj1: extra1.adj1 || '',
                adj2: extra1.adj2 || '',
                adj3: extra1.adj3 || ''
            });
            all9Rows.push({
                bahan: extra2.bahan || '',
                isCustom: true,
                adj1: extra2.adj1 || '',
                adj2: extra2.adj2 || '',
                adj3: extra2.adj3 || ''
            });

            // Disposisi theme styling
            var dispClass = 'disp-release';
            var dispLower = (sheet.disposisi || '').toLowerCase();
            if (dispLower.includes('reject')) {
                dispClass = 'disp-reject';
            } else if (dispLower.includes('adj') || dispLower.includes('adjustment')) {
                dispClass = 'disp-adjustment';
            } else if (dispLower.includes('resampling') || dispLower.includes('hold')) {
                dispClass = 'disp-resampling';
            }

            var sheetHtml = `
            <div class="lembar-sheet-card" data-sheet-index="${sIdx}">
                <!-- Top Header Info Bar -->
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="lembar-header-badge">
                            <i class="ri-file-edit-line"></i> LEMBAR FORM ADJUSTMENT #${sIdx + 1}
                        </span>
                        <span class="badge bg-light text-dark border px-2 py-1 fs-12">
                            Batch / Pasangan: <strong>${escapeHtml(sheet.no_batch || String(sIdx + 1))}</strong>
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 fs-12">
                            1 Lembar untuk 3x Adjustment
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-light text-muted border fs-12">
                            <i class="ri-lock-line me-1"></i> Data Otentik
                        </span>
                    </div>
                </div>

                <!-- Notes Resmi Sesuai Google Spreadsheet (FRM/QLB/04/104/011-00) -->
                <div class="d-flex justify-content-end mb-2">
                    <div class="doc-notes-box text-start">
                        <div class="fw-bold"><i class="ri-information-line me-1 text-primary"></i> Notes :</div>
                        <div>• 1 lembar : 1 batch (Dissolver) atau 1 pasangan batch (Blending)</div>
                        <div>• 1 lembar untuk 3x adjustment</div>
                    </div>
                </div>

                <!-- 1. Header Dokumen Resmi (Format Solid Terpadu) -->
                <table class="doc-unified-table mb-0" style="border-bottom: none;">
                    <tr>
                        <td style="width: 22%; text-align: center; vertical-align: middle; padding: 6px; border-right: 1px solid #000;">
                            <img src="{{ asset('assets/images/logo-bas.png') }}" alt="BAS Logo" style="height: 38px; object-fit: contain;">
                        </td>
                        <td style="width: 50%; text-align: center; vertical-align: middle; padding: 8px; border-right: 1px solid #000;">
                            <div class="fw-bold text-dark mb-0" style="font-size: 1.25rem; letter-spacing: 0.8px;">FORM ADJUSTMENT</div>
                        </td>
                        <td style="width: 28%; padding: 0; vertical-align: middle;">
                            <table class="w-100 h-100" style="border-collapse: collapse; font-size: 0.80rem;">
                                <tr style="border-bottom: 1px solid #000;">
                                    <td class="px-2 py-1 bg-light fw-bold text-start" style="width: 55%; border: none; border-right: 1px solid #000;">Tanggal Record Doc</td>
                                    <td class="px-2 py-1 text-start" style="width: 45%; border: none;">: <span class="doc-date-display">${escapeHtml(globalDocDate)}</span></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1 bg-light fw-bold text-start" style="border: none; border-right: 1px solid #000;">Halaman</td>
                                    <td class="px-2 py-1 text-start fw-semibold" style="border: none;">: ${sIdx + 1} / ${totalSheets}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- 2. Metadata Informasi (3 Baris Rapi Sesuai Spreadsheet) -->
                <table class="doc-unified-table mb-0" style="border-bottom: none;">
                    <tr>
                        <td style="width: 14%; font-weight: 700; background: #fafafa;">Proses</td>
                        <td style="width: 36%;">: <span class="fw-semibold">${escapeHtml(sheet.proses || 'Blending')}</span></td>
                        <td style="width: 15%; font-weight: 700; background: #fafafa;">Jenis Kecap</td>
                        <td style="width: 35%;">: <span>${escapeHtml(sheet.jenis_kecap || '-')}</span></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; background: #fafafa;">No. Batch</td>
                        <td>: <span class="fw-bold text-primary">${escapeHtml(sheet.no_batch || '-')}</span></td>
                        <td style="font-weight: 700; background: #fafafa;">Tanggal Produksi</td>
                        <td>: <span>${escapeHtml(sheet.tanggal_produksi || globalDocDate)}</span></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; background: #fafafa;">Volume Batch</td>
                        <td>: <span>${escapeHtml(sheet.volume_batch || '-')}</span></td>
                        <td style="font-weight: 700; background: #fafafa;">Shift</td>
                        <td>: <span class="badge bg-light text-dark border fs-12">${escapeHtml(sheet.shift || '-')}</span></td>
                    </tr>
                </table>

                <!-- 3. Tabel Utama Bahan, Adjusment, Disposisi & Tanda Tangan (SATU TABEL SOLID TUNGGAL) -->
                <div class="table-responsive">
                    <table class="doc-unified-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 28%; height: 32px;">BAHAN</th>
                                <th style="width: 18%;">ADJUSMENT 1</th>
                                <th style="width: 18%;">ADJUSMENT 2</th>
                                <th style="width: 18%;">ADJUSMENT 3</th>
                                <th style="width: 18%;">DISPOSISI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris 1 s/d 9: 7 Bahan Standar + 2 Baris Tambahan (DISPOSISI Rowspan 11) -->
                            ${all9Rows.map(function(r, idx) {
                                return `
                                <tr>
                                    <td class="text-start ps-3 ${!r.isCustom ? 'fw-bold bg-white' : 'text-muted bg-white'}">
                                        ${escapeHtml(r.bahan || (r.isCustom ? '-' : ''))}
                                    </td>
                                    <td class="text-center fw-semibold">${escapeHtml(r.adj1 || '-')}</td>
                                    <td class="text-center fw-semibold">${escapeHtml(r.adj2 || '-')}</td>
                                    <td class="text-center fw-semibold">${escapeHtml(r.adj3 || '-')}</td>
                                    ${idx === 0 ? `
                                    <td rowspan="11" class="align-middle text-center bg-white p-3">
                                        <div class="fw-bold text-muted fs-11 mb-2">DISPOSISI AKHIR</div>
                                        <div class="fs-14 ${dispClass}">${escapeHtml(sheet.disposisi || 'Release')}</div>
                                    </td>
                                    ` : ''}
                                </tr>
                                `;
                            }).join('')}

                            <!-- Baris 10: Keterangan (dimulai di kolom 1, rowspan 5) & Jam Pemberian Adj (kolom 2, 3, 4) -->
                            <tr>
                                <td rowspan="5" class="align-top text-start p-2 bg-white">
                                    <div class="fw-bold fs-12 mb-1 text-dark">Keterangan :</div>
                                    <div class="p-2 text-muted fs-12 bg-light rounded border text-start" style="min-height: 110px; white-space: pre-line;">${escapeHtml(sheet.keterangan || '-')}</div>
                                </td>
                                <td class="text-center py-2">
                                    <div class="fs-11 text-muted">Jam :</div>
                                    <div class="fw-semibold">${escapeHtml(sheet.adj1_jam || '-')}</div>
                                </td>
                                <td class="text-center py-2">
                                    <div class="fs-11 text-muted">Jam :</div>
                                    <div class="fw-semibold">${escapeHtml(sheet.adj2_jam || '-')}</div>
                                </td>
                                <td class="text-center py-2">
                                    <div class="fs-11 text-muted">Jam :</div>
                                    <div class="fw-semibold">${escapeHtml(sheet.adj3_jam || '-')}</div>
                                </td>
                            </tr>

                            <!-- Baris 11: Status (kolom 2, 3, 4) -->
                            <tr>
                                <td class="text-center py-2">
                                    <div class="fs-11 text-muted mb-1">Status :</div>
                                    <span class="badge bg-light text-dark border fs-11">${escapeHtml(sheet.adj1_status || '-')}</span>
                                </td>
                                <td class="text-center py-2">
                                    <div class="fs-11 text-muted mb-1">Status :</div>
                                    <span class="badge bg-light text-dark border fs-11">${escapeHtml(sheet.adj2_status || '-')}</span>
                                </td>
                                <td class="text-center py-2">
                                    <div class="fs-11 text-muted mb-1">Status :</div>
                                    <span class="badge bg-light text-dark border fs-11">${escapeHtml(sheet.adj3_status || '-')}</span>
                                </td>
                            </tr>

                            <!-- Baris 12: Header Tanda Tangan (Semua 4 Kolom Sejajar Sesuai Spreadsheet) -->
                            <tr>
                                <td class="fw-bold fs-11 text-center bg-light">Tanda Tangan,</td>
                                <td class="fw-bold fs-11 text-center bg-light">Tanda Tangan,</td>
                                <td class="fw-bold fs-11 text-center bg-light">Tanda Tangan,</td>
                                <td class="fw-bold fs-11 text-center bg-light">Tanda Tangan,</td>
                            </tr>

                            <!-- Baris 13: Ruang Tanda Tangan (Semua 4 Kolom Sejajar) -->
                            <tr>
                                <td><div class="sign-space-cell"></div></td>
                                <td><div class="sign-space-cell"></div></td>
                                <td><div class="sign-space-cell"></div></td>
                                <td><div class="sign-space-cell"></div></td>
                            </tr>

                            <!-- Baris 14: Nama Tanda Tangan (Semua 4 Kolom Sejajar) -->
                            <tr>
                                <td class="p-2 text-center">
                                    <div class="fw-bold border-bottom pb-1">${escapeHtml(sheet.adj1_petugas || '-')}</div>
                                    <small class="text-muted d-block mt-1">Petugas Produksi</small>
                                </td>
                                <td class="p-2 text-center">
                                    <div class="fw-bold border-bottom pb-1">${escapeHtml(sheet.adj2_petugas || '-')}</div>
                                    <small class="text-muted d-block mt-1">Petugas Produksi</small>
                                </td>
                                <td class="p-2 text-center">
                                    <div class="fw-bold border-bottom pb-1">${escapeHtml(sheet.adj3_petugas || '-')}</div>
                                    <small class="text-muted d-block mt-1">Petugas Produksi</small>
                                </td>
                                <td class="p-2 text-center">
                                    <div class="fw-bold border-bottom pb-1">${escapeHtml(sheet.qc_analis || '-')}</div>
                                    <small class="text-muted d-block mt-1">Analis QC</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 4. Kode Dokumen Resmi -->
                <div class="text-end mt-2 pe-1">
                    <span class="fs-11 fw-semibold text-muted fst-italic">FRM/QLB/04/104/011-00</span>
                </div>
            </div>
            `;
            container.append(sheetHtml);
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
