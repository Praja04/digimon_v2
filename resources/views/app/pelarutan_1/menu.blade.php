@extends('layouts.component.main')
@section('title', 'Pelarutan 1 & Pelarutan 2')
@section('styles')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #405189 0%, #5b6fa8 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #0ab39c 0%, #16c7a9 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f7b84b 0%, #f9c76c 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #299cdb 0%, #4db3e8 100%);
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .card-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
            z-index: 1;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        }

        .card-hover:hover::before {
            opacity: 1;
        }

        .avatar-xl {
            width: 4.5rem;
            height: 4.5rem;
        }

        .badge {
            font-weight: 500;
            padding: 0.35rem 0.75rem;
        }
    </style>
@endsection

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

            <div class="row g-4 mb-3">
                <!-- Pelarutan 1 Card -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card border-0 shadow-lg overflow-hidden h-100 card-hover">
                        <div class="card-header bg-gradient-primary text-white border-0 py-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="avatar-xl bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3">
                                        <i class="ri-bar-chart-box-line fs-1 text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-white mb-1 fw-bold">Pelarutan 1</h5>
                                        <p class="text-white text-opacity-75 mb-0 small">Analisis dan Monitoring Pelarutan 1
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary-subtle text-primary">Analisis</span>
                                    <span class="badge bg-primary-subtle text-primary">Monitoring</span>
                                </div>
                                <a href="{{ route('pelarutan-1.index') }}"
                                    class="btn btn-primary rounded-pill px-4 d-flex align-items-center">
                                    Lihat Detail <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pelarutan 2 Card -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card border-0 shadow-lg overflow-hidden h-100 card-hover">
                        <div class="card-header bg-gradient-success text-white border-0 py-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="avatar-xl bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center me-3">
                                        <i class="ri-line-chart-line fs-1 text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-white mb-1 fw-bold">Pelarutan 2</h5>
                                        <p class="text-white text-opacity-75 mb-0 small">Analisis dan Monitoring Pelarutan 2
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-2">
                                    <span class="badge bg-success-subtle text-success">Analisis</span>
                                    <span class="badge bg-success-subtle text-success">Monitoring</span>
                                </div>
                                <a href="{{ route('pelarutan-2.index') }}"
                                    class="btn btn-success rounded-pill px-4 d-flex align-items-center">
                                    Lihat Detail <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
