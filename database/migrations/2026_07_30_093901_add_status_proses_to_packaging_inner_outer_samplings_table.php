<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'packaging_inner_outer_samplings',
            function (Blueprint $table): void {
                $table
                    ->string('status_proses', 20)
                    ->default('draft')
                    ->after('packaging_incoming_id');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'packaging_inner_outer_samplings',
            function (Blueprint $table): void {
                $table->dropColumn('status_proses');
            }
        );
    }
};