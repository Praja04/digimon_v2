<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'packaging_pouch_samplings',
            function (Blueprint $table): void {
                $table
                    ->json('hasil_thickness')
                    ->nullable()
                    ->after('hasil_sampel');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'packaging_pouch_samplings',
            function (Blueprint $table): void {
                $table->dropColumn(
                    'hasil_thickness'
                );
            }
        );
    }
};
