@extends('layouts.component.main')
@section('title', 'Detail Sample - Shelf Life')
@section('styles')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-weight: 500;
            font-size: 0.8125rem;
        }

        .font-monospace {
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5px;
        }

        .card-header h5 {
            color: #2c3e50;
        }

        thead th {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }
    </style>
@endsection
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
                                <li class="breadcrumb-item"><a href="{{ route('shelf-life.sample.index') }}">Masuk
                                        Sample</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Detail Produksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 align-items-center">
                                <div class="col-sm-6 col-md-8">
                                    <h4 class="mb-1 text-dark">Nomor PO:
                                        <strong>{{ $data->productionBatch->po_number }}</strong>
                                    </h4>
                                </div>
                                <div class="col-sm-6 col-md-4 text-sm-end text-start mt-2 mt-sm-0">
                                    <button class="btn btn-primary" id="btnAdd">
                                        Tambah Data
                                    </button>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <p class="mb-0 text-muted small">Tanggal Produksi:</p>
                                    <h6 class="fw-bold">
                                        {{ \Carbon\Carbon::parse($data->productionBatch->date)->locale('id')->translatedFormat('d F Y') }}
                                    </h6>
                                </div>
                                <div class="col-6 col-md-4">
                                    <p class="mb-0 text-muted small">Varian:</p>
                                    <h6 class="fw-bold">{{ $data->productionBatch->variant }}</h6>
                                </div>
                                <div class="col-6 col-md-4">
                                    <p class="mb-0 text-muted small">Storage:</p>
                                    <h6 class="fw-bold">{{ $data->storage }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-0 mb-4">

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm mb-5">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0 fw-semibold">Data Sample Shelf Life</h5>
                            <p class="text-muted mb-0 small">Daftar detail sample yang telah diinput</p>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="fw-semibold ps-4">Bulan Ke-</th>
                                            <th class="fw-semibold">Kelompok Tanggal</th>
                                            <th class="fw-semibold">Tanggal Filling</th>
                                            <th class="fw-semibold">Variant FG</th>
                                            <th class="fw-semibold">Koding</th>
                                            <th class="fw-semibold">Jam Koding</th>
                                            <th class="fw-semibold">Lokasi</th>
                                            <th class="fw-semibold text-center pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                        @forelse ($shelfLifeSamplingDetails as $item)
                                            <tr>
                                                <td class="ps-4">
                                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                                        {{ $item->bulan_ke }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-medium">
                                                        {{ $item->kelompok_tanggal }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-medium text-dark">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_filling)->locale('id')->translatedFormat('d F Y') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-medium text-dark">{{ $item->variant_fg }}</div>
                                                    <small class="text-muted">{{ $item->kelompok_sample }}</small>
                                                </td>
                                                <td>
                                                    <span class="font-monospace fw-medium">{{ $item->koding }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $item->jam_koding }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium small">{{ $item->ruang_sl }}</span>
                                                        <span class="text-muted small">Bin:
                                                            {{ $item->bin_location }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center pe-4">
                                                    <button class="btn btn-primary btn-sm" id="btnShowQrCode"
                                                        data-bs-toggle="modal" data-bs-target="#qrModal{{ $item->id }}"
                                                        title="Lihat QR Code">
                                                        <i class="mdi mdi-qrcode"></i>
                                                    </button>

                                                    <!-- Modal QR Code -->
                                                    <div class="modal fade" id="qrModal{{ $item->id }}" tabindex="-1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content shadow-sm">
                                                                <div class="modal-header bg-light py-2">
                                                                    <h6 class="modal-title">QR Code</h6>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-center p-3"
                                                                    id="qrPrintArea{{ $item->id }}">
                                                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(env('QC_URL') . 'scan/shelf-life-sampling/' . $item->id, 'QRCODE') }}"
                                                                        alt="QR" class="img-fluid mb-2"
                                                                        style="max-width:180px;">
                                                                    <div class="small text-muted">
                                                                        Shelf
                                                                        Life_{{ $item->bin_location }}_{{ $item->id }}
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light py-2">
                                                                    <button type="button" class="btn btn-sm btn-light"
                                                                        data-bs-dismiss="modal">Tutup</button>
                                                                    <button
                                                                        onclick="printQR('qrPrintArea{{ $item->id }}')"
                                                                        class="btn btn-sm btn-primary">
                                                                        Cetak
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button class="btn btn-warning btn-sm ms-1" id="btnEdit"
                                                        data-id="{{ $item->id }}" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm ms-1" id="btnDelete"
                                                        data-id="{{ $item->id }}" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <p class="mb-1">Belum ada data sample</p>
                                                        <small>Klik tombol "Tambah Data" untuk menambahkan sample
                                                            baru</small>
                                                    </div>
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

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-lg-4">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" name="shelf_life_sample_id" id="shelf_life_sample_id"
                                value="{{ $data->id }}">
                            <label for="variant_fg" class="form-label">Varian FG <span
                                    style="color: red;">*</span></label>
                            <select name="variant_fg" id="variant_fg" class="form-control select2">
                                <option value="">-- Pilih Varian FG --</option>
                                @if (isset($kecap['status']) && $kecap['status'] === 'success' && !empty($kecap['data']))
                                    @foreach ($kecap['data'] as $item)
                                        <option value="{{ $item['variant_fg']['name'] }}"
                                            data-kelompok="{{ $item['variant_fg']['kelompok'] }}">
                                            {{ $item['variant_fg']['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="errorVariantFg text-danger"></small>
                        </div>

                        <div class="col-lg-4">
                            <label for="kelompok_sample" class="form-label">Kelompok Sample <span
                                    style="color: red;">*</span></label>
                            <input type="text" name="kelompok_sample" id="kelompok_sample"
                                class="form-control bg-light" value="" readonly>
                            <small class="errorKelompokSample text-danger"></small>
                        </div>
                        <div class="col-lg-4">
                            <label for="variant" class="form-label">Tanggal Filling <span
                                    style="color: red;">*</span></label>
                            <input type="date" name="tanggal_filling" id="tanggal_filling" class="form-control"
                                value="{{ date('Y-m-d') }}">
                            <small class="errorTanggalFilling text-danger"></small>
                        </div>
                        <div class="col-lg-4">
                            <label for="variant" class="form-label">Kelompok Tanggal <span
                                    style="color: red;">*</span></label>
                            <input type="text" name="kelompok_tanggal" id="kelompok_tanggal" class="form-control">
                            <small class="errorKelompokTanggal text-danger"></small>
                        </div>
                        <div class="col-lg-4">
                            <label for="koding" class="form-label">Koding <span style="color: red;">*</span></label>
                            <input type="text" name="koding" id="koding" class="form-control"
                                oninput="this.value = this.value.toUpperCase();">
                            <small class="text-danger errorKoding"></small>
                        </div>
                        <div class="col-lg-4">
                            <label for="jam_koding" class="form-label">Jam Koding <span
                                    style="color: red;">*</span></label>
                            <input type="time" name="jam_koding" id="jam_koding" class="form-control">
                            <small class="text-danger errorJamKoding"></small>
                        </div>
                        <div class="col-lg-4">
                            <label for="bulan_ke" class="form-label">Bulan Ke- <span style="color: red;">*</span></label>
                            <select name="bulan_ke" id="bulan_ke" class="form-control select2">
                                <option value="">Pilih Bulan Ke-</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="15">15</option>
                                <option value="18">18</option>
                                <option value="21">21</option>
                                <option value="24">24</option>
                            </select>
                            <small class="errorBulanKe text-danger"></small>
                        </div>
                        <div class="col-lg-4">
                            <label for="ruang_sl" class="form-label">Ruang SL <span style="color: red;">*</span></label>
                            <select name="ruang_sl" id="ruang_sl" class="form-control select2">
                                <option value="">Pilih Ruang SL</option>
                                <option value="SL Bawah">SL Bawah</option>
                                <option value="SL Atas">SL Atas</option>
                            </select>
                            <small class="errorRuangSl text-danger"></small>
                        </div>
                        <div class="col-lg-4">
                            <label for="bin_location" class="form-label">Bin <span style="color: red;">*</span></label>
                            <input type="text" name="bin_location" id="bin_location" class="form-control"
                                oninput="this.value = this.value.toUpperCase();">
                            <small class="text-danger errorBinLocation"></small>
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
@endsection

@section('scripts')
    <script>
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

            $('.select2').select2({
                placeholder: '-- Pilih Opsi --',
                dropdownParent: $('#modal')
            });

            $('#variant_fg').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var kelompok = selectedOption.data('kelompok');

                if (kelompok) {
                    $('#kelompok_sample').val(kelompok);
                } else {
                    $('#kelompok_sample').val('');
                }
            });

            var usedBulan = @json($shelfLifeSamplingDetails->pluck('bulan_ke')->toArray());
            var firstKelompokTanggal = @json($shelfLifeSamplingDetails->first()->kelompok_tanggal ?? '');

            usedBulan.forEach(function(bulan) {
                $('#bulan_ke option[value="' + bulan + '"]').remove();
            });

            $(document).on('click', '#btnAdd', function() {
                $('#form')[0].reset();
                $('#variant_fg').val('').trigger('change');
                $('#bulan_ke').val('').trigger('change');
                $('#ruang_sl').val('').trigger('change');
                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');

                if (firstKelompokTanggal) {
                    $('#kelompok_tanggal').val(firstKelompokTanggal);
                    $('#kelompok_tanggal').prop('readonly', true).addClass('bg-light');
                } else {
                    $('#kelompok_tanggal').prop('readonly', false).removeClass('bg-light');
                }

                var allBulan = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 15, 18, 21, 24];
                var nextBulan = null;

                for (var i = 0; i < allBulan.length; i++) {
                    if (!usedBulan.includes(allBulan[i])) {
                        nextBulan = allBulan[i];
                        break;
                    }
                }

                if (nextBulan !== null) {
                    $('#bulan_ke').val(nextBulan).trigger('change');
                }

                $('#modalTitle').text('Tambah Sample Detail');
                $('#modal').modal('show');
            });

            $(document).on('click', '#btnEdit', function() {
                var id = $(this).data('id');

                $.ajax({
                    type: "GET",
                    url: "{{ url('shelf-life/sample/detail/edit') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#save').val("edit-data");
                        $('#form')[0].reset();
                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#modalTitle').text('Edit Sample Detail');
                        $('#modal').modal('show');

                        $('#id').val(response.id);
                        $('#shelf_life_sample_id').val(response.shelf_life_sample_id);
                        $('#variant_fg').val(response.variant_fg).trigger('change');
                        setTimeout(function() {
                            $('#kelompok_sample').val(response.kelompok_sample);
                        }, 100);

                        if (response.tanggal_filling) {
                            $('#tanggal_filling').val(response.tanggal_filling);
                        }

                        $('#kelompok_tanggal').val(response.kelompok_tanggal);
                        $('#koding').val(response.koding);
                        $('#jam_koding').val(response.jam_koding);

                        var bulanKeOption = $('#bulan_ke option[value="' + response.bulan_ke +
                            '"]');
                        if (bulanKeOption.length === 0) {
                            $('#bulan_ke').append(new Option(response.bulan_ke, response
                                .bulan_ke, true, true));
                        } else {
                            $('#bulan_ke').val(response.bulan_ke);
                        }
                        $('#bulan_ke').trigger('change');
                        $('#ruang_sl').val(response.ruang_sl).trigger('change');
                        $('#bin_location').val(response.bin_location);
                        if (firstKelompokTanggal) {
                            $('#kelompok_tanggal').prop('readonly', true).addClass('bg-light');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching data:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal mengambil data. Silakan coba lagi.',
                        });
                    }
                });
            });

            $('#form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('shelf-life.sample.detail.store') }}",
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
                        $('#form')[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        console.error('Submit Error:', xhr.responseText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            if (errors.variant_fg) {
                                $('#variant_fg').addClass('is-invalid');
                                $('.errorVariantFg').html(errors.variant_fg[0]);
                            }
                            if (errors.kelompok_sample) {
                                $('#kelompok_sample').addClass('is-invalid');
                                $('.errorKelompokSample').html(errors.kelompok_sample[0]);
                            }
                            if (errors.tanggal_filling) {
                                $('#tanggal_filling').addClass('is-invalid');
                                $('.errorTanggalFilling').html(errors.tanggal_filling[0]);
                            }
                            if (errors.kelompok_tanggal) {
                                $('#kelompok_tanggal').addClass('is-invalid');
                                $('.errorKelompokTanggal').html(errors.kelompok_tanggal[0]);
                            }
                            if (errors.koding) {
                                $('#koding').addClass('is-invalid');
                                $('.errorKoding').html(errors.koding[0]);
                            }
                            if (errors.jam_koding) {
                                $('#jam_koding').addClass('is-invalid');
                                $('.errorJamKoding').html(errors.jam_koding[0]);
                            }
                            if (errors.bulan_ke) {
                                $('#bulan_ke').addClass('is-invalid');
                                $('.errorBulanKe').html(errors.bulan_ke[0]);
                            }
                            if (errors.ruang_sl) {
                                $('#ruang_sl').addClass('is-invalid');
                                $('.errorRuangSl').html(errors.ruang_sl[0]);
                            }
                            if (errors.bin_location) {
                                $('#bin_location').addClass('is-invalid');
                                $('.errorBinLocation').html(errors.bin_location[0]);
                            }

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: xhr.responseJSON && xhr.responseJSON.message ? xhr
                                    .responseJSON.message :
                                    'Terjadi kesalahan, silakan coba lagi.',
                            });
                        }
                    }
                });
            });

            $(document).on('click', '#btnDelete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus saja!',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('shelf-life/sample/detail') }}/" + id,
                            dataType: "json",
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                }).then(function() {
                                    window.location.reload();
                                });
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
        });
    </script>
@endsection
