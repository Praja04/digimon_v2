<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'packaging_incomings',
            function (Blueprint $table): void {
                $table
                    ->unsignedInteger('jumlah_sampel')
                    ->nullable();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'packaging_incomings',
            function (Blueprint $table): void {
                $table->dropColumn('jumlah_sampel');
            }
        );
    }
};