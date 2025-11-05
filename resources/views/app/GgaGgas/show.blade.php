@extends('layouts.component.main')
@section('title', 'Detail GGA & GGAS')
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
                                <li class="breadcrumb-item"><a href="{{ route('productionbatch.index') }}">Persiapan
                                        Masak</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('gga-ggas.index') }}">GGA & GGAS</a></li>
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
                                                            Input GGA / GGAS
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
                                                            <a class="nav-link active" id="nav-gga-tab" data-bs-toggle="tab"
                                                                href="#nav-gga" role="tab" aria-controls="nav-gga"
                                                                aria-selected="true">GGA</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" id="nav-ggas-tab" data-bs-toggle="tab"
                                                                href="#nav-ggas" role="tab" aria-controls="nav-ggas"
                                                                aria-selected="false">GGAS</a>
                                                        </li>
                                                    </ul>
                                                </nav>

                                                <div class="tab-content border border-top-0 p-4" id="nav-tabContent">
                                                    <!-- TAB GGA - ACTIVE -->
                                                    <div class="tab-pane fade show active" id="nav-gga" role="tabpanel"
                                                        aria-labelledby="nav-gga-tab">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Batch Number</th>
                                                                        <th>Dissolver</th>
                                                                        <th>QR Code (URL)</th>
                                                                        <th>Status</th>
                                                                        <th>Disposisi</th>
                                                                        <th>Catatan</th>
                                                                        <th>Keterangan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($productionBatch->gga as $gga)
                                                                        @php
                                                                            $dispositionUpper = strtoupper(
                                                                                $gga->status ?? '',
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
                                                                            <td>{{ $gga->batch_number }}</td>
                                                                            <td>{{ $gga->dissolver_number }}</td>
                                                                            <td>
                                                                                <!-- Tombol untuk buka modal GGA -->
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-primary"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#qrModalGGA{{ $gga->id }}">
                                                                                    QR Code {{ $gga->id }}
                                                                                </button>

                                                                                <!-- Modal GGA -->
                                                                                <div class="modal fade"
                                                                                    id="qrModalGGA{{ $gga->id }}"
                                                                                    tabindex="-1"
                                                                                    aria-labelledby="qrModalGGALabel{{ $gga->id }}"
                                                                                    aria-hidden="true">
                                                                                    <div
                                                                                        class="modal-dialog modal-dialog-centered modal-lg">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header py-2">
                                                                                                <h5 class="modal-title"
                                                                                                    id="qrModalGGALabel{{ $gga->id }}">
                                                                                                    QR Code GGA - ID
                                                                                                    {{ $gga->id }}
                                                                                                </h5>
                                                                                                <button type="button"
                                                                                                    class="btn-close btn-sm"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close"></button>
                                                                                            </div>
                                                                                            <div class="modal-body text-center"
                                                                                                id="qrPrintAreaGGA{{ $gga->id }}">
                                                                                                <div
                                                                                                    style="display: inline-block;">
                                                                                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('gga.show_batch', $gga->id), 'QRCODE') }}"
                                                                                                        alt="QR Code">
                                                                                                </div>
                                                                                                <p>GGA/{{ $productionBatch->po_number }}/{{ $productionBatch->production_date }}/{{ $gga->batch_number }}
                                                                                                </p>
                                                                                            </div>
                                                                                            <div
                                                                                                class="modal-footer justify-content-center py-2">
                                                                                                <button type="button"
                                                                                                    class="btn btn-sm btn-dark"
                                                                                                    data-bs-dismiss="modal">Tutup</button>
                                                                                                <button
                                                                                                    onclick="printQR('qrPrintAreaGGA{{ $gga->id }}')"
                                                                                                    class="btn btn-sm btn-primary">Cetak</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                {{ $gga->status ?? '-' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $gga->disposition }}
                                                                                @if (in_array($gga->disposition, ['Adjustment', 'Resampling']) && $gga->revisi == null && $gga->not_standard == true)
                                                                                    <button class="btn btn-sm btn-warning"
                                                                                        id="btnRevisiGGA"
                                                                                        data-id="{{ $gga->id }}">
                                                                                        ❗
                                                                                    </button>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($gga->revisi != null)
                                                                                    Revisi Ke-{{ $gga->revisi }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <button class="btn btn-sm btn-info"
                                                                                    id="btnDetailGga"
                                                                                    data-id="{{ $gga->id }}">Lihat</button>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="7">
                                                                                <p class="text-muted">Tidak ada data
                                                                                    tersedia.
                                                                                </p>
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <!-- TAB GGAS - TIDAK ACTIVE -->
                                                    <div class="tab-pane fade show" id="nav-ggas" role="tabpanel"
                                                        aria-labelledby="nav-ggas-tab">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Batch Number</th>
                                                                        <th>Dissolver</th>
                                                                        <th>QR Code (URL)</th>
                                                                        <th>Status</th>
                                                                        <th>Disposisi</th>
                                                                        <th>Catatan</th>
                                                                        <th>Keterangan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($productionBatch->ggas as $ggas)
                                                                        @php
                                                                            $dispositionUpper = strtoupper(
                                                                                $ggas->status ?? '',
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
                                                                            <td>{{ $ggas->batch_number }}</td>
                                                                            <td>{{ $ggas->dissolver_number }}</td>
                                                                            <td>
                                                                                <!-- Tombol untuk buka modal GGAS -->
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-primary"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#qrModalGGAS{{ $ggas->id }}">
                                                                                    QR Code {{ $ggas->id }}
                                                                                </button>

                                                                                <!-- Modal GGAS -->
                                                                                <div class="modal fade"
                                                                                    id="qrModalGGAS{{ $ggas->id }}"
                                                                                    tabindex="-1"
                                                                                    aria-labelledby="qrModalGGASLabel{{ $ggas->id }}"
                                                                                    aria-hidden="true">
                                                                                    <div
                                                                                        class="modal-dialog modal-dialog-centered modal-lg">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header py-2">
                                                                                                <h5 class="modal-title"
                                                                                                    id="qrModalGGALabel{{ $ggas->id }}">
                                                                                                    QR Code GGAS - ID
                                                                                                    {{ $ggas->id }}
                                                                                                </h5>
                                                                                                <button type="button"
                                                                                                    class="btn-close btn-sm"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close"></button>
                                                                                            </div>
                                                                                            <div class="modal-body text-center"
                                                                                                id="qrPrintAreaGGAS{{ $ggas->id }}">
                                                                                                <div
                                                                                                    style="display: inline-block;">
                                                                                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('ggas.show_batch', $ggas->id), 'QRCODE') }}"
                                                                                                        alt="QR Code">
                                                                                                </div>
                                                                                                <p>GGA/{{ $productionBatch->po_number }}/{{ $productionBatch->production_date }}/{{ $ggas->batch_number }}
                                                                                                </p>
                                                                                            </div>
                                                                                            <div
                                                                                                class="modal-footer justify-content-center py-2">
                                                                                                <button type="button"
                                                                                                    class="btn btn-sm btn-dark"
                                                                                                    data-bs-dismiss="modal">Tutup</button>
                                                                                                <button
                                                                                                    onclick="printQR('qrPrintAreaGGA{{ $ggas->id }}')"
                                                                                                    class="btn btn-sm btn-primary">Cetak</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                {{ $ggas->status ?? '-' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $ggas->disposition }}
                                                                                @if (in_array($ggas->disposition, ['Adjustment', 'Resampling']) && $ggas->revisi == null && $ggas->not_standard == true)
                                                                                    <button class="btn btn-sm btn-warning"
                                                                                        id="btnRevisiGGAS"
                                                                                        data-id="{{ $ggas->id }}">
                                                                                        ❗
                                                                                    </button>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($ggas->revisi != null)
                                                                                    Revisi Ke-{{ $ggas->revisi }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <button class="btn btn-sm btn-info"
                                                                                    id="btnDetailGgas"
                                                                                    data-id="{{ $ggas->id }}">Lihat</button>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="7">
                                                                                <p class="text-muted">Tidak ada data
                                                                                    tersedia.
                                                                                </p>
                                                                            </td>
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input GGA / GGAS</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="production_batch_id" value="{{ $productionBatch->id }}">
                        <div class="mb-3">
                            <label class="form-label" for="dissolver_number">Nomor Dissolver <span
                                    style="color: red">*</span></label>
                            <select name="dissolver_number" id="dissolver_number" class="form-control">
                                <option value="" selected disabled>-- Pilih Dissolver Number --</option>
                                @for ($i = 1; $i <= 8; $i++)
                                    <option value="Dissolver {{ $i }}">Dissolver {{ $i }}</option>
                                @endfor
                            </select>
                            <small class="text-danger errorDissolverNumber"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="type">Jenis Sample <span
                                    style="color: red">*</span></label>
                            <select name="type" id="type" class="form-control">
                                <option value="" selected disabled>Pilih Jenis Sample</option>
                                <option value="GGA">GGA</option>
                                <option value="GGAS">GGAS</option>
                            </select>
                            <small class="text-danger errorType"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="batch_number">Nomor Batch <span
                                    style="color: red">*</span></label>
                            <select name="batch_number" id="batch_number" class="form-control">
                                <option selected disabled>Pilih Jenis Sample terlebih dahulu</option>
                            </select>
                            <small class="text-danger errorBatchNumber"></small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Generate Ulang - GGA -->
    <div class="modal fade" id="modalRevisiGGA" tabindex="-1">
        <div class="modal-dialog">
            <form id="formRevisiGGA">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Revisi Batch</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_revisi_gga" id="id_revisi_gga">
                        <input type="hidden" name="id_productbatch_gga" id="id_productbatch_gga">
                        <div class="mb-3">
                            <label class="form-label" for="batch_number">Nomor Batch <span
                                    style="color: red">*</span></label>
                            <input type="number" name="batch_number_gga" id="batch_number_gga" class="form-control"
                                readonly>
                            <small class="text-danger errorBatchNumberRevisiGga"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="revisi">Revisi Ke- <span style="color: red">*</span></label>
                            <input type="number" name="revisi_gga" id="revisi_gga" class="form-control" readonly>
                            <small class="text-danger errorRevisiGga"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="save_revisi_gga" type="submit" class="btn btn-primary">Generate Ulang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Generate Ulang - GGAS -->
    <div class="modal fade" id="modalRevisiGGAS" tabindex="-1">
        <div class="modal-dialog">
            <form id="formRevisiGGAS">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Revisi Batch</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_revisi_ggas" id="id_revisi_ggas">
                        <input type="hidden" name="id_productbatch_ggas" id="id_productbatch_ggas">
                        <div class="mb-3">
                            <label class="form-label" for="batch_number">Nomor Batch <span
                                    style="color: red">*</span></label>
                            <input type="number" name="batch_number_ggas" id="batch_number_ggas" class="form-control"
                                readonly>
                            <small class="text-danger errorBatchNumberRevisiGgas"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="revisi">Revisi Ke- <span style="color: red">*</span></label>
                            <input type="number" name="revisi_ggas" id="revisi_ggas" class="form-control" readonly>
                            <small class="text-danger errorRevisiGgas"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="save_revisi_ggas" type="submit" class="btn btn-primary">Generate Ulang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Keterangan GGA -->
    <div class="modal fade" id="detailGgaModal" tabindex="-1" aria-labelledby="detailGgaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail GGA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-lg-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="disposition_remark_detail_gga" id="disposition_remark_detail_gga" class="form-control"
                            rows="2" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Keterangan GGAS -->
    <div class="modal fade" id="detailGgasModal" tabindex="-1" aria-labelledby="detailGgasModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail GGAS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-lg-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="disposition_remark_detail_ggas" id="disposition_remark_detail_ggas" class="form-control"
                            rows="2" disabled></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const allBatches = JSON.parse('{!! addslashes(json_encode($batches)) !!}');
        const releasedGgaBatches = JSON.parse('{!! addslashes(json_encode($validGgaBatches)) !!}');

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

        $('select[name="type"]').on('change', function() {
            const type = $(this).val();
            const $batchSelect = $('select[name="batch_number"]');

            $batchSelect.empty();

            if (type === 'GGA') {
                allBatches.forEach(batch => {
                    $batchSelect.append(`<option value="${batch}">${batch}</option>`);
                });
            } else if (type === 'GGAS') {
                if (releasedGgaBatches.length === 0) {
                    $batchSelect.append(`<option disabled>Semua batch belum lolos GGA</option>`);
                } else {
                    releasedGgaBatches.forEach(batch => {
                        $batchSelect.append(`<option value="${batch}">${batch}</option>`);
                    });
                }
            }
        });

        $('body').on('click', '#btnRevisiGGA', function() {
            let id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: "{{ route('gga-ggas.show_revisi_gga', '') }}/" + id,
                dataType: "json",
                success: function(response) {
                    $('#modalLabel').html("Edit Data");
                    $('#save').val("edit-data");
                    $('#modalRevisiGGA').modal('show');

                    $('.form-control').removeClass('is-invalid');
                    $('.text-danger').html('');

                    $('#id_revisi_gga').val(response.data.id);
                    $('#id_productbatch_gga').val(response.data.production_batch_id);
                    $('#batch_number_gga').val(response.data.batch_number);
                    $('#revisi_gga').val(response.revisi);
                }
            });
        })

        $('body').on('click', '#btnDetailGga', function() {
            const id = $(this).data('id');

            $.ajax({
                type: "GET",
                url: "{{ route('gga.edit', '') }}/" + id,
                dataType: "json",
                beforeSend: function() {
                    $('#form')[0].reset();
                    $('.text-danger').html('');
                    $('.form-control').removeClass('is-invalid');
                },
                success: function(response) {
                    let remarkText = '';

                    if (response.disposition_remark != null &&
                        response.disposition_remark != '-' &&
                        response.disposition != 'Adjustment') {
                        remarkText = response.disposition_remark;
                    } else if (response.disposition == 'Adjustment') {
                        remarkText =
                            `Adjustment Air: ${response.adjustment_qty_air || 0} Liter, Garam: ${response.adjustment_qty_garam || 0} Kg, Gula: ${response.adjustment_qty_gula || 0} Kg`;
                    } else if (response.is_adjustment == true) {
                        remarkText = 'Adjustment';
                    } else {
                        remarkText = '-';
                    }

                    $('#disposition_remark_detail_gga').val(remarkText);

                    $('#detailGgaModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memuat data. Silakan coba lagi.',
                    });
                }
            });
        });

        $('body').on('click', '#btnRevisiGGAS', function() {
            let id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: "{{ route('gga-ggas.show_revisi_ggas', '') }}/" + id,
                dataType: "json",
                success: function(response) {
                    $('#modalLabel').html("Edit Data");
                    $('#save').val("edit-data");
                    $('#modalRevisiGGAS').modal('show');

                    $('.form-control').removeClass('is-invalid');
                    $('.text-danger').html('');

                    $('#id_revisi_ggas').val(response.data.id);
                    $('#id_productbatch_ggas').val(response.data.production_batch_id);
                    $('#batch_number_ggas').val(response.data.batch_number);
                    $('#revisi_ggas').val(response.revisi);
                }
            });
        })

        $('body').on('click', '#btnDetailGgas', function() {
            const id = $(this).data('id');

            $.ajax({
                type: "GET",
                url: "{{ route('ggas.edit', '') }}/" + id,
                dataType: "json",
                beforeSend: function() {
                    $('#form')[0].reset();
                    $('.text-danger').html('');
                    $('.form-control').removeClass('is-invalid');
                },
                success: function(response) {
                    let remarkText = '';

                    if (response.disposition_remark != null &&
                        response.disposition_remark != '-' &&
                        response.disposition != 'Adjustment') {
                        remarkText = response.disposition_remark;
                    } else if (response.disposition == 'Adjustment') {
                        remarkText =
                            `Adjustment Air: ${response.adjustment_qty_air || 0} Liter, Garam: ${response.adjustment_qty_garam || 0} Kg, Gula: ${response.adjustment_qty_gula || 0} Kg`;
                    } else if (response.is_adjustment == true) {
                        remarkText = 'Adjustment';
                    } else {
                        remarkText = '-';
                    }

                    $('#disposition_remark_detail_ggas').val(remarkText);

                    $('#detailGgasModal').modal('show');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memuat data. Silakan coba lagi.',
                    });
                }
            });
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
                    url: "{{ route('gga-ggas.store') }}",
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
                            if (errors.dissolver_number) {
                                $('#dissolver_number').addClass('is-invalid');
                                $('.errorDissolverNumber').html(errors.dissolver_number.join(
                                    '<br>'));
                            }
                            if (errors.type) {
                                $('#type').addClass('is-invalid');
                                $('.errorType').html(errors.type.join('<br>'));
                            }
                            if (errors.batch_number) {
                                $('#batch_number').addClass('is-invalid');
                                $('.errorBatchNumber').html(errors.batch_number.join('<br>'));
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

            $('#formRevisiGGA').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('gga-ggas.update_revisi_gga') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save_revisi_gga').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#save_revisi_gga').prop('disabled', false).text('Generate Ulang');
                    },
                    success: function(response) {
                        $('#modalRevisiGGA').modal('hide');
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
                            $('#save_revisi_gga').prop('disabled', false).text(
                                'Generate Ulang');
                            return;
                        }

                        if (xhr.status === 422) {
                            let errors = response.errors;
                            if (errors.batch_number_gga) {
                                $('#batch_number_gga').addClass('is-invalid');
                                $('.errorBatchNumberRevisiGga').html(errors.batch_number_gga
                                    .join('<br>'));
                            }
                            if (errors.revisi_gga) {
                                $('#revisi_gga').addClass('is-invalid');
                                $('.errorRevisiGga').html(errors.revisi_gga.join('<br>'));
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

            $('#formRevisiGGAS').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('gga-ggas.update_revisi_ggas') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save_revisi_ggas').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#save_revisi_ggas').prop('disabled', false).text('Generate Ulang');
                    },
                    success: function(response) {
                        $('#modalRevisiGGAS').modal('hide');
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
                            $('#save_revisi_ggas').prop('disabled', false).text(
                                'Generate Ulang');
                            return;
                        }

                        if (xhr.status === 422) {
                            let errors = response.errors;
                            if (errors.batch_number_ggas) {
                                $('#batch_number_ggas').addClass('is-invalid');
                                $('.errorBatchNumberRevisiGgas').html(errors.batch_number_ggas
                                    .join('<br>'));
                            }
                            if (errors.revisi_ggas) {
                                $('#revisi_ggas').addClass('is-invalid');
                                $('.errorRevisiGgas').html(errors.revisi_ggas.join('<br>'));
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
