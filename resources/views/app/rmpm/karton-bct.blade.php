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

                    <div class="d-flex align-items-center gap-2">
                        <span id="autoSaveIndicator" class="small text-muted me-2 d-none">
                            <span class="status-saving text-primary d-none"><i class="mdi mdi-cloud-sync-outline me-1"></i>Menyimpan otomatis...</span>
                            <span class="status-saved text-success d-none"><i class="mdi mdi-cloud-check-outline me-1"></i>Tersimpan otomatis</span>
                            <span class="status-offline text-muted d-none"><i class="mdi mdi-cloud-outline me-1"></i>Tersimpan di perangkat lokal</span>
                        </span>

                        <button
                            type="button"
                            id="saveButton"
                            class="btn btn-primary">
                            <i class="mdi mdi-content-save-outline me-1"></i>
                            Simpan
                        </button>

                        <button
                            type="button"
                            id="resamplingButton"
                            class="btn btn-warning">
                            <i class="mdi mdi-refresh me-1"></i>
                            Resampling
                        </button>

                        <a
                            href="{{ route('rmpm.pm.karton.display', $packagingIncoming) }}"
                            class="btn btn-light">
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
                                                    placeholder="0.00">
                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    id="bct4gt2"
                                                    class="form-control text-center"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="0.00">
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

                <div class="mt-4 d-none" id="resamplingSection">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Hasil BCT (Resampling)</h5>
                            <p class="text-muted mb-0">
                                Isi nilai BCT secara manual pada tabel berikut. Kolom resampling tidak wajib diisi semua.
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                Resampling ke-<span id="resamplingCount">1</span>
                            </span>
                            <button
                                type="button"
                                id="cancelResamplingButton"
                                class="btn btn-outline-danger btn-sm"
                                title="Hapus pemeriksaan resampling terakhir">
                                <i class="mdi mdi-trash-can-outline me-1"></i>
                                Hapus Resampling
                            </button>
                        </div>
                    </div>

                    <div id="bctTables"></div>
                </div>

                <div class="mt-4">
                    <div class="summary-card">
                        <div>
                            <span class="summary-label">Rata-rata BCT</span>
                            <h3 class="mb-0 text-primary" id="averageValue">0.00</h3>
                        </div>

                        <div>
                            <span class="summary-label">Status</span>
                            <h4 class="mb-0 text-secondary fw-bold" id="statusText">
                                Tidak Terisi
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
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        padding: 18px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .summary-card>div {
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
    document.addEventListener('DOMContentLoaded', function() {
        const storageKey = 'karton_bct_manual_data_{{ $packagingIncoming->id }}';
        const serverData = @json($bctData ?? null);
        const storeUrl = '{{ route("rmpm.pm.karton.bct.store", $packagingIncoming) }}';
        const csrfToken = '{{ csrf_token() }}';
        const maximumResampling = 2;
        const itemsPerSampling = 10;

        const resamplingSection = document.getElementById('resamplingSection');
        const bctTables = document.getElementById('bctTables');
        const saveButton = document.getElementById('saveButton');
        const resamplingButton = document.getElementById('resamplingButton');
        const cancelResamplingButton = document.getElementById('cancelResamplingButton');
        const resamplingCountElement = document.getElementById('resamplingCount');
        const averageValue = document.getElementById('averageValue');
        const statusText = document.getElementById('statusText');
        const alertContainer = document.getElementById('alertContainer');
        const bct4gt1Input = document.getElementById('bct4gt1');
        const bct4gt2Input = document.getElementById('bct4gt2');

        let state = {
            resamplingCount: 0,
            bct4gt1: '',
            bct4gt2: '',
            values: [],
        };

        function loadState() {
            // 1. Coba ambil dari server data yang tersimpan di database
            if (serverData && (serverData.bct4gt1 !== null || serverData.bct4gt2 !== null || (Array.isArray(serverData.values) && serverData.values.length > 0))) {
                state.bct4gt1 = serverData.bct4gt1 !== null && serverData.bct4gt1 !== undefined ? String(serverData.bct4gt1) : '';
                state.bct4gt2 = serverData.bct4gt2 !== null && serverData.bct4gt2 !== undefined ? String(serverData.bct4gt2) : '';
                state.resamplingCount = Math.min(Math.max(parseInt(serverData.resampling_count ?? 0) || 0, 0), maximumResampling);

                const expectedLength = state.resamplingCount * itemsPerSampling;
                state.values = Array.isArray(serverData.values) ?
                    serverData.values.slice(0, expectedLength).map(function(v) {
                        return v !== null && v !== undefined ? String(v) : '';
                    }) :
                    [];

                while (state.values.length < expectedLength) {
                    state.values.push('');
                }

                localStorage.setItem(storageKey, JSON.stringify(state));
                return;
            }

            // 2. Fallback: ambil dari localStorage jika di server belum ada
            const saved = localStorage.getItem(storageKey);

            if (!saved) {
                return;
            }

            try {
                const parsed = JSON.parse(saved);

                state.bct4gt1 = parsed.bct4gt1 ?? '';
                state.bct4gt2 = parsed.bct4gt2 ?? '';

                let savedCount = parseInt(parsed.resamplingCount ?? 0) || 0;
                savedCount = Math.min(Math.max(savedCount, 0), maximumResampling);

                // Periksa apakah ada nilai resampling yang terisi atau sengaja ditambahkan
                const hasAnyResamplingValue = Array.isArray(parsed.values) && parsed.values.some(function(v) {
                    return v !== '' && v !== null && v !== undefined && parseFloat(v) > 0;
                });

                if (savedCount > 0 && (hasAnyResamplingValue || parsed.resamplingExplicitlyAdded)) {
                    state.resamplingCount = savedCount;
                    const expectedLength = state.resamplingCount * itemsPerSampling;
                    state.values = Array.isArray(parsed.values) ?
                        parsed.values.slice(0, expectedLength) :
                        [];
                    while (state.values.length < expectedLength) {
                        state.values.push('');
                    }
                } else {
                    state.resamplingCount = 0;
                    state.values = [];
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

            if (state.resamplingCount <= 0) {
                if (resamplingSection) resamplingSection.classList.add('d-none');
                if (resamplingButton) resamplingButton.disabled = false;
            } else {
                if (resamplingSection) resamplingSection.classList.remove('d-none');
                if (resamplingCountElement) resamplingCountElement.textContent = state.resamplingCount;
                if (resamplingButton) resamplingButton.disabled = state.resamplingCount >= maximumResampling;

                for (let samplingIndex = 0; samplingIndex < state.resamplingCount; samplingIndex++) {
                    const startIndex = samplingIndex * itemsPerSampling;
                    let headers = '';
                    let inputs = '';

                    for (let itemIndex = 0; itemIndex < itemsPerSampling; itemIndex++) {
                        const globalIndex = startIndex + itemIndex;
                        const number = itemIndex + 1;
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
                                <div class="bct-result-title d-flex justify-content-between align-items-center">
                                    <span>Hasil BCT - Resampling ${samplingIndex + 1}</span>
                                    <span class="badge bg-light text-muted border font-monospace" style="font-size: 11px;">10 Sampel</span>
                                </div>

                                <div class="table-responsive">
                                    <table
                                        class="table table-bordered align-middle bct-result-table mb-0"
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
            }

            if (bct4gt1Input) bct4gt1Input.value = state.bct4gt1;
            if (bct4gt2Input) bct4gt2Input.value = state.bct4gt2;

            updateSummary();
        }

        function updateSummary() {
            const filledValues = [];

            // 1. Data dari 2 Sampel Awal
            const v1 = parseFloat(state.bct4gt1);
            if (Number.isFinite(v1) && v1 > 0) {
                filledValues.push(v1);
            }

            const v2 = parseFloat(state.bct4gt2);
            if (Number.isFinite(v2) && v2 > 0) {
                filledValues.push(v2);
            }

            // 2. Data dari Resampling (tidak wajib diisi 10)
            if (Array.isArray(state.values)) {
                state.values.forEach(function(val) {
                    const v = parseFloat(val);
                    if (Number.isFinite(v) && v > 0) {
                        filledValues.push(v);
                    }
                });
            }

            const totalFilled = filledValues.length;

            // Perhitungan Average: berdasarkan data yang diisi (2 sampel awal + data resampling)
            const average = totalFilled > 0 ?
                (filledValues.reduce(function(sum, val) {
                    return sum + val;
                }, 0) / totalFilled) :
                0;

            averageValue.textContent = average.toFixed(2);

            // Status: Terisi (Jika ada min 1 data yang >0) & Tidak Terisi (Jika belum ada data yang terisi atau seluruh data = 0)
            if (totalFilled > 0) {
                statusText.textContent = 'Terisi';
                statusText.className = 'mb-0 text-success fw-bold';
            } else {
                statusText.textContent = 'Tidak Terisi';
                statusText.className = 'mb-0 text-secondary fw-bold';
            }

            // Highlight warning pada input individual jika ada nilai terisi < 110
            const allInputs = [
                bct4gt1Input,
                bct4gt2Input,
                ...document.querySelectorAll('.bct-input')
            ];

            allInputs.forEach(function(input) {
                if (!input) return;
                const val = parseFloat(input.value);
                input.classList.toggle(
                    'bct-warning',
                    Number.isFinite(val) && val > 0 && val < 110
                );
            });
        }

        // Auto-Save Management
        let autoSaveTimer = null;
        let isAutoSaving = false;
        let pendingAutoSave = false;

        const autoSaveIndicator = document.getElementById('autoSaveIndicator');

        function setAutoSaveStatus(status) {
            if (!autoSaveIndicator) return;
            autoSaveIndicator.classList.remove('d-none');
            const saving = autoSaveIndicator.querySelector('.status-saving');
            const saved = autoSaveIndicator.querySelector('.status-saved');
            const offline = autoSaveIndicator.querySelector('.status-offline');
            if (saving) saving.classList.toggle('d-none', status !== 'saving');
            if (saved) saved.classList.toggle('d-none', status !== 'saved');
            if (offline) offline.classList.toggle('d-none', status !== 'offline');
        }

        function calculateCurrentSummary() {
            const filledValues = [];
            const v1 = parseFloat(state.bct4gt1);
            if (Number.isFinite(v1) && v1 > 0) filledValues.push(v1);
            const v2 = parseFloat(state.bct4gt2);
            if (Number.isFinite(v2) && v2 > 0) filledValues.push(v2);

            if (Array.isArray(state.values)) {
                state.values.forEach(function(val) {
                    const v = parseFloat(val);
                    if (Number.isFinite(v) && v > 0) filledValues.push(v);
                });
            }

            const totalFilled = filledValues.length;
            const average = totalFilled > 0 ?
                (filledValues.reduce(function(sum, val) {
                    return sum + val;
                }, 0) / totalFilled) :
                0;
            const currentStatus = totalFilled > 0 ? 'Terisi' : 'Tidak Terisi';

            return {
                average,
                currentStatus
            };
        }

        function scheduleAutoSave() {
            // Simpan langsung ke localStorage untuk persistensi offline seketika
            localStorage.setItem(storageKey, JSON.stringify(state));

            if (autoSaveTimer) {
                clearTimeout(autoSaveTimer);
            }

            autoSaveTimer = setTimeout(function() {
                executeAutoSave();
            }, 1800);
        }

        function executeAutoSave() {
            if (isAutoSaving) {
                pendingAutoSave = true;
                return;
            }

            const {
                average,
                currentStatus
            } = calculateCurrentSummary();

            isAutoSaving = true;
            setAutoSaveStatus('saving');

            fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        bct4gt1: state.bct4gt1 !== '' ? parseFloat(state.bct4gt1) : null,
                        bct4gt2: state.bct4gt2 !== '' ? parseFloat(state.bct4gt2) : null,
                        resampling_count: state.resamplingCount,
                        values: state.values,
                        average: average,
                        status: currentStatus,
                        is_auto_save: 1
                    })
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(res) {
                    isAutoSaving = false;
                    if (res.success) {
                        setAutoSaveStatus('saved');
                    } else {
                        setAutoSaveStatus('offline');
                    }

                    if (pendingAutoSave) {
                        pendingAutoSave = false;
                        scheduleAutoSave();
                    }
                })
                .catch(function() {
                    isAutoSaving = false;
                    setAutoSaveStatus('offline');

                    if (pendingAutoSave) {
                        pendingAutoSave = false;
                        scheduleAutoSave();
                    }
                });
        }

        bctTables.addEventListener('input', function(event) {
            if (!event.target.classList.contains('bct-input')) {
                return;
            }

            const index = parseInt(event.target.dataset.index);
            state.values[index] = event.target.value;

            updateSummary();
            scheduleAutoSave();
        });

        document.addEventListener('keydown', function(event) {
            const target = event.target;
            if (!target || target.tagName !== 'INPUT') return;

            if (event.key === 'Enter') {
                const isBackwards = event.shiftKey;
                const allInputs = [
                    bct4gt1Input,
                    bct4gt2Input,
                    ...Array.from(document.querySelectorAll('.bct-input'))
                ].filter(Boolean);

                const currentIndex = allInputs.indexOf(target);
                if (currentIndex !== -1) {
                    event.preventDefault();
                    const nextIndex = isBackwards ? currentIndex - 1 : currentIndex + 1;
                    if (nextIndex >= 0 && nextIndex < allInputs.length) {
                        allInputs[nextIndex].focus();
                        allInputs[nextIndex].select();
                    }
                }
            }
        });

        if (bct4gt1Input) {
            bct4gt1Input.addEventListener('input', function() {
                state.bct4gt1 = this.value;
                updateSummary();
                scheduleAutoSave();
            });
        }

        if (bct4gt2Input) {
            bct4gt2Input.addEventListener('input', function() {
                state.bct4gt2 = this.value;
                updateSummary();
                scheduleAutoSave();
            });
        }

        if (resamplingButton) {
            resamplingButton.addEventListener('click', function() {
                if (state.resamplingCount >= maximumResampling) {
                    return;
                }

                state.resamplingCount++;
                state.resamplingExplicitlyAdded = true;

                for (let index = 0; index < itemsPerSampling; index++) {
                    state.values.push('');
                }

                renderTables();

                showAlert(
                    'info',
                    `Kolom Resampling ke-${state.resamplingCount} berhasil ditampilkan.`
                );

                if (resamplingSection) {
                    resamplingSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }

                scheduleAutoSave();
            });
        }

        if (cancelResamplingButton) {
            cancelResamplingButton.addEventListener('click', function() {
                if (state.resamplingCount <= 0) return;

                if (confirm('Apakah Anda yakin ingin menghapus kelompok pemeriksaan Resampling terakhir?')) {
                    state.resamplingCount--;
                    state.values.splice(state.resamplingCount * itemsPerSampling, itemsPerSampling);

                    if (state.resamplingCount === 0) {
                        state.resamplingExplicitlyAdded = false;
                    }

                    renderTables();

                    showAlert(
                        'warning',
                        'Pemeriksaan Resampling berhasil dihapus.'
                    );

                    scheduleAutoSave();
                }
            });
        }

        if (saveButton) {
            saveButton.addEventListener('click', function() {
                if (autoSaveTimer) {
                    clearTimeout(autoSaveTimer);
                }

                state.bct4gt1 = bct4gt1Input.value;
                state.bct4gt2 = bct4gt2Input.value;

                const {
                    average,
                    currentStatus
                } = calculateCurrentSummary();

                // Simpan cadangan ke localStorage
                localStorage.setItem(storageKey, JSON.stringify(state));

                // UI Loading state
                const originalHtml = saveButton.innerHTML;
                saveButton.disabled = true;
                saveButton.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span>Menyimpan...`;

                // Kirim dan simpan ke database server
                fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            bct4gt1: state.bct4gt1 !== '' ? parseFloat(state.bct4gt1) : null,
                            bct4gt2: state.bct4gt2 !== '' ? parseFloat(state.bct4gt2) : null,
                            resampling_count: state.resamplingCount,
                            values: state.values,
                            average: average,
                            status: currentStatus,
                        })
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(res) {
                        saveButton.disabled = false;
                        saveButton.innerHTML = originalHtml;

                        if (res.success) {
                            setAutoSaveStatus('saved');
                            showAlert(
                                'success',
                                '<i class="mdi mdi-check-circle-outline me-1"></i>Data BCT berhasil disimpan.'
                            );
                        } else {
                            setAutoSaveStatus('offline');
                            showAlert(
                                'warning',
                                res.message || 'Data tersimpan di perangkat lokal, namun gagal sinkron ke database.'
                            );
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Gagal Sinkronisasi',
                                    text: res.message || 'Data tersimpan di perangkat lokal, namun gagal sinkron ke database.'
                                });
                            }
                        }
                    })
                    .catch(function(err) {
                        saveButton.disabled = false;
                        saveButton.innerHTML = originalHtml;
                        setAutoSaveStatus('offline');

                        showAlert(
                            'info',
                            'Data BCT tersimpan di perangkat lokal. Terjadi kendala saat menghubungkan ke server.'
                        );
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kendala Koneksi',
                                text: 'Data BCT tersimpan di perangkat lokal. Terjadi kendala saat menghubungkan ke database server.'
                            });
                        }
                    });
            });
        }

        loadState();
        renderTables();
    });
</script>
@endsection