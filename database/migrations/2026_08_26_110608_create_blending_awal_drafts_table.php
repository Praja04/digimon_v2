<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blending_awal_drafts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('blending_awal_id')->unique();

            $table->decimal('brix', 12, 4)->nullable();
            $table->decimal('nacl', 12, 4)->nullable();
            $table->decimal('bj', 12, 4)->nullable();
            $table->decimal('visco', 12, 4)->nullable();
            $table->decimal('aw', 12, 4)->nullable();
            $table->decimal('ph', 12, 4)->nullable();

            $table->string('organo')->nullable();
            $table->string('aroma')->nullable();

            $table->unsignedBigInteger('color_id')->nullable();

            $table->string('status_disposition')->nullable();
            $table->text('disposition_remark')->nullable();

            $table->decimal('adjustment_qty_air', 12, 4)->nullable();
            $table->decimal('adjustment_qty_caramel', 12, 4)->nullable();
            $table->decimal('adjustment_qty_garam', 12, 4)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('blending_awal_id')
                ->references('id')
                ->on('blending_awal')
                ->cascadeOnDelete();

            $table->foreign('color_id')
                ->references('id')
                ->on('colors')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blending_awal_drafts');
    }
};