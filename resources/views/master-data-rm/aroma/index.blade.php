@extends('layouts.component.main')

@section('title')
    Master Aroma RM
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Aroma Bahan Baku (RM)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Master Data RM</a>
                            </li>
                            <li class="breadcrumb-item active">Aroma</li>
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
                            <h4 class="card-title mb-1">Daftar Pilihan Aroma RM</h4>
                            <p class="text-muted mb-0">Kelola master data pilihan aroma untuk analisa bahan baku.</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" id="btnAdd">
                                <i class="mdi mdi-plus-circle-outline me-1"></i>
                                Tambah Aroma
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;" class="text-center">No.</th>
                                        <th>Nama Pilihan Aroma</th>
                                        <th style="width: 130px;" class="text-center">Status</th>
                                        <th style="width: 120px;" class="text-center">Aksi</th>
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
        <div class="modal fade" id="modalAroma" tabindex="-1" aria-labelledby="modalAromaLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAromaLabel">Tambah Aroma RM</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formAroma">
                        <input type="hidden" name="id" id="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_pilihan" class="form-label">
                                    Nama Pilihan Aroma <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nama_pilihan" name="nama_pilihan" placeholder="Contoh: OK, Kurang, Tidak Ada, Tidak Sesuai..." required>
                                <div class="invalid-feedback" id="errorNamaPilihan"></div>
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

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('master-aroma-rm.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'nama_pilihan', name: 'nama_pilihan' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            language: {
                processing: 'Memuat data...',
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        function resetForm() {
            $('#formAroma')[0].reset();
            $('#id').val('');
            $('#statusAktif').prop('checked', true);
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#errorStatus').text('');
            $('#modalAromaLabel').text('Tambah Aroma RM');
        }

        $('#btnAdd').on('click', function () {
            resetForm();
            $('#modalAroma').modal('show');
        });

        $('#formAroma').on('submit', function (e) {
            e.preventDefault();

            const $btn = $('#btnSubmit');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#errorStatus').text('');

            $.ajax({
                url: "{{ route('master-aroma-rm.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status) {
                        $('#modalAroma').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.nama_pilihan) {
                            $('#nama_pilihan').addClass('is-invalid');
                            $('#errorNamaPilihan').text(errors.nama_pilihan[0]);
                        }
                        if (errors.status) {
                            $('#errorStatus').text(errors.status[0]);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                        });
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i> Simpan');
                }
            });
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');
            resetForm();

            $.ajax({
                url: `{{ url('master-data-rm/aroma') }}/${id}/edit`,
                type: 'GET',
                success: function (res) {
                    if (res.status && res.data) {
                        const d = res.data;
                        $('#id').val(d.id);
                        $('#nama_pilihan').val(d.nama_pilihan);

                        if (d.status) {
                            $('#statusAktif').prop('checked', true);
                        } else {
                            $('#statusNonAktif').prop('checked', true);
                        }

                        $('#modalAromaLabel').text('Edit Aroma RM');
                        $('#modalAroma').modal('show');
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal mengambil data aroma.'
                    });
                }
            });
        });

        $(document).on('click', '.btnDelete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Pilihan aroma "${nama}" akan dihapus!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('master-data-rm/aroma') }}/${id}`,
                        type: 'DELETE',
                        success: function (res) {
                            if (res.status) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data aroma.'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
