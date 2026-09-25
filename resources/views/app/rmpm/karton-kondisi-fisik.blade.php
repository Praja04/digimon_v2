@extends('layouts.component.main')

@section('title', 'Pemeriksaan Kondisi Fisik Karton')

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

    $opsiJenisKetidaksesuaian = [
        'Dimensi Tidak Standar',
        'BCT Tidak Standar',
        'Berat Under',
        'Berat Over',
        'Gramasi Tidak Standar',
        'Barcode Tidak Terbaca',
        'Design Tidak Sesuai',
        'Warna Tidak Sesuai',
        'Tulisan Tidak Sesuai',
    ];

    $jenisKetidaksesuaianTerpilih =
        is_array($sampling?->jenis_ketidaksesuaian)
            ? $sampling->jenis_ketidaksesuaian
            : [];

    $jenisKetidaksesuaianLainnyaTersimpan =
        collect($jenisKetidaksesuaianTerpilih)
            ->first(
                fn ($value) =>
                    ! in_array(
                        $value,
                        $opsiJenisKetidaksesuaian,
                        true
                    )
            );

    $jenisKetidaksesuaianLainnyaValue = old(
        'jenis_ketidaksesuaian_lainnya',
        $jenisKetidaksesuaianLainnyaTersimpan
    );

    $lainnyaTerpilih =
        filled($jenisKetidaksesuaianLainnyaValue);

    $firstSample =
        is_array($sampling?->hasil_sampel)
        && isset($sampling->hasil_sampel[0])
            ? $sampling->hasil_sampel[0]
            : [];

    $gramasiTersimpan =
        $firstSample['gramasi']
        ?? collect($sampling?->hasil_sampel ?? [])
            ->pluck('gramasi')
            ->first(
                fn ($value) => filled($value)
            );

    $gramasiSpb = old(
        'gramasi',
        $gramasiTersimpan
    );

    $gramasiTipeTersimpan =
        $firstSample['gramasi_tipe']
        ?? null;

    $gramasiTipe = old(
        'gramasi_tipe',
        $gramasiTipeTersimpan
    );

    $gramasiLayersTersimpan =
        $firstSample['gramasi_layers']
        ?? [];

    $gramasiLayers = old(
        'gramasi_layers',
        $gramasiLayersTersimpan
    );

    $scanBarcodeTersimpan =
        $firstSample['scan_barcode']
        ?? collect($sampling?->hasil_sampel ?? [])
            ->pluck('scan_barcode')
            ->first(
                fn ($value) => filled($value)
            );

    $scanBarcodeSpb = old(
        'scan_barcode',
        $scanBarcodeTersimpan
    );

    $noBarcodeTersimpan =
        $firstSample['no_barcode']
        ?? collect($sampling?->hasil_sampel ?? [])
            ->pluck('no_barcode')
            ->first(
                fn ($value) => filled($value)
            );

    $noBarcodeSpb = old(
        'no_barcode',
        $noBarcodeTersimpan
    );

    $isForeman =
        auth()->check()
        && auth()->user()?->role === 'Foreman';

    /*
     * Pada Karton, data Draft dan Final disimpan di tabel terpisah.
     * Kalau $finalSampling sudah ada, berarti proses pernah disimpan Final.
     */
    $isFinal =
        $finalSampling !== null;

    $isLocked =
        $isFinal
        && ! $isForeman;
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0">
                            Pemeriksaan Kondisi Fisik Karton
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Pemeriksaan dimensi, visual, gramasi, barcode, dan foto Karton berdasarkan nomor SPB.
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
                                Kondisi Fisik
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div id="alertContainer" class="mb-3"></div>

        @if (
            ! $isFinal
            && $sampling?->status_proses === 'draft'
        )
            <div class="alert alert-warning">
                <i class="mdi mdi-content-save-edit-outline me-1"></i>
                Data sementara ditemukan. Silakan lanjutkan pengisian lalu pilih
                <strong>Simpan Final</strong>.
            </div>
        @endif

        @if ($isLocked)
            <div class="alert alert-info">
                <i class="mdi mdi-lock-outline me-1"></i>
                Data pemeriksaan Karton ini sudah <strong>Final</strong>.
                Data hanya dapat dilihat. Koreksi data Final hanya dapat dilakukan oleh
                <strong>Foreman</strong>.
            </div>
        @elseif ($isFinal && $isForeman)
            <div class="alert alert-warning">
                <i class="mdi mdi-account-edit-outline me-1"></i>
                Data pemeriksaan Karton ini sudah <strong>Final</strong>.
                Anda login sebagai <strong>Foreman</strong>, sehingga data masih dapat dikoreksi.
            </div>
        @endif

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

            <fieldset
                id="kartonSamplingFieldset"
                @disabled($isLocked)
            >
            <div class="card border-0 shadow-sm sampling-card">

                <div class="sampling-header">
                    <div>
                        <span class="sampling-label">
                            PACKAGING ONLINE
                        </span>

                        <h2 class="mb-1">
                            KONDISI FISIK KARTON
                        </h2>

                        <p class="mb-0">
                            Pemeriksaan lengkap kondisi fisik Karton sesuai standar Packaging Online.
                        </p>
                    </div>

                    <div class="sampling-header-icon">
                        <i class="mdi mdi-ruler-square"></i>
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
                                class="form-control bg-light"
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
                                class="form-control bg-light"
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
                                class="form-control bg-light"
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
                                class="form-control bg-light"
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

                        <div class="col-xl-6 col-md-6">
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
                                class="form-control bg-light"
                                min="1"
                                max="50"
                                value="{{ $packagingIncoming->jumlah_sampel }}"
                                readonly
                                required
                            >

                            <small class="text-muted d-block mt-1">
                                Jumlah sampel mengikuti data Incoming PM dan tidak perlu diisi ulang.
                            </small>
                        </div>

                        <div class="col-xl-6 col-md-6">
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
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                value="{{ old(
                                    'no_batch',
                                    $sampling?->no_batch
                                ) }}"
                            >
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="mdi mdi-clipboard-text-outline"></i>
                        Hasil Pemeriksaan Sampel Karton
                    </div>

                    <div class="table-responsive inspection-table-wrapper">
                        <table class="table table-bordered align-middle inspection-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 60px;">No. Sampel</th>
                                    <th>Panjang (mm)</th>
                                    <th>Lebar (mm)</th>
                                    <th>Tinggi (mm)</th>
                                    <th class="text-center" style="min-width: 130px;">Design</th>
                                    <th class="text-center" style="min-width: 130px;">Warna</th>
                                    <th class="text-center" style="min-width: 130px;">Tulisan</th>
                                </tr>
                            </thead>

                            <tbody id="sampleRows"></tbody>
                        </table>
                    </div>

                    <div class="section-title mt-4">
                        <i class="mdi mdi-layers-outline"></i>
                        Gramasi SPB
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-xl-4 col-md-6">
                            <label
                                for="gramasi_tipe"
                                class="form-label fw-semibold"
                            >
                                Tipe Wall Karton
                            </label>

                            <select
                                name="gramasi_tipe"
                                id="gramasi_tipe"
                                class="form-select"
                            >
                                <option value="">-- Pilih Tipe Wall --</option>
                                <option
                                    value="Single Wall"
                                    @selected($gramasiTipe === 'Single Wall')
                                >
                                    Single Wall (3 Layer: K - M - K)
                                </option>
                                <option
                                    value="Double Wall"
                                    @selected($gramasiTipe === 'Double Wall')
                                >
                                    Double Wall (5 Layer: K - M - M - M - K)
                                </option>
                            </select>

                            <small class="text-muted d-block mt-1">
                                Pilih jenis wall untuk menampilkan input layer gramasi.
                            </small>
                        </div>
                    </div>

                    <div
                        id="gramasiLayersWrapper"
                        class="card border rounded-3 p-3 mb-4 bg-light-subtle d-none"
                        style="max-width: 540px;"
                    >
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0 text-dark" id="gramasiLayersTitle">
                                Detail Layer Gramasi
                            </h6>
                            <span class="badge bg-primary" id="gramasiLayersBadge"></span>
                        </div>

                        <div id="gramasiLayersContainer" class="d-flex flex-column gap-2">
                            <!-- Dynamic Layer Inputs (K, M, K or K, M, M, M, K) -->
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="mdi mdi-camera-outline"></i>
                        Foto Pengecekan
                    </div>

                    <div class="photo-field mb-4">

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
                            Maksimal total 10 foto. Maksimal 2 MB per foto.
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
                        <i class="mdi mdi-barcode-scan"></i>
                        Pemeriksaan Barcode
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-3">
                        <i class="mdi mdi-barcode-scan me-1"></i>
                        Kolom <strong>No Barcode</strong> dapat diisi manual
                        atau langsung menggunakan barcode scanner fisik (HID/keyboard).
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-xl-4 col-md-6">
                            <label
                                for="scan_barcode"
                                class="form-label fw-semibold"
                            >
                                Scan Barcode
                            </label>

                            <select
                                name="scan_barcode"
                                id="scan_barcode"
                                class="form-select"
                            >
                                <option value="">Pilih Status Barcode</option>
                                <option
                                    value="Terbaca"
                                    @selected($scanBarcodeSpb === 'Terbaca')
                                >
                                    Terbaca
                                </option>
                                <option
                                    value="Tidak Terbaca"
                                    @selected($scanBarcodeSpb === 'Tidak Terbaca')
                                >
                                    Tidak Terbaca
                                </option>
                            </select>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <label
                                for="no_barcode"
                                class="form-label fw-semibold"
                            >
                                No Barcode
                            </label>

                            <input
                                type="text"
                                name="no_barcode"
                                id="no_barcode"
                                class="form-control barcode-input"
                                value="{{ $noBarcodeSpb }}"
                                autocomplete="off"
                                placeholder="Scan / ketik barcode"
                            >
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
                                @foreach ($opsiJenisKetidaksesuaian as $item)
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

                                <div class="form-check mb-0">
                                    <input
                                        type="checkbox"
                                        name="jenis_ketidaksesuaian[]"
                                        value="Lainnya"
                                        id="jenis_lainnya"
                                        class="form-check-input jenis-ketidaksesuaian"
                                        @checked($lainnyaTerpilih)
                                    >

                                    <label
                                        for="jenis_lainnya"
                                        class="form-check-label"
                                    >
                                        Lainnya
                                    </label>
                                </div>
                            </div>

                            <div
                                id="jenisLainnyaWrapper"
                                class="mt-3 {{ $lainnyaTerpilih ? '' : 'd-none' }}"
                            >
                                <label
                                    for="jenis_ketidaksesuaian_lainnya"
                                    class="form-label"
                                >
                                    Jenis Ketidaksesuaian Lainnya
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="jenis_ketidaksesuaian_lainnya"
                                    id="jenis_ketidaksesuaian_lainnya"
                                    class="form-control"
                                    maxlength="255"
                                    value="{{ $jenisKetidaksesuaianLainnyaValue }}"
                                    placeholder="Tulis jenis ketidaksesuaian lainnya"
                                >
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
                            Maksimal total 10 foto. Maksimal 2 MB per foto.
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
                </div>
            </div>
            </fieldset>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a
                    href="{{ route(
                        'rmpm.pm.karton.display',
                        $packagingIncoming
                    ) }}"
                    class="btn btn-light"
                >
                    <i class="mdi mdi-arrow-left me-1"></i>
                    Kembali
                </a>

                @if (! $isLocked)
                    @if (! $isFinal)
                        <button
                            type="submit"
                            value="draft"
                            class="btn btn-warning px-4 save-button"
                            formnovalidate
                        >
                            <i class="mdi mdi-content-save-edit-outline me-1"></i>
                            Simpan Sementara
                        </button>
                    @endif

                    <button
                        type="submit"
                        value="final"
                        id="submitButton"
                        class="btn btn-primary px-4 save-button"
                    >
                        <i class="mdi mdi-check-circle-outline me-1"></i>
                        {{ $isFinal && $isForeman
                            ? 'Simpan Koreksi'
                            : 'Simpan Final' }}
                    </button>
                @endif
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
        border-bottom: 4px solid #65a30d;
        background: linear-gradient(
            135deg,
            #f7fee7,
            #f8fafc
        );
    }

    .sampling-label {
        display: block;
        margin-bottom: 5px;
        color: #4d7c0f;
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
        color: #65a30d;
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

    .inspection-table-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .inspection-table {
        min-width: 1200px;
        margin-bottom: 0;
    }

    .option-radio-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 145px;
    }

    .option-radio {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        margin: 0;
        padding: 6px 9px;
        border: 1px solid #dbe3ec;
        border-radius: 8px;
        background: #fff;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: .15s ease;
        white-space: nowrap;
    }

    .option-radio:hover {
        border-color: #6366f1;
        background: #eef2ff;
    }

    .option-radio:focus-within {
        border-color: #6366f1;
        background: #eef2ff;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.25);
    }

    .option-radio input {
        min-width: auto !important;
        width: 14px;
        height: 14px;
        margin: 0;
        cursor: pointer;
    }

    .option-radio.is-selected {
        border-color: #6366f1;
        background: #eef2ff;
        color: #4338ca;
    }

    .inspection-table thead th {
        padding: 10px 8px;
        background: #f1f5f9;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        font-size: 13px;
    }

    .inspection-table tbody td {
        padding: 8px;
        vertical-align: middle;
    }

    .inspection-table th:first-child,
    .inspection-table td:first-child {
        width: 85px;
        min-width: 85px;
        position: sticky;
        left: 0;
        z-index: 2;
        background: #ffffff;
    }

    .inspection-table thead th:first-child {
        z-index: 3;
        background: #f1f5f9;
    }

    .inspection-table input,
    .inspection-table select {
        min-width: 115px;
    }

    .inspection-table .barcode-input {
        min-width: 190px;
    }

    .inspection-table .foto-sample-input {
        min-width: 160px;
    }

    .sample-photo-saved {
        display: block;
        margin-top: 5px;
        font-size: 11px;
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
        const isSamplingLocked =
            @json($isLocked);

        const isFinalRecord =
            @json($isFinal);

        const isForeman =
            @json($isForeman);

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

        const jenisLainnyaCheckbox =
            document.getElementById(
                'jenis_lainnya'
            );

        const jenisLainnyaWrapper =
            document.getElementById(
                'jenisLainnyaWrapper'
            );

        const jenisLainnyaInput =
            document.getElementById(
                'jenis_ketidaksesuaian_lainnya'
            );

        const submitButton =
            document.getElementById(
                'submitButton'
            );

        const gramasiTipeSelect =
            document.getElementById(
                'gramasi_tipe'
            );

        const gramasiLayersWrapper =
            document.getElementById(
                'gramasiLayersWrapper'
            );

        const gramasiLayersContainer =
            document.getElementById(
                'gramasiLayersContainer'
            );

        const gramasiLayersTitle =
            document.getElementById(
                'gramasiLayersTitle'
            );

        const gramasiLayersBadge =
            document.getElementById(
                'gramasiLayersBadge'
            );

        const gramasiTotalInput =
            document.getElementById(
                'gramasi'
            );

        const initialGramasiTipe =
            @json($gramasiTipe);

        const initialGramasiLayers =
            @json(
                is_array($gramasiLayers)
                    ? array_values($gramasiLayers)
                    : []
            );

        const layerConfig = {
            'Single Wall': [
                { label: 'K', desc: 'Kraft / Outer Liner' },
                { label: 'M', desc: 'Medium / Flute' },
                { label: 'K', desc: 'Kraft / Inner Liner' },
            ],
            'Double Wall': [
                { label: 'K', desc: 'Kraft / Outer Liner' },
                { label: 'M', desc: 'Medium / Flute 1' },
                { label: 'M', desc: 'Medium / Middle Liner' },
                { label: 'M', desc: 'Medium / Flute 2' },
                { label: 'K', desc: 'Kraft / Inner Liner' },
            ],
        };

        function calculateGramasiTotal() {
            if (!gramasiLayersContainer) return;
            const inputs =
                gramasiLayersContainer.querySelectorAll(
                    '.gramasi-layer-input'
                );

            let total = 0;
            let hasValue = false;

            inputs.forEach(function (input) {
                const val = parseFloat(input.value);
                if (!isNaN(val)) {
                    total += val;
                    hasValue = true;
                }
            });

            if (gramasiTotalInput) {
                gramasiTotalInput.value = hasValue ? parseFloat(total.toFixed(2)) : '';
            }
        }

        function renderGramasiLayers(type, savedValues = []) {
            if (!gramasiLayersWrapper || !gramasiLayersContainer) return;

            if (!type || !layerConfig[type]) {
                gramasiLayersWrapper.classList.add('d-none');
                gramasiLayersContainer.innerHTML = '';
                return;
            }

            const layers = layerConfig[type];
            if (gramasiLayersTitle) {
                gramasiLayersTitle.textContent = `Detail Layer Gramasi (${type})`;
            }
            if (gramasiLayersBadge) {
                gramasiLayersBadge.textContent = `${layers.length} Layer`;
            }

            gramasiLayersWrapper.classList.remove('d-none');

            let html = '';
            layers.forEach(function (layer, idx) {
                const val =
                    savedValues[idx] !== undefined && savedValues[idx] !== null
                        ? savedValues[idx]
                        : '';

                html += `
                    <div class="row align-items-center g-2">
                        <div class="col-3 col-sm-2">
                            <span class="badge bg-secondary-subtle text-dark border w-100 py-2 fs-6 fw-bold text-center">
                                ${escapeHtml(layer.label)} :
                            </span>
                        </div>
                        <div class="col-9 col-sm-10">
                            <div class="input-group">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="gramasi_layers[${idx}]"
                                    id="gramasi_layer_${idx}"
                                    class="form-control gramasi-layer-input"
                                    value="${escapeHtml(val)}"
                                    placeholder="${escapeHtml(layer.desc)}"
                                    ${isSamplingLocked ? 'disabled' : ''}
                                >
                                <span class="input-group-text">gsm</span>
                            </div>
                        </div>
                    </div>
                `;
            });

            gramasiLayersContainer.innerHTML = html;

            gramasiLayersContainer
                .querySelectorAll('.gramasi-layer-input')
                .forEach(function (input) {
                    input.addEventListener('input', calculateGramasiTotal);
                });
        }

        if (gramasiTipeSelect) {
            gramasiTipeSelect.addEventListener('change', function () {
                renderGramasiLayers(this.value);
                calculateGramasiTotal();
            });

            if (initialGramasiTipe) {
                renderGramasiLayers(initialGramasiTipe, initialGramasiLayers);
            }
        }

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

        function option(value, current, label = value) {
            return `
                <option
                    value="${escapeHtml(value)}"
                    ${current === value ? 'selected' : ''}
                >
                    ${escapeHtml(label)}
                </option>
            `;
        }

        function radioHtml(name, value = '') {
            const idBase = name
                .replaceAll('[', '_')
                .replaceAll(']', '');

            return `
                <div class="option-radio-group">
                    <label
                        class="option-radio ${value === 'OK' ? 'is-selected' : ''}"
                        for="${idBase}_ok"
                    >
                        <input
                            type="radio"
                            name="${name}"
                            id="${idBase}_ok"
                            value="OK"
                            ${value === 'OK' ? 'checked' : ''}
                            ${isSamplingLocked ? 'disabled' : ''}
                        >
                        <span>OK</span>
                    </label>

                    <label
                        class="option-radio ${value === 'NOK' ? 'is-selected' : ''}"
                        for="${idBase}_nok"
                    >
                        <input
                            type="radio"
                            name="${name}"
                            id="${idBase}_nok"
                            value="NOK"
                            ${value === 'NOK' ? 'checked' : ''}
                            ${isSamplingLocked ? 'disabled' : ''}
                        >
                        <span>NOK</span>
                    </label>
                </div>
            `;
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
                            <td class="text-center fw-semibold">
                                ${index + 1}
                            </td>

                            <td>
                                <input
                                    type="text"
                                    inputmode="decimal"
                                    name="samples[${index}][panjang]"
                                    value="${escapeHtml(sample.panjang)}"
                                    class="form-control form-control-sm"
                                    placeholder="mm"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..*?)\\..*/g, '$1')"
                                >
                            </td>

                            <td>
                                <input
                                    type="text"
                                    inputmode="decimal"
                                    name="samples[${index}][lebar]"
                                    value="${escapeHtml(sample.lebar)}"
                                    class="form-control form-control-sm"
                                    placeholder="mm"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..*?)\\..*/g, '$1')"
                                >
                            </td>

                            <td>
                                <input
                                    type="text"
                                    inputmode="decimal"
                                    name="samples[${index}][tinggi]"
                                    value="${escapeHtml(sample.tinggi)}"
                                    class="form-control form-control-sm"
                                    placeholder="mm"
                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..*?)\\..*/g, '$1')"
                                >
                            </td>

                            <td class="text-center">
                                ${radioHtml(`samples[${index}][design]`, sample.design)}
                            </td>

                            <td class="text-center">
                                ${radioHtml(`samples[${index}][warna]`, sample.warna)}
                            </td>

                            <td class="text-center">
                                ${radioHtml(`samples[${index}][tulisan]`, sample.tulisan)}
                            </td>
                        </tr>
                    `
                );
            }

            rowsContainer
                .querySelectorAll('.option-radio input[type="radio"]')
                .forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        const group = radio.closest('.option-radio-group');
                        group?.querySelectorAll('.option-radio').forEach(function (label) {
                            label.classList.remove('is-selected');
                        });
                        radio.closest('.option-radio')?.classList.add('is-selected');
                    });
                });
        }

        function setupKeyboardNavigation(formElement) {
            if (!formElement) return;

            function isVisibleAndEditable(el) {
                if (!el) return false;
                if (el.disabled || el.readOnly || el.type === 'hidden') return false;
                if (el.offsetParent === null && getComputedStyle(el).display === 'none' && !el.closest('.option-radio')) return false;
                return true;
            }

            function findFocusableInCell(cell) {
                if (!cell) return null;
                const checkedRadio = cell.querySelector('input[type="radio"]:checked');
                if (checkedRadio && isVisibleAndEditable(checkedRadio)) return checkedRadio;

                const firstRadio = cell.querySelector('.option-radio input[type="radio"]');
                if (firstRadio && isVisibleAndEditable(firstRadio)) return firstRadio;

                const input = cell.querySelector('input:not([type="hidden"]), select, textarea');
                if (input && isVisibleAndEditable(input)) return input;

                return null;
            }

            function focusControl(el) {
                if (!el) return;
                el.focus();
                if (typeof el.select === 'function' && el.type !== 'radio' && el.type !== 'checkbox' && el.type !== 'file') {
                    el.select();
                }
            }

            function findNextColumnFirstInput(rows, currentColIndex) {
                if (!rows.length) return null;
                const maxCols = rows[0].cells.length;
                for (let c = currentColIndex + 1; c < maxCols; c++) {
                    for (let r = 0; r < rows.length; r++) {
                        const cell = rows[r].cells[c];
                        if (cell) {
                            const ctrl = findFocusableInCell(cell);
                            if (ctrl) return ctrl;
                        }
                    }
                }
                return null;
            }

            function findPrevColumnLastInput(rows, currentColIndex) {
                if (!rows.length) return null;
                for (let c = currentColIndex - 1; c >= 0; c--) {
                    for (let r = rows.length - 1; r >= 0; r--) {
                        const cell = rows[r].cells[c];
                        if (cell) {
                            const ctrl = findFocusableInCell(cell);
                            if (ctrl) return ctrl;
                        }
                    }
                }
                return null;
            }

            function getFocusableElements(container) {
                const selector = 'input:not([type="hidden"]):not([disabled]):not([readonly]), select:not([disabled]), textarea:not([disabled])';
                const elements = Array.from(container.querySelectorAll(selector));
                return elements.filter(el => isVisibleAndEditable(el));
            }

            formElement.addEventListener('keydown', function (event) {
                const target = event.target;
                if (!target) return;

                if (target.tagName === 'TEXTAREA' && !event.ctrlKey) {
                    if (event.key === 'Tab') {
                        event.preventDefault();
                        const focusables = getFocusableElements(formElement);
                        const currentIndex = focusables.indexOf(target);
                        if (currentIndex !== -1) {
                            const nextIndex = event.shiftKey ? currentIndex - 1 : currentIndex + 1;
                            if (nextIndex >= 0 && nextIndex < focusables.length) {
                                focusControl(focusables[nextIndex]);
                            }
                        }
                    }
                    return;
                }

                if (target.tagName === 'BUTTON' || (target.tagName === 'INPUT' && target.type === 'submit')) {
                    return;
                }

                const isEnter = event.key === 'Enter';
                const isTab = event.key === 'Tab';
                const isArrowDown = event.key === 'ArrowDown';
                const isArrowUp = event.key === 'ArrowUp';

                if (!isEnter && !isTab && !isArrowDown && !isArrowUp) {
                    return;
                }

                const isInTable = target.closest('table') !== null;
                if ((isArrowDown || isArrowUp) && !isInTable) {
                    return;
                }

                if (target.tagName === 'SELECT' && (isArrowDown || isArrowUp)) {
                    return;
                }

                const isBackwards = event.shiftKey || isArrowUp;

                if (isEnter || isTab || isArrowDown || isArrowUp) {
                    event.preventDefault();
                }

                const currentCell = target.closest('td, th');
                const currentRow = target.closest('tr');
                const currentTable = target.closest('table');

                if (currentCell && currentRow && currentTable) {
                    const tbody = currentRow.closest('tbody') || currentTable;
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const rowIndex = rows.indexOf(currentRow);
                    const colIndex = currentCell.cellIndex;

                    if (rowIndex !== -1) {
                        const step = isBackwards ? -1 : 1;
                        const targetRowIndex = rowIndex + step;

                        if (targetRowIndex >= 0 && targetRowIndex < rows.length) {
                            const targetRow = rows[targetRowIndex];
                            const targetCell = targetRow.cells[colIndex];
                            if (targetCell) {
                                const targetInput = findFocusableInCell(targetCell);
                                if (targetInput) {
                                    focusControl(targetInput);
                                    return;
                                }
                            }
                        } else if (!isBackwards && targetRowIndex >= rows.length) {
                            const nextInput = findNextColumnFirstInput(rows, colIndex);
                            if (nextInput) {
                                focusControl(nextInput);
                                return;
                            }
                        } else if (isBackwards && targetRowIndex < 0) {
                            const prevInput = findPrevColumnLastInput(rows, colIndex);
                            if (prevInput) {
                                focusControl(prevInput);
                                return;
                            }
                        }
                    }
                }

                const focusables = getFocusableElements(formElement);
                let currentIndex = -1;

                for (let i = 0; i < focusables.length; i++) {
                    if (focusables[i] === target || (target.type === 'radio' && focusables[i].name === target.name)) {
                        currentIndex = i;
                        break;
                    }
                }
                if (currentIndex === -1) {
                    currentIndex = focusables.indexOf(target);
                }

                if (currentIndex !== -1) {
                    const nextIndex = isBackwards ? currentIndex - 1 : currentIndex + 1;
                    if (nextIndex >= 0 && nextIndex < focusables.length) {
                        focusControl(focusables[nextIndex]);
                        return;
                    }
                }
            });
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

            const tampilkanLainnya =
                ada
                && jenisLainnyaCheckbox.checked;

            jenisLainnyaWrapper.classList.toggle(
                'd-none',
                !tampilkanLainnya
            );

            jenisLainnyaInput.disabled =
                !tampilkanLainnya;

            jenisLainnyaInput.required =
                tampilkanLainnya;

            if (!tampilkanLainnya) {
                jenisLainnyaInput.value = '';
            }
        }

        konfirmasiSelect.addEventListener(
            'change',
            updateKetidaksesuaian
        );

        document
            .querySelectorAll(
                '.jenis-ketidaksesuaian'
            )
            .forEach(function (checkbox) {
                checkbox.addEventListener(
                    'change',
                    function () {
                        if (
                            checkbox.checked
                            && konfirmasiSelect.value
                                !== 'Ada'
                        ) {
                            konfirmasiSelect.value =
                                'Ada';
                        }

                        updateKetidaksesuaian();
                    }
                );
            });

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

                if (isSamplingLocked) {
                    showAlert(
                        'warning',
                        'Data sudah final. Koreksi hanya dapat dilakukan oleh Foreman.'
                    );
                    return;
                }

                const saveMode =
                    event.submitter?.value ?? 'final';

                const isFinal =
                    saveMode === 'final';

                if (
                    isFinal
                    && ! form.reportValidity()
                ) {
                    return;
                }

                if (
                    isFinal
                    && konfirmasiSelect.value === 'Ada'
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
                    isFinal
                    && konfirmasiSelect.value === 'Ada'
                    && jenisLainnyaCheckbox.checked
                    && jenisLainnyaInput.value.trim() === ''
                ) {
                    showAlert(
                        'danger',
                        'Jenis ketidaksesuaian lainnya wajib diisi.'
                    );

                    jenisLainnyaInput.focus();

                    return;
                }

                if (
                    isFinal
                    && existingFotoCount === 0
                    && fotoInput.files.length === 0
                ) {
                    showAlert(
                        'danger',
                        'Foto pengecekan wajib diunggah.'
                    );

                    return;
                }

                if (
                    isFinal
                    && konfirmasiSelect.value === 'Ada'
                    && existingFotoKetidaksesuaianCount === 0
                    && fotoKetidaksesuaianInput.files.length === 0
                ) {
                    showAlert(
                        'danger',
                        'Foto ketidaksesuaian wajib diunggah.'
                    );

                    return;
                }

                document
                    .querySelectorAll('.save-button')
                    .forEach(function (button) {
                        button.disabled = true;
                    });

                const clickedButton =
                    event.submitter;

                if (clickedButton) {
                    clickedButton.innerHTML = `
                        <span
                            class="spinner-border
                                   spinner-border-sm me-1"
                        ></span>
                        Menyimpan...
                    `;
                }

                const formData =
                    new FormData(form);

                formData.set(
                    'save_mode',
                    saveMode
                );

                const csrfToken =
                    form.querySelector(
                        'input[name="_token"]'
                    )?.value ?? '';

                try {
                    const response =
                        await fetch(
                            form.action,
                            {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    Accept:
                                        'application/json',
                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                    'X-CSRF-TOKEN':
                                        csrfToken
                                },
                                body:
                                    formData
                            }
                        );

                    const contentType =
                        response.headers.get('content-type') ?? '';

                    if (!contentType.includes('application/json')) {
                        const body = await response.text();

                        throw new Error(
                            'Server tidak mengembalikan JSON. ' +
                            body.slice(0, 150)
                        );
                    }

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
                            ${
                                isFinalRecord && isForeman
                                    ? 'Simpan Koreksi'
                                    : 'Simpan Final'
                            }
                        `;
                    }
                }
            }
        );

        buildRows(
            jumlahInput.value
        );

        updateKetidaksesuaian();

        setupKeyboardNavigation(form);
    }
);
</script>

@endsection
