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

                <div
                    class="
                        page-title-box
                        d-sm-flex
                        align-items-center
                        justify-content-between
                    "
                >
                    <h4 class="mb-sm-0">
                        RMPM
                    </h4>

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

                <div class="card overflow-hidden shadow-sm h-100">

                    <div
                        class="card-body p-0"
                        style="
                            background:
                                linear-gradient(
                                    135deg,
                                    #5369aa 0%,
                                    #445b9b 100%
                                );
                        "
                    >
                        <div class="p-4">

                            <div class="d-flex align-items-center">

                                <div
                                    class="
                                        flex-shrink-0
                                        d-flex
                                        align-items-center
                                        justify-content-center
                                        rounded
                                    "
                                    style="
                                        width: 85px;
                                        height: 85px;
                                        background: rgba(255, 255, 255, 0.18);
                                    "
                                >
                                    <i
                                        class="mdi mdi-flask-outline text-white"
                                        style="font-size: 40px;"
                                    ></i>
                                </div>

                                <div class="flex-grow-1 ms-4">

                                    <h3 class="text-white mb-1">
                                        RM
                                    </h3>

                                    <p class="text-white-50 mb-0">
                                        Raw Material
                                    </p>

                                    <small class="text-white-50">
                                        Data incoming dan analisa bahan baku
                                    </small>

                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="card-body">

                        <div
                            class="
                                d-flex
                                flex-wrap
                                align-items-center
                                justify-content-between
                                gap-3
                            "
                        >
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
                                Lihat Detail

                                <i class="mdi mdi-arrow-right ms-1"></i>
                            </a>

                        </div>

                    </div>

                </div>

            </div>

            {{-- PACKAGING MATERIAL --}}
            <div class="col-xl-6 col-lg-6 col-md-12">

                <div class="card overflow-hidden shadow-sm h-100">

                    <div
                        class="card-body p-0"
                        style="
                            background:
                                linear-gradient(
                                    135deg,
                                    #16b89f 0%,
                                    #11a990 100%
                                );
                        "
                    >
                        <div class="p-4">

                            <div class="d-flex align-items-center">

                                <div
                                    class="
                                        flex-shrink-0
                                        d-flex
                                        align-items-center
                                        justify-content-center
                                        rounded
                                    "
                                    style="
                                        width: 85px;
                                        height: 85px;
                                        background: rgba(255, 255, 255, 0.18);
                                    "
                                >
                                    <i
                                        class="mdi mdi-package-variant-closed text-white"
                                        style="font-size: 40px;"
                                    ></i>
                                </div>

                                <div class="flex-grow-1 ms-4">

                                    <h3 class="text-white mb-1">
                                        PM
                                    </h3>

                                    <p class="text-white-50 mb-0">
                                        Packaging Material
                                    </p>

                                    <small class="text-white-50">
                                        Data incoming dan sampling kemasan
                                    </small>

                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="card-body">

                        <div
                            class="
                                d-flex
                                flex-wrap
                                align-items-center
                                justify-content-between
                                gap-3
                            "
                        >
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
                                Lihat Detail

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