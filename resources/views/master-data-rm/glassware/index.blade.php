@extends('layouts.component.main')

@section('title')
    Daftar Glassware RM
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Glassware RM</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Master Data RM</a>
                            </li>
                            <li class="breadcrumb-item active">Glassware</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h4 class="card-title mb-1">Daftar Glassware</h4>
                            <p class="text-muted mb-0">Kelola master data Glassware laboratorium (Beaker, Cawan, dsb.).</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div style="min-width: 180px;">
                                <select class="form-select form-select-sm" id="filterJenis">
                                    <option value="">-- Semua Jenis --</option>
                                    @foreach ($jenisGlasswareOptions as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary" id="btnAdd">
                                <i class="mdi mdi-plus-circle-outline me-1"></i>
                                Tambah Data
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">No.</th>
                                        <th>Jenis Glassware</th>
                                        <th>Nomor Glassware</th>
                                        <th>Berat Glassware (Tare)</th>
                                        <th style="width: 130px;">Status</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL FORM --}}
        <div class="modal fade" id="modalGlassware" tabindex="-1" aria-labelledby="modalGlasswareLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalGlasswareLabel">Tambah Glassware</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formGlassware">
                        <input type="hidden" name="id" id="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="jenis_glassware" class="form-label">
                                    Jenis Glassware <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="jenis_glassware" name="jenis_glassware" list="jenisGlasswareList" placeholder="Contoh: BEAKER 250G, BEAKER 500G, CAWAN" required>
                                <datalist id="jenisGlasswareList">
                                    <option value="BEAKER 250G">
                                    <option value="BEAKER 500G">
                                    <option value="CAWAN">
                                </datalist>
                                <div class="invalid-feedback" id="errorJenisGlassware"></div>
                            </div>

                            <div class="mb-3">
                                <label for="nomor_glassware" class="form-label">
                                    Nomor Glassware <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nomor_glassware" name="nomor_glassware" placeholder="Contoh: 1, 2, 3..." required>
                                <div class="invalid-feedback" id="errorNomorGlassware"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-7 mb-3">
                                    <label for="berat_glassware" class="form-label">
                                        Berat Glassware (Tare)
                                    </label>
                                    <input type="number" step="0.0001" min="0" class="form-control" id="berat_glassware" name="berat_glassware" placeholder="Contoh: 125.4500">
                                    <div class="invalid-feedback" id="errorBeratGlassware"></div>
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label for="uom_berat" class="form-label">
                                        UoM Berat <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="uom_berat" name="uom_berat" value="g" required>
                                    <div class="invalid-feedback" id="errorUomBerat"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusAktif" value="1" checked>
                                    <label class="form-check-label text-success fw-medium" for="statusAktif">
                                        Aktif
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusNonAktif" value="0">
                                    <label class="form-check-label text-danger fw-medium" for="statusNonAktif">
                                        Tidak Aktif
                                    </label>
                                </div>
                                <div class="text-danger small mt-1" id="errorStatus"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const modalElement = document.getElementById('modalGlassware');
        const modalGlassware = new bootstrap.Modal(modalElement);

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('master-glassware.index') }}",
                type: 'GET',
                data: function (d) {
                    d.filter_jenis = $('#filterJenis').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenis_glassware', name: 'jenis_glassware' },
                { data: 'nomor_glassware', name: 'nomor_glassware' },
                { data: 'berat_glassware', name: 'berat_glassware' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Data Glassware RM belum tersedia',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        $('#filterJenis').on('change', function () {
            table.ajax.reload();
        });

        function clearValidation() {
            $('#jenis_glassware').removeClass('is-invalid');
            $('#nomor_glassware').removeClass('is-invalid');
            $('#berat_glassware').removeClass('is-invalid');
            $('#uom_berat').removeClass('is-invalid');
            $('#errorJenisGlassware').html('');
            $('#errorNomorGlassware').html('');
            $('#errorBeratGlassware').html('');
            $('#errorUomBerat').html('');
            $('#errorStatus').html('');
        }

        function resetForm() {
            $('#formGlassware')[0].reset();
            $('#id').val('');
            $('#uom_berat').val('g');
            $('#statusAktif').prop('checked', true);
            clearValidation();
        }

        $('#btnAdd').on('click', function () {
            resetForm();
            $('#modalGlasswareLabel').text('Tambah Glassware');
            modalGlassware.show();
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');
            resetForm();

            $.ajax({
                url: "{{ url('/master-data-rm/glassware') }}/" + id + "/edit",
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Memuat...',
                        text: 'Mengambil data glassware RM',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.status) {
                        $('#id').val(response.data.id);
                        $('#jenis_glassware').val(response.data.jenis_glassware);
                        $('#nomor_glassware').val(response.data.nomor_glassware);
                        $('#berat_glassware').val(response.data.berat_glassware);
                        $('#uom_berat').val(response.data.uom_berat || 'g');

                        if (response.data.status) {
                            $('#statusAktif').prop('checked', true);
                        } else {
                            $('#statusNonAktif').prop('checked', true);
                        }
                        $('#modalGlasswareLabel').text('Edit Glassware');
                        modalGlassware.show();
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('Error', 'Gagal mengambil data glassware RM.', 'error');
                }
            });
        });

        $('#formGlassware').on('submit', function (e) {
            e.preventDefault();
            clearValidation();

            const formData = $(this).serialize();
            const btnSubmit = $('#btnSubmit');
            const originalBtnText = btnSubmit.html();

            btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: "{{ route('master-glassware.store') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    btnSubmit.prop('disabled', false).html(originalBtnText);
                    if (response.status) {
                        modalGlassware.hide();
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    btnSubmit.prop('disabled', false).html(originalBtnText);
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.jenis_glassware) {
                            $('#jenis_glassware').addClass('is-invalid');
                            $('#errorJenisGlassware').text(errors.jenis_glassware[0]);
                        }
                        if (errors.nomor_glassware) {
                            $('#nomor_glassware').addClass('is-invalid');
                            $('#errorNomorGlassware').text(errors.nomor_glassware[0]);
                        }
                        if (errors.berat_glassware) {
                            $('#berat_glassware').addClass('is-invalid');
                            $('#errorBeratGlassware').text(errors.berat_glassware[0]);
                        }
                        if (errors.uom_berat) {
                            $('#uom_berat').addClass('is-invalid');
                            $('#errorUomBerat').text(errors.uom_berat[0]);
                        }
                        if (errors.status) {
                            $('#errorStatus').text(errors.status[0]);
                        }
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    }
                }
            });
        });

        $(document).on('click', '.btnDelete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus glassware <strong>${nama}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/master-data-rm/glassware') }}/" + id,
                        type: 'DELETE',
                        dataType: 'json',
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Menghapus...',
                                text: 'Mohon tunggu',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });
                        },
                        success: function (response) {
                            Swal.close();
                            if (response.status) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function () {
                            Swal.close();
                            Swal.fire('Error', 'Gagal menghapus data.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
