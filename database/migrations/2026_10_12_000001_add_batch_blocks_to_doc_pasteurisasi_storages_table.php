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
        Schema::table('doc_pasteurisasi_storages', function (Blueprint $table) {
            $table->json('batch_blocks')->nullable()->after('halaman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_pasteurisasi_storages', function (Blueprint $table) {
            $table->dropColumn('batch_blocks');
        });
    }
};
