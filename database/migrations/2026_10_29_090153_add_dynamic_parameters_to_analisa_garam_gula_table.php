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
                if (!Schema::hasColumn('analisa_garam_gula', 'ph')) {
                    $table->float('ph')->nullable()->after('gross_weight');
                }
                if (!Schema::hasColumn('analisa_garam_gula', 'brix')) {
                    $table->float('brix')->nullable()->after('ph');
                }
                if (!Schema::hasColumn('analisa_garam_gula', 'berat_jenis')) {
                    $table->float('berat_jenis')->nullable()->after('brix');
                }
                if (!Schema::hasColumn('analisa_garam_gula', 'suhu')) {
                    $table->float('suhu')->nullable()->after('berat_jenis');
                }
                if (!Schema::hasColumn('analisa_garam_gula', 'aw')) {
                    $table->float('aw')->nullable()->after('suhu');
                }
                if (!Schema::hasColumn('analisa_garam_gula', 'viskositas')) {
                    $table->float('viskositas')->nullable()->after('aw');
                }
                if (!Schema::hasColumn('analisa_garam_gula', 'tn')) {
                    $table->float('tn')->nullable()->after('viskositas');
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
                $columns = ['ph', 'brix', 'berat_jenis', 'suhu', 'aw', 'viskositas', 'tn'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('analisa_garam_gula', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
