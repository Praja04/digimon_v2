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
            $table->renameColumn('adjustment_qty_gula', 'adjustment_qty_caramel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blending_awal', function (Blueprint $table) {
            $table->renameColumn('adjustment_qty_caramel', 'adjustment_qty_gula');
        });
    }
};
