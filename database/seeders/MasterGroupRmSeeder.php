<?php

namespace Database\Seeders;

use App\Models\MasterGroupRm;
use App\Models\MasterJenisBahan;
use Illuminate\Database\Seeder;

class MasterGroupRmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gulaKelapa = MasterJenisBahan::where('nama', 'GULA KELAPA')->first();
        if (!$gulaKelapa) {
            $gulaKelapa = MasterJenisBahan::firstOrCreate(
                ['nama' => 'GULA KELAPA'],
                ['status' => true]
            );
        }

        $items = [
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'pH',
                'kategori' => 'Qty sample pH Under 5,3 (#)',
                'target' => 'Max. 30%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'pH',
                'kategori' => 'Qty sample pH Over 6,5 (#)',
                'target' => 'Max. 30%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => '%Kotoran',
                'kategori' => '%Qty Sample Kotoran Over 10-14%(#)',
                'target' => 'Max. 10%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => '%Kotoran',
                'kategori' => '%Qty Sample Kotoran Over 14%(#)',
                'target' => 'Max. 10%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Organo',
                'kategori' => 'OK',
                'target' => 'Min. 70%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Organo',
                'kategori' => 'Agak asam',
                'target' => 'Max. 30%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Organo',
                'kategori' => 'Asam',
                'target' => 'Max. 20%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Organo',
                'kategori' => 'Agak pahit',
                'target' => 'Max. 20%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Organo',
                'kategori' => 'Pahit',
                'target' => 'Max. 10%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Organo',
                'kategori' => 'Sagu',
                'target' => 'Max. 30%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Organo',
                'kategori' => 'Kapur',
                'target' => 'Max. 30%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Organo',
                'kategori' => 'Campuran (Bisa input teks)',
                'target' => 'Max. 20%',
                'status' => true,
            ],
            [
                'jenis_bahan_id' => $gulaKelapa->id,
                'grup' => 'A',
                'parameter' => 'Kadar Air',
                'kategori' => '%Qty Sample KA Over 8% (#)',
                'target' => 'Max. 30%',
                'status' => true,
            ],
        ];

        foreach ($items as $item) {
            MasterGroupRm::updateOrCreate(
                [
                    'jenis_bahan_id' => $item['jenis_bahan_id'],
                    'grup' => $item['grup'],
                    'parameter' => $item['parameter'],
                    'kategori' => $item['kategori'],
                ],
                $item
            );
        }
    }
}
