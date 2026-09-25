<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Compatibility no-op.
        // status_proses sudah menjadi bagian schema final packaging_inner_outer_samplings.
    }

    public function down(): void
    {
        // No-op.
    }
};