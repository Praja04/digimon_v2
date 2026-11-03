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
        if (Schema::hasTable('analisa_short_term')) {
            Schema::table('analisa_short_term', function (Blueprint $table) {
                if (!Schema::hasColumn('analisa_short_term', 'nacl')) {
                    $table->float('nacl')->nullable()->after('aroma');
                }
                if (!Schema::hasColumn('analisa_short_term', 'gross_weight')) {
                    $table->float('gross_weight')->nullable()->after('nacl');
                }
                if (!Schema::hasColumn('analisa_short_term', 'fisik')) {
                    $table->string('fisik')->nullable()->after('gross_weight');
                }
                if (!Schema::hasColumn('analisa_short_term', 'berat_jenis')) {
                    $table->float('berat_jenis')->nullable()->after('fisik');
                }
                if (!Schema::hasColumn('analisa_short_term', 'suhu')) {
                    $table->float('suhu')->nullable()->after('berat_jenis');
                }
                if (!Schema::hasColumn('analisa_short_term', 'aw')) {
                    $table->float('aw')->nullable()->after('suhu');
                }
                if (!Schema::hasColumn('analisa_short_term', 'viskositas')) {
                    $table->float('viskositas')->nullable()->after('aw');
                }
                if (!Schema::hasColumn('analisa_short_term', 'tn')) {
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
        if (Schema::hasTable('analisa_short_term')) {
            Schema::table('analisa_short_term', function (Blueprint $table) {
                $columns = ['nacl', 'gross_weight', 'fisik', 'berat_jenis', 'suhu', 'aw', 'viskositas', 'tn'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('analisa_short_term', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
