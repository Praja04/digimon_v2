@extends('layouts.component.main')
@section('title', 'Monitoring Daily Tank')
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
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="start_date" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                                    <input type="date" id="end_date" class="form-control">
                                </div>
                                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end gap-2">
                                    <button type="button" id="btnFilter" class="btn btn-primary flex-fill">
                                        <i class="mdi mdi-filter"></i> Filter
                                    </button>
                                    <button type="button" id="btnReset" class="btn btn-secondary flex-fill">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </button>
                                </div>
                                @if (Auth::user()->role === 'Analis Field')
                                    <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end">
                                        <button type="button" id="btnAdd" class="btn btn-success w-100">
                                            <i class="mdi mdi-plus"></i> Tambah Data
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <!-- End Filter Section -->

                            <div class="table-responsive">
                                <table id="datatable" class="table nowrap align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Storage</th>
                                            <th>Tanggal Sampling</th>
                                            <th>Sampling Point</th>
                                            <th>Jenis Analisa</th>
                                            <th>Hasil</th>
                                            <th>Detail</th>
                                            <th>Analisa</th>
                                            <th width="1">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Monitoring Daily Tank</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-lg-12">
                            <input type="hidden" name="id" id="id">
                            <label for="storage" class="form-label">Storage <span style="color: red;">*</span></label>
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
                            <small class="text-danger errorStorage"></small>
                        </div>

                        <div class="col-lg-12">
                            <label for="sampling_point" class="form-label">Sampling Point <span
                                    style="color: red;">*</span></label>
                            <select id="sampling_point" name="sampling_point" class="form-control">
                                <option value="">-- Pilih Sampling Point --</option>
                                <option value="DT">DT</option>
                                <option value="DTP1">DTP1</option>
                                <option value="DTP2">DTP2</option>
                                <option value="DTP3">DTP3</option>
                                <option value="DTS">DTS</option>
                                <option value="DTB">DTB</option>
                                <option value="K1">K1</option>
                                <option value="K2">K2</option>
                                <option value="K3">K3</option>
                            </select>
                            <small class="text-danger errorSamplingPoint"></small>
                        </div>
                        <div class="col-lg-12">
                            <label for="jenis_analisa" class="form-label">Jenis Analisa <span
                                    style="color: red;">*</span></label>
                            <select id="jenis_analisa" name="jenis_analisa" class="form-control">
                                <option value="">-- Pilih Jenis Analisa --</option>
                                <option value="Kimia">Kimia</option>
                                <option value="Mikro">Mikro</option>
                            </select>
                            <small class="text-danger errorJenisAnalisa"></small>
                        </div>
                        <div class="col-lg-12">
                            <label for="jenis_sample" class="form-label">Jenis Sample <span
                                    style="color: red;">*</span></label>
                            <select id="jenis_sample" name="jenis_sample" class="form-control">
                            </select>
                            <small class="text-danger errorJenisSample"></small>
                        </div>
                        <div class="col-lg-12">
                            <label for="jenis_sample" class="form-label">Keterangan Level</label>
                            <textarea id="keterangan_level" name="keterangan_level" class="form-control" rows="3"
                                oninput="this.value = this.value.toUpperCase();"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="save">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalAnalisa" tabindex="-1" aria-labelledby="modalAnalisaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formAnalisa">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Analisa Storage</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-lg-12">
                            <input type="hidden" name="id_analisa" id="id_analisa">
                            <label class="form-label">Visco <span style="color: red;">*</span></label>
                            <input type="text" name="visco" id="visco" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorVisco"></small>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Brix <span style="color: red;">*</span></label>
                            <input type="text" name="brix" id="brix" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorBrix"></small>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">AW <span style="color: red;">*</span></label>
                            <input type="text" name="aw" id="aw" class="form-control comma-input"
                                placeholder="Contoh: 0,00">
                            <small class="text-danger errorAw"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="saveAnalisa">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2 bg-light">
                    <h6 class="modal-title mb-0">Detail Monitoring Daily Tank</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <!-- QR Code Section -->
                    <div class="text-center mb-3" id="qrPrintAreaDetail">
                        <div style="display: inline-block;" id="qr_code_container"></div>
                        <p class="mt-2 mb-1 small text-muted" id="qr_code_text">-</p>
                    </div>

                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-sm btn-primary" onclick="printQR('qrPrintAreaDetail')">
                            <i class="mdi mdi-printer"></i> Cetak QR
                        </button>
                    </div>

                    <hr class="my-2">

                    <!-- Identitas Sampel -->
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1 fw-bold">IDENTITAS SAMPEL</small>
                        <div class="row g-1 small">
                            <div class="col-4"><span class="text-muted">Storage:</span></div>
                            <div class="col-8"><strong id="detail_storage">-</strong></div>

                            <div class="col-4"><span class="text-muted">Sampling Point:</span></div>
                            <div class="col-8"><strong id="detail_sampling_point">-</strong></div>

                            <div class="col-4"><span class="text-muted">Jenis Analisa:</span></div>
                            <div class="col-8"><strong id="detail_jenis_analisa">-</strong></div>

                            <div class="col-4"><span class="text-muted">Jenis Sample:</span></div>
                            <div class="col-8"><strong id="detail_jenis_sample">-</strong></div>

                            <div class="col-4"><span class="text-muted">Status:</span></div>
                            <div class="col-8"><span id="detail_status_pemakaian">-</span></div>

                            <div class="col-4"><span class="text-muted">Tgl Sampling:</span></div>
                            <div class="col-8"><strong id="detail_tanggal_sampling">-</strong></div>

                            <div class="col-4"><span class="text-muted">QC Field:</span></div>
                            <div class="col-8"><strong id="detail_qc_field">-</strong></div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <!-- Data Analisa (tampil jika ada) -->
                    <div id="analisa_section" style="display: none;">
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1 fw-bold">DATA ANALISA</small>
                            <div class="row g-1 small">
                                <div class="col-4"><span class="text-muted">Shift:</span></div>
                                <div class="col-8"><strong id="detail_shift_analisa">-</strong></div>

                                <div class="col-4"><span class="text-muted">QC Analisa:</span></div>
                                <div class="col-8"><strong id="detail_qc_analisa">-</strong></div>

                                <div class="col-4"><span class="text-muted">Tgl Analisa:</span></div>
                                <div class="col-8"><strong id="detail_tanggal_analisa">-</strong></div>
                            </div>
                        </div>

                        <hr class="my-2">

                        <!-- Parameter Uji MIKRO -->
                        <div id="parameter_mikro" style="display: none;">
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1 fw-bold">PARAMETER UJI (MIKRO)</small>
                                <div class="row g-1 small">
                                    <div class="col-4"><span class="text-muted">EB:</span></div>
                                    <div class="col-8"><strong id="detail_eb">-</strong></div>

                                    <div class="col-4"><span class="text-muted">TPC:</span></div>
                                    <div class="col-8"><strong id="detail_tpc">-</strong></div>

                                    <div class="col-4"><span class="text-muted">YM:</span></div>
                                    <div class="col-8"><strong id="detail_ym">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Hasil:</span></div>
                                    <div class="col-8"><span id="detail_hasil">-</span></div>
                                </div>
                            </div>
                            <hr class="my-2">
                        </div>

                        <!-- Parameter Uji KIMIA -->
                        <div id="parameter_kimia" style="display: none;">
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1 fw-bold">PARAMETER UJI (KIMIA)</small>
                                <div class="row g-1 small">
                                    <div class="col-4"><span class="text-muted">BRIX:</span></div>
                                    <div class="col-8"><strong id="detail_brix">-</strong></div>

                                    <div class="col-4"><span class="text-muted">NACL:</span></div>
                                    <div class="col-8"><strong id="detail_nacl">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Bj:</span></div>
                                    <div class="col-8"><strong id="detail_bj">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Visco:</span></div>
                                    <div class="col-8"><strong id="detail_visco">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Aw:</span></div>
                                    <div class="col-8"><strong id="detail_aw">-</strong></div>

                                    <div class="col-4"><span class="text-muted">pH:</span></div>
                                    <div class="col-8"><strong id="detail_ph">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Buih:</span></div>
                                    <div class="col-8"><strong id="detail_buih">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Organo:</span></div>
                                    <div class="col-8"><strong id="detail_organo">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Endapan:</span></div>
                                    <div class="col-8"><strong id="detail_endapan">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Warna:</span></div>
                                    <div class="col-8"><strong id="detail_color">-</strong></div>

                                    <div class="col-4"><span class="text-muted">Status Parameter:</span></div>
                                    <div class="col-8"><span id="detail_status_parameter">-</span></div>
                                </div>
                            </div>
                            <hr class="my-2">
                        </div>

                        <!-- Hasil & Disposisi -->
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1 fw-bold">HASIL & DISPOSISI</small>
                            <div class="row g-1 small">
                                <div class="col-4"><span class="text-muted">Disposisi:</span></div>
                                <div class="col-8"><span id="detail_status_disposisi">-</span></div>

                                <div class="col-4" id="tindakan_label" style="display: none;"><span
                                        class="text-muted">Tindakan:</span></div>
                                <div class="col-8" id="tindakan_value" style="display: none;"><span
                                        id="detail_tindakan_lanjutan">-</span></div>

                                <div class="col-4" id="catatan_label" style="display: none;"><span
                                        class="text-muted">Catatan:</span></div>
                                <div class="col-8" id="catatan_value" style="display: none;"><em
                                        id="detail_catatan_analis" class="text-muted small">-</em></div>

                                <div class="col-4" id="alasan_label" style="display: none;"><span
                                        class="text-muted">Alasan:</span></div>
                                <div class="col-8" id="alasan_value" style="display: none;"><em
                                        id="detail_alasan_disposisi" class="text-muted small">-</em></div>
                            </div>
                        </div>
                    </div>

                    <!-- Info jika belum ada analisa -->
                    <div id="no_analisa_section">
                        <div class="alert alert-info py-2 mb-0 small text-center">
                            <i class="mdi mdi-information-outline"></i> Belum ada data analisa
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function printQR(id) {
            const content = document.getElementById(id).innerHTML;
            const win = window.open('', '', 'height=600,width=600');
            win.document.write('<html><head><title>Print QR Code</title>');
            win.document.write('<style>body{text-align:center; font-size:14px; padding:20px;}</style>');
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

            const allSamples = [{
                    value: 'Sample P',
                    text: 'Sample P'
                },
                {
                    value: 'Sample Tengah',
                    text: 'Sample Tengah'
                },
                {
                    value: 'Sample Awal Trf',
                    text: 'Sample Awal Trf'
                },
                {
                    value: 'Sample Per Trf',
                    text: 'Sample Per Trf'
                },
                {
                    value: 'Awal Trf',
                    text: 'Awal Trf'
                }
            ];

            $('#jenis_analisa').on('change', function() {
                const selectedAnalisa = $(this).val();
                const $jenisSample = $('#jenis_sample');

                // Hapus semua opsi dulu
                $jenisSample.empty();

                // Tambahkan placeholder
                $jenisSample.append('<option value="">-- Pilih Jenis Sample --</option>');

                if (selectedAnalisa === 'Kimia') {
                    // Tambahkan semua sample kecuali 'Awal Trf'
                    allSamples.forEach(sample => {
                        if (sample.value !== 'Awal Trf') {
                            $jenisSample.append(
                                `<option value="${sample.value}">${sample.text}</option>`);
                        }
                    });
                } else if (selectedAnalisa === 'Mikro') {
                    // Tampilkan hanya 'Awal Trf'
                    $jenisSample.append('<option value="Awal Trf">Awal Trf</option>');
                } else {
                    // Jika belum pilih analisa, tampilkan default placeholder saja
                    $jenisSample.append('<option value="">-- Pilih Jenis Sample --</option>');
                }
            });

            // Inisialisasi DataTable
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('monitoring-daily-tank.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'storage',
                        name: 'storage'
                    },
                    {
                        data: 'tanggal_sampling',
                        name: 'tanggal_sampling'
                    },
                    {
                        data: 'sampling_point',
                        name: 'sampling_point'
                    },
                    {
                        data: 'jenis_analisa',
                        name: 'jenis_analisa'
                    },
                    {
                        data: 'hasil_analisa',
                        name: 'hasil_analisa'
                    },
                    {
                        data: 'detail',
                        name: 'detail',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'analisa',
                        name: 'analisa',
                        orderable: false,
                        searchable: false,
                        visible: {{ in_array(auth()->user()->role, ['Analis Kimia', 'Analis Mikro']) ? 'true' : 'false' }}
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        visible: {{ in_array(auth()->user()->role, ['Analis Field']) ? 'true' : 'false' }}
                    }
                ]
            });

            // Tombol Filter
            $('#btnFilter').click(function() {
                table.ajax.reload();
            });

            // Tombol Reset
            $('#btnReset').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });

            // Filter otomatis saat tekan Enter pada input tanggal
            $('#start_date, #end_date').on('keypress', function(e) {
                if (e.which == 13) {
                    table.ajax.reload();
                }
            });

            $('body').on('click', '#btnAdd', function() {
                $('#form').trigger("reset");
                $('#id').val('');

                $('#storage').val('').trigger('change');
                $('#sampling_point').val('').trigger('change');
                $('#jenis_analisa').val('').trigger('change');
                $('#jenis_sample').html('<option value="">-- Pilih Jenis Sample --</option>');

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
                $('#modal').modal('show');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-daily-tank.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#save').val("edit-data");

                        $('#storage').val('').trigger('change');
                        $('#sampling_point').val('').trigger('change');
                        $('#jenis_analisa').val('').trigger('change');
                        $('#jenis_sample').html(
                            '<option value="">-- Pilih Jenis Sample --</option>');

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#id').val(response.id);
                        $('#storage').val(response.storage).trigger('change');
                        $('#sampling_point').val(response.sampling_point).trigger('change');
                        $('#jenis_analisa').val(response.jenis_analisa).trigger('change');
                        $('#jenis_sample').val(response.jenis_sample).trigger('change');

                        $('#modal').modal('show');
                    }
                });
            })

            $('body').on('click', '.btn-detail', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-daily-tank.show', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        // QR Code
                        if (response.qr_code) {
                            $('#qr_code_container').html('<img src="data:image/png;base64,' +
                                response.qr_code +
                                '" alt="QR Code" style="max-width: 150px;">');
                            let qrText = 'MDT-' + response.storage + '/' + response
                                .jenis_analisa + '/' + response.id;
                            $('#qr_code_text').text(qrText);
                        } else {
                            $('#qr_code_container').html(
                                '<p class="text-muted small">QR Code tidak tersedia</p>');
                            $('#qr_code_text').text('-');
                        }

                        // Identitas Sampel
                        $('#detail_storage').text(response.storage || '-');
                        $('#detail_sampling_point').text(response.sampling_point || '-');
                        $('#detail_jenis_analisa').text(response.jenis_analisa || '-');
                        $('#detail_jenis_sample').text(response.jenis_sample || '-');
                        $('#detail_tanggal_sampling').text(response.tanggal_sampling || '-');
                        $('#detail_qc_field').text(response.qc_field_name || '-');

                        // Status Pemakaian
                        let statusText = response.status_pemakaian || '-';
                        if (response.status_pemakaian === 'Habis') {
                            statusText = '<strong class="text-danger">' + statusText +
                                '</strong>';
                        }
                        $('#detail_status_pemakaian').html(statusText);

                        // Cek apakah ada data analisa berdasarkan jenis analisa
                        let hasAnalisa = false;

                        if (response.jenis_analisa === 'Mikro') {
                            hasAnalisa = response.eb !== null || response.tpc !== null ||
                                response.ym !== null;
                        } else if (response.jenis_analisa === 'Kimia') {
                            hasAnalisa = response.brix !== null || response.nacl !== null ||
                                response.bj !== null;
                        }

                        if (hasAnalisa) {
                            // Tampilkan section analisa
                            $('#analisa_section').show();
                            $('#no_analisa_section').hide();

                            // Data Analisa
                            $('#detail_shift_analisa').text(response.shift_analisa ? "Shift " +
                                response.shift_analisa : '-');
                            $('#detail_qc_analisa').text(response.qc_analisa_name || '-');
                            $('#detail_tanggal_analisa').text(response.tanggal_analisa || '-');

                            // Tampilkan parameter sesuai jenis analisa
                            if (response.jenis_analisa === 'Mikro') {
                                $('#parameter_mikro').show();
                                $('#parameter_kimia').hide();

                                // Parameter Uji Mikro
                                $('#detail_eb').text(response.eb !== null ? response.eb : '-');
                                $('#detail_tpc').text(response.tpc !== null ? response.tpc :
                                    '-');
                                $('#detail_ym').text(response.ym !== null ? response.ym : '-');

                                // Hasil untuk Mikro
                                let hasilHtml = '-';
                                if (response.hasil) {
                                    if (response.hasil === 'OK') {
                                        hasilHtml = '<span class="badge bg-success">OK</span>';
                                    } else if (response.hasil === 'NOT OK') {
                                        hasilHtml =
                                            '<span class="badge bg-danger">NOT OK</span>';
                                    } else {
                                        hasilHtml =
                                            '<span class="badge bg-warning text-dark">PENDING</span>';
                                    }
                                }
                                $('#detail_hasil').html(hasilHtml);

                            } else if (response.jenis_analisa === 'Kimia') {
                                $('#parameter_mikro').hide();
                                $('#parameter_kimia').show();

                                // Parameter Uji Kimia
                                $('#detail_brix').text(response.brix !== null ? response.brix :
                                    '-');
                                $('#detail_nacl').text(response.nacl !== null ? response.nacl :
                                    '-');
                                $('#detail_bj').text(response.bj !== null ? response.bj : '-');
                                $('#detail_visco').text(response.visco !== null ? response
                                    .visco : '-');
                                $('#detail_aw').text(response.aw !== null ? response.aw : '-');
                                $('#detail_ph').text(response.ph !== null ? response.ph : '-');
                                $('#detail_buih').text(response.buih !== null ? response.buih :
                                    '-');
                                $('#detail_organo').text(response.organo || '-');
                                $('#detail_endapan').text(response.endapan || '-');
                                $('#detail_color').text(response.color_name || '-');

                                // Status Parameter untuk Kimia
                                let statusParamHtml = '-';
                                if (response.status_parameter) {
                                    if (response.status_parameter === 'OK') {
                                        statusParamHtml =
                                            '<span class="badge bg-success">OK</span>';
                                    } else if (response.status_parameter === 'NOT OK') {
                                        statusParamHtml =
                                            '<span class="badge bg-danger">NOT OK</span>';
                                    }
                                }
                                $('#detail_status_parameter').html(statusParamHtml);
                            }

                            // Status Disposisi (sama untuk Mikro dan Kimia)
                            let disposisiHtml = '-';
                            if (response.status_disposisi) {
                                if (response.status_disposisi === 'RELEASE') {
                                    disposisiHtml =
                                        '<span class="badge bg-success">RELEASE</span>';
                                } else if (response.status_disposisi === 'TIDAK STD') {
                                    disposisiHtml =
                                        '<span class="badge bg-danger">TIDAK STD</span>';
                                } else {
                                    disposisiHtml =
                                        '<span class="badge bg-warning text-dark">RELEASE BERSYARAT</span>';
                                }
                            }
                            $('#detail_status_disposisi').html(disposisiHtml);

                            // Tindakan Lanjutan
                            if (response.tindakan_lanjutan) {
                                $('#tindakan_label').show();
                                $('#tindakan_value').show();
                                let tindakanHtml = response.tindakan_lanjutan;
                                if (response.tindakan_lanjutan === 'Drain') {
                                    tindakanHtml = '<strong class="text-danger">' +
                                        tindakanHtml + '</strong>';
                                } else if (response.tindakan_lanjutan === 'Rework') {
                                    tindakanHtml = '<strong class="text-warning">' +
                                        tindakanHtml + '</strong>';
                                }
                                $('#detail_tindakan_lanjutan').html(tindakanHtml);
                            } else {
                                $('#tindakan_label').hide();
                                $('#tindakan_value').hide();
                            }

                            // Catatan Analis
                            if (response.catatan_analis) {
                                $('#catatan_label').show();
                                $('#catatan_value').show();
                                $('#detail_catatan_analis').text(response.catatan_analis);
                            } else {
                                $('#catatan_label').hide();
                                $('#catatan_value').hide();
                            }

                            // Alasan Disposisi
                            if (response.alasan_disposisi) {
                                $('#alasan_label').show();
                                $('#alasan_value').show();
                                $('#detail_alasan_disposisi').text(response.alasan_disposisi);
                            } else {
                                $('#alasan_label').hide();
                                $('#alasan_value').hide();
                            }
                        } else {
                            // Sembunyikan section analisa
                            $('#analisa_section').hide();
                            $('#parameter_mikro').hide();
                            $('#parameter_kimia').hide();
                            $('#no_analisa_section').show();
                        }

                        // Tampilkan modal
                        $('#modalDetail').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal mengambil data detail.',
                        });
                    }
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-daily-tank.store') }}",
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
                        });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.storage) {
                                $('#storage').addClass('is-invalid');
                                $('.errorStorage').html(errors.storage.join('<br>'));
                            }
                            if (errors.storage) {
                                $('#sampling_point').addClass('is-invalid');
                                $('.errorSamplingPoint').html(errors.sampling_point.join(
                                    '<br>'));
                            }

                            if (errors.jenis_analisa) {
                                $('#jenis_analisa').addClass('is-invalid');
                                $('.errorJenisAnalisa').html(errors.jenis_analisa.join('<br>'));
                            }

                            if (errors.jenis_sample) {
                                $('#jenis_sample').addClass('is-invalid');
                                $('.errorJenisSample').html(errors.jenis_sample.join('<br>'));
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

            $('body').on('click', '#btnDelete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus saja!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "monitoring-daily-tank/" + id,
                            dataType: "json",
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                });
                                table.ajax.reload();
                            }
                        });
                    }
                })
            })
        });
    </script>
@endsection
