<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimbanganRetailMesinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mesins = ['A', 'B', 'C'];
        $variants = ['JB', 'KC', 'MN'];
        $statuses = ['OK', 'REJECT'];
        $units = ['g']; /* |-------------------------------------------------------------------------- | Generate data 5 hari kebelakang |-------------------------------------------------------------------------- */
        for ($day = 0; $day < 5; $day++) {
            $tanggal = Carbon::now()->subDays($day)->startOfDay();
            $rows = []; /* |-------------------------------------------------------------------------- | Generate 300 transaksi per hari |-------------------------------------------------------------------------- */
            for ($i = 1; $i <= 300; $i++) { /* |-------------------------------------------------------------------------- | Random waktu dalam konsep shift |-------------------------------------------------------------------------- | 06:00 hari ini -> 05:59 hari berikutnya |-------------------------------------------------------------------------- */
                $start = $tanggal->copy()->setTime(6, 0, 0);
                $end = $tanggal->copy()->addDay()->setTime(5, 59, 59);
                $randomTimestamp = rand($start->timestamp, $end->timestamp);
                $waktu = Carbon::createFromTimestamp($randomTimestamp);
                $rows[] = ['mesin' => $mesins[array_rand($mesins)], 'variant' => $variants[array_rand($variants)], 'waktu' => $waktu, 'status' => $statuses[array_rand($statuses)], 'nik' => str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT), /* |-------------------------------------------------------------------------- | Berat random realistic |-------------------------------------------------------------------------- */ 'berat' => rand(5000, 9000) / 100, 'unit' => $units[array_rand($units)], 'created_at' => now(), 'updated_at' => now(),];
            } /* |-------------------------------------------------------------------------- | Bulk insert (lebih cepat) |-------------------------------------------------------------------------- */
            DB::table('timbangan_retail_mesin')->insert($rows);
        }
    }
}
