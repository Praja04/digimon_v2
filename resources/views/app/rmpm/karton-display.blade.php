@extends('layouts.component.main')

@section('title')
    Display Karton
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-sm-0">
                            Display Karton
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Pilih jenis pemeriksaan untuk SPB
                            <strong>{{ $packagingIncoming->no_spb }}</strong>.
                        </p>
                    </div>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.pm') }}">
                                    Packaging Material
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.pm.karton') }}">
                                    Karton
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Display Karton
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="selected-karton-card mb-4">
            <div class="selected-karton-icon">
                <i class="mdi mdi-package-variant-closed"></i>
            </div>

            <div class="selected-karton-data">
                <span>Data Karton Dipilih</span>

                <h5>{{ $packagingIncoming->no_spb }}</h5>

                <p>
                    {{ $packagingIncoming->jenisIncoming?->nama ?? 'Karton' }}
                    &nbsp;•&nbsp;
                    {{ $packagingIncoming->jenisMaterial?->nama ?? '-' }}
                </p>
            </div>

            <a
                href="{{ route('rmpm.pm.karton') }}"
                class="btn btn-primary px-3"
            >
                <i class="mdi mdi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

        <div class="row g-3">

            <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="display-card display-orange">
                    <div class="display-card-top">
                        <div class="display-icon">
                            <i class="mdi mdi-arrow-collapse-vertical"></i>
                        </div>

                        <span class="display-number">1</span>
                    </div>

                    <div class="display-content">
                        <h4>BCT</h4>

                        <p>
                            Pemeriksaan kekuatan tekan Karton
                            menggunakan metode Box Compression Test.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'rmpm.pm.karton.bct',
                            $packagingIncoming
                        ) }}"
                        class="btn btn-primary w-100"
                    >
                        <i class="mdi mdi-arrow-right-circle-outline me-1"></i>
                        Buka Pemeriksaan BCT
                    </a>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="display-card display-yellow">
                    <div class="display-card-top">
                        <div class="display-icon">
                            <i class="mdi mdi-scale-balance"></i>
                        </div>

                        <span class="display-number">2</span>
                    </div>

                    <div class="display-content">
                        <h4>Berat</h4>

                        <p>
                            Pemeriksaan berat dan gramasi material
                            Karton berdasarkan standar.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'rmpm.pm.karton.sampling',
                            $packagingIncoming
                        ) }}"
                        class="btn btn-primary w-100"
                    >
                        <i class="mdi mdi-arrow-right-circle-outline me-1"></i>
                        Buka Pemeriksaan Berat
                    </a>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="display-card display-green">
                    <div class="display-card-top">
                        <div class="display-icon">
                            <i class="mdi mdi-ruler-square"></i>
                        </div>

                        <span class="display-number">3</span>
                    </div>

                    <div class="display-content">
                        <h4>Kondisi Fisik</h4>

                        <p>
                            Pemeriksaan dimensi, visual,
                            panjang, lebar, dan tinggi Karton.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="btn btn-primary w-100"
                        disabled
                    >
                        Buka Kondisi Fisik
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection

@section('styles')

<style>
    .selected-karton-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 7px 20px rgba(15, 23, 42, 0.07);
    }

    .selected-karton-icon {
        width: 54px;
        height: 54px;
        flex: 0 0 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #fff7ed;
        color: #f97316;
        font-size: 28px;
    }

    .selected-karton-data {
        min-width: 0;
        flex: 1 1 auto;
    }

    .selected-karton-data span {
        display: block;
        margin-bottom: 2px;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .selected-karton-data h5 {
        margin: 0 0 2px;
        color: #1e293b;
        font-size: 18px;
        font-weight: 700;
    }

    .selected-karton-data p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    .display-card {
        min-height: 245px;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-top: 4px solid;
        border-radius: 17px;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.07);
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease;
    }

    .display-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 27px rgba(15, 23, 42, 0.11);
    }

    .display-orange {
        border-top-color: #f97316;
    }

    .display-yellow {
        border-top-color: #eab308;
    }

    .display-green {
        border-top-color: #65a30d;
    }

    .display-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .display-number {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #ef4444;
        color: #ffffff;
        font-size: 15px;
        font-weight: 700;
    }

    .display-icon {
        width: 68px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 17px;
        font-size: 35px;
    }

    .display-orange .display-icon {
        background: #fff7ed;
        color: #f97316;
    }

    .display-yellow .display-icon {
        background: #fefce8;
        color: #ca8a04;
    }

    .display-green .display-icon {
        background: #f7fee7;
        color: #65a30d;
    }

    .display-content {
        flex-grow: 1;
    }

    .display-card h4 {
        margin-bottom: 7px;
        color: #334155;
        font-size: 20px;
        font-weight: 600;
    }

    .display-card p {
        margin-bottom: 18px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.55;
    }

    .display-card .btn {
        min-height: 40px;
        font-size: 13px;
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        .selected-karton-card {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .selected-karton-card .btn {
            width: 100%;
        }

        .display-card {
            min-height: 220px;
        }
    }
</style>

@endsection