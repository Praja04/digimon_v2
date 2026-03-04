<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonitoringPasteurisasiSeeder extends Seeder
{
    public function run(): void
    {
        $productionBatchId = 39;
        $qcUserId = 4;

        $batch = DB::table('production_batches')
            ->where('id', $productionBatchId)
            ->first();

        if (!$batch) {
            return;
        }

        $variant = strtoupper($batch->variant);

        $rows = DB::table('monitoring_pasteurisasi')
            ->where('production_batch_id', $productionBatchId)
            ->pluck('id');

        foreach ($rows as $id) {

            // Default values
            $brix = 76;
            $visco = 18;
            $nacl = 6.2;
            $bj = 1.38;
            $ph = 4.8;

            switch ($variant) {

                case 'MSD':
                    $brix = rand(7400, 7600) / 100;
                    $visco = rand(700, 1000) / 100;
                    $nacl = rand(1150, 1450) / 100;
                    $bj = rand(1380, 1390) / 1000;
                    $ph = rand(420, 500) / 100;
                    break;

                case 'JB':
                    $brix = rand(7600, 8000) / 100;
                    $visco = rand(1600, 2500) / 100;
                    $nacl = rand(600, 630) / 100;
                    $bj = rand(1380, 1400) / 1000;
                    $ph = rand(450, 500) / 100;
                    break;

                case 'SS1':
                    $brix = rand(7500, 7900) / 100;
                    $visco = rand(1400, 2200) / 100;
                    $nacl = rand(630, 650) / 100;
                    $bj = rand(1380, 1390) / 1000;
                    $ph = rand(450, 500) / 100;
                    break;

                case 'SS2':
                    $brix = rand(7500, 7900) / 100;
                    $visco = rand(1600, 2400) / 100;
                    $nacl = rand(630, 650) / 100;
                    $bj = rand(1380, 1390) / 1000;
                    $ph = rand(450, 500) / 100;
                    break;

                case 'BB':
                    $brix = rand(7700, 8100) / 100;
                    $visco = rand(1700, 2800) / 100;
                    $nacl = rand(600, 630) / 100;
                    $bj = rand(1390, 1410) / 1000;
                    $ph = rand(430, 530) / 100;
                    break;
            }

            $aw = rand(6000, 6700) / 10000;
            $buih = rand(10, 50) / 100;
            $endapan = rand(0, 9) / 100;

            DB::table('monitoring_pasteurisasi')->where('id', $id)->update([
                'disposition' => 'Release',
                'status'      => 'OK',
                'created_by'  => $qcUserId,
                'scanned_at'  => now(),
                'brix'        => $brix,
                'nacl'        => $nacl,
                'bj'          => $bj,
                'visco'       => $visco,
                'aw'          => $aw,
                'buih'        => $buih,
                'ph'          => $ph,
                'organo'      => 'OK',
                'aroma'       => 'OK',
                'endapan'     => $endapan,
                'shift'       => 1,
            ]);
        }
    }
}
