@extends('layouts.component.main')
@section('title', 'Menu Pasteurisasi & Storage')
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

            <div class="row">
                @if (auth()->user()->role == 'Analis Kimia' || auth()->user()->role == 'Foreman')
                    {{-- Turun Blending --}}
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-6">
                        <div class="card ribbon-box right overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="ribbon ribbon-success ribbon-shape trending-ribbon">
                                    <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                    <span class="trending-ribbon-text">Monitoring</span>
                                </div>
                                <img src="{{ asset('assets/images/blending_awal.png') }}" alt="gambar" height="100">
                                <h5 class="mb-1 mt-4"><a href="" class="link-primary">Turun Blending</a></h5>
                                <p class="text-muted mb-4">Analisa Turun Blending</p>
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('analisa.monitoring-turun-blending.index') }}"
                                        class="btn btn-light w-100">Lihat
                                        Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pasteurisasi --}}
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-6">
                        <div class="card ribbon-box right overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="ribbon ribbon-success ribbon-shape trending-ribbon">
                                    <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                    <span class="trending-ribbon-text">Monitoring</span>
                                </div>
                                <img src="{{ asset('assets/images/blending_awal.png') }}" alt="gambar" height="100">
                                <h5 class="mb-1 mt-4"><a href="" class="link-primary">Pasteurisasi</a></h5>
                                <p class="text-muted mb-4">Analisa Pasteurisasi</p>
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('analisa.monitoring-pasteurisasi.index') }}"
                                        class="btn btn-light w-100">Lihat
                                        Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Storage Kimia --}}
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-6">
                        <div class="card ribbon-box right overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="ribbon ribbon-warning ribbon-shape trending-ribbon">
                                    <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                    <span class="trending-ribbon-text">Monitoring Storage</span>
                                </div>
                                <img src="{{ asset('assets/images/blending_awal.png') }}" alt="gambar" height="100">
                                <h5 class="mb-1 mt-4"><a href="" class="link-primary">Storage Kimia</a></h5>
                                <p class="text-muted mb-4">Analisa Storage Kimia</p>
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('analisa.monitoring-storage-kimia.index') }}"
                                        class="btn btn-light w-100">Lihat
                                        Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (auth()->user()->role == 'Analis Field' || auth()->user()->role == 'Analis Kimia' || auth()->user()->role == 'Foreman')
                    {{-- Storage Before Use --}}
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-6">
                        <div class="card ribbon-box right overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="ribbon ribbon-info ribbon-shape trending-ribbon">
                                    <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                    <span class="trending-ribbon-text">Monitoring Storage</span>
                                </div>
                                <img src="{{ asset('assets/images/blending_awal.png') }}" alt="gambar" height="100">
                                <h5 class="mb-1 mt-4"><a href="" class="link-primary">Storage Before Use</a></h5>
                                <p class="text-muted mb-4">Analisa Storage Before Use</p>
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('monitoring-storage-before-use.index') }}"
                                        class="btn btn-light w-100">Lihat
                                        Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                @if (auth()->user()->role == 'Analis Mikro' || auth()->user()->role == 'Foreman')
                    {{-- Storage Mikro --}}
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-6">
                        <div class="card ribbon-box right overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="ribbon ribbon-warning ribbon-shape trending-ribbon">
                                    <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                    <span class="trending-ribbon-text">Monitoring Storage</span>
                                </div>
                                <img src="{{ asset('assets/images/blending_awal.png') }}" alt="gambar" height="100">
                                <h5 class="mb-1 mt-4"><a href="" class="link-primary">Storage Mikro</a></h5>
                                <p class="text-muted mb-4">Analisa Storage Mikro</p>
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('analisa.monitoring-storage-mikro.index') }}"
                                        class="btn btn-light w-100">Lihat
                                        Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
