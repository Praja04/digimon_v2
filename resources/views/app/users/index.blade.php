@extends('layouts.component.main')

@section('title', 'Pengguna')

@section('content')

    <div class="page-content">
        <div class="container-fluid">

            {{-- PAGE TITLE --}}
            <div class="row">
                <div class="col-12">

                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                        <h4 class="mb-sm-0">
                            @yield('title')
                        </h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="javascript:void(0);">
                                        Master Data
                                    </a>
                                </li>

                                <li class="breadcrumb-item active">
                                    @yield('title')
                                </li>
                            </ol>
                        </div>

                    </div>

                </div>
            </div>

            {{-- USER TABLE --}}
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">

                        <div class="card-header d-flex align-items-center justify-content-between">

                            <h5 class="card-title mb-0">
                                Daftar @yield('title')
                            </h5>

                            <button
                                type="button"
                                id="btnAdd"
                                class="btn btn-primary btn-sm"
                            >
                                <i class="mdi mdi-plus-circle-outline me-1"></i>
                                Tambah Data
                            </button>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table
                                    id="datatable"
                                    class="table table-hover nowrap align-middle"
                                    style="width: 100%;"
                                >
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">
                                                #
                                            </th>

                                            <th style="width: 80px;">
                                                Foto
                                            </th>

                                            <th>
                                                Nama
                                            </th>

                                            <th>
                                                Email
                                            </th>

                                            <th>
                                                Hak Akses
                                            </th>

                                            <th style="width: 170px;">
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

    {{-- ====================================================== --}}
    {{-- MODAL TAMBAH / EDIT PENGGUNA --}}
    {{-- ====================================================== --}}
    <div
        id="modal"
        class="modal fade"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <form
                    id="form"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div class="modal-header">

                        <h4
                            class="modal-title"
                            id="modalLabel"
                        ></h4>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>

                    </div>

                    <div class="modal-body">

                        {{-- USER ID --}}
                        <input
                            type="hidden"
                            name="id"
                            id="id"
                        >

                        {{-- NAMA LENGKAP --}}
                        <div class="mb-3">

                            <label
                                for="name"
                                class="form-label"
                            >
                                Nama Lengkap
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                maxlength="255"
                                autocomplete="name"
                                autofocus
                            >

                            <small class="text-danger errorName"></small>

                        </div>

                        {{-- EMAIL --}}
                        <div class="mb-3">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                maxlength="255"
                                autocomplete="email"
                            >

                            <small class="text-danger errorEmail"></small>

                        </div>

                        {{-- PASSWORD --}}
                        <div class="mb-3">

                            <label
                                for="password"
                                class="form-label"
                            >
                                Password

                                <span
                                    id="passwordRequiredMark"
                                    class="text-danger"
                                >
                                    *
                                </span>
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter"
                                >

                                <button
                                    type="button"
                                    id="togglePassword"
                                    class="btn btn-outline-secondary"
                                    tabindex="-1"
                                    title="Tampilkan password"
                                >
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>

                            </div>

                            <small
                                id="passwordHelp"
                                class="text-muted"
                            >
                                Password wajib diisi untuk pengguna baru.
                            </small>

                            <br>

                            <small class="text-danger errorPassword"></small>

                        </div>

                        {{-- KONFIRMASI PASSWORD --}}
                        <div class="mb-3">

                            <label
                                for="password_confirmation"
                                class="form-label"
                            >
                                Konfirmasi Password

                                <span
                                    id="passwordConfirmationRequiredMark"
                                    class="text-danger"
                                >
                                    *
                                </span>
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                    autocomplete="new-password"
                                    placeholder="Ulangi password"
                                >

                                <button
                                    type="button"
                                    id="togglePasswordConfirmation"
                                    class="btn btn-outline-secondary"
                                    tabindex="-1"
                                    title="Tampilkan konfirmasi password"
                                >
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>

                            </div>

                            <small class="text-danger errorPasswordConfirmation"></small>

                        </div>

                        {{-- HAK AKSES --}}
                        <div class="mb-3">

                            <label
                                for="role"
                                class="form-label"
                            >
                                Hak Akses
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                id="role"
                                name="role"
                                class="select2 form-control"
                                style="width: 100%;"
                            >
                                <option value="">
                                    -- Pilih Hak Akses --
                                </option>

                                <option value="0">
                                    Head Of Department
                                </option>

                                <option value="1">
                                    Supervisor
                                </option>

                                <option value="2">
                                    Foreman
                                </option>

                                <option value="3">
                                    Analis Kimia
                                </option>

                                <option value="4">
                                    Analis Mikro
                                </option>

                                <option value="5">
                                    Analis RM
                                </option>

                                <option value="6">
                                    Analis Field
                                </option>

                                <option value="7">
                                    Operator
                                </option>
                            </select>

                            <small class="text-danger errorRole"></small>

                        </div>

                        {{-- UPLOAD PHOTO --}}
                        <div class="mb-3">

                            <label
                                for="photo"
                                class="form-label"
                            >
                                Upload Foto
                            </label>

                            <input
                                type="file"
                                id="photo"
                                name="photo"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp"
                            >

                            <small class="text-muted">
                                Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                            </small>

                            <br>

                            <small class="text-danger errorPhoto"></small>

                            {{-- PHOTO PREVIEW --}}
                            <div
                                id="photoPreviewWrapper"
                                class="mt-3 d-none"
                            >
                                <div class="d-flex align-items-center gap-3">

                                    <img
                                        id="photoPreview"
                                        src=""
                                        alt="Preview foto pengguna"
                                        class="rounded-circle border"
                                        width="100"
                                        height="100"
                                        style="object-fit: cover;"
                                    >

                                    <div>
                                        <div class="fw-semibold">
                                            Preview Foto
                                        </div>

                                        <small class="text-muted">
                                            Foto baru akan disimpan setelah tombol Simpan ditekan.
                                        </small>
                                    </div>

                                </div>
                            </div>

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
                            class="btn btn-primary"
                            id="save"
                        >
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

            const modalElement = $('#modal');
            const formElement = document.getElementById('form');
            const defaultPhoto = "{{ asset('assets/images/users/avatar-default.png') }}";

            /*
             * Select2
             */
            $('.select2').select2({
                placeholder: '-- Pilih Opsi --',
                dropdownParent: modalElement,
                width: '100%'
            });

            /*
             * CSRF AJAX
             */
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /*
             * DataTable
             */
            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",

                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],

                language: {
                    processing: 'Memuat data...',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada data pengguna',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });

            /*
             * Membersihkan pesan validasi.
             */
            function clearValidationErrors() {
                $('#name').removeClass('is-invalid');
                $('#email').removeClass('is-invalid');
                $('#password').removeClass('is-invalid');
                $('#password_confirmation').removeClass('is-invalid');
                $('#role').removeClass('is-invalid');
                $('#photo').removeClass('is-invalid');

                $('.errorName').html('');
                $('.errorEmail').html('');
                $('.errorPassword').html('');
                $('.errorPasswordConfirmation').html('');
                $('.errorRole').html('');
                $('.errorPhoto').html('');
            }

            /*
             * Membersihkan preview foto.
             */
            function clearPhotoPreview() {
                $('#photoPreview')
                    .attr('src', '');

                $('#photoPreviewWrapper')
                    .addClass('d-none');
            }

            /*
             * Menampilkan preview foto.
             */
            function showPhotoPreview(photoUrl) {
                if (!photoUrl) {
                    clearPhotoPreview();
                    return;
                }

                $('#photoPreview')
                    .attr('src', photoUrl);

                $('#photoPreviewWrapper')
                    .removeClass('d-none');
            }

            /*
             * Reset form untuk Tambah Pengguna.
             */
            function prepareCreateForm() {
                formElement.reset();

                $('#id').val('');
                $('#role').val('').trigger('change');

                $('#password')
                    .prop('required', true);

                $('#password_confirmation')
                    .prop('required', true);

                $('#passwordRequiredMark')
                    .removeClass('d-none');

                $('#passwordConfirmationRequiredMark')
                    .removeClass('d-none');

                $('#passwordHelp')
                    .text('Password wajib diisi untuk pengguna baru.');

                $('#modalLabel')
                    .text('Tambah Pengguna');

                clearValidationErrors();
                clearPhotoPreview();
            }

            /*
             * Reset form untuk Edit Pengguna.
             */
            function prepareEditForm() {
                formElement.reset();

                $('#role').val('').trigger('change');

                $('#password')
                    .prop('required', false)
                    .val('');

                $('#password_confirmation')
                    .prop('required', false)
                    .val('');

                $('#passwordRequiredMark')
                    .addClass('d-none');

                $('#passwordConfirmationRequiredMark')
                    .addClass('d-none');

                $('#passwordHelp')
                    .text('Kosongkan password jika tidak ingin mengubahnya.');

                $('#modalLabel')
                    .text('Edit Pengguna');

                clearValidationErrors();
                clearPhotoPreview();
            }

            /*
             * Tombol Tambah.
             */
            $('body').on('click', '#btnAdd', function () {
                prepareCreateForm();

                modalElement.modal('show');
            });

            /*
             * Tombol Edit.
             */
            $('body').on('click', '#btnEdit', function () {
                const id = $(this).data('id');

                prepareEditForm();

                $.ajax({
                    type: 'GET',
                    url: "{{ route('users.edit', '') }}/" + id,
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

                        /*
                         * Mendukung response controller:
                         *
                         * { data: {...} }
                         *
                         * maupun response lama:
                         *
                         * { id, name, email, role }
                         */
                        const user = response.data ?? response;

                        $('#id').val(user.id ?? '');
                        $('#name').val(user.name ?? '');
                        $('#email').val(user.email ?? '');
                        $('#role').val(user.role ?? '').trigger('change');

                        if (user.photo) {
                            showPhotoPreview(user.photo);
                        } else {
                            clearPhotoPreview();
                        }

                        modalElement.modal('show');
                    },

                    error: function (xhr) {
                        Swal.close();

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text:
                                xhr.responseJSON?.message
                                ?? 'Data pengguna gagal dimuat.'
                        });
                    }
                });
            });

            /*
             * Preview foto.
             */
            $('#photo').on('change', function (event) {
                clearValidationErrors();

                const file = event.target.files[0];

                if (!file) {
                    clearPhotoPreview();
                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!allowedTypes.includes(file.type)) {
                    $(this).val('');

                    $('#photo')
                        .addClass('is-invalid');

                    $('.errorPhoto')
                        .html('Gunakan file JPG, JPEG, PNG, atau WEBP.');

                    clearPhotoPreview();

                    return;
                }

                const maximumSize = 2 * 1024 * 1024;

                if (file.size > maximumSize) {
                    $(this).val('');

                    $('#photo')
                        .addClass('is-invalid');

                    $('.errorPhoto')
                        .html('Ukuran foto maksimal 2 MB.');

                    clearPhotoPreview();

                    return;
                }

                const reader = new FileReader();

                reader.onload = function (loadEvent) {
                    showPhotoPreview(loadEvent.target.result);
                };

                reader.readAsDataURL(file);
            });

            /*
             * Toggle password.
             */
            $('#togglePassword').on('click', function () {
                const passwordInput = $('#password');
                const icon = $(this).find('i');

                const showPassword =
                    passwordInput.attr('type') === 'password';

                passwordInput.attr(
                    'type',
                    showPassword ? 'text' : 'password'
                );

                icon.toggleClass(
                    'mdi-eye-outline',
                    !showPassword
                );

                icon.toggleClass(
                    'mdi-eye-off-outline',
                    showPassword
                );
            });

            /*
             * Toggle konfirmasi password.
             */
            $('#togglePasswordConfirmation').on('click', function () {
                const confirmationInput = $('#password_confirmation');
                const icon = $(this).find('i');

                const showPassword =
                    confirmationInput.attr('type') === 'password';

                confirmationInput.attr(
                    'type',
                    showPassword ? 'text' : 'password'
                );

                icon.toggleClass(
                    'mdi-eye-outline',
                    !showPassword
                );

                icon.toggleClass(
                    'mdi-eye-off-outline',
                    showPassword
                );
            });

            /*
             * Submit Tambah / Edit.
             *
             * Wajib memakai FormData agar file foto ikut terkirim.
             */
            $('#form').on('submit', function (event) {
                event.preventDefault();

                clearValidationErrors();

                const formData = new FormData(formElement);

                $.ajax({
                    url: "{{ route('users.store') }}",
                    type: 'POST',
                    data: formData,
                    dataType: 'json',

                    /*
                     * Wajib untuk upload file.
                     */
                    processData: false,
                    contentType: false,

                    beforeSend: function () {
                        $('#save')
                            .prop('disabled', true)
                            .html(
                                '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                            );
                    },

                    complete: function () {
                        $('#save')
                            .prop('disabled', false)
                            .text('Simpan');
                    },

                    success: function (response) {
                        modalElement.modal('hide');

                        formElement.reset();

                        $('#id').val('');
                        $('#role').val('').trigger('change');

                        clearPhotoPreview();
                        clearValidationErrors();

                        table.ajax.reload(null, false);

                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message
                        });
                    },

                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors =
                                xhr.responseJSON?.errors ?? {};

                            if (errors.name) {
                                $('#name').addClass('is-invalid');

                                $('.errorName')
                                    .html(errors.name.join('<br>'));
                            }

                            if (errors.email) {
                                $('#email').addClass('is-invalid');

                                $('.errorEmail')
                                    .html(errors.email.join('<br>'));
                            }

                            if (errors.password) {
                                $('#password').addClass('is-invalid');

                                $('.errorPassword')
                                    .html(errors.password.join('<br>'));
                            }

                            if (errors.password_confirmation) {
                                $('#password_confirmation')
                                    .addClass('is-invalid');

                                $('.errorPasswordConfirmation')
                                    .html(
                                        errors.password_confirmation.join('<br>')
                                    );
                            }

                            if (errors.role) {
                                $('#role').addClass('is-invalid');

                                $('.errorRole')
                                    .html(errors.role.join('<br>'));
                            }

                            if (errors.photo) {
                                $('#photo').addClass('is-invalid');

                                $('.errorPhoto')
                                    .html(errors.photo.join('<br>'));
                            }

                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text:
                                xhr.responseJSON?.message
                                ?? 'Terjadi kesalahan, silakan coba lagi.'
                        });
                    }
                });
            });

            /*
             * Tombol Hapus.
             */
            $('body').on('click', '#btnDelete', function () {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data dan foto pengguna akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        type: 'DELETE',
                        url: "{{ url('/pengguna') }}/" + id,
                        dataType: 'json',
                        data: {
                            id: id
                        },

                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message
                            });

                            table.ajax.reload(null, false);
                        },

                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text:
                                    xhr.responseJSON?.message
                                    ?? 'Data pengguna gagal dihapus.'
                            });
                        }
                    });
                });
            });

            /*
             * Bersihkan modal ketika ditutup.
             */
            modalElement.on('hidden.bs.modal', function () {
                formElement.reset();

                $('#id').val('');
                $('#role').val('').trigger('change');

                $('#password')
                    .attr('type', 'password');

                $('#password_confirmation')
                    .attr('type', 'password');

                $('#togglePassword i')
                    .removeClass('mdi-eye-off-outline')
                    .addClass('mdi-eye-outline');

                $('#togglePasswordConfirmation i')
                    .removeClass('mdi-eye-off-outline')
                    .addClass('mdi-eye-outline');

                clearPhotoPreview();
                clearValidationErrors();
            });

        });
    </script>

@endsection