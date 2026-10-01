<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_standar_rms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_jenis_bahan');
            $table->string('parameter', 100);
            $table->decimal('min_standar', 10, 3)->nullable();
            $table->decimal('max_standar', 10, 3)->nullable();
            $table->string('target_text', 150)->nullable();
            $table->string('uom', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('id_jenis_bahan')
                ->references('id')
                ->on('master_jenis_bahans')
                ->onDelete('cascade');

            $table->index(['id_jenis_bahan', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_standar_rms');
    }
};
