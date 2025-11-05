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
        Schema::create('konfirmasi_kedatangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_identitas')->constrained('identitas_rm')->onDelete('cascade');
            $table->dateTime('waktu_kedatangan')->nullable();
            $table->dateTime('waktu_analisa')->nullable();
            $table->foreignId('diterima_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('dianalisa_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfirmasi_kedatangan');
    }
};
