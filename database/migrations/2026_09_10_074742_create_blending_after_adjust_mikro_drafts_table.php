<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blending_after_adjust_mikro_drafts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('blending_after_adjust_mikro_id');

            $table->string('eb')->nullable();
            $table->string('tpc')->nullable();
            $table->string('ym')->nullable();
            $table->string('hasil')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->unique(
                'blending_after_adjust_mikro_id',
                'baam_draft_unique'
            );

            $table->foreign(
                'blending_after_adjust_mikro_id',
                'baam_draft_mikro_fk'
            )
                ->references('id')
                ->on('blending_after_adjust_mikro')
                ->cascadeOnDelete();

            $table->foreign(
                'created_by',
                'baam_draft_user_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blending_after_adjust_mikro_drafts');
    }
};