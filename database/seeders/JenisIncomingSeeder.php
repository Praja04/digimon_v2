<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisIncomingSeeder extends Seeder
{
    public function run(): void
    {
        // kosongkan dulu biar gak duplicate
        DB::table('jenis_incomings')->truncate();

        DB::table('jenis_incomings')->insert([
            [
                'nama' => 'Inner / Outer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kardus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Outers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}