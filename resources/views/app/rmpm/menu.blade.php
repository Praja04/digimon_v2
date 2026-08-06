@extends('layouts.component.main')

@section('title')
    RMPM
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
                            RMPM
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Pilih modul Raw Material atau Packaging Material.
                        </p>
                    </div>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">
                                    Menu
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                RMPM
                            </li>

                        </ol>
                    </div>

                </div>

            </div>
        </div>

        {{-- PILIHAN RM DAN PM --}}
        <div class="row g-4">

            {{-- RAW MATERIAL --}}
            <div class="col-xl-6 col-lg-6 col-md-12">

                <div class="card border-0 shadow-sm h-100 overflow-hidden module-card">

                    <div class="module-header rm-header">

                        <div class="d-flex align-items-center">

                            <div class="module-icon">
                                <i class="mdi mdi-flask-outline"></i>
                            </div>

                            <div class="flex-grow-1 ms-4">

                                <span class="module-category">
                                    RAW MATERIAL
                                </span>

                                <h2 class="text-white mb-1">
                                    RM
                                </h2>

                                <p class="text-white-50 mb-0">
                                    Raw Material
                                </p>

                                <small class="text-white-50">
                                    Data incoming dan analisa bahan baku.
                                </small>

                            </div>

                        </div>

                    </div>

                    <div class="card-body p-4">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <div>
                                <span class="badge bg-primary-subtle text-primary me-2">
                                    Incoming
                                </span>

                                <span class="badge bg-primary-subtle text-primary">
                                    Analisa
                                </span>
                            </div>

                            <a
                                href="{{ route('rmpm.rm') }}"
                                class="btn btn-primary rounded-pill px-4"
                            >
                                Buka Raw Material

                                <i class="mdi mdi-arrow-right ms-1"></i>
                            </a>

                        </div>

                    </div>

                </div>

            </div>

            {{-- PACKAGING MATERIAL --}}
            <div class="col-xl-6 col-lg-6 col-md-12">

                <div class="card border-0 shadow-sm h-100 overflow-hidden module-card">

                    <div class="module-header pm-header">

                        <div class="d-flex align-items-center">

                            <div class="module-icon">
                                <i class="mdi mdi-package-variant-closed"></i>
                            </div>

                            <div class="flex-grow-1 ms-4">

                                <span class="module-category">
                                    PACKAGING MATERIAL
                                </span>

                                <h2 class="text-white mb-1">
                                    PM
                                </h2>

                                <p class="text-white-50 mb-0">
                                    Packaging Material
                                </p>

                                <small class="text-white-50">
                                    Data incoming dan sampling kemasan.
                                </small>

                            </div>

                        </div>

                    </div>

                    <div class="card-body p-4">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <div>
                                <span class="badge bg-success-subtle text-success me-2">
                                    Incoming
                                </span>

                                <span class="badge bg-success-subtle text-success">
                                    Sampling
                                </span>
                            </div>

                            <a
                                href="{{ route('rmpm.pm') }}"
                                class="btn btn-success rounded-pill px-4"
                            >
                                Buka Packaging Material

                                <i class="mdi mdi-arrow-right ms-1"></i>
                            </a>

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
    .module-card {
        border-radius: 18px;
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease;
    }

    .module-card:hover {
        transform: translateY(-6px);
        box-shadow:
            0 18px 38px rgba(15, 23, 42, 0.14) !important;
    }

    .module-header {
        padding: 28px;
    }

    .rm-header {
        background: linear-gradient(
            135deg,
            #5369aa 0%,
            #3f518c 100%
        );
    }

    .pm-header {
        background: linear-gradient(
            135deg,
            #16b89f 0%,
            #0d927d 100%
        );
    }

    .module-icon {
        width: 86px;
        height: 86px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        font-size: 43px;
    }

    .module-category {
        display: block;
        margin-bottom: 3px;
        color: rgba(255, 255, 255, 0.75);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    @media (max-width: 575.98px) {
        .module-header {
            padding: 22px;
        }

        .module-icon {
            width: 66px;
            height: 66px;
            font-size: 33px;
        }
    }
</style>

@endsection