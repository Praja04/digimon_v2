<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelarutan_1_drafts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pelarutan_1_id')->unique();

            $table->decimal('brix', 10, 4)->nullable();
            $table->decimal('nacl', 10, 4)->nullable();
            $table->string('organo')->nullable();

            $table->string('status_disposition')->nullable();
            $table->text('disposition_remark')->nullable();

            $table->decimal('adjustment_qty_gula_tebu', 12, 4)->nullable();
            $table->decimal('adjustment_qty_gula_kelapa', 12, 4)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('pelarutan_1_id')
                ->references('id')
                ->on('pelarutan_1')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelarutan_1_drafts');
    }
};