@extends('layouts.component.main')
@section('title', 'Data Hasil Analisa - Shelf Life')
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
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Daftar @yield('title')</h5>
                        </div>
                        <div class="card-body">
                            <!-- Filter Section -->
                            <div class="row mb-3 g-2">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="tanggal_produksi" class="form-label">Tanggal Produksi</label>
                                    <input type="date" id="tanggal_produksi" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
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
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="nomor_po" class="form-label">Nomor PO</label>
                                    <select name="nomor_po" id="nomor_po" class="form-control">
                                        <option value="">-- Pilih Nomor PO --</option>
                                    </select>
                                    <div class="errorNomorPO"></div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end">
                                    <button type="button" id="btnReset" class="btn btn-secondary w-100">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <!-- End Filter Section -->

                            <!-- Info Section -->
                            <div id="infoSection" class="mb-3" style="display: none;">
                                <div class="alert alert-light border">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Nomor PO:</strong> <span id="infoPO">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Storage:</strong> <span id="infoStorage">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Tanggal Produksi:</strong> <span id="infoTanggal">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total Data:</strong> <span id="infoTotal">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div id="loadingState" class="text-center py-5" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat data...</p>
                            </div>

                            <!-- Empty State -->
                            <div id="emptyState" class="text-center py-5">
                                <p class="text-muted">Silakan pilih filter dan klik tombol "Tampilkan" untuk melihat data
                                </p>
                            </div>

                            <!-- No Data State -->
                            <div id="noDataState" class="text-center py-5" style="display: none;">
                                <p class="text-muted">Tidak ada data untuk filter yang dipilih</p>
                            </div>


                            <!-- Data Container -->
                            <div id="dataContainer" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" style="font-size: 13px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center align-middle" style="width: 60px;">Bulan</th>
                                                <th class="align-middle">Detail Logistik & Hasil Analisa Lengkap</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">
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

    <div class="modal fade" id="modalMikro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Analisa Mikro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted fw-bold small text-uppercase mb-3" style="letter-spacing: 1px;">Informasi
                        Pengujian</p>
                    <div class="row g-3 mb-4">
                        <div class="col-4">
                            <label class="d-block small text-muted mb-0">Waktu Sample Masuk</label>
                            <span class="fw-bold" id="mikroWaktuSampel">-</span>
                        </div>
                        <div class="col-4">
                            <label class="d-block small text-muted mb-0">Waktu Analisa</label>
                            <span class="fw-bold" id="mikroWaktuAnalisa">-</span>
                        </div>
                        <div class="col-4">
                            <label class="d-block small text-muted mb-0">Shift / Analis</label>
                            <span class="fw-bold"><span id="mikroShift">-</span> / <span id="mikroAnalis">-</span></span>
                        </div>

                    </div>

                    <p class="text-muted fw-bold small text-uppercase mb-3" style="letter-spacing: 1px;">Hasil
                        Laboratorium</p>
                    <div class="row g-3 border rounded p-3 bg-light">
                        <div class="col-3 text-center border-end">
                            <label class="d-block small text-muted mb-1">TPC</label>
                            <h5 class="fw-bold text-primary mb-0" id="mikroTPC">-</h5>
                        </div>
                        <div class="col-3 text-center border-end">
                            <label class="d-block small text-muted mb-1">YM</label>
                            <h5 class="fw-bold text-primary mb-0" id="mikroYM">-</h5>
                        </div>
                        <div class="col-3 text-center border-end">
                            <label class="d-block small text-muted mb-1">EB</label>
                            <h5 class="fw-bold text-primary mb-0" id="mikroEB">-</h5>
                        </div>
                        <div class="col-3 text-center">
                            <label class="d-block small text-muted mb-1">SA</label>
                            <h5 class="fw-bold text-primary mb-0" id="mikroSA">-</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKimia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Analisa Kimia</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-8 border-end">
                            <p class="text-muted fw-bold small text-uppercase mb-3" style="letter-spacing: 1px;">Petugas &
                                Waktu</p>
                            <div class="row g-3">
                                <div class="col-4">
                                    <label class="d-block small text-muted mb-0">Waktu Sample Masuk</label>
                                    <span class="fw-bold" id="kimiaWaktuSampel">-</span>
                                </div>
                                <div class="col-4">
                                    <label class="d-block small text-muted mb-0">Waktu Analisa</label>
                                    <span class="fw-bold" id="kimiaWaktuAnalisa">-</span>
                                </div>
                                <div class="col-4">
                                    <label class="d-block small text-muted mb-0">Nama Analis</label>
                                    <span class="fw-bold" id="kimiaAnalis">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 ps-md-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="d-block small text-muted mb-0">Aroma</label>
                                    <span class="fw-bold" id="kimiaAroma">-</span>
                                </div>
                                <div class="col-6">
                                    <label class="d-block small text-muted mb-0">Organo</label>
                                    <span class="fw-bold" id="kimiaOrgano">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <span class="text-muted fw-bold small text-uppercase mb-3" style="letter-spacing: 1px;">Hasil
                        Pengujian Kimia</span>
                    <div class="row g-4 p-3 rounded bg-light border">
                        <div class="col-md-3 col-6 text-center border-end">
                            <label class="d-block small text-muted mb-1">NaCl</label>
                            <h5 class="fw-bold"><span id="kimiaNacl">-</span></h5>
                        </div>
                        <div class="col-md-3 col-6 text-center border-end">
                            <label class="d-block small text-muted mb-1">Brix</label>
                            <h5 class="fw-bold"><span id="kimiaBrix">-</span></h5>
                        </div>
                        <div class="col-md-3 col-6 text-center border-end">
                            <label class="d-block small text-muted mb-1">pH</label>
                            <h5 class="fw-bold" id="kimiaPh">-</h5>
                        </div>
                        <div class="col-md-3 col-6 text-center">
                            <label class="d-block small text-muted mb-1">Viskositas</label>
                            <h5 class="fw-bold"><span id="kimiaVisco">-</span></h5>
                        </div>
                        <div class="col-md-3 col-6 text-center border-end border-top pt-3">
                            <label class="d-block small text-muted mb-1">aW</label>
                            <span class="fw-bold" id="kimiaAw">-</span>
                        </div>
                        <div class="col-md-3 col-6 text-center border-end border-top pt-3">
                            <label class="d-block small text-muted mb-1">Berat Jenis</label>
                            <span class="fw-bold" id="kimiaBj">-</span>
                        </div>
                        <div class="col-md-3 col-6 text-center border-end border-top pt-3">
                            <label class="d-block small text-muted mb-1">Buih</label>
                            <span class="fw-bold" id="kimiaBuih">-</span>
                        </div>
                        <div class="col-md-3 col-6 text-center border-top pt-3">
                            <label class="d-block small text-muted mb-1">Total Nitrogen</label>
                            <span class="fw-bold" id="kimiaTotalN">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentData = [];

        $(document).ready(function() {
            // Setup AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Event handler untuk tanggal_produksi dan storage
            $('#tanggal_produksi, #storage').on('change', function() {
                var tanggal_produksi = $('#tanggal_produksi').val();
                var storage = $('#storage').val();
                var $nomorPO = $('#nomor_po');

                console.log('Filter changed:', {
                    tanggal_produksi,
                    storage
                });

                // Reset dropdown PO
                $nomorPO.empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('.errorNomorPO').html('');

                // Hide data
                $('#emptyState').show();
                $('#loadingState, #noDataState, #dataContainer, #infoSection').hide();
                currentData = [];

                if (tanggal_produksi && storage) {
                    loadPOData(tanggal_produksi, storage);
                }
            });

            // Event handler untuk nomor_po
            $('#nomor_po').on('change', function() {
                var tanggal_produksi = $('#tanggal_produksi').val();
                var storage = $('#storage').val();
                var nomor_po = $(this).val();

                console.log('PO changed:', {
                    tanggal_produksi,
                    storage,
                    nomor_po
                });

                if (tanggal_produksi && storage && nomor_po) {
                    loadData();
                } else {
                    $('#emptyState').show();
                    $('#loadingState, #noDataState, #dataContainer, #infoSection').hide();
                }
            });

            // Event handler untuk reset button
            $('#btnReset').on('click', function() {
                $('#tanggal_produksi').val('');
                $('#storage').val('');
                $('#nomor_po').empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('.errorNomorPO').html('');

                $('#emptyState').show();
                $('#loadingState, #noDataState, #dataContainer, #infoSection').hide();
                currentData = [];

                console.log('Form reset');
            });

            // Function untuk load PO data
            function loadPOData(tanggal_produksi, storage) {
                var $nomorPO = $('#nomor_po');

                $.ajax({
                    url: "{{ route('shelf-life.result.get-po') }}",
                    type: "POST",
                    data: {
                        tanggal_produksi: tanggal_produksi,
                        storage: storage
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $nomorPO.prop('disabled', true);
                        $nomorPO.html('<option value="">Loading...</option>');
                        console.log('Loading PO data...');
                    },
                    success: function(response) {
                        console.log('PO Response:', response);

                        $nomorPO.prop('disabled', false);
                        $nomorPO.empty().append('<option value="">-- Pilih Nomor PO --</option>');

                        if (response.status === 'success' && response.count > 0) {
                            $.each(response.po_list, function(index, item) {
                                $nomorPO.append($('<option>', {
                                    value: item.id,
                                    text: item.po_number
                                }));
                            });

                            // Auto-select jika hanya ada 1 PO
                            if (response.count === 1 && response.selected_id) {
                                $nomorPO.val(response.selected_id);
                                // Trigger change untuk load data
                                $nomorPO.trigger('change');
                            }
                        } else {
                            $nomorPO.append('<option value="">-- Tidak Ada PO Release --</option>');
                            $('.errorNomorPO').html(
                                '<small class="text-danger">Tidak ada Nomor PO yang Release.</small>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Get PO Error:', {
                            xhr,
                            status,
                            error
                        });
                        console.error('Response Text:', xhr.responseText);

                        $nomorPO.prop('disabled', false);
                        $nomorPO.empty().append('<option value="">-- Gagal mengambil data --</option>');
                        $('.errorNomorPO').html(
                            '<small class="text-danger">Terjadi kesalahan saat mengambil data PO.</small>'
                        );
                    }
                });
            }

            // Function untuk load data
            function loadData() {
                var tanggal_produksi = $('#tanggal_produksi').val();
                var storage = $('#storage').val();
                var nomor_po = $('#nomor_po').val();

                console.log('Loading data with params:', {
                    tanggal_produksi,
                    storage,
                    nomor_po
                });

                $('#emptyState, #noDataState, #dataContainer').hide();
                $('#loadingState').show();

                $.ajax({
                    url: "{{ route('shelf-life.result.get-data') }}",
                    type: "GET",
                    data: {
                        tanggal_produksi: tanggal_produksi,
                        storage: storage,
                        nomor_po: nomor_po
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Data Response:', response);

                        $('#loadingState').hide();

                        if (response.status === 'success' && response.data.length > 0) {
                            currentData = response.data;
                            renderTable(response.data);

                            // Update info section
                            $('#infoPO').text(response.po_number || '-');
                            $('#infoStorage').text(storage);
                            $('#infoTanggal').text(formatDate(tanggal_produksi));
                            $('#infoTotal').text(response.data.length);
                            $('#infoSection').show();

                            $('#dataContainer').show();
                        } else {
                            $('#noDataState').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Get Data Error:', {
                            xhr,
                            status,
                            error
                        });
                        console.error('Response Text:', xhr.responseText);

                        $('#loadingState').hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal mengambil data. Silakan coba lagi.'
                        });
                    }
                });
            }

            let currentData = [];

            function displayValue(value) {
                if (value === null || value === undefined || value === '' || value === 'null') {
                    return '--';
                }
                return value;
            }

            // Function untuk render table
            function renderTable(data) {
                var html = '';

                $.each(data, function(index, item) {
                    var hasMikro = item.shelf_life_sampling_mikro != null;
                    var hasKimia = item.shelf_life_sampling_kimia != null;

                    html += `
                        <tr style="border-bottom: 10px solid #f4f6f9;">
                            <td class="text-center align-middle" style="background: #ffffff; border-right: 1px solid #eee;">
                                <div class="fw-bold text-dark fs-5">${displayValue(item.bulan_ke)}</div>
                                <small class="text-muted text-uppercase" style="font-size: 10px;">Bulan</small>
                            </td>
                            <td class="p-0">
                                <div class="container-fluid py-3 px-4 bg-white">
                                    <div class="row align-items-center">
                                        
                                        <div class="col-md-3 border-end">
                                            <h6 class="fw-bold text-primary mb-1">${displayValue(item.variant_fg)}</h6>
                                            <div style="font-size: 12px; line-height: 1.6;">
                                                <div class="d-flex justify-content-between pe-3">
                                                    <span class="text-muted">Kelompok Tanggal:</span> <b>${displayValue(item.kelompok_tanggal)}</b>
                                                </div>
                                                <div class="d-flex justify-content-between pe-3">
                                                    <span class="text-muted">Kelompok Sample:</span> <b>${displayValue(item.kelompok_sample)}</b>
                                                </div>
                                                <div class="d-flex justify-content-between pe-3">
                                                    <span class="text-muted">Koding:</span> <b>${displayValue(item.koding)}</b>
                                                </div>
                                                <div class="d-flex justify-content-between pe-3">
                                                    <span class="text-muted">Jam Koding:</span> <b>${displayValue(item.jam_koding)}</b>
                                                </div>
                                                <div class="d-flex justify-content-between pe-3">
                                                    <span class="text-muted">Bin Loc:</span> <b>${displayValue(item.bin_location)}</b>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 border-end ps-4">
                                            <p class="fw-bold text-success small text-uppercase mb-2" style="font-size: 10px; letter-spacing: 1px;">
                                                <i class="mdi mdi-microscope me-1"></i> Mikro
                                            </p>
                                            <div class="row g-2 text-center">
                                                <div class="col-3"><small class="text-muted d-block">TPC</small><b class="fs-6">${hasMikro ? displayValue(item.shelf_life_sampling_mikro.tpc) : '--'}</b></div>
                                                <div class="col-3"><small class="text-muted d-block">YM</small><b class="fs-6">${hasMikro ? displayValue(item.shelf_life_sampling_mikro.ym) : '--'}</b></div>
                                                <div class="col-3"><small class="text-muted d-block">EB</small><b class="fs-6">${hasMikro ? displayValue(item.shelf_life_sampling_mikro.eb) : '--'}</b></div>
                                                <div class="col-3"><small class="text-muted d-block">SA</small><b class="fs-6">${hasMikro ? displayValue(item.shelf_life_sampling_mikro.sa) : '--'}</b></div>
                                            </div>
                                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                                <small class="text-muted">Analis: ${hasMikro ? displayValue(item.shelf_life_sampling_mikro.nama_analis) : '-'}</small>
                                                ${hasMikro ? `<button class="btn btn-sm btn-outline-primary" onclick="showMikroDetail(${index})">Detail &rarr;</button>` : ''}
                                            </div>
                                        </div>

                                        <div class="col-md-5 ps-4">
                                            <p class="fw-bold text-info small text-uppercase mb-2" style="font-size: 10px; letter-spacing: 1px;">
                                                <i class="mdi mdi-flask-outline me-1"></i> Kimia
                                            </p>
                                            <div class="row g-2 text-center mb-2">
                                                <div class="col"><small class="text-muted d-block">NaCl</small><b>${hasKimia ? displayValue(item.shelf_life_sampling_kimia.nacl) : '--'}</b></div>
                                                <div class="col"><small class="text-muted d-block">Brix</small><b>${hasKimia ? displayValue(item.shelf_life_sampling_kimia.brix) : '--'}</b></div>
                                                <div class="col"><small class="text-muted d-block">pH</small><b>${hasKimia ? displayValue(item.shelf_life_sampling_kimia.ph) : '--'}</b></div>
                                                <div class="col"><small class="text-muted d-block">aW</small><b>${hasKimia ? displayValue(item.shelf_life_sampling_kimia.aw) : '--'}</b></div>
                                                <div class="col"><small class="text-muted d-block">Visco</small><b>${hasKimia ? displayValue(item.shelf_life_sampling_kimia.visco) : '--'}</b></div>
                                            </div>
                                            <div class="p-2 bg-light rounded d-flex justify-content-between align-items-center" style="font-size: 11px;">
                                                <span>Aroma: <b>${hasKimia ? displayValue(item.shelf_life_sampling_kimia.aroma) : '-'}</b></span>
                                                <span>Organo: <b>${hasKimia ? displayValue(item.shelf_life_sampling_kimia.organo) : '-'}</b></span>
                                                <span>BJ: <b>${hasKimia ? displayValue(item.shelf_life_sampling_kimia.bj) : '-'}</b></span>
                                                ${hasKimia ? `<button class="btn btn-sm btn-outline-success" onclick="showKimiaDetail(${index})">Detail &rarr;</button>` : ''}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#tableBody').html(html);
            }

            // Helper functions
            function formatDate(dateString) {
                if (!dateString) return '-';
                var date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            function formatTime(dateString) {
                if (!dateString) return '-';
                var date = new Date(dateString);
                return date.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Global functions untuk modal
            window.showMikroDetail = function(index) {
                var item = currentData[index];
                var mikro = item.shelf_life_sampling_mikro;
                if (mikro) {
                    $('#mikroWaktuSampel').text(mikro.scanned_at ? formatDate(mikro.scanned_at) + ' ' +
                        formatTime(mikro.scanned_at) : '-');
                    $('#mikroWaktuAnalisa').text(mikro.waktu_analisa ? formatDate(mikro.waktu_analisa) + ' ' +
                        formatTime(mikro.waktu_analisa) : '-');
                    $('#mikroShift').text(displayValue(mikro.shift_analis));
                    $('#mikroAnalis').text(displayValue(mikro.nama_analis));
                    $('#mikroTPC').text(displayValue(mikro.tpc) === '--' ? '0' : displayValue(mikro.tpc));
                    $('#mikroYM').text(displayValue(mikro.ym) === '--' ? '0' : displayValue(mikro.ym));
                    $('#mikroEB').text(displayValue(mikro.eb) === '--' ? '0' : displayValue(mikro.eb));
                    $('#mikroSA').text(displayValue(mikro.sa) === '--' ? '0' : displayValue(mikro.sa));
                    new bootstrap.Modal(document.getElementById('modalMikro')).show();
                }
            };

            window.showKimiaDetail = function(index) {
                var item = currentData[index];
                var kimia = item.shelf_life_sampling_kimia;
                if (kimia) {
                    $('#kimiaWaktuSampel').text(kimia.scanned_at ? formatDate(kimia.scanned_at) + ' ' +
                        formatTime(kimia.scanned_at) : '-');
                    $('#kimiaWaktuAnalisa').text(kimia.waktu_analisa ? formatDate(kimia.waktu_analisa) + ' ' +
                        formatTime(kimia.waktu_analisa) : '-');
                    $('#kimiaAnalis').text(displayValue(kimia.nama_analis));
                    $('#kimiaNacl').text(displayValue(kimia.nacl) === '--' ? '0' : displayValue(kimia.nacl));
                    $('#kimiaBrix').text(displayValue(kimia.brix) === '--' ? '0' : displayValue(kimia.brix));
                    $('#kimiaAw').text(displayValue(kimia.aw) === '--' ? '0' : displayValue(kimia.aw));
                    $('#kimiaPh').text(displayValue(kimia.ph) === '--' ? '0' : displayValue(kimia.ph));
                    $('#kimiaBj').text(displayValue(kimia.bj) === '--' ? '0' : displayValue(kimia.bj));
                    $('#kimiaBuih').text(displayValue(kimia.buih) === '--' ? '0' : displayValue(kimia.buih));
                    $('#kimiaVisco').text(displayValue(kimia.visco) === '--' ? '0' : displayValue(kimia.visco));
                    $('#kimiaTotalN').text(displayValue(kimia.total_nitrogen) === '--' ? '0' : displayValue(
                        kimia.total_nitrogen));
                    $('#kimiaAroma').text(displayValue(kimia.aroma) === '--' ? '0' : displayValue(kimia.aroma));
                    $('#kimiaOrgano').text(displayValue(kimia.organo) === '--' ? '0' : displayValue(kimia
                        .organo));
                    new bootstrap.Modal(document.getElementById('modalKimia')).show();
                }
            };
        });
    </script>
@endsection
