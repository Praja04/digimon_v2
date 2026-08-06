@extends('layouts.component.main')

@section('title')
    Pemeriksaan Inner / Outer
@endsection

@section('content')

@php
    $jenisIncomingName = strtolower(
        $packagingIncoming->jenisIncoming?->nama ?? ''
    );

    $jenisMaterialName = strtolower(
        $packagingIncoming->jenisMaterial?->nama ?? ''
    );

    $isOuter =
        (
            str_contains($jenisIncomingName, 'outer')
            && ! str_contains($jenisIncomingName, 'inner')
        )
        || str_contains($jenisMaterialName, 'outer');


    $existingFotoPengecekan = collect(
        $sampling?->foto_pengecekan ?? []
    )->filter()->values()->all();

    $existingFotoKetidaksesuaian = collect(
        $sampling?->foto_ketidaksesuaian ?? []
    )->filter()->values()->all();

    $selectedJenisKetidaksesuaian = collect(
        old(
            'jenis_ketidaksesuaian',
            $sampling?->jenis_ketidaksesuaian ?? []
        )
    )->filter()->values()->all();
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0">
                            Pemeriksaan
                            {{ $packagingIncoming->jenisIncoming?->nama ?? 'Inner / Outer' }}
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Pemeriksaan sampel Packaging Material berdasarkan nomor SPB.
                        </p>
                    </div>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.pm') }}">
                                    Packaging Material
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.pm.inner-outer') }}">
                                    Inner / Outer
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Pemeriksaan
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div id="alertContainer" class="mb-3"></div>

        @if ($sampling?->status_proses === 'draft')
            <div class="alert alert-warning">
                <i class="mdi mdi-content-save-edit-outline me-1"></i>
                Data sementara ditemukan. Silakan lanjutkan pengisian lalu pilih
                <strong>Simpan Final</strong>.
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-end">
                    <div class="back-action-card">
                        <div class="back-action-icon">
                            <i class="mdi mdi-format-list-bulleted"></i>
                        </div>

                        <div class="back-action-content">
                            <strong>Daftar Inner / Outer</strong>
                            <small>Kembali ke daftar SPB</small>
                        </div>

                        <a
                            href="{{ route('rmpm.pm.inner-outer') }}"
                            class="btn btn-primary px-4"
                        >
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form
            id="innerOuterSamplingForm"
            method="POST"
            action="{{ route(
                'rmpm.pm.inner-outer.sampling.store',
                $packagingIncoming
            ) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="card border-0 shadow-sm sampling-card mb-4">

                <div class="sampling-header">
                    <div>
                        <span class="sampling-label">
                            PACKAGING MATERIAL
                        </span>

                        <h3 class="mb-1">
                            {{ $isOuter ? 'OUTER' : 'INNER' }}
                        </h3>

                        <p class="mb-0">
                            Pemeriksaan incoming material
                        </p>
                    </div>

                    <div class="sampling-header-icon">
                        <i class="mdi mdi-clipboard-check-outline"></i>
                    </div>
                </div>

                <div class="card-body p-4">

                    <div class="section-title">
                        <i class="mdi mdi-information-outline"></i>
                        Identitas Incoming
                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Nomor SPB</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $packagingIncoming->no_spb }}"
                                readonly
                            >
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Jenis Incoming</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $packagingIncoming->jenisIncoming?->nama ?? '-' }}"
                                readonly
                            >
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Supplier</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{
                                    $packagingIncoming->supplier?->nama
                                    ?? $packagingIncoming->supplier?->nama_supplier
                                    ?? '-'
                                }}"
                                readonly
                            >
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Nomor Mobil</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $packagingIncoming->no_mobil ?? '-' }}"
                                readonly
                            >
                        </div>

                        <div class="col-xl-6 col-md-6">
                            <label class="form-label">MID</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $packagingIncoming->mid ?? '-' }}"
                                readonly
                            >
                        </div>

                        <div class="col-xl-6 col-md-6">
                            <label class="form-label">Jenis Material</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $packagingIncoming->jenisMaterial?->nama ?? '-' }}"
                                readonly
                            >
                        </div>

                    </div>

                    <div class="section-title">
                        <i class="mdi mdi-flask-outline"></i>
                        Informasi Sampel
                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-xl-3 col-md-6">
                            <label
                                for="jumlah_sampel"
                                class="form-label"
                            >
                                Jumlah Sampel
                            </label>

                            <input
                                type="number"
                                name="jumlah_sampel"
                                id="jumlah_sampel"
                                class="form-control"
                                min="1"
                                max="50"
                                value="{{ old(
                                    'jumlah_sampel',
                                    $sampling?->jumlah_sampel ?? 5
                                ) }}"
                                required
                            >
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label
                                for="no_batch"
                                class="form-label"
                            >
                                Nomor Batch
                            </label>

                            <input
                                type="text"
                                name="no_batch"
                                id="no_batch"
                                class="form-control"
                                value="{{ old(
                                    'no_batch',
                                    $sampling?->no_batch
                                ) }}"
                                placeholder="Masukkan nomor batch"
                            >
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label
                                for="lot_sebelum"
                                class="form-label"
                            >
                                Lot Sebelum
                            </label>

                            <input
                                type="text"
                                name="lot_sebelum"
                                id="lot_sebelum"
                                class="form-control"
                                value="{{ old(
                                    'lot_sebelum',
                                    $sampling?->lot_sebelum
                                ) }}"
                                placeholder="Masukkan lot sebelum"
                            >
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label
                                for="lot_setelah"
                                class="form-label"
                            >
                                Lot Setelah
                            </label>

                            <input
                                type="text"
                                name="lot_setelah"
                                id="lot_setelah"
                                class="form-control"
                                value="{{ old(
                                    'lot_setelah',
                                    $sampling?->lot_setelah
                                ) }}"
                                placeholder="Masukkan lot setelah"
                            >
                        </div>

                    </div>

                    <div class="section-title">
                        <i class="mdi mdi-ruler-square"></i>
                        Hasil Pemeriksaan Sampel
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle inspection-table">
                            <thead>
                                <tr>
                                    <th>No. Sampel</th>
                                    <th>Berat Gross (Kg)</th>
                                    <th>Inside Core (cm)</th>
                                    <th>Lebar (mm)</th>
                                    <th>Pitch (mm)</th>
                                    <th>Thickness (Mikron)</th>
                                    <th>Arah Vertikal</th>
                                    <th>Arah Terbalik</th>
                                    <th>Laminasi</th>
                                    <th>Barcode</th>
                                    <th>Design</th>
                                    <th>Warna</th>
                                    <th>Tulisan</th>
                                </tr>
                            </thead>

                            <tbody id="sampleRows"></tbody>
                        </table>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-12">
                            <label class="form-label">
                                Foto Pengecekan
                                <span class="text-danger">*</span>
                            </label>

                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-camera-photo"
                                    data-target="foto_pengecekan"
                                >
                                    <i class="mdi mdi-camera-outline me-1"></i>
                                    Gunakan Kamera
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary btn-file-photo"
                                    data-target="foto_pengecekan"
                                >
                                    <i class="mdi mdi-folder-image me-1"></i>
                                    Pilih File
                                </button>
                            </div>

                            <input
                                type="file"
                                name="foto_pengecekan[]"
                                id="foto_pengecekan"
                                class="d-none multi-photo-input"
                                accept="image/*"
                                multiple
                                data-existing-count="{{ count($existingFotoPengecekan) }}"
                            >

                            <small class="text-muted d-block">
                                Maksimal 10 foto. Wajib minimal 1 foto untuk Simpan Final.
                            </small>

                            @if (count($existingFotoPengecekan) > 0)
                                <div class="saved-photo-wrap mt-3">
                                    <div class="fw-semibold mb-2">
                                        Foto Pengecekan Tersimpan
                                    </div>

                                    <div class="photo-preview-grid">
                                        @foreach ($existingFotoPengecekan as $index => $photo)
                                            <a
                                                href="{{ asset('storage/' . $photo) }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="photo-preview-item"
                                            >
                                                <img
                                                    src="{{ asset('storage/' . $photo) }}"
                                                    alt="Foto Pengecekan {{ $index + 1 }}"
                                                >
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div
                                id="foto_pengecekan_preview"
                                class="photo-preview-grid mt-3"
                            ></div>
                        </div>
                    </div>

                    <div class="inspection-note mt-3">
                        <small class="text-muted d-block">
                            Jumlah baris otomatis mengikuti field
                            <strong>Jumlah Sampel</strong>.
                        </small>

                        <small class="text-muted d-block">
                            Field <strong>Pitch</strong> hanya dapat diisi untuk Outer.
                        </small>
                    </div>

                    <div class="section-title mt-4">
                        <i class="mdi mdi-clipboard-check-outline"></i>
                        Kesimpulan Pemeriksaan
                    </div>

                    <div class="row g-3">

                        <div class="col-xl-3 col-md-6">
                            <label for="coa" class="form-label">CoA</label>
                            <select name="coa" id="coa" class="form-select">
                                <option value="">Pilih status CoA</option>
                                <option value="Ada" @selected($sampling?->coa === 'Ada')>Ada</option>
                                <option value="Tidak Ada" @selected($sampling?->coa === 'Tidak Ada')>Tidak Ada</option>
                            </select>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label
                                for="rekomendasi"
                                class="form-label"
                            >
                                Rekomendasi
                            </label>

                            <select
                                name="rekomendasi"
                                id="rekomendasi"
                                class="form-select"
                                required
                            >
                                <option value="">Pilih rekomendasi</option>
                                @foreach ([
                                    'Diterima',
                                    'Diterima Bersyarat',
                                    'Ditolak',
                                    'WIP',
                                ] as $option)
                                    <option
                                        value="{{ $option }}"
                                        @selected($sampling?->rekomendasi === $option)
                                    >
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label
                                for="konfirmasi_ketidaksesuaian"
                                class="form-label"
                            >
                                Konfirmasi Ketidaksesuaian
                            </label>

                            <select
                                name="konfirmasi_ketidaksesuaian"
                                id="konfirmasi_ketidaksesuaian"
                                class="form-select"
                            >
                                <option value="">Pilih konfirmasi</option>
                                <option
                                    value="Ada"
                                    @selected(
                                        $sampling?->konfirmasi_ketidaksesuaian === 'Ada'
                                    )
                                >
                                    Ada
                                </option>
                                <option
                                    value="Tidak Ada"
                                    @selected(
                                        $sampling?->konfirmasi_ketidaksesuaian === 'Tidak Ada'
                                    )
                                >
                                    Tidak Ada
                                </option>
                            </select>
                        </div>

                        <div class="col-xl-6 col-12">
                            <label class="form-label">
                                Jenis Ketidaksesuaian
                            </label>

                            <div
                                id="jenisKetidaksesuaianGroup"
                                class="nonconformity-grid"
                            >
                                @foreach ([
                                    'Miss Print',
                                    'Berat Under',
                                    'Dimensi Tidak Standar',
                                    'Pitch Under',
                                    'Delaminasi',
                                    'Salah Design',
                                    'Barcode Tidak Terbaca',
                                ] as $option)
                                    <label class="nonconformity-option">
                                        <input
                                            type="checkbox"
                                            name="jenis_ketidaksesuaian[]"
                                            value="{{ $option }}"
                                            @checked(in_array($option, $selectedJenisKetidaksesuaian, true))
                                        >

                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <small
                                id="jenisKetidaksesuaianHelp"
                                class="text-muted d-block mt-1"
                            >
                                Bisa memilih lebih dari satu.
                            </small>
                        </div>

                        <div class="col-xl-6 col-12">
                            <label class="form-label">
                                Foto Ketidaksesuaian
                                <span
                                    id="fotoKetidaksesuaianRequired"
                                    class="text-danger d-none"
                                >*</span>
                            </label>

                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-camera-photo"
                                    data-target="foto_ketidaksesuaian"
                                >
                                    <i class="mdi mdi-camera-outline me-1"></i>
                                    Gunakan Kamera
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary btn-file-photo"
                                    data-target="foto_ketidaksesuaian"
                                >
                                    <i class="mdi mdi-folder-image me-1"></i>
                                    Pilih File
                                </button>
                            </div>

                            <input
                                type="file"
                                name="foto_ketidaksesuaian[]"
                                id="foto_ketidaksesuaian"
                                class="d-none multi-photo-input"
                                accept="image/*"
                                multiple
                                data-existing-count="{{ count($existingFotoKetidaksesuaian) }}"
                            >

                            <small
                                id="fotoKetidaksesuaianHelp"
                                class="text-muted d-block"
                            >
                                Maksimal 10 foto. Tidak wajib apabila tidak terdapat ketidaksesuaian.
                            </small>

                            @if (count($existingFotoKetidaksesuaian) > 0)
                                <div class="saved-photo-wrap mt-3">
                                    <div class="fw-semibold mb-2">
                                        Foto Ketidaksesuaian Tersimpan
                                    </div>

                                    <div class="photo-preview-grid">
                                        @foreach ($existingFotoKetidaksesuaian as $index => $photo)
                                            <a
                                                href="{{ asset('storage/' . $photo) }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="photo-preview-item"
                                            >
                                                <img
                                                    src="{{ asset('storage/' . $photo) }}"
                                                    alt="Foto Ketidaksesuaian {{ $index + 1 }}"
                                                >
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div
                                id="foto_ketidaksesuaian_preview"
                                class="photo-preview-grid mt-3"
                            ></div>
                        </div>

                        <div class="col-xl-6 col-12">
                            <label
                                for="keterangan"
                                class="form-label"
                            >
                                Keterangan
                            </label>

                            <textarea
                                name="keterangan"
                                id="keterangan"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan keterangan pemeriksaan"
                            >{{ old(
                                'keterangan',
                                $sampling?->keterangan
                            ) }}</textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a
                            href="{{ route('rmpm.pm.inner-outer') }}"
                            class="btn btn-light"
                        >
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Batal
                        </a>

                        <button
                            type="submit"
                            value="draft"
                            class="btn btn-warning px-4 save-button"
                        >
                            <i class="mdi mdi-content-save-edit-outline me-1"></i>
                            Simpan Sementara
                        </button>

                        <button
                            type="submit"
                            value="final"
                            id="submitButton"
                            class="btn btn-primary px-4 save-button"
                        >
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            Simpan Final
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>


<div class="modal fade" id="photoCameraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <video
                    id="photoCameraPreview"
                    autoplay
                    playsinline
                    style="width:100%;border-radius:12px;"
                ></video>

                <canvas id="photoCameraCanvas" hidden></canvas>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-primary"
                    id="btnTakePhoto"
                >
                    <i class="mdi mdi-camera me-1"></i>
                    Ambil Foto
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')

<style>
    .back-action-card {
        width: 100%;
        max-width: 430px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.07);
    }

    .back-action-icon {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 25px;
    }

    .back-action-content {
        min-width: 0;
        flex-grow: 1;
    }

    .back-action-content strong,
    .back-action-content small {
        display: block;
    }

    .back-action-content small {
        margin-top: 2px;
        color: #64748b;
    }

    .sampling-card {
        overflow: hidden;
        border-radius: 18px;
    }

    .sampling-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 25px 28px;
        border-bottom: 4px solid #ef4444;
        background: linear-gradient(135deg, #eef2ff, #f8fafc);
    }

    .sampling-label {
        display: block;
        margin-bottom: 6px;
        color: #4f46e5;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .sampling-header-icon {
        width: 72px;
        height: 72px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #ffffff;
        color: #f59e0b;
        font-size: 40px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        font-size: 16px;
        font-weight: 700;
    }

    .section-title i {
        color: #4f46e5;
        font-size: 21px;
    }

    .inspection-table thead th {
        min-width: 130px;
        background: #f1f5f9;
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    .inspection-table thead th:first-child {
        min-width: 70px;
    }

    .inspection-table input,
    .inspection-table select {
        min-width: 100px;
    }

    .inspection-note {
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
    }


    .nonconformity-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .nonconformity-option {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        cursor: pointer;
    }

    .photo-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 10px;
    }

    .photo-preview-item {
        display: block;
        overflow: hidden;
        border: 1px solid #dbe3ec;
        border-radius: 10px;
        background: #fff;
    }

    .photo-preview-item img {
        width: 100%;
        height: 110px;
        display: block;
        object-fit: cover;
    }

    .saved-photo-wrap {
        padding: 12px;
        border: 1px solid #dbe3ec;
        border-radius: 12px;
        background: #f8fafc;
    }

    @media (max-width: 575.98px) {
        .back-action-card {
            max-width: 100%;
            flex-wrap: wrap;
        }

        .back-action-card .btn {
            width: 100%;
        }

        .sampling-header {
            align-items: flex-start;
            gap: 20px;
        }

        .nonconformity-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById(
            'innerOuterSamplingForm'
        );

        const jumlahSampelInput = document.getElementById(
            'jumlah_sampel'
        );

        const sampleRows = document.getElementById(
            'sampleRows'
        );

        const submitButton = document.getElementById(
            'submitButton'
        );

        const alertContainer = document.getElementById(
            'alertContainer'
        );

        const konfirmasiSelect = document.getElementById('konfirmasi_ketidaksesuaian');
        const jenisKetidaksesuaianGroup = document.getElementById('jenisKetidaksesuaianGroup');
        const jenisKetidaksesuaianHelp = document.getElementById('jenisKetidaksesuaianHelp');
        const fotoPengecekanInput = document.getElementById('foto_pengecekan');
        const fotoKetidaksesuaianInput = document.getElementById('foto_ketidaksesuaian');
        const fotoKetidaksesuaianHelp = document.getElementById('fotoKetidaksesuaianHelp');
        const fotoKetidaksesuaianRequired = document.getElementById('fotoKetidaksesuaianRequired');
        const hasExistingFotoPengecekan = @json(count($existingFotoPengecekan) > 0);
        const hasExistingFotoKetidaksesuaian = @json(count($existingFotoKetidaksesuaian) > 0);

        const cameraModalElement = document.getElementById('photoCameraModal');
        const cameraModal = new bootstrap.Modal(cameraModalElement);
        const cameraPreview = document.getElementById('photoCameraPreview');
        const cameraCanvas = document.getElementById('photoCameraCanvas');
        const btnTakePhoto = document.getElementById('btnTakePhoto');

        let cameraStream = null;
        let activePhotoInput = null;

        const isOuter = @json($isOuter);

        const savedSamples = @json(
            $sampling?->hasil_sampel ?? []
        );

        let samples = Array.isArray(savedSamples)
            ? savedSamples
            : [];

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function buildOption(value, label, selectedValue) {
            return `
                <option
                    value="${value}"
                    ${selectedValue === value ? 'selected' : ''}
                >
                    ${label}
                </option>
            `;
        }

        function buildStatusSelect(name, selectedValue) {
            return `
                <select
                    name="${name}"
                    class="form-select form-select-sm"
                >
                    <option value="">Pilih</option>
                    ${buildOption('OK', 'OK', selectedValue)}
                    ${buildOption('NG', 'NG', selectedValue)}
                </select>
            `;
        }

        function buildDirectionSelect(name, selectedValue) {
            return `
                <select
                    name="${name}"
                    class="form-select form-select-sm"
                >
                    <option value="">Pilih</option>
                    ${buildOption('V', 'V', selectedValue)}
                    ${buildOption('-', '-', selectedValue)}
                </select>
            `;
        }

        function buildNumberInput(
            index,
            field,
            value,
            step = '0.01'
        ) {
            return `
                <input
                    type="number"
                    min="0"
                    step="${step}"
                    name="samples[${index}][${field}]"
                    value="${escapeHtml(value)}"
                    class="form-control form-control-sm"
                >
            `;
        }

        function buildSampleRows(total) {
            const safeTotal = Math.max(
                1,
                Math.min(parseInt(total || 1), 50)
            );

            while (samples.length < safeTotal) {
                samples.push({});
            }

            samples = samples.slice(0, safeTotal);
            sampleRows.innerHTML = '';

            for (let index = 0; index < safeTotal; index++) {
                const sample = samples[index] ?? {};

                sampleRows.insertAdjacentHTML(
                    'beforeend',
                    `
                        <tr>
                            <td class="text-center sample-number">
                                ${index + 1}
                            </td>

                            <td>
                                ${buildNumberInput(
                                    index,
                                    'berat_gross',
                                    sample.berat_gross
                                )}
                            </td>

                            <td>
                                ${buildNumberInput(
                                    index,
                                    'inside_core',
                                    sample.inside_core
                                )}
                            </td>

                            <td>
                                ${buildNumberInput(
                                    index,
                                    'lebar',
                                    sample.lebar
                                )}
                            </td>

                            <td>
                                ${
                                    isOuter
                                        ? buildNumberInput(
                                            index,
                                            'pitch',
                                            sample.pitch
                                        )
                                        : `
                                            <input
                                                type="text"
                                                name="samples[${index}][pitch]"
                                                class="form-control form-control-sm text-center"
                                                value="-"
                                                readonly
                                            >
                                        `
                                }
                            </td>

                            <td>
                                ${buildNumberInput(
                                    index,
                                    'thickness',
                                    sample.thickness
                                )}
                            </td>

                            <td>
                                ${buildDirectionSelect(
                                    `samples[${index}][arah_vertikal]`,
                                    sample.arah_vertikal
                                )}
                            </td>

                            <td>
                                ${buildDirectionSelect(
                                    `samples[${index}][arah_terbalik]`,
                                    sample.arah_terbalik
                                )}
                            </td>

                            <td>
                                ${buildStatusSelect(
                                    `samples[${index}][laminasi]`,
                                    sample.laminasi
                                )}
                            </td>

                            <td>
                                <input
                                    type="text"
                                    name="samples[${index}][barcode]"
                                    value="${escapeHtml(sample.barcode)}"
                                    class="form-control form-control-sm"
                                >
                            </td>

                            <td>
                                ${buildStatusSelect(
                                    `samples[${index}][design]`,
                                    sample.design
                                )}
                            </td>

                            <td>
                                ${buildStatusSelect(
                                    `samples[${index}][warna]`,
                                    sample.warna
                                )}
                            </td>

                            <td>
                                <input
                                    type="text"
                                    name="samples[${index}][tulisan]"
                                    value="${escapeHtml(sample.tulisan)}"
                                    class="form-control form-control-sm"
                                    placeholder="Tulis hasil"
                                >
                            </td>
                        </tr>
                    `
                );
            }

            const fieldOrder = [
                'berat_gross',
                'inside_core',
                'lebar',
                'pitch',
                'thickness',
                'arah_vertikal',
                'arah_terbalik',
                'laminasi',
                'barcode',
                'design',
                'warna',
                'tulisan'
            ];

            fieldOrder.forEach(
                function (field, fieldIndex) {
                    sampleRows
                        .querySelectorAll(
                            `[name$="[${field}]"]`
                        )
                        .forEach(
                            function (
                                element,
                                rowIndex
                            ) {
                                if (
                                    element.readOnly
                                ) {
                                    element.tabIndex =
                                        -1;

                                    return;
                                }

                                element.tabIndex =
                                    1
                                    + (
                                        fieldIndex
                                        * safeTotal
                                    )
                                    + rowIndex;
                            }
                        );
                }
            );
        }

        function showAlert(type, message) {
            alertContainer.innerHTML = `
                <div
                    class="alert alert-${type}
                           alert-dismissible fade show"
                >
                    ${escapeHtml(message)}

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>
                </div>
            `;

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }


        function getCheckedKetidaksesuaianCount() {
            return jenisKetidaksesuaianGroup.querySelectorAll(
                'input[type="checkbox"]:checked'
            ).length;
        }

        function updateKetidaksesuaianFields() {
            const adaKetidaksesuaian =
                konfirmasiSelect.value === 'Ada';

            jenisKetidaksesuaianGroup
                .querySelectorAll('input[type="checkbox"]')
                .forEach(function (checkbox) {
                    checkbox.disabled = !adaKetidaksesuaian;

                    if (!adaKetidaksesuaian) {
                        checkbox.checked = false;
                    }
                });

            fotoKetidaksesuaianRequired.classList.toggle(
                'd-none',
                !adaKetidaksesuaian
            );

            jenisKetidaksesuaianHelp.textContent = adaKetidaksesuaian
                ? 'Pilih satu atau lebih jenis ketidaksesuaian.'
                : 'Tidak perlu dipilih apabila tidak terdapat ketidaksesuaian.';

            fotoKetidaksesuaianHelp.textContent = adaKetidaksesuaian
                ? 'Wajib minimal 1 foto. Maksimal 10 foto.'
                : 'Tidak wajib apabila hasil pemeriksaan sesuai.';
        }

        function mergeFiles(input, files) {
            const existingCount = Number(
                input.dataset.existingCount ?? 0
            );

            const dataTransfer = new DataTransfer();

            Array.from(input.files ?? []).forEach(function (file) {
                dataTransfer.items.add(file);
            });

            Array.from(files ?? []).forEach(function (file) {
                dataTransfer.items.add(file);
            });

            if (existingCount + dataTransfer.files.length > 10) {
                alert('Total foto maksimal 10.');
                return false;
            }

            input.files = dataTransfer.files;
            renderSelectedPhotos(input);

            return true;
        }

        function renderSelectedPhotos(input) {
            const preview = document.getElementById(
                `${input.id}_preview`
            );

            preview.innerHTML = '';

            Array.from(input.files ?? []).forEach(function (file) {
                const item = document.createElement('div');
                item.className = 'photo-preview-item';

                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.alt = file.name;

                item.appendChild(image);
                preview.appendChild(item);
            });
        }

        document.querySelectorAll('.btn-file-photo')
            .forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById(
                        this.dataset.target
                    ).click();
                });
            });

        document.querySelectorAll('.multi-photo-input')
            .forEach(function (input) {
                input.addEventListener('change', function () {
                    const selectedFiles = Array.from(this.files ?? []);
                    const existingCount = Number(
                        this.dataset.existingCount ?? 0
                    );

                    if (existingCount + selectedFiles.length > 10) {
                        alert('Total foto maksimal 10.');
                        this.value = '';
                        renderSelectedPhotos(this);
                        return;
                    }

                    renderSelectedPhotos(this);
                });
            });

        document.querySelectorAll('.btn-camera-photo')
            .forEach(function (button) {
                button.addEventListener('click', async function () {
                    activePhotoInput = document.getElementById(
                        this.dataset.target
                    );

                    cameraModal.show();

                    try {
                        cameraStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: {
                                    ideal: 'environment'
                                }
                            },
                            audio: false
                        });

                        cameraPreview.srcObject = cameraStream;
                    } catch (error) {
                        alert('Kamera tidak dapat dibuka. Pastikan izin kamera aktif.');
                        cameraModal.hide();
                    }
                });
            });

        btnTakePhoto.addEventListener('click', function () {
            if (!activePhotoInput) {
                return;
            }

            cameraCanvas.width = cameraPreview.videoWidth;
            cameraCanvas.height = cameraPreview.videoHeight;

            cameraCanvas
                .getContext('2d')
                .drawImage(cameraPreview, 0, 0);

            cameraCanvas.toBlob(function (blob) {
                const file = new File(
                    [blob],
                    `foto-${Date.now()}.jpg`,
                    { type: 'image/jpeg' }
                );

                if (mergeFiles(activePhotoInput, [file])) {
                    stopCamera();
                    cameraModal.hide();
                }
            }, 'image/jpeg', 0.9);
        });

        function stopCamera() {
            if (!cameraStream) {
                return;
            }

            cameraStream.getTracks().forEach(function (track) {
                track.stop();
            });

            cameraStream = null;
            cameraPreview.srcObject = null;
        }

        cameraModalElement.addEventListener(
            'hidden.bs.modal',
            stopCamera
        );

        konfirmasiSelect.addEventListener('change', updateKetidaksesuaianFields);

        jumlahSampelInput.addEventListener(
            'input',
            function () {
                buildSampleRows(this.value);
            }
        );

        form.addEventListener(
            'submit',
            async function (event) {
                event.preventDefault();

                const saveMode =
                    event.submitter?.value ?? 'final';

                const isFinal =
                    saveMode === 'final';

                if (
                    isFinal
                    && !form.reportValidity()
                ) {
                    return;
                }


                if (isFinal) {
                    const totalFotoPengecekan =
                        Number(fotoPengecekanInput.dataset.existingCount ?? 0)
                        + fotoPengecekanInput.files.length;

                    if (totalFotoPengecekan < 1) {
                        alert('Foto pengecekan wajib minimal 1 foto.');
                        return;
                    }

                    if (konfirmasiSelect.value === 'Ada') {
                        if (getCheckedKetidaksesuaianCount() < 1) {
                            alert('Pilih minimal satu jenis ketidaksesuaian.');
                            return;
                        }

                        const totalFotoKetidaksesuaian =
                            Number(fotoKetidaksesuaianInput.dataset.existingCount ?? 0)
                            + fotoKetidaksesuaianInput.files.length;

                        if (totalFotoKetidaksesuaian < 1) {
                            alert('Foto ketidaksesuaian wajib minimal 1 foto.');
                            return;
                        }
                    }
                }

                document
                    .querySelectorAll('.save-button')
                    .forEach(function (button) {
                        button.disabled = true;
                    });
                submitButton.innerHTML = `
                    <span
                        class="spinner-border
                               spinner-border-sm me-1"
                    ></span>
                    Menyimpan...
                `;

                const formData =
                    new FormData(form);

                formData.set(
                    'save_mode',
                    saveMode
                );

                try {
                    const response = await fetch(
                        form.action,
                        {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },
                            body: formData
                        }
                    );

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422) {
                            const firstMessage =
                                Object.values(
                                    result.errors ?? {}
                                ).flat()[0]
                                ?? 'Validasi gagal.';

                            throw new Error(firstMessage);
                        }

                        throw new Error(
                            result.message
                            ?? 'Data gagal disimpan.'
                        );
                    }

                    showAlert(
                        'success',
                        result.message
                    );

                    setTimeout(function () {
                        window.location.href =
                            result.redirect_url;
                    }, 1000);
                } catch (error) {
                    showAlert(
                        'danger',
                        error.message
                        ?? 'Terjadi kesalahan.'
                    );
                } finally {
                    document
                        .querySelectorAll('.save-button')
                        .forEach(function (button) {
                            button.disabled = false;
                        });

                    const draftButton =
                        document.querySelector(
                            '.save-button[value="draft"]'
                        );

                    const finalButton =
                        document.querySelector(
                            '.save-button[value="final"]'
                        );

                    if (draftButton) {
                        draftButton.innerHTML = `
                            <i class="mdi mdi-content-save-edit-outline me-1"></i>
                            Simpan Sementara
                        `;
                    }

                    if (finalButton) {
                        finalButton.innerHTML = `
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            Simpan Final
                        `;
                    }
                }
            }
        );

        buildSampleRows(
            jumlahSampelInput.value
        );

        updateKetidaksesuaianFields();
    });
</script>

@endsection