<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">

        <!-- Dark Logo -->
        <a href="{{ route('homepage.index') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img
                    src="{{ asset('assets/images/icon-utility/kecap.png') }}"
                    alt="Logo"
                    height="22"
                >
            </span>

            <span class="logo-lg">
                <img
                    src="{{ asset('assets/images/icon-utility/kecap.png') }}"
                    alt="Logo"
                    height="90"
                >
            </span>
        </a>

        <!-- Light Logo -->
        <a href="{{ route('homepage.index') }}" class="logo logo-light">
            <span class="logo-sm">
                <img
                    src="{{ asset('assets/images/icon-utility/kecap.png') }}"
                    alt="Logo"
                    height="22"
                >
            </span>

            <span class="logo-lg">
                <img
                    src="{{ asset('assets/images/icon-utility/kecap.png') }}"
                    alt="Logo"
                    height="90"
                >
            </span>
        </a>

        <button
            type="button"
            class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover"
        >
            <i class="ri-record-circle-line"></i>
        </button>

    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu"></div>

            @php
                $userRole = auth()->user()->role;

                $showDashboardSection = in_array(
                    $userRole,
                    [
                        'Head Of Dapartement',
                        'Supervisor',
                        'Foreman',
                    ],
                    true
                );

                $showMainMenuSection = in_array(
                    $userRole,
                    [
                        'Head Of Dapartement',
                        'Supervisor',
                        'Foreman',
                        'Analis RM',
                        'Analis Kimia',
                        'Analis Mikro',
                        'Analis Field',
                        'Helper',
                    ],
                    true
                );

                $showMasterDataSection = in_array(
                    $userRole,
                    [
                        'Head Of Dapartement',
                        'Supervisor',
                        'Foreman',
                    ],
                    true
                );

                $dashboardActive = request()->routeIs('dashboard.*');

                $retailFinishGoodActive = request()->routeIs([
                    'dashboard.press-test-mesin.index',
                    'dashboard.timbangan-retail.*',
                ]);

                $timbanganRetailActive = request()->routeIs(
                    'dashboard.timbangan-retail.*'
                );

                $masterDataPmActive = request()->routeIs([
                    'jenis-incoming.*',
                    'jenis-material.*',
                    'supplier.*',
                    'sampling-status.*',
                    'uom.*',
                    'recommendation.*',
                    'nonconformity-type.*',
                ]);
            @endphp

            <ul class="navbar-nav" id="navbar-nav">

                {{-- ===================================================== --}}
                {{-- DASHBOARD --}}
                {{-- ===================================================== --}}
                @if ($showDashboardSection)

                    <li class="menu-title">
                        <span data-key="t-menu">
                            Dashboard
                        </span>
                    </li>

                    <li class="nav-item">

                        <a
                            class="nav-link menu-link {{ $dashboardActive ? 'active' : '' }}"
                            href="#Dashboards"
                            data-bs-toggle="collapse"
                            role="button"
                            aria-expanded="{{ $dashboardActive ? 'true' : 'false' }}"
                            aria-controls="Dashboards"
                        >
                            <i class="mdi mdi-monitor-dashboard"></i>

                            <span data-key="t-dashboards">
                                Dashboard
                            </span>
                        </a>

                        <div
                            id="Dashboards"
                            class="collapse menu-dropdown {{ $dashboardActive ? 'show' : '' }}"
                        >
                            <ul class="nav nav-sm flex-column">

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.proses-masak.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.proses-masak.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-chef-hat"></i>
                                        Proses Masak
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.pelarutan.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.pelarutan.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-flask"></i>
                                        Pelarutan
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.blending-awal.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.blending-awal.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-blender"></i>
                                        Blending Awal
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.blending-after-adjust.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.blending-after-adjust.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-blender-outline"></i>
                                        Blending After Adjust
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.monitoring-turun-blending.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.monitoring-turun-blending.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-chart-line"></i>
                                        Monitoring Turun Blending
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.monitoring-pasteurisasi.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.monitoring-pasteurisasi.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-thermometer"></i>
                                        Monitoring Pasteurisasi
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.monitoring-storage-kimia.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.monitoring-storage-kimia.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-database"></i>
                                        Monitoring Storage
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.monitoring-storage-mikro.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.monitoring-storage-mikro.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-bacteria"></i>
                                        Monitoring Storage Mikro
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.monitoring-on-going-mikro.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.monitoring-on-going-mikro.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-bacteria-outline"></i>
                                        Monitoring On Going Mikro
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.rmpm.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.rmpm.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-chemical-weapon"></i>
                                        RMPM
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        href="{{ route('dashboard.shelf-life.index') }}"
                                        class="nav-link {{
                                            request()->routeIs(
                                                'dashboard.shelf-life.index'
                                            ) ? 'active' : ''
                                        }}"
                                    >
                                        <i class="mdi mdi-calendar-clock"></i>
                                        Shelf Life
                                    </a>
                                </li>

                                {{-- RETAIL FINISH GOOD --}}
                                <li class="nav-item">

                                    <a
                                        class="nav-link menu-link {{
                                            $retailFinishGoodActive
                                                ? 'active'
                                                : ''
                                        }}"
                                        href="#RetailFG"
                                        data-bs-toggle="collapse"
                                        role="button"
                                        aria-expanded="{{
                                            $retailFinishGoodActive
                                                ? 'true'
                                                : 'false'
                                        }}"
                                        aria-controls="RetailFG"
                                    >
                                        <i class="mdi mdi-package-variant-closed"></i>

                                        <span>
                                            Retail Finish Good
                                        </span>
                                    </a>

                                    <div
                                        id="RetailFG"
                                        class="collapse menu-dropdown {{
                                            $retailFinishGoodActive
                                                ? 'show'
                                                : ''
                                        }}"
                                    >
                                        <ul class="nav nav-sm flex-column">

                                            <li class="nav-item">
                                                <a
                                                    href="{{ route('dashboard.press-test-mesin.index') }}"
                                                    class="nav-link {{
                                                        request()->routeIs(
                                                            'dashboard.press-test-mesin.index'
                                                        ) ? 'active' : ''
                                                    }}"
                                                >
                                                    <i class="mdi mdi-clipboard-text-play-outline"></i>
                                                    Press Test
                                                </a>
                                            </li>

                                            {{-- TIMBANGAN RETAIL --}}
                                            <li class="nav-item">

                                                <a
                                                    class="nav-link {{
                                                        $timbanganRetailActive
                                                            ? 'active'
                                                            : ''
                                                    }}"
                                                    href="#TimbanganRetail"
                                                    data-bs-toggle="collapse"
                                                    role="button"
                                                    aria-expanded="{{
                                                        $timbanganRetailActive
                                                            ? 'true'
                                                            : 'false'
                                                    }}"
                                                    aria-controls="TimbanganRetail"
                                                >
                                                    <i class="mdi mdi-scale-bathroom"></i>
                                                    Timbangan Retail
                                                </a>

                                                <div
                                                    id="TimbanganRetail"
                                                    class="collapse menu-dropdown {{
                                                        $timbanganRetailActive
                                                            ? 'show'
                                                            : ''
                                                    }}"
                                                >
                                                    <ul class="nav nav-sm flex-column">

                                                        <li class="nav-item">
                                                            <a
                                                                href="{{ route('dashboard.timbangan-retail.index') }}"
                                                                class="nav-link {{
                                                                    request()->routeIs(
                                                                        'dashboard.timbangan-retail.index'
                                                                    ) ? 'active' : ''
                                                                }}"
                                                            >
                                                                <i class="mdi mdi-view-dashboard-outline"></i>
                                                                Dashboard
                                                            </a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a
                                                                href="{{ route('dashboard.timbangan-retail.analisa') }}"
                                                                class="nav-link {{
                                                                    request()->routeIs(
                                                                        'dashboard.timbangan-retail.analisa'
                                                                    ) ? 'active' : ''
                                                                }}"
                                                            >
                                                                <i class="mdi mdi-chart-bell-curve-cumulative"></i>
                                                                Analisa Abnormal
                                                            </a>
                                                        </li>

                                                        <li class="nav-item">
                                                            <a
                                                                href="{{ route('dashboard.timbangan-retail.mesin-ranking') }}"
                                                                class="nav-link {{
                                                                    request()->routeIs(
                                                                        'dashboard.timbangan-retail.mesin-ranking'
                                                                    ) ? 'active' : ''
                                                                }}"
                                                            >
                                                                <i class="mdi mdi-television-play"></i>
                                                                Ranking Mesin (TV Mode)
                                                            </a>
                                                        </li>

                                                    </ul>
                                                </div>

                                            </li>

                                        </ul>
                                    </div>

                                </li>

                            </ul>
                        </div>

                    </li>

                @endif

                {{-- ===================================================== --}}
                {{-- MENU UTAMA --}}
                {{-- ===================================================== --}}
                @if ($showMainMenuSection)

                    <li class="menu-title">
                        <span data-key="t-menu">
                            Menu
                        </span>
                    </li>

{{-- RMPM --}}
@if (in_array(
    $userRole,
    [
        'Head Of Dapartement',
        'Supervisor',
        'Foreman',
        'Analis RM',
    ],
    true
))

    <li class="nav-item">
        <a
            href="{{ route('rmpm.index') }}"
            class="nav-link menu-link {{
                request()->routeIs([
                    'rmpm.index',
                    'rmpm.rm',
                    'rmpm.pm',
                    'rmpm.show',
                    'rmpm.analisa',
                ]) ? 'active' : ''
            }}"
        >
            <i class="mdi mdi-puzzle-outline"></i>

            <span data-key="t-widgets">
                RMPM
            </span>
        </a>
    </li>

@endif

                    {{-- PELARUTAN --}}
                    @if (in_array(
                        $userRole,
                        [
                            'Head Of Dapartement',
                            'Supervisor',
                            'Foreman',
                            'Analis Kimia',
                        ],
                        true
                    ))

                        <li class="nav-item">
                            <a
                                href="{{ route('pelarutan-1.menu') }}"
                                class="nav-link menu-link {{
                                    request()->routeIs([
                                        'pelarutan-1.menu',
                                        'pelarutan-1.index',
                                        'pelarutan-1.show',
                                        'pelarutan-1.show_batch',
                                        'pelarutan-2.index',
                                        'pelarutan-2.show',
                                        'pelarutan-2.show_batch',
                                    ]) ? 'active' : ''
                                }}"
                            >
                                <i class="mdi mdi-flask-outline"></i>

                                <span>
                                    Pelarutan
                                </span>
                            </a>
                        </li>

                    @endif

                    {{-- BLENDING --}}
                    @if (in_array(
                        $userRole,
                        [
                            'Head Of Dapartement',
                            'Supervisor',
                            'Foreman',
                            'Analis Kimia',
                            'Analis Mikro',
                            'Analis Field',
                        ],
                        true
                    ))

                        <li class="nav-item">
                            <a
                                href="{{ route('analisa.blending-awal.menu') }}"
                                class="nav-link menu-link {{
                                    request()->routeIs([
                                        'analisa.blending-awal.menu',
                                        'analisa.blending-awal.index',
                                        'analisa.blending-awal.show',
                                        'analisa.blending-awal.show_batch',
                                        'analisa.blending-awal-mikro.index',
                                        'analisa.blending-awal-mikro.show',
                                        'analisa.blending-awal-mikro.show_batch',
                                    ]) ? 'active' : ''
                                }}"
                            >
                                <i class="mdi mdi-blender-software"></i>

                                <span>
                                    Blending
                                </span>
                            </a>
                        </li>

                    @endif

                    {{-- MONITORING --}}
                    @if (in_array(
                        $userRole,
                        [
                            'Head Of Dapartement',
                            'Supervisor',
                            'Foreman',
                            'Analis Kimia',
                            'Analis Field',
                            'Analis Mikro',
                        ],
                        true
                    ))

                        <li class="nav-item">
                            <a
                                href="{{ route('analisa.monitoring-turun-blending.menu') }}"
                                class="nav-link menu-link {{
                                    request()->routeIs([
                                        'monitoring-storage-kimia.index',
                                        'monitoring-storage-kimia.show',
                                        'analisa.monitoring-turun-blending.menu',
                                        'analisa.monitoring-turun-blending.index',
                                        'analisa.monitoring-turun-blending.show',
                                        'analisa.monitoring-turun-blending.show_batch',
                                        'analisa.monitoring-pasteurisasi.index',
                                        'analisa.monitoring-pasteurisasi.show',
                                        'analisa.monitoring-pasteurisasi.show_batch',
                                        'analisa.monitoring-storage-kimia.index',
                                        'analisa.monitoring-storage-kimia.show',
                                        'analisa.monitoring-storage-kimia.show_batch',
                                        'analisa.monitoring-storage-mikro.index',
                                        'analisa.monitoring-storage-mikro.show',
                                        'analisa.monitoring-storage-mikro.show_batch',
                                        'monitoring-storage-before-use.index',
                                        'monitoring-storage-before-use.analisa',
                                    ]) ? 'active' : ''
                                }}"
                            >
                                <i class="mdi mdi-thermometer"></i>

                                <span>
                                    Monitoring Pasteurisasi & Storage
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('monitoring-daily-tank.menu') }}"
                                class="nav-link menu-link {{
                                    request()->routeIs([
                                        'monitoring-daily-tank.menu',
                                        'monitoring-daily-tank.index',
                                        'monitoring-daily-tank.show',
                                        'analisa.monitoring-daily-tank-mikro.show',
                                        'analisa.monitoring-daily-tank-kimia.show',
                                        'monitoring-ongoing-kimia.index',
                                        'monitoring-ongoing-mikro.index',
                                        'monitoring-ongoing-kimia.show',
                                        'monitoring-ongoing-kimia.analisa',
                                        'monitoring-ongoing-mikro.analisa',
                                    ]) ? 'active' : ''
                                }}"
                            >
                                <i class="mdi mdi-bottle-wine"></i>

                                <span>
                                    Monitoring Filling
                                </span>
                            </a>
                        </li>

                    @endif

                    {{-- SHELF LIFE --}}
                    @if (in_array(
                        $userRole,
                        [
                            'Head Of Dapartement',
                            'Supervisor',
                            'Foreman',
                            'Helper',
                            'Analis Kimia',
                            'Analis Mikro',
                        ],
                        true
                    ))

                        <li class="nav-item">
                            <a
                                href="{{ route('shelf-life.index') }}"
                                class="nav-link menu-link {{
                                    request()->routeIs([
                                        'shelf-life.index',
                                        'shelf-life.sample.index',
                                        'shelf-life.sample.show',
                                        'shelf-life.checksheet.index',
                                        'shelf-life.analysis-kimia.index',
                                        'shelf-life.analysis-kimia.show',
                                        'shelf-life.analysis-mikro.index',
                                        'shelf-life.analysis-mikro.show',
                                        'shelf-life.result.index',
                                    ]) ? 'active' : ''
                                }}"
                            >
                                <i class="mdi mdi-calendar-clock"></i>

                                <span data-key="t-widgets">
                                    Shelf Life
                                </span>
                            </a>
                        </li>

                    @endif

                    {{-- PRESS TEST DATA --}}
                    @if (in_array(
                        $userRole,
                        [
                            'Head Of Dapartement',
                            'Supervisor',
                            'Foreman',
                            'Helper',
                        ],
                        true
                    ))

                        <li class="nav-item">
                            <a
                                href="{{ route('press-test-data.index') }}"
                                class="nav-link menu-link {{
                                    request()->routeIs(
                                        'press-test-data.index'
                                    ) ? 'active' : ''
                                }}"
                            >
                                <i class="mdi mdi-clipboard-text-play-outline"></i>

                                <span data-key="t-widgets">
                                    Press Test Data
                                </span>
                            </a>
                        </li>

                    @endif

                    {{-- SCAN --}}
                    @if (in_array(
                        $userRole,
                        [
                            'Analis Kimia',
                            'Analis Mikro',
                            'Analis RM',
                        ],
                        true
                    ))

                        <li class="nav-item">
                            <a
                                href="{{ route('scan.index') }}"
                                class="nav-link menu-link {{
                                    request()->routeIs('scan.*')
                                        ? 'active'
                                        : ''
                                }}"
                            >
                                <i class="mdi mdi-barcode-scan"></i>

                                <span data-key="t-scan">
                                    Scan
                                </span>
                            </a>
                        </li>

                    @endif

                    {{-- NOTIFIKASI --}}
                    @if (in_array(
                        $userRole,
                        [
                            'Head Of Dapartement',
                            'Supervisor',
                            'Foreman',
                        ],
                        true
                    ))

                        <li class="nav-item">
                            <a
                                href="{{ route('notifications.index') }}"
                                class="nav-link menu-link {{
                                    request()->routeIs('notifications.*')
                                        ? 'active'
                                        : ''
                                }}"
                            >
                                <i class="mdi mdi-bell"></i>

                                <span data-key="t-widgets">
                                    Notifikasi
                                </span>
                            </a>
                        </li>

                    @endif

                @endif

                {{-- ===================================================== --}}
                {{-- PROFILE --}}
                {{-- ===================================================== --}}
                <li class="nav-item">
                    <a
                        href="{{ route('profile.index') }}"
                        class="nav-link menu-link {{
                            request()->routeIs('profile.index')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="mdi mdi-account"></i>

                        <span data-key="t-profile">
                            Profile
                        </span>
                    </a>
                </li>

{{-- ===================================================== --}}
{{-- MASTER DATA --}}
{{-- ===================================================== --}}
@if ($showMasterDataSection)

    <li class="menu-title">
        <i class="ri-more-fill"></i>

        <span data-key="t-pages">
            Master Data
        </span>
    </li>

    {{-- PENGGUNA --}}
    @if (in_array(
        $userRole,
        [
            'Head Of Dapartement',
            'Supervisor',
        ],
        true
    ))

        <li class="nav-item">
            <a
                href="{{ route('users.index') }}"
                class="nav-link menu-link {{
                    request()->routeIs('users.*')
                        ? 'active'
                        : ''
                }}"
            >
                <i class="mdi mdi-account-group"></i>

                <span data-key="t-users">
                    Pengguna
                </span>
            </a>
        </li>

    @endif

    {{-- WARNA KHUSUS FOREMAN --}}
    @if ($userRole === 'Foreman')

        <li class="nav-item">
            <a
                href="{{ route('colors.index') }}"
                class="nav-link menu-link {{
                    request()->routeIs('colors.*')
                        ? 'active'
                        : ''
                }}"
            >
                <i class="mdi mdi-palette"></i>

                <span data-key="t-colors">
                    Warna
                </span>
            </a>
        </li>

    @endif

    {{-- MASTER DATA PM --}}
    <li class="nav-item">

        <a
            href="#sidebarMasterDataPm"
            class="nav-link menu-link {{
                $masterDataPmActive
                    ? 'active'
                    : ''
            }}"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="{{
                $masterDataPmActive
                    ? 'true'
                    : 'false'
            }}"
            aria-controls="sidebarMasterDataPm"
        >
            <i class="mdi mdi-package-variant-closed"></i>

            <span>
                Master Data PM
            </span>
        </a>

        <div
            id="sidebarMasterDataPm"
            class="collapse menu-dropdown {{
                $masterDataPmActive
                    ? 'show'
                    : ''
            }}"
        >
            <ul class="nav nav-sm flex-column">

                <li class="nav-item">
                    <a
                        href="{{
                            Route::has('jenis-incoming.index')
                                ? route('jenis-incoming.index')
                                : '#'
                        }}"
                        class="nav-link {{
                            request()->routeIs('jenis-incoming.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="mdi mdi-package-variant"></i>
                        Jenis Incoming
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        href="{{
                            Route::has('jenis-material.index')
                                ? route('jenis-material.index')
                                : '#'
                        }}"
                        class="nav-link {{
                            request()->routeIs('jenis-material.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="mdi mdi-layers-outline"></i>
                        Jenis Material
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        href="{{
                            Route::has('supplier.index')
                                ? route('supplier.index')
                                : '#'
                        }}"
                        class="nav-link {{
                            request()->routeIs('supplier.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="mdi mdi-truck-delivery-outline"></i>
                        Supplier
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        href="{{
                            Route::has('sampling-status.index')
                                ? route('sampling-status.index')
                                : '#'
                        }}"
                        class="nav-link {{
                            request()->routeIs('sampling-status.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                        Status Sampling
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        href="{{
                            Route::has('uom.index')
                                ? route('uom.index')
                                : '#'
                        }}"
                        class="nav-link {{
                            request()->routeIs('uom.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="mdi mdi-scale-balance"></i>
                        UOM
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        href="{{
                            Route::has('recommendation.index')
                                ? route('recommendation.index')
                                : '#'
                        }}"
                        class="nav-link {{
                            request()->routeIs('recommendation.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="mdi mdi-clipboard-check-outline"></i>
                        Rekomendasi
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        href="{{
                            Route::has('nonconformity-type.index')
                                ? route('nonconformity-type.index')
                                : '#'
                        }}"
                        class="nav-link {{
                            request()->routeIs('nonconformity-type.*')
                                ? 'active'
                                : ''
                        }}"
                    >
                        <i class="mdi mdi-alert-circle-outline"></i>
                        Jenis Ketidaksesuaian
                    </a>
                </li>

            </ul>
        </div>

    </li>

@endif

            </ul>

        </div>
    </div>

    <div class="sidebar-background"></div>

</div>
<!-- Left Sidebar End -->