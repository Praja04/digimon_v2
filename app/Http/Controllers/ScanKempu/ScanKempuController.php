<?php

namespace App\Http\Controllers\ScanKempu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScanKempuController extends Controller
{
    protected string $warehouseApi;

    public function __construct()
    {
        $this->warehouseApi = env('WAREHOUSE_API_URL', 'http://127.0.0.1:8000/api');
    }

    /**
     * Memeriksa apakah user saat ini berhak melakukan Force Scan QC
     * Otoritas: role != 'operator' ATAU memiliki permission 'kempu-qc-force'
     */
    protected function canForceScan(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
            return true;
        }

        if (method_exists($user, 'hasAnyPermission') && $user->hasAnyPermission(['kempu-qc-force', 'super-admin'])) {
            return true;
        }

        $role = strtolower(trim($user->role ?? ''));
        if ($role && $role !== 'operator') {
            return true;
        }

        return false;
    }

    /**
     * Halaman Report Scan Kempu QC (QC PM & QC Proses)
     */
    public function report()
    {
        try {
            $response = Http::timeout(8)->get("{$this->warehouseApi}/kempu/qc/report/stats");
            $statsData = $response->json('data') ?? [];
        } catch (\Throwable $e) {
            $statsData = [];
        }

        return view('app.scan_kempu.report', compact('statsData'));
    }

    /**
     * Halaman Dashboard Monitoring & Traceability Kempu (Dialihkan ke Report)
     */
    public function dashboard()
    {
        return redirect()->route('scan-kempu.report');
    }

    /**
     * Halaman Hub QC PM (Pengecekan Biasa & Cek Incoming Bulk)
     */
    public function pmIndex()
    {
        $response = Http::get("{$this->warehouseApi}/kempu/qc/pm/cards");
        $data = $response->json('data') ?? [];

        $totalQcPmPending = $data['total_qc_pm_pending'] ?? 0;
        $totalSpbPending  = $data['total_spb_pending'] ?? 0;
        $rawCards         = $data['cards'] ?? [];

        // Mapping route internal Digimon
        $cards = [
            'biasa' => array_merge($rawCards['biasa'] ?? [], [
                'route'       => route('scan-kempu.scan', 'qc-pm'),
                'badge_color' => 'primary',
                'icon'        => 'ri-qr-scan-2-line',
            ]),
            'bulk' => array_merge($rawCards['bulk'] ?? [], [
                'route'       => route('scan-kempu.pm.bulk'),
                'badge_color' => 'success',
                'icon'        => 'ri-stack-line',
            ]),
        ];

        if ($this->canForceScan() && isset($rawCards['qc-force'])) {
            $cards['qc-force'] = array_merge($rawCards['qc-force'], [
                'route'       => route('scan-kempu.scan', 'qc-force'),
                'badge_color' => 'danger',
                'icon'        => 'ri-shield-flash-line',
            ]);
        }

        return view('app.scan_kempu.pm.index', compact('cards', 'totalQcPmPending', 'totalSpbPending'));
    }

    /**
     * Halaman Scanner Bulk Incoming QC PM
     */
    public function bulkView()
    {
        return view('app.scan_kempu.pm.bulk');
    }

    /**
     * Halaman Hub QC Proses (Pre Cuci & After Filling)
     */
    public function prosesIndex()
    {
        $response = Http::get("{$this->warehouseApi}/kempu/qc/proses/cards");
        $data = $response->json('data') ?? [];

        $totalPreCuciPending      = $data['total_pre_cuci_pending'] ?? 0;
        $totalAfterFillingPending = $data['total_after_filling_pending'] ?? 0;
        $rawCards                 = $data['cards'] ?? [];

        $cards = [
            'qc-pre-cuci' => array_merge($rawCards['qc-pre-cuci'] ?? [], [
                'route'       => route('scan-kempu.scan', 'qc-pre-cuci'),
                'badge_color' => 'primary',
                'icon'        => 'ri-shield-check-line',
            ]),
            'qc-after-filling' => array_merge($rawCards['qc-after-filling'] ?? [], [
                'route'       => route('scan-kempu.scan', 'qc-after-filling'),
                'badge_color' => 'success',
                'icon'        => 'ri-flask-line',
            ]),
        ];

        if ($this->canForceScan() && isset($rawCards['qc-force'])) {
            $cards['qc-force'] = array_merge($rawCards['qc-force'], [
                'route'       => route('scan-kempu.scan', 'qc-force'),
                'badge_color' => 'danger',
                'icon'        => 'ri-shield-flash-line',
            ]);
        }

        return view('app.scan_kempu.proses.index', compact('cards', 'totalPreCuciPending', 'totalAfterFillingPending'));
    }

    /**
     * Halaman Scanner Individual (Biasa)
     */
    public function scan($type)
    {
        if ($type === 'qc-force' && !$this->canForceScan()) {
            return redirect()->route('scan-kempu.pm.index')
                ->with('error', 'Akses ditolak: Hanya user dengan otoritas khusus QC (Non-Operator) yang dapat mengakses Force Scan.');
        }

        $response = Http::get("{$this->warehouseApi}/kempu/qc/configs");
        $configs = $response->json('data') ?? [];

        if (!isset($configs[$type])) {
            return redirect()->route('scan-kempu.pm.index')->with('error', 'Tipe QC tidak valid.');
        }

        $card = $configs[$type];
        return view('app.scan_kempu.proses.scan', compact('card'));
    }

    /**
     * API Proxy: Mengambil data list kempu untuk datatable
     */
    public function traceabilityData(Request $request)
    {
        try {
            $response = Http::get("{$this->warehouseApi}/kempu/traceability/data", $request->all());
            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil data dari server Warehouse: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * API Proxy: Mengambil riwayat tracking per kempu (Timeline)
     */
    public function traceabilityHistory($id)
    {
        try {
            $response = Http::get("{$this->warehouseApi}/kempu/traceability/history/{$id}");
            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil data riwayat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Proxy: Mengambil statistik KPI & Charts untuk Dashboard
     */
    public function traceabilityStats()
    {
        try {
            $response = Http::get("{$this->warehouseApi}/kempu/traceability/stats");
            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API Proxy: Mengambil statistik KPI Report QC
     */
    public function reportStats(Request $request)
    {
        try {
            $response = Http::timeout(8)->get("{$this->warehouseApi}/kempu/qc/report/stats", $request->all());
            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil statistik report: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * API Proxy: Mengambil data report QC (Server-side Pagination & Filter)
     */
    public function reportData(Request $request)
    {
        try {
            $response = Http::timeout(10)->get("{$this->warehouseApi}/kempu/qc/report/data", $request->all());
            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil data report: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Export Report CSV untuk QC Kempu
     */
    public function reportExport(Request $request)
    {
        try {
            $url = "{$this->warehouseApi}/kempu/qc/report/export?" . http_build_query($request->all());
            return redirect($url);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh report: ' . $e->getMessage());
        }
    }
}