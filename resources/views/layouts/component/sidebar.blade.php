<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets') }}/images/icon-utility/kecap.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets') }}/images/icon-utility/kecap.png" alt="" height="90">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets') }}/images/icon-utility/kecap.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets') }}/images/icon-utility/kecap.png" alt="" height="90">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>

            @php
                $userRole = auth()->user()->role;
            @endphp

            <ul class="navbar-nav" id="navbar-nav">

                {{-- SECTION: DASHBOARD --}}
                @if (in_array($userRole, ['Supervisor', 'Foreman']))
                    <li class="menu-title"><span data-key="t-menu">Dashboard</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
                            href="#Dashboards" data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="Dashboards">
                            <i class="mdi mdi-monitor-dashboard"></i> <span data-key="t-dashboards">Dashboard</span>
                        </a>
                        <div class="collapse menu-dropdown {{ request()->routeIs('dashboard.*') ? 'show' : '' }}"
                            id="Dashboards">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item"><a href="{{ route('dashboard.gga-ggas.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.gga-ggas.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-flask"></i> Analisis GGA & GGAS</a></li>
                                <li class="nav-item"><a href="{{ route('dashboard.blending-awal.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.blending-awal.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-blender"></i> Analisis Blending Awal</a>
                                </li>
                                <li class="nav-item"><a href="{{ route('dashboard.blending-after-adjust.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.blending-after-adjust.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-blender-outline"></i> Analisis Blending
                                        After Adjust</a></li>
                                <li class="nav-item"><a href="{{ route('dashboard.monitoring-turun-blending.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.monitoring-turun-blending.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-chart-line"></i> Monitoring Turun
                                        Blending</a></li>
                                <li class="nav-item"><a href="{{ url('dashboard/monitoring/storage') }}"
                                        class="nav-link"><i class="mdi mdi-database"></i> Monitoring Storage</a></li>
                                <li class="nav-item"><a href="{{ url('dashboard/mikro/blending/after') }}"
                                        class="nav-link"><i class="mdi mdi-blender"></i> Blending After Adjust</a>
                                </li>
                                <li class="nav-item"><a href="{{ url('dashboard/mikro/monitoring/storage') }}"
                                        class="nav-link"><i class="mdi mdi-database"></i> Monitoring Storage</a></li>
                                <li class="nav-item"><a href="{{ url('dashboard/rm') }}" class="nav-link"><i
                                            class="mdi mdi-chemical-weapon"></i> Dashboard RMPM</a></li>
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- SECTION: MENU UTAMA --}}
                @php
                    $showMenuSection = in_array($userRole, [
                        'Supervisor',
                        'Foreman',
                        'Analis RM',
                        'Operator',
                        'Analis Kimia',
                        'Analis Mikro',
                        'Analis Field',
                    ]);
                @endphp

                @if ($showMenuSection)
                    <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                    {{-- RMPM Menu --}}
                    @if (in_array($userRole, ['Supervisor', 'Foreman', 'Analis RM']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['rmpm.index', 'rmpm.show']) ? 'active' : '' }}"
                                href="{{ route('rmpm.index') }}">
                                <i class="mdi mdi-puzzle-outline"></i> <span data-key="t-widgets">RMPM</span>
                            </a>
                        </li>
                    @endif

                    {{-- Persiapan Masak Menu --}}
                    {{-- @if (in_array($userRole, ['Supervisor', 'Foreman', 'Operator']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['productionbatch.*', 'gga-ggas.index', 'gga-ggas.show', 'blending-awal.index', 'blending-awal.show', 'monitoring-turun-blending.index', 'monitoring-turun-blending.show', 'monitoring-pasteurisasi.index', 'monitoring-pasteurisasi.show', 'monitoring-pasteurisasi.show_batch', 'monitoring-storage-kimia.index', 'monitoring-storage-kimia.show']) ? 'active' : '' }}"
                                href="{{ route('productionbatch.index') }}">
                                <i class="mdi mdi-puzzle-outline"></i> <span data-key="t-widgets">Persiapan Masak</span>
                            </a>
                        </li>
                    @endif --}}

                    {{-- GGA & GGAS Menu --}}
                    @if (in_array($userRole, ['Supervisor', 'Foreman', 'Analis Kimia']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['gga.menu', 'gga.index', 'gga.show', 'gga.show_batch', 'ggas.index', 'ggas.show', 'ggas.show_batch']) ? 'active' : '' }}"
                                href="{{ route('gga.menu') }}">
                                <i class="mdi mdi-flask-outline"></i> <span>GGA & GGAS</span>
                            </a>
                        </li>
                    @endif

                    {{-- Blending Menu --}}
                    @if (in_array($userRole, ['Supervisor', 'Foreman', 'Analis Kimia', 'Analis Mikro', 'Analis Field']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['analisa.blending-awal.menu', 'analisa.blending-awal.index', 'analisa.blending-awal.show', 'analisa.blending-awal.show_batch', 'analisa.blending-awal-mikro.index', 'analisa.blending-awal-mikro.show', 'analisa.blending-awal-mikro.show_batch']) ? 'active' : '' }}"
                                href="{{ route('analisa.blending-awal.menu') }}">
                                <i class="mdi mdi-blender-software"></i> <span>Blending</span>
                            </a>
                        </li>
                    @endif

                    {{-- Monitoring Pasteurisasi & Storage Menu --}}
                    @if (in_array($userRole, ['Supervisor', 'Foreman', 'Analis Kimia', 'Analis Field', 'Analis Mikro']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['monitoring-storage-kimia.index', 'monitoring-storage-kimia.show', 'analisa.monitoring-turun-blending.menu', 'analisa.monitoring-turun-blending.index', 'analisa.monitoring-turun-blending.show', 'analisa.monitoring-turun-blending.show_batch', 'analisa.monitoring-pasteurisasi.index', 'analisa.monitoring-pasteurisasi.show', 'analisa.monitoring-pasteurisasi.show_batch', 'analisa.monitoring-storage-kimia.index', 'analisa.monitoring-storage-kimia.show', 'analisa.monitoring-storage-kimia.show_batch', 'analisa.monitoring-storage-mikro.index', 'analisa.monitoring-storage-mikro.show', 'analisa.monitoring-storage-mikro.show_batch', 'monitoring-storage-before-use.index', 'monitoring-storage-before-use.analisa']) ? 'active' : '' }}"
                                href="{{ route('analisa.monitoring-turun-blending.menu') }}">
                                <i class="mdi mdi-thermometer"></i> <span>Monitoring Pasteurisasi & Storage</span>
                            </a>
                        </li>

                        {{-- Monitoring Filling Menu --}}
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['monitoring-daily-tank.menu', 'monitoring-daily-tank.index', 'monitoring-daily-tank.show', 'analisa.monitoring-daily-tank-mikro.show', 'analisa.monitoring-daily-tank-kimia.show', 'monitoring-ongoing-kimia.index', 'monitoring-ongoing-mikro.index', 'monitoring-ongoing-kimia.show', 'monitoring-ongoing-mikro.analisa']) ? 'active' : '' }}"
                                href="{{ route('monitoring-daily-tank.menu') }}">
                                <i class="mdi mdi-bottle-wine"></i> <span>Monitoring Filling</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs(['shelf-life.index', 'shelf-life.sample.index', 'shelf-life.sample.show', 'shelf-life.checksheet.index', 'shelf-life.analysis-kimia.index', 'shelf-life.analysis-kimia.show', 'shelf-life.analysis-mikro.index', 'shelf-life.analysis-mikro.show']) ? 'active' : '' }}"
                            href="{{ route('shelf-life.index') }}">
                            <i class="mdi mdi-calendar-clock"></i> <span data-key="t-widgets">Shelf Life</span>
                        </a>
                    </li>

                    {{-- Notifikasi Menu --}}
                    @if (in_array($userRole, ['Supervisor', 'Foreman']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['notifications.*']) ? 'active' : '' }}"
                                href="{{ route('notifications.index') }}">
                                <i class="mdi mdi-bell"></i> <span data-key="t-widgets">Notifikasi</span>
                            </a>
                        </li>
                    @endif
                @endif

                {{-- SECTION: MASTER DATA --}}
                @if (in_array($userRole, ['Supervisor', 'Foreman']))
                    <li class="menu-title">
                        <i class="ri-more-fill"></i> <span data-key="t-pages">Master Data</span>
                    </li>

                    {{-- Pengguna Menu (Supervisor only) --}}
                    @if ($userRole === 'Supervisor')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['users.*']) ? 'active' : '' }}"
                                href="{{ route('users.index') }}">
                                <i class="mdi mdi-account-group"></i> <span data-key="t-users">Pengguna</span>
                            </a>
                        </li>
                    @endif

                    {{-- Warna Menu (Foreman only) --}}
                    @if ($userRole === 'Foreman')
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['colors.*']) ? 'active' : '' }}"
                                href="{{ route('colors.index') }}">
                                <i class="mdi mdi-palette"></i> <span data-key="t-colors">Warna</span>
                            </a>
                        </li>
                    @endif
                @endif


                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs(['scan.*']) ? 'active' : '' }}"
                        href="{{ route('scan.index') }}">
                        <i class="mdi mdi-barcode-scan"></i> <span data-key="t-scan">Scan</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
