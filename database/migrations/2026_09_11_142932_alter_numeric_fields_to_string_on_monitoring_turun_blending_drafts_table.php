<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_turun_blending_drafts', function (Blueprint $table) {
            $table->string('brix')->nullable()->change();
            $table->string('visco')->nullable()->change();
            $table->string('aw')->nullable()->change();

            $table->string('adjustment_qty_air')->nullable()->change();
            $table->string('adjustment_qty_gula')->nullable()->change();
            $table->string('adjustment_qty_garam')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_turun_blending_drafts', function (Blueprint $table) {
            $table->decimal('brix', 12, 4)->nullable()->change();
            $table->decimal('visco', 12, 4)->nullable()->change();
            $table->decimal('aw', 12, 4)->nullable()->change();

            $table->decimal('adjustment_qty_air', 12, 4)->nullable()->change();
            $table->decimal('adjustment_qty_gula', 12, 4)->nullable()->change();
            $table->decimal('adjustment_qty_garam', 12, 4)->nullable()->change();
        });
    }
};