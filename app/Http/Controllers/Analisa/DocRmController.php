<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\LaporanKalkulasiRmExport;
use App\Http\Controllers\Controller;
use App\Models\IdentitasRM;
use App\Models\MasterStandarRm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DocRmController extends Controller
{
    /**
     * Tampilkan Halaman Form & Preview Dokumen Hasil Kalkulasi Raw Material
     */
    public function index(Request $request)
    {
        // Prioritaskan daftar Raw Material yang bertipe Gula (Gula Tebu & Gula Kelapa)
        $identitasList = IdentitasRM::where('jenis', 'LIKE', '%gula%')
            ->orderBy('tanggal_kedatangan', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $selectedId = $request->input('id');
        if (!$selectedId || !$identitasList->contains('id', $selectedId)) {
            // Default ke data Gula Tebu terbaru terlebih dahulu
            $defaultTebu = $identitasList->first(function ($item) {
                $j = strtoupper(trim($item->jenis ?? ''));
                return str_contains($j, 'TEBU') || (!str_contains($j, 'KELAPA') && str_contains($j, 'GULA'));
            });
            $selectedId = $defaultTebu?->id ?? $identitasList->first()?->id;
        }

        $docData = null;
        if ($selectedId) {
            $docData = $this->getDocumentData($selectedId);
        }

        return view('app.doc_rm.index', [
            'identitasList' => $identitasList,
            'selectedId'    => $selectedId,
            'docData'       => $docData,
        ]);
    }

    /**
     * Download Excel Dokumen Kalkulasi RM
     */
    public function exportExcel(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return redirect()->back()->with('error', 'Silakan pilih data kedatangan RM terlebih dahulu.');
        }

        $identitas = IdentitasRM::with([
            'analisaShortTerm',
            'analisaGaramGula',
        ])->findOrFail($id);

        if ($request->has('standart_bps')) {
            $standartBps = $request->input('standart_bps');
            $identitas->standart_bps = $standartBps;
            DB::table('identitas_rm')->where('id', $id)->update(['standart_bps' => $standartBps ?: null]);
        }

        $cleanSpb = preg_replace('/[^A-Za-z0-9_\-]/', '_', $identitas->no_spb ?? ('ID_' . $id));
        $cleanJenis = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper(trim($identitas->jenis ?? 'RM')));
        $timestamp = now()->format('Ymd_His');

        $fileName = "LAPORAN_KALKULASI_{$cleanJenis}_{$cleanSpb}_{$timestamp}.xlsx";

        return Excel::download(
            new LaporanKalkulasiRmExport($identitas),
            $fileName
        );
    }

    /**
     * Update Standart BPS secara langsung dari input manual user
     */
    public function updateStandartBps(Request $request)
    {
        $id = $request->input('id');
        $standartBps = trim((string)$request->input('standart_bps', ''));

        if (!$id) {
            return response()->json(['status' => 'error', 'message' => 'ID RM tidak valid'], 400);
        }

        DB::table('identitas_rm')->where('id', $id)->update([
            'standart_bps' => $standartBps ?: null,
            'updated_at'   => now(),
        ]);

        return response()->json([
            'status'       => 'success',
            'message'      => 'Standart BPS berhasil diperbarui',
            'standart_bps' => $standartBps,
        ]);
    }

    /**
     * Helper untuk mengambil dan mengkalkulasi seluruh parameter RM
     */
    public function getDocumentData($id): ?array
    {
        $identitas = IdentitasRM::with([
            'analisaShortTerm',
            'analisaGaramGula',
        ])->find($id);

        if (!$identitas) {
            return null;
        }

        $jenisRaw = trim($identitas->jenis ?? 'Gula Tebu');
        $jenisUpper = strtoupper($jenisRaw);
        $isGulaKelapa = str_contains($jenisUpper, 'KELAPA');
        $isGulaTebu = str_contains($jenisUpper, 'TEBU') || (!$isGulaKelapa && str_contains($jenisUpper, 'GULA'));

        $title = $isGulaTebu ? 'GULA TEBU' : ($isGulaKelapa ? 'GULA KELAPA' : $jenisUpper);
        $displayJenis = $isGulaTebu ? 'Gula Tebu' : ($isGulaKelapa ? 'Gula Kelapa' : ucwords(strtolower($jenisRaw)));

        // Ambil data sampel analisa (Jika ada monitoring/long term, hitung rata-rata gabungan)
        $samples = $identitas->analisaShortTerm ?? collect();
        $hasMonitoring = $samples->contains(fn($s) => strtolower($s->kategori ?? '') === 'monitoring');
        if ($hasMonitoring) {
            $incomingSamples = $samples;
        } else {
            $incomingSamples = $samples->filter(function ($s) {
                return empty($s->kategori) || in_array(strtolower($s->kategori), ['incoming', 'sta']);
            });
            if ($incomingSamples->isEmpty()) {
                $incomingSamples = $samples;
            }
        }
        if ($incomingSamples->isEmpty() && $identitas->analisaGaramGula->isNotEmpty()) {
            $incomingSamples = $identitas->analisaGaramGula;
        }

        $sampleCount = $incomingSamples->count();

        // Arrays numerik
        $phVals = $incomingSamples->pluck('ph')->filter(fn($v) => $v !== null && $v !== '')->map(fn($v) => (float)$v)->values();
        $brixVals = $incomingSamples->pluck('brix')->filter(fn($v) => $v !== null && $v !== '')->map(fn($v) => (float)$v)->values();
        $kotoranVals = $incomingSamples->pluck('kotoran')->filter(fn($v) => $v !== null && $v !== '')->map(fn($v) => (float)$v)->values();
        
        $kaField = $incomingSamples->first()?->offsetExists('%ka') ? '%ka' : 'ka';
        $kaVals = $incomingSamples->pluck($kaField)->filter(fn($v) => $v !== null && $v !== '')->map(fn($v) => (float)$v)->values();
        $organoVals = $incomingSamples->pluck('organo')->filter(fn($v) => !empty($v))->map(fn($v) => trim((string)$v))->values();

        // Ambil Standar Mutu dari Master Data (MasterStandarRm)
        $cleanJenis = strtoupper(trim((string)$identitas->jenis));
        $standards = MasterStandarRm::where('status', true)
            ->whereHas('jenisBahan', function ($q) use ($cleanJenis) {
                $q->whereRaw('UPPER(nama) = ?', [$cleanJenis]);
            })
            ->get();

        if ($standards->isEmpty()) {
            $standards = MasterStandarRm::where('status', true)
                ->whereHas('jenisBahan', function ($q) use ($isGulaKelapa) {
                    if ($isGulaKelapa) {
                        $q->where('nama', 'LIKE', '%KELAPA%');
                    } else {
                        $q->where('nama', 'LIKE', '%TEBU%');
                    }
                })
                ->get();
        }

        // 1. Standar pH
        $stdPh = $standards->first(fn($s) => stripos($s->parameter, 'ph') !== false);
        $phMinStd = $stdPh?->min_standar ?? ($isGulaKelapa ? 5.30 : 5.50);
        $phMaxStd = $stdPh?->max_standar ?? 6.50;
        $phStdLabel = number_format($phMinStd, 2, ',', '.') . ' - ' . number_format($phMaxStd, 2, ',', '.');

        // 2. Standar Brix
        $stdBrix = $standards->first(fn($s) => stripos($s->parameter, 'brix') !== false);
        $brixMinStd = $stdBrix?->min_standar ?? 8.7;
        $brixStdLabel = 'Min, ' . number_format($brixMinStd, 1, ',', '.');

        // 3. Standar %Kotoran
        $stdKot = $standards->first(fn($s) => stripos($s->parameter, 'kotoran') !== false);
        $kotMaxStd = $stdKot?->max_standar ?? 10.0;
        $kotStdLabel = 'Max, ' . number_format($kotMaxStd, 0, ',', '.') . '% w/w';

        // 4. Standar %Kadar Air
        $stdKa = $standards->first(fn($s) => stripos($s->parameter, 'kadar air') !== false || stripos($s->parameter, 'ka') !== false);
        $kaMaxStd = $stdKa?->max_standar ?? ($isGulaTebu ? 3.0 : 8.0);
        $kaStdLabel = 'Max, ' . number_format($kaMaxStd, 0, ',', '.') . '%';

        // 1. pH calculations
        $avgPh = $phVals->isNotEmpty() ? $phVals->avg() : null;
        $minPh = $phVals->isNotEmpty() ? $phVals->min() : null;
        $maxPh = $phVals->isNotEmpty() ? $phVals->max() : null;

        $totalPh = $phVals->count() ?: 1;
        $cntPhUnder = $phVals->filter(fn($v) => $v < $phMinStd)->count();
        $pctPhUnder = ($cntPhUnder / $totalPh) * 100;

        $cntPhInSpec = $phVals->filter(fn($v) => $v >= $phMinStd && $v <= $phMaxStd)->count();
        $pctPhInSpec = ($cntPhInSpec / $totalPh) * 100;

        $cntPhOver = $phVals->filter(fn($v) => $v > $phMaxStd)->count();
        $pctPhOver = ($cntPhOver / $totalPh) * 100;

        $pctPhUnder55 = $pctPhUnder;
        $pctPhUnder53 = $pctPhUnder;
        $pctPhOver65 = $pctPhOver;
        $cntPhOver65_70 = $phVals->filter(fn($v) => $v > $phMaxStd && $v <= ($phMaxStd + 0.50))->count();
        $pctPhOver65_70 = ($cntPhOver65_70 / $totalPh) * 100;
        $cntPhOver70 = $phVals->filter(fn($v) => $v > ($phMaxStd + 0.50))->count();
        $pctPhOver70 = ($cntPhOver70 / $totalPh) * 100;

        // 2. Brix calculations
        $avgBrix = $brixVals->isNotEmpty() ? $brixVals->avg() : null;
        $minBrix = $brixVals->isNotEmpty() ? $brixVals->min() : null;
        $maxBrix = $brixVals->isNotEmpty() ? $brixVals->max() : null;

        $totalBrix = $brixVals->count() ?: 1;
        $cntBrixUnder = $brixVals->filter(fn($v) => $v < $brixMinStd)->count();
        $pctBrixUnder = ($cntBrixUnder / $totalBrix) * 100;

        $cntBrixInSpec = $brixVals->filter(fn($v) => $v >= $brixMinStd)->count();
        $pctBrixInSpec = ($cntBrixInSpec / $totalBrix) * 100;

        // 3. %Kotoran calculations
        $avgKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->avg() : null;
        $minKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->min() : null;
        $maxKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->max() : null;

        $totalKot = $kotoranVals->count() ?: 1;
        $cntKotInSpec = $kotoranVals->filter(fn($v) => $v <= $kotMaxStd)->count();
        $pctKotInSpec = ($cntKotInSpec / $totalKot) * 100;

        $cntKotOver = $kotoranVals->filter(fn($v) => $v > $kotMaxStd)->count();
        $pctKotOver = ($cntKotOver / $totalKot) * 100;

        $cntKot10_14 = $kotoranVals->filter(fn($v) => $v > $kotMaxStd && $v <= ($kotMaxStd + 4.0))->count();
        $cntKotOver14 = $kotoranVals->filter(fn($v) => $v > ($kotMaxStd + 4.0))->count();
        $pctKot10_14 = ($cntKot10_14 / $totalKot) * 100;
        $pctKotOver14 = ($cntKotOver14 / $totalKot) * 100;

        // 4. Organoleptik
        $totalOrgano = $organoVals->count() ?: 1;
        if ($isGulaKelapa) {
            $organoDefs = [
                ['label' => '1, OK', 'key' => 'ok', 'ket' => 'Terasa/dominan rasa gula kelapa', 'isMurni' => true],
                ['label' => '2, Agak asam', 'key' => 'agak_asam', 'ket' => 'Terasa/dominan rasa gula kelapa', 'isMurni' => true],
                ['label' => '3, Asam', 'key' => 'asam', 'ket' => 'Terasa/dominan rasa gula kelapa', 'isMurni' => true],
                ['label' => '4, Agak pahit', 'key' => 'agak_pahit', 'ket' => 'Terasa/dominan rasa gula kelapa', 'isMurni' => true],
                ['label' => '5, Pahit', 'key' => 'pahit', 'ket' => 'Terasa/dominan rasa gula kelapa', 'isMurni' => false],
                ['label' => '6, Sagu', 'key' => 'sagu', 'ket' => 'Terasa/dominan rasa gula kelapa', 'isMurni' => true],
                ['label' => '7, Kapur', 'key' => 'kapur', 'ket' => 'Terasa/dominan rasa gula kelapa', 'isMurni' => true],
                ['label' => '8, Lain-lain (tengik, kapur, endapan)', 'key' => 'lain', 'ket' => 'Terasa/dominan rasa gula kelapa', 'isMurni' => true],
                ['label' => '9, Campuran', 'key' => 'campuran', 'ket' => 'Tidak terasa/tidak dominan rasa gula kelapa', 'isMurni' => false],
            ];
            $ketMurni = '';
        } else {
            $organoDefs = [
                ['label' => '1, OK', 'key' => 'ok', 'ket' => 'Terasa/dominan rasa gula tebu', 'isMurni' => true],
                ['label' => '2, Pahit', 'key' => 'pahit', 'ket' => 'Terasa/dominan rasa gula tebu', 'isMurni' => false],
                ['label' => '3, Agak pahit', 'key' => 'agak_pahit', 'ket' => 'Terasa/dominan rasa gula tebu', 'isMurni' => true],
                ['label' => '4, Asam dan pH < 5,5', 'key' => 'asam', 'ket' => 'Terasa/dominan rasa gula tebu', 'isMurni' => true],
                ['label' => '5, Agak asam dan pH normal', 'key' => 'agak_asam', 'ket' => 'Terasa/dominan rasa gula tebu', 'isMurni' => true],
                ['label' => '6, Lain-lain (tengik, kapur, endapan)', 'key' => 'lain', 'ket' => 'Terasa/dominan rasa gula tebu', 'isMurni' => true],
                ['label' => '7, Campuran', 'key' => 'campuran', 'ket' => 'Tidak terasa/tidak dominan rasa gula tebu', 'isMurni' => false],
            ];
            $ketMurni = '';
        }

        $organoItems = [];
        $murniPctTotal = 0;
        $campuranPctTotal = 0;

        foreach ($organoDefs as $def) {
            $cnt = $organoVals->filter(function ($val) use ($def) {
                $v = strtolower(trim((string)$val));
                switch ($def['key']) {
                    case 'ok':
                        return in_array($v, ['ok', '1', 'sesuai', 'normal']);
                    case 'pahit':
                        return $v === 'pahit' || ($v !== 'agak pahit' && str_contains($v, 'pahit'));
                    case 'agak_pahit':
                        return str_contains($v, 'agak pahit');
                    case 'asam':
                        return str_contains($v, 'asam dan ph < 5') || ($v === 'asam');
                    case 'agak_asam':
                        return str_contains($v, 'agak asam') || str_contains($v, 'ph normal');
                    case 'asin':
                        return str_contains($v, 'asin');
                    case 'sagu':
                        return str_contains($v, 'sagu');
                    case 'kapur':
                        return str_contains($v, 'kapur') && !str_contains($v, 'lain');
                    case 'lain':
                        return str_contains($v, 'lain') || str_contains($v, 'tengik') || str_contains($v, 'endapan');
                    case 'campuran':
                        return str_contains($v, 'campur');
                    default:
                        return false;
                }
            })->count();

            $pct = round(($cnt / $totalOrgano) * 100);
            if ($def['isMurni']) {
                $murniPctTotal += $pct;
            } else {
                $campuranPctTotal += $pct;
            }

            $organoItems[] = [
                'label'   => $def['label'],
                'key'     => $def['key'],
                'pct'     => $pct,
                'ket'     => $def['ket'],
                'isMurni' => $def['isMurni'],
            ];
        }

        // 5. %Kadar Air
        $avgKa = $kaVals->isNotEmpty() ? $kaVals->avg() : null;
        $minKa = $kaVals->isNotEmpty() ? $kaVals->min() : null;
        $maxKa = $kaVals->isNotEmpty() ? $kaVals->max() : null;

        $totalKa = $kaVals->count() ?: 1;
        $cntKaInSpec = $kaVals->filter(fn($v) => $v <= $kaMaxStd)->count();
        $pctKaInSpec = ($cntKaInSpec / $totalKa) * 100;

        $cntKaOver = $kaVals->filter(fn($v) => $v > $kaMaxStd)->count();
        $pctKaOver = ($cntKaOver / $totalKa) * 100;

        return [
            'identitas'       => $identitas,
            'id'              => $identitas->id,
            'jenis'           => $jenisUpper,
            'title'           => $title,
            'display_jenis'   => $displayJenis,
            'is_supported'    => ($isGulaTebu || $isGulaKelapa),
            'is_gula_kelapa'  => $isGulaKelapa,
            'is_gula_tebu'    => $isGulaTebu,
            'tgl_incoming'    => $identitas->tanggal_kedatangan ? Carbon::parse($identitas->tanggal_kedatangan)->format('d-M-Y') : '-',
            'vendor'          => $identitas->supplier ?? '-',
            'spb'             => $identitas->no_spb ?? '-',
            'standart_bps'    => $identitas->standart_bps ?? '',
            'nopol'           => $identitas->no_plat ?? '-',
            'qty'             => $identitas->jumlah_kedatangan ? number_format((float)$identitas->jumlah_kedatangan, 0, ',', '.') : '-',
            'sample_count'    => $sampleCount,
            
            // Standar Dinamis dari Master Data (MasterStandarRm)
            'ph_min_std'      => $phMinStd,
            'ph_max_std'      => $phMaxStd,
            'ph_std_label'    => $phStdLabel,
            'brix_min_std'    => $brixMinStd,
            'brix_std_label'  => $brixStdLabel,
            'kot_max_std'     => $kotMaxStd,
            'kot_std_label'   => $kotStdLabel,
            'ka_max_std'      => $kaMaxStd,
            'ka_std_label'    => $kaStdLabel,

            // pH
            'avg_ph'          => $avgPh,
            'min_ph'          => $minPh,
            'max_ph'          => $maxPh,
            'pct_ph_under'    => $pctPhUnder,
            'pct_ph_inspec'   => $pctPhInSpec,
            'pct_ph_over'     => $pctPhOver,
            'pct_ph_under_55' => $pctPhUnder55,
            'pct_ph_under_53' => $pctPhUnder53,
            'pct_ph_over_65'  => $pctPhOver65,
            'pct_ph_over_65_70' => $pctPhOver65_70,
            'pct_ph_over_70'  => $pctPhOver70,

            // Brix
            'avg_brix'        => $avgBrix,
            'min_brix'        => $minBrix,
            'max_brix'        => $maxBrix,
            'pct_brix_under'  => $pctBrixUnder,
            'pct_brix_inspec' => $pctBrixInSpec,

            // %Kotoran
            'avg_kotoran'     => $avgKotoran,
            'min_kotoran'     => $minKotoran,
            'max_kotoran'     => $maxKotoran,
            'pct_kot_inspec'  => $pctKotInSpec,
            'pct_kot_over'    => $pctKotOver,
            'pct_kot_10_14'   => $pctKot10_14,
            'pct_kot_over_14' => $pctKotOver14,

            // Organo
            'organo_items'    => $organoItems,
            'murni_pct'       => $murniPctTotal,
            'campuran_pct'    => $campuranPctTotal,
            'ket_murni'       => $ketMurni,

            // %KA
            'avg_ka'          => $avgKa,
            'min_ka'          => $minKa,
            'max_ka'          => $maxKa,
            'pct_ka_inspec'   => $pctKaInSpec,
            'pct_ka_over_8'   => $pctKaOver,
            'pct_ka_over'     => $pctKaOver,
        ];
    }
}
