<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Compatibility no-op.
        // Field Inner/Outer terkait foto dan ketidaksesuaian sudah JSON pada schema final.
    }

    public function down(): void
    {
        // No-op.
    }
};