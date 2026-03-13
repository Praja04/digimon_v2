<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonitoringTurunBlendingSeeder extends Seeder
{
    public function run(): void
    {
        $productionBatchId = 28;
        $qcUserId = 4;

        $batch = DB::table('production_batches')
            ->where('id', $productionBatchId)
            ->first();

        if (!$batch) {
            return;
        }

        $variant = strtoupper($batch->variant);

        $rows = DB::table('monitoring_turun_blending')
            ->where('production_batch_id', $productionBatchId)
            ->pluck('id');

        foreach ($rows as $id) {
            $brix = 76;
            $visco = 18;

            switch ($variant) {

                case 'MSD':
                    $brix = rand(7400, 7600) / 100;
                    $visco = rand(700, 1000) / 100;
                    break;

                case 'JB':
                    $brix = rand(7600, 8000) / 100;
                    $visco = rand(1600, 2500) / 100;
                    break;

                case 'SS1':
                    $brix = rand(7500, 7900) / 100;
                    $visco = rand(1400, 2200) / 100;
                    break;

                case 'SS2':
                    $brix = rand(7500, 7900) / 100;
                    $visco = rand(1600, 2400) / 100;
                    break;

                case 'BB':
                    $brix = rand(7700, 8100) / 100;
                    $visco = rand(1700, 2800) / 100;
                    break;
            }

            // aw selalu < 0.6700
            $aw = rand(6000, 6700) / 10000;

            DB::table('monitoring_turun_blending')->where('id', $id)->update([
                'disposition' => 'Release',
                'status'      => 'OK',
                'created_by'  => $qcUserId,
                'scanned_at'  => now(),
                'brix'        => $brix,
                'visco'       => $visco,
                'aw'          => $aw,
                'shift'       => 1,
            ]);
        }
    }
}
