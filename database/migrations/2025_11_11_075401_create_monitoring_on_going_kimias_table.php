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
        Schema::create('monitoring_on_going_kimia', function (Blueprint $table) {
            $table->id();
            $table->string('storage'); // storage yang dipakai untuk filling
            $table->foreignId('production_batch_id')->constrained('production_batches')->onDelete('cascade');
            $table->string('formulation')->nullable(); // bila lebih dari 1 PO dalam 1 storage
            $table->string('variant');
            $table->string('jenis_sampel');
            $table->date('filling_date');
            $table->time('jam_koding');

            // Data analisa lab kimia
            $table->foreignId('analis_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('shift')->nullable();
            $table->timestamp('received_at')->nullable();

            // Parameter analisa
            $table->string('berat_jenis')->nullable();
            $table->string('visco')->nullable();
            $table->string('brix')->nullable();
            $table->string('aw')->nullable();
            $table->string('nacl')->nullable();
            $table->string('ph')->nullable();
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('restrict');
            $table->string('organo')->nullable();

            $table->enum('status', ['OK', 'NOT OK'])->nullable();
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
        Schema::dropIfExists('monitoring_on_going_kimia');
    }
};
