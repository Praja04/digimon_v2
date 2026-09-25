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
                                            <div class="col-lg-6 col-sm-6 mt-3">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-list-ordered"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Batch :</p>
                                                            <h5 class="mb-0">{{ $blending->batch_range }} @if ($blending->additionalBatches)
                                                                    @foreach ($blending->additionalBatches as $relasi)
                                                                        -{{ $relasi->batch }}
                                                                    @endforeach
                                                                @endif
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 mt-3">
                                                <div class="p-2 border border-dashed rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div
                                                                class="avatar-title rounded bg-transparent text-success fs-24">
                                                                <i class="ri-hashtag"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <p class="text-muted mb-1">Nomor Blending :</p>
                                                            <h5 class="mb-0">{{ $blending->nomor_blending }}</h5>
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
                                        <div class="col-lg-12 d-none" id="draftAlert">
                                            <div class="alert alert-warning mb-0">
                                                <i class="mdi mdi-content-save-outline me-1"></i>
                                                <strong>Draft ditemukan.</strong>
                                                Nilai sementara sebelumnya dimuat. Anda boleh lanjut menyimpan sementara atau langsung Simpan Final.
                                            </div>
                                        </div>
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
                                                        class="form-control comma-input"
                                                        placeholder="Masukkan Nama Analis"
                                                        oninput="this.value = this.value.toUpperCase();">
                                                    <small class="text-danger errorNamaAnalis"></small>
                                                </div>
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
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-warning" id="btnDraft">
                                            <i class="mdi mdi-content-save-outline me-1"></i>
                                            Simpan Sementara
                                        </button>

                                        <button type="submit" class="btn btn-success" id="btnFinal">
                                            <i class="mdi mdi-check-circle-outline me-1"></i>
                                            Simpan Final
                                        </button>
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
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
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

            function clearErrors() {
                $('.form-control').removeClass('is-invalid');
                $('.errorShiftAnalis, .errorNamaAnalis, .errorEb, .errorTpc, .errorYm').html('');
            }

            function showValidationErrors(errors) {
                if (!errors) {
                    return;
                }

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
            }

            function getErrorMessage(xhr, fallback) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    return xhr.responseJSON.message;
                }

                if (
                    xhr.responseText &&
                    xhr.responseText.trim().startsWith('<')
                ) {
                    return fallback + ' Server mengembalikan response non-JSON.';
                }

                return fallback;
            }

            function canInputByDay(baseTime, plusDay) {
                if (!baseTime) {
                    return true;
                }

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

            function showCurrentStep() {
                const step = currentBlendingData.current_step;
                const draft = currentBlendingData.draft || {};
                const shift = currentBlendingData.shift;
                const namaAnalis = currentBlendingData.nama_analis;
                const eb = currentBlendingData.eb;
                const tpc = currentBlendingData.tpc;
                const ym = currentBlendingData.ym;
                const baseTime = currentBlendingData.updated_at;

                $('#analisContainer, #ebContainer, #tpcContainer, #ymContainer').addClass('d-none');

                $('#shift_analis, #nama_analis, #eb, #tpc, #ym')
                    .val('')
                    .prop('disabled', true);

                $('#btnDraft, #btnFinal').prop('disabled', true);

                if (step === 'analis') {
                    $('#modalTitle').text('Input Data Analisa - Analis');
                    $('#statusText').html(
                        'Langkah 1/4 - Input <strong>Shift</strong> dan <strong>Nama Analis</strong>.'
                    );

                    $('#analisContainer').removeClass('d-none');

                    $('#shift_analis')
                        .val(draft.shift ?? '')
                        .prop('disabled', false);

                    $('#nama_analis')
                        .val(draft.nama_analis ?? '')
                        .prop('disabled', false);

                    $('#btnDraft, #btnFinal').prop('disabled', false);
                    return;
                }

                if (step === 'eb') {

                    if (!canInputByDay(baseTime, 1)) {
                        $('#statusText').html(
                            `EB dapat diinput mulai tanggal <strong>${formatDate(baseTime, 1)}</strong>`
                        );
                        return;
                    }

                    $('#modalTitle').text('Input Data Analisa - EB');
                    $('#statusText').html(
                        `Shift <strong>${shift}</strong> - Analis: <strong>${namaAnalis}</strong><br>` +
                        'Langkah 2/4 - Input EB.'
                    );

                    $('#ebContainer').removeClass('d-none');

                    $('#eb')
                        .val(draft.eb ?? '')
                        .prop('disabled', false)
                        .focus();

                    $('#btnDraft, #btnFinal').prop('disabled', false);
                    return;
                }

                if (step === 'tpc') {

                    if (!canInputByDay(baseTime, 3)) {
                        $('#statusText').html(
                            `TPC dapat diinput mulai tanggal <strong>${formatDate(baseTime, 3)}</strong>`
                        );
                        return;
                    }

                    $('#modalTitle').text('Input Data Analisa - TPC');
                    $('#statusText').html(
                        `EB final: <strong>${eb}</strong><br>` +
                        'Langkah 3/4 - Input TPC.'
                    );

                    $('#tpcContainer').removeClass('d-none');

                    $('#tpc')
                        .val(draft.tpc ?? '')
                        .prop('disabled', false)
                        .focus();

                    $('#btnDraft, #btnFinal').prop('disabled', false);
                    return;
                }

                if (step === 'ym') {

                    if (!canInputByDay(baseTime, 5)) {
                        $('#statusText').html(
                            `YM dapat diinput mulai tanggal <strong>${formatDate(baseTime, 5)}</strong>`
                        );
                        return;
                    }

                    $('#modalTitle').text('Input Data Analisa - YM');
                    $('#statusText').html(
                        `EB final: <strong>${eb}</strong> | TPC final: <strong>${tpc}</strong><br>` +
                        'Langkah 4/4 - Input YM.'
                    );

                    $('#ymContainer').removeClass('d-none');

                    $('#ym')
                        .val(draft.ym ?? '')
                        .prop('disabled', false)
                        .focus();

                    $('#btnDraft, #btnFinal').prop('disabled', false);
                    return;
                }

                $('#modalTitle').text('Data Analisa Lengkap');
                $('#statusText').html(
                    `EB: <strong>${eb}</strong> | TPC: <strong>${tpc}</strong> | YM: <strong>${ym}</strong>`
                );
            }

            function loadBlendingData(blendingId, showModal = false) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('analisa.blending-awal-mikro.getBlendingData') }}",
                    data: {
                        id: blendingId
                    },
                    dataType: 'json',

                    success: function(response) {
                        currentBlendingData = response.data;

                        if (currentBlendingData.has_draft) {
                            $('#draftAlert').removeClass('d-none');
                        } else {
                            $('#draftAlert').addClass('d-none');
                        }

                        showCurrentStep();

                        if (showModal) {
                            $('#modal').modal('show');
                        }
                    },

                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: getErrorMessage(
                                xhr,
                                'Gagal mengambil data blending.'
                            )
                        });
                    }
                });
            }


            const blendingId = $('#id').val();

            function reloadCurrentData() {
                $('#loadingContainer').removeClass('d-none');
                $('#statusContainer').addClass('d-none');

                loadBlendingData(blendingId, false);

                setTimeout(function() {
                    $('#loadingContainer').addClass('d-none');
                    $('#statusContainer').removeClass('d-none');
                }, 100);
            }

            /*
             * Override loader untuk tampilan batch:
             * status container ditampilkan setelah data berhasil.
             */
            function loadBatchData() {
                $('#loadingContainer').removeClass('d-none');
                $('#statusContainer').addClass('d-none');
                $('#draftAlert').addClass('d-none');
                $('#analisContainer, #ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#btnDraft, #btnFinal').prop('disabled', true);

                $.ajax({
                    type: 'GET',
                    url: "{{ route('analisa.blending-awal-mikro.getBlendingData') }}",
                    data: {
                        id: blendingId
                    },
                    dataType: 'json',

                    success: function(response) {
                        currentBlendingData = response.data;

                        $('#loadingContainer').addClass('d-none');
                        $('#statusContainer').removeClass('d-none');

                        if (currentBlendingData.has_draft) {
                            $('#draftAlert').removeClass('d-none');
                        } else {
                            $('#draftAlert').addClass('d-none');
                        }

                        showCurrentStep();
                    },

                    error: function(xhr) {
                        $('#loadingContainer').addClass('d-none');

                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: getErrorMessage(
                                xhr,
                                'Gagal mengambil data blending.'
                            )
                        });
                    }
                });
            }

            /*
             * SIMPAN SEMENTARA = OPSIONAL.
             * Field pada step aktif tetap wajib.
             * Draft tidak membuat step maju dan tidak kontak Production.
             */
            $('#btnDraft').on('click', function() {
                clearErrors();

                const button = $(this);

                $.ajax({
                    url: "{{ route('analisa.blending-awal-mikro.draft.store') }}",
                    type: 'POST',
                    data: $('#form').serialize(),
                    dataType: 'json',

                    beforeSend: function() {
                        button
                            .prop('disabled', true)
                            .html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

                        $('#btnFinal').prop('disabled', true);
                    },

                    complete: function() {
                        button.html(
                            '<i class="mdi mdi-content-save-outline me-1"></i> Simpan Sementara'
                        );
                    },

                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Draft Tersimpan',
                            text: response.message
                        }).then(function() {
                            loadBatchData();
                        });
                    },

                    error: function(xhr) {
                        const response = xhr.responseJSON;

                        $('#btnDraft, #btnFinal').prop('disabled', false);

                        if (response && response.errors) {
                            showValidationErrors(response.errors);
                        }

                        Swal.fire({
                            icon: xhr.status === 422 || xhr.status === 409 ? 'warning' : 'error',
                            title: 'Gagal Disimpan',
                            text: getErrorMessage(xhr, 'Draft gagal disimpan.')
                        });
                    }
                });
            });

            /*
             * SIMPAN FINAL boleh langsung dipakai tanpa Simpan Sementara.
             */
            $('#form').on('submit', function(e) {
                e.preventDefault();

                clearErrors();

                const form = $(this);

                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Final?',
                    text: 'Data pada langkah ini akan difinalkan dan proses akan lanjut ke langkah berikutnya.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan Final',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('analisa.blending-awal-mikro.update') }}",
                        type: 'POST',
                        data: form.serialize(),
                        dataType: 'json',

                        beforeSend: function() {
                            $('#btnDraft, #btnFinal').prop('disabled', true);

                            $('#btnFinal').html(
                                '<i class="mdi mdi-loading mdi-spin me-1"></i> Proses Final...'
                            );
                        },

                        complete: function() {
                            $('#btnFinal').html(
                                '<i class="mdi mdi-check-circle-outline me-1"></i> Simpan Final'
                            );
                        },

                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Simpan Final Berhasil',
                                text: response.message
                            }).then(function() {
                                if (response.current_step === 'complete') {
                                    window.location.href =
                                        "{{ route('analisa.blending-awal-mikro.show', '') }}/" +
                                        {{ $blending->productionBatch->id }};
                                    return;
                                }

                                loadBatchData();
                            });
                        },

                        error: function(xhr) {
                            const response = xhr.responseJSON;

                            $('#btnDraft, #btnFinal').prop('disabled', false);

                            if (response && response.errors) {
                                showValidationErrors(response.errors);
                            }

                            Swal.fire({
                                icon: xhr.status === 422 || xhr.status === 409 ? 'warning' : 'error',
                                title: 'Simpan Final Gagal',
                                text: getErrorMessage(
                                    xhr,
                                    'Data final gagal disimpan. Draft tetap aman.'
                                )
                            });
                        }
                    });
                });
            });

            loadBatchData();
        });
    </script>
@endsection
