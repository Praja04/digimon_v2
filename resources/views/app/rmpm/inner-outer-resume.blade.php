@extends('layouts.component.main')

@section('title', 'Laporan Sampling Inner / Outer')

@section('content')
@php
    $samples = is_array($sampling->hasil_sampel)
        ? array_values($sampling->hasil_sampel)
        : [];

    $fotoPengecekan = is_array($sampling->foto_pengecekan)
        ? array_values(array_filter($sampling->foto_pengecekan))
        : [];

    $fotoKetidaksesuaian = is_array($sampling->foto_ketidaksesuaian)
        ? array_values(array_filter($sampling->foto_ketidaksesuaian))
        : [];

    $jenisKetidaksesuaian = is_array($sampling->jenis_ketidaksesuaian)
        ? array_values(array_filter($sampling->jenis_ketidaksesuaian))
        : [];

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

    $jenisLabel = $isOuter ? 'OUTER' : 'INNER';

    $tanggalLaporan =
        $sampling->updated_at
        ?? $sampling->created_at
        ?? $packagingIncoming->created_at;

    $rekomendasiClass = match ($sampling->rekomendasi) {
        'Diterima' => 'status-success',
        'Diterima Bersyarat' => 'status-warning',
        'Ditolak' => 'status-danger',
        default => 'status-info',
    };

    $resultIcon = function (?string $value): string {
        return match (strtoupper((string) $value)) {
            'OK' => 'mdi-check-circle text-success',
            'NG', 'NOK', 'NOT OK' => 'mdi-close-circle text-danger',
            default => 'mdi-minus-circle text-muted',
        };
    };
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="report-toolbar d-print-none">
            <a
                href="{{ route('rmpm.pm.inner-outer') }}"
                class="btn btn-light border"
            >
                <i class="mdi mdi-arrow-left me-1"></i>
                Kembali
            </a>

            <div class="d-flex gap-2">
                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="window.print()"
                >
                    <i class="mdi mdi-printer-outline me-1"></i>
                    Print Laporan
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm report-card">
            <div class="report-header">
                <div class="report-brand">
                    <img
                        src="{{ asset('assets/images/logo-miesedaap.png') }}"
                        alt="Logo Mie Sedaap"
                        class="report-logo"
                    >

                    <div>
                        <span class="report-kicker">
                            PACKAGING ONLINE
                        </span>

                        <h2 class="report-title">
                            LAPORAN HASIL SAMPLING {{ $jenisLabel }}
                        </h2>

                        <p class="report-subtitle">
                            Resume pemeriksaan Packaging Material berdasarkan nomor SPB
                        </p>
                    </div>
                </div>

                <div class="report-meta">
                    <div class="report-date">
                        {{ $tanggalLaporan?->format('d M Y') ?? '-' }}
                    </div>

                    <div class="report-time">
                        {{ $tanggalLaporan?->format('H:i') ?? '-' }} WIB
                    </div>

                    <div class="report-code">
                        {{ $jenisLabel }}/{{ $packagingIncoming->no_spb }}
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="summary-strip">
                    <div class="summary-item">
                        <span>Supplier</span>
                        <strong>
                            {{
                                $packagingIncoming->supplier?->nama
                                ?? $packagingIncoming->supplier?->nama_supplier
                                ?? '-'
                            }}
                        </strong>
                    </div>

                    <div class="summary-item">
                        <span>Truck</span>
                        <strong>{{ $packagingIncoming->no_mobil ?? '-' }}</strong>
                    </div>

                    <div class="summary-item">
                        <span>SPB</span>
                        <strong>{{ $packagingIncoming->no_spb ?? '-' }}</strong>
                    </div>

                    <div class="summary-item">
                        <span>Jenis</span>
                        <strong>{{ $packagingIncoming->jenisIncoming?->nama ?? $jenisLabel }}</strong>
                    </div>
                </div>

                <div class="identity-grid">
                    <div class="identity-item">
                        <span>NAMA ITEM</span>
                        <strong>{{ $packagingIncoming->jenisMaterial?->nama ?? '-' }}</strong>
                    </div>

                    <div class="identity-item">
                        <span>MID</span>
                        <strong>{{ $packagingIncoming->mid ?? '-' }}</strong>
                    </div>

                    <div class="identity-item">
                        <span>JUMLAH SAMPEL</span>
                        <strong>{{ $sampling->jumlah_sampel ?? count($samples) }}</strong>
                    </div>

                    <div class="identity-item">
                        <span>REKOMENDASI</span>
                        <span class="status-pill {{ $rekomendasiClass }}">
                            {{ $sampling->rekomendasi ?? '-' }}
                        </span>
                    </div>

                    <div class="identity-item">
                        <span>NOMOR BATCH</span>
                        <strong>{{ $sampling->no_batch ?? '-' }}</strong>
                    </div>

                    <div class="identity-item">
                        <span>LOT SEBELUM</span>
                        <strong>{{ $sampling->lot_sebelum ?? '-' }}</strong>
                    </div>

                    <div class="identity-item">
                        <span>LOT SETELAH</span>
                        <strong>{{ $sampling->lot_setelah ?? '-' }}</strong>
                    </div>

                    <div class="identity-item">
                        <span>CoA</span>
                        <strong>{{ $sampling->coa ?? '-' }}</strong>
                    </div>
                </div>

                <div class="status-legend">
                    <span>
                        <i class="mdi mdi-check-circle text-success"></i>
                        Baik / Sesuai
                    </span>

                    <span>
                        <i class="mdi mdi-close-circle text-danger"></i>
                        Tidak Baik / Tidak Sesuai
                    </span>

                    <span>
                        <i class="mdi mdi-alert text-warning"></i>
                        Perlu Perhatian
                    </span>
                </div>

                <div class="report-section">
                    <div class="report-section-title">
                        <i class="mdi mdi-flask-outline"></i>
                        Hasil Pemeriksaan Sampel
                    </div>

                    <div class="table-responsive report-table-wrapper">
                        <table class="table table-bordered align-middle report-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
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

                            <tbody>
                                @forelse ($samples as $index => $sample)
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}</td>
                                        <td>{{ $sample['berat_gross'] ?? '-' }}</td>
                                        <td>{{ $sample['inside_core'] ?? '-' }}</td>
                                        <td>{{ $sample['lebar'] ?? '-' }}</td>
                                        <td>{{ $sample['pitch'] ?? '-' }}</td>
                                        <td>{{ $sample['thickness'] ?? '-' }}</td>
                                        <td>{{ $sample['arah_vertikal'] ?? '-' }}</td>
                                        <td>{{ $sample['arah_terbalik'] ?? '-' }}</td>

                                        @foreach (['laminasi', 'barcode', 'design', 'warna', 'tulisan'] as $field)
                                            @php
                                                $value = $sample[$field] ?? null;
                                            @endphp

                                            <td>
                                                @if (in_array($field, ['laminasi', 'design', 'warna'], true))
                                                    <span class="result-value">
                                                        <i class="mdi {{ $resultIcon($value) }}"></i>
                                                        {{ $value ?? '-' }}
                                                    </span>
                                                @else
                                                    {{ $value ?? '-' }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center py-4 text-muted">
                                            Belum ada data sampel.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="report-section">
                    <div class="report-section-title">
                        <i class="mdi mdi-clipboard-check-outline"></i>
                        Kesimpulan Pemeriksaan
                    </div>

                    <div class="conclusion-grid">
                        <div class="conclusion-item">
                            <span>CoA</span>
                            <strong>{{ $sampling->coa ?? '-' }}</strong>
                        </div>

                        <div class="conclusion-item">
                            <span>Rekomendasi</span>
                            <strong>{{ $sampling->rekomendasi ?? '-' }}</strong>
                        </div>

                        <div class="conclusion-item">
                            <span>Konfirmasi Ketidaksesuaian</span>
                            <strong>{{ $sampling->konfirmasi_ketidaksesuaian ?? '-' }}</strong>
                        </div>

                        <div class="conclusion-item">
                            <span>Status Proses</span>
                            <strong>Final</strong>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span class="detail-label">Jenis Ketidaksesuaian</span>

                        @if (count($jenisKetidaksesuaian) > 0)
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @foreach ($jenisKetidaksesuaian as $item)
                                    <span class="issue-badge">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                                        {{ $item }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="text-success mt-2">
                                <i class="mdi mdi-check-circle-outline me-1"></i>
                                Tidak ada ketidaksesuaian.
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <span class="detail-label">Keterangan</span>
                        <div class="description-box mt-2">
                            {{ $sampling->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                        </div>
                    </div>
                </div>

                <div class="report-section">
                    <div class="report-section-title">
                        <i class="mdi mdi-camera-outline"></i>
                        Dokumentasi
                    </div>

                    <div class="row g-4">
                        <div class="col-xl-6">
                            <span class="detail-label">Foto Pengecekan</span>

                            @if (count($fotoPengecekan) > 0)
                                <div class="photo-grid mt-2">
                                    @foreach ($fotoPengecekan as $photo)
                                        <a
                                            href="{{ asset('storage/' . $photo) }}"
                                            target="_blank"
                                            class="photo-card"
                                        >
                                            <img
                                                src="{{ asset('storage/' . $photo) }}"
                                                alt="Foto Pengecekan"
                                            >
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-photo mt-2">
                                    Tidak ada foto pengecekan.
                                </div>
                            @endif
                        </div>

                        <div class="col-xl-6">
                            <span class="detail-label">Foto Ketidaksesuaian</span>

                            @if (count($fotoKetidaksesuaian) > 0)
                                <div class="photo-grid mt-2">
                                    @foreach ($fotoKetidaksesuaian as $photo)
                                        <a
                                            href="{{ asset('storage/' . $photo) }}"
                                            target="_blank"
                                            class="photo-card"
                                        >
                                            <img
                                                src="{{ asset('storage/' . $photo) }}"
                                                alt="Foto Ketidaksesuaian"
                                            >
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-photo mt-2">
                                    Tidak ada foto ketidaksesuaian.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('styles')
<style>
.report-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}
.report-card{overflow:hidden;border-radius:18px}
.report-header{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:24px 28px;border-bottom:4px solid #ef4444;background:linear-gradient(135deg,#fff,#f8fafc)}
.report-brand{display:flex;align-items:center;gap:20px;min-width:0}
.report-logo{width:118px;max-height:72px;object-fit:contain}
.report-kicker{display:block;margin-bottom:4px;color:#4f46e5;font-size:12px;font-weight:800;letter-spacing:1px}
.report-title{margin:0;color:#0f172a;font-size:25px;font-weight:800}
.report-subtitle{margin:5px 0 0;color:#64748b}
.report-meta{flex:0 0 auto;text-align:right}
.report-date{display:inline-block;padding:7px 12px;border-radius:8px;background:#4f46e5;color:#fff;font-weight:800}
.report-time,.report-code{margin-top:5px;color:#64748b;font-size:12px}
.summary-strip{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-bottom:22px}
.summary-item{padding:14px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;text-align:center}
.summary-item span,.summary-item strong{display:block}
.summary-item span{color:#64748b;font-size:12px}
.summary-item strong{margin-top:4px;color:#1e293b}
.identity-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:20px}
.identity-item span,.identity-item strong{display:block}
.identity-item>span:first-child{margin-bottom:5px;color:#64748b;font-size:12px;font-weight:800}
.identity-item strong{color:#334155;font-size:15px}
.status-pill{display:inline-flex!important;align-items:center;width:max-content;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:800}
.status-success{background:#dcfce7;color:#15803d}.status-warning{background:#fef3c7;color:#b45309}.status-danger{background:#fee2e2;color:#b91c1c}.status-info{background:#dbeafe;color:#1d4ed8}
.status-legend{display:flex;flex-wrap:wrap;gap:24px;padding:14px 0 20px;border-bottom:1px solid #e2e8f0;color:#64748b;font-size:13px;font-weight:600}
.status-legend span{display:inline-flex;align-items:center;gap:6px}
.report-section{margin-top:26px;padding:18px;border:1px solid #e2e8f0;border-radius:14px;background:#fff}
.report-section-title{display:flex;align-items:center;gap:8px;margin:-18px -18px 18px;padding:13px 16px;border-bottom:1px solid #e2e8f0;border-radius:14px 14px 0 0;background:#f8fafc;color:#475569;font-weight:800}
.report-table-wrapper{max-height:520px}.report-table{min-width:1650px;margin-bottom:0}
.report-table thead th{position:sticky;top:0;z-index:2;padding:9px 8px;background:#eef2f7;text-align:center;white-space:nowrap;font-size:12px}
.report-table td{padding:8px;text-align:center;white-space:nowrap;font-size:12px}
.result-value{display:inline-flex;align-items:center;gap:4px;font-weight:700}
.conclusion-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
.conclusion-item{padding:13px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc}
.conclusion-item span,.conclusion-item strong{display:block}
.conclusion-item span,.detail-label{color:#64748b;font-size:12px;font-weight:800}
.conclusion-item strong{margin-top:5px;color:#1e293b}
.issue-badge{display:inline-flex;align-items:center;padding:7px 10px;border-radius:8px;background:#fff7ed;color:#c2410c;font-size:12px;font-weight:700}
.description-box{padding:13px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;color:#334155;white-space:pre-wrap}
.photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px}
.photo-card{display:block;overflow:hidden;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc}
.photo-card img{width:100%;height:135px;display:block;object-fit:cover}
.empty-photo{padding:28px 16px;border:1px dashed #cbd5e1;border-radius:12px;color:#94a3b8;text-align:center;background:#f8fafc}
@media(max-width:991.98px){.report-header{align-items:flex-start}.summary-strip,.identity-grid,.conclusion-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:575.98px){.report-toolbar{align-items:stretch;flex-direction:column}.report-toolbar>div{display:grid!important}.report-header{align-items:flex-start;flex-direction:column}.report-brand{align-items:flex-start;flex-direction:column}.report-meta{width:100%;text-align:left}.summary-strip,.identity-grid,.conclusion-grid{grid-template-columns:1fr}}
@media print{
    @page{
        size:A4 landscape;
        margin:8mm;
    }

    html,
    body{
        width:100%;
        margin:0!important;
        padding:0!important;
        background:#fff!important;
        -webkit-print-color-adjust:exact!important;
        print-color-adjust:exact!important;
    }

    .vertical-menu,
    .navbar-header,
    .footer,
    .d-print-none{
        display:none!important;
    }

    .main-content{
        margin-left:0!important;
    }

    .page-content,
    .container-fluid{
        width:100%!important;
        max-width:none!important;
        margin:0!important;
        padding:0!important;
    }

    .report-card{
        width:100%!important;
        max-width:none!important;
        margin:0!important;
        border:0!important;
        border-radius:0!important;
        box-shadow:none!important;
    }

    .report-header{
        padding:10px 12px!important;
        gap:12px!important;
        border-bottom-width:2px!important;
    }

    .report-logo{
        width:78px!important;
        max-height:46px!important;
    }

    .report-kicker{
        font-size:8px!important;
    }

    .report-title{
        font-size:16px!important;
    }

    .report-subtitle{
        margin-top:2px!important;
        font-size:8px!important;
    }

    .report-date{
        padding:4px 7px!important;
        font-size:9px!important;
    }

    .report-time,
    .report-code{
        margin-top:2px!important;
        font-size:8px!important;
    }

    .card-body{
        padding:10px!important;
    }

    .summary-strip{
        grid-template-columns:repeat(4,1fr)!important;
        gap:4px!important;
        margin-bottom:10px!important;
    }

    .summary-item{
        padding:6px!important;
        border-radius:4px!important;
    }

    .summary-item span{
        font-size:7px!important;
    }

    .summary-item strong{
        margin-top:1px!important;
        font-size:9px!important;
    }

    .identity-grid{
        grid-template-columns:repeat(4,1fr)!important;
        gap:6px!important;
        margin-bottom:8px!important;
    }

    .identity-item>span:first-child{
        margin-bottom:1px!important;
        font-size:7px!important;
    }

    .identity-item strong{
        font-size:9px!important;
    }

    .status-pill{
        padding:3px 6px!important;
        font-size:8px!important;
    }

    .status-legend{
        gap:10px!important;
        padding:6px 0 8px!important;
        font-size:8px!important;
    }

    .report-section{
        margin-top:10px!important;
        padding:8px!important;
        border-radius:6px!important;
        break-inside:auto!important;
        page-break-inside:auto!important;
    }

    .report-section-title{
        margin:-8px -8px 8px!important;
        padding:6px 8px!important;
        border-radius:6px 6px 0 0!important;
        font-size:9px!important;
    }

    .report-table-wrapper{
        width:100%!important;
        max-height:none!important;
        overflow:visible!important;
    }

    .report-table{
        width:100%!important;
        min-width:0!important;
        table-layout:fixed!important;
        margin:0!important;
    }

    .report-table thead{
        display:table-header-group;
    }

    .report-table tr{
        page-break-inside:avoid!important;
        break-inside:avoid!important;
    }

    .report-table thead th{
        position:static!important;
        padding:3px 2px!important;
        font-size:6px!important;
        line-height:1.05!important;
        white-space:normal!important;
        overflow-wrap:anywhere!important;
    }

    .report-table td{
        padding:3px 2px!important;
        font-size:6px!important;
        line-height:1.05!important;
        white-space:normal!important;
        overflow-wrap:anywhere!important;
    }

    .conclusion-grid{
        grid-template-columns:repeat(4,1fr)!important;
        gap:5px!important;
    }

    .conclusion-item{
        padding:6px!important;
        border-radius:5px!important;
    }

    .conclusion-item span,
    .detail-label{
        font-size:7px!important;
    }

    .conclusion-item strong{
        margin-top:2px!important;
        font-size:8px!important;
    }

    .issue-badge{
        padding:3px 5px!important;
        font-size:7px!important;
    }

    .description-box{
        padding:6px!important;
        font-size:8px!important;
    }

    .photo-grid{
        grid-template-columns:repeat(5,1fr)!important;
        gap:5px!important;
    }

    .photo-card img{
        height:75px!important;
    }

    .empty-photo{
        padding:10px!important;
        font-size:8px!important;
    }
}
</style>
@endsection