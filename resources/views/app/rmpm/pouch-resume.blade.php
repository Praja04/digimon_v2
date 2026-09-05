@extends('layouts.component.main')

@section('title', 'Laporan Sampling Pouch')

@section('content')

@php
    $samples = is_array($sampling->hasil_sampel)
        ? array_values($sampling->hasil_sampel)
        : [];

    $thicknessBottom = is_array($sampling->hasil_thickness)
        ? array_values($sampling->hasil_thickness)
        : [];

    $samplePertama = $samples[0] ?? [];

    $barcode = $samplePertama['barcode'] ?? null;
    $qrCode = $samplePertama['qr_code'] ?? null;

    $jenisKetidaksesuaian = is_array($sampling->jenis_ketidaksesuaian)
        ? array_values(array_filter($sampling->jenis_ketidaksesuaian))
        : [];

    $fotoKetidaksesuaian = is_array($sampling->foto_ketidaksesuaian)
        ? array_values(array_filter($sampling->foto_ketidaksesuaian))
        : [];

    $tanggalLaporan = $sampling->updated_at
        ?? $sampling->created_at
        ?? $packagingIncoming->created_at;

    $badgeClass = match ($sampling->rekomendasi) {
        'Diterima' => 'status-success',
        'Diterima Bersyarat' => 'status-warning',
        'Ditolak' => 'status-danger',
        default => 'status-info',
    };

    $statusIcon = function (?string $value): string {
        return match (strtoupper((string) $value)) {
            'OK' => 'mdi-check-circle text-success',
            'NOK', 'NOT OK' => 'mdi-close-circle text-danger',
            default => 'mdi-minus-circle text-muted',
        };
    };
@endphp

<div class="page-content">
    <div class="container-fluid">

        <div class="report-toolbar d-print-none">
            <a
                href="{{ route('rmpm.pm.pouch') }}"
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
                            LAPORAN HASIL SAMPLING POUCH
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
                        POUCH/{{ $packagingIncoming->no_spb }}
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
                        <strong>{{ $packagingIncoming->jenisIncoming?->nama ?? 'Pouch' }}</strong>
                    </div>
                </div>

                <div class="identity-grid">
                    <div class="identity-item">
                        <span>NAMA ITEM</span>
                        <strong>{{ $packagingIncoming->jenisMaterial?->nama ?? '-' }}</strong>
                    </div>

                    <div class="identity-item">
                        <span>QUANTITY INCOMING</span>
                        <strong>
                            {{ $sampling->qty ?? $packagingIncoming->jumlah ?? '-' }}
                            {{ $sampling->uom ?? '' }}
                        </strong>
                    </div>

                    <div class="identity-item">
                        <span>JUMLAH SAMPEL</span>
                        <strong>{{ $sampling->jumlah_sampel ?? count($samples) }}</strong>
                    </div>

                    <div class="identity-item">
                        <span>REKOMENDASI</span>
                        <span class="status-pill {{ $badgeClass }}">
                            {{ $sampling->rekomendasi ?? '-' }}
                        </span>
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
                                    <th>Panjang (mm)</th>
                                    <th>Lebar (mm)</th>
                                    <th>Thickness (mikron)</th>
                                    <th>Berat (g)</th>
                                    <th>Side Seal 1</th>
                                    <th>Side Seal 2</th>
                                    <th>Bottom Seal</th>
                                    <th>Bottom High</th>
                                    <th>Design</th>
                                    <th>Warna</th>
                                    <th>Tulisan</th>
                                    <th>Drop Test</th>
                                    <th>Pretest</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($samples as $index => $sample)
                                    <tr>
                                        <td class="text-center fw-bold">
                                            {{ $index + 1 }}
                                        </td>
                                        <td>{{ $sample['panjang'] ?? '-' }}</td>
                                        <td>{{ $sample['lebar'] ?? '-' }}</td>
                                        <td>{{ $sample['tebal'] ?? '-' }}</td>
                                        <td>{{ $sample['berat'] ?? '-' }}</td>
                                        <td>{{ $sample['side_seal_1'] ?? '-' }}</td>
                                        <td>{{ $sample['side_seal_2'] ?? '-' }}</td>
                                        <td>{{ $sample['bottom_seal'] ?? '-' }}</td>
                                        <td>{{ $sample['bottom_high'] ?? '-' }}</td>

                                        @foreach ([
                                            'design',
                                            'warna',
                                            'tulisan',
                                            'drop_test',
                                            'pretest',
                                        ] as $field)
                                            @php
                                                $value = $sample[$field] ?? null;
                                            @endphp

                                            <td class="text-center">
                                                <span class="result-value">
                                                    <i class="mdi {{ $statusIcon($value) }}"></i>
                                                    {{ $value ?? '-' }}
                                                </span>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center py-4 text-muted">
                                            Belum ada data sampel.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-xl-6">
                        <div class="report-section h-100">
                            <div class="report-section-title">
                                <i class="mdi mdi-ruler"></i>
                                Thickness Bottom
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle report-table compact-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Thickness Bottom 1</th>
                                            <th>Thickness Bottom 2</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @for ($index = 0; $index < 3; $index++)
                                            @php
                                                $row = $thicknessBottom[$index] ?? [];
                                            @endphp

                                            <tr>
                                                <td class="text-center">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td>{{ $row['nilai_1'] ?? '-' }}</td>
                                                <td>{{ $row['nilai_2'] ?? '-' }}</td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="report-section h-100">
                            <div class="report-section-title">
                                <i class="mdi mdi-barcode-scan"></i>
                                Barcode & QR Code
                            </div>

                            <div class="data-list">
                                <div class="data-row">
                                    <span>Barcode</span>
                                    <strong>{{ $barcode ?: '-' }}</strong>
                                </div>

                                <div class="data-row">
                                    <span>QR Code</span>
                                    <strong>{{ $qrCode ?: '-' }}</strong>
                                </div>
                            </div>

                            <small class="text-muted d-block mt-3">
                                Pemeriksaan Barcode dan QR Code dilakukan pada 1 sampel.
                            </small>
                        </div>
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
                        <span class="detail-label">
                            Jenis Ketidaksesuaian
                        </span>

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

                            @if ($sampling->foto_pengecekan)
                                <a
                                    href="{{ asset('storage/' . $sampling->foto_pengecekan) }}"
                                    target="_blank"
                                    class="photo-card mt-2"
                                >
                                    <img
                                        src="{{ asset('storage/' . $sampling->foto_pengecekan) }}"
                                        alt="Foto Pengecekan"
                                    >
                                </a>
                            @else
                                <div class="empty-photo mt-2">
                                    Tidak ada foto.
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
.report-kicker{display:block;margin-bottom:4px;color:#dc2626;font-size:12px;font-weight:800;letter-spacing:1px}
.report-title{margin:0;color:#0f172a;font-size:25px;font-weight:800}
.report-subtitle{margin:5px 0 0;color:#64748b}
.report-meta{flex:0 0 auto;text-align:right}
.report-date{display:inline-block;padding:7px 12px;border-radius:8px;background:#4f46e5;color:#fff;font-weight:800}
.report-time,.report-code{margin-top:5px;color:#64748b;font-size:12px}
.summary-strip{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-bottom:28px}
.summary-item{padding:14px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;text-align:center}
.summary-item span,.summary-item strong{display:block}
.summary-item span{color:#64748b;font-size:12px}
.summary-item strong{margin-top:4px;color:#1e293b}
.identity-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px;margin-bottom:20px}
.identity-item span,.identity-item strong{display:block}
.identity-item>span:first-child{margin-bottom:5px;color:#64748b;font-size:12px;font-weight:800}
.identity-item strong{color:#334155;font-size:15px}
.status-pill{display:inline-flex!important;align-items:center;width:max-content;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:800}
.status-success{background:#dcfce7;color:#15803d}
.status-warning{background:#fef3c7;color:#b45309}
.status-danger{background:#fee2e2;color:#b91c1c}
.status-info{background:#dbeafe;color:#1d4ed8}
.status-legend{display:flex;flex-wrap:wrap;gap:24px;padding:14px 0 20px;border-bottom:1px solid #e2e8f0;color:#64748b;font-size:13px;font-weight:600}
.status-legend span{display:inline-flex;align-items:center;gap:6px}
.report-section{margin-top:26px;padding:18px;border:1px solid #e2e8f0;border-radius:14px;background:#fff}
.report-section-title{display:flex;align-items:center;gap:8px;margin:-18px -18px 18px;padding:13px 16px;border-bottom:1px solid #e2e8f0;border-radius:14px 14px 0 0;background:#f8fafc;color:#475569;font-weight:800}
.report-table-wrapper{max-height:520px}
.report-table{min-width:1750px;margin-bottom:0}
.report-table thead th{position:sticky;top:0;z-index:2;padding:9px 8px;background:#eef2f7;text-align:center;white-space:nowrap;font-size:12px}
.report-table td{padding:8px;text-align:center;white-space:nowrap;font-size:12px}
.compact-table{min-width:0}
.result-value{display:inline-flex;align-items:center;gap:4px;font-weight:700}
.data-list{display:grid;gap:10px}
.data-row{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:11px 12px;border:1px solid #e2e8f0;border-radius:9px;background:#f8fafc}
.data-row span{color:#64748b}
.data-row strong{color:#1e293b;text-align:right;word-break:break-all}
.conclusion-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
.conclusion-item{padding:13px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc}
.conclusion-item span,.conclusion-item strong{display:block}
.conclusion-item span,.detail-label{color:#64748b;font-size:12px;font-weight:800}
.conclusion-item strong{margin-top:5px;color:#1e293b}
.issue-badge{display:inline-flex;align-items:center;padding:7px 10px;border-radius:8px;background:#fff7ed;color:#c2410c;font-size:12px;font-weight:700}
.description-box{padding:13px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;color:#334155;white-space:pre-wrap}
.photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px}
.photo-card{display:block;overflow:hidden;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc}
.photo-card img{width:100%;height:180px;display:block;object-fit:cover}
.photo-grid .photo-card img{height:130px}
.empty-photo{padding:28px 16px;border:1px dashed #cbd5e1;border-radius:12px;color:#94a3b8;text-align:center;background:#f8fafc}

.report-access-card{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    margin-bottom:24px;
    padding:18px 20px;
    border:1px solid #c7d2fe;
    border-radius:14px;
    background:linear-gradient(135deg,#eef2ff,#ffffff);
}

.report-access-info{
    display:flex;
    align-items:center;
    gap:14px;
    min-width:0;
}

.report-access-icon{
    width:52px;
    height:52px;
    flex:0 0 52px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:12px;
    background:#4f46e5;
    color:#fff;
    font-size:28px;
}

.report-access-info strong{
    display:block;
    color:#1e293b;
    font-size:15px;
}

.report-access-info p{
    margin-top:3px;
    color:#64748b;
    font-size:13px;
}

.report-access-info small{
    display:block;
    max-width:720px;
    margin-top:5px;
    overflow:hidden;
    color:#4f46e5;
    text-overflow:ellipsis;
    white-space:nowrap;
}

.report-qr-box{
    flex:0 0 auto;
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#fff;
}

.report-qr-image{
    width:92px;
    height:92px;
    object-fit:contain;
}

.report-qr-box span{
    max-width:110px;
    color:#475569;
    font-size:12px;
    font-weight:700;
}

.print-qr-block{
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-bottom:20px;
    padding:12px 14px;
    border:1px solid #cbd5e1;
    border-radius:10px;
}

.print-qr-block strong,
.print-qr-block small{
    display:block;
}

.print-qr-block small{
    margin-top:3px;
    color:#64748b;
}

.print-qr-block img{
    width:82px;
    height:82px;
    object-fit:contain;
}

@media (max-width:991.98px){.report-header{align-items:flex-start}.summary-strip,.identity-grid,.conclusion-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:575.98px){.report-toolbar{align-items:stretch;flex-direction:column}.report-toolbar>div{display:grid!important}.report-header{align-items:flex-start;flex-direction:column}.report-brand{align-items:flex-start;flex-direction:column}.report-meta{width:100%;text-align:left}.summary-strip,.identity-grid,.conclusion-grid{grid-template-columns:1fr}}
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

    .compact-table{
        min-width:0!important;
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

    .photo-card img,
    .photo-grid .photo-card img{
        height:75px!important;
    }

    .empty-photo{
        padding:10px!important;
        font-size:8px!important;
    }
}
</style>
@endsection