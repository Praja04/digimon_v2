<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inner_outers')) {
            return;
        }

        Schema::create('inner_outers', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 255)->unique();
            $table->enum('jenis', ['inner', 'outer']);
            $table->string('nama_material', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inner_outers');
    }
};