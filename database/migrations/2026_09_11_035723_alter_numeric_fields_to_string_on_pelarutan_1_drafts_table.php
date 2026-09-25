<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelarutan_1_drafts', function (Blueprint $table) {
            $table->string('brix')->nullable()->change();
            $table->string('nacl')->nullable()->change();
            $table->string('adjustment_qty_gula_tebu')->nullable()->change();
            $table->string('adjustment_qty_gula_kelapa')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pelarutan_1_drafts', function (Blueprint $table) {
            $table->decimal('brix', 10, 4)->nullable()->change();
            $table->decimal('nacl', 10, 4)->nullable()->change();
            $table->decimal('adjustment_qty_gula_tebu', 12, 4)->nullable()->change();
            $table->decimal('adjustment_qty_gula_kelapa', 12, 4)->nullable()->change();
        });
    }
};