<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jenis_incomings')) {
            return;
        }

        Schema::create('jenis_incomings', function (Blueprint $table): void {
            $table->id();
            $table->string('kategori', 50);
            $table->string('nama', 255);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_incomings');
    }
};