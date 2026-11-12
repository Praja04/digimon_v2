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
        Schema::table('timbangan_retail_mesin', function (Blueprint $table) {
            $table->boolean('resampling')->default(false)->after('filler');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timbangan_retail_mesin', function (Blueprint $table) {
            $table->dropColumn('resampling');
        });
    }
};

