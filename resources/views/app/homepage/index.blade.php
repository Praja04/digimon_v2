@extends('layouts.component.main')
@section('title', 'Halaman Utama')

@section('styles')
    <style>
        .banner-wrapper {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        /* Logo animation */
        .logo-banner {
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 991px) {
            .banner-wrapper {
                min-height: 450px !important;
            }

            .display-4 {
                font-size: 2.2rem !important;
            }

            .logo-banner {
                max-height: 140px !important;
            }

            .position-absolute[style*="top: 30px"] {
                top: 20px !important;
                right: 20px !important;
            }
        }

        @media (max-width: 576px) {
            .banner-wrapper {
                min-height: 500px !important;
            }

            .display-4 {
                font-size: 1.8rem !important;
            }

            .fs-5 {
                font-size: 1rem !important;
            }

            .px-5 {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
            }

            .logo-banner {
                max-height: 100px !important;
            }

            .position-absolute[style*="top: 30px"] {
                top: 15px !important;
                right: 15px !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- QC Hero Banner -->
            <div class="banner-wrapper position-relative"
                style="background: url('{{ asset('assets/images/banner/banner_5.png') }}') center center/cover no-repeat;
                   min-height: 500px;
                   border-radius: 12px;
                   overflow: hidden;">

                <!-- Dark overlay -->
                <div class="position-absolute w-100 h-100"
                    style="background: linear-gradient(135deg, rgba(30, 60, 114, 0.85) 0%, rgba(42, 82, 152, 0.75) 100%);
                       top: 0; left: 0;">
                </div>

                <!-- Logo -->
                <div class="position-absolute" style="top: 30px; right: 40px; z-index: 3;">
                    <img src="{{ asset('assets/images/icon-utility/kecap.png') }}" alt="QC Logo" class="logo-banner"
                        style="max-height: 180px; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.4));">
                </div>

                <!-- Content -->
                <div class="position-relative h-100 d-flex align-items-center px-5 py-5"
                    style="z-index: 2; min-height: 500px;">

                    <div class="text-white" style="max-width: 900px;">

                        <!-- Greeting -->
                        <div class="mb-4">
                            <p class="fs-5 mb-2 opacity-75">
                                <i class="ri-hand-heart-line me-2"></i>
                                Selamat Datang, <strong>{{ Auth::user()->name ?? 'User' }}</strong>
                            </p>
                            <p class="mb-0 opacity-75">
                                <i class="ri-calendar-line me-2"></i>
                                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
                            </p>
                        </div>

                        <!-- Title -->
                        <h1 class="display-4 fw-bold mb-3 text-white">
                            Quality Control Portal
                        </h1>

                        <!-- Description -->
                        <p class="fs-5 mb-4 opacity-90" style="max-width: 650px; line-height: 1.6;">
                            Sistem monitoring dan analisis quality control terpadu untuk mendukung
                            pengendalian mutu produk agar sesuai standar dan terdokumentasi dengan baik.
                        </p>

                        <!-- QC Highlights -->
                        <div class="d-flex flex-wrap gap-4 mt-4">

                            <div class="d-flex align-items-start gap-2">
                                <i class="ri-search-eye-line fs-4 text-warning"></i>
                                <div>
                                    <strong>Inspeksi Produk</strong>
                                    <div class="small opacity-75">
                                        Pemeriksaan kualitas secara terstruktur
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-2">
                                <i class="ri-file-list-3-line fs-4 text-success"></i>
                                <div>
                                    <strong>Pencatatan QC</strong>
                                    <div class="small opacity-75">
                                        Hasil uji terdokumentasi dengan rapi
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-2">
                                <i class="ri-alert-line fs-4 text-danger"></i>
                                <div>
                                    <strong>Temuan & Tindakan</strong>
                                    <div class="small opacity-75">
                                        Identifikasi dan tindak lanjut ketidaksesuaian
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Soft CTA -->
                        <div class="mt-4">
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill me-2">
                                <i class="ri-information-line me-1"></i>
                                Lakukan inspeksi sesuai prosedur
                            </span>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                <i class="ri-shield-check-line me-1"></i>
                                Pastikan standar mutu terpenuhi
                            </span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
