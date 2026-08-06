@extends('layouts.component.main')

@section('title')
    Daftar Jenis Material
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">

                <div
                    class="
                        page-title-box
                        d-sm-flex
                        align-items-center
                        justify-content-between
                    "
                >
                    <h4 class="mb-sm-0">
                        Jenis Material
                    </h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">
                                    Master Data PM
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Jenis Material
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

                    <div
                        class="
                            card-header
                            d-flex
                            flex-wrap
                            align-items-center
                            justify-content-between
                            gap-3
                        "
                    >
                        <div>
                            <h4 class="card-title mb-1">
                                Daftar Jenis Material
                            </h4>

                            <p class="text-muted mb-0">
                                Kelola master Jenis Material Packaging Material.
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
                                class="
                                    table
                                    table-bordered
                                    table-striped
                                    table-hover
                                    align-middle
                                    w-100
                                "
                            >
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">
                                            No.
                                        </th>

                                        <th>
                                            Kode
                                        </th>

                                        <th>
                                            Nama
                                        </th>

                                        <th style="width: 150px;">
                                            Status
                                        </th>

                                        <th style="width: 140px;">
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

{{-- MODAL TAMBAH / EDIT --}}
<div
    class="modal fade"
    id="modalJenisMaterial"
    tabindex="-1"
    aria-labelledby="modalJenisMaterialLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form id="formJenisMaterial">

                @csrf

                <input
                    type="hidden"
                    name="id"
                    id="id"
                >

                <div class="modal-header px-4 py-3">

                    <h4
                        class="modal-title"
                        id="modalJenisMaterialLabel"
                    >
                        Tambah Jenis Material
                    </h4>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Tutup"
                    ></button>

                </div>

                <div class="modal-body px-4 py-4">

                    {{-- KODE --}}
                    <div class="mb-4">

                        <label
                            for="kode"
                            class="form-label"
                        >
                            Kode
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode"
                            id="kode"
                            class="form-control"
                            maxlength="50"
                            placeholder="Masukkan kode material"
                        >

                        <div
                            class="invalid-feedback"
                            id="errorKode"
                        ></div>

                        <small class="text-muted">
                            Contoh: KARDUS, INNER, OUTER, PE1.
                        </small>

                    </div>

                    {{-- NAMA --}}
                    <div class="mb-4">

                        <label
                            for="nama"
                            class="form-label"
                        >
                            Nama Jenis Material
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama"
                            id="nama"
                            class="form-control"
                            maxlength="100"
                            placeholder="Masukkan nama jenis material"
                        >

                        <div
                            class="invalid-feedback"
                            id="errorNama"
                        ></div>

                        <small class="text-muted">
                            Contoh: Kardus, Inner, Outer, Post Film.
                        </small>

                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">

                        <label class="form-label d-block">
                            Status
                            <span class="text-danger">*</span>
                        </label>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label
                                    for="statusAktif"
                                    class="
                                        border
                                        rounded
                                        p-3
                                        w-100
                                        d-flex
                                        align-items-center
                                        gap-2
                                    "
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
                                    class="
                                        border
                                        rounded
                                        p-3
                                        w-100
                                        d-flex
                                        align-items-center
                                        gap-2
                                    "
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
            'modalJenisMaterial'
        );

        const modalJenisMaterial = new bootstrap.Modal(
            modalElement
        );

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,

            ajax: {
                url: "{{ route('jenis-material.index') }}",
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
                emptyTable: 'Data Jenis Material belum tersedia',

                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        function clearValidation() {
            $('#kode').removeClass('is-invalid');
            $('#nama').removeClass('is-invalid');

            $('#errorKode').html('');
            $('#errorNama').html('');
            $('#errorStatus').html('');
        }

        function resetForm() {
            $('#formJenisMaterial')[0].reset();

            $('#id').val('');
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

            $('#modalJenisMaterialLabel').text(
                'Tambah Jenis Material'
            );

            modalJenisMaterial.show();

            setTimeout(function () {
                $('#kode').trigger('focus');
            }, 300);
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');

            resetForm();

            $.ajax({
                url: "{{ url('/jenis-material') }}/" + id + "/edit",
                type: 'GET',
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

                    $('#modalJenisMaterialLabel').text(
                        'Edit Jenis Material'
                    );

                    $('#id').val(response.data.id);
                    $('#kode').val(response.data.kode);
                    $('#nama').val(response.data.nama);

                    if (response.data.status) {
                        $('#statusAktif').prop('checked', true);
                        $('#statusTidakAktif').prop('checked', false);
                    } else {
                        $('#statusAktif').prop('checked', false);
                        $('#statusTidakAktif').prop('checked', true);
                    }

                    modalJenisMaterial.show();
                },

                error: function (xhr) {
                    Swal.close();

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

        $('#formJenisMaterial').on(
            'submit',
            function (event) {
                event.preventDefault();

                clearValidation();

                $.ajax({
                    url: "{{ route('jenis-material.store') }}",
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
                        modalJenisMaterial.hide();

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
            }
        );

        $(document).on(
            'click',
            '.btnDelete',
            function () {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data?',
                    html:
                        'Jenis Material <strong>' +
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
                        url: "{{ url('/jenis-material') }}/" + id,
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
            }
        );

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