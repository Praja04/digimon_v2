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
        Schema::table('analisa_short_term', function (Blueprint $table) {
            if (!Schema::hasColumn('analisa_short_term', 'no_beaker')) {
                $table->string('no_beaker')->nullable()->after('ph');
            }
            if (!Schema::hasColumn('analisa_short_term', 'berat_beaker_500')) {
                $table->decimal('berat_beaker_500', 10, 4)->nullable()->after('no_beaker');
            }
            if (!Schema::hasColumn('analisa_short_term', 'berat_beaker_250')) {
                $table->decimal('berat_beaker_250', 10, 4)->nullable()->after('berat_beaker_500');
            }
            if (!Schema::hasColumn('analisa_short_term', 'timbang_a')) {
                $table->decimal('timbang_a', 10, 4)->nullable()->after('berat_beaker_250');
            }
            if (!Schema::hasColumn('analisa_short_term', 'timbang_b')) {
                $table->decimal('timbang_b', 10, 4)->nullable()->after('timbang_a');
            }
            if (!Schema::hasColumn('analisa_short_term', 'rasa')) {
                $table->string('rasa')->nullable()->after('kotoran');
            }
            if (!Schema::hasColumn('analisa_short_term', 'aroma_pengotor')) {
                $table->string('aroma_pengotor')->nullable()->after('rasa');
            }
            if (!Schema::hasColumn('analisa_short_term', 'no_cawan')) {
                $table->string('no_cawan')->nullable()->after('aroma_pengotor');
            }
            if (!Schema::hasColumn('analisa_short_term', 'berat_cawan')) {
                $table->decimal('berat_cawan', 10, 4)->nullable()->after('no_cawan');
            }
            if (!Schema::hasColumn('analisa_short_term', 'timbang_aa')) {
                $table->decimal('timbang_aa', 10, 4)->nullable()->after('berat_cawan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analisa_short_term', function (Blueprint $table) {
            $columns = [
                'no_beaker',
                'berat_beaker_500',
                'berat_beaker_250',
                'timbang_a',
                'timbang_b',
                'rasa',
                'aroma_pengotor',
                'no_cawan',
                'berat_cawan',
                'timbang_aa',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('analisa_short_term', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
