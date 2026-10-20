<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE monitoring_storage_kimia MODIFY COLUMN disposition ENUM('Release', 'Release Bersyarat', 'Resampling', 'Reject', 'Adjustment', 'Repro', 'Leveling', 'Jalan Bareng', 'Drain') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE monitoring_storage_kimia MODIFY COLUMN disposition ENUM('Release', 'Release Bersyarat', 'Resampling', 'Reject', 'Adjustment', 'Repro', 'Leveling', 'Jalan Bareng') NULL");
    }
};
