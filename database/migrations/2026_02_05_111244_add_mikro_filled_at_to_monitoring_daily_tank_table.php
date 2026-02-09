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
            $table->dateTime('mikro_filled_at')->nullable()->after('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_daily_tank', function (Blueprint $table) {
            $table->dropColumn('mikro_filled_at');
        });
    }
};
