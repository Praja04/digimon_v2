@extends('layouts.component.main')

@section('title', 'Jenis Incoming')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- PAGE TITLE --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                        <h4 class="mb-sm-0">
                            Jenis Incoming
                        </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0)">
                                        Master Data PM
                                    </a>
                                </li>

                                <li class="breadcrumb-item active">
                                    Jenis Incoming
                                </li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">

                            <div>
                                <h5 class="card-title mb-1">
                                    Daftar Jenis Incoming
                                </h5>

                                <p class="text-muted mb-0">
                                    Kelola kategori incoming Packaging Material.
                                </p>
                            </div>

                            <button
                                type="button"
                                id="btnAdd"
                                class="btn btn-primary"
                            >
                                <i class="mdi mdi-plus-circle-outline me-1"></i>
                                Tambah Data
                            </button>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">
                                <table
                                    id="datatable"
                                    class="table table-bordered table-striped align-middle w-100"
                                >
                                    <thead class="table-light">
                                        <tr>
                                            <th width="60">No.</th>
                                            <th>Kategori</th>
                                            <th>Nama</th>
                                            <th width="140">Status</th>
                                            <th width="120">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- MODAL TAMBAH DAN EDIT --}}
    <div
        id="modalJenisIncoming"
        class="modal fade"
        tabindex="-1"
        aria-labelledby="modalJenisIncomingLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form id="formJenisIncoming">

                    @csrf

                    <input
                        type="hidden"
                        name="id"
                        id="id"
                    >

                    <div class="modal-header">

                        <h5
                            class="modal-title"
                            id="modalJenisIncomingLabel"
                        >
                            Tambah Jenis Incoming
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Tutup"
                        ></button>

                    </div>

                    <div class="modal-body">

                        {{-- KATEGORI --}}
                        <div class="mb-3">

                            <label
                                for="kategori"
                                class="form-label"
                            >
                                Kategori
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="kategori"
                                id="kategori"
                                class="form-control"
                                value="PM"
                                maxlength="50"
                                placeholder="Contoh: PM"
                            >

                            <div
                                class="invalid-feedback"
                                id="errorKategori"
                            ></div>

                        </div>

                        {{-- NAMA --}}
                        <div class="mb-3">

                            <label
                                for="nama"
                                class="form-label"
                            >
                                Nama Jenis Incoming
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                maxlength="100"
                                placeholder="Contoh: Inner / Outer"
                            >

                            <div
                                class="invalid-feedback"
                                id="errorNama"
                            ></div>

                        </div>

                        {{-- STATUS --}}
                        <div class="mb-3">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Status
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select"
                            >
                                <option value="1">
                                    Aktif
                                </option>

                                <option value="0">
                                    Tidak Aktif
                                </option>
                            </select>

                            <div
                                class="invalid-feedback"
                                id="errorStatus"
                            ></div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Tutup
                        </button>

                        <button
                            type="submit"
                            id="btnSave"
                            class="btn btn-primary"
                        >
                            <i class="mdi mdi-content-save-outline me-1"></i>
                            Simpan
                        </button>

                    </div>

                </form>

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

            const modalElement = document.getElementById('modalJenisIncoming');
            const modal = new bootstrap.Modal(modalElement);

            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,

                ajax: "{{ route('jenis-incoming.index') }}",

                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            function resetForm() {
                $('#formJenisIncoming')[0].reset();

                $('#id').val('');
                $('#kategori').val('PM');
                $('#status').val('1');

                $('#kategori').removeClass('is-invalid');
                $('#nama').removeClass('is-invalid');
                $('#status').removeClass('is-invalid');

                $('#errorKategori').html('');
                $('#errorNama').html('');
                $('#errorStatus').html('');
            }

            function showValidationErrors(errors) {
                if (errors.kategori) {
                    $('#kategori').addClass('is-invalid');
                    $('#errorKategori').html(errors.kategori[0]);
                }

                if (errors.nama) {
                    $('#nama').addClass('is-invalid');
                    $('#errorNama').html(errors.nama[0]);
                }

                if (errors.status) {
                    $('#status').addClass('is-invalid');
                    $('#errorStatus').html(errors.status[0]);
                }
            }

            // TAMBAH DATA
            $('#btnAdd').on('click', function () {
                resetForm();

                $('#modalJenisIncomingLabel').text(
                    'Tambah Jenis Incoming'
                );

                modal.show();

                setTimeout(function () {
                    $('#nama').trigger('focus');
                }, 300);
            });

            // EDIT DATA
            $(document).on('click', '.btnEdit', function () {
                const id = $(this).data('id');

                resetForm();

                $.ajax({
                    type: 'GET',
                    url: "{{ url('/jenis-incoming') }}/" + id + "/edit",
                    dataType: 'json',

                    beforeSend: function () {
                        Swal.fire({
                            title: 'Memuat data...',
                            allowOutsideClick: false,
                            didOpen: function () {
                                Swal.showLoading();
                            }
                        });
                    },

                    success: function (response) {
                        Swal.close();

                        $('#modalJenisIncomingLabel').text(
                            'Edit Jenis Incoming'
                        );

                        $('#id').val(response.data.id);
                        $('#kategori').val(response.data.kategori);
                        $('#nama').val(response.data.nama);
                        $('#status').val(
                            response.data.status ? '1' : '0'
                        );

                        modal.show();
                    },

                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Data tidak berhasil dimuat.'
                        });
                    }
                });
            });

            // SIMPAN DATA
            $('#formJenisIncoming').on('submit', function (event) {
                event.preventDefault();

                $('#kategori').removeClass('is-invalid');
                $('#nama').removeClass('is-invalid');
                $('#status').removeClass('is-invalid');

                $('#errorKategori').html('');
                $('#errorNama').html('');
                $('#errorStatus').html('');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('jenis-incoming.store') }}",
                    data: $(this).serialize(),
                    dataType: 'json',

                    beforeSend: function () {
                        $('#btnSave')
                            .prop('disabled', true)
                            .html(
                                '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...'
                            );
                    },

                    complete: function () {
                        $('#btnSave')
                            .prop('disabled', false)
                            .html(
                                '<i class="mdi mdi-content-save-outline me-1"></i> Simpan'
                            );
                    },

                    success: function (response) {
                        modal.hide();
                        resetForm();

                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                    },

                    error: function (xhr) {
                        if (xhr.status === 422) {
                            showValidationErrors(
                                xhr.responseJSON.errors ?? {}
                            );

                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat menyimpan data.'
                        });
                    }
                });
            });

            // HAPUS DATA
            $(document).on('click', '.btnDelete', function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data?',
                    html:
                        'Jenis Incoming <strong>' +
                        nama +
                        '</strong> akan dihapus.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545'
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        type: 'DELETE',
                        url: "{{ url('/jenis-incoming') }}/" + id,
                        dataType: 'json',

                        success: function (response) {
                            table.ajax.reload(null, false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        },

                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tidak dapat dihapus',
                                text:
                                    xhr.responseJSON?.message ??
                                    'Terjadi kesalahan saat menghapus data.'
                            });
                        }
                    });
                });
            });

        });
    </script>
@endsection