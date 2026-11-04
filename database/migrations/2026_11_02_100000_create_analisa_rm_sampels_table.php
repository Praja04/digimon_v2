<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel monolitik analisa_rm_sampels digantikan sepenuhnya oleh
     * skema normalisasi dinamis (analisa_rm_transaksi & analisa_rm_sample_details).
     */
    public function up(): void
    {
        // Biarkan data lama tetap ada, proses migrasi otomatis ke skema baru
        // dan backup tabel akan ditangani secara aman oleh migrasi berikutnya:
        // 2026_11_03_160000_create_dynamic_rm_analysis_tables.php
    }

    public function down(): void
    {
    }
};
