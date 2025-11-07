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
        Schema::create('monitoring_pasteurisasi_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->unsignedBigInteger('monitoring_pasteurisasi_id');
            $table->foreign('monitoring_pasteurisasi_id', 'mp_relation_fk')
                ->references('id')
                ->on('monitoring_pasteurisasi')
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
        Schema::dropIfExists('monitoring_pasteurisasi_relations');
    }
};
