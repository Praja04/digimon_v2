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
        Schema::create('sampling_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_identitas')->constrained('identitas_rm')->onDelete('cascade');
            $table->string('coa')->nullable();
            $table->string('surat_jalan')->nullable();
            $table->string('packing_list')->nullable();
            $table->string('identitas_kemasan')->nullable();
            $table->string('logo_halal')->nullable();
            $table->string('kesesuaian_matriks_bahan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_dokumen');
    }
};
