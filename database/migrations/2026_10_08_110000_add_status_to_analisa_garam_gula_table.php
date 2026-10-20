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
        if (Schema::hasTable('analisa_garam_gula')) {
            Schema::table('analisa_garam_gula', function (Blueprint $table) {
                if (!Schema::hasColumn('analisa_garam_gula', 'status')) {
                    $table->string('status', 20)->default('final')->after('disposisi');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('analisa_garam_gula')) {
            Schema::table('analisa_garam_gula', function (Blueprint $table) {
                if (Schema::hasColumn('analisa_garam_gula', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
