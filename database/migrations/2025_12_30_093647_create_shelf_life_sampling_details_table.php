<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shelf_life_sampling_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelf_life_sample_id')->constrained('shelf_life_samples')->onDelete('cascade');
            $table->string('variant_fg')->nullable();
            $table->string('kelompok_sample')->nullable();
            $table->string('kelompok_tanggal')->nullable();
            $table->string('koding')->nullable();
            $table->time('jam_koding')->nullable();
            $table->unsignedTinyInteger('bulan_ke')->nullable();
            $table->string('ruang_sl')->nullable();
            $table->string('bin_location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelf_life_sampling_detail');
    }
};
