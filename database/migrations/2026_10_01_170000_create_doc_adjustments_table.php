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
        Schema::create('doc_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_batch_id')->nullable()->index();
            $table->date('tanggal_record_doc')->nullable();
            $table->string('halaman')->default('1');
            $table->string('proses')->nullable()->default('Blending');
            $table->string('jenis_kecap')->nullable();
            $table->string('no_batch')->nullable();
            $table->date('tanggal_produksi')->nullable();
            $table->string('volume_batch')->nullable();
            $table->string('shift')->nullable();
            $table->json('bahan_rows')->nullable();
            $table->text('disposisi')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('adj1_jam')->nullable();
            $table->string('adj1_status')->nullable();
            $table->string('adj1_petugas')->nullable();
            $table->string('adj2_jam')->nullable();
            $table->string('adj2_status')->nullable();
            $table->string('adj2_petugas')->nullable();
            $table->string('adj3_jam')->nullable();
            $table->string('adj3_status')->nullable();
            $table->string('adj3_petugas')->nullable();
            $table->string('qc_analis')->nullable();
            $table->string('doc_code')->default('FRM/QLB/04/104/011-00');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('production_batch_id')
                ->references('id')
                ->on('production_batches')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_adjustments');
    }
};
