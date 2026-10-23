<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('master_standar_rms', 'jenis_standar')) {
            Schema::table('master_standar_rms', function (Blueprint $table) {
                $table->enum('jenis_standar', ['kuanti', 'kuali'])
                    ->default('kuanti')
                    ->after('id_jenis_bahan');
            });
        }

        // Populate existing data: if min or max is not null, it's kuanti, otherwise kuali
        DB::table('master_standar_rms')
            ->whereNull('min_standar')
            ->whereNull('max_standar')
            ->update(['jenis_standar' => 'kuali']);

        DB::table('master_standar_rms')
            ->where(function ($q) {
                $q->whereNotNull('min_standar')->orWhereNotNull('max_standar');
            })
            ->update(['jenis_standar' => 'kuanti']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('master_standar_rms', 'jenis_standar')) {
            Schema::table('master_standar_rms', function (Blueprint $table) {
                $table->dropColumn('jenis_standar');
            });
        }
    }
};
