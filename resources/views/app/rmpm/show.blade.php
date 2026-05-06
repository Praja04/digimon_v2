@extends('layouts.component.main')
@section('title', 'Detail Identitas')
@section('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #demo,
            #demo * {
                visibility: visible;
            }

            #demo {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page Title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('rmpm.index') }}">RMPM</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#samplingModal">
                                    <i class="ri-add-line align-bottom me-1"></i> Sampling
                                </button>
                                <button type="button" class="btn btn-warning" id="btnBukaModalKonfirmasi">
                                    Konfirmasi
                                </button>
                                <a href="{{ route('rmpm.analisa', $identitas->id) }}" class="btn btn-info">
                                    <i class="ri-filter-3-line align-bottom me-1"></i> Analisa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Konfirmasi Jam --}}
            @php $konfirmasi = $konfirmasi ?? null; @endphp
            @if ($konfirmasi)
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body py-3">
                                <div class="d-flex flex-wrap gap-4 align-items-center">
                                    <strong class="text-muted"><i class="ri-time-line me-1"></i> Konfirmasi Waktu</strong>
                                    @if ($konfirmasi->waktu_kedatangan)
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success-subtle text-success fs-6">
                                                <i class="ri-truck-line me-1"></i>
                                                {{ \Carbon\Carbon::parse($konfirmasi->waktu_kedatangan)->format('d M Y, H:i') }}
                                                WIB
                                            </span>
                                        </div>
                                    @endif
                                    @if ($konfirmasi->waktu_analisa)
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-info-subtle text-info fs-6">
                                                <i class="ri-flask-line me-1"></i>
                                                Analisa:
                                                {{ \Carbon\Carbon::parse($konfirmasi->waktu_analisa)->format('d M Y, H:i') }}
                                                WIB
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main Detail Card --}}
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="card" id="demo">
                        <div class="row">

                            {{-- Header --}}
                            <div class="col-lg-12">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-4">
                                            <img src="{{ asset('assets/images/icon-utility/kecap.png') }}" alt="Logo"
                                                height="100" class="rounded-2">
                                            <div>
                                                <h4 class="mb-0 fw-bold text-dark">{{ $identitas->jenis }}</h4>
                                                <small class="text-muted">{{ $identitas->asal_bahan }} ·
                                                    {{ $identitas->no_spb }}</small>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex flex-column flex-sm-row justify-content-sm-end align-items-sm-center text-sm-end text-center mt-2">
                                            <div>
                                                <span class="badge bg-primary fs-6 fw-semibold mb-1 mb-sm-0">
                                                    {{ \Carbon\Carbon::parse($identitas->tanggal_kedatangan)->format('d M Y') }}
                                                </span>
                                                <div class="text-muted fs-6 mt-1">
                                                    {{ \Carbon\Carbon::parse($identitas->tanggal_kedatangan)->format('H:i') }}
                                                    WIB
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-2 text-center">
                                        <div class="col-6 col-lg">
                                            <div class="border rounded p-2 bg-light">
                                                <div class="text-muted small">Supplier</div>
                                                <div class="fw-semibold">{{ $identitas->supplier }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg">
                                            <div class="border rounded p-2 bg-light">
                                                <div class="text-muted small">Truck</div>
                                                <div class="fw-semibold">{{ $identitas->no_plat }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg">
                                            <div class="border rounded p-2 bg-light">
                                                <div class="text-muted small">SPB</div>
                                                <div class="fw-semibold">{{ $identitas->no_spb }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg">
                                            <div class="border rounded p-2 bg-light">
                                                <div class="text-muted small">Jenis</div>
                                                <div class="fw-semibold">{{ $identitas->jenis }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Tambahan & Disposisi --}}
                            <div class="col-lg-12">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-lg-3 col-6">
                                            <p class="text-muted mb-2 text-uppercase fw-semibold">Nama Bahan</p>
                                            <h5 class="fs-14 mb-0">{{ $identitas->jenis }}</h5>
                                        </div>
                                        <div class="col-lg-3 col-6">
                                            <p class="text-muted mb-2 text-uppercase fw-semibold">Jumlah Kedatangan</p>
                                            <h5 class="fs-14 mb-0">{{ $identitas->jumlah_kedatangan }} kg</h5>
                                        </div>
                                        <div class="col-lg-3 col-6">
                                            <p class="text-muted mb-2 text-uppercase fw-semibold">Lot / Batch</p>
                                            <h5 class="fs-14 mb-0">{{ $identitas->lot_batch }}</h5>
                                        </div>
                                        <div class="col-lg-3 col-6">
                                            <p class="text-muted mb-2 text-uppercase fw-semibold">Disposisi</p>
                                            @if ($identitas->jenis == 'Gula Tebu' || $identitas->jenis == 'Gula Kelapa')
                                                @php
                                                    $disposisi_short_term =
                                                        $analisa_short_term->first()->disposisi ?? null;
                                                    $disposisi_long_term =
                                                        $analisa_long_term->first()->disposisi ?? null;
                                                @endphp
                                                <h6>Short Term : {{ $disposisi_short_term ?? 'Belum input' }}</h6>
                                                <h6>Long Term : {{ $disposisi_long_term ?? 'Belum input' }}</h6>
                                            @else
                                                @php $disposisi_gula_garam = $analisa_garam_gula->first()->disposisi ?? null; @endphp
                                                <h6>Disposisi : {{ $disposisi_gula_garam ?? 'Belum input' }}</h6>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Keterangan Status --}}
                            <div class="col-12 mb-3">
                                <div class="alert alert-light border-0 shadow-sm">
                                    <div class="d-flex flex-wrap align-items-center gap-4">
                                        <strong class="text-muted">Keterangan Status:</strong>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ri-checkbox-circle-fill text-success fs-4 fw-bold"></i>
                                            <span class="text-muted fw-semibold">Baik / Tersedia / Sesuai</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ri-close-circle-fill text-danger fs-4 fw-bold"></i>
                                            <span class="text-muted fw-semibold">Tidak Baik / Tidak Tersedia</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ri-alert-fill text-warning fs-4 fw-bold"></i>
                                            <span class="text-muted fw-semibold">Bermasalah / Tidak Sesuai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-semibold text-muted">Sample Kondisi Mobil</h5>
                                        @if ($data_mobil && auth()->user()->role == 'Foreman')
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editModalMobil">
                                                <i class="ri-edit-box-line me-1"></i>Update
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body p-3">
                                        @if ($data_mobil)
                                            @php
                                                // yes_good = yes berarti bagus → hijau. yes_bad = yes berarti buruk → merah/warning
                                                $mobilFields = [
                                                    'bersih' => ['label' => 'Bersih', 'type' => 'yes_good'],
                                                    'kering' => ['label' => 'Kering', 'type' => 'yes_good'],
                                                    'benda_asing' => [
                                                        'label' => 'Tidak Ada Benda Asing',
                                                        'type' => 'yes_good',
                                                    ],
                                                    'cacat' => ['label' => 'Tidak Cacat', 'type' => 'yes_good'],
                                                    'segel' => ['label' => 'Segel', 'type' => 'yes_good'],
                                                    'berbau' => ['label' => 'Berbau', 'type' => 'yes_bad'],
                                                ];
                                            @endphp
                                            <div class="row g-2">
                                                @foreach ($mobilFields as $key => $cfg)
                                                    @php $val = optional($data_mobil)->$key; @endphp
                                                    <div class="col-6 col-md-4">
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if ($val === null)
                                                                <i
                                                                    class="ri-indeterminate-circle-fill text-secondary fs-4 fw-bold"></i>
                                                            @elseif ($cfg['type'] === 'yes_good')
                                                                @if ($val === 'yes')
                                                                    <i
                                                                        class="ri-checkbox-circle-fill text-success fs-4 fw-bold"></i>
                                                                @else
                                                                    <i
                                                                        class="ri-close-circle-fill text-danger fs-4 fw-bold"></i>
                                                                @endif
                                                            @else
                                                                @if ($val === 'yes')
                                                                    <i class="ri-alert-fill text-warning fs-4 fw-bold"></i>
                                                                @else
                                                                    <i
                                                                        class="ri-checkbox-circle-fill text-success fs-4 fw-bold"></i>
                                                                @endif
                                                            @endif
                                                            <span
                                                                class="text-muted fw-semibold">{{ $cfg['label'] }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-center text-muted mb-0">Belum ada data</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-semibold text-muted">Dokumen Pendukung</h5>
                                        @if ($data_dokumen && auth()->user()->role == 'Foreman')
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editModalDokumen">
                                                <i class="ri-edit-box-line me-1"></i>Update
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body p-3">
                                        @if ($data_dokumen)
                                            <div class="row g-2">
                                                @foreach ([
            'coa' => 'CoA',
            'surat_jalan' => 'Surat Jalan Vendor',
            'packing_list' => 'Packing List',
            'identitas_kemasan' => 'Identitas Kemasan',
            'logo_halal' => 'Logo Halal',
            'kesesuaian_matriks_bahan' => 'Kesesuaian Matriks Bahan Baku',
        ] as $key => $label)
                                                    @php $val = optional($data_dokumen)->$key; @endphp
                                                    <div class="col-6 col-md-4">
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if ($val === 'yes')
                                                                <i
                                                                    class="ri-checkbox-circle-fill text-success fs-4 fw-bold"></i>
                                                            @elseif ($val === 'no')
                                                                <i
                                                                    class="ri-close-circle-fill text-danger fs-4 fw-bold"></i>
                                                            @else
                                                                <i
                                                                    class="ri-indeterminate-circle-fill text-secondary fs-4 fw-bold"></i>
                                                            @endif
                                                            <span
                                                                class="text-muted fw-semibold">{{ $label }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-center text-muted mb-0">Belum ada data</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-semibold text-muted">Sample Fisik Kemasan</h5>
                                        @if ($data_kemasan && auth()->user()->role == 'Foreman')
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editModalKemasan">
                                                <i class="ri-edit-box-line me-1"></i>Update
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body p-3">
                                        @if ($data_kemasan)
                                            @php
                                                $kemasanFields =
                                                    $identitas->jenis === 'Garam'
                                                        ? [
                                                            'kotor' => ['label' => 'Kotor', 'type' => 'yes_bad'],
                                                            'berair' => ['label' => 'Berair', 'type' => 'yes_bad'],
                                                            'basah' => ['label' => 'Basah', 'type' => 'yes_bad'],
                                                            'campuran' => ['label' => 'Campuran', 'type' => 'yes_bad'],
                                                            'rusak' => [
                                                                'label' => 'Rusak / Robek',
                                                                'type' => 'yes_bad',
                                                            ],
                                                            'sesuai_std' => [
                                                                'label' => 'Sesuai STD',
                                                                'type' => 'yes_good',
                                                            ],
                                                        ]
                                                        : [
                                                            'kotor' => ['label' => 'Kotor', 'type' => 'yes_bad'],
                                                            'rusak' => [
                                                                'label' => 'Rusak / Sobek',
                                                                'type' => 'yes_bad',
                                                            ],
                                                            'sesuai_std' => [
                                                                'label' => 'Sesuai STD',
                                                                'type' => 'yes_good',
                                                            ],
                                                        ];
                                            @endphp
                                            <div class="row g-2">
                                                @foreach ($kemasanFields as $key => $cfg)
                                                    @php
                                                        $val = optional($data_kemasan)->$key;
                                                        $keterangan_key = 'keterangan_' . $key;
                                                    @endphp
                                                    <div class="col-6 col-md-4">
                                                        <div
                                                            class="d-flex {{ !empty(optional($data_kemasan)->$keterangan_key) ? 'align-items-start' : 'align-items-center' }} gap-2">
                                                            @if ($val === null)
                                                                <i
                                                                    class="ri-indeterminate-circle-fill text-secondary fs-4 fw-bold mt-1"></i>
                                                            @elseif ($cfg['type'] === 'yes_good')
                                                                @if ($val === 'yes')
                                                                    <i
                                                                        class="ri-checkbox-circle-fill text-success fs-4 fw-bold mt-1"></i>
                                                                @else
                                                                    <i
                                                                        class="ri-close-circle-fill text-danger fs-4 fw-bold mt-1"></i>
                                                                @endif
                                                            @else
                                                                @if ($val === 'yes')
                                                                    <i
                                                                        class="ri-alert-fill text-warning fs-4 fw-bold mt-1"></i>
                                                                @else
                                                                    <i
                                                                        class="ri-checkbox-circle-fill text-success fs-4 fw-bold mt-1"></i>
                                                                @endif
                                                            @endif
                                                            <div>
                                                                <span
                                                                    class="text-muted fw-semibold">{{ $cfg['label'] }}</span>
                                                                @if (!empty(optional($data_kemasan)->$keterangan_key))
                                                                    <div class="text-warning small fst-italic mt-1">
                                                                        <i class="ri-error-warning-line"></i>
                                                                        {{ $data_kemasan->$keterangan_key }} Zak
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if (!empty($data_kemasan->lain_lain))
                                                    <div class="col-12 mt-2">
                                                        <div
                                                            class="d-flex {{ !empty(optional($data_kemasan)->$keterangan_key) ? 'align-items-start' : 'align-items-center' }} gap-2">
                                                            <i class="ri-information-line text-info fs-4 fw-bold mt-1"></i>
                                                            <div>
                                                                <span class="text-muted fw-semibold">Lain-lain</span>
                                                                <div class="text-muted small fst-italic mt-1">
                                                                    {{ $data_kemasan->lain_lain }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-center text-muted mb-0">Belum ada data</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-semibold text-muted">Sample Fisik Raw</h5>
                                        @if ($data_raw && auth()->user()->role == 'Foreman')
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#editModalRaw">
                                                <i class="ri-edit-box-line me-1"></i>Update
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body p-3">
                                        @if ($data_raw)
                                            @php
                                                $rawFields = [
                                                    'leleh' => ['label' => 'Leleh', 'type' => 'yes_bad'],
                                                    'warna' => [
                                                        'label' => 'Warna Tidak Sesuai Standar',
                                                        'type' => 'yes_bad',
                                                    ],
                                                    'campuran' => ['label' => 'Campuran', 'type' => 'yes_bad'],
                                                    'aroma' => [
                                                        'label' => 'Aroma Tidak Sesuai Standar',
                                                        'type' => 'yes_bad',
                                                    ],
                                                    'sesuai_std' => ['label' => 'Sesuai Standar', 'type' => 'yes_good'],
                                                ];
                                            @endphp
                                            <div class="row g-2">
                                                @foreach ($rawFields as $key => $cfg)
                                                    @php
                                                        $val = optional($data_raw)->$key;
                                                        $keterangan_key = 'keterangan_' . $key;
                                                    @endphp
                                                    <div class="col-6 col-md-4">
                                                        <div
                                                            class="d-flex {{ !empty(optional($data_raw)->$keterangan_key) ? 'align-items-start' : 'align-items-center' }} gap-2">
                                                            @if ($val === null)
                                                                <i
                                                                    class="ri-indeterminate-circle-fill text-secondary fs-4 fw-bold mt-1"></i>
                                                            @elseif ($cfg['type'] === 'yes_good')
                                                                @if ($val === 'yes')
                                                                    <i
                                                                        class="ri-checkbox-circle-fill text-success fs-4 fw-bold mt-1"></i>
                                                                @else
                                                                    <i
                                                                        class="ri-close-circle-fill text-danger fs-4 fw-bold mt-1"></i>
                                                                @endif
                                                            @else
                                                                @if ($val === 'yes')
                                                                    <i
                                                                        class="ri-alert-fill text-warning fs-4 fw-bold mt-1"></i>
                                                                @else
                                                                    <i
                                                                        class="ri-checkbox-circle-fill text-success fs-4 fw-bold mt-1"></i>
                                                                @endif
                                                            @endif
                                                            <div>
                                                                <span
                                                                    class="text-muted fw-semibold">{{ $cfg['label'] }}</span>
                                                                @if (!empty(optional($data_raw)->$keterangan_key))
                                                                    <div class="text-warning small fst-italic mt-1">
                                                                        <i class="ri-error-warning-line"></i>
                                                                        {{ $data_raw->$keterangan_key }} Zak
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-center text-muted mb-0">Belum ada data</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- ── TABEL ANALISA ── --}}
                            @if ($identitas->jenis == 'Gula Tebu' || $identitas->jenis == 'Gula Kelapa')

                                {{-- Short Term --}}
                                <div class="col-lg-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 fw-semibold">Short Term Analisa</h5>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped align-middle mb-0">
                                                    <thead class="table-light text-muted text-uppercase small">
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th>Brix</th>
                                                            <th>pH</th>
                                                            <th>Kotoran</th>
                                                            <th>KA</th>
                                                            <th>Organo</th>
                                                            <th>Warna</th>
                                                            <th>Aroma</th>
                                                            <th>Disposisi</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($analisa_short_term as $short)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $short->brix ?? '-' }}</td>
                                                                <td>{{ $short->ph ?? '-' }}</td>
                                                                <td>{{ $short->kotoran ?? '-' }}</td>
                                                                <td>{{ $short->ka ?? '-' }}</td>
                                                                <td>{{ $short->organo ?? '-' }}</td>
                                                                <td>{{ $short->warna ?? '-' }}</td>
                                                                <td>{{ $short->aroma ?? '-' }}</td>
                                                                <td>{{ $short->disposisi ?? '-' }}</td>
                                                                <td>{{ $short->keterangan ?? '-' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="9" class="text-center text-muted py-4">
                                                                    Belum ada data short term</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Long Term --}}
                                <div class="col-lg-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 fw-semibold">Long Term Analisa</h5>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped align-middle mb-0">
                                                    <thead class="table-light text-muted text-uppercase small">
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th>Uji Kristal</th>
                                                            <th>Disposisi</th>
                                                            <th>Attachment</th>
                                                            <th>Keterangan</th>
                                                            <th width="12%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($analisa_long_term as $long)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>
                                                                    @if ($long->uji_kristal === 'positif')
                                                                        <span
                                                                            class="badge bg-danger-subtle text-danger">Positif</span>
                                                                    @elseif ($long->uji_kristal === 'negatif')
                                                                        <span
                                                                            class="badge bg-success-subtle text-success">Negatif</span>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>{{ $long->disposisi ?? '-' }}</td>
                                                                <td>
                                                                    @if (!empty($long->attachment) && $long->attachment !== '-')
                                                                        <a href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#imageModal{{ $long->id }}"
                                                                            class="d-inline-flex align-items-center gap-1 text-primary small">
                                                                            <i class="mdi mdi-image-outline fs-5"></i>
                                                                            Lihat
                                                                        </a>
                                                                        <div class="modal fade"
                                                                            id="imageModal{{ $long->id }}"
                                                                            tabindex="-1" aria-hidden="true">
                                                                            <div
                                                                                class="modal-dialog modal-dialog-centered">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title">Lampiran
                                                                                            Kristal — Sampel
                                                                                            {{ $loop->iteration }}</h5>
                                                                                        <button type="button"
                                                                                            class="btn-close"
                                                                                            data-bs-dismiss="modal"></button>
                                                                                    </div>
                                                                                    <div class="modal-body text-center">
                                                                                        <img src="{{ asset('storage/uploads/attachment_analisa/' . $long->attachment) }}"
                                                                                            alt="Lampiran Analisa"
                                                                                            class="img-fluid rounded">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted small">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $long->keterangan ?? '-' }}</td>
                                                                <td class="text-end">
                                                                    @if (strtolower($long->uji_kristal ?? '') === 'positif' && strtolower($long->disposisi ?? '') !== 'release')
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-warning"
                                                                            data-id="{{ $long->id }}"
                                                                            data-disposisi="{{ $long->disposisi }}"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#updateDisposisiModal">
                                                                            <i class="ri-edit-line"></i> Update
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted py-4">
                                                                    Belum ada data long term</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Garam / Gula --}}
                                <div class="col-lg-12">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0 fw-semibold">Hasil Analisa</h5>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped align-middle mb-0">
                                                    <thead class="table-light text-muted text-uppercase small">
                                                        <tr>
                                                            <th width="5%">#</th>
                                                            <th>Fisik</th>
                                                            <th>%KA</th>
                                                            <th>Kotoran</th>
                                                            <th>Organo</th>
                                                            <th>Warna</th>
                                                            <th>Aroma</th>
                                                            <th>%NaCl</th>
                                                            <th>Gross Weight</th>
                                                            <th>Disposisi</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($analisa_garam_gula as $gg)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $gg->fisik ?? '-' }}</td>
                                                                <td>{{ $gg->{'%ka'} ?? '-' }}</td>
                                                                <td>{{ $gg->kotoran ?? '-' }}</td>
                                                                <td>{{ $gg->organo ?? '-' }}</td>
                                                                <td>{{ $gg->warna ?? '-' }}</td>
                                                                <td>{{ $gg->aroma ?? '-' }}</td>
                                                                <td>{{ $gg->{'%nacl'} ?? '-' }}</td>
                                                                <td>{{ $gg->gross_weight ?? '-' }}</td>
                                                                <td>{{ $gg->disposisi ?? '-' }}</td>
                                                                <td>{{ $gg->keterangan ?? '-' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="10" class="text-center text-muted py-4">
                                                                    Belum ada data analisa</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>{{-- end .row --}}
                    </div>{{-- end #demo --}}
                </div>
            </div>

        </div>
    </div>

    {{-- Pilihan Sampling --}}
    <div class="modal fade" id="samplingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Kategori Sampling</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @php
                        $samplingComplete =
                            !is_null($data_mobil) &&
                            !is_null($data_dokumen) &&
                            !is_null($data_kemasan) &&
                            ($identitas->jenis === 'Garam' || !is_null($data_raw));
                    @endphp
                    @if ($samplingComplete)
                        <div class="alert alert-info text-center">Anda sudah mengisi semua sampling.</div>
                    @else
                        <p>Silakan pilih kategori sampling yang ingin Anda isi.</p>
                        <div class="list-group">
                            @if (is_null($data_mobil))
                                <button type="button" class="list-group-item list-group-item-action sampling-option"
                                    data-sampling="kondisi_mobil" data-bs-dismiss="modal">
                                    <i class="ri-truck-line me-2"></i> Sampling Kondisi Mobil
                                </button>
                            @endif
                            @if (is_null($data_dokumen))
                                <button type="button" class="list-group-item list-group-item-action sampling-option"
                                    data-sampling="kondisi_dokumen" data-bs-dismiss="modal">
                                    <i class="ri-file-text-line me-2"></i> Sampling Dokumen
                                </button>
                            @endif
                            @if (is_null($data_kemasan))
                                <button type="button" class="list-group-item list-group-item-action sampling-option"
                                    data-sampling="kondisi_kemasan" data-bs-dismiss="modal">
                                    <i class="ri-inbox-line me-2"></i> Sampling Kemasan
                                </button>
                            @endif
                            @if ($identitas->jenis !== 'Garam' && is_null($data_raw))
                                <button type="button" class="list-group-item list-group-item-action sampling-option"
                                    data-sampling="kondisi_raw" data-bs-dismiss="modal">
                                    <i class="ri-flask-line me-2"></i> Sampling Raw
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Sampling Kondisi Mobil --}}
    <div class="modal fade" id="modalKondisiMobil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sampling Kondisi Mobil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="form-sampling" id="form-kondisi-mobil">
                        @csrf
                        <input type="hidden" name="id_identitas" value="{{ $identitas->id }}">
                        @foreach (['bersih' => 'a. Bersih', 'kering' => 'b. Kering', 'benda_asing' => 'c. Tidak Ada Benda Asing', 'cacat' => 'd. Tidak Cacat / Bolong', 'segel' => 'e. Segel', 'berbau' => 'f. Tidak Berbau'] as $field => $label)
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">{{ $label }}</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}-yes" value="yes">
                                        <label class="form-check-label" for="{{ $field }}-yes">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}-no" value="no">
                                        <label class="form-check-label" for="{{ $field }}-no">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                        @endforeach
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Simpan Kondisi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Kondisi Mobil --}}
    <div class="modal fade" id="editModalMobil" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Kondisi Mobil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editMobilForm">
                        @csrf
                        <input type="hidden" name="id_mobil" value="{{ $data_mobil->id ?? null }}">
                        @foreach (['bersih' => 'a. Bersih', 'kering' => 'b. Kering', 'benda_asing' => 'c. Tidak Ada Benda Asing', 'cacat' => 'd. Tidak Cacat', 'segel' => 'e. Segel', 'berbau' => 'f. Berbau'] as $field => $label)
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">{{ $label }}</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_yes_edit" value="yes"
                                            {{ optional($data_mobil)->$field === 'yes' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}_yes_edit">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_no_edit" value="no"
                                            {{ optional($data_mobil)->$field === 'no' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}_no_edit">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                        @endforeach
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Kondisi Mobil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Sampling Dokumen --}}
    <div class="modal fade" id="modalDokumen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sampling Dokumen — {{ $identitas->jenis }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="form-sampling" id="form-dokumen">
                        @csrf
                        <input type="hidden" name="id_identitas" value="{{ $identitas->id }}">
                        @foreach (['coa' => 'a. CoA', 'surat_jalan' => 'b. Surat Jalan Vendor', 'packing_list' => 'c. Packing List', 'identitas_kemasan' => 'd. Identitas di Kemasan', 'logo_halal' => 'e. Logo Halal di Kemasan', 'kesesuaian_matriks_bahan' => 'f. Kesesuaian dengan Matriks Bahan Baku'] as $field => $label)
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">{{ $label }}</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_yes" value="yes">
                                        <label class="form-check-label" for="{{ $field }}_yes">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_no" value="no">
                                        <label class="form-check-label" for="{{ $field }}_no">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                        @endforeach
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Sampling</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Dokumen --}}
    <div class="modal fade" id="editModalDokumen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Dokumen Pendukung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editDokumenForm">
                        @csrf
                        <input type="hidden" name="id_dokumen" value="{{ $data_dokumen->id ?? '' }}">
                        @foreach (['coa' => 'a. CoA', 'surat_jalan' => 'b. Surat Jalan Vendor', 'packing_list' => 'c. Packing List', 'identitas_kemasan' => 'd. Identitas di Kemasan', 'logo_halal' => 'e. Logo Halal di Kemasan', 'kesesuaian_matriks_bahan' => 'f. Kesesuaian dengan Matriks Bahan Baku'] as $field => $label)
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">{{ $label }}</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_yes_edit" value="yes"
                                            {{ optional($data_dokumen)->$field === 'yes' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}_yes_edit">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_no_edit" value="no"
                                            {{ optional($data_dokumen)->$field === 'no' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}_no_edit">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                        @endforeach
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Sampling</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Sampling Kemasan --}}
    <div class="modal fade" id="modalKemasan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sampling Kemasan — {{ $identitas->jenis }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="form-sampling" id="form-kemasan">
                        @csrf
                        <input type="hidden" name="id_identitas" value="{{ $identitas->id }}">
                        @if ($identitas->jenis == 'Garam')
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0 fw-semibold">a. Kotor</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kotor" id="kotor-yes"
                                                value="yes">
                                            <label class="form-check-label" for="kotor-yes">Iya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kotor" id="kotor-no"
                                                value="no">
                                            <label class="form-check-label" for="kotor-no">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="zak-input-wrapper mt-2" style="display:none;">
                                    <label class="form-label small text-muted">Jumlah zak kotor?</label>
                                    <input type="number" class="form-control form-control-sm" name="keterangan_kotor"
                                        placeholder="Contoh: 5" min="0">
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">b. Berair</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="berair" id="berair-yes"
                                            value="yes">
                                        <label class="form-check-label" for="berair-yes">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="berair" id="berair-no"
                                            value="no">
                                        <label class="form-check-label" for="berair-no">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">c. Basah</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="basah" id="basah-yes"
                                            value="yes">
                                        <label class="form-check-label" for="basah-yes">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="basah" id="basah-no"
                                            value="no">
                                        <label class="form-check-label" for="basah-no">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">d. Campuran</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="campuran" id="campuran-yes"
                                            value="yes">
                                        <label class="form-check-label" for="campuran-yes">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="campuran" id="campuran-no"
                                            value="no">
                                        <label class="form-check-label" for="campuran-no">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0 fw-semibold">e. Rusak / Sobek</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rusak" id="rusak-yes"
                                                value="yes">
                                            <label class="form-check-label" for="rusak-yes">Iya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rusak" id="rusak-no"
                                                value="no">
                                            <label class="form-check-label" for="rusak-no">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="zak-input-wrapper mt-2" style="display:none;">
                                    <label class="form-label small text-muted">Jumlah zak rusak / sobek?</label>
                                    <input type="number" class="form-control form-control-sm" name="keterangan_rusak"
                                        placeholder="Contoh: 5" min="0">
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0 fw-semibold">f. Sesuai STD</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sesuai_std"
                                                id="sesuai_std-yes" value="yes">
                                            <label class="form-check-label" for="sesuai_std-yes">Iya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sesuai_std"
                                                id="sesuai_std-no" value="no">
                                            <label class="form-check-label" for="sesuai_std-no">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="zak-input-wrapper mt-2" style="display:none;">
                                    <label class="form-label small text-muted">Jumlah zak tidak standar?</label>
                                    <input type="number" class="form-control form-control-sm"
                                        name="keterangan_sesuai_std" placeholder="Contoh: 5" min="0">
                                </div>
                            </div>
                        @else
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0 fw-semibold">a. Kotor</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kotor" id="kotor-yes"
                                                value="yes">
                                            <label class="form-check-label" for="kotor-yes">Iya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kotor" id="kotor-no"
                                                value="no">
                                            <label class="form-check-label" for="kotor-no">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="zak-input-wrapper mt-2" style="display:none;">
                                    <label class="form-label small text-muted">Jumlah zak kotor?</label>
                                    <input type="number" class="form-control form-control-sm" name="keterangan_kotor"
                                        placeholder="Contoh: 5" min="0">
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0 fw-semibold">b. Rusak / Sobek</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rusak" id="rusak-yes"
                                                value="yes">
                                            <label class="form-check-label" for="rusak-yes">Iya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rusak" id="rusak-no"
                                                value="no">
                                            <label class="form-check-label" for="rusak-no">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="zak-input-wrapper mt-2" style="display:none;">
                                    <label class="form-label small text-muted">Jumlah zak rusak / sobek?</label>
                                    <input type="number" class="form-control form-control-sm" name="keterangan_rusak"
                                        placeholder="Contoh: 5" min="0">
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0 fw-semibold">c. Sesuai STD</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sesuai_std"
                                                id="sesuai_std-yes" value="yes">
                                            <label class="form-check-label" for="sesuai_std-yes">Iya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="sesuai_std"
                                                id="sesuai_std-no" value="no">
                                            <label class="form-check-label" for="sesuai_std-no">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="zak-input-wrapper mt-2" style="display:none;">
                                    <label class="form-label small text-muted">Jumlah zak tidak standar?</label>
                                    <input type="number" class="form-control form-control-sm"
                                        name="keterangan_sesuai_std" placeholder="Contoh: 5" min="0">
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">d. Lain-lain</label>
                                <input class="form-control w-50" type="text" name="lain_lain">
                            </div>
                        @endif
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Simpan Sampling</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Kemasan --}}
    <div class="modal fade" id="editModalKemasan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Sample Fisik Kemasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editKemasanForm">
                        @csrf
                        <input type="hidden" name="id_kemasan" value="{{ $data_kemasan->id ?? null }}">
                        @php
                            $modalFields =
                                $identitas->jenis === 'Garam'
                                    ? [
                                        'kotor' => 'a. Kotor',
                                        'berair' => 'b. Berair',
                                        'basah' => 'c. Basah',
                                        'campuran' => 'd. Campuran',
                                        'rusak' => 'e. Rusak / Robek',
                                        'sesuai_std' => 'f. Sesuai STD',
                                    ]
                                    : [
                                        'kotor' => 'a. Kotor',
                                        'rusak' => 'b. Rusak / Sobek',
                                        'sesuai_std' => 'c. Sesuai STD',
                                    ];
                        @endphp
                        @foreach ($modalFields as $field => $label)
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">{{ $label }}</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_yes_edit" value="yes"
                                            {{ optional($data_kemasan)->$field === 'yes' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}_yes_edit">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_no_edit" value="no"
                                            {{ optional($data_kemasan)->$field === 'no' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}_no_edit">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                        @endforeach
                        <div class="mb-3">
                            <label
                                class="form-label fw-semibold">{{ $identitas->jenis === 'Garam' ? 'g. Lain-lain' : 'd. Lain-lain' }}</label>
                            <input type="text" class="form-control" name="lain_lain"
                                value="{{ $data_kemasan->lain_lain ?? '' }}" placeholder="Tuliskan keterangan lain...">
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Sample Fisik</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Sampling Raw --}}
    <div class="modal fade" id="modalRaw" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sampling Raw — {{ $identitas->jenis }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="form-sampling" id="form-raw">
                        @csrf
                        <input type="hidden" name="id_identitas" value="{{ $identitas->id }}">
                        @foreach (['leleh' => 'a. Leleh', 'warna' => 'b. Warna tidak sesuai STD', 'campuran' => 'c. Campuran', 'aroma' => 'd. Aroma tidak STD', 'sesuai_std' => 'e. Sesuai STD'] as $field => $label)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label mb-0 fw-semibold">{{ $label }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="{{ $field }}"
                                                id="{{ $field }}-yes" value="yes">
                                            <label class="form-check-label" for="{{ $field }}-yes">Iya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="{{ $field }}"
                                                id="{{ $field }}-no" value="no">
                                            <label class="form-check-label" for="{{ $field }}-no">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="zak-input-wrapper mt-2" style="display:none;">
                                    <label class="form-label small text-muted">Jumlah zak?</label>
                                    <input type="number" class="form-control form-control-sm"
                                        name="keterangan_{{ $field }}" placeholder="Contoh: 5" min="0">
                                </div>
                            </div>
                            <hr class="my-2">
                        @endforeach
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Simpan Sampling</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Raw --}}
    <div class="modal fade" id="editModalRaw" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Sample Fisik Raw</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editRawForm">
                        @csrf
                        <input type="hidden" name="id_raw" value="{{ $data_raw->id ?? null }}">
                        @foreach (['leleh' => 'a. Leleh', 'warna' => 'b. Warna Sesuai Standar', 'aroma' => 'c. Aroma Tidak Sesuai Standar', 'sesuai_std_raw' => 'd. Sesuai Standar', 'campuran_raw' => 'e. Campuran'] as $field => $label)
                            @php
                                $fieldName = str_replace('_raw', '', $field);
                                $value = optional($data_raw)->$fieldName;
                            @endphp
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0 fw-semibold">{{ $label }}</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_yes" value="yes"
                                            {{ $value === 'yes' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}_yes">Iya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}"
                                            id="{{ $field }}_no" value="no"
                                            {{ $value === 'no' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $field }}_no">Tidak</label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                        @endforeach
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Sample Fisik Raw</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Konfirmasi Jam --}}
    <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Jam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="jamInput" class="form-label" id="labelJam">Jam Kedatangan</label>
                        <input type="datetime-local" class="form-control" id="jamInput"
                            value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" id="btnSimpanJam" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Update Disposisi Long Term --}}
    <div class="modal fade" id="updateDisposisiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formUpdateDisposisi">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Disposisi Long Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_analisa" id="disposisi_id">
                        <div class="mb-3">
                            <label for="disposisi_new" class="form-label">Disposisi Baru</label>
                            <select name="disposisi" id="disposisi_new" class="form-select">
                                <option value="">-- Pilih Disposisi --</option>
                                <option value="Release">Release</option>
                                <option value="Reject">Reject</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ── Routing pilihan sampling ──
            $('.sampling-option').on('click', function() {
                const map = {
                    kondisi_mobil: 'modalKondisiMobil',
                    kondisi_dokumen: 'modalDokumen',
                    kondisi_kemasan: 'modalKemasan',
                    kondisi_raw: 'modalRaw',
                };
                const id = map[$(this).data('sampling')];
                if (id) setTimeout(() => new bootstrap.Modal(document.getElementById(id)).show(), 400);
            });

            // ── Zak input toggle ──
            $('form.form-sampling input[type=radio]').on('change', function() {
                const $parent = $(this).closest('.mb-3');
                const $wrapper = $parent.find('.zak-input-wrapper');
                $wrapper.hide().find('input[type=number]').val('');
                const name = $(this).attr('name');
                if (name === 'sesuai_std') {
                    if ($(this).val() === 'no') $wrapper.show();
                } else {
                    if ($(this).val() === 'yes') $wrapper.show();
                }
            });

            // ── Generic sampling submit ──
            const samplingUrls = {
                'form-kondisi-mobil': "{{ route('sampling-kondisi-mobil.store') }}",
                'form-dokumen': "{{ route('sampling-dokumen.store') }}",
                'form-kemasan': "{{ route('sampling-kemasan.store') }}",
                'form-raw': "{{ route('sampling-raw.store') }}",
            };

            $('.form-sampling').on('submit', function(e) {
                e.preventDefault();
                const url = samplingUrls[this.id];
                if (!url) return;
                $.ajax({
                    type: 'POST',
                    url,
                    data: $(this).serialize(),
                    success: r => {
                        $(this).closest('.modal').modal('hide');
                        Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: r.message || 'Data berhasil disimpan!'
                            })
                            .then(() => location.reload());
                    },
                    error: xhr => {
                        if (xhr.status === 422) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi Gagal',
                                html: Object.values(xhr.responseJSON.errors).map(m =>
                                    `• ${m.join('<br>')}`).join('\n')
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                            });
                        }
                    },
                });
            });

            // ── Edit Mobil ──
            $('#editMobilForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('sampling-kondisi-mobil.update') }}",
                    method: 'POST',
                    data: {
                        id: $('[name="id_mobil"]').val(),
                        bersih: $('[name="bersih"]:checked').val(),
                        kering: $('[name="kering"]:checked').val(),
                        benda_asing: $('[name="benda_asing"]:checked').val(),
                        cacat: $('[name="cacat"]:checked').val(),
                        segel: $('[name="segel"]:checked').val(),
                        berbau: $('[name="berbau"]:checked').val(),
                    },
                    success: () => {
                        $('#editModalMobil').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data mobil berhasil diperbarui'
                        }).then(() => location.reload());
                    },
                    error: xhr => Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                    }),
                });
            });

            // ── Edit Dokumen ──
            $('#editDokumenForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('sampling-dokumen.update') }}",
                    data: {
                        id: $('[name="id_dokumen"]').val(),
                        coa: $('[name="coa"]:checked').val(),
                        surat_jalan: $('[name="surat_jalan"]:checked').val(),
                        packing_list: $('[name="packing_list"]:checked').val(),
                        identitas_kemasan: $('[name="identitas_kemasan"]:checked').val(),
                        logo_halal: $('[name="logo_halal"]:checked').val(),
                        kesesuaian_matriks_bahan: $('[name="kesesuaian_matriks_bahan"]:checked')
                            .val(),
                    },
                    success: r => {
                        $('#editModalDokumen').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: r.message || 'Data berhasil disimpan!'
                        }).then(() => location.reload());
                    },
                    error: xhr => Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                    }),
                });
            });

            // ── Edit Kemasan ──
            $('#editKemasanForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('sampling-kemasan.update') }}",
                    data: {
                        id: $('[name="id_kemasan"]').val(),
                        kotor: $('[name="kotor"]:checked').val(),
                        rusak: $('[name="rusak"]:checked').val(),
                        sesuai_std: $('[name="sesuai_std"]:checked').val(),
                        lain_lain: $('[name="lain_lain"]').val(),
                        berair: $('[name="berair"]:checked').val(),
                        basah: $('[name="basah"]:checked').val(),
                        campuran: $('[name="campuran"]:checked').val(),
                    },
                    success: r => {
                        $('#editModalKemasan').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: r.message || 'Data berhasil disimpan!'
                        }).then(() => location.reload());
                    },
                    error: xhr => Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                    }),
                });
            });

            // ── Edit Raw ──
            $('#editRawForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('sampling-raw.update') }}",
                    data: {
                        id: $('[name="id_raw"]').val(),
                        leleh: $('[name="leleh"]:checked').val(),
                        warna: $('[name="warna"]:checked').val(),
                        campuran: $('[name="campuran_raw"]:checked').val(),
                        aroma: $('[name="aroma"]:checked').val(),
                        sesuai_std: $('[name="sesuai_std_raw"]:checked').val(),
                    },
                    success: r => {
                        $('#editModalRaw').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: r.message || 'Data berhasil disimpan!'
                        }).then(() => location.reload());
                    },
                    error: xhr => Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan!'
                    }),
                });
            });

            // ── Konfirmasi Jam ──
            KonfirmasiInitialCheck();
            $('#btnBukaModalKonfirmasi').on('click', KonfirmasiCheckAndShow);
            $('#btnSimpanJam').on('click', handleKonfirmasiJam);

            function KonfirmasiInitialCheck() {
                $.ajax({
                    url: "{{ url('rmpm/konfirmasi/' . $identitas->id) }}",
                    type: 'GET',
                    dataType: 'json',
                    success: r => {
                        if (r.jam_analisa_exists) {
                            $('#labelJam').text('Jam Analisa (Sudah Terisi)');
                            $('#jamInput').val(r.jam_analisa);
                            $('#btnSimpanJam').hide();
                        } else if (r.sampling_complete) {
                            $('#labelJam').text('Jam Analisa');
                        } else {
                            $('#labelJam').text('Jam Kedatangan');
                        }
                    },
                });
            }

            function KonfirmasiCheckAndShow() {
                $.ajax({
                    url: "{{ url('rmpm/konfirmasi/' . $identitas->id) }}",
                    type: 'GET',
                    dataType: 'json',
                    success: r => {
                        if (r.jam_analisa_exists) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Selesai',
                                text: 'Konfirmasi Jam Analisa sudah terisi.'
                            });
                            KonfirmasiInitialCheck();
                            $('#modalKonfirmasi').modal('show');
                            $('#btnSimpanJam').hide();
                        } else if (r.sampling_complete) {
                            $('#labelJam').text('Jam Analisa');
                            $('#jamInput').val('{{ now()->format('Y-m-d\TH:i') }}');
                            $('#btnSimpanJam').show();
                            $('#modalKonfirmasi').modal('show');
                        } else {
                            $('#labelJam').text('Jam Kedatangan');
                            $('#jamInput').val('{{ now()->format('Y-m-d\TH:i') }}');
                            $('#btnSimpanJam').show();
                            $('#modalKonfirmasi').modal('show');
                        }
                    },
                    error: () => Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat status konfirmasi.'
                    }),
                });
            }

            function handleKonfirmasiJam() {
                $.ajax({
                    url: "{{ route('rmpm.konfirmasi.update') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: "{{ $identitas->id }}",
                        tipe: $('#labelJam').text().includes('Analisa') ? 'analisa' : 'kedatangan',
                        jam: $('#jamInput').val(),
                    },
                    success: r => {
                        $('#modalKonfirmasi').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: r.message || 'Data berhasil disimpan!'
                        }).then(() => location.reload());
                    },
                    error: xhr => Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Gagal menyimpan jam.'
                    }),
                });
            }

            // ── Update Disposisi Long Term ──
            $('#updateDisposisiModal').on('show.bs.modal', function(e) {
                const btn = $(e.relatedTarget);
                $('#disposisi_id').val(btn.data('id'));
                $('#disposisi_new').val(btn.data('disposisi')?.toLowerCase() || '');
            });

            $('#formUpdateDisposisi').on('submit', function(e) {
                e.preventDefault();
                const disposisiBaru = $('#disposisi_new').val();
                if (!disposisiBaru) return Swal.fire({
                    icon: 'warning',
                    text: 'Silakan pilih disposisi baru.'
                });
                $.ajax({
                    url: "{{ route('rmpm.update-disposisi.long-term') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: $('#disposisi_id').val(),
                        disposisi: disposisiBaru
                    },
                    success: r => {
                        $('#updateDisposisiModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: r.message || 'Disposisi berhasil diperbarui!'
                        }).then(() => location.reload());
                    },
                    error: xhr => Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                    }),
                });
            });
        });
    </script>
@endsection
