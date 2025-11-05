@extends('layouts.component.main')
@section('title', 'Persiapan Masak')
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

            <!-- Main Content Cards -->
            <div class="row g-4">
                <!-- Input PO Card -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all">
                        <div class="position-relative overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <div
                                        class="avatar-xl bg-light border rounded-4 mx-auto d-flex align-items-center justify-content-center mb-3">
                                        <img src="{{ asset('assets/images/masak.jpg') }}" alt="Tambah PO Masak"
                                            class="rounded-3" height="80" style="object-fit: cover;">
                                    </div>
                                    <h5 class="mb-2 fw-semibold">
                                        <a href="{{ route('productionbatch.create') }}"
                                            class="link-primary text-decoration-none">
                                            Tambah PO Masak
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-3 fs-14">Kelola Purchase Order untuk Produksi Masak</p>
                                </div>

                                <a href="{{ route('productionbatch.create') }}" class="btn btn-primary w-100 rounded-pill">
                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Batch GGA & GGAS Card -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all">
                        <div class="position-relative overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <div
                                        class="avatar-xl bg-light border rounded-4 mx-auto d-flex align-items-center justify-content-center mb-3">
                                        <img src="{{ asset('assets/images/nomor_po.jpg') }}" alt="Batch GGA & GGAS"
                                            class="rounded-3" height="80" style="object-fit: cover;">
                                    </div>
                                    <h5 class="mb-2 fw-semibold">
                                        <a href="{{ route('gga-ggas.index') }}" class="link-success text-decoration-none">
                                            Batch GGA & GGAS
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-3 fs-14">Monitoring Batch Produksi GGA & GGAS</p>
                                </div>

                                <a href="{{ route('gga-ggas.index') }}" class="btn btn-success w-100 rounded-pill">
                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Batch Blending Card -->
                <div class="col-xl-4 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all">
                        <div class="position-relative overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <div
                                        class="avatar-xl bg-light border rounded-4 mx-auto d-flex align-items-center justify-content-center mb-3">
                                        <img src="{{ asset('assets/images/blending_awal.png') }}" alt="Batch Blending"
                                            class="rounded-3" height="80" style="object-fit: cover;">
                                    </div>
                                    <h5 class="mb-2 fw-semibold">
                                        <a href="{{ route('blending-awal.index') }}"
                                            class="link-warning text-decoration-none">
                                            Batch Blending
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-3 fs-14">Proses Blending Awal Produksi</p>
                                </div>

                                <a href="{{ route('blending-awal.index') }}" class="btn btn-warning w-100 rounded-pill">
                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monitoring Turun Blending Card -->
                <div class="col-xl-4 col-lg-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all">
                        <div class="position-relative overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <div
                                        class="avatar-xl bg-light border rounded-4 mx-auto d-flex align-items-center justify-content-center mb-3">
                                        <img src="{{ asset('assets/images/blending_adjust.png') }}"
                                            alt="Monitoring Turun Blending" class="rounded-3" height="80"
                                            style="object-fit: cover;">
                                    </div>
                                    <h5 class="mb-2 fw-semibold">
                                        <a href="{{ route('monitoring-turun-blending.index') }}"
                                            class="link-primary text-decoration-none">
                                            Monitoring Turun Blending
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-3 fs-14">Real-time monitoring proses blending</p>
                                </div>

                                <a href="{{ route('monitoring-turun-blending.index') }}"
                                    class="btn btn-primary w-100 rounded-pill">
                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pasteurisasi Card -->
                <div class="col-xl-4 col-lg-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all">
                        <div class="position-relative overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <div
                                        class="avatar-xl bg-light border rounded-4 mx-auto d-flex align-items-center justify-content-center mb-3">
                                        <img src="{{ asset('assets/images/blending_adjust.png') }}" alt="Pasteurisasi"
                                            class="rounded-3" height="80" style="object-fit: cover;">
                                    </div>
                                    <h5 class="mb-2 fw-semibold">
                                        <a href="{{ route('monitoring-pasteurisasi.index') }}"
                                            class="link-info text-decoration-none">
                                            Pasteurisasi
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-3 fs-14">Real-time proses pasteurisasi</p>
                                </div>

                                <a href="{{ route('monitoring-pasteurisasi.index') }}"
                                    class="btn btn-info w-100 rounded-pill">
                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monitoring Storage Card -->
                <div class="col-xl-4 col-lg-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow-lg transition-all">
                        <div class="position-relative overflow-hidden">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <div
                                        class="avatar-xl bg-light border rounded-4 mx-auto d-flex align-items-center justify-content-center mb-3">
                                        <img src="{{ asset('assets/images/blending_adjust.png') }}"
                                            alt="Monitoring Storage Kimia" class="rounded-3" height="80"
                                            style="object-fit: cover;">
                                    </div>
                                    <h5 class="mb-2 fw-semibold">
                                        <a href="{{ route('monitoring-storage-kimia.index') }}"
                                            class="link-secondary text-decoration-none">
                                            Monitoring Storage Kimia
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-3 fs-14">Monitoring sistem penyimpanan</p>
                                </div>

                                <a href="{{ route('monitoring-storage-kimia.index') }}"
                                    class="btn btn-secondary w-100 rounded-pill">
                                    <i class="ri-eye-line me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
