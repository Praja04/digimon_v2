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
                    ->decimal(
                        'quantity_incoming',
                        15,
                        2
                    )
                    ->nullable()
                    ->after('jam_kedatangan');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'packaging_incomings',
            function (Blueprint $table): void {
                $table->dropColumn(
                    'quantity_incoming'
                );
            }
        );
    }
};