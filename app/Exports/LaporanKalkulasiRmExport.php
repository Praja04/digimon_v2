<?php

namespace App\Exports;

use App\Models\IdentitasRM;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKalkulasiRmExport implements WithMultipleSheets
{
    protected IdentitasRM $identitas;

    public function __construct(IdentitasRM $identitas)
    {
        $this->identitas = $identitas;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Lembar Laporan Kalkulasi (persis seperti Gambar 2 & 3)
        $sheets[] = new LaporanKalkulasiSheetExport($this->identitas);

        // Sheet 2: Data Mentah Sampel (Brix, pH, Kotoran, KA, Organo per sampel)
        $sheets[] = new DataSampelRmSheetExport($this->identitas);

        return $sheets;
    }
}

/**
 * Sheet 1: Hasil Kalkulasi / Laporan Analisa RM
 */
class LaporanKalkulasiSheetExport implements WithEvents, WithTitle
{
    protected IdentitasRM $identitas;

    public function __construct(IdentitasRM $identitas)
    {
        $this->identitas = $identitas;
    }

    public function title(): string
    {
        $jenisUpper = strtoupper(trim($this->identitas->jenis ?? 'GULA TEBU'));
        $isGulaKelapa = str_contains($jenisUpper, 'KELAPA');
        $isGulaTebu = str_contains($jenisUpper, 'TEBU') || (!$isGulaKelapa && str_contains($jenisUpper, 'GULA'));
        $cleanTitle = $isGulaTebu ? 'GULA TEBU' : ($isGulaKelapa ? 'GULA KELAPA' : $jenisUpper);
        return 'LAPORAN ' . substr($cleanTitle, 0, 20);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildReport($sheet);
            },
        ];
    }

    protected function buildReport(Worksheet $sheet): void
    {
        // Nonaktifkan gridlines default agar kita bisa mengatur garis border sendiri
        $sheet->setShowGridlines(true);

        $identitas = $this->identitas;
        $jenisUpper = strtoupper(trim($identitas->jenis ?? 'GULA TEBU'));
        $isGulaKelapa = str_contains($jenisUpper, 'KELAPA');
        $isGulaTebu = str_contains($jenisUpper, 'TEBU') || (!$isGulaKelapa && str_contains($jenisUpper, 'GULA'));
        $titleMain = $isGulaTebu ? 'GULA TEBU' : ($isGulaKelapa ? 'GULA KELAPA' : $jenisUpper);

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

        // Jika analisa garam gula (misal Garam)
        if ($incomingSamples->isEmpty() && $identitas->analisaGaramGula->isNotEmpty()) {
            $incomingSamples = $identitas->analisaGaramGula;
        }

        $sampleCount = $incomingSamples->count();

        // Data arrays numerik
        $phVals = $incomingSamples->pluck('ph')->filter(fn($v) => $v !== null && $v !== '')->map(fn($v) => (float)$v)->values();
        $brixVals = $incomingSamples->pluck('brix')->filter(fn($v) => $v !== null && $v !== '')->map(fn($v) => (float)$v)->values();
        $kotoranVals = $incomingSamples->pluck('kotoran')->filter(fn($v) => $v !== null && $v !== '')->map(fn($v) => (float)$v)->values();
        
        // Kadar air (ka atau %ka)
        $kaField = $incomingSamples->first()?->offsetExists('%ka') ? '%ka' : 'ka';
        $kaVals = $incomingSamples->pluck($kaField)->filter(fn($v) => $v !== null && $v !== '')->map(fn($v) => (float)$v)->values();

        // Organo values
        $organoVals = $incomingSamples->pluck('organo')->filter(fn($v) => !empty($v))->map(fn($v) => trim((string)$v))->values();

        // -------------------------------------------------------------
        // 1. HEADER ATAS
        // -------------------------------------------------------------
        $tglIncoming = $identitas->tanggal_kedatangan
            ? Carbon::parse($identitas->tanggal_kedatangan)->format('d-M-Y')
            : '-';
        $vendor = $identitas->supplier ?? '-';
        $spb = $identitas->no_spb ?? '-';
        $standartBps = $identitas->standart_bps ?? '';
        $nopol = $identitas->no_plat ?? '-';
        $qty = $identitas->jumlah_kedatangan ? number_format((float)$identitas->jumlah_kedatangan, 0, ',', '.') : '-';

        // Judul Utama (Center)
        $sheet->setCellValue('D1', $titleMain);
        $sheet->mergeCells('D1:G1');
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Kiri
        $sheet->setCellValue('B3', 'Tgl Incoming');
        $sheet->setCellValue('C3', $tglIncoming);

        $sheet->setCellValue('B4', 'Vendor');
        $sheet->setCellValue('C4', $vendor);

        $sheet->setCellValue('B5', 'SPB');
        $sheet->setCellValue('C5', $spb);

        $sheet->setCellValue('B6', 'Standart BPS');
        $sheet->setCellValue('C6', $standartBps);
        $cleanBps = strtolower(trim((string)$standartBps));
        if ($cleanBps === 'kritis') {
            $sheet->getStyle('C6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF990000');
            $sheet->getStyle('C6')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        } elseif ($cleanBps === 'standar') {
            $sheet->getStyle('C6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF15803D');
            $sheet->getStyle('C6')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        } elseif ($cleanBps === 'non-kritis') {
            $sheet->getStyle('C6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF475569');
            $sheet->getStyle('C6')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        } elseif ($cleanBps !== '') {
            $sheet->getStyle('C6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0369A1');
            $sheet->getStyle('C6')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        }

        // Header Kanan
        $sheet->setCellValue('G3', 'Nopol');
        $sheet->setCellValue('H3', $nopol);

        $sheet->setCellValue('G4', 'Qty (kg)');
        $sheet->setCellValue('H4', $qty);

        $sheet->setCellValue('G5', 'Jumlah Sampel');
        $sheet->setCellValue('H5', $sampleCount);
        $sheet->getStyle('H5')->getFont()->setBold(true);

        $sheet->getStyle('B3:B6')->getFont()->setBold(false);
        $sheet->getStyle('G3:G5')->getFont()->setBold(false);

        // -------------------------------------------------------------
        // 2. TABEL KIRI & TABEL KANAN (START ROW 8)
        // -------------------------------------------------------------
        $headerFill = 'FFD9E1F2'; // Soft blue/gray
        $subHeaderFill = 'FFD9E1F2';
        $yellowFill = 'FFFFFF00';
        $greenFill = 'FFC6EFCE';

        // Header Tabel Kiri (B=Parameter Col1, C=Parameter Col2, D=Awal, E=Keterangan)
        $sheet->setCellValue('B8', 'Parameter');
        $sheet->mergeCells('B8:C8');
        $sheet->setCellValue('D8', 'Awal');
        $sheet->setCellValue('E8', 'Keterangan');
        $sheet->getStyle('B8:E8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle('B8:E8')->getFont()->setBold(true);
        $sheet->getStyle('B8:E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Tabel Kanan (G=Parameter, H=Awal, I=Keterangan)
        $sheet->setCellValue('G8', 'Parameter');
        $sheet->setCellValue('H8', 'Awal');
        $sheet->setCellValue('I8', 'Keterangan');
        $sheet->getStyle('G8:I8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerFill);
        $sheet->getStyle('G8:I8')->getFont()->setBold(true);
        $sheet->getStyle('G8:I8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Ambil Standar Mutu Dinamis dari Master Data (MasterStandarRm)
        $cleanJenis = strtoupper(trim((string)$this->identitas->jenis));
        $standards = \App\Models\MasterStandarRm::where('status', true)
            ->whereHas('jenisBahan', function ($q) use ($cleanJenis) {
                $q->whereRaw('UPPER(nama) = ?', [$cleanJenis]);
            })
            ->get();

        if ($standards->isEmpty()) {
            $standards = \App\Models\MasterStandarRm::where('status', true)
                ->whereHas('jenisBahan', function ($q) use ($isGulaKelapa) {
                    if ($isGulaKelapa) {
                        $q->where('nama', 'LIKE', '%KELAPA%');
                    } else {
                        $q->where('nama', 'LIKE', '%TEBU%');
                    }
                })
                ->get();
        }

        $stdPh = $standards->first(fn($s) => stripos($s->parameter, 'ph') !== false);
        $phMinSpec = $stdPh?->min_standar ?? ($isGulaKelapa ? 5.30 : 5.50);
        $phMaxSpec = $stdPh?->max_standar ?? 6.50;
        $phStdText = 'pH (STD : ' . number_format($phMinSpec, 2, ',', '.') . ' - ' . number_format($phMaxSpec, 2, ',', '.') . ')';

        $stdBrix = $standards->first(fn($s) => stripos($s->parameter, 'brix') !== false);
        $brixMinSpec = $stdBrix?->min_standar ?? 8.7;
        $brixStdText = 'Brix (STD : Min, ' . number_format($brixMinSpec, 1, ',', '.') . ')';

        $stdKot = $standards->first(fn($s) => stripos($s->parameter, 'kotoran') !== false);
        $kotMaxSpec = $stdKot?->max_standar ?? 10.0;
        $kotStdText = '%Kotoran (STD : Max, ' . number_format($kotMaxSpec, 0, ',', '.') . '% w/w)';

        $stdKa = $standards->first(fn($s) => stripos($s->parameter, 'kadar air') !== false || stripos($s->parameter, 'ka') !== false);
        $kaMaxSpec = $stdKa?->max_standar ?? ($isGulaTebu ? 3.0 : 8.0);
        $kaStdText = '%Kadar Air (STD : Max, ' . number_format($kaMaxSpec, 0, ',', '.') . '%)';

        // --- TABEL KIRI: pH ---
        $sheet->setCellValue('B9', $phStdText);
        $sheet->mergeCells('B9:E9');
        $sheet->getStyle('B9:E9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle('B9:E9')->getFont()->setBold(true);

        $avgPh = $phVals->isNotEmpty() ? $phVals->avg() : null;
        $minPh = $phVals->isNotEmpty() ? $phVals->min() : null;
        $maxPh = $phVals->isNotEmpty() ? $phVals->max() : null;

        $sheet->setCellValue('B10', 'Avg,');
        $sheet->mergeCells('B10:C10');
        $sheet->setCellValue('D10', $avgPh !== null ? number_format($avgPh, 2, ',', '.') : '-');

        $sheet->setCellValue('B11', 'Min,');
        $sheet->mergeCells('B11:C11');
        $sheet->setCellValue('D11', $minPh !== null ? number_format($minPh, 2, ',', '.') : '-');

        $sheet->setCellValue('B12', 'Max,');
        $sheet->mergeCells('B12:C12');
        $sheet->setCellValue('D12', $maxPh !== null ? number_format($maxPh, 2, ',', '.') : '-');

        // Highlight pH jika out of spec
        if ($avgPh !== null && ($avgPh < $phMinSpec || $avgPh > $phMaxSpec)) {
            $sheet->getStyle('D10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }
        if ($minPh !== null && $minPh < $phMinSpec) {
            $sheet->getStyle('D11')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }
        if ($maxPh !== null && $maxPh > $phMaxSpec) {
            $sheet->getStyle('D12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        // Distribusi %Qty Sample pH
        $totalPhSampel = $phVals->count() ?: 1;

        if ($isGulaKelapa) {
            // Gula Kelapa: Under, InSpec, Over
            $cntPhUnder = $phVals->filter(fn($v) => $v < $phMinSpec)->count();
            $pctPhUnder = ($cntPhUnder / $totalPhSampel) * 100;
            $cntPhInSpec = $phVals->filter(fn($v) => $v >= $phMinSpec && $v <= $phMaxSpec)->count();
            $pctPhInSpec = ($cntPhInSpec / $totalPhSampel) * 100;
            $cntPhOver = $phVals->filter(fn($v) => $v > $phMaxSpec)->count();
            $pctPhOver = ($cntPhOver / $totalPhSampel) * 100;

            $sheet->setCellValue('B13', '%Qty Sample');
            $sheet->mergeCells('B13:B15');
            $sheet->getStyle('B13:B15')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('C13', 'pH Under ' . number_format($phMinSpec, 2, ',', '.') . ' (#)');
            $sheet->setCellValue('D13', number_format($pctPhUnder, 0) . '%');
            if ($pctPhUnder > 0) {
                $sheet->getStyle('D13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
            }

            $sheet->setCellValue('C14', 'pH Sesuai Standar (' . number_format($phMinSpec, 2, ',', '.') . ' - ' . number_format($phMaxSpec, 2, ',', '.') . ') (#)');
            $sheet->setCellValue('D14', number_format($pctPhInSpec, 0) . '%');
            if ($pctPhInSpec > 0) {
                $sheet->getStyle('D14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);
            }

            $sheet->setCellValue('C15', 'pH Over ' . number_format($phMaxSpec, 2, ',', '.') . ' (#)');
            $sheet->setCellValue('D15', number_format($pctPhOver, 0) . '%');
            if ($pctPhOver > 0) {
                $sheet->getStyle('D15')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
            }

            $startBrixRow = 16;
        } else {
            // Gula Tebu: Under, InSpec, Over [max - max+0.50], Over > [max+0.50]
            $cntPhUnder = $phVals->filter(fn($v) => $v < $phMinSpec)->count();
            $pctPhUnder = ($cntPhUnder / $totalPhSampel) * 100;
            $cntPhInSpec = $phVals->filter(fn($v) => $v >= $phMinSpec && $v <= $phMaxSpec)->count();
            $pctPhInSpec = ($cntPhInSpec / $totalPhSampel) * 100;
            $cntPhOver65_70 = $phVals->filter(fn($v) => $v > $phMaxSpec && $v <= ($phMaxSpec + 0.50))->count();
            $pctPhOver65_70 = ($cntPhOver65_70 / $totalPhSampel) * 100;
            $cntPhOver70 = $phVals->filter(fn($v) => $v > ($phMaxSpec + 0.50))->count();
            $pctPhOver70 = ($cntPhOver70 / $totalPhSampel) * 100;

            $sheet->setCellValue('B13', '%Qty Sample');
            $sheet->mergeCells('B13:B16');
            $sheet->getStyle('B13:B16')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('C13', 'pH Under ' . number_format($phMinSpec, 2, ',', '.') . ' (#)');
            $sheet->setCellValue('D13', number_format($pctPhUnder, 0) . '%');
            if ($pctPhUnder > 0) {
                $sheet->getStyle('D13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
            }

            $sheet->setCellValue('C14', 'pH Sesuai Standar (' . number_format($phMinSpec, 2, ',', '.') . ' - ' . number_format($phMaxSpec, 2, ',', '.') . ') (#)');
            $sheet->setCellValue('D14', number_format($pctPhInSpec, 0) . '%');
            if ($pctPhInSpec > 0) {
                $sheet->getStyle('D14')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);
            }

            $sheet->setCellValue('C15', 'pH Over ' . number_format($phMaxSpec, 2, ',', '.') . '-' . number_format($phMaxSpec + 0.50, 2, ',', '.') . ' (#)');
            $sheet->setCellValue('D15', number_format($pctPhOver65_70, 0) . '%');
            if ($pctPhOver65_70 > 0) {
                $sheet->getStyle('D15')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
            }

            $sheet->setCellValue('C16', 'pH Over ' . number_format($phMaxSpec + 0.50, 2, ',', '.') . ' (#)');
            $sheet->setCellValue('D16', number_format($pctPhOver70, 0) . '%');
            if ($pctPhOver70 > 0) {
                $sheet->getStyle('D16')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
            }

            $startBrixRow = 17;
        }

        // --- TABEL KIRI: Brix ---
        $r = $startBrixRow;
        $sheet->setCellValue("B{$r}", $brixStdText);
        $sheet->mergeCells("B{$r}:E{$r}");
        $sheet->getStyle("B{$r}:E{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("B{$r}:E{$r}")->getFont()->setBold(true);

        $avgBrix = $brixVals->isNotEmpty() ? $brixVals->avg() : null;
        $minBrix = $brixVals->isNotEmpty() ? $brixVals->min() : null;
        $maxBrix = $brixVals->isNotEmpty() ? $brixVals->max() : null;

        $sheet->setCellValue('B' . ($r + 1), 'Avg,');
        $sheet->mergeCells('B' . ($r + 1) . ':C' . ($r + 1));
        $sheet->setCellValue('D' . ($r + 1), $avgBrix !== null ? number_format($avgBrix, 1, ',', '.') : '-');

        $sheet->setCellValue('B' . ($r + 2), 'Min,');
        $sheet->mergeCells('B' . ($r + 2) . ':C' . ($r + 2));
        $sheet->setCellValue('D' . ($r + 2), $minBrix !== null ? number_format($minBrix, 1, ',', '.') : '-');

        $sheet->setCellValue('B' . ($r + 3), 'Max,');
        $sheet->mergeCells('B' . ($r + 3) . ':C' . ($r + 3));
        $sheet->setCellValue('D' . ($r + 3), $maxBrix !== null ? number_format($maxBrix, 1, ',', '.') : '-');

        if ($avgBrix !== null && $avgBrix < $brixMinSpec) {
            $sheet->getStyle('D' . ($r + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }
        if ($minBrix !== null && $minBrix < $brixMinSpec) {
            $sheet->getStyle('D' . ($r + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        $totalBrixSampel = $brixVals->count() ?: 1;
        $cntBrixInSpec = $brixVals->filter(fn($v) => $v >= $brixMinSpec)->count();
        $pctBrixInSpec = ($cntBrixInSpec / $totalBrixSampel) * 100;
        $cntBrixUnder = $brixVals->filter(fn($v) => $v < $brixMinSpec)->count();
        $pctBrixUnder = ($cntBrixUnder / $totalBrixSampel) * 100;

        $sheet->setCellValue('B' . ($r + 4), '%Qty Sample');
        $sheet->mergeCells('B' . ($r + 4) . ':B' . ($r + 5));
        $sheet->getStyle('B' . ($r + 4) . ':B' . ($r + 5))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C' . ($r + 4), 'Brix Sesuai Standar (>= ' . number_format($brixMinSpec, 1, ',', '.') . ') (#)');
        $sheet->setCellValue('D' . ($r + 4), number_format($pctBrixInSpec, 0) . '%');
        if ($pctBrixInSpec > 0) {
            $sheet->getStyle('D' . ($r + 4))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);
        }

        $sheet->setCellValue('C' . ($r + 5), 'Brix Under ' . number_format($brixMinSpec, 1, ',', '.') . ' (#)');
        $sheet->setCellValue('D' . ($r + 5), number_format($pctBrixUnder, 0) . '%');
        if ($pctBrixUnder > 0) {
            $sheet->getStyle('D' . ($r + 5))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        // --- TABEL KIRI: %Kotoran ---
        $rKot = $r + 6;
        $sheet->setCellValue("B{$rKot}", $kotStdText);
        $sheet->mergeCells("B{$rKot}:E{$rKot}");
        $sheet->getStyle("B{$rKot}:E{$rKot}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("B{$rKot}:E{$rKot}")->getFont()->setBold(true);

        $avgKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->avg() : null;
        $minKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->min() : null;
        $maxKotoran = $kotoranVals->isNotEmpty() ? $kotoranVals->max() : null;

        $sheet->setCellValue('B' . ($rKot + 1), 'Avg,');
        $sheet->mergeCells('B' . ($rKot + 1) . ':C' . ($rKot + 1));
        $sheet->setCellValue('D' . ($rKot + 1), $avgKotoran !== null ? number_format($avgKotoran, 2, ',', '.') : '-');

        $sheet->setCellValue('B' . ($rKot + 2), 'Min,');
        $sheet->mergeCells('B' . ($rKot + 2) . ':C' . ($rKot + 2));
        $sheet->setCellValue('D' . ($rKot + 2), $minKotoran !== null ? number_format($minKotoran, 2, ',', '.') : '-');

        $sheet->setCellValue('B' . ($rKot + 3), 'Max,');
        $sheet->mergeCells('B' . ($rKot + 3) . ':C' . ($rKot + 3));
        $sheet->setCellValue('D' . ($rKot + 3), $maxKotoran !== null ? number_format($maxKotoran, 2, ',', '.') : '-');

        if ($avgKotoran !== null && $avgKotoran > $kotMaxSpec) {
            $sheet->getStyle('D' . ($rKot + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }
        if ($minKotoran !== null && $minKotoran > $kotMaxSpec) {
            $sheet->getStyle('D' . ($rKot + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }
        if ($maxKotoran !== null && $maxKotoran > $kotMaxSpec) {
            $sheet->getStyle('D' . ($rKot + 3))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        $totalKotSampel = $kotoranVals->count() ?: 1;
        $cntKotInSpec = $kotoranVals->filter(fn($v) => $v <= $kotMaxSpec)->count();
        $pctKotInSpec = ($cntKotInSpec / $totalKotSampel) * 100;
        $cntKot10_14 = $kotoranVals->filter(fn($v) => $v > $kotMaxSpec && $v <= ($kotMaxSpec + 4.0))->count();
        $cntKotOver14 = $kotoranVals->filter(fn($v) => $v > ($kotMaxSpec + 4.0))->count();
        $pctKot10_14 = ($cntKot10_14 / $totalKotSampel) * 100;
        $pctKotOver14 = ($cntKotOver14 / $totalKotSampel) * 100;

        $sheet->setCellValue('B' . ($rKot + 4), '%Qty Sample');
        $sheet->mergeCells('B' . ($rKot + 4) . ':B' . ($rKot + 6));
        $sheet->getStyle('B' . ($rKot + 4) . ':B' . ($rKot + 6))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C' . ($rKot + 4), 'Kotoran Sesuai Standar (<= ' . number_format($kotMaxSpec, 0, ',', '.') . '%) (#)');
        $sheet->setCellValue('D' . ($rKot + 4), number_format($pctKotInSpec, 0) . '%');
        if ($pctKotInSpec > 0) {
            $sheet->getStyle('D' . ($rKot + 4))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);
        }

        $sheet->setCellValue('C' . ($rKot + 5), '%Qty Sample Kotoran Over ' . number_format($kotMaxSpec, 0, ',', '.') . '-14%(#)');
        $sheet->setCellValue('D' . ($rKot + 5), number_format($pctKot10_14, 0) . '%');
        if ($pctKot10_14 > 0) {
            $sheet->getStyle('D' . ($rKot + 5))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        $sheet->setCellValue('C' . ($rKot + 6), '%Qty Sample Kotoran Over 14%(#)');
        $sheet->setCellValue('D' . ($rKot + 6), number_format($pctKotOver14, 0) . '%');
        if ($pctKotOver14 > 0) {
            $sheet->getStyle('D' . ($rKot + 6))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        $leftTableEndRow = $rKot + 6;

        // -------------------------------------------------------------
        // TABEL KANAN: Organo, %KA, Rasa Khas (Columns G, H, I)
        // -------------------------------------------------------------
        $sheet->setCellValue('G9', 'Organo');
        $sheet->mergeCells('G9:I9');
        $sheet->getStyle('G9:I9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle('G9:I9')->getFont()->setBold(true);

        $totalOrganoSampel = $organoVals->count() ?: 1;

        if ($isGulaKelapa) {
            // 9 Opsi Organo Gula Kelapa
            $organoItems = [
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
            // 7 Opsi Organo Gula Tebu
            $organoItems = [
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

        $rowOrg = 10;
        $murniPctTotal = 0;
        $campuranPctTotal = 0;

        foreach ($organoItems as $item) {
            $cnt = $organoVals->filter(function ($val) use ($item) {
                $v = strtolower(trim((string)$val));
                switch ($item['key']) {
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

            $pct = round(($cnt / $totalOrganoSampel) * 100);
            if ($item['isMurni']) {
                $murniPctTotal += $pct;
            } else {
                $campuranPctTotal += $pct;
            }

            $sheet->setCellValue("G{$rowOrg}", $item['label']);
            $sheet->setCellValue("H{$rowOrg}", $pct . '%');
            $sheet->setCellValue("I{$rowOrg}", $item['ket']);

            if ($item['key'] === 'ok' && $pct > 0) {
                $sheet->getStyle("H{$rowOrg}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);
            }

            $rowOrg++;
        }

        // --- TABEL KANAN: %Kadar Air ---
        $rKa = $rowOrg;
        $sheet->setCellValue("G{$rKa}", $kaStdText);
        $sheet->mergeCells("G{$rKa}:I{$rKa}");
        $sheet->getStyle("G{$rKa}:I{$rKa}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("G{$rKa}:I{$rKa}")->getFont()->setBold(true);

        $avgKa = $kaVals->isNotEmpty() ? $kaVals->avg() : null;
        $minKa = $kaVals->isNotEmpty() ? $kaVals->min() : null;
        $maxKa = $kaVals->isNotEmpty() ? $kaVals->max() : null;

        $sheet->setCellValue('G' . ($rKa + 1), 'Avg,');
        $sheet->setCellValue('H' . ($rKa + 1), $avgKa !== null ? number_format($avgKa, 2, ',', '.') : '-');

        $sheet->setCellValue('G' . ($rKa + 2), 'Min,');
        $sheet->setCellValue('H' . ($rKa + 2), $minKa !== null ? number_format($minKa, 2, ',', '.') : '-');

        $sheet->setCellValue('G' . ($rKa + 3), 'Max,');
        $sheet->setCellValue('H' . ($rKa + 3), $maxKa !== null ? number_format($maxKa, 2, ',', '.') : '-');

        if ($avgKa !== null && $avgKa > $kaMaxSpec) {
            $sheet->getStyle('H' . ($rKa + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }
        if ($minKa !== null && $minKa > $kaMaxSpec) {
            $sheet->getStyle('H' . ($rKa + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }
        if ($maxKa !== null && $maxKa > $kaMaxSpec) {
            $sheet->getStyle('H' . ($rKa + 3))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        $totalKaSampel = $kaVals->count() ?: 1;
        $cntKaInSpec = $kaVals->filter(fn($v) => $v <= $kaMaxSpec)->count();
        $pctKaInSpec = ($cntKaInSpec / $totalKaSampel) * 100;
        $cntKaOver = $kaVals->filter(fn($v) => $v > $kaMaxSpec)->count();
        $pctKaOver = ($cntKaOver / $totalKaSampel) * 100;

        $sheet->setCellValue('G' . ($rKa + 4), '%Qty Sample KA Sesuai Standar (<= ' . number_format($kaMaxSpec, 0, ',', '.') . '%) (#)');
        $sheet->setCellValue('H' . ($rKa + 4), number_format($pctKaInSpec, 2, ',', '.') . '%');
        if ($pctKaInSpec > 0) {
            $sheet->getStyle('H' . ($rKa + 4))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);
        }

        $sheet->setCellValue('G' . ($rKa + 5), '%Qty Sample KA Over ' . number_format($kaMaxSpec, 0, ',', '.') . '% (#)');
        $sheet->setCellValue('H' . ($rKa + 5), number_format($pctKaOver, 2, ',', '.') . '%');
        if ($pctKaOver > 0) {
            $sheet->getStyle('H' . ($rKa + 5))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($yellowFill);
        }

        // --- TABEL KANAN: Rasa Khas Gula ---
        $rRasa = $rKa + 6;
        $displayJenisRasa = $isGulaTebu ? 'Gula Tebu' : ($isGulaKelapa ? 'Gula Kelapa' : ucwords(strtolower($jenisUpper)));
        $sheet->setCellValue("G{$rRasa}", 'Rasa Khas ' . $displayJenisRasa);
        $sheet->mergeCells("G{$rRasa}:I{$rRasa}");
        $sheet->getStyle("G{$rRasa}:I{$rRasa}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($subHeaderFill);
        $sheet->getStyle("G{$rRasa}:I{$rRasa}")->getFont()->setBold(true);

        $sheet->setCellValue('G' . ($rRasa + 1), 'Murni');
        $sheet->setCellValue('H' . ($rRasa + 1), $murniPctTotal . '%');
        $sheet->setCellValue('I' . ($rRasa + 1), $ketMurni);
        if ($murniPctTotal > 0) {
            $sheet->getStyle('H' . ($rRasa + 1))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($greenFill);
        }

        $sheet->setCellValue('G' . ($rRasa + 2), 'Campuran');
        $sheet->setCellValue('H' . ($rRasa + 2), $campuranPctTotal . '%');
        $sheet->setCellValue('I' . ($rRasa + 2), 'Rasa campuran dan pahit');

        $rightTableEndRow = $rRasa + 2;

        // -------------------------------------------------------------
        // 3. BORDER STYLING & CELL ALIGNMENT
        // -------------------------------------------------------------
        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        // Border Tabel Kiri (B8:E[end])
        $sheet->getStyle("B8:E{$leftTableEndRow}")->applyFromArray($thinBorder);
        $sheet->getStyle("D8:E{$leftTableEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border Tabel Kanan (G8:I[end])
        $sheet->getStyle("G8:I{$rightTableEndRow}")->applyFromArray($thinBorder);
        $sheet->getStyle("H8:H{$rightTableEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set Column Widths agar rapi dan proporsional
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(4);
        $sheet->getColumnDimension('G')->setWidth(38);
        $sheet->getColumnDimension('H')->setWidth(14);
        $sheet->getColumnDimension('I')->setWidth(36);

        // Font default seluruh sheet: Calibri 10
        $maxRow = max($leftTableEndRow, $rightTableEndRow);
        $sheet->getStyle("A1:I{$maxRow}")->getFont()->setName('Calibri')->setSize(10);
        $sheet->getStyle('D1')->getFont()->setSize(14)->setBold(true);
    }
}

/**
 * Sheet 2: Data Mentah Hasil Uji Per Sampel (1 s/d N)
 */
class DataSampelRmSheetExport implements WithEvents, WithTitle
{
    protected IdentitasRM $identitas;

    public function __construct(IdentitasRM $identitas)
    {
        $this->identitas = $identitas;
    }

    public function title(): string
    {
        return 'DATA SAMPEL';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildSampleSheet($sheet);
            },
        ];
    }

    protected function buildSampleSheet(Worksheet $sheet): void
    {
        $sheet->setShowGridlines(true);

        $identitas = $this->identitas;
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

        // Header sheet 2
        $sheet->setCellValue('A1', 'DATA HASIL UJI SAMPEL INCOMING');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue('A2', 'No SPB: ' . ($identitas->no_spb ?? '-') . ' | Bahan: ' . ($identitas->jenis ?? '-') . ' | Supplier: ' . ($identitas->supplier ?? '-'));
        $sheet->mergeCells('A2:I2');

        // Table Header
        $headers = ['Sampel Ke', 'Brix (°Bx)', 'pH', 'Timbang A (g)', 'Timbang B (g)', '% Kotoran', 'Timbang AA (g)', '% Kadar Air', 'Organoleptik'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        $headerRow = 4;
        foreach ($headers as $idx => $h) {
            $col = $cols[$idx];
            $sheet->setCellValue("{$col}{$headerRow}", $h);
        }

        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');
        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $currRow = 5;
        foreach ($incomingSamples as $idx => $s) {
            $sampelNo = $idx + 1;
            $brix = $s->brix !== null ? number_format((float)$s->brix, 2) : '-';
            $ph = $s->ph !== null ? number_format((float)$s->ph, 2) : '-';
            $timbangA = $s->timbang_a !== null ? number_format((float)$s->timbang_a, 4) : '-';
            $timbangB = $s->timbang_b !== null ? number_format((float)$s->timbang_b, 4) : '-';
            $kotoran = $s->kotoran !== null ? number_format((float)$s->kotoran, 3) : '-';
            $timbangAa = $s->timbang_aa !== null ? number_format((float)$s->timbang_aa, 4) : '-';
            
            $kaVal = $s->ka ?? $s->{'%ka'} ?? null;
            $ka = $kaVal !== null ? number_format((float)$kaVal, 2) : '-';
            $organo = $s->organo ?? '-';

            $sheet->setCellValue("A{$currRow}", $sampelNo);
            $sheet->setCellValue("B{$currRow}", $brix);
            $sheet->setCellValue("C{$currRow}", $ph);
            $sheet->setCellValue("D{$currRow}", $timbangA);
            $sheet->setCellValue("E{$currRow}", $timbangB);
            $sheet->setCellValue("F{$currRow}", $kotoran);
            $sheet->setCellValue("G{$currRow}", $timbangAa);
            $sheet->setCellValue("H{$currRow}", $ka);
            $sheet->setCellValue("I{$currRow}", $organo);

            $sheet->getStyle("A{$currRow}:H{$currRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currRow++;
        }

        $lastRow = max(5, $currRow - 1);
        $sheet->getStyle("A4:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
