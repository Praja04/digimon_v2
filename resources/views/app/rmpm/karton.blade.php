@extends('layouts.component.main')

@section('title')
    Karton
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
                            Karton
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Pilih menu Karton yang akan digunakan.
                        </p>
                    </div>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.index') }}">
                                    RMPM
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.pm') }}">
                                    Packaging Material
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Karton
                            </li>

                        </ol>
                    </div>

                </div>

            </div>
        </div>

        {{-- HEADER --}}
        <div class="karton-header mb-3">

            <div class="karton-header-icon">
                <i class="mdi mdi-package-variant-closed"></i>
            </div>

            <div class="flex-grow-1">

                <span class="karton-label">
                    PACKAGING MATERIAL
                </span>

                <h3 class="mb-1 text-white">
                    Karton
                </h3>

                <p class="mb-0 karton-description">
                    Kelola daftar incoming dan proses pemeriksaan Karton.
                </p>

            </div>

            <a
                href="{{ route('rmpm.pm') }}"
                class="btn btn-light px-3"
            >
                <i class="mdi mdi-arrow-left me-1"></i>
                Kembali
            </a>

        </div>

        {{-- PILIHAN MENU --}}
        <div class="row g-3">

            {{-- MENU KARTON --}}
            <div class="col-xl-6 col-lg-6 col-md-6">

                <a
                    href="{{ route('rmpm.pm.karton') }}"
                    class="karton-menu-card karton-menu-orange"
                >

                    <div class="karton-menu-top">

                        <span class="karton-step">
                            PILIHAN 1
                        </span>

                        <i class="mdi mdi-arrow-right karton-arrow"></i>

                    </div>

                    <div class="karton-menu-body">

                        <div class="karton-menu-icon">
                            <i class="mdi mdi-format-list-bulleted-square"></i>
                        </div>

                        <div class="karton-menu-content">

                            <h4>
                                Menu Karton
                            </h4>

                            <p>
                                Menampilkan daftar SPB Karton,
                                jenis material, dan status sampling.
                            </p>

                        </div>

                    </div>

                    <div class="karton-menu-footer">

                        <span>
                            Buka Menu Karton
                        </span>

                        <i class="mdi mdi-arrow-right"></i>

                    </div>

                </a>

            </div>

            {{-- DISPLAY KARTON --}}
            <div class="col-xl-6 col-lg-6 col-md-6">

                <a
                    href="{{ route('rmpm.pm.karton.display') }}"
                    class="karton-menu-card karton-menu-green"
                >

                    <div class="karton-menu-top">

                        <span class="karton-step">
                            PILIHAN 2
                        </span>

                        <i class="mdi mdi-arrow-right karton-arrow"></i>

                    </div>

                    <div class="karton-menu-body">

                        <div class="karton-menu-icon">
                            <i class="mdi mdi-view-dashboard-outline"></i>
                        </div>

                        <div class="karton-menu-content">

                            <h4>
                                Display Karton
                            </h4>

                            <p>
                                Pilih pemeriksaan BCT, Berat,
                                atau Kondisi Fisik Karton.
                            </p>

                        </div>

                    </div>

                    <div class="karton-menu-footer">

                        <span>
                            Buka Display Karton
                        </span>

                        <i class="mdi mdi-arrow-right"></i>

                    </div>

                </a>

            </div>

        </div>

    </div>
</div>

@endsection

@section('styles')

<style>
    .karton-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 17px 20px;
        border-radius: 15px;
        background: linear-gradient(
            135deg,
            #f97316,
            #ea580c
        );
        box-shadow: 0 8px 20px rgba(234, 88, 12, 0.18);
    }

    .karton-header-icon {
        width: 58px;
        height: 58px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        font-size: 31px;
    }

    .karton-label {
        display: block;
        margin-bottom: 2px;
        color: rgba(255, 255, 255, 0.82);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .karton-description {
        color: rgba(255, 255, 255, 0.86);
        font-size: 13px;
    }

    .karton-menu-card {
        position: relative;
        min-height: 220px;
        height: 100%;
        display: flex;
        flex-direction: column;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-top: 4px solid;
        border-radius: 16px;
        background: #ffffff;
        color: #1e293b;
        text-decoration: none;
        box-shadow: 0 7px 18px rgba(15, 23, 42, 0.07);
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            border-color 0.2s ease;
    }

    .karton-menu-card:hover {
        color: #1e293b;
        transform: translateY(-4px);
        box-shadow: 0 13px 26px rgba(15, 23, 42, 0.11);
    }

    .karton-menu-orange {
        border-top-color: #f97316;
    }

    .karton-menu-green {
        border-top-color: #65a30d;
    }

    .karton-menu-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .karton-step {
        padding: 5px 11px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 10px;
        font-weight: 700;
    }

    .karton-arrow {
        color: #94a3b8;
        font-size: 22px;
    }

    .karton-menu-body {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-grow: 1;
    }

    .karton-menu-icon {
        width: 64px;
        height: 64px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        font-size: 32px;
    }

    .karton-menu-orange .karton-menu-icon {
        background: #fff7ed;
        color: #f97316;
    }

    .karton-menu-green .karton-menu-icon {
        background: #f7fee7;
        color: #65a30d;
    }

    .karton-menu-content {
        min-width: 0;
    }

    .karton-menu-content h4 {
        margin-bottom: 6px;
        color: #334155;
        font-size: 20px;
        font-weight: 600;
    }

    .karton-menu-content p {
        margin-bottom: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.55;
    }

    .karton-menu-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 18px;
        padding-top: 13px;
        border-top: 1px solid #e2e8f0;
        color: #4f46e5;
        font-size: 13px;
        font-weight: 700;
    }

    .karton-menu-footer i {
        font-size: 18px;
    }

    @media (max-width: 767.98px) {
        .karton-header {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .karton-header .btn {
            width: 100%;
        }

        .karton-menu-card {
            min-height: 210px;
        }

        .karton-menu-body {
            align-items: flex-start;
        }
    }

    @media (max-width: 575.98px) {
        .karton-menu-body {
            gap: 13px;
        }

        .karton-menu-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            font-size: 28px;
        }

        .karton-menu-content h4 {
            font-size: 18px;
        }
    }
</style>

@endsection
