@extends('layouts.component.main')
@section('title', 'Profile')
@section('styles')
    <style>
        .profile-img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            cursor: pointer;
        }

        .profile-user:hover .profile-img-overlay {
            opacity: 1;
        }
    </style>
@endsection
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
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->


            <div class="position-relative mx-n4 mt-n4">
                <div class="profile-wid-bg profile-setting-img">
                    <img src="{{ asset('assets') }}/images/banner/banner_5.png" class="profile-wid-img" alt="">
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-3">
                    <div class="card mt-n5">
                        <div class="card-body p-4">
                            <div class="text-center">
                                <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                                    <img src="{{ auth()->user()->image ? Storage::url('users/' . auth()->user()->image) : asset('assets/images/users/user-dummy-img.jpg') }}"
                                        class="rounded-circle avatar-xl img-thumbnail user-profile-image shadow"
                                        id="profile-img-preview" alt="user-profile-image">

                                    @if (auth()->user()->image)
                                        <div class="profile-img-overlay rounded-circle" id="btnDeleteImage">
                                            <i class="ri-delete-bin-fill text-white" style="font-size: 20px;"></i>
                                        </div>
                                    @endif

                                    <div class="position-absolute" style="bottom: 4px; right: 4px;">
                                        <label for="profile-img-file-input"
                                            class="avatar-title rounded-circle bg-primary text-white shadow mb-0 d-flex align-items-center justify-content-center"
                                            style="width: 28px; height: 28px; cursor: pointer; border: 2px solid #fff;">
                                            <i class="ri-camera-fill" style="font-size: 13px;"></i>
                                        </label>
                                        <input id="profile-img-file-input" type="file" name="image"
                                            class="profile-img-file-input" accept="image/*" style="display: none;">
                                    </div>
                                </div>
                                <h5 class="fs-16 mb-1">{{ auth()->user()->name }}</h5>
                                <p class="text-muted mb-0">{{ auth()->user()->role }}</p>
                            </div>
                        </div>
                    </div>
                    <!--end card-->

                </div>
                <!--end col-->
                <div class="col-xxl-9">
                    <div class="card mt-xxl-n5">
                        <div class="card-header">
                            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                        <i class="fas fa-home"></i> Detail Profile
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                        <i class="far fa-user"></i> Ubah Password
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content">
                                <div class="tab-pane active" id="personalDetails" role="tabpanel">
                                    <form id="formProfile">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <input type="hidden" name="id" value="{{ auth()->user()->id }}">
                                                    <label for="name" class="form-label">Nama Lengkap <span
                                                            style="color: red">*</span></label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        value="{{ auth()->user()->name }}">
                                                    <small class="text-danger errorName"></small>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email <span
                                                            style="color: red">*</span></label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        value="{{ auth()->user()->email }}">
                                                    <small class="text-danger errorEmail"></small>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="submit" id="btnProfile"
                                                        class="btn btn-primary">Perbarui</button>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                </div>
                                <!--end tab-pane-->
                                <div class="tab-pane" id="changePassword" role="tabpanel">
                                    <form id="formPassword">
                                        <div class="row g-2">
                                            <div class="col-lg-4">
                                                <div>
                                                    <input type="hidden" name="id" id="id"
                                                        value="{{ auth()->user()->id }}">
                                                    <label for="old_password" class="form-label">Kata Sandi Lama <span
                                                            style="color: red">*</span></label>
                                                    <input type="password" class="form-control" id="old_password"
                                                        name="old_password" placeholder="Masukkan kata sandi lama">
                                                    <small class="text-danger errorOldPassword"></small>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="password" class="form-label">Kata Sandi Baru <span
                                                            style="color: red">*</span></label>
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" placeholder="Masukkan kata sandi baru">
                                                    <small class="text-danger errorPassword"></small>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="password_confirmation" class="form-label">Konfirmasi Kata
                                                        Sandi <span style="color: red">*</span></label>
                                                    <input type="password" class="form-control"
                                                        id="password_confirmation" name="password_confirmation"
                                                        placeholder="Masukkan konfirmasi kata sandi">
                                                    <small class="text-danger errorPasswordConfirmation"></small>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" id="btnPassword"
                                                        class="btn btn-primary">Perbarui</button>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
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

            $('#formProfile').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: new FormData(this),
                    url: "{{ route('profile.update') }}",
                    type: "POST",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#btnProfile').prop('disabled', true).html(
                            'Proses...'
                        );

                        $('#formProfile .form-control').removeClass('is-invalid');
                        $('#formProfile .text-danger').html('');
                    },
                    complete: function() {
                        $('#btnProfile').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        $('#formProfile').trigger("reset");
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#name').addClass('is-invalid');
                                $('#formProfile .errorName').html(errors.name.join('<br>'));
                            }
                            if (errors.email) {
                                $('#email').addClass('is-invalid');
                                $('#formProfile .errorEmail').html(errors.email.join('<br>'));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }
                    }
                })
            })

            $('#formPassword').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('profile.updatePassword') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnPassword').prop('disabled', true).html(
                            'Proses...'
                        );

                        $('#formPassword .form-control').removeClass('is-invalid');
                        $('#formPassword .text-danger').html('');
                    },
                    complete: function() {
                        $('#btnPassword').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        $('#formPassword').trigger("reset");
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.old_password) {
                                $('#old_password').addClass('is-invalid');
                                $('#formPassword .errorOldPassword').html(errors.old_password
                                    .join('<br>'));
                            }
                            if (errors.password) {
                                $('#password').addClass('is-invalid');
                                $('#formPassword .errorPassword').html(errors.password.join(
                                    '<br>'));
                            }
                            if (errors.password_confirmation) {
                                $('#password_confirmation').addClass('is-invalid');
                                $('#formPassword .errorPasswordConfirmation').html(errors
                                    .password_confirmation.join('<br>'));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }
                    }
                })
            })

            $('#profile-img-file-input').change(function() {
                var file = this.files[0];
                if (file) {
                    var formData = new FormData();
                    formData.append('image', file);
                    formData.append('id', '{{ auth()->user()->id }}');

                    $.ajax({
                        data: formData,
                        url: "{{ route('profile.updateImage') }}",
                        type: "POST",
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                $('#profile-img-preview').attr('src', e.target.result);
                            };
                            reader.readAsDataURL(file);

                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            }).then(function() {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text: errors.image ? errors.image[0] :
                                        'Terjadi kesalahan.',
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text: 'Terjadi kesalahan, silakan coba lagi.',
                                });
                            }
                        }
                    });
                }
            });

            $('#btnDeleteImage').click(function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus Foto Profil',
                    text: 'Apakah Anda yakin ingin menghapus foto profil?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            data: {
                                id: '{{ auth()->user()->id }}'
                            },
                            url: "{{ route('profile.deleteImage') }}",
                            type: "POST",
                            dataType: 'json',
                            success: function(response) {
                                $('#profile-img-preview').attr('src',
                                    "{{ asset('assets/images/users/user-dummy-img.jpg') }}"
                                );
                                $('#btnDeleteImage').closest('.avatar-xs').remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                }).then(function() {
                                    window.location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text: 'Terjadi kesalahan, silakan coba lagi.',
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
