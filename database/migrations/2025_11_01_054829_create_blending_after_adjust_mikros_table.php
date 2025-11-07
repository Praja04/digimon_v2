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
        Schema::create('blending_after_adjust_mikro', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->string('batch_range')->nullable();
            $table->integer('nomor_blending')->nullable();
            $table->float('volume')->nullable();
            $table->float('eb')->nullable();
            $table->float('tpc')->nullable();
            $table->float('ym')->nullable();
            $table->string('hasil', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blending_after_adjust_mikro');
    }
};
