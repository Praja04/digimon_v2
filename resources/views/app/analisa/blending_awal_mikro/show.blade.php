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
                                                <h4>{{ $productionBatch->po_number }} (Nomor PO)</h4>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><a href="#"
                                                            class="text-primary d-block">{{ Session::get('username') }}</a>
                                                    </div>
                                                    <div class="vr"></div>

                                                    <div class="text-muted">Tanggal Produksi : <span
                                                            class="text-body fw-medium">{{ $productionBatch->date }}</span>
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
                                                            <h5 class="mb-0">{{ $productionBatch->variant }}</h5>
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
                                                            <h5 class="mb-0">{{ $productionBatch->batch_range }}</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end row -->

                                        <div class="mt-4 text-muted">
                                            <h5 class="fs-14">Description :</h5>
                                            <p>{{ $productionBatch->description ?? '-' }}</p>
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
                                <div class="table-responsive table-card mb-4">
                                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th>Nomor PO</th>
                                                <th>Batch Range</th>
                                                <th>No Blending</th>
                                                <th>Volume</th>
                                                <th>Nama Analis</th>
                                                <th>Shift Analis</th>
                                                <th>EB</th>
                                                <th>TPC</th>
                                                <th>YM</th>
                                                <th>Hasil</th>
                                                @if (auth()->user()->role == 'Analis Mikro')
                                                    <th>Aksi</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($productionBatch->blendingAfterAdjustMikro as $blending)
                                                <tr>
                                                    <td>{{ $productionBatch->po_number }}</td>
                                                    <td>{{ $blending->batch_range }}</td>
                                                    <td>{{ $blending->nomor_blending }}</td>
                                                    <td>{{ $blending->volume }}</td>
                                                    <td>{{ $blending->nama_analis ?? '-' }}</td>
                                                    <td>{{ $blending->shift ? 'Shift ' . $blending->shift : '-' }}
                                                    </td>
                                                    <td>{{ $blending->eb ?? '-' }}</td>
                                                    <td>{{ $blending->tpc ?? '-' }}</td>
                                                    <td>{{ $blending->ym ?? '-' }}</td>
                                                    <td>
                                                        @if ($blending->hasil === 'OK')
                                                            <span class="badge bg-success">OK</span>
                                                        @elseif ($blending->hasil === 'NOT OK')
                                                            <span class="badge bg-danger">NOT OK</span>
                                                        @elseif ($blending->hasil === 'PENDING')
                                                            <span class="badge bg-warning text-dark">PENDING</span>
                                                        @else
                                                            <span class="badge bg-secondary">-</span>
                                                        @endif
                                                    </td>
                                                    @if (auth()->user()->role == 'Analis Mikro')
                                                        <td>
                                                            @if (is_null($blending->ym))
                                                                <button class="btn btn-sm btn-primary" id="btnAnalisa"
                                                                    blending-id="{{ $blending->id }}">Input
                                                                    Analisa</button>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success">
                                                                    <i class="ri-check-line"></i> Lengkap
                                                                </span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada data tersedia.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal input GGA tunggal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Input Data Analisa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none error-alert"></div>
                        <input type="hidden" name="id" id="id">

                        <!-- ✅ Info Status -->
                        <div class="mb-3">
                            <div class="alert alert-info" id="statusInfo">
                                <strong>Status:</strong> <span id="statusText"></span>
                            </div>
                        </div>

                        <!-- EB Field -->
                        <div id="ebContainer" class="mb-3 d-none">
                            <label class="form-label">EB <span style="color: red;">*</span></label>
                            <input type="text" name="eb" id="eb" class="form-control comma-input"
                                placeholder="Masukkan nilai EB">
                            <small class="text-danger errorEb"></small>
                        </div>

                        <!-- TPC Field -->
                        <div id="tpcContainer" class="mb-3 d-none">
                            <label class="form-label">TPC <span style="color: red;">*</span></label>
                            <input type="text" name="tpc" id="tpc" class="form-control comma-input"
                                placeholder="Masukkan nilai TPC">
                            <small class="text-danger errorTpc"></small>
                        </div>

                        <!-- YM Field -->
                        <div id="ymContainer" class="mb-3 d-none">
                            <label class="form-label">YM <span style="color: red;">*</span></label>
                            <input type="text" name="ym" id="ym" class="form-control comma-input"
                                placeholder="Masukkan nilai YM">
                            <small class="text-danger errorYm"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                    </div>
                </div>
            </form>
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

            $('body').on('click', '#btnAnalisa', function() {
                let blendingId = $(this).attr('blending-id');

                // Reset form
                $('#form')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
                $('#ebContainer, #tpcContainer, #ymContainer').addClass('d-none');

                // Set ID
                $('#id').val(blendingId);

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.blending-awal-mikro.getBlendingData') }}",
                    data: {
                        id: blendingId
                    },
                    dataType: "json",
                    success: function(response) {
                        currentBlendingData = response.data;
                        showNextField();
                        $('#modal').modal('show');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal mengambil data blending.'
                        });
                    }
                });
            });

            function showNextField() {
                let eb = currentBlendingData.eb;
                let tpc = currentBlendingData.tpc;
                let ym = currentBlendingData.ym;

                // Sembunyikan semua field dulu
                $('#ebContainer, #tpcContainer, #ymContainer').addClass('d-none');
                $('#eb, #tpc, #ym').val('').prop('disabled', true);

                // ✅ Logika urutan: EB → TPC → YM (cek null/undefined secara eksplisit)
                if (eb === null || eb === undefined) {
                    // Jika EB belum diisi
                    $('#modalTitle').text('Input Data Analisa - EB');
                    $('#statusText').text('Langkah 1/3 - Input EB terlebih dahulu');
                    $('#ebContainer').removeClass('d-none');
                    $('#eb').prop('disabled', false).focus();

                } else if (tpc === null || tpc === undefined) {
                    // Jika EB sudah, tapi TPC belum
                    $('#modalTitle').text('Input Data Analisa - TPC');
                    $('#statusText').html(`EB sudah diisi: <strong>${eb}</strong><br>Langkah 2/3 - Input TPC`);
                    $('#tpcContainer').removeClass('d-none');
                    $('#tpc').prop('disabled', false).focus();

                } else if (ym === null || ym === undefined) {
                    // Jika EB & TPC sudah, tapi YM belum
                    $('#modalTitle').text('Input Data Analisa - YM');
                    $('#statusText').html(
                        `EB: <strong>${eb}</strong> | TPC: <strong>${tpc}</strong><br>Langkah 3/3 - Input YM (terakhir)`
                    );
                    $('#ymContainer').removeClass('d-none');
                    $('#ym').prop('disabled', false).focus();

                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Data Lengkap',
                        text: 'Semua parameter analisa sudah diisi.'
                    });
                    $('#modal').modal('hide');
                }
            }

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
                        $('#modal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            window.location.reload();
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
