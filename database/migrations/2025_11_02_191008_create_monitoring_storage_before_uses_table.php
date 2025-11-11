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
        Schema::create('monitoring_storage_before_uses', function (Blueprint $table) {
            $table->id();
            $table->string('storage')->nullable();
            $table->string('variant')->nullable();
            $table->enum('jenis_sample', ['Before Tiban', 'Flushing', 'Level 0'])->nullable();
            $table->string('tahap_flushing')->nullable();
            $table->dateTime('waktu_selesai_pemakaian')->nullable();
            $table->dateTime('estimasi_kadaluarsa')->nullable();
            $table->float('visco')->nullable();
            $table->float('brix')->nullable();
            $table->float('aw')->nullable();
            $table->string('hasil')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_storage_before_uses');
    }
};
