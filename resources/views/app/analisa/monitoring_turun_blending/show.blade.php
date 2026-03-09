@extends('layouts.component.main')
@section('title', 'Analisa Monitoring Turun Blending')
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
                                        href="{{ route('analisa.monitoring-turun-blending.index') }}">Analisa
                                        Monitoring Turun Blending</a></li>
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
                                                <th>Waktu Scan</th>
                                                <th>Status</th>
                                                <th>Disposisi</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            @forelse ($productionBatch->MonitoringTurunBlending as $blending)
                                                @php
                                                    // Tentukan class berdasarkan disposition
                                                    $dispositionUpper = strtoupper($blending->status ?? '');
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
                                                        {{ $productionBatch->po_number }}

                                                        @if ($blending->revisi != null)
                                                            <span class="badge {{ $badgeClass }} ms-1"
                                                                title="Revisi ke-{{ $blending->revisi }}">
                                                                Rev. {{ $blending->revisi }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $blending->batch_range }}

                                                        @if ($blending->additionalBatches)
                                                            @foreach ($blending->additionalBatches as $relasi)
                                                                <span class="badge bg-info">{{ $relasi->batch }}</span>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>{{ $blending->nomor_blending }}</td>
                                                    <td>{{ $blending->volume }}</td>
                                                    <td>{{ $blending->scanned_at ? \Carbon\Carbon::parse($blending->scanned_at)->format('d/m/Y H:i:s') : '-' }}
                                                    </td>
                                                    <td>
                                                        @if ($blending->status)
                                                            <span
                                                                class="badge {{ match (strtoupper($blending->status)) {
                                                                    'OK' => 'bg-success',
                                                                    'NOT OK' => 'bg-danger',
                                                                    'ADJUSTMENT' => 'bg-warning text-dark',
                                                                    default => 'bg-secondary',
                                                                } }}">
                                                                {{ $blending->status }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $blending->disposition ?? '-' }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" id="btnDetail"
                                                            data-id="{{ $blending->id }}">
                                                            <i class="ri-eye-line"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        @if (is_null($blending->status))
                                                            <a href="{{ route('analisa.monitoring-turun-blending.show_batch', $blending->id) }}"
                                                                class="btn btn-sm btn-primary"
                                                                data-id="{{ $blending->id }}">
                                                                Analisa Data
                                                            </a>
                                                        @else
                                                            @if (auth()->user()->role == 'Foreman')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-warning open-blending-modal-edit"
                                                                    data-id="{{ $blending->id }}">
                                                                    Kelola Data
                                                                </button>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success">
                                                                    <i class="ri-check-line align-middle"></i> Lengkap
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr class="text-center">
                                                    <td colspan="9">Tidak ada data tersedia.</td>
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

    <!-- Modal input-->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kelola Data Monitoring Turun Blending</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="alert alert-danger d-none error-alert"></div>
                        <input type="hidden" name="id" id="id">
                        <div class="col-lg-6">
                            <label class="form-label">BRIX <span style="color: red">*</span></label>
                            <input type="text" name="brix" id="brix" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorBrix"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Visco <span style="color: red">*</span></label>
                            <input type="text" name="visco" id="visco" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorVisco"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Aw <span style="color: red">*</span></label>
                            <input type="text" name="aw" id="aw" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorAw"></small>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Status <span style="color: red">*</span></label>
                            <select name="status_disposition" id="status_disposition"
                                class="form-control disposition-select">
                                <option value="">-- Pilih Status --</option>
                                <option value="OK">OK</option>
                                <option value="NOT OK">NOT OK</option>
                                <option value="Adjustment">Adjustment</option>
                            </select>
                            <small class="text-danger errorStatusDisposition"></small>
                        </div>
                        @if (auth()->user()->role == 'Foreman')
                            <div class="col-lg-12">
                                <label class="form-label">Disposition</label>
                                <select name="disposition" id="disposition" class="form-control disposition-select">
                                    <option value="">-- Pilih Disposition --</option>
                                    <option value="Release">Release</option>
                                    <option value="Release Bersyarat">Release Bersyarat</option>
                                    <option value="Resampling">Resampling</option>
                                    <option value="Adjustment">Adjustment</option>
                                    <option value="Reject">Reject</option>
                                    <option value="Repro">Repro</option>
                                    {{-- <option value="Jalan Bareng">Jalan Bareng</option>
                                    <option value="Leveling">Leveling</option> --}}
                                </select>
                            </div>
                        @endif
                        <div class="col-lg-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="disposition_remark" id="disposition_remark" class="form-control" rows="2"
                                placeholder="Isi catatan jika diperlukan..." oninput="this.value = this.value.toUpperCase();"></textarea>
                        </div>

                        <div class="mb-3 d-none adjustment-qty-wrapper">
                            <h6 class="form-label fw-bold">Adjustment Qty</h6>
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Air (Liter)</label>
                                    <input type="text" name="adjustment_qty_air"
                                        class="form-control adjustment-qty comma-input" placeholder="0,00">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Gula (Kg)</label>
                                    <input type="text" name="adjustment_qty_gula"
                                        class="form-control adjustment-qty comma-input" placeholder="0,00">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Garam (Kg)</label>
                                    <input type="text" name="adjustment_qty_garam"
                                        class="form-control adjustment-qty comma-input" placeholder="0,00">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="detailModalLabel">Detail Data Monitoring Turun Blending</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <!-- Informasi Produksi -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Informasi Produksi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Batch Range</span>
                                    <span class="fw-medium">: <span id="detail_batch_range">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Nomor Blending</span>
                                    <span class="fw-medium">: <span id="detail_nomor_blending">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Shift</span>
                                    <span class="fw-medium">: <span id="detail_shift">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Volume</span>
                                    <span class="fw-medium">: <span id="detail_volume">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Storage</span>
                                    <span class="fw-medium">: <span id="detail_storage">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Revisi</span>
                                    <span class="fw-medium">: <span id="detail_revisi">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parameter Analisa -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Parameter Analisa</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Brix</span>
                                    <span class="fw-medium">: <span id="detail_brix">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Visco</span>
                                    <span class="fw-medium">: <span id="detail_visco">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">AW</span>
                                    <span class="fw-medium">: <span id="detail_aw">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Disposisi -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Status & Disposisi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Status</span>
                                    <span class="fw-medium">: <span id="detail_status">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Disposisi</span>
                                    <span class="fw-medium">: <span id="detail_disposition">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Not Standard</span>
                                    <span class="fw-medium">: <span id="detail_not_standard">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan/Catatan -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2 mb-3">Keterangan</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0" id="detail_remark" style="white-space: pre-wrap;">-</p>
                        </div>
                    </div>

                    <!-- Informasi Tambahan -->
                    <div>
                        <h6 class="border-bottom pb-2 mb-3">Informasi Tambahan</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Dibuat Oleh</span>
                                    <span class="fw-medium">: <span id="detail_created_by">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Tanggal Dibuat</span>
                                    <span class="fw-medium">: <span id="detail_created_at">-</span></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <span class="text-muted" style="min-width: 140px;">Terakhir Diupdate</span>
                                    <span class="fw-medium">: <span id="detail_updated_at">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function formatDecimal(value, forDatabase = false) {
            if (value === null || value === undefined || value === '') {
                return '';
            }

            let stringValue = String(value);

            if (forDatabase) {
                return stringValue.replace(/\./g, '').replace(',', '.');
            } else {
                return stringValue.replace(/\./g, ',');
            }
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.select2').select2({
                placeholder: '-- Pilih Opsi --',
                dropdownParent: $('#modal')
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

            function toggleAdjustmentFields(status, showOnly = false) {
                const qtyWrapper = $('.adjustment-qty-wrapper');
                const qtyInput = $('.adjustment-qty');

                if (status === 'Adjustment') {
                    qtyWrapper.removeClass('d-none');
                    qtyInput.prop('required', true);
                } else {
                    qtyWrapper.addClass('d-none');
                    qtyInput.prop('required', false);

                    if (!showOnly) {
                        qtyInput.val('');
                    }
                }
            }

            $('#status_disposition').on('change', function() {
                const selected = $(this).val();
                toggleAdjustmentFields(selected);
            });

            $('.open-blending-modal').on('click', function() {
                const id = $(this).data('id');

                $('#form')[0].reset();
                $('.text-danger').html('');
                $('.form-control').removeClass('is-invalid');

                $('#id').val(id);
                $('#disposition').val('').trigger('change');

                $('#status_disposition').val('').trigger('change');
                $('#status_disposition').prop('disabled', false);

                $('.adjustment-qty-wrapper').addClass('d-none');
                $('.adjustment-qty').prop('required', false).val('');

                $('#modal').modal('show');
            });

            $('body').on('click', '.open-blending-modal-edit', function() {
                const id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.monitoring-turun-blending.edit', '') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        $('#form')[0].reset();
                        $('.text-danger').html('');
                        $('.form-control').removeClass('is-invalid');
                    },
                    success: function(response) {
                        const userRole = "{{ auth()->user()->role }}";

                        $('#id').val(response.id);
                        $('#brix').val(formatDecimal(response.brix));
                        $('#visco').val(formatDecimal(response.visco));
                        $('#aw').val(formatDecimal(response.aw));
                        $('#disposition_remark').val(response.disposition_remark || '');

                        $('#status_disposition').val(response.status);
                        if (userRole === 'Foreman') {
                            $('#status_disposition').val(response.status);
                            $('#status_disposition').prop('disabled', true);
                        } else {
                            $('#status_disposition').val(response.status);
                            $('#status_disposition').prop('disabled', false);
                        }

                        $('#disposition').val(response.disposition);

                        if (response.status === 'Adjustment') {
                            $('.adjustment-qty-wrapper').removeClass('d-none');
                            $('input[name="adjustment_qty_air"]').val(response
                                .adjustment_qty_air || '');
                            $('input[name="adjustment_qty_gula"]').val(response
                                .adjustment_qty_gula || '');
                            $('input[name="adjustment_qty_garam"]').val(response
                                .adjustment_qty_garam || '');

                            $('.adjustment-qty').prop('required', true);
                        } else {
                            $('.adjustment-qty-wrapper').addClass('d-none');
                            $('.adjustment-qty').prop('required', false).val('');
                        }

                        $('#modal').modal('show');
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

            $('body').on('click', '#btnDetail', function() {
                const id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('analisa.monitoring-turun-blending.edit', '') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        // Reset semua field
                        $('#detail_batch_range, #detail_nomor_blending, #detail_shift, #detail_volume, #detail_storage, #detail_revisi')
                            .text('-');
                        $('#detail_brix, #detail_visco, #detail_aw')
                            .text('-');
                        $('#detail_status, #detail_disposition, #detail_not_standard, #detail_remark')
                            .text('-');
                        $('#detail_created_by, #detail_created_at, #detail_updated_at').text(
                            '-');
                    },
                    success: function(response) {
                        // Informasi Produksi
                        $('#detail_batch_range').text(response.batch_range || '-');
                        $('#detail_nomor_blending').text(response.nomor_blending || '-');
                        $('#detail_shift').text(response.shift || '-');
                        $('#detail_volume').text(response.volume ? response.volume + ' L' :
                            '-');
                        $('#detail_storage').text(response.storage || '-');
                        $('#detail_revisi').text(response.revisi || '-');

                        // Parameter Analisa
                        $('#detail_brix').text(response.brix || '-');
                        $('#detail_visco').text(response.visco || '-');
                        $('#detail_aw').text(response.aw || '-');

                        // Status & Disposisi
                        $('#detail_status').text(response.status || '-');
                        $('#detail_disposition').text(response.disposition || '-');
                        $('#detail_not_standard').text(response.not_standard == 1 ? 'Ya' :
                            'Tidak');

                        // Keterangan
                        let remarkText = '-';
                        if (response.disposition_remark &&
                            response.disposition_remark != '-' &&
                            response.disposition != 'Adjustment') {
                            remarkText = response.disposition_remark;
                        } else if (response.disposition == 'Adjustment') {
                            remarkText =
                                `Adjustment:\n• Air: ${response.adjustment_qty_air || 0} Liter\n• Garam: ${response.adjustment_qty_garam || 0} Kg\n• Gula: ${response.adjustment_qty_gula || 0} Kg`;
                        } else if (response.is_adjustment == true) {
                            remarkText = 'Adjustment';
                        }
                        $('#detail_remark').text(remarkText);

                        // Informasi Tambahan - Safe access untuk user
                        let createdByText = '-';
                        if (response.user && response.user.name) {
                            createdByText = response.user.name;
                        } else if (response.user && response.user.username) {
                            createdByText = response.user.username;
                        } else if (response.created_by) {
                            createdByText = 'ID: ' + response.created_by;
                        }
                        $('#detail_created_by').text(createdByText);

                        $('#detail_created_at').text(response.created_at ? formatDateTime(
                            response.created_at) : '-');
                        $('#detail_updated_at').text(response.updated_at ? formatDateTime(
                            response.updated_at) : '-');

                        $('#detailModal').modal('show');
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

            // Helper function untuk format tanggal
            function formatDateTime(dateString) {
                const date = new Date(dateString);
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return date.toLocaleDateString('id-ID', options);
            }

            $('#form').submit(function(e) {
                e.preventDefault();

                const wasDisabled = $('#status_disposition').prop('disabled');
                if (wasDisabled) {
                    $('#status_disposition').prop('disabled', false);
                }

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('analisa.monitoring-turun-blending.update') }}",
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
                        $('#modal').modal('hide');
                        $('#form').trigger("reset");

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

                            if (errors.brix) {
                                $('#brix').addClass('is-invalid');
                                $('.errorBrix').html(errors.brix.join('<br>'));
                            }
                            if (errors.visco) {
                                $('#visco').addClass('is-invalid');
                                $('.errorVisco').html(errors.visco.join('<br>'));
                            }
                            if (errors.aw) {
                                $('#aw').addClass('is-invalid');
                                $('.errorAw').html(errors.aw.join('<br>'));
                            }
                            if (errors.status_disposition) {
                                $('#status_disposition').addClass('is-invalid');
                                $('.errorStatusDisposition').html(errors.status_disposition
                                    .join('<br>'));
                            }
                            if (errors.disposition) {
                                $('#disposition').addClass('is-invalid');
                                $('.errorDisposition').html(errors.disposition.join('<br>'));
                            }
                            if (errors.adjustment_qty_air) {
                                $('input[name="adjustment_qty_air"]').addClass('is-invalid');
                            }
                            if (errors.adjustment_qty_gula) {
                                $('input[name="adjustment_qty_gula"]').addClass('is-invalid');
                            }
                            if (errors.adjustment_qty_garam) {
                                $('input[name="adjustment_qty_garam"]').addClass('is-invalid');
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
