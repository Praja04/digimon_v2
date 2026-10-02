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
</style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Trace Batching Pasteurisasi & Storage</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('analisa.monitoring-turun-blending.menu') }}">Menu</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('analisa.monitoring-turun-blending.export.index') }}">Dokumen Analisis Pasteurisasi & Storage</a></li>
                                <li class="breadcrumb-item active">Trace Batching</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            @php
                $turunCount = $productionBatch->monitoringTurunBlending ? $productionBatch->monitoringTurunBlending->count() : 0;
                $pastCount = $productionBatch->monitoringPasteurisasi ? $productionBatch->monitoringPasteurisasi->count() : 0;
                $stKimiaCount = $productionBatch->monitoringStorageKimia ? $productionBatch->monitoringStorageKimia->count() : 0;
                $stMikroCount = $productionBatch->monitoringStorageMikro ? $productionBatch->monitoringStorageMikro->count() : 0;
                $totalData = $turunCount + $pastCount + $stKimiaCount + $stMikroCount;

                $pastComplete = $pastCount > 0 ? $productionBatch->isMonitoringPasteurisasiComplete() : true;
                $stComplete = $stKimiaCount > 0 ? $productionBatch->isMonitoringStorageKimiaComplete() : true;
                $isAllComplete = ($pastCount > 0 || $stKimiaCount > 0) && $pastComplete && $stComplete;
            @endphp

            <!-- Hero Banner Card -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="hero-banner p-4">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-8 col-md-7">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-primary bg-opacity-75 px-2 py-1 fw-bold fs-11">PRODUCTION BATCH PO</span>
                                    @if($isAllComplete)
                                        <span class="badge bg-success px-2 py-1 fw-bold fs-11"><i class="ri-check-line me-1"></i> Pasteurisasi & ST: Selesai</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2 py-1 fw-bold fs-11"><i class="ri-time-line me-1"></i> Pasteurisasi & ST: Dalam Proses</span>
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
                                    <span class="text-white text-opacity-75 small d-block mb-1">Total Titik Analisis & Monitoring</span>
                                    <h3 class="text-info fw-bold mb-1">{{ $totalData }} <span class="fs-14 fw-normal text-white">Data Tercatat</span></h3>
                                    <div class="text-white text-opacity-75 fs-12 mt-1">
                                        <span class="badge bg-primary-subtle text-primary me-1">Turun: {{ $turunCount }}</span>
                                        <span class="badge bg-info-subtle text-info me-1">Past: {{ $pastCount }}</span>
                                        <span class="badge bg-warning-subtle text-warning me-1">ST Kimia: {{ $stKimiaCount }}</span>
                                        <span class="badge bg-success-subtle text-success">ST Mikro: {{ $stMikroCount }}</span>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-end gap-2">
                                        <a href="{{ route('analisa.monitoring-turun-blending.export.index') }}" class="btn btn-sm btn-light bg-opacity-10 text-white border-light border-opacity-25">
                                            <i class="ri-arrow-left-line me-1"></i> Kembali
                                        </a>
                                        <a href="{{ route('doc-pasteurisasi-storage.export', ['po_id' => $productionBatch->id]) }}" class="btn btn-sm btn-success">
                                            <i class="ri-file-excel-2-line me-1"></i> Export Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-tabs nav-tabs-custom" id="traceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-turun-btn" data-bs-toggle="tab" data-bs-target="#tab-turun" type="button" role="tab" aria-controls="tab-turun" aria-selected="true">
                                <i class="ri-arrow-down-circle-line"></i> 1. Turun Blending ({{ $turunCount }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-past-btn" data-bs-toggle="tab" data-bs-target="#tab-past" type="button" role="tab" aria-controls="tab-past" aria-selected="false">
                                <i class="ri-temp-hot-line"></i> 2. Pasteurisasi ({{ $pastCount }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-stkimia-btn" data-bs-toggle="tab" data-bs-target="#tab-stkimia" type="button" role="tab" aria-controls="tab-stkimia" aria-selected="false">
                                <i class="ri-inbox-line"></i> 3. Storage Tank Kimia ({{ $stKimiaCount }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-stmikro-btn" data-bs-toggle="tab" data-bs-target="#tab-stmikro" type="button" role="tab" aria-controls="tab-stmikro" aria-selected="false">
                                <i class="ri-microscope-line"></i> 4. Storage Tank Mikro ({{ $stMikroCount }})
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Tab 1: Turun Blending -->
                        <div class="tab-pane fade show active" id="tab-turun" role="tabpanel" aria-labelledby="tab-turun-btn">
                            <div class="card shadow-sm border-0 card-tab-container">
                                <div class="card-header bg-white py-3">
                                    <h6 class="card-title mb-0 fw-bold text-primary">Data Monitoring Turun Blending</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Jam / Waktu</th>
                                                    <th>Tangki</th>
                                                    <th>Brix</th>
                                                    <th>pH</th>
                                                    <th>Organo</th>
                                                    <th>Disposisi</th>
                                                    <th>Analis / PIC</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($productionBatch->monitoringTurunBlending as $mtb)
                                                    <tr>
                                                        <td>{{ $mtb->created_at ? $mtb->created_at->format('H:i:s d/m/Y') : '-' }}</td>
                                                        <td><span class="badge bg-light text-dark fw-semibold">{{ $mtb->tank ?? '-' }}</span></td>
                                                        <td><span class="fw-semibold">{{ $mtb->brix ?? '-' }}</span></td>
                                                        <td><span class="fw-semibold">{{ $mtb->ph ?? '-' }}</span></td>
                                                        <td><span class="badge bg-info-subtle text-info">{{ $mtb->organo ?? 'OK' }}</span></td>
                                                        <td>
                                                            @if($mtb->disposition == 'Release')
                                                                <span class="badge bg-success">Release</span>
                                                            @elseif($mtb->disposition == 'Reject')
                                                                <span class="badge bg-danger">Reject</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ $mtb->disposition ?? '-' }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $mtb->user->name ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data monitoring turun blending untuk PO ini.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Pasteurisasi -->
                        <div class="tab-pane fade" id="tab-past" role="tabpanel" aria-labelledby="tab-past-btn">
                            <div class="card shadow-sm border-0 card-tab-container">
                                <div class="card-header bg-white py-3">
                                    <h6 class="card-title mb-0 fw-bold text-success">Data Analisa & Monitoring Pasteurisasi</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Jam</th>
                                                    <th>Shift</th>
                                                    <th>BJ</th>
                                                    <th>Brix</th>
                                                    <th>pH</th>
                                                    <th>Aw</th>
                                                    <th>Viskositas</th>
                                                    <th>Organo / Aroma</th>
                                                    <th>Warna</th>
                                                    <th>Disposisi</th>
                                                    <th>PIC</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($productionBatch->monitoringPasteurisasi as $mp)
                                                    <tr>
                                                        <td>{{ $mp->created_at ? $mp->created_at->format('H:i') : '-' }}</td>
                                                        <td><span class="badge bg-secondary">Shift {{ $mp->shift ?? '-' }}</span></td>
                                                        <td>{{ $mp->bj ?? '-' }}</td>
                                                        <td><strong>{{ $mp->brix ?? '-' }}</strong></td>
                                                        <td><strong>{{ $mp->ph ?? '-' }}</strong></td>
                                                        <td>{{ $mp->aw ?? '-' }}</td>
                                                        <td>{{ $mp->visco ?? '-' }}</td>
                                                        <td>{{ $mp->organo ?? 'OK' }}</td>
                                                        <td>{{ $mp->color->name ?? '-' }}</td>
                                                        <td>
                                                            @if($mp->disposition == 'Release')
                                                                <span class="badge bg-success">Release</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">{{ $mp->disposition ?? '-' }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $mp->user->name ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center py-4 text-muted">Belum ada data analisa pasteurisasi untuk PO ini.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Storage Tank Kimia -->
                        <div class="tab-pane fade" id="tab-stkimia" role="tabpanel" aria-labelledby="tab-stkimia-btn">
                            <div class="card shadow-sm border-0 card-tab-container">
                                <div class="card-header bg-white py-3">
                                    <h6 class="card-title mb-0 fw-bold text-warning">Data Analisa Storage Tank Kimia</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Jam</th>
                                                    <th>Storage</th>
                                                    <th>BJ</th>
                                                    <th>Brix</th>
                                                    <th>pH</th>
                                                    <th>% NaCl</th>
                                                    <th>Viskositas</th>
                                                    <th>Organo</th>
                                                    <th>Warna</th>
                                                    <th>Aw</th>
                                                    <th>Disposisi</th>
                                                    <th>PIC</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($productionBatch->monitoringStorageKimia as $msk)
                                                    <tr>
                                                        <td>{{ $msk->created_at ? $msk->created_at->format('H:i') : '-' }}</td>
                                                        <td><span class="badge bg-primary">{{ $msk->storage ?? 'ST 01' }}</span></td>
                                                        <td>{{ $msk->bj ?? '-' }}</td>
                                                        <td><strong>{{ $msk->brix ?? '-' }}</strong></td>
                                                        <td><strong>{{ $msk->ph ?? '-' }}</strong></td>
                                                        <td>{{ $msk->nacl ?? '-' }}</td>
                                                        <td>{{ $msk->visco ?? '-' }}</td>
                                                        <td>{{ $msk->organo ?? 'OK' }}</td>
                                                        <td>{{ $msk->color->name ?? '-' }}</td>
                                                        <td>{{ $msk->aw ?? '-' }}</td>
                                                        <td>
                                                            @if($msk->disposition == 'Release')
                                                                <span class="badge bg-success">Release</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">{{ $msk->disposition ?? '-' }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $msk->user->name ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="12" class="text-center py-4 text-muted">Belum ada data analisa storage kimia untuk PO ini.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Storage Tank Mikro -->
                        <div class="tab-pane fade" id="tab-stmikro" role="tabpanel" aria-labelledby="tab-stmikro-btn">
                            <div class="card shadow-sm border-0 card-tab-container">
                                <div class="card-header bg-white py-3">
                                    <h6 class="card-title mb-0 fw-bold text-success">Data Analisa Storage Tank Mikro</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Jam / Waktu</th>
                                                    <th>Storage</th>
                                                    <th>TPC</th>
                                                    <th>Yeast & Mold</th>
                                                    <th>Coliform</th>
                                                    <th>Hasil / Status</th>
                                                    <th>Analis Mikro</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($productionBatch->monitoringStorageMikro as $msm)
                                                    <tr>
                                                        <td>{{ $msm->created_at ? $msm->created_at->format('H:i:s d/m/Y') : '-' }}</td>
                                                        <td><span class="badge bg-info">{{ $msm->storage ?? 'ST' }}</span></td>
                                                        <td>{{ $msm->tpc ?? '-' }}</td>
                                                        <td>{{ $msm->ym ?? '-' }}</td>
                                                        <td>{{ $msm->coliform ?? '-' }}</td>
                                                        <td>
                                                            @if(in_array($msm->hasil, ['OK', 'Release']))
                                                                <span class="badge bg-success">{{ $msm->hasil }}</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ $msm->hasil ?? '-' }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $msm->user->name ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data analisa storage mikro untuk PO ini.</td>
                                                    </tr>
                                                @endforelse
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
