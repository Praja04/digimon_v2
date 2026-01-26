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
        Schema::table('press_test_data', function (Blueprint $table) {
            $table->renameColumn('nama_analis', 'nama_analis_field');
            $table->renameColumn('mesin', 'mesin_press_test');
            $table->string('mesin_retail')->nullable()->after('mesin_press_test');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('press_test_data', function (Blueprint $table) {
            $table->renameColumn('mesin_press_test', 'mesin');
            $table->dropColumn('mesin_retail');
        });
    }
};
