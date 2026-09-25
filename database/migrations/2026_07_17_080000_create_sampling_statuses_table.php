<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sampling_statuses')) {
            return;
        }

        Schema::create('sampling_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 100)->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sampling_statuses');
    }
};