<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Compatibility no-op.
        // foto_ketidaksesuaian sudah menjadi bagian schema final packaging_karton_samplings.
    }

    public function down(): void
    {
        // No-op.
    }
};