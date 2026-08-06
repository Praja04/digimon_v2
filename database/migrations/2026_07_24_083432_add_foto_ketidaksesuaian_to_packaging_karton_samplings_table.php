<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'packaging_karton_samplings',
            function (Blueprint $table) {
                $table
                    ->string('foto_ketidaksesuaian')
                    ->nullable()
                    ->after('foto');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'packaging_karton_samplings',
            function (Blueprint $table) {
                $table->dropColumn(
                    'foto_ketidaksesuaian'
                );
            }
        );
    }
};