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
        if (!Schema::hasTable('master_glasswares')) {
            Schema::create('master_glasswares', function (Blueprint $table) {
                $table->id();
                $table->string('jenis_glassware');
                $table->string('nomor_glassware');
                $table->decimal('berat_glassware', 10, 4)->nullable();
                $table->string('uom_berat')->default('g');
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_glasswares');
    }
};
