@extends('layouts.component.main')
@section('title', 'Analisis Mikro - Shelf Life')
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
                                <li class="breadcrumb-item"><a href="{{ route('shelf-life.index') }}">Menu</a></li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('shelf-life.analysis-mikro.index') }}">Analisis Mikro</a>
                                </li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Detail Produksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 align-items-center">
                                <div class="col-sm-6 col-md-8">
                                    <h4 class="mb-1 text-dark">Nomor PO:
                                        <strong>{{ $data->shelfLifeSample->productionBatch->po_number }}</strong>
                                    </h4>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Tanggal Produksi:</p>
                                    <h6 class="fw-bold">
                                        {{ \Carbon\Carbon::parse($data->shelfLifeSample->productionBatch->date)->locale('id')->translatedFormat('d F Y') }}
                                    </h6>
                                </div>
                                <div class="col-6 col-md-4">
                                    <p class="mb-0 text-muted small">Varian:</p>
                                    <h6 class="fw-bold">{{ $data->shelfLifeSample->productionBatch->variant }}</h6>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Variant FG:</p>
                                    <h6 class="fw-bold">{{ $data->variant_fg }}</h6>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Kelompok Sample:</p>
                                    <h6 class="fw-bold">{{ $data->kelompok_sample }}</h6>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Kelompok Tanggal:</p>
                                    <h6 class="fw-bold">{{ $data->kelompok_tanggal }}</h6>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Bulan Ke:</p>
                                    <h6 class="fw-bold">{{ $data->bulan_ke }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="form">
                                <div class="row g-3">
                                    <input type="hidden" name="shelf_life_sampling_detail_id"
                                        id="shelf_life_sampling_detail_id" value="{{ $data->id }}">
                                    <input type="hidden" name="bulan_ke" id="bulan_ke" value="{{ $bulanKe }}">

                                    <!-- Loading Indicator -->
                                    <div id="loadingContainer" class="col-lg-12">
                                        <div class="alert alert-primary text-center">
                                            <i class="mdi mdi-loading mdi-spin me-2"></i>
                                            <strong>Memuat data...</strong>
                                        </div>
                                    </div>

                                    <!-- Info Status -->
                                    <div class="col-lg-12 d-none" id="statusContainer">
                                        <div class="alert alert-info" id="statusInfo">
                                            <strong>Status:</strong> <span id="statusText"></span>
                                        </div>
                                    </div>

                                    <!-- Analis Field -->
                                    <div id="analisContainer" class="col-lg-12 d-none">
                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <label class="form-label">Shift <span style="color: red;">*</span></label>
                                                <select name="shift_analis" id="shift_analis" class="form-control">
                                                    <option value="">-- Pilih Shift --</option>
                                                    <option value="1">Shift 1</option>
                                                    <option value="2">Shift 2</option>
                                                    <option value="3">Shift 3</option>
                                                </select>
                                                <small class="text-danger errorShiftAnalis"></small>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label">Nama Analis <span
                                                        style="color: red;">*</span></label>
                                                <input type="text" name="nama_analis" id="nama_analis"
                                                    class="form-control" placeholder="Masukkan Nama Analis"
                                                    oninput="this.value = this.value.toUpperCase();">
                                                <small class="text-danger errorNamaAnalis"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EB & SA Field (H+1) - Digabung dalam 1 row -->
                                    <div id="ebSaContainer" class="col-lg-12 d-none">
                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <label class="form-label">EB <span style="color: red;">*</span></label>
                                                <input type="text" name="eb" id="eb"
                                                    class="form-control comma-input" placeholder="Masukkan nilai EB">
                                                <small class="text-danger errorEb"></small>
                                            </div>
                                            <div class="col-lg-6" id="saFieldContainer">
                                                <label class="form-label">SA <span style="color: red;">*</span></label>
                                                <input type="text" name="sa" id="sa"
                                                    class="form-control comma-input" placeholder="Masukkan nilai SA">
                                                <small class="text-danger errorSa"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TPC Field (H+3) -->
                                    <div id="tpcContainer" class="col-lg-12 d-none">
                                        <label class="form-label">TPC <span style="color: red;">*</span></label>
                                        <input type="text" name="tpc" id="tpc"
                                            class="form-control comma-input" placeholder="Masukkan nilai TPC">
                                        <small class="text-danger errorTpc"></small>
                                    </div>

                                    <!-- YM Field (H+5) -->
                                    <div id="ymContainer" class="col-lg-12 d-none">
                                        <label class="form-label">YM <span style="color: red;">*</span></label>
                                        <input type="text" name="ym" id="ym"
                                            class="form-control comma-input" placeholder="Masukkan nilai YM">
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

            let currentMikroData = null;
            let detailId = $('#shelf_life_sampling_detail_id').val();
            let bulanKe = parseInt($('#bulan_ke').val());
            let showSa = [1, 24].includes(bulanKe);

            if (!showSa) {
                $('#saFieldContainer').addClass('d-none');
            }

            function loadMikroData() {
                $('#loadingContainer').removeClass('d-none');
                $('#statusContainer').addClass('d-none');
                $('#analisContainer, #ebSaContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#btnSave').prop('disabled', true);

                $.ajax({
                    type: "GET",
                    url: "{{ route('shelf-life.analysis-mikro.get-mikro') }}",
                    data: {
                        id: detailId
                    },
                    dataType: "json",
                    success: function(response) {
                        currentMikroData = response.data;
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

            function canInputByDay(baseTime, plusDay) {
                const base = new Date(baseTime);
                const now = new Date();

                base.setDate(base.getDate() + plusDay);

                return now >= base;
            }

            function formatDate(dateStr, plusDay) {
                const d = new Date(dateStr);
                d.setDate(d.getDate() + plusDay);

                return d.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            }

            function showNextField() {
                let shift = currentMikroData.shift_analis;
                let nama_analis = currentMikroData.nama_analis;
                let eb = currentMikroData.eb;
                let sa = currentMikroData.sa;
                let tpc = currentMikroData.tpc;
                let ym = currentMikroData.ym;
                let baseTime = currentMikroData.updated_at;

                $('#analisContainer, #ebSaContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#shift_analis, #nama_analis, #eb, #sa, #tpc, #ym').val('').prop('disabled', true);
                $('#btnSave').prop('disabled', true);

                $('.text-danger').text('');
                $('.form-control').removeClass('is-invalid');

                if (!shift || !nama_analis) {
                    $('#statusText').text('Langkah 1/4 - Input Shift dan Nama Analis terlebih dahulu');
                    $('#analisContainer').removeClass('d-none');
                    $('#shift_analis, #nama_analis').prop('disabled', false);

                    $('#btnSave').prop('disabled', false);
                    return;
                }

                if (eb === null || eb === undefined || (showSa && (sa === null || sa === undefined))) {
                    if (!canInputByDay(baseTime, 1)) {
                        $('#statusText').html(
                            `EB ${showSa ? 'dan SA' : ''} dapat diinput mulai tanggal <strong>${formatDate(baseTime, 1)}</strong>`
                        );
                        return;
                    }

                    let statusHtml =
                    `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>`;
                    statusHtml += `Langkah 2/4 - Input EB${showSa ? ' dan SA' : ''} (H+1)`;

                    $('#statusText').html(statusHtml);
                    $('#ebSaContainer').removeClass('d-none');
                    $('#eb').prop('disabled', false);

                    if (showSa) {
                        $('#sa').prop('disabled', false);
                    }

                    $('#eb').focus();
                    $('#btnSave').prop('disabled', false);
                    return;
                }

                if (tpc === null || tpc === undefined) {
                    if (!canInputByDay(baseTime, 3)) {
                        $('#statusText').html(
                            `TPC dapat diinput mulai tanggal <strong>${formatDate(baseTime, 3)}</strong>`
                        );
                        return;
                    }

                    let statusHtml =
                    `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>`;
                    statusHtml += `EB: <strong>${eb}</strong>`;

                    if (showSa) {
                        statusHtml += ` | SA: <strong>${sa}</strong>`;
                    }

                    statusHtml += `<br>Langkah 3/4 - Input TPC (H+3)`;

                    $('#statusText').html(statusHtml);
                    $('#tpcContainer').removeClass('d-none');
                    $('#tpc').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);
                    return;
                }

                if (ym === null || ym === undefined) {
                    if (!canInputByDay(baseTime, 5)) {
                        $('#statusText').html(
                            `YM dapat diinput mulai tanggal <strong>${formatDate(baseTime, 5)}</strong>`
                        );
                        return;
                    }

                    let statusHtml =
                    `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>`;
                    statusHtml += `EB: <strong>${eb}</strong>`;

                    if (showSa) {
                        statusHtml += ` | SA: <strong>${sa}</strong>`;
                    }

                    statusHtml += ` | TPC: <strong>${tpc}</strong><br>Langkah 4/4 - Input YM (H+5)`;

                    $('#statusText').html(statusHtml);
                    $('#ymContainer').removeClass('d-none');
                    $('#ym').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);
                    return;
                }

                showCompleteData(shift, nama_analis, eb, sa, tpc, ym);
            }

            function showCompleteData(shift, nama_analis, eb, sa, tpc, ym) {
                $('#statusText').html(`
                    <div class="text-success">
                        <i class="mdi mdi-check-all"></i> <strong>Semua parameter analisa sudah lengkap!</strong>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-success me-2">Shift: ${shift}</span>
                        <span class="badge bg-success me-2">Analis: ${nama_analis}</span>
                        <span class="badge bg-success me-2">EB: ${eb}</span>
                        ${showSa ? `<span class="badge bg-success me-2">SA: ${sa}</span>` : ''}
                        <span class="badge bg-success me-2">TPC: ${tpc}</span>
                        <span class="badge bg-success">YM: ${ym}</span>
                    </div>
                `);

                $('#analisContainer, #ebSaContainer, #tpcContainer, #ymContainer').removeClass('d-none');

                $('#shift_analis').val(shift).prop('disabled', true);
                $('#nama_analis').val(nama_analis).prop('readonly', true);
                $('#eb').val(eb).prop('readonly', true);
                $('#tpc').val(tpc).prop('readonly', true);
                $('#ym').val(ym).prop('readonly', true);

                if (showSa) {
                    $('#sa').val(sa).prop('readonly', true);
                }

                $('#btnSave').addClass('d-none');

                $('.form-control[readonly], .form-control:disabled').css({
                    'background-color': '#f8f9fa',
                    'cursor': 'not-allowed',
                    'border-color': '#28a745'
                });
            }

            loadMikroData();

            $('#form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('shelf-life.analysis-mikro.store') }}",
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
                        $('#btnSave').prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan'
                        );
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            loadMikroData();
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

                            if (errors.shift_analis) {
                                $('#shift_analis').addClass('is-invalid');
                                $('.errorShiftAnalis').html(errors.shift_analis.join('<br>'));
                            }
                            if (errors.nama_analis) {
                                $('#nama_analis').addClass('is-invalid');
                                $('.errorNamaAnalis').html(errors.nama_analis.join('<br>'));
                            }
                            if (errors.eb) {
                                $('#eb').addClass('is-invalid');
                                $('.errorEb').html(errors.eb.join('<br>'));
                            }
                            if (errors.sa) {
                                $('#sa').addClass('is-invalid');
                                $('.errorSa').html(errors.sa.join('<br>'));
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
