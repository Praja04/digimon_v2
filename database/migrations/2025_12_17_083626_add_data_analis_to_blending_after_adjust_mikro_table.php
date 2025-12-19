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
        Schema::table('blending_after_adjust_mikro', function (Blueprint $table) {
            $table->string('nama_analis')->nullable()->after('volume');
            $table->string('shift')->nullable()->after('nama_analis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blending_after_adjust_mikro', function (Blueprint $table) {
            $table->dropColumn(['nama_analis', 'shift']);
        });
    }
};
