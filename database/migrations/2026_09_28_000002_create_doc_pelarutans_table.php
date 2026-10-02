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
        Schema::create('doc_pelarutans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_batch_id')->nullable()->constrained('production_batches')->onDelete('cascade');
            $table->date('tanggal_record_doc')->nullable();
            $table->string('halaman')->default('1');
            $table->json('batch_blocks')->nullable();
            $table->text('catatan')->nullable();
            $table->string('pic_sampling')->nullable();
            $table->string('pic_analis')->nullable();
            $table->string('pic_checker')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_pelarutans');
    }
};
