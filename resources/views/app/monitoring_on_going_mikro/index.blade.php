@extends('layouts.component.main')
@section('title', 'Monitoring On Going Mikro')
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
                                            <th>Nomor PO</th>
                                            <th>Variant</th>
                                            <th>Tanggal Filling</th>
                                            <th>Status</th>
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
                        <h5 class="modal-title">Input Monitoring On Going Mikro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-lg-4">
                            <input type="hidden" name="id" id="id">
                            <label for="tanggal_produksi" class="form-label">Tanggal Produksi <span
                                    style="color: red;">*</span></label>
                            <input type="date" name="tanggal_produksi" id="tanggal_produksi" class="form-control">
                            <small class="errorTanggalProduksi text-danger"></small>
                        </div>

                        <div class="col-lg-4">
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

                        <div class="col-lg-4">
                            <label for="nomor_po" class="form-label">Nomor PO <span style="color: red;">*</span></label>
                            <select id="nomor_po" name="nomor_po" class="select2 form-control">
                                <option value="">-- Pilih Nomor PO --</option>
                            </select>
                            <small class="text-danger errorNomorPO"></small>
                        </div>

                        <div class="col-lg-12">
                            <label for="variant" class="form-label">Variant <span style="color: red;">*</span></label>
                            <select id="variant" name="variant" class="select2 form-control">
                                <option value="">-- Pilih Variant --</option>
                            </select>
                            <small class="text-danger errorVariant"></small>
                        </div>

                        <!-- No Filler -->
                        <div class="col-lg-4" id="no_filler_wrapper">
                            <label for="no_filler" class="form-label">No Filler / Mesin <span
                                    style="color: red;">*</span></label>
                            <input type="text" name="no_filler" id="no_filler" class="form-control">
                            <small class="text-danger errorNoFiller"></small>
                        </div>

                        <!-- No Kempu/Jeriken -->
                        <div class="col-lg-4" id="no_kempu_jeriken_wrapper" style="display: none;">
                            <label for="no_kempu_jeriken" class="form-label">No Kempu / Jeriken <span
                                    style="color: red;">*</span></label>
                            <input type="number" name="no_kempu_jeriken" id="no_kempu_jeriken" class="form-control">
                            <small class="text-danger errorNoKempuJeriken"></small>
                        </div>

                        <!-- Running Number -->
                        <div class="col-lg-4" id="running_number_wrapper" style="display: none;">
                            <label for="running_number" class="form-label">Running Number <span
                                    style="color: red;">*</span></label>
                            <input type="number" name="running_number" id="running_number" class="form-control">
                            <small class="text-danger errorRunningNumber"></small>
                        </div>

                        <!-- Koding -->
                        <div class="col-lg-6" id="koding_wrapper">
                            <label for="koding" class="form-label">Koding</label>
                            <input type="text" name="koding" id="koding" class="form-control">
                            <small class="text-danger errorKoding"></small>
                        </div>

                        <!-- Jam Koding -->
                        <div class="col-lg-6" id="jam_koding_wrapper">
                            <label for="jam_koding" class="form-label">Jam Koding <span
                                    style="color: red;">*</span></label>
                            <input type="time" name="jam_koding" id="jam_koding" class="form-control">
                            <small class="text-danger errorJamKoding"></small>
                        </div>

                        <!-- Jenis Sampel 3 kolom -->
                        <div class="col-lg-4">
                            <label for="jenis_sampel_1" class="form-label">Jenis Sampel 1 <span
                                    style="color: red;">*</span></label>
                            <select id="jenis_sampel_1" name="jenis_sampel_1" class="select2 form-control">
                                <option value="">-- Pilih --</option>
                                <option value="Sampel Jam 00:00">Sampel Jam 00:00</option>
                                <option value="Sampel Jam 01:00">Sampel Jam 01:00</option>
                                <option value="Sampel Jam 02:00">Sampel Jam 02:00</option>
                                <option value="Sampel Jam 03:00">Sampel Jam 03:00</option>
                                <option value="Sampel Jam 04:00">Sampel Jam 04:00</option>
                                <option value="Sampel Jam 05:00">Sampel Jam 05:00</option>
                                <option value="Sampel Jam 06:00">Sampel Jam 06:00</option>
                                <option value="Sampel Jam 07:00">Sampel Jam 07:00</option>
                                <option value="Sampel Jam 08:00">Sampel Jam 08:00</option>
                                <option value="Sampel Jam 09:00">Sampel Jam 09:00</option>
                                <option value="Sampel Jam 10:00">Sampel Jam 10:00</option>
                                <option value="Sampel Jam 11:00">Sampel Jam 11:00</option>
                                <option value="Sampel Jam 12:00">Sampel Jam 12:00</option>
                                <option value="Sampel Jam 13:00">Sampel Jam 13:00</option>
                                <option value="Sampel Jam 14:00">Sampel Jam 14:00</option>
                                <option value="Sampel Jam 15:00">Sampel Jam 15:00</option>
                                <option value="Sampel Jam 16:00">Sampel Jam 16:00</option>
                                <option value="Sampel Jam 17:00">Sampel Jam 17:00</option>
                                <option value="Sampel Jam 18:00">Sampel Jam 18:00</option>
                                <option value="Sampel Jam 19:00">Sampel Jam 19:00</option>
                                <option value="Sampel Jam 20:00">Sampel Jam 20:00</option>
                                <option value="Sampel Jam 21:00">Sampel Jam 21:00</option>
                                <option value="Sampel Jam 22:00">Sampel Jam 22:00</option>
                                <option value="Sampel Jam 23:00">Sampel Jam 23:00</option>
                                <option value="Awal Filling">Awal Filling</option>
                                <option value="Akhir Filling">Akhir Filling</option>
                                <option value="Awal Storage">Awal Storage</option>
                                <option value="Akhir Storage">Akhir Storage</option>
                                <option value="Awal PO">Awal PO</option>
                                <option value="Akhir PO">Akhir PO</option>
                                <option value="After Downtime">After Downtime</option>
                                <option value="Pergantian Roll">Pergantian Roll</option>
                                <option value="Gagal Filling Heater">Gagal Filling Heater</option>
                                <option value="Gagal Filling UV">Gagal Filling UV</option>
                                <option value="Jeriken">Jeriken</option>
                            </select>
                            <small class="text-danger errorJenisSampel1"></small>
                        </div>

                        <div class="col-lg-4">
                            <label for="jenis_sampel_2" class="form-label">Jenis Sampel 2</label>
                            <select id="jenis_sampel_2" name="jenis_sampel_2" class="select2 form-control">
                                <option value="">-- Pilih --</option>
                                <option value="Sampel Jam 00:00">Sampel Jam 00:00</option>
                                <option value="Sampel Jam 01:00">Sampel Jam 01:00</option>
                                <option value="Sampel Jam 02:00">Sampel Jam 02:00</option>
                                <option value="Sampel Jam 03:00">Sampel Jam 03:00</option>
                                <option value="Sampel Jam 04:00">Sampel Jam 04:00</option>
                                <option value="Sampel Jam 05:00">Sampel Jam 05:00</option>
                                <option value="Sampel Jam 06:00">Sampel Jam 06:00</option>
                                <option value="Sampel Jam 07:00">Sampel Jam 07:00</option>
                                <option value="Sampel Jam 08:00">Sampel Jam 08:00</option>
                                <option value="Sampel Jam 09:00">Sampel Jam 09:00</option>
                                <option value="Sampel Jam 10:00">Sampel Jam 10:00</option>
                                <option value="Sampel Jam 11:00">Sampel Jam 11:00</option>
                                <option value="Sampel Jam 12:00">Sampel Jam 12:00</option>
                                <option value="Sampel Jam 13:00">Sampel Jam 13:00</option>
                                <option value="Sampel Jam 14:00">Sampel Jam 14:00</option>
                                <option value="Sampel Jam 15:00">Sampel Jam 15:00</option>
                                <option value="Sampel Jam 16:00">Sampel Jam 16:00</option>
                                <option value="Sampel Jam 17:00">Sampel Jam 17:00</option>
                                <option value="Sampel Jam 18:00">Sampel Jam 18:00</option>
                                <option value="Sampel Jam 19:00">Sampel Jam 19:00</option>
                                <option value="Sampel Jam 20:00">Sampel Jam 20:00</option>
                                <option value="Sampel Jam 21:00">Sampel Jam 21:00</option>
                                <option value="Sampel Jam 22:00">Sampel Jam 22:00</option>
                                <option value="Sampel Jam 23:00">Sampel Jam 23:00</option>
                                <option value="Awal Filling">Awal Filling</option>
                                <option value="Akhir Filling">Akhir Filling</option>
                                <option value="Awal Storage">Awal Storage</option>
                                <option value="Akhir Storage">Akhir Storage</option>
                                <option value="Awal PO">Awal PO</option>
                                <option value="Akhir PO">Akhir PO</option>
                                <option value="After Downtime">After Downtime</option>
                                <option value="Pergantian Roll">Pergantian Roll</option>
                                <option value="Gagal Filling Heater">Gagal Filling Heater</option>
                                <option value="Gagal Filling UV">Gagal Filling UV</option>
                                <option value="Jeriken">Jeriken</option>
                            </select>
                            <small class="text-danger errorJenisSampel2"></small>
                        </div>

                        <div class="col-lg-4">
                            <label for="jenis_sampel_3" class="form-label">Jenis Sampel 3</label>
                            <select id="jenis_sampel_3" name="jenis_sampel_3" class="select2 form-control">
                                <option value="">-- Pilih --</option>
                                <option value="Sampel Jam 00:00">Sampel Jam 00:00</option>
                                <option value="Sampel Jam 01:00">Sampel Jam 01:00</option>
                                <option value="Sampel Jam 02:00">Sampel Jam 02:00</option>
                                <option value="Sampel Jam 03:00">Sampel Jam 03:00</option>
                                <option value="Sampel Jam 04:00">Sampel Jam 04:00</option>
                                <option value="Sampel Jam 05:00">Sampel Jam 05:00</option>
                                <option value="Sampel Jam 06:00">Sampel Jam 06:00</option>
                                <option value="Sampel Jam 07:00">Sampel Jam 07:00</option>
                                <option value="Sampel Jam 08:00">Sampel Jam 08:00</option>
                                <option value="Sampel Jam 09:00">Sampel Jam 09:00</option>
                                <option value="Sampel Jam 10:00">Sampel Jam 10:00</option>
                                <option value="Sampel Jam 11:00">Sampel Jam 11:00</option>
                                <option value="Sampel Jam 12:00">Sampel Jam 12:00</option>
                                <option value="Sampel Jam 13:00">Sampel Jam 13:00</option>
                                <option value="Sampel Jam 14:00">Sampel Jam 14:00</option>
                                <option value="Sampel Jam 15:00">Sampel Jam 15:00</option>
                                <option value="Sampel Jam 16:00">Sampel Jam 16:00</option>
                                <option value="Sampel Jam 17:00">Sampel Jam 17:00</option>
                                <option value="Sampel Jam 18:00">Sampel Jam 18:00</option>
                                <option value="Sampel Jam 19:00">Sampel Jam 19:00</option>
                                <option value="Sampel Jam 20:00">Sampel Jam 20:00</option>
                                <option value="Sampel Jam 21:00">Sampel Jam 21:00</option>
                                <option value="Sampel Jam 22:00">Sampel Jam 22:00</option>
                                <option value="Sampel Jam 23:00">Sampel Jam 23:00</option>
                                <option value="Awal Filling">Awal Filling</option>
                                <option value="Akhir Filling">Akhir Filling</option>
                                <option value="Awal Storage">Awal Storage</option>
                                <option value="Akhir Storage">Akhir Storage</option>
                                <option value="Awal PO">Awal PO</option>
                                <option value="Akhir PO">Akhir PO</option>
                                <option value="After Downtime">After Downtime</option>
                                <option value="Pergantian Roll">Pergantian Roll</option>
                                <option value="Gagal Filling Heater">Gagal Filling Heater</option>
                                <option value="Gagal Filling UV">Gagal Filling UV</option>
                                <option value="Jeriken">Jeriken</option>
                            </select>
                            <small class="text-danger errorJenisSampel3"></small>
                        </div>

                        <div class="col-lg-12">
                            <label for="filling_date" class="form-label">Tanggal Filling <span
                                    style="color: red;">*</span></label>
                            <input type="date" name="filling_date" id="filling_date" class="form-control">
                            <small class="text-danger errorFillingDate"></small>
                        </div>

                        <div class="col-lg-12">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3"
                                oninput="this.value = this.value.toUpperCase();"></textarea>
                            <small class="text-danger errorKeterangan"></small>
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

    <!-- Modal Detail Monitoring Ongoing Mikro -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Monitoring Ongoing Mikro</h5>
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

                    <!-- Informasi Dasar -->
                    <div class="mb-3">
                        <h6 class="mb-2 fw-bold">Informasi Dasar</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="35%">Storage</td>
                                <td width="5%">:</td>
                                <td><strong id="detail_storage">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nomor PO</td>
                                <td>:</td>
                                <td><strong id="detail_po_number">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Variant</td>
                                <td>:</td>
                                <td><strong id="detail_variant">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">No Filler</td>
                                <td>:</td>
                                <td><strong id="detail_no_filler">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">No Kempu/Jeriken</td>
                                <td>:</td>
                                <td><strong id="detail_no_kempu_jeriken">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Running Number</td>
                                <td>:</td>
                                <td><strong id="detail_running_number">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Filling</td>
                                <td>:</td>
                                <td><strong id="detail_filling_date">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Koding</td>
                                <td>:</td>
                                <td><strong id="detail_koding">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jam Koding</td>
                                <td>:</td>
                                <td><strong id="detail_jam_koding">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Sampel</td>
                                <td>:</td>
                                <td><strong id="detail_jenis_sampel">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keterangan</td>
                                <td>:</td>
                                <td><strong id="detail_keterangan">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dibuat Pada</td>
                                <td>:</td>
                                <td><strong id="detail_created_at">-</strong></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Parameter Uji Mikrobiologi -->
                    <div class="mb-3 pt-3 border-top">
                        <h6 class="mb-2 fw-bold">Parameter Uji Mikro</h6>
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="border rounded p-2">
                                    <small class="text-muted d-block">EB</small>
                                    <strong class="d-block" id="detail_eb">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2">
                                    <small class="text-muted d-block">TPC</small>
                                    <strong class="d-block" id="detail_tpc">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2">
                                    <small class="text-muted d-block">YM</small>
                                    <strong class="d-block" id="detail_ym">-</strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="border rounded p-2">
                                    <small class="text-muted d-block">Benda Asing</small>
                                    <strong class="d-block" id="detail_benda_asing">-</strong>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted d-block">Hasil</small>
                            <div id="detail_hasil">-</div>
                        </div>
                    </div>

                    <!-- Hasil & Disposisi -->
                    <div class="pt-3 border-top">
                        <h6 class="mb-2 fw-bold">Hasil & Disposisi</h6>
                        <div class="mb-2">
                            <small class="text-muted d-block">Disposisi</small>
                            <strong id="detail_disposition">-</strong>
                        </div>
                        <div id="remarks_section" class="mb-2" style="display: none;">
                            <small class="text-muted d-block">Remarks</small>
                            <div class="border rounded p-2 bg-light">
                                <em id="detail_remarks" class="small">-</em>
                            </div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Diupdate Pada</small>
                            <strong id="detail_updated_at">-</strong>
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
        $('.select2').select2({
            placeholder: '-- Pilih Opsi --',
            dropdownParent: $('#modal')
        });

        function checkVariantType(variantName) {
            const isKempuOrJeriken = variantName.toLowerCase().includes('kempu') ||
                variantName.toLowerCase().includes('jeriken');

            const $noKempuJerikenWrapper = $('#no_kempu_jeriken_wrapper');
            const $runningNumberWrapper = $('#running_number_wrapper');
            const $noFillerWrapper = $('#no_filler_wrapper');
            const $kodingWrapper = $('#koding_wrapper');
            const $jamKodingWrapper = $('#jam_koding_wrapper');

            if (isKempuOrJeriken) {
                // Tampilkan field Kempu/Jeriken dan Running Number
                $noKempuJerikenWrapper.show();
                $runningNumberWrapper.show();

                // Set kolom: No Filler, No Kempu/Jeriken, Running Number = 4-4-4 (3 kolom)
                $noFillerWrapper.removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');
                $noKempuJerikenWrapper.removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');
                $runningNumberWrapper.removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');

                // Koding dan Jam Koding tetap 6-6
                $kodingWrapper.removeClass('col-lg-12 col-lg-4 col-lg-8').addClass('col-lg-6');
                $jamKodingWrapper.removeClass('col-lg-12 col-lg-4 col-lg-8').addClass('col-lg-6');

            } else {
                // Sembunyikan field Kempu/Jeriken dan Running Number
                $noKempuJerikenWrapper.hide();
                $runningNumberWrapper.hide();

                // Reset error dan value
                $('.errorNoKempuJeriken, .errorRunningNumber').html('');
                $('#no_kempu_jeriken, #running_number').removeClass('is-invalid').val('');

                // No Filler jadi col-lg-4
                $noFillerWrapper.removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');

                // Koding dan Jam Koding jadi col-lg-4 (total 3 field: filler, koding, jam = 4-4-4)
                $kodingWrapper.removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');
                $jamKodingWrapper.removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');
            }
        }

        function loadVariantByPo(production_batch_id) {
            const $variant = $('#variant');

            // Reset variant dropdown
            $variant.empty().append('<option value="">-- Pilih Variant --</option>');
            $('.errorVariant').html('');

            $('#no_kempu_jeriken_wrapper, #running_number_wrapper').hide();
            $('#no_filler_wrapper').removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');
            $('#koding_wrapper').removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');
            $('#jam_koding_wrapper').removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');

            if (production_batch_id) {
                $.ajax({
                    url: "{{ route('monitoring-ongoing-mikro.get-variant') }}",
                    type: "POST",
                    data: {
                        production_batch_id: production_batch_id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $variant.prop('disabled', true);
                        $variant.empty().append('<option value="">Memuat variant...</option>');
                    },
                    success: function(response) {
                        $variant.prop('disabled', false);

                        if (response.status === 'success' && response.count > 0) {
                            $variant.empty().append(
                                '<option value="">-- Pilih Variant --</option>');

                            response.variant_list.forEach(item => {
                                $variant.append(
                                    `<option value="${item.display_name}">${item.display_name}</option>`
                                );
                            });

                            // Auto-select jika hanya ada 1 variant
                            if (response.count === 1) {
                                $variant.val(response.variant_list[0].display_name).trigger(
                                    'change');
                                checkVariantType(response.variant_list[0].display_name);
                            }
                        } else {
                            $variant.empty().append(
                                '<option value="">-- Tidak Ada Variant --</option>');
                            $('.errorVariant').html(
                                '<small class="text-danger">Tidak ada variant yang tersedia untuk PO ini.</small>'
                            );
                        }
                    },
                    error: function(xhr) {
                        $variant.prop('disabled', false);
                        $variant.empty().append(
                            '<option value="">-- Gagal mengambil data --</option>');
                        $('.errorVariant').html(
                            '<small class="text-danger">Terjadi kesalahan saat mengambil data variant.</small>'
                        );

                        console.error('Error:', xhr.responseJSON);
                    }
                });
            } else {
                $variant.prop('disabled', true);
            }
        }

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

            // Inisialisasi DataTable
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('monitoring-ongoing-mikro.index') }}",
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
                        data: 'po_number',
                        name: 'po_number'
                    },
                    {
                        data: 'variant',
                        name: 'variant'
                    },
                    {
                        data: 'filling_date',
                        name: 'filling_date'
                    },
                    {
                        data: 'hasil',
                        name: 'hasil',
                        orderable: false,
                        searchable: false
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
                $('#nomor_po').val('').trigger('change').prop('disabled', true);
                $('#variant').val('').trigger('change').prop('disabled', true);
                $('#jenis_sampel_1').val('').trigger('change');
                $('#jenis_sampel_2').val('').trigger('change');
                $('#jenis_sampel_3').val('').trigger('change');

                $('#no_kempu_jeriken_wrapper, #running_number_wrapper').hide();
                $('#no_filler_wrapper').removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');
                $('#koding_wrapper').removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');
                $('#jam_koding_wrapper').removeClass('col-lg-12 col-lg-6 col-lg-8').addClass('col-lg-4');

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
                $('#modal').modal('show');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-ongoing-mikro.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#save').val("edit-data");

                        $('#form').trigger("reset");
                        $('#id').val('');

                        $('#storage').val('').trigger('change');
                        $('#nomor_po').val('').trigger('change');
                        $('#variant').val('').trigger('change');
                        $('#jenis_sampel').val('').trigger('change');

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#id').val(response.id);
                        $('#storage').val(response.storage).trigger('change');
                        $('#nomor_po').val(response.production_batch_id).trigger('change');
                        loadVariantByPo(response.production_batch_id);
                        setTimeout(function() {
                            $('#variant').val(response.variant).trigger('change');

                            checkVariantType(response.variant);
                            $('#no_kempu_jeriken').val(response.no_kempu_jeriken);
                        }, 500);
                        $('#variant').val(response.variant).trigger('change');
                        $('#no_filler').val(response.no_filler);
                        $('#no_kempu_jeriken').val(response.no_kempu_jeriken);
                        $('#koding').val(response.koding);
                        $('#jam_koding').val(response.jam_koding);
                        $('#filling_date').val(response.filling_date);
                        $('#jenis_sampel_1').val(response.jenis_sampel_1).trigger('change');
                        $('#jenis_sampel_2').val(response.jenis_sampel_2).trigger('change');
                        $('#jenis_sampel_3').val(response.jenis_sampel_3).trigger('change');
                        $('#keterangan').val(response.keterangan);

                        $('#modal').modal('show');
                    }
                });
            })

            $('body').on('click', '.btn-detail', function() {
                let id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('monitoring-ongoing-mikro.show', '') }}/" + id,
                    dataType: "json",
                    beforeSend: function() {
                        // Reset semua field ke default
                        $('#detail_storage, #detail_po_number, #detail_variant, #detail_no_filler, #detail_no_kempu_jeriken, #detail_running_number')
                            .text('-');
                        $('#detail_filling_date, #detail_koding, #detail_jam_koding, #detail_jenis_sampel, #detail_keterangan, #detail_created_at')
                            .text('-');
                        $('#detail_shift, #detail_analis_eb, #detail_analis_tpc, #detail_analis_ym, #detail_analis_benda_asing, #detail_received_at')
                            .text('-');
                        $('#detail_eb, #detail_tpc, #detail_ym, #detail_benda_asing').text('-');
                        $('#detail_disposition, #detail_remarks, #detail_updated_at').text('-');
                    },
                    success: function(response) {
                        // QR Code
                        if (response.qr_code) {
                            $('#qr_code_container').html('<img src="data:image/png;base64,' +
                                response.qr_code +
                                '" alt="QR Code" style="max-width: 150px;">');
                            let qrText = 'MONITORING-ONGOING-MIKRO/' + response.po_number +
                                '/' + response
                                .date + '/' + response.id;
                            $('#qr_code_text').text(qrText);
                        } else {
                            $('#qr_code_container').html(
                                '<p class="text-muted small">QR Code tidak tersedia</p>');
                            $('#qr_code_text').text('-');
                        }

                        // Informasi Dasar
                        $('#detail_storage').text(response.storage || '-');
                        $('#detail_po_number').text(response.po_number || '-');
                        $('#detail_variant').text(response.variant || '-');
                        $('#detail_no_filler').text(response.no_filler || '-');
                        $('#detail_no_kempu_jeriken').text(response.no_kempu_jeriken || '-');
                        $('#detail_running_number').text(response.running_number || '-');
                        $('#detail_filling_date').text(response.filling_date_formatted || '-');
                        $('#detail_koding').text(response.koding || '-');
                        $('#detail_jam_koding').text(response.jam_koding || '-');
                        $('#detail_jenis_sampel').text(response.jenis_sampel || '-');
                        $('#detail_keterangan').text(response.keterangan || '-');
                        $('#detail_created_at').text(response.created_at_formatted || '-');

                        // Data Analisa
                        $('#detail_shift').text(response.shift ? "Shift " + response.shift :
                            '-');
                        $('#detail_analis_eb').text(response.analis_eb_name || '-');
                        $('#detail_analis_tpc').text(response.analis_tpc_name || '-');
                        $('#detail_analis_ym').text(response.analis_ym_name || '-');
                        $('#detail_analis_benda_asing').text(response.analis_benda_asing_name ||
                            '-');
                        $('#detail_received_at').text(response.received_at_formatted || '-');

                        // Parameter Mikrobiologi
                        $('#detail_eb').text(response.eb || '-');
                        $('#detail_tpc').text(response.tpc || '-');
                        $('#detail_ym').text(response.ym || '-');
                        $('#detail_benda_asing').text(response.benda_asing || '-');

                        // Hasil
                        let hasilHtml = '-';
                        if (response.hasil === 'OK' || response.hasil === 'Pass') {
                            hasilHtml = '<span class="badge bg-success">' + response.hasil +
                                '</span>';
                        } else if (response.hasil === 'NOT OK' || response.hasil === 'Fail') {
                            hasilHtml = '<span class="badge bg-danger">' + response.hasil +
                                '</span>';
                        } else if (response.hasil) {
                            hasilHtml =
                                '<span class="badge bg-warning text-dark">PENDING</span>';
                        }
                        $('#detail_hasil').html(hasilHtml);

                        // Disposisi
                        $('#detail_disposition').text(response.disposition || '-');

                        // Remarks (tampilkan hanya jika ada)
                        if (response.remarks) {
                            $('#remarks_section').show();
                            $('#detail_remarks').text(response.remarks);
                        } else {
                            $('#remarks_section').hide();
                        }

                        $('#detail_updated_at').text(response.updated_at_formatted || '-');

                        $('#modalDetail').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal memuat detail data.',
                        });
                    }
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('monitoring-ongoing-mikro.store') }}",
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
                            if (errors.tanggal_produksi) {
                                $('#tanggal_produksi').addClass('is-invalid');
                                $('.errorTanggalProduksi').html(errors.tanggal_produksi.join(
                                    '<br>'));
                            }

                            if (errors.storage) {
                                $('#storage').addClass('is-invalid');
                                $('.errorStorage').html(errors.storage.join('<br>'));
                            }
                            if (errors.nomor_po) {
                                $('#nomor_po').addClass('is-invalid');
                                $('.errorNomorPO').html(errors.nomor_po.join('<br>'));
                            }

                            if (errors.variant) {
                                $('#variant').addClass('is-invalid');
                                $('.errorVariant').html(errors.variant.join('<br>'));
                            }

                            if (errors.no_filler) {
                                $('#no_filler').addClass('is-invalid');
                                $('.errorNoFiller').html(errors.no_filler.join('<br>'));
                            }

                            if (errors.no_kempu_jeriken) {
                                $('#no_kempu_jeriken').addClass('is-invalid');
                                $('.errorNoKempuJeriken').html(errors.no_kempu_jeriken.join(
                                    '<br>'));
                            }

                            if (errors.running_number) {
                                $('#running_number').addClass('is-invalid');
                                $('.errorRunningNumber').html(errors.running_number.join(
                                    '<br>'));
                            }

                            if (errors.koding) {
                                $('#koding').addClass('is-invalid');
                                $('.errorKoding').html(errors.koding.join('<br>'));
                            }

                            if (errors.jam_koding) {
                                $('#jam_koding').addClass('is-invalid');
                                $('.errorJamKoding').html(errors.jam_koding.join('<br>'));
                            }

                            if (errors.jenis_sampel_1) {
                                $('#jenis_sampel_1').addClass('is-invalid');
                                $('.errorJenisSampel1').html(errors.jenis_sampel_1.join(
                                    '<br>'));
                            }

                            if (errors.filling_date) {
                                $('#filling_date').addClass('is-invalid');
                                $('.errorFillingDate').html(errors.filling_date.join('<br>'));
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
                            url: "monitoring-ongoing-mikro/" + id,
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

            $('#variant').on('change', function() {
                const selectedVariant = $(this).val();
                if (selectedVariant) {
                    checkVariantType(selectedVariant);
                }
            });

            $('#nomor_po').on('change', function() {
                const production_batch_id = $(this).val();
                loadVariantByPo(production_batch_id);
            });

            $('#tanggal_produksi, #storage').on('change', function() {
                const tanggal_produksi = $('#tanggal_produksi').val();
                const storage = $('#storage').val();
                const $nomorPO = $('#nomor_po');

                $nomorPO.empty().append('<option value="">-- Pilih Nomor PO --</option>');
                $('.errorNomorPO').html('');

                if (tanggal_produksi && storage) {
                    $.ajax({
                        url: "{{ route('monitoring-ongoing-mikro.get-po') }}",
                        type: "POST",
                        data: {
                            tanggal_produksi: tanggal_produksi,
                            storage: storage
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            $nomorPO.prop('disabled', true);
                        },
                        success: function(response) {
                            $nomorPO.prop('disabled', false);

                            if (response.status === 'success' && response.count > 0) {

                                response.po_list.forEach(item => {
                                    $nomorPO.append(
                                        `<option value="${item.id}">${item.po_number}</option>`
                                    );
                                });

                                if (response.count === 1) {
                                    $nomorPO.val(response.selected_id).trigger('change');
                                    loadVariantByPo(response.selected_id);
                                } else if (response.count > 1) {
                                    $nomorPO.val('').trigger('change');
                                }
                            } else {
                                $nomorPO.empty().append(
                                        '<option value="">-- Tidak Ada PO Release --</option>')
                                    .val('');
                                $('.errorNomorPO').html(
                                    '<small class="text-danger">Tidak ada Nomor PO yang Release.</small>'
                                );
                            }
                        },
                        error: function() {
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
