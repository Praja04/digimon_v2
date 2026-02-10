<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Papan Informasi Storage | Digital Monitoring</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/images/icon-utility/kecap.png">

    <style>
        :root {
            --bg-dark: #0f172a;
            --accent-blue: #0284c7;
            --danger-red: #b91c1c;
            --warning-orange: #ea580c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: url('{{ asset('assets/images/banner/banner_5.png') }}') center center/cover no-repeat fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            color: #fff;
        }

        .page-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(30, 60, 114, 0.9) 0%, rgba(42, 82, 152, 0.8) 100%);
            z-index: 1;
        }

        .main-container {
            position: relative;
            z-index: 2;
            padding: 12px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 10px 20px;
            margin-bottom: 12px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .logo-qc {
            max-width: 110px;
            filter: drop-shadow(0 8px 15px rgba(0, 0, 0, 0.4));
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .storage-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 8px;
            flex-grow: 1;
        }

        .tank-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 8px;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease;
            font-size: 0.75rem;
        }

        .tank-card:hover {
            transform: translateY(-3px);
        }

        .tank-card.updating {
            opacity: 0.7;
        }

        .tank-id {
            color: var(--accent-blue);
            font-weight: 800;
            font-size: 1rem;
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .prod-label {
            font-weight: 700;
            color: var(--danger-red);
            line-height: 1.2;
            min-height: 32px;
            margin-bottom: 4px;
        }

        .data-list {
            flex-grow: 1;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #f8fafc;
            padding: 1px 0;
            font-size: 0.68rem;
        }

        .st-badge {
            font-size: 0.6rem;
            font-weight: 800;
            text-align: center;
            padding: 4px;
            border-radius: 4px;
            margin-top: 6px;
            text-transform: uppercase;
        }

        .bg-done {
            background: #dcfce7;
            color: #15803d;
        }

        .bg-progress {
            background: #fef9c3;
            color: #a16207;
        }

        .bg-warning {
            background: #ffedd5;
            color: #ea580c;
            border: 1px solid #fdba74;
        }

        .bg-cleaning {
            background: #f1f5f9;
            color: #64748b;
        }

        .bg-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .sidebar-wrapper {
            width: 230px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 10px;
            color: #1e293b;
            border-left: 5px solid var(--accent-blue);
        }

        .info-card h6 {
            font-weight: 800;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        #live-clock {
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .text-info {
            color: #0284c7 !important;
        }

        .refresh-indicator {
            display: inline-block;
            margin-left: 10px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .refresh-indicator.active {
            opacity: 1;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .refresh-indicator.active i {
            animation: spin 1s linear infinite;
        }
    </style>
</head>

<body>
    <div class="page-overlay"></div>

    <div class="main-container">
        <header class="glass-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-0 fw-bold">
                    PAPAN INFORMASI STORAGE
                    <span class="refresh-indicator" id="refreshIndicator">
                        <i class="ri-loader-4-line"></i>
                    </span>
                </h3>
            </div>

            <img src="{{ asset('assets/images/icon-utility/kecap.png') }}" alt="Logo" class="logo-qc">

            <div class="d-flex align-items-center gap-4">
                <div class="text-end border-end pe-3">
                    <div id="live-clock">00:00:00</div>
                    <small class="fw-bold">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</small>
                </div>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="toggleFullscreen()">
                    <i class="ri-fullscreen-fill"></i>
                </button>
            </div>
        </header>

        <div class="d-flex gap-2 flex-grow-1 overflow-hidden">
            <div class="flex-grow-1 overflow-auto pe-1">
                <div class="storage-grid" id="storageGrid">
                    @foreach ($tanks as $tank)
                        <div class="tank-card {{ $tank['st'] == 'WARNING' || $tank['st'] == 'NOT OK' || $tank['st'] == 'REJECT' ? 'border border-danger border-2' : '' }}"
                            data-tank-id="{{ $tank['id'] }}">
                            <div class="tank-id">
                                {{ $tank['id'] }}
                                @if (!empty($tank['line']))
                                    <span class="badge bg-primary" style="font-size: 0.5rem;">{{ $tank['line'] }}</span>
                                @endif
                            </div>
                            <div class="prod-label">{{ $tank['prod'] }}</div>
                            <div class="data-list">
                                @if (isset($tank['tgl']) && $tank['tgl'])
                                    <div class="data-row"><span>Tgl:</span> <b>{{ $tank['tgl'] }}</b></div>
                                @endif
                                @if (isset($tank['vol']) && $tank['vol'])
                                    <div class="data-row"><span>Vol:</span> <b>{{ $tank['vol'] }}</b></div>
                                @endif
                                @if (isset($tank['batch']) && $tank['batch'])
                                    <div class="data-row"><span>Batch:</span> <b>{{ $tank['batch'] }}</b></div>
                                @endif
                                @if (isset($tank['batch_info']) && $tank['batch_info'])
                                    <div class="data-row text-info"><span>B.Info:</span>
                                        <b>{{ $tank['batch_info'] }}</b>
                                    </div>
                                @endif
                                @if (isset($tank['exp']))
                                    <div class="data-row text-danger"><span>EXP:</span> <b>{{ $tank['exp'] }}</b>
                                    </div>
                                @endif
                                @if (isset($tank['done']))
                                    <div class="data-row"><span>Done:</span> <b>{{ $tank['done'] }}</b></div>
                                @endif
                            </div>
                            <div class="st-badge {{ $tank['css'] }}">{{ $tank['st'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="sidebar-wrapper">
                <div class="info-card">
                    <h6>Daily K1</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between"><span>Lvl:</span><b>± 1000 L</b></div>
                        <div class="d-flex justify-content-between"><span>Asal:</span><b>B4/N</b></div>
                        <div class="d-flex justify-content-between"><span>Tgl:</span><b>22/01/26</b></div>
                    </div>
                </div>
                <div class="info-card">
                    <h6>Daily K2</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between"><span>Lvl:</span><b>3000 L</b></div>
                        <div class="d-flex justify-content-between"><span>Asal:</span><b>D5/E</b></div>
                    </div>
                </div>
                <div class="info-card">
                    <h6>Daily K3</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between"><span>Lvl:</span><b>± 3800 L</b></div>
                        <div class="d-flex justify-content-between"><span>Asal:</span><b>D2/B</b></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Update clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
            document.getElementById('live-clock').textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Fullscreen toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // AJAX Auto-refresh data
        function refreshStorageData() {
            const refreshIndicator = document.getElementById('refreshIndicator');
            refreshIndicator.classList.add('active');

            $.ajax({
                url: '{{ route('storage-information.get-data') }}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateStorageGrid(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error refreshing data:', error);
                },
                complete: function() {
                    setTimeout(() => {
                        refreshIndicator.classList.remove('active');
                    }, 500);
                }
            });
        }

        // Update storage grid dengan data baru
        function updateStorageGrid(tanks) {
            const grid = document.getElementById('storageGrid');

            tanks.forEach((tank, index) => {
                const tankCard = grid.children[index];
                if (!tankCard) return;

                // Tambah class updating untuk smooth transition
                tankCard.classList.add('updating');

                // Update border untuk warning/danger
                tankCard.classList.remove('border', 'border-danger', 'border-2');
                if (tank.st === 'WARNING' || tank.st === 'NOT OK' || tank.st === 'REJECT') {
                    tankCard.classList.add('border', 'border-danger', 'border-2');
                }

                // Update content
                const prodLabel = tankCard.querySelector('.prod-label');
                if (prodLabel) prodLabel.textContent = tank.prod;

                const dataList = tankCard.querySelector('.data-list');
                if (dataList) {
                    let dataHTML = '';

                    if (tank.tgl) {
                        dataHTML += `<div class="data-row"><span>Tgl:</span> <b>${tank.tgl}</b></div>`;
                    }
                    if (tank.vol) {
                        dataHTML += `<div class="data-row"><span>Vol:</span> <b>${tank.vol}</b></div>`;
                    }
                    if (tank.batch) {
                        dataHTML += `<div class="data-row"><span>Batch:</span> <b>${tank.batch}</b></div>`;
                    }
                    if (tank.batch_info) {
                        dataHTML +=
                            `<div class="data-row text-info"><span>B.Info:</span> <b>${tank.batch_info}</b></div>`;
                    }
                    if (tank.exp) {
                        dataHTML += `<div class="data-row text-danger"><span>EXP:</span> <b>${tank.exp}</b></div>`;
                    }
                    if (tank.done) {
                        dataHTML += `<div class="data-row"><span>Done:</span> <b>${tank.done}</b></div>`;
                    }

                    dataList.innerHTML = dataHTML;
                }

                // Update status badge
                const stBadge = tankCard.querySelector('.st-badge');
                if (stBadge) {
                    stBadge.className = 'st-badge ' + tank.css;
                    stBadge.textContent = tank.st;
                }

                // Remove updating class setelah animasi
                setTimeout(() => {
                    tankCard.classList.remove('updating');
                }, 200);
            });
        }

        // Auto refresh setiap 30 detik
        setInterval(refreshStorageData, 30000);

        // Setup CSRF token untuk AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
</body>

</html>
