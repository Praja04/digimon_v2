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
        Schema::table('monitoring_turun_blending', function (Blueprint $table) {
            $table->dropColumn('bj');
            $table->dropColumn('ph');
            $table->dropColumn('buih');
            $table->dropColumn('endapan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_turun_blending', function (Blueprint $table) {
            $table->float('bj')->nullable();
            $table->float('ph')->nullable();
            $table->float('buih')->nullable();
            $table->float('endapan')->nullable();
        });
    }
};
