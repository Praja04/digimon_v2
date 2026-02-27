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
        Schema::rename('gga', 'pelarutan_1');
        Schema::rename('ggas', 'pelarutan_2');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('pelarutan_1', 'gga');
        Schema::rename('pelarutan_2', 'ggas');
    }
};
