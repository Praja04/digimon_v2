<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'packaging_pouch_samplings',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('packaging_incoming_id')
                    ->unique()
                    ->constrained('packaging_incomings')
                    ->cascadeOnDelete();

                $table
                    ->string('status_proses', 20)
                    ->default('draft');

                $table->decimal('qty', 15, 2)->nullable();
                $table->string('uom', 50)->nullable();
                $table->unsignedInteger('jumlah_sampel')->default(1);
                $table->json('hasil_sampel')->nullable();

                $table->string('coa', 50)->nullable();
                $table->string('rekomendasi', 100)->nullable();
                $table->string('konfirmasi_ketidaksesuaian', 50)->nullable();
                $table->string('jenis_ketidaksesuaian', 255)->nullable();

                $table->string('foto_pengecekan')->nullable();
                $table->string('foto_ketidaksesuaian')->nullable();
                $table->text('keterangan')->nullable();

                $table->foreignId('created_by')->nullable();
                $table->foreignId('updated_by')->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'packaging_pouch_samplings'
        );
    }
};