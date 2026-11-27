@extends('layouts.auth.main')
@section('title', 'Masuk')
@section('content')
    <div class="col-lg-6">
        <div class="p-lg-5 p-4">
            <div>
                <h5 class="text-primary">Selamat Datang Kembali!</h5>
                <p class="text-muted">Masuk untuk melanjutkan ke {{ env('APP_NAME') }}.</p>
            </div>

            <div class="mt-4">
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="email" name="email"
                            placeholder="Masukan email">
                        <small class="text-danger errorEmail mt-2"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Kata Sandi <span style="color:red">*</span></label>
                        <input type="password" class="form-control password-input" placeholder="Masukan kata sandi"
                            id="password" name="password">
                        <small class="text-danger errorPassword mt-2"></small>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-success w-100" type="submit" id="login">Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end col -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#email').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorEmail').html('');
            });

            $('#password').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorPassword').html('');
            });

            $('#loginForm').submit(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('login') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#login').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#login').prop('disabled', false).text('Masuk');
                        Swal.close();
                    },
                    success: function(response) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.email) {
                                $('#email').addClass('is-invalid');
                                $('.errorEmail').html(errors.email.join('<br>'));
                            }
                            if (errors.password) {
                                $('#password').addClass('is-invalid');
                                $('.errorPassword').html(errors.password.join('<br>'));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Terjadi kesalahan, silakan coba lagi.',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
