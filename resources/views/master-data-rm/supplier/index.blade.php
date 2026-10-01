@extends('layouts.component.main')

@section('title')
    Daftar Supplier / Manufactur RM
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Supplier / Manufactur RM</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Master Data RM</a>
                            </li>
                            <li class="breadcrumb-item active">Supplier / Manufactur</li>
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
                            <h4 class="card-title mb-1">Daftar Supplier / Manufactur</h4>
                            <p class="text-muted mb-0">Kelola master data Supplier / Manufactur berdasarkan Jenis Bahan RM.</p>
                        </div>
                        <button type="button" class="btn btn-primary" id="btnAdd">
                            <i class="mdi mdi-plus-circle-outline me-1"></i>
                            Tambah Data
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">No.</th>
                                        <th>Jenis Bahan</th>
                                        <th>Nama Supplier / Manufactur</th>
                                        <th style="width: 130px;">Status</th>
                                        <th style="width: 120px;">Aksi</th>
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
        <div class="modal fade" id="modalSupplierRm" tabindex="-1" aria-labelledby="modalSupplierRmLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSupplierRmLabel">Tambah Supplier / Manufactur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formSupplierRm">
                        <input type="hidden" name="id" id="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="jenis_bahan_id" class="form-label">
                                    Jenis Bahan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2" id="jenis_bahan_id" name="jenis_bahan_id" required>
                                    <option value="">-- Pilih Jenis Bahan --</option>
                                    @foreach ($jenisBahans as $jb)
                                        <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="errorJenisBahan"></div>
                            </div>

                            <div class="mb-3">
                                <label for="nama_supplier" class="form-label">
                                    Nama Supplier / Manufactur <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" placeholder="Contoh: SUMBER SARI MANIS, CV PERTIWI JAYA" required>
                                <div class="invalid-feedback" id="errorNamaSupplier"></div>
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

        const modalElement = document.getElementById('modalSupplierRm');
        const modalSupplierRm = new bootstrap.Modal(modalElement);

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('master-supplier-rm.index') }}",
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenis_bahan', name: 'jenisBahan.nama' },
                { data: 'nama_supplier', name: 'nama_supplier' },
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
                emptyTable: 'Data Supplier RM belum tersedia',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        function clearValidation() {
            $('#jenis_bahan_id').removeClass('is-invalid');
            $('#nama_supplier').removeClass('is-invalid');
            $('#errorJenisBahan').html('');
            $('#errorNamaSupplier').html('');
            $('#errorStatus').html('');
        }

        function resetForm() {
            $('#formSupplierRm')[0].reset();
            $('#id').val('');
            $('#jenis_bahan_id').val('');
            $('#statusAktif').prop('checked', true);
            clearValidation();
        }

        $('#btnAdd').on('click', function () {
            resetForm();
            $('#modalSupplierRmLabel').text('Tambah Supplier / Manufactur');
            modalSupplierRm.show();
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');
            resetForm();

            $.ajax({
                url: "{{ url('/master-data-rm/supplier') }}/" + id + "/edit",
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Memuat...',
                        text: 'Mengambil data supplier RM',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.status) {
                        $('#id').val(response.data.id);
                        $('#jenis_bahan_id').val(response.data.jenis_bahan_id);
                        $('#nama_supplier').val(response.data.nama_supplier);
                        if (response.data.status) {
                            $('#statusAktif').prop('checked', true);
                        } else {
                            $('#statusNonAktif').prop('checked', true);
                        }
                        $('#modalSupplierRmLabel').text('Edit Supplier / Manufactur');
                        modalSupplierRm.show();
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('Error', 'Gagal mengambil data supplier RM.', 'error');
                }
            });
        });

        $('#formSupplierRm').on('submit', function (e) {
            e.preventDefault();
            clearValidation();

            const formData = $(this).serialize();
            const btnSubmit = $('#btnSubmit');
            const originalBtnText = btnSubmit.html();

            btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: "{{ route('master-supplier-rm.store') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    btnSubmit.prop('disabled', false).html(originalBtnText);
                    if (response.status) {
                        modalSupplierRm.hide();
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
                    btnSubmit.prop('disabled', false).html(originalBtnText);
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.jenis_bahan_id) {
                            $('#jenis_bahan_id').addClass('is-invalid');
                            $('#errorJenisBahan').text(errors.jenis_bahan_id[0]);
                        }
                        if (errors.nama_supplier) {
                            $('#nama_supplier').addClass('is-invalid');
                            $('#errorNamaSupplier').text(errors.nama_supplier[0]);
                        }
                        if (errors.status) {
                            $('#errorStatus').text(errors.status[0]);
                        }
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    }
                }
            });
        });

        $(document).on('click', '.btnDelete', function () {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Apakah Anda yakin ingin menghapus supplier <strong>${nama}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/master-data-rm/supplier') }}/" + id,
                        type: 'DELETE',
                        dataType: 'json',
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Menghapus...',
                                text: 'Mohon tunggu',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });
                        },
                        success: function (response) {
                            Swal.close();
                            if (response.status) {
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
                        error: function () {
                            Swal.close();
                            Swal.fire('Error', 'Gagal menghapus data.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
