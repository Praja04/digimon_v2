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

                        <div class="col-lg-6">
                            <label for="nomor_po" class="form-label">Nomor PO <span style="color: red;">*</span></label>
                            <select id="nomor_po" name="nomor_po" class="select2 form-control">
                                <option value="">-- Pilih Nomor PO --</option>
                                @foreach ($passedPoNumbers as $id => $poNumber)
                                    <option value="{{ $id }}">{{ $poNumber }}</option>
                                @endforeach
                            </select>
                            <small class="text-danger errorNomorPO"></small>
                        </div>

                        <div class="col-lg-6">
                            <label for="variant" class="form-label">Variant <span style="color: red;">*</span></label>
                            <select id="variant" name="variant" class="select2 form-control">
                                <option value="">-- Pilih Variant --</option>
                                <option value="Pouch 77 gram - SS2">Pouch 77 gram - SS2</option>
                                <option value="Pouch 250 gram - JB">Pouch 250 gram - JB</option>
                                <option value="Pouch 550 gram - JB">Pouch 550 gram - JB</option>
                                <option value="Pouch 700 gram - JB">Pouch 700 gram - JB</option>
                                <option value="Pouch 1000 gram - JB">Pouch 1000 gram - JB</option>
                                <option value="BB 40 gram - BB">BB 40 gram - BB</option>
                                <option value="BB 77 gram - BB">BB 77 gram - BB</option>
                                <option value="BB 725 gram - BB">BB 725 gram - BB</option>
                                <option value="BB 270 gram - BB">BB 270 gram - BB</option>
                                <option value="Sachet 20 gram - SS1">Sachet 20 gram - SS1</option>
                                <option value="Sachet 12,5 gram - SS1">Sachet 12,5 gram - SS1</option>
                                <option value="Jeriken 6 kg - JB">Jeriken 6 kg - JB</option>
                                <option value="Jeriken 25 kg MSD - MSD">Jeriken 25 kg MSD - MSD</option>
                                <option value="Jeriken 25 kg - JB">Jeriken 25 kg - JB</option>
                                <option value="Kempu (Lokal) - MSD Lokal">Kempu (Lokal) - MSD Lokal</option>
                                <option value="Kempu (NR2) - NR2">Kempu (NR2) - NR2</option>
                            </select>
                            <small class="text-danger errorVariant"></small>
                        </div>
                        <!-- No Filler -->
                        <div class="col-lg-6">
                            <label for="no_filler" class="form-label">No Filler <span
                                    style="color: red;">*</span></label>
                            <input type="number" name="no_filler" id="no_filler" class="form-control">
                            <small class="text-danger errorNoFiller"></small>
                        </div>

                        <div class="col-lg-6">
                            <label for="no_kempu_jeriken" class="form-label">No Kempu / Jeriken <span
                                    style="color: red;">*</span></label>
                            <input type="number" name="no_kempu_jeriken" id="no_kempu_jeriken" class="form-control">
                            <small class="text-danger errorNoKempuJeriken"></small>
                        </div>

                        <div class="col-lg-6">
                            <label for="koding" class="form-label">Koding <span style="color: red;">*</span></label>
                            <input type="text" name="koding" id="koding" class="form-control">
                            <small class="text-danger errorKoding"></small>
                        </div>

                        <div class="col-lg-6">
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
                                <option value="Sampel Jam 24:00">Sampel Jam 24:00</option>
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
                                <option value="Sampel Jam 24:00">Sampel Jam 24:00</option>
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
                                <option value="Sampel Jam 24:00">Sampel Jam 24:00</option>
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
                        <button type="submit" class="btn btn-success" id="save">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Monitoring Ongoing Mikro -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title">Detail Monitoring Ongoing Mikro</h5>
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

                    <!-- Info Dasar Section -->
                    <h6 class="mb-2 small">Informasi Dasar :</h6>
                    <div class="row g-2 small mb-3">
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Storage</span>
                                <strong id="detail_storage">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Nomor PO</span>
                                <strong id="detail_po_number">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Formulasi</span>
                                <strong id="detail_formulation">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Variant</span>
                                <strong id="detail_variant">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">No Filler</span>
                                <strong id="detail_no_filler">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">No Kempu/Jeriken</span>
                                <strong id="detail_no_kempu_jeriken">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Tanggal Filling</span>
                                <strong id="detail_filling_date">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Koding</span>
                                <strong id="detail_koding">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Jam Koding</span>
                                <strong id="detail_jam_koding">-</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <span class="text-muted d-block">Jenis Sampel</span>
                                <strong id="detail_jenis_sampel">-</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <span class="text-muted d-block">Keterangan</span>
                                <p id="detail_keterangan" class="mb-0">-</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Dibuat Pada</span>
                                <strong id="detail_created_at">-</strong>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <!-- Analisa Lab Mikro Section -->
                    <h6 class="mb-2 small">Analisa Lab Mikro :</h6>
                    <div class="row g-2 small mb-3">
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Analis EB</span>
                                <strong id="detail_analis_eb">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Analis TPC</span>
                                <strong id="detail_analis_tpc">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Analis YM</span>
                                <strong id="detail_analis_ym">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Analis Benda Asing</span>
                                <strong id="detail_analis_benda_asing">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Shift</span>
                                <strong id="detail_shift">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Diterima Pada</span>
                                <strong id="detail_received_at">-</strong>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <!-- Parameter Analisa Section -->
                    <h6 class="mb-2 small">Parameter Analisa Mikro :</h6>
                    <div class="row g-2 small mb-3">
                        <div class="col-6 col-md-3">
                            <div class="mb-2">
                                <span class="text-muted d-block">EB</span>
                                <strong id="detail_eb">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-2">
                                <span class="text-muted d-block">TPC</span>
                                <strong id="detail_tpc">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-2">
                                <span class="text-muted d-block">YM</span>
                                <strong id="detail_ym">-</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="mb-2">
                                <span class="text-muted d-block">Benda Asing</span>
                                <strong id="detail_benda_asing">-</strong>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <!-- Status & Disposition Section -->
                    <h6 class="mb-2 small">Hasil & Disposisi :</h6>
                    <div class="row g-2 small">
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Hasil</span>
                                <span id="detail_hasil" class="badge">-</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Disposition</span>
                                <strong id="detail_disposition">-</strong>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="mb-2">
                                <span class="text-muted d-block">Diupdate Pada</span>
                                <strong id="detail_updated_at">-</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-2">
                                <span class="text-muted d-block">Remarks</span>
                                <p id="detail_remarks" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
                $('#nomor_po').val('').trigger('change');
                $('#variant').val('').trigger('change');
                $('#jenis_sampel_1').val('').trigger('change');
                $('#jenis_sampel_2').val('').trigger('change');
                $('#jenis_sampel_3').val('').trigger('change');

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
                        $('#detail_storage, #detail_po_number, #detail_formulation, #detail_variant, #detail_jenis_sampel, #detail_filling_date, #detail_jam_koding, #detail_created_at')
                            .text('-');
                        $('#detail_analis, #detail_shift, #detail_received_at').text('-');
                        $('#detail_berat_jenis, #detail_visco, #detail_brix, #detail_aw, #detail_nacl, #detail_ph, #detail_color, #detail_organo')
                            .text('-');
                        $('#detail_status').removeClass('bg-success bg-danger').addClass(
                            'bg-secondary').text('-');
                        $('#detail_disposition, #detail_remarks, #detail_updated_at').text('-');
                    },
                    success: function(response) {
                        // Show modal
                        $('#modalDetail').modal('show');

                        // QR Code
                        $('#qr_code_container').html(
                            `<img src="data:image/png;base64,${response.qr_code}" alt="QR Code" style="width: 150px; height: 150px;">`
                        );
                        $('#qr_code_text').text(`Mikro_${response.variant}_${response.jenis_sampel}`);

                        // Informasi Dasar
                        $('#detail_storage').text(response.storage);
                        $('#detail_po_number').text(response.po_number);
                        $('#detail_formulation').text(response.formulation);
                        $('#detail_variant').text(response.variant);
                        $('#detail_no_filler').text(response.no_filler);
                        $('#detail_no_kempu_jeriken').text(response.no_kempu_jeriken);
                        $('#detail_filling_date').text(response.filling_date_formatted);
                        $('#detail_koding').text(response.koding);
                        $('#detail_jam_koding').text(response.jam_koding);
                        $('#detail_jenis_sampel').text(response.jenis_sampel);
                        $('#detail_keterangan').text(response.keterangan);
                        $('#detail_created_at').text(response.created_at_formatted);

                        // Analisa Lab Mikro
                        $('#detail_analis_eb').text(response.analis_eb_name);
                        $('#detail_analis_tpc').text(response.analis_tpc_name);
                        $('#detail_analis_ym').text(response.analis_ym_name);
                        $('#detail_analis_benda_asing').text(response.analis_benda_asing_name);
                        $('#detail_shift').text(response.shift);
                        $('#detail_received_at').text(response.received_at_formatted);

                        // Parameter Analisa Mikro
                        $('#detail_eb').text(response.eb);
                        $('#detail_tpc').text(response.tpc);
                        $('#detail_ym').text(response.ym);
                        $('#detail_benda_asing').text(response.benda_asing);

                        // Hasil & Disposisi
                        const hasilBadge = $('#detail_hasil');
                        hasilBadge.text(response.hasil);
                        if (response.hasil === 'Pass') {
                            hasilBadge.removeClass().addClass('badge bg-success');
                        } else if (response.hasil === 'Fail') {
                            hasilBadge.removeClass().addClass('badge bg-danger');
                        } else {
                            hasilBadge.removeClass().addClass('badge bg-secondary');
                        }

                        $('#detail_disposition').text(response.disposition);
                        $('#detail_remarks').text(response.remarks);
                        $('#detail_updated_at').text(response.updated_at_formatted);
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
        });
    </script>
@endsection
