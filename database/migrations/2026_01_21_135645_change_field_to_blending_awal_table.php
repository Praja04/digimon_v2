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
        Schema::table('blending_awal', function (Blueprint $table) {
            $table->dropColumn('buih');
            $table->dropColumn('endapan');
            $table->string('aroma')->nullable()->after('organo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blending_awal', function (Blueprint $table) {
            $table->string('buih')->nullable();
            $table->string('endapan')->nullable();
            $table->dropColumn('aroma');
        });
    }
};
