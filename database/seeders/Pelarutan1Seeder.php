<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Pelarutan1Seeder extends Seeder
{
    public function run(): void
    {
        $productionBatchId = 37;
        $qcUserId = 4;

        $batch = DB::table('production_batches')
            ->where('id', $productionBatchId)
            ->first();

        if (!$batch) {
            return;
        }

        $variant = strtoupper($batch->variant);

        $rows = DB::table('pelarutan_1')
            ->where('production_batch_id', $productionBatchId)
            ->pluck('id');

        foreach ($rows as $id) {

            // Default
            $brix = 80;
            $nacl = 2;

            switch ($variant) {

                case 'MSD':
                    // Brix min 78
                    $brix = rand(7800, 8200) / 100;

                    // NaCl 6.0 – 7.0
                    $nacl = rand(600, 700) / 100;
                    break;

                case 'JB':
                    // Brix min 81
                    $brix = rand(8100, 8500) / 100;

                    // NaCl 1.0 – 3.0
                    $nacl = rand(100, 300) / 100;
                    break;

                case 'SS1':
                    // Brix min 80
                    $brix = rand(8000, 8400) / 100;

                    // NaCl 1.0 – 3.0
                    $nacl = rand(100, 300) / 100;
                    break;

                case 'SS2':
                    // Brix min 80
                    $brix = rand(8000, 8400) / 100;

                    // NaCl 1.0 – 3.0
                    $nacl = rand(100, 300) / 100;
                    break;

                case 'BB':
                    // Brix min 83
                    $brix = rand(8300, 8700) / 100;

                    // NaCl 2.0 – 3.0
                    $nacl = rand(200, 300) / 100;
                    break;
            }

            DB::table('pelarutan_1')->where('id', $id)->update([
                'brix'        => $brix,
                'nacl'        => $nacl,
                'organo'      => 'OK',
                'disposition' => 'Release',
                'status'      => 'OK',
                'scanned_at'  => now(),
                'created_by'  => $qcUserId,
            ]);
        }
    }
}
