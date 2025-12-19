@extends('layouts.component.main')
@section('title', 'Analisa Blending Awal')
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
                                        href="{{ route('analisa.blending-awal-mikro.index') }}">Analisa
                                        Blending Awal - Mikro</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row gx-lg-5">
                                <div class="col-xl-12">
                                    <div class="mt-xl-0 mt-5">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <h4>{{ $blending->productionBatch->po_number }} (Nomor PO)</h4>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><a href="#"
                                                            class="text-primary d-block">{{ Session::get('username') }}</a>
                                                    </div>
                                                    <div class="vr"></div>

                                                    <div class="text-muted">Tanggal Produksi : <span
                                                            class="text-body fw-medium">{{ $blending->productionBatch->date }}</span>
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
                                                                <i class="ri-drop-fill"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Variant :</p>
                                                            <h5 class="mb-0">{{ $blending->productionBatch->variant }}
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
                                                                <i class="ri-arrow-left-right-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Batch Range :</p>
                                                            <h5 class="mb-0">{{ $blending->productionBatch->batch_range }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end row -->

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Description :</h5>
                                            <p>{{ $blending->productionBatch->description ?? '-' }}</p>
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
                                <form id="form">
                                    <div class="row g-3">
                                        <div class="alert alert-danger d-none error-alert"></div>
                                        <input type="hidden" name="id" id="id" value="{{ $blending->id }}">

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

                                        <!-- Analis Field -->
                                        <div id="analisContainer" class="col-lg-12 d-none">
                                            <div class="mb-3">
                                                <label class="form-label">Shift <span style="color: red;">*</span></label>
                                                <select name="shift_analis" id="shift_analis" class="form-control">
                                                    <option value="">-- Pilih Shift --</option>
                                                    <option value="1">Shift 1 (06:00 - 14:00)</option>
                                                    <option value="2">Shift 2 (14:00 - 22:00)</option>
                                                    <option value="3">Shift 3 (22:00 - 06:00)</option>
                                                </select>
                                                <small class="text-danger errorShiftAnalis"></small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama Analis <span
                                                        style="color: red;">*</span></label>
                                                <input type="text" name="nama_analis" id="nama_analis"
                                                    class="form-control comma-input" placeholder="Masukkan Nama Analis">
                                                <small class="text-danger errorNamaAnalis"></small>
                                            </div>
                                        </div>

                                        <!-- EB Field -->
                                        <div id="ebContainer" class="col-lg-12 d-none">
                                            <label class="form-label">EB <span style="color: red;">*</span></label>
                                            <input type="text" name="eb" id="eb"
                                                class="form-control comma-input" placeholder="Masukkan nilai EB">
                                            <small class="text-danger errorEb"></small>
                                        </div>

                                        <!-- TPC Field -->
                                        <div id="tpcContainer" class="col-lg-12 d-none">
                                            <label class="form-label">TPC <span style="color: red;">*</span></label>
                                            <input type="text" name="tpc" id="tpc"
                                                class="form-control comma-input" placeholder="Masukkan nilai TPC">
                                            <small class="text-danger errorTpc"></small>
                                        </div>

                                        <!-- YM Field -->
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

            let currentBlendingData = null;
            let blendingId = $('#id').val();

            // Load data blending saat halaman dibuka
            function loadBlendingData() {
                // Tampilkan loading
                $('#loadingContainer').removeClass('d-none');
                $('#statusContainer').addClass('d-none');
                $('#analisContainer, #ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#btnSave').prop('disabled', true);

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.blending-awal-mikro.getBlendingData') }}",
                    data: {
                        id: blendingId
                    },
                    dataType: "json",
                    success: function(response) {
                        currentBlendingData = response.data;
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
                            text: 'Gagal mengambil data blending.'
                        });
                    }
                });
            }

            function showNextField() {
                let shift = currentBlendingData.shift;
                let nama_analis = currentBlendingData.nama_analis;
                let eb = currentBlendingData.eb;
                let tpc = currentBlendingData.tpc;
                let ym = currentBlendingData.ym;

                // Sembunyikan semua field dulu
                $('#analisContainer, #ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#shift_analis, #nama_analis, #eb, #tpc, #ym').val('').prop('disabled', true);
                $('#btnSave').prop('disabled', true);

                // ✅ STEP 1: Input Shift & Nama Analis terlebih dahulu
                if (!shift || !nama_analis) {
                    $('#statusText').text('Langkah 1/4 - Input Shift dan Nama Analis terlebih dahulu');
                    $('#analisContainer').removeClass('d-none');
                    $('#shift_analis, #nama_analis').prop('disabled', false);

                    // Auto-set shift berdasarkan jam saat ini (opsional, bisa di-skip jika user ingin pilih manual)
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
                    $('#btnSave').prop('disabled', false);

                // ✅ STEP 2: Jika Shift & Nama Analis sudah, input EB
                } else if (eb === null || eb === undefined) {
                    $('#statusText').html(
                        `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>Langkah 2/4 - Input EB`
                    );
                    $('#ebContainer').removeClass('d-none');
                    $('#eb').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);

                    // ✅ STEP 3: Jika EB sudah, input TPC
                } else if (tpc === null || tpc === undefined) {
                    $('#statusText').html(
                        `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>EB: <strong>${eb}</strong><br>Langkah 3/4 - Input TPC`
                    );
                    $('#tpcContainer').removeClass('d-none');
                    $('#tpc').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);

                    // ✅ STEP 4: Jika TPC sudah, input YM (terakhir)
                } else if (ym === null || ym === undefined) {
                    $('#statusText').html(
                        `Shift <strong>${shift}</strong> - Analis: <strong>${nama_analis}</strong><br>EB: <strong>${eb}</strong> | TPC: <strong>${tpc}</strong><br>Langkah 4/4 - Input YM (terakhir)`
                    );
                    $('#ymContainer').removeClass('d-none');
                    $('#ym').prop('disabled', false).focus();
                    $('#btnSave').prop('disabled', false);

                    // ✅ Semua sudah lengkap
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Data Lengkap',
                        text: 'Semua parameter analisa sudah diisi.'
                    }).then(() => {
                        window.location.href =
                            "{{ route('analisa.blending-awal-mikro.show', '') }}/" +
                            {{ $blending->productionBatch->id }};
                    });
                }
            }

            loadBlendingData();

            $('#form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('analisa.blending-awal-mikro.update') }}",
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
                            loadBlendingData();
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
