<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimbanganRetailBdktSeeder extends Seeder
{
    /**
     * Standar variant sesuai VARIANT_STANDARDS di TimbanganRetailMesinController.
     */
    private const VARIANT_STANDARDS = [
        'Sachet YB 20gr PCS'            => ['min' =>   19.14, 'std' =>   20.64, 'max' =>   21.64, 'tu1' =>   18.84, 'tu2' =>   17.04],
        'Sachet YB 20gr RENCENG'        => ['min' =>  244.68, 'std' =>  247.68, 'max' =>  259.68, 'tu1' =>  226.08, 'tu2' =>  204.48],
        'Sachet BB 40gr PCS'            => ['min' =>   39.10, 'std' =>   41.10, 'max' =>   42.10, 'tu1' =>   37.50, 'tu2' =>   33.90],
        'Sachet BB 40gr RENCENG'        => ['min' =>  489.20, 'std' =>  493.20, 'max' =>  505.20, 'tu1' =>  450.00, 'tu2' =>  406.80],
        'Pouch YB 77gr'                 => ['min' =>   78.70, 'std' =>   79.20, 'max' =>   82.70, 'tu1' =>   74.70, 'tu2' =>   70.20],
        'Pouch BB 77gr'                 => ['min' =>   78.70, 'std' =>   79.20, 'max' =>   82.70, 'tu1' =>   74.70, 'tu2' =>   70.20],
        'Pouch YB 250gr'                => ['min' =>  253.00, 'std' =>  255.00, 'max' =>  257.00, 'tu1' =>  246.00, 'tu2' =>  237.00],
        'Pouch BB 270gr'                => ['min' =>  273.00, 'std' =>  275.00, 'max' =>  277.00, 'tu1' =>  266.00, 'tu2' =>  257.00],
        'Pouch YB 550gr'                => ['min' =>  556.00, 'std' =>  561.00, 'max' =>  566.00, 'tu1' =>  545.80, 'tu2' =>  530.80],
        'Pouch YB 700gr'                => ['min' =>  706.00, 'std' =>  711.00, 'max' =>  716.00, 'tu1' =>  696.00, 'tu2' =>  681.00],
        'Pouch BB 725gr'                => ['min' =>  730.00, 'std' =>  735.00, 'max' =>  740.00, 'tu1' =>  720.00, 'tu2' =>  705.00],
        'Pouch YB 1000gr'               => ['min' => 1007.50, 'std' => 1012.50, 'max' => 1017.50, 'tu1' =>  997.50, 'tu2' =>  982.50],
        'Sachet BB 40gr RENCENG (6+1)'  => ['min' =>  569.40, 'std' =>  575.40, 'max' =>  589.40, 'tu1' =>  525.00, 'tu2' =>  474.60],
        'Sachet YB 20gr RENCENG (6+1)'  => ['min' =>  284.46, 'std' =>  288.96, 'max' =>  302.96, 'tu1' =>  263.76, 'tu2' =>  238.56],
        'Sachet YB 20gr RENCENG (12+1)' => ['min' =>  268.32, 'std' =>  268.32, 'max' =>  281.32, 'tu1' =>  244.92, 'tu2' =>  221.52],
        'Pouch BB 430gr'                => ['min' =>  434.50, 'std' =>  439.50, 'max' =>  444.50, 'tu1' =>  426.60, 'tu2' =>  413.70],
    ];

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

            // 150-250 transaksi BDKT per hari
            $count = rand(150, 250);

            for ($i = 0; $i < $count; $i++) {
                $start = $tanggal->copy()->setTime(6, 0, 0);
                $end   = $tanggal->copy()->addDay()->setTime(5, 59, 59);
                $waktu = Carbon::createFromTimestamp(rand($start->timestamp, $end->timestamp));

                $variant = $variants[array_rand($variants)];
                $mesin   = $mesins[array_rand($mesins)];
                $nik     = $niks[array_rand($niks)];
                $std     = self::VARIANT_STANDARDS[$variant];

                $roll = rand(1, 100);
                if ($roll <= 85) {
                    $berat = $this->randomFloat($std['min'], $std['max']);
                } elseif ($roll <= 95) {
                    if (rand(0, 1) === 0) {
                        $berat = $this->randomFloat($std['tu1'], $std['min']);
                    } else {
                        $berat = $this->randomFloat($std['max'], $std['max'] * 1.05);
                    }
                } else {
                    if (rand(0, 1) === 0) {
                        $berat = $this->randomFloat($std['tu2'] * 0.90, $std['tu2']);
                    } else {
                        $berat = $this->randomFloat($std['max'] * 1.05, $std['max'] * 1.15);
                    }
                }

                $berat = round($berat, 2);
                $status = ($berat >= $std['min'] && $berat <= $std['max']) ? 'OK' : 'NOT OK';
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

            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('timbangan_retail_bdkt')->insert($chunk);
            }

            $this->command->info("✅ BDKT Hari -{$day}: {$count} transaksi di-seed.");
        }

        $this->command->info("🎉 Seeder BDKT selesai!");
    }

    private function randomFloat(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
