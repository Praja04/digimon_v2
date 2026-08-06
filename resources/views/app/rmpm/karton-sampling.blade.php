@extends('layouts.component.main')

@section('title', 'Pemeriksaan Berat Karton')

@section('content')

@php
    $fotoPengecekanTersimpan =
        is_array($sampling?->foto)
            ? array_values(
                array_filter($sampling->foto)
            )
            : [];

    $fotoKetidaksesuaianTersimpan =
        is_array($sampling?->foto_ketidaksesuaian)
            ? array_values(
                array_filter(
                    $sampling->foto_ketidaksesuaian
                )
            )
            : [];

    $jenisKetidaksesuaianTerpilih =
        is_array($sampling?->jenis_ketidaksesuaian)
            ? $sampling->jenis_ketidaksesuaian
            : [];
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0">
                            Pemeriksaan Berat Karton
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Pengisian berat dan gramasi Karton berdasarkan nomor SPB.
                        </p>
                    </div>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.pm.karton') }}">
                                    Karton
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route(
                                    'rmpm.pm.karton.display',
                                    $packagingIncoming
                                ) }}">
                                    Display Karton
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Berat
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div id="alertContainer" class="mb-3"></div>

        <form
            id="kartonSamplingForm"
            method="POST"
            action="{{ route(
                'rmpm.pm.karton.sampling.store',
                $packagingIncoming
            ) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="card border-0 shadow-sm sampling-card">

                <div class="sampling-header">
                    <div>
                        <span class="sampling-label">
                            PACKAGING ONLINE
                        </span>

                        <h2 class="mb-1">
                            BERAT KARTON
                        </h2>

                        <p class="mb-0">
                            Pemeriksaan berat, gramasi, dan hasil kesesuaian Karton.
                        </p>
                    </div>

                    <div class="sampling-header-icon">
                        <i class="mdi mdi-scale-balance"></i>
                    </div>
                </div>

                <div class="card-body p-4">

                    <div class="section-title">
                        <i class="mdi mdi-information-outline"></i>
                        Identitas Incoming
                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">
                                Nomor SPB
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $packagingIncoming->no_spb }}"
                                readonly
                            >
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">
                                Jenis Incoming
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $packagingIncoming->jenisIncoming?->nama ?? '-' }}"
                                readonly
                            >
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">
                                Supplier
                            </label>

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
                            <label class="form-label">
                                Nomor Mobil
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $packagingIncoming->no_mobil ?? '-' }}"
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
                                    $sampling?->jumlah_sampel ?? 4
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
                            >
                        </div>

                    </div>

                    <div class="section-title">
                        <i class="mdi mdi-scale-balance"></i>
                        Hasil Pemeriksaan Berat
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle inspection-table">
                            <thead>
                                <tr>
                                    <th>No. Sampel</th>
                                    <th>Berat</th>
                                    <th>Hasil Berat</th>
                                    <th>Gramasi</th>
                                </tr>
                            </thead>

                            <tbody id="sampleRows"></tbody>
                        </table>
                    </div>

                    <div class="section-title mt-4">
                        <i class="mdi mdi-camera-outline"></i>
                        Foto Pengecekan
                    </div>

                    <div class="photo-field">

                        <div class="d-flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                data-camera-target="foto"
                            >
                                <i class="mdi mdi-camera me-1"></i>
                                Gunakan Kamera
                            </button>

                            <label class="btn btn-outline-secondary mb-0">
                                <i class="mdi mdi-folder-image me-1"></i>
                                Pilih File

                                <input
                                    type="file"
                                    name="foto[]"
                                    id="foto"
                                    accept="image/*"
                                    multiple
                                    hidden
                                >
                            </label>
                        </div>

                        <small class="text-muted d-block mt-2">
                            Maksimal total 10 foto. Maksimal 5 MB per foto.
                        </small>

                        <div
                            id="fotoPreview"
                            class="photo-grid mt-3"
                        >
                            @foreach (
                                $fotoPengecekanTersimpan
                                as $fotoPath
                            )
                                <a
                                    href="{{ asset(
                                        'storage/' . $fotoPath
                                    ) }}"
                                    target="_blank"
                                >
                                    <img
                                        src="{{ asset(
                                            'storage/' . $fotoPath
                                        ) }}"
                                        alt="Foto pengecekan"
                                    >
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="mdi mdi-clipboard-check-outline"></i>
                        Kesimpulan Pemeriksaan
                    </div>

                    <div class="row g-3">

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">
                                CoA
                            </label>

                            <select
                                name="coa"
                                class="form-select"
                            >
                                <option value="">
                                    Pilih CoA
                                </option>

                                <option
                                    value="Ada"
                                    @selected(
                                        $sampling?->coa === 'Ada'
                                    )
                                >
                                    Ada
                                </option>

                                <option
                                    value="Tidak Ada"
                                    @selected(
                                        $sampling?->coa === 'Tidak Ada'
                                    )
                                >
                                    Tidak Ada
                                </option>
                            </select>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">
                                Rekomendasi
                            </label>

                            <select
                                name="rekomendasi"
                                class="form-select"
                                required
                            >
                                <option value="">
                                    Pilih rekomendasi
                                </option>

                                @foreach ([
                                    'Diterima',
                                    'Diterima Bersyarat',
                                    'Ditolak',
                                    'WIP',
                                ] as $item)
                                    <option
                                        value="{{ $item }}"
                                        @selected(
                                            $sampling?->rekomendasi
                                            === $item
                                        )
                                    >
                                        {{ $item }}
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
                                required
                            >
                                <option value="">
                                    Pilih konfirmasi
                                </option>

                                <option
                                    value="Ada"
                                    @selected(
                                        $sampling?->konfirmasi_ketidaksesuaian
                                        === 'Ada'
                                    )
                                >
                                    Ada
                                </option>

                                <option
                                    value="Tidak Ada"
                                    @selected(
                                        $sampling?->konfirmasi_ketidaksesuaian
                                        === 'Tidak Ada'
                                    )
                                >
                                    Tidak Ada
                                </option>
                            </select>
                        </div>

                        <div
                            class="col-xl-3 col-md-6"
                            id="jenisKetidaksesuaianWrapper"
                        >
                            <label class="form-label">
                                Jenis Ketidaksesuaian
                            </label>

                            <div class="checkbox-card">
                                @foreach ([
                                    'Berat Under',
                                    'Berat Over',
                                    'Gramasi Tidak Standar',
                                ] as $item)
                                    <div class="form-check mb-2">
                                        <input
                                            type="checkbox"
                                            name="jenis_ketidaksesuaian[]"
                                            value="{{ $item }}"
                                            id="jenis_{{ $loop->index }}"
                                            class="form-check-input jenis-ketidaksesuaian"
                                            @checked(
                                                in_array(
                                                    $item,
                                                    $jenisKetidaksesuaianTerpilih,
                                                    true
                                                )
                                            )
                                        >

                                        <label
                                            for="jenis_{{ $loop->index }}"
                                            class="form-check-label"
                                        >
                                            {{ $item }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div
                        id="fotoKetidaksesuaianWrapper"
                        class="mt-4"
                    >
                        <label class="form-label fw-semibold">
                            Foto Ketidaksesuaian
                        </label>

                        <div class="d-flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="btn btn-outline-danger"
                                data-camera-target="foto_ketidaksesuaian"
                            >
                                <i class="mdi mdi-camera me-1"></i>
                                Gunakan Kamera
                            </button>

                            <label class="btn btn-outline-secondary mb-0">
                                <i class="mdi mdi-folder-image me-1"></i>
                                Pilih File

                                <input
                                    type="file"
                                    name="foto_ketidaksesuaian[]"
                                    id="foto_ketidaksesuaian"
                                    accept="image/*"
                                    multiple
                                    hidden
                                >
                            </label>
                        </div>

                        <small class="text-muted d-block mt-2">
                            Maksimal total 10 foto. Maksimal 5 MB per foto.
                        </small>

                        <div
                            id="fotoKetidaksesuaianPreview"
                            class="photo-grid mt-3"
                        >
                            @foreach (
                                $fotoKetidaksesuaianTersimpan
                                as $fotoPath
                            )
                                <a
                                    href="{{ asset(
                                        'storage/' . $fotoPath
                                    ) }}"
                                    target="_blank"
                                >
                                    <img
                                        src="{{ asset(
                                            'storage/' . $fotoPath
                                        ) }}"
                                        alt="Foto ketidaksesuaian"
                                    >
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label">
                            Keterangan
                        </label>

                        <textarea
                            name="keterangan"
                            class="form-control"
                            rows="4"
                        >{{ old(
                            'keterangan',
                            $sampling?->keterangan
                        ) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a
                            href="{{ route(
                                'rmpm.pm.karton.display',
                                $packagingIncoming
                            ) }}"
                            class="btn btn-light"
                        >
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Batal
                        </a>

                        <button
                            type="submit"
                            id="submitButton"
                            class="btn btn-primary px-4"
                        >
                            <i class="mdi mdi-content-save-outline me-1"></i>
                            Simpan Berat
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

<div
    class="modal fade"
    id="cameraModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Ambil Foto
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>
            </div>

            <div class="modal-body">
                <video
                    id="cameraVideo"
                    autoplay
                    playsinline
                    class="camera-video"
                ></video>

                <canvas
                    id="cameraCanvas"
                    class="d-none"
                ></canvas>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-primary"
                    id="captureButton"
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
    .sampling-card {
        overflow: hidden;
        border-radius: 18px;
    }

    .sampling-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 28px;
        border-bottom: 4px solid #eab308;
        background: linear-gradient(
            135deg,
            #fefce8,
            #f8fafc
        );
    }

    .sampling-label {
        display: block;
        margin-bottom: 5px;
        color: #a16207;
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
        color: #ca8a04;
        font-size: 40px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        font-weight: 700;
    }

    .inspection-table thead th {
        min-width: 170px;
        background: #f1f5f9;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .photo-field,
    .checkbox-card {
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .photo-grid {
        display: grid;
        grid-template-columns: repeat(
            auto-fill,
            minmax(120px, 1fr)
        );
        gap: 12px;
    }

    .photo-grid img {
        width: 100%;
        height: 120px;
        display: block;
        object-fit: cover;
        border: 1px solid #dee2e6;
        border-radius: 10px;
    }

    .camera-video {
        width: 100%;
        min-height: 280px;
        max-height: 65vh;
        border-radius: 12px;
        background: #111827;
        object-fit: cover;
    }
</style>

@endsection

@section('scripts')

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const form =
            document.getElementById(
                'kartonSamplingForm'
            );

        const jumlahInput =
            document.getElementById(
                'jumlah_sampel'
            );

        const rowsContainer =
            document.getElementById(
                'sampleRows'
            );

        const konfirmasiSelect =
            document.getElementById(
                'konfirmasi_ketidaksesuaian'
            );

        const jenisWrapper =
            document.getElementById(
                'jenisKetidaksesuaianWrapper'
            );

        const fotoKetidaksesuaianWrapper =
            document.getElementById(
                'fotoKetidaksesuaianWrapper'
            );

        const submitButton =
            document.getElementById(
                'submitButton'
            );

        const alertContainer =
            document.getElementById(
                'alertContainer'
            );

        const existingFotoCount =
            @json(
                count($fotoPengecekanTersimpan)
            );

        const existingFotoKetidaksesuaianCount =
            @json(
                count(
                    $fotoKetidaksesuaianTersimpan
                )
            );

        const savedSamples =
            @json(
                $sampling?->hasil_sampel ?? []
            );

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function buildRows(total) {
            const safeTotal = Math.max(
                1,
                Math.min(
                    parseInt(total || 1),
                    50
                )
            );

            rowsContainer.innerHTML = '';

            for (
                let index = 0;
                index < safeTotal;
                index++
            ) {
                const sample =
                    savedSamples[index] ?? {};

                rowsContainer.insertAdjacentHTML(
                    'beforeend',
                    `
                        <tr>
                            <td class="text-center">
                                ${index + 1}
                            </td>

                            <td>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="samples[${index}][berat]"
                                    value="${escapeHtml(sample.berat)}"
                                    class="form-control"
                                >
                            </td>

                            <td>
                                <select
                                    name="samples[${index}][hasil_berat]"
                                    class="form-select"
                                >
                                    <option value="">
                                        Pilih
                                    </option>

                                    <option
                                        value="OK"
                                        ${
                                            sample.hasil_berat === 'OK'
                                                ? 'selected'
                                                : ''
                                        }
                                    >
                                        OK
                                    </option>

                                    <option
                                        value="NOK"
                                        ${
                                            sample.hasil_berat === 'NOK'
                                                ? 'selected'
                                                : ''
                                        }
                                    >
                                        NOK
                                    </option>
                                </select>
                            </td>

                            <td>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="samples[${index}][gramasi]"
                                    value="${escapeHtml(sample.gramasi)}"
                                    class="form-control"
                                >
                            </td>
                        </tr>
                    `
                );
            }
        }

        function showAlert(
            type,
            message
        ) {
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

        function validatePhotoInput(
            input,
            existingCount
        ) {
            if (
                existingCount
                + input.files.length
                > 10
            ) {
                alert(
                    'Maksimal total 10 foto.'
                );

                input.value = '';

                return false;
            }

            return true;
        }

        function previewFiles(
            input,
            previewId,
            existingCount
        ) {
            if (
                !validatePhotoInput(
                    input,
                    existingCount
                )
            ) {
                return;
            }

            const preview =
                document.getElementById(
                    previewId
                );

            preview
                .querySelectorAll(
                    '[data-new-preview="1"]'
                )
                .forEach(
                    element => element.remove()
                );

            Array.from(input.files)
                .forEach(function (file) {
                    const wrapper =
                        document.createElement('div');

                    wrapper.dataset.newPreview = '1';

                    const image =
                        document.createElement('img');

                    image.src =
                        URL.createObjectURL(file);

                    image.alt =
                        'Preview foto baru';

                    wrapper.appendChild(image);
                    preview.appendChild(wrapper);
                });
        }

        const fotoInput =
            document.getElementById('foto');

        const fotoKetidaksesuaianInput =
            document.getElementById(
                'foto_ketidaksesuaian'
            );

        fotoInput.addEventListener(
            'change',
            function () {
                previewFiles(
                    this,
                    'fotoPreview',
                    existingFotoCount
                );
            }
        );

        fotoKetidaksesuaianInput
            .addEventListener(
                'change',
                function () {
                    previewFiles(
                        this,
                        'fotoKetidaksesuaianPreview',
                        existingFotoKetidaksesuaianCount
                    );
                }
            );

        function updateKetidaksesuaian() {
            const ada =
                konfirmasiSelect.value === 'Ada';

            jenisWrapper.classList.toggle(
                'd-none',
                !ada
            );

            fotoKetidaksesuaianWrapper
                .classList.toggle(
                    'd-none',
                    !ada
                );

            document
                .querySelectorAll(
                    '.jenis-ketidaksesuaian'
                )
                .forEach(function (checkbox) {
                    checkbox.disabled = !ada;

                    if (!ada) {
                        checkbox.checked = false;
                    }
                });

            fotoKetidaksesuaianInput
                .disabled = !ada;
        }

        jumlahInput.addEventListener(
            'input',
            function () {
                buildRows(this.value);
            }
        );

        konfirmasiSelect.addEventListener(
            'change',
            updateKetidaksesuaian
        );

        let stream = null;
        let activeInputId = null;

        const cameraModalElement =
            document.getElementById(
                'cameraModal'
            );

        const cameraModal =
            new bootstrap.Modal(
                cameraModalElement
            );

        const video =
            document.getElementById(
                'cameraVideo'
            );

        const canvas =
            document.getElementById(
                'cameraCanvas'
            );

        document
            .querySelectorAll(
                '[data-camera-target]'
            )
            .forEach(function (button) {
                button.addEventListener(
                    'click',
                    async function () {
                        activeInputId =
                            this.dataset.cameraTarget;

                        const activeInput =
                            document.getElementById(
                                activeInputId
                            );

                        if (activeInput.disabled) {
                            return;
                        }

                        try {
                            stream =
                                await navigator
                                    .mediaDevices
                                    .getUserMedia({
                                        video: {
                                            facingMode:
                                                'environment'
                                        },
                                        audio: false
                                    });

                            video.srcObject = stream;
                            cameraModal.show();
                        } catch (error) {
                            alert(
                                'Kamera tidak dapat diakses. Periksa izin kamera browser.'
                            );
                        }
                    }
                );
            });

        document
            .getElementById(
                'captureButton'
            )
            .addEventListener(
                'click',
                function () {
                    if (
                        !activeInputId
                        || !stream
                    ) {
                        return;
                    }

                    canvas.width =
                        video.videoWidth;

                    canvas.height =
                        video.videoHeight;

                    canvas
                        .getContext('2d')
                        .drawImage(
                            video,
                            0,
                            0
                        );

                    canvas.toBlob(
                        function (blob) {
                            const input =
                                document.getElementById(
                                    activeInputId
                                );

                            const existingCount =
                                activeInputId === 'foto'
                                    ? existingFotoCount
                                    : existingFotoKetidaksesuaianCount;

                            if (
                                existingCount
                                + input.files.length
                                >= 10
                            ) {
                                alert(
                                    'Maksimal total 10 foto.'
                                );

                                return;
                            }

                            const dataTransfer =
                                new DataTransfer();

                            Array.from(
                                input.files
                            ).forEach(
                                file =>
                                    dataTransfer
                                        .items
                                        .add(file)
                            );

                            const cameraFile =
                                new File(
                                    [blob],
                                    `camera-${Date.now()}.jpg`,
                                    {
                                        type:
                                            'image/jpeg'
                                    }
                                );

                            dataTransfer
                                .items
                                .add(cameraFile);

                            input.files =
                                dataTransfer.files;

                            input.dispatchEvent(
                                new Event('change')
                            );

                            cameraModal.hide();
                        },
                        'image/jpeg',
                        0.9
                    );
                }
            );

        cameraModalElement.addEventListener(
            'hidden.bs.modal',
            function () {
                if (stream) {
                    stream
                        .getTracks()
                        .forEach(
                            track =>
                                track.stop()
                        );

                    stream = null;
                }

                video.srcObject = null;
            }
        );

        form.addEventListener(
            'submit',
            async function (event) {
                event.preventDefault();

                if (!form.reportValidity()) {
                    return;
                }

                if (
                    konfirmasiSelect.value
                    === 'Ada'
                    && document.querySelectorAll(
                        '.jenis-ketidaksesuaian:checked'
                    ).length === 0
                ) {
                    showAlert(
                        'danger',
                        'Pilih minimal satu jenis ketidaksesuaian.'
                    );

                    return;
                }

                if (
                    existingFotoCount === 0
                    && fotoInput.files.length === 0
                ) {
                    showAlert(
                        'danger',
                        'Foto pengecekan wajib diunggah.'
                    );

                    return;
                }

                if (
                    konfirmasiSelect.value === 'Ada'
                    && existingFotoKetidaksesuaianCount === 0
                    && fotoKetidaksesuaianInput
                        .files.length === 0
                ) {
                    showAlert(
                        'danger',
                        'Foto ketidaksesuaian wajib diunggah.'
                    );

                    return;
                }

                submitButton.disabled = true;

                submitButton.innerHTML = `
                    <span
                        class="spinner-border
                               spinner-border-sm me-1"
                    ></span>
                    Menyimpan...
                `;

                try {
                    const response =
                        await fetch(
                            form.action,
                            {
                                method: 'POST',
                                headers: {
                                    Accept:
                                        'application/json',
                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                },
                                body:
                                    new FormData(form)
                            }
                        );

                    const result =
                        await response.json();

                    if (!response.ok) {
                        const errors =
                            Object.values(
                                result.errors ?? {}
                            ).flat();

                        throw new Error(
                            errors[0]
                            ?? result.message
                            ?? 'Data gagal disimpan.'
                        );
                    }

                    showAlert(
                        'success',
                        result.message
                    );

                    setTimeout(
                        function () {
                            window.location.href =
                                result.redirect_url;
                        },
                        800
                    );
                } catch (error) {
                    showAlert(
                        'danger',
                        error.message
                        ?? 'Terjadi kesalahan.'
                    );
                } finally {
                    submitButton.disabled = false;

                    submitButton.innerHTML = `
                        <i class="mdi mdi-content-save-outline me-1"></i>
                        Simpan Berat
                    `;
                }
            }
        );

        buildRows(
            jumlahInput.value
        );

        updateKetidaksesuaian();
    }
);
</script>

@endsection
