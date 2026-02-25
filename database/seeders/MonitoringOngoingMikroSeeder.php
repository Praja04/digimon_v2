<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonitoringOngoingMikroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productionBatchId = 33;

        DB::table('monitoring_on_going_mikro')->where('production_batch_id', $productionBatchId)->update([
            'nama_analis' => 'Analis 1',
            'shift' => 1,
            'eb' => 0,
            'tpc' => 30,
            'ym' => 0,
            'benda_asing' => 'Tidak Ada',
            'hasil' => 'OK',
            'disposition' => 'Release',
            'scanned_at' => now(),
        ]);
    }
}
