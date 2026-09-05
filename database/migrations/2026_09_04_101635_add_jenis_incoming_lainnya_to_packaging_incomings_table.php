<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packaging_incomings', function (Blueprint $table) {
            $table->string(
                'jenis_incoming_lainnya',
                255
            )
                ->nullable()
                ->after('jenis_incoming_id');
        });
    }

    public function down(): void
    {
        Schema::table('packaging_incomings', function (Blueprint $table) {
            $table->dropColumn(
                'jenis_incoming_lainnya'
            );
        });
    }
};