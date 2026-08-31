<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blending_awal_foreman_drafts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blending_awal_id')
                ->unique()
                ->constrained('blending_awal')
                ->cascadeOnDelete();

            $table->string('disposition')->nullable();
            $table->text('disposition_remark')->nullable();

            $table->decimal('adjustment_qty_air', 10, 3)->nullable();
            $table->decimal('adjustment_qty_garam', 10, 3)->nullable();
            $table->decimal('adjustment_qty_caramel', 10, 3)->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blending_awal_foreman_drafts');
    }
};