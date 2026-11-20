@extends('layouts.component.main')

@section('title')
    Master Group RM
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Master Group RM</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Master Data RM</a>
                            </li>
                            <li class="breadcrumb-item active">Master Group</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER CARD --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="filter_jenis_bahan" class="form-label small fw-semibold text-muted mb-1">
                                    <i class="mdi mdi-filter-variant me-1"></i>Filter Jenis Bahan
                                </label>
                                <select class="form-select form-select-sm" id="filter_jenis_bahan">
                                    <option value="">-- Semua Jenis Bahan --</option>
                                    @foreach($jenisBahans as $jb)
                                        <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_grup" class="form-label small fw-semibold text-muted mb-1">
                                    <i class="mdi mdi-group me-1"></i>Filter Grup
                                </label>
                                <select class="form-select form-select-sm" id="filter_grup">
                                    <option value="">-- Semua Grup --</option>
                                    @foreach($grups as $g)
                                        <option value="{{ $g }}">Group {{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_status" class="form-label small fw-semibold text-muted mb-1">
                                    <i class="mdi mdi-check-circle-outline me-1"></i>Filter Status
                                </label>
                                <select class="form-select form-select-sm" id="filter_status">
                                    <option value="">-- Semua Status --</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-light border w-100" id="btnResetFilter">
                                    <i class="mdi mdi-refresh me-1"></i>Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT TABLE --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 bg-white">
                        <div>
                            <h4 class="card-title mb-1 fw-bold text-dark">
                                <i class="mdi mdi-group text-primary me-1"></i>Daftar Master Group RM
                            </h4>
                            <p class="text-muted small mb-0">
                                Standar parameter dan kriteria target penentuan Group (Group A, B, C) untuk analisa Jenis Bahan RM.
                            </p>
                        </div>
                        <button type="button" class="btn btn-primary" id="btnAdd">
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            Tambah Data Group
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No.</th>
                                        <th>Jenis Bahan</th>
                                        <th style="width: 100px;">Grup</th>
                                        <th>Parameter</th>
                                        <th>Kategori / Kriteria</th>
                                        <th style="width: 130px;">Target</th>
                                        <th style="width: 110px;">Status</th>
                                        <th style="width: 100px;">Aksi</th>
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
        <div class="modal fade" id="modalGroup" tabindex="-1" aria-labelledby="modalGroupLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light py-2">
                        <h5 class="modal-title fw-bold fs-6" id="modalGroupLabel">Tambah Master Group RM</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formGroup">
                        <input type="hidden" name="id" id="id">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label for="jenis_bahan_id" class="form-label fw-semibold small">
                                    Jenis Bahan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-sm" id="jenis_bahan_id" name="jenis_bahan_id" required>
                                    <option value="">-- Pilih Jenis Bahan --</option>
                                    @foreach($jenisBahans as $jb)
                                        <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="errorJenisBahan"></div>
                            </div>

                            <div class="mb-3">
                                <label for="grup" class="form-label fw-semibold small">
                                    Grup <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="grup" name="grup" placeholder="Contoh: A, B, atau C" required list="listGrup">
                                <datalist id="listGrup">
                                    <option value="A">
                                    <option value="B">
                                    <option value="C">
                                </datalist>
                                <small class="text-muted fs-11">Pilih atau ketik huruf/nama grup (misal: A).</small>
                                <div class="invalid-feedback" id="errorGrup"></div>
                            </div>

                            <div class="mb-3">
                                <label for="parameter" class="form-label fw-semibold small">
                                    Parameter <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="parameter" name="parameter" placeholder="Contoh: pH, %Kotoran, Organo, Kadar Air" required list="listParameter">
                                <datalist id="listParameter">
                                    <option value="pH">
                                    <option value="%Kotoran">
                                    <option value="Organo">
                                    <option value="Kadar Air">
                                    <option value="Brix">
                                    <option value="Warna">
                                    <option value="Aroma">
                                </datalist>
                                <div class="invalid-feedback" id="errorParameter"></div>
                            </div>

                            <div class="mb-3">
                                <label for="kategori" class="form-label fw-semibold small">
                                    Kategori / Kriteria <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="kategori" name="kategori" placeholder="Contoh: Qty sample pH Under 5,3 (#) atau OK" required>
                                <div class="invalid-feedback" id="errorKategori"></div>
                            </div>

                            <div class="mb-3">
                                <label for="target" class="form-label fw-semibold small">
                                    Target <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" id="target" name="target" placeholder="Contoh: Max. 30%, Min. 70%" required>
                                <div class="invalid-feedback" id="errorTarget"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small d-block">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusAktif" value="1" checked>
                                    <label class="form-check-label" for="statusAktif">Aktif</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusNonaktif" value="0">
                                    <label class="form-check-label" for="statusNonaktif">Tidak Aktif</label>
                                </div>
                                <div class="text-danger small" id="errorStatus"></div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary" id="btnSave">
                                <i class="mdi mdi-content-save me-1"></i>
                                Simpan Data
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

        const modalElement = document.getElementById('modalGroup');
        const modalGroup = new bootstrap.Modal(modalElement);

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('master-group-rm.index') }}",
                type: 'GET',
                data: function (d) {
                    d.filter_jenis_bahan = $('#filter_jenis_bahan').val();
                    d.filter_grup = $('#filter_grup').val();
                    d.filter_status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenis_bahan', name: 'jenisBahan.nama' },
                { data: 'grup_badge', name: 'grup' },
                { data: 'parameter_display', name: 'parameter' },
                { data: 'kategori_display', name: 'kategori' },
                { data: 'target_badge', name: 'target' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Data Master Group RM belum tersedia',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        // Event filter
        $('#filter_jenis_bahan, #filter_grup, #filter_status').on('change', function () {
            table.draw();
        });

        $('#btnResetFilter').on('click', function () {
            $('#filter_jenis_bahan').val('');
            $('#filter_grup').val('');
            $('#filter_status').val('');
            table.draw();
        });

        function clearValidation() {
            $('#jenis_bahan_id').removeClass('is-invalid');
            $('#grup').removeClass('is-invalid');
            $('#parameter').removeClass('is-invalid');
            $('#kategori').removeClass('is-invalid');
            $('#target').removeClass('is-invalid');
            $('#errorJenisBahan').html('');
            $('#errorGrup').html('');
            $('#errorParameter').html('');
            $('#errorKategori').html('');
            $('#errorTarget').html('');
            $('#errorStatus').html('');
        }

        function resetForm() {
            $('#formGroup')[0].reset();
            $('#id').val('');
            $('#jenis_bahan_id').val('');
            $('#statusAktif').prop('checked', true);
            clearValidation();
        }

        $('#btnAdd').on('click', function () {
            resetForm();
            $('#modalGroupLabel').text('Tambah Master Group RM');
            modalGroup.show();
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');
            resetForm();
            $('#modalGroupLabel').text('Edit Master Group RM');

            $.ajax({
                url: "{{ url('/master-data-rm/group') }}/" + id + "/edit",
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#id').val(data.id);
                        $('#jenis_bahan_id').val(data.jenis_bahan_id);
                        $('#grup').val(data.grup);
                        $('#parameter').val(data.parameter);
                        $('#kategori').val(data.kategori);
                        $('#target').val(data.target);

                        if (data.status) {
                            $('#statusAktif').prop('checked', true);
                        } else {
                            $('#statusNonaktif').prop('checked', true);
                        }

                        modalGroup.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengambil data Master Group RM.'
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server saat memuat data.'
                    });
                }
            });
        });

        $('#formGroup').on('submit', function (e) {
            e.preventDefault();
            clearValidation();

            const btnSave = $('#btnSave');
            const originalText = btnSave.html();
            btnSave.html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...').prop('disabled', true);

            $.ajax({
                url: "{{ route('master-group-rm.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    btnSave.html(originalText).prop('disabled', false);
                    if (response.status === 'success') {
                        modalGroup.hide();
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    btnSave.html(originalText).prop('disabled', false);
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.jenis_bahan_id) {
                            $('#jenis_bahan_id').addClass('is-invalid');
                            $('#errorJenisBahan').text(errors.jenis_bahan_id[0]);
                        }
                        if (errors.grup) {
                            $('#grup').addClass('is-invalid');
                            $('#errorGrup').text(errors.grup[0]);
                        }
                        if (errors.parameter) {
                            $('#parameter').addClass('is-invalid');
                            $('#errorParameter').text(errors.parameter[0]);
                        }
                        if (errors.kategori) {
                            $('#kategori').addClass('is-invalid');
                            $('#errorKategori').text(errors.kategori[0]);
                        }
                        if (errors.target) {
                            $('#target').addClass('is-invalid');
                            $('#errorTarget').text(errors.target[0]);
                        }
                        if (errors.status) {
                            $('#errorStatus').text(errors.status[0]);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan pada sistem.'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.btnDelete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus Data Group?',
                text: `Apakah Anda yakin ingin menghapus "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="mdi mdi-delete me-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/master-data-rm/group') }}/" + id,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data Master Group RM.'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
