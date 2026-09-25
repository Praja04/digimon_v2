<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Compatibility no-op.
        // hasil_thickness sudah menjadi bagian schema final packaging_pouch_samplings.
    }

    public function down(): void
    {
        // No-op.
    }
};