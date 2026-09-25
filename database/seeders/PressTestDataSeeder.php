<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PressTestData;

class PressTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'variant_name' => 'P 77gr YB/BB',
                'ok_min'       => 10.70,
                'ok_max'       => 11.20,
                'bocor_min'    => 11.45,
                'bocor_max'    => 11.50,
                'gap'          => 0.25,
                'note'         => 'Gap sempit — presisi tinggi',
            ],
            [
                'variant_name' => 'P 250gr YB',
                'ok_min'       => 10.00,
                'ok_max'       => 10.15,
                'bocor_min'    => 11.41,
                'bocor_max'    => 11.50,
                'gap'          => 1.26,
                'note'         => 'Gap lebar — toleransi besar',
            ],
            [
                'variant_name' => 'P 270gr BB',
                'ok_min'       => 10.00,
                'ok_max'       => 10.15,
                'bocor_min'    => 11.41,
                'bocor_max'    => 11.50,
                'gap'          => 1.26,
                'note'         => 'Sama dengan P 250gr YB',
            ],
            [
                'variant_name' => 'P 550gr YB',
                'ok_min'       => 9.30,
                'ok_max'       => 9.70,
                'bocor_min'    => 10.00,
                'bocor_max'    => 11.50,
                'gap'          => 0.30,
                'note'         => 'Gap kecil',
            ],
            [
                'variant_name' => 'P 700gr YB',
                'ok_min'       => 9.00,
                'ok_max'       => 9.30,
                'bocor_min'    => 9.85,
                'bocor_max'    => 11.50,
                'gap'          => 0.55,
                'note'         => 'Standard Pouch',
            ],
            [
                'variant_name' => 'P 725gr BB',
                'ok_min'       => 8.55,
                'ok_max'       => 9.25,
                'bocor_min'    => 10.05,
                'bocor_max'    => 11.50,
                'gap'          => 0.80,
                'note'         => 'Pouch 725',
            ],
            [
                'variant_name' => 'P 1000gr YB',
                'ok_min'       => 8.30,
                'ok_max'       => 8.90,
                'bocor_min'    => 11.10,
                'bocor_max'    => 11.30,
                'gap'          => 2.20,
                'note'         => 'Gap terlebar — zona abu luas',
            ],
        ];

        foreach ($data as $item) {
            PressTestData::updateOrCreate(
                ['variant_name' => $item['variant_name']],
                $item
            );
        }
    }
}
