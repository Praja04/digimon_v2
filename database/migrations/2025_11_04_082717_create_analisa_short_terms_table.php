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
        Schema::create('analisa_short_term', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_identitas')->constrained('identitas_rm')->onDelete('cascade');
            $table->float('brix')->nullable();
            $table->float('ph')->nullable();
            $table->string('kotoran')->nullable();
            $table->float('ka')->nullable();
            $table->string('organo')->nullable();
            $table->string('warna')->nullable();
            $table->string('aroma')->nullable();
            $table->string('disposisi')->nullable();
            $table->string('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisa_short_term');
    }
};
