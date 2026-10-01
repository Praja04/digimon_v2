@extends('layouts.component.main')
@section('title', 'Dokumen Analisis Blending (Export & Cetak)')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.menu') }}">Menu</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h5 class="card-title mb-0">Daftar Dokumen Analisis Blending</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('doc-blending-awal.index') }}" class="btn btn-primary btn-sm">
                                    <i class="ri-file-text-line me-1"></i> Form Hasil Analisis Blending
                                </a>
                                <a href="{{ route('doc-adjustment.index') }}" class="btn btn-warning btn-sm text-white">
                                    <i class="ri-file-settings-line me-1"></i> Form Adjustment
                                </a>
                                <button type="button" id="btnExportBlending" class="btn btn-success btn-sm">
                                    <i class="ri-file-excel-2-line me-1"></i> Export Blending
                                </button>
                                <button type="button" id="btnExportAdjust" class="btn btn-soft-warning btn-sm">
                                    <i class="ri-file-excel-line me-1"></i> Export Rekap Adjust
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filter Section -->
                            <div class="row mb-3 g-2">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="start_date" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" id="end_date" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label for="status_filter" class="form-label">Status</label>
                                    <select id="status_filter" class="form-select">
                                        <option value="">Semua</option>
                                        <option value="complete">Selesai Analisa</option>
                                        <option value="progress">On Progress</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 d-flex align-items-end gap-2">
                                    <button type="button" id="btnFilter" class="btn btn-primary flex-fill">
                                        <i class="mdi mdi-filter"></i> Filter
                                    </button>
                                    <button type="button" id="btnReset" class="btn btn-secondary flex-fill">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <!-- End Filter Section -->

                            <div class="table-responsive">
                                <table id="datatable" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>PO</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah Blending</th>
                                            <th>Status</th>
                                            <th style="min-width: 260px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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

            // Inisialisasi DataTable
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('analisa.blending-awal.export.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status_filter').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'po_number',
                        name: 'po_number'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'blending_count',
                        name: 'blending_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Tombol Filter
            $('#btnFilter').click(function() {
                table.ajax.reload();
            });

            // Tombol Reset
            $('#btnReset').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#status_filter').val('');
                table.ajax.reload();
            });

            // Filter otomatis saat tekan Enter pada input tanggal
            $('#start_date, #end_date').on('keypress', function(e) {
                if (e.which == 13) {
                    table.ajax.reload();
                }
            });

            // Filter otomatis saat dropdown status berubah
            $('#status_filter').on('change', function() {
                table.ajax.reload();
            });

            // Tombol Export Blending ke Excel dengan filter aktif
            $('#btnExportBlending').click(function() {
                var params = $.param({
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    status: $('#status_filter').val()
                });

                var exportUrl = "{{ route('analisa.blending-awal.export.download') }}?" + params;
                window.location.href = exportUrl;
            });

            // Tombol Export Adjust Blending ke Excel dengan filter aktif
            $('#btnExportAdjust').click(function() {
                var params = $.param({
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    status: $('#status_filter').val()
                });

                var exportUrl = "{{ route('analisa.blending-awal.export.adjust') }}?" + params;
                window.location.href = exportUrl;
            });
        });
    </script>
@endsection
