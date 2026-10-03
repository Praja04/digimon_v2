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
        // BEAKER 250G: 1 sd 90 (berat kalibrasi aktual dari laboratorium)
        $beaker250Weights = [
            1 => 128.30, 2 => 128.90, 3 => 127.45, 4 => 125.10, 5 => 125.86,
            6 => 126.76, 7 => 128.31, 8 => 129.78, 9 => 132.94, 10 => 128.67,
            11 => 128.39, 12 => 128.12, 13 => 127.91, 14 => 128.56, 15 => 127.12,
            16 => 130.11, 17 => 131.31, 18 => 127.51, 19 => 128.32, 20 => 126.60,
            21 => 129.13, 22 => 128.99, 23 => 129.10, 24 => 129.25, 25 => 127.55,
            26 => 130.08, 27 => 130.02, 28 => 126.37, 29 => 130.19, 30 => 126.21,
            31 => 125.90, 32 => 127.42, 33 => 128.41, 34 => 126.47, 35 => 129.74,
            36 => 127.66, 37 => 130.75, 38 => 128.77, 39 => 130.21, 40 => 126.97,
            41 => 129.15, 42 => 130.31, 43 => 129.03, 44 => 130.67, 45 => 126.84,
            46 => 127.21, 47 => 129.86, 48 => 129.33, 49 => 125.68, 50 => 128.94,
            51 => 124.79, 52 => 127.60, 53 => 128.92, 54 => 129.73, 55 => 129.02,
            56 => 124.74, 57 => 126.06, 58 => 130.36, 59 => 127.76, 60 => 128.39,
            61 => 128.54, 62 => 128.11, 63 => 129.55, 64 => 128.60, 65 => 128.22,
            66 => 129.01, 67 => 128.70, 68 => 128.81, 69 => 128.14, 70 => 126.09,
            71 => 127.83, 72 => 128.09, 73 => 128.69, 74 => 128.72, 75 => 126.87,
            76 => 129.35, 77 => 127.13, 78 => 130.44, 79 => 129.45, 80 => 127.51,
            81 => 128.39, 82 => 127.62, 83 => 128.78, 84 => 131.42, 85 => 128.39,
            86 => 130.51, 87 => 129.19, 88 => 129.77, 89 => 129.94, 90 => 130.74,
        ];

        for ($i = 1; $i <= 90; $i++) {
            $defaultBerat = $beaker250Weights[$i] ?? (130.0000 + round($i * 0.05, 4));
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

        // BEAKER 500G: 1 sd 90 (berat kalibrasi aktual dari laboratorium)
        $beaker500Weights = [
            1 => 228.38, 2 => 233.24, 3 => 231.93, 4 => 231.70, 5 => 234.24,
            6 => 235.41, 7 => 227.59, 8 => 234.47, 9 => 236.54, 10 => 229.31,
            11 => 235.97, 12 => 231.90, 13 => 226.71, 14 => 230.49, 15 => 239.27,
            16 => 233.98, 17 => 228.31, 18 => 229.54, 19 => 231.96, 20 => 232.40,
            21 => 242.74, 22 => 234.04, 23 => 231.23, 24 => 238.38, 25 => 230.73,
            26 => 236.85, 27 => 232.20, 28 => 233.66, 29 => 230.67, 30 => 230.85,
            31 => 229.45, 32 => 233.16, 33 => 229.42, 34 => 226.53, 35 => 233.17,
            36 => 236.59, 37 => 232.38, 38 => 228.83, 39 => 228.62, 40 => 229.40,
            41 => 230.12, 42 => 228.98, 43 => 231.00, 44 => 232.42, 45 => 231.82,
            46 => 238.43, 47 => 229.61, 48 => 240.39, 49 => 230.49, 50 => 244.98,
            51 => 247.88, 52 => 229.00, 53 => 232.13, 54 => 230.79, 55 => 227.46,
            56 => 247.12, 57 => 230.01, 58 => 231.20, 59 => 225.61, 60 => 232.65,
            61 => 244.69, 62 => 227.96, 63 => 243.31, 64 => 244.18, 65 => 233.14,
            66 => 234.07, 67 => 231.91, 68 => 233.73, 69 => 249.91, 70 => 232.74,
            71 => 231.77, 72 => 233.10, 73 => 237.27, 74 => 236.97, 75 => 236.10,
            76 => 228.80, 77 => 232.98, 78 => 227.35, 79 => 224.52, 80 => 232.52,
            81 => 228.16, 82 => 232.38, 83 => 233.26, 84 => 234.85, 85 => 223.26,
            86 => 232.31, 87 => 231.85, 88 => 230.20, 89 => 235.30, 90 => 229.98,
        ];

        for ($i = 1; $i <= 90; $i++) {
            $defaultBerat = $beaker500Weights[$i] ?? (210.0000 + round($i * 0.05, 4));
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

        // CAWAN: 1 sd 240 (berat kalibrasi aktual dari laboratorium)
        $cawanWeights = [
            1 => 73.67, 2 => 68.34, 3 => 67.49, 4 => 71.79, 5 => 82.51, 6 => 75.83, 7 => 74.23, 8 => 76.63, 9 => 73.00, 10 => 73.28,
            11 => 73.06, 12 => 68.05, 13 => 68.77, 14 => 69.90, 15 => 86.21, 16 => 76.92, 17 => 82.80, 18 => 77.25, 19 => 71.83, 20 => 86.23,
            21 => 76.70, 22 => 73.71, 23 => 86.72, 24 => 86.19, 25 => 84.28, 26 => 86.64, 27 => 87.24, 28 => 85.94, 29 => 80.18, 30 => 87.96,
            31 => 84.42, 32 => 82.66, 33 => 87.15, 34 => 86.95, 35 => 90.05, 36 => 83.94, 37 => 85.10, 38 => 85.01, 39 => 85.08, 40 => 89.27,
            41 => 87.33, 42 => 87.60, 43 => 84.20, 44 => 84.48, 45 => 83.00, 46 => 86.34, 47 => 87.61, 48 => 84.01, 49 => 85.10, 50 => 86.44,
            51 => 90.28, 52 => 79.04, 53 => 77.68, 54 => 88.47, 55 => 74.07, 56 => 87.91, 57 => 86.65, 58 => 81.68, 59 => 73.65, 60 => 84.65,
            61 => 82.79, 62 => 86.43, 63 => 88.68, 64 => 88.41, 65 => 88.92, 66 => 71.95, 67 => 90.94, 68 => 84.25, 69 => 88.39, 70 => 84.54,
            71 => 83.15, 72 => 87.47, 73 => 87.00, 74 => 85.13, 75 => 85.68, 76 => 86.31, 77 => 87.67, 78 => 82.84, 79 => 83.44, 80 => 87.13,
            81 => 88.05, 82 => 86.44, 83 => 88.26, 84 => 84.89, 85 => 83.63, 86 => 87.97, 87 => 90.46, 88 => 82.75, 89 => 83.64, 90 => 86.08,
            91 => 87.61, 92 => 86.02, 93 => 88.01, 94 => 70.53, 95 => 86.41, 96 => 84.16, 97 => 80.28, 98 => 86.52, 99 => 85.27, 100 => 85.52,
            101 => 88.00, 102 => 86.23, 103 => 85.85, 104 => 85.18, 105 => 79.65, 106 => 84.45, 107 => 82.76, 108 => 86.36, 109 => 88.47, 110 => 81.84,
            111 => 88.14, 112 => 88.80, 113 => 85.59, 114 => 84.07, 115 => 84.97, 116 => 85.85, 117 => 83.92, 118 => 84.16, 119 => 87.17, 120 => 88.18,
            121 => 88.21, 122 => 86.45, 123 => 81.58, 124 => 85.25, 125 => 86.45, 126 => 86.65, 127 => 88.00, 128 => 88.49, 129 => 86.80, 130 => 84.19,
            131 => 86.62, 132 => 84.29, 133 => 83.65, 134 => 84.77, 135 => 82.53, 136 => 97.21, 137 => 89.97, 138 => 92.65, 139 => 87.51, 140 => 88.14,
            141 => 87.89, 142 => 88.02, 143 => 87.03, 144 => 85.00, 145 => 90.46, 146 => 88.15, 147 => 87.44, 148 => 88.05, 149 => 88.72, 150 => 90.57,
            151 => 89.05, 152 => 84.58, 153 => 85.00, 154 => 85.98, 155 => 84.58, 156 => 70.98, 157 => 87.60, 158 => 87.20, 159 => 83.85, 160 => 83.94,
            161 => 84.41, 162 => 86.93, 163 => 84.87, 164 => 86.32, 165 => 86.86, 166 => 88.83, 167 => 85.22, 168 => 86.38, 169 => 80.37, 170 => 87.75,
            171 => 88.76, 172 => 86.42, 173 => 86.78, 174 => 86.27, 175 => 83.50, 176 => 85.09, 177 => 84.90, 178 => 85.14, 179 => 85.90, 180 => 86.04,
            181 => 86.28, 182 => 86.84, 183 => 84.38, 184 => 84.44, 185 => 88.59, 186 => 81.93, 187 => 84.81, 188 => 84.81, 189 => 83.00, 190 => 86.99,
            191 => 87.43, 192 => 82.74, 193 => 87.11, 194 => 89.90, 195 => 91.53, 196 => 87.69, 197 => 84.81, 198 => 83.74, 199 => 86.53, 200 => 86.51,
            201 => 87.37, 202 => 87.77, 203 => 88.77, 204 => 85.87, 205 => 86.39, 206 => 84.11, 207 => 78.72, 208 => 85.58, 209 => 83.68, 210 => 86.32,
            211 => 87.62, 212 => 87.27, 213 => 89.29, 214 => 90.42, 215 => 86.23, 216 => 87.47, 217 => 87.17, 218 => 89.37, 219 => 87.51, 220 => 84.39,
            221 => 86.53, 222 => 86.67, 223 => 87.51, 224 => 87.85, 225 => 86.39, 226 => 88.77, 227 => 88.49, 228 => 86.95, 229 => 86.52, 230 => 86.43,
            231 => 87.56, 232 => 87.84, 233 => 87.65, 234 => 87.14, 235 => 84.62, 236 => 83.53, 237 => 84.15, 238 => 82.05, 239 => 83.62, 240 => 85.21,
        ];

        for ($i = 1; $i <= 240; $i++) {
            $defaultBerat = $cawanWeights[$i] ?? (45.0000 + round($i * 0.02, 4));
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
