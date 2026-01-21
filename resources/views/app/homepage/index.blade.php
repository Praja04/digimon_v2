@extends('layouts.component.main')
@section('title', 'Halaman Utama')

@section('styles')
    <style>
        .banner-wrapper {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            overflow: hidden;
        }

        /* Logo animation */
        .logo-banner {
            animation: fadeInDown 0.8s ease-out;
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.4));
            transition: all 0.3s ease;
            max-width: 100%;
            height: auto;
        }

        .logo-wrapper {
            transition: all 0.3s ease;
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

        .qc-highlight {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .qc-highlight:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Desktop - Large screens */
        @media (min-width: 1200px) {
            .banner-wrapper {
                min-height: 550px !important;
            }

            .logo-wrapper {
                max-width: 180px;
            }
        }

        /* Laptop/Tablet Landscape (992px - 1199px) */
        @media (max-width: 1199px) {
            .banner-wrapper {
                min-height: 500px !important;
            }

            .display-4 {
                font-size: 2.5rem !important;
            }

            .logo-wrapper {
                max-width: 150px;
            }
        }

        /* Tablet Portrait (768px - 991px) */
        @media (max-width: 991px) {
            .banner-wrapper {
                min-height: 450px !important;
            }

            .display-4 {
                font-size: 2.2rem !important;
            }

            .logo-wrapper {
                top: 20px !important;
                right: 20px !important;
                max-width: 130px;
            }

            .content-wrapper {
                max-width: 100% !important;
            }

            .qc-highlights-wrapper {
                flex-direction: column !important;
            }
        }

        /* Mobile Landscape (576px - 767px) */
        @media (max-width: 767px) {
            .banner-wrapper {
                min-height: 520px !important;
            }

            .display-4 {
                font-size: 2rem !important;
            }

            .fs-5 {
                font-size: 1.1rem !important;
            }

            .logo-wrapper {
                max-width: 110px;
                top: 18px !important;
                right: 18px !important;
            }

            .qc-highlight {
                padding: 0.75rem !important;
            }

            .badge {
                font-size: 0.85rem !important;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            .banner-wrapper {
                min-height: 600px !important;
                border-radius: 8px !important;
            }

            .display-4 {
                font-size: 1.75rem !important;
                line-height: 1.3 !important;
            }

            .fs-5 {
                font-size: 1rem !important;
            }

            .banner-content {
                padding-left: 1.5rem !important;
                padding-right: 1.5rem !important;
                padding-top: 5rem !important;
            }

            .logo-wrapper {
                max-width: 90px;
                top: 15px !important;
                right: 15px !important;
            }

            .greeting-text {
                font-size: 0.95rem !important;
            }

            .qc-highlight {
                padding: 0.65rem !important;
            }

            .qc-highlight i {
                font-size: 1.25rem !important;
            }

            .qc-highlight strong {
                font-size: 0.95rem !important;
            }

            .qc-highlight .small {
                font-size: 0.8rem !important;
            }

            .badge {
                font-size: 0.8rem !important;
                padding: 0.4rem 0.8rem !important;
            }

            .cta-badges {
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            .cta-badges .badge {
                margin-bottom: 0.5rem !important;
            }
        }

        /* Extra Small Mobile (up to 375px) */
        @media (max-width: 375px) {
            .banner-wrapper {
                min-height: 620px !important;
            }

            .display-4 {
                font-size: 1.5rem !important;
            }

            .logo-wrapper {
                max-width: 75px;
                top: 12px !important;
                right: 12px !important;
            }
        }

        /* Very Small Mobile (up to 320px) */
        @media (max-width: 320px) {
            .logo-wrapper {
                max-width: 65px;
            }

            .display-4 {
                font-size: 1.35rem !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- QC Hero Banner -->
            <div class="banner-wrapper position-relative mb-3"
                style="background: url('{{ asset('assets/images/banner/banner_5.png') }}') center center/cover no-repeat;">

                <!-- Dark overlay -->
                <div class="position-absolute w-100 h-100"
                    style="background: linear-gradient(135deg, rgba(30, 60, 114, 0.85) 0%, rgba(42, 82, 152, 0.75) 100%);
                       top: 0; left: 0;">
                </div>

                <!-- Logo -->
                <div class="position-absolute logo-wrapper" style="top: 30px; right: 40px; z-index: 3;">
                    <img src="{{ asset('assets/images/icon-utility/kecap.png') }}" alt="QC Logo" class="logo-banner">
                </div>

                <!-- Content -->
                <div class="position-relative h-100 d-flex align-items-center banner-content px-4 px-md-5 py-4 py-md-5"
                    style="z-index: 2; min-height: 500px;">

                    <div class="text-white content-wrapper" style="max-width: 900px;">

                        <!-- Greeting -->
                        <div class="mb-3 mb-md-4">
                            <p class="fs-5 mb-2 opacity-75 greeting-text">
                                <i class="ri-hand-heart-line me-2"></i>
                                {{ now()->hour < 10 ? 'Selamat Pagi' : (now()->hour < 15 ? 'Selamat Siang' : (now()->hour < 18 ? 'Selamat Sore' : 'Selamat Malam')) }},
                                <strong>{{ Auth::user()->name ?? 'User' }}</strong>
                            </p>
                            <p class="mb-0 opacity-75 greeting-text">
                                <i class="ri-calendar-line me-2"></i>
                                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}
                            </p>
                        </div>

                        <!-- Title -->
                        <h1 class="display-4 fw-bold mb-3 text-white">
                            Quality Control Portal
                        </h1>

                        <!-- Description -->
                        <p class="fs-5 mb-3 mb-md-4 opacity-90" style="max-width: 650px; line-height: 1.6;">
                            Sistem monitoring dan analisis quality control terpadu untuk mendukung
                            pengendalian mutu produk agar sesuai standar dan terdokumentasi dengan baik.
                        </p>

                        <!-- QC Highlights -->
                        <div class="d-flex flex-wrap gap-2 gap-md-3 mt-3 mt-md-4 qc-highlights-wrapper">

                            <div class="qc-highlight d-flex align-items-start gap-2 flex-fill">
                                <i class="ri-search-eye-line fs-4 text-warning"></i>
                                <div>
                                    <strong class="d-block">Inspeksi Produk</strong>
                                    <div class="small opacity-75">
                                        Pemeriksaan kualitas secara terstruktur
                                    </div>
                                </div>
                            </div>

                            <div class="qc-highlight d-flex align-items-start gap-2 flex-fill">
                                <i class="ri-file-list-3-line fs-4 text-success"></i>
                                <div>
                                    <strong class="d-block">Pencatatan QC</strong>
                                    <div class="small opacity-75">
                                        Hasil uji terdokumentasi dengan rapi
                                    </div>
                                </div>
                            </div>

                            <div class="qc-highlight d-flex align-items-start gap-2 flex-fill">
                                <i class="ri-alert-line fs-4 text-danger"></i>
                                <div>
                                    <strong class="d-block">Temuan & Tindakan</strong>
                                    <div class="small opacity-75">
                                        Identifikasi dan tindak lanjut ketidaksesuaian
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Soft CTA -->
                        <div class="mt-3 mt-md-4 cta-badges d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
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
