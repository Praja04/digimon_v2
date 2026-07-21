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

                <div
                    class="
                        page-title-box
                        d-sm-flex
                        align-items-center
                        justify-content-between
                    "
                >
                    <h4 class="mb-sm-0">
                        Packaging Material
                    </h4>

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

        <div class="row">
            <div class="col-12">

                <div class="card">

                    <div class="card-body text-center py-5">

                        <div class="mb-4">

                            <i
                                class="mdi mdi-package-variant-closed text-success"
                                style="font-size: 80px;"
                            ></i>

                        </div>

                        <h3 class="mb-2">
                            Packaging Material
                        </h3>

                        <p class="text-muted mb-4">
                            Halaman PM sedang dipersiapkan.
                            Untuk saat ini pengembangan difokuskan
                            pada modul Raw Material.
                        </p>

                        <a
                            href="{{ route('rmpm.index') }}"
                            class="btn btn-primary"
                        >
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Kembali ke RMPM
                        </a>

                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

@endsection