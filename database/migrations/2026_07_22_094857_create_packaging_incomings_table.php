<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packaging_incomings')) {
            return;
        }

        Schema::create('packaging_incomings', function (Blueprint $table): void {
            $table->id();
            $table->string('no_spb', 100)->unique();

            $table->foreignId('jenis_incoming_id')
                ->constrained('jenis_incomings')
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->restrictOnDelete();

            $table->foreignId('jenis_material_id')
                ->constrained('jenis_materials')
                ->restrictOnDelete();

            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('uoms')
                ->nullOnDelete();

            $table->foreignId('sampling_status_id')
                ->nullable()
                ->constrained('sampling_statuses')
                ->nullOnDelete();

            $table->string('mid', 100)->nullable();
            $table->string('no_mobil', 100)->nullable();
            $table->date('tanggal_kedatangan');
            $table->time('jam_kedatangan')->nullable();
            $table->decimal('jumlah', 15, 2)->nullable();
            $table->string('no_batch', 150)->nullable();
            $table->text('keterangan')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->unsignedInteger('jumlah_sampel')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packaging_incomings');
    }
};