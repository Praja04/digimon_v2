<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Compatibility no-op.
        // Input quantity_incoming disimpan ke kolom packaging_incomings.jumlah. Tidak membuat kolom duplikat.
    }

    public function down(): void
    {
        // No-op.
    }
};