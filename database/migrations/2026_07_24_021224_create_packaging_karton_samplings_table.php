<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packaging_karton_samplings')) {
            return;
        }

        Schema::create('packaging_karton_samplings', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('packaging_incoming_id')
                ->unique()
                ->constrained('packaging_incomings')
                ->cascadeOnDelete();

            $table->unsignedInteger('jumlah_sampel')->default(1);
            $table->string('no_batch')->nullable();
            $table->string('lot_sebelum')->nullable();
            $table->string('lot_setelah')->nullable();

            $table->json('hasil_sampel')->nullable();
            $table->string('coa')->nullable();
            $table->string('rekomendasi')->nullable();
            $table->string('konfirmasi_ketidaksesuaian')->nullable();

            $table->json('jenis_ketidaksesuaian')->nullable();
            $table->json('foto')->nullable();
            $table->json('foto_ketidaksesuaian')->nullable();

            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packaging_karton_samplings');
    }
};