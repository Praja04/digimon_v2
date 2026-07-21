@extends('layouts.component.main')

@section('title')
    Daftar Supplier
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                    <h4 class="mb-sm-0">
                        Supplier
                    </h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">
                                    Master Data PM
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Supplier
                            </li>

                        </ol>
                    </div>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="card">

                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">

                        <div>
                            <h4 class="card-title mb-1">
                                Daftar Supplier
                            </h4>

                            <p class="text-muted mb-0">
                                Kelola supplier berdasarkan Jenis Incoming.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="btn btn-primary"
                            id="btnAdd"
                        >
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            Tambah Data
                        </button>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table
                                id="datatable"
                                class="table table-bordered table-striped table-hover align-middle w-100"
                            >
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">
                                            No.
                                        </th>

                                        <th>
                                            Jenis Incoming
                                        </th>

                                        <th>
                                            Kode
                                        </th>

                                        <th>
                                            Nama Supplier
                                        </th>

                                        <th style="width: 140px;">
                                            Status
                                        </th>

                                        <th style="width: 130px;">
                                            Aksi
                                        </th>
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

<div
    class="modal fade"
    id="modalSupplier"
    tabindex="-1"
    aria-labelledby="modalSupplierLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form id="formSupplier">

                @csrf

                <input
                    type="hidden"
                    name="id"
                    id="id"
                >

                <div class="modal-header px-4 py-3">

                    <h4
                        class="modal-title"
                        id="modalSupplierLabel"
                    >
                        Tambah Supplier
                    </h4>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>

                </div>

                <div class="modal-body px-4 py-4">

                    <div class="mb-4">

                        <label
                            for="jenisIncomingId"
                            class="form-label"
                        >
                            Jenis Incoming
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="jenis_incoming_id"
                            id="jenisIncomingId"
                            class="form-select"
                        >
                            <option value="">
                                -- Pilih Jenis Incoming --
                            </option>

                            @foreach ($jenisIncomings as $jenisIncoming)
                                <option value="{{ $jenisIncoming->id }}">
                                    {{ $jenisIncoming->nama }}
                                </option>
                            @endforeach
                        </select>

                        <div
                            class="invalid-feedback"
                            id="errorJenisIncomingId"
                        ></div>

                    </div>

                    <div class="mb-4">

                        <label
                            for="kode"
                            class="form-label"
                        >
                            Kode Supplier
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode"
                            id="kode"
                            class="form-control"
                            maxlength="50"
                            placeholder="Masukkan kode supplier"
                        >

                        <div
                            class="invalid-feedback"
                            id="errorKode"
                        ></div>

                        <small class="text-muted">
                            Contoh: UNIPACK, CMI_KARDUS, MIM.
                        </small>

                    </div>

                    <div class="mb-4">

                        <label
                            for="nama"
                            class="form-label"
                        >
                            Nama Supplier
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama"
                            id="nama"
                            class="form-control"
                            maxlength="150"
                            placeholder="Masukkan nama supplier"
                        >

                        <div
                            class="invalid-feedback"
                            id="errorNama"
                        ></div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label d-block">
                            Status
                            <span class="text-danger">*</span>
                        </label>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label
                                    for="statusAktif"
                                    class="border rounded p-3 w-100 d-flex align-items-center gap-2"
                                >
                                    <input
                                        class="form-check-input mt-0"
                                        type="radio"
                                        name="status"
                                        id="statusAktif"
                                        value="1"
                                        checked
                                    >

                                    <span>
                                        <i class="mdi mdi-check-circle-outline text-success me-1"></i>
                                        Aktif
                                    </span>
                                </label>

                            </div>

                            <div class="col-md-6">

                                <label
                                    for="statusTidakAktif"
                                    class="border rounded p-3 w-100 d-flex align-items-center gap-2"
                                >
                                    <input
                                        class="form-check-input mt-0"
                                        type="radio"
                                        name="status"
                                        id="statusTidakAktif"
                                        value="0"
                                    >

                                    <span>
                                        <i class="mdi mdi-close-circle-outline text-danger me-1"></i>
                                        Tidak Aktif
                                    </span>
                                </label>

                            </div>

                        </div>

                        <div
                            class="text-danger small mt-2"
                            id="errorStatus"
                        ></div>

                    </div>

                </div>

                <div class="modal-footer px-4 py-3">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        <i class="mdi mdi-close me-1"></i>
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="btnSave"
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
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content'
                )
            }
        });

        const modalElement = document.getElementById(
            'modalSupplier'
        );

        const modalSupplier = new bootstrap.Modal(
            modalElement
        );

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,

            ajax: {
                url: "{{ route('supplier.index') }}",
                type: 'GET'
            },

            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'jenis_incoming',
                    name: 'jenisIncoming.nama'
                },
                {
                    data: 'kode',
                    name: 'kode'
                },
                {
                    data: 'nama',
                    name: 'nama'
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
            ],

            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Data Supplier belum tersedia',

                paginate: {
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        function clearValidation() {
            $('#jenisIncomingId').removeClass('is-invalid');
            $('#kode').removeClass('is-invalid');
            $('#nama').removeClass('is-invalid');

            $('#errorJenisIncomingId').html('');
            $('#errorKode').html('');
            $('#errorNama').html('');
            $('#errorStatus').html('');
        }

        function resetForm() {
            $('#formSupplier')[0].reset();

            $('#id').val('');
            $('#jenisIncomingId').val('');
            $('#kode').val('');
            $('#nama').val('');

            $('#statusAktif').prop('checked', true);
            $('#statusTidakAktif').prop('checked', false);

            clearValidation();
        }

        function resetSaveButton() {
            $('#btnSave')
                .prop('disabled', false)
                .html(
                    '<i class="mdi mdi-content-save-outline me-1"></i> Simpan'
                );
        }

        function showValidationErrors(errors) {
            if (errors.jenis_incoming_id) {
                $('#jenisIncomingId').addClass('is-invalid');

                $('#errorJenisIncomingId').html(
                    errors.jenis_incoming_id[0]
                );
            }

            if (errors.kode) {
                $('#kode').addClass('is-invalid');
                $('#errorKode').html(errors.kode[0]);
            }

            if (errors.nama) {
                $('#nama').addClass('is-invalid');
                $('#errorNama').html(errors.nama[0]);
            }

            if (errors.status) {
                $('#errorStatus').html(errors.status[0]);
            }
        }

        $('#btnAdd').on('click', function () {
            resetForm();

            $('#modalSupplierLabel').text(
                'Tambah Supplier'
            );

            modalSupplier.show();
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');

            resetForm();

            $.ajax({
                url: "{{ url('/supplier') }}/" + id + "/edit",
                type: 'GET',
                dataType: 'json',

                success: function (response) {
                    $('#modalSupplierLabel').text(
                        'Edit Supplier'
                    );

                    $('#id').val(response.data.id);

                    $('#jenisIncomingId').val(
                        response.data.jenis_incoming_id
                    );

                    $('#kode').val(response.data.kode);
                    $('#nama').val(response.data.nama);

                    if (response.data.status) {
                        $('#statusAktif').prop('checked', true);
                    } else {
                        $('#statusTidakAktif').prop('checked', true);
                    }

                    modalSupplier.show();
                },

                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text:
                            xhr.responseJSON?.message ??
                            'Data tidak berhasil dimuat.'
                    });
                }
            });
        });

        $('#formSupplier').on('submit', function (event) {
            event.preventDefault();

            clearValidation();

            $.ajax({
                url: "{{ route('supplier.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',

                beforeSend: function () {
                    $('#btnSave')
                        .prop('disabled', true)
                        .html(
                            '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...'
                        );
                },

                success: function (response) {
                    modalSupplier.hide();

                    resetForm();
                    resetSaveButton();

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
                    resetSaveButton();

                    if (xhr.status === 422) {
                        showValidationErrors(
                            xhr.responseJSON.errors ?? {}
                        );

                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text:
                            xhr.responseJSON?.message ??
                            'Terjadi kesalahan saat menyimpan data.'
                    });
                }
            });
        });

        $(document).on('click', '.btnDelete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                icon: 'warning',
                title: 'Hapus data?',
                html:
                    'Supplier <strong>' +
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
                    url: "{{ url('/supplier') }}/" + id,
                    type: 'DELETE',
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

        modalElement.addEventListener(
            'hidden.bs.modal',
            function () {
                resetForm();
                resetSaveButton();
            }
        );

    });
</script>

@endsection