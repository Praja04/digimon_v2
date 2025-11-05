@extends('layouts.component.main')
@section('title', 'Analisa Monitoring Daily Tank Mikro')
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
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="mb-3 text-dark fw-semibold">{{ $monitoringDailyTank->storage ?? '-' }} <span
                                    class="fw-normal text-muted">(Storage)</span></h5>

                            <div class="row g-3 text-dark">
                                <div class="col-md-6 col-lg-4">
                                    <label class="d-block small text-muted mb-1">Tanggal Sampling</label>
                                    <span class="fw-medium">{{ $monitoringDailyTank->tanggal_sampling ?? '-' }}</span>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label class="d-block small text-muted mb-1">Sampling Point</label>
                                    <span class="fw-medium">{{ $monitoringDailyTank->sampling_point ?? '-' }}</span>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label class="d-block small text-muted mb-1">Status Pemakaian</label>
                                    <span class="fw-medium">{{ $monitoringDailyTank->status_pemakaian ?? '-' }}</span>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label class="d-block small text-muted mb-1">Jenis Analisa</label>
                                    <span class="fw-medium">{{ $monitoringDailyTank->jenis_analisa ?? '-' }}</span>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label class="d-block small text-muted mb-1">Jenis Sample</label>
                                    <span class="fw-medium">{{ $monitoringDailyTank->jenis_sample ?? '-' }}</span>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label class="d-block small text-muted mb-1">QC Field</label>
                                    <span class="fw-medium">{{ $monitoringDailyTank->qcField->name ?? '-' }}</span>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label class="d-block small text-muted mb-1">Operator</label>
                                    <span class="fw-medium">{{ auth()->user()->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form id="form">
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $monitoringDailyTank->id }}">
                                        <label class="form-label">BRIX <span style="color: red">*</span></label>
                                        <input type="text" name="brix" id="brix" class="form-control comma-input"
                                            placeholder="Contoh: 0,00" value="{{ $monitoringDailyTank->brix ?? '' }}">
                                        <small class="text-danger errorBrix"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">NACL <span style="color: red">*</span></label>
                                        <input type="text" name="nacl" id="nacl" class="form-control comma-input"
                                            placeholder="Contoh: 0,00" value="{{ $monitoringDailyTank->nacl ?? '' }}">
                                        <small class="text-danger errorNacl"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Bj <span style="color: red">*</span></label>
                                        <input type="text" name="bj" id="bj" class="form-control comma-input"
                                            placeholder="Contoh: 0,00" value="{{ $monitoringDailyTank->bj ?? '' }}">
                                        <small class="text-danger errorBj"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Visco <span style="color: red">*</span></label>
                                        <input type="text" name="visco" id="visco" class="form-control comma-input"
                                            placeholder="Contoh: 0,00" value="{{ $monitoringDailyTank->visco ?? '' }}">
                                        <small class="text-danger errorVisco"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Aw <span style="color: red">*</span></label>
                                        <input type="text" name="aw" id="aw" class="form-control comma-input"
                                            placeholder="Contoh: 0,00" value="{{ $monitoringDailyTank->aw ?? '' }}">
                                        <small class="text-danger errorAw"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">pH</label>
                                        <input type="text" name="ph" id="ph"
                                            class="form-control comma-input" placeholder="Contoh: 0,00"
                                            value="{{ $monitoringDailyTank->ph ?? '' }}">
                                        <small class="text-danger errorPh"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Buih</label>
                                        <input type="text" name="buih" id="buih"
                                            class="form-control comma-input" placeholder="Contoh: 0,00"
                                            value="{{ $monitoringDailyTank->buih ?? '' }}">
                                        <small class="text-danger errorBuih"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Organo <span style="color: red">*</span></label>
                                        <input type="text" name="organo" id="organo" class="form-control"
                                            oninput="this.value = this.value.toUpperCase();"
                                            value="{{ $monitoringDailyTank->organo ?? '' }}">
                                        <small class="text-danger errorOrgano"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Endapan</label>
                                        <input type="text" name="endapan" id="endapan" class="form-control"
                                            oninput="this.value = this.value.toUpperCase();"
                                            value="{{ $monitoringDailyTank->endapan ?? '' }}">
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Warna <span style="color: red">*</span></label>
                                        <select name="color" id="color" class="select2 form-control">
                                            <option value="">-- Pilih Warna --</option>
                                            @foreach ($colors as $color)
                                                <option value="{{ $color->id }}"
                                                    {{ $monitoringDailyTank->color_id == $color->id ? 'selected' : '' }}>
                                                    {{ $color->name }} ({{ $color->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger errorColor"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Status <span style="color: red">*</span></label>
                                        <select name="status_parameter" id="status_parameter" class="form-control">
                                            <option value="">-- Pilih Status --</option>
                                            <option value="OK"
                                                {{ $monitoringDailyTank->status_parameter == 'OK' ? 'selected' : '' }}>OK
                                            </option>
                                            <option value="NOT OK"
                                                {{ $monitoringDailyTank->status_parameter == 'NOT OK' ? 'selected' : '' }}>
                                                NOT OK</option>
                                        </select>
                                        <small class="text-danger errorStatusParameter"></small>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Disposisi <span style="color: red">*</span></label>
                                        <select name="status_disposisi" id="status_disposisi" class="form-control">
                                            <option value="">-- Pilih Disposisi --</option>
                                            <option value="RELEASE"
                                                {{ $monitoringDailyTank->status_disposisi == 'RELEASE' ? 'selected' : '' }}>
                                                RELEASE</option>
                                            <option value="RELEASE BERSYARAT"
                                                {{ $monitoringDailyTank->status_disposisi == 'RELEASE BERSYARAT' ? 'selected' : '' }}>
                                                RELEASE BERSYARAT</option>
                                            <option value="TIDAK STD"
                                                {{ $monitoringDailyTank->status_disposisi == 'TIDAK STD' ? 'selected' : '' }}>
                                                TIDAK STD</option>
                                        </select>
                                        <small class="text-danger errorStatusDisposisi"></small>
                                    </div>
                                    <div class="col-lg-12 d-none">
                                        <label class="form-label">Tindakan Lanjutan (TIDAK STD)</label>
                                        <select name="tindakan_lanjutan" id="tindakan_lanjutan" class="form-control">
                                            <option value="">-- Pilih Tindakan Lanjutan --</option>
                                            <option value="Drain"
                                                {{ $monitoringDailyTank->tindakan_lanjutan == 'Drain' ? 'selected' : '' }}>
                                                Drain</option>
                                            <option value="Release Bersyarat"
                                                {{ $monitoringDailyTank->tindakan_lanjutan == 'Release Bersyarat' ? 'selected' : '' }}>
                                                Release Bersyarat</option>
                                        </select>
                                        <small class="text-danger errorStatusParameter"></small>
                                    </div>
                                    <div class="col-lg-12">
                                        <label class="form-label">Alasan Disposisi</label>
                                        <textarea name="alasan_disposisi" id="alasan_disposisi" class="form-control" rows="3"
                                            placeholder="Isi alasan disposisi jika diperlukan..." oninput="this.value = this.value.toUpperCase();">
                                            {{ $monitoringDailyTank->alasan_disposisi }}</textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.select2').select2();

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Event handler untuk perubahan status parameter
            $('#status_parameter').on('change', function() {
                const status = $(this).val();
                const disposisiSelect = $('#status_disposisi');

                // Reset disposisi
                disposisiSelect.val('').trigger('change');

                if (status === 'OK') {
                    // Jika OK, set disposisi ke RELEASE dan disable
                    disposisiSelect.html('<option value="RELEASE">RELEASE</option>');
                    disposisiSelect.val('RELEASE').prop('readonly', true).css({
                        'background-color': '#e9ecef',
                        'pointer-events': 'none'
                    });

                    // Sembunyikan tindakan lanjutan
                    $('#tindakan_lanjutan').closest('.col-lg-12').removeClass('d-block').addClass('d-none');
                    $('#tindakan_lanjutan').val('');

                } else if (status === 'NOT OK') {
                    // Jika NOT OK, tampilkan hanya 2 pilihan
                    disposisiSelect.html(`
                <option value="">-- Pilih Disposisi --</option>
                <option value="RELEASE BERSYARAT">RELEASE BERSYARAT</option>
                <option value="TIDAK STD">TIDAK STD</option>
            `);
                    disposisiSelect.prop('disabled', false);

                } else {
                    // Jika belum memilih status, kembalikan ke semua pilihan
                    disposisiSelect.html(`
                <option value="">-- Pilih Disposisi --</option>
                <option value="RELEASE">RELEASE</option>
                <option value="RELEASE BERSYARAT">RELEASE BERSYARAT</option>
                <option value="TIDAK STD">TIDAK STD</option>
            `);
                    disposisiSelect.prop('disabled', false);

                    // Sembunyikan tindakan lanjutan
                    $('#tindakan_lanjutan').closest('.col-lg-12').removeClass('d-block').addClass('d-none');
                    $('#tindakan_lanjutan').val('');
                }
            });

            // Event handler untuk perubahan disposisi
            $('#status_disposisi').on('change', function() {
                const disposisi = $(this).val();

                if (disposisi === 'TIDAK STD') {
                    // Tampilkan field tindakan lanjutan
                    $('#tindakan_lanjutan').closest('.col-lg-12').removeClass('d-none').addClass('d-block');
                } else {
                    // Sembunyikan field tindakan lanjutan
                    $('#tindakan_lanjutan').closest('.col-lg-12').removeClass('d-block').addClass('d-none');
                    $('#tindakan_lanjutan').val('');
                }
            });

            // Inisialisasi saat halaman dimuat (jika ada nilai default)
            if ($('#status_parameter').val()) {
                $('#status_parameter').trigger('change');
            }

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
                    url: "{{ route('analisa.monitoring-daily-tank-kimia.update') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnSave').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );
                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#btnSave').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            window.location.href =
                                "{{ route('monitoring-daily-tank.index') }}";
                        });
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

                        if (xhr.status === 422 && response && response.errors) {
                            let errors = response.errors;

                            if (errors.brix) {
                                $('#brix').addClass('is-invalid');
                                $('.errorBrix').html(errors.brix.join('<br>'));
                            }
                            if (errors.nacl) {
                                $('#nacl').addClass('is-invalid');
                                $('.errorNacl').html(errors.nacl.join('<br>'));
                            }
                            if (errors.bj) {
                                $('#bj').addClass('is-invalid');
                                $('.errorBj').html(errors.bj.join('<br>'));
                            }
                            if (errors.visco) {
                                $('#visco').addClass('is-invalid');
                                $('.errorVisco').html(errors.visco.join('<br>'));
                            }
                            if (errors.aw) {
                                $('#aw').addClass('is-invalid');
                                $('.errorAw').html(errors.aw.join('<br>'));
                            }
                            if (errors.organo) {
                                $('#organo').addClass('is-invalid');
                                $('.errorOrgano').html(errors.organo.join('<br>'));
                            }
                            if (errors.buih) {
                                $('#buih').addClass('is-invalid');
                                $('.errorBuih').html(errors.buih.join('<br>'));
                            }
                            if (errors.ph) {
                                $('#ph').addClass('is-invalid');
                                $('.errorPh').html(errors.ph.join('<br>'));
                            }
                            if (errors.color) {
                                $('#color').addClass('is-invalid');
                                $('.errorColor').html(errors.color.join('<br>'));
                            }
                            if (errors.status_parameter) {
                                $('#status_parameter').addClass('is-invalid');
                                $('.errorStatusParameter').html(errors.status_parameter.join(
                                    '<br>'));
                            }
                            if (errors.status_disposisi) {
                                $('#status_disposisi').addClass('is-invalid');
                                $('.errorStatusDisposisi').html(errors.status_disposisi.join(
                                    '<br>'));
                            }
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                        });
                    }
                });
            });
        });
    </script>
@endsection
