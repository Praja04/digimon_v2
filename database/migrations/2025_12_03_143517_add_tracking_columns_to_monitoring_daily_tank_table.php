<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monitoring_daily_tank', function (Blueprint $table) {
            $table->timestamp('scanned_at')->nullable()->after('alasan_disposisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_daily_tank', function (Blueprint $table) {
            $table->dropColumn('scanned_at');
        });
    }
};
