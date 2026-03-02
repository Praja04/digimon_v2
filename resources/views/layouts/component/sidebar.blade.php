<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('homepage.index') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets') }}/images/icon-utility/kecap.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets') }}/images/icon-utility/kecap.png" alt="" height="90">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('homepage.index') }}" class="logo logo-light">
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
                {{-- Dashboard hanya untuk Head Of Dapartement, Supervisor dan Foreman --}}
                @if (in_array($userRole, ['Head Of Dapartement', 'Supervisor', 'Foreman']))
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
                                <li class="nav-item">
                                    <a href="{{ route('dashboard.proses-masak.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.proses-masak.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-chef-hat"></i>Proses Masak</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('dashboard.pelarutan.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.pelarutan.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-flask"></i>Pelarutan</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('dashboard.blending-awal.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.blending-awal.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-blender"></i>Blending Awal</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('dashboard.blending-after-adjust.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.blending-after-adjust.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-blender-outline"></i>Blending
                                        After Adjust</a>
                                </li>
                                <li class="nav-item"><a href="{{ route('dashboard.monitoring-turun-blending.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.monitoring-turun-blending.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-chart-line"></i> Monitoring Turun
                                        Blending</a>
                                </li>
                                <li class="nav-item"><a href="{{ route('dashboard.monitoring-pasteurisasi.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.monitoring-pasteurisasi.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-thermometer"></i> Monitoring Pasteurisasi</a></li>
                                <li class="nav-item"><a href="{{ route('dashboard.monitoring-storage-kimia.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.monitoring-storage-kimia.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-database"></i> Monitoring Storage</a></li>

                                <li class="nav-item"><a href="{{ route('dashboard.monitoring-storage-mikro.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.monitoring-storage-mikro.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-bacteria"></i> Monitoring Storage Mikro</a>
                                </li>
                                <li class="nav-item"><a href="{{ route('dashboard.monitoring-on-going-mikro.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.monitoring-on-going-mikro.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-bacteria-outline"></i> Monitoring On Going Mikro</a>
                                </li>
                                <li class="nav-item"><a href="{{ route('dashboard.rmpm.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.rmpm.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-chemical-weapon"></i> RMPM</a></li>
                                <li class="nav-item"><a href="{{ route('dashboard.shelf-life.index') }}"
                                        class="nav-link {{ request()->routeIs('dashboard.shelf-life.index') ? 'active' : '' }}"><i
                                            class="mdi mdi-calendar-clock"></i> Shelf Life</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menu-link {{ request()->routeIs(['dashboard.press-test-mesin.index']) ? 'active' : '' }}"
                                        href="#Mesin" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                        aria-controls="Mesin">
                                        <i class="mdi mdi-cog-outline"></i> <span>Mesin</span>
                                    </a>
                                    <div class="collapse menu-dropdown {{ request()->routeIs(['dashboard.press-test-mesin.index', 'dashboard.timbangan-retail.index']) ? 'show' : '' }}"
                                        id="Mesin">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{ route('dashboard.press-test-mesin.index') }}"
                                                    class="nav-link {{ request()->routeIs('dashboard.press-test-mesin.index') ? 'active' : '' }}">
                                                    <i class="mdi mdi-clipboard-text-play-outline"></i> Press Test
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('dashboard.timbangan-retail.index') }}"
                                                    class="nav-link {{ request()->routeIs('dashboard.timbangan-retail.index') ? 'active' : '' }}">
                                                    <i class="mdi mdi-scale-bathroom"></i> Timbangan Retail
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- SECTION: MENU UTAMA --}}
                @php
                    $showMenuSection = in_array($userRole, [
                        'Head Of Dapartement',
                        'Supervisor',
                        'Foreman',
                        'Analis RM',
                        'Analis Kimia',
                        'Analis Mikro',
                        'Analis Field',
                        'Helper',
                    ]);
                @endphp

                @if ($showMenuSection)
                    <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                    {{-- RMPM Menu: Head Of Dapartement, Supervisor, Foreman, Analis RM --}}
                    @if (in_array($userRole, ['Head Of Dapartement', 'Supervisor', 'Foreman', 'Analis RM']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['rmpm.index', 'rmpm.show']) ? 'active' : '' }}"
                                href="{{ route('rmpm.index') }}">
                                <i class="mdi mdi-puzzle-outline"></i> <span data-key="t-widgets">RMPM</span>
                            </a>
                        </li>
                    @endif

                    {{-- Pelarutan Menu: Head Of Dapartement, Supervisor, Foreman, Analis Kimia --}}
                    @if (in_array($userRole, ['Head Of Dapartement', 'Supervisor', 'Foreman', 'Analis Kimia']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['pelarutan-1.menu', 'pelarutan-1.index', 'pelarutan-1.show', 'pelarutan-1.show_batch', 'pelarutan-2.index', 'pelarutan-2.show', 'pelarutan-2.show_batch']) ? 'active' : '' }}"
                                href="{{ route('pelarutan-1.menu') }}">
                                <i class="mdi mdi-flask-outline"></i> <span>Pelarutan</span>
                            </a>
                        </li>
                    @endif

                    {{-- Blending Menu: Head Of Dapartement, Supervisor, Foreman, Analis Kimia, Analis Mikro, Analis Field --}}
                    @if (in_array($userRole, [
                            'Head Of Dapartement',
                            'Supervisor',
                            'Foreman',
                            'Analis Kimia',
                            'Analis Mikro',
                            'Analis Field',
                        ]))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['analisa.blending-awal.menu', 'analisa.blending-awal.index', 'analisa.blending-awal.show', 'analisa.blending-awal.show_batch', 'analisa.blending-awal-mikro.index', 'analisa.blending-awal-mikro.show', 'analisa.blending-awal-mikro.show_batch']) ? 'active' : '' }}"
                                href="{{ route('analisa.blending-awal.menu') }}">
                                <i class="mdi mdi-blender-software"></i> <span>Blending</span>
                            </a>
                        </li>
                    @endif

                    {{-- Monitoring Pasteurisasi & Storage Menu: Head Of Dapartement, Supervisor, Foreman, Analis Kimia, Analis Field, Analis Mikro --}}
                    @if (in_array($userRole, [
                            'Head Of Dapartement',
                            'Supervisor',
                            'Foreman',
                            'Analis Kimia',
                            'Analis Field',
                            'Analis Mikro',
                        ]))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['monitoring-storage-kimia.index', 'monitoring-storage-kimia.show', 'analisa.monitoring-turun-blending.menu', 'analisa.monitoring-turun-blending.index', 'analisa.monitoring-turun-blending.show', 'analisa.monitoring-turun-blending.show_batch', 'analisa.monitoring-pasteurisasi.index', 'analisa.monitoring-pasteurisasi.show', 'analisa.monitoring-pasteurisasi.show_batch', 'analisa.monitoring-storage-kimia.index', 'analisa.monitoring-storage-kimia.show', 'analisa.monitoring-storage-kimia.show_batch', 'analisa.monitoring-storage-mikro.index', 'analisa.monitoring-storage-mikro.show', 'analisa.monitoring-storage-mikro.show_batch', 'monitoring-storage-before-use.index', 'monitoring-storage-before-use.analisa']) ? 'active' : '' }}"
                                href="{{ route('analisa.monitoring-turun-blending.menu') }}">
                                <i class="mdi mdi-thermometer"></i> <span>Monitoring Pasteurisasi & Storage</span>
                            </a>
                        </li>

                        {{-- Monitoring Filling Menu --}}
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['monitoring-daily-tank.menu', 'monitoring-daily-tank.index', 'monitoring-daily-tank.show', 'analisa.monitoring-daily-tank-mikro.show', 'analisa.monitoring-daily-tank-kimia.show', 'monitoring-ongoing-kimia.index', 'monitoring-ongoing-mikro.index', 'monitoring-ongoing-kimia.show', 'monitoring-ongoing-kimia.analisa', 'monitoring-ongoing-mikro.analisa']) ? 'active' : '' }}"
                                href="{{ route('monitoring-daily-tank.menu') }}">
                                <i class="mdi mdi-bottle-wine"></i> <span>Monitoring Filling</span>
                            </a>
                        </li>
                    @endif

                    {{-- Shelf Life Menu: Head Of Dapartement, Supervisor, Foreman, Helper, Analis Kimia, Analis Mikro --}}
                    @if (in_array($userRole, ['Head Of Dapartement', 'Supervisor', 'Foreman', 'Helper', 'Analis Kimia', 'Analis Mikro']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['shelf-life.index', 'shelf-life.sample.index', 'shelf-life.sample.show', 'shelf-life.checksheet.index', 'shelf-life.analysis-kimia.index', 'shelf-life.analysis-kimia.show', 'shelf-life.analysis-mikro.index', 'shelf-life.analysis-mikro.show', 'shelf-life.result.index']) ? 'active' : '' }}"
                                href="{{ route('shelf-life.index') }}">
                                <i class="mdi mdi-calendar-clock"></i> <span data-key="t-widgets">Shelf Life</span>
                            </a>
                        </li>
                    @endif

                    @if (in_array($userRole, ['Head Of Dapartement', 'Supervisor', 'Foreman', 'Helper']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['press-test-data.index']) ? 'active' : '' }}"
                                href="{{ route('press-test-data.index') }}">
                                <i class="mdi mdi-clipboard-text-play-outline"></i> <span data-key="t-widgets">Press
                                    Test
                                    Data</span>
                            </a>
                        </li>
                    @endif

                    {{-- Scan Menu: Analis Kimia, Analis Mikro --}}
                    @if (in_array($userRole, ['Analis Kimia', 'Analis Mikro']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['scan.*']) ? 'active' : '' }}"
                                href="{{ route('scan.index') }}">
                                <i class="mdi mdi-barcode-scan"></i> <span data-key="t-scan">Scan</span>
                            </a>
                        </li>
                    @endif

                    {{-- Notifikasi Menu: Head Of Dapartement, Supervisor, Foreman --}}
                    @if (in_array($userRole, ['Head Of Dapartement', 'Supervisor', 'Foreman']))
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs(['notifications.*']) ? 'active' : '' }}"
                                href="{{ route('notifications.index') }}">
                                <i class="mdi mdi-bell"></i> <span data-key="t-widgets">Notifikasi</span>
                            </a>
                        </li>
                    @endif
                @endif

                {{-- SECTION: MASTER DATA --}}
                @if (in_array($userRole, ['Head Of Dapartement', 'Supervisor', 'Foreman']))
                    <li class="menu-title">
                        <i class="ri-more-fill"></i> <span data-key="t-pages">Master Data</span>
                    </li>

                    {{-- Pengguna Menu (Head Of Dapartement, Supervisor only) --}}
                    @if (in_array($userRole, ['Head Of Dapartement', 'Supervisor']))
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

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
