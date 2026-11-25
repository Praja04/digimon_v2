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
        if (Schema::hasTable('analisa_rm_transaksi')) {
            Schema::table('analisa_rm_transaksi', function (Blueprint $table) {
                if (!Schema::hasColumn('analisa_rm_transaksi', 'group')) {
                    $table->string('group', 50)->nullable()->after('disposisi');
                }
            });

            try {
                \Illuminate\Support\Facades\DB::statement('CREATE OR REPLACE VIEW transaksi_analisa_rm AS SELECT * FROM analisa_rm_transaksi');
            } catch (\Throwable $e) {
                // Abaikan jika database environment tidak menggunakan view
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('analisa_rm_transaksi')) {
            Schema::table('analisa_rm_transaksi', function (Blueprint $table) {
                if (Schema::hasColumn('analisa_rm_transaksi', 'group')) {
                    $table->dropColumn('group');
                }
            });

            try {
                \Illuminate\Support\Facades\DB::statement('CREATE OR REPLACE VIEW transaksi_analisa_rm AS SELECT * FROM analisa_rm_transaksi');
            } catch (\Throwable $e) {
                //
            }
        }
    }
};
