@extends('layouts.component.main')

@section('title')
    Sampling Pouch
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0">Sampling Pouch</h4>
                        <p class="text-muted mb-0 mt-1">Rincian pemeriksaan Pouch berdasarkan nomor SPB.</p>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.pm') }}">Packaging Material</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('rmpm.pm.pouch') }}">Pouch</a></li>
                            <li class="breadcrumb-item active">Sampling</li>
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

        <form
            id="pouchSamplingForm"
            method="POST"
            action="{{ route(
                'rmpm.pm.pouch.sampling.store',
                $packagingIncoming
            ) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="card border-0 shadow-sm sampling-card">
                <div class="sampling-header">
                    <div>
                        <span class="sampling-label">PACKAGING ONLINE</span>
                        <h2 class="mb-1">POUCH</h2>
                        <p class="mb-0">Pemeriksaan ukuran, seal, ketebalan, berat, dan visual Pouch.</p>
                    </div>
                    <div class="sampling-header-icon"><i class="mdi mdi-clipboard-check-outline"></i></div>
                </div>

                <div class="card-body p-4">
                    <div class="section-title"><i class="mdi mdi-information-outline"></i> Identitas Incoming</div>

                    <div class="row g-3 mb-4">
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Nomor SPB</label>
                            <input type="text" class="form-control" value="{{ $packagingIncoming->no_spb ?? '-' }}" readonly>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Nama Supplier</label>
                            <input type="text" class="form-control" value="{{ $packagingIncoming->supplier?->nama ?? $packagingIncoming->supplier?->nama_supplier ?? '-' }}" readonly>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Nomor Mobil</label>
                            <input type="text" class="form-control" value="{{ $packagingIncoming->no_mobil ?? '-' }}" readonly>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Nama Item</label>
                            <input type="text" class="form-control" value="{{ $packagingIncoming->jenisMaterial?->nama ?? '-' }}" readonly>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Qty</label>
                            <input type="number" id="qty" name="qty" class="form-control" min="0" placeholder="Masukkan qty" value="{{ old('qty', $sampling?->qty) }}">
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">UOM</label>
                            <input type="text" id="uom" name="uom" class="form-control" value="{{ old('uom', $sampling?->uom ?? 'Pcs') }}">
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label for="jumlah_sampel" class="form-label">Jumlah Sampel</label>
                            <input type="number" id="jumlah_sampel" name="jumlah_sampel" class="form-control" min="1" max="50" value="{{ old('jumlah_sampel', $sampling?->jumlah_sampel ?? 4) }}" required>
                        </div>
                    </div>

                    <div class="section-title"><i class="mdi mdi-flask-outline"></i> Hasil Pemeriksaan Sampel</div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle inspection-table">
                            <thead>
                                <tr>
                                    <th>No. Sampel</th>
                                    <th>Panjang (mm)</th>
                                    <th>Lebar (mm)</th>
                                    <th>Berat (g)</th>
                                    <th>Side Seal 1 (mm)</th>
                                    <th>Side Seal 2 (mm)</th>
                                    <th>Bottom Seal (mm)</th>
                                    <th>Bottom High (mm)</th>
                                    <th>Design</th>
                                    <th>Warna</th>
                                    <th>Tulisan</th>
                                    <th>Barcode & QR Code</th>
                                    <th>Drop Test</th>
                                    <th>Pretest</th>
                                </tr>
                            </thead>
                            <tbody id="sampleRows"></tbody>
                        </table>
                    </div>

                    <div class="section-title mt-4">
                        <i class="mdi mdi-ruler"></i>
                        Pemeriksaan Thickness
                    </div>

                    <div class="inspection-note mb-3">
                        <small class="text-muted d-block">
                            Tabel Thickness memiliki 3 baris tetap dan 2 kolom input.
                            Jumlah baris tidak mengikuti Jumlah Sampel.
                        </small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle thickness-table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">No.</th>
                                    <th>Thickness 1 (mikron)</th>
                                    <th>Thickness 2 (mikron)</th>
                                </tr>
                            </thead>

                            <tbody>
                                @for ($index = 0; $index < 3; $index++)
                                    @php
                                        $thicknessRow =
                                            $sampling?->hasil_thickness[$index]
                                            ?? [];
                                    @endphp

                                    <tr>
                                        <td class="text-center fw-semibold">
                                            {{ $index + 1 }}
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="thickness[{{ $index }}][nilai_1]"
                                                class="form-control thickness-input"
                                                value="{{ old(
                                                    'thickness.' . $index . '.nilai_1',
                                                    $thicknessRow['nilai_1'] ?? ''
                                                ) }}"
                                            >
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="thickness[{{ $index }}][nilai_2]"
                                                class="form-control thickness-input"
                                                value="{{ old(
                                                    'thickness.' . $index . '.nilai_2',
                                                    $thicknessRow['nilai_2'] ?? ''
                                                ) }}"
                                            >
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-xl-6 col-md-8">
                            <label
                                for="foto_pengecekan"
                                class="form-label"
                            >
                                Foto Pengecekan
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="file"
                                name="foto_pengecekan"
                                id="foto_pengecekan"
                                class="form-control"
                                accept="image/*"
                                @required(! $sampling?->foto_pengecekan)
                            >

                            @if ($sampling?->foto_pengecekan)
                                <div class="existing-photo-card mt-3">
                                    <div class="existing-photo-label">
                                        Foto Pengecekan Tersimpan
                                    </div>

                                    <a
                                        href="{{ asset('storage/' . $sampling->foto_pengecekan) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="existing-photo-link"
                                    >
                                        <img
                                            src="{{ asset('storage/' . $sampling->foto_pengecekan) }}"
                                            alt="Foto Pengecekan"
                                            class="existing-photo-thumb"
                                        >
                                    </a>

                                    <small class="text-success d-block mt-2">
                                        Klik foto untuk membuka ukuran penuh.
                                    </small>
                                </div>
                            @else
                                <small class="text-muted d-block mt-1">
                                    Wajib untuk Simpan Final. Tidak wajib untuk Simpan Sementara.
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="inspection-note mt-3">
                        <small class="text-muted d-block">
                            Foto Pengecekan digunakan sebagai bukti proses pemeriksaan Pouch.
                        </small>
                    </div>

                    <div class="section-title mt-4"><i class="mdi mdi-clipboard-check-outline"></i> Kesimpulan Pemeriksaan</div>
                    <div class="row g-3">
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">CoA</label>
                            <select id="coa" name="coa" class="form-select">
                                <option value="">Pilih CoA</option>
                                <option value="Ada" @selected($sampling?->coa === 'Ada')>Ada</option>
                                <option value="Tidak Ada" @selected($sampling?->coa === 'Tidak Ada')>Tidak Ada</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Rekomendasi</label>
                            <select id="rekomendasi" name="rekomendasi" class="form-select" required>
                                <option value="">Pilih rekomendasi</option>
                                <option value="Diterima" @selected($sampling?->rekomendasi === 'Diterima')>Diterima</option>
                                <option value="Diterima Bersyarat" @selected($sampling?->rekomendasi === 'Diterima Bersyarat')>Diterima Bersyarat</option>
                                <option value="Ditolak" @selected($sampling?->rekomendasi === 'Ditolak')>Ditolak</option>
                                <option value="WIP" @selected($sampling?->rekomendasi === 'WIP')>WIP</option>
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label">Konfirmasi Ketidaksesuaian</label>
                            <select id="konfirmasi_ketidaksesuaian" name="konfirmasi_ketidaksesuaian" class="form-select">
                                <option value="">Pilih konfirmasi</option>
                                <option value="Ada" @selected($sampling?->konfirmasi_ketidaksesuaian === 'Ada')>Ada</option>
                                <option value="Tidak Ada" @selected($sampling?->konfirmasi_ketidaksesuaian === 'Tidak Ada')>Tidak Ada</option>
                            </select>
                        </div>
                        <div class="col-xl-6 col-md-12">
                            <label class="form-label d-block">
                                Jenis Ketidaksesuaian
                            </label>

                            @php
                                $selectedNonconformities = old(
                                    'jenis_ketidaksesuaian',
                                    $sampling?->jenis_ketidaksesuaian ?? []
                                );

                                $selectedNonconformities = is_array(
                                    $selectedNonconformities
                                )
                                    ? $selectedNonconformities
                                    : [];

                                $nonconformityOptions = [
                                    'Miss Print',
                                    'Ukuran Tidak Standar',
                                    'Seal Tidak Standar',
                                    'Thickness Tidak Standar',
                                    'Barcode Tidak Terbaca',
                                ];
                            @endphp

                            <div
                                id="jenis_ketidaksesuaian"
                                class="nonconformity-options"
                            >
                                @foreach ($nonconformityOptions as $option)
                                    <label class="nonconformity-option">
                                        <input
                                            type="checkbox"
                                            name="jenis_ketidaksesuaian[]"
                                            value="{{ $option }}"
                                            class="form-check-input nonconformity-checkbox"
                                            @checked(in_array($option, $selectedNonconformities, true))
                                        >

                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <small class="text-muted d-block mt-1">
                                Bisa memilih lebih dari satu.
                            </small>
                        </div>
                        <div class="col-xl-6 col-md-12">
                            <label class="form-label">
                                Foto Ketidaksesuaian
                                <span
                                    id="fotoRequiredMark"
                                    class="text-danger d-none"
                                >
                                    *
                                </span>
                            </label>

                            <div class="d-flex gap-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary"
                                    id="btnCameraFoto"
                                >
                                    <i class="mdi mdi-camera-outline me-1"></i>
                                    Gunakan Kamera
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    id="btnFileFoto"
                                >
                                    <i class="mdi mdi-folder-image me-1"></i>
                                    Pilih File
                                </button>
                            </div>

                            <!-- Modal Kamera Langsung -->
                            <div class="modal fade" id="cameraModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                Foto Ketidaksesuaian
                                            </h5>
                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal">
                                            </button>
                                        </div>

                                        <div class="modal-body text-center">
                                            <video
                                                id="cameraPreview"
                                                autoplay
                                                playsinline
                                                style="width:100%;border-radius:10px;">
                                            </video>

                                            <canvas
                                                id="cameraCanvas"
                                                hidden>
                                            </canvas>
                                        </div>

                                        <div class="modal-footer">
                                            <button
                                                type="button"
                                                class="btn btn-primary"
                                                id="takePhoto">
                                                <i class="mdi mdi-camera me-1"></i>
                                                Ambil Foto
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input
                                type="file"
                                id="foto"
                                name="foto[]"
                                class="form-control mt-2"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                            >

                            <small id="fotoHelp" class="text-muted d-block mt-1">
                                Tidak wajib apabila tidak terdapat ketidaksesuaian.
                                Maksimal 10 foto, masing-masing maksimal 5 MB.
                            </small>

                            @php
                                $existingNonconformityPhotos =
                                    $sampling?->foto_ketidaksesuaian ?? [];

                                $existingNonconformityPhotos = is_array(
                                    $existingNonconformityPhotos
                                )
                                    ? array_values(array_filter($existingNonconformityPhotos))
                                    : [];
                            @endphp

                            <div
                                id="existingPhotoInfo"
                                class="mt-3 {{ count($existingNonconformityPhotos) > 0 ? '' : 'd-none' }}"
                                data-existing-count="{{ count($existingNonconformityPhotos) }}"
                            >
                                @if (count($existingNonconformityPhotos) > 0)
                                    <div class="existing-photo-label mb-2">
                                        Foto Ketidaksesuaian Tersimpan
                                        <span class="badge bg-success-subtle text-success ms-1">
                                            {{ count($existingNonconformityPhotos) }} foto
                                        </span>
                                    </div>

                                    <div class="existing-photo-grid">
                                        @foreach ($existingNonconformityPhotos as $photoIndex => $photoPath)
                                            <a
                                                href="{{ asset('storage/' . $photoPath) }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="existing-photo-link"
                                                title="Buka foto {{ $photoIndex + 1 }}"
                                            >
                                                <img
                                                    src="{{ asset('storage/' . $photoPath) }}"
                                                    alt="Foto Ketidaksesuaian {{ $photoIndex + 1 }}"
                                                    class="existing-photo-thumb"
                                                >

                                                <span class="existing-photo-number">
                                                    {{ $photoIndex + 1 }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>

                                    <small class="text-muted d-block mt-2">
                                        Klik foto untuk membuka ukuran penuh.
                                    </small>
                                @endif
                            </div>

                            <div id="selectedPhotoInfo" class="mt-2"></div>
                        </div>
                        <div class="col-xl-6 col-md-12">
                            <label class="form-label">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" class="form-control" rows="4" placeholder="Masukkan keterangan">{{ old('keterangan', $sampling?->keterangan) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('rmpm.pm.pouch') }}" class="btn btn-light">
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

@endsection

@section('styles')
<style>
.sampling-card{overflow:hidden;border-radius:18px}.sampling-header{display:flex;align-items:center;justify-content:space-between;padding:24px 28px;border-bottom:4px solid #ef4444;background:linear-gradient(135deg,#ecfeff,#f8fafc)}.sampling-label{display:block;margin-bottom:5px;color:#0f766e;font-size:12px;font-weight:700;letter-spacing:1px}.sampling-header-icon{width:74px;height:74px;display:flex;align-items:center;justify-content:center;border-radius:18px;background:#fff;color:#14b8a6;font-size:42px;box-shadow:0 8px 20px rgba(15,23,42,.08)}.section-title{display:flex;align-items:center;gap:8px;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid #e2e8f0;color:#334155;font-weight:700}.inspection-table thead th{min-width:135px;background:#f1f5f9;text-align:center;vertical-align:middle;white-space:nowrap}.inspection-table tbody td{vertical-align:middle}.inspection-table input,.inspection-table select{min-width:110px}.sample-number{min-width:80px!important;text-align:center;font-weight:700}

.inspection-note{
    padding:12px 14px;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#f8fafc;
}

.thickness-table thead th{
    background:#f1f5f9;
    text-align:center;
    vertical-align:middle;
    white-space:nowrap;
}

.thickness-table input{
    min-width:140px;
}



.nonconformity-options{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:10px;
    padding:12px;
    border:1px solid #dbe3ec;
    border-radius:10px;
    background:#f8fafc;
}

.nonconformity-option{
    display:flex;
    align-items:center;
    gap:8px;
    margin:0;
    padding:10px 12px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:#fff;
    cursor:pointer;
}

.nonconformity-option:hover{
    border-color:#6366f1;
}

.existing-photo-card{
    padding:12px;
    border:1px solid #dbe3ec;
    border-radius:12px;
    background:#f8fafc;
}

.existing-photo-label{
    color:#334155;
    font-weight:700;
}

.existing-photo-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(110px,1fr));
    gap:10px;
}

.existing-photo-link{
    position:relative;
    display:block;
    overflow:hidden;
    border:1px solid #dbe3ec;
    border-radius:10px;
    background:#fff;
}

.existing-photo-thumb{
    width:100%;
    height:110px;
    display:block;
    object-fit:cover;
    transition:transform .2s ease;
}

.existing-photo-link:hover .existing-photo-thumb{
    transform:scale(1.04);
}

.existing-photo-number{
    position:absolute;
    right:7px;
    bottom:7px;
    min-width:24px;
    height:24px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:999px;
    background:rgba(15,23,42,.78);
    color:#fff;
    font-size:12px;
    font-weight:700;
}

@media (max-width:575.98px){
    .nonconformity-options{
        grid-template-columns:1fr;
    }

    .existing-photo-grid{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}

</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('pouchSamplingForm');
    const jumlahInput = document.getElementById('jumlah_sampel');
    const rowsContainer = document.getElementById('sampleRows');
    const alertContainer = document.getElementById('alertContainer');
    const konfirmasiSelect = document.getElementById('konfirmasi_ketidaksesuaian');
    const jenisCheckboxes = Array.from(
        document.querySelectorAll('.nonconformity-checkbox')
    );
    const fotoInput = document.getElementById('foto');
    const fotoRequiredMark = document.getElementById('fotoRequiredMark');
    const fotoHelp = document.getElementById('fotoHelp');
    const existingPhotoInfo = document.getElementById('existingPhotoInfo');
    const selectedPhotoInfo = document.getElementById('selectedPhotoInfo');

    let samples = @json($sampling?->hasil_sampel ?? []);
    samples = Array.isArray(samples) ? samples : [];

    function esc(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function selectHtml(name, value) {
        return `
            <select name="${name}" class="form-select form-select-sm">
                <option value="">Pilih</option>
                <option value="OK" ${value === 'OK' ? 'selected' : ''}>OK</option>
                <option value="NOK" ${value === 'NOK' ? 'selected' : ''}>NOK</option>
            </select>
        `;
    }

    function inputHtml(index, field, value) {
        return `
            <input
                type="number"
                step="0.01"
                min="0"
                name="samples[${index}][${field}]"
                value="${esc(value)}"
                class="form-control form-control-sm"
            >
        `;
    }

    function renderRows(total) {
        const safeTotal = Math.max(
            1,
            Math.min(parseInt(total || 1), 50)
        );

        while (samples.length < safeTotal) {
            samples.push({});
        }

        samples = samples.slice(0, safeTotal);
        rowsContainer.innerHTML = '';

        const fieldOrder = [
            'panjang',
            'lebar',
            'berat',
            'side_seal_1',
            'side_seal_2',
            'bottom_seal',
            'bottom_high',
            'design',
            'warna',
            'tulisan',
            'barcode_qr',
            'drop_test',
            'pretest'
        ];

        for (let index = 0; index < safeTotal; index++) {
            const sample = samples[index] ?? {};

            rowsContainer.insertAdjacentHTML(
                'beforeend',
                `<tr>
                    <td>
                        <input
                            type="text"
                            class="form-control form-control-sm sample-number"
                            value="${index + 1}"
                            readonly
                            tabindex="-1"
                        >
                    </td>
                    <td>${inputHtml(index, 'panjang', sample.panjang)}</td>
                    <td>${inputHtml(index, 'lebar', sample.lebar)}</td>
                    <td>${inputHtml(index, 'berat', sample.berat)}</td>
                    <td>${inputHtml(index, 'side_seal_1', sample.side_seal_1)}</td>
                    <td>${inputHtml(index, 'side_seal_2', sample.side_seal_2)}</td>
                    <td>${inputHtml(index, 'bottom_seal', sample.bottom_seal)}</td>
                    <td>${inputHtml(index, 'bottom_high', sample.bottom_high)}</td>
                    <td>${selectHtml(`samples[${index}][design]`, sample.design)}</td>
                    <td>${selectHtml(`samples[${index}][warna]`, sample.warna)}</td>
                    <td>${selectHtml(`samples[${index}][tulisan]`, sample.tulisan)}</td>
                    <td>${selectHtml(`samples[${index}][barcode_qr]`, sample.barcode_qr)}</td>
                    <td>${selectHtml(`samples[${index}][drop_test]`, sample.drop_test)}</td>
                    <td>${selectHtml(`samples[${index}][pretest]`, sample.pretest)}</td>
                </tr>`
            );
        }

        fieldOrder.forEach(function (field, fieldIndex) {
            rowsContainer
                .querySelectorAll(`[name$="[${field}]"]`)
                .forEach(function (element, rowIndex) {
                    element.tabIndex =
                        1 + (fieldIndex * safeTotal) + rowIndex;
                });
        });

        const sampleTabCount =
            fieldOrder.length * safeTotal;

        for (let columnIndex = 0; columnIndex < 2; columnIndex++) {
            for (let rowIndex = 0; rowIndex < 3; rowIndex++) {
                const element = document.querySelector(
                    `[name="thickness[${rowIndex}][nilai_${columnIndex + 1}]"]`
                );

                if (element) {
                    element.tabIndex =
                        sampleTabCount
                        + 1
                        + (columnIndex * 3)
                        + rowIndex;
                }
            }
        }

    }

    function showAlert(type, message) {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${esc(message)}
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

    function getExistingPhotoCount() {
        return Number(
            existingPhotoInfo?.dataset.existingCount
            ?? 0
        );
    }

    function updateSelectedPhotoInfo() {
        const selectedCount =
            fotoInput.files?.length ?? 0;

        const totalCount =
            getExistingPhotoCount()
            + selectedCount;

        if (selectedCount === 0) {
            selectedPhotoInfo.innerHTML = '';
            return;
        }

        const badgeClass =
            totalCount > 10
                ? 'bg-danger-subtle text-danger'
                : 'bg-primary-subtle text-primary';

        selectedPhotoInfo.innerHTML = `
            <span class="badge ${badgeClass}">
                ${selectedCount} foto baru dipilih — total ${totalCount}/10
            </span>
        `;

        if (totalCount > 10) {
            fotoInput.value = '';

            selectedPhotoInfo.innerHTML = `
                <span class="badge bg-danger-subtle text-danger">
                    Total foto tidak boleh lebih dari 10.
                </span>
            `;
        }
    }

    function updateKetidaksesuaianFields() {
        const ada =
            konfirmasiSelect.value === 'Ada';

        fotoRequiredMark.classList.toggle(
            'd-none',
            !ada
        );

        const tidakAda =
            konfirmasiSelect.value === 'Tidak Ada';

        jenisCheckboxes.forEach(
            function (checkbox) {
                checkbox.required = false;
                checkbox.disabled = tidakAda;

                if (tidakAda) {
                    checkbox.checked = false;
                }
            }
        );

        if (ada) {
            fotoInput.required =
                getExistingPhotoCount() === 0;

            fotoHelp.textContent =
                'Pilih maksimal 10 foto termasuk foto yang sudah tersimpan. Maksimal 5 MB per foto.';

            return;
        }

        fotoInput.required = false;
        fotoInput.value = '';
        selectedPhotoInfo.innerHTML = '';

        fotoHelp.textContent =
            'Tidak wajib apabila tidak terdapat ketidaksesuaian. Maksimal 10 foto.';
    }

    const btnCameraFoto = document.getElementById('btnCameraFoto');
    const btnFileFoto = document.getElementById('btnFileFoto');

    const cameraModalElement =
        document.getElementById('cameraModal');

    const cameraModal =
        new bootstrap.Modal(cameraModalElement);

    const cameraPreview =
        document.getElementById('cameraPreview');

    const cameraCanvas =
        document.getElementById('cameraCanvas');

    const takePhoto =
        document.getElementById('takePhoto');

    let cameraStream = null;

    btnFileFoto.addEventListener('click', function () {
        fotoInput.click();
    });


    btnCameraFoto.addEventListener(
        'click',
        async function () {

            cameraModal.show();

            try {

                cameraStream =
                    await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: {
                                ideal: 'environment'
                            }
                        },
                        audio: false
                    });


                cameraPreview.srcObject =
                    cameraStream;

            } catch(error) {

                alert(
                    'Kamera tidak dapat dibuka. Pastikan izin kamera aktif.'
                );

                cameraModal.hide();
            }

        }
    );


    takePhoto.addEventListener(
        'click',
        function () {

            cameraCanvas.width =
                cameraPreview.videoWidth;

            cameraCanvas.height =
                cameraPreview.videoHeight;


            cameraCanvas
                .getContext('2d')
                .drawImage(
                    cameraPreview,
                    0,
                    0
                );


            cameraCanvas.toBlob(
                function(blob){

                    const file =
                        new File(
                            [
                                blob
                            ],
                            'foto-ketidaksesuaian.jpg',
                            {
                                type:'image/jpeg'
                            }
                        );


                    const dataTransfer =
                        new DataTransfer();


                    Array.from(
                        fotoInput.files ?? []
                    )
                    .forEach(
                        file =>
                            dataTransfer.items.add(file)
                    );


                    dataTransfer.items.add(file);


                    fotoInput.files =
                        dataTransfer.files;


                    updateSelectedPhotoInfo();

                    stopCamera();

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
            stopCamera();
        }
    );


    function stopCamera(){

        if(cameraStream){

            cameraStream
                .getTracks()
                .forEach(
                    track => track.stop()
                );

            cameraStream = null;
        }
    }

    fotoInput.addEventListener(
        'change',
        updateSelectedPhotoInfo
    );

    jumlahInput.addEventListener(
        'input',
        function () {
            renderRows(this.value);
        }
    );

    konfirmasiSelect.addEventListener(
        'change',
        updateKetidaksesuaianFields
    );

    jenisCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (
                checkbox.checked
                && konfirmasiSelect.value !== 'Ada'
            ) {
                konfirmasiSelect.value = 'Ada';
                updateKetidaksesuaianFields();
            }
        });
    });

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
                && konfirmasiSelect.value === 'Ada'
                && !jenisCheckboxes.some(
                    checkbox => checkbox.checked
                )
            ) {
                showAlert(
                    'danger',
                    'Pilih minimal satu jenis ketidaksesuaian.'
                );

                document
                    .getElementById('jenis_ketidaksesuaian')
                    ?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                return;
            }

            if (isFinal && !form.reportValidity()) {
                return;
            }

            const clickedButton =
                event.submitter;

            document
                .querySelectorAll('.save-button')
                .forEach(function (button) {
                    button.disabled = true;
                });

            if (clickedButton) {
                clickedButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Menyimpan...
                `;
            }

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
                        ?? 'Data sampling gagal disimpan.'
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
                    700
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
                        Simpan Final
                    `;
                }
            }
        }
    );

    renderRows(jumlahInput.value);
    updateKetidaksesuaianFields();
});
</script>
@endsection