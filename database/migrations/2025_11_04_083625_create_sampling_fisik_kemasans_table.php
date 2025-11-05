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
        Schema::create('sampling_fisik_kemasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_identitas')->constrained('identitas_rm')->onDelete('cascade');
            $table->string('kotor')->nullable();
            $table->text('keterangan_kotor')->nullable();
            $table->string('rusak')->nullable();
            $table->text('keterangan_rusak')->nullable();
            $table->string('sesuai_std')->nullable();
            $table->text('keterangan_sesuai_std')->nullable();
            $table->string('berair')->nullable();
            $table->text('keterangan_berair')->nullable();
            $table->string('basah')->nullable();
            $table->text('keterangan_basah')->nullable();
            $table->string('campuran')->nullable();
            $table->text('keterangan_campuran')->nullable();
            $table->string('lain_lain')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_fisik_kemasan');
    }
};
