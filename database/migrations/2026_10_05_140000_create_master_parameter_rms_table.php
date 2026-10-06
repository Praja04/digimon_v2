<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_parameter_rms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_bahan_id')->nullable()->constrained('master_jenis_bahans')->nullOnDelete();
            $table->string('kategori', 50); // 'warna', 'aroma', 'organo'
            $table->string('nama_pilihan', 150);
            $table->boolean('is_custom')->default(false);
            $table->integer('urutan')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['kategori', 'status']);
            $table->index(['jenis_bahan_id', 'kategori']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_parameter_rms');
    }
};
