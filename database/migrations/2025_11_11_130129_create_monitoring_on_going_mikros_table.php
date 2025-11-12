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
        Schema::create('monitoring_on_going_mikro', function (Blueprint $table) {
            $table->id();
            $table->string('storage'); // storage yang dipakai untuk filling
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->string('formulation')->nullable(); // bila lebih dari 1 PO dalam 1 storage
            $table->string('variant');
            $table->string('no_filler');
            $table->string('no_kempu_jeriken');
            $table->date('filling_date');
            $table->string('koding');
            $table->time('jam_koding');
            $table->string('jenis_sampel_1');
            $table->string('jenis_sampel_2')->nullable();
            $table->string('jenis_sampel_3')->nullable();
            $table->text('keterangan')->nullable();

            // Data analisa lab kimia
            $table->foreignId('analis_eb')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('analis_tpc')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('analis_ym')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('analis_benda_asing')->nullable()->constrained('users')->onDelete('set null');
            $table->string('shift')->nullable();
            $table->timestamp('received_at')->nullable();

            // Parameter hasil analisa mikro
            $table->string('eb')->nullable();
            $table->string('tpc')->nullable();
            $table->string('ym')->nullable();
            $table->string('benda_asing')->nullable();

            $table->string('hasil')->nullable();
            $table->string('disposition')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_on_going_mikro');
    }
};
