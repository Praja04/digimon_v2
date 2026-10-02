@extends('layouts.component.main')
@section('title', 'Trace Batching - PO ' . $productionBatch->po_number)

@section('styles')
<style>
    /* Hero Header Styling */
    .hero-banner {
        background: linear-gradient(135deg, #1e2538 0%, #29344d 100%);
        border-radius: 12px;
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .hero-stat-box {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        padding: 1rem 1.25rem;
    }

    /* Custom Navigation Tabs */
    .nav-tabs-custom {
        border-bottom: 2px solid #e9ebec;
        background: #ffffff;
        border-radius: 10px 10px 0 0;
        padding: 0.5rem 1rem 0 1rem;
    }

    .nav-tabs-custom .nav-link {
        color: #495057;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 0.85rem 1.25rem;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-tabs-custom .nav-link:hover {
        color: #405189;
        border-color: transparent;
    }

    .nav-tabs-custom .nav-link.active {
        color: #405189;
        background-color: transparent;
        border-bottom: 3px solid #405189;
    }

    .nav-tabs-custom .nav-link i {
        font-size: 1.15rem;
    }

    /* Table & Card Styles */
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #495057;
        vertical-align: middle;
    }

    .table tbody td {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .card-tab-container {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .badge-soft-primary {
        background-color: rgba(64, 81, 137, 0.1);
        color: #405189;
    }
    .badge-soft-success {
        background-color: rgba(10, 179, 156, 0.1);
        color: #0ab39c;
    }
    .badge-soft-warning {
        background-color: rgba(247, 184, 75, 0.15);
        color: #b57a05;
    }
    .badge-soft-danger {
        background-color: rgba(240, 101, 72, 0.1);
        color: #f06548;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Trace Batching Raw Material & Blending</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.menu') }}">Menu</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('analisa.blending-awal.export.index') }}">Dokumen Analisis Blending (Export & Cetak)</a></li>
                            <li class="breadcrumb-item active">Trace Batching</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @php
            $kimiaCount = $productionBatch->BlendingAwal ? $productionBatch->BlendingAwal->count() : 0;
            $mikroCount = $productionBatch->blendingAfterAdjustMikro ? $productionBatch->blendingAfterAdjustMikro->count() : 0;
            $totalBatches = $kimiaCount + $mikroCount;

            $kimiaComplete = $kimiaCount > 0 ? $productionBatch->isBlendingAwalComplete() : true;
            $mikroComplete = $mikroCount > 0 ? $productionBatch->isBlendingAwalMikroComplete() : true;
            $isAllComplete = ($kimiaCount > 0 || $mikroCount > 0) && $kimiaComplete && $mikroComplete;

            // Hitung status disposisi untuk Tab 3
            $allKimia = $productionBatch->BlendingAwal ?? collect();
            $allMikro = $productionBatch->blendingAfterAdjustMikro ?? collect();

            $countRelease = $allKimia->where('disposition', 'Release')->count() + $allMikro->filter(function($i){ return in_array($i->hasil, ['OK', 'Release']); })->count();
            $countReleaseBersyarat = $allKimia->where('disposition', 'Release Bersyarat')->count();
            $countAdjustment = $allKimia->where('disposition', 'Adjustment')->count();
            $countReject = $allKimia->where('disposition', 'Reject')->count() + $allMikro->filter(function($i){ return in_array($i->hasil, ['Reject', 'NOT OK']); })->count();
            $countPending = $allKimia->filter(function($i) { return is_null($i->disposition); })->count() + $allMikro->filter(function($i) { return is_null($i->hasil); })->count();
        @endphp

        <!-- 1. Hero Banner Card -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="hero-banner p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-8 col-md-7">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary bg-opacity-75 px-2 py-1 fw-bold fs-11">PRODUCTION BATCH PO</span>
                                @if($isAllComplete)
                                    <span class="badge bg-success px-2 py-1 fw-bold fs-11"><i class="ri-check-line me-1"></i> Blending: Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark px-2 py-1 fw-bold fs-11"><i class="ri-time-line me-1"></i> Blending: Dalam Proses</span>
                                @endif
                            </div>
                            <h2 class="text-white fw-bold mb-2 display-6">{{ $productionBatch->po_number }}</h2>
                            <div class="d-flex flex-wrap align-items-center gap-3 text-white text-opacity-75 fs-13">
                                <div><i class="ri-price-tag-3-line text-warning me-1"></i> Varian: <strong class="text-white">{{ $productionBatch->variant ?? '-' }}</strong></div>
                                <div>|</div>
                                <div><i class="ri-calendar-event-line text-info me-1"></i> Tanggal: <strong class="text-white">{{ \Carbon\Carbon::parse($productionBatch->date)->format('d/m/Y') }}</strong></div>
                                <div>|</div>
                                <div><i class="ri-stack-line text-success me-1"></i> Range: <strong class="text-white">{{ $productionBatch->batch_range ?? '-' }}</strong></div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-5">
                            <div class="hero-stat-box text-end">
                                <span class="text-white text-opacity-75 small d-block mb-1">Rentang Batch & Total Input</span>
                                <h3 class="text-info fw-bold mb-1">{{ $productionBatch->batch_range ?? '-' }} <span class="fs-14 fw-normal text-white">({{ $totalBatches }} Batch)</span></h3>
                                <div class="text-white text-opacity-75 fs-12 mt-1">
                                    <span class="badge bg-primary-subtle text-primary me-1">Kimia: {{ $kimiaCount }} Batch</span>
                                    <span class="badge bg-success-subtle text-success">Mikro: {{ $mikroCount }} Batch</span>
                                </div>
                                <div class="mt-3 d-flex justify-content-end gap-2">
                                    <a href="{{ route('analisa.blending-awal.export.index') }}" class="btn btn-sm btn-light bg-opacity-10 text-white border-light border-opacity-25">
                                        <i class="ri-arrow-left-line me-1"></i> Kembali
                                    </a>
                                    <a href="{{ route('doc-blending-awal.export', ['po_id' => $productionBatch->id]) }}" class="btn btn-sm btn-success">
                                        <i class="ri-file-excel-2-line me-1"></i> Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Nav Tabs (1. Trace Waktu, 2. Parameter Batch, 3. Status Disposisi) -->
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs nav-tabs-custom" id="traceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-waktu-btn" data-bs-toggle="tab" data-bs-target="#tab-waktu" type="button" role="tab" aria-controls="tab-waktu" aria-selected="true">
                            <i class="ri-time-line"></i> 1. Trace Jam & Waktu Pengerjaan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-parameter-btn" data-bs-toggle="tab" data-bs-target="#tab-parameter" type="button" role="tab" aria-controls="tab-parameter" aria-selected="false">
                            <i class="ri-flask-line"></i> 2. Parameter Masing-Masing Batch
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-disposisi-btn" data-bs-toggle="tab" data-bs-target="#tab-disposisi" type="button" role="tab" aria-controls="tab-disposisi" aria-selected="false">
                            <i class="ri-shield-check-line"></i> 3. Status & Disposisi
                        </button>
                    </li>
                </ul>

                <div class="card card-tab-container shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="tab-content" id="traceTabsContent">

                            <!-- ========================================== -->
                            <!-- TAB 1: Trace Jam & Waktu Pengerjaan -->
                            <!-- ========================================== -->
                            <div class="tab-pane fade show active" id="tab-waktu" role="tabpanel" aria-labelledby="tab-waktu-btn">
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-primary"><i class="ri-history-line me-1"></i> Log Trace Kronologis Waktu Analisa PO</h5>
                                        <p class="text-muted small mb-0">Rincian jam, tanggal input, operator/analis blending, dan selang waktu pengerjaan batch.</p>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary fs-12">{{ $totalBatches }} Aktivitas Tercatat</span>
                                </div>

                                @php
                                    $timelineItems = collect();
                                    if ($productionBatch->BlendingAwal) {
                                        foreach($productionBatch->BlendingAwal as $item) {
                                            $timelineItems->push([
                                                'type' => 'Blending Awal (Kimia)',
                                                'badge_color' => 'bg-primary',
                                                'item' => $item,
                                                'batch_no' => $item->batch_range ?: ($item->nomor_blending ?: '1'),
                                                'no_blending' => $item->nomor_blending ?? '-',
                                                'time' => $item->created_at ? \Carbon\Carbon::parse($item->created_at) : null,
                                                'analis' => $item->user->name ?? 'Analis Kimia',
                                                'disposisi' => $item->disposition,
                                                'status_input' => !is_null($item->disposition)
                                            ]);
                                        }
                                    }
                                    if ($productionBatch->blendingAfterAdjustMikro) {
                                        foreach($productionBatch->blendingAfterAdjustMikro as $item) {
                                            $timelineItems->push([
                                                'type' => 'Blending Adjust (Mikro)',
                                                'badge_color' => 'bg-success',
                                                'item' => $item,
                                                'batch_no' => $item->batch_range ?: ($item->nomor_blending ?: '1'),
                                                'no_blending' => $item->nomor_blending ?? '-',
                                                'time' => $item->created_at ? \Carbon\Carbon::parse($item->created_at) : null,
                                                'analis' => $item->nama_analis ?? 'Analis Mikro',
                                                'disposisi' => $item->hasil,
                                                'status_input' => !is_null($item->hasil)
                                            ]);
                                        }
                                    }
                                    $timelineItems = $timelineItems->sortBy(function($t) {
                                        return $t['time'] ? $t['time']->timestamp : 0;
                                    })->values();
                                @endphp

                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="50">#</th>
                                                <th>Kategori</th>
                                                <th>Batch #</th>
                                                <th>No. Blending</th>
                                                <th>Tanggal Input</th>
                                                <th>Jam / Waktu Input</th>
                                                <th>PIC / Analis</th>
                                                <th>Status Pengerjaan</th>
                                                <th>Disposisi Saat Ini</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($timelineItems as $idx => $t)
                                                <tr>
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td>
                                                        <span class="badge {{ $t['badge_color'] }}">{{ $t['type'] }}</span>
                                                    </td>
                                                    <td><strong class="text-dark">Batch {{ $t['batch_no'] }}</strong></td>
                                                    <td><span class="badge bg-light text-dark border">Tangki {{ $t['no_blending'] }}</span></td>
                                                    <td>
                                                        <i class="ri-calendar-line text-muted me-1"></i>
                                                        {{ $t['time'] ? $t['time']->setTimezone('Asia/Jakarta')->format('d-m-Y') : '-' }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info-subtle text-info fs-12">
                                                            <i class="ri-time-line me-1"></i>
                                                            {{ $t['time'] ? $t['time']->setTimezone('Asia/Jakarta')->format('H:i:s') . ' WIB' : '-' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <i class="ri-user-3-line text-muted me-1"></i>
                                                        <strong>{{ $t['analis'] }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($t['status_input'])
                                                            <span class="badge bg-success-subtle text-success"><i class="ri-checkbox-circle-line me-1"></i> Selesai Diinput</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning"><i class="ri-loader-4-line me-1"></i> Draft / Belum Lengkap</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($t['disposisi'] === 'Release' || $t['disposisi'] === 'OK')
                                                            <span class="badge bg-success">{{ $t['disposisi'] }}</span>
                                                        @elseif($t['disposisi'] === 'Release Bersyarat')
                                                            <span class="badge bg-warning text-dark">{{ $t['disposisi'] }}</span>
                                                        @elseif($t['disposisi'] === 'Adjustment')
                                                            <span class="badge bg-info text-dark">{{ $t['disposisi'] }}</span>
                                                        @elseif($t['disposisi'] === 'Reject' || $t['disposisi'] === 'NOT OK')
                                                            <span class="badge bg-danger">{{ $t['disposisi'] }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">Pending</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-4 text-muted">
                                                        <i class="ri-time-line fs-2 d-block mb-1"></i>
                                                        Belum ada data riwayat waktu analisa blending untuk PO ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ========================================== -->
                            <!-- TAB 2: Parameter Masing-Masing Batch -->
                            <!-- ========================================== -->
                            <div class="tab-pane fade" id="tab-parameter" role="tabpanel" aria-labelledby="tab-parameter-btn">
                                
                                <!-- Parameter Blending Awal (Kimia) -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="fw-bold text-primary mb-0">
                                            <i class="ri-bar-chart-box-line me-1"></i> Parameter Analisa - Blending Awal ({{ $kimiaCount }} Batch)
                                        </h5>
                                        @if($kimiaCount > 0)
                                            <a href="{{ route('analisa.blending-awal.show', ['id' => $productionBatch->id]) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                                <i class="ri-external-link-line me-1"></i> Modul Blending Awal
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="40">#</th>
                                                    <th>Batch</th>
                                                    <th>No. Blending</th>
                                                    <th>Brix (°Bx)</th>
                                                    <th>NaCl (%)</th>
                                                    <th>BJ</th>
                                                    <th>Visko</th>
                                                    <th>pH</th>
                                                    <th>Aw</th>
                                                    <th>Organoleptik</th>
                                                    <th>Warna</th>
                                                    <th>Adj. Air / Garam / Caramel</th>
                                                    <th>Revisi</th>
                                                    <th>PIC Analis</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($productionBatch->BlendingAwal as $idx => $k)
                                                    @php
                                                        $adjParts = [];
                                                        if ($k->adjustment_qty_air) $adjParts[] = 'Air: ' . $k->adjustment_qty_air . ' L';
                                                        if ($k->adjustment_qty_garam) $adjParts[] = 'Garam: ' . $k->adjustment_qty_garam . ' kg';
                                                        if ($k->adjustment_qty_caramel) $adjParts[] = 'Caramel: ' . $k->adjustment_qty_caramel . ' kg';
                                                        $adjSummary = !empty($adjParts) ? implode(' | ', $adjParts) : '-';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td><strong class="text-primary">Batch {{ $k->batch_range ?: ($idx + 1) }}</strong></td>
                                                        <td>Tangki {{ $k->nomor_blending ?? '-' }}</td>
                                                        <td>
                                                            @if($k->brix !== null)
                                                                <span class="badge bg-light text-dark border fs-12 fw-bold">{{ number_format((float)$k->brix, 2) }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($k->nacl !== null)
                                                                <span class="badge bg-light text-dark border fs-12 fw-bold">{{ number_format((float)$k->nacl, 2) }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $k->bj !== null ? number_format((float)$k->bj, 4) : '-' }}</td>
                                                        <td>{{ $k->visco !== null ? number_format((float)$k->visco, 2) : '-' }}</td>
                                                        <td>{{ $k->ph !== null ? number_format((float)$k->ph, 2) : '-' }}</td>
                                                        <td>{{ $k->aw !== null ? number_format((float)$k->aw, 3) : '-' }}</td>
                                                        <td>{{ $k->organo ?? '-' }}</td>
                                                        <td>{{ $k->color->name ?? '-' }}</td>
                                                        <td>{{ $adjSummary }}</td>
                                                        <td>{{ $k->revisi ?? '-' }}</td>
                                                        <td>{{ $k->user->name ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="14" class="text-center py-3 text-muted">Tidak ada data parameter Blending Awal (Kimia)</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Parameter Blending After Adjust (Mikro) -->
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="fw-bold text-success mb-0">
                                            <i class="ri-microscope-line me-1"></i> Parameter Analisa - Blending After Adjust Mikro ({{ $mikroCount }} Batch)
                                        </h5>
                                        @if($mikroCount > 0)
                                            <a href="{{ route('analisa.blending-awal-mikro.show', ['id' => $productionBatch->id]) }}" class="btn btn-outline-success btn-sm" target="_blank">
                                                <i class="ri-external-link-line me-1"></i> Modul Blending Mikro
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th width="40">#</th>
                                                    <th>Batch</th>
                                                    <th>No. Blending</th>
                                                    <th>Volume (L)</th>
                                                    <th>Shift</th>
                                                    <th>EB</th>
                                                    <th>TPC</th>
                                                    <th>YM</th>
                                                    <th>Hasil / Disposisi</th>
                                                    <th>PIC Analis Mikro</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($productionBatch->blendingAfterAdjustMikro as $idx => $m)
                                                    <tr>
                                                        <td>{{ $idx + 1 }}</td>
                                                        <td><strong class="text-success">Batch {{ $m->batch_range ?: ($idx + 1) }}</strong></td>
                                                        <td>Tangki {{ $m->nomor_blending ?? '-' }}</td>
                                                        <td>{{ $m->volume !== null ? number_format($m->volume, 0) . ' L' : '-' }}</td>
                                                        <td>{{ $m->shift ?? '-' }}</td>
                                                        <td>{{ $m->eb ?? '-' }}</td>
                                                        <td>{{ $m->tpc ?? '-' }}</td>
                                                        <td>{{ $m->ym ?? '-' }}</td>
                                                        <td>
                                                            @if($m->hasil === 'OK' || $m->hasil === 'Release')
                                                                <span class="badge bg-success">{{ $m->hasil }}</span>
                                                            @elseif($m->hasil)
                                                                <span class="badge bg-warning text-dark">{{ $m->hasil }}</span>
                                                            @else
                                                                <span class="badge bg-secondary">Pending</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $m->nama_analis ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center py-3 text-muted">Tidak ada data parameter Blending After Adjust (Mikro)</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <!-- ========================================== -->
                            <!-- TAB 3: Status & Disposisi -->
                            <!-- ========================================== -->
                            <div class="tab-pane fade" id="tab-disposisi" role="tabpanel" aria-labelledby="tab-disposisi-btn">
                                
                                <!-- Ringkasan Disposisi Cards -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6 col-md-3">
                                        <div class="p-3 border rounded bg-success bg-opacity-10 text-center">
                                            <h6 class="text-success mb-1 fw-bold">RELEASE</h6>
                                            <h3 class="mb-0 text-success fw-bold">{{ $countRelease }}</h3>
                                            <span class="text-muted fs-11">Batch Memenuhi Standar</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-3 border rounded bg-warning bg-opacity-10 text-center">
                                            <h6 class="text-warning text-dark mb-1 fw-bold">ADJUSTMENT</h6>
                                            <h3 class="mb-0 text-warning text-dark fw-bold">{{ $countAdjustment + $countReleaseBersyarat }}</h3>
                                            <span class="text-muted fs-11">Perlu / Dilakukan Penyesuaian</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-3 border rounded bg-danger bg-opacity-10 text-center">
                                            <h6 class="text-danger mb-1 fw-bold">REJECT</h6>
                                            <h3 class="mb-0 text-danger fw-bold">{{ $countReject }}</h3>
                                            <span class="text-muted fs-11">Tidak Memenuhi Standar</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-3 border rounded bg-secondary bg-opacity-10 text-center">
                                            <h6 class="text-secondary mb-1 fw-bold">PENDING / ON PROGRESS</h6>
                                            <h3 class="mb-0 text-secondary fw-bold">{{ $countPending }}</h3>
                                            <span class="text-muted fs-11">Belum Disposisi Lengkap</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Disposisi Tiap Batch Blending -->
                                <h5 class="fw-bold text-dark mb-3"><i class="ri-list-check-2 me-1"></i> Detail Disposisi Tiap Batch Blending</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="40">#</th>
                                                <th>Kategori</th>
                                                <th>Batch #</th>
                                                <th>Status Analisa</th>
                                                <th>Disposisi QC</th>
                                                <th>Catatan / Remark Disposisi</th>
                                                <th>PIC / Analis</th>
                                                <th>Waktu Disposisi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $dispIdx = 1; @endphp
                                            {{-- Blending Awal (Kimia) Disposisi --}}
                                            @foreach($productionBatch->BlendingAwal as $k)
                                                <tr>
                                                    <td>{{ $dispIdx++ }}</td>
                                                    <td><span class="badge bg-primary">Blending Awal (Kimia)</span></td>
                                                    <td><strong class="text-dark">Batch {{ $k->batch_range ?: '-' }}</strong></td>
                                                    <td>
                                                        @if($k->status == 'OK')
                                                            <span class="badge bg-success-subtle text-success">OK</span>
                                                        @elseif($k->status == 'NOT OK')
                                                            <span class="badge bg-danger-subtle text-danger">NOT OK</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning">{{ $k->status ?? 'Adjustment' }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($k->disposition == 'Release')
                                                            <span class="badge bg-success px-2 py-1 fs-12"><i class="ri-checkbox-circle-line me-1"></i> Release</span>
                                                        @elseif($k->disposition == 'Release Bersyarat')
                                                            <span class="badge bg-warning text-dark px-2 py-1 fs-12"><i class="ri-alert-line me-1"></i> Release Bersyarat</span>
                                                        @elseif($k->disposition == 'Adjustment')
                                                            <span class="badge bg-info text-dark px-2 py-1 fs-12"><i class="ri-tools-line me-1"></i> Adjustment</span>
                                                        @elseif($k->disposition == 'Reject')
                                                            <span class="badge bg-danger px-2 py-1 fs-12"><i class="ri-close-circle-line me-1"></i> Reject</span>
                                                        @else
                                                            <span class="badge bg-secondary px-2 py-1 fs-12">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $k->disposition_remark ?: '-' }}</td>
                                                    <td>{{ $k->user->name ?? '-' }}</td>
                                                    <td>{{ $k->updated_at ? \Carbon\Carbon::parse($k->updated_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}</td>
                                                </tr>
                                            @endforeach

                                            {{-- Blending After Adjust (Mikro) Disposisi --}}
                                            @foreach($productionBatch->blendingAfterAdjustMikro as $m)
                                                <tr>
                                                    <td>{{ $dispIdx++ }}</td>
                                                    <td><span class="badge bg-success">Blending Adjust (Mikro)</span></td>
                                                    <td><strong class="text-dark">Batch {{ $m->batch_range ?: '-' }}</strong></td>
                                                    <td>
                                                        @if($m->hasil == 'OK' || $m->hasil == 'Release')
                                                            <span class="badge bg-success-subtle text-success">OK</span>
                                                        @elseif($m->hasil)
                                                            <span class="badge bg-warning-subtle text-warning">{{ $m->hasil }}</span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($m->hasil == 'Release' || $m->hasil == 'OK')
                                                            <span class="badge bg-success px-2 py-1 fs-12"><i class="ri-checkbox-circle-line me-1"></i> {{ $m->hasil }}</span>
                                                        @elseif($m->hasil == 'Reject' || $m->hasil == 'NOT OK')
                                                            <span class="badge bg-danger px-2 py-1 fs-12"><i class="ri-close-circle-line me-1"></i> {{ $m->hasil }}</span>
                                                        @elseif($m->hasil)
                                                            <span class="badge bg-warning text-dark px-2 py-1 fs-12"><i class="ri-alert-line me-1"></i> {{ $m->hasil }}</span>
                                                        @else
                                                            <span class="badge bg-secondary px-2 py-1 fs-12">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>-</td>
                                                    <td>{{ $m->nama_analis ?? '-' }}</td>
                                                    <td>{{ $m->updated_at ? \Carbon\Carbon::parse($m->updated_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}</td>
                                                </tr>
                                            @endforeach

                                            @if($totalBatches === 0)
                                                <tr>
                                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data disposisi blending</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
