@extends('layouts.component.main')

@section('title')
    Daftar Asal Bahan RM
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Asal Bahan RM</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Master Data RM</a>
                            </li>
                            <li class="breadcrumb-item active">Asal Bahan</li>
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
                            <h4 class="card-title mb-1">Daftar Asal Bahan</h4>
                            <p class="text-muted mb-0">Kelola master data Asal Bahan berdasarkan Jenis Bahan & Supplier / Manufactur RM.</p>
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
                                        <th>Supplier / Manufactur</th>
                                        <th>Asal Bahan</th>
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
        <div class="modal fade" id="modalAsalBahan" tabindex="-1" aria-labelledby="modalAsalBahanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAsalBahanLabel">Tambah Asal Bahan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formAsalBahan">
                        <input type="hidden" name="id" id="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="jenis_bahan_id" class="form-label">
                                    Jenis Bahan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="jenis_bahan_id" name="jenis_bahan_id" required>
                                    <option value="">-- Pilih Jenis Bahan --</option>
                                    @foreach ($jenisBahans as $jb)
                                        <option value="{{ $jb->id }}">{{ $jb->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="errorJenisBahan"></div>
                            </div>

                            <div class="mb-3">
                                <label for="supplier_rm_id" class="form-label">
                                    Supplier / Manufactur <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="supplier_rm_id" name="supplier_rm_id" required disabled>
                                    <option value="">-- Pilih Jenis Bahan Terlebih Dahulu --</option>
                                </select>
                                <div class="invalid-feedback" id="errorSupplierRm"></div>
                            </div>

                            <div class="mb-3">
                                <label for="asal_bahan" class="form-label">
                                    Asal Bahan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="asal_bahan" name="asal_bahan" placeholder="Contoh: TULUNGAGUNG, KUDUS, PURWOKERTO" required>
                                <div class="invalid-feedback" id="errorAsalBahan"></div>
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

        const modalElement = document.getElementById('modalAsalBahan');
        const modalAsalBahan = new bootstrap.Modal(modalElement);

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('master-asal-bahan.index') }}",
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'jenis_bahan', name: 'jenisBahan.nama' },
                { data: 'supplier', name: 'supplierRm.nama_supplier' },
                { data: 'asal_bahan', name: 'asal_bahan' },
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
                emptyTable: 'Data Asal Bahan RM belum tersedia',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Selanjutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        function loadSuppliersByJenis(jenisId, selectedSupplierId = null) {
            const supplierSelect = $('#supplier_rm_id');
            if (!jenisId) {
                supplierSelect.html('<option value="">-- Pilih Jenis Bahan Terlebih Dahulu --</option>').prop('disabled', true);
                return;
            }

            supplierSelect.html('<option value="">Memuat supplier...</option>').prop('disabled', true);

            $.ajax({
                url: "{{ url('/master-data-rm/supplier/get-by-jenis-bahan') }}/" + jenisId,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.status && response.data.length > 0) {
                        let options = '<option value="">-- Pilih Supplier / Manufactur --</option>';
                        response.data.forEach(function (sup) {
                            const isSelected = selectedSupplierId && selectedSupplierId == sup.id ? 'selected' : '';
                            options += `<option value="${sup.id}" ${isSelected}>${sup.nama_supplier}</option>`;
                        });
                        supplierSelect.html(options).prop('disabled', false);
                    } else {
                        supplierSelect.html('<option value="">-- Tidak ada supplier untuk jenis bahan ini --</option>').prop('disabled', true);
                    }
                },
                error: function () {
                    supplierSelect.html('<option value="">-- Gagal memuat supplier --</option>').prop('disabled', true);
                }
            });
        }

        $('#jenis_bahan_id').on('change', function () {
            loadSuppliersByJenis($(this).val());
        });

        function clearValidation() {
            $('#jenis_bahan_id').removeClass('is-invalid');
            $('#supplier_rm_id').removeClass('is-invalid');
            $('#asal_bahan').removeClass('is-invalid');
            $('#errorJenisBahan').html('');
            $('#errorSupplierRm').html('');
            $('#errorAsalBahan').html('');
            $('#errorStatus').html('');
        }

        function resetForm() {
            $('#formAsalBahan')[0].reset();
            $('#id').val('');
            $('#jenis_bahan_id').val('');
            $('#supplier_rm_id').html('<option value="">-- Pilih Jenis Bahan Terlebih Dahulu --</option>').prop('disabled', true);
            $('#statusAktif').prop('checked', true);
            clearValidation();
        }

        $('#btnAdd').on('click', function () {
            resetForm();
            $('#modalAsalBahanLabel').text('Tambah Asal Bahan');
            modalAsalBahan.show();
        });

        $(document).on('click', '.btnEdit', function () {
            const id = $(this).data('id');
            resetForm();

            $.ajax({
                url: "{{ url('/master-data-rm/asal-bahan') }}/" + id + "/edit",
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Memuat...',
                        text: 'Mengambil data asal bahan RM',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.status) {
                        $('#id').val(response.data.id);
                        $('#jenis_bahan_id').val(response.data.jenis_bahan_id);
                        $('#asal_bahan').val(response.data.asal_bahan);
                        
                        loadSuppliersByJenis(response.data.jenis_bahan_id, response.data.supplier_rm_id);

                        if (response.data.status) {
                            $('#statusAktif').prop('checked', true);
                        } else {
                            $('#statusNonAktif').prop('checked', true);
                        }
                        $('#modalAsalBahanLabel').text('Edit Asal Bahan');
                        modalAsalBahan.show();
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('Error', 'Gagal mengambil data asal bahan RM.', 'error');
                }
            });
        });

        $('#formAsalBahan').on('submit', function (e) {
            e.preventDefault();
            clearValidation();

            const formData = $(this).serialize();
            const btnSubmit = $('#btnSubmit');
            const originalBtnText = btnSubmit.html();

            btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

            $.ajax({
                url: "{{ route('master-asal-bahan.store') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    btnSubmit.prop('disabled', false).html(originalBtnText);
                    if (response.status) {
                        modalAsalBahan.hide();
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
                        if (errors.supplier_rm_id) {
                            $('#supplier_rm_id').addClass('is-invalid');
                            $('#errorSupplierRm').text(errors.supplier_rm_id[0]);
                        }
                        if (errors.asal_bahan) {
                            $('#asal_bahan').addClass('is-invalid');
                            $('#errorAsalBahan').text(errors.asal_bahan[0]);
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
                html: `Apakah Anda yakin ingin menghapus asal bahan <strong>${nama}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/master-data-rm/asal-bahan') }}/" + id,
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
