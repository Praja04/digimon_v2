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
        Schema::table('blending_awal_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('borrower_production_batch_id')->nullable()->after('production_batch_id');
            $table->foreign('borrower_production_batch_id')->references('id')->on('production_batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blending_awal_relations', function (Blueprint $table) {
            $table->dropForeign(['borrower_production_batch_id']);
            $table->dropColumn('borrower_production_batch_id');
        });
    }
};
