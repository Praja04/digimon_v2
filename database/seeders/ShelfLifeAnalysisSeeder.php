<?php

namespace Database\Seeders;

use App\Models\ShelfLifeSamplingDetail;
use App\Models\ShelfLifeSamplingKimia;
use App\Models\ShelfLifeSamplingMikro;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShelfLifeAnalysisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $details = ShelfLifeSamplingDetail::where('kelompok_tanggal', '8')
            ->orderBy('bulan_ke', 'asc')
            ->get();

        if ($details->isEmpty()) {
            $this->command->error('Data dengan kelompok_tanggal "10" tidak ditemukan!');
            return;
        }

        $this->command->info('Ditemukan ' . $details->count() . ' data sampling detail');
        $this->command->info('==============================================');

        foreach ($details as $detail) {
            $bulanKe = $detail->bulan_ke;

            // Cek apakah sudah ada analisa kimia
            $existingKimia = ShelfLifeSamplingKimia::where('shelf_life_sampling_detail_id', $detail->id)->first();
            if ($existingKimia) {
                $this->command->warn("Bulan ke-{$bulanKe} (ID: {$detail->id}) sudah memiliki analisa kimia, skip...");
            } else {
                // Tentukan kondisi parameter berdasarkan bulan_ke
                $hideVisco = in_array($bulanKe, [7, 8, 9, 10, 11, 15, 21]);
                $showTotalNitrogen = in_array($bulanKe, [6, 12, 18, 24]);

                // Create Analisa Kimia
                $kimiaData = [
                    'shelf_life_sampling_detail_id' => $detail->id,
                    'shift_analis' => $this->getRandomShift(),
                    'nama_analis' => $this->getRandomAnalis(),
                    'waktu_analisa' => Carbon::now()->subDays(rand(1, 10))->setTime(rand(8, 16), rand(0, 59)),
                    'nacl' => $this->generateNacl($bulanKe),
                    'brix' => $this->generateBrix($bulanKe),
                    'aw' => $this->generateAw($bulanKe),
                    'ph' => $this->generatePh($bulanKe),
                    'bj' => $this->generateBj($bulanKe),
                    'buih' => $this->generateBuih($bulanKe),
                    'aroma' => $this->getRandomAroma(),
                    'color_id' => 1,
                    'organo' => $this->getRandomOrgano(),
                    'scanned_at' => Carbon::now()->subDays(rand(1, 10)),
                ];

                // Tambahkan visco jika tidak hide
                if (!$hideVisco) {
                    $kimiaData['visco'] = $this->generateVisco($bulanKe);
                }

                // Tambahkan total nitrogen jika perlu
                if ($showTotalNitrogen) {
                    $kimiaData['total_nitrogen'] = $this->generateTotalNitrogen($bulanKe);
                }

                ShelfLifeSamplingKimia::create($kimiaData);
                $this->command->info("✓ Analisa Kimia Bulan ke-{$bulanKe} (Detail ID: {$detail->id}) berhasil dibuat");
            }

            // Cek apakah sudah ada analisa mikro
            $existingMikro = ShelfLifeSamplingMikro::where('shelf_life_sampling_detail_id', $detail->id)->first();
            if ($existingMikro) {
                $this->command->warn("Bulan ke-{$bulanKe} (ID: {$detail->id}) sudah memiliki analisa mikro, skip...");
            } else {
                // Create Analisa Mikro
                $mikroData = [
                    'shelf_life_sampling_detail_id' => $detail->id,
                    'shift_analis' => $this->getRandomShift(),
                    'nama_analis' => $this->getRandomAnalis(),
                    'waktu_analisa' => Carbon::now()->subDays(rand(1, 10))->setTime(rand(8, 16), rand(0, 59)),
                    'eb' => $this->generateEB($bulanKe),
                    'tpc' => $this->generateTPC($bulanKe),
                    'ym' => $this->generateYM($bulanKe),
                    'scanned_at' => Carbon::now()->subDays(rand(1, 10)),
                ];

                // Tambahkan SA hanya untuk bulan ke-1 dan ke-24
                if (in_array($bulanKe, [1, 24])) {
                    $mikroData['sa'] = $this->generateSA($bulanKe);
                }

                ShelfLifeSamplingMikro::create($mikroData);
                $this->command->info("✓ Analisa Mikro Bulan ke-{$bulanKe} (Detail ID: {$detail->id}) berhasil dibuat");
            }
        }

        $this->command->info('==============================================');
        $this->command->info('Shelf Life Analysis Seeder berhasil dijalankan!');
        $this->command->info('Total Analisa Kimia: ' . ShelfLifeSamplingKimia::count());
        $this->command->info('Total Analisa Mikro: ' . ShelfLifeSamplingMikro::count());
    }

    /**
     * Generate nilai NACL berdasarkan bulan_ke (simulasi degradasi)
     */
    private function generateNacl($bulanKe): float
    {
        // Nilai awal 15.0-18.0, sedikit meningkat seiring waktu
        $base = rand(150, 180) / 10;
        $degradation = ($bulanKe * 0.05); // Naik 0.05 per bulan
        return round($base + $degradation, 2);
    }

    /**
     * Generate nilai BRIX berdasarkan bulan_ke
     */
    private function generateBrix($bulanKe): float
    {
        // Nilai awal 48.0-52.0, sedikit menurun seiring waktu
        $base = rand(480, 520) / 10;
        $degradation = ($bulanKe * 0.03); // Turun 0.03 per bulan
        return round($base - $degradation, 2);
    }

    /**
     * Generate nilai Aw berdasarkan bulan_ke
     */
    private function generateAw($bulanKe): float
    {
        // Nilai awal 0.650-0.750, sedikit meningkat seiring waktu
        $base = rand(650, 750) / 1000;
        $degradation = ($bulanKe * 0.001); // Naik 0.001 per bulan
        return round($base + $degradation, 3);
    }

    /**
     * Generate nilai pH berdasarkan bulan_ke
     */
    private function generatePh($bulanKe): float
    {
        // Nilai awal 4.5-5.5, relatif stabil
        $base = rand(45, 55) / 10;
        $variation = rand(-2, 2) / 100; // Variasi kecil
        return round($base + $variation, 2);
    }

    /**
     * Generate nilai BJ berdasarkan bulan_ke
     */
    private function generateBj($bulanKe): float
    {
        // Nilai awal 1.300-1.350
        $base = rand(1300, 1350) / 1000;
        return round($base, 3);
    }

    /**
     * Generate nilai Buih berdasarkan bulan_ke
     */
    private function generateBuih($bulanKe): float
    {
        // Nilai awal 0.5-1.5, meningkat seiring waktu
        $base = rand(5, 15) / 10;
        $increase = ($bulanKe * 0.02); // Naik 0.02 per bulan
        return round($base + $increase, 2);
    }

    /**
     * Generate nilai Visco berdasarkan bulan_ke
     */
    private function generateVisco($bulanKe): float
    {
        // Nilai awal 200.0-300.0, menurun seiring waktu
        $base = rand(2000, 3000) / 10;
        $degradation = ($bulanKe * 2); // Turun 2 per bulan
        return round($base - $degradation, 2);
    }

    /**
     * Generate nilai Total Nitrogen
     */
    private function generateTotalNitrogen($bulanKe): float
    {
        // Nilai awal 1.0-2.0
        $base = rand(10, 20) / 10;
        return round($base, 2);
    }

    /**
     * Generate nilai EB (E. coli) - biasanya negatif atau sangat rendah
     */
    private function generateEB($bulanKe): float
    {
        // Kemungkinan 80% negatif (0), 20% positif rendah
        if (rand(1, 100) <= 80) {
            return 0;
        }
        return round(rand(1, 5) / 10, 2); // 0.1 - 0.5
    }

    /**
     * Generate nilai SA (Salmonella) - hanya untuk bulan ke-1 dan ke-24
     */
    private function generateSA($bulanKe): float
    {
        // Kemungkinan 95% negatif
        if (rand(1, 100) <= 95) {
            return 0;
        }
        return round(rand(1, 3) / 10, 2); // 0.1 - 0.3
    }

    /**
     * Generate nilai TPC - meningkat seiring waktu
     */
    private function generateTPC($bulanKe): float
    {
        // Nilai awal 100-1000, meningkat seiring waktu
        $base = rand(100, 1000);
        $increase = ($bulanKe * 50); // Naik 50 per bulan
        return round($base + $increase, 2);
    }

    /**
     * Generate nilai YM (Yeast & Mold) - meningkat seiring waktu
     */
    private function generateYM($bulanKe): float
    {
        // Nilai awal 10-100, meningkat seiring waktu
        $base = rand(10, 100);
        $increase = ($bulanKe * 10); // Naik 10 per bulan
        return round($base + $increase, 2);
    }

    /**
     * Get random shift
     */
    private function getRandomShift(): int
    {
        return rand(1, 3);
    }

    /**
     * Get random analis name
     */
    private function getRandomAnalis(): string
    {
        $analis = [
            'BUDI SANTOSO',
            'SITI NURHALIZA',
            'AHMAD FAUZI',
            'DEWI LESTARI',
            'RIZKI PRATAMA',
            'MAYA SARI',
            'ANDI WIJAYA',
            'RATNA WATI',
            'FAJAR NUGROHO',
            'LINDA WIJAYANTI',
        ];

        return $analis[array_rand($analis)];
    }

    /**
     * Get random aroma for kecap
     */
    private function getRandomAroma(): string
    {
        $aromas = [
            'KHAS KECAP',
            'KHAS KECAP MANIS',
            'NORMAL',
            'KHAS',
            'SESUAI STANDAR',
        ];

        return $aromas[array_rand($aromas)];
    }

    /**
     * Get random organo result
     */
    private function getRandomOrgano(): string
    {
        $organos = [
            'BAIK',
            'NORMAL',
            'SESUAI STANDAR',
            'TIDAK ADA KELAINAN',
        ];

        return $organos[array_rand($organos)];
    }
}
