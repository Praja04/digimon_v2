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
            $table->dropColumn('nacl');
            $table->dropColumn('organo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_turun_blending', function (Blueprint $table) {
            $table->float('nacl')->nullable()->after('brix');
            $table->string('organo')->nullable()->after('aw');
        });
    }
};
