@extends('layouts.component.main')

@section('title')
    Packaging Material
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                    <div>
                        <h4 class="mb-sm-0">
                            Packaging Material
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Pilih proses Packaging Material yang akan dikerjakan.
                        </p>
                    </div>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.index') }}">
                                    RMPM
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                PM
                            </li>

                        </ol>
                    </div>

                </div>

            </div>
        </div>

        {{-- HEADER PM --}}
        <div class="row mb-4">
            <div class="col-12">

                <div class="card border-0 shadow-sm overflow-hidden pm-header-card">

                    <div class="card-body p-4">

                        <div class="d-flex align-items-center">

                            <div class="pm-header-icon">
                                <i class="mdi mdi-package-variant-closed"></i>
                            </div>

                            <div class="ms-4">

                                <span class="pm-header-label">
                                    PACKAGING MATERIAL
                                </span>

                                <h2 class="text-white mb-1">
                                    PM
                                </h2>

                                <p class="text-white-50 mb-0">
                                    Input incoming dan proses sampling kemasan.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- EMPAT MENU PM --}}
        <div class="row g-4">

            {{-- INPUT INCOMING --}}
            <div class="col-xl-3 col-lg-6 col-md-6">

                <a
                    href="{{ route('rmpm.pm.incoming.create') }}"
                    class="text-decoration-none"
                >
                    <div class="card border-0 shadow-sm h-100 pm-menu-card incoming-card">

                        <div class="card-body p-4">

                            <div class="d-flex align-items-start justify-content-between mb-4">

                                <span class="step-badge">
                                    Tahap 1
                                </span>

                                <i class="mdi mdi-arrow-right menu-arrow"></i>

                            </div>

                            <div class="menu-icon incoming-icon">
                                <i class="mdi mdi-cloud-upload-outline"></i>
                            </div>

                            <h4 class="text-dark mt-4 mb-2">
                                Input Incoming
                            </h4>

                            <p class="text-muted mb-0">
                                Buat data kedatangan dan sampel Packaging Material.
                            </p>

                        </div>

                    </div>
                </a>

            </div>

            {{-- INNER / OUTER --}}
            <div class="col-xl-3 col-lg-6 col-md-6">

                <a
                    href="{{ route('rmpm.pm.inner-outer') }}"
                    class="text-decoration-none"
                >
                    <div class="card border-0 shadow-sm h-100 pm-menu-card inner-card">

                        <div class="card-body p-4">

                            <div class="d-flex align-items-start justify-content-between mb-4">

                                <span class="step-badge">
                                    Tahap 2
                                </span>

                                <i class="mdi mdi-arrow-right menu-arrow"></i>

                            </div>

                            <div class="menu-icon inner-icon">
                                <i class="mdi mdi-chart-donut"></i>
                            </div>

                            <h4 class="text-dark mt-4 mb-2">
                                Inner / Outer
                            </h4>

                            <p class="text-muted mb-0">
                                Pemeriksaan material film, roll, inner, dan outer.
                            </p>

                        </div>

                    </div>
                </a>

            </div>

            {{-- KARTON --}}
            <div class="col-xl-3 col-lg-6 col-md-6">

                <a
                    href="{{ route('rmpm.pm.karton') }}"
                    class="text-decoration-none"
                >
                    <div class="card border-0 shadow-sm h-100 pm-menu-card karton-card">

                        <div class="card-body p-4">

                            <div class="d-flex align-items-start justify-content-between mb-4">

                                <span class="step-badge">
                                    Tahap 3
                                </span>

                                <i class="mdi mdi-arrow-right menu-arrow"></i>

                            </div>

                            <div class="menu-icon karton-icon">
                                <i class="mdi mdi-package-variant-closed"></i>
                            </div>

                            <h4 class="text-dark mt-4 mb-2">
                                Karton
                            </h4>

                            <p class="text-muted mb-0">
                                Pemeriksaan dimensi, visual, gramasi, dan BCT.
                            </p>

                        </div>

                    </div>
                </a>

            </div>

            {{-- POUCH --}}
            <div class="col-xl-3 col-lg-6 col-md-6">

                <a
                    href="{{ route('rmpm.pm.pouch') }}"
                    class="text-decoration-none"
                >
                    <div class="card border-0 shadow-sm h-100 pm-menu-card pouch-card">

                        <div class="card-body p-4">

                            <div class="d-flex align-items-start justify-content-between mb-4">

                                <span class="step-badge">
                                    Tahap 4
                                </span>

                                <i class="mdi mdi-arrow-right menu-arrow"></i>

                            </div>

                            <div class="menu-icon pouch-icon">
                                <i class="mdi mdi-package-variant"></i>
                            </div>

                            <h4 class="text-dark mt-4 mb-2">
                                Pouch
                            </h4>

                            <p class="text-muted mb-0">
                                Pemeriksaan ukuran, seal, ketebalan, dan visual.
                            </p>

                        </div>

                    </div>
                </a>

            </div>

        </div>

        {{-- BACK --}}
        <div class="row mt-4">
            <div class="col-12 text-end">

                <a
                    href="{{ route('rmpm.index') }}"
                    class="btn btn-light"
                >
                    <i class="mdi mdi-arrow-left me-1"></i>
                    Kembali ke RMPM
                </a>

            </div>
        </div>

    </div>
</div>

@endsection

@section('styles')

<style>
    .pm-header-card {
        border-radius: 18px;
        background: linear-gradient(
            135deg,
            #16b89f 0%,
            #0d927d 100%
        );
    }

    .pm-header-icon {
        width: 78px;
        height: 78px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        font-size: 42px;
    }

    .pm-header-label {
        display: block;
        margin-bottom: 3px;
        color: rgba(255, 255, 255, 0.75);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .pm-menu-card {
        min-height: 260px;
        border-radius: 18px;
        overflow: hidden;
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease;
    }

    .pm-menu-card:hover {
        transform: translateY(-7px);
        box-shadow:
            0 18px 38px rgba(15, 23, 42, 0.15) !important;
    }

    .incoming-card {
        border-top: 5px solid #14b8a6 !important;
    }

    .inner-card {
        border-top: 5px solid #f97316 !important;
    }

    .karton-card {
        border-top: 5px solid #65a30d !important;
    }

    .pouch-card {
        border-top: 5px solid #06b6d4 !important;
    }

    .step-badge {
        padding: 6px 12px;
        border-radius: 50px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .menu-arrow {
        color: #94a3b8;
        font-size: 25px;
        transition:
            color 0.2s ease,
            transform 0.2s ease;
    }

    .pm-menu-card:hover .menu-arrow {
        color: #4f46e5;
        transform: translateX(5px);
    }

    .menu-icon {
        width: 76px;
        height: 76px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 19px;
        font-size: 39px;
    }

    .incoming-icon {
        background: #ecfdf5;
        color: #14b8a6;
    }

    .inner-icon {
        background: #fff7ed;
        color: #f97316;
    }

    .karton-icon {
        background: #f7fee7;
        color: #65a30d;
    }

    .pouch-icon {
        background: #ecfeff;
        color: #0891b2;
    }

    @media (max-width: 575.98px) {
        .pm-header-icon {
            width: 64px;
            height: 64px;
            font-size: 34px;
        }

        .pm-menu-card {
            min-height: auto;
        }
    }
</style>

@endsection