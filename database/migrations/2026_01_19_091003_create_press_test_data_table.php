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
        Schema::create('press_test_data', function (Blueprint $table) {
            $table->id();
            $table->string('nama_analis');
            $table->string('shift')->nullable();
            $table->string('variant');
            $table->datetime('waktu');
            $table->string('mesin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('press_test_data');
    }
};
