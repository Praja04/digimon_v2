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
        Schema::create('monitoring_daily_tank', function (Blueprint $table) {
            $table->id();
            // Identitas Sampel
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->string('storage', 100)->nullable();
            $table->dateTime('tanggal_sampling')->nullable();
            $table->string('sampling_point', 50)->nullable(); // DT, DTP1, DTP2, dst.
            $table->enum('status_pemakaian', ['Filling', 'Habis'])->nullable();
            $table->enum('jenis_analisa', ['Kimia', 'Mikro'])->nullable();
            $table->string('jenis_sample', 50)->nullable(); // Awal Trf, Tengah PO, dll
            $table->text('keterangan_level')->nullable();

            // Data QC & Lab
            $table->foreignId('qc_field')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('tanggal_diterima_lab')->nullable();

            // Analisa
            $table->string('shift_analisa', 20)->nullable();
            $table->foreignId('qc_analisa')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('tanggal_analisa')->nullable();

            // Parameter & Hasil Uji (maksimal 3 parameter umum)
            $table->float('eb')->nullable();
            $table->float('tpc')->nullable();
            $table->float('ym')->nullable();
            $table->enum('hasil', ['OK', 'NOT OK', 'PENDING'])->nullable();

            // Parameter Kimia
            $table->float('brix')->nullable();
            $table->float('nacl')->nullable();
            $table->float('bj')->nullable();
            $table->float('visco')->nullable();
            $table->float('aw')->nullable();
            $table->float('buih')->nullable();
            $table->float('ph')->nullable();
            $table->string('organo')->nullable();
            $table->string('endapan')->nullable();
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('restrict');
            $table->enum('status', ['OK', 'NOT OK'])->nullable();

            // Hasil Analisa & Catatan
            $table->text('catatan_analis')->nullable();
            $table->dateTime('tanggal_input_hasil')->nullable();

            // Disposisi / Keputusan Akhir
            $table->enum('disposisi', ['Release', 'Release Bersyarat', 'Drain'])->nullable();
            $table->text('alasan_disposisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_daily_tank');
    }
};
