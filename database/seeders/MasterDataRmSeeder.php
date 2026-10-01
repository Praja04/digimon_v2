<?php

namespace Database\Seeders;

use App\Models\MasterAsalBahan;
use App\Models\MasterGlassware;
use App\Models\MasterJenisBahan;
use App\Models\MasterStandarRm;
use App\Models\MasterSupplierRm;
use Illuminate\Database\Seeder;

class MasterDataRmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Jenis Bahan Data
        $jenisBahans = [
            'GULA TEBU',
            'GULA KELAPA',
            'GULA KELAPA B',
            'GARAM',
            'GULA',
            'MSG',
        ];

        $jenisBahanMap = [];
        foreach ($jenisBahans as $nama) {
            $jb = MasterJenisBahan::firstOrCreate(
                ['nama' => $nama],
                ['status' => true]
            );
            $jenisBahanMap[$nama] = $jb->id;
        }

        // 2 & 3. Supplier / Manufactur & Asal Bahan
        $supplierAsalList = [
            [
                'jenis_bahan' => 'GULA TEBU',
                'supplier'    => 'SUMBER SARI MANIS',
                'asal_bahan'  => 'TULUNGAGUNG',
            ],
            [
                'jenis_bahan' => 'GULA TEBU',
                'supplier'    => 'SUMBER BERKAH AGUNG CV',
                'asal_bahan'  => 'TULUNGAGUNG',
            ],
            [
                'jenis_bahan' => 'GULA TEBU',
                'supplier'    => 'PT CAHAYA MAKMUR SUMARDI',
                'asal_bahan'  => 'KUDUS',
            ],
            [
                'jenis_bahan' => 'GULA TEBU',
                'supplier'    => 'PERTIWI JAYA, CV',
                'asal_bahan'  => 'TULUNGAGUNG',
            ],
            [
                'jenis_bahan' => 'GULA KELAPA B',
                'supplier'    => 'CAKRAWALA PASIR GANCLENG',
                'asal_bahan'  => 'PANGANDARAN',
            ],
            [
                'jenis_bahan' => 'GULA KELAPA',
                'supplier'    => 'SLAMET SUMBER SEJAHTERA, PT',
                'asal_bahan'  => 'PURWOKERTO',
            ],
            [
                'jenis_bahan' => 'GULA KELAPA',
                'supplier'    => 'PT KARUNIA TIRTO MULYO',
                'asal_bahan'  => 'PURWOKERTO',
            ],
            [
                'jenis_bahan' => 'GULA KELAPA',
                'supplier'    => 'LANGSUNG SUGIH BERKAH',
                'asal_bahan'  => 'PURWOKERTO',
            ],
        ];

        foreach ($supplierAsalList as $item) {
            $jenisBahanId = $jenisBahanMap[$item['jenis_bahan']] ?? null;
            if ($jenisBahanId) {
                $supplier = MasterSupplierRm::firstOrCreate(
                    [
                        'jenis_bahan_id' => $jenisBahanId,
                        'nama_supplier'  => $item['supplier'],
                    ],
                    [
                        'status' => true,
                    ]
                );

                MasterAsalBahan::firstOrCreate(
                    [
                        'jenis_bahan_id' => $jenisBahanId,
                        'supplier_rm_id' => $supplier->id,
                        'asal_bahan'     => $item['asal_bahan'],
                    ],
                    [
                        'status' => true,
                    ]
                );
            }
        }

        // 4. Glassware
        // BEAKER 250G: 1 sd 90 (berat sekitar 130 - 135 gram)
        for ($i = 1; $i <= 90; $i++) {
            $defaultBerat = 130.0000 + round($i * 0.05, 4);
            MasterGlassware::updateOrCreate(
                [
                    'jenis_glassware' => 'BEAKER 250G',
                    'nomor_glassware' => (string) $i,
                ],
                [
                    'berat_glassware' => $defaultBerat,
                    'uom_berat'       => 'g',
                    'status'          => true,
                ]
            );
        }

        // BEAKER 500G: 1 sd 90 (berat sekitar 210 - 215 gram)
        for ($i = 1; $i <= 90; $i++) {
            $defaultBerat = 210.0000 + round($i * 0.05, 4);
            MasterGlassware::updateOrCreate(
                [
                    'jenis_glassware' => 'BEAKER 500G',
                    'nomor_glassware' => (string) $i,
                ],
                [
                    'berat_glassware' => $defaultBerat,
                    'uom_berat'       => 'g',
                    'status'          => true,
                ]
            );
        }

        // CAWAN: 1 sd 240 (berat sekitar 45 - 50 gram)
        for ($i = 1; $i <= 240; $i++) {
            $defaultBerat = 45.0000 + round($i * 0.02, 4);
            MasterGlassware::updateOrCreate(
                [
                    'jenis_glassware' => 'CAWAN',
                    'nomor_glassware' => (string) $i,
                ],
                [
                    'berat_glassware' => $defaultBerat,
                    'uom_berat'       => 'g',
                    'status'          => true,
                ]
            );
        }

        // 5. Master Standar Mutu RM
        $standarList = [
            // GULA KELAPA
            ['jenis' => 'GULA KELAPA', 'parameter' => 'pH', 'min' => 5.5, 'max' => 6.5, 'target' => '5.5 - 6.5', 'uom' => ''],
            ['jenis' => 'GULA KELAPA', 'parameter' => 'Brix', 'min' => 67.0, 'max' => null, 'target' => 'Min 67 °Bx', 'uom' => '°Bx'],
            ['jenis' => 'GULA KELAPA', 'parameter' => '%Kadar Air', 'min' => null, 'max' => 8.0, 'target' => 'Maks 8.0%', 'uom' => '%'],
            ['jenis' => 'GULA KELAPA', 'parameter' => '%Kotoran', 'min' => null, 'max' => 10.0, 'target' => 'Maks 10.0%', 'uom' => '%'],
            ['jenis' => 'GULA KELAPA', 'parameter' => 'Organo', 'min' => null, 'max' => null, 'target' => 'OK >= 70%', 'uom' => '%'],
            ['jenis' => 'GULA KELAPA', 'parameter' => 'Warna', 'min' => null, 'max' => null, 'target' => 'Coklat tua / Coklat muda / Coklat', 'uom' => ''],
            ['jenis' => 'GULA KELAPA', 'parameter' => 'Aroma', 'min' => null, 'max' => null, 'target' => 'OK / Khas', 'uom' => ''],

            // GULA KELAPA B
            ['jenis' => 'GULA KELAPA B', 'parameter' => 'pH', 'min' => 5.5, 'max' => 6.5, 'target' => '5.5 - 6.5', 'uom' => ''],
            ['jenis' => 'GULA KELAPA B', 'parameter' => 'Brix', 'min' => 67.0, 'max' => null, 'target' => 'Min 67 °Bx', 'uom' => '°Bx'],
            ['jenis' => 'GULA KELAPA B', 'parameter' => '%Kadar Air', 'min' => null, 'max' => 8.0, 'target' => 'Maks 8.0%', 'uom' => '%'],
            ['jenis' => 'GULA KELAPA B', 'parameter' => '%Kotoran', 'min' => null, 'max' => 10.0, 'target' => 'Maks 10.0%', 'uom' => '%'],
            ['jenis' => 'GULA KELAPA B', 'parameter' => 'Organo', 'min' => null, 'max' => null, 'target' => 'OK >= 70%', 'uom' => '%'],
            ['jenis' => 'GULA KELAPA B', 'parameter' => 'Warna', 'min' => null, 'max' => null, 'target' => 'Coklat tua / Coklat muda / Coklat', 'uom' => ''],
            ['jenis' => 'GULA KELAPA B', 'parameter' => 'Aroma', 'min' => null, 'max' => null, 'target' => 'OK / Khas', 'uom' => ''],

            // GULA TEBU
            ['jenis' => 'GULA TEBU', 'parameter' => 'pH', 'min' => 5.5, 'max' => 6.5, 'target' => '5.5 - 6.5', 'uom' => ''],
            ['jenis' => 'GULA TEBU', 'parameter' => 'Brix', 'min' => 65.0, 'max' => null, 'target' => 'Min 65 °Bx', 'uom' => '°Bx'],
            ['jenis' => 'GULA TEBU', 'parameter' => '%Kadar Air', 'min' => null, 'max' => 3.0, 'target' => 'Maks 3.0%', 'uom' => '%'],
            ['jenis' => 'GULA TEBU', 'parameter' => '%Kotoran', 'min' => null, 'max' => 10.0, 'target' => 'Maks 10.0%', 'uom' => '%'],
            ['jenis' => 'GULA TEBU', 'parameter' => 'Organo', 'min' => null, 'max' => null, 'target' => 'OK >= 70%', 'uom' => '%'],
            ['jenis' => 'GULA TEBU', 'parameter' => 'Warna', 'min' => null, 'max' => null, 'target' => 'Gelap / Sesuai Standar', 'uom' => ''],
            ['jenis' => 'GULA TEBU', 'parameter' => 'Aroma', 'min' => null, 'max' => null, 'target' => 'OK / Khas', 'uom' => ''],

            // GARAM
            ['jenis' => 'GARAM', 'parameter' => '%KA', 'min' => null, 'max' => 4.0, 'target' => 'Maks 4.0%', 'uom' => '%'],
            ['jenis' => 'GARAM', 'parameter' => 'Kotoran', 'min' => null, 'max' => 0.5, 'target' => 'Maks 0.5%', 'uom' => '%'],
            ['jenis' => 'GARAM', 'parameter' => '%NaCl', 'min' => 97.0, 'max' => null, 'target' => 'Min 97.0%', 'uom' => '%'],
            ['jenis' => 'GARAM', 'parameter' => 'Gross Weight', 'min' => null, 'max' => null, 'target' => 'Sesuai Standar', 'uom' => 'kg'],
            ['jenis' => 'GARAM', 'parameter' => 'Fisik', 'min' => null, 'max' => null, 'target' => 'Bersih & Kering', 'uom' => ''],
            ['jenis' => 'GARAM', 'parameter' => 'Organo', 'min' => null, 'max' => null, 'target' => 'OK', 'uom' => ''],
            ['jenis' => 'GARAM', 'parameter' => 'Warna', 'min' => null, 'max' => null, 'target' => 'Putih', 'uom' => ''],
            ['jenis' => 'GARAM', 'parameter' => 'Aroma', 'min' => null, 'max' => null, 'target' => 'OK / Khas', 'uom' => ''],
        ];

        foreach ($standarList as $std) {
            $jbId = $jenisBahanMap[$std['jenis']] ?? null;
            if ($jbId) {
                MasterStandarRm::updateOrCreate(
                    [
                        'id_jenis_bahan' => $jbId,
                        'parameter'      => $std['parameter'],
                    ],
                    [
                        'min_standar'    => $std['min'],
                        'max_standar'    => $std['max'],
                        'target_text'    => $std['target'],
                        'uom'            => $std['uom'],
                        'status'         => true,
                    ]
                );
            }
        }
    }
}
