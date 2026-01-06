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
        Schema::create('shelf_life_sampling_kimia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shelf_life_sampling_detail_id')->constrained('shelf_life_sampling_detail')->onDelete('cascade');
            $table->string('shift_analis')->nullable();
            $table->string('nama_analis')->nullable();
            $table->dateTime('waktu_analisa')->nullable();
            $table->float('nacl')->nullable();
            $table->float('brix')->nullable();
            $table->float('aw')->nullable();
            $table->float('ph')->nullable();
            $table->float('bj')->nullable();
            $table->float('buih')->nullable();
            $table->string('aroma')->nullable();
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('restrict');
            $table->string('organo')->nullable();
            $table->float('visco')->nullable();
            $table->string('total_nitrogen')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shelf_life_sampling_kimia');
    }
};
