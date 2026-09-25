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
        Schema::create('analisa_long_term_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analisa_long_term_id')->nullable()->constrained('analisa_long_term')->onDelete('cascade');
            $table->foreignId('id_identitas')->constrained('identitas_rm')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action')->default('Simpan Final'); // Simpan Sementara (Draft), Simpan Final, Update Disposisi
            $table->string('uji_kristal')->nullable();
            $table->string('disposisi')->nullable();
            $table->string('group')->nullable();
            $table->text('attachment')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisa_long_term_histories');
    }
};
