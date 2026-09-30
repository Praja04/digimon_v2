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
        if (!Schema::hasTable('master_supplier_rms')) {
            Schema::create('master_supplier_rms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('jenis_bahan_id')->constrained('master_jenis_bahans')->cascadeOnDelete();
                $table->string('nama_supplier');
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_supplier_rms');
    }
};
