<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incoming;

class IncomingSeeder extends Seeder
{
    public function run(): void
    {
        Incoming::insert([
            ['jenis_incoming' => 'Inner / Outer'],
            ['jenis_incoming' => 'Kardus'],
            ['jenis_incoming' => 'Outers'],
        ]);
    }
}