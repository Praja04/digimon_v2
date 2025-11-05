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
        Schema::create('konfirmasi_blending_after_adjust_mikro', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blending_after_adjust_mikro_id');
            $table->foreign('blending_after_adjust_mikro_id', 'baam_relation_fk')
                ->references('id')
                ->on('blending_after_adjust_mikro')
                ->onDelete('cascade');
            $table->string('shift', 10)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfirmasi_blending_after_adjust_mikro');
    }
};
