@extends('layouts.component.main')

@section('title')
    Parameter Organoleptik RM
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Parameter Organoleptik RM</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Master Data RM</a>
                            </li>
                            <li class="breadcrumb-item active">Parameter Organoleptik</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h4 class="card-title mb-1">Daftar Pilihan Parameter (Warna, Aroma & Organo)</h4>
                            <p class="text-muted mb-0">Kelola master data opsi dropdown analisa organoleptik bahan baku.</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <div style="min-width: 170px;">
                                <select class="form-select form-select-sm" id="filterKategori">
                                    <option value="">-- Semua Kategori --</option>
                                    <option value="warna">Warna</option>
                                    <option value="aroma">Aroma</option>
                                    <option value="organo">Organo (Rasa)</option>
                                </select>
                            </div>
                            <div style="min-width: 180px;">
                                <select class="form-select form-select-sm" id="filterJenisBahan">
                                    <option value="">-- Semua Jenis Bahan --</option>
                                    <option value="global">Global / Semua Bahan</option>
                                    @foreach ($jenisBahans as $jb)
                                        <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="btnAdd">
                                <i class="mdi mdi-plus-circle-outline me-1"></i>
                                Tambah Pilihan
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No.</th>
                                        <th style="width: 140px;">Kategori</th>
                                        <th>Nilai / Pilihan</th>
                                        <th>Jenis Bahan</th>
                                        <th style="width: 150px;">Tipe Input</th>
                                        <th style="width: 110px;">Status</th>
                                        <th style="width: 110px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL FORM --}}
        <div class="modal fade" id="modalParameter" tabindex="-1" aria-labelledby="modalParameterLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalParameterLabel">Tambah Pilihan Parameter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formParameter">
                        <input type="hidden" name="id" id="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="kategori" class="form-label">
                                    Kategori Parameter <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="warna">Warna</option>
                                    <option value="aroma">Aroma</option>
                                    <option value="organo">Organo (Rasa)</option>
                                </select>
                                <div class="invalid-feedback" id="errorKategori"></div>
                            </div>

                            <div class="mb-3">
                                <label for="nama_pilihan" class="form-label">
                                    Nama / Nilai Pilihan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nama_pilihan" name="nama_pilihan" placeholder="Contoh: Coklat, OK, Asam, Gelap..." required>
                                <div class="invalid-feedback" id="errorNamaPilihan"></div>
                            </div>

                            <div class="mb-3">
                                <label for="jenis_bahan_id" class="form-label">
                                    Jenis Bahan <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <select class="form-select" id="jenis_bahan_id" name="jenis_bahan_id">
                                    <option value="">-- Berlaku untuk Semua Bahan (Global) --</option>
                                    @foreach ($jenisBahans as $jb)
                                        <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text small text-muted">Pilih jenis bahan jika opsi ini khusus untuk bahan tertentu.</div>
                                <div class="invalid-feedback" id="errorJenisBahanId"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="urutan" class="form-label">Urutan Tampil</label>
                                    <input type="number" min="0" class="form-control" id="urutan" name="urutan" value="0">
                                </div>
                                <div class="col-md-6 mb-3 d-flex flex-column justify-content-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="is_custom" name="is_custom" value="1">
                                        <label class="form-check-label" for="is_custom">Input Teks Bebas</label>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">Aktifkan jika opsi ini membutuhkan input teks manual (seperti Lain-lain / Campuran).</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusAktif" value="1" checked>
                                    <label class="form-check-label text-success fw-medium" for="statusAktif">
                                        Aktif
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusNonAktif" value="0">
                                    <label class="form-check-label text-danger fw-medium" for="statusNonAktif">
                                        Tidak Aktif
                                    </label>
                                </div>
                                <div class="text-danger small mt-1" id="errorStatus"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master-parameter-rm.index') }}",
                data: function (d) {
                    d.filter_kategori = $('#filterKategori').val();
                    d.filter_jenis_bahan = $('#filterJenisBahan').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'kategori', name: 'kategori' },
                { data: 'nama_pilihan', name: 'nama_pilihan', className: 'fw-semibold text-dark' },
                { data: 'jenis_bahan', name: 'jenisBahan.nama' },
                { data: 'is_custom', name: 'is_custom', className: 'text-center' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        $('#filterKategori, #filterJenisBahan').on('change', function () {
            table.draw();
        });

        function resetForm() {
            $('#formParameter')[0].reset();
            $('#id').val('');
            $('#is_custom').prop('checked', false);
            $('#statusAktif').prop('checked', true);
            $('.form-control, .form-select').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#errorStatus').text('');
            $('#modalParameterLabel').text('Tambah Pilihan Parameter');
        }

        $('#btnAdd').on('click', function () {
            resetForm();
            $('#modalParameter').modal('show');
        });

        $('#formParameter').on('submit', function (e) {
            e.preventDefault();

            const $btn = $('#btnSubmit');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');
            $('.form-control, .form-select').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#errorStatus').text('');

            $.ajax({
                url: "{{ route('master-parameter-rm.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    if (res.status) {
                        $('#modalParameter').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.kategori) {
                            $('#kategori').addClass('is-invalid');
                            $('#errorKategori').text(errors.kategori[0]);
                        }
                        if (errors.nama_pilihan) {
                            $('#nama_pilihan').addClass('is-invalid');
                            $('#errorNamaPilihan').text(errors.nama_pilihan[0]);
                        }
                        if (errors.jenis_bahan_id) {
                            $('#jenis_bahan_id').addClass('is-invalid');
                            $('#errorJenisBahanId').text(errors.jenis_bahan_id[0]);
                        }
                        if (errors.status) {
                            $('#errorStatus').text(errors.status[0]);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server.'
                        });
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i> Simpan');
                }
            });
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');
            resetForm();

            $.ajax({
                url: `{{ url('master-data-rm/parameter') }}/${id}/edit`,
                type: 'GET',
                success: function (res) {
                    if (res.status && res.data) {
                        const d = res.data;
                        $('#id').val(d.id);
                        $('#kategori').val(d.kategori);
                        $('#nama_pilihan').val(d.nama_pilihan);
                        $('#jenis_bahan_id').val(d.jenis_bahan_id || '');
                        $('#urutan').val(d.urutan || 0);
                        $('#is_custom').prop('checked', !!d.is_custom);

                        if (d.status) {
                            $('#statusAktif').prop('checked', true);
                        } else {
                            $('#statusNonAktif').prop('checked', true);
                        }

                        $('#modalParameterLabel').text('Edit Pilihan Parameter');
                        $('#modalParameter').modal('show');
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal mengambil data parameter.'
                    });
                }
            });
        });

        $(document).on('click', '.btnDelete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Data "${nama}" akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('master-data-rm/parameter') }}/${id}`,
                        type: 'DELETE',
                        success: function (res) {
                            if (res.status) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data parameter.'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
