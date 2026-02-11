@extends('layouts.component.main')
@section('title', 'Detail Monitoring Storage')
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
                                        href="{{ route('monitoring-storage-kimia.index') }}">Monitoring Storage</a>
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
                                                            Input Monitoring Storage
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
                                                        <a class="nav-link active" id="nav-monitoring-storage-kimia-tab"
                                                            data-bs-toggle="tab" href="#nav-monitoring-storage-kimia"
                                                            role="tab" aria-controls="nav-monitoring-storage-kimia"
                                                            aria-selected="true">Monitoring Storage Kimia</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="nav-monitoring-storage-mikro-tab"
                                                            data-bs-toggle="tab" href="#nav-monitoring-storage-mikro"
                                                            role="tab" aria-controls="nav-monitoring-storage-mikro"
                                                            aria-selected="true">Monitoring Storage Mikro</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                            <div class="tab-content border border-top-0 p-4"
                                                id="nav-monitoring-storage-kimia-content">
                                                <div class="tab-pane fade show active" id="nav-monitoring-storage-kimia"
                                                    role="tabpanel" aria-labelledby="nav-monitoring-storage-kimia-tab">
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
                                                                @forelse ($productionBatch->MonitoringStorageKimia as $storageKimia)
                                                                    @php
                                                                        // Tentukan class berdasarkan disposition
                                                                        $dispositionUpper = strtoupper(
                                                                            $storageKimia->status ?? '',
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
                                                                            @if ($storageKimia->revisi != null)
                                                                                {{ $storageKimia->batch_range }}

                                                                                <span class="badge {{ $badgeClass }} ms-1"
                                                                                    title="Revisi ke-{{ $storageKimia->revisi }}">
                                                                                    Rev. {{ $storageKimia->revisi }}
                                                                                </span>
                                                                            @else
                                                                                {{ $storageKimia->batch_range }}
                                                                            @endif

                                                                            @if ($storageKimia->additional_batch_info)
                                                                                @foreach ($storageKimia->additional_batch_info as $relasi)
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
                                                                                data-bs-target="#qrModalKimia{{ $storageKimia->id }}">
                                                                                QR Code {{ $storageKimia->id }}
                                                                            </button>

<!-- Modal QR Code -->
<div class="modal fade" id="qrModalKimia{{ $storageKimia->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title">QR Code Monitoring Storage Kimia #{{ $storageKimia->id }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3" id="qrPrintKimiaArea{{ $storageKimia->id }}">
                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('analisa.monitoring-storage-kimia.show_batch', $storageKimia->id), 'QRCODE') }}"
                    alt="QR" class="img-fluid mb-2" style="max-width:180px;">
                <div class="small text-muted">
                    MONITORING-STORAGE-KIMIA/{{ $productionBatch->po_number }}/{{ $productionBatch->date }}/{{ $storageKimia->id }}
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                <button onclick="printQR('qrPrintKimiaArea{{ $storageKimia->id }}')" class="btn btn-sm btn-primary">Cetak</button>
            </div>
        </div>
    </div>
</div>
                                                                        </td>
                                                                        <td>{{ $storageKimia->storage ?? '-' }}</td>
                                                                        <td>{{ $storageKimia->status ?? '-' }}</td>
                                                                        <td>
                                                                            {{ $storageKimia->disposition }}
                                                                            @if (in_array($storageKimia->disposition, ['Adjustment', 'Resampling', 'Leveling', 'Jalan Bareng']) &&
                                                                                    $storageKimia->revisi == null &&
                                                                                    $storageKimia->not_standard == true)
                                                                                <button class="btn btn-sm btn-warning"
                                                                                    id="btnRevisi"
                                                                                    data-id="{{ $storageKimia->id }}"
                                                                                    data-batch="{{ $storageKimia->batch_range }}"
                                                                                    data-po="{{ $storageKimia->production_batch_id }}"
                                                                                    data-disposition="{{ $storageKimia->disposition }}">
                                                                                    ❗
                                                                                </button>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($storageKimia->revisi != null)
                                                                                Revisi Ke-{{ $storageKimia->revisi }}
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

                                                <div class="tab-pane fade show " id="nav-monitoring-storage-mikro"
                                                    role="tabpanel" aria-labelledby="nav-monitoring-storage-mikro-tab">
                                                    <div class="table-responsive">
                                                        <table class="table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Batch Range</th>
                                                                    <th>QR Code (URL)</th>
                                                                    <th>Storage</th>
                                                                    <th>Hasil</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($productionBatch->monitoringStorageMikro as $storageMikro)
                                                                    @php
                                                                        // Tentukan class berdasarkan disposition
                                                                        $dispositionUpper = strtoupper(
                                                                            $storageMikro->status ?? '',
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
                                                                            @if ($storageMikro->revisi != null)
                                                                                {{ $storageMikro->batch_range }}

                                                                                <span
                                                                                    class="badge {{ $badgeClass }} ms-1"
                                                                                    title="Revisi ke-{{ $storageMikro->revisi }}">
                                                                                    Rev. {{ $storageMikro->revisi }}
                                                                                </span>
                                                                            @else
                                                                                {{ $storageMikro->batch_range }}
                                                                            @endif

                                                                            @if ($storageMikro->additional_batch_info)
                                                                                @foreach ($storageMikro->additional_batch_info as $relasi)
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
                                                                                data-bs-target="#qrModalMikro{{ $storageMikro->id }}">
                                                                                QR Code {{ $storageMikro->id }}
                                                                            </button>

                                                                            <!-- Modal Besar -->
                                                                            <div class="modal fade"
                                                                                id="qrModalMikro{{ $storageMikro->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="qrModalMikroLabel{{ $storageMikro->id }}"
                                                                                aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-sm">
        <div class="modal-header bg-light py-2">
            <h6 class="modal-title">QR Code Monitoring Storage Mikro #{{ $storageMikro->id }}</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center p-3" id="qrPrintMikroArea{{ $storageMikro->id }}">
            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('analisa.monitoring-storage-mikro.show_batch', $storageMikro->id), 'QRCODE') }}"
                alt="QR" class="img-fluid mb-2" style="max-width:180px;">
            <div class="small text-muted">
                MONITORING-STORAGE-MIKRO/{{ $productionBatch->po_number }}/{{ $productionBatch->date }}/{{ $storageMikro->id }}
            </div>
        </div>
        <div class="modal-footer bg-light py-2">
            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
            <button onclick="printQR('qrPrintMikroArea{{ $storageMikro->id }}')" class="btn btn-sm btn-primary">Cetak</button>
        </div>
    </div>
</div>
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ $storageMikro->storage ?? '-' }}</td>
                                                                        <td>{{ $storageMikro->hasil ?? '-' }}</td>
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
                        <h5 class="modal-title">Input Monitoring Storage Kimia</h5>
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
                            <input type="text" name="nomor_blending" id="nomor_blending" class="form-control">
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

        function populateBatchOptions() {
            const $start = $('#batch_range');
            $start.empty();

            if (!validGgasBatches || validGgasBatches.length === 0) {
                $start.append('<option disabled>Belum ada Batch yang lolos Monitoring Pasteurisasi(Release)</option>');
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
                url: "{{ route('monitoring-storage-kimia.getLastRevisi') }}",
                data: {
                    production_batch_id: poId,
                    batch_range: batch
                },
                dataType: "json",
                success: function(res) {
                    // Tampilkan nomor batch dan revisi
                    $('#batch_number_blending').val(batch);
                    $('#revisi_blending').val(res.revisi);
                    $('#no_blending_revisi').val(res.nomor_blending);
                    $('#volume_revisi').val(res.volume);
                    $('#storage_revisi').val(res.storage);


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
                            url: "{{ route('monitoring-storage-kimia.getAvailableAdditionalBatch') }}",
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
                            url: "{{ route('monitoring-storage-kimia.getJalanBareng') }}",
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
            const printArea = document.getElementById(id);

            if (!printArea) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Area print tidak ditemukan'
                });
                return;
            }

            const qrImage = printArea.querySelector('img');
            const qrLabel = printArea.querySelector('.small.text-muted');

            if (!qrImage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'QR Code tidak ditemukan'
                });
                return;
            }

            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

            if (isMobile) {
                printQRMobile(qrImage, qrLabel);
            } else {
                printQRDesktop(qrImage, qrLabel);
            }
        }

        function printQRDesktop(qrImage, qrLabel) {
            const printWindow = window.open('', '_blank', 'width=300,height=400');

            if (!printWindow) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pop-up Diblokir',
                    text: 'Mohon izinkan pop-up untuk print.'
                });
                return;
            }

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Print QR</title>
                    <style>
                        @page {
                            size: 58mm auto;
                            margin: 0;
                        }
                        
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        
                        body {
                            width: 58mm;
                            margin: 0 auto;
                            padding: 5mm 3mm;
                            font-family: Arial, sans-serif;
                            background: white;
                        }
                        
                        .container {
                            text-align: center;
                            width: 100%;
                        }
                        
                        .qr-image {
                            width: 45mm;
                            height: 45mm;
                            display: block;
                            margin: 0 auto 3mm auto;
                        }
                        
                        .qr-label {
                            font-size: 8pt;
                            color: #000;
                            word-wrap: break-word;
                            line-height: 1.3;
                        }
                        
                        @media print {
                            body {
                                padding: 2mm;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <img src="${qrImage.src}" alt="QR" class="qr-image">
                        <div class="qr-label"><strong>${qrLabel ? qrLabel.textContent.trim() : ''}</strong></div>
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();

            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.focus();
                    printWindow.print();
                    setTimeout(function() {
                        printWindow.close();
                    }, 500);
                }, 250);
            };
        }

        function printQRMobile(qrImage, qrLabel) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = 220;
            canvas.height = 280;

            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                const qrSize = 170;
                const qrX = (canvas.width - qrSize) / 2;
                const qrY = 10;
                ctx.drawImage(img, qrX, qrY, qrSize, qrSize);

                ctx.fillStyle = 'black';
                ctx.font = 'bold 10px Arial';
                ctx.textAlign = 'center';
                const labelText = qrLabel ? qrLabel.textContent.trim() : '';

                const maxWidth = 200;
                const lineHeight = 14;
                const words = labelText.split('/');
                let line = '';
                let y = qrY + qrSize + 20;

                words.forEach((word, index) => {
                    if (index > 0) line += '/';
                    const testLine = line + word;
                    const metrics = ctx.measureText(testLine);

                    if (metrics.width > maxWidth && index > 0) {
                        ctx.fillText(line, canvas.width / 2, y);
                        line = word;
                        y += lineHeight;
                    } else {
                        line = testLine;
                    }
                });
                ctx.fillText(line, canvas.width / 2, y);

                canvas.toBlob(function(blob) {
                    if (navigator.share && isMobileDevice()) {
                        const file = new File([blob], 'qr-code.png', {
                            type: 'image/png'
                        });

                        navigator.share({
                            files: [file],
                            title: 'Print QR Code',
                            text: 'QR Code untuk print'
                        }).catch(err => {
                            fallbackPrint(blob);
                        });
                    } else {
                        fallbackPrint(blob);
                    }
                }, 'image/png');
            };

            img.onerror = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat QR code'
                });
            };

            img.src = qrImage.src;
        }

        function fallbackPrint(blob) {
            const url = URL.createObjectURL(blob);
            const printWindow = window.open(url, '_blank');

            if (!printWindow) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pop-up Diblokir',
                    text: 'Mohon izinkan pop-up untuk print.'
                });
                return;
            }

            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                }, 500);
            };
        }

        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        $('#batch_range').on('change', function() {
            const selectedBatch = $(this).val();

            if (!selectedBatch) {
                $('#nomor_blending').val('');
                $('#volume').val('');
                $('#storage').val('');
                return;
            }

            $.ajax({
                type: "GET",
                url: "{{ route('monitoring-storage-kimia.getBatchData') }}",
                data: {
                    production_batch_id: "{{ $productionBatch->id }}",
                    batch_range: selectedBatch
                },
                dataType: "json",
                success: function(response) {
                    Swal.close();

                    if (response.status === 'success' && response.data) {
                        const data = response.data;

                        $('#nomor_blending').val(data.nomor_blending || '');

                        if (data.volume_after_cooling) {
                            const formattedVolume = parseFloat(data.volume_after_cooling)
                                .toFixed(2)
                                .replace('.', ',');
                            $('#volume').val(formattedVolume);
                        } else {
                            $('#volume').val('');
                        }

                        if (data.storage) {
                            $('#storage').val(data.storage);
                        } else {
                            $('#storage').val('');
                        }
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Tidak Lengkap',
                            text: 'Beberapa data mungkin perlu diisi manual',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    let errorMessage = 'Gagal memuat data batch dari Portal Produksi';
                    let errorTitle = 'Kesalahan';

                    if (xhr.status === 404) {
                        errorTitle = 'Data Tidak Ditemukan';
                        errorMessage = 'Data monitoring pasteurisasi tidak ditemukan untuk batch ini.';
                    } else if (xhr.status === 401) {
                        errorTitle = 'Autentikasi Gagal';
                        errorMessage = 'Koneksi ke Portal Produksi gagal. Hubungi administrator.';
                    } else if (xhr.status === 0 || xhr.statusText === 'timeout') {
                        errorTitle = 'Timeout';
                        errorMessage = 'Koneksi ke Portal Produksi timeout. Coba lagi.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: errorTitle,
                        html: `<p>${errorMessage}</p><small>Silakan isi form secara manual atau coba lagi.</small>`,
                        confirmButtonText: 'OK'
                    });

                    $('#nomor_blending').val('');
                    $('#volume').val('');
                    $('#storage').val('');
                }
            });
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            populateBatchOptions();

            if ($('#batch_range').val()) {
                $('#batch_range').trigger('change');
            }

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-storage-kimia.store') }}",
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
                                $('.errorNomorBlending').html(errors.nomor_blending
                                    .join(
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
                    url: "{{ route('monitoring-storage-kimia.storeRevisi') }}",
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
                                $('.errorNoBlendingRevisi').html(errors
                                    .no_blending_revisi.join(
                                        '<br>'));
                            }

                            if (errors.volume_revisi) {
                                $('#volume_revisi').addClass('is-invalid');
                                $('.errorVolumeRevisi').html(errors.volume_revisi
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
