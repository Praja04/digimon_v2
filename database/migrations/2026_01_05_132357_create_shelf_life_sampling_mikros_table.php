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
        Schema::create('shelf_life_sampling_mikro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelf_life_sampling_detail_id')->constrained('shelf_life_sampling_detail')->onDelete('cascade');
            $table->string('shift_analis')->nullable();
            $table->string('nama_analis')->nullable();
            $table->dateTime('waktu_analisa')->nullable();
            $table->float('eb')->nullable();
            $table->float('sa')->nullable();
            $table->float('tpc')->nullable();
            $table->float('ym')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelf_life_sampling_mikro');
    }
};
