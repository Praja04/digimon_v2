<?php

namespace App\Http\Controllers\Analisa;

use App\Exports\LaporanKalkulasiRmExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RMPMController;
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
        $identitasList = IdentitasRM::with('analisaSampel')
            ->orderBy('tanggal_kedatangan', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $selectedId = $request->input('id');
        if (!$selectedId || !$identitasList->contains('id', $selectedId)) {
            $defaultItem = $identitasList->first(fn($item) => $item->analisaSampel && $item->analisaSampel->isNotEmpty()) ?? $identitasList->first();
            $selectedId = $defaultItem?->id;
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
            'samplingDokumen',
            'samplingKondisiMobil',
            'samplingFisikKemasan',
            'samplingFisikRaw',
            'KonfirmasiKedatangan.diterimaBy',
            'KonfirmasiKedatangan.dianalisaBy',
            'analisaSampel',
            'analisaShortTerm',
            'analisaGaramGula',
            'analisaBahanKering',
            'analisaBahanCair',
            'analisaLongTerm',
        ])->findOrFail($id);

        if ($request->has('standart_bps')) {
            $standartBps = $request->input('standart_bps');
            $identitas->standart_bps = $standartBps;
            DB::table('identitas_rm')->where('id', $id)->update(['standart_bps' => $standartBps ?: null]);
        }

        $cleanSpb = preg_replace('/[^A-Za-z0-9_\-]/', '_', $identitas->no_spb ?? ('ID_' . $id));
        $cleanJenis = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtoupper(trim($identitas->jenis ?? 'RM')));
        $timestamp = now()->format('Ymd_His');

        $fileName = "LAPORAN_ANALISIS_{$cleanJenis}_{$cleanSpb}_{$timestamp}.xlsx";

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
     * Resolusi jenis template laporan berdasarkan nama/jenis material
     */
    public static function resolveTemplateType(string $jenisRaw): string
    {
        $upper = strtoupper(trim($jenisRaw));

        if (str_contains($upper, 'GARAM')) {
            return 'garam';
        }
        if (str_contains($upper, 'SARI KECAP') || str_contains($upper, 'SKM')) {
            return 'skm';
        }
        if (str_contains($upper, 'CARAMEL') || str_contains($upper, 'KARAMEL')) {
            return 'karamel';
        }
        if (str_contains($upper, 'GLUKOSA') || str_contains($upper, 'CORN SYRUP') || str_contains($upper, 'HFCS') || str_contains($upper, 'GULA CAIR')) {
            return 'gula_cair';
        }
        if (str_contains($upper, 'MSG') || str_contains($upper, 'ND 17103') || str_contains($upper, 'ND 08505') || str_contains($upper, 'ND17103') || str_contains($upper, 'ND08505') || str_contains($upper, 'MINOR')) {
            return 'minor';
        }
        return 'gula'; // Default: Gula Tebu, Gula Kelapa, Gula Kelapa B, Gula Rafinasi
    }

    /**
     * Metadata & Konfigurasi Kolom untuk masing-masing template laporan
     */
    public static function getTemplateMeta(string $type): array
    {
        switch ($type) {
            case 'garam':
                return [
                    'type'         => 'garam',
                    'doc_no'       => 'FRM/QRP/01/002/001',
                    'title'        => 'HASIL PENGECEKAN FISIK DAN ANALISIS GARAM',
                    'kemasan'      => 'zak',
                    'param_keys'   => ['fisik', 'ka', 'kotoran', 'organo', 'warna', 'aroma', 'nacl'],
                    'param_labels' => ['Fisik', '% KA', '% Kotoran', 'Organo', 'Warna', 'Aroma', '% NaCl'],
                    'note'         => null,
                ];
            case 'skm':
                return [
                    'type'         => 'skm',
                    'doc_no'       => 'FRM/QRP/01/003/001',
                    'title'        => 'HASIL PENGECEKAN FISIK DAN ANALISIS SARI KECAP MURNI  (SKM)',
                    'kemasan'      => 'drum',
                    'param_keys'   => ['berat_jenis', 'brix', 'organo', 'aroma', 'warna', 'ph', 'nacl', 'tn'],
                    'param_labels' => ['BJ (g/mL)', 'Brix', 'Organo', 'Aroma', 'Warna', 'pH', '% NaCl', '% TN*'],
                    'note'         => '* Monitoring 1 tahun sekali',
                ];
            case 'karamel':
                return [
                    'type'         => 'karamel',
                    'doc_no'       => 'FRM/QRP/01/004/001',
                    'title'        => 'HASIL PENGECEKAN FISIK DAN ANALISIS KARAMEL',
                    'kemasan'      => 'drum',
                    'param_keys'   => ['berat_jenis', 'warna'],
                    'param_labels' => ['BJ (g/mL)', 'Warna'],
                    'note'         => null,
                ];
            case 'gula_cair':
                return [
                    'type'         => 'gula_cair',
                    'doc_no'       => 'FRM/QRP/01/008/001-01',
                    'title'        => 'HASIL PENGECEKAN FISIK DAN ANALISIS GULA CAIR',
                    'kemasan'      => 'cair',
                    'param_keys'   => ['brix', 'ph', 'warna', 'aroma', 'berat_jenis', 'viskositas', 'keterangan'],
                    'param_labels' => ['Brix', 'pH', 'Warna', 'Aroma', 'Berat Jenis (g/ml)', 'Viskositas (poice)', 'Keterangan'],
                    'note'         => '*Monitoring 1 bulan sekali',
                ];
            case 'minor':
                return [
                    'type'         => 'minor',
                    'doc_no'       => 'FRM/QRP/01/005/001-00',
                    'title'        => 'HASIL PENGECEKAN FISIK DAN ANALISIS MINOR INGREDIENT',
                    'kemasan'      => 'zak',
                    'param_keys'   => ['fisik', 'ka', 'kotoran', 'organo', 'warna', 'aroma'],
                    'param_labels' => ['Fisik', '% KA', '% Kotoran', 'Organo*', 'Warna', 'Aroma*'],
                    'note'         => '*ND17103 tidak dianalisis Organo dan Aroma',
                ];
            case 'gula':
            default:
                return [
                    'type'         => 'gula',
                    'doc_no'       => 'FRM/QRP/01/001/001-01',
                    'title'        => 'HASIL PENGECEKAN FISIK DAN ANALISIS GULA',
                    'kemasan'      => 'zak',
                    'param_keys'   => ['brix', 'ph', 'kotoran', 'organo', 'warna', 'aroma', 'uji_kristal', 'ka'],
                    'param_labels' => ['Brix', 'pH', '% Kotoran', 'Organo', 'Warna', 'Aroma', 'Kristal', '% KA'],
                    'note'         => '*KA hanya untuk Gula Rafinasi (jika ada)',
                ];
        }
    }

    /**
     * Helper untuk mengambil dan mengkalkulasi seluruh parameter RM
     */
    public function getDocumentData($id): ?array
    {
        $identitas = IdentitasRM::with([
            'samplingDokumen',
            'samplingKondisiMobil',
            'samplingFisikKemasan',
            'samplingFisikRaw',
            'KonfirmasiKedatangan.diterimaBy',
            'KonfirmasiKedatangan.dianalisaBy',
            'analisaSampel',
            'analisaShortTerm',
            'analisaGaramGula',
            'analisaBahanKering',
            'analisaBahanCair',
            'analisaLongTerm',
        ])->find($id);

        if (!$identitas) {
            return null;
        }

        return $this->formatDocumentData($identitas);
    }

    /**
     * Helper untuk memformat data model IdentitasRM ke struktur dokumen template
     */
    public function formatDocumentData(IdentitasRM $identitas): array
    {
        $id = $identitas->id;
        $jenisRaw = trim($identitas->jenis ?? 'Raw Material');
        $templateType = self::resolveTemplateType($jenisRaw);
        $templateMeta = self::getTemplateMeta($templateType);

        $jenisUpper = strtoupper($jenisRaw);
        $isGulaKelapa = str_contains($jenisUpper, 'KELAPA') || $jenisUpper === 'GK';
        $isGulaTebu = (str_contains($jenisUpper, 'TEBU') || $jenisUpper === 'GT') && !str_contains($jenisUpper, 'RAFINASI');
        $isGula = ($templateType === 'gula');

        // Ambil data sampel analisa (akumulasi Incoming dan STA) menggunakan RMPMController yang sudah terflatten
        try {
            $rmController = new RMPMController();
            $accumulatedSamples = $rmController->getFlattenedSamplesForIdentitas($id, ['incoming', 'sta']);
            if ($accumulatedSamples->isEmpty()) {
                $accumulatedSamples = $rmController->getFlattenedSamplesForIdentitas($id);
            }
        } catch (\Throwable $e) {
            $accumulatedSamples = $identitas->analisaSampel ?? collect();
        }

        $sampleCount = $accumulatedSamples->count();

        // Siapkan array sample rows sesuai spesifikasi template
        $sampleRows = [];
        $numericSums = [];
        $numericCounts = [];

        foreach ($accumulatedSamples as $idx => $s) {
            $row = [
                'no'           => $idx + 1,
                'sampel_ke'    => $s->sampel_ke ?? ($idx + 1),
                'kategori'     => $s->kategori ?? 'incoming',
                'brix'         => (is_numeric($s->brix) && $s->brix !== '') ? (float)$s->brix : ($s->brix ?? '-'),
                'ph'           => (is_numeric($s->ph) && $s->ph !== '') ? (float)$s->ph : ($s->ph ?? '-'),
                'kotoran'      => (is_numeric($s->kotoran) && $s->kotoran !== '') ? (float)$s->kotoran : ($s->kotoran ?? '-'),
                'ka'           => (is_numeric($s->ka ?? $s->{'%ka'}) && ($s->ka ?? $s->{'%ka'}) !== '') ? (float)($s->ka ?? $s->{'%ka'}) : ($s->ka ?? $s->{'%ka'} ?? '-'),
                'nacl'         => (is_numeric($s->nacl ?? $s->{'%nacl'}) && ($s->nacl ?? $s->{'%nacl'}) !== '') ? (float)($s->nacl ?? $s->{'%nacl'}) : ($s->nacl ?? $s->{'%nacl'} ?? '-'),
                'berat_jenis'  => (is_numeric($s->berat_jenis) && $s->berat_jenis !== '') ? (float)$s->berat_jenis : ($s->berat_jenis ?? '-'),
                'viskositas'   => (is_numeric($s->viskositas) && $s->viskositas !== '') ? (float)$s->viskositas : ($s->viskositas ?? '-'),
                'tn'           => (is_numeric($s->tn ?? $s->{'%tn'}) && ($s->tn ?? $s->{'%tn'}) !== '') ? (float)($s->tn ?? $s->{'%tn'}) : ($s->tn ?? $s->{'%tn'} ?? '-'),
                'organo'       => $s->organo ?? ($s->rasa ?? '-'),
                'warna'        => $s->warna ?? '-',
                'aroma'        => $s->aroma ?? '-',
                'fisik'        => $s->fisik ?? '-',
                'uji_kristal'  => $s->uji_kristal ?? ($identitas->analisaLongTerm->last()?->uji_kristal ?? '-'),
                'keterangan'   => $s->keterangan ?? '-',
            ];

            // Hitung akumulasi numerik untuk rata-rata
            foreach (['brix', 'ph', 'kotoran', 'ka', 'nacl', 'berat_jenis', 'viskositas', 'tn'] as $numKey) {
                if (is_numeric($row[$numKey])) {
                    $numericSums[$numKey] = ($numericSums[$numKey] ?? 0) + $row[$numKey];
                    $numericCounts[$numKey] = ($numericCounts[$numKey] ?? 0) + 1;
                }
            }

            $sampleRows[] = $row;
        }

        // Hitung rata-rata
        $averages = [];
        foreach (['brix', 'ph', 'kotoran', 'ka', 'nacl', 'berat_jenis', 'viskositas', 'tn'] as $numKey) {
            if (!empty($numericCounts[$numKey])) {
                $decimals = in_array($numKey, ['berat_jenis']) ? 3 : 2;
                $averages[$numKey] = number_format($numericSums[$numKey] / $numericCounts[$numKey], $decimals, ',', '.');
            } else {
                $averages[$numKey] = '-';
            }
        }

        // Ambil objek relasi sampling
        $dataMobil = $identitas->samplingKondisiMobil;
        $dataDokumen = $identitas->samplingDokumen;
        $dataKemasan = $identitas->samplingFisikKemasan;
        $dataRaw = $identitas->samplingFisikRaw;
        $konfirmasi = $identitas->KonfirmasiKedatangan;

        // Sampling kondisi mobil
        $kondisiMobil = [
            'bersih'      => $dataMobil?->bersih === 'yes' ? '√' : ($dataMobil?->bersih === 'no' ? '×' : '-'),
            'kering'      => $dataMobil?->kering === 'yes' ? '√' : ($dataMobil?->kering === 'no' ? '×' : '-'),
            'benda_asing' => $dataMobil?->benda_asing === 'yes' ? '√' : ($dataMobil?->benda_asing === 'no' ? '×' : '-'),
            'berbau'      => $dataMobil?->berbau === 'yes' ? '√' : ($dataMobil?->berbau === 'no' ? '×' : '-'),
            'cacat'       => $dataMobil?->cacat === 'yes' ? '√' : ($dataMobil?->cacat === 'no' ? '×' : '-'),
            'segel'       => $dataMobil?->segel === 'yes' ? '√' : ($dataMobil?->segel === 'no' ? '×' : '-'),
            'keterangan'  => $dataMobil?->keterangan_bersih ?: ($dataMobil?->keterangan_kering ?: ($dataMobil?->keterangan_benda_asing ?: ($dataMobil?->keterangan_cacat ?: ($dataMobil?->keterangan_segel ?: ($dataMobil?->keterangan_berbau ?: '-'))))),
        ];

        // Sampling dokumen
        $dokumenCheck = [
            'coa'               => $dataDokumen?->coa === 'yes' ? '√' : ($dataDokumen?->coa === 'no' ? '×' : '-'),
            'surat_jalan'       => $dataDokumen?->surat_jalan === 'yes' ? '√' : ($dataDokumen?->surat_jalan === 'no' ? '×' : '-'),
            'packing_list'      => $dataDokumen?->packing_list === 'yes' ? '√' : ($dataDokumen?->packing_list === 'no' ? '×' : '-'),
            'identitas_kemasan' => $dataDokumen?->identitas_kemasan === 'yes' ? '√' : ($dataDokumen?->identitas_kemasan === 'no' ? '×' : '-'),
            'logo_halal'        => $dataDokumen?->logo_halal === 'yes' ? '√' : ($dataDokumen?->logo_halal === 'no' ? '×' : '-'),
            'matriks_bahan'     => $dataDokumen?->kesesuaian_matriks_bahan === 'yes' ? '√' : ($dataDokumen?->kesesuaian_matriks_bahan === 'no' ? '×' : '-'),
        ];

        // Sampling fisik kemasan
        $fisikKemasan = [
            'leleh'       => $dataKemasan?->keterangan_leleh ?? ($dataKemasan?->leleh ?? '-'),
            'kotor'       => $dataKemasan?->kotor ?? '-',
            'berair'      => $dataKemasan?->berair ?? '-',
            'basah'       => $dataKemasan?->basah ?? '-',
            'warna'       => $dataKemasan?->warna ?? ($dataRaw?->warna ?? '-'),
            'campuran'    => $dataKemasan?->campuran ?? '-',
            'aroma'       => $dataKemasan?->aroma ?? ($dataRaw?->aroma ?? '-'),
            'rusak'       => $dataKemasan?->rusak ?? '-',
            'kembung'     => $dataKemasan?->kembung ?? '-',
            'lain_lain'   => $dataKemasan?->lain_lain ?? '-',
            'zak_pupuk'   => '-',
            'zak_kimia'   => '-',
            'zak_ternak'  => '-',
            'aroma_kotor' => '-',
            'total_bagus' => $identitas->jumlah_kedatangan ? number_format((float)$identitas->jumlah_kedatangan, 0, ',', '.') : '-',
        ];

        // Serah terima & Disposisi
        $jamSampling = $konfirmasi?->waktu_kedatangan ? Carbon::parse($konfirmasi->waktu_kedatangan)->format('H:i') : '-';
        $latestLongTerm = $identitas->analisaLongTerm->last();
        $disposisiVal = $identitas->disposisi 
            ?: ($latestLongTerm?->disposisi 
            ?: ($accumulatedSamples->last()?->disposisi 
            ?: ($accumulatedSamples->first()?->disposisi ?: 'Belum Disposisi')));
        $groupVal = $identitas->group 
            ?: ($latestLongTerm?->group 
            ?: ($accumulatedSamples->last()?->group 
            ?: ($accumulatedSamples->first()?->group ?: '-')));

        $serahTerima = [
            'jam_sampling'    => $jamSampling,
            'ttd_sampling'    => $konfirmasi?->diterimaBy?->name ?? 'QC Field',
            'jumlah_sampling' => $sampleCount > 0 ? $sampleCount . ' Sampel' : '-',
            'jam_resampling'  => '-',
            'ttd_resampling'  => '-',
            'jumlah_resampling'=> '-',
            'disposisi'       => $disposisiVal,
            'group'           => $groupVal,
        ];

        // Tanda tangan
        $signatures = [
            'qc_field'  => $konfirmasi?->diterimaBy?->name ?? null,
            'qc_analis' => $konfirmasi?->dianalisaBy?->name ?? null,
            'spv'       => null,
        ];

        // Standar Mutu Dinamis untuk keperluan acuan / info
        $cleanJenis = strtoupper(trim((string)$identitas->jenis));
        try {
            $standards = MasterStandarRm::where('status', true)
                ->whereHas('jenisBahan', function ($q) use ($cleanJenis) {
                    $q->whereRaw('UPPER(nama) = ?', [$cleanJenis]);
                })
                ->get();
        } catch (\Throwable $e) {
            $standards = collect();
        }

        // 1. Standar pH
        $stdPh = $standards->first(fn($s) => stripos($s->parameter, 'ph') !== false);
        $phMinStd = $stdPh?->min_standar ?? ($isGulaKelapa ? 5.30 : 5.50);
        $phMaxStd = $stdPh?->max_standar ?? 6.50;
        $phStdLabel = number_format($phMinStd, 2, ',', '.') . ' - ' . number_format($phMaxStd, 2, ',', '.');

        // 2. Standar Brix
        $stdBrix = $standards->first(fn($s) => stripos($s->parameter, 'brix') !== false);
        $brixMinStd = $stdBrix?->min_standar ?? 8.7;
        $brixStdLabel = 'Min. ' . number_format($brixMinStd, 1, ',', '.');

        // 3. Standar %Kotoran
        $stdKot = $standards->first(fn($s) => stripos($s->parameter, 'kotoran') !== false);
        $kotMaxStd = $stdKot?->max_standar ?? 10.0;
        $kotStdLabel = 'Max. ' . number_format($kotMaxStd, 0, ',', '.') . '% w/w';

        // 4. Standar %Kadar Air
        $stdKa = $standards->first(fn($s) => stripos($s->parameter, 'kadar air') !== false || stripos($s->parameter, 'ka') !== false);
        $kaMaxStd = $stdKa?->max_standar ?? ($isGulaTebu ? 3.0 : 8.0);
        $kaStdLabel = 'Max. ' . number_format($kaMaxStd, 0, ',', '.') . '%';

        // Hitung persentase statistik khusus Gula (jika diperlukan untuk summary / Gula Tebu & Gula Kelapa)
        $phVals = collect($sampleRows)->pluck('ph')->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
        $brixVals = collect($sampleRows)->pluck('brix')->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
        $kotVals = collect($sampleRows)->pluck('kotoran')->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
        $kaVals = collect($sampleRows)->pluck('ka')->filter(fn($v) => is_numeric($v))->map(fn($v) => (float)$v)->values();
        $organoVals = collect($sampleRows)->pluck('organo')->filter(fn($v) => $v !== '-' && !empty($v))->values();

        // pH Stats
        $avgPh = $phVals->isNotEmpty() ? $phVals->avg() : null;
        $minPh = $phVals->isNotEmpty() ? $phVals->min() : null;
        $maxPh = $phVals->isNotEmpty() ? $phVals->max() : null;

        $totalPh = $phVals->count() ?: 1;
        $pctPhUnder = ($phVals->filter(fn($v) => $v < $phMinStd)->count() / $totalPh) * 100;
        $pctPhInSpec = ($phVals->filter(fn($v) => $v >= $phMinStd && $v <= $phMaxStd)->count() / $totalPh) * 100;
        $pctPhOver = ($phVals->filter(fn($v) => $v > $phMaxStd)->count() / $totalPh) * 100;
        $pctPhUnder55 = ($phVals->filter(fn($v) => $v < 5.50)->count() / $totalPh) * 100;
        $pctPhUnder53 = ($phVals->filter(fn($v) => $v < 5.30)->count() / $totalPh) * 100;
        $pctPhOver65 = ($phVals->filter(fn($v) => $v > 6.50)->count() / $totalPh) * 100;
        $pctPhOver65_70 = ($phVals->filter(fn($v) => $v > 6.50 && $v <= 7.00)->count() / $totalPh) * 100;
        $pctPhOver70 = ($phVals->filter(fn($v) => $v > 7.00)->count() / $totalPh) * 100;

        // Brix Stats
        $avgBrix = $brixVals->isNotEmpty() ? $brixVals->avg() : null;
        $minBrix = $brixVals->isNotEmpty() ? $brixVals->min() : null;
        $maxBrix = $brixVals->isNotEmpty() ? $brixVals->max() : null;

        $totalBrix = $brixVals->count() ?: 1;
        $pctBrixUnder = ($brixVals->filter(fn($v) => $v < $brixMinStd)->count() / $totalBrix) * 100;
        $pctBrixInSpec = ($brixVals->filter(fn($v) => $v >= $brixMinStd)->count() / $totalBrix) * 100;

        // %Kotoran Stats
        $avgKotoran = $kotVals->isNotEmpty() ? $kotVals->avg() : null;
        $minKotoran = $kotVals->isNotEmpty() ? $kotVals->min() : null;
        $maxKotoran = $kotVals->isNotEmpty() ? $kotVals->max() : null;

        $totalKot = $kotVals->count() ?: 1;
        $pctKotInSpec = ($kotVals->filter(fn($v) => $v <= $kotMaxStd)->count() / $totalKot) * 100;
        $pctKotOver = ($kotVals->filter(fn($v) => $v > $kotMaxStd)->count() / $totalKot) * 100;
        $pctKot10_14 = ($kotVals->filter(fn($v) => $v > 10.0 && $v <= 14.0)->count() / $totalKot) * 100;
        $pctKotOver14 = ($kotVals->filter(fn($v) => $v > 14.0)->count() / $totalKot) * 100;

        // Organo Stats
        $totalOrgano = $organoVals->count() ?: 1;

        if ($isGulaKelapa) {
            $organoDefinitions = [
                ['key' => 'ok',          'label' => '1, OK', 'ket' => 'Terasa/dominan rasa gula kelapa'],
                ['key' => 'agak_asam',   'label' => '2, Agak asam', 'ket' => 'Terasa/dominan rasa gula kelapa'],
                ['key' => 'asam',        'label' => '3, Asam', 'ket' => 'Terasa/dominan rasa gula kelapa'],
                ['key' => 'agak_pahit',  'label' => '4, Agak pahit', 'ket' => 'Terasa/dominan rasa gula kelapa'],
                ['key' => 'pahit',       'label' => '5, Pahit', 'ket' => 'Terasa/dominan rasa gula kelapa'],
                ['key' => 'sagu',        'label' => '6, Sagu', 'ket' => 'Terasa/dominan rasa gula kelapa'],
                ['key' => 'kapur',       'label' => '7, Kapur', 'ket' => 'Terasa/dominan rasa gula kelapa'],
                ['key' => 'lain',        'label' => '8, Lain-lain (tengik, kapur, endapan putih dll)', 'ket' => 'Terasa/dominan rasa gula kelapa'],
                ['key' => 'campuran',    'label' => '9, Campuran', 'ket' => 'Tidak terasa/tidak dominan rasa gula kelapa'],
            ];
            $ketMurni = 'Total poin organo dari no 1--4 dan 6-8';
        } else {
            // Default / Gula Tebu
            $teksDominan = 'Terasa/dominan rasa ' . ($isGulaTebu ? 'gula tebu' : 'gula kelapa');
            $teksTidakDominan = 'Tidak terasa/tidak dominan rasa ' . ($isGulaTebu ? 'gula tebu' : 'gula kelapa');
            $organoDefinitions = [
                ['key' => 'ok',         'label' => '1, OK', 'ket' => $teksDominan],
                ['key' => 'pahit',      'label' => '2, Pahit', 'ket' => $teksDominan],
                ['key' => 'agak_pahit', 'label' => '3, Agak pahit', 'ket' => $teksDominan],
                ['key' => 'asam',       'label' => '4, Asam dan pH < 5,5', 'ket' => $teksDominan],
                ['key' => 'agak_asam',  'label' => '5, Agak asam dan pH normal', 'ket' => $teksDominan],
                ['key' => 'lain',       'label' => '6, Lain-lain (tengik, kapur, endapan putih dll)', 'ket' => $teksDominan],
                ['key' => 'campuran',   'label' => '7, Campuran', 'ket' => $teksTidakDominan],
            ];
            $ketMurni = 'Total poin organo dari no 1 & 3-6';
        }

        $counts = [];
        foreach ($organoDefinitions as $d) {
            $counts[$d['key']] = 0;
        }

        foreach ($organoVals as $val) {
            $v = strtolower(trim((string)$val));
            if ($isGulaKelapa) {
                if (str_contains($v, 'campuran') || str_contains($v, 'tidak terasa') || str_starts_with($v, '9')) {
                    $counts['campuran']++;
                } elseif (str_contains($v, 'agak pahit') || str_starts_with($v, '4')) {
                    $counts['agak_pahit']++;
                } elseif (str_contains($v, 'pahit') || str_starts_with($v, '5')) {
                    $counts['pahit']++;
                } elseif (str_contains($v, 'agak asam') || str_starts_with($v, '2')) {
                    $counts['agak_asam']++;
                } elseif (str_contains($v, 'asam') || str_starts_with($v, '3')) {
                    $counts['asam']++;
                } elseif (str_contains($v, 'sagu') || str_starts_with($v, '6')) {
                    $counts['sagu']++;
                } elseif ((str_contains($v, 'kapur') && !str_contains($v, 'lain')) || str_starts_with($v, '7')) {
                    $counts['kapur']++;
                } elseif (in_array($v, ['ok', 'sesuai', 'khas', 'normal', 'standar']) || str_contains($v, 'khas') || str_starts_with($v, '1')) {
                    $counts['ok']++;
                } else {
                    $counts['lain']++;
                }
            } else {
                // Gula Tebu
                if (str_contains($v, 'agak pahit') || str_starts_with($v, '3')) {
                    $counts['agak_pahit']++;
                } elseif (str_contains($v, 'agak asam') || str_contains($v, 'ph normal') || str_starts_with($v, '5')) {
                    $counts['agak_asam']++;
                } elseif (str_contains($v, 'asam') || str_starts_with($v, '4')) {
                    $counts['asam']++;
                } elseif (str_contains($v, 'pahit') || str_starts_with($v, '2')) {
                    $counts['pahit']++;
                } elseif (str_contains($v, 'campuran') || str_contains($v, 'tidak terasa') || str_starts_with($v, '7')) {
                    $counts['campuran']++;
                } elseif (in_array($v, ['ok', 'sesuai', 'khas', 'normal', 'standar']) || str_contains($v, 'khas') || str_starts_with($v, '1')) {
                    $counts['ok']++;
                } else {
                    $counts['lain']++;
                }
            }
        }

        $organoItems = [];
        foreach ($organoDefinitions as $def) {
            $cnt = $counts[$def['key']];
            $pct = round(($cnt / $totalOrgano) * 100);
            $organoItems[] = [
                'label'   => $def['label'],
                'key'     => $def['key'],
                'count'   => $cnt,
                'pct'     => $pct,
                'ket'     => $def['ket'],
            ];
        }

        if ($organoVals->isEmpty()) {
            $murniPctTotal = 0;
            $campuranPctTotal = 0;
        } else {
            // Formula sesuai format standar:
            // Campuran : %pahit + %campuran (Pahit + Campuran)
            // Murni : 100% - Campuran
            $pctPahit = collect($organoItems)->firstWhere('key', 'pahit')['pct'] ?? 0;
            $pctCampuranItem = collect($organoItems)->firstWhere('key', 'campuran')['pct'] ?? 0;
            $campuranPctTotal = $pctPahit + $pctCampuranItem;
            $murniPctTotal = max(0, 100 - $campuranPctTotal);
        }

        // %KA Stats
        $avgKa = $kaVals->isNotEmpty() ? $kaVals->avg() : null;
        $minKa = $kaVals->isNotEmpty() ? $kaVals->min() : null;
        $maxKa = $kaVals->isNotEmpty() ? $kaVals->max() : null;

        $totalKa = $kaVals->count() ?: 1;
        $pctKaInSpec = ($kaVals->filter(fn($v) => $v <= $kaMaxStd)->count() / $totalKa) * 100;
        $pctKaOver = ($kaVals->filter(fn($v) => $v > $kaMaxStd)->count() / $totalKa) * 100;

        $jamKedatangan = $konfirmasi?->waktu_kedatangan ? Carbon::parse($konfirmasi->waktu_kedatangan)->format('H:i') . ' WIB' : '-';

        return [
            'identitas'       => $identitas,
            'id'              => $identitas->id,
            'jenis'           => $jenisUpper,
            'nama_bahan'      => $identitas->nama_bahan ?: $identitas->jenis,
            'title'           => $templateMeta['title'],
            'doc_no'          => $templateMeta['doc_no'],
            'template_type'   => $templateType,
            'template_meta'   => $templateMeta,
            'kemasan_type'    => $templateMeta['kemasan'],
            'is_supported'    => true,
            'is_gula'         => $isGula,
            'is_gula_kelapa'  => $isGulaKelapa,
            'is_gula_tebu'    => $isGulaTebu,
            'tgl_incoming'    => $identitas->tanggal_kedatangan ? Carbon::parse($identitas->tanggal_kedatangan)->format('d-M-Y') : '-',
            'jam_kedatangan'  => $jamKedatangan,
            'vendor'          => $identitas->supplier ?? '-',
            'spb'             => $identitas->no_spb ?? '-',
            'asal_bahan'      => $identitas->asal_bahan ?? '-',
            'lot_batch'       => $identitas->no_lot ?? '-',
            'nopol'           => $identitas->no_plat ?? '-',
            'qty'             => $identitas->jumlah_kedatangan ? number_format((float)$identitas->jumlah_kedatangan, 0, ',', '.') : '-',
            'exp_date'        => $identitas->exp_date ? Carbon::parse($identitas->exp_date)->format('d-M-Y') : '-',
            'standart_bps'    => $identitas->standart_bps ?? '',
            'sample_count'    => $sampleCount,

            // Pengecekan Kedatangan, Fisik, Dokumen
            'kondisi_mobil'   => $kondisiMobil,
            'dokumen'         => $dokumenCheck,
            'fisik_kemasan'   => $fisikKemasan,
            'serah_terima'    => $serahTerima,
            'signatures'      => $signatures,

            // Tabel Analisis Sampel
            'sample_rows'     => $sampleRows,
            'averages'        => $averages,
            'note'            => $templateMeta['note'],

            // Standar Dinamis
            'ph_min_std'      => $phMinStd,
            'ph_max_std'      => $phMaxStd,
            'ph_std_label'    => $phStdLabel,
            'brix_min_std'    => $brixMinStd,
            'brix_std_label'  => $brixStdLabel,
            'kot_max_std'     => $kotMaxStd,
            'kot_std_label'   => $kotStdLabel,
            'ka_max_std'      => $kaMaxStd,
            'ka_std_label'    => $kaStdLabel,

            // pH Detail
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

            // Brix Detail
            'avg_brix'        => $avgBrix,
            'min_brix'        => $minBrix,
            'max_brix'        => $maxBrix,
            'pct_brix_under'  => $pctBrixUnder,
            'pct_brix_inspec' => $pctBrixInSpec,

            // %Kotoran Detail
            'avg_kotoran'     => $avgKotoran,
            'min_kotoran'     => $minKotoran,
            'max_kotoran'     => $maxKotoran,
            'pct_kot_inspec'  => $pctKotInSpec,
            'pct_kot_over'    => $pctKotOver,
            'pct_kot_10_14'   => $pctKot10_14,
            'pct_kot_over_14' => $pctKotOver14,

            // Organo Detail
            'organo_items'    => $organoItems,
            'murni_pct'       => $murniPctTotal,
            'campuran_pct'    => $campuranPctTotal,
            'ket_murni'       => $ketMurni,
            'ket_campuran'    => 'Rasa campuran dan pahit',

            // %KA Detail
            'avg_ka'          => $avgKa,
            'min_ka'          => $minKa,
            'max_ka'          => $maxKa,
            'pct_ka_inspec'   => $pctKaInSpec,
            'pct_ka_over'     => $pctKaOver,
        ];
    }
}
