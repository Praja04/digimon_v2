@extends('layouts.component.main')
@section('title', 'Dashboard - Timbangan Retail')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">Timbangan Retail</h4>
                        <p class="text-muted mb-0 small">Monitoring berat & transaksi per mesin</p>
                    </div>
                    <button class="btn btn-success btn-sm" id="btnExportModal">
                        <i class="ri-file-excel-2-line me-1"></i> Export Excel
                    </button>
                </div>
                <hr class="mt-3 mb-0">
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="row mb-4 g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Tanggal</label>
                <input type="date" id="filterDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Variant</label>
                <select id="filterVariant" class="form-select form-select-sm">
                    <option value="">-- Semua Variant --</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Mesin</label>
                <select id="filterMesin" class="form-select form-select-sm">
                    <option value="">-- Semua Mesin --</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <button id="btnFilter" class="btn btn-primary btn-sm w-100">
                    <i class="ri-search-line me-1"></i> Tampilkan
                </button>
            </div>
        </div>

        {{-- Loading Indicator --}}
        <div id="loadingState" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-2 small">Memuat data...</p>
        </div>

        <div id="mainContent" class="d-none">


            {{-- Shift Statistics --}}
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <h6 class="fw-semibold text-dark mb-3"><i class="ri-time-line me-1 text-primary"></i>Statistik Per Shift</h6>
                </div>
                @foreach(['Shift 1' => ['06:00 – 13:59', 'primary'], 'Shift 2' => ['14:00 – 21:59', 'warning'], 'Shift 3' => ['22:00 – 05:59 (besok)', 'danger']] as $shift => $meta)
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-{{ $meta[1] }} bg-opacity-10 border-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold text-{{ $meta[1] }}">{{ $shift }}</span>
                                <small class="text-muted">{{ $meta[0] }}</small>
                            </div>
                        </div>
                        <div class="card-body py-3">
                            <div class="row g-2 text-center" id="stat_{{ Str::slug($shift) }}">
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="small text-muted">Transaksi</div>
                                        <div class="fw-bold shift-count">-</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="small text-muted">Total</div>
                                        <div class="fw-bold shift-total">-</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <div class="small text-muted">Min</div>
                                        <div class="fw-bold text-success shift-min">-</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <div class="small text-muted">Avg</div>
                                        <div class="fw-bold text-primary shift-avg">-</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-light rounded p-2">
                                        <div class="small text-muted">Max</div>
                                        <div class="fw-bold text-danger shift-max">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Line Chart --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h6 class="fw-semibold mb-0"><i class="ri-line-chart-line me-1 text-primary"></i>Grafik Berat per Waktu</h6>
                    <small class="text-muted">Semua mesin dalam rentang shift yang dipilih</small>
                </div>
                <div class="card-body pt-2">
                    <div id="lineChart" style="min-height:300px;"></div>
                </div>
            </div>

            {{-- Per Mesin & Transaksi Terbaru --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0"><i class="ri-device-line me-1 text-primary"></i>Data Per Mesin & Transaksi Terbaru</h6>
                        <span class="badge bg-primary-subtle text-primary" id="mesinCount">0 mesin</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Mesin</th>
                                    <th class="text-center">Transaksi</th>
                                    <th class="text-end">Total Berat</th>
                                    <th class="text-end">Min</th>
                                    <th class="text-end">Avg</th>
                                    <th class="text-end">Max</th>
                                    <th>Terbaru – Waktu</th>
                                    <th>Terbaru – Berat</th>
                                    <th>Status</th>
                                    <th>Variant</th>
                                    <th class="pe-3">NIK</th>
                                </tr>
                            </thead>
                            <tbody id="mesinTableBody">
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">Belum ada data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- end #mainContent --}}

    </div>
</div>

{{-- Export Modal --}}
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="ri-file-excel-2-line me-1 text-success"></i>Export Excel</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal</label>
                    <input type="date" id="exportDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    <div class="form-text">Export mencakup Shift 1 s/d Shift 3 dari tanggal ini.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Variant <span class="text-muted">(opsional)</span></label>
                    <select id="exportVariant" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-semibold">Mesin <span class="text-muted">(opsional)</span></label>
                    <select id="exportMesin" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnDoExport" class="btn btn-success btn-sm">
                    <i class="ri-download-line me-1"></i>Download
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    (() => {
        'use strict';

        const BASE_URL = '/api/timbangan-retail';
        let lineChart = null;

        // ─── Util ────────────────────────────────────────────────────────
        const fmt = (v, unit = '') => v !== null && v !== undefined ?
            `${Number(v).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 3 })}${unit ? ' ' + unit : ''}` :
            '-';

        const statusBadge = (s) => {
            if (!s) return '-';
            const map = {
                sukses: 'success',
                gagal: 'danger',
                proses: 'warning',
                ok: 'success'
            };
            const cls = map[s.toLowerCase()] ?? 'secondary';
            return `<span class="badge bg-${cls}-subtle text-${cls}">${s}</span>`;
        };

        // ─── Parse DateTime Fix ──────────────────────────────────────────
        function parseDateTime(dateStr) {
            if (!dateStr) return null;
            try {
                // Format: "2026-05-08 19:39:17"
                const [datePart, timePart] = dateStr.split(' ');
                const [year, month, day] = datePart.split('-').map(Number);
                const [hour, minute, second] = timePart.split(':').map(Number);
                // Buat timestamp dengan timezone UTC untuk konsistensi
                return Date.UTC(year, month - 1, day, hour, minute, second);
            } catch (e) {
                console.warn('Invalid date format:', dateStr);
                return null;
            }
        }

        // ─── Load filter options ─────────────────────────────────────────
        async function loadFilterOptions() {
            try {
                const res = await fetch(`${BASE_URL}/filter-options`);
                const data = await res.json();

                const populate = (selectIds, items) => {
                    selectIds.forEach(id => {
                        const el = document.getElementById(id);
                        const current = el.value;
                        // keep first option
                        while (el.options.length > 1) el.remove(1);
                        items.forEach(v => {
                            const o = new Option(v, v);
                            el.add(o);
                        });
                        el.value = current;
                    });
                };

                populate(['filterVariant', 'exportVariant'], data.variants ?? []);
                populate(['filterMesin', 'exportMesin'], data.mesins ?? []);
            } catch (e) {
                console.error('Filter options error:', e);
            }
        }

        // ─── Fetch & Render ──────────────────────────────────────────────
        async function loadData() {
            const date = document.getElementById('filterDate').value;
            const variant = document.getElementById('filterVariant').value;
            const mesin = document.getElementById('filterMesin').value;

            if (!date) {
                alert('Pilih tanggal terlebih dahulu.');
                return;
            }

            document.getElementById('loadingState').classList.remove('d-none');
            document.getElementById('mainContent').classList.add('d-none');

            try {
                const params = new URLSearchParams({
                    date
                });
                if (variant) params.append('variant', variant);
                if (mesin) params.append('mesin', mesin);

                const res = await fetch(`${BASE_URL}/data?${params}`);
                const data = await res.json();

                if (!data.success) throw new Error(data.message ?? 'Gagal memuat data');

                renderShiftStats(data.shift_stats);
                renderChart(data.chart_data);
                renderMesinTable(data.per_mesin);

                document.getElementById('mainContent').classList.remove('d-none');
            } catch (e) {
                alert('Error: ' + e.message);
            } finally {
                document.getElementById('loadingState').classList.add('d-none');
            }
        }

        // ─── Shift Stats ─────────────────────────────────────────────────
        function renderShiftStats(stats) {
            const slugMap = {
                'Shift 1': 'shift-1',
                'Shift 2': 'shift-2',
                'Shift 3': 'shift-3'
            };
            Object.entries(stats ?? {}).forEach(([name, s]) => {
                const id = `stat_${slugMap[name]}`;
                const el = document.getElementById(id);
                if (!el) return;
                el.querySelector('.shift-count').textContent = (s.count ?? 0).toLocaleString('id-ID');
                el.querySelector('.shift-total').textContent = fmt(s.total);
                el.querySelector('.shift-min').textContent = fmt(s.min);
                el.querySelector('.shift-avg').textContent = fmt(s.average);
                el.querySelector('.shift-max').textContent = fmt(s.max);
            });
        }

        // ─── Line Chart (FIXED) ──────────────────────────────────────────
        function renderChart(chartData) {
            // Group by mesin dengan parsing tanggal yang benar
            const grouped = {};
            (chartData ?? []).forEach(d => {
                if (!d.mesin || d.y === null || d.y === undefined) return;

                if (!grouped[d.mesin]) grouped[d.mesin] = [];

                const timestamp = parseDateTime(d.x);
                if (timestamp) {
                    grouped[d.mesin].push({
                        x: timestamp,
                        y: Number(d.y)
                    });
                }
            });

            const series = Object.entries(grouped).map(([mesin, points]) => ({
                name: mesin,
                data: points.sort((a, b) => a.x - b.x)
            }));

            const options = {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: true
                    },
                    animations: {
                        enabled: true,
                        speed: 400
                    }
                },
                series,
                xaxis: {
                    type: 'datetime',
                    labels: {
                        datetimeFormatter: {
                            hour: 'HH:mm',
                            minute: 'HH:mm:ss'
                        },
                        style: {
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: v => Number(v).toLocaleString('id-ID', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 3
                        }),
                        style: {
                            fontSize: '11px'
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy HH:mm:ss'
                    },
                    y: {
                        formatter: v => fmt(v)
                    }
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                markers: {
                    size: series.length <= 2 ? 4 : 0
                },
                legend: {
                    position: 'top'
                },
                colors: ['#0d6efd', '#198754', '#fd7e14', '#dc3545', '#6f42c1', '#20c997'],
                grid: {
                    borderColor: '#f0f0f0'
                },
                noData: {
                    text: 'Tidak ada data untuk ditampilkan',
                    style: {
                        color: '#aaa'
                    }
                }
            };

            if (lineChart) {
                lineChart.destroy();
            }
            lineChart = new ApexCharts(document.getElementById('lineChart'), options);
            lineChart.render();
        }

        // ─── Per Mesin Table ─────────────────────────────────────────────
        function renderMesinTable(perMesin) {
            const tbody = document.getElementById('mesinTableBody');
            document.getElementById('mesinCount').textContent = `${(perMesin ?? []).length} mesin`;

            if (!perMesin || perMesin.length === 0) {
                tbody.innerHTML = `<tr><td colspan="11" class="text-center text-muted py-4">Tidak ada data</td></tr>`;
                return;
            }

            tbody.innerHTML = perMesin.map(m => {
                const tr = m.transaksi_terbaru ?? {};
                const waktuTerbaru = tr.waktu ? new Date(tr.waktu).toLocaleString('id-ID', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                }) : '-';

                return `
                    <tr>
                        <td class="ps-3 fw-semibold">${m.mesin ?? '-'}</td>
                        <td class="text-center">${(m.jumlah_transaksi ?? 0).toLocaleString('id-ID')}</td>
                        <td class="text-end">${fmt(m.total_berat)}</td>
                        <td class="text-end text-success">${fmt(m.min_berat)}</td>
                        <td class="text-end text-primary">${fmt(m.average_berat)}</td>
                        <td class="text-end text-danger">${fmt(m.max_berat)}</td>
                        <td><small class="text-muted">${waktuTerbaru}</small></td>
                        <td class="fw-medium">${fmt(tr.berat)}</td>
                        <td>${statusBadge(tr.status)}</td>
                        <td class="fw-semibold">${tr.variant ?? '-'}</td>
                        <td class="pe-3">${tr.nik ?? '-'}</td>
                    </tr>`;
            }).join('');
        }

        // ─── Export ──────────────────────────────────────────────────────
        function doExport() {
            const date = document.getElementById('exportDate').value;
            const variant = document.getElementById('exportVariant').value;
            const mesin = document.getElementById('exportMesin').value;

            if (!date) {
                alert('Pilih tanggal untuk export.');
                return;
            }

            const params = new URLSearchParams({
                date
            });
            if (variant) params.append('variant', variant);
            if (mesin) params.append('mesin', mesin);

            window.location.href = `${BASE_URL}/export?${params}`;
        }

        // ─── Event Listeners ─────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btnFilter').addEventListener('click', loadData);

            // Enter key di filter date
            document.getElementById('filterDate').addEventListener('keydown', e => {
                if (e.key === 'Enter') loadData();
            });

            // Export modal
            document.getElementById('btnExportModal').addEventListener('click', () => {
                document.getElementById('exportDate').value = document.getElementById('filterDate').value;
                new bootstrap.Modal(document.getElementById('exportModal')).show();
            });

            document.getElementById('btnDoExport').addEventListener('click', doExport);
        });

        // ─── Auto Enter di filter select ─────────────────────────────────
        ['filterVariant', 'filterMesin'].forEach(id => {
            document.getElementById(id)?.addEventListener('change', loadData);
        });

        // ─── Init ────────────────────────────────────────────────────────
        loadFilterOptions().then(() => loadData());

    })();
</script>
@endsection