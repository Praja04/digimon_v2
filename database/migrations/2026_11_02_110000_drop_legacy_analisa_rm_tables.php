<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membersihkan tabel analisa lama yang sudah digantikan oleh analisa_rm_sampels.
     */
    public function up(): void
    {
        Schema::dropIfExists('analisa_bahan_cair');
        Schema::dropIfExists('analisa_bahan_kering');
        Schema::dropIfExists('analisa_garam_gula');
        Schema::dropIfExists('analisa_short_term');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tabel tidak di-restore otomatis karena seluruh data tersimpan permanen di analisa_rm_sampels
    }
};

