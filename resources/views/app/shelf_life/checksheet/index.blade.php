@extends('layouts.component.main')
@section('title', 'Checksheet - Shelf Life')
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
                                            <th>Tanggal Produksi</th>
                                            <th>Nomor PO</th>
                                            <th>Varian FG</th>
                                            <th>Kelompok Sample</th>
                                            <th>Ruang SL</th>
                                            <th>Bin</th>
                                            <th>Bulan Ke-</th>
                                            <th>Tanggal Analisa</th>
                                            <th class="text-center">Check</th>
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

            // Set max date to today
            var today = new Date().toISOString().split('T')[0];
            $('#tanggal_analisa').attr('max', today);

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: "{{ route('shelf-life.checksheet.index') }}",
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
                        data: 'tanggal_produksi_formatted',
                        name: 'tanggal_produksi',
                        render: function(data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'nomor_po',
                        name: 'nomor_po'
                    },
                    {
                        data: 'variant_fg',
                        name: 'variant_fg'
                    },
                    {
                        data: 'kelompok_sample',
                        name: 'kelompok_sample'
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
                        data: 'bulan_ke',
                        name: 'bulan_ke'
                    },
                    {
                        data: 'tanggal_analisa_formatted',
                        name: 'tanggal_analisa',
                        render: function(data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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

            $(document).on('change', '.checksheet-checkbox', function() {
                var checkbox = $(this);
                var id = checkbox.data('id');
                var isChecked = checkbox.is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('shelf-life.checksheet.update-status') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        is_checked: isChecked
                    },
                    beforeSend: function() {
                        checkbox.prop('disabled', true);
                    },
                    success: function(response) {
                        checkbox.prop('disabled', false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        checkbox.prop('disabled', false);
                        checkbox.prop('checked', !isChecked);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengupdate status. Silakan coba lagi.',
                        });
                    }
                });
            });
        });
    </script>
@endsection
