@extends('layouts.component.main')
@section('title', 'Menu Monitoring Filling')
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
                {{-- Daily Tank --}}
                <div class="col-12 col-sm-6 col-lg-4 col-xl-6">
                    <div class="card ribbon-box right overflow-hidden">
                        <div class="card-body text-center p-4">
                            <div class="ribbon ribbon-info ribbon-shape trending-ribbon">
                                <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                <span class="trending-ribbon-text">Monitoring</span>
                            </div>
                            <img src="{{ asset('assets/images/blending_awal.png') }}" alt="gambar" height="100">
                            <h5 class="mb-1 mt-4"><a href="" class="link-primary">Daily Tank</a></h5>
                            <p class="text-muted mb-4">Monitoring Daily Tank</p>
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('monitoring-daily-tank.index') }}" class="btn btn-light w-100">Lihat
                                    Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- On Going --}}
                <div class="col-12 col-sm-6 col-lg-4 col-xl-6">
                    <div class="card ribbon-box right overflow-hidden">
                        <div class="card-body text-center p-4">
                            <div class="ribbon ribbon-info ribbon-shape trending-ribbon">
                                <i class="ri-hand-heart-fill text-white align-bottom"></i>
                                <span class="trending-ribbon-text">Monitoring</span>
                            </div>
                            <img src="{{ asset('assets/images/blending_awal.png') }}" alt="gambar" height="100">
                            <h5 class="mb-1 mt-4"><a href="" class="link-primary">On Going</a></h5>
                            <p class="text-muted mb-4">Monitoring On Going - Kimia</p>
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div id="chart-gga" data-colors='["--vz-danger"]'></div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('monitoring-ongoing-kimia.index') }}" class="btn btn-light w-100">Lihat
                                    Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
