@extends('layouts.component.main')

@section('title')
    {{ $title }}
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
                            {{ $title }}
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            {{ $subtitle }}
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
                                {{ $title }}
                            </li>

                        </ol>
                    </div>

                </div>

            </div>
        </div>

        {{-- CONTENT --}}
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">

                <div class="card border-0 shadow-sm overflow-hidden placeholder-card">

                    <div class="card-body text-center py-5 px-4">

                        <div class="placeholder-icon mx-auto mb-4">
                            <i class="mdi {{ $icon }}"></i>
                        </div>

                        <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-3">
                            {{ $step }}
                        </span>

                        <h2 class="mb-3">
                            {{ $title }}
                        </h2>

                        <p class="text-muted fs-5 mb-4">
                            {{ $subtitle }}
                        </p>

                        <div class="alert alert-info border-0 text-start mx-auto placeholder-alert">

                            <div class="d-flex align-items-start gap-3">

                                <div class="fs-4">
                                    <i class="mdi mdi-information-outline"></i>
                                </div>

                                <div>
                                    <h6 class="mb-1">
                                        Modul sedang disiapkan
                                    </h6>

                                    <p class="mb-0">
                                        Halaman ini sudah terhubung dengan route dan
                                        controller. Form serta tabel transaksi akan
                                        dibuat pada tahap berikutnya.
                                    </p>
                                </div>

                            </div>

                        </div>

                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">

                            <a
                                href="{{ route('rmpm.pm') }}"
                                class="btn btn-light"
                            >
                                <i class="mdi mdi-arrow-left me-1"></i>
                                Kembali ke PM
                            </a>

                            <button
                                type="button"
                                class="btn btn-primary"
                                disabled
                            >
                                <i class="mdi mdi-tools me-1"></i>
                                Modul Akan Dibuat
                            </button>

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
    .placeholder-card {
        border-radius: 18px;
    }

    .placeholder-icon {
        width: 96px;
        height: 96px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 24px;
        background: linear-gradient(
            135deg,
            #eef2ff,
            #e0e7ff
        );
        color: #4f46e5;
        font-size: 50px;
    }

    .placeholder-alert {
        max-width: 620px;
    }
</style>

@endsection