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
        if (Schema::hasTable('monitoring_storage_kimia') && !Schema::hasColumn('monitoring_storage_kimia', 'jenis_sampel')) {
            Schema::table('monitoring_storage_kimia', function (Blueprint $table) {
                $table->string('jenis_sampel', 50)->nullable()->after('storage');
            });
        }

        if (Schema::hasTable('monitoring_storage_mikro') && !Schema::hasColumn('monitoring_storage_mikro', 'jenis_sampel')) {
            Schema::table('monitoring_storage_mikro', function (Blueprint $table) {
                $table->string('jenis_sampel', 50)->nullable()->after('storage');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('monitoring_storage_kimia') && Schema::hasColumn('monitoring_storage_kimia', 'jenis_sampel')) {
            Schema::table('monitoring_storage_kimia', function (Blueprint $table) {
                $table->dropColumn('jenis_sampel');
            });
        }

        if (Schema::hasTable('monitoring_storage_mikro') && Schema::hasColumn('monitoring_storage_mikro', 'jenis_sampel')) {
            Schema::table('monitoring_storage_mikro', function (Blueprint $table) {
                $table->dropColumn('jenis_sampel');
            });
        }
    }
};
