@extends('layouts.component.main')

@section('title')
    Pemeriksaan BCT
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0">Pemeriksaan BCT</h4>
                        <p class="text-muted mb-0 mt-1">
                            Input manual hasil Box Compression Test Karton.
                        </p>
                    </div>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.pm.karton') }}">Karton</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route(
                                            'rmpm.pm.karton.display',
                                            $packagingIncoming
                                        ) }}">
                                            Display Karton
                                        </a>
                            </li>
                            <li class="breadcrumb-item active">BCT</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div id="alertContainer" class="mb-3"></div>

        <div class="card border-0 shadow-sm bct-card">
            <div class="bct-header">
                <div>
                    <span class="bct-label">PACKAGING ONLINE</span>
                    <h2 class="mb-1">BCT</h2>
                    <p class="mb-0">
                        Pemeriksaan kekuatan tekan Karton dengan input data manual.
                    </p>
                </div>

                <div class="bct-header-icon">
                    <i class="mdi mdi-package-variant-closed-check"></i>
                </div>
            </div>

            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                    <div>
                        <h5 class="mb-1">Input Hasil BCT</h5>
                        <p class="text-muted mb-0">
                            Resampling dapat dilakukan sampai 2 kali,
                            sehingga total data BCT maksimal 20 pcs.
                        </p>
                    </div>

                    <div class="d-flex gap-2">
                        <button
                            type="button"
                            id="saveButton"
                            class="btn btn-primary"
                        >
                            <i class="mdi mdi-content-save-outline me-1"></i>
                            Simpan
                        </button>

                        <button
                            type="button"
                            id="resamplingButton"
                            class="btn btn-warning"
                        >
                            <i class="mdi mdi-refresh me-1"></i>
                            Resampling
                        </button>

                        <a
                            href="{{ route('rmpm.pm.karton.display', $packagingIncoming) }}"
                            class="btn btn-light"
                        >
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="row g-4">

                    <div class="col-xl-4">
                        <div class="result-panel">
                            <div class="result-panel-title">
                                Hasil BCT kgf
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>BCT kgf 1</th>
                                            <th>BCT kgf 2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input
                                                    type="number"
                                                    id="bct4gt1"
                                                    class="form-control text-center"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="0.00"
                                                >
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    id="bct4gt2"
                                                    class="form-control text-center"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="0.00"
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <small class="text-muted d-block mt-2">
                                Nilai awal diisi manual.
                            </small>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="info-panel">
                            <div class="info-icon">
                                <i class="mdi mdi-information-outline"></i>
                            </div>
                            <div>
                                <strong>Aturan Resampling</strong>
                                <p class="mb-0 text-muted">
                                    Klik tombol Resampling untuk menambahkan
                                    kelompok pemeriksaan berikutnya.
                                    Setiap kelompok berisi 10 data BCT.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Hasil BCT</h5>
                            <p class="text-muted mb-0">
                                Isi nilai BCT secara manual pada tabel berikut.
                            </p>
                        </div>

                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                            Resampling ke-<span id="resamplingCount">1</span>
                        </span>
                    </div>

                    <div id="bctTables"></div>
                </div>

                <div class="mt-4">
                    <div class="summary-card">
                        <div>
                            <span class="summary-label">Rata-rata BCT</span>
                            <h3 class="mb-0" id="averageValue">0.00</h3>
                        </div>

                        <div>
                            <span class="summary-label">Jumlah Data Terisi</span>
                            <h3 class="mb-0">
                                <span id="filledCount">0</span>
                                /
                                <span id="totalCount">10</span>
                            </h3>
                        </div>

                        <div>
                            <span class="summary-label">Status</span>
                            <h4 class="mb-0" id="statusText">
                                Belum Lengkap
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning border-0 mt-3 mb-0">
                    <i class="mdi mdi-alert-outline me-1"></i>
                    Jika nilai BCT di bawah 110, hasil akan diberi tanda peringatan.
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@section('styles')
<style>
    .bct-card {
        overflow: hidden;
        border-radius: 18px;
    }

    .bct-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 24px 28px;
        border-bottom: 4px solid #ef4444;
        background: linear-gradient(135deg, #eef2ff, #f8fafc);
    }

    .bct-label {
        display: block;
        margin-bottom: 5px;
        color: #4f46e5;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .bct-header-icon {
        width: 74px;
        height: 74px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #ffffff;
        color: #f97316;
        font-size: 42px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .result-panel,
    .info-panel {
        height: 100%;
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
    }

    .result-panel-title {
        padding: 10px 14px;
        margin: -18px -18px 16px;
        border-radius: 14px 14px 0 0;
        background: #a7d08c;
        text-align: center;
        font-weight: 700;
    }

    .result-panel table thead th,
    .bct-result-table thead th {
        background: #a7d08c;
        text-align: center;
        vertical-align: middle;
    }

    .info-panel {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        background: #f8fafc;
    }

    .info-icon {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 26px;
    }

    .bct-result-wrapper {
        margin-bottom: 20px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
    }

    .bct-result-title {
        margin-bottom: 12px;
        color: #334155;
        font-weight: 700;
    }

    .bct-result-table input {
        min-width: 100px;
        text-align: center;
    }

    .bct-warning {
        background: #fff1f2 !important;
        border-color: #ef4444 !important;
        color: #b91c1c !important;
        font-weight: 700;
    }

    .summary-card {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        padding: 18px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .summary-card > div {
        padding: 14px;
        border-radius: 12px;
        background: #ffffff;
        text-align: center;
    }

    .summary-label {
        display: block;
        margin-bottom: 6px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    @media (max-width: 767.98px) {
        .bct-header {
            align-items: flex-start;
        }

        .summary-card {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const storageKey = 'karton_bct_manual_data_{{ $packagingIncoming->id }}';
        const maximumResampling = 2;
        const itemsPerSampling = 10;

        const bctTables = document.getElementById('bctTables');
        const saveButton = document.getElementById('saveButton');
        const resamplingButton = document.getElementById('resamplingButton');
        const resamplingCountElement = document.getElementById('resamplingCount');
        const averageValue = document.getElementById('averageValue');
        const filledCount = document.getElementById('filledCount');
        const totalCount = document.getElementById('totalCount');
        const statusText = document.getElementById('statusText');
        const alertContainer = document.getElementById('alertContainer');

        let state = {
            resamplingCount: 1,
            bct4gt1: '',
            bct4gt2: '',
            values: Array(itemsPerSampling).fill(''),
        };

        function loadState() {
            const saved = localStorage.getItem(storageKey);

            if (!saved) {
                return;
            }

            try {
                const parsed = JSON.parse(saved);

                state.resamplingCount = Math.min(
                    Math.max(parseInt(parsed.resamplingCount || 1), 1),
                    maximumResampling
                );

                state.bct4gt1 = parsed.bct4gt1 ?? '';
                state.bct4gt2 = parsed.bct4gt2 ?? '';

                const expectedLength =
                    state.resamplingCount * itemsPerSampling;

                state.values = Array.isArray(parsed.values)
                    ? parsed.values.slice(0, expectedLength)
                    : [];

                while (state.values.length < expectedLength) {
                    state.values.push('');
                }
            } catch (error) {
                localStorage.removeItem(storageKey);
            }
        }

        function showAlert(type, message) {
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show">
                    ${message}
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>
                </div>
            `;

            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderTables() {
            bctTables.innerHTML = '';

            for (
                let samplingIndex = 0;
                samplingIndex < state.resamplingCount;
                samplingIndex++
            ) {
                const startIndex = samplingIndex * itemsPerSampling;

                let headers = '';
                let inputs = '';

                for (
                    let itemIndex = 0;
                    itemIndex < itemsPerSampling;
                    itemIndex++
                ) {
                    const globalIndex = startIndex + itemIndex;
                    const number = globalIndex + 1;
                    const value = state.values[globalIndex] ?? '';

                    headers += `
                        <th>BCT kgf ${number}</th>
                    `;

                    inputs += `
                        <td>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control bct-input"
                                data-index="${globalIndex}"
                                value="${escapeHtml(value)}"
                                placeholder="0.00"
                            >
                        </td>
                    `;
                }

                bctTables.insertAdjacentHTML(
                    'beforeend',
                    `
                        <div class="bct-result-wrapper">
                            <div class="bct-result-title">
                                Hasil BCT - Pengujian ${samplingIndex + 1}
                            </div>

                            <div class="table-responsive">
                                <table
                                    class="table table-bordered align-middle
                                           bct-result-table mb-0"
                                >
                                    <thead>
                                        <tr>${headers}</tr>
                                    </thead>
                                    <tbody>
                                        <tr>${inputs}</tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `
                );
            }

            document.getElementById('bct4gt1').value =
                state.bct4gt1;

            document.getElementById('bct4gt2').value =
                state.bct4gt2;

            resamplingCountElement.textContent =
                state.resamplingCount;

            totalCount.textContent =
                state.resamplingCount * itemsPerSampling;

            resamplingButton.disabled =
                state.resamplingCount >= maximumResampling;

            updateSummary();
        }

        function updateSummary() {
            const numericValues = state.values
                .map(function (value) {
                    return parseFloat(value);
                })
                .filter(function (value) {
                    return Number.isFinite(value);
                });

            const total = numericValues.length;

            const average = total > 0
                ? numericValues.reduce(function (sum, value) {
                    return sum + value;
                }, 0) / total
                : 0;

            filledCount.textContent = total;
            averageValue.textContent = average.toFixed(2);

            const expectedTotal =
                state.resamplingCount * itemsPerSampling;

            if (total < expectedTotal) {
                statusText.textContent = 'Belum Lengkap';
                statusText.className = 'mb-0 text-warning';
            } else if (
                numericValues.some(function (value) {
                    return value < 110;
                })
            ) {
                statusText.textContent = 'Perlu Pemeriksaan';
                statusText.className = 'mb-0 text-danger';
            } else {
                statusText.textContent = 'OK';
                statusText.className = 'mb-0 text-success';
            }

            document
                .querySelectorAll('.bct-input')
                .forEach(function (input) {
                    const value = parseFloat(input.value);

                    input.classList.toggle(
                        'bct-warning',
                        Number.isFinite(value) && value < 110
                    );
                });
        }

        bctTables.addEventListener('input', function (event) {
            if (!event.target.classList.contains('bct-input')) {
                return;
            }

            const index = parseInt(event.target.dataset.index);
            state.values[index] = event.target.value;

            updateSummary();
        });

        document
            .getElementById('bct4gt1')
            .addEventListener('input', function () {
                state.bct4gt1 = this.value;
            });

        document
            .getElementById('bct4gt2')
            .addEventListener('input', function () {
                state.bct4gt2 = this.value;
            });

        resamplingButton.addEventListener('click', function () {
            if (state.resamplingCount >= maximumResampling) {
                return;
            }

            state.resamplingCount++;

            for (
                let index = 0;
                index < itemsPerSampling;
                index++
            ) {
                state.values.push('');
            }

            renderTables();

            showAlert(
                'info',
                'Kolom resampling kedua berhasil ditambahkan.'
            );
        });

        saveButton.addEventListener('click', function () {
            state.bct4gt1 =
                document.getElementById('bct4gt1').value;

            state.bct4gt2 =
                document.getElementById('bct4gt2').value;

            localStorage.setItem(
                storageKey,
                JSON.stringify(state)
            );

            showAlert(
                'success',
                'Data BCT manual berhasil disimpan tanpa reload halaman.'
            );
        });

        loadState();
        renderTables();
    });
</script>
@endsection