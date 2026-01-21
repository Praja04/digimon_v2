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
        Schema::table('monitoring_pasteurisasi', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropColumn('color_id');
            $table->string('aroma')->nullable()->after('organo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_pasteurisasi', function (Blueprint $table) {
            $table->foreignId('color_id')
                ->nullable()
                ->constrained('colors')
                ->onDelete('restrict')
                ->after('endapan');
            $table->dropColumn('aroma');
        });
    }
};
