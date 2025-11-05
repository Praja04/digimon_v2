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
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form id="form">
                                <div class="row g-3">
                                    <div class="alert alert-danger d-none error-alert"></div>
                                    <input type="hidden" name="id" id="id"
                                        value="{{ $monitoringDailyTank->id }}">

                                    <!-- ✅ Loading Indicator -->
                                    <div id="loadingContainer" class="col-lg-12">
                                        <div class="alert alert-primary text-center">
                                            <i class="mdi mdi-loading mdi-spin me-2"></i>
                                            <strong>Memuat data...</strong>
                                        </div>
                                    </div>

                                    <!-- ✅ Info Status -->
                                    <div class="col-lg-12 d-none" id="statusContainer">
                                        <div class="alert alert-info" id="statusInfo">
                                            <strong>Status:</strong> <span id="statusText"></span>
                                        </div>
                                    </div>

                                    <!-- EB Field -->
                                    <div id="ebContainer" class="col-lg-12 d-none">
                                        <label class="form-label">EB <span style="color: red;">*</span></label>
                                        <input type="text" name="eb" id="eb" class="form-control comma-input"
                                            placeholder="Masukkan nilai EB">
                                        <small class="text-danger errorEb"></small>
                                    </div>

                                    <!-- TPC Field -->
                                    <div id="tpcContainer" class="col-lg-12 d-none">
                                        <label class="form-label">TPC <span style="color: red;">*</span></label>
                                        <input type="text" name="tpc" id="tpc" class="form-control comma-input"
                                            placeholder="Masukkan nilai TPC">
                                        <small class="text-danger errorTpc"></small>
                                    </div>

                                    <!-- YM Field -->
                                    <div id="ymContainer" class="col-lg-12 d-none">
                                        <label class="form-label">YM <span style="color: red;">*</span></label>
                                        <input type="text" name="ym" id="ym" class="form-control comma-input"
                                            placeholder="Masukkan nilai YM">
                                        <small class="text-danger errorYm"></small>
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

            let currentMonitoringDailyTankData = null;
            let monitoringDailyTankId = $('#id').val();

            // Load data monitoring daily tank saat halaman dibuka
            function loadMonitoringDailyTankData() {
                // Tampilkan loading
                $('#loadingContainer').removeClass('d-none');
                $('#statusContainer').addClass('d-none');
                $('#ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#btnSave').prop('disabled', true);

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.monitoring-daily-tank-mikro.getData') }}",
                    data: {
                        id: monitoringDailyTankId
                    },
                    dataType: "json",
                    success: function(response) {
                        currentMonitoringDailyTankData = response.data;
                        // Sembunyikan loading
                        $('#loadingContainer').addClass('d-none');
                        $('#statusContainer').removeClass('d-none');
                        showNextField();
                    },
                    error: function() {
                        $('#loadingContainer').addClass('d-none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal mengambil data.'
                        });
                    }
                });
            }

            function showNextField() {
                let eb = currentMonitoringDailyTankData.eb;
                let tpc = currentMonitoringDailyTankData.tpc;
                let ym = currentMonitoringDailyTankData.ym;

                // Sembunyikan semua field dulu
                $('#ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#eb, #tpc, #ym').val('').prop('disabled', true);
                $('#btnSave').prop('disabled', true);

                // ✅ Logika urutan: EB → TPC → YM (cek null/undefined secara eksplisit)
                if (eb === null || eb === undefined) {
                    // Jika EB belum diisi
                    $('#statusText').text('Langkah 1/3 - Input EB terlebih dahulu');
                    $('#ebContainer').removeClass('d-none');
                    $('#eb').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);

                } else if (tpc === null || tpc === undefined) {
                    // Jika EB sudah, tapi TPC belum
                    $('#statusText').html(`EB sudah diisi: <strong>${eb}</strong><br>Langkah 2/3 - Input TPC`);
                    $('#tpcContainer').removeClass('d-none');
                    $('#tpc').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);

                } else if (ym === null || ym === undefined) {
                    // Jika EB & TPC sudah, tapi YM belum
                    $('#statusText').html(
                        `EB: <strong>${eb}</strong> | TPC: <strong>${tpc}</strong><br>Langkah 3/3 - Input YM (terakhir)`
                    );
                    $('#ymContainer').removeClass('d-none');
                    $('#ym').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);

                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Data Lengkap',
                        text: 'Semua parameter analisa sudah diisi.'
                    }).then(() => {
                        window.location.href =
                            "{{ route('monitoring-daily-tank.index') }}"
                    });
                }
            }

            // Panggil fungsi untuk load data saat halaman dibuka
            loadMonitoringDailyTankData();

            $('#form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('analisa.monitoring-daily-tank-mikro.update') }}",
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
                            // Reload data untuk menampilkan field berikutnya
                            loadMonitoringDailyTankData();
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

                            if (errors.eb) {
                                $('#eb').addClass('is-invalid');
                                $('.errorEb').html(errors.eb.join('<br>'));
                            }
                            if (errors.tpc) {
                                $('#tpc').addClass('is-invalid');
                                $('.errorTpc').html(errors.tpc.join('<br>'));
                            }
                            if (errors.ym) {
                                $('#ym').addClass('is-invalid');
                                $('.errorYm').html(errors.ym.join('<br>'));
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
