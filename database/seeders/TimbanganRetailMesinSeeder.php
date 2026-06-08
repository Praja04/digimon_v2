<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimbanganRetailMesinSeeder extends Seeder
{
    /**
     * Standar variant sesuai VARIANT_STANDARDS di TimbanganRetailMesinController.
     * Berat di-generate realistis: mayoritas normal, sebagian kecil abnormal.
     */
    private const VARIANT_STANDARDS = [
        'Sachet YB 12,5gr PCS'     => ['min' =>   12.05, 'std' =>   13.05, 'max' =>   14.05, 'tu1' =>   11.93, 'tu2' =>   10.80],
        'Sachet YB 12,5gr RENCENG' => ['min' =>  154.60, 'std' =>  156.60, 'max' =>  168.60, 'tu1' =>  143.10, 'tu2' =>  129.60],
        'Sachet YB 20gr PCS'       => ['min' =>   19.14, 'std' =>   20.64, 'max' =>   21.64, 'tu1' =>   18.84, 'tu2' =>   17.04],
        'Sachet YB 20gr RENCENG'   => ['min' =>  244.68, 'std' =>  247.68, 'max' =>  259.68, 'tu1' =>  226.08, 'tu2' =>  204.48],
        'Sachet BB 40gr PCS'       => ['min' =>   39.10, 'std' =>   41.10, 'max' =>   42.10, 'tu1' =>   37.50, 'tu2' =>   33.90],
        'Sachet BB 40gr RENCENG'   => ['min' =>  489.20, 'std' =>  493.20, 'max' =>  505.20, 'tu1' =>  450.00, 'tu2' =>  406.80],
        'Pouch YB 77gr'            => ['min' =>   78.70, 'std' =>   79.20, 'max' =>   82.70, 'tu1' =>   74.70, 'tu2' =>   70.20],
        'Pouch BB 77gr'            => ['min' =>   78.70, 'std' =>   79.20, 'max' =>   82.70, 'tu1' =>   74.70, 'tu2' =>   70.20],
        'Pouch YB 250gr'           => ['min' =>  253.00, 'std' =>  255.00, 'max' =>  257.00, 'tu1' =>  246.00, 'tu2' =>  237.00],
        'Pouch BB 270gr'           => ['min' =>  273.00, 'std' =>  275.00, 'max' =>  277.00, 'tu1' =>  266.00, 'tu2' =>  257.00],
        'Pouch YB 550gr'           => ['min' =>  556.00, 'std' =>  561.00, 'max' =>  566.00, 'tu1' =>  545.80, 'tu2' =>  530.80],
        'Pouch YB 700gr'           => ['min' =>  706.00, 'std' =>  711.00, 'max' =>  716.00, 'tu1' =>  696.00, 'tu2' =>  681.00],
        'Pouch BB 725gr'           => ['min' =>  730.00, 'std' =>  735.00, 'max' =>  740.00, 'tu1' =>  720.00, 'tu2' =>  705.00],
        'Pouch YB 1000gr'          => ['min' => 1007.50, 'std' => 1012.50, 'max' => 1017.50, 'tu1' =>  997.50, 'tu2' =>  982.50],
    ];

    /**
     * Daftar mesin sesuai yang dipakai di production.
     */
    private const MESINS = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
        'O', 'P', 'Q', 'R', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'AE', 'AF', 'AG', 'AH', 'AI', 'AJ',
    ];

    private const NIKS = [
        '220000101', '220000102', '220000155', '220000203', '220000262',
        '220000311', '220000350', '220000401', '220000455', '220000502',
        '220000610', '220000715', '220000820', '220000901', '220001005',
    ];

    public function run(): void
    {
        $variants = array_keys(self::VARIANT_STANDARDS);
        $mesins   = self::MESINS;
        $niks     = self::NIKS;

        // Generate data 7 hari kebelakang
        for ($day = 0; $day < 7; $day++) {
            $tanggal = Carbon::now()->subDays($day)->startOfDay();
            $rows = [];

            // 400–600 transaksi per hari
            $count = rand(400, 600);

            for ($i = 0; $i < $count; $i++) {
                // Random waktu dalam production day (06:00 → 05:59 hari berikutnya)
                $start = $tanggal->copy()->setTime(6, 0, 0);
                $end   = $tanggal->copy()->addDay()->setTime(5, 59, 59);
                $waktu = Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp));

                $variant = $variants[array_rand($variants)];
                $mesin   = $mesins[array_rand($mesins)];
                $nik     = $niks[array_rand($niks)];
                $std     = self::VARIANT_STANDARDS[$variant];

                // Generate berat yang realistis
                // 85% normal (antara min–max), 10% slightly abnormal, 5% very abnormal
                $roll = rand(1, 100);
                if ($roll <= 85) {
                    // Normal: antara min dan max
                    $berat = $this->randomFloat($std['min'], $std['max']);
                } elseif ($roll <= 95) {
                    // Slightly abnormal: antara tu1 dan min, atau sedikit di atas max
                    if (rand(0, 1) === 0) {
                        // Below min (tu1 → min range)
                        $berat = $this->randomFloat($std['tu1'], $std['min']);
                    } else {
                        // Over max (max → max+10%)
                        $berat = $this->randomFloat($std['max'], $std['max'] * 1.05);
                    }
                } else {
                    // Very abnormal: below tu2 or way over max
                    if (rand(0, 1) === 0) {
                        // Kritis: below tu2
                        $berat = $this->randomFloat($std['tu2'] * 0.90, $std['tu2']);
                    } else {
                        // Very over: way above max
                        $berat = $this->randomFloat($std['max'] * 1.05, $std['max'] * 1.15);
                    }
                }

                $berat = round($berat, 2);

                // Determine status based on weight
                $status = ($berat >= $std['min'] && $berat <= $std['max']) ? 'OK' : 'NOT OK';

                // Assign filler (1–8) randomly, some null
                $filler = rand(1, 10) <= 7 ? (string) rand(1, 8) : null;

                $rows[] = [
                    'mesin'      => $mesin,
                    'variant'    => $variant,
                    'waktu'      => $waktu->format('Y-m-d H:i:s'),
                    'status'     => $status,
                    'berat'      => $berat,
                    'unit'       => 'g',
                    'nik'        => $nik,
                    'filler'     => $filler,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert per hari (chunk 100 per insert untuk menghindari limit)
            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('timbangan_retail_mesin')->insert($chunk);
            }

            $this->command->info("✅ Hari -{$day}: {$count} transaksi di-seed.");
        }

        $this->command->info("🎉 Seeder selesai! Total ±" . (7 * 500) . " data timbangan retail.");
    }

    /**
     * Generate random float between min and max.
     */
    private function randomFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
