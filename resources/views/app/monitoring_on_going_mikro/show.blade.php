@extends('layouts.component.main')
@section('title', 'Analisa - Monitoring On Going Mikro')
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
                                        href="{{ route('monitoring-ongoing-mikro.index') }}">Monitoring On Going Mikro</a>
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

                                        <!-- Compact info grid -->
                                        <div class="row g-3 mt-4">
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-calendar-check-line text-success fs-4 me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Nomor PO</small>
                                                        <span
                                                            class="fw-semibold">{{ $monitoringOnGoing->productionBatch->po_number ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-drop-fill text-info fs-4 me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Variant</small>
                                                        <span
                                                            class="fw-semibold">{{ $monitoringOnGoing->variant ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-calendar-event-fill text-warning fs-4 me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Tanggal Filling</small>
                                                        <span
                                                            class="fw-semibold">{{ $monitoringOnGoing->filling_date ? \Carbon\Carbon::parse($monitoringOnGoing->filling_date)->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-time-line text-warning fs-4 me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Jam Koding</small>
                                                        <span
                                                            class="fw-semibold">{{ $monitoringOnGoing->jam_koding ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-flask-line text-primary fs-4 me-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Jenis Sampel</small>
                                                        <span
                                                            class="fw-semibold">{{ $monitoringOnGoing->jenis_sampel ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            @if (auth()->user()->role == 'Analis Mikro')
                                <!-- FORM MIKRO -->
                                <form id="formMikro">
                                    <input type="hidden" name="id" id="id"
                                        value="{{ $monitoringOnGoing->id }}">
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

                                        <!-- Analis Field -->
                                        <div id="analisContainer" class="col-lg-12 d-none">
                                            <div class="row g-3">
                                                <div class="col-lg-6">
                                                    <label class="form-label">Shift <span
                                                            style="color: red;">*</span></label>
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
                                                        class="form-control comma-input" placeholder="Masukkan Nama Analis"
                                                        oninput="this.value = this.value.toUpperCase();">
                                                    <small class="text-danger errorNamaAnalis"></small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- EB Field -->
                                        <div id="ebContainer" class="col-lg-12 d-none">
                                            <label class="form-label">EB <span class="text-danger">*</span></label>
                                            <input type="text" name="eb" id="eb"
                                                class="form-control comma-input" placeholder="Masukkan nilai EB">
                                            <small class="text-danger errorEb"></small>
                                        </div>

                                        <!-- TPC Field -->
                                        <div id="tpcContainer" class="col-lg-12 d-none">
                                            <label class="form-label">TPC <span class="text-danger">*</span></label>
                                            <input type="text" name="tpc" id="tpc"
                                                class="form-control comma-input" placeholder="Masukkan nilai TPC">
                                            <small class="text-danger errorTpc"></small>
                                        </div>

                                        <!-- YM Field -->
                                        <div id="ymContainer" class="col-lg-12 d-none">
                                            <label class="form-label">YM <span class="text-danger">*</span></label>
                                            <input type="text" name="ym" id="ym"
                                                class="form-control comma-input" placeholder="Masukkan nilai YM">
                                            <small class="text-danger errorYm"></small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary" id="btnSave">
                                            <i class="mdi mdi-content-save me-1"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            @endif

                            @if (auth()->user()->role == 'Analis Kimia')
                                <!-- FORM KIMIA -->
                                <form id="formKimia">
                                    <input type="hidden" name="id" id="id"
                                        value="{{ $monitoringOnGoing->id }}">
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <label class="form-label">Benda Asing <span
                                                    style="color: red;">*</span></label>
                                            <select name="benda_asing" id="benda_asing" class="form-control">
                                                <option value="">-- Pilih Benda Asing --</option>
                                                <option value="Ada"
                                                    {{ $monitoringOnGoing->benda_asing == 'Ada' ? 'selected' : '' }}>
                                                    Ada</option>
                                                <option value="Tidak Ada"
                                                    {{ $monitoringOnGoing->benda_asing == 'Tidak Ada' ? 'selected' : '' }}>
                                                    Tidak Ada</option>
                                            </select>
                                            <small class="text-danger errorBendaAsing"></small>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2 mt-3">
                                            <button type="submit" class="btn btn-primary" id="btnSaveKimia">
                                                <i class="mdi mdi-content-save me-1"></i> Simpan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
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

            // Validasi input hanya koma untuk desimal
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
            let monitoringId = $('#id').val();

            // Load data mikro
            function loadData() {
                $('#loadingContainer').removeClass('d-none');
                $('#statusContainer').addClass('d-none');
                $('#ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#btnSave').prop('disabled', true);

                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-ongoing-mikro.edit', '') }}/" + monitoringId,
                    dataType: "json",
                    success: function(response) {
                        currentData = response;

                        // Set ID jika data sudah ada
                        if (currentData.id) {
                            $('#id').val(currentData.id);
                        }

                        $('#loadingContainer').addClass('d-none');
                        $('#statusContainer').removeClass('d-none');
                        showNextField();
                    },
                    error: function(xhr) {
                        $('#loadingContainer').addClass('d-none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: xhr.responseJSON?.message || 'Gagal mengambil data.'
                        });
                    }
                });
            }

            function isEmpty(value) {
                return value === null || value === undefined || value === '';
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
                let shift = currentData.shift;
                let nama_analis = currentData.nama_analis;
                let eb = currentData.eb;
                let tpc = currentData.tpc;
                let ym = currentData.ym;
                let baseTime = currentData.updated_at;

                // Reset semua field
                $('#analisContainer, #ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#shift_analis, #nama_analis, #eb, #tpc, #ym').val('').prop('disabled', true);
                $('#btnSave').prop('disabled', true);

                // Reset error messages
                $('.text-danger').text('');
                $('.form-control').removeClass('is-invalid');

                // ===== STEP 1 : Shift & Nama Analis =====
                if (!shift || !nama_analis) {
                    $('#statusText').text('Langkah 1/4 - Input Shift dan Nama Analis');
                    $('#analisContainer').removeClass('d-none');
                    $('#shift_analis, #nama_analis').prop('disabled', false);
                    $('#btnSave').prop('disabled', false);
                    return;
                }

                // ===== STEP 2 : EB (H + 1) =====
                if (eb === null || eb === undefined) {
                    if (!canInputByDay(baseTime, 1)) {
                        $('#statusText').html(
                            `EB dapat diinput mulai tanggal <strong>${formatDate(baseTime, 1)}</strong>`
                        );
                        return;
                    }

                    $('#statusText').html(
                        `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>
                        Langkah 2/4 - Input EB`
                    );

                    $('#ebContainer').removeClass('d-none');
                    $('#eb').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);
                    return;
                }

                // ===== STEP 3 : TPC (H + 3) =====
                if (tpc === null || tpc === undefined) {
                    if (!canInputByDay(baseTime, 3)) {
                        $('#statusText').html(
                            `TPC dapat diinput mulai tanggal <strong>${formatDate(baseTime, 3)}</strong>`
                        );
                        return;
                    }

                    $('#statusText').html(
                        `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>
                        EB: <strong>${eb}</strong><br>
                        Langkah 3/4 - Input TPC`
                    );

                    $('#tpcContainer').removeClass('d-none');
                    $('#tpc').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);
                    return;
                }

                // ===== STEP 4 : YM (H + 5) =====
                if (ym === null || ym === undefined) {
                    if (!canInputByDay(baseTime, 5)) {
                        $('#statusText').html(
                            `YM dapat diinput mulai tanggal <strong>${formatDate(baseTime, 5)}</strong>`
                        );
                        return;
                    }

                    $('#statusText').html(
                        `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>
                        EB: <strong>${eb}</strong> | TPC: <strong>${tpc}</strong><br>
                        Langkah 4/4 - Input YM`
                    );

                    $('#ymContainer').removeClass('d-none');
                    $('#ym').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);
                    return;
                }

                $('#statusText').html(`
                    <div class="text-success">
                        <i class="mdi mdi-check-all"></i> <strong>Semua parameter analisa sudah lengkap!</strong>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-success me-2">Shift: ${shift}</span>
                        <span class="badge bg-success me-2">Analis: ${nama_analis}</span>
                        <span class="badge bg-success me-2">EB: ${eb}</span>
                        <span class="badge bg-success me-2">TPC: ${tpc}</span>
                        <span class="badge bg-success">YM: ${ym}</span>
                    </div>
                `);

                // Tampilkan semua data dalam mode readonly
                $('#ebContainer, #tpcContainer, #ymContainer').removeClass('d-none');
                $('#eb').val(eb).prop('disabled', true);
                $('#tpc').val(tpc).prop('disabled', true);
                $('#ym').val(ym).prop('disabled', true);

                // Sembunyikan tombol simpan karena sudah lengkap
                $('#btnSave').addClass('d-none');

            }

            // Submit form
            $('#formMikro').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-ongoing-mikro.analisa.mikro') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnSave').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...'
                        );
                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').text('');
                    },
                    complete: function() {
                        $('#btnSave').prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan'
                        );
                    },
                    success: function(response) {
                        // Cek apakah semua parameter sudah lengkap

                        let eb = $('#eb').val();
                        let tpc = $('#tpc').val();
                        let ym = $('#ym').val();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Data berhasil disimpan!',
                        }).then(() => {
                            loadData();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

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
                                $('.errorEb').text(errors.eb[0]);
                            }
                            if (errors.tpc) {
                                $('#tpc').addClass('is-invalid');
                                $('.errorTpc').text(errors.tpc[0]);
                            }
                            if (errors.ym) {
                                $('#ym').addClass('is-invalid');
                                $('.errorYm').text(errors.ym[0]);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan, silakan coba lagi.'
                            });
                        }
                    }
                });
            });

            // Load data saat halaman dibuka
            loadData();

            $('#formKimia').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-ongoing-mikro.analisa.kimia') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnSaveKimia').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#btnSaveKimia').prop('disabled', false).text('Simpan');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Data berhasil disimpan!',
                        }).then(() => {
                            window.location.href =
                                "{{ route('monitoring-ongoing-mikro.index') }}";
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.benda_asing) {
                                $('#benda_asing').addClass('is-invalid');
                                $('.errorBendaAsing').html(errors.benda_asing.join('<br>'));
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
