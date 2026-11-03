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
        Schema::create('analisa_bahan_kering', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_identitas')->constrained('identitas_rm')->onDelete('cascade');
            $table->unsignedInteger('sampel_ke')->default(1);
            $table->double('gross_weight')->nullable(); // kg
            $table->double('ka')->nullable();           // % Kadar Air
            $table->string('kotoran')->nullable();      // Nilai angka atau teks seperti "Sesuai standar"
            $table->string('organo')->nullable();
            $table->string('warna')->nullable();
            $table->string('fisik')->nullable();
            $table->double('brix')->nullable();         // Khusus rafinasi
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
        Schema::dropIfExists('analisa_bahan_kering');
    }
};

