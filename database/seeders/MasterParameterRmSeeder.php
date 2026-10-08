<?php

namespace Database\Seeders;

use App\Models\MasterParameterRm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterParameterRmSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_parameter_rms')->truncate();

        // 1. Warna RM
        $warnaList = [
            'Coklat',
            'Coklat tua',
            'Coklat muda',
            'Gelap',
        ];

        foreach ($warnaList as $w) {
            MasterParameterRm::create([
                'jenis_bahan_id' => null,
                'kategori'       => 'warna',
                'nama_pilihan'   => $w,
                'is_custom'      => false,
                'urutan'         => 0,
                'status'         => true,
            ]);
        }

        // 2. Aroma RM
        $aromaList = [
            'OK',
            'Kurang',
            'Tidak Ada',
            'Tidak Sesuai',
        ];

        foreach ($aromaList as $a) {
            MasterParameterRm::create([
                'jenis_bahan_id' => null,
                'kategori'       => 'aroma',
                'nama_pilihan'   => $a,
                'is_custom'      => false,
                'urutan'         => 0,
                'status'         => true,
            ]);
        }

        // 3. Organo (Rasa) RM
        $organoList = [
            ['nama' => 'OK', 'is_custom' => false],
            ['nama' => 'Agak asam', 'is_custom' => false],
            ['nama' => 'Asam', 'is_custom' => false],
            ['nama' => 'Agak pahit', 'is_custom' => false],
            ['nama' => 'Pahit', 'is_custom' => false],
            ['nama' => 'Sagu', 'is_custom' => false],
            ['nama' => 'Kapur', 'is_custom' => false],
            ['nama' => 'Lain-lain', 'is_custom' => true],
            ['nama' => 'Campuran', 'is_custom' => true],
        ];

        foreach ($organoList as $o) {
            MasterParameterRm::create([
                'jenis_bahan_id' => null,
                'kategori'       => 'organo',
                'nama_pilihan'   => $o['nama'],
                'is_custom'      => $o['is_custom'],
                'urutan'         => 0,
                'status'         => true,
            ]);
        }
    }
}
