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
        Schema::create('identitas_rm', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan')->nullable();
            $table->dateTime('tanggal_kedatangan')->nullable();
            $table->string('supplier')->nullable();
            $table->string('asal_bahan')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('no_spb')->nullable();
            $table->string('jumlah_kedatangan')->nullable();
            $table->string('lot_batch')->nullable();
            $table->string('jenis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identitas_rm');
    }
};
