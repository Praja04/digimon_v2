<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'packaging_inner_outer_samplings',
            function (Blueprint $table) {
                $table->id();

                $table
                    ->foreignId('packaging_incoming_id')
                    ->constrained('packaging_incomings')
                    ->cascadeOnDelete();

                $table
                    ->unsignedInteger('jumlah_sampel')
                    ->default(1);

                $table
                    ->string('no_batch')
                    ->nullable();

                $table
                    ->string('lot_sebelum')
                    ->nullable();

                $table
                    ->string('lot_setelah')
                    ->nullable();

                $table->json('hasil_sampel');

                $table
                    ->string('coa')
                    ->nullable();

                $table
                    ->string('rekomendasi')
                    ->nullable();

                $table
                    ->string('konfirmasi_ketidaksesuaian')
                    ->nullable();

                $table
                    ->string('jenis_ketidaksesuaian')
                    ->nullable();

                $table
                    ->string('foto_pengecekan')
                    ->nullable();

                $table
                    ->string('foto_ketidaksesuaian')
                    ->nullable();

                $table
                    ->text('keterangan')
                    ->nullable();

                $table
                    ->foreignId('created_by')
                    ->nullable();

                $table
                    ->foreignId('updated_by')
                    ->nullable();

                $table->timestamps();

                $table->unique(
                    'packaging_incoming_id'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'packaging_inner_outer_samplings'
        );
    }
};