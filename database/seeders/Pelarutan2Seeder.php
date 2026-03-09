<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Pelarutan2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productionBatchId = 25;
        $qcUserId = 4;

        $batch = DB::table('production_batches')
            ->where('id', $productionBatchId)
            ->first();

        if (!$batch) {
            return;
        }

        $variant = strtoupper($batch->variant);

        $rows = DB::table('pelarutan_2')
            ->where('production_batch_id', $productionBatchId)
            ->pluck('id');

        foreach ($rows as $id) {

            // Default
            $brix = 78;
            $nacl = 4;
            $visco = 100;

            switch ($variant) {

                case 'MSD':
                    $brix = rand(7400, 7600) / 100;
                    $nacl = rand(800, 900) / 100;
                    $visco = rand(700, 1000) / 100;
                    break;

                case 'JB':
                    $brix = rand(7800, 8200) / 100;
                    $nacl = rand(300, 600) / 100;
                    $visco = rand(1600, 2500) / 100;
                    break;

                case 'SS1':
                    $brix = rand(7700, 8100) / 100;
                    $nacl = rand(300, 600) / 100;
                    $visco = rand(1400, 2200) / 100;
                    break;

                case 'SS2':
                    $brix = rand(7700, 8100) / 100;
                    $nacl = rand(300, 600) / 100;
                    $visco = rand(1600, 2400) / 100;
                    break;

                case 'BB':
                    $brix = rand(8100, 8500) / 100;
                    $nacl = rand(400, 500) / 100;
                    $visco = rand(1700, 2800) / 100;
                    break;
            }

            DB::table('pelarutan_2')->where('id', $id)->update([
                'brix'        => $brix,
                'nacl'        => $nacl,
                'visco'       => $visco,
                'organo'      => 'OK',
                'disposition' => 'Release',
                'status'      => 'OK',
                'scanned_at'  => now(),
                'created_by'  => $qcUserId,
            ]);
        }
    }
}
