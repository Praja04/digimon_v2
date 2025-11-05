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
        Schema::create('monitoring_turun_blending_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->unsignedBigInteger('monitoring_turun_blending_id');
            $table->foreign('monitoring_turun_blending_id', 'mtb_relation_fk')
                ->references('id')
                ->on('monitoring_turun_blending')
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
        Schema::dropIfExists('monitoring_turun_blending_relations');
    }
};
