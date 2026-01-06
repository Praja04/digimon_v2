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
                                    <label for="kelompok_tanggal" class="form-label">Kelompok Tanggal</label>
                                    <select id="kelompok_tanggal" class="form-select select2" disabled>
                                        <option value="">Pilih Kelompok Tanggal</option>
                                    </select>
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

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('shelf-life.checksheet.index') }}",
                    data: function(d) {
                        d.kelompok_sample = $('#kelompok_sample').val();
                        d.kelompok_tanggal = $('#kelompok_tanggal').val();
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

            // Event ketika kelompok sample berubah
            $('#kelompok_sample').on('change', function() {
                var kelompokSample = $(this).val();
                var kelompokTanggalSelect = $('#kelompok_tanggal');

                // Reset kelompok tanggal
                kelompokTanggalSelect.val('').trigger('change');
                kelompokTanggalSelect.html('<option value="">Pilih Kelompok Tanggal</option>');

                if (kelompokSample) {
                    // Disable dropdown sementara
                    kelompokTanggalSelect.prop('disabled', true);

                    $.ajax({
                        url: "{{ route('shelf-life.checksheet.index') }}",
                        type: 'GET',
                        data: {
                            get_kelompok_tanggal: true,
                            kelompok_sample: kelompokSample
                        },
                        success: function(response) {
                            if (response.length > 0) {
                                $.each(response, function(index, value) {
                                    kelompokTanggalSelect.append(
                                        $('<option></option>').val(value).text(
                                            value)
                                    );
                                });
                                kelompokTanggalSelect.prop('disabled', false);
                            } else {
                                kelompokTanggalSelect.append(
                                    '<option value="">Tidak ada data</option>'
                                );
                                kelompokTanggalSelect.prop('disabled', true);
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal memuat kelompok tanggal',
                            });
                            kelompokTanggalSelect.prop('disabled', true);
                        }
                    });
                } else {
                    kelompokTanggalSelect.prop('disabled', true);
                }
            });

            // Event ketika kelompok tanggal berubah
            $('#kelompok_tanggal').on('change', function() {
                var kelompokSample = $('#kelompok_sample').val();
                var kelompokTanggal = $(this).val();

                if (kelompokSample && kelompokTanggal) {
                    table.ajax.reload();
                }
            });

            // Reset button
            $('#btnReset').on('click', function() {
                $('#kelompok_sample').val('').trigger('change');
                $('#kelompok_tanggal').val('').trigger('change');
                $('#kelompok_tanggal').html('<option value="">Pilih Kelompok Tanggal</option>');
                $('#kelompok_tanggal').prop('disabled', true);
                table.ajax.reload();
            });

            // Update checkbox status
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
                        });
                    },
                    error: function(xhr) {
                        checkbox.prop('disabled', false);
                        checkbox.prop('checked', isChecked === 0);

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
