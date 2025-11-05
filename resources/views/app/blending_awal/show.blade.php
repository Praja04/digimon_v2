@extends('layouts.component.main')
@section('title', 'Detail Blending Awal')
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
                                <li class="breadcrumb-item"><a href="{{ route('blending-awal.index') }}">Blending Awal</a>
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
                                                            Input Blending Awal
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
                                            <p>{{ $productionBatch->description }}</p>
                                        </div>

                                        <div class="product-content mt-5">
                                            <h5 class="fs-14 mb-3">Generate Barcode :</h5>
                                            <nav>
                                                <ul class="nav nav-tabs nav-tabs-custom nav-success" id="nav-tab"
                                                    role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="nav-blending-awal-kimia-tab"
                                                            data-bs-toggle="tab" href="#nav-blending-awal-kimia"
                                                            role="tab" aria-controls="nav-blending-awal-kimia"
                                                            aria-selected="true">Blending Awal</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="nav-blending-awal-mikro-tab"
                                                            data-bs-toggle="tab" href="#nav-blending-awal-mikro"
                                                            role="tab" aria-controls="nav-blending-awal-mikro"
                                                            aria-selected="false">Blending Mikro - Adjustment</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                            <div class="tab-content border border-top-0 p-4" id="nav-tabContent">
                                                <div class="tab-pane fade show active" id="nav-blending-awal-kimia"
                                                    role="tabpanel" aria-labelledby="nav-blending-awal-kimia-tab">
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
                                                                @forelse ($productionBatch->BlendingAwal as $blending)
                                                                    @php
                                                                        // Tentukan class berdasarkan disposition
                                                                        $dispositionUpper = strtoupper(
                                                                            $blending->status ?? '',
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
                                                                            @if ($blending->revisi != null)
                                                                                {{ $blending->batch_range }} ❗
                                                                            @else
                                                                                {{ $blending->batch_range }}
                                                                            @endif

                                                                            @if ($blending->additional_batch_info)
                                                                                @foreach ($blending->additional_batch_info as $relasi)
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
                                                                                data-bs-target="#qrModal{{ $blending->id }}">
                                                                                QR Code {{ $blending->id }}
                                                                            </button>

                                                                            <!-- Modal Besar -->
                                                                            <div class="modal fade"
                                                                                id="qrModal{{ $blending->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="qrModalLabel{{ $blending->id }}"
                                                                                aria-hidden="true">
                                                                                <div
                                                                                    class="modal-dialog modal-dialog-centered modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header py-2">
                                                                                            <h5 class="modal-title"
                                                                                                id="qrModalLabel{{ $blending->id }}">
                                                                                                QR Code - Blending Awal</h5>
                                                                                            <button type="button"
                                                                                                class="btn-close btn-sm"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body text-center"
                                                                                            id="qrPrintArea{{ $blending->id }}">
                                                                                            <div
                                                                                                style="display: inline-block;">
                                                                                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('analisa.blending-awal.show_batch', $blending->id), 'QRCODE') }}"
                                                                                                    alt="QR Code">
                                                                                            </div>
                                                                                            <p>Blending
                                                                                                Awal/{{ $productionBatch->po_number }}/{{ $productionBatch->variant }}/{{ $blending->batch_range }}
                                                                                            </p>
                                                                                        </div>
                                                                                        <div
                                                                                            class="modal-footer justify-content-center py-2">
                                                                                            <button type="button"
                                                                                                class="btn btn-sm btn-dark"
                                                                                                data-bs-dismiss="modal">Tutup</button>
                                                                                            <button
                                                                                                onclick="printQR('qrPrintArea{{ $blending->id }}')"
                                                                                                class="btn btn-sm btn-primary">Cetak</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ $blending->storage ?? '-' }}</td>
                                                                        <td>{{ $blending->status ?? '-' }}</td>
                                                                        <td>
                                                                            {{ $blending->disposition }}
                                                                            @if (in_array($blending->disposition, ['Adjustment', 'Resampling', 'Leveling', 'Jalan Bareng']) &&
                                                                                    $blending->revisi == null &&
                                                                                    $blending->not_standard == true)
                                                                                <button class="btn btn-sm btn-warning"
                                                                                    id="btnRevisi"
                                                                                    data-id="{{ $blending->id }}"
                                                                                    data-batch="{{ $blending->batch_range }}"
                                                                                    data-po="{{ $blending->production_batch_id }}"
                                                                                    data-disposition="{{ $blending->disposition }}">
                                                                                    ❗
                                                                                </button>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($blending->revisi != null)
                                                                                Revisi Ke-{{ $blending->revisi }}
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
                                                <div class="tab-pane fade show" id="nav-blending-awal-mikro"
                                                    role="tabpanel" aria-labelledby="nav-blending-awal-mikro-tab">
                                                    <div class="table-responsive">
                                                        <table class="table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Batch Range</th>
                                                                    <th>QR Code (URL)</th>
                                                                    <th>Hasil</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($productionBatch->blendingAfterAdjustMikro as $blendingMikro)
                                                                    <tr>
                                                                        <td>{{ $blendingMikro->batch_range }}</td>
                                                                        <td>
                                                                            <!-- Tombol untuk buka modal -->
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-primary"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#qrModal{{ $blendingMikro->id }}">
                                                                                QR Code {{ $blendingMikro->id }}
                                                                            </button>

                                                                            <!-- Modal Besar -->
                                                                            <div class="modal fade"
                                                                                id="qrModal{{ $blendingMikro->id }}"
                                                                                tabindex="-1"
                                                                                aria-labelledby="qrModalLabel{{ $blendingMikro->id }}"
                                                                                aria-hidden="true">
                                                                                <div
                                                                                    class="modal-dialog modal-dialog-centered modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header py-2">
                                                                                            <h5 class="modal-title"
                                                                                                id="qrModalLabel{{ $blendingMikro->id }}">
                                                                                                QR Code - Blending Awal
                                                                                                Mikro</h5>
                                                                                            <button type="button"
                                                                                                class="btn-close btn-sm"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body text-center"
                                                                                            id="qrPrintArea{{ $blendingMikro->id }}">
                                                                                            <div
                                                                                                style="display: inline-block;">
                                                                                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('analisa.blending-awal-mikro.show_batch', $blendingMikro->id), 'QRCODE') }}"
                                                                                                    alt="QR Code">
                                                                                            </div>
                                                                                            <p>Blending
                                                                                                Awal
                                                                                                Mikro/{{ $productionBatch->po_number }}/{{ $productionBatch->variant }}/{{ $blendingMikro->batch_range }}
                                                                                            </p>
                                                                                        </div>
                                                                                        <div
                                                                                            class="modal-footer justify-content-center py-2">
                                                                                            <button type="button"
                                                                                                class="btn btn-sm btn-dark"
                                                                                                data-bs-dismiss="modal">Tutup</button>
                                                                                            <button
                                                                                                onclick="printQR('qrPrintArea{{ $blendingMikro->id }}')"
                                                                                                class="btn btn-sm btn-primary">Cetak</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        </td>
                                                                        <td>
                                                                            @if ($blendingMikro->hasil === 'OK')
                                                                                <span class="badge bg-success">OK</span>
                                                                            @elseif ($blendingMikro->hasil === 'NOT OK')
                                                                                <span class="badge bg-danger">NOT OK</span>
                                                                            @elseif ($blendingMikro->hasil === 'PENDING')
                                                                                <span
                                                                                    class="badge bg-warning text-dark">PENDING</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">-</span>
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
                        <h5 class="modal-title">Input Blending Awal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="production_batch_id" value="{{ $productionBatch->id }}">

                        <div class="mb-3">
                            <label for="batch_start" class="form-label">Batch Pertama <span
                                    style="color: red">*</span></label>
                            <select name="batch_start" id="batch_start" class="form-control"></select>
                            <small class="text-danger errorBatchStart"></small>
                        </div>

                        <div class="mb-3">
                            <label for="batch_end" class="form-label">Batch Kedua <span
                                    style="color: red">*</span></label>
                            <select name="batch_end" id="batch_end" class="form-control"></select>
                            <small class="text-danger errorBatchEnd"></small>
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
            <form id="formRevisiBlending">
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

                        <!-- Batch Tambahan 1 (full width) -->
                        <div class="mb-3 d-none" id="additional_batch_group">
                            <label class="form-label">Pilih Batch Tambahan 1</label>
                            <select name="additional_batch[]" id="additional_batch" class="form-control">
                                <option value="">-- Pilih Batch --</option>
                            </select>
                            <input type="hidden" name="production_batch_id_leveling[]" id="po_id_leveling_1">
                        </div>

                        <!-- Batch Tambahan 2 (full width) -->
                        <div class="mb-3 d-none" id="additional_batch_group_2">
                            <label class="form-label">Pilih Batch Tambahan 2</label>
                            <select name="additional_batch[]" id="additional_batch_2" class="form-control">
                                <option value="">-- Pilih Batch --</option>
                            </select>
                            <input type="hidden" name="production_batch_id_leveling[]" id="po_id_leveling_2">
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

        const allBatches = JSON.parse('{!! addslashes(json_encode($allBatches)) !!}');
        const validGgasBatches = JSON.parse('{!! addslashes(json_encode($availableValidBatches)) !!}');

        // Isi select option hanya dengan batch yang valid
        function populateBatchOptions() {
            const $start = $('#batch_start');
            const $end = $('#batch_end');

            $start.empty();
            $end.empty();

            if (validGgasBatches.length === 0) {
                $start.append('<option disabled>Semua batch belum lolos GGAS</option>');
                $end.append('<option disabled>Semua batch belum lolos GGAS</option>');
                return;
            }

            validGgasBatches.forEach(batch => {
                $start.append(`<option value="${batch}">${batch}</option>`);
                $end.append(`<option value="${batch}">${batch}</option>`);
            });
        }

        populateBatchOptions();

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

        $('body').on('click', '#btnRevisi', function() {
            $('#formRevisiBlending')[0].reset();
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
            $('#additional_batch_2').empty();
            $('#po_id_leveling_1').val('');
            $('#po_id_leveling_2').val('');

            // Ambil nomor revisi terakhir
            $.ajax({
                type: "GET",
                url: "{{ route('blending-awal.getLastRevisiBlendingAwal') }}",
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
                            '<option value="">-- Pilih Batch (Optional) --</option>');
                        $('#additional_batch_2').append(
                            '<option value="">-- Pilih Batch (Optional) --</option>');

                        // Load available batches
                        $.ajax({
                            type: "GET",
                            url: "{{ route('blending-awal.getAvailableAdditionalBatch') }}",
                            data: {
                                production_batch_id: poId,
                                exclude_batch: batch
                            },
                            dataType: "json",
                            success: function(batchRes) {
                                batchRes.data.forEach(function(batchItem) {
                                    let option =
                                        `<option data-id_additional_po="${batchItem.po_id}" value="${batchItem.batch_number}">Batch ${batchItem.batch_number} (PO ${batchItem.po_number})</option>`;
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
                            '<option value="">-- Pilih Batch (Optional) --</option>');
                        $('#additional_batch_2').append(
                            '<option value="">-- Pilih Batch (Optional) --</option>');

                        // Load jalan bareng batches
                        $.ajax({
                            type: "GET",
                            url: "{{ route('blending-awal.getJalanBareng') }}",
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

        // Event handler untuk menyimpan PO ID saat memilih batch tambahan 1
        $('#additional_batch').on('change', function() {
            let selectedOption = $(this).find('option:selected');
            let poId = selectedOption.data('id_additional_po');
            let selectedValue = $(this).val();

            $('#po_id_leveling_1').val(poId || '');

            // Cek apakah nilai yang dipilih sama dengan batch tambahan 2
            if (selectedValue && selectedValue === $('#additional_batch_2').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batch Sama',
                    text: 'Batch Tambahan 1 dan Batch Tambahan 2 tidak boleh sama!',
                });
                $(this).val(''); // Reset pilihan
                $('#po_id_leveling_1').val('');
            }
        });

        // Event handler untuk menyimpan PO ID saat memilih batch tambahan 2
        $('#additional_batch_2').on('change', function() {
            let selectedOption = $(this).find('option:selected');
            let poId = selectedOption.data('id_additional_po');
            let selectedValue = $(this).val();

            $('#po_id_leveling_2').val(poId || '');

            // Cek apakah nilai yang dipilih sama dengan batch tambahan 1
            if (selectedValue && selectedValue === $('#additional_batch').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batch Sama',
                    text: 'Batch Tambahan 2 dan Batch Tambahan 1 tidak boleh sama!',
                });
                $(this).val(''); // Reset pilihan
                $('#po_id_leveling_2').val('');
            }
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('blending-awal.store') }}",
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

            $('#formRevisiBlending').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('blending-awal.storeRevisi') }}",
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
