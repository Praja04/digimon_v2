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
        Schema::create('monitoring_storage_kimia_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->unsignedBigInteger('monitoring_storage_kimia_id');
            $table->foreign('monitoring_storage_kimia_id', 'msk_relation_fk')
                ->references('id')
                ->on('monitoring_storage_kimia')
                ->onDelete('cascade');
            $table->string('batch', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_storage_kimia_relations');
    }
};
