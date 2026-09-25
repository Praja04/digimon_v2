<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Compatibility no-op.
        // jumlah_sampel sudah menjadi bagian schema final packaging_incomings.
    }

    public function down(): void
    {
        // No-op.
    }
};