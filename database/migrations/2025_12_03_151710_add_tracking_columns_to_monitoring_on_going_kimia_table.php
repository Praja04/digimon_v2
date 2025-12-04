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
        Schema::table('monitoring_on_going_kimia', function (Blueprint $table) {
            $table->timestamp('scanned_at')->nullable()->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_on_going_kimia', function (Blueprint $table) {
            $table->dropColumn('scanned_at');
        });
    }
};
