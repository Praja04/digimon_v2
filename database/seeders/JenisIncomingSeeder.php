<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisIncomingSeeder extends Seeder
{
    public function run(): void
    {
        $jenisIncomings = [
            'Inner / Outer',
            'Kardus',
            'Outers',
        ];

        foreach ($jenisIncomings as $nama) {
            DB::table('jenis_incomings')->updateOrInsert(
                [
                    'kategori' => 'PM',
                    'nama' => $nama,
                ],
                [
                    'status' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}