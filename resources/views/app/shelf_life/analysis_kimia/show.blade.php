@extends('layouts.component.main')
@section('title', 'Analisis Kimia - Shelf Life')
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
                                <li class="breadcrumb-item"><a
                                        href="{{ route('shelf-life.analysis-kimia.index') }}">Analisis Kimia</a></li>
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
                                    <h6 class="fw-bold">
                                        {{ $data->variant_fg }}
                                    </h6>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Kelompok Sample:</p>
                                    <h6 class="fw-bold">
                                        {{ $data->kelompok_sample }}
                                    </h6>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Kelompok Tanggal:</p>
                                    <h6 class="fw-bold">
                                        {{ $data->kelompok_tanggal }}
                                    </h6>
                                </div>
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Bulan Ke:</p>
                                    <h6 class="fw-bold">
                                        {{ $data->bulan_ke }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="mt-0 mb-4">

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="form">
                                <input type="hidden" name="shelf_life_sampling_detail_id"
                                    id="shelf_life_sampling_detail_id" value="{{ $data->id }}">

                                <div class="row g-3">
                                    <!-- Loading Indicator -->
                                    <div id="loadingContainer" class="col-lg-12">
                                        <div class="alert alert-primary text-center">
                                            <i class="mdi mdi-loading mdi-spin me-2"></i>
                                            <strong>Memuat data...</strong>
                                        </div>
                                    </div>

                                    <!-- Info Status -->
                                    <div class="col-lg-12 d-none" id="statusContainer">
                                        <div class="alert alert-info">
                                            <strong>Status:</strong> <span id="statusText"></span>
                                        </div>
                                    </div>

                                    <!-- Step 1: Shift & Nama Analis -->
                                    <div id="analisContainer" class="col-lg-12 d-none">
                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <label class="form-label">Shift <span style="color: red;">*</span></label>
                                                <select name="shift_analis" id="shift_analis" class="form-control">
                                                    <option value="">-- Pilih Shift --</option>
                                                    <option value="1">Shift 1 (06:00 - 14:00)</option>
                                                    <option value="2">Shift 2 (14:00 - 22:00)</option>
                                                    <option value="3">Shift 3 (22:00 - 06:00)</option>
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

                                    <!-- Step 2+: Parameter Analisis Kimia -->
                                    <div id="parameterContainer" class="d-none">
                                        <div class="row g-3">
                                            <div class="col-lg-4">
                                                <label class="form-label">NACL <span style="color: red">*</span></label>
                                                <input type="text" name="nacl" id="nacl"
                                                    class="form-control comma-input" placeholder="Contoh: 0,00">
                                                <small class="text-danger errorNacl"></small>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">BRIX <span style="color: red">*</span></label>
                                                <input type="text" name="brix" id="brix"
                                                    class="form-control comma-input" placeholder="Contoh: 0,00">
                                                <small class="text-danger errorBrix"></small>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Aw <span style="color: red">*</span></label>
                                                <input type="text" name="aw" id="aw"
                                                    class="form-control comma-input" placeholder="Contoh: 0,00">
                                                <small class="text-danger errorAw"></small>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">pH <span style="color: red">*</span></label>
                                                <input type="text" name="ph" id="ph"
                                                    class="form-control comma-input" placeholder="Contoh: 0,00">
                                                <small class="text-danger errorPh"></small>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Bj <span style="color: red">*</span></label>
                                                <input type="text" name="bj" id="bj"
                                                    class="form-control comma-input" placeholder="Contoh: 0,00">
                                                <small class="text-danger errorBj"></small>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Buih <span style="color: red">*</span></label>
                                                <input type="text" name="buih" id="buih"
                                                    class="form-control comma-input" placeholder="Contoh: 0,00">
                                                <small class="text-danger errorBuih"></small>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Aroma <span style="color: red">*</span></label>
                                                <input type="text" name="aroma" id="aroma" class="form-control"
                                                    oninput="this.value = this.value.toUpperCase();">
                                                <small class="text-danger errorAroma"></small>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Warna <span style="color: red">*</span></label>
                                                <select name="color" id="color" class="select2 form-control">
                                                    <option value="">-- Pilih Warna --</option>
                                                    @foreach ($colors as $color)
                                                        <option value="{{ $color->id }}">
                                                            {{ $color->name }} ({{ $color->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-danger errorColor"></small>
                                            </div>
                                            <div class="col-lg-4">
                                                <label class="form-label">Organo <span style="color: red">*</span></label>
                                                <input type="text" name="organo" id="organo" class="form-control"
                                                    oninput="this.value = this.value.toUpperCase();">
                                                <small class="text-danger errorOrgano"></small>
                                            </div>
                                            @php
                                                $hideVisco = in_array($bulanKe, [7, 8, 9, 10, 11, 15, 21]);
                                            @endphp

                                            @if (!$hideVisco)
                                                <div class="col-lg-4">
                                                    <label class="form-label">Visco <span
                                                            style="color: red">*</span></label>
                                                    <input type="text" name="visco" id="visco"
                                                        class="form-control comma-input" placeholder="Contoh: 0,00">
                                                    <small class="text-danger errorVisco"></small>
                                                </div>
                                            @endif

                                            @php
                                                $showTotalNitrogen = in_array($bulanKe, [6, 12, 18, 24]);
                                            @endphp

                                            @if ($showTotalNitrogen)
                                                <div class="col-lg-4">
                                                    <label class="form-label">Total Nitrogen <span
                                                            style="color: red">*</span></label>
                                                    <input type="text" name="total_nitrogen" id="total_nitrogen"
                                                        class="form-control comma-input">
                                                    <small class="text-danger errorTotalNitrogen"></small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" id="save">
                                            <i class="mdi mdi-content-save me-1"></i> Simpan
                                        </button>
                                    </div>
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
            $('.select2').select2({
                placeholder: '-- Pilih Opsi --'
            });

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

            let currentData = null;
            let shelfLifeSampleDetailId = $('#shelf_life_sampling_detail_id').val();

            function loadData() {
                $('#loadingContainer').removeClass('d-none');
                $('#statusContainer').addClass('d-none');
                $('#analisContainer, #parameterContainer').addClass('d-none');
                $('#save').prop('disabled', true);

                $.ajax({
                    type: "GET",
                    url: "{{ route('shelf-life.analysis-kimia.edit', '') }}/" + shelfLifeSampleDetailId,
                    dataType: "json",
                    success: function(response) {
                        console.log(response);
                        currentData = response.data || {};
                        $('#loadingContainer').addClass('d-none');
                        $('#statusContainer').removeClass('d-none');
                        showNextStep();
                    },
                    error: function(xhr) {
                        $('#loadingContainer').addClass('d-none');

                        if (xhr.status === 404) {
                            currentData = {};
                            $('#statusContainer').removeClass('d-none');
                            showNextStep();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: xhr.responseJSON?.message || 'Gagal mengambil data.'
                            });
                        }
                    }
                });
            }

            const bulanKe = {{ $bulanKe }};
            const hideVisco = [7, 8, 9, 10, 11, 15, 21].includes(bulanKe);
            const showTotalNitrogen = [6, 12, 18, 24].includes(bulanKe);

            function showNextStep() {
                $('#analisContainer, #parameterContainer').addClass('d-none');
                $('#shift_analis, #nama_analis').val('').prop('disabled', true);
                $('#parameterContainer input, #parameterContainer select').val('').prop('disabled', true);
                $('#save').prop('disabled', true);

                $('.text-danger').text('');
                $('.form-control').removeClass('is-invalid');

                let shift = currentData.shift_analis;
                let nama = currentData.nama_analis;

                let requiredParams = ['nacl', 'brix', 'aw', 'ph', 'bj', 'buih', 'aroma', 'color_id', 'organo'];

                if (!hideVisco) {
                    requiredParams.push('visco');
                }

                if (showTotalNitrogen) {
                    requiredParams.push('total_nitrogen');
                }

                let allParams = requiredParams.every(param => currentData[param] !== null && currentData[param] !==
                    undefined && currentData[param] !== '');

                if (!shift || !nama) {
                    $('#statusText').html(
                        '<i class="mdi mdi-numeric-1-circle"></i> Langkah 1 - Pilih Shift dan Nama Analis terlebih dahulu'
                    );
                    $('#analisContainer').removeClass('d-none');
                    $('#shift_analis, #nama_analis').prop('disabled', false);

                    const currentHour = new Date().getHours();
                    let suggestedShift = 1;
                    if (currentHour >= 6 && currentHour < 14) {
                        suggestedShift = 1;
                    } else if (currentHour >= 14 && currentHour < 22) {
                        suggestedShift = 2;
                    } else {
                        suggestedShift = 3;
                    }
                    $('#shift_analis').val(suggestedShift);
                    $('#nama_analis').focus();
                    $('#save').prop('disabled', false);
                } else if (!allParams) {
                    $('#statusText').html(`
                        <div>
                            <i class="mdi mdi-numeric-2-circle"></i> Langkah 2 - Input Parameter Analisis Kimia
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-success me-2">Shift: ${shift}</span>
                            <span class="badge bg-success">Analis: ${nama}</span>
                        </div>
                    `);

                    $('#parameterContainer').removeClass('d-none');
                    $('#parameterContainer input, #parameterContainer select').prop('disabled', false);

                    if (currentData.nacl) $('#nacl').val(currentData.nacl);
                    if (currentData.brix) $('#brix').val(currentData.brix);
                    if (currentData.aw) $('#aw').val(currentData.aw);
                    if (currentData.ph) $('#ph').val(currentData.ph);
                    if (currentData.bj) $('#bj').val(currentData.bj);
                    if (currentData.buih) $('#buih').val(currentData.buih);
                    if (currentData.aroma) $('#aroma').val(currentData.aroma);
                    if (currentData.color_id) $('#color').val(currentData.color_id).trigger('change');
                    if (currentData.organo) $('#organo').val(currentData.organo);
                    if (!hideVisco && currentData.visco) {
                        $('#visco').val(currentData.visco);
                    }
                    if (showTotalNitrogen && currentData.total_nitrogen) {
                        $('#total_nitrogen').val(currentData.total_nitrogen);
                    }

                    $('#nacl').focus();
                    $('#save').prop('disabled', false);
                } else {
                    showCompleteData(shift, nama);
                }
            }

            function showCompleteData(shift, nama) {
                $('#statusContainer').removeClass('d-none');
                $('#statusContainer .alert').removeClass('alert-info').addClass('alert-success');
                $('#statusText').html('<strong>✓ Data Lengkap</strong> - Semua parameter analisa sudah diisi');

                $('#analisContainer, #parameterContainer').removeClass('d-none');

                const shiftText = shift == 1 ? 'Shift 1 (06:00 - 14:00)' :
                    shift == 2 ? 'Shift 2 (14:00 - 22:00)' :
                    'Shift 3 (22:00 - 06:00)';

                $('#shift_analis').val(shift).prop('disabled', true);
                $('#nama_analis').val(nama).prop('readonly', true);

                $('#nacl').val(currentData.nacl).prop('readonly', true);
                $('#brix').val(currentData.brix).prop('readonly', true);
                $('#aw').val(currentData.aw).prop('readonly', true);
                $('#ph').val(currentData.ph).prop('readonly', true);
                $('#bj').val(currentData.bj).prop('readonly', true);
                $('#buih').val(currentData.buih).prop('readonly', true);
                $('#aroma').val(currentData.aroma).prop('readonly', true);
                $('#color').val(currentData.color_id).trigger('change').prop('disabled', true);
                $('#organo').val(currentData.organo).prop('readonly', true);

                if (!hideVisco) {
                    $('#visco').val(currentData.visco).prop('readonly', true);
                }

                if (showTotalNitrogen) {
                    $('#total_nitrogen').val(currentData.total_nitrogen).prop('readonly', true);
                }

                $('#save').addClass('d-none');

                $('#analisContainer label').first().html('Shift <span class="badge bg-success ms-2">Terisi</span>');
                $('#analisContainer .col-lg-6:last-child label').html(
                    'Nama Analis <span class="badge bg-success ms-2">Terisi</span>');

                $('#parameterContainer label').each(function() {
                    let currentLabel = $(this).html();
                    currentLabel = currentLabel.replace(/<span style="color:\s*red[^>]*>\*<\/span>/gi, '');
                    currentLabel = currentLabel.trim() +
                        ' <span class="badge bg-success ms-2">Terisi</span>';
                    $(this).html(currentLabel);
                });

                // Tambahkan styling untuk readonly fields
                $('.form-control[readonly], .form-control:disabled, .select2-container').css({
                    'background-color': '#f8f9fa',
                    'cursor': 'not-allowed',
                    'border-color': '#28a745'
                });
            }

            $('#form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('shelf-life.analysis-kimia.store') }}",
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
                        $('#save').prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan'
                        );
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            let formData = new FormData(document.getElementById(
                                'form'));
                            let shift = formData.get('shift_analis');
                            let nama = formData.get('nama_analis');
                            let allFilled = shift && nama &&
                                formData.get('nacl') && formData.get('brix') &&
                                formData.get('aw') && formData.get('ph') &&
                                formData.get('bj') && formData.get('buih') &&
                                formData.get('aroma') && formData.get('color') &&
                                formData.get('organo') && formData.get('visco') &&
                                formData.get('total_nitrogen');

                            if (allFilled) {
                                window.location.href =
                                    "{{ route('shelf-life.analysis-kimia.show', '') }}/" +
                                    shelfLifeSampleDetailId;
                            } else {
                                loadData();
                            }
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

                        if (xhr.status === 403 && response && response.message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Akses Ditolak',
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
                            if (errors.nacl) {
                                $('#nacl').addClass('is-invalid');
                                $('.errorNacl').html(errors.nacl.join('<br>'));
                            }
                            if (errors.brix) {
                                $('#brix').addClass('is-invalid');
                                $('.errorBrix').html(errors.brix.join('<br>'));
                            }
                            if (errors.aw) {
                                $('#aw').addClass('is-invalid');
                                $('.errorAw').html(errors.aw.join('<br>'));
                            }
                            if (errors.ph) {
                                $('#ph').addClass('is-invalid');
                                $('.errorPh').html(errors.ph.join('<br>'));
                            }
                            if (errors.bj) {
                                $('#bj').addClass('is-invalid');
                                $('.errorBj').html(errors.bj.join('<br>'));
                            }
                            if (errors.buih) {
                                $('#buih').addClass('is-invalid');
                                $('.errorBuih').html(errors.buih.join('<br>'));
                            }
                            if (errors.aroma) {
                                $('#aroma').addClass('is-invalid');
                                $('.errorAroma').html(errors.aroma.join('<br>'));
                            }
                            if (errors.color) {
                                $('#color').addClass('is-invalid');
                                $('.errorColor').html(errors.color.join('<br>'));
                            }
                            if (errors.organo) {
                                $('#organo').addClass('is-invalid');
                                $('.errorOrgano').html(errors.organo.join('<br>'));
                            }
                            if (errors.visco) {
                                $('#visco').addClass('is-invalid');
                                $('.errorVisco').html(errors.visco.join('<br>'));
                            }
                            if (errors.total_nitrogen) {
                                $('#total_nitrogen').addClass('is-invalid');
                                $('.errorTotalNitrogen').html(errors.total_nitrogen.join(
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

            loadData();
        });
    </script>
@endsection
