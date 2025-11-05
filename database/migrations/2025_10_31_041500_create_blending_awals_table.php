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
        Schema::create('blending_awal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->string('batch_range')->nullable();
            $table->integer('nomor_blending')->nullable();
            $table->float('volume')->nullable();
            $table->float('brix')->nullable();
            $table->float('nacl')->nullable();
            $table->float('bj')->nullable();
            $table->float('visco')->nullable();
            $table->float('aw')->nullable();
            $table->float('buih')->nullable();
            $table->float('ph')->nullable();
            $table->string('organo')->nullable();
            $table->string('endapan')->nullable();
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('restrict');
            $table->enum('disposition', ['Release', 'Release Bersyarat', 'Resampling', 'Reject', 'Adjustment', 'Repro', 'Leveling', 'Jalan Bareng'])->nullable();
            $table->text('disposition_remark')->nullable();
            $table->string('revisi', 50)->nullable();
            $table->float('adjustment_qty_air')->nullable();
            $table->float('adjustment_qty_garam')->nullable();
            $table->float('adjustment_qty_gula')->nullable();
            $table->tinyInteger('is_adjustment')->default(0);
            $table->string('storage', 100)->nullable();
            $table->tinyInteger('not_standard')->default(0);
            $table->enum('status', ['OK', 'NOT OK', 'Adjustment'])->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blending_awal');
    }
};
