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
                                <li class="breadcrumb-item"><a href="{{ route('monitoring-daily-tank.menu') }}">Menu</a></li>
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
                                            <th>Nomor PO</th>
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
                        <div class="col-lg-6">
                            <input type="hidden" name="id" id="id">
                            <label for="tanggal_produksi" class="form-label">Tanggal Produksi <span
                                    style="color: red;">*</span></label>
                            <input type="date" name="tanggal_produksi" id="tanggal_produksi" class="form-control">
                            <small class="errorTanggalProduksi text-danger"></small>
                        </div>

                        <div class="col-lg-6">
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

                        <div class="col-lg-6">
                            <label for="nomor_po" class="form-label">Nomor PO <span style="color: red;">*</span></label>
                            <select name="nomor_po" id="nomor_po" class="form-control">
                                <option value="">-- Pilih Nomor PO --</option>
                            </select>
                            <small class="text-danger errorNomorPO"></small>
                        </div>

                        <div class="col-lg-6">
                            <label for="sampling_point" class="form-label">Sampling Point <span
                                    style="color: red;">*</span></label>
                            <select id="sampling_point" name="sampling_point" class="form-control">
                                <option value="">-- Pilih Sampling Point --</option>
                                <option value="DTP1">DTP1</option>
                                <option value="DTP2">DTP2</option>
                                <option value="DTP3">DTP3</option>
                                <option value="DTS">DTS</option>
                                <option value="DTB">DTB</option>
                                <option value="DTD1">DTD1</option>
                                <option value="DTD2">DTD2</option>
                                <option value="DTD3">DTD3</option>
                            </select>
                            <small class="text-danger errorSamplingPoint"></small>
                        </div>

                        <div class="col-lg-6">
                            <label for="jenis_analisa" class="form-label">Jenis Analisa <span
                                    style="color: red;">*</span></label>
                            <select id="jenis_analisa" name="jenis_analisa" class="form-control">
                                <option value="">-- Pilih Jenis Analisa --</option>
                                <option value="Kimia">Kimia</option>
                                <option value="Mikro">Mikro</option>
                            </select>
                            <small class="text-danger errorJenisAnalisa"></small>
                        </div>

                        <div class="col-lg-6">
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
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="save">Simpan</button>
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
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="saveAnalisa">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail - Version Simple & Clean -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Monitoring Daily Tank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- QR Code -->
                    <div class="text-center mb-3 pb-3 border-bottom" id="qrPrintAreaDetail">
                        <div id="qr_code_container" class="mb-2"></div>
                        <p class="small text-muted mb-2" id="qr_code_text">-</p>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="printQR('qrPrintAreaDetail')">
                            Cetak QR
                        </button>
                    </div>

                    <!-- Identitas Sampel -->
                    <div class="mb-3">
                        <h6 class="mb-2 fw-bold">Identitas Sampel</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="35%">Storage</td>
                                <td width="5%">:</td>
                                <td><strong id="detail_storage">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Sampling Point</td>
                                <td>:</td>
                                <td><strong id="detail_sampling_point">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Analisa</td>
                                <td>:</td>
                                <td><strong id="detail_jenis_analisa">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Sample</td>
                                <td>:</td>
                                <td><strong id="detail_jenis_sample">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>:</td>
                                <td><span id="detail_status_pemakaian">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tgl Sampling</td>
                                <td>:</td>
                                <td><strong id="detail_tanggal_sampling">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">QC Field</td>
                                <td>:</td>
                                <td><strong id="detail_qc_field">-</strong></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Info Belum Ada Analisa -->
                    <div id="no_analisa_section">
                        <div class="alert alert-light text-center border">
                            <p class="mb-0 text-muted">Belum ada data analisa</p>
                        </div>
                    </div>

                    <!-- Section Analisa -->
                    <div id="analisa_section" style="display: none;">
                        <!-- Data Analisa -->
                        <div class="mb-3 pt-3 border-top">
                            <h6 class="mb-2 fw-bold">Data Analisa</h6>
                            <div class="row">
                                <div class="col-4">
                                    <small class="text-muted d-block">Shift</small>
                                    <strong id="detail_shift_analisa">-</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">QC Analisa</small>
                                    <strong id="detail_qc_analisa">-</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Tanggal Analisa</small>
                                    <strong id="detail_tanggal_analisa">-</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Parameter Mikro -->
                        <div id="parameter_mikro" class="mb-3" style="display: none;">
                            <h6 class="mb-2 fw-bold">Parameter Uji Mikrobiologi</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <small class="text-muted d-block">EB</small>
                                        <strong class="d-block" id="detail_eb">-</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <small class="text-muted d-block">TPC</small>
                                        <strong class="d-block" id="detail_tpc">-</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-2">
                                        <small class="text-muted d-block">YM</small>
                                        <strong class="d-block" id="detail_ym">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted d-block">Hasil</small>
                                <div id="detail_hasil">-</div>
                            </div>
                        </div>

                        <!-- Parameter Kimia -->
                        <div id="parameter_kimia" class="mb-3" style="display: none;">
                            <h6 class="mb-2 fw-bold">Parameter Uji Kimia</h6>
                            <div class="row g-2">
                                <div class="col-3">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">BRIX</small>
                                        <strong id="detail_brix">-</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">NACL</small>
                                        <strong id="detail_nacl">-</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">BJ</small>
                                        <strong id="detail_bj">-</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">Visco</small>
                                        <strong id="detail_visco">-</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">AW</small>
                                        <strong id="detail_aw">-</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">pH</small>
                                        <strong id="detail_ph">-</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">Buih</small>
                                        <strong id="detail_buih">-</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">Warna</small>
                                        <strong id="detail_color">-</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">Organo</small>
                                        <strong id="detail_organo">-</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2 text-center">
                                        <small class="text-muted d-block">Endapan</small>
                                        <strong id="detail_endapan">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted d-block">Status Parameter</small>
                                <div id="detail_status_parameter">-</div>
                            </div>
                        </div>

                        <!-- Hasil & Disposisi -->
                        <div class="pt-3 border-top">
                            <h6 class="mb-2 fw-bold">Hasil & Disposisi</h6>
                            <div class="mb-2">
                                <small class="text-muted d-block">Disposisi</small>
                                <strong id="detail_status_disposisi">-</strong>
                            </div>
                            <div id="tindakan_section" class="mb-2" style="display: none;">
                                <small class="text-muted d-block">Tindakan Lanjutan</small>
                                <strong id="detail_tindakan_lanjutan">-</strong>
                            </div>
                            <div id="catatan_section" class="mb-2" style="display: none;">
                                <small class="text-muted d-block">Catatan Analis</small>
                                <div class="border rounded p-2 bg-light">
                                    <em id="detail_catatan_analis" class="small">-</em>
                                </div>
                            </div>
                            <div id="alasan_section" style="display: none;">
                                <small class="text-muted d-block">Alasan Disposisi</small>
                                <div class="border rounded p-2 bg-light">
                                    <em id="detail_alasan_disposisi" class="small">-</em>
                                </div>
                            </div>
                        </div>
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
                    value: 'Sample PP',
                    text: 'Sample PP'
                },
                {
                    value: 'Sample Tengah',
                    text: 'Sample Tengah'
                },
                {
                    value: 'Sample Awal Transfer',
                    text: 'Sample Awal Transfer'
                },
                {
                    value: 'Sample Per Transfer',
                    text: 'Sample Per Transfer'
                },
                {
                    value: 'Awal Transfer',
                    text: 'Awal Transfer'
                },
                {
                    value: 'Sample Akhir',
                    text: 'Sample Akhir'
                },
            ];

            // Handler untuk Jenis Analisa
            $('#jenis_analisa').on('change', function() {
                const selectedAnalisa = $(this).val();
                const $jenisSample = $('#jenis_sample');

                $jenisSample.empty();
                $jenisSample.append('<option value="">-- Pilih Jenis Sample --</option>');

                if (selectedAnalisa === 'Kimia') {
                    allSamples.forEach(sample => {
                        if (sample.value !== 'Awal Transfer') {
                            $jenisSample.append(
                                `<option value="${sample.value}">${sample.text}</option>`);
                        }
                    });
                } else if (selectedAnalisa === 'Mikro') {
                    $jenisSample.append('<option value="Awal Transfer">Awal Transfer</option>');
                    $jenisSample.append('<option value="Tengah PO">Tengah PO</option>');
                    $jenisSample.append('<option value="Akhir PO">Akhir PO</option>');
                }
            });

            // Inisialisasi DataTable
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('monitoring-daily-tank.index') }}",
                    type: 'GET',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTable Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data. Silakan refresh halaman.',
                        });
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nomor_po',
                        name: 'nomor_po'
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
                        visible: {{ in_array(auth()->user()->role, ['Analis Kimia', 'Analis Mikro', 'Foreman']) ? 'true' : 'false' }}
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

            // Enter key untuk filter
            $('#start_date, #end_date').on('keypress', function(e) {
                if (e.which == 13) {
                    table.ajax.reload();
                }
            });

            // Tombol Tambah Data
            $('body').on('click', '#btnAdd', function() {
                $('#form').trigger("reset");
                $('#id').val('');
                $('#nomor_po').empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('#storage').val('');
                $('#sampling_point').val('');
                $('#jenis_analisa').val('');
                $('#jenis_sample').html('<option value="">-- Pilih Jenis Sample --</option>');

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
                $('#modal').modal('show');
            });

            // Tombol Edit
            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ url('monitoring-daily-tank/edit') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#save').val("edit-data");

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#id').val(response.id);
                        $('#storage').val(response.storage);
                        $('#sampling_point').val(response.sampling_point);
                        $('#jenis_analisa').val(response.jenis_analisa).trigger('change');

                        // Set jenis sample setelah jenis analisa di-trigger
                        setTimeout(function() {
                            $('#jenis_sample').val(response.jenis_sample);
                        }, 100);

                        $('#keterangan_level').val(response.keterangan_level);
                        $('#modal').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Edit Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal mengambil data.',
                        });
                    }
                });
            });

            // Tombol Detail
            $('body').on('click', '.btn-detail', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ url('monitoring-daily-tank/show') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        // QR Code
                        if (response.qr_code) {
                            if (response.jenis_analisa == 'Mikro') {
                                $('#qr_code_container').html(
                                    '<img src="data:image/png;base64,' +
                                    response.qr_code +
                                    '" alt="QR Code" style="max-width: 150px;">');
                                let qrText = 'MONITORING-DAILY-TANK-MIKRO/' + response
                                    .po_number + '/' + response
                                    .date + '/' + response.id;
                                $('#qr_code_text').text(qrText);
                            } else {
                                $('#qr_code_container').html(
                                    '<img src="data:image/png;base64,' +
                                    response.qr_code +
                                    '" alt="QR Code" style="max-width: 150px;">');
                                let qrText = 'MONITORING-DAILY-TANK-KIMIA/' + response
                                    .po_number + '/' + response
                                    .date + '/' + response.id;
                                $('#qr_code_text').text(qrText);
                            }
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

                        // Cek apakah ada data analisa
                        let hasAnalisa = false;
                        if (response.jenis_analisa === 'Mikro') {
                            hasAnalisa = response.eb !== null || response.tpc !== null ||
                                response.ym !== null;
                        } else if (response.jenis_analisa === 'Kimia') {
                            hasAnalisa = response.brix !== null || response.nacl !== null ||
                                response.bj !== null;
                        }

                        if (hasAnalisa) {
                            $('#analisa_section').show();
                            $('#no_analisa_section').hide();

                            $('#detail_shift_analisa').text(response.shift_analisa ? "Shift " +
                                response.shift_analisa : '-');
                            $('#detail_qc_analisa').text(response.qc_analisa_name || '-');
                            $('#detail_tanggal_analisa').text(response.tanggal_analisa || '-');

                            if (response.jenis_analisa === 'Mikro') {
                                $('#parameter_mikro').show();
                                $('#parameter_kimia').hide();

                                $('#detail_eb').text(response.eb !== null ? response.eb : '-');
                                $('#detail_tpc').text(response.tpc !== null ? response.tpc :
                                    '-');
                                $('#detail_ym').text(response.ym !== null ? response.ym : '-');

                                let hasilHtml = '-';
                                if (response.hasil === 'OK') {
                                    hasilHtml = '<span class="badge bg-success">OK</span>';
                                } else if (response.hasil === 'NOT OK') {
                                    hasilHtml = '<span class="badge bg-danger">NOT OK</span>';
                                } else if (response.hasil) {
                                    hasilHtml =
                                        '<span class="badge bg-warning text-dark">PENDING</span>';
                                }
                                $('#detail_hasil').html(hasilHtml);

                            } else if (response.jenis_analisa === 'Kimia') {
                                $('#parameter_mikro').hide();
                                $('#parameter_kimia').show();

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

                                let statusParamHtml = '-';
                                if (response.status === 'OK') {
                                    statusParamHtml =
                                        '<span class="badge bg-success">OK</span>';
                                } else if (response.status === 'NOT OK') {
                                    statusParamHtml =
                                        '<span class="badge bg-danger">NOT OK</span>';
                                }
                                $('#detail_status_parameter').html(statusParamHtml);
                            }

                            // Disposisi
                            let disposisiHtml = '-';
                            if (response.disposisi === 'Release') {
                                disposisiHtml = '<span class="badge bg-success">Release</span>';
                            } else if (response.disposisi === 'Drain') {
                                disposisiHtml = '<span class="badge bg-danger">Drain</span>';
                            } else if (response.disposisi) {
                                disposisiHtml =
                                    '<span class="badge bg-success">Release Bersyarat</span>';
                            }
                            $('#detail_status_disposisi').html(disposisiHtml);

                            // Tindakan, Catatan, Alasan
                            if (response.tindakan_lanjutan) {
                                $('#tindakan_section').show();
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
                                $('#tindakan_section').hide();
                            }

                            if (response.catatan_analis) {
                                $('#catatan_section').show();
                                $('#detail_catatan_analis').text(response.catatan_analis);
                            } else {
                                $('#catatan_section').hide();
                            }

                            if (response.alasan_disposisi) {
                                $('#alasan_section').show();
                                $('#detail_alasan_disposisi').text(response.alasan_disposisi);
                            } else {
                                $('#alasan_section').hide();
                            }
                        } else {
                            $('#analisa_section').hide();
                            $('#no_analisa_section').show();
                        }

                        $('#modalDetail').modal('show');
                    },
                    error: function(xhr) {
                        console.error('Detail Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal mengambil data detail.',
                        });
                    }
                });
            });

            // Submit Form
            $('#form').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-daily-tank.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Proses...');
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
                        console.error('Submit Error:', xhr.responseText);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            if (errors.tanggal_produksi) {
                                $('#tanggal_produksi').addClass('is-invalid');
                                $('.errorTanggalProduksi').html(errors.tanggal_produksi[0]);
                            }
                            if (errors.storage) {
                                $('#storage').addClass('is-invalid');
                                $('.errorStorage').html(errors.storage[0]);
                            }
                            if (errors.nomor_po) {
                                $('#nomor_po').addClass('is-invalid');
                                $('.errorNomorPO').html(errors.nomor_po[0]);
                            }
                            if (errors.sampling_point) {
                                $('#sampling_point').addClass('is-invalid');
                                $('.errorSamplingPoint').html(errors.sampling_point[0]);
                            }
                            if (errors.jenis_analisa) {
                                $('#jenis_analisa').addClass('is-invalid');
                                $('.errorJenisAnalisa').html(errors.jenis_analisa[0]);
                            }
                            if (errors.jenis_sample) {
                                $('#jenis_sample').addClass('is-invalid');
                                $('.errorJenisSample').html(errors.jenis_sample[0]);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }
                    }
                });
            });

            // Tombol Delete
            $('body').on('click', '#btnDelete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus saja!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('monitoring-daily-tank') }}/" + id,
                            dataType: "json",
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                console.error('Delete Error:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal menghapus data.',
                                });
                            }
                        });
                    }
                });
            });

            // Get PO by Date and Storage
            $('#tanggal_produksi, #storage').on('change', function() {
                const tanggal_produksi = $('#tanggal_produksi').val();
                const storage = $('#storage').val();
                const $nomorPO = $('#nomor_po');

                $nomorPO.empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('.errorNomorPO').html('');

                if (tanggal_produksi && storage) {
                    $.ajax({
                        url: "{{ route('monitoring-daily-tank.get-po') }}",
                        type: "POST",
                        data: {
                            tanggal_produksi: tanggal_produksi,
                            storage: storage
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $nomorPO.prop('disabled', true);
                            $nomorPO.html('<option value="">Loading...</option>');
                        },
                        success: function(response) {
                            $nomorPO.prop('disabled', false);
                            $nomorPO.empty().append(
                                '<option value="">-- Pilih Nomor PO --</option>');

                            if (response.status === 'success' && response.count > 0) {
                                response.po_list.forEach(item => {
                                    $nomorPO.append(
                                        `<option value="${item.id}">${item.po_number}</option>`
                                    );
                                });

                                if (response.count === 1 && response.selected_id) {
                                    $nomorPO.val(response.selected_id);
                                }
                            } else {
                                $nomorPO.append(
                                    '<option value="">-- Tidak Ada PO Release --</option>');
                                $('.errorNomorPO').html(
                                    '<small class="text-danger">Tidak ada Nomor PO yang Release.</small>'
                                );
                            }
                        },
                        error: function(xhr) {
                            console.error('Get PO Error:', xhr.responseText);
                            $nomorPO.prop('disabled', false);
                            $nomorPO.empty().append(
                                '<option value="">-- Gagal mengambil data --</option>');
                            $('.errorNomorPO').html(
                                '<small class="text-danger">Terjadi kesalahan saat mengambil data PO.</small>'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endsection
