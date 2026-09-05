<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('suppliers')) {
            return;
        }

        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('jenis_incoming_id')
                ->nullable()
                ->constrained('jenis_incomings')
                ->cascadeOnDelete();

            $table->string('kode', 50)
                ->nullable()
                ->unique();

            $table->string('nama', 150)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};