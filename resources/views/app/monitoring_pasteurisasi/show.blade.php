@extends('layouts.component.main')
@section('title', 'Detail Monitoring Pasteurisasi')
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
                                        href="{{ route('monitoring-pasteurisasi.index') }}">Monitoring Pasteurisasi</a>
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
                                                <h4>{{ $productionBatch->po_number }} (Nomor PO)</h4>
                                                <div class="hstack gap-3 flex-wrap">
                                                    <div><a href="#"
                                                            class="text-primary d-block">{{ Session::get('username') }}</a>
                                                    </div>
                                                    <div class="vr"></div>

                                                    <div class="text-muted">Tanggal Produksi : <span
                                                            class="text-body fw-medium">{{ $productionBatch->date }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#inputModal">
                                                            Input Monitoring Pasteurisasi
                                                        </button>
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

                                        <div class="product-content mt-5">
                                            <h5 class="fs-14 mb-3">Generate Barcode :</h5>
                                            <nav>
                                                <ul class="nav nav-tabs nav-tabs-custom nav-success" id="nav-tab"
                                                    role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="nav-monitoring-pasteurisasi-tab"
                                                            data-bs-toggle="tab" href="#nav-monitoring-pasteurisasi"
                                                            role="tab" aria-controls="nav-monitoring-pasteurisasi"
                                                            aria-selected="true">Monitoring Pasteurisasi</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                            <div class="tab-content border border-top-0 p-4"
                                                id="nav-monitoring-pasteurisasi-content">
                                                <div class="tab-pane fade show active" id="nav-monitoring-pasteurisasi"
                                                    role="tabpanel" aria-labelledby="nav-monitoring-pasteurisasi-tab">
                                                    <div class="table-responsive">
                                                        <table class="table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Batch Range</th>
                                                                    <th>QR Code (URL)</th>
                                                                    <th>Storage</th>
                                                                    <th>Status</th>
                                                                    <th>Disposisi</th>
                                                                    <th>Revisi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($productionBatch->MonitoringPasteurisasi as $pasteurisasi)
                                                                    @php
                                                                        // Tentukan class berdasarkan disposition
                                                                        $dispositionUpper = strtoupper(
                                                                            $pasteurisasi->status ?? '',
                                                                        );
                                                                        $rowClass = match ($dispositionUpper) {
                                                                            'NOT OK' => 'table-danger',
                                                                            'ADJUSTMENT' => 'table-warning',
                                                                            default => '',
                                                                        };

                                                                        $badgeClass = match ($dispositionUpper) {
                                                                            'NOT OK' => 'bg-danger',
                                                                            'ADJUSTMENT' => 'bg-warning text-dark',
                                                                            'OK' => 'bg-success',
                                                                            default => 'bg-secondary',
                                                                        };
                                                                    @endphp
                                                                    <tr class="{{ $rowClass }}">
                                                                        <td>
                                                                            @if ($pasteurisasi->revisi != null)
                                                                                {{ $pasteurisasi->batch_range }} ❗
                                                                            @else
                                                                                {{ $pasteurisasi->batch_range }}
                                                                            @endif

                                                                            @if ($pasteurisasi->additional_batch_info)
                                                                                @foreach ($pasteurisasi->additional_batch_info as $relasi)
                                                                                    <span
                                                                                        class="badge bg-info">{{ $relasi->batch }}</span>
                                                                                @endforeach
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <!-- Tombol untuk buka modal -->
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-primary"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#qrModal{{ $pasteurisasi->id }}">
                                                                                QR Code {{ $pasteurisasi->id }}
                                                                            </button>

                                                                            <!-- Modal Besar -->
                                                                            <div class="modal fade"
                                                                                id="qrModal{{ $pasteurisasi->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="qrModalLabel{{ $pasteurisasi->id }}"
                                                                                aria-hidden="true">
                                                                                <div
                                                                                    class="modal-dialog modal-dialog-centered modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header py-2">
                                                                                            <h5 class="modal-title"
                                                                                                id="qrModalLabel{{ $pasteurisasi->id }}">
                                                                                                QR Code - Monitoring
                                                                                                Pasteurisasi</h5>
                                                                                            <button type="button"
                                                                                                class="btn-close btn-sm"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body text-center"
                                                                                            id="qrPrintArea{{ $pasteurisasi->id }}">
                                                                                            <div
                                                                                                style="display: inline-block;">
                                                                                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('analisa.monitoring-pasteurisasi.show_batch', $pasteurisasi->id), 'QRCODE') }}"
                                                                                                    alt="QR Code">
                                                                                            </div>
                                                                                            <p>Monitoring
                                                                                                Pasteurisasi/{{ $productionBatch->po_number }}/{{ $productionBatch->variant }}/{{ $pasteurisasi->batch_range }}
                                                                                            </p>
                                                                                        </div>
                                                                                        <div
                                                                                            class="modal-footer justify-content-center py-2">
                                                                                            <button type="button"
                                                                                                class="btn btn-sm btn-dark"
                                                                                                data-bs-dismiss="modal">Tutup</button>
                                                                                            <button
                                                                                                onclick="printQR('qrPrintArea{{ $pasteurisasi->id }}')"
                                                                                                class="btn btn-sm btn-primary">Cetak</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ $pasteurisasi->storage ?? '-' }}</td>
                                                                        <td>{{ $pasteurisasi->status ?? '-' }}</td>
                                                                        <td>
                                                                            {{ $pasteurisasi->disposition }}
                                                                            @if (in_array($pasteurisasi->disposition, ['Adjustment', 'Resampling', 'Leveling', 'Jalan Bareng']) &&
                                                                                    $pasteurisasi->revisi == null &&
                                                                                    $pasteurisasi->not_standard == true)
                                                                                <button class="btn btn-sm btn-warning"
                                                                                    id="btnRevisi"
                                                                                    data-id="{{ $pasteurisasi->id }}"
                                                                                    data-batch="{{ $pasteurisasi->batch_range }}"
                                                                                    data-po="{{ $pasteurisasi->production_batch_id }}"
                                                                                    data-disposition="{{ $pasteurisasi->disposition }}">
                                                                                    ❗
                                                                                </button>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($pasteurisasi->revisi != null)
                                                                                Revisi Ke-{{ $pasteurisasi->revisi }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr class="text-center">
                                                                        <td colspan="7">Tidak ada data tersedia.</td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Monitoring Pasteurisasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="production_batch_id" value="{{ $productionBatch->id }}">

                        <div class="mb-3">
                            <label for="batch_range" class="form-label">Batch <span style="color: red">*</span></label>
                            <select class="form-control" name="batch_range" id="batch_range"></select>
                            <small class="text-danger errorBatchRange"></small>
                        </div>

                        <div class="mb-3">
                            <label for="nomor_blending" class="form-label">Nomor Blending <span
                                    style="color: red">*</span></label>
                            <input type="number" name="nomor_blending" id="nomor_blending" class="form-control">
                            <small class="text-danger errorNomorBlending"></small>
                        </div>

                        <div class="mb-3">
                            <label for="volume" class="form-label">Volume <span style="color: red">*</span></label>
                            <input type="text" name="volume" id="volume" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorVolume"></small>
                        </div>
                        <div class="mb-3">
                            <label for="storage" class="form-label">Storage</label>
                            <select name="storage" id="storage" class="form-control">
                                <option value="">-- Pilih Storage --</option>
                                <optgroup label="A">
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="A3">A3</option>
                                    <option value="A4">A4</option>
                                    <option value="A5">A5</option>
                                </optgroup>
                                <optgroup label="B">
                                    <option value="B1">B1</option>
                                    <option value="B2">B2</option>
                                    <option value="B3">B3</option>
                                    <option value="B4">B4</option>
                                    <option value="B5">B5</option>
                                </optgroup>
                                <optgroup label="C">
                                    <option value="C1">C1</option>
                                    <option value="C2">C2</option>
                                    <option value="C3">C3</option>
                                    <option value="C4">C4</option>
                                    <option value="C5">C5</option>
                                </optgroup>
                                <optgroup label="D">
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="D5">D5</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Generate Ulang -->
    <div class="modal fade" id="modalRevisi" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="formRevisi">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Revisi Batch</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_revisi_blending" id="id_revisi_blending">
                        <input type="hidden" name="id_productbatch_revisi_blending"
                            id="id_productbatch_revisi_blending">
                        <input type="hidden" name="disposition_revisi_blending" id="disposition_revisi_blending">
                        <input type="hidden" id="revisi_additional_batch_po_id" name="revisi_additional_batch_po_id">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="batch_number">Nomor Batch</label>
                                <input type="text" name="batch_number_blending" id="batch_number_blending"
                                    class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="revisi">Revisi Ke- </label>
                                <input type="text" name="revisi_blending" id="revisi_blending" class="form-control"
                                    readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Blending <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" name="no_blending_revisi"
                                    id="no_blending_revisi">
                                <small class="text-danger errorNoBlendingRevisi"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Volume After <span style="color: red;">*</span></label>
                                <input type="text" class="form-control comma-input" placeholder="Contoh: 0,00"
                                    name="volume_revisi" id="volume_revisi">
                                <small class="text-danger errorVolumeRevisi"></small>
                            </div>
                        </div>

                        <!-- Storage (full width) -->
                        <div class="mb-3">
                            <label for="storage_revisi" class="form-label">Storage (Optional)</label>
                            <select name="storage_revisi" id="storage_revisi" class="form-control">
                                <option value="">-- Pilih Storage --</option>
                                <optgroup label="A">
                                    <option value="A1">A1</option>
                                    <option value="A2">A2</option>
                                    <option value="A3">A3</option>
                                    <option value="A4">A4</option>
                                    <option value="A5">A5</option>
                                </optgroup>
                                <optgroup label="B">
                                    <option value="B1">B1</option>
                                    <option value="B2">B2</option>
                                    <option value="B3">B3</option>
                                    <option value="B4">B4</option>
                                    <option value="B5">B5</option>
                                </optgroup>
                                <optgroup label="C">
                                    <option value="C1">C1</option>
                                    <option value="C2">C2</option>
                                    <option value="C3">C3</option>
                                    <option value="C4">C4</option>
                                    <option value="C5">C5</option>
                                </optgroup>
                                <optgroup label="D">
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="D5">D5</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="additional_batch_group">
                            <label class="form-label">Pilih Batch Tambahan</label>
                            <select name="additional_batch" id="additional_batch" class="form-control">
                                <option value="">-- Pilih Batch --</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button id="save_revisi_blending" type="submit" class="btn btn-primary">Generate Ulang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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

        const allBatches = @json($filteredBatchGroups);
        const validGgasBatches = @json($filteredBatchGroups);

        // Isi select option hanya dengan batch yang valid
        function populateBatchOptions() {
            const $start = $('#batch_range');
            $start.empty();

            if (!validGgasBatches || validGgasBatches.length === 0) {
                $start.append('<option disabled>Belum ada Batch yang lolos Monitoring Turun Blending(Release)</option>');
                return;
            }

            validGgasBatches.forEach(batch => {
                $start.append(`<option value="${batch}">${batch}</option>`);
            });

            console.log('Options added:', $start.find('option').length);
        }

        $('body').on('click', '#btnRevisi', function() {
            $('#formRevisi')[0].reset();
            $('.form-control').removeClass('is-invalid');
            $('.text-danger').html('');

            let poId = $(this).data('po');
            let id_blending = $(this).data('id');
            let batch = $(this).data('batch');
            let disposition = $(this).data('disposition');

            $('#id_revisi_blending').val(id_blending);
            $('#id_productbatch_revisi_blending').val(poId);
            $('#batch_range_revisi_blending').val(batch);
            $('#disposition_revisi_blending').val(disposition);
            $('#revisi_additional_batch_po_id').val('');

            // Reset semua field batch tambahan
            $('#additional_batch_group').addClass('d-none');
            $('#additional_batch_group_2').addClass('d-none');
            $('#additional_batch').empty();

            // Ambil nomor revisi terakhir
            $.ajax({
                type: "GET",
                url: "{{ route('monitoring-pasteurisasi.getLastRevisi') }}",
                data: {
                    production_batch_id: poId,
                    batch_range: batch
                },
                dataType: "json",
                success: function(res) {
                    // Tampilkan nomor batch dan revisi
                    $('#batch_number_blending').val(batch);
                    $('#revisi_blending').val(res.revisi);

                    // Handle berdasarkan disposition
                    if (disposition === 'Leveling') {
                        // Tampilkan batch tambahan 1 dan 2
                        $('#additional_batch_group').removeClass('d-none');
                        $('#additional_batch_group_2').removeClass('d-none');

                        $('#additional_batch').append(
                            '<option value="">-- Pilih Batch --</option>');

                        // Load available batches
                        $.ajax({
                            type: "GET",
                            url: "{{ route('monitoring-pasteurisasi.getAvailableAdditionalBatch') }}",
                            data: {
                                production_batch_id: poId,
                                exclude_batch: batch
                            },
                            dataType: "json",
                            success: function(batchRes) {
                                batchRes.data.forEach(function(batchItem) {
                                    let option =
                                        `<option data-id_additional_po="${batchItem.po_id}" value="${batchItem.batch_range}">Batch ${batchItem.batch_range} (PO ${batchItem.po_number})</option>`;
                                    $('#additional_batch').append(option);
                                    $('#additional_batch_2').append(option);
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text: 'Gagal memuat data batch tambahan.'
                                });
                            }
                        });

                    } else if (disposition === 'Jalan Bareng') {
                        // Tampilkan batch tambahan 1 dan 2
                        $('#additional_batch_group').removeClass('d-none');
                        $('#additional_batch_group_2').removeClass('d-none');

                        $('#additional_batch').append(
                            '<option value="">-- Pilih Batch --</option>');

                        // Load jalan bareng batches
                        $.ajax({
                            type: "GET",
                            url: "{{ route('monitoring-pasteurisasi.getJalanBareng') }}",
                            data: {
                                production_batch_id: poId
                            },
                            dataType: "json",
                            success: function(batchRes) {
                                let seen = new Set();
                                batchRes.data.forEach(function(batchItem) {
                                    let value =
                                        `${batchItem.batch_range}-${batchItem.po_number}`;
                                    if (!seen.has(value)) {
                                        seen.add(value);
                                        let option =
                                            `<option data-id_additional_po="${batchItem.po_id}" value="${batchItem.batch_range}">Batch ${batchItem.batch_range} (PO ${batchItem.po_number})</option>`;
                                        $('#additional_batch').append(option);
                                        $('#additional_batch_2').append(option);
                                    }
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text: 'Gagal memuat data batch jalan bareng.'
                                });
                            }
                        });

                    } else {
                        // Disposition lain: sembunyikan batch tambahan
                        $('#additional_batch_group').addClass('d-none');
                        $('#additional_batch_group_2').addClass('d-none');
                        $('#id_productbatch_revisi_blending').val(poId);
                    }

                    $('#modalRevisi').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Gagal mengambil data revisi.'
                    });
                }
            });
        });

        function printQR(id) {
            const content = document.getElementById(id).innerHTML;
            const win = window.open('', '', 'height=600,width=600');
            win.document.write('<html><head><title>Print QR</title>');
            win.document.write('<style>body{text-align:center; font-size:12px;}</style>');
            win.document.write('</head><body>');
            win.document.write(content);
            win.document.write('</body></html>');
            win.document.close();
            win.focus();
            win.print();
            win.close();
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            populateBatchOptions();

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-pasteurisasi.store') }}",
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
                        $('#inputModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            window.location.reload()
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
                            $('#save').prop('disabled', false).text('Simpan');
                            return;
                        }

                        if (xhr.status === 422) {
                            let errors = response.errors;
                            if (errors.batch_start) {
                                $('#batch_start').addClass('is-invalid');
                                $('.errorBatchStart').html(errors.batch_start.join(
                                    '<br>'));
                            }
                            if (errors.batch_end) {
                                $('#batch_end').addClass('is-invalid');
                                $('.errorBatchEnd').html(errors.batch_end.join(
                                    '<br>'));
                            }
                            if (errors.nomor_blending) {
                                $('#nomor_blending').addClass('is-invalid');
                                $('.errorNomorBlending').html(errors.nomor_blending.join(
                                    '<br>'));
                            }

                            if (errors.volume) {
                                $('#volume').addClass('is-invalid');
                                $('.errorVolume').html(errors.volume.join('<br>'));
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

            $('#formRevisi').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-pasteurisasi.storeRevisi') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save_revisi_blending').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#save_revisi_blending').prop('disabled', false).text(
                            'Generate Ulang');
                    },
                    success: function(response) {
                        $('#modalRevisi').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(() => {
                            window.location.reload()
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
                            $('#save_revisi_blending').prop('disabled', false).text(
                                'Generate Ulang');
                            return;
                        }

                        if (xhr.status === 422) {
                            let errors = response.errors;
                            if (errors.no_blending_revisi) {
                                $('#no_blending_revisi').addClass('is-invalid');
                                $('.errorNoBlendingRevisi').html(errors.no_blending_revisi.join(
                                    '<br>'));
                            }

                            if (errors.volume_revisi) {
                                $('#volume_revisi').addClass('is-invalid');
                                $('.errorVolumeRevisi').html(errors.volume_revisi.join('<br>'));
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
