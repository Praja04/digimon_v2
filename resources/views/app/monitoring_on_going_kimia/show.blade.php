@extends('layouts.component.main')
@section('title', 'Analisa - Monitoring On Going Kimia')
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
                                <li class="breadcrumb-item"><a
                                        href="{{ route('monitoring-ongoing-kimia.index') }}">Monitoring On Going
                                        Kimia</a></li>
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
                                                <h4>{{ $monitoringOnGoing->storage ?? '-' }} (Storage)</h4>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><a href="#"
                                                            class="text-primary d-block">{{ auth()->user()->name }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
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
                                                            <p class="text-muted mb-1">Nomor PO :</p>
                                                            <h5 class="mb-0">
                                                                {{ $monitoringOnGoing->productionBatch->po_number ?? '-' }}
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
                                                                <i class="ri-drop-fill"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Variant :</p>
                                                            <h5 class="mb-0">
                                                                {{ $monitoringOnGoing->variant ?? '-' }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end col -->
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-lg-6 col-sm-6">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-calendar-event-fill"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Tanggal Filling :</p>
                                                            <h5 class="mb-0">
                                                                {{ $monitoringOnGoing->filling_date ? \Carbon\Carbon::parse($monitoringOnGoing->filling_date)->locale('id')->translatedFormat('d F Y') : '-' }}
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
                                                                <i class="ri-time-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Jam Koding :</p>
                                                            <h5 class="mb-0">
                                                                {{ $monitoringOnGoing->jam_koding ?? '-' }}
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
                            <div class="card-body">
                                <h5 class="mb-3">Hasil Analisa Kimia</h5>
                                <form id="form">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <input type="hidden" id="id" name="id"
                                                value="{{ $monitoringOnGoing->id }}">
                                            <label for="berat_jenis" class="form-label">Bj <span
                                                    style="color: red">*</span></label>
                                            <input type="text" class="form-control comma-input" id="berat_jenis"
                                                name="berat_jenis" placeholder="0,00"
                                                value="{{ str_replace('.', ',', $monitoringOnGoing->berat_jenis ?? '') }}">
                                            <small class="text-danger errorBeratJenis"></small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="visco" class="form-label">Visco <span
                                                    style="color: red">*</span></label>
                                            <input type="text" class="form-control comma-input" id="visco"
                                                name="visco" placeholder="0,00"
                                                value="{{ str_replace('.', ',', $monitoringOnGoing->visco ?? '') }}">
                                            <small class="text-danger errorVisco"></small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="brix" class="form-label">Brix <span
                                                    style="color: red">*</span></label>
                                            <input type="text" class="form-control comma-input" id="brix"
                                                name="brix" placeholder="0,00"
                                                value="{{ str_replace('.', ',', $monitoringOnGoing->brix ?? '') }}">
                                            <small class="text-danger errorBrix"></small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="aw" class="form-label">Aw <span
                                                    style="color: red">*</span></label>
                                            <input type="text" class="form-control comma-input" id="aw"
                                                name="aw" placeholder="0,00"
                                                value="{{ str_replace('.', ',', $monitoringOnGoing->aw ?? '') }}">
                                            <small class="text-danger errorAw"></small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="nacl" class="form-label">NaCl <span
                                                    style="color: red">*</span></label>
                                            <input type="text" class="form-control comma-input" id="nacl"
                                                name="nacl" placeholder="0,00"
                                                value="{{ str_replace('.', ',', $monitoringOnGoing->nacl ?? '') }}">
                                            <small class="text-danger errorNaCl"></small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="ph" class="form-label">pH</label>
                                            <input type="text" class="form-control comma-input" id="ph"
                                                name="ph" placeholder="0,00"
                                                value="{{ str_replace('.', ',', $monitoringOnGoing->ph ?? '') }}">
                                            <small class="text-danger errorPh"></small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="color" class="form-label">Warna <span
                                                    style="color: red">*</span></label>
                                            <select class="form-control select2" id="color" name="color">
                                                <option value="">-- Pilih Warna --</option>
                                                @foreach ($colors as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $monitoringOnGoing->color_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name . ' - ' . $item->code }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-danger errorColor"></small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="organo" class="form-label">Organo <span
                                                    style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="organo" name="organo"
                                                value="{{ $monitoringOnGoing->organo ?? '' }}"
                                                oninput="this.value = this.value.toUpperCase();">
                                            <small class="text-danger errorOrgano"></small>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="status_disposition" class="form-label">Status <span
                                                    style="color: red">*</span></label>
                                            <select class="form-control" id="status_disposition"
                                                name="status_disposition">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="OK"
                                                    {{ $monitoringOnGoing->status == 'OK' ? 'selected' : '' }}>OK</option>
                                                <option value="NOT OK"
                                                    {{ $monitoringOnGoing->status == 'NOT OK' ? 'selected' : '' }}>NOT OK
                                                </option>
                                            </select>
                                            <small class="text-danger errorStatusDisposition"></small>
                                        </div>

                                        @if (auth()->user()->role == 'Foreman')
                                            <div class="col-lg-12 mb-3">
                                                <label class="form-label">Disposisi <span
                                                        style="color: red">*</span></label>
                                                <select name="disposition" id="disposition" class="form-control"
                                                    required>
                                                    <option value="">-- Pilih Disposisi --</option>
                                                    @if ($monitoringOnGoing->status == 'OK')
                                                        <option value="Release"
                                                            {{ $monitoringOnGoing->disposisi == 'Release' ? 'selected' : '' }}>
                                                            Release</option>
                                                    @else
                                                        <option value="Hold"
                                                            {{ $monitoringOnGoing->disposisi == 'Hold' ? 'selected' : '' }}>
                                                            Hold</option>
                                                    @endif
                                                </select>
                                                <small class="text-danger errorStatusDisposisi"></small>
                                            </div>
                                        @endif

                                        <div class="col-md-12 mb-3">
                                            <label for="remark" class="form-label">Catatan</label>
                                            <textarea class="form-control" id="remark" name="remark" rows="3"
                                                oninput="this.value = this.value.toUpperCase();"></textarea>
                                            <small class="text-danger errorRemark"></small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
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
        $('.select2').select2({
            placeholder: '-- Pilih Opsi --',
        });

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

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-ongoing-kimia.store.analisa') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#save').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then((result) => {
                            window.location.href =
                                "{{ route('monitoring-ongoing-kimia.index') }}"
                        })
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        if (xhr.status === 409 && response && response.message) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal Disimpan',
                                text: response.message,
                            });
                            return;
                        }

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.berat_jenis) {
                                $('#berat_jenis').addClass('is-invalid');
                                $('.errorBeratJenis').html(errors.berat_jenis.join('<br>'));
                            }
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
                            if (errors.nacl) {
                                $('#nacl').addClass('is-invalid');
                                $('.errorNaCl').html(errors.nacl.join('<br>'));
                            }
                            if (errors.ph) {
                                $('#ph').addClass('is-invalid');
                                $('.errorPh').html(errors.ph.join('<br>'));
                            }
                            if (errors.color) {
                                $('#color').addClass('is-invalid');
                                $('.errorColor').html(errors.color.join('<br>'));
                            }
                            if (errors.organo) {
                                $('#organo').addClass('is-invalid');
                                $('.errorOrgano').html(errors.organo.join('<br>'));
                            }
                            if (errors.status_disposition) {
                                $('#status_disposition').addClass('is-invalid');
                                $('.errorStatusDisposition').html(errors.status_disposition
                                    .join('<br>'));
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
