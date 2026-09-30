@extends('layouts.component.main')

@section('title')
    Master Standar Mutu RM
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Standar Mutu Bahan Baku (RM)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Master Data RM</a>
                            </li>
                            <li class="breadcrumb-item active">Standar Mutu</li>
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
                            <h4 class="card-title mb-1">Daftar Standar Mutu Parameter RM</h4>
                            <p class="text-muted mb-0">Kelola batas toleransi (Min, Max, & Target) per parameter untuk tiap jenis bahan baku.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <select id="filterJenisBahan" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">-- Semua Jenis Bahan --</option>
                                @foreach ($jenisBahans as $jb)
                                    <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-primary btn-sm" id="btnAdd">
                                <i class="mdi mdi-plus-circle-outline me-1"></i>
                                Tambah Standar
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">No.</th>
                                        <th>Jenis Bahan</th>
                                        <th>Parameter</th>
                                        <th>Batas Standar</th>
                                        <th style="width: 80px;">UoM</th>
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
        <div class="modal fade" id="modalStandar" tabindex="-1" aria-labelledby="modalStandarLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalStandarLabel">Tambah Standar Mutu RM</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formStandar">
                        <input type="hidden" name="id" id="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="id_jenis_bahan" class="form-label">
                                    Jenis Bahan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="id_jenis_bahan" name="id_jenis_bahan" required>
                                    <option value="">-- Pilih Jenis Bahan --</option>
                                    @foreach ($jenisBahans as $jb)
                                        <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="errorJenisBahan"></div>
                            </div>

                            <div class="mb-3">
                                <label for="parameter" class="form-label">
                                    Parameter Analisa <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="parameter" name="parameter" list="parameterSuggestions" placeholder="Contoh: pH, Brix, %Kadar Air, %Kotoran, Organo" required>
                                <datalist id="parameterSuggestions">
                                    <option value="pH"></option>
                                    <option value="Brix"></option>
                                    <option value="%Kadar Air"></option>
                                    <option value="%Kotoran"></option>
                                    <option value="Organo"></option>
                                    <option value="Warna"></option>
                                    <option value="Aroma"></option>
                                    <option value="%NaCl"></option>
                                    <option value="Gross Weight"></option>
                                    <option value="Fisik"></option>
                                </datalist>
                                <div class="invalid-feedback" id="errorParameter"></div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="min_standar" class="form-label">Min. Standar</label>
                                    <input type="number" step="any" class="form-control" id="min_standar" name="min_standar" placeholder="Contoh: 5.5">
                                </div>
                                <div class="col-6">
                                    <label for="max_standar" class="form-label">Maks. Standar</label>
                                    <input type="number" step="any" class="form-control" id="max_standar" name="max_standar" placeholder="Contoh: 10.0">
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-8">
                                    <label for="target_text" class="form-label">Target / Kriteria Teks</label>
                                    <input type="text" class="form-control" id="target_text" name="target_text" placeholder="Contoh: OK >= 70%, Sesuai Standar">
                                </div>
                                <div class="col-4">
                                    <label for="uom" class="form-label">Satuan (UoM)</label>
                                    <input type="text" class="form-control" id="uom" name="uom" placeholder="%, °Bx, kg">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan / Catatan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Catatan spesifikasi (opsional)"></textarea>
                            </div>

                            <div class="mb-2">
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

        const modalElement = document.getElementById('modalStandar');
        const modalStandar = new bootstrap.Modal(modalElement);

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('master-standar-rm.index') }}",
                data: function (d) {
                    d.filter_jenis_bahan = $('#filterJenisBahan').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'jenis_bahan', name: 'jenisBahan.nama' },
                { data: 'parameter', name: 'parameter' },
                { data: 'standar_display', name: 'standar_display', orderable: false },
                { data: 'uom', name: 'uom', className: 'text-center', defaultContent: '-' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                processing: "Memuat data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data standar mutu yang cocok",
                emptyTable: "Belum ada data standar mutu RM",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                }
            }
        });

        $('#filterJenisBahan').on('change', function () {
            table.ajax.reload();
        });

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#errorStatus').text('');
        }

        function resetForm() {
            $('#formStandar')[0].reset();
            $('#id').val('');
            $('#statusAktif').prop('checked', true);
            clearErrors();
        }

        $('#btnAdd').on('click', function () {
            resetForm();
            $('#modalStandarLabel').text('Tambah Standar Mutu RM');
            modalStandar.show();
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');
            clearErrors();

            $.ajax({
                url: `/master-data-rm/standar/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Memuat Data...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function (res) {
                    Swal.close();
                    if (res.status && res.data) {
                        const data = res.data;
                        $('#id').val(data.id);
                        $('#id_jenis_bahan').val(data.id_jenis_bahan);
                        $('#parameter').val(data.parameter);
                        $('#min_standar').val(data.min_standar);
                        $('#max_standar').val(data.max_standar);
                        $('#target_text').val(data.target_text);
                        $('#uom').val(data.uom);
                        $('#keterangan').val(data.keterangan);

                        if (data.status == 1 || data.status === true) {
                            $('#statusAktif').prop('checked', true);
                        } else {
                            $('#statusNonAktif').prop('checked', true);
                        }

                        $('#modalStandarLabel').text('Edit Standar Mutu RM');
                        modalStandar.show();
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memuat data.'
                    });
                }
            });
        });

        $('#formStandar').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const formData = new FormData(this);
            const $submitBtn = $('#btnSubmit');

            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: "{{ route('master-standar-rm.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    $submitBtn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i> Simpan');
                    if (res.status) {
                        modalStandar.hide();
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    $submitBtn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i> Simpan');
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.id_jenis_bahan) {
                            $('#id_jenis_bahan').addClass('is-invalid');
                            $('#errorJenisBahan').text(errors.id_jenis_bahan[0]);
                        }
                        if (errors.parameter) {
                            $('#parameter').addClass('is-invalid');
                            $('#errorParameter').text(errors.parameter[0]);
                        }
                        if (errors.status) {
                            $('#errorStatus').text(errors.status[0]);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.btnDelete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus Data?',
                text: `Standar "${nama}" akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/master-data-rm/standar/${id}`,
                        type: 'DELETE',
                        dataType: 'json',
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Menghapus...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        },
                        success: function (res) {
                            if (res.status) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data.'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
