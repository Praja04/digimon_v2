<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inner_outers', function (Blueprint $table) {

            $table->id();

            $table->string('kode')->unique();

            $table->enum('jenis', [
                'inner',
                'outer'
            ]);

            $table->string('nama_material');

            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('inner_outers');
    }
};