@extends('layouts.component.main')
@section('title', 'Analisis Kimia - Shelf Life')
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
                                <li class="breadcrumb-item"><a href="{{ route('shelf-life.index') }}">Menu</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle-outline me-2"></i>
                            {!! session('error') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Daftar @yield('title')</h5>
                        </div>
                        <div class="card-body">
                            <!-- Filter Section -->
                            <div class="row mb-3 g-2">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <label for="kelompok_sample" class="form-label">Kelompok Sample</label>
                                    <select id="kelompok_sample" class="form-select select2">
                                        <option value="">Pilih Kelompok Sample</option>
                                        <option value="Retail">Retail</option>
                                        <option value="Non Retail">Non Retail</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <label for="tanggal_analisa" class="form-label">Tanggal Analisa</label>
                                    <input type="date" id="tanggal_analisa" class="form-control" disabled>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 d-flex align-items-end">
                                    <button type="button" id="btnReset" class="btn btn-secondary w-100">
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
                                            <th>Kelompok Sample</th>
                                            <th>Kelompok Tanggal</th>
                                            <th>Varian FG</th>
                                            <th>Bulan Ke-</th>
                                            <th>Ruang SL</th>
                                            <th>Bin</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Analisa</th>
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

            $('.select2').select2({
                placeholder: '-- Pilih Opsi --'
            });

            var today = new Date().toISOString().split('T')[0];
            $('#tanggal_analisa').attr('max', today);

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: "{{ route('shelf-life.analysis-kimia.index') }}",
                    data: function(d) {
                        d.kelompok_sample = $('#kelompok_sample').val();
                        d.tanggal_analisa = $('#tanggal_analisa').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kelompok_sample',
                        name: 'kelompok_sample'
                    },
                    {
                        data: 'kelompok_tanggal',
                        name: 'kelompok_tanggal'
                    },
                    {
                        data: 'variant_fg',
                        name: 'variant_fg'
                    },
                    {
                        data: 'bulan_ke',
                        name: 'bulan_ke'
                    },
                    {
                        data: 'ruang_sl',
                        name: 'ruang_sl'
                    },
                    {
                        data: 'bin_location',
                        name: 'bin_location'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [4, 'asc']
                ],
                language: {
                    emptyTable: "Data tidak ditemukan.",
                    processing: '<i class="mdi mdi-loading mdi-spin me-2"></i> Memuat data...',
                    zeroRecords: "Tidak ada data yang sesuai dengan filter"
                }
            });

            $('#kelompok_sample').on('change', function() {
                var kelompokSample = $(this).val();

                if (kelompokSample) {
                    $('#tanggal_analisa').prop('disabled', false);
                } else {
                    $('#tanggal_analisa').prop('disabled', true).val('');
                    table.ajax.reload();
                }
            });

            $('#tanggal_analisa').on('change', function() {
                var kelompokSample = $('#kelompok_sample').val();
                var tanggalAnalisa = $(this).val();

                if (kelompokSample && tanggalAnalisa) {
                    var selectedDate = new Date(tanggalAnalisa);
                    var todayDate = new Date(today);

                    if (selectedDate > todayDate) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Tanggal analisa tidak boleh melebihi hari ini',
                        });
                        $(this).val('');
                        return;
                    }

                    table.ajax.reload();
                }
            });

            $('#btnReset').on('click', function() {
                $('#kelompok_sample').val('').trigger('change');
                $('#tanggal_analisa').val('').prop('disabled', true);
                table.ajax.reload();
            });
        });
    </script>
@endsection
