@extends('layouts.component.main')
@section('title', 'Menu Blending Awal')
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

            <div class="row">
                @if (auth()->user()->role == 'Analis Kimia' || auth()->user()->role == 'Foreman')
                    <div class="col-xl-6 col-lg-4">
                        <div class="card ribbon-box right overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="ribbon ribbon-success ribbon-shape trending-ribbon">
                                    <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                    <span class="trending-ribbon-text">Kimia</span>
                                </div>
                                <img src="{{ asset('assets/images/blending_awal.png') }}" alt="gambar" height="100">
                                <h5 class="mb-1 mt-4"><a href="" class="link-primary">Data Blending</a></h5>
                                <p class="text-muted mb-4">Analisis Blending - Kimia</p>
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('analisa.blending-awal.index') }}" class="btn btn-light w-100">Lihat
                                        Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (auth()->user()->role == 'Analis Mikro' ||
                        auth()->user()->role == 'Analis Field' ||
                        auth()->user()->role == 'Foreman')
                    <div class="col-xl-6 col-lg-4">
                        <div class="card ribbon-box right overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="ribbon ribbon-warning ribbon-shape trending-ribbon">
                                    <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                    <span class="trending-ribbon-text">Mikro</span>
                                </div>
                                <img src="{{ asset('assets/images/blending_adjust.png') }}" alt="gambar" height="100">
                                <h5 class="mb-1 mt-4"><a href="" class="link-primary">Data Blending After
                                        Adjustment</a>
                                </h5>
                                <p class="text-muted mb-4">Analisis Blending After Adjust - Mikro</p>
                                <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div id="chart-ggas" data-colors='["--vz-danger"]'></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="mt-4">
                                        <a href="{{ route('analisa.blending-awal-mikro.index') }}"
                                            class="btn btn-light w-100">Lihat
                                            Detail</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
