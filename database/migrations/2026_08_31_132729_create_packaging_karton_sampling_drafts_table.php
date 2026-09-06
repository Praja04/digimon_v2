<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packaging_karton_sampling_drafts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('packaging_incoming_id');
            $table->unique(
                'packaging_incoming_id',
                'karton_draft_incoming_unique'
            );
            $table->foreign(
                'packaging_incoming_id',
                'karton_draft_incoming_fk'
            )
                ->references('id')
                ->on('packaging_incomings')
                ->cascadeOnDelete();

            $table->unsignedInteger('jumlah_sampel')->nullable();
            $table->string('no_batch')->nullable();
            $table->json('hasil_sampel')->nullable();

            $table->string('coa')->nullable();
            $table->string('rekomendasi')->nullable();
            $table->string('konfirmasi_ketidaksesuaian')->nullable();

            $table->json('jenis_ketidaksesuaian')->nullable();
            $table->json('foto')->nullable();
            $table->json('foto_ketidaksesuaian')->nullable();

            $table->text('keterangan')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign(
                'created_by',
                'karton_draft_created_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign(
                'updated_by',
                'karton_draft_updated_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packaging_karton_sampling_drafts');
    }
};
