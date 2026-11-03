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
        Schema::create('analisa_bahan_cair', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_identitas')->constrained('identitas_rm')->onDelete('cascade');
            $table->unsignedInteger('sampel_ke')->default(1);
            $table->double('brix')->nullable();         // °Bx
            $table->double('ph')->nullable();
            $table->double('berat_jenis')->nullable();  // BJ
            $table->double('suhu')->nullable();         // °C
            $table->double('aw')->nullable();           // Water activity
            $table->double('viskositas')->nullable();   // cP
            $table->double('nacl')->nullable();         // % NaCl
            $table->double('tn')->nullable();           // % Total Nitrogen
            $table->double('gross_weight')->nullable(); // kg
            $table->string('warna')->nullable();
            $table->string('aroma')->nullable();
            $table->string('organo')->nullable();
            $table->string('disposisi')->nullable();
            $table->string('status')->default('draft'); // draft / final
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisa_bahan_cair');
    }
};

