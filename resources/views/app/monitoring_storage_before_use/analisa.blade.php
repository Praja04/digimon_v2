@extends('layouts.component.main')
@section('title', 'Analisa Monitoring Storage Before Use')
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

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row gx-lg-5">
                                <div class="col-xl-12">
                                    <div class="mt-xl-0 mt-5">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>{{ $monitoringStorageBeforeUse->storage ?? '-' }} (Storage)</h4>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><a href="#"
                                                            class="text-primary d-block">{{ auth()->user()->name }}</a>
                                                    </div>
                                                    <div class="vr"></div>

                                                    <div class="text-muted">Jenis Sample : <span
                                                            class="text-body fw-medium">{{ $monitoringStorageBeforeUse->jenis_sample ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-time-fill"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Waktu Selesai Pemakaian :</p>
                                                            <h5 class="mb-0">
                                                                {{ $monitoringStorageBeforeUse->waktu_selesai_pemakaian ?? '-' }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-calendar-check-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Estimasi Kadaluarsa :</p>
                                                            <h5 class="mb-0">
                                                                {{ $monitoringStorageBeforeUse->estimasi_kadaluarsa ?? '-' }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                    <div class="col-lg-12">
                        <div class="card">
                            <!--end card-body-->
                            <div class="card-body">
                                <form id="formAnalisa">
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <input type="hidden" name="id_analisa" id="id_analisa"
                                                value="{{ $monitoringStorageBeforeUse->id }}">
                                            <label class="form-label">Visco <span style="color: red">*</span></label>
                                            <input type="text" name="visco" id="visco"
                                                class="form-control comma-input" placeholder="Contoh: 0,00"
                                                value="{{ old('visco', $monitoringStorageBeforeUse->visco ?? '') }}">
                                            <small class="text-danger errorVisco"></small>
                                        </div>
                                        <div class="col-lg-12">
                                            <label class="form-label">Brix <span style="color: red">*</span></label>
                                            <input type="text" name="brix" id="brix"
                                                class="form-control comma-input" placeholder="Contoh: 0,00"
                                                value="{{ old('brix', $monitoringStorageBeforeUse->brix ?? '') }}">
                                            <small class="text-danger errorBrix"></small>
                                        </div>
                                        <div class="col-lg-12">
                                            <label class="form-label">AW <span style="color: red">*</span></label>
                                            <input type="text" name="aw" id="aw"
                                                class="form-control comma-input" placeholder="Contoh: 0,00"
                                                value="{{ old('aw', $monitoringStorageBeforeUse->aw ?? '') }}">
                                            <small class="text-danger errorAw"></small>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary" id="saveAnalisa">Simpan</button>
                                        </div>
                                    </div>
                                </form>
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

            document.querySelectorAll('.comma-input').forEach(function(el) {
                el.addEventListener('input', function() {
                    const value = this.value;
                    if (value.includes('.')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Format Salah!',
                            text: 'Gunakan tanda koma (,) untuk desimal, bukan titik (.)',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#3085d6'
                        });
                        this.value = value.replace(/\./g, ',');
                    }
                });
            });

            $('#formAnalisa').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-storage-before-use.storeAnalisa') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#saveAnalisa').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#saveAnalisa').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then((result) => {
                            window.location.href =
                                "{{ route('monitoring-storage-before-use.index') }}/"
                        })


                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.visco) {
                                $('#visco').addClass('is-invalid');
                                $('.errorVisco').html(errors.visco.join('<br>'));
                            }
                            if (errors.brix) {
                                $('#brix').addClass('is-invalid');
                                $('.errorBrix').html(errors.brix.join('<br>'));
                            }
                            if (errors.aw) {
                                $('#aw').addClass('is-invalid');
                                $('.errorAw').html(errors.aw.join('<br>'));
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
        });
    </script>
@endsection
