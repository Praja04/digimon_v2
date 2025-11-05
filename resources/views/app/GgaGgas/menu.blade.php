@extends('layouts.component.main')
@section('title', 'GGA & GGAS')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row mt-4">
                <div class="col-xl-6 col-lg-6">
                    <div class="card ribbon-box right overflow-hidden">
                        <div class="card-body text-center p-4">
                            <div class="ribbon ribbon-success ribbon-shape trending-ribbon">
                                <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                <span class="trending-ribbon-text">GGA</span>
                            </div>
                            <img src="{{ asset('assets/images/gga.png') }}" alt="gambar" height="100">
                            <h5 class="mb-1 mt-4"><a href="" class="link-primary">GGA Proses</a></h5>
                            <p class="text-muted mb-4">Analisis GGA</p>
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('gga.index') }}" class="btn btn-light w-100">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6">
                    <div class="card ribbon-box right overflow-hidden">
                        <div class="card-body text-center p-4">
                            <div class="ribbon ribbon-success ribbon-shape trending-ribbon">
                                <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                <span class="trending-ribbon-text">GGAS</span>
                            </div>
                            <img src="{{ asset('assets/images/ggas.png') }}" alt="gambar" height="100">
                            <h5 class="mb-1 mt-4"><a href="" class="link-primary">GGAS Proses</a></h5>
                            <p class="text-muted mb-4">Analisis GGAS</p>
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div id="chart-ggas" data-colors='["--vz-danger"]'></div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('ggas.index') }}" class="btn btn-light w-100">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
