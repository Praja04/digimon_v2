<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_turun_blending_drafts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('monitoring_turun_blending_id');

            $table->decimal('brix', 12, 4)->nullable();
            $table->decimal('visco', 12, 4)->nullable();
            $table->decimal('aw', 12, 4)->nullable();

            $table->string('status_disposition', 50)->nullable();
            $table->string('disposition', 100)->nullable();
            $table->text('disposition_remark')->nullable();

            $table->decimal('adjustment_qty_air', 12, 4)->nullable();
            $table->decimal('adjustment_qty_gula', 12, 4)->nullable();
            $table->decimal('adjustment_qty_garam', 12, 4)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // Unique index
            $table->unique(
                'monitoring_turun_blending_id',
                'uq_mtb_draft_blending'
            );

            // Foreign key
            $table->foreign(
                'monitoring_turun_blending_id',
                'fk_mtb_draft_blending'
            )
                ->references('id')
                ->on('monitoring_turun_blending')
                ->cascadeOnDelete();

            // Created by
            $table->foreign(
                'created_by',
                'fk_mtb_draft_created_by'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_turun_blending_drafts');
    }
};
