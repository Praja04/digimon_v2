<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelarutan_2_drafts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pelarutan_2_id')->unique();

            $table->decimal('brix', 10, 4)->nullable();
            $table->decimal('nacl', 10, 4)->nullable();
            $table->decimal('visco', 10, 4)->nullable();

            $table->string('organo')->nullable();

            $table->string('status_disposition')->nullable();

            $table->text('disposition_remark')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('pelarutan_2_id')
                ->references('id')
                ->on('pelarutan_2')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelarutan_2_drafts');
    }
};