<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Compatibility no-op.
        // Kolom status sudah menjadi bagian schema final jenis_incomings; sinkronisasi existing DB dilakukan migration 2026_08_14.
    }

    public function down(): void
    {
        // No-op.
    }
};